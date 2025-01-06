

<div class="modal fade" id="createQrcode" tabindex="-1" aria-labelledby="createQrcodeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createQrcodeModalLabel">QR Code</h5>
        <button type="button" class="btn-close hide-by-print" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="header"></div>
        <div class="row">
            <div class="col-4 pe-0"><span id="canvas-qrcode"></span></div>
            <div class="col-8 p-0" id="box-qrcodetext"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary hide-by-print" id="qrcode-print-btn"><i class="ti-print"></i> drucken</button>
      </div>
    </div>
  </div>
</div>