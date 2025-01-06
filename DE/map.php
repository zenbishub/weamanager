<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map</title>
    <link rel="stylesheet" href="vendor/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendor/base/vendor.bundle.base.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="vendor/jquery-ui/jquery-ui.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/custom.css?<?=time()?>" rel="stylesheet">
    <link href="css/iframe.css?<?=time()?>" rel="stylesheet">
    <link href="css/daimler.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png" />
    <style>
    body {
        background-color: #000;
    }

    #map-viewer {
        position: relative;

    }

    #map-viewer img {
        width: 1600px;

    }

    .pin {
        position: absolute;
        border-radius: 50%;
        display: block;
        width: 18px;
        border: 2px solid #000;
        height: 18px;
        background-color: yellow;
    }

    .pin-selected {
        position: absolute;
        border-radius: 50%;
        display: block;
        width: 18px;
        border: 2px solid #000;
        height: 18px;
        background-color: blue;
        z-index: 100;
    }

    .pin-legende {
        border-radius: 50%;
        display: inline-block;
        width: 18px;
        border: 2px solid #000;
        height: 18px;
    }

    .pinData {
        position: absolute;
        margin-top: 15px;
    }

    div#map-legende {
        background-color: #f0f8ff7a;
        position: fixed;
        right: 0;
        top: 0;
        z-index: 100;
    }

    div#loader {
        position: fixed;
        z-index: 101;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        background-color: #000000ad;
    }

    span#overlay-text {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    iframe#iframe-map {
        width: 100%;
        height: 80vh;
    }
    </style>
</head>

<body>
    <div class='container-fluid p-0'>
        <div id='loader'><span class='fs-1 text-light' id='overlay-text'>Lade Positions...</span></div>
        <div class="row">
            <div id='map-viewer' class="col-12 p-0 overflow-auto">
                <img src='images/prepWerk5mapSmall.jpg?t=<?=time()?>'>
            </div>
            <div id="map-legende" class="p-2 col-12 col-md-3 col-xl-2 stretch-card">
                <div class="card bg-white" id="map-legende-box">
                    <div class="card-header">
                        <span class="p-3">Scanner Übersicht</span>
                    </div>
                    <div class="card-body p-0 overflow-auto">
                        <table id="map-legende-box-ul" class="table"></table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="modal-iframe" data-bd-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-iframe-body">
                </div>

            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script src="js/map.js?t=<?=time()?>"></script>
</body>

</html>