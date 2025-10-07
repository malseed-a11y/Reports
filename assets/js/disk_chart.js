document.addEventListener("DOMContentLoaded", function () {
  const diskCtx = document.getElementById("diskChart");
  if (diskCtx && window.diskData) {
    new Chart(diskCtx, {
      type: "doughnut",
      data: {
        labels: [
          "Used (" + diskData.used + " GB)",
          "Free (" + diskData.free + " GB)",
        ],
        datasets: [
          {
            data: [diskData.used, diskData.free],
            backgroundColor: ["#FF6384", "#36A2EB"],
            hoverBackgroundColor: ["#FF6384", "#36A2EB"],
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: "bottom",
          },
          title: {
            display: true,
            text: "Disk Usage",
          },
        },
      },
    });
  }
});
