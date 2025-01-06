<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <p class="card-title float-end">Personal</p>
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
                                        <h4 class="card-title">Unterschrift berechtigter Personal</h4>
                                        <form class="form-sample" method="post" enctype="multipart/form-data"
                                            action="class/action.php">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">Daimler-ID</label>
                                                        <div class="col-sm-9 p-0">
                                                            <input name="Daimler-ID" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">Daimler-PN</label>
                                                        <div class="col-sm-9 p-0">
                                                            <input type="text" name="Personalnummer"
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">Name</label>
                                                        <div class="col-sm-9 p-0">
                                                            <input type="text" name="Name" class="form-control"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">Vorname</label>
                                                        <div class="col-sm-9 p-0">
                                                            <input class="form-control" name="Vorname" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-end">
                                                <div class="col-12 text-end">
                                                    <input type="hidden" name="add_personData" value="new">
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
                                Personal
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse show"
                            aria-labelledby="panelsStayOpen-headingTwo">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table customized">
                                        <thead>
                                            <tr>
                                                <th>Lf.Nr.</th>
                                                <th>Name, Vorname</th>
                                                <th>Daimler-ID</th>
                                                <th>Daimler-PN</th>
                                                <th></th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $lf=1;foreach($PRDATA as $id=>$PR):?>
                                            <tr>
                                                <td class="align-middle p-1"><?=$lf?></td>
                                                <td class="align-middle p-1"><?=$PR['Name']?>, <?=$PR['Vorname']?></td>
                                                <td class="align-middle p-1"><?=$PR['Daimler-ID']?></td>
                                                <td class="align-middle p-1"><?=$PR['Personalnummer']?></td>
                                                <td class="align-middle p-1">
                                                    <div class="row">
                                                        <div class="col-6 p-0">
                                                            <a href="personal:<?=$PR['Personalnummer']?>:<?=$id?>"
                                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                                class="btn btn-light border p-2 open-edit-modal"><i
                                                                    class="ti-pencil"></i></a>
                                                        </div>
                                                        <div class="col-6 p-0">
                                                            <a href="class/action.php?p=personal&deletedata=<?=$PR['Personalnummer']?>"
                                                                class="btn btn-light border p-2 pointer confirm-action"><i
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