<div class="p-4">
  <div id="calendar"></div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales-all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var el = document.getElementById('calendar');
  if (!el) return;

  var cal = new FullCalendar.Calendar(el, {
    initialView: 'dayGridMonth',
    locale: @json($locale),
    headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' },
    buttonText: {
      today: @json($labels['today']),
      month: @json($labels['month']),
      week:  @json($labels['week']),
      day:   @json($labels['day']),
      list:  @json($labels['list']),
    },
    dayHeaderFormat: { weekday: 'short' },
    titleFormat:     { year: 'numeric', month: 'long' },
    events: @json($events) // ← seguro y sin escapar mal
  });

  cal.render();
});
</script>
