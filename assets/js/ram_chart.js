// assets/js/ram_chart.js

// نأخذ الـ interval من PHP (Settings) مع حد أدنى 1000ms
const RAM_POLL_INTERVAL = Math.max(Number(RamAjax.interval) || 1000, 1000);
// عدد النقاط القصوى في الشارت
const RAM_MAX_POINTS = 30;

let ramLabels = [];
let ramValues = [];
let ramChart = null;

document.addEventListener("DOMContentLoaded", function () {
  const ramCtx = document.getElementById("ramChart");

  if (!ramCtx || typeof Chart === "undefined") {
    console.warn("ramChart canvas or Chart.js not found");
    return;
  }

  ramChart = new Chart(ramCtx, {
    type: "line",
    data: {
      labels: ramLabels,
      datasets: [
        {
          label: "RAM Usage (%)",
          data: ramValues,
          borderWidth: 2,
          borderColor: "rgba(16, 185, 129, 1)",
          backgroundColor: "rgba(16, 185, 129, 0.15)",
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
            text: "RAM (%)",
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
            maxTicksLimit: 7, // لا يظهر أكثر من 7 تواريخ
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
              return "RAM: " + value.toFixed(1) + " %";
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

  startRamPolling();
});

function startRamPolling() {
  setInterval(async () => {
    try {
      const formData = new FormData();
      formData.append("action", RamAjax.action);

      const response = await fetch(RamAjax.url, {
        method: "POST",
        body: formData,
        cache: "no-store",
      });

      if (!response.ok) throw new Error("Network error");

      const data = await response.json();
      const ram = Number(data.ram) || 0;

      addRamPoint(ram);
    } catch (e) {
      console.error("Error fetching RAM usage:", e);
    }
  }, RAM_POLL_INTERVAL);
}

function addRamPoint(ramValue) {
  const now = new Date();

  // label مختصر HH:MM
  const label =
    now.getHours().toString().padStart(2, "0") +
    ":" +
    now.getMinutes().toString().padStart(2, "0");

  ramLabels.push(label);
  ramValues.push(ramValue);

  if (ramLabels.length > RAM_MAX_POINTS) {
    ramLabels.shift();
    ramValues.shift();
  }

  ramChart.update();
}
