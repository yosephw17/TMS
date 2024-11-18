// Pie Chart Example
document.addEventListener("DOMContentLoaded", function () {
    fetch("/projects-chart-data")
        .then((response) => response.json())
        .then((data) => {
            initPieChart(data);
        })
        .catch((error) => console.error("Error fetching chart data:", error));
});

function initPieChart(data) {
    var ctx = document.getElementById("myPieChart");

    new Chart(ctx, {
        type: "pie",
        data: {
            labels: data.labels,
            datasets: [
                {
                    data: data.data,
                    backgroundColor: [
                        "#007bff",
                        "#dc3545",
                        "#ffc107",
                        "#28a745",
                    ],
                },
            ],
        },
    });
}
