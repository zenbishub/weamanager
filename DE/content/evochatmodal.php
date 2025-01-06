<div class="modal fade" id="evochatModal" tabindex="-1" data-bs-backdrop="false" data-bs-backdrop="static"
    aria-labelledby="evochatModalLabel" aria-hidden="true">
    <div class="modal-dialog shadow-lg" id="evochat-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="evochatModalLabel">Chatverlauf</h5>
                <button type="button" class="btn-close" id="btn-chat-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body alert-warning">
                <div class="row">
                    <div class="col-12 p-0">

                        <div class="row">
                            <div class="col-12 border rounded pt-3 pb-2" id="evochat-verlauf">
                                <a id="chatbottom" href="#chatbottom"></a>
                                <div id="verlauf"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 p-0">
                        <form method="post" enctype="multipart/form-data" action="class/action.php" id="evochat-form">
                            <div id="header-evochat"></div>
                            <div class="mb-2">
                                <textarea class="form-control" id="message-text" name="message-text"
                                    placeholder="Nachricht schreiben..."></textarea>
                                <input type="hidden" name="send_to_evochat" value="<?=time()?>">
                                <input type="hidden" name="Empfaenger" id="send-to-evochat">
                                <input type="hidden" name="Werknummer" id="werknummer"
                                    value="<?=$_SESSION['werknummer']?>">
                                <input type="hidden" name="Absender" id="Absender"
                                    value="<?=$_SESSION['weamanageruser']?>">
                                <input type="hidden" name="returnURI" id="returnURI">
                                <input type="hidden" name="Target" id="target">
                            </div>
                            <div class="nav navbar p-0 shadow-none align-items-center">
                                <div class="col-2 ps-0">
                                    <span type="button"
                                        class="fs-2 pointer  text-primary p-0 ps-2 pe-2 border btn-light rounded"
                                        id="start-audio-record"><i class="fa fa-microphone" id="icon-record-audio"
                                            style="cursor: pointer;"></i></span>
                                </div>
                                <div class="col-8 p-0">
                                    <div id="audio"></div>
                                    <input type="hidden" name="addaudio" id="addaudio">

                                </div>

                                <div class="col-2 text-end pe-0 pt-1">
                                    <label>
                                        <span type="button"
                                            class="fs-2 pointer text-primary p-0 ps-2 pe-2 border btn-light rounded"><i
                                                class="fas fa-camera"></i></span>
                                        <input type="file" class="d-none" name="chat_attachment" id="chat_attachment"
                                            accept=".JPG,.JPEG">
                                    </label>
                                </div>
                            </div>
                            <div id="showinputvalue" class="col-12 text-end mb-3"></div>

                            <div class="modal-footer border-0 p-0">
                                <button type="sublit" id="evochat-send-btn" class="btn btn-primary">absenden</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/recorder.js"></script>