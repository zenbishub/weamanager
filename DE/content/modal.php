<div class="modal fade" id="confirmModal" data-bs-backdrop="static" aria-labelledby="confirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-protokoll">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel"><i class="ti-alert"></i> Bitte bestätigen
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="class/action.php" id="protokoll-form">
                <div class="modal-body p-1 p-lg-3" id="modal-protokoll">
                    lade Inhalt...
                </div>
                <div class="row p-3 border-top">
                    <div class="col-6 pt-2 text-left"></div>
                    <div class="col-6 pt-2 text-right">
                        <button type="submit" class="btn btn-primary" id="change-position-status">bestätigen</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>