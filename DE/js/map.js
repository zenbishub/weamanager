function setPin(id, top = 460, left = 1160) {
  var mapViewer = $("#map-viewer");

  mapViewer.append(
    "<span class='pin target' id='" +
      id +
      "' style='top:" +
      top +
      "px;left:" +
      left +
      "px;'></span>"
  );
}
function getScannerData() {
  $.post(
    "class/GeoMap.php",
    {
      getscannerdata: 1,
    },
    function (response) {
      //console.log(response);
      var toArray = JSON.parse(response);

      $.each(toArray, function (i) {
        var pinData;
        setPin(toArray[i].IP.replaceAll(".", ""));
        if (toArray[i].last_posession) {
          var pinData = JSON.parse(toArray[i].last_posession);
        }

        watchPositionOnMap(toArray[i].IP, pinData);
      });
      setTimeout(() => {
        $("#loader").remove();
      }, 3500);
    }
  );
}

function watchPositionOnMap(IP, pinData) {
  var div = IP.replaceAll(".", "");

  setInterval(function () {
    $.post(
      "class/GeoMap.php",
      {
        getIPPosition: IP,
      },
      function (data) {
        //console.log(data);
        var splitData = data.split("/");
        var rfnum = "nicht belegt";
        var Firma = "";
        var Status = pinData.Status;
        var tstamp = parseInt(pinData.Anmeldung);
        var date = new Date();
        var day = date.getDate();

        if (pinData && Status < 120 && day == tstamp) {
          //console.log(tstamp);
          rfnum = "WN" + pinData.rfnum + "/";
          Firma = pinData.Firma;
        }
        $("#" + div).css({
          top: splitData[0] + "px",
          left: splitData[1] + "px",
        });

        if (splitData[0] == 0) {
          $("#" + div).addClass("d-none");
        }
        $("#" + div).attr("title", rfnum + "" + Firma);
        $("#" + div).html(
          "<span class='pinData badge badge-danger border rounded'>" +
            splitData[2] +
            "</span>"
        );
      }
    );
  }, 3000);
}
function legende() {
  $("#map-legende-box-ul").html("");
  $.post(
    "class/GeoMap.php",
    {
      getLegende: 1,
    },
    function (data) {
      //console.log(data);

      $("#map-legende-box-ul").append(data);
      openMapIframe();
      pinSelectedShow();
    }
  );
}
function openMapIframe() {
  $(".open-map-iframe").click(function () {
    var body = $("#modal-iframe-body");
    var url = $(this).attr("href");
    body
      .html("")
      .delay("1000")
      .html(`<iframe src="${url}" id="iframe-map"></iframe>`);

    $("#modal-iframe").modal("show");
    return false;
  });
}
function pinSelectedShow() {
  $(".pin-legende").click(function () {
    var pin = $(this).attr("data-index");
    var tr = $(this).parent("td").parent("tr");

    $(".target").removeClass("pin-selected").addClass("pin");
    $("tr").removeClass("bg-info");
    $("#" + pin)
      .removeClass("pin")
      .addClass("pin-selected");
    tr.addClass("bg-info");
  });
}
$(function () {
  getScannerData();
  legende();
});
