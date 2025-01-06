function inputData() {
  $("#search_input").on("keyup", function () {
    var elem = $(this);
    var radio = $("[name='filter_radio']");
    var filter = null;
    $.each(radio, function () {
      var checked = $(this).prop("checked");
      if (checked == true) {
        filter = $(this).val();
      }
    });
    console.log(filter);
    $.post(
      "class/ajax",
      {
        ajaxFindSendungsnummer: 1,
        filter: filter,
      },
      function (response) {
        //console.log(response);
        // return;
        var array = JSON.parse(response);
        let unique = [...new Set(array)];
        //console.log(unique);
        elem.autocomplete({
          source: unique,
        });
        openData(filter);
      }
    );
  });
}
function openData(filter) {
  $("#open-sendung-data").click(function () {
    var input = $("#search_input");
    var value = input.val();
    var body = $("#dataSendung-body");
    if (value != "") {
      $.post(
        "class/ajax",
        { getSendungsData: value, filter: filter },
        function (response) {
          body.addClass("overflow-auto").html(response);
          $("#dataSendung").modal("show");
        }
      );
    }
  });
}
function openLieferInfo() {
  $(".open-liefer-info").click(function () {
    var value = $(this).attr("data");
    var body = $("#dataSendung-body");

    $.post(
      "class/ajax",
      { getSendungsDataByNumber: value },
      function (response) {
        body.addClass("overflow-auto").html(response);
        $("#dataSendung").modal("show");
      }
    );
  });
}
$(function () {
  inputData();
  openLieferInfo();
});
