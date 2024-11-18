// assets/demo/chart-bar-demo.js

// Function to initialize the bar chart
function initBarChart(data) {
    var ctx = document.getElementById("myBarChart");
    if (ctx) {
        var myBarChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: [
                    "January",
                    "February",
                    "March",
                    "April",
                    "May",
                    "June",
                    "July",
                    "August",
                    "September",
                    "October",
                    "November",
                    "December",
                ],
                datasets: [
                    {
                        label: "Customers Registered",
                        backgroundColor: "rgba(2,117,216,1)",
                        borderColor: "rgba(2,117,216,1)",
                        data: data,
                    },
                ],
            },
            options: {
                scales: {
                    xAxes: [
                        {
                            time: {
                                unit: "month",
                            },
                            gridLines: {
                                display: false,
                            },
                            ticks: {
                                maxTicksLimit: 12,
                            },
                        },
                    ],
                    yAxes: [
                        {
                            ticks: {
                                min: 0,
                                maxTicksLimit: 5,
                            },
                            gridLines: {
                                display: true,
                            },
                        },
                    ],
                },
                legend: {
                    display: false,
                },
            },
        });
    }
}

// Fetch data and initialize chart
document.addEventListener("DOMContentLoaded", function () {
    fetch("/customers-chart-data")
        .then((response) => response.json())
        .then((data) => {
            initBarChart(data);
        })
        .catch((error) => console.error("Error fetching chart data:", error));
});
