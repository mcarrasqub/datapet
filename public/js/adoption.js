document.addEventListener('DOMContentLoaded', function() {
  const adoptionModal = document.getElementById('adoptionModal');
  
  if (adoptionModal) {
    adoptionModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const petId = button.getAttribute('data-pet-id');
      const petName = button.getAttribute('data-pet-name');
      
      const modalPetId = adoptionModal.querySelector('#modalPetId');
      const modalPetName = adoptionModal.querySelector('#modalPetName');
      
      modalPetId.value = petId;
      modalPetName.textContent = petName;
    });
  }
});