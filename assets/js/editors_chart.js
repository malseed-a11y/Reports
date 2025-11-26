document.addEventListener("DOMContentLoaded", function () {
  if (typeof editorsData === "undefined") {
    console.error("editorsData not found!");
    return;
  }

  console.log(editorsData);

  const canvas = document.getElementById("editorsChart");
  if (!canvas) {
    console.error("Canvas #editorsChart not found!");
    return;
  }

  const ctx = canvas.getContext("2d");

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: editorsData.labels,
      datasets: [
        {
          label: "Total Editor Activity",
          data: editorsData.totals,
          backgroundColor: "rgba(54, 162, 235, 0.6)",
          borderColor: "rgba(54, 162, 235, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: true,
          position: "top",
        },
        tooltip: {
          enabled: true,
          displayColors: false,
          callbacks: {
            title: function (tooltipItems) {
              const item = tooltipItems[0];
              const editorName = item.label;
              const total = item.formattedValue;
              return editorName + " (Total: " + total + ")";
            },
            label: function (context) {
              const index = context.dataIndex;
              const detail = editorsData.details[index];

              const posts = detail.posts || 0;
              const edits = detail.edits || 0;
              const deletes = detail.deletes || 0;

              return [
                "Posts:   " + posts,
                "Edits:   " + edits,
                "Deletes: " + deletes,
              ];
            },
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: "Number of Actions",
          },
        },
        x: {
          title: {
            display: true,
            text: "Editors",
          },
        },
      },
    },
  });
});
