var statusDIV = "#status";
let type = navigator.connection.effectiveType;

function updateConnectionStatus() {
  if (navigator.connection.effectiveType == "2g") {
    $("body").append(
      "<div id='prevent-overlay'><div id='offline-text' class='display-1 text-light'>SLOW</div></div>"
    );
  }
  if (navigator.connection.effectiveType == "4g") {
    $("#prevent-overlay").remove();
  }
  type = navigator.connection.effectiveType;
}
navigator.connection.addEventListener("change", updateConnectionStatus);

window.addEventListener("load", (event) => {
  statusDIV.textContent = navigator.onLine ? "Online" : "OFFline";
  var connection = navigator.onLine;
  var statusDisplay = $("#wifi-status");
  if (statusDisplay.lenght > 0) {
    statusDisplay.className = "text-danger p-2";
  }
  if (connection) {
    $("#status").append(
      " <span id='check-connection' class='small'><span id='sessiontime' class='float-end'>Status </span>"
    );
    $("#wifi-status").addClass("text-success").removeClass("text-danger");
  }
  var sessiontime = $("#sessiontime");
  if (sessiontime.lenght > 0) {
    sessiontime();
  }
});
function reloadApp() {
  $("#connectionOffline-body, #wifi-status").click(function () {
    var Check = prompt("Geben Sie Geräte-PIN ein", "");
    if (Check == "8080") {
      location.href = location.href;
    }
  });
}
window.addEventListener("offline", (event) => {
  var body = $("#connectionOffline-body");
  var header = $("#header-connectionOffline");
  header.remove();
  $("#connectionOffline").modal("show").css({ top: "3em" });
  body.html(
    "<div class='row p-5'><div class='col-12 text-center'><h3 class='text-center'>Lost Connection</h3</div></div>"
  );
  $("#wifi-status").removeClass("text-success").addClass("text-danger");
  reloadApp();
});

window.addEventListener("online", (event) => {
  $("#check-connection").removeClass("text-danger").addClass("text-success");
  $("#wifi-status").addClass("text-success").removeClass("text-danger");
  $("#connectionOffline").modal("hide");
});
