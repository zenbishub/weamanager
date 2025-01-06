<?php 
  extract($_REQUEST);  
?>
<form action="class/publicExtern">
    <div class="row justify-content-center">
        <div class="col-8 p-4">
                <div class="form-group">
                    <label for="target">Aufleger-Kennzeichen</label>
                    <input type="search" name="LKW_nummer" class="form-control input-lg font-large" id="target" placeholder="Kennzeichen" required>
                </div>
                <div class="form-group">
                    <input type="hidden" name="set_LKW_nummer" value="<?=$target?>">
                    <input type="hidden" name="target_werk" value="<?=$werk?>">
                    <button class="btn btn-primary">eintragen</button>
                </div>
        </div>
    </div>
</form>