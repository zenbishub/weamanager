function pressedKey(event, count, index) {
  var vNum = index;
  var keyWord = event.code.substr(0, 5);
  var digit = parseInt(event.code.substr(5));
  if ((event.code == "Numpad4" || event.code == "ArrowLeft") && vNum >= 1) {
    vNum--;
  }
  if ((event.code == "Numpad6" || event.code == "ArrowRight") && vNum < count) {
    vNum++;
  }
  if (event.code == "Numpad2" || event.code == "ArrowDown") {
    if (vNum == 0 || vNum == 3 || vNum == 6 || vNum == 9) {
      vNum = vNum + 3;
    }
    if (vNum == 1 || vNum == 4 || vNum == 7 || vNum == 10) {
      vNum = vNum + 3;
    }
    if (vNum == 2 || vNum == 5 || vNum == 8 || vNum == 11) {
      vNum = vNum + 3;
    }
  }
  if (event.code == "Numpad8" || event.code == "ArrowUp") {
    if (vNum == 12 || vNum == 11 || vNum == 9 || vNum == 6 || vNum == 3) {
      vNum = vNum - 3;
    }
    if (vNum == 13 || vNum == 10 || vNum == 7 || vNum == 4 || vNum == 1) {
      vNum = vNum - 3;
    }
    if (vNum == 14 || vNum == 8 || vNum == 5) {
      vNum = vNum - 3;
    }
  }

  if (keyWord == "Digit") {
    return callVideoById(digit, count, event);
  }
  return vNum;
}
function callVideoById(digit, count, event) {
  var overlay = $("#overlay-video-digit");
  var videoDigit = $("#video-digit");
  var isDigit = $("#video-digit").val();
  var number = isDigit + "" + digit;
  overlay.removeClass("d-none");

  if (number > 0 && number <= 12 && videoDigit.length <= 2) {
    videoDigit.val(number);

    return parseInt(number) - 1;
  } else {
    videoDigit.val("");
  }
}
function videoArray(array, event, index, count) {
  $.each(array, function (i) {
    if (i == index) {
      $(this).focus();
      $(this)
        .parent("div")
        .removeClass("bg-dark")
        .addClass("bg-danger")
        .addClass("p-2");

      if (event.code == "Numpad5" || event.code == "NumpadAdd") {
        $("#overlay-video-digit").addClass("d-none");
        $(this).addClass("fullscreen");
        $(this).focus();
      }

      if (event.code == "NumpadSubtract") {
        $(this).focus();
        $("#overlay-video-digit").addClass("d-none");
        $("video").removeClass("fullscreen");
        $(this).focus();
      }
      if (event.code == "NumpadMultiply" || event.code == "Numpad0") {
        parent.focus();
      }
    }
  });
}
$(function () {
  setTimeout(function () {
    var videos = $("video");
    var count = parseInt(videos.length) - 1;
    index = 0;
    $.each(videos, function (i) {
      if (i == index) {
        $(this).focus();
        $(this)
          .parent("div")
          .removeClass("bg-dark")
          .addClass("bg-danger")
          .addClass("p-2");
      }
    });

    $(document).on("keydown", function (event) {
      console.log(event.code);
      $(".card-body")
        .removeClass("bg-danger")
        .removeClass("p-2")
        .addClass("bg-dark")
        .addClass("p-0");
      index = pressedKey(event, count, index);
      videoArray(videos, event, index, count);
    });
  }, 500);
});
