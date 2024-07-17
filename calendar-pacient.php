<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login-pacient.html");
    exit();
}

$prenume = $_SESSION['user'];
$cnp = $_SESSION['cnp'];

// Setăm user_type pentru pacient
$_SESSION['user_type'] = 'pacient';

include 'config.php';

// Obținem medic_id pentru pacientul curent
$sql = "SELECT medic_id, nume, prenume FROM pacienti WHERE CNP = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $cnp);
$stmt->execute();
$result = $stmt->get_result();
$pacient_data = $result->fetch_assoc();
$medic_id = $pacient_data['medic_id'];
$nume_complet = $pacient_data['prenume'] . ' ' . $pacient_data['nume'];

// Salvăm medic_id în sesiune pentru a-l folosi ulterior
$_SESSION['medic_id'] = $medic_id;

$stmt->close();

// Obținem ora de început și sfârșit pentru medicul curent
$sql = "SELECT TIME_FORMAT(ora_inceput, '%H:%i') as ora_inceput, TIME_FORMAT(ora_sfarsit, '%H:%i') as ora_sfarsit FROM medici WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $medic_id);
$stmt->execute();
$result = $stmt->get_result();
$medic_program = $result->fetch_assoc();

$ora_inceput = $medic_program['ora_inceput'];
$ora_sfarsit = $medic_program['ora_sfarsit'];

$stmt->close();

// Obținerea numărului de notificări necitite din tabelul notificari
$sql = "SELECT COUNT(*) as unread_count 
        FROM notificari n
        JOIN programari p ON n.programare_id = p.id
        WHERE p.pacient_CNP = ? AND n.destinatar = 'pacient' AND n.citit = 0";

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
$row = $result->fetch_assoc();
$unread_notifications = $row['unread_count'];

$stmt->close();

// Obținerea numărului de mesaje necitite din tabelul mesaje
$sql = "SELECT COUNT(*) as unread_count 
        FROM mesaje 
        WHERE cnp = ? AND destinatar = 'pacient' AND citit = 0";

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
$row = $result->fetch_assoc();
$unread_messages = $row['unread_count'];

$stmt->close();
$conn->close();

// Calcularea numărului total de notificări necitite
$total_unread = $unread_notifications + $unread_messages;
?>

<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://kit.fontawesome.com/a7bbb09be2.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="style/calendar.css">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/ro.js'></script>
<title>Calendar</title>
<style>
    .fc-day-sat, .fc-day-sun {
        background-color: #f0f0f0; /* Grayed out */
        pointer-events: none; /* Disable click events */
    }
    .fc-day-sat .fc-daygrid-day-top, .fc-day-sun .fc-daygrid-day-top {
        background-color: #d0d0d0; /* Slightly darker to distinguish */
    }
    .modal {
  display: none; /* Ascunde modalul la început */
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content-p {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 35%;
  border-radius: 10px;
  text-align: center;
  margin-top: 200px;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
.error-message {
    color: red;
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
      <li><a href="#"><i class="fa fa-user fa-sm"></i> Profil</a></li>
      <li><a href="calendar-pacient.php"><i class="fa fa-calendar fa-sm"></i> Programările Tale</a></li>
      <li><a href="past_medical_records.php"><i class="fa fa-notes-medical fa-sm"></i> Istoric Medical</a></li>
      <li><a href="webminarii.php"><i class="fa fa-graduation-cap fa-sm"></i>Webminarii</a></li>
    </ul>
  </div>

  <nav id="navbar" class="navsticky">
    <div class="container">
      <h1 class="logo"><a href="index.html">MedFM</a></h1>
      <ul>
        <li><a class="current" href="patient-home.php">Acasă</a></li>
        <li class="navbar-item">
          <a href="pacient-inbox.php">Inbox</a>
          <?php if ($total_unread > 0): ?>
            <span class="notification-badge"><?php echo $total_unread; ?></span>
          <?php endif; ?>
        </li>
        <li><a href="#">Bună, <?php echo htmlspecialchars($prenume); ?></a></li>
      </ul>
    </div>
  </nav>

<div class="calendar-container">
    <div id='calendar'></div>
</div>

<!-- Tooltip -->
<div id="tooltip" class="tooltip">Solicitarea dvs.</div>

<!-- Modal pentru solicitarea programarii -->
<div id="modal" class="modal">
    <div class="modal-content-p">
    <span class="close">&times;</span>
    <p>Solicitați programare la <span id="appointment-time"></span> pe data de <span id="appointment-date"></span>?</p>
    <button id="request-appointment" class="btn">Solicita programare</button>
    </div>
</div>

<!-- Modal pentru mesajul de succes -->
<div id="success-modal" class="modal">
    <div class="modal-content-p">
    <span class="close">&times;</span>
    <p>Solicitare programare cu succes!</p>
    </div>
</div>

<!-- Modal pentru mesajul de eroare -->
<div id="error-modal" class="modal">
    <div class="modal-content-p">
        <span class="close">&times;</span>
        <p class="error-message" id="error-message"></p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var tooltip = document.getElementById('tooltip');
        var successModal = document.getElementById('success-modal');
        var errorModal = document.getElementById('error-modal');
        var errorMessage = document.getElementById('error-message');

        var oraInceput = "<?php echo $ora_inceput; ?>";
        var oraSfarsit = "<?php echo $ora_sfarsit; ?>";
        var pacientCNP = "<?php echo $cnp; ?>";
        var pacientNumeComplet = "<?php echo $nume_complet; ?>";

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
            titleFormat: { // Personalizează formatul titlului
                month: 'long'
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
            eventDidMount: function(info) {
                if (info.event.extendedProps.status === 'SOLICITATA') {
                    // Schimbăm culoarea doar dacă este solicitarea pacientului conectat
                    if (info.event.extendedProps.pacient_cnp === pacientCNP) {
                        info.el.style.backgroundColor = '#F6D776';  // Culoarea pentru pacientul conectat
                    } else {
                        info.el.style.backgroundColor = '#436850';  // Culoarea pentru alte solicitări
                    }
                    info.el.style.color = 'black';  // Schimbă culoarea textului în negru
                    info.el.style.borderColor = '#FFF67E';  // Schimbă bordura în #FFF67E
                    info.el.style.display = 'flex';
                    info.el.style.alignItems = 'center';
                    info.el.style.justifyContent = 'center';
                    info.el.style.fontSize = '11px'; // Diminuăm fontul
                    info.el.style.lineHeight = '1.3'; // Reducem spațiul între rânduri
                    info.el.style.padding = '3px 0';
                    
                    // Adăugăm logica pentru tooltip
                    if (info.event.extendedProps.pacient_cnp === pacientCNP) {
                        info.el.addEventListener('mouseenter', function() {
                            tooltip.innerText = 'Solicitarea dvs.'; // Resetăm textul tooltip-ului
                            var rect = info.el.getBoundingClientRect();
                            tooltip.style.top = (rect.top - 10) + 'px';
                            tooltip.style.left = (rect.left + rect.width + 10) + 'px';
                            tooltip.style.display = 'block';
                        });
                        info.el.addEventListener('mouseleave', function() {
                            tooltip.style.display = 'none';
                        });
                    }
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

                    // Tooltip pentru programările aprobate ale pacientului conectat
                    if (info.event.extendedProps.pacient_cnp === pacientCNP) {
                        info.el.addEventListener('mouseenter', function() {
                            tooltip.innerText = 'Programarea dvs.'; // Schimbăm textul tooltip-ului
                            var rect = info.el.getBoundingClientRect();
                            tooltip.style.top = (rect.top - 10) + 'px';
                            tooltip.style.left = (rect.left + rect.width + 10) + 'px';
                            tooltip.style.display = 'block';
                        });
                        info.el.addEventListener('mouseleave', function() {
                            tooltip.style.display = 'none';
                        });
                    }
                }
            },
            eventContent: function(arg) {
                let customHtml = arg.event.title;
                if (arg.event.extendedProps.status === 'SOLICITATA' && arg.event.extendedProps.pacient_cnp === pacientCNP) {
                    customHtml = `<div style="color: black; text-align: center;">ASTEPTARE CONFIRMARE</div>`;
                } else if (arg.event.extendedProps.status === 'APROBATA' && arg.event.extendedProps.pacient_cnp === pacientCNP) {
                    customHtml = `<div style="color: white; text-align: center;">${pacientNumeComplet}</div>`;
                } else {
                    customHtml = ''; // Nu afișăm nimic pentru alte programări
                }
                return { html: customHtml };
            },
            dateClick: function(info) {
                var day = new Date(info.dateStr).getUTCDay();
                var dateStr = info.dateStr.split('T')[0];
                var timeStr = info.dateStr.split('T')[1].split('+')[0];
                var currentTimeStr = currentDateTime.time;

                if (dateStr > currentDateTime.date || (dateStr === currentDateTime.date && timeStr >= currentTimeStr)) {
                    if (day !== 0 && day !== 6) { // Exclude sâmbăta (6) și duminica (0)
                        var timeFormatted = timeStr.split(':').slice(0, 2).join(':');

                        console.log('Date:', dateStr);  // Debug: verificăm data
                        console.log('Time:', timeFormatted);  // Debug: verificăm ora

                        fetch('check_appointment.php?date=' + dateStr + '&time=' + timeFormatted + '&pacient_cnp=' + pacientCNP)
                            .then(response => response.json())
                            .then(data => {
                                if (data.exists) {
                                    errorMessage.innerText = 'Aveți deja o programare în această zi!';
                                    errorModal.style.display = 'block';
                                } else {
                                    document.getElementById('appointment-date').innerText = dateStr;
                                    document.getElementById('appointment-time').innerText = timeFormatted;
                                    document.getElementById('modal').style.display = 'block';
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                }
            }
        });

        calendar.render();

        // Modal functionality
        var modal = document.getElementById('modal');
        modal.style.display = 'none';  // Ascunde modalul la început
        successModal.style.display = 'none';  // Ascunde modalul de succes la început
        errorModal.style.display = 'none';  // Ascunde modalul de eroare la început

        var span = document.getElementsByClassName('close');

        for (var i = 0; i < span.length; i++) {
            span[i].onclick = function() {
                this.parentElement.parentElement.style.display = 'none';
            }
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            } else if (event.target == successModal) {
                successModal.style.display = 'none';
            } else if (event.target == errorModal) {
                errorModal.style.display = 'none';
            }
        }

        document.getElementById('request-appointment').onclick = function() {
            var date = document.getElementById('appointment-date').innerText;
            var time = document.getElementById('appointment-time').innerText;

            console.log('Requesting appointment for date:', date, 'and time:', time); // Debug: verificăm datele și ora pentru solicitare

            fetch('request_appointment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({date: date, time: time, pacient_cnp: '<?php echo $cnp; ?>', nume_pacient: '<?php echo $nume_complet; ?>'})
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response from request_appointment:', data); // Debug: verificăm răspunsul de la server
                if (data.success) {
                    modal.style.display = 'none';
                    successModal.style.display = 'block';
                    calendar.refetchEvents();

                    // Adăugăm notificarea în tabelul `notificari`
                    fetch('add_notification.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            programare_id: data.programare_id,
                            mesaj: 'Solicitarea dvs. a fost trimisă cu succes!',
                            destinatar: 'pacient'
                        })
                    })
                    .then(response => response.json())
                    .then(notificationData => {
                        console.log('Notification added:', notificationData);
                    })
                    .catch(error => console.error('Error adding notification:', error));
                } else {
                    alert('A apărut o eroare. Vă rugăm să încercați din nou.');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
</script>

<footer id="secondary-footer">
    <div class="footer-elements">
        <div class="footer-item"><a href="about.php">Despre Noi</a></div>
        <div class="footer-item"><a href="contact.php">Contactează-ne</a></div>
        <div class="footer-item"><a href="#" id="gdpr-link">GDPR</a></div>
    </div>
    <p style="font-weight: bold;">MedFM &copy; 2024, Toate drepturile rezervate</p>
</footer>

<!-- Include GDPR Modal -->
<?php include 'gdpr_modal.php'; ?>  

</body>
</html>


</body>
</html>
