document.addEventListener("DOMContentLoaded", function () {
  if (typeof editorsData === "undefined") {
    console.error("editorsData not found!");
    return;
  }
  console.log(editorsData);

  const ctx = document.getElementById("editorsChart");
  if (!ctx) {
    console.error("Canvas #editorsChart not found!");
    return;
  }

  new Chart(ctx.getContext("2d"), {
    type: "bar",
    data: {
      labels: editorsData.labels,
      datasets: [
        {
          label: "Number of Posts",
          data: editorsData.data,
          backgroundColor: "rgba(54, 162, 235, 0.6)",
          borderColor: "rgba(54, 162, 235, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true, position: "top" },
        tooltip: { enabled: true },
      },
      scales: {
        y: {
          beginAtZero: true,
          title: { display: true, text: "Number of Posts" },
        },
        x: { title: { display: true, text: "Editors" } },
      },
    },
  });
});
