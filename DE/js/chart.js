$(function() {
    var barChartCanvas = $("#barChartOne").get(0).getContext("2d");
    new Chart(barChartCanvas, {
      type: 'bar',
      data: {
        labels: ["2013"],
        datasets: [{
          label: 'Label hier',
          data: [10],
          backgroundColor: ['cyan'],
          borderColor: ['black'],
          borderWidth: 1,
          fill: false
        }]
      }
    });
    var barChartCanvas = $("#barChartTwo").get(0).getContext("2d");
    new Chart(barChartCanvas, {
      type: 'horizontalBar',
      data: {
        labels: ["2013"],
        datasets: [{
          label: 'Label hier',
          data: [10],
          backgroundColor: ['cyan'],
          borderColor: ['black'],
          borderWidth: 1,
          fill: false
        }]
      }
    });
});