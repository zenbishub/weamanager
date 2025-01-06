<?php 
extract($_REQUEST);
//echo $getRFnum;
require_once '../class/Controller.php';
require_once '../class/Sqlite.php';
$controller = new Controller();
$langfile = strtolower($_SESSION['wealanguage']);
$readDB = file_get_contents("../languages/$langfile/".$langfile."_formtext.json");
$form = json_decode($readDB,true);
$readDB = file_get_contents("../languages/$langfile/".$langfile."_dropdown-one.json");
$dropOne  = json_decode($readDB,true);
$readDB = file_get_contents("../languages/$langfile/".$langfile."_dropdown-two.json");
$dropTwo = json_decode($readDB,true);
$array = $controller->editRFnumData($getRFnum,"../");
$ladungDaten = $controller->selectLadungDaten("../");
$frzTypDaten = $controller->selectFRZDaten("../");
?>

<div class="row justify-content-center">

    <h3>Wartenummer bearbeiten <span class="h1 float-end"><?=$getRFnum?></span></h3>
    <form method="POST" action="class/action.php" id="formular-lieferung-erfassen" enctype="multipart/form-data">
        <div class="row mb-3">
            <div class="col-6 p-0">
                <div id="emailHelp12" class="form-text small"><?=$form['string15']?> *</div>
                <select class="form-control text-black" name="ladung" id="ladung" required>
                    <option value=""><?=$form['string36']?></option>
                    <?php $i=0; foreach($ladungDaten as $daten):?>
                    <option value="<?=$daten['KN']?>"><?=$dropOne['string'.$i]?></option>
                    <?php $i++;
                        endforeach;?>
                </select>
            </div>
        </div>
        <div class="row mb-3" id="row-leegut-abholnummer">
            <div class="col-6 p-0">
                <div id="emailHelp13" class="form-text small"><?=$form['string43']?></div>
                <input type="search" class="form-control" name="leegut_abholnummer" id="leegut_abholnummer"
                    value="<?=$array['leegut_abholnummer']?>">
            </div>
        </div>
        <div class="row mb-3">
            <div class="mb-1 col-md-6 ps-0 pe-0 pe-md-2">
                <div id="emailHelp5" class="form-text small"><?=$form['string11']?> *</div>
                <input type="search" class="form-control" name="firma" value="<?=$array['Firma']?>" placeholder=""
                    required>
            </div>
            <div class="mb-1 col-md-6 p-0">
                <div id="emailHelp6" class="form-text small"><?=$form['string12']?> *</div>
                <input type="search" class="form-control" name="name_fahrer" value="<?=$array['Name Fahrer']?>"
                    placeholder="" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6 ps-0 pe-2 pe-md-2">
                <div class="mb-1">
                    <div id="emailHelp10" class="form-text small"><?=$form['string9']?> *
                    </div>
                    <select class="form-control text-black" name="FRZTyp" id="FRZTyp" required>
                        <option value=""><?=$form['string36']?></option>
                        <?php $i=1; foreach($frzTypDaten as $typ):?>
                        <option value="<?=$typ['value']?>"><?=$dropTwo['string'.$i]?>
                        </option>
                        <?php $i++; endforeach;?>
                    </select>
                </div>
            </div>
            <div class="col-6 p-0">
                <div id="emailHelp9" class="form-text small"><?=$form['string10']?> *</div>
                <input type="search" class="form-control" name="knznummer" value="<?=$array['Nummer']?>" placeholder=""
                    required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6 p-0 pe-2 pe-0 pe-lg-2">
                <div id="emailHelp11" class="form-text small"><?=$form['string13']?></div>
                <input type="search" class="form-control" name="knznummer_aufleger"
                    value="<?=$array['knznummer_aufleger']?>" placeholder="">
            </div>
        </div>
        <div class="row p-2 mb-1 alert-danger border rounded">
            <div class="col-3 p-0">
                <div class="form-text small"><?=$form['string16']?> *</div>
            </div>
            <div class="col-3 p-0 form-check form-check-flat">
                <label class="form-check-label small">
                    <input type="radio" name="Gefahrgut" value="NEIN" required><?=$form['string23']?>
                    <i class="input-helper"></i>
                </label>
            </div>
            <div class="col-3 p-0 form-check form-check-flat">
                <label class="form-check-label small">
                    <input type="radio" name="Gefahrgut" value="JA" required><?=$form['string22']?>
                    <i class="input-helper"></i>
                </label>
            </div>

            <div class="col-3 p-0 text-right">
                <div class="p-0">
                    <input type="search" class="form-control p-1" name="Gefahrgutpunkte" id="gefahrpunkte"
                        placeholder="<?=$form['string39']?>">
                </div>
            </div>
        </div>
        <div class="row p-2 alert-info border rounded mb-1">
            <div class="row p-0 pb-2 mb-2 border-bottom">
                <div class="col-3 p-0">
                    <div id="emailHelp14" class="form-text small"><?=$form['string17']?> *
                    </div>
                </div>
                <div class="col-3 p-0">
                    <div class="form-check form-check-flat">
                        <label class="form-check-label small">
                            <input type="radio" name="Zollgut" value="NEIN" required><?=$form['string23']?>
                            <i class="input-helper"></i>
                        </label>
                    </div>
                </div>
                <div class="col-6 p-0">
                    <div class="form-check form-check-flat">
                        <label class="form-check-label small">
                            <input type="radio" name="Zollgut" value="JA" required><?=$form['string22']?>
                            <i class="input-helper"></i>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row p-0 pb-2 mb-2  border-bottom">
                <div class="col-3 p-0">
                    <div id="emailHelp30" class="form-text small"><?=$form['string44']?> *
                    </div>
                </div>
                <div class="col-3 p-0">
                    <div class="form-check form-check-flat">
                        <label class="form-check-label small">
                            <input type="radio" name="leergut_mitnahme" value="NEIN" required><?=$form['string23']?>
                            <i class="input-helper"></i>
                        </label>
                    </div>
                </div>
                <div class="col-6 p-0">
                    <div class="form-check form-check-flat">
                        <label class="form-check-label small">
                            <input type="radio" name="leergut_mitnahme" value="JA" required><?=$form['string22']?>
                            <i class="input-helper"></i>
                        </label>
                    </div>
                </div>

            </div>
            <div class="row p-0 mt-2">
                <div class="p-0">
                    <div id="emailHelp16" class="form-text small"><?=$form['string25']?>
                    </div>
                    <textarea type="search" class="form-control" id="ladung_beschreibung"
                        name="ladung_beschreibung"><?=$array['ladung_beschreibung']?></textarea>
                </div>
            </div>
        </div>
        <div class="row mt-2 pt-2">
            <div class="col-12 p-0">
                <input type="hidden" name="rfnum" id="rfnum" value="<?=$getRFnum?>">
                <input type="hidden" name="returnURI" id="return-after" value="wert">
                <input type="hidden" name="update_to_order" id="update_to_order" value="maingate">
                <button type="submit" class="btn btn-primary float-end" id="btn-anmelden">bearbeiten</button>
            </div>
        </div>
</div>