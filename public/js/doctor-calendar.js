document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridDay',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        locale: 'es',
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Agenda'
        },
        allDaySlot: false,
        eventMinHeight: 40,
        slotLabelFormat: {
            hour: 'numeric',
            minute: '2-digit',
            omitZeroMinute: false,
            meridiem: 'short'
        },
        eventTimeFormat: {
            hour: 'numeric',
            minute: '2-digit',
            meridiem: 'short'
        },
        events: window.DoctorCalendarConfig.eventsUrl,
        eventClick: function(info) {
            openViewModal(info.event);
        }
    });
    calendar.render();
});

function openViewModal(event) {
    let props = event.extendedProps;
    
    document.getElementById('pet_name').innerText = props.pet_name;
    
    let startObj = event.start;
    let endObj = event.end;
    
    let startTimeStr = startObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    let endTimeStr = endObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    document.getElementById('start_time').innerText = startTimeStr;
    document.getElementById('end_time').innerText = endTimeStr;
    
    let statusBadge = document.getElementById('status');
    if (props.status === 'scheduled') {
        statusBadge.className = 'badge bg-success';
        statusBadge.innerText = 'Programada';
    } else {
        statusBadge.className = 'badge bg-danger';
        statusBadge.innerText = 'Cancelada';
    }

    document.getElementById('reason').innerText = props.reason || 'Sin notas adicionales.';

    new bootstrap.Modal(document.getElementById('appointmentModal')).show();
}
