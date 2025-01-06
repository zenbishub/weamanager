<?php
extract($_REQUEST);
  require_once '../class/Controller.php';
  $controller = new Controller();
  $bmis = $controller->getBMIData("../");
  $online = $controller->getOnlineData("../");
?>
<form method="post" action="class/action.php">
    <span class="h4">Leergut Mitnahme</span>
    <div class="row mt-4">
        <div class="col-12">
            <div class="row">
                <div class="col-3 col-md-2 ps-0">
                    <div class="form group">
                        <label for="" class="small p-0">LT-Liste</label>
                        <select name="Leergut-LT" class="form-control form-control-sm text-black" id="" required>
                            <option value="">Ladungdsträger wählen</option>
                            <option value="EP-5009">Europalette 5009</option>
                            <option value="GB-2941">Gitterbox / 2941</option>
                            <option value="GB-5770">Plastik / 5770</option>
                            <option value="Diverses">Diverse Ladungsträger</option>
                        </select>
                    </div>
                </div>
                <div class="col-3 col-md-2 ps-0">
                    <div class="form-group">
                        <label for="" class="small m-0">Menge</label>
                        <input type="search" class="form-control" name="Leergut-Mng" id="" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="overflow-auto">
                <table class="table table-hover customized">
                    <tr>
                        <th></th>
                        <th>Typ</th>
                        <th>Herst.</th>
                        <th>Gew.</th>
                        <th>INV</th>
                        <th>BMI</th>
                        <th>Beschreibung</th>
                        <th>Bild</th>
                    </tr>
                    <tbody>
                        <?php 
                        foreach($bmis as $bmi):
                            $styleRow =""; 
                        if(in_array($bmi['BMI-Nummer'], $online)){
                            $styleRow = "style='background:lightgreen' title='Stapler angemeldet'";
                        }
                        ?>
                        <tr <?=$styleRow?>>
                            <td>
                                <div class="form-check form-check-flat">
                                    <label class="form-check-label">
                                        <input class="radio" type="radio" name="set_stapler_leergut_unload"
                                            value="<?=$bmi['BMI-Nummer']?>" required>
                                        <i class="input-helper"></i>
                                    </label>
                                </div>
                            </td>
                            <td class="align-middle p-1"><?=$bmi['BMI-Typ']?></td>
                            <td class="align-middle p-1"><?=$bmi['Hersteller']?></td>
                            <td class="align-middle p-1"><?=$bmi['Gewicht']?></td>
                            <td class="align-middle p-1"><?=$bmi['INV-Nummer']?></td>
                            <td class="align-middle p-1"><?=$bmi['BMI-Nummer']?></td>
                            <td class="align-middle p-1"><?=$bmi['Beschreibung']?></td>
                            <td class="align-middle p-1">
                                <?php if(!empty($bmi['BMI-Bild'])):?>
                                <img class="img-thumbnail pointer picturehover-show"
                                    title="<?=$bmi['BMI-Typ']?> <?=$bmi['Hersteller']?> <?=$bmi['BMI-Nummer']?>"
                                    alt="db/<?=$_SESSION['werknummer']."/bmi/".$bmi['BMI-Bild']?>"
                                    data-bs-toggle="modal" data-bs-target="#pictureviwever"
                                    src="db/<?=$_SESSION['werknummer']."/bmi/TN".$bmi['BMI-Bild']?>">
                            </td>
                            <?php endif;?>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer border-0">
            <input type="hidden" name="set_to_leergut_unload" value="1">
            <input type="hidden" name="rfnum" id="rfnum" value="<?=$rfnum?>">
            <button type="submit" class="btn btn-dark" id="submit-button">Stapler für Leergut zuweisen</button>
        </div>
    </div>
    </div>
</form>