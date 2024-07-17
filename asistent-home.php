<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

$prenume = $_SESSION['user'];
$cnp = $_SESSION['cnp'];
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';

// Ștergem mesajul din sesiune după ce este afișat
unset($_SESSION['message']);
unset($_SESSION['message_type']);

// Setăm user_type pentru asistent
$_SESSION['user_type'] = 'asistent';

include 'config.php';

// Obținem medic_id, nume și prenume pentru asistentul curent
$sql = "SELECT medic_id, nume, prenume FROM asistenti WHERE CNP = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('s', $cnp);
if (!$stmt->execute()) {
    die("Execute statement failed: " . $stmt->error);
}
$result = $stmt->get_result();
if (!$result) {
    die("Get result failed: " . $stmt->error);
}
$asistent = $result->fetch_assoc();
$medic_id = $asistent['medic_id'];
$nume_asistent = $asistent['nume'];
$prenume_asistent = $asistent['prenume'];
$stmt->close();

// Obținem ora de început și sfârșit pentru medicul asociat
$sql = "SELECT TIME_FORMAT(ora_inceput, '%H:%i') as ora_inceput, TIME_FORMAT(ora_sfarsit, '%H:%i') as ora_sfarsit FROM medici WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('i', $medic_id);
if (!$stmt->execute()) {
    die("Execute statement failed: " . $stmt->error);
}
$result = $stmt->get_result();
if (!$result) {
    die("Get result failed: " . $stmt->error);
}
$medic_program = $result->fetch_assoc();

$ora_inceput = $medic_program['ora_inceput'];
$ora_sfarsit = $medic_program['ora_sfarsit'];

$stmt->close();

// Obținerea numărului de notificări necitite din tabela notificari
$sql = "SELECT COUNT(*) as unread_count 
        FROM notificari n
        JOIN programari p ON n.programare_id = p.id
        WHERE p.medic_id = ? AND n.destinatar = 'asistent' AND n.citit = 0";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('i', $medic_id);
if (!$stmt->execute()) {
    die("Execute statement failed: " . $stmt->error);
}
$result = $stmt->get_result();
if (!$result) {
    die("Get result failed: " . $stmt->error);
}
$row = $result->fetch_assoc();
$unread_notifications = $row['unread_count'];
$stmt->close();

// Obținerea numărului de mesaje necitite din tabela mesaje
$sql = "SELECT COUNT(*) as unread_count 
        FROM mesaje 
        WHERE medic_id = ? AND destinatar = 'asistent' AND citit = 0";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('i', $medic_id);
if (!$stmt->execute()) {
    die("Execute statement failed: " . $stmt->error);
}
$result = $stmt->get_result();
if (!$result) {
    die("Get result failed: " . $stmt->error);
}
$row = $result->fetch_assoc();
$unread_messages = $row['unread_count'];
$stmt->close();
$conn->close();

// Calcularea numărului total de notificări necitite
$total_unread = $unread_notifications + $unread_messages;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://kit.fontawesome.com/a7bbb09be2.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style/asistent.css">
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/ro.js'></script>
  <title>Asistent</title>
  <style>
    .message.error {
        background: #ffdddd;
        border: 1px solid #f44336;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
    }
    .btn-approve {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px 20px;
        margin: 5px;
        cursor: pointer;
        border-radius: 5px;
    }
    .btn-decline {
        background-color: #f44336;
        color: white;
        border: none;
        padding: 10px 20px;
        margin: 5px;
        cursor: pointer;
        border-radius: 5px;
    }
    .fc-day-sat, .fc-day-sun {
        background-color: #f0f0f0; /* Grayed out */
        pointer-events: none; /* Disable click events */
    }
    .fc-day-sat .fc-daygrid-day-top, .fc-day-sun .fc-daygrid-day-top {
        background-color: #d0d0d0; /* Slightly darker to distinguish */
    }
    .notification-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 20px;
        height: 20px;
        padding: px;
        border-radius: 50%;
        background: red;
        color: white;
        font-size: 12px;
        text-align: center;
        line-height: 20px;
    }
    .navbar-item {
        position: relative;
    }
    .fc-toolbar-title {
        text-transform: uppercase;
        font-size: 24px; /* Măriți dimensiunea textului după preferințe */
        padding-top: 20px;
    }
  </style>
</head>
<body>
  <div id="sidebar">
    <ul>
      <li><a href="#"><i class="fa fa-user fa-sm"></i> Log Out</a></li>
    </ul>
  </div>

  <nav id="navbar" class="navsticky">
    <div class="container">
      <h1 class="logo"><a href="index.html">MedFM</a></h1>
      <ul>
      <li><a href="asistent-home.php">Calendar</a></li>
        <li class="navbar-item">
            
          <a href="asistent-inbox.php">Inbox</a>
          <?php if ($total_unread > 0): ?>
            <span class="notification-badge"><?php echo $total_unread; ?></span>
          <?php endif; ?>
        </li>

        <li><a href="#">Bună, <?php echo htmlspecialchars($prenume); ?></a></li>
      </ul>
    </div>
  </nav>

  <div class="container calendar-container">
    <div id='calendar'></div>
  </div>

  <footer id="secondary-footer">
    <p>MedFM &copy; 2023, All Rights Reserved</p>
  </footer>

  <!-- Modal pentru adăugarea programărilor -->
  <div id="add-appointment-modal" class="modal" style="display: none;">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Adauga programare</h2>
      <div class="message error" style="display: none;"></div>
      <p>Adauga datele pacientului pentru programarea din data de <span id="selected-date"></span>, la ora <span id="selected-time"></span></p>
      <div class="form-group">
        <label for="prenume">Prenume:</label>
        <input type="text" id="prenume" required>
      </div>
      <div class="form-group">
        <label for="nume">Nume:</label>
        <span style="margin-left:20px"></span><input type="text" id="nume" required>
      </div>
      <button id="add-appointment" class="btn">Adauga programare</button>
    </div>
  </div>

  <!-- Modal pentru aprobarea programărilor -->
  <div id="approve-appointment-modal" class="modal" style="display: none;">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2 id="approve-title">Doriti sa aprobati programarea din data <span id="approve-date"></span>, la ora <span id="approve-time"></span>, pentru pacientul <span id="approve-patient"></span>?</h2>
      <button id="approve-yes" class="btn-approve">DA</button>
      <button id="approve-no" class="btn-decline">NU</button>
    </div>
  </div>

  <!-- Modal pentru anularea programărilor -->
  <div id="cancel-appointment-modal" class="modal" style="display: none;">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Vreti sa anulati programarea?</h2>
      <button id="cancel-yes" class="btn-approve">DA</button>
      <button id="cancel-no" class="btn-decline">NU</button>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var modal = document.getElementById('add-appointment-modal');
    var approveModal = document.getElementById('approve-appointment-modal');
    var cancelModal = document.getElementById('cancel-appointment-modal');
    var spans = document.getElementsByClassName('close');
    var selectedDate, selectedTime, selectedEvent;

    var oraInceput = "<?php echo $ora_inceput; ?>";
    var oraSfarsit = "<?php echo $ora_sfarsit; ?>";

    function getCurrentDateTime() {
        var now = new Date();
        var hours = now.getHours();
        var minutes = now.getMinutes();
        if (minutes >= 0 && minutes < 30) {
            minutes = 30;
        } else {
            minutes = 0;
            hours += 1;
        }
        return {
            date: now.toISOString().split('T')[0],
            time: (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes
        };
    }

    var currentDateTime = getCurrentDateTime();

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ro',
        initialView: 'timeGridWeek',
        slotMinTime: oraInceput,
        slotMaxTime: oraSfarsit,
        allDaySlot: false,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('get_appointments.php')
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        },
        eventClick: function(info) {
            console.log('Event clicked:', info.event); // Debug: verificăm evenimentul clicat
            selectedEvent = info.event;
            if (info.event.extendedProps.status === 'SOLICITATA') {
                document.getElementById('approve-date').innerText = info.event.start.toISOString().split('T')[0];
                document.getElementById('approve-time').innerText = info.event.start.toTimeString().split(' ')[0].slice(0, 5); // Format hh:mm
                document.getElementById('approve-patient').innerText = info.event.extendedProps.nume_pacient;
                approveModal.style.display = 'block';
            } else if (info.event.extendedProps.status === 'APROBATA') {
                cancelModal.style.display = 'block';
            }
        },
        eventDidMount: function(info) {
            if (info.event.extendedProps.status === 'SOLICITATA') {
                info.el.style.backgroundColor = '#FFF67E';
                info.el.style.color = 'black';  // Schimbă culoarea textului în negru
                info.el.style.borderColor = '#FFF67E';  // Schimbă bordura în #FFF67E
                info.el.style.display = 'flex';
                info.el.style.alignItems = 'center';
                info.el.style.justifyContent = 'center';
                info.el.style.fontSize = '11px'; // Diminuăm fontul
                info.el.style.lineHeight = '1.3'; // Reducem spațiul între rânduri
                info.el.style.padding = '3px 0';
            } else if (info.event.extendedProps.status === 'APROBATA') {
                info.el.style.backgroundColor = '#436850';
                info.el.style.color = 'white';  // Schimbă culoarea textului în alb
                info.el.style.borderColor = '#12372A';  // Schimbă bordura în #12372A
                info.el.style.display = 'flex';
                info.el.style.alignItems = 'center';
                info.el.style.justifyContent = 'center';
                info.el.style.fontSize = '13px'; // Diminuăm fontul
                info.el.style.lineHeight = '1.3'; // Reducem spațiul între rânduri
                info.el.style.padding = '6px 0';
            }
        },
        eventContent: function(arg) {
            let customHtml = arg.event.title;
            if (arg.event.extendedProps.status === 'SOLICITATA') {
                customHtml = `<div style="color: black; text-align: center;">ASTEPTARE CONFIRMARE</div>`;
            }
            return { html: customHtml };
        },
        dateClick: function(info) {
            var day = new Date(info.dateStr).getUTCDay();
            var dateStr = info.dateStr.split('T')[0];
            var timeStr = info.dateStr.split('T')[1];
            var currentTimeStr = currentDateTime.time;

            if (dateStr > currentDateTime.date || (dateStr === currentDateTime.date && timeStr >= currentTimeStr)) {
                if (day !== 0 && day !== 6) { // Exclude sâmbăta (6) și duminica (0)
                    selectedDate = dateStr;
                    selectedTime = timeStr.slice(0, 5);
                    document.getElementById('selected-date').innerText = selectedDate;
                    document.getElementById('selected-time').innerText = selectedTime;
                    modal.style.display = 'block';
                }
            }
        }
    });

    calendar.render();

    // Resetează formularul modalului și ascunde mesajele de eroare la închiderea modalului
    function resetModal() {
        document.getElementById('prenume').value = '';
        document.getElementById('nume').value = '';
        var errorMessage = modal.querySelector('.message.error');
        if (errorMessage) {
            errorMessage.style.display = 'none';
            errorMessage.innerText = '';
        }
    }

    for (var i = 0; i < spans.length; i++) {
        spans[i].onclick = function() {
            modal.style.display = 'none';
            approveModal.style.display = 'none';
            cancelModal.style.display = 'none';
            resetModal(); // Resetează formularul modalului la închidere
        }
    }

    window.onclick = function(event) {
        if (event.target == modal || event.target == approveModal || event.target == cancelModal) {
            modal.style.display = 'none';
            approveModal.style.display = 'none';
            cancelModal.style.display = 'none';
            resetModal(); // Resetează formularul modalului la închidere
        }
    }

    document.getElementById('add-appointment').onclick = function() {
        var prenume = document.getElementById('prenume').value;
        var nume = document.getElementById('nume').value;

        fetch('add_appointment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                date: selectedDate,
                time: selectedTime,
                prenume: prenume,
                nume: nume,
                medic_id: <?php echo $medic_id; ?>
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modal.style.display = 'none';
                calendar.refetchEvents();
            } else {
                var errorMessage = modal.querySelector('.message.error');
                if (errorMessage) {
                    errorMessage.innerText = data.message;
                    errorMessage.style.display = 'block';
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }

    document.getElementById('approve-yes').onclick = function() {
        fetch('update_appointment_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: selectedEvent.id,
                action: 'approve'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                approveModal.style.display = 'none';
                calendar.refetchEvents();
            } else {
                alert('A apărut o eroare. Vă rugăm să încercați din nou.');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    document.getElementById('approve-no').onclick = function() {
    fetch('update_appointment_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: selectedEvent.id,
            action: 'delete'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Appointment deleted successfully'); // Debug: confirmare ștergere reușită
            approveModal.style.display = 'none';
            calendar.refetchEvents();
        } else {
            alert('A apărut o eroare. Vă rugăm să încercați din nou.');
            console.error('Delete error:', data.error); // Debug: mesaj de eroare
        }
    })
    .catch(error => console.error('Error:', error));
}

document.getElementById('cancel-yes').onclick = function() {
    fetch('update_appointment_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: selectedEvent.id,
            action: 'delete'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Appointment deleted successfully'); // Debug: confirmare ștergere reușită
            cancelModal.style.display = 'none';
            calendar.refetchEvents();
        } else {
            alert('A apărut o eroare. Vă rugăm să încercați din nou.');
            console.error('Delete error:', data.error); // Debug: mesaj de eroare
        }
    })
    .catch(error => console.error('Error:', error));
}


});


  </script>
</body>
</html>
