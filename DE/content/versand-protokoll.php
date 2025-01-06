<?php
extract($_REQUEST);
    $arrayPoints = [
        "Beförderungspapiere aushändigen",
        "ADR-Bescheinigung vorhanden",
        "aktuelle schriftliche Weisungen in der Sprache des Fahrers ausgehändigt"
    ];
?>
<div class="row">
    <div class="col-12 h4 mb-3">
        Warenannahme und Ladungsicherung
    </div>
</div>
<div class="row">
    <div class="col-10 h5 mb-5 ">
        Tätigkeiten für Versandabwicklung
    </div>
    <div class="col-2 p-0">
        <div class="row">
            <div class="col-4 p-0 small text-center">
                Ja
            </div>
            <div class="col-4 p-0 small text-center">
                Nein
            </div>
            <div class="col-4 p-0 small text-center">
                entf.
            </div>
        </div>

    </div>
    <?php $i=1; foreach($arrayPoints as $point):?>
    <div class="col-10 small mb-4">
        <?php echo $i++;?>. <?=$point?>
        <input type="hidden" name="Frage_<?=$i?>" value="<?=$point?>">
    </div>
    <div class="col-2 p-0">
        <div class="row">
            <div class="col-4 text-center">
                <input type="radio" name="check_<?=$i?>" value="ja" required>
            </div>
            <div class="col-4 text-center">
                <input type="radio" name="check_<?=$i?>" value="nein" required>
            </div>
            <div class="col-4 text-center">
                <input type="radio" name="check_<?=$i?>" value="entf." required>
            </div>
        </div>
    </div>
    <?php endforeach;?>
</div>
<div class="row mt-2">
    <div class="col-6">
        <div class="form-text small">für Mitarbeiter Fa. Daimler Buses / Digitale Unterschrift</div>
        <input type="search" class="form-control col-md-6" name="person_sign" id="" placeholder="ID/Personalnummer"
            required>
    </div>
</div>
</div>

<input type="hidden" name="prozess_done" value="versand_protokoll">
<input type="hidden" name="return_uri" value="<?=$confirmaction_versand?>">
<input type="hidden" name="rfnum" value="<?=$rfnum?>">