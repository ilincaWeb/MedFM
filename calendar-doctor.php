<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login_doctor.html");
    exit();
}
$prenume = $_SESSION['user'];
$cnp = $_SESSION['cnp'];

include 'config.php';

// Obținem medic_id pentru medicul curent
$sql = "SELECT id, TIME_FORMAT(ora_inceput, '%H:%i') as ora_inceput, TIME_FORMAT(ora_sfarsit, '%H:%i') as ora_sfarsit FROM medici WHERE CNP = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $cnp);
$stmt->execute();
$result = $stmt->get_result();
$medic_data = $result->fetch_assoc();
$medic_id = $medic_data['id'];
$ora_inceput = $medic_data['ora_inceput'];
$ora_sfarsit = $medic_data['ora_sfarsit'];
$_SESSION['medic_id'] = $medic_id;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
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
    .fc-timegrid-slot:hover {
        background-color: inherit; /* Remove hover highlight */
    }
    .fc-timegrid-axis-cushion {
        background-color: inherit; /* Prevent the axis from being highlighted */
    }
    .fc-timegrid-col:hover {
        background-color: inherit; /* Remove hover highlight */
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
      <li><a href="calendar-doctor.php"><i class="fa fa-calendar fa-sm"></i> Programările Mele</a></li>
      <li><a href="medical-records.php"><i class="fa fa-notes-medical fa-sm"></i> Istoric Medical</a></li>
    </ul>
  </div>

  <nav id="navbar" class="navsticky">
    <div class="container">
      <h1 class="logo"><a href="index.html">MedFM</a></h1>
      <ul>
        <li><a href="contact.php">Contactează-ne</a></li>
        <li><a href="profile.html">Bună, <?php echo htmlspecialchars($_SESSION['user']); ?></a></li>
      </ul>
    </div>
  </nav>

  <div class="calendar-container">
    <div id='calendar'></div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var oraInceput = "<?php echo $ora_inceput; ?>";
        var oraSfarsit = "<?php echo $ora_sfarsit; ?>";
        var medicId = "<?php echo $_SESSION['medic_id']; ?>";

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
                fetch('get_doctor_appointments.php?medic_id=' + medicId)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            },
            eventDidMount: function(info) {
                if (info.event.extendedProps.status === 'APROBATA') {
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
                if (arg.event.extendedProps.status === 'APROBATA') {
                    customHtml = `<div style="color: white; text-align: center;">${arg.event.title}</div>`;
                }
                return { html: customHtml };
            }
        });

        calendar.render();
    });
  </script>

  <footer id="secondary-footer">
    <p>MedFM &copy; 2023, All Rights Reserved</p>
  </footer>
</body>
</html>
