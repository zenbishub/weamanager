function openModalLadeporzess() {
  $(".open-modal-ladeprozess").click(function () {
    var body = $("#modal-body-offlineladePorzess");
    var split = $(this).attr("alt").split("&");
    var target = split[0];
    var werk = split[1];
    body.load(
      "content/form-offline-lade-prozess",
      { target: target, werk: werk },
      function () {}
    );
  });
}
function openModalBoxladen() {
  $(".open-modal-boxladen").click(function () {
    var body = $("#modal-body-confirmLadeProzess");
    var title = $("#Label-confirmLadeProzess");
    var split = $(this).attr("alt").split("&");
    var BoxNummer = split[0];
    var BB = split[1];
    var lkw = split[2];
    title.text("Bitte bestätigen");
    body.html(
      "<span class='h3'>" +
        BoxNummer +
        " " +
        BB +
        " </span><br><span class='h4'>wird in den  Aufleger/Anhänger " +
        lkw +
        " verladen?</span><div class='card-footer text-end'><button id='proz-btn' class='btn btn-primary'>ok</button></div>"
    );
    $("#proz-btn").click(function () {
      var uri =
        "class/publicExtern?offline_load_box=" +
        BoxNummer +
        "&BB=" +
        BB +
        "&lkw=" +
        lkw;
      location.href = uri;
    });
  });
}
function openModalConfirmBeladen() {
  $(".open-modal-confirm-beladen").click(function () {
    var body = $("#modal-body-confirmBeladen");
    var title = $("#Label-confirmBeladen");
    var auftrag = $(this).attr("alt");
    title.text("Bitte bestätigen");
    body.html(
      "<span class='h3'>Ist das Verladen fertig?</span><div class='card-footer text-end'><button id='proz-btn' class='btn btn-primary'>ja</button></div>"
    );
    $("#proz-btn").click(function () {
      $.post(
        "class/publicExtern",
        { auftrag_fertig: auftrag },
        function (data) {
          location.reload();
        }
      );
    });
  });
}
function missedLkw() {
  $(".missed-lkw").click(function () {
    alert(
      "Bitte zuerst Aufleger/Anhänger Kennzeichen in den die Boxen verladen werden eingeben"
    );
  });
}
function openSeqLager() {
  $(".open-seqLager").click(function () {
    var BB = $(this).attr("alt");
    var body = $("#modal-body-openSeqLagerFull");
    var title = $("#Label-openSeqLagerFull");
    $.post(
      "class/publicExtern",
      { openSeqlagerFull: 1, searchBB: BB },
      function (htmlResponse) {
        body.html(htmlResponse).addClass("p-0");
        title.html("Übersicht Seq-Lager");
      }
    );
  });
}
function openHofLager() {
  $(".open-hoflager").click(function () {
    var BB = $(this).attr("alt");
    var body = $("#modal-body-startBeladen");
    var title = $("#Label-startBeladen");
    $.post(
      "class/publicExtern",
      { openHoflagerBeladen: 1, searchBB: BB },
      function (htmlResponse) {
        body.html(htmlResponse);
        title.html("In Hoflager buchen");
        openReader();
        ajaxBuchen("inhoflager_buchen");
      }
    );
  });
}
function openStartBeladen() {
  $(".open-start-beladen, #reset-frame").click(function () {
    var body = $("#modal-body-startBeladen");
    var title = $("#Label-startBeladen");
    $.post(
      "class/publicExtern",
      { openStartBeladen: 1 },
      function (htmlResponse) {
        body.html(htmlResponse);
        title.html("");
        var input = $("#scannLT");
        // var scans = $("#first-scannLT-row");

        setInterval(function () {
          var value = input.val();
          var valLength = value.length;
          // console.log(valLength);

          if (valLength >= 9 && valLength <= 11) {
            $(`
            <div class='row'>
            <div class="col-sm-12 form-group mb-1 ps-0 pe-0">
              <input type="search" class="form-control" name="versand_boxnummer[]" value="${value}" readonly>
            </div></div>`).insertAfter(input);
            input.val("").focus();
          }
        }, 1000);
        openReader();
        filterForTruckLoading();
        ajaxBuchen("versand_buchen");
      }
    );
  });
}
function sequenzlagerPlaceItems() {
  $(".sequenzlager-place-items").click(function () {
    let BD = $("#boxdata").text();
    var platzname = $(this).attr("alt");

    var title = $("#Label-confirmPlaceInseqLager");
    var body = $("#modal-body-confirmPlaceInseqLager");
    if (BD == "undefined" || BD == "") {
      return;
    }
    $.post(
      "class/publicExtern",
      { openPlatzBelegung: platzname, boxdata: BD },
      function (htmlResponse) {
        console.log(BD);
        body.html(htmlResponse);
        title.text("SeqLager-Platz belegen?");
        $("#confirmPlaceInseqLager").modal("show");
        return;
      }
    );
  });
}
function filterForTruckLoading() {
  var iFrame = $("#iframe-lkw-loading");
  var selLKW = $("#versand_lkw");

  $("#versand_werk").on("change", function () {
    selLKW.val("A").change();
    var selWerkVal = $(this).get(0).value;

    var selLKWVal = selLKW.get(0).value;
    iFrame.attr(
      "src",
      "https://sevom011n030.bus.corpintra.net/NU/shipping/?open=truckload&truckID=" +
        selWerkVal +
        "_" +
        selLKWVal
    );

    selLKW.on("change", function () {
      selLKWVal = $(this).get(0).value;
      iFrame.attr(
        "src",
        "https://sevom011n030.bus.corpintra.net/NU/shipping/?open=truckload&truckID=" +
          selWerkVal +
          "_" +
          selLKWVal
      );
    });
  });
}
function ajaxBuchen(div) {
  $("#" + div).submit(function () {
    var sendBtn = $("#alert-" + div);

    var inputBox = $("#scannLT");
    var iFrameSrc = $("#iframe-lkw-loading").attr("src");

    $(this).ajaxSubmit({
      beforeSubmit: function () {
        prozessing();
      },
      uploadProgress: function () {
        sendBtn.html(
          '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        );
      },
      resetForm: false,
      error: function () {
        alert("Fehler beim senden");
      },
      success: function (response) {
        console.log(response);
        inputBox.val("").removeAttr("style").focus();

        if (div == "versand_buchen") {
          if (response != "2") {
            $("#success-alert-" + div)
              .removeClass("d-none")
              .html(response);
            $("#iframe-lkw-loading").attr("src", iFrameSrc);
          }
          $("#spinner").remove();
          return;
        }
        if (response != 1) {
          $("#success-alert-" + div).removeClass("d-none");
          $("#success-alert-" + div)
            .addClass("bg-danger")
            .addClass("text-light")
            .removeClass("bg-success")
            .text(response);
          $("#spinner").remove();
        }
        if (response == 1) {
          //var text = $("#success-alert-" + div).attr("alt");
          $("#success-alert-" + div)
            .text("lade...")
            .removeClass("d-none")
            .removeClass("bg-danger");
          $("#success-alert-" + div)
            .addClass("bg-success")
            .addClass("text-light")
            .text("erfolgreich gebucht");
          $("#spinner").remove();
          //$("#iframe-lkw-loading").attr("src", iFrameSrc);
        }
      },
    });
    return false;
  });
}

function ajaxGetBoxInhalt(div) {
  $("#" + div).submit(function () {
    var sendBtn = $("#alert-" + div);

    $(this).ajaxSubmit({
      beforeSubmit: function () {
        prozessing();
      },
      uploadProgress: function () {
        sendBtn.html(
          '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        );
      },
      resetForm: true,
      error: function () {
        alert("Fehler beim senden");
      },
      success: function (response) {
        var boxnummer_content = $("#boxnummer_content");
        boxnummer_content.html(response);
        $("#spinner").remove();
      },
    });
    return false;
  });
}

function openReader() {
  $(".open-reader").click(function () {
    var value = $(this).attr("alt");
    $("body").append(
      "<div id='overlay'><span id='close-qr-reader'><i class='ti ti-close'><i></span><div class='qr-reader' id='qr-reader'></div></div>"
    );

    $("#qr-reader").load(
      "content/readBBbyscann",
      { scannrequire: value },
      function () {
        $("#close-qr-reader").click(function () {
          html5QrCode.stop();
          $("#overlay").remove();
        });
      }
    );
  });
}
function requestVersandList() {
  $("#request-versand-list").click(function () {
    var datum = $(this).attr("data-index");
    prozessing();
    $.post(
      "/NU/offline/class/action",
      {
        versand_generieren: 1,
        Datum: datum,
      },
      function (response) {
        //console.log(response);
        $("#spinner").remove();
      }
    );
  });
}
function openCheckInhalt() {
  $(".open-check-inhalt").click(function () {
    var body = $("#modal-body-checkInhalt");
    body.load("content/qrcode-boxchecker", {}, function () {
      ajaxGetBoxInhalt("form-find_boxcontent");
    });
  });
}
$(function () {
  requestVersandList();
  openModalLadeporzess();
  openModalBoxladen();
  openModalConfirmBeladen();
  missedLkw();
  //openSeqLager();
  openStartBeladen();
  openHofLager();
  openCheckInhalt();
});
