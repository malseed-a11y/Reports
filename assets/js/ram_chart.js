document.addEventListener("DOMContentLoaded", function () {
  const ramCtx = document.getElementById("ramChart");
  if (ramCtx && window.ramData) {
    new Chart(ramCtx, {
      type: "line",
      data: {
        labels: ramData.times,
        datasets: [
          {
            label: "RAM Usage (MB)",
            data: ramData.values.map((v) => parseFloat(v) || 0),
            borderWidth: 2,
            borderColor: "blue",
            backgroundColor: "rgba(0, 0, 255, 0.1)",
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
            title: {
              display: true,
              text: "MB",
            },
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
