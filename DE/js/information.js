function openPrioBox() {
  $(".set-prio").click(function () {
    const body = $("#prio-body");
    const header = $("#prioModalLabel");
    let data = $(this).attr("alt").split("&");
    let title = data[1];
    let knz = data[2];
    header.html(`<h3>${title}, ${knz}</h3>`);
    body.html(`
    <form action="class/action" id="form-prio">
        <div class="row justify-content-center">
        <div class="col-12 mb-2 p-0 text-center"><h4>Prio für Abladeprozess ändern</h5></div>
            <div class="col-6 p-0">
                <select class="form-control" name="Prio" required>
                    <option value="">Prio Auswahl</option>
                    <option value="SD">Sehr dringend</option>
                    <option value="D">Dringend</option>
                    <option value="unset">Normal</option>
                </select>
            </div>
        <div class="col-4 mb-5">
        <input type="hidden" name="rfnum" value="${data[0]}">
        <input type="hidden" name="add_unloadprio" value="1">
        <input type="hidden" name="returnURI" value="information">
            <button class="btn btn-primary p-2">Prio speichern</button>
        </div>
        </div>
    </form>
    `);
  });
}
function ajaxGetInformation() {
  var onParking = $("#on-parking");
  var imProzess = $("#im-prozess");
  setInterval(function () {
    $.post(
      "class/ajax",
      {
        ajaxGetInformationOnParking: 1,
      },
      function (data) {
        onParking.html(data);
        openPrioBox();
      }
    );
  }, 15000);

  setInterval(function () {
    $.post(
      "class/ajax",
      {
        ajaxGetInformationImProzess: 1,
      },
      function (data) {
        imProzess.html(data);
        openPrioBox();
      }
    );
  }, 20000);
}
$(function () {
  ajaxGetInformation();
  openPrioBox();
});
