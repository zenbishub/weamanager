<?php 
    extract($_REQUEST);
?>
<div class="row pt-4">
    <div class="col-12 mb-4">
        <span class="h4"></span>
    </div>
    <?php if($zollaction==2):?>
    <div class="col-11">
        Ware für andere Werke dabei
    </div>
    <div class="col-1 p-0">
        <div class="form-check form-check-flat">
            <label class="form-check-label small">
                <input type="radio" name="Zollbearbeitung" value="andere Werke dabei" required>
                <i class="input-helper"></i>
            </label>
        </div>
    </div>
    <input type="hidden" name="zollannahme" value="clearing">
    <?php endif;?>

    <?php
        $array=[
            "Zolldokumente sind nicht IO",
            "Sonstiger Ablehnungsgrund"
        ];
    if($zollaction==3):?>
    <div class="col-12 mb-4">
        <span class="h4">Zollbearbeitung angelehnt aus dem folgenden Grund</span>
    </div>

    <?php foreach($array as $item):?>
    <div class="col-10 mt-4">
        <?=$item?>
    </div>
    <div class="col-1 pe-3 mt-3">
        <div class="form-check form-check-flat">
            <label class="form-check-label small">
                <input type="radio" name="Zollbearbeitung" value="abgelehnt" required>
                <i class="input-helper"></i>
            </label>
        </div>
    </div>
    <?php endforeach;?>
    <input type="hidden" name="zollannahme" value="abgelehnt">
    <?php endif;?>
    <div class="col-12 pt-4">
        <textarea name="Zollmeldung" class="form-control" cols="30" rows="10" placeholder="Text / Zollbearbeitung"
            required></textarea>
    </div>

</div>