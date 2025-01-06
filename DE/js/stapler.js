jQuery.fn.clickToggle = function (a, b) {
  return this.on("click", function (ev) {
    [b, a][(this.$_io ^= 1)].call(this, ev);
  });
};
function setOnlineUser(ajaxpath) {
  $.post(
    ajaxpath,
    {
      setOnlineUser: 1,
    },
    function (data) {
      console.log(data);
    }
  );
}
function prozessing() {
  $("body").append(
    "<div id='spinner'><div class='box-middle'><div class='spinner-border text-light' role='status'><span class='visually-hidden'>Loading...</span></div></div></div>"
  );
}
function chooseLanguage() {
  $(".choose-language").click(function () {
    var lang = $(this).attr("title");
    var return_uri = $(this).attr("alt");
    location.href =
      "class/action?set_language=" + lang + "&return_uri=" + return_uri;
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
function userStatus() {
  $(".user-status").click(function () {
    if (confirm("Status ändern? ")) {
      var status = $(this).attr("href");
      var icon = $("#messageDropdown-userstatus");

      $.post(
        "class/publicAjax",
        {
          changeRessourseStatus: status,
        },
        function (response) {
          console.log(response);
          switch (response) {
            case "notavailable":
              icon.html(
                `<span class='btn btn-danger-custom me-2'></span> <i class="ti-na mx-0"></i>`
              );
              break;
            case "available":
              icon.html(
                `<span class='btn btn-success-custom me-2'></span> <i class="ti-user mx-0"></i>`
              );
              break;
          }
        }
      );
      $("#messageDropdown-userstatus").removeClass("show");
      $(".dropdown-menu").removeClass("show");
    }
    return false;
  });
}
function syncWarteschlange(interval1) {
  var warteschlange = $("#column-warteschlange");
  var uri = warteschlange.attr("alt");

  $.post(
    "class/publicAjax",
    {
      ajaxStaplerWarteschlange: uri,
    },
    function (response) {
      //console.log(response);
      if (response == "redirecttologin") {
        location.href = "login?app=stapler";
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
      stopSoundByClick();
      ajaxMonitorSchichtLeiter();
      openReklamation();
      warenannhameKontrolle(interval1);
      openModalLadeporzess();
      openModalBoxladen();
      openModalConfirmBeladen();
      missedLkw();
      openSeqLager();
      requestVersandList();
      openStartBeladen();
      sequenzlagerPlaceItems();
      openHofLager();
      openCheckInhalt();
      sendToNextStep();
      takeImageFromDocument();
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
      alert(data);
      location.href = location.href;
    });
  });
}
function stopSoundByClick() {
  var sound = $(".sound");
  var btn = $(".stop-sound");
  if (sound.length > 0) {
    $.each(btn, function () {
      $(this).bind("click", function () {
        var rfnum = $(this).attr("alt");
        $.post(
          "class/ajax",
          { stopsound: 1, rfnum: rfnum },
          function (response) {
            btn.remove();
            sound.remove();
            location.href = location.href;
          }
        );
      });
    });
  }
}
function toolTipp(clicked) {
  clicked.click(function () {
    var text = $(this).attr("title");
    var position = $(this).position();
    var plusLeft = parseInt(position.left) + 20;
    $(
      "<div class='tooltip-window' style='left:" +
        plusLeft +
        "px;top:" +
        position.top +
        "px'><span class='close-tooltipp pointer d-block'><i class='ti-close'></i></span>" +
        text +
        "</div>"
    ).insertAfter($(this));
    $(".close-tooltipp").click(function () {
      $(this).parent(".tooltip-window").remove();
    });
  });
}
function sessionReload() {
  $("#btn-session-reload").click(function () {
    var dinamictext = $("#dinamic-text");
    var url = "class/action?oldsession=reset&return=stapler";
    dinamictext.text("Wollen Sie das Programm beenden?");
    appAlert(url);
  });
}
function locationReload() {
  $("#btn-location-reload").click(function () {
    location.href = location.href;
  });
}
function warenannhameKontrolle(interval1) {
  $(".prozess-done-leergutmitnahme-stapler").click(function () {
    var modal_protokoll = $("#modal-protokoll");
    var rfnum = $(this).attr("alt");
    modal_protokoll.load(
      "content/wa-leergut-protokoll",
      { confirmaction_wa: "stapler", rfnum: rfnum },
      function () {
        checkValidforSignition();
        pressKeyNumber();
      }
    );
  });
  $(".prozess-done-manueller-auftrag-stapler").click(function () {
    var modal_protokoll = $("#modal-protokoll");
    var rfnum = $(this).attr("alt");
    modal_protokoll.load(
      "content/ma-auftrag-protokoll",
      { confirmaction_wa: "stapler", rfnum: rfnum },
      function () {
        checkValidforSignition();
        clearInterval(interval1);
        pressKeyNumber();
      }
    );
  });
  $(".prozess-done-warenannahme-stapler").click(function () {
    var modal_protokoll = $("#modal-protokoll");
    var rfnum = $(this).attr("alt");
    var platz = $(this).attr("data");
    modal_protokoll.load(
      "content/wa-protokoll",
      { confirmaction_wa: "stapler", rfnum: rfnum, platz: platz },
      function () {
        checkValidforSignition();
        clearInterval(interval1);
        pressKeyNumber();
      }
    );
  });
  $(".done-manueller-auftrag-stapler").click(function () {
    var auftragID = $(this).attr("alt");
    var uri = "class/action?done_manueller_auftrag=" + auftragID;
    location.href = uri;
  });

  $("#prozess-done-versand").click(function () {
    var modal_protokoll = $("#modal-protokoll");
    var rfnum = $(this).attr("alt");
    modal_protokoll.load(
      "content/versand-protokoll",
      { confirmaction_versand: "wea", rfnum: rfnum },
      function () {
        clearInterval(interval1);
        pressKeyNumber();
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
        chatpost.html(data);
        openMessageBoxEvoChat();
        var btnReaded = $("#btn-message-readed");
        btnReaded.click(function () {
          var messageID = $(".message-item");
          $.each(messageID, function () {
            var id = $(this).attr("alt");
            $.post("class/publicAjax", { messagereaded: id }, function (data) {
              //console.log(data + "gelesen");
            });
          });
        });
      }
    }
  );
}
function ajaxMonitorSchichtLeiter() {
  var comingnext = $("#coming-next");
  $.post(
    "class/publicAjax",
    {
      ajaxMonitorSchichtLeiter: 1,
    },
    function (response) {
      comingnext.html(response);
      staplerZuweisen();
      pictureViwever();
    }
  );
}
function staplerZuweisen() {
  $(".set-forkbully").click(function () {
    var carddata = $(this).attr("alt").split(":");
    var rfnum = carddata[0];
    var kfznum = carddata[1];
    var submitButton = $("#submit-button");
    $(
      "<input type='hidden' name='return' value='stapler'><input type='hidden' name='stapler_for_unload' value='" +
        rfnum +
        "'><input type='hidden' name='kfznum' value='" +
        kfznum +
        "'>"
    ).insertAfter(submitButton);
  });
}
function pictureViwever() {
  $(".pictureviwever-show").click(function () {
    var elem = $(this);
    var source = $(this).attr("alt");
    var split = $(this).attr("data").split("&");
    var bmi_nummer = split[0];
    var empfaenger = split[1];
    var pictureviweverBody = $("#pictureviwever-body");
    var pictureviweverLabel = $("#pictureviweverLabel");
    var title = $(this).attr("title");
    var modalsize = $("#modalsize");
    var hasClass = $(this).hasClass("modal-xl");
    pictureviweverBody.html("<img src='" + source + "' class='img-fluid'>");
    pictureviweverLabel.text(title);
    modalsize.addClass("modal-dialog-centered").removeClass("modal-xl");
    if (hasClass == true) {
      modalsize.removeClass("modal-dialog-centered").addClass("modal-xl");
    }
    pictureviweverBody.load(
      "content/imagepopup",
      { bmi_nummer: bmi_nummer, empfaenger: empfaenger },
      function () {
        var controls = elem.hasClass("controls");
        //console.log(hasClass);
        if (controls == false) {
          $("#btn-controls").hide();
        }
        if (controls == true) {
          $("#btn-controls").show();
        }

        $(".image-hover").attr("src", source);
        var count = 1;
        var step = 90;
        $("#rotate-button").click(function () {
          var Picture = pictureviweverBody
            .children("div")
            .children("div")
            .children("img");
          pictureviweverBody.children("div").children("div").addClass("col-12");
          var enker = parseInt(step);
          Picture.css({ transform: "rotate(" + enker + "deg)" });
          if (count == 4) {
            count = 1;
            step = 0;
          }
          //console.log(enker);
          count++;
          step = parseInt(step + 90);
        });

        var zoom = 25;
        $("#zoomin-button").click(function () {
          var modalsize = $("#modalsize");
          var startWidth = modalsize.width();
          var zommIn = parseInt(startWidth + zoom);
          modalsize.css({ width: zommIn + "px" });
          if (count == 6) {
            count = 1;
            zoom = 0;
          }
          count++;
          zoom = parseInt(zoom);
        });
        $("#zoomout-button").click(function () {
          var modalsize = $("#modalsize");
          var startWidth = modalsize.width();
          var zommIn = parseInt(startWidth - zoom);
          modalsize.css({ width: zommIn + "px" });
          if (count == 6) {
            count = 1;
            zoom = 0;
          }
          count++;
          zoom = parseInt(zoom);
        });
      }
    );
  });
}
// function openMessageBoxEvoChat() {
//   $("#send_to_evochat, .message-item").click(function () {
//     var split = $(this).attr("data").split("&");
//     var bmi_nummer = split[1];
//     var absender = split[0];

//     var header = $("#header-evochat");
//     var sendto = $("#send-to-evochat");
//     var returnURI = $("#returnURI");
//     var verlauf = $("#verlauf");
//     var target = $("#target");
//     returnURI.val("");
//     target.val("SF-JIT");
//     verlauf.html("");
//     header.html(
//       "Nachricht senden an <span class='h4'>" + bmi_nummer + "</span>"
//     );
//     sendto.val(bmi_nummer);
//     $("#evochatModal").modal("show");
//     syncMessages(bmi_nummer, absender);
//     $("#btn-chat-close").click(function () {
//       verlauf.html("close");
//     });
//     $("#chat_attachment").on("change", function () {
//       var value = $(this).val().split("/");
//       if (value != "") {
//         $("#showinputvalue").html("<span class='small'>image loaded</span>");
//       }
//     });
//   });
// }
function openMessageBoxEvoChat() {
  $("#send_to_evochat, .message-item").click(function () {
    var intv;
    var split = $(this).attr("data").split("&");
    var bmi_nummer = split[1];
    var userwhowrite = split[0];
    var absender = $("#Absender");
    //var empfaenger = "Zentrale";
    var header = $("#header-evochat");
    var target = $("#target");
    var sendto = $("#send-to-evochat");
    var returnURI = $("#returnURI");
    var verlauf = $("#verlauf");
    returnURI.val("");
    verlauf.html("");
    $.post(
      "class/ajax.php",
      {
        getBMINummForChatList: 1,
        callChat: bmi_nummer,
      },
      function (data) {
        //console.log(data);
        header.html(data);
        $("#select-empfaenger").on("change", function () {
          var to = $(this).get(0).value;
          sendto.val(to);
          target.val(to);
          absender.val(userwhowrite);
          verlauf.html(
            "<div class='p-4 text-center text-primary'><span class='spinner-border spinner-border-sm text-primary' role='status' aria-hidden='true'></span> Chat loading...</div>"
          );
          syncMessages(to, userwhowrite);
        });
        $("#btn-chat-close").click(function () {
          verlauf.html("close");
        });
      }
    );
    sendto.val(userwhowrite);
    target.val(userwhowrite);
    syncMessages(userwhowrite);
    $("#evochatModal").modal("show").draggable();
    $(".modal-backdrop").remove();
    $("#btn-chat-close").click(function () {
      verlauf.html("close");
    });
    $("#chat_attachment").on("change", function () {
      var value = $(this).val().split("/");
      if (value != "") {
        $("#showinputvalue").html("<span class='small'>image loaded</span>");
      }
    });
  });
}
function syncMessages(bmi_nummer, absender, count = 0) {
  var verlauf = $("#verlauf");
  $.post(
    "class/ajax",
    {
      getChatVerlauf: 1,
      user: bmi_nummer,
      absender: absender,
      returnURI: "stapler",
    },
    function (data) {
      verlauf.html(data);
      if (count == 0) {
        setTimeout(() => {
          var scrollY = verlauf.height();
          $("#evochat-verlauf").scrollTop(scrollY);
        }, 5000);
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
function checkValidforSignition() {
  $("#protokoll-form").submit(function () {
    var sendBtn = $("#change-position-status");
    var before = sendBtn.text();
    var Personalnummer = $("#person_sign").val();
    var stampError = $("#stamp-error");
    $(this).ajaxSubmit({
      beforeSubmit: function () {
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
        alert("Fehler beim senden");
      },
      success: function (response) {
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
              $("#spinner").remove();
              return false;
            }
            if (response != "ok") {
              alert(response);
              $("#spinner").remove();
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
function sessiontime() {
  var idleTime = 0;
  // Increment the idle time counter every minute.
  setInterval(timerIncrement, 60000); // 1 minute
  // Zero the idle timer on mouse movement.
  $(this).mousemove(function () {
    idleTime = 0;
  });
  $(this).keypress(function () {
    idleTime = 0;
  });
  $(this).click(function () {
    idleTime = 0;
  });
  function timerIncrement() {
    var out = $("#sessiontime");
    var timeRange = 121;
    var calcRest;
    idleTime = idleTime + 1;
    calcRest = timeRange - idleTime;
    if (idleTime > timeRange) {
      // 120 minutes
      window.location.href = "class/action?oldsession=reset&return=stapler";
    }
    var string = "Autoabmelden in " + calcRest + " Min.";
    if (calcRest == "-1") {
      string = "abmelden";
    }
    out.html("<span class='small'>" + string + "</span>");
  }
  //timerIncrement();
}

function evoChatLastShowMessage(empfaenger, iPing) {
  var elem = $("#getmessage");
  var werknummer = elem.attr("title");

  var showmessage = $("#getmessage-text");

  if (elem.length > 0) {
    $.post(
      "class/publicAjax",
      {
        evoChatLastShowMessage: 1,
        werknummer: werknummer,
        empfaenger: empfaenger,
        target: "SF-JIT",
      },
      function (response) {
        if (response) {
          elem.removeClass("d-none");
          var pinging = "";
          if (iPing == 1 || iPing == 5) {
            pinging =
              "<embed src='assets/sound/pinging.mp3' class='pingsound' style='opacity:0; height:.1em; width:.1em;'></embed>";
          }
          showmessage.html(response + pinging);
          openMessageBoxEvoChat();
          $("#close-getmessage").click(function () {
            //var key = $(this).next("div").children("div").attr("data-index");
            var key = showmessage.children("div").attr("data-index");
            //console.log(key);
            $.post(
              "class/publicAjax",
              {
                evochatmessagereaded: key,
              },
              function (response) {
                if (response) {
                  location.href = location.href;
                }
              }
            );
          });
        }
      }
    );
  }
}

function sendToNextStep() {
  $(".sendtonextstep").click(function () {
    var body = $("#sendtonextstep-body");
    var data = $(this).attr("alt").split(":");
    body.load("content/goto-nextstep", {
      rfnum: data[0],
      Nummer: data[1],
      returnURI: "../de/stapler",
    });
    $("#sendtonextstep").modal("show");
  });
}
function pressKeyNumber() {
  var signinput = $("#person_sign");
  //var backkey = $("#backkey");
  $(".tasten").click(function () {
    var value = $(this).attr("data-index");
    var isValue = signinput.val();

    signinput.val(isValue + "" + value);
  });
  $(".backkey").click(function () {
    var isValue = signinput.val();
    var len = isValue.length;
    // alert(isValue.substr(0, len - 1));
    // //isValue.val();
    signinput.val(isValue.substr(0, len - 1)).focus();
  });
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
function takeImageFromDocument() {
  //alert($("#take-image-from-document").length);
  $("#take-image-from-document").click(function () {
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
$(function () {
  var ajaxpath = "class/ajax";
  var iPing = 0;
  var interval1 = setInterval(syncWarteschlange, 10000);
  setInterval(() => {
    if (iPing == 10) {
      iPing = 0;
    }
    evoChatLastShowMessage(weamanageruser, iPing);
    iPing++;
  }, 10000);
  // setInterval(() => {
  //   setOnlineUser(ajaxpath);
  // }, 30000);
  userStatus();
  syncWarteschlange(interval1);
  takeImageFromDocument();
  stopSoundByClick();
  sendToNextStep();
  soundModus();
  sessionReload();
  locationReload();
  ajaxMonitorSchichtLeiter();
  openMessageBoxEvoChat();
  sessiontime();
  openReklamation();
});
