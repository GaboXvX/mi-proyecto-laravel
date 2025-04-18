//ocultar y ver la sidebar
document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.getElementById("menuToggle");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("main-content");

    menuToggle.addEventListener("click", function () {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle("active");  // Para movil
        } else {
            sidebar.classList.toggle("collapsed");  // Para escritorio
            mainContent.classList.toggle("collapsed");
        }
    });
});

//graficos
const chartConfig = {
    type: 'bar',
    data: {
      labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo'],
      datasets: [{
        label: 'Incidencias',
        data: [12, 19, 3, 5, 2],
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        }
      }
    }
  };

// Inicializar gráficos
new Chart(document.getElementById('chart1'), chartConfig);

//no permite que la fecha de inicio sea mayor que la fecha de fin
document.getElementById("fecha_inicio").addEventListener("change", function() {
  let fecha_inicio = new Date(this.value);
  let fecha_fin = new Date(document.getElementById("fecha_fin").value);

  if (fecha_fin && fecha_inicio > fecha_fin) {
      alert("La fecha de inicio no puede ser mayor que la fecha de fin.");
      this.value = "";
  }
});
//no permite que la fecha de fin sea menor que la fecha de inicio
document.getElementById("fecha_fin").addEventListener("change", function() {
  let fecha_inicio = new Date(document.getElementById("fecha_inicio").value);
  let fecha_fin = new Date(this.value);

  if (fecha_inicio && fecha_fin < fecha_inicio) {
      alert("La fecha de fin no puede ser menor que la fecha de inicio.");
      this.value = "";
  }
});

//limitar caracteres
document.getElementById('cedula').addEventListener('input', function(e) {
  if (this.value.length > 8) {
      this.value = this.value.slice(0, 8); 
  }
});

document.getElementById('nombre').addEventListener('input', function(e) {
  if (this.value.length > 11) {
      this.value = this.value.slice(0, 11); 
  }
});

document.getElementById('apellido').addEventListener('input', function(e) {
  if (this.value.length > 11) {
      this.value = this.value.slice(0, 11); 
  }
});

document.getElementById('cedula').addEventListener('blur', async function () {
    const cedula = this.value.trim();
    if (cedula.length < 8) return;

    try {
        const response = await fetch('/validar-cedula', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ cedula }),
        });

        const data = await response.json();
        if (data.exists) {
            this.classList.add('is-invalid');
            this.nextElementSibling.textContent = 'Cédula ya registrada';
        } else {
            this.classList.remove('is-invalid');
            this.nextElementSibling.textContent = '';
        }
    } catch (error) {
        console.error('Error de validación:', error);
    }
});

document.getElementById('correo').addEventListener('blur', async function () {
    const correo = this.value.trim();
    if (!correo) return;

    try {
        const response = await fetch('/validar-correo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ correo }),
        });

        const data = await response.json();
        if (data.exists) {
            this.classList.add('is-invalid');
            this.nextElementSibling.textContent = 'Correo ya registrado';
        } else {
            this.classList.remove('is-invalid');
            this.nextElementSibling.textContent = '';
        }
    } catch (error) {
        console.error('Error de validación:', error);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registroPersonaForm');
    const submitButton = document.getElementById('submitRegistroPersona');
    const modalErrorContainer = document.getElementById('modalErrorContainer');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        submitButton.disabled = true;
        modalErrorContainer.classList.add('d-none');
        modalErrorContainer.innerHTML = '';

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (!result.success) {
                handleErrors(result.errors);
                submitButton.disabled = false;
                return;
            }

            // Recargar lista de personas
            location.reload();
        } catch (error) {
            modalErrorContainer.classList.remove('d-none');
            modalErrorContainer.innerHTML = 'Ocurrió un error inesperado.';
        } finally {
            submitButton.disabled = false;
        }
    });

    function handleErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const input = document.getElementById(field);
            const feedback = input.nextElementSibling;

            input.classList.add('is-invalid');
            feedback.textContent = messages[0];
        }

        if (errors.general) {
            modalErrorContainer.classList.remove('d-none');
            modalErrorContainer.innerHTML = errors.general.join('<br>');
        }

        const firstErrorField = document.querySelector('.is-invalid');
        if (firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth' });
        }
    }
});

document.getElementById('registroPersonaForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const globalAlerts = document.getElementById('global-alerts');

    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    globalAlerts.classList.add('d-none');
    globalAlerts.textContent = '';

    try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';

        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: new FormData(form),
        });

        const data = await response.json();

        if (data.status === 'error') {
            if (data.errors) {
                Object.entries(data.errors).forEach(([field, messages]) => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.classList.add('is-invalid');
                        input.nextElementSibling.textContent = messages[0];
                    }
                });
            }
            if (data.message) {
                globalAlerts.classList.remove('d-none');
                globalAlerts.classList.add('alert-danger');
                globalAlerts.textContent = data.message;
            }
        } else if (data.status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('registroPersonaModal')).hide();
            alert(data.message);
            location.reload();
        }
    } catch (error) {
        globalAlerts.classList.remove('d-none');
        globalAlerts.classList.add('alert-danger');
        globalAlerts.textContent = 'Error de conexión. Intente nuevamente.';
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Registrar';
    }
});