<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timer TMI GabischCollector</title>
    <link href="css/style.css" rel="stylesheet">
    <script src="vendor/jquery/jquery.min.js"></script>
    <script>
    function counter() {
        var i = 0;
        var b = 0;
        var c = 0;
        setInterval(function() {
            var counter = $("#counter");
            counter.text(i);
            if (i == 20) {
                checkKonfiFiles();
                i = 0;
            }
            i++;
        }, 1000)
        setInterval(function() {
            var counter = $("#counter_change");
            counter.text(c);
            if (c == 20) {
                timerJobs();
                c = 0;
            }
            c++;
        }, 1000)
    }

    function checkKonfiFiles() {
        var output = $("#done_output");
        $.post("class/GabischCollector.php", {
            "runtimer": "on"
        }, function(data) {
            output.html(data);

        });

    }

    function timerJobs() {
        var output = $("#done_jobs");
        $.post("class/Job.php", {
            change_status: "on"
        }, function(data) {
            output.html(data);
        });
    }
    $(function() {
        counter();
    });
    </script>
</head>

<body>
    <div class='nav navbar bg-dark text-light ps-2'>
        <h4>Timer for TMI </h4>

        <div id='showzeit' class='h3 float-right'></div>
    </div>
    <div>
        <div class="container-fluid m-4">
            <div class="row">
                <div class="col-12 col-md-4 col-lg-3 border p-3">
                    <h4>Reset Daily-Data</h4>
                    <p>Aktualisierung in 20/<span id="counter"></span> Sekunden</p>
                    <div id="done_output"></div>

                </div>
                <div class="col-12 col-md-4 col-lg-3 border p-3">
                    <h4>Change Unload-Status by Custom Time</h4>
                    <p>Aktualisierung in 20/<span id="counter_change"></span> Sekunden</p>
                    <h5>TMI automatische Statusänderung</h5>
                    <div id="done_jobs">wartend...</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>