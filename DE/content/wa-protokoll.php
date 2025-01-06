<?php
$platz = "";
extract($_REQUEST);
    $arrayPoints = [
        "Versandstücke ohne Beschädigungen, dicht, außen keine gefährlichen Anhaftungen",
        "Ladefläche sauber und trocken, keine gefährliche Verunreinigungen",
        "Ladungsicherungsmittel ausreichend und in ordnungsgemäßem Zustand (DLR 9.5)",
        "Ladung ausreichend gegen Verrutschen, Umfallen, Herunterfallen gesichert (DLR 9)"
    ];
    
?>
<div class="row">
    <div class="col-12 h4 mb-3 mt-3">
        Warenannahme und Ladungsicherung
    </div>
</div>
<?php if($platz != "Schrottplatz"):?>
<div class="row">
    <div class="col-8 col-lg-10 h5 mb-5 ">
        Tätigkeiten für Verlader
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
            <div class="col-4 p-0 p-md-2 text-center">
                <div class="form-check form-check-flat">
                    <label class="form-check-label">
                        <input type="radio" name="check_<?=$i?>" value="ja" required>
                        <i class="input-helper"></i>
                    </label>
                </div>
            </div>
            <div class="col-4 p-0 p-md-2 text-center">
                <div class="form-check form-check-flat">
                    <label class="form-check-label">
                        <input type="radio" name="check_<?=$i?>" value="nein" required>
                        <i class="input-helper"></i>
                    </label>
                </div>
            </div>
            <div class="col-4 p-0 p-md-2 text-center">
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
<?php endif;?>
<div class="row m-2 p-0 border-top pt-3">
    <div class="col-12 col-md-6">
        <div class="form-text small">für Mitarbeiter Fa. Damler Buses / Digitale Unterschrift</div>
        <input type="search" autocomplete="off" class="form-control col-md-6 alert-secondary" name="person_sign"
            id="person_sign" placeholder="ID/Personalnummer" required readonly>
        <div id="stamp-error"></div>
        <?php include '../content/z-tastatur-small.php';?>
    </div>
</div>
</div>

<input type="hidden" name="prozess_done" value="wa_protokoll">
<input type="hidden" name="return_uri" value="<?=$confirmaction_wa?>">
<input type="hidden" name="rfnum" value="<?=$rfnum?>">