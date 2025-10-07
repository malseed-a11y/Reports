document.addEventListener("DOMContentLoaded", function () {
  const ramCtx = document.getElementById("ramChart");
  const ramData = window.ramData;
  const times = ramData ? ramData.times : [];
  const values = ramData ? ramData.values : [];

  if (ramCtx && ramData) {
    const formattedTimes = times.map((t) => {
      if (!t) return "";
      const parts = t.split(" ")[1];
      return parts ? parts.slice(0, 5) : "";
    });

    // Calculate min and max for y-axis
    const yMin = Math.min(...values.map((v) => parseFloat(v) || 0));
    const yMax = Math.max(...values.map((v) => parseFloat(v) || 0));

    new Chart(ramCtx, {
      type: "line",
      data: {
        labels: formattedTimes,
        datasets: [
          {
            label: "RAM Usage (MB)",
            data: values.map((v) => parseFloat(v) || 0),
            borderWidth: 1,
            borderColor: "blue",
            backgroundColor: "rgba(0,0,255,0.1)",
            fill: true,
            tension: 0.4,
            pointRadius: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            title: { display: true, text: "MB" },
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
