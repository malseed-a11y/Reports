document.addEventListener("DOMContentLoaded", function () {
  const cpuCtx = document.getElementById("cpuChart");
  if (cpuCtx && window.cpuData) {
    new Chart(cpuCtx, {
      type: "line",
      data: {
        labels: cpuData.times,
        datasets: [
          {
            label: "CPU Usage",
            data: cpuData.values.map((v) => parseFloat(v) || 0),
            borderWidth: 2,
            borderColor: "red",
            backgroundColor: "rgba(255, 0, 0, 0.1)",
            fill: true,
            tension: 0.4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
          },
          x: {
            title: {
              display: true,
              text: "Time",
            },
          },
        },
      },
    });
  }
});
