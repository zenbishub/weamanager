jQuery.fn.clickToggle = function (a, b) {
  return this.on("click", function (ev) {
    [b, a][(this.$_io ^= 1)].call(this, ev);
  });
};
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

$(function () {
  //   $('#datepicker').appendDtpicker({
  //     "autodateOnStart": false,
  //     "timelistScroll": false,
  //     "locale": "de",
  //     "minuteInterval": 15,
  //     "todayButton": true,
  //     "futureOnly": true,
  //     "dateFormat": "DD.MM.YY hh:mm",
  //     "allowWdays": [0, 1, 2, 3, 4, 5, 6] // 0: Sun, 1: Mon, 2: Tue, 3: Wed, 4: Thr, 5: Fri, 6: Sat
  // });
  showHideNavi();
  $("#datepicker").datepicker({
    prevText: "&#x3c;zurück",
    prevStatus: "",
    prevJumpText: "&#x3c;&#x3c;",
    prevJumpStatus: "",
    nextText: "Vor&#x3e;",
    nextStatus: "",
    nextJumpText: "&#x3e;&#x3e;",
    nextJumpStatus: "",
    currentText: "heute",
    currentStatus: "",
    todayText: "heute",
    todayStatus: "",
    clearText: "-",
    clearStatus: "",
    closeText: "schließen",
    closeStatus: "",
    monthNames: [
      "Januar",
      "Februar",
      "März",
      "April",
      "Mai",
      "Juni",
      "Juli",
      "August",
      "September",
      "Oktober",
      "November",
      "Dezember",
    ],
    monthNamesShort: [
      "Jan",
      "Feb",
      "Mär",
      "Apr",
      "Mai",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Okt",
      "Nov",
      "Dez",
    ],
    dayNames: [
      "Sonntag",
      "Montag",
      "Dienstag",
      "Mittwoch",
      "Donnerstag",
      "Freitag",
      "Samstag",
    ],
    dayNamesShort: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
    dayNamesMin: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
    firstDay: 1,
    showWeek: true,
    //dateFormat:'d MM, y'
    dateFormat: "dd.mm",
  });
});
