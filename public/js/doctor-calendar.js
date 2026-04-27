document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    const calendar = new FullCalendar.Calendar(calendarEl, {
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
        events: globalThis.DoctorCalendarConfig.eventsUrl,
        eventClick: function(info) {
            openViewModal(info.event);
        }
    });
    calendar.render();
});

function openViewModal(event) {
    const props = event.extendedProps;
    
    document.getElementById('pet_name').innerText = props.pet_name;
    
    const startObj = event.start;
    const endObj = event.end;
    
    const startTimeStr = startObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    const endTimeStr = endObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    document.getElementById('start_time').innerText = startTimeStr;
    document.getElementById('end_time').innerText = endTimeStr;
    
    const statusBadge = document.getElementById('status');
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
