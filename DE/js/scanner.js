function checkIfScannerOnline() {
  $(".checkScannerOnline").click(function () {
    var num = $(this).attr("alt");
    var span = $(this).prev("span");
    console.log(num);
    span.html(
      '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">ping...</span></div>'
    );
    $.post(
      "class/ajax",
      {
        checkScannerOnline: 1,
        checkIP: num,
      },
      function (data) {
        span.html(data);
      }
    );
  });
}
function callOnceByClick() {
  $(".call-once-by-click").click(function () {
    var btn = $(this);
    var rfnum = $(this).attr("data-index");
    $.post(
      "class/ajax",
      {
        callOnceByClick: 1,
        callrfnum: rfnum,
      },
      function (response) {
        if (response == "1") {
          btn.removeClass("btn-info").addClass("btn-success");
        }
      }
    );
  });
}
$(function () {
  checkIfScannerOnline();
  callOnceByClick();
});
