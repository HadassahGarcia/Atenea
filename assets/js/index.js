// Abrir modal según botón
document.querySelectorAll(".sidebar button").forEach(btn => {
  btn.addEventListener("click", e => {
    const modalID = btn.dataset.modal;
    document.getElementById(modalID).classList.add("active");
  });
});

// Cerrar modal
document.querySelectorAll(".modal .close").forEach(btn => {
  btn.addEventListener("click", () => {
    btn.closest(".modal").classList.remove("active");
  });
});

// Cerrar al hacer clic fuera
document.querySelectorAll(".modal").forEach(modal => {
  modal.addEventListener("click", e => {
    if (e.target === modal) modal.classList.remove("active");
  });
});



