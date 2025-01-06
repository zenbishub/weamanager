<?php 
    extract($_REQUEST);
?>
<div class="row pt-4">
    <div class="col-12 mb-4">
        <span class="h4">Sendungsinfo</span>
    </div>
    <div class="col-12 pe-3 mb-3">
        <label for="" class="m-0 p-0 small">Sendungen</label>
        <input type="search" class="form-control col-6" name="sendungnummern" placeholder="Sendung" required>
    </div>
    <div class="col-12">
        <label for="" class="m-0 p-0 small">Collis</label>
        <textarea name="collis" class="form-control" rows="5" placeholder="Anzahl Collis / Information"
            required></textarea>
    </div>
</div>