function goToStep2() {
    const fields = ['name', 'apellido', 'email', 'telefono', 'password', 'password_confirmation'];
    let valid = true;

    fields.forEach(id => {
        const el = document.getElementById(id);
        if (el && !el.value.trim()) valid = false;
    });

    if (!valid) {
        alert('Por favor complete todos los campos obligatorios (*)');
        return;
    }

    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';
    
    // Cambiar color del indicador 2
    const ind2 = document.getElementById('step2-indicator');
    if(ind2) ind2.style.backgroundColor = '#28a745';
}

function goToStep1() {
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';
}