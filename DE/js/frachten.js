function ajaxDataPicking() {
  $.post(
    "class/ajax",
    {
      frachtenData: 1,
    },
    function (response) {
      //console.log(response);
    }
  );
}
$(function () {
  ajaxDataPicking();
});
