<div class="container bg-white border rounded">
    <div class="row pt-4">
    <h4>Upload Informationsdatei(.xlsx)</h4>
    <div class="col-6 mt-2">
        <form action="class/ajax" method="post" enctype="multipart/form-data" id="ajax-upload">
            <div class="form-group">
                <select name="folder" class="form-control text-black col-6 col-md-4" required>
                    <option value="">Lieferant wählen</option>
                    <option value="ekol">Ekol</option>
                </select>
            </div>
            <div class="form-group">
                <input type="file" name="infomation_file" class="form-control col-6" required>
            </div>
            <div class="form-group">
                <input type="hidden" name="upload_zoll_information" value="1">
                <button type="submit" id="send-btn" class="btn btn-primary text-white">hochladen</button>
                <div class="row mt-4">
                    <div class="col-6 p-0" id="result-box">

                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
