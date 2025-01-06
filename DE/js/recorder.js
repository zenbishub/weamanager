var startAudioRecord = document.getElementById("start-audio-record");
let recorder = null;
const player = document.getElementById("audio");
var icon = document.getElementById("icon-record-audio");
var closeBtn = document.getElementById("btn-chat-close");

function stopStreamedAudio(stream) {
  const tracks = stream.getTracks();
  tracks.forEach((track) => {
    if (track.readyState == "live" && track.kind === "audio") {
      track.stop();
    }
  });
}
function doRecordAudio() {
  return new Promise(function (resolve) {
    navigator.mediaDevices
      .getUserMedia({
        audio: true,
      })
      .then(function (stream) {
        const audioStream = stream;
        const mediaRecorder = new MediaRecorder(stream);
        const audioChunks = [];
        mediaRecorder.addEventListener("dataavailable", function (event) {
          audioChunks.push(event.data);
        });
        const start = function () {
          startAudioRecord.onclick = async function () {
            player.innerHTML = "";
            if (recorder != null) {
              const audio = await recorder.stop();
              const reader = new FileReader();
              reader.readAsDataURL(audio.audioBlob);
              reader.onloadend = function () {
                let base64 = reader.result;
                base64 = base64.split(",")[1];
                let addaudio = document.getElementById("addaudio");
                addaudio.value = base64;
              };
            }
          };
          mediaRecorder.start();
        };
        const stop = function () {
          return new Promise(function (resolve) {
            mediaRecorder.addEventListener("stop", function () {
              const audioBlob = new Blob(audioChunks);
              const audioUrl = URL.createObjectURL(audioBlob);
              player.innerHTML =
                "<audio controls><source src='" +
                audioUrl +
                "' type='audio/mpeg'></audio>";
              resolve({
                audioBlob,
              });
            });
            mediaRecorder.stop();
          });
        };
        resolve({
          start,
          stop,
        });
        closeBtn.addEventListener("click", () => {
          stopStreamedAudio(audioStream);
        });
      });
  });
}
async function recordAudio() {
  recorder = await doRecordAudio();
  recorder.start();
  let hasClass = startAudioRecord.classList.contains("text-danger");
  if (hasClass != true) {
    startAudioRecord.classList.add("text-danger");
    icon.classList = "fa fa-stop-circle";
    player.innerHTML = `<div class="spinner-grow text-danger me-1" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>recording...`;
  } else {
    startAudioRecord.classList.toggle("text-danger");
    icon.classList = "fa fa-microphone";
  }
}
startAudioRecord.addEventListener("click", recordAudio);
