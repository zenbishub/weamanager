function completeFieldsByScan(Nummer) {
  var form_lieferung = $("#lieferung-erfassen");
  var input_quickmodus = $("#lieferung-schnellerfassen");
  var register_form_to_waitlist = $("#register-form-to-waitlist");
  var waitlist = $("#waitlist");

  if (Nummer != "") {
    form_lieferung.removeClass("d-none");
    register_form_to_waitlist.removeClass("d-none");
    input_quickmodus.addClass("d-none");
    waitlist.addClass("d-none");

    $.post(
      "class/action.php",
      { requestfrzdatabyscanqrcode: 1, modifyNummer: Nummer },
      function (response) {
        console.log(response);
        var split = response.split(",");
        var firma = $("#firma_autocomplete");
        var knznummer = $("#knznummer_autocomplete");
        var FRZTyp = $("#FRZTyp");
        var name_fahrer = $("#name_fahrer_autocomplete");
        var knznummer_aufleger = $("#knznummer_aufleger");
        var radio_legitimation_licens = $("#radio_legitimation_licens");
        var legitimation = $("#legitimation");
        var radio = $("input[type=radio]");

        $.each(radio, function () {
          var val = $(this).val();
          if (val == split[5]) {
            $(this).prop("checked", true);
          }
        });

        firma.val(split[1]).addClass("alert-success");
        knznummer.val(split[3]).addClass("alert-success");
        FRZTyp.addClass("alert-success");
        FRZTyp.append(
          '<option value="' + split[2] + '">' + split[2] + "</option>"
        );
        FRZTyp.val(split[2]);
        name_fahrer.val(split[4]).addClass("alert-success");
        knznummer_aufleger.val(split[5]).addClass("alert-success");

        if (split[6] == "Führerschein") {
          radio_legitimation_licens.prop("checked", "checked");
        }

        legitimation.val(split[7]).addClass("alert-success");
        $("#btn-quickmodus").text("Schnellanmeldung");
      }
    );
  }
}
