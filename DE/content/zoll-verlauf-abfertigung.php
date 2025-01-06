<?php
spl_autoload_register(function ($class_name) {
    include '../class/'.$class_name . '.php';
});
$o = new Controller(); 
$verlauf = new Verlauf();
$getOrderListForZoll = $o->getOrderListForZoll("../");?>
<div id="column-verlauf-tabelle">
    <?php $verlauf->verlaufTabelle($getOrderListForZoll)?>
</div>