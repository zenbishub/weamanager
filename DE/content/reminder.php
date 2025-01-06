<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <p class="card-title float-end">Erinnerungen</p>
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
                                        <h4 class="card-title">Neue Erinnerung / Hinweis</h4>
                                        <form class="form-sample" method="post" enctype="multipart/form-data"
                                            action="class/action.php">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">Ersteller,
                                                            Daimler-ID</label>
                                                        <div class="col-sm-9 p-0">
                                                            <input name="Ersteller" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label
                                                            class="col-sm-3 p-0 col-form-label">Anzeige-Uhrzeit</label>
                                                        <div class="col-sm-9 p-0">
                                                            <input type="time" name="Uhrzeit" class="form-control"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">

                                                    <div class="form-group row">
                                                        <label class="col-sm-3 p-0 col-form-label">Turnus</label>
                                                        <div class="col-sm-9 p-0">
                                                            <select name="Turnus" class="form-control" id="turnus"
                                                                required>
                                                                <option value="">wählen</option>
                                                                <option value="1">täglich</option>
                                                                <option value="2">wöchentlich</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row d-none" id="weekDays">
                                                        <label class="col-sm-3 p-0 col-form-label">Wochentag</label>
                                                        <div class="col-sm-9 p-0">
                                                            <select name="Turnus-Plan" class="form-control" id="Days">
                                                                <option value="">wählen</option>
                                                                <option value="1">Montags</option>
                                                                <option value="2">Dienstags</option>
                                                                <option value="3">Mittwochs</option>
                                                                <option value="4">Donnerstags</option>
                                                                <option value="5">Freitags</option>
                                                            </select>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <label
                                                            class="col-sm-3 p-0 col-form-label">Erinnerung-Text</label>
                                                        <div class="col-sm-9 p-0">
                                                            <textarea name="Erinnerung-Text" class="form-control"
                                                                required
                                                                placeholder="Ihr Text für Erinnerung"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row justify-content-end">
                                                <div class="col-12 text-end">
                                                    <input type="hidden" name="add_reminder" value="new">
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
                                Erinnerungen
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse show overflow-auto"
                            aria-labelledby="panelsStayOpen-headingTwo">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table customized">
                                        <thead>
                                            <tr>
                                                <th>Lf.Nr.</th>
                                                <th>Uhrzeit</th>
                                                <th>Turnus</th>
                                                <th>Ersteller</th>
                                                <th>Erinnerung</th>
                                                <th></th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($ReminderDATA)):?>
                                            <tr>
                                                <td colspan="6" class="align-middle text-center">keine
                                                    Erinnerungen vorhanden</td>
                                            </tr>
                                            <?php endif?>
                                            <?php $lf=1;foreach($ReminderDATA as $id=>$reminder):
                                                $turnus = "";
                                                $turnusPLan = "";
                                                switch($reminder['Turnus']):
                                                        case 1:
                                                            $turnus="Täglich";
                                                            break;
                                                        case 2:
                                                            $turnus ="Wöchentilch";
                                                        break;
                                                endswitch;
                                                switch($reminder['Turnus-Plan']):
                                                        case 1:
                                                            $turnusPLan="Monatgs";
                                                            break;
                                                        case 2:
                                                            $turnusPLan="Dienstags";
                                                        break;
                                                        case 3:
                                                            $turnusPLan="Mittwochs";
                                                        break;
                                                        case 4:
                                                            $turnusPLan="Donnerstags";
                                                        break;
                                                        case 5:
                                                            $turnusPLan="Freitags";
                                                        break;
                                                endswitch;

                                                ?>
                                            <tr>
                                                <td class="align-middle"><?=$lf?></td>
                                                <td class="align-middle"><?=$reminder['Uhrzeit']?></td>
                                                <td class="align-middle"><?=$turnus?>
                                                    <?php if(!empty($turnusPLan)):?>
                                                    <?=" / ".$turnusPLan?>
                                                    <?php endif;?>
                                                </td>
                                                <td class="align-middle"><?=$reminder['Ersteller']?></td>
                                                <td class="align-middle"><?=$reminder['Erinnerung-Text']?></td>

                                                <td>
                                                    <div class="row">

                                                        <div class="col-6 p-0">
                                                            <a href="class/action.php?p=reminder&deletedata=<?=$reminder['Reminder-ID']?>"
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