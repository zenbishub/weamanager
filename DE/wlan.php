<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>WLAN Testing</title>
    <link rel="stylesheet" href="vendor/ti-icons/css/themify-icons.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="vendor/base/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom.css?t=<?=time()?>">
    <link rel="shortcut icon" href="../images/favicon.png" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0 body-image">
                <div class="row w-100 mx-0">
                    <div class="col-8 col-md-6 col-lg-5 col-xl-4 rounded-2 mx-auto">
                        <div class="auth-form-light text-center py-5 px-4 px-sm-5 rounded-1">
                            WLAN TESTING
                            <span class="h1" id="wifi-icon"><i class="fas fa-wifi"></i></span>
                            <span class="ms-3 h1" id="counter"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script>
    $(function() {
        var i = 1;
        $("#counter").html(i);
        setInterval(
            () => {
                $("#counter").html(i);
                if (i > 6) {
                    $("#wifi-icon").addClass("text-danger");
                }
                i++;
            }, 1000
        )
        setInterval(() => {
            location.href = location.href;

        }, 6000);
    });
    </script>
</body>

</html>