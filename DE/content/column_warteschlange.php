<?php 
    switch($_SESSION['weamanageruser']){
        case "MONITOR":
            $colStyle="col-md-8";
        break;
        default:
            $colStyle="col-md-6 col-xl-4";
    }
?>
<div class="<?=$colStyle?> p-0 stretch-card maincolumn" alt="warteschlange">
    <div class="card p-0 rounded">
        <div class="card-header h5 card-title-custom p-1 pe-1 ps-1">
            <span class="ti-control-shuffle"></span> Wartend
            <div class="float-end d-none d-lg-block">
                <button class="btn btn-info text-light border p-1 pe-1 ps-1" id="save-prio">Prio</button>
                <button class="btn btn-info text-light border p-1 pe-1 ps-1" id="btn-toParkingOverview"
                    data-bs-toggle="modal" data-bs-target="#toParkingOverview">Übersicht-Tab</button>
                <a href="information" target="_blank"
                    class="btn btn-info text-light border p-1 pe-1 ps-1">Übersicht-Window</a>
            </div>
        </div>
        <div class="card-body p-0 p-md-2 ui-widget ui-helper-clearfix">
            <div id="column-warteschlange"></div>
        </div>
    </div>
</div>