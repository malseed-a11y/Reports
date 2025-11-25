document.addEventListener("DOMContentLoaded", function () {
  const ramCtx = document.getElementById("ramChart");
  const ramData = window.ramData;

  const times = ramData ? ramData.labels : [];
  const values = ramData ? ramData.data : [];

  if (ramCtx && ramData) {
    const formattedTimes = times.map((t) => {
      if (!t) return "";
      const parts = t.split(" ")[1];
      return parts ? parts.slice(0, 5) : "";
    });

    const yMin = Math.min(...values.map((v) => parseFloat(v) || 0));
    const yMax = Math.max(...values.map((v) => parseFloat(v) || 0));

    new Chart(ramCtx, {
      type: "line",
      data: {
        labels: formattedTimes,
        datasets: [
          {
            label: "ram Usage (MB)",
            data: values.map((v) => parseFloat(v) || 0),
            borderWidth: 2,
            borderColor: "red",
            backgroundColor: "rgba(255,0,0,0.1)",
            fill: true,
            tension: 0.4,
            pointRadius: 2,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            title: { display: true, text: "ram Usage (MB)" },
            min: Math.floor(yMin * 0.9),
            max: Math.ceil(yMax * 1.1),
          },
          x: {
            title: { display: true, text: "Time (HH:MM)" },
          },
        },
        plugins: {
          tooltip: {
            mode: "index",
            intersect: false,
          },
          legend: {
            display: true,
            position: "top",
          },
        },
      },
    });
  }
});
