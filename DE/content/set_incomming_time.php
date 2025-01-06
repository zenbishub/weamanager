<?php
require_once '../class/Controller.php';
$o = new Controller();
$readDB=$o->getUnloadPlaceData("../");
extract($_REQUEST);

?>

<form action="class/action">
    <div class="row mb-5">
        <div class="col-12">
            <h3><?=$Nummer?></h3>
            <h5>Einfahrtszeit hinzufügen</h5>
        </div>
        <div class="col-8">
            <input type="time" class="form-control" name="incomming_time" id="incomming_time" required>
            <input type="hidden" name="incomming_time_data" value="<?=$rfnum?>:<?=$Nummer?>">
            <input type="hidden" name="returnURI" value="../">
        </div>
        <div class="col-4 ps-0"><button type="submit" class="btn p-2 text-light btn-info">hinzufügen</button></div>
        <div class="col-8 mt-2">
            <select class="form-control" name="incomming_platz" required>
                <option value="">Abladestelle wählen</option>
                <?php foreach($readDB as $data):?>
                <option value="<?=$data['Platz']?>"><?=$data['Platz']?></option>
                <?php endforeach;?>
            </select>

        </div>
    </div>
</form>