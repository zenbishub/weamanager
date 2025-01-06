function startVideo() {
  var modal = $("#startvideo-modal");
  var body = $("#startvideo-modal-body");
  var header = $("#header-startvideo-modal");
  header.remove();
  modal.addClass("p-0");
  body
    .removeClass("p-1")
    .removeClass("p-lg-3")
    .addClass("p-0")
    .html(
      `<div class="row">
            <div class="col-12 p-0 text-center">
                <div class="card border-0">
                    <div class="card-body p-0" style="background:#000">
                        <video id="startvideo" style="height: 99.5vh;" controls loop autoplay="true" muted="muted">
                            <source src="assets/vid/TMI_Anmeldung_reduced.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </div>`
    );
  setTimeout(function () {
    modal.modal("show");
    $("body").hover(function () {
      modal.modal("hide");
    });
  }, 1000);
}
$(function () {
  var screenWidth = $(window).width();
  if (screenWidth > 520) {
    var count = 1;
    setInterval(function () {
      //console.log(count);
      if (count == 300) {
        startVideo();
      }
      $("html").hover(function () {
        count = 1;
      });
      count++;
    }, 1000);
  }
});
