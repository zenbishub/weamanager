function syncMessagesAfterSending(bmi_nummer, absender, count = 0) {
  var verlauf = $("#verlauf");
  $.post(
    "class/ajax.php",
    {
      getChatVerlauf: 1,
      user: bmi_nummer,
      empfaenger: "",
      absender: absender,
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
function senMessageAsync() {
  $("#evochat-form").submit(function () {
    var sendBtn = $("#evochat-send-btn");
    var before = sendBtn.text();
    var bmi_nummer = $("#send-to-evochat").val();
    var absender = $("#Absender").val();
    var verlauf = $("#verlauf");
    $(this).ajaxSubmit({
      beforeSubmit: function () {
        verlauf.html(
          '<span class="small d-block">sending...</span><div class="progress"><div class="progress-bar" id="progress-bar" role="progressbar"  aria-valuemin="0" aria-valuemax="100"></div></div>'
        );
      },
      uploadProgress: function (event, position, total, percentComplete) {
        console.log(percentComplete + "%");
        $("#progress-bar").width(percentComplete + "%");
        $("#progress-bar").html(
          '<div id="progress-status"><span id="procent"></span></div>'
        );
        //$("#upload-procent").text("Upload "+percentComplete+"%");
      },
      clearForm: true,
      resetForm: true,
      error: function () {
        alert("Warnung!", "Fehler: Upload nicht erfolgreich");
      },
      success: function (response) {
        //console.log(response);
        $.post(
          "class/ajax.php",
          {
            getChatVerlauf: 1,
            user: bmi_nummer,
          },
          function (data) {
            $("#audio").html("");
            $("#addaudio").val("");
            sendBtn.html(
              "<span class='spinner-border spinner-border-sm text-light' role='status' aria-hidden='true'></span>"
            );
            syncMessagesAfterSending(bmi_nummer, absender, 0);
            setTimeout(() => {
              var scrollY = verlauf.height();
              $("#evochat-verlauf").scrollTop(scrollY);
              sendBtn.html(before);
            }, 1000);
          }
        );
      },
    });
    return false;
  });
}

$(function () {
  senMessageAsync();
});
