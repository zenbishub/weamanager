                        <div class="col-12 col-md-9 col-xl-6 <?=$hideStartMask?> border-left border-right bg-light p-0 p-lg-3 pb-5 pt-3 rounded"
                            id="register-form-to-waitlist">
                            <div class="row">
                                <div class="col-6 col-lg-8">
                                    <h4><?=$form['string2']?></h4> <span id="gadget"></span>
                                </div>
                                <div class="col-6 col-lg-4 text-right">
                                    <button class="btn btn-dark ps-2 fs-6 ps-lg-3 pe-lg-3 pe-2"
                                        id="btn-quickmodus"><?=$form['string3']?></button>
                                </div>
                            </div>
                            <hr>
                            <div class="row d-none justify-content-center" id="start-lieferung-schnellerfassen">
                                <div class="col-12 mb-2 text-center">
                                    <button class="btn btn-dark startbutton hide-by-terminal" alt="wartenummer"
                                        id="start-by-wartenummer">
                                        <?=$form['string4']?>
                                    </button>

                                </div>
                                <div class="col-12  mb-2 text-center">
                                    <button class="btn btn-dark startbutton" alt="voranmeldung"
                                        id="start-by-voranmeldung">
                                        <?=$form['string51']?>
                                    </button>
                                </div>

                                <div class="col-12  mb-2 text-center">
                                    <button class="btn btn-dark startbutton" alt="kennzeichen"
                                        id="start-by-kennzeichen">
                                        <?=$form['string5']?>
                                    </button>
                                </div>
                                <div class="col-12  mb-2 text-center">
                                    <button class="btn btn-dark startbutton" alt="sonderfahrt"
                                        id="start-by-sonderfahrt">
                                        <?=$form['string41']?>
                                    </button>
                                </div>
                                <!-- <div class="col-12  mb-2 text-center">
                                    <button class="btn btn-dark startbutton" alt="lieferung" id="start-by-lieferung">
                                        <?=$form['string6']?>
                                    </button>
                                </div> -->

                                <div class="col-12  mb-2 text-center">
                                    <button class="btn btn-dark startbutton hide-by-terminal" data-bs-toggle="modal"
                                        data-bs-target="#readQRModal" id="start-by-qrcode">
                                        QR-Code
                                    </button>
                                </div>
                                <!-- Sonderprozesse -->
                                <div class="col-12  mb-2 text-center">
                                    <button type="button" class="btn btn-warning text-white startbutton"
                                        id="btn-karosserie-werkschutz" alt="karosserie" data="specialprocess1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#simpleAlertModal"><?=$form['string49']?></button>
                                </div>
                                <!-- <div class="col-12  mb-2 text-center">
                                    <button type="button"
                                        class="btn btn-warning text-white startbutton  hide-by-terminal"
                                        id="btn-trailertausch-werkschutz" alt="trailertausch" data="specialprocess2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#simpleAlertModal"><?=$form['string50']?></button>
                                </div> -->
                                <div class="col-12  mb-2 text-center">
                                    <button class="btn btn-warning text-white startbutton" id="start-for-external"
                                        alt="external" data="specialprocess3" data-bs-toggle="modal"
                                        data-bs-target="#simpleAlertModal">
                                        Fremdfirmen
                                    </button>
                                </div>
                                <!-- <div class="col-12  mb-2 text-center">
                                    <button class="btn btn-warning text-white startbutton" id="start-for-schrottpickup"
                                        alt="schrottpicker" data="specialprocess4" data-bs-toggle="modal"
                                        data-bs-target="#simpleAlertModal">
                                        Entsorgungsfirmen
                                    </button>
                                </div> -->
                                <div class="col-9 border rounded p-3" id="scanner-info">
                                    <?=$o->scannerNummer()?>
                                </div>
                            </div>
                            <div class="row mb-2 justify-content-center">
                                <div id="errorbox" class="alert-danger rounded text-center p-2 d-none col-10 border">
                                </div>
                            </div>
                            <div class="row d-none justify-content-center" id="lieferung-schnellerfassen">
                                <div class="col-10 col-md-6 pt-3 p-md-0 mt-4 mb-2 d-none"
                                    id="input-kennzeichen-voranmeldung">
                                    <div id="emailHelp1" class="form-text small mb-2"><?=$form['string52']?>
                                    </div>
                                    <input type="search" class="form-control form-control-lg p-2 font-larger"
                                        name="voranmeldung" id="voranmeldung">
                                </div>
                                <div class="col-8 col-md-6 pt-3 p-md-0 mt-4 mb-2 d-none" id="input-kennzeichen">
                                    <div id="emailHelp1" class="form-text small mb-2"><?=$form['string5']?></div>
                                    <input type="search" class="form-control form-control-lg p-2 font-larger"
                                        name="schnellanmeldung" id="schnellanmeldung">
                                </div>
                                <div class="col-md-6 pt-3 p-md-0 mt-4 mb-2 d-none" id="input-lieferung">
                                    <div class="row justify-content-center">
                                        <div class="col-8 form-group">
                                            <div class="form-text small mb-2"><?=$form['string6']?></div>
                                            <input type="search" class="form-control form-control-lg p-2 font-larger"
                                                name="Lieferung_ID" id="Lieferung_ID">
                                        </div>
                                        <div class="col-12 text-center form-group">
                                            <button class="btn btn-dark btn-lg" id="check-anmelde-id"
                                                alt="<?=$_SESSION['werknummer']?>">ok</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 pt-3 p-md-0 mt-4 mb-2 d-none" id="input-sonderfahrt">
                                    <div class="row justify-content-center">
                                        <div class="col-6 p-0 form-group">
                                            <div class="form-text small mb-2"><?=$form['string41']?></div>
                                            <input type="search" autocomplete="off"
                                                class="form-control form-control-lg p-2 font-larger" name="Sonderfahrt"
                                                id="Sonderfahrt">
                                        </div>
                                        <div class="col-12 form-group text-center">
                                            <button class="btn btn-dark" id="check-anmelde-id-sonderfahrt"
                                                alt="<?=$_SESSION['werknummer']?>">ok</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 pt-3 p-md-0 mt-4 mb-2 d-none" id="input-wartenummer">
                                    <form action="class/action.php" method="post" id="ongate-form">
                                        <div class="row justify-content-center">
                                            <div class="col-4 p-0 form-group">
                                                <div class="form-text small mb-2"><?=$form['string4']?></div>
                                                <input type="search" autocomplete="off"
                                                    class="form-control form-control-lg p-2 font-larger"
                                                    name="Wartenummer" id="Wartenummer" readonly>
                                            </div>
                                            <div class="col-12 form-group text-center">
                                                <button class="btn btn-dark" id="check-anmelde-id-wartenummer"
                                                    alt="<?=$_SESSION['werknummer']?>">ok</button>
                                            </div>
                                            <?php include 'content/z-tastatur.php'?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row justify-content-center" id="lieferung-erfassen">
                                <form method="POST" action="class/action.php" id="formular-lieferung-erfassen"
                                    enctype="multipart/form-data">
                                    <?php if(isset($error)):?>
                                    <div class="row mb-2">
                                        <div class="col-12 p-0">
                                            <div id="box-error-message" class="alert-danger p-2 rounded">
                                                <?=$form['string35']?></div>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                    <?php if(isset($checkcode) && $checkcode=="notfound"):?>
                                    <div class="row mb-2">
                                        <div class="col-12 p-0">
                                            <div id="box-error-message" class="alert-danger p-2 rounded">
                                                Fehler: <?=$requestNummer?> nicht im System</div>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                    <div class="row">
                                        <div id="alertbox"
                                            class="alert-info rounded text-center p-2 d-none col-12  mb-3 border rounded">
                                            <span class="h5">Sonderfahrt</span> <span id="sonderfahrt-id"
                                                class="h4"></span>
                                            <p class="small">Bitte füllen Sie die Pflichtfelder aus und drücken Sie
                                                Button "erfassen" unten</p>
                                        </div>

                                        <div id="alertbox-preregesier"
                                            class="alert-info rounded text-center p-2 d-none col-12  mb-3 border rounded">
                                            <span class="h5">Voranmeldung</span> <span id="preregister-id"
                                                class="h4"></span>
                                            <p class="small">Bitte füllen Sie die Pflichtfelder aus und drücken Sie
                                                Button "erfassen" unten</p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6 ps-0 pe-2 pe-md-2">
                                            <div id="emailHelp3" class="form-text small"><?=$form['string7']?> *</div>
                                            <select class="form-control text-black" name="anlieferwerk"
                                                id="anlieferwerk" required>
                                                <!-- <option value=""><?=$form['string36']?></option> -->
                                                <?php
                                                unset($plants[1]);
                                                foreach($plants as $plant):?>
                                                <option value="<?=$plant['Werknummer']?>:<?=$plant['Werkname']?>">
                                                    <?=$plant['Werkname']?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="col-6 p-0">
                                            <div id="emailHelp12" class="form-text small"><?=$form['string15']?> *</div>
                                            <select class="form-control text-black" name="ladung" id="ladung" required>
                                                <option value=""><?=$form['string36']?></option>
                                                <?php $i=0;
                                               //unset($ladungDaten[5]);
                                                //unset($ladungDaten[7]);
                                                foreach($ladungDaten as $daten):?>
                                                <option value="<?=$daten['KN']?>"><?=$dropOne['string'.$i]?></option>
                                                <?php $i++;
                                                  endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3 d-none" id="row-leegut-abholnummer">
                                        <div class="col-12 p-0">
                                            <div id="emailHelp13" class="form-text small"><?=$form['string43']?> *</div>
                                            <input type="search" class="form-control" name="leegut_abholnummer"
                                                id="leegut_abholnummer">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="mb-1 col-md-6 ps-0 pe-0 pe-md-2">
                                            <div id="emailHelp5" class="form-text small"><?=$form['string11']?> *</div>
                                            <input type="search" class="form-control" name="firma"
                                                id="firma_autocomplete" placeholder="" required>
                                        </div>
                                        <div class="mb-1 col-md-6 p-0">
                                            <div id="emailHelp6" class="form-text small"><?=$form['string12']?> *</div>
                                            <input type="search" class="form-control" name="name_fahrer"
                                                id="name_fahrer_autocomplete" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6 ps-0 pe-2 pe-md-2">
                                            <div class="mb-1">
                                                <div id="emailHelp10" class="form-text small"><?=$form['string9']?> *
                                                </div>
                                                <select class="form-control text-black" name="FRZTyp" id="FRZTyp"
                                                    required>
                                                    <option value=""><?=$form['string36']?></option>
                                                    <?php $i=1; foreach($frzTypDaten as $typ):?>
                                                    <option value="<?=$typ['value']?>"><?=$dropTwo['string'.$i]?>
                                                    </option>
                                                    <?php $i++; endforeach;?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6 p-0">
                                            <div id="emailHelp9" class="form-text small"><?=$form['string10']?> *</div>
                                            <input type="search" class="form-control" name="knznummer"
                                                id="knznummer_autocomplete" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6 p-0 pe-2 pe-0 pe-lg-2">
                                            <div id="emailHelp11" class="form-text small"><?=$form['string13']?></div>
                                            <input type="search" class="form-control" name="knznummer_aufleger"
                                                id="knznummer_aufleger" placeholder="">
                                        </div>
                                        <div class="col-6 p-0 text-center hide-by-terminal">
                                            <div id="emailHelp11" class="form-text small"><?=$form['string14']?></div>
                                            <div class="row justify-content-center">
                                                <div class="col-11 p-0" id="image-loaded">
                                                    <button type="button"
                                                        class="btn-lg btn-rounded btn-light border shadow col-12 p-2"
                                                        id="take-image-from-document" data-bs-toggle="modal"
                                                        data-bs-target="#diverseModal" data-index="lieferschein"><span
                                                            class="font-large"><i class="ti-camera"></i></span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row p-2 mb-1 alert-danger border rounded">
                                        <div class="col-3 p-0">
                                            <div class="form-text small"><?=$form['string16']?> *</div>
                                        </div>
                                        <div class="col-3 p-0 form-check form-check-flat">
                                            <label class="form-check-label small">
                                                <input type="radio" name="Gefahrgut" value="NEIN"
                                                    required><?=$form['string23']?>
                                                <i class="input-helper"></i>
                                            </label>
                                        </div>
                                        <div class="col-3 p-0 form-check form-check-flat">
                                            <label class="form-check-label small">
                                                <input type="radio" name="Gefahrgut" value="JA"
                                                    required><?=$form['string22']?>
                                                <i class="input-helper"></i>
                                            </label>
                                        </div>

                                        <div class="col-3 p-0 text-right">
                                            <div class="p-0">
                                                <input type="search" class="form-control p-1" name="Gefahrgutpunkte"
                                                    id="gefahrpunkte" placeholder="<?=$form['string39']?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-2 alert-info border rounded mb-1">
                                        <div class="row p-0 pb-2 mb-2 border-bottom">
                                            <div class="col-3 p-0">
                                                <div id="emailHelp14" class="form-text small"><?=$form['string17']?> *
                                                </div>
                                            </div>
                                            <div class="col-3 p-0">
                                                <div class="form-check form-check-flat">
                                                    <label class="form-check-label small">
                                                        <input type="radio" name="Zollgut" value="NEIN"
                                                            required><?=$form['string23']?>
                                                        <i class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6 p-0">
                                                <div class="form-check form-check-flat">
                                                    <label class="form-check-label small">
                                                        <input type="radio" name="Zollgut" value="JA"
                                                            required><?=$form['string22']?>
                                                        <i class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row p-0 pb-2 mb-2  border-bottom">
                                            <div class="col-3 p-0">
                                                <div id="emailHelp30" class="form-text small"><?=$form['string44']?> *
                                                </div>
                                            </div>
                                            <div class="col-3 p-0">
                                                <div class="form-check form-check-flat">
                                                    <label class="form-check-label small">
                                                        <input type="radio" name="leergut_mitnahme" value="NEIN"
                                                            required><?=$form['string23']?>
                                                        <i class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6 p-0">
                                                <div class="form-check form-check-flat">
                                                    <label class="form-check-label small">
                                                        <input type="radio" name="leergut_mitnahme" value="JA"
                                                            required><?=$form['string22']?>
                                                        <i class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- <div class="row p-0 pb-2 border-bottom">
                                            <div class="col-3 p-0">
                                                <div id="emailHelp14" class="form-text small"><?=$form['string19']?>
                                                </div>
                                            </div>
                                            <div class="col-3 p-0">
                                                <div class="form-check form-check-flat">
                                                    <label class="form-check-label small">
                                                        <input type="radio" name="beladen_for"
                                                            value="Inland"><?=$form['string21']?>
                                                        <i class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6 p-0">
                                                <div class="form-check form-check-flat">
                                                    <label class="form-check-label small">
                                                        <input type="radio" name="beladen_for"
                                                            value="Ausland"><?=$form['string40']?>
                                                        <i class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="row p-0 mt-2">
                                            <div class="p-0">
                                                <div id="emailHelp16" class="form-text small"><?=$form['string25']?>
                                                </div>
                                                <textarea class="form-control" id="ladung_beschreibung"
                                                    name="ladung_beschreibung"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 p-0 mt-2 mb-3">
                                            <div class="row">
                                                <div class="form-check col-1 p-0">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="dgsvo-contitions"
                                                            class="form-check-input" id="dgsvo-contitions" checked
                                                            required>
                                                        <i class="input-helper"></i></label>
                                                </div>
                                                <div class="col-11 p-0 ps-1 form-text small">* <?=$form['string47']?>
                                                    <a href="#" class="ps-1 nav-link d-inline" id="show-dgsvo"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#diverseModal"><?=$form['string48']?></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 p-0 mb-3">
                                            <div class="row">
                                                <div class="form-check col-1 p-0">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="rememberme"
                                                            class="form-check-input">
                                                        <i class="input-helper"></i></label>
                                                </div>
                                                <div class=" col-11 p-0 ps-1 form-text small"><?=$form['string30']?>
                                                    (optional)</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12 p-0 small">
                                            * <?=$form['string29']?>
                                        </div>
                                    </div>
                                    <div class="row mt-2 border-top pt-2">
                                        <div class="col-12 p-0">
                                            <input type="hidden" id="anmeldeID" name="anmeldeID" value="">
                                            <input type="hidden" name="sofahnum" id="sofahnum" value="">
                                            <input type="hidden" name="rfnum" id="rfnum" value="<?=$rfnum?>">

                                            <input type="hidden" name="add_to_order" id="add_to_order" value="mobile">
                                            <input type="hidden" class="form-control" name="lieferschein"
                                                id="lieferschein">
                                            <button type="submit" class="btn btn-primary float-end"
                                                id="btn-anmelden"><?=$form['string31']?></button>
                                        </div>
                                    </div>
                            </div>
                            </form>
                        </div>