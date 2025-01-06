<?php
$rfnum=null;
extract($_REQUEST);
require_once '../class/Controller.php';
$controler = new Controller();
$BMIs = $controler->getBMIData("../");
?>
<div class="row">
    <div class="col-12 form-group">
        <label for="selstapler" class="small p-0">Ziel-Stapler auswählen</label>
        <select name="stapler_for_auftrag" id="selstapler" class="form-control" required>
            <option value="">wählen</option>
            <?php foreach($BMIs as $bmi):?>
            <option value="<?=$bmi['BMI-Nummer']?>"><?=$bmi['BMI-Typ']?> / <?=$bmi['BMI-Nummer']?></option>
            <?php endforeach;?>
        </select>
    </div>
    <div class="col-12 form-group">
        <div class="form-check form-check-primary mb-2">
            <label class="form-check-label">
                <input type="radio" class="form-check-input" value="Normal" name="todo" checked="">
                Normal
                <i class="input-helper"></i></label>
        </div>

        <div class="form-check form-check-danger">
            <label class="form-check-label">
                <input type="radio" class="form-check-input" value="Dringend" name="todo">
                Dringend
                <i class="input-helper"></i></label>
        </div>
    </div>

    <div class="col-12">
        <textarea name="umfang" cols="30" rows="10" class="form-control" placeholder="Aufgabe beschreibung"
            required></textarea>
    </div>
    <input type="hidden" name="add_manueller_auftrag" value="1">
    <input type="hidden" name="timestamp" value="<?=time()?>">
    <input type="hidden" name="rfnum" value="<?=$rfnum?>">
</div>