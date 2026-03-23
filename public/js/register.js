function selectType(type) {
    document.getElementById('registration_type').value = type;
    
    // Mostrar form general que estaba oculto
    const formWrapper = document.getElementById('form-wrapper');
    if(formWrapper) formWrapper.style.display = 'block';
    
    // Resetear estilos visuales de las cards
    const cardNew = document.getElementById('card-new-client');
    const iconNew = document.getElementById('icon-new');
    const titleNew = document.getElementById('title-new');
    
    const cardExisting = document.getElementById('card-existing-client');
    const iconExisting = document.getElementById('icon-existing');
    const titleExisting = document.getElementById('title-existing');
    
    // Indicador visual de los pasos
    const stepIndicators = document.getElementById('step-indicators');
    
    // Boton volver
    const btnVolver = document.getElementById('btn-volver');
    
    // Limpiar alertas previas
    const alerts = document.querySelectorAll('.invalid-feedback');
    alerts.forEach(el => el.style.display = 'none');
    
    // Elementos a deshabilitar si es cliente existente para que viajen como null 
    const step1Inputs = document.querySelectorAll('#step1 input, #step1 select');
    
    if (type === 'new_client') {
        // Estilos card New Client
        cardNew.classList.add('border-pet-green');
        if(iconNew) iconNew.classList.replace('text-muted', 'text-pet-green');
        if(titleNew) titleNew.classList.replace('text-muted', 'text-pet-green');
        
        // Estilos card Existing
        cardExisting.classList.remove('border-pet-green');
        iconExisting.classList.replace('text-pet-green', 'text-muted');
        titleExisting.classList.replace('text-pet-green', 'text-muted');
        
        // Mostrar step1 y ocultar step2 (Reset)
        document.getElementById('step1').style.display = 'block';
        document.getElementById('step2').style.display = 'none';
        
        // Mostrar indicadores de paso
        if(stepIndicators) {
            stepIndicators.classList.remove('d-none');
            stepIndicators.classList.add('d-flex');
        }
        
        // Mostrar boton volver para cuando avance al paso 2
        if(btnVolver) btnVolver.style.display = 'block';
        
        // Restablecer el color del paso 2 al inicial
        revertStep2Indicator();
        
        // Ocultar select de clientes en step2
        document.getElementById('client_selection_div').style.display = 'none';
        
        // Habilitar campos
        step1Inputs.forEach(input => input.disabled = false);
        
    } else {
        // Estilos card Existing
        cardExisting.classList.add('border-pet-green');
        iconExisting.classList.replace('text-muted', 'text-pet-green');
        titleExisting.classList.replace('text-muted', 'text-pet-green');
        
        // Estilos card New
        cardNew.classList.remove('border-pet-green');
        if(iconNew) iconNew.classList.replace('text-pet-green', 'text-muted');
        if(titleNew) titleNew.classList.replace('text-pet-green', 'text-muted');
        
        // Ocultar step 1 y mostrar step 2 directamente
        document.getElementById('step1').style.display = 'none';
        document.getElementById('step2').style.display = 'block';
        
        // Ocultar indicadores de paso porque ya no tienen sentido
        if(stepIndicators) {
            stepIndicators.classList.remove('d-flex');
            stepIndicators.classList.add('d-none');
        }

        
        // Ocultar boton volver porque estamos en modo standalone
        if(btnVolver) btnVolver.style.display = 'none';
        
        // Mostrar select de clientes en step2
        document.getElementById('client_selection_div').style.display = 'block';
        
        // Desactivar campos para que el Request no los exiga en Laravel
        step1Inputs.forEach(input => input.disabled = true);
    }
}

function goToStep2() {
    const regType = document.getElementById('registration_type').value;
    
    // Solo validamos step 1 si estamos creando un cliente nuevo
    if (regType === 'new_client') {
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
    }

    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';
    
    // Cambiar color del indicador 2
    const ind2 = document.getElementById('step2-indicator');
    const text2 = document.getElementById('step2-text');
    
    if(ind2) {
        ind2.classList.remove('bg-light', 'text-muted', 'border');
        ind2.classList.add('bg-pet-green', 'text-white');
    }
    if(text2) {
        text2.classList.remove('text-muted');
        text2.classList.add('text-pet-green', 'fw-bold');
    }
}

function revertStep2Indicator() {
    const ind2 = document.getElementById('step2-indicator');
    const text2 = document.getElementById('step2-text');
    
    if(ind2) {
        ind2.classList.remove('bg-pet-green', 'text-white');
        ind2.classList.add('bg-light', 'text-muted', 'border');
    }
    if(text2) {
        text2.classList.remove('text-pet-green', 'fw-bold');
        text2.classList.add('text-muted');
    }
}

function goToStep1() {
    const regType = document.getElementById('registration_type').value;
    // No podemos volver a step1 si estamos en existing_client
    if (regType === 'existing_client') {
        return;
    }
    
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';
    
    revertStep2Indicator();
}