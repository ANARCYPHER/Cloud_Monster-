

let VisitorsByMonthly_BarChart = new ApexCharts(document.getElementById('chart-visits-my-monthly'), {

    chart: {
        type: "area",
        fontFamily: 'inherit',
        height: 240,
        parentHeightOffset: 0,
        toolbar: {
            show: false,
        },
        animations: {
            enabled: false
        }
    },
    dataLabels: {
        enabled: false,
    },
    fill: {
        opacity: .16,
        type: 'solid'
    },
    stroke: {
        width: 2,
        lineCap: "round",
        curve: "smooth",
    },
    series: [],
    grid: {
        padding: {
            top: -20,
            right: 0,
            left: -4,
            bottom: 12
        },
        strokeDashArray: 4,
        xaxis: {
            lines: {
                show: true
            }
        },
    },
    xaxis: {
        labels: {
            padding: 0
        },
        tooltip: {
            enabled: false
        },
        axisBorder: {
            show: false,
        },
        type: 'datetime',
    },
    yaxis: {
        labels: {
            padding: 4
        }
    },
    colors: ["#206bc4","#f66d9b"],
    noData: {
        text: 'Loading...'
    },
    legend: {
        show: true,
    }
});
function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}


if($("#chart-live-visits").length === 1){
    var VisitorsLive_BarChart = new ApexCharts(document.getElementById('chart-live-visits'), {
        chart: {
            type: "line",
            fontFamily: 'inherit',
            height: 240,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
            animations: {
                enabled: false
            },
        },
        markers: {
            size: 0
        },
        animations: {
            enabled: true,
            easing: 'linear',
            dynamicAnimation: {
                speed: 1000
            }
        },
        fill: {
            opacity: 1,
        },
        stroke: {
            lineCap: "round",
            curve: "smooth",
        },
        series: [],
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: 8
            },
            strokeDashArray: 4,
        },
        xaxis: {
            type: 'datetime',
            labels: {
                padding: 0,
                formatter: function(value, timestamp) {
                    return new Date(value).toLocaleTimeString();
                }
            },

            tooltip: {
                enabled: false
            }
        },
        yaxis: {
            labels: {
                padding: 4
            },
        },
        labels: [],
        colors: ["#206bc4"],
        legend: {
            show: false,
        },
        noData: {
            text: 'Loading...'
        },
    });
    document.addEventListener("DOMContentLoaded", function () {
        window.ApexCharts && VisitorsLive_BarChart.render();
    });
}


// @formatter:on
document.addEventListener("DOMContentLoaded", function() {
    let visitorsMap = $('#visitors-map');
    if( visitorsMap.length === 1){
        visitorsMap.vectorMap({
            map: 'world_en',
            backgroundColor: 'transparent',
            color: 'rgba(120, 130, 140, .1)',
            borderColor: 'transparent',
            scaleColors: ["#d2e1f3", "#206bc4"],
            normalizeFunction: 'polynomial',
            values: (chart_data = {}),
            onLabelShow: function (event, label, code) {
                if (chart_data[code] > 0) {
                    label.append(': <strong>' + chart_data[code] + '</strong>');
                }
            },
        });
    }
});
// @formatter:off


document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts && VisitorsByMonthly_BarChart.render();
});
