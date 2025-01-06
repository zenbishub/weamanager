jQuery.fn.clickToggle = function (a, b) {
  return this.on("click", function (ev) {
    [b, a][(this.$_io ^= 1)].call(this, ev);
  });
};
function openMapIframe() {
  $(".open-map-iframe").click(function () {
    var body = $("#modal-iframe-body");
    var url = $(this).attr("data");
    body
      .html("")
      .delay("1000")
      .html(`<iframe src="${url}" id="iframe-map"></iframe>`);
    $("#modal-iframe").modal("show");
  });
}
function showHideNavi() {
  $("#showhidenavi").on("mouseover", function () {
    $(".sb-topnav").animate({ top: "0px" }, function () {
      setTimeout(() => {
        $(".sb-topnav").animate({ top: "-70px" });
      }, 5000);
    });
  });
}
function getKeyCode(appPath = "weamanager/de/") {
  $(document).on("keydown", function (e) {
    var flag = false;
    if (e.code == "NumLock" || e.code == "Backquote") {
      flag = true;
      flag = false;
      var modalViewer = $("#modal-viewer");
      var uri = location.href;
      var urimodify = uri.replace(
        appPath + "maingate",
        appPath + "way-to-locations"
      );
      modalViewer.html(
        "<iframe src='" + urimodify + "' id='iframe-locations'></iframe>"
      );
      $("#viewerModal").modal("show");
      $("#iframe-locations").focus();
    }
    console.log(e.code);
    if (e.code == "NumpadMultiply" || e.code == "Numpad0") {
      $("#viewerModal").modal("hide");
    }
  });
}
function reloadLocation() {
  //reload Location every 10 Min.
  var locationStatusbar = $("#location-statusbar");
  var range = 600; // 10 Min
  var seconds = 1;
  setInterval(() => {
    if (seconds == range) {
      location.href = location.href;
      seconds = 1;
    }
    locationStatusbar.html(
      " | refresh in " + range + " / " + seconds + " Sek."
    );
    seconds++;
  }, 1000);
}
function timerForClearDaily() {
  var showzeit = $("#showzeit");
  $.post(
    "class/ajax",
    {
      runtimer: "on",
    },
    function (response) {
      showzeit.html(response);
    }
  );
}
function appAlert(url = null) {
  $("#dialog-confirm")
    .removeClass("d-none")
    .dialog({
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        ja: function () {
          $(this).dialog("close");
          location.href = url;
        },
        Nein: function () {
          $(this).dialog("close");
        },
      },
    });
}
function editRfnumData() {
  $(".edit-rfnum").click(function () {
    var rfnum = $(this).attr("alt");
    var body = $("#body-editRFnum");

    body.load("content/edit_rfnum_data", { getRFnum: rfnum }, function () {
      setTimeout(() => {
        var returnURI = $("#return-after");
        returnURI.val(location.href);
      }, 800);
    });
  });
}
function ajaxPforteMonitorEinfahrt() {
  var gateColumn = $("#gate-column-right");
  $.post("class/ajax", { ajaxPforteMonitorEinfahrt: 1 }, function (data) {
    gateColumn.html(data);
  });
}
function ajaxPforteMonitorAusfahrt() {
  var gateColumn = $("#gate-column-left");
  $.post("class/ajax", { ajaxPforteMonitorAusfahrt: 1 }, function (data) {
    gateColumn.html(data);
  });
}
function soloMonitorEinfahrt() {
  var hideMonitorEinfahrt = $("#hide-monitor-einfahrt");
  var monitorEinfahrt = $("#monitor-einfahrt");
  var monitorAusfahrt = $("#monitor-ausfahrt");

  hideMonitorEinfahrt.clickToggle(
    function () {
      monitorEinfahrt.addClass("d-none");
      monitorAusfahrt.removeClass("col-md-5").addClass("col-md-8");
    },
    function () {}
  );
}
function soloMonitorAusfahrt() {
  var hideMonitorAusfahrt = $("#hide-monitor-ausfahrt");
  var monitorEinfahrt = $("#monitor-einfahrt");
  var monitorAusfahrt = $("#monitor-ausfahrt");

  hideMonitorAusfahrt.clickToggle(function () {
    monitorAusfahrt.addClass("d-none");
    monitorEinfahrt.removeClass("col-md-5").addClass("col-md-8");
  });
}
function soloMonitorWerksverkehr() {
  var hideMonitorWerksverkehr = $("#hide-monitor-werksverkehr");
  var monitorWerksverkehr = $("#monitor-werksverkehr");
  var monitorEinfahrt = $("#monitor-einfahrt");
  var monitorAusfahrt = $("#monitor-ausfahrt");

  hideMonitorWerksverkehr.clickToggle(function () {
    monitorWerksverkehr.addClass("d-none");
    monitorEinfahrt.removeClass("col-md-5").addClass("col-md-6");
    monitorAusfahrt.removeClass("col-md-5").addClass("col-md-6");
  });
}
function controlButtons() {
  var hideButtons = $("#hide-control-buttons");
  var showButtons = $("#show-control-buttons");
  hideButtons.click(function () {
    $("#gate-control-buttons").addClass("d-none");
  });
  showButtons.click(function () {
    $("#gate-control-buttons").removeClass("d-none");
    $("#monitor-werksverkehr").removeClass("d-none");
    $("#monitor-einfahrt").removeClass("col-md-6").addClass("col-md-5");
    $("#monitor-ausfahrt").removeClass("col-md-6").addClass("col-md-5");
  });
}
function werksVerkehr() {
  $("#werksverkehr-button").hover(
    function () {
      title = $(this);
      $.post("class/publicAjax", { getRFID: 1 }, function (data) {
        title.attr("title", data);
        $(".column-werksverkehr").click(function () {
          var value = $(this).attr("alt");
          var rfnum = $(this).attr("title");
          var layout = $(this).attr("data");
          var dinamictext = $("#dinamic-text");
          var url = "";
          if (value == "werksverkehr") {
            url =
              "class/action?add_to_prozess_werksverkehr=werksverkehr&return=maingate?controls=" +
              layout +
              "&rfnum=" +
              rfnum;
            dinamictext.text("Werksverkehr zur Einfahrt hinzufügen?");
          }
          appAlert(url);
        });
      });
    },
    function () {
      title = $(this);
      title.attr("title", "");
    }
  );
}
function ajaxWerksVerkehrCounter() {
  var werksverkehrButton = $("#werksverkehr-button");
  $.post("class/publicAjax", { ajaxWerksVerkehrCounter: 1 }, function (data) {
    werksverkehrButton.attr("title", data);
    werksVerkehr();
  });
}
function werksverkehrBox() {
  var werksverkehrBox = $("#werksverkehr-box");
  $.post("class/publicAjax", { ajaxWerksVerkehrBox: 1 }, function (response) {
    werksverkehrBox.html(response);
  });
}
function ajaxPforteMonitor(iPing = 0) {
  var gateColumnRightBottom = $("#gate-column-right-bottom");
  var layoutVariante = gateColumnRightBottom.attr("alt");
  $.post(
    "class/publicAjax",
    {
      ajaxMonitorPforte: 1,
      layoutVariante: layoutVariante,
    },
    function (response) {
      gateColumnRightBottom.html(response);
      einfahtAllow();
      openSendBox();
      openMessageBoxEvoChat();
      evoChatLastShowMessage(iPing);
      checkIfScannerOnline();
      editRfnumData();
      openMapIframe();
    }
  );
}
function autoChangeStatusWerksverkehr() {
  //300 - 5 Minuten
  $.post("class/publicAjax", {
    autoChangeStatusWerksverkehr: 1,
    range: 300,
  });
}
function loadIframe() {
  $("#btn-open-locations").click(function () {
    var appPath = $(this).attr("href");
    var modalViewer = $("#modal-viewer");
    var uri = location.href;
    var urimodify = uri.replace(
      appPath + "maingate",
      appPath + "way-to-locations"
    );
    modalViewer.html(
      "<iframe src='" + urimodify + "' id='iframe-locations'></iframe>"
    );
  });
}
function einfahtAllow() {
  $(".change-status").click(function () {
    var data = $(this).attr("alt").split(":");
    var rfnum = data[0];
    location.href =
      "class/action?add_to_prozess=1&rfnum=" + rfnum + "&redirect=maingate";
  });
}
function openSendBox() {
  $(".opensendbox").click(function () {
    var elem = $(this).attr("alt").split("%20");
    var returnURI = $("#return");
    var rfnum = $("#rfnum");
    var kfznum = $("#kfznum");
    var werknummer = $("#werknummer");
    var header = $("#header");
    rfnum.val(elem[0]);
    kfznum.val(elem[1]);
    werknummer.val(elem[3]);
    header.text("Nachricht an: " + elem[1] + ", " + elem[2]);
    returnURI.val("maingate?controls=custom");
  });
}
function openMessageBoxEvoChat() {
  $("#send_to_evochat,.message-item").click(function () {
    var intv;
    var split = $(this).attr("data").split("&");
    var bmi_nummer = split[1];
    var absender = $("#Absender");
    var header = $("#header-evochat");
    var target = $("#target");
    var sendto = $("#send-to-evochat");
    var returnURI = $("#returnURI");
    var verlauf = $("#verlauf");
    returnURI.val("");
    verlauf.html("");
    $.post(
      "class/ajax",
      {
        getBMINummForChatList: 1,
        callChat: "pforte",
      },
      function (data) {
        $(".pingsound").remove();
        header.html(data);
        $("#select-empfaenger").on("change", function () {
          bmi_nummer = $(this).get(0).value;
          sendto.val(bmi_nummer);
          target.val(bmi_nummer);
          absender.val("PFORTE");
          verlauf.html(
            "<div class='p-4 text-center text-primary'><span class='spinner-border spinner-border-sm text-primary' role='status' aria-hidden='true'></span> Chat loading...</div>"
          );
          syncMessages(bmi_nummer, 0);
        });
      }
    );
    sendto.val(bmi_nummer);
    target.val(bmi_nummer);
    syncMessages(bmi_nummer);
    $("#evochatModal").modal("show");
    $(".modal-backdrop").remove();
    $("#btn-chat-close").click(function () {
      verlauf.html("close");
      clearInterval(intv);
    });
  });
}
function syncMessages(bmi_nummer, count = 0) {
  var verlauf = $("#verlauf");
  $.post(
    "class/ajax",
    {
      getChatVerlauf: 1,
      user: bmi_nummer,
      empfaenger: "",
      absender: "PFORTE",
      returnURI: "maingate",
    },
    function (data) {
      verlauf.html(data);
      if (count == 0) {
        setTimeout(() => {
          var scrollY = verlauf.height();
          $("#evochat-verlauf").scrollTop(scrollY);
        }, 400);
      }

      $(".open-attachment").click(function () {
        var src = $(this).attr("alt");
        $("body").append(
          "<div id='overlay-attachment'><span id='close-attachment'><i class='fas fa-times'></i></span><div id='attachment-frame'><img class='img-fluid' src='" +
            src +
            "'></div></div>"
        );
        $("#close-attachment").click(function () {
          $("#overlay-attachment").remove();
        });
      });
    }
  );
}
function evoChatLastShowMessage(iPing) {
  var elem = $("#getmessage");
  var werknummer = elem.attr("title");
  var showmessage = $("#getmessage-text");
  if (elem.length > 0) {
    $.post(
      "class/publicAjax",
      {
        evoChatLastShowMessage: "pforte",
        werknummer: werknummer,
        empfaenger: "PFORTE",
      },
      function (response) {
        if (response) {
          elem.removeClass("d-none");
          var pinging = "";
          console.log(iPing);
          if (iPing == 1 || iPing == 5) {
            pinging =
              "<embed src='assets/sound/pinging.mp3' class='pingsound' style='opacity:0; height:.1em; width:.1em;'></embed>";
          }
          showmessage.html(response + pinging);
          openMessageBoxEvoChat();
          $("#close-getmessage").click(function () {
            var key = showmessage.children(".message-item").attr("data-index");
            $.post(
              "class/publicAjax",
              {
                evochatmessagereaded: key,
              },
              function () {
                location.reload();
              }
            );
          });
        }
      }
    );
  }
}
function gateSchlagBaum(ip) {
  $(".controls").click(function () {
    var elem = $(this);
    var button = $(this).attr("alt");
    var text = $(this).text();
    var action, area;
    switch (button) {
      case "e-open":
        action = "on";
        switcher = 0;
        area = "einfahrt";

        break;
      case "e-close":
        action = "off";
        switcher = 0;
        area = "einfahrt";
        break;
      case "a-open":
        action = "on";
        switcher = 0;
        area = "ausfahrt";
        break;
      case "a-close":
        action = "off";
        switcher = 0;
        area = "ausfahrt";
        break;
    }
    $(".controls").addClass("btn-success").removeClass("btn-danger");
    $.post(
      "class/Proxi",
      {
        status: "device",
        device: "Devicename",
        ip: ip,
        relay: "relay",
        area: area,
        action: switcher + "?turn=" + action,
      },
      function (data) {
        elem.removeClass("btn-success").addClass("btn-danger");
        setTimeout(() => {
          if (button == "e-close" || button == "a-close") {
            elem.addClass("btn-success").removeClass("btn-danger");
          }
        }, 2000);
      }
    );
  });
}
function scannerinfo() {
  $("#btn-open-scannerinfo").click(function () {
    var body = $("#body-scannerInfo");
    body.load("content/scannerinfo", function () {
      // from scanner.js
      checkIfScannerOnline();
    });
  });
}

$(function () {
  var iPing = 0;
  $("#gate-control-buttons").draggable();
  getKeyCode();
  showHideNavi();
  reloadLocation();
  ajaxPforteMonitorEinfahrt();
  ajaxPforteMonitorAusfahrt();
  ajaxPforteMonitor();
  soloMonitorEinfahrt();
  soloMonitorAusfahrt();
  soloMonitorWerksverkehr();
  controlButtons();
  werksVerkehr();
  scannerinfo();
  autoChangeStatusWerksverkehr();
  setInterval(ajaxPforteMonitorEinfahrt, 3000);
  setInterval(ajaxPforteMonitorAusfahrt, 3000);
  setInterval(() => {
    if (iPing == 10) {
      iPing = 0;
    }
    ajaxPforteMonitor(iPing);
    iPing++;
    werksverkehrBox();
  }, 10000);
  setInterval(autoChangeStatusWerksverkehr, 1000);
  setInterval(ajaxWerksVerkehrCounter, 3000);
  setInterval(timerForClearDaily, 1000);
  loadIframe();
});
