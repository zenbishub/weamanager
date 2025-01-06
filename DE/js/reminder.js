function setTurnus() {
  var turnus = $("#turnus");
  var days = $("#weekDays");
  turnus.on("change", function () {
    var value = $(this).get(0).value;
    if (value == 2) {
      days.removeClass("d-none");
    }
  });
}
function showReminder() {
  setInterval(function () {
    $.post("class/ajax", { checkRemider: 1 }, function (response) {
      if (response != "") {
        var toArray = JSON.parse(response);
        var body = $("#reminder-body");
        var header = $("#reminderModalLabel");
        var close_reminder = $("#close-reminder");
        header.text("Erinnerung für " + toArray["Ersteller"]);
        body.html(
          "<h5>" + toArray["Uhrzeit"] + "</h5>" + toArray["Erinnerung-Text"]
        );
        if (close_reminder.length == 0) {
          $(
            "<div class='card-footer'><a href='class/action?setReminderID=" +
              toArray["Reminder-ID"] +
              "' class='btn btn-primary text-light' id='close-reminder'>Danke für die Erinnerung</a></div>"
          ).insertAfter(body);
        }
        $("#reminder").modal("show");
      }
    });
  }, 10000);
}
$(function () {
  setTurnus();
  showReminder();
});
