<div class="modal fade" id="zollactionModal" tabindex="-1" aria-labelledby="zollactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="class/action" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="zollactionModalLabel">Meldung von der Zollstelle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="zoll-actionmodal-body">
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="add_zoll_meldung" name="add_zoll_meldung" value="1">
                    <input type="hidden" name="rfnum" id="zoll-rfnum" value="">
                    <button type="submit" class="btn btn-primary" id="submit-btn">Zollstellemeldung setzen</button>
                </div>
            </div>
        </form>
    </div>
</div>