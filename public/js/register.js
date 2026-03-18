function goToStep2() {
  const name = document.getElementById('name').value.trim();
  const apellido = document.getElementById('apellido').value.trim();
  const email = document.getElementById('email').value.trim();
  const telefono = document.getElementById('telefono').value.trim();
  const password = document.getElementById('password').value;
  const passwordConfirmation = document.getElementById('password_confirmation').value;

  if (!name || !apellido || !email || !telefono || !password || !passwordConfirmation) {
    alert('Por favor complete todos los campos obligatorios (*)');
    return false;
  }

  if (password !== passwordConfirmation) {
    alert('Las contraseñas no coinciden');
    return false;
  }

  if (password.length < 8) {
    alert('La contraseña debe tener al menos 8 caracteres');
    return false;
  }

  document.getElementById('hidden_name').value = name;
  document.getElementById('hidden_apellido').value = apellido;
  document.getElementById('hidden_email').value = email;
  document.getElementById('hidden_telefono').value = telefono;
  document.getElementById('hidden_password').value = password;
  document.getElementById('hidden_password_confirmation').value = passwordConfirmation;
  document.getElementById('hidden_direccion').value = document.getElementById('direccion').value;
  document.getElementById('hidden_contacto_emergencia').value = document.getElementById('contacto_emergencia').value;
  document.getElementById('hidden_tel_emergencia').value = document.getElementById('tel_emergencia').value;

  document.getElementById('step1').style.display = 'none';
  document.getElementById('step2').style.display = 'block';

  document.getElementById('step2-indicator').style.backgroundColor = '#28a745';
  document.getElementById('step2-indicator').classList.remove('bg-light', 'text-muted', 'border');
  document.getElementById('step2-text').style.color = '#28a745';
  document.getElementById('step2-text').classList.remove('text-muted');

  return false;
}

function goToStep1() {
  document.getElementById('step1').style.display = 'block';
  document.getElementById('step2').style.display = 'none';

  document.getElementById('step2-indicator').style.backgroundColor = '';
  document.getElementById('step2-indicator').classList.add('bg-light', 'text-muted', 'border');
  document.getElementById('step2-text').style.color = '';
  document.getElementById('step2-text').classList.add('text-muted');

  return false;
}