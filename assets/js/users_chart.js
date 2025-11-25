document.addEventListener("DOMContentLoaded", function () {
  const userCtx = document.getElementById("userChart");
  const userData = window.userData;
  const times = userData ? userData.times : [];
  const values = userData ? userData.values : [];

  if (userCtx && userData) {
    // Extract only HH:MM from timestamps
    const formattedTimes = times.map((t) => {
      if (!t) return "";
      const parts = t.split(" ")[1]; // get "HH:MM:SS"
      return parts ? parts.slice(0, 5) : ""; // take first 5 chars "HH:MM"
    });

    // Calculate min and max for y-axis
    const yMin = Math.min(...values.map((v) => parseFloat(v) || 0));
    const yMax = Math.max(...values.map((v) => parseFloat(v) || 0));

    new Chart(userCtx, {
      type: "line",
      data: {
        labels: formattedTimes,
        datasets: [
          {
            label: "user Usage (%)",
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
            title: { display: true, text: "user Usage (%)" },
            min: Math.floor(yMin * 0.9), // 10% below min
            max: Math.ceil(yMax * 1.1), // 10% above max
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
