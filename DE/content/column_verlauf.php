<div class="row p-0 pb-1 maincolumn <?=$showContent?>" alt="verlauf">
    <div class="col-xl-12 p-0 pb-2">
        <div class="accordion card" id="accordion1">
            <div class="accordion-item border-left-primary  border-left-5">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <i class="fas fa-chart-bar me-1"></i> Verlauf
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                    data-bs-parent="#accordion1">
                    <div class="accordion-body p-2 border-0">
                        <ul class="nav nav-tabs mt-2 bg-light" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="heute-tab" data-bs-toggle="tab"
                                    data-bs-target="#heute" type="button" role="tab" aria-controls="heute"
                                    aria-selected="true">Heute</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="ten-tab" data-bs-toggle="tab" data-bs-target="#ten"
                                    type="button" role="tab" aria-controls="ten" aria-selected="false">10 Tage</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="twenty-tab" data-bs-toggle="tab" data-bs-target="#twenty"
                                    type="button" role="tab" aria-controls="twenty" aria-selected="false">30
                                    Tage</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active overflow-auto" id="heute" role="tabpanel"
                                aria-labelledby="heute-tab">
                                <div id="column-verlauf-tabelle" class="pt-2">
                                    <?php //$verlauf->verlaufTabelle($getOrderList)?>
                                </div>
                            </div>
                            <div class="tab-pane fade overflow-auto" id="ten" role="tabpanel" aria-labelledby="ten-tab">
                                <div id="column-verlauf-tabelle-10days" class="pt-2">
                                    <div class="row justify-content-center m-4 p-4">
                                        <div class="col-1">
                                            <div class="spinner-border" role="status"><span
                                                    class="sr-only">Loading...</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade overflow-auto" id="twenty" role="tabpanel"
                                aria-labelledby="twenty-tab">
                                <div id="column-verlauf-tabelle-20days" class="pt-2">
                                    <div class="row justify-content-center m-4 p-4">
                                        <div class="col-1">
                                            <div class="spinner-border" role="status"><span
                                                    class="sr-only">Loading...</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>