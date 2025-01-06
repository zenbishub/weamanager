<?php
extract($_REQUEST);
    $arrayPoints = [
        "Frachtbrief empfangen",
        "Speditionsauftrag empfangen",
        "Zollgut-Durchlaufschein"
    ];
?>
<div class="row">
    <div class="col-12 h4 mb-3">
        Warenannahme / Wareneingang
    </div>
</div>
<div class="row">
    <div class="col-8 col-lg-10 h5 mb-5 ">
        Dokumente
    </div>
    <div class="col-4 col-lg-2 p-0">
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
    <div class="col-8 col-lg-10 small mb-4">
        <?php echo $i++;?>. <?=$point?>
        <input type="hidden" name="Frage_<?=$i?>" value="<?=$point?>">
    </div>
    <div class="col-4 col-lg-2 p-0">
        <div class="row">
            <div class="col-4 text-center">
                <div class="form-check form-check-flat">
                    <label class="form-check-label">
                        <input type="radio" name="check_<?=$i?>" value="ja" required>
                        <i class="input-helper"></i>
                    </label>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="form-check form-check-flat">
                    <label class="form-check-label">
                        <input type="radio" name="check_<?=$i?>" value="nein" required>
                        <i class="input-helper"></i>
                    </label>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="form-check form-check-flat">
                    <label class="form-check-label">
                        <input type="radio" name="check_<?=$i?>" value="entf." required>
                        <i class="input-helper"></i>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach;?>
</div>
<div class="row mt-2">
    <div class="col-6">
        <div class="form-text small">für Mitarbeiter Fa. Daimler Buses / Digitale Unterschrift</div>
        <input type="search" autocomplete="off" class="form-control col-md-6 alert-secondary" name="person_sign"
            id="person_sign" placeholder="ID/Personalnummer" required>
        <div id="stamp-error"></div>
    </div>
</div>
</div>

<input type="hidden" name="prozess_done" value="wa_buro">
<input type="hidden" name="return_uri" value="<?=$confirmaction_wa?>">
<input type="hidden" name="rfnum" value="<?=$rfnum?>">