jQuery.fn.clickToggle = function (a, b) {
  return this.on("click", function (ev) {
    [b, a][(this.$_io ^= 1)].call(this, ev);
  });
};
function setOnlineUser() {
  $.post(
    "class/ajax",
    {
      setOnlineUser: 1,
    },
    function (data) {
      //console.log(data);
    }
  );
}
function infomationsAndUpdates() {
  $("#informations").click(function () {
    let sign = prompt(
      "nur für Administrator zugänglich. Bitte Passwort eingeben",
      ""
    );
    if (sign != 8978) {
      return;
    }
    if (sign == 8978) {
      var body = $("#updates-body");
      $("#updates").modal("show");
      body.load("content/apkupdates");
    }
  });
}
function openLiveViewCam() {
  $("a#live-view").clickToggle(
    function () {
      $("#frame-live-view").html(
        `<iframe src="content/capture" id="liveViewiframe"></iframe>`
      );
      $("#frame-live-view-row").removeClass("d-none");
      $("#liveViewiframe").bind("load", function () {
        $(this)
          .contents()
          .find("[id=camvideo]")
          .clickToggle(
            function () {
              $("#liveViewiframe").addClass("zoom-cam-live");
              $("#frame-live-view")
                .removeClass("col-3")
                .addClass("wea-zoom-live-view");
            },
            function () {
              $("#liveViewiframe").removeClass("zoom-cam-live");
              $("#frame-live-view")
                .addClass("col-3")
                .removeClass("wea-zoom-live-view");
            }
          );
        $("#close-live-view").click(function () {
          $("#frame-live-view-row").addClass("d-none");
          $("#frame-live-view").html("");
        });
      });
      return false;
    },
    function () {
      $("#frame-live-view-row").addClass("d-none");
      $("#frame-live-view").html("");
      $("#frame-live-view").addClass("col-3").removeClass("wea-zoom-live-view");
    }
  );
}
function checkConnectionToWiFi() {
  var elem = $("#wifi-status");
  setInterval(function () {
    elem.removeClass("text-success").addClass("text-danger");
    $.post("class/publicAjax", { ConnectionToWiFi: 1 }, function (response) {
      //console.log(response);

      if (response == "stable") {
        elem
          .removeClass("text-light")
          .removeClass("text-danger")
          .addClass("text-success");
      }
    });
  }, 5000);
}
function prozessing() {
  $("body").append(
    "<div id='spinner'><div class='box-middle'><div class='spinner-border text-light' role='status'><span class='visually-hidden'>Loading...</span></div></div></div>"
  );
}
function openScannWindow() {
  $(".take-scann-from-document").click(function () {
    var waitnumber = $(this).attr("alt");
    var host = "53.16.250.30:30447";

    window.open(
      "http://" + host + "/scanaction.html?waitnumber=" + waitnumber,
      "nW",
      "width=500 height=800",
      "_blank"
    );
  });
}

function checkMonitorWidth() {
  var screenWidth = $(window).width();
  var hideBYterminal = $(".hide-by-terminal");
  var gadget = $("#gadget");
  var add_to_order = $("#add_to_order");
  var col_qrcode = $("#col-qrcode");
  var col_qrcode_row = $("#col-qrcode-row");
  col_qrcode_row.addClass("bg-opacity-45");
  col_qrcode.html(
    '<div type="button" class="db-petrol20 text-light p-2 rounded-3 text-center border-2" id="readQRModal-btn" data-bs-toggle="modal" data-bs-target="#readQRModal"><i class="fas fa-qrcode"></i></div>'
  );

  if (screenWidth > 520) {
    gadget.html("am Terminal");
    add_to_order.val("terminal");
    hideBYterminal.addClass("d-none");
    col_qrcode.html("");
    col_qrcode_row.removeClass("bg-opacity-45");
  }
}
function chooseLanguage() {
  $(".choose-language").click(function () {
    var lang = $(this).attr("title");
    var return_uri = $(this).attr("alt");
    prozessing();
    location.href =
      "class/action?set_language=" + lang + "&return_uri=" + return_uri;
  });
}
function sendFormData() {
  $("#formular-lieferung-erfassen, .prozessing").on("submit", function () {
    prozessing();
  });
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
function basicAlert() {
  $("#dialog-confirm")
    .removeClass("d-none")
    .dialog({
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        OK: function () {
          $(this).dialog("close");
        },
      },
    });
}
function checkZollgut() {
  $("[name=Zollgut]").click(function () {
    var value = $(this).val();
    var boxLegitimation = $("#box-legitimation");
    var boxZollgutconfirm = $("#box-zollgutconfirm");
    var radio_zollgut_freigabe_checked = $("#radio_zollgut_freigabe_checked");
    var successOutlined = $("#success-outlined-zoll");
    if (value == "JA") {
      boxZollgutconfirm.removeClass("d-none");
      //radio_zollgut_freigabe_checked.prop("required", true);
      //successOutlined.prop("required", true);
    }
    if (value == "NEIN") {
      boxZollgutconfirm.addClass("d-none");
      radio_zollgut_freigabe_checked.prop("required", false);
      successOutlined.prop("required", false);
    }
  });
}
function selectLegitimation() {
  $("#FRZTyp").on("change", function () {
    var pass = $("#legitimation-pass");
    var licens = $("#legitimation-licens");
    var value = $(this).get(0).value;
    if (value == "Ohne FRZ") {
      pass.removeClass("d-none");
      licens.addClass("d-none");
    }
    if (value != "Ohne FRZ") {
      pass.addClass("d-none");
      licens.removeClass("d-none");
    }
  });
}
function isConfirmLegitimation(interval1) {
  var isContentswaitnumber = $("#order-waitnumber");
  var simpleAlertModal = $("#simpleAlertModal");
  if (isContentswaitnumber.length > 0) {
    var waitnumber = isContentswaitnumber.text();

    $.post(
      "class/publicAjax",
      { checkLegitimationConfirmation: 1, rfnum: waitnumber },
      function (data) {
        // console.log(data);
        // return;
        if (data !== "") {
          var obj = JSON.parse(data);
          //console.log(obj);
          if (obj.legitimation == "not confirmed") {
            simpleAlertModal.modal("show");
            var body = $("#simple-alert-body");
            var title = $("#alertModalLabel");

            body.load(
              "content/werkschutz-confirmation",
              { route: "werkschutz", waitnumber: waitnumber },
              function () {
                var docnummer = $("#docnummer");
                title.text("Bestätigen durch Werkschutz");

                if ($(window).width() < 500) {
                  $("#small-tastatur").addClass("ml-auto").addClass("mr-auto");
                }
                clearInterval(interval1);
                pressKeyNumberSmall();
                $("#btn-close").click(function () {
                  location.href = location.href;
                });
              }
            );
          }
        }
      }
    );
  }
}

function confirmLegitimation() {
  $(
    "#btn-karosserie-werkschutz, #btn-trailertausch-werkschutz,#start-for-external, #start-for-schrottpickup, #form-leave-plant"
  ).click(function () {
    var body = $("#simple-alert-body");
    var title = $("#alertModalLabel");
    var route = $(this).attr("alt");
    var rfnum = $(this).attr("data");
    // console.log(route);

    body.load(
      "content/werkschutz-confirmation",
      { route: route, rfnum: rfnum },
      function () {
        var header = $("#docnummer");
        title.text("Bestätigen durch Werkschutz");
        if (route == "external" || route == "schrottpicker") {
          header.text("Angaben");
        }
        if (route == "werkverlassen" || route == "werkverlassen_ohne_prozess") {
          header.text("Bestätigen");
        }
        if ($(window).width() < 500) {
          $("#small-tastatur").addClass("ml-auto").addClass("mr-auto");
        }
        pressKeyNumberSmall();
        autoSuggesting("Dienstleister", "Firma");
        autoSuggesting("Kennzeichen", "Nummer");
        autoSuggesting("Spedition", "Firma");
      }
    );
    return false;
  });
}
function confirmZollLegitimation() {
  var body = $("#simple-alert-body");
  var title = $("#alertModalLabel");
  $("#btn-label-zollstelle").click(function () {
    body.load(
      "content/werkschutz-confirmation",
      { route: "zollstelle" },
      function () {
        title.text("Bestätigen durch Zollstelle");
      }
    );
  });
}
function autoSuggesting(input, keyword, type = "#") {
  var width = $(window).width();
  if (width > 500) {
    var hideElem = $("#register-form-to-waitlist").hasClass("d-none");
    if (hideElem == false) {
      $.get(
        "class/publicAjax",
        {
          autoSuggesting: keyword,
        },
        function (response) {
          var toArray = JSON.parse(response);
          //console.log(toArray);
          $(type + "" + input).autocomplete({
            source: toArray,
          });
        }
      );
    }
  }
}
function autoCompleteFields() {
  $(".startbutton").click(function () {
    var responseData = $(this).attr("alt");
    //console.log(responseData);
    $.post(
      "class/publicAjax",
      { ajax_knum: 1, responseData: responseData },
      function (response) {
        // console.log(response);
        // return;
        var form_lieferung = $("#lieferung-erfassen");
        var input_quickmodus = $("#lieferung-schnellerfassen");
        $("#schnellanmeldung, #voranmeldung").autocomplete({
          source: response.split(","),
          select: function (event, ui) {
            var Nummer = ui.item.label;
            //Nummer = null;
            if (Nummer != "") {
              form_lieferung.removeClass("d-none");
              input_quickmodus.addClass("d-none");

              $.post(
                "class/action",
                {
                  requestfrzdata: 1,
                  Nummer: Nummer,
                  responseData: responseData,
                },
                function (response) {
                  //console.log(response);
                  // return;

                  var split = response.split(",");
                  var firma = $("#firma_autocomplete");
                  var knznummer = $("#knznummer_autocomplete");
                  var knznummer_aufleger = $("#knznummer_aufleger");
                  var FRZTyp = $("#FRZTyp");
                  var name_fahrer = $("#name_fahrer_autocomplete");
                  var anlieferwerk = $("#anlieferwerk");
                  var radio_legitimation_licens = $(
                    "#radio_legitimation_licens"
                  );
                  var legitimation = $("#legitimation");
                  var radio = $("input[type=radio]");
                  var box_error_message = $("#box-error-message");
                  var transport_for = split[9];

                  // $.each(radio, function () {
                  //   var val = $(this).val();
                  //   if (val == split[5]) {
                  //     $(this).prop("checked", true);
                  //   }
                  // });
                  box_error_message.addClass("d-none");
                  firma.val(split[1]).addClass("alert-success");
                  knznummer.val(split[3]).addClass("alert-success");
                  FRZTyp.addClass("alert-success");
                  FRZTyp.append(
                    '<option value="' + split[2] + '">' + split[2] + "</option>"
                  );
                  FRZTyp.val(split[2]);
                  knznummer_aufleger.val(split[5]).addClass("alert-success");
                  name_fahrer.val(split[4]).addClass("alert-success");
                  if (split[6] == "Führerschein") {
                    radio_legitimation_licens.prop("checked", "checked");
                  }
                  legitimation.val(split[7]).addClass("alert-success");
                  if (split[8] == "zollgut") {
                    var zollRadio = $("input[name=Zollgut]");
                    $.each(zollRadio, function () {
                      var val = $(this).val();
                      if (val == "JA") {
                        $(this).prop("checked", "checked");
                        FRZTyp.prop("selectedIndex", 2);
                        $("input[name=dgsvo-contitions]").prop(
                          "checked",
                          "checked"
                        );
                      }
                    });
                  }
                  anlieferwerk
                    .addClass("alert-success")
                    .prop("selectedIndex", 0);
                  $("#btn-quickmodus").text("Schnellanmeldung");
                  $("#start-lieferung-schnellerfassen").addClass("d-none");
                  $("#dgsvo-contitions").prop("checked", "checked");
                  switch (transport_for) {
                    case "WERK 5":
                    case "Versand Werk 5":
                      $("#ladung").prop("selectedIndex", 1);
                      break;
                    case "WERK 9":
                      $("#ladung").prop("selectedIndex", 2);
                      break;
                  }
                  if (responseData == "voranmeldung") {
                    FRZTyp.prop("selectedIndex", 1);
                    $("#preregister-id").html(split[5]);
                    console.log(split);
                    if (split[15] == "redirect") {
                      $("#preregister-id").html(
                        split[5] +
                          "<br> " +
                          split[10] +
                          ", " +
                          split[11] +
                          " " +
                          split[12] +
                          " " +
                          split[13]
                      );
                      firma.removeClass("alert-success");
                      name_fahrer.removeClass("alert-success");
                      knznummer.removeClass("alert-success").val("");
                      knznummer_aufleger.removeClass("alert-success").val("");
                      $("#preregister-id").next("p").remove();
                      $("input, select").prop("disabled", "disabled");
                      $("#btn-anmelden").attr("type", "button");
                    }
                    if (split[15] == "notredirect") {
                      $("#preregister-id").html(
                        split[5] +
                          "<br> " +
                          split[10] +
                          ", " +
                          split[11] +
                          " " +
                          split[12] +
                          " " +
                          split[13]
                      );
                      firma.removeClass("alert-success");
                      name_fahrer.removeClass("alert-success");
                      knznummer.removeClass("alert-success").val("");
                      knznummer_aufleger.removeClass("alert-success").val("");
                    }
                    $("#alertbox-preregesier").removeClass("d-none");
                    // knznummer.val("");
                    // knznummer_aufleger.val("");
                  }
                }
              );
            }
          },
        });
      }
    );
  });
}
function startAnmeldung() {
  var lieferung_schnellerfassen = $("#lieferung-schnellerfassen");
  var form_lieferung = $("#lieferung-erfassen");
  var start_scann = $("#start-scann");
  var btn_label_werkschutz = $("#btn-karosserie-werkschutz");
  var btn_label_trailertausch = $("#btn-trailertausch-werkschutz");
  var start_by_voranmeldung = $("#start-by-voranmeldung");
  var start_by_lieferung = $("#start-by-lieferung");
  var start_by_kennzeichen = $("#start-by-kennzeichen");
  var start_by_wartenummer = $("#start-by-wartenummer");
  var start_by_sonderfahrt = $("#start-by-sonderfahrt");
  var start_by_qrcode = $("#start-by-qrcode");
  var start_for_external = $("#start-for-external");
  var start_for_schrottpickup = $("#start-for-schrottpickup");
  var input_kennzeichen = $("#input-kennzeichen");
  var input_kennzeichen_voranmeldung = $("#input-kennzeichen-voranmeldung");
  var input_lieferung = $("#input-lieferung");
  var input_wartenummer = $("#input-wartenummer");
  var input_sonderfahrt = $("#input-sonderfahrt");
  var scanner_info = $("#scanner-info");

  $(
    "#start-by-lieferung, #start-by-voranmeldung, #start-by-kennzeichen, #start-by-wartenummer, #start-by-sonderfahrt"
  ).click(function () {
    var route = $(this).attr("alt");
    start_by_lieferung.addClass("d-none");
    start_by_qrcode.addClass("d-none");
    start_by_kennzeichen.addClass("d-none");
    start_scann.addClass("d-none");
    scanner_info.addClass("d-none");
    start_by_voranmeldung.addClass("d-none");
    start_by_wartenummer.addClass("d-none");
    start_by_sonderfahrt.addClass("d-none");
    btn_label_werkschutz.addClass("d-none");
    btn_label_trailertausch.addClass("d-none");
    start_for_external.addClass("d-none");
    start_for_schrottpickup.addClass("d-none");
    lieferung_schnellerfassen.removeClass("d-none");
    switch (route) {
      case "voranmeldung":
        input_kennzeichen_voranmeldung.removeClass("d-none");
        $("#voranmeldung").focus();
        break;
      case "kennzeichen":
        input_kennzeichen.removeClass("d-none");
        $("#schnellanmeldung").focus();
        break;
      case "lieferung":
        input_lieferung.removeClass("d-none");
        break;
      case "wartenummer":
        input_wartenummer.removeClass("d-none");
        break;
      case "sonderfahrt":
        input_sonderfahrt.removeClass("d-none");
        $("#Sonderfahrt").focus();
        break;
    }
    $("#check-anmelde-id, #check-anmelde-id-sonderfahrt").click(function () {
      var ID = $(this).parent("div").prev("div").children("input");
      var werknummer = $(this).attr("alt");
      var errorbox = $("#errorbox");
      var anmeldeID = $("#anmeldeID");
      var value = ID.val();
      var route = ID.attr("id");

      $.post(
        "class/ajax",
        { check_anmelde_id: value, route: route, werknummer: werknummer },
        function (response) {
          console.log(response);

          if (response == "ID invalid") {
            errorbox.html("Number does not exists").removeClass("d-none");
            return;
          }

          anmeldeID.val(value);
          form_lieferung.removeClass("d-none");
          lieferung_schnellerfassen.addClass("d-none");
          input_lieferung.addClass("d-none");

          //$("#btn-quickmodus").text("Schnellanmeldung");

          var split = response.split(",");
          var werkdata = split[0].split(":");
          var werknummer = werkdata[0];
          var werkname = werkdata[1];
          var spedition = werkdata[2];
          var fahrttyp = werkdata[3];
          var anlieferwerk = $("#anlieferwerk");
          var firma = $("#firma_autocomplete");
          var knznummer = $("#knznummer_autocomplete");
          var FRZTyp = $("#FRZTyp");
          var knznummer_aufleger = $("#knznummer_aufleger");
          var ladung = $("#ladung");
          var name_fahrer = $("#name_fahrer_autocomplete");
          var legitimation = $("#legitimation");
          var lieferschein = $("#lieferschein");
          // var gefahrpunkte = $("#gefahrpunkte");
          // var radio_legitimation = $('input[name=radio_legitimation]');
          // var kennzeichnugspflichtig = $('input[name=kennzeichnugspflichtig]');
          // var beladen_for = $("input[name=beladen_for]");
          // var entladen = $("input[name=entladen]");
          var ladung_beschriebung = $("#ladung_beschriebung");
          var sofahnum = $("#sofahnum");

          if (fahrttyp == "Sonderfahrt") {
            $("#sonderfahrt-id").html("<span class=''>" + value + "</span>");
            $("#alertbox").removeClass("d-none");
            sofahnum.val(value);
          }

          anlieferwerk.addClass("alert-success");
          anlieferwerk.append(
            '<option value="' +
              werknummer +
              ":" +
              werkname +
              '">' +
              werkname +
              "</option>"
          );
          anlieferwerk.val(werknummer + ":" + werkname);
          firma.val(spedition).addClass("alert-success");
          name_fahrer.val(split[4]).addClass("alert-success");
          legitimation.val(split[6]).addClass("alert-success");
          knznummer.val(split[7]).addClass("alert-success");
          FRZTyp.addClass("alert-success");
          FRZTyp.append(
            '<option value="' + split[8] + '">' + split[8] + "</option>"
          );
          FRZTyp.val(split[8]);
          knznummer_aufleger.val(split[9]).addClass("alert-success");
          lieferschein.val(split[10]).addClass("alert-success");
          ladung.addClass("alert-success");
          ladung.append(
            '<option value="' + fahrttyp + '">' + fahrttyp + "</option>"
          );
          ladung.val(fahrttyp);
          ladung_beschriebung.val(split[16]).addClass("alert-success");
          $("#btn-quickmodus").text("Schnellanmeldung");
          $("#start-lieferung-schnellerfassen").addClass("d-none");
        }
      );
    });
    $("#check-anmelde-id-wartenummer").click(function () {
      $("body").append("<div class='ui-widget-overlay ui-front'></div>");
    });
  });
}
function checkValidforSignition() {
  $("#protokoll-form").submit(function () {
    var sendBtn = $("#change-position-status");
    var before = sendBtn.text();
    var Personalnummer = $("#person_sign").val();
    var stampError = $("#stamp-error");
    $(this).ajaxSubmit({
      beforeSubmit: function () {
        stampError.html("");
        $.post(
          "class/ajax",
          {
            checkUserRole: 1,
            Personalnummer: Personalnummer,
            route: "personal",
          },
          function (data) {
            if (data != "1") {
              stampError.html(data);
              return false;
            }
          }
        );
        sendBtn.html(before);
      },
      uploadProgress: function () {
        sendBtn.html(
          '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        );
      },
      resetForm: false,
      error: function () {
        alert(
          "Fehler: Verbindung zum Server verloren! Probieren Sie noch ein Mal."
        );
      },
      success: function (response) {
        $("#spinner").remove();

        $.post(
          "class/ajax",
          {
            checkUserRole: 1,
            Personalnummer: Personalnummer,
            route: "personal",
          },
          function (data) {
            if (data != "1") {
              stampError.html(data);

              return false;
            }
            if (response != "ok") {
              prozessing();
              var dinamictext = $("#dinamic-text");
              dinamictext.text(response);
              $("#spinner").remove();
              basicAlert();
              return false;
            }

            location.href = location.href;
          }
        );
      },
    });
    return false;
  });
}
function checkValidWerkschutz(interval1) {
  $("#werkschutz-form").submit(function () {
    var Personalnummer = $("#person_sign").val();
    var stampError = $("#stamp-error");
    var route = $("#route").val();
    var vehicle_gone = $("#vehicle_gone").val();
    var gadget = $("#gadget").text();

    var isContentswaitnumber = $("#order-waitnumber");

    $(this).ajaxSubmit({
      beforeSubmit: function () {
        stampError.html("");
      },
      uploadProgress: function () {
        // sendBtn.html(
        //   '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        // );
      },
      resetForm: false,
      error: function () {
        alert(
          "Fehler: Verbindung zum Server verloren! Probieren Sie noch ein Mal."
        );
      },
      success: function () {
        $("#spinner").remove();

        $.post(
          "class/ajax",
          {
            checkUserRole: 1,
            Personalnummer: Personalnummer,
            route: route,
            vehicle_gone: vehicle_gone,
          },
          function (data) {
            if (
              data == "werkverlassen" ||
              data == "werkverlassen_ohne_prozess"
            ) {
              prozessing();
              clearInterval(interval1);
              setTimeout(() => {
                location.href = "class/action?oldsession=reset&return=wea";
              }, 1000);

              return false;
            }
            if (data != "1") {
              stampError.html(data);
              return false;
            }

            if (isContentswaitnumber.length > 0) {
              var waitnumber = isContentswaitnumber.text();
              $.post(
                "class/ajax",
                { confirmWerkschutz: 1, rfnum: waitnumber },
                function (response) {
                  prozessing();

                  if (response == "confirmed") {
                    location.href = location.href;
                  }
                }
              );
              return false;
            }

            switch (route) {
              case "karosserie":
                var Empfaenger = $("#Empfaenger").get(0).value;
                var Dienstleister = $("#Dienstleister").val();
                var Kennzeichen = $("#Kennzeichen").val();
                $("#btn-karosserie-werkschutz")
                  .text("Legitimation OK")
                  .addClass("text-white")
                  .addClass("bg-success")
                  .removeAttr("data-bs-toggle")
                  .attr("title", gadget)
                  .attr("data-bs-target", "spezialprocess-1")
                  .attr(
                    "data-index",
                    `${Empfaenger};${Dienstleister};${Kennzeichen}`
                  );
                startSpecialProcess();
                break;
              case "trailertausch":
                $("#btn-trailertausch-werkschutz")
                  .text("Legitimation OK")
                  .addClass("text-white")
                  .addClass("bg-success")
                  .removeAttr("data-bs-toggle")
                  .attr("title", gadget)
                  .attr("data-bs-target", "spezialprocess-2");
                startSpecialProcess();
                break;
              case "external":
                var Empfaenger = $("#Empfaenger").val();
                var Spedition = $("#Spedition").val();
                var Kennzeichen = $("#Kennzeichen").val();
                $("#start-for-external")
                  .text("Legitimation OK")
                  .addClass("text-white")
                  .addClass("bg-success")
                  .removeAttr("data-bs-toggle")
                  .attr("title", gadget)
                  .attr("data-bs-target", "spezialprocess-3")
                  .attr(
                    "data-index",
                    `${Empfaenger};${Spedition};${Kennzeichen}`
                  );

                startSpecialProcess();
                break;
              case "schrottpicker":
                var Dienstleister = $("#Dienstleister").val();
                var Abholgut = $("#Abholgut").val();
                var Kennzeichen = $("#Kennzeichen").val();
                $("#start-for-schrottpickup")
                  .text("Legitimation OK")
                  .addClass("text-white")
                  .addClass("bg-success")
                  .removeAttr("data-bs-toggle")
                  .attr("title", gadget)
                  .attr("data-bs-target", "spezialprocess-4")
                  .attr(
                    "data-index",
                    `${Dienstleister};${Abholgut};${Kennzeichen}`
                  );
                startSpecialProcess();
                break;
              case "zollstelle":
                $("#btn-label-zollstelle")
                  .text("Zolldokumente in Ordnung")
                  .addClass("text-white")
                  .addClass("bg-success")
                  .attr("title", gadget)
                  .removeAttr("data-bs-toggle");
                $("#radio_zollgut_freigabe_checked").prop("checked", true);

                break;
            }
            $("#simpleAlertModal").modal("hide");
          }
        );
      },
    });
    return false;
  });
}
function lieferungSchnellerfassen(base) {
  $("#btn-quickmodus").clickToggle(
    function () {
      var btn = $(this);
      var input_quickmodus = $("#start-lieferung-schnellerfassen");
      var form_lieferung = $("#lieferung-erfassen");
      var ongateform = $("#ongate-form");

      input_quickmodus.removeClass("d-none");
      form_lieferung.addClass("d-none");

      //console.log(ongateform);
      ongateform.trigger("reset");
      btn.removeClass("ps-1").addClass("ps-2");
      btn.removeClass("pe-1").addClass("pe-2");
      btn.html("<i class='ti-close'></i>");
      autoCompleteFields();
      startAnmeldung();
    },
    function () {
      // var btn = $(this);
      // var input_quickmodus = $("#start-lieferung-schnellerfassen");
      // var form_lieferung = $("#lieferung-erfassen");

      // input_quickmodus.addClass("d-none")
      // form_lieferung.removeClass("d-none");
      // btn.removeClass("ps-2").addClass("ps-1");
      // btn.removeClass("pe-2").addClass("pe-1");
      // btn.text("Schnellanmeldung");
      // $("#start-by-lieferung, #start-by-kennzeichen, #start-by-wartenummer, #start-by-sonderfahrt").removeClass("d-none");

      // $("#input-lieferung, #input-kennzeichen, #input-wartenummer, #input-sonderfahrt, #errorbox").addClass("d-none");

      location.href = base;
    }
  );
}
function openReklamation() {
  $(".open-reklamation").click(function () {
    var elem = $(this).attr("alt");
    var sendBtn = $("#reklamation-btn");
    var reklamation_hidden_rfnum = $("#reklamation_hidden_rfnum");
    reklamation_hidden_rfnum.val(elem);
    $("#open-by-mobile").removeClass("d-none");
  });
}
function checkBeforeSubmit() {
  $("#FRZTyp").on("change", function () {
    var selectFRZtyp = $("#FRZTyp").get(0).value;
    var fieldAufleger = $("#knznummer_aufleger");
    fieldAufleger.attr("required", false);

    if (selectFRZtyp == "LKW+Aufleger/Anh.") {
      fieldAufleger.attr("required", true);
    }
  });
  $("#ladung").on("change", function () {
    var selectLadung = $(this).get(0).value;
    var row_leegut_abholnummer = $("#row-leegut-abholnummer");
    var leegut_abholnummer = $("#leegut_abholnummer");
    if (selectLadung == "Leergut-Abholung") {
      row_leegut_abholnummer.removeClass("d-none");
      leegut_abholnummer.prop("required", "required");
    } else {
      row_leegut_abholnummer.addClass("d-none");
      leegut_abholnummer.removeAttr("required");
    }
  });
}
function syncWarteschlange(interval1) {
  var warteschlange = $("#runleaf");
  var uri = warteschlange.attr("alt");

  $.post(
    "class/publicAjax",
    {
      ajaxWarteschlange: uri,
    },
    function (response) {
      //console.log(response);
      if (response == "") {
        clearInterval(interval1);
        console.log("Interval " + interval1 + " Location reload in 2 Sek.");
        setTimeout(() => {
          location.href = "class/action?oldsession=reset&return=wea";
          //location.href = location.href;
        }, 2000);
        return;
      }
      warteschlange.html(response);
      var contens = $(".laufschein").length;
      if (contens > 0) {
        var carddata = $(".laufschein").attr("alt").split(":");
        var rfnum = carddata[0];
        var kfznum = carddata[1];
        var werknum = carddata[2];
        conversation(rfnum, kfznum, werknum);
      }
      chooseLanguage();
      openRegisterForm();
      showMap();
      showLocation();
      stopSoundByClick();
      stopZollSoundByClick();
      checkInactiveTime();
      takeImageFromocument();
      pictureViwever();
      checkValidforSignition();
      warenannhameKontrolle(interval1);
      openScannWindow();
      sendFormData();
      openReklamation();
      isConfirmLegitimation(interval1);
      confirmLegitimation();
      sendToNextStep();
      setOnlineUser();
      if ($("#start-lauf").length > 0) {
        clearInterval(interval1);
      }
      if ($("#startpage").length > 0) {
        clearInterval(interval1);
      }
    }
  );
}
function soundModus() {
  $("#sound-modus").click(function () {
    var modus = $(this).attr("alt");
    $.post("class/ajax", { changesoundmodus: modus }, function (data) {
      location.href = location.href;
    });
  });
}
function stopSoundByClick() {
  var sound = $(".sound");
  if (sound.length > 0) {
    $("#close-alert, .stop-sound").click(function () {
      var rfnum = $(this).attr("alt");
      var confirmText = $(this).attr("data-index");
      var driveInData = $(this).attr("data");
      if (driveInData == 2) {
        if (!confirm(confirmText)) {
          return false;
        }
      }
      $.post(
        "class/ajax",
        { stopsound: 1, rfnum: rfnum, driveInData: driveInData },
        function (response) {
          //console.log(response);
          sound.remove();
          location.href = location.href;
        }
      );
    });
  }
}
function stopZollSoundByClick() {
  var sound = $(".sound");
  if (sound.length > 0) {
    $("#zoll-close-alert, .stop-zoll-sound").click(function () {
      var rfnum = $(this).attr("alt");
      $.post(
        "class/ajax",
        { stopzollsound: 1, rfnum: rfnum },
        function (response) {
          console.log(response);
          sound.remove();
          location.href = location.href;
        }
      );
    });
  }
}
function openRegisterForm() {
  var navbarNav = $("#navbarNav");
  $("#register").click(function () {
    var btn = $(this);
    var formElem = $("#register-form");
    var otherForm = $("#register-form-to-waitlist");
    var warteschlange = $("#waitlist");
    formElem.removeClass("d-none").addClass("d-block");
    warteschlange.addClass("d-none");
    otherForm.addClass("d-none");
    navbarNav.removeClass("show");
  });
  $("#register-to-waitlist, #register-to-waitlist-link").click(function () {
    var btn = $(this);
    var formElem = $("#register-form-to-waitlist");
    var otherForm = $("#register-form");
    var warteschlange = $("#waitlist");
    formElem.removeClass("d-none").addClass("d-block");
    warteschlange.addClass("d-none");
    otherForm.addClass("d-none");
    navbarNav.removeClass("show");
  });
}
function openFormToWaitlist() {
  var btnText = $("#btn-add-to-waitlist").html();
  $("#btn-add-to-waitlist").clickToggle(
    function () {
      var btn = $(this);
      var formElem = $("#register-form-to-waitlist");
      var warteschlange = $("#waitlist");
      btn.html("<i class='ti-close'></i>");
      formElem.removeClass("d-none").addClass("d-block");
      //warteschlange.addClass("d-none");
    },
    function () {
      var btn = $(this);
      var formElem = $("#register-form-to-waitlist");
      var warteschlange = $("#waitlist");
      btn.html(btnText);
      formElem.removeClass("d-block").addClass("d-none");
      //warteschlange.removeClass("d-none");
    }
  );
}
function showMap() {
  $(".showmap").click(function () {
    alert();
    $("body").append(
      "<div id='overlay'><span class='float-right font-larger' id='close-map'><i class='ti-close'></i></span><div id='viewer-loader'></div></div>"
    );
    $("#viewer-loader").load("map", function () {
      toolTipp($(".mappoint"));
      $("#close-map").click(function () {
        $("#overlay").remove();
      });
    });
  });
}
function showLocation() {
  $("#showlocation").click(function () {
    var sourse = $(this).attr("alt");
    var title = $(this).attr("title");
    $("body").append(
      "<div id='overlay'><span class='float-right font-larger' id='close'><i class='ti-close'></i></span><div id='viewer-loader'></div></div>"
    );
    $("#viewer-loader").html(
      "<div class='card'><div class='card-header h6 p-2'>" +
        title +
        "</div><div class='card-body p-0'><video src='assets/vid/" +
        sourse +
        "' controls autoplay></video></div></div>"
    );
    var is_sound = $("#sound-file-reminding").length;
    if (is_sound > 0) {
      var sound = document.getElementById("sound-file-reminding");
      sound.pause();
    }
    $("#close").click(function () {
      $("#overlay").remove();
    });
    //});
  });
}
function toolTipp(clicked) {
  clicked.clickToggle(
    function () {
      var text = $(this).attr("title");
      var position = $(this).position();
      var pLeft = 0;
      var pTop = parseInt(position.top + 20);
      $(".tooltip-window").remove();
      $(
        "<div class='tooltip-window' style='left:" +
          pLeft +
          "px;top:" +
          pTop +
          "px'>" +
          text +
          "</div>"
      ).appendTo($(this));
    },
    function () {
      $(".tooltip-window").remove();
    }
  );
}
function sessionReload() {
  $("#btn-session-reload").click(function () {
    var dinamictext = $("#dinamic-text");
    var url = "class/action?oldsession=reset&return=wea";
    dinamictext.text(
      "Reset aktueller Session. Alle Daten werden zurückgesetzt."
    );
    appAlert(url);
  });
}
function warenannhameKontrolle(interval1) {
  $(".prozess-done-leergutmitnahme-stapler").click(function () {
    var modal_protokoll = $("#modal-protokoll");
    var rfnum = $(this).attr("alt");

    modal_protokoll.load(
      "content/wa-leergut-protokoll",
      { confirmaction_wa: "wea", rfnum: rfnum },
      function () {
        // $("#small-tastatur").addClass("ml-auto").addClass("mr-auto");
        if ($(window).width() < 500) {
          $("#small-tastatur").addClass("ml-auto").addClass("mr-auto");
        }
        pressKeyNumberSmall();
      }
    );
  });

  $(".prozess-done-warenannahme-stapler").click(function () {
    var modal_protokoll = $("#modal-protokoll");
    var rfnum = $(this).attr("alt");
    var platz = $(this).attr("data");
    modal_protokoll.load(
      "content/wa-protokoll",
      { confirmaction_wa: "wea", rfnum: rfnum, platz: platz },
      function () {
        //$("#small-tastatur").addClass("ml-auto").addClass("mr-auto");
        if ($(window).width() < 500) {
          $("#small-tastatur").addClass("ml-auto").addClass("mr-auto");
        }
        clearInterval(interval1);
        pressKeyNumberSmall();
      }
    );
  });
  $(".prozess-done-warenannahme-buro").click(function () {
    var modal_protokoll = $("#modal-protokoll");
    var rfnum = $(this).attr("alt");
    modal_protokoll.load(
      "content/wa-buro",
      { confirmaction_wa: "wea", rfnum: rfnum },
      function () {
        //$("#small-tastatur").addClass("ml-auto").addClass("mr-auto");
        if ($(window).width() < 500) {
          $("#small-tastatur").addClass("ml-auto").addClass("mr-auto");
        }
        clearInterval(interval1);
        pressKeyNumberSmall();
      }
    );
  });
  $("#prozess-done-versand").click(function () {
    var modal_protokoll = $("#modal-protokoll");
    var rfnum = $(this).attr("alt");
    modal_protokoll.load(
      "content/versand-protokoll",
      { confirmaction_versand: "wea", rfnum: rfnum },
      function () {
        //$("#small-tastatur").addClass("ml-auto").addClass("mr-auto");
        if ($(window).width() < 500) {
          $("#small-tastatur").addClass("ml-auto").addClass("mr-auto");
        }
        clearInterval(interval1);
        pressKeyNumberSmall();
      }
    );
  });
}
function conversation(rfnum, kfznum, werknum) {
  $.post(
    "class/publicAjax",
    {
      ajaxConversation: 1,
      rfnum: rfnum,
      Nummer: kfznum,
      Werknummer: werknum,
    },
    function (data) {
      if (data != "") {
        var chatpost = $("#chatpost");
        $("#chatModal").modal("show");
        // console.log(data)
        chatpost.html(data);

        var btnReaded = $("#btn-message-readed");
        btnReaded.click(function () {
          var messageID = $(".message-item");

          $.each(messageID, function () {
            var id = $(this).attr("alt");
            $.post("class/publicAjax", { messagereaded: id }, function (data) {
              console.log(data + "gelesen");
            });
          });
        });
      }
    }
  );
}
function loadIframe() {
  $("#btn-open-locations").click(function () {
    var modalViewer = $("#modal-viewer");
    var uri = location.href;
    var urimodify = uri.replace(
      "weamanager/de/wea",
      "weamanager/de/way-to-locations"
    );
    modalViewer.html(
      "<iframe src='" + urimodify + "' id='iframe-locations'></iframe>"
    );
  });
}
function startSound(rfnum = null, soundInterval) {
  if (rfnum) {
    var rfnum = rfnum;
    $.post(
      "class/publicAjax",
      {
        ajaxCheckStausForSoundStart: 1,
        rfnum: rfnum,
      },
      function (data) {
        var sound = $("#sound-file-reminding").length;
        if (sound == 0 && data != "") {
          console.log(data);
          $("body").append(data);
          var audio = document.getElementById("sound-file-reminding");
          audio.play();
          //clearInterval(soundInterval);
        }
      }
    );
  }
}
function startSoundByCall(rfnum = null, soundInterval, text) {
  if (rfnum) {
    var rfnum = rfnum;
    $.post(
      "class/publicAjax",
      {
        ajaxCheckStausForSoundByCall: 1,
        rfnum: rfnum,
      },
      function (data) {
        var sound = $("#call-by-click").length;
        var achtungText = text;
        if (sound == 0 && data != "") {
          $("body").append(data);
          var audio = document.getElementById("call-by-click");
          audio.play();
          var body = $("#connectionOffline-body");

          body.html(
            `<div class="row"><div class="col-12 text-center mt-4 mb-4 text-danger"><button class="btn btn-lg text-danger">
              <i class="ti-bell" style="font-size:6em"></i>
            </button><h1>${achtungText}</h1></div></div>`
          );
          $(`<div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-dismiss="modal">
              Close
            </button></div>`).insertAfter(body);
          $("#connectionOffline").modal("show");
          $("body").click(function () {
            $("#call-by-click").remove();
          });
          //clearInterval(soundInterval);
        }
      }
    );
  }
}
function startSoundZoll(rfnum = null, soundInterval) {
  if (rfnum) {
    var rfnum = rfnum;
    $.post(
      "class/publicAjax",
      {
        ajaxCheckStausForSoundStartZoll: 1,
        rfnum: rfnum,
      },
      function (data) {
        var sound = $("#sound-file-reminding").length;
        if (sound == 0 && data != "") {
          $("body").append(data);
          var audio = document.getElementById("sound-file-reminding");
          audio.play();
          //clearInterval(soundInterval);
        }
      }
    );
  }
}
function readQRModalOpen() {
  $("#readQRModal-btn, #start-by-qrcode").click(function () {
    var readQRcontent = $("#readQRModal-content");
    readQRcontent.load("content/readerqrcode", function () {});
  });
}
function sendScannerData() {
  var rfnum = $("#order-waitnumber").text();

  $.post(
    "class/publicAjax",
    { ajaxScannerData: 1, rfnum: rfnum },
    function (data) {
      //console.log(data);
    }
  );
}
function checkInactiveTime() {
  $.post("class/publicAjax", { ajaxCheckInactiveTime: 1 }, function (data) {
    //console.log(data);
  });
}
function takeImageFromocument() {
  $("#take-image-from-document, #take-image-later").click(function () {
    var rfnum = "undefined";
    var rfnum_autor = $("#rfnum_autor");
    var rfnum_start = $("#rfnum");
    var nummer = $("#knznummer_autor");
    var pictureAutor = $("#picture-autor");
    var body = $("#diverse-modal-body");
    var diverseModalLabel = $("#diverseModalLabel");
    var picture_autor = "start";
    var knznummer = "undefined";
    var route = $(this).attr("data-index");

    if (rfnum_start.length > 0) {
      rfnum = rfnum_start.val();
    }
    if (rfnum_autor.length > 0) {
      rfnum = rfnum_autor.val();
    }
    if (pictureAutor.length > 0) {
      picture_autor = pictureAutor.val();
    }
    if (nummer.length > 0) {
      knznummer = nummer.val();
    }
    body.load(
      "content/wea-take-picture",
      {
        rfnum: rfnum,
        knznummer: knznummer,
        picture_autor: picture_autor,
        route: route,
      },
      function () {
        diverseModalLabel.html("<i class='ti-camera'></i>");
      }
    );
    $("#diverseModal").modal("show");
  });
}
function pictureViwever() {
  $(".pictureviwever-show").click(function () {
    var source = $(this).attr("alt");
    var pictureviweverBody = $("#pictureviwever-body");
    var pictureviweverLabel = $("#pictureviweverLabel");
    var title = $(this).attr("title");
    var modalsize = $("#modalsize");
    var hasClass = $(this).hasClass("modal-xl");

    if (hasClass == true) {
      modalsize.removeClass("modal-dialog-centered").addClass("modal-xl");
    }
    pictureviweverBody.html("<img src='" + source + "' class='img-fluid'>");
    pictureviweverLabel.text(title);
  });
}
function startSpecialProcess() {
  $(
    "#btn-karosserie-werkschutz, #btn-trailertausch-werkschutz, #start-for-external, #start-for-schrottpickup"
  ).click(function () {
    var title = $(this);
    var process = $(this).attr("data");
    var externalData = $(this).attr("data-index");
    var gadget = $(this).attr("title");

    $.post("class/publicAjax", { getRFID: 1 }, function (calcrfnum) {
      title.attr("title", calcrfnum);
      var rfnum = calcrfnum;
      var dinamictext = $("#dinamic-text");
      var headertext;
      var url = "";
      switch (process) {
        case "specialprocess1":
          headertext = "Fahrzeug zur Einfahrt hinzufügen?";
          break;
        case "specialprocess2":
          headertext = "Trailertausch für Werk 9 zur Einfahrt hinzufügen?";
          break;
        case "specialprocess3":
          headertext = "Einfahrt für Fremdfirma hinzufügen?";
          break;
        case "specialprocess4":
          headertext = "Einfahrt für Entsorgungsfirma hinzufügen?";
          break;
        case "specialprocess5":
          headertext = "Einfahrt für vorangemeldete Lieferung hinzufügen?";
          break;
      }
      url =
        "class/action?add_to_prozess_werksverkehr=" +
        process +
        "&return=wea&rfnum=" +
        rfnum +
        "&externalData=" +
        externalData +
        "&gadget=" +
        gadget;
      dinamictext.text(headertext);
      //}
      appAlert(url);
      pressKeyNumberSmall();
    });
  });
}
function sendToNextStep() {
  $(".sendtonextstep").click(function () {
    var body = $("#sendtonextstep-body");
    var data = $(this).attr("alt").split(":");
    body.load("content/goto-nextstep", {
      rfnum: data[0],
      Nummer: data[1],
      returnURI: "../de/wea",
    });
    $("#sendtonextstep").modal("show");
  });
}
function pressKeyNumber() {
  var Wartenummer = $("#Wartenummer");
  var backkey = $("#backkey");
  $(".tasten").click(function () {
    var value = $(this).attr("data-index");
    var isValue = Wartenummer.val();

    Wartenummer.val(isValue + "" + value);
  });
  $(".backkey").click(function () {
    var isValue = Wartenummer.val();
    var len = isValue.length;
    // alert(isValue.substr(0, len - 1));
    // //isValue.val();
    Wartenummer.val(isValue.substr(0, len - 1)).focus();
  });
}
function pressKeyNumberSmall() {
  var signInput = $("#person_sign");
  var backkey = $("#backkey");
  $(".tasten").click(function () {
    var value = $(this).attr("data-index");
    var isValue = signInput.val();

    signInput.val(isValue + "" + value);
  });
  $(".backkey").click(function () {
    var isValue = signInput.val();
    var len = isValue.length;
    // alert(isValue.substr(0, len - 1));
    // //isValue.val();
    signInput.val(isValue.substr(0, len - 1)).focus();
  });
}
$(function () {
  autoSuggesting("firma_autocomplete", "Firma");
  autoSuggesting("knznummer_autocomplete", "Nummer");
  autoSuggesting("knznummer_aufleger", "knznummer_aufleger");

  sendFormData();
  openScannWindow();
  checkZollgut();
  confirmLegitimation();
  checkMonitorWidth();
  selectLegitimation();
  sessionReload();

  soundModus();
  openRegisterForm();
  openFormToWaitlist();
  showMap();
  showLocation();
  sendToNextStep();
  lieferungSchnellerfassen(base);
  checkBeforeSubmit();
  loadIframe();
  stopSoundByClick();
  stopZollSoundByClick();
  readQRModalOpen();
  takeImageFromocument();

  openLiveViewCam();
  //checkConnectionToWiFi();
  pressKeyNumber();
  infomationsAndUpdates();
  // var interval1 = setInterval(function () {
  //   syncWarteschlange(interval1, soundObj);
  //   checkInactiveTime();
  //   sendScannerData();
  // }, 10000);
  checkValidWerkschutz(interval1);
  var interval1 = setInterval(function () {
    syncWarteschlange(interval1);

    checkInactiveTime();
    sendScannerData();
  }, 10000);

  var soundInterval = setInterval(function () {
    startSound(rfnum, soundInterval);
    startSoundZoll(rfnum, soundInterval);
    startSoundByCall(rfnum, soundInterval, alarmText);
  }, 10000);
  syncWarteschlange(interval1);
});
