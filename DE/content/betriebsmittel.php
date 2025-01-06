<?php
  require_once 'class/Controller.php';
  $o=new Controller();
  $werkdata = $o->selectPlant();
//print_R($BMIs);
?>
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <p class="card-title float-end">Betriebsmittel</p>
            </div>
            <div class="card-body">
                <div class="accordion" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item border-0 border-bottom p-2">
                        <button type="button" class="btn btn-dark btn-rounded btn-fw" data-bs-toggle="collapse"
                            data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="false"
                            aria-controls="panelsStayOpen-collapseOne">neu</button>
                        <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse"
                            aria-labelledby="panelsStayOpen-headingOne">
                            <div class="accordion-body">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Daten Erfassen</h4>
                                        <form class="form-sample" method="post" enctype="multipart/form-data"
                                            action="class/action.php">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">Werk</label>
                                                        <div class="col-sm-9 p-0">
                                                            <select name="Plant" class="form-control p-2" required>
                                                                <option value="">wählen</option>
                                                                <?php foreach($werkdata as $data):?>
                                                                <option
                                                                    value="<?=$data['Werkname']?>&<?=$data['Werknummer']?>&<?=$data['INUMMER']?>">
                                                                    <?=$data['Werkname']?></option>
                                                                <?php endforeach;?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">Abteilung</label>
                                                        <div class="col-sm-9 p-0">
                                                            <select name="DEP" class="form-control p-2" required>
                                                                <option value="">wählen</option>
                                                                <option value="Wareneingang:601">Wareneingang</option>
                                                                <option value="Versand-SKD:602">Versand / SKD</option>
                                                                <option value="Lackierung:603">Lackierung</option>
                                                                <option value="Versand-Werk 9:401">Versand-Werk 9
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">BMI-Typ</label>
                                                        <div class="col-sm-9 p-0">
                                                            <select name="BMI-Typ" class="form-control p-2" required>
                                                                <option value="">wählen</option>
                                                                <option value="Frontstapler">Frontstapler</option>
                                                                <option value="Schlepper">Schlepper</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">Hersteller</label>
                                                        <div class="col-sm-9 p-0">
                                                            <input type="text" name="Hersteller" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">Gewicht
                                                            (Kilo)</label>
                                                        <div class="col-sm-9 p-0">
                                                            <input type="text" name="Gewicht" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">BMI-Nummer</label>
                                                        <div class="col-sm-9 p-0">
                                                            <input type="text" name="BMI-Nummer" class="form-control"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">INV-Nummer</label>
                                                        <div class="col-sm-9 p-0">
                                                            <input type="text" name="INV-Nummer" class="form-control"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">BMI-Bild</label>
                                                        <div class="col-sm-9 p-0">
                                                            <input type="file" accept=".jpg" name="BMI-Bild"
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">Beschreibung</label>
                                                        <div class="col-sm-9 p-0">
                                                            <textarea class="form-control" name="Beschreibung"
                                                                required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="hidden" name="add_BMIData" value="new">
                                                    <button type="submit"
                                                        class="btn btn-dark btn-rounded btn-fw">eintragen</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false"
                                aria-controls="panelsStayOpen-collapseTwo">
                                Betriebsmittel
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse show"
                            aria-labelledby="panelsStayOpen-headingTwo">
                            <div class="accordion-body">
                                <div class="overflow-auto">
                                    <table class="table customized">
                                        <thead>
                                            <tr>
                                                <th>Lf.Nr.</th>
                                                <th>Werk</th>
                                                <th>W-Nr.</th>
                                                <th>INr.</th>
                                                <th>Abteilung</th>
                                                <th>Typ</th>
                                                <th>Hersteller</th>
                                                <th>Gewicht</th>
                                                <th>INV-Nummer</th>
                                                <th>BMI-Nummer</th>
                                                <th>Beschreibung</th>
                                                <th>Bild</th>
                                                <th>QR-Code</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $lf=1;foreach($BMIs as $id=>$bmi):
                          $expl = explode("&",$bmi['Plant']);
                          $werkname = $expl[0];
                          $werknummer = $expl[1];
                          $inummer = $expl[2];
                          ?>
                                            <tr>
                                                <td class="align-middle p-1"><?=$lf?></td>
                                                <td class="align-middle p-1"><?=$werkname?></td>
                                                <td class="align-middle p-1"><?=$werknummer?></td>
                                                <td class="align-middle p-1"><?=$inummer?></td>
                                                <td class="align-middle p-1"><?=$bmi['DEP']?></td>
                                                <td class="align-middle p-1"><?=$bmi['BMI-Typ']?></td>
                                                <td class="align-middle p-1"><?=$bmi['Hersteller']?></td>
                                                <td class="align-middle p-1"><?=$bmi['Gewicht']?></td>
                                                <td class="align-middle p-1"><?=$bmi['INV-Nummer']?></td>
                                                <td class="align-middle p-1"><?=$bmi['BMI-Nummer']?></td>
                                                <td class="align-middle p-1"><?=$bmi['Beschreibung']?></td>

                                                <td class="align-middle p-1">
                                                    <?php if(!empty($bmi['BMI-Bild'])):?>
                                                    <img class="img-thumbnail pointer pictureviwever-show"
                                                        title="<?=$bmi['BMI-Typ']?> <?=$bmi['Hersteller']?> <?=$bmi['BMI-Nummer']?>"
                                                        alt="db/<?=$_SESSION['werknummer']."/bmi/".$bmi['BMI-Bild']?>"
                                                        data="<?=$array['Stapler']['BMI-Nummer'].'&'.$_SESSION['weamanageruser']?>"
                                                        data-bs-toggle="modal" data-bs-target="#pictureviwever"
                                                        src="db/<?=$_SESSION['werknummer']."/bmi/TN".$bmi['BMI-Bild']?>">
                                                    <?php endif;?>
                                                </td>

                                                <td><button class="btn btn-light border p-2 create-access-qrcode"
                                                        title="Res-Nr: <?=$bmi['BMI-Nummer']?>" data-bs-toggle="modal"
                                                        data-bs-target="#createQRcode"
                                                        alt="<?=$_SESSION['werknummer']?>:<?=$inummer?>;<?=$bmi['BMI-Nummer']?>;startstapler">QRC</button>
                                                </td>
                                                <td class="align-middle p-1" colspan="3">
                                                    <div class="row p-0 m-0">
                                                        <div class="col-6 p-0 text-right">
                                                            <a href="bmi:<?=$bmi['BMI-Nummer']?>:<?=$id?>"
                                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                                class="btn btn-light border p-2 open-edit-modal"><i
                                                                    class="ti-pencil"></i></a>
                                                        </div>
                                                        <div class="col-6 p-0">
                                                            <a href="class/action.php?p=bmi&deletedata=<?=$bmi['BMI-Nummer']?>"
                                                                class="btn btn-light border p-2 confirm-action pointer"><i
                                                                    class="ti-trash"></i></a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $lf++; endforeach;?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>