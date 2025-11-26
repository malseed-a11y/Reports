document.addEventListener("DOMContentLoaded", function () {
  const diskCtx = document.getElementById("diskChart");

  if (!diskCtx) {
    console.error("diskChart canvas not found");
    return;
  }

  if (typeof diskData === "undefined") {
    console.error("diskData is not defined");
    return;
  }

  const themes = Number(diskData.themes) || 0;
  const plugins = Number(diskData.plugins) || 0;
  const uploads = Number(diskData.uploads) || 0;
  const admin = Number(diskData.admin) || 0;

  const labels = [
    "Themes (" + themes.toFixed(2) + " MB)",
    "Plugins (" + plugins.toFixed(2) + " MB)",
    "Uploads (" + uploads.toFixed(2) + " MB)",
    "Admin (" + admin.toFixed(2) + " MB)",
  ];

  const data = [themes, plugins, uploads, admin];

  new Chart(diskCtx, {
    type: "doughnut",
    data: {
      labels: labels,
      datasets: [
        {
          data: data,
          backgroundColor: [
            "#FF6384", // themes
            "#36A2EB", // plugins
            "#FFCE56", // uploads
            "#4BC0C0", // admin
          ],
          hoverBackgroundColor: [
            "#FF6384",
            "#36A2EB",
            "#FFCE56",
            "#4BC0C0",
            "#9966FF",
          ],
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
          text: "Disk Usage (Folders + Free Space)",
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              let label = context.label || "";
              if (label) {
              }
              return label;
            },
          },
        },
      },
    },
  });
});
