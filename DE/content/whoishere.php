<?php
spl_autoload_register(function ($class_name) {
    include '../class/'. $class_name . '.php';
});
    $controller = new Controller();
    $arrays = $controller->whoIsOnline();
    
?>
<div class="card">
    <div class="card-body">
        <table class="table">
            <?php $lf=1; foreach($arrays as $array):?>
            <tr>
                <td><?=$lf++?></td>
                <td><?=$array['user']?></td>
                <td><?=date("H:i:s",$array['time'])?></td>
            </tr>
            <?php endforeach;?>
        </table>
    </div>
</div>