const sidebar = document.querySelector('.sidebar');
const toggleSidebarBtn = document.getElementById('menuToggle');

toggleSidebarBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    sidebar.classList.toggle('active');
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

//Para que el sub menu se mantenga abierto
document.addEventListener("DOMContentLoaded", function () {
  const submenu = document.getElementById("layouts");
  const toggleLink = document.querySelector('[href="#layouts"]');
  const chevronIcon = toggleLink.querySelector(".right-icon i");
  const currentPath = window.location.pathname; // Obtiene la URL actual
  const submenuLinks = document.querySelectorAll("#layouts .nav-link");

  // Detectar si la página actual está dentro del submenú
  let isSubmenuActive = false;
  submenuLinks.forEach(link => {
      if (link.getAttribute("href") === currentPath) {
          link.classList.add("active"); // Resaltar el enlace actual
          isSubmenuActive = true;
      }
  });

  if (isSubmenuActive) {
      submenu.classList.add("show");
      chevronIcon.classList.replace("bi-chevron-down", "bi-chevron-up");
  }

  // Evento para abrir/cerrar manualmente
  toggleLink.addEventListener("click", function () {
      if (submenu.classList.contains("show")) {
          submenu.classList.remove("show");
          localStorage.setItem("submenuOpen", "false");
          chevronIcon.classList.replace("bi-chevron-up", "bi-chevron-down");
      } else {
          submenu.classList.add("show");
          localStorage.setItem("submenuOpen", "true");
          chevronIcon.classList.replace("bi-chevron-down", "bi-chevron-up");
      }
  });
});

//no permite que la fecha de inicio sea mayor que la fecha de fin
document.getElementById("fecha_inicio").addEventListener("change", function() {
  let fecha_inicio = new Date(this.value);
  let fecha_fin = new Date(document.getElementById("fecha_fin").value);

  if (fecha_fin && fecha_inicio > fecha_fin) {
      alert("La fecha de inicio no puede ser mayor que la fecha de fin.");
      this.value = "";
  }
});

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
