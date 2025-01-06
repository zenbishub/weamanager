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
      //console.log(data);
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
          if ($("#select-werk").length > 0) {
            var selectWerk = $("#select-werk").get(0).value;
            if (selectWerk != "") {
              $(this).dialog("close");
              $("#form-select-werk").submit();
              return;
            }
          }
          location.href = url;
          return false;
        },
        Nein: function () {
          $(this).dialog("close");
        },
      },
    });
}
function showHideNavi() {
  $("#btn-showhidenavi").on("click", function () {
    $(".sb-topnav").animate({ top: "0px" }, function () {
      setTimeout(() => {
        $(".sb-topnav").animate({ top: "-70px" });
      }, 5000);
    });
  });
  $("#showhidenavi").on("mouseover", function () {
    $(".sb-topnav").animate({ top: "0px" }, function () {
      setTimeout(() => {
        $(".sb-topnav").animate({ top: "-70px" });
      }, 5000);
    });
  });
}
function changeUnloadPlant() {
  $(".change-unload-plant").click(function () {
    var header = $("#header-changeUnloadPlant");
    var body = $("#body-changeUnloadPlant");
    var rfnum = $(this).attr("data-index");
    header.html("");
    body.html(`<div class="row">
          <div class="col-12">
            <h3>Abladewerk ändern</h3>
            <div class="form-group mt-3">
              <div class="form-check mb-2">
                  <label class="form-check-label ">
                    <input type="radio" class="form-check-input" name="zoll_unload_plant" value="Transport für Werk 5">
                              Ware für Werk 5
                            <i class="input-helper"></i></label>
                </div>
                <div class="form-check">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="zoll_unload_plant" value="Transport für Werk 9">
                              Ware für Werk 9
                            <i class="input-helper"></i></label>
                </div>
            </div>
            <div class="form-group">
              <button class="btn btn-primary p-2" id="btn-change-unload-plant">ändern</button>
            </div>
          </div>
        </div>`);
    $("#changeUnloadPlant").modal("show");
    $("#btn-change-unload-plant").click(function () {
      var inputs = $("input[name=zoll_unload_plant]");
      $("#badge-changed-plant").remove();
      $.each(inputs, function () {
        var checked = $(this);
        if (checked.prop("checked") == true) {
          $.post(
            "class/ajax",
            {
              changeZollUnloadPlant: 1,
              rfnum: rfnum,
              Ladung: checked.val(),
            },
            function (response) {
              $(response).insertAfter("#btn-change-unload-plant");
            }
          );
        }
      });
    });
  });
}
function removeFromOrder() {
  $(".remove-from-order").click(function () {
    var dinamictext = $("#dinamic-text");
    var url = $(this).attr("href");
    dinamictext.text("Lieferfahrzeug aus der Warteschlage entfernen?");
    appAlert(url);
    return false;
  });
}
function pictureViwever() {
  $(".pictureviwever-show").click(function () {
    var elem = $(this);
    var source = $(this).attr("alt");
    //var split = $(this).attr("data").split("&");
    var bmi_nummer = "";
    var pictureviweverBody = $("#pictureviwever-body");
    var empfaenger = "";
    var pictureviweverLabel = $("#pictureviweverLabel");
    var title = $(this).attr("title");
    var modalsize = $("#modalsize");
    var hasClass = $(this).hasClass("modal-xl");
    pictureviweverLabel.html("");
    //modalsize.addClass("modal-dialog-centered").removeClass("modal-xl");
    if (hasClass == true) {
      modalsize.removeClass("modal-dialog-centered").addClass("modal-xl");
    }
    pictureviweverBody.load(
      "content/imagepopup",
      { bmi_nummer: bmi_nummer, empfaenger: empfaenger },
      function () {
        var controls = elem.hasClass("controls");
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
          if (count == 4) {
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
    pictureviweverLabel.text(title);
  });
}
function zollAbfetigung() {
  $(".zoll-abfetigung-btn").click(function () {
    var nummer = $(this).attr("title");
    var rfnum = $(this).attr("alt");
    var dinamictext = $("#dinamic-text");
    var url = "form";
    dinamictext.html(`<center><p>Zollabfertigung bestätigen</p><h3>${nummer}</h3>
      <div class="row">
      <form action="class/action.php" id="form-select-werk">
        <div class="col-12 form-group">
        <input type="hidden" name="pass_zollgut" value="${rfnum}">
         <select class="form-select" name="Ladung" id="select-werk">
            <option value="">Abladewerk wählen</option>
            <option value="Wareneingang Werk 5">Werk 5</option>
            <option value="Transport für Werk 9">Werk 9</option>
          </select>
          <input type="hidden" name="return" value="zollgut">
        </div>
        </form>
      </div>
      Der Fahrer wird über die Entscheidung informiert und aufgefordert seine Zolldokumente abzuholen.</center>`);
    appAlert(url);
  });
}
function zollWatreListe(werknummer) {
  $.post(
    "class/ajax.php",
    {
      ajaxZollwatreListe: 1,
      werknummer: werknummer,
    },
    function (response) {
      var zollWarteliste = $("#zoll-warteliste");
      zollWarteliste.html(response);
      zollAbfetigung();
      openSendBox();
      removeFromOrder();
      zollActionButtons();
      openMessageBoxEvoChat();
      moreZollInfomation();
      checkIfScannerOnline();
      zollSendungInformation();
      changeUnloadPlant();
      pictureViwever();
      // evoChatLastShowMessage();
    }
  );
}
function zollDoneListe(werknummer) {
  $.post(
    "class/ajax.php",
    {
      ajaxZollDoneListe: 1,
      werknummer: werknummer,
    },
    function (response) {
      var zollDoneliste = $("#zoll-abgefertigt");
      zollDoneliste.html(response);
      openMessageBoxEvoChat();
      moreZollInfomation();
      checkIfScannerOnline();
      openSendBox();
      removeFromOrder();
      zollSendungInformation();
      changeUnloadPlant();
      pictureViwever();
    }
  );
}
function zollActionButtons() {
  $(".zoll-information").click(function () {
    var action = $(this).attr("data");
    var body = $("#zoll-actionmodal-body");
    var rfnum = $(this).attr("alt");

    body.load(
      "content/zoll-confirmation.php",
      { zollaction: action },
      function () {
        $("#zoll-rfnum").val(rfnum);
      }
    );
  });
}
function zollSendungInformation() {
  $(".info-erfassen").click(function () {
    var rfnum = $(this).attr("data");
    var body = $("#zoll-actionmodal-body");
    var header = $("#zollactionModalLabel");
    var submitBtn = $("#submit-btn");
    var hiddenName = $("#add_zoll_meldung");
    hiddenName.attr("name", "sendungsinfo_erfassen");
    header.html("");
    submitBtn.text("Sendungsinfo erfassen");

    body.load("content/zoll-sendungsinfo-erfassen.php", function () {
      $("#zoll-rfnum").val(rfnum);
    });
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
    returnURI.val("zollgut");
  });
}
function openMessageBoxEvoChat() {
  $("#send_to_evochat, .message-item").click(function () {
    var intv;
    var split = $(this).attr("data").split("&");
    var bmi_nummer = split[1];
    var userwhowrite = "ZOLLSTELLE";
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
        callChat: "zollstelle",
      },
      function (data) {
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
function syncMessages(bmi_nummer, count = 0) {
  var verlauf = $("#verlauf");
  $.post(
    "class/ajax.php",
    {
      getChatVerlauf: 1,
      user: bmi_nummer,
      empfaenger: "",
      absender: "ZOLLSTELLE",
      returnURI: "zollgut",
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
      "class/publicAjax.php",
      {
        evoChatLastShowMessage: "zollstelle",
        werknummer: werknummer,
        empfaenger: "ZOLLSTELLE",
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
            var key = showmessage.children(".message-item").attr("data-index");
            $.post(
              "class/publicAjax.php",
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
function openContentByRequest() {
  $(".open-content-by-request").click(function () {
    var page = $(this).attr("alt");
    var target = $("#" + page);

    target.load("content/" + page + ".php", function () {
      clickHover();
      asyncUpload();
      $.getScript("vendor/datatables/jquery.dataTables.min.js");
      $.getScript("vendor/datatables/dataTables.bootstrap4.min.js");
      $.getScript("js/demo/datatables-demo.js");
    });
  });
}
function clickHover() {
  $(".click-hover").clickToggle(
    function () {
      var row = $(this).parent("tr");
      row.css("background-color", "#7bdcdf");
    },
    function () {
      var row = $(this).parent("tr");
      row.removeAttr("style");
    }
  );
}
function asyncUpload() {
  $("#ajax-upload").submit(function () {
    var sendBtn = $("#send-btn");
    var resultBox = $("#result-box");
    var before = sendBtn.text();
    $(this).ajaxSubmit({
      beforeSubmit: function () {
        sendBtn.html(
          '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        );
      },
      uploadProgress: function (event, position, total, percentComplete) {
        console.log(percentComplete + "%");
        $("#progress-bar").width(percentComplete + "%");
        $("#progress-bar").html(
          '<div id="progress-status"><span id="procent"></span></div>'
        );
        //$("#upload-procent").text("Upload "+percentComplete+"%");
      },
      resetForm: true,
      error: function () {
        alert("Warnung!, Fehler: Upload nicht erfolgreich");
      },
      success: function (response) {
        sendBtn.html(before);
        resultBox.html(response);
      },
    });
    return false;
  });
}
function moreZollInfomation() {
  $(".more-infomation").click(function () {
    var body = $("#body-cardInfo");
    var title = $("#header-cardInfo");
    var data = $(this).attr("data-index").split(":");
    var kennzeichen = data[0];
    var spedition = data[1];
    $.post(
      "class/ajax",
      { ajaxGetGoodsInformation: kennzeichen },
      function (html) {
        body.html(html);
        title.html(
          "<span class='h4'>" +
            kennzeichen +
            " " +
            spedition +
            "</span> / Sendungen"
        );
      }
    );
  });
}
function openHelpDesk() {
  $("#show-help-box").click(function () {
    var modal = $("#helpdesk");
    var body = $("#helpdesk-body");
    var video = $(this).attr("data");
    modal.modal("show");
    body.load("content/helpdesk", {
      getVideo: video,
    });
  });
}
$(function () {
  var ajaxpath = "class/ajax";
  var iPing = 0;
  showHideNavi();
  setInterval(function () {
    zollWatreListe(werknummer);
    zollDoneListe(werknummer);
  }, 10000);

  setInterval(() => {
    if (iPing == 10) {
      iPing = 0;
    }
    evoChatLastShowMessage(iPing);
    iPing++;
  }, 10000);
  setInterval(() => {
    setOnlineUser(ajaxpath);
  }, 30000);
  zollWatreListe(werknummer);
  zollDoneListe(werknummer);
  openSendBox();
  openContentByRequest();
  moreZollInfomation();
  openHelpDesk();
});
