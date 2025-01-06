<div class="modal fade" id="reklamation" data-bs-backdrop="static" tabindex="-1" aria-labelledby="reklamationModalLabel"
    aria-hidden="true">
    <form action="class/action.php" method="post" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reklamationModalLabel">Reklamations Bilder Lieferung <span
                            id="reklamations-bilder-zu"></span></h5>
                    <button type="button" class="btn-close hide-by-print" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group d-none" id="open-by-mobile">
                            <label for="input" class="small">Kamera öffnen</label>
                            <button type="button"
                                class="btn-lg btn-rounded btn-light border shadow col-12 p-2 btn-image"
                                id="take-image-from-document" data-index="reklamation_image"><span class="font-large"><i
                                        class="ti-camera"></i></span></button>
                        </div>
                        <div class="form-group">
                            <label for="input" class="small">Hochladen</label>
                            <input type="file" name="reklamation_bilder[]" class="form-control" multiple>
                        </div>
                        <div class="form-group">
                            <textarea name="reklamation_beschreibung" class="form-control" cols="30" rows="5"
                                placeholder="Reklamation Beschreibung" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="add_reklamation" value="1">
                    <input type="hidden" class="form-control" name="reklamation_image" id="reklamation_image">
                    <input type="hidden" name="rfnum" id="reklamation_hidden_rfnum" value="">
                    <input type="hidden" name="returnURI" value="<?=$_SERVER['REQUEST_URI']?>">
                    <button type="submit" class="btn btn-primary" id="reklamation-btn"><i class="ti-arrow"></i>
                        hochladen</button>
                </div>
            </div>
        </div>
    </form>
</div>