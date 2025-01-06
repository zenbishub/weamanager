<?php
$rfnum="";
extract($_REQUEST);
$arrayPoints  = [];

switch($route){
    case "werkschutz":
    case "trailertausch":
            $arrayPoints = [
                "Führerschein vorhanden",
                "Lichtbild übereinstimmend",
                "Name, Vorname übereinstimmend"
            ];
        break;
    case "karosserie":
    case "external":
    case "schrottpicker":
            $arrayPoints = [
                "Führerschein vorhanden",
                "Lichtbild übereinstimmend",
            ];
        break;
    case "zollstelle":
        $arrayPoints = [
            "Zolldokumente in Bearbeitung"
        ];
    break;
    case "werkverlassen":
    case "werkverlassen_ohne_prozess":
        $arrayPoints = [
            "Fahrzeug hat das Firmengelände verlassen",
            "Das ausgehändigte Gerät zurück bekommen"
        ];
    break;
}
?>
<div class="row pt-4">
    <div class="col-10 col-lg-10 small mb-4">
        <span class="display-3" id="docnummer">Führerschein prüfen</span>
    </div>
    <?php if($route=="external"):?>
    <div class="row">
        <div class="col-12 p-0">
            <div class="form-group">
                <label for="Empfaenger">Empfänger</label>
                <input type="search" class="form-control border-secondary" id="Empfaenger"
                    placeholder="Empfänger im Werk" required>
            </div>
        </div>
        <div class="col-12 p-0">
            <div class="form-group">
                <label for="Spedition">Firma / Spedition</label>
                <input type="search" class="form-control border-secondary" id="Spedition"
                    placeholder="Firma / Spedition" required>
            </div>
        </div>
        <div class="col-12 p-0">
            <div class="form-group">
                <label for="Kennzeichen">Autokennzeichen</label>
                <input type="search" class="form-control border-secondary" id="Kennzeichen"
                    placeholder="Autokennzeichen" required>
            </div>
        </div>
    </div>
    <?php endif;?>

    <?php if($route=="schrottpicker"):?>
    <div class="row">
        <div class="col-12 p-0">
            <div class="form-group">
                <label for="Abholgut">Entsorgungsgut</label>
                <select class="form-control" id="Abholgut" required>
                    <option selected>wählen</option>
                    <option value="Metallschrott">Metallschrott</option>
                    <option value="Papier/Kartonagen">Papier / Kartonagen</option>
                    <option value="Holzabfall">Holzabfall</option>
                    <option value="Gefahrgut">Gefahrgut</option>
                    <option value="Sondermüll">Sondermüll</option>
                </select>
            </div>
        </div>
        <div class="col-12 p-0">
            <div class="form-group">
                <label for="Dienstleister">Dienstleister / Firma</label>
                <input type="search" class="form-control border-secondary" autocomplete="off" id="Dienstleister"
                    placeholder="Name des Dienstleisters" required>
            </div>
        </div>

        <div class="col-12 p-0">
            <div class="form-group">
                <label for="Kennzeichen">Autokennzeichen</label>
                <input type="search" class="form-control border-secondary" autocomplete="off" id="Kennzeichen"
                    placeholder="Autokennzeichen" required>
            </div>
        </div>
    </div>
    <?php endif;?>

    <?php if($route=="karosserie"):?>
    <div class="row">
        <div class="col-12 p-0">
            <div class="form-group">
                <label for="Dienstleister">Abladestelle</label>
                <select class="form-control" id="Empfaenger" required>
                    <option value="">wählen</option>
                    <option value="Karosserieplatz">Karosserieplatz</option>
                    <option value="Lacklager 31_34">Lacklager 31 / 34</option>
                    <option value="Lackierung 62301">Lackierung 62301</option>
                    <option value="Instandhaltung 1530">Instandhaltung Halle 30</option>
                    <option value="Instandhaltung 1560">Instandhaltung Halle 60</option>
                    <option value="Holzanlieferung">Holzanlieferung</option>
                    <option value="Gefahrgut Gase">Gefahrgut Gase</option>
                    <option value="Diesel-AdBlue-Lacke">Diesel / AdBlue /Lacke</option>
                </select>
            </div>
        </div>

        <div class="col-12 p-0">
            <div class="form-group">
                <label for="Dienstleister">Dienstleister / Firma</label>
                <input type="search" class="form-control border-secondary" autocomplete="off" id="Dienstleister"
                    placeholder="Name des Dienstleisters" required>
            </div>
        </div>

        <div class="col-12 p-0">
            <div class="form-group">
                <label for="Kennzeichen">Autokennzeichen</label>
                <input type="search" class="form-control border-secondary" autocomplete="off" id="Kennzeichen"
                    placeholder="Autokennzeichen" required>
            </div>
        </div>
    </div>
    <?php endif;?>

    <?php $i=1; foreach($arrayPoints as $point):?>
    <div class="col-10 col-lg-10 small mb-4">
        <?php echo $i++;?>. <?=$point?>
        <input type="hidden" name="Frage_<?=$i?>" value="<?=$point?>">
    </div>
    <div class="col-2 col-lg-2 p-0">
        <div class="row">
            <div class="col-4 text-center">
                <div class="form-check form-check-flat">
                    <label class="form-check-label">
                        <input type="radio" name="check_<?=$i?>" value="ja" required>
                        <i class="input-helper"></i>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach;?>
</div>
<div class="row m-2 p-0 border-top pt-2 justify-content-center">
    <div class="col-12 col-md-9">
        <div class="form-text small">nur für Mitarbeiter</div>
        <input type="search" autocomplete="no" class="form-control col-md-8 alert-secondary" name="person_sign"
            id="person_sign" placeholder="ID/Werkschutz" required readonly>
        <div id="stamp-error"></div>
        <?php include '../content/z-tastatur-small.php';?>
    </div>
</div>
</div>
<input type="hidden" name="checkUserRole" id="route" value="<?=$route?>">
<?php if($route=="werkverlassen"):?>
<input type="hidden" name="vehicle_gone" id="vehicle_gone" value="<?=$rfnum?>">
<?php endif;?>
<?php if($route=="werkverlassen_ohne_prozess"):?>
<input type="hidden" name="delete_by_maingate" id="delete_by_maingate" value="<?=$rfnum?>">
<?php endif;?>