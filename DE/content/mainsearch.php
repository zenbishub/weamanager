<?php 
$archivdata = $o->getFromArchiv();
// echo "<pre>";
// print_R($archivdata);

?>
<div class="card stretch-card">
    <div class="card-body" id="mainsearch">
        <div class="table-responsive pt-2">
            <table class="table table-striped table-bordered rounded dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="small bg-dark text-light">
                        <th class="">#</th>
                        <th class="pt-3 pb-3 font-weight-light">Anmeldung</th>
                        <th class="pt-3 pb-3 font-weight-light">Firma</th>
                        <th class="pt-3 pb-3 font-weight-light">Kennzeichen</th>
                        <th class="pt-3 pb-3 font-weight-light">Ladung</th>
                        <th class="pt-3 pb-3 font-weight-light">Dokumente</th>
                        <th class="pt-3 pb-3 font-weight-light">Abladestelle</th>
                        <th class="pt-3 pb-3 font-weight-light">Einfahrt</th>
                        <th class="pt-3 pb-3 font-weight-light">Abfertigung</th>
                        <th class="pt-3 pb-3 font-weight-light">Signiert</th>
                        <th class="pt-3 pb-3 font-weight-light">Verlassen</th>
                        <th class="pt-3 pb-3 font-weight-light">Pforte</th>

                        <th class="pt-3 pb-3 font-weight-light">Protokoll</th>
                    </tr>
                </thead>
                <tbody class="bg-light">
                    <?php $lf=1; foreach($archivdata as $id=>$data):
                    $link =null;
                    $Pforte= null;
                    $protokoll = json_decode($data['Protokoll_WA'],true);
                    if(!empty($data['Lieferdokument'])){
                        $link = "<a href='ScannFolder/".$data['Lieferdokument']."' target='_blank'>Lieferdokument</a>";
                    }
                    if(empty($data['Einfahrt'])){
                        $data['Einfahrt'] = $data['Abfertigung'];
                    }
                    if(!empty($data['Pforte'])){
                        $Pforte =  "<span title='".$data['Pforte']."'>QT</span>";
                    }
                    ?>
                    <tr class="small text-dark">
                        <td class="p-2 align-middle"><?=$lf++?></td>
                        <td class="p-2 align-middle"><?=$data['Anmeldung']?></td>
                        <td class="p-2 align-middle"><?=$data['Firma']?></td>
                        <td class="p-2 align-middle"><?=$data['Nummer']?></td>
                        <td class="p-2 align-middle"><?=$data['Ladung']?></td>
                        <td class="p-2 align-middle"><?=$link?></td>
                        <td class="p-2 align-middle"><?=$data['Platz']?></td>
                        <td class="p-2 align-middle"><?=$data['Einfahrt']?></td>
                        <td class="p-2 align-middle"><?=$data['Abfertigung']?></td>
                        <td class="p-2 align-middle"><?=$protokoll['signed']?></td>
                        <td class="p-2 align-middle"><?=date("d.m.y, H:i",$data['gone'])?></td>
                        <td class="p-2 text-center"><?=$Pforte?></td>
                        <td class="p-2 text-center align-middle">
                            <?php if(!empty($data['Protokoll_WA'])):?>
                            <a href="class/html2pdf.php?create=<?=$id?>" target="_blank"
                                class="btn btn-light border p-1 pointer text-danger"><i class="far fa-file-pdf"></i></a>
                            <?php endif;?>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>