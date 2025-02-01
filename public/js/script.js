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