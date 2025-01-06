<div class="col-xl-4 p-0 maincolumn" alt="utilities">
    <div class="row">
        <div class="col-12 col-md-6 col-xl-12 p-0 stretch-card d-none d-sm-block">
            <div class="card">
                <div class="card-header h5 card-title-custom pt-1 pb-1 pe-1">
                    <span class="ti-info"></span> Information / Abladeplatz
                    <div class="float-end">

                        <select id="lkw-filter" class="form-select pt-0 ps-1 pe-0 pb-0 small">
                            <option value="">LKW filtern</option>
                            <?php foreach($lieferant->LKWfilter as $key=>$value):?>
                            <option value="<?=$key?>"><?=$value?></option>
                            <?php endforeach;?>
                            <option value="ALLLKWs">alle</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="row" id="column-right">
                        <div class="col-md-9 p-0">
                            <div class="card p-0 mb-2 rounded shadow-none">
                                <div class="card-body p-0">
                                    <div id="stapler-overview" class="small">lade...</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 p-0 stretch-card">
                            <div class="card p-0 mb-2 rounded shadow-none">
                                <div class="card-body p-0">
                                    <div id="online-stapler-overview" class="small">lade...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-12 p-0 stretch-card">
            <div class="card p-0 mb-2 rounded">
                <div class="card-header h5 card-title-custom p-1 pe-1 ps-1">
                    <span class="ti-direction"></span> Voranmeldung
                    <div class="float-end d-none d-lg-block">
                        <button class="btn btn-info text-light border p-1 pe-1 ps-1" id="btn-toVoranmeldung"
                            data-bs-toggle="modal" data-bs-target="#toVoranmeldungOverview">Mehr Info</button>
                        <button class="btn btn-info text-light border p-1 pe-1 ps-1" id="btn-to-emailupload"
                            data-bs-toggle="modal" data-bs-target="#toVoranmeldungUpload">E-mail upload</button>
                        <?php if(date("d.m.Y",$sapAvis)!=date("d.m.Y")):?>
                        <a href="class/action?downloadfile=avisierungSAPfrachten.vbs"
                            class="btn btn-warning text-light border p-1 pe-1 ps-1" id="btn-avis-frachten">SAP-TAs</a>
                        <?php endif;?>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div id="getankommendlist" class="small"></div>
                </div>
            </div>
        </div>
    </div>
</div>