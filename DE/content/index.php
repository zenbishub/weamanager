<!doctype html>
<html>

<head>
    <title>Webcam Ocrad.js Example</title>
    <style>
    body {
        background: whiteSmoke;
        font-family: sans-serif;
        margin: 30px;
    }

    #transcription,
    #video {
        background: white;
        display: inline-block;
        border: 1px solid #ddd;
        margin: 10px;
    }

    #video {
        flex-grow: 1;
        width: 500px;
        background: black;
    }

    #text {
        font-size: 25px;
        text-align: center;
        flex-grow: 1;
        padding: 30px;
    }

    #transcription {
        width: 300px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    #controls {
        padding: 20px;
        color: #222;
        border-top: 1px solid #ddd;
        background: whiteSmoke;
    }

    #main {
        display: flex;
    }

    #transcription.recognizing {
        color: gray;
    }

    #transcription.done {
        color: black;
    }
    </style>
</head>

<body>
    <h1>Webcam Ocrad.js Example</h1>

    <script src="ocrad.js"></script>
    <script>
    function recognize_snapshot() {
        // document.getElementById('text').innerText = "(Recognizing...)"
        // document.getElementById('transcription').className = "recognizing"
        OCRAD(document.getElementById("video"), {
            //numeric: true
            //invert: document.getElementById('whiteText').checked // set this for white on black text
        }, function(text) {
            console.log(text);
            if (text != "") {
                document.getElementById('text').innerText = text;

            }
            //document.getElementById('transcription').className = "done"
            //document.getElementById('text').innerText = text || "(empty)";
        })

    }
    async function getDevices() {
        const devices = await navigator.mediaDevices.enumerateDevices();
        console.log(devices);
    }

    function acquiredVideo(stream) {
        var video = document.getElementById('video')
        if ('mozSrcObject' in video) {
            video.mozSrcObject = stream;
        } else if (window.webkitURL) {
            video.src = window.webkitURL.createObjectURL(stream);
        } else {
            video.src = stream;
        }
        video.play();

        document.getElementById('blackText').checked = true;
    }
    window.onload = function() {
        getDevices();
        // navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator
        //     .mozGetUserMedia;
        // navigator.mediaDevices.getUserMedia({
        //     video: true
        // }, acquiredVideo, function() {})
        var video = document.getElementById('video')
        navigator.mediaDevices.getUserMedia({
            video: true
        }).then(stream => {
            video.srcObject = stream
        })

    }
    //recognize_snapshot();
    setInterval(recognize_snapshot, 1000);
    </script>

    <div id="main">
        <video id="video" autoplay></video>
        <div id="transcription">
            <div id="text">
                Click on the video to recognize a still capture
            </div>
            <!-- <div id="controls">
                <input type="radio" name="textColor" id="whiteText" value="white" onchange="recognize_snapshot()">
                <label for="whiteText"><b>White Text</b> on Dark Background</label><br>
                <input type="radio" name="textColor" id="blackText" value="black" onchange="recognize_snapshot()"
                    checked>
                <label for="blackText"><b>Black Text</b> on Light Background</label>
            </div> -->
        </div>
    </div>



</body>

</html>