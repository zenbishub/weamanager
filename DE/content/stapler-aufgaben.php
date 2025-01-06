<?php 
spl_autoload_register(function ($class_name) {
    include '../class/'. $class_name . '.php';
});
$controller = new Controller();
$arrays = $controller->staplerAufgaben();
// echo "<pre>";
//             print_R($arrays);

            // [rfnum] => 2
            //         [Firma] => Zenbis Transport
            //         [Platz] => Wartespur-1
            //         [Aufgabe] => im Prozess
?>
<table class="table">
    <tr>
        <th>#</th>
        <th>Stapler</th>
        <th>Platz</th>
        <th>Watrenummer</th>
        <th>Auftrag</th>
        <th>Aufgabe</th>
    </tr>
    <?php if(empty($arrays)):?>
    <tr>
        <td colspan="6" class="alert-info text-center p-2">Keine manuelle Aufträge</td>
    </tr>
    <?php endif?>

    <?php $lf=1; foreach($arrays as $bmi_nummer=>$BMIarray):
        foreach($BMIarray as $array):?>
    <tr>
        <td><?=$lf++?></td>
        <td><?=$bmi_nummer?></td>
        <td><?=$array['Platz']?></td>
        <td><?=$array['rfnum']?></td>
        <td><?=$array['Firma']?></td>
        <td><?=$array['Aufgabe']?></td>
    </tr>
    <?php endforeach; endforeach;?>

</table>