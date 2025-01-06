function charts2(dataY, dataX) {
  var barChartCanvas2 = $("#barChartTwo").get(0).getContext("2d");
  new Chart(barChartCanvas2, {
    //type: 'bar',
    type: "horizontalBar",
    data: {
      labels: dataY,
      datasets: [
        {
          label: "Label hier",
          data: dataX,
          backgroundColor: "cyan",
          borderColor: "black",
          borderWidth: 1,
          fill: false,
        },
      ],
    },

    options: {
      scales: {
        xAxes: [
          {
            ticks: {
              beginAtZero: true,
            },
          },
        ],
      },
    },
  });
}
function charts1(dataZollY, dataZollX) {
  var barChartCanvas1 = $("#barChartOne").get(0).getContext("2d");
  new Chart(barChartCanvas1, {
    type: "bar",
    data: {
      labels: dataZollY,
      datasets: [
        {
          label: "Speditionen mit Zollgut",
          data: dataZollX,
          backgroundColor: "cyan",
          borderColor: "black",
          borderWidth: 1,
          fill: false,
        },
      ],
    },
    options: {
      scales: {
        yAxes: [
          {
            ticks: {
              beginAtZero: true,
            },
          },
        ],
      },
    },
  });
}
function getChartData(werknummer) {
  $.post(
    "class/Statistik.php",
    { ajaxGetZollChartData: 1, werknummer: werknummer },
    function (response) {
      if (response == "keine Daten") {
        $("#no-data-1").html(response).addClass("alert alert-warning");
        return;
      }
      var jsonResponse = JSON.parse(response);
      var dataY = [];
      var dataX = [];
      for (var i = 0; i < jsonResponse.length; i++) {
        var array = jsonResponse[i];
        for (var key in array) {
          var value = array[key];
          dataY.push(key);
          dataX.push(value);
        }
      }
      charts1(dataY, dataX);
    }
  );

  $.post(
    "class/Statistik.php",
    { ajaxGetChartData: 1, werknummer: werknummer },
    function (response) {
      // console.log(response);
      // return;
      if (response == "keine Daten") {
        $("#no-data-2").html(response).addClass("alert alert-warning");
        return;
      }
      var jsonResponse = JSON.parse(response);
      var dataY = [];
      var dataX = [];
      for (var i = 0; i < jsonResponse.length; i++) {
        var array = jsonResponse[i];
        for (var key in array) {
          var value = array[key];
          dataY.push(key);
          dataX.push(value);
        }
      }
      charts2(dataY, dataX);
    }
  );
}

$(function () {
  getChartData(werknummer);
});
