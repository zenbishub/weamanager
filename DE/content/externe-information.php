<?php
    session_start();
    require_once '../class/Externe.php';
    $extern = new Externe($_SESSION['werknummer'], "../");
    $ecol = $extern->getReferenceTable();
    
?>
<ul class="nav nav-pills mb-2 p-2 bg-white" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link " id="pills-lieferant1-tab" data-bs-toggle="pill" data-bs-target="#lieferant1"
            type="button" role="tab" aria-controls="pills-lieferant1" aria-selected="true">Ekol, Türkei</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-lieferant2-tab" data-bs-toggle="pill" data-bs-target="#lieferant2"
            type="button" role="tab" aria-controls="pills-lieferant2" aria-selected="false">Spediton 2</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-lieferant3-tab" data-bs-toggle="pill" data-bs-target="#lieferant3"
            type="button" role="tab" aria-controls="pills-lieferant3" aria-selected="false">Spediton 3</button>
    </li>
</ul>
<div class="tab-content bg-white" id="pills-tabContent">
    <div class="tab-pane show active bg-white" id="lieferant1" role="tabpanel" aria-labelledby="pills-lieferant1-tab">
        <div class="table-responsive-overflow">
            <?=$ecol?>
        </div>
    </div>
    <div class="tab-pane fade bg-white" id="lieferant2" role="tabpanel" aria-labelledby="pills-lieferant2-tab">

    </div>
    <div class="tab-pane fade bg-white" id="lieferant3" role="tabpanel" aria-labelledby="pills-lieferant3-tab">...</div>
</div>