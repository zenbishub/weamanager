function openQrcodeReader() {
  $("#open-qrcode-reader").click(function () {
    var body = $("#readQRModal-body");
    body.load("content/readloginqrcode.php");
  });
}
$(function () {
  openQrcodeReader();
});
