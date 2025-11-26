const CPU_POLL_INTERVAL = Math.max(Number(CpuAjax.interval) || 1000, 1000);

const CPU_MAX_POINTS = 30;

let cpuLabels = [];
let cpuValues = [];
let cpuChart = null;

document.addEventListener("DOMContentLoaded", function () {
  const cpuCtx = document.getElementById("cpuChart");

  if (!cpuCtx || typeof Chart === "undefined") {
    console.warn("cpuChart canvas or Chart.js not found");
    return;
  }

  cpuChart = new Chart(cpuCtx, {
    type: "line",
    data: {
      labels: cpuLabels,
      datasets: [
        {
          label: "CPU Usage (%)",
          data: cpuValues,
          borderWidth: 2,
          borderColor: "rgba(37, 99, 235, 1)",
          backgroundColor: "rgba(37, 99, 235, 0.15)",
          fill: true,
          tension: 0.4,
          pointRadius: 0,
          pointHoverRadius: 0,
          pointHitRadius: 0,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: false,
      scales: {
        y: {
          beginAtZero: true,
          suggestedMin: 0,
          suggestedMax: 100,
          ticks: {
            stepSize: 20,
          },
          title: {
            display: true,
            text: "CPU (%)",
          },
          grid: {
            color: "rgba(209, 213, 219, 0.6)",
          },
        },
        x: {
          title: {
            display: true,
            text: "Time (HH:MM)",
          },
          ticks: {
            maxTicksLimit: 7,
            autoSkip: true,
            maxRotation: 0,
            minRotation: 0,
          },
          grid: {
            color: "rgba(243, 244, 246, 0.8)",
          },
        },
      },
      plugins: {
        legend: {
          display: true,
          position: "top",
          labels: {
            usePointStyle: true,
          },
        },
        tooltip: {
          mode: "index",
          intersect: false,
          callbacks: {
            label: function (context) {
              const value = context.parsed.y || 0;
              return "CPU: " + value.toFixed(1) + " %";
            },
          },
        },
      },
      elements: {
        line: {
          borderCapStyle: "round",
          borderJoinStyle: "round",
        },
      },
    },
  });

  startCpuPolling();
});

function startCpuPolling() {
  setInterval(async () => {
    try {
      const formData = new FormData();
      formData.append("action", CpuAjax.action);

      const response = await fetch(CpuAjax.url, {
        method: "POST",
        body: formData,
        cache: "no-store",
      });

      if (!response.ok) throw new Error("Network error");

      const data = await response.json();
      const cpu = Number(data.cpu) || 0;

      addCpuPoint(cpu);
    } catch (e) {
      console.error("Error fetching CPU usage:", e);
    }
  }, CPU_POLL_INTERVAL);
}

function addCpuPoint(cpuValue) {
  const now = new Date();

  const label =
    now.getHours().toString().padStart(2, "0") +
    ":" +
    now.getMinutes().toString().padStart(2, "0");

  cpuLabels.push(label);
  cpuValues.push(cpuValue);

  if (cpuLabels.length > CPU_MAX_POINTS) {
    cpuLabels.shift();
    cpuValues.shift();
  }

  cpuChart.update();
}
