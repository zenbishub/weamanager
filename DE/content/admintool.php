<?php
  require_once 'class/Controller.php';
  $o=new Controller();
  $online = $o->getAppOnOff();
//print_R($BMIs);
?>
<div class="row">
    <div class="col-12 grid-margin stretch-card pb-5">
        <div class="card pb-4">
            <div class="card-header">
                <p class="card-title float-end">App Einstellungen</p>
            </div>
            <div class="card-body">
                <div class="card">
                    <div class="card-header">
                        <div class="card-body pb-4">
                            <?=$online?>
                            <form action="class/action" method="post">
                                <div class="row">
                                    <div class="col-6 p-0">
                                        <div class="form-group col-6 ps-0">
                                            <select class="form-select" name="app" aria-label="Default select example">
                                                <option selected>App online/offline</option>
                                                <option value="online">online</option>
                                                <option value="offline">offline</option>
                                            </select>

                                        </div>
                                        <div class="col-6">
                                            <input type="hidden" name="on_off" value="1">
                                            <button type="submit" class="btn btn-primary p-2">ändern</button>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>