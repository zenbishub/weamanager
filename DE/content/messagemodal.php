<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Messagebox</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="header"></div>
                <form method="post" action="class/action">
                    <label for="message-who" class="col-form-label small">Absender</label>
                    <select name="message-who" id="message-who" class="form-control col-6" required>
                        <option value="">Absender wählen</option>
                        <option value="Hauptpforte">Hauptpforte</option>
                        <option value="Wareneingang">Wareneingang</option>
                        <option value="Zollstelle">Zollstelle</option>
                        <option value="Bereich Versand">Bereich Versand</option>
                        <option value="Bereich Lack">Bereich Lackierung</option>
                    </select>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label small">Nachricht:</label>
                        <textarea class="form-control" id="message-text" name="message-text"></textarea>
                        <input type="hidden" name="return" id="return">
                        <input type="hidden" name="rfnum" id="rfnum">
                        <input type="hidden" name="Nummer" id="kfznum">
                        <input type="hidden" name="Werknummer" id="werknummer">
                        <input type="hidden" name="addnewmessage" value="1">
                        <input type="hidden" name="sendtime" value="<?=time()?>">
                    </div>
                    <div class="modal-footer">
                        <button type="sublit" class="btn btn-primary">absenden</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>