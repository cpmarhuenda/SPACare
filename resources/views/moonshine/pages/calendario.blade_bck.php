<div class="p-4">
     <div id="calendar"></div>
</div>
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales-all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log(" FullCalendar JS ejecutado");

    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: @json($citas),

       eventClick: function(info) {
    info.jsEvent.preventDefault();

    const enlace = info.event.extendedProps.videollamada;

    if (enlace && enlace.startsWith('http')) {
        window.open(enlace, '_blank');
    } else {
        alert('Esta cita no tiene enlace de videollamada válido.');
    }
}
    });

    calendar.render();
});
</script>
