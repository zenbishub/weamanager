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
function lkwFilter() {
  $("#lkw-filter").on("change", function () {
    var val = $(this).get(0).value;
    location.href = "class/action?setLKWfilter=" + val;
  });
}
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
function camImage() {
  var img = $("#camera-frame-image");
  var streamSource = "notactivated.jpg";
  $.post("class/ajax_cam", { checkStream: 1 }, function (response) {
    img.attr("data", response);
  });
  var timestamp = new Date().getTime();
  var datum = new Date().toString();
  var active = img.attr("data");
  switch (active) {
    case "active":
      streamSource = "image.jpg";
      $("#camera-frame").removeClass("d-none");
      break;
    case "notactive":
      streamSource = "notactivated.jpg";
      $("#camera-frame").addClass("d-none");
      break;
  }
  img.attr("src", "cam_image/" + streamSource + "?t=" + timestamp);
  $("#camera-frame-image").click(function () {
    var body = $("#camera-frame-body");
    var frame = $("#camera-frame");
    $("#camera-frame-header").remove();
    $(
      "<div class='card-header' id='camera-frame-header'><span>" +
        datum +
        "</span><span class='pointer float-right' id='btn-close-camimage'><i class='ti ti-close'></i></span></div>"
    ).insertBefore(body);
    frame.css({
      width: "100%",
      height: "100%",
      top: "50%",
      left: "50%",
      transform: "translate(-50%,-50%)",
    });
    $("#btn-close-camimage").click(function () {
      $("#camera-frame-header").remove();
      frame.removeAttr("style");
    });
  });
}
function whoIsOnline() {
  $("#btn-whoisonline").click(function () {
    var body = $("#whoishere-body");
    $("#whoishere").modal("show");
    body.load("content/whoishere");
  });
}
function closeAlert(base) {
  $("#close-alert").click(function () {
    location.href = base;
  });
}
function prozessing() {
  $("#spinner").remove();
  $("body").append(
    "<div id='spinner'><div class='box-middle'><div class='spinner-border text-light' role='status'><span class='visually-hidden'>Loading...</span></div></div></div>"
  );
}
function hideAlertsAfterAppear() {
  setTimeout(function () {
    $("#action-alerts").slideUp("slow");
  }, 5000);
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
function toPrinter(div) {
  var content = $("#" + div).html();
  $("body").html(content);
  window.print();
  location.href = location.href;
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
function formAlert(elem) {
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
          elem.submit();
        },
        Nein: function () {
          $(this).dialog("close");
        },
      },
    });
}
function removeFromOrder() {
  $(".remove-from-order").click(function () {
    var dinamictext = $("#dinamic-text");
    var url = $(this).attr("href");
    dinamictext.text("Lieferfahrzeug aus der Warteschlage entfernen?");
    appAlert(url + "&remove_from_order=1");
    return false;
  });
}
function passThrougtEntry() {
  $(".pass-througt-entry").click(function () {
    var dinamictext = $("#dinamic-text");
    var id = $("#entry_passed").val();
    var return_uri = $("#return_uri").val();
    var url = "class/action?entry_passed=" + id + "&return_uri=" + return_uri;
    dinamictext.text("Lieferfahrzeug auf dem Werksgeländer?");
    appAlert(url);
    return false;
  });
}
function prozessDone() {
  $(".prozess_done").click(function () {
    var dinamictext = $("#dinamic-text");
    var id = $(this).prev("input").val();
    var url = "class/action?prozess_done=" + id;
    dinamictext.text("Das Lieferfahzeug wurde abgeladen?");
    appAlert(url);
    return false;
  });
}
function vehicleGone() {
  $(".vehicle_gone").click(function () {
    //vehicle-gone
    var dinamictext = $("#dinamic-text");
    var id = $(this).prev("input").val();
    var url = "class/action?vehicle_gone=" + id;
    dinamictext.text("Hat Lieferfahrzeug das Werksgeländer verlassen?");
    appAlert(url);
    return false;
  });
}
function syncWarteschlange(ajaxpath) {
  var warteschlange = $("#column-warteschlange");
  var improzess = $("#column-im-prozess");
  var setplaceforrfnum = $("#set-place-for-rfnum");

  $.post(
    ajaxpath,
    {
      ajaxWarteschlange: 1,
    },
    function (response) {
      var screenWidth = $(window).width();
      warteschlange.html(response);
      openMapIframe();
      cardInfomationFrame();
      passThrougtEntry();
      pictureViwever();
      removeFromOrder();
      stopSoundByClick();
      openSendBox();
      showMoreInfomation();
      staplerZuweisen();
      setPlaceBySelect();
      openCreateQrcode();
      checkIfScannerOnline();
      //callOnceByClick();

      soundListener(ajaxpath);
      setIncommingTime();
      $(".card-drag").on("click", function () {
        var holdDelay = 0;
        var elem = $(this).children("div").children("div");
        var elems = $(".card-drag").children("div").children("div");
        elems.removeClass("bg-info").addClass("bg-light");
        elem.removeClass("bg-light").addClass("bg-info");
        if (screenWidth < 1200) {
          var holdDelay = 1500;
        }
        setTimeout(function () {
          $(".card-drag").draggable({
            zIndex: 10,
            drag: function () {
              var selected = $(this);
              var elem = $(".ui-draggable-dragging");
              selected.css({ opacity: ".5" });
              elem.css({ width: "25em", overflow: "hidden" });
            },
            stop: function () {
              var rfnum = $(this).attr("alt");
              $.post(
                ajaxpath,
                {
                  ajaxSetToProzess: 1,
                  rfnum: rfnum,
                },
                function () {
                  setplaceforrfnum.val(rfnum);
                  $(".ui.draggable").fadeOut();
                }
              );
            },
            revert: function () {
              var selected = $(this);
              selected.css({ opacity: "1" });
            },
            containment: "document",
            helper: "clone",
            cursor: "move",
          });
          improzess.droppable({
            accept: "#column-warteschlange > .card-drag",
            classes: {
              "ui-droppable-active": "ui-state-highlight",
            },
            drop: function (event, ui) {
              $("#targetPlace").modal("show");
              //$(".card-drag").draggable("disable");
            },
          });
          showMoreInfomation();
          onclickClearInterval();
        }, holdDelay);
      });
    }
  );
}
function syncImProzess(ajaxpath) {
  var improzess = $("#column-im-prozess");
  $.post(
    ajaxpath,
    {
      ajaxImProzess: 1,
    },
    function (response) {
      improzess.html(response);
      openMapIframe();
      cardInfomationFrame();
      vehicleGone();
      prozessDone();
      removeFromOrder();
      warenannhameKontrolle();
      pictureViwever();
      staplerZuweisen();
      openSendBox();
      showMoreInfomation();
      onclickClearInterval();
      entladePlatzZuweisen();
      checkValidforSignition(ajaxpath);
      openCreateQrcode();
      openReklamation();
      openLeergutMitnahmeDialog();
      openMauellerAuftragDialog();
      openMauellerAuftragDialogAll();
      //checkIfScannerOnline();
      callOnceByClick();
      sendToNextStep();
      soundListenerWeiterleitung(ajaxpath);
    }
  );
}
function setPlaceBySelect() {
  $(".setplace-select").on("change", function () {
    var parentForm = $(this).parent("form");
    var dinamictext = $("#dinamic-text");
    dinamictext.text("Bitte bestätigen!");
    formAlert(parentForm);
  });
}
function syncStaplerbelegung() {
  var staplerOverview = $("#stapler-overview");
  var onlineStaplerOverview = $("#online-stapler-overview");
  staplerOverview.load("content/staplerbelegung", function () {
    pictureViwever();
  });
  onlineStaplerOverview.load("content/stapleronline", function () {
    pictureViwever();
    openMauellerAuftragDialog();
    openMauellerAuftragDialogAll();
    openStaplerAufgaben();
  });
}
function callDataTable(selector) {
  // let table = $("." + selector);

  // console.log(table);

  new DataTable("." + selector, {
    pageLength: 100,

    layout: {
      topStart: {
        buttons: [
          {
            extend: "copy",
            text: 'Kopieren <i class="bi bi-stickies"></i>',
            className: "btn-inverse-primary pt-2 pb-2",
          },
          {
            extend: "excel",
            title: "",
            text: 'Excel <i class="bi bi-file-earmark-excel"></i>',
            className: "btn-inverse-primary pt-2 pb-2",
          },
          {
            extend: "pdfHtml5",
            text: 'PDF <i class="bi bi-file-pdf"></i>',
            orientation: "landscape",
            pageSize: "LEGAL",
            className: "btn-inverse-primary pt-2 pb-2",
          },
          "print",
        ],
      },
      bottomStart: {
        pageLength: {
          menu: [15, 25, 50, 100],
        },
      },
    },

    language: {
      search: "Suchen",
      info: "Anzeige _PAGE_ von _PAGES_",
      infoEmpty: "Keine Datensätze gefunden",
      infoFiltered: "(gefiltert aus _MAX_ Einträgen)",
      lengthMenu: " _MENU_",
      zeroRecords: "Keine Datensätze gefunden",
      paginate: {
        previous: "<",
        next: ">",
        first: "|<",
        last: ">|",
      },
    },
    fnDrawCallback: function () {
      $(".verlauf-row").click(function () {
        $(".verlauf-row").removeClass("bg-primary-custom");
        $(this).addClass("bg-primary-custom");
      });
    },
    bDestroy: true,
  });
}

function syncVerlaufTabelle(ajaxpath) {
  var verlauftabelle = $("#column-verlauf-tabelle");

  $.post(
    ajaxpath,
    {
      ajaxVerlaufTabelle: 1,
    },
    function (response) {
      verlauftabelle.html(response);
      getAnkommendList(ajaxpath);
      callDataTable("dataTable");
    }
  );

  $("#heute-tab").click(function () {
    verlauftabelle.html("");
    syncVerlaufTabelle(ajaxpath);
  });
}
function syncVerlaufTabelleTen(ajaxpath) {
  $("#ten-tab").click(function () {
    var columnverlauftabelleten = $("#column-verlauf-tabelle-10days");
    columnverlauftabelleten.html("");
    $.post(
      ajaxpath,
      {
        ajaxVerlaufTabelleTen: 1,
      },
      function (response) {
        columnverlauftabelleten.html(response);
        getAnkommendList(ajaxpath);
        callDataTable("dataTable");
      }
    );
  });
}
function syncVerlaufTabelleTwenty(ajaxpath) {
  $("#twenty-tab").click(function () {
    var columnverlauftabelletwenty = $("#column-verlauf-tabelle-20days");
    columnverlauftabelletwenty.html("");
    $.post(
      ajaxpath,
      {
        ajaxVerlaufTabelleTwenty: 1,
      },
      function (response) {
        columnverlauftabelletwenty.html(response);
        getAnkommendList(ajaxpath);
        callDataTable("dataTable");
      }
    );
  });
}
function getAnkommendList(ajaxpath) {
  var ankommend = $("#getankommendlist");
  $.post(
    ajaxpath,
    {
      getankommendlist: 1,
    },
    function (response) {
      ankommend.html(response);
      showPreRegistData(ajaxpath);
      entladePlatzZuweisen();
    }
  );
}
function stopSound() {
  // var sound = $(".sound");
  // var btn = $(".stop-sound");
  // var rfnum = btn.attr("alt");
  // if(sound.length>0){
  //   setTimeout(function(){
  //     $.post("class/ajax",{"stopsound":1,"rfnum":rfnum},function(){
  //       btn.remove();
  //       sound.remove();
  //       stopSoundByClick();
  //     });
  //   },30000);
  // }
}
function stopSoundByClick(ajaxpath) {
  var sound = $(".sound");
  var btn = $(".stop-sound");
  if (sound.length > 0) {
    $.each(btn, function () {
      $(this).bind("click", function () {
        var rfnum = $(this).attr("alt");
        //console.log(rfnum);
        $.post(ajaxpath, { stopsound: 1, rfnum: rfnum }, function () {
          btn.remove();
          sound.remove();
          stopSound();
        });
      });
    });
  }
}
function soundModus(ajaxpath) {
  $("#sound-modus").click(function () {
    var modus = $(this).attr("alt");
    $.post(ajaxpath, { changesoundmodus: modus }, function (data) {
      //alert(data);
      location.href = location.href;
    });
  });
}
function warenannhameKontrolle() {
  $(".prozess-done-warenannahme-stapler").click(function () {
    var modal_protokoll = $("#modal-protokoll");
    var rfnum = $(this).attr("alt");
    var platz = $(this).attr("data");
    modal_protokoll.load(
      "content/wa-protokoll",
      { confirmaction_wa: "index", rfnum: rfnum, platz: platz },
      function () {
        $("#cardInfo").modal("hide");
        pressKeyNumberSmall();
        $(".close").click(function () {
          $("#modal-protokoll").html("");
        });
      }
    );
  });
  $(".prozess-done-warenannahme-buro").click(function () {
    var modal_protokoll = $("#modal-protokoll");
    var rfnum = $(this).attr("alt");

    modal_protokoll.load(
      "content/wa-buro",
      { confirmaction_wa: "index", rfnum: rfnum },
      function () {
        //clearInterval(interval1);
        $("#cardInfo").modal("hide");
        pressKeyNumberSmall();
        $(".close").click(function () {
          $("#modal-protokoll").html("");
        });
      }
    );
  });
  $("#prozess-done-versand").click(function () {
    var modal_protokoll = $("#modal-protokoll");
    var rfnum = $(this).attr("alt");

    modal_protokoll.load(
      "content/versand-protokoll",
      { confirmaction_versand: "index", rfnum: rfnum },
      function () {
        $("#cardInfo").modal("hide");
        pressKeyNumberSmall();
        $(".close").click(function () {
          $("#modal-protokoll").html("");
        });
      }
    );
  });
}
function openSendBox() {
  $(".opensendbox").click(function () {
    var elem = $(this).attr("alt").split("%20");
    var returnURI = $("#return");
    var rfnum = $("#rfnum");
    var kfznum = $("#kfznum");
    var werknummer = $("#werknummer");
    var target = $("#message-who");
    var header = $("#header");
    rfnum.val(elem[0]);
    kfznum.val(elem[1]);
    werknummer.val(elem[3]);
    if (elem[4] == "toDriver") {
      target.remove();
    }
    header.text("Nachricht an: " + elem[1] + ", " + elem[2]);
    returnURI.val("index");
  });
}
function pictureViwever() {
  $(".pictureviwever-show").click(function () {
    var elem = $(this);
    var source = $(this).attr("alt");
    var split = $(this).attr("data").split("&");
    var bmi_nummer = split[0];
    var pictureviweverBody = $("#pictureviwever-body");
    var empfaenger = split[1];
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
function pictureHover() {
  $(".picturehover-show").hover(
    function () {
      var elem = $(this);
      var source = $(this).attr("alt");
      var bmi_nummer = $(this).attr("data");

      $(".div-image-hover").remove();
      $(
        "<div class='div-image-hover p-2 bg-light rounded shadow' id='image-popup'></div>"
      ).insertAfter(elem);
      $("#image-popup").load(
        "content/imagepopup",
        { bmi_nummer: bmi_nummer },
        function () {
          $(".image-hover").attr("src", source);
        }
      );
    },
    function () {
      //$(".div-image-hover").remove();
      $("body").click(function () {
        $(".div-image-hover").remove();
      });
    }
  );
}
function confirmAction() {
  $(".confirm-action").click(function () {
    if (!confirm("Action bestätigen!")) {
      return false;
    }
  });
}
function loadEditForm(ajaxpath) {
  $(".open-edit-modal").click(function () {
    var hrefdata = $(this).attr("href").split(":");
    var filter = hrefdata[2];

    var editModalBody = $("#edit-modal-body");
    switch (hrefdata[0]) {
      case "bmi":
        $.post(
          ajaxpath,
          { loadEditFormBMI: 1, editfilter: filter },
          function (data) {
            editModalBody.html(data);
          }
        );
        break;
      case "personal":
        $.post(
          ajaxpath,
          { loadEditFormPersonal: 1, editfilter: filter },
          function (data) {
            editModalBody.html(data);
          }
        );
        break;
      case "entladestellen":
        $.post(
          ajaxpath,
          { loadEditFormEntladestellen: 1, editfilter: filter },
          function (data) {
            editModalBody.html(data);
          }
        );
        break;
    }
  });
}
function staplerZuweisen() {
  $(".set-forkbully").click(function () {
    var carddata = $(this).attr("alt").split(":");
    var rfnum = carddata[0];
    var kfznum = carddata[1];
    var submitButton = $("#submit-button");
    $("#hidden-tmp").remove();
    $(`<div id="hidden-tmp">
        <input type='hidden' name='return' value=''>
        <input type='hidden' name='stapler_for_unload' value='${rfnum}'>
        <input type='hidden' name='kfznum' value='${kfznum}'></div>`).insertAfter(
      submitButton
    );
  });
}
function changeViewport(intv1, intv2, intv3, ajaxpath) {
  var mainViewport = $("#main-viewport");
  var changeViewport = $("#change-viewport");
  var icon = changeViewport.children("i");
  var maincolumn = $(".maincolumn");
  var columnwarteschlange = $("#column-warteschlange");
  var columnimprozess = $("#column-im-prozess");
  var columnright = $("#column-right");

  changeViewport.clickToggle(
    function () {
      if (confirm("Modus Benutzeroberfläche ändern aktivieren?")) {
        columnwarteschlange.html("Content will appear after saving");
        columnimprozess.html("Content will appear after saving");
        columnright.html("Content will appear after saving");
        icon.removeClass("ti-layout").addClass("ti-save");
        $("#workspace").sortable();
        $("#myOrder").sortable();
        $("#column-right").sortable();
        mainViewport.addClass("alert-secondary").addClass("p-2");
        clearInterval(intv1);
        clearInterval(intv2);
        clearInterval(intv3);
        $.each(maincolumn, function () {
          $(this).removeClass("stretch-card");
        });
      }
    },
    function () {
      var maincolumn = $(".maincolumn");
      var arr = [];
      $.each(maincolumn, function () {
        var data = $(this).attr("alt");
        arr.push(data);
      });
      //alert(arr);
      icon.removeClass("ti-save").addClass("ti-layout");
      if (confirm("Variante Speichern?")) {
        $.post(
          ajaxpath,
          {
            saveMyPortview: 1,
            Viewport: arr,
          },
          function () {
            location.href = location.href;
          }
        );
      } else {
        location.href = location.href;
      }
    }
  );
}
function autoChangeStatusWerksverkehr() {
  //300 - 5 Minuten
  $.post(
    "class/publicAjax",
    {
      autoChangeStatusWerksverkehr: 1,
      range: 60,
    },
    function (data) {
      //console.log(data);
    }
  );
}
function showMoreInfomation() {
  $(".show-more-infomation").clickToggle(
    function () {
      var elem = $(this);
      elem
        .parent("div")
        .parent("div")
        .parent("div")
        .removeClass("card-height-small");
    },
    function () {
      var elem = $(this);
      elem
        .parent("div")
        .parent("div")
        .parent("div")
        .addClass("card-height-small");
    }
  );
}
function cardInfomationFrame() {
  $(".card-infomation-frame").click(function () {
    var rfnum = $(this).attr("data");
    var body = $("#cardInfo-body");
    var cardInfo = $(".card-info");
    $.each(cardInfo, function () {
      var cardRfnum = $(this).attr("alt");
      if (cardRfnum == rfnum) {
        var html = $(this).html();

        body.html(html);
        body.children(".card-body ").removeClass("card-height-small");
        body
          .children(".card-body ")
          .children(".row")
          .children(".col-1")
          .html("");
        body
          .children(".card")
          .children(".card-body")
          .removeClass("card-height-small");
        body
          .children(".card")
          .children(".card-body")
          .children(".row")
          .children(".col-1")
          .html("");
      }

      warenannhameKontrolle();
      checkIfScannerOnline();
      callOnceByClick();
      entladePlatzZuweisen();
      setIncommingTime();
      staplerZuweisen();
      openMauellerAuftragDialog();
      openLeergutMitnahmeDialog();
      sendToNextStep();
      openMapIframe();
      return;
    });
  });
}
function onclickClearInterval() {
  $(".onclick-clear-interval").click(function () {
    $("#statusleiste").html(
      "<span id='cleared' class='small'>Interval Stopped</span>"
    );
  });
}
function saveOrder(column, ajaxpath) {
  var cardOrder = $(".card-order");
  var arrIds = [];

  $.each(cardOrder, function () {
    var rfnum = $(this).attr("alt");
    arrIds.push(rfnum);
  });

  $.post(
    ajaxpath,
    { save_neworder: column, neworder: arrIds },
    function (data) {
      //console.log(data);
      //location.reload();
    }
  );
}
function entladePlatzZuweisen() {
  $(".set-unload-place").click(function () {
    var setplaceforrfnum = $("#set-place-for-rfnum");
    var rfnum = $(this).attr("alt");
    $("#cardInfo").modal("hide");
    $("#targetPlace").modal("show");
    setplaceforrfnum.val(rfnum);
  });
}
function openMessageBoxEvoChat(ajaxpath) {
  $("#send_to_evochat, .message-item").click(function () {
    var intv;
    var split = $(this).attr("data").split("&");
    var bmi_nummer = split[0];
    var absender = $("#Absender");
    var target = $("#target");
    var header = $("#header-evochat");
    var sendto = $("#send-to-evochat");
    var returnURI = $("#returnURI");
    var verlauf = $("#verlauf");
    returnURI.val("");
    verlauf.html("");
    $("html").css("overflow", "hidden");

    $.post(
      ajaxpath,
      {
        getBMINummForChatList: 1,
        callChat: "Zentrale",
      },
      function (data) {
        header.html(data);
        $("#select-empfaenger").on("change", function () {
          bmi_nummer = $(this).get(0).value;
          sendto.val(bmi_nummer);
          target.val(bmi_nummer);
          absender.val("Zentrale");
          verlauf.html(
            "<div class='p-4 text-center text-primary'><span class='spinner-border spinner-border-sm text-primary' role='status' aria-hidden='true'></span> Chat loading...</div>"
          );
          syncMessages(bmi_nummer, ajaxpath);
        });
      }
    );
    sendto.val(bmi_nummer);
    target.val(bmi_nummer);
    syncMessages(bmi_nummer, ajaxpath);
    $("#evochatModal").modal("show");
    //$(".modal-backdrop").remove();
    $("#btn-chat-close").click(function () {
      verlauf.html("close");
      $("html").removeAttr("style");
      bmi_nummer = null;
    });
    $("#chat_attachment").on("change", function () {
      var value = $(this).val().split("/");
      if (value != "") {
        $("#showinputvalue").html("<span class='small'>image loaded</span>");
      }
    });
  });
}
function syncMessages(bmi_nummer, ajaxpath, count = 0) {
  var verlauf = $("#verlauf");
  $.post(
    ajaxpath,
    {
      getChatVerlauf: 1,
      user: bmi_nummer,
      empfaenger: "",
      absender: "Zentrale",
      returnURI: "",
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
function checkValidforSignition(ajaxpath) {
  $("#protokoll-form").submit(function () {
    var sendBtn = $("#change-position-status");
    var before = sendBtn.text();
    var Personalnummer = $("#person_sign").val();
    var stampError = $("#stamp-error");
    $(this).ajaxSubmit({
      beforeSubmit: function () {
        $.post(
          ajaxpath,
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
        alert("Fehler beim senden!");
      },
      success: function () {
        prozessing();
        $.post(
          ajaxpath,
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

            location.href = location.href;
          }
        );
      },
    });
    return false;
  });
}
function evoChatLastShowMessage(iPing, ajaxpath) {
  var elem = $("#getmessage");
  var werknummer = elem.attr("title");
  var showmessage = $("#getmessage-text");

  if (elem.length > 0) {
    $.post(
      "class/publicAjax",
      {
        evoChatLastShowMessage: "Zentrale",
        werknummer: werknummer,
        empfaenger: "Zentrale",
      },
      function (response) {
        //console.log(werknummer);
        if (response != "") {
          elem.removeClass("d-none");
          var pinging = "";

          if (iPing == 1 || iPing == 5) {
            pinging =
              "<embed src='assets/sound/pinging.mp3' class='pingsound' style='opacity:0; height:.1em; width:.1em;'></embed>";
          }
          showmessage.html(response + pinging);
          openMessageBoxEvoChat(ajaxpath);
          $("#close-getmessage").click(function () {
            //var key = $(this).next("div").children("div").attr("data");
            var key = showmessage.children(".message-item").attr("data-index");

            $.post(
              "class/publicAjax",
              {
                evochatmessagereaded: key,
              },
              function (response) {
                if (response) {
                  location.reload();
                }
              }
            );
          });
        }
      }
    );
  }
}
function showPreRegistData(ajaxpath) {
  $(".show-preregist-data").click(function () {
    var id = $(this).attr("alt");
    var preregisterata = $("#pre-register-data");
    $.post(ajaxpath, { getPreRegisterData: 1, preID: id }, function (response) {
      preregisterata.html(response);
    });
  });
  $(".more-information-preregister").click(function () {
    var kennzeichen = $(this).attr("data");
    var body = $("#preRegisterInfo-body");
    $.post(
      ajaxpath,
      { getInformFromPreRegisterData: kennzeichen },
      (response) => {
        body.html(response);
      }
    );
  });
}
function showQRcode(output, qrtext) {
  output.html("").qrcode({
    width: 100,
    height: 100,
    text: qrtext,
  });
}
function openCreateQrcode() {
  $(".create-qrcode").click(function () {
    var nummer = $(this).attr("alt").replace(/\s/g, "");
    var spedition = $(this).attr("title");
    var output = $("#canvas-qrcode");
    var createQrcodeModalLabel = $("#createQrcodeModalLabel");
    var outputText = $("#box-qrcodetext");
    var printBtn = $("#qrcode-print-btn");
    createQrcodeModalLabel.html("Schnellanmeldungs-QRcode für " + nummer);
    outputText.html(
      "<p class='h5'>QR Code für schnelle Anmeldung Autokennzeichen</p><h3>" +
        nummer +
        "</h3><p class='h4'>" +
        spedition +
        "</p>"
    );
    showQRcode(output, "saved-" + nummer);
    printBtn.click(function () {
      var content = $("#createQrcode").html();

      $(".container-fluid, .navbar, .footer").hide();
      $(".hide-by-print").css({ opacity: "0" });
      showQRcode(output, nummer);
      window.print(content);

      $(".container-fluid, .navbar, .footer").show();
      $(".hide-by-print").css({ opacity: "1" });
      //location.href=location.href;
    });
  });
}
function openCreateStaplerQRcode() {
  $(".create-access-qrcode").click(function () {
    var value = $(this).attr("alt");
    var title = $(this).attr("title");
    var body = $("#createQRcode-body");
    $("#createQRcode-footer").remove();
    body
      .addClass("text-center")
      .html("<h3>" + title + "</h3><div id='canvas-qrcode'></div>");
    $(
      "<div class='card-footer text-end' id='createQRcode-footer'><button class='btn btn-primary' id='pr'>drucken</button></div>"
    ).insertAfter(body);
    var output = $("#canvas-qrcode");
    showQRcode(output, value);
    $("#pr").click(function () {
      $("body").html(body);
      $("body").css({ "background-color": "#fff" });
      showQRcode(output, value);
      window.print();
      location.href = location.href;
    });
  });
}
function openReklamation() {
  $(".open-reklamation").click(function () {
    var elem = $(this).attr("alt");
    var sendBtn = $("#reklamation-btn");
    var reklamation_hidden_rfnum = $("#reklamation_hidden_rfnum");
    reklamation_hidden_rfnum.val(elem);
  });
}
function openLeergutMitnahmeDialog() {
  $(".open-leergut-mitnahme-dialog").click(function () {
    var rfnum = $(this).attr("alt");
    var changeModalSize = $(".modal-fullscreen");
    var body = $("#diverse-modal-body");
    changeModalSize
      .removeClass("modal-fullscreen")
      .addClass("modal-defaultsize");
    body.load("content/leergut-mitnahme", { rfnum: rfnum }, function () {
      pictureHover();
    });
  });
}
function openMauellerAuftragDialog() {
  $(".open-maueller-auftrag-dialog").click(function () {
    var body = $("#ma-auftrag-modal-body");
    var title = $("#manuellerAuftragLabel");
    var rfnum = $(this).attr("alt");
    title.text("Manueller Auftrag erstellen");
    $("#manuellerAuftrag").modal("show");
    $("#ma-auftrag-footer").removeClass("d-none");
    $("#ma-auftrag-modal-dialog").removeClass("modal-xl");
    body.load("content/manueller-auftrag", { rfnum: rfnum }, function () {
      $("#cardInfo").modal("hide");
    });
  });
}
function openMauellerAuftragDialogAll() {
  $(".open-maueller-auftrag-dialog-overview").click(function () {
    var body = $("#ma-auftrag-modal-body");
    var title = $("#manuellerAuftragLabel");
    var rfnum = $(this).attr("alt");
    title.text("alle Manuelle Aufträge");
    $("#ma-auftrag-footer").addClass("d-none");
    $("#ma-auftrag-modal-dialog").addClass("modal-xl");
    $("#manuellerAuftrag").modal("show");

    body.load(
      "content/manueller-auftrag-alle",
      { rfnum: rfnum },
      function () {}
    );
  });
}
function sortColumnContent(ajaxpath) {
  showHideNavi();
  $("#save-order").clickToggle(
    function () {
      var elem = $(this);
      elem.addClass("bg-success");
      $(".card-head-prozess")
        .removeClass("bg-light")
        .addClass("bg-warning")
        .css({ cursor: "move" });
      $("#column-im-prozess").sortable({
        update: function () {
          saveOrder("Mittel", ajaxpath);
        },
      });
    },
    function () {
      location.reload();
    }
  );

  $("#save-prio").clickToggle(
    function () {
      var elem = $(this);
      elem.addClass("bg-success");
      $(".card-head-hover").removeClass("bg-light").addClass("bg-warning");
      $("#column-warteschlange").sortable({
        update: function () {
          saveOrder("Leftcolumn", ajaxpath);
        },
      });
    },
    function () {
      location.reload();
    }
  );
}
function toParkingOverview() {
  $("#btn-toParkingOverview").click(function () {
    var body = $("#toParkingOverview-body");
    var header = $("#toParkingOverviewModalLabel");
    body
      .removeClass("p-lg-3")
      .html("<iframe src='information' id='iframe-lkw-parking'></iframe>");
  });
}
function toVoranmeldung(base) {
  $("#btn-toVoranmeldung").click(function () {
    body = $("#toVoranmeldungOverview-body");
    body
      .removeClass("p-1")
      .removeClass("p-lg-3")
      .addClass("p-0")
      .html(
        "<iframe src='" + base + "comingup' id='iframe-vor-anmeldung'></iframe>"
      );
  });
}
function toEmailupload(ajaxpath) {
  $("#btn-to-emailupload").click(function () {
    body = $("#toVoranmeldungUpload-body");
    $.get(ajaxpath, { getEimalUploadForm: 1 }, function (response) {
      body.html(response);
    });
  });
}
function soundListener(ajaxpath) {
  setTimeout(function () {
    $.post(
      ajaxpath,
      {
        soundListener: 1,
      },
      function (response) {
        var isSound = $("#sound-file-reminding").length;
        // if (response == "") {
        //   $("#sound-file-reminding").remove();
        //   return;
        // }

        if (isSound == 0 && response != "") {
          $("body").append(response);
          $("#reminderModal").modal("show");
          var sound = document.getElementById("sound-file-reminding");
          sound.play();
          $("[data-bs-dismiss=modal]").on("click", function () {
            sound.pause();
          });
        }
      }
    );
  }, 5000);
}

function soundListenerWeiterleitung(ajaxpath) {
  setTimeout(function () {
    $.post(
      ajaxpath,
      {
        soundListenerWeiterleitung: 1,
      },
      function (response) {
        if (response != "") {
          var isSound = $("#sound-file-reminding").length;
          if (isSound == 0) {
            $("body").append(response);

            $("#reminderModalweiterleitung").modal("show");
            var sound = document.getElementById(
              "sound-file-reminding-wartenummer"
            );
            sound.play();
            $("[data-bs-dismiss=modal]").on("click", function () {
              sound.pause();
            });
          }
        }
      }
    );
  }, 8000);
}

function setIncommingTime() {
  $(".set-incomming-time").click(function () {
    var body = $("#setIncomminTime-body");
    var data = $(this).attr("alt").split(":");
    body.load("content/set_incomming_time", {
      rfnum: data[0],
      Nummer: data[1],
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
      returnURI: "../de/",
    });
    $("#sendtonextstep").modal("show");
  });
}
function openStaplerAufgaben() {
  $("#open-stapler-aufgaben").click(function () {
    var body = $("#staplerAufgaben-body");
    body.load("content/stapler-aufgaben.php", function () {});
  });
}
function pressKeyNumberSmall() {
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
  const ajaxpath = "class/ajax";
  var iPing = 0;
  var intv1 = setInterval(() => {
    syncWarteschlange(ajaxpath);
  }, 20000);
  var intv2 = setInterval(() => {
    syncImProzess(ajaxpath);
    getAnkommendList(ajaxpath);
  }, 20000);
  var intv3 = setInterval(() => {
    syncStaplerbelegung();
  }, 10000);
  setInterval(() => {
    setOnlineUser(ajaxpath);
  }, 30000);
  // clearInterval(intv1);
  // clearInterval(intv2);

  setInterval(autoChangeStatusWerksverkehr, 3000);
  // setInterval(() => {
  //   syncVerlaufTabelle(ajaxpath);
  // }, 5000);
  // setInterval(() => {
  //   syncVerlaufTabelleTen(ajaxpath);
  // }, 20000);
  // setInterval(() => {
  //   syncVerlaufTabelleTwenty(ajaxpath);
  // }, 20000);

  setInterval(() => {
    if (iPing == 10) {
      iPing = 0;
    }
    evoChatLastShowMessage(iPing, ajaxpath);
    iPing++;
  }, 10000);
  setInterval(() => {
    camImage();
  }, 1000);
  openMapIframe();
  lkwFilter();
  sortColumnContent(ajaxpath);
  openMessageBoxEvoChat(ajaxpath);
  hideAlertsAfterAppear();
  removeFromOrder();
  changeViewport(intv1, intv2, intv3, ajaxpath);
  passThrougtEntry();
  vehicleGone();
  prozessDone();
  syncWarteschlange(ajaxpath);
  syncImProzess(ajaxpath);
  syncStaplerbelegung();
  getAnkommendList(ajaxpath);
  stopSound();
  soundModus(ajaxpath);
  openSendBox();
  stopSoundByClick(ajaxpath);
  warenannhameKontrolle();
  pictureViwever();
  confirmAction();
  loadEditForm(ajaxpath);
  onclickClearInterval();
  syncVerlaufTabelle(ajaxpath);
  syncVerlaufTabelleTen(ajaxpath);
  syncVerlaufTabelleTwenty(ajaxpath);
  openHelpDesk();
  cardInfomationFrame();
  toParkingOverview();
  openCreateQrcode();
  openCreateStaplerQRcode();
  openMauellerAuftragDialog();
  openMauellerAuftragDialogAll();
  pictureHover();
  toVoranmeldung(base);
  closeAlert(base);
  toEmailupload(ajaxpath);
  sendToNextStep();
  whoIsOnline();
});
