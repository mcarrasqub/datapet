let currentEventId = null;

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    if(!calendarEl) return;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
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
        events: globalThis.CalendarConfig.eventsUrl,
        dateClick: function(info) {
            openCreateModal(info.dateStr.split('T')[0], info.dateStr.split('T')[1]?.substring(0, 5));
        },
        eventClick: function(info) {
            openEditModal(info.event);
        }
    });
    calendar.render();
});

function openCreateModal(date = '', startTime = '') {
    currentEventId = null;
    document.getElementById('appointmentForm').reset();
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('appointmentForm').action = globalThis.CalendarConfig.storeUrl;
    
    document.getElementById('statusGroup').style.display = 'none';
    document.getElementById('btnDelete').style.display = 'none';
    document.getElementById('appointmentModalLabel').innerText = 'Nueva Cita';
    
    if (date) document.getElementById('date').value = date;
    if (startTime) {
        document.getElementById('start_time').value = startTime;
        const [h, m] = startTime.split(':');
        const endH = (Number.parseInt(h) + 1).toString().padStart(2, '0');
        document.getElementById('end_time').value = `${endH}:${m}`;
    }

    new bootstrap.Modal(document.getElementById('appointmentModal')).show();
}

function openEditModal(event) {
    currentEventId = event.id;
    const props = event.extendedProps;
    
    document.getElementById('appointmentForm').action = `/appointments/${currentEventId}`;
    document.getElementById('formMethod').value = 'PUT';
    
    document.getElementById('doctor_id').value = props.doctor_id;
    document.getElementById('pet_id').value = props.pet_id;
    
    const startObj = event.start;
    const endObj = event.end;
    
    const dateStr = startObj.getFullYear() + '-' + String(startObj.getMonth() + 1).padStart(2, '0') + '-' + String(startObj.getDate()).padStart(2, '0');
    document.getElementById('date').value = dateStr;
    
    document.getElementById('start_time').value = String(startObj.getHours()).padStart(2, '0') + ':' + String(startObj.getMinutes()).padStart(2, '0');
    document.getElementById('end_time').value = String(endObj.getHours()).padStart(2, '0') + ':' + String(endObj.getMinutes()).padStart(2, '0');
    
    document.getElementById('status').value = props.status;
    document.getElementById('reason').value = props.reason || '';

    document.getElementById('statusGroup').style.display = 'block';
    document.getElementById('btnDelete').style.display = 'inline-block';
    document.getElementById('appointmentModalLabel').innerText = 'Editar Cita';

    new bootstrap.Modal(document.getElementById('appointmentModal')).show();
}

function deleteAppointment() {
    if(confirm('¿Está seguro de eliminar físicamente esta cita? Esta acción no se puede deshacer.')) {
        const form = document.getElementById('deleteForm');
        form.action = `/appointments/${currentEventId}`;
        form.submit();
    }
}
