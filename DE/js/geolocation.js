function geoFindMe() {
  const geoposition = document.querySelector("#geoposition");
  //const geopositionlink = document.querySelector("#geoposition-link");
  const ip = geoposition.getAttribute("data-index");
  geoposition.innerHTML = `<div class="spinner-border spinner-border-sm" role="status">
  <span class="visually-hidden">Loading...</span>
</div>`;
  const options = {
    enableHighAccuracy: true,
    timeout: 15000,
    maximumAge: 0,
  };

  function success(position) {
    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;
    //var latlon = position.coords.latitude + "," + position.coords.longitude;

    //alert(`https://www.openstreetmap.org/#map=18/${latitude}/${longitude}`);
    geoposition.innerHTML =
      "X" + latitude + "<br>Y" + longitude + "<br>" + ip.substring(6);
    //geopositionlink.innerHTML = `<a id="map-link" class="btn btn-sm btn-info p-1 ps-2 pe-2 text-light" href="https://www.openstreetmap.org/?mlat=${latitude}&mlon=${longitude}&zoom=18layers=M">geo</a>`;
    $.post(
      "class/ajax",
      {
        setgeolocation: 1,
        scannerIP: ip,
        coordinateX: latitude,
        coordinateY: longitude,
      },
      function (response) {
        // console.log(response);
        // geoposition.innerHTML =
        //   "X" + latitude + "<br>Y" + longitude + "<br>" + ip;
      }
    );
  }

  function error() {
    geoposition.textContent = "Unable to catch location";
    setTimeout(function () {
      geoposition.innerHTML = `<div class="text-center"><div class="spinner-border spinner-border-sm" role="status">
  <span class="visually-hidden">Loading...</span>
</div></div>`;
    }, 5000);
    setTimeout(geoFindMe, 8000);
  }
  if (!navigator.geolocation) {
    geoposition.textContent = "Geolocation is not supported by your browser";
  } else {
    geoposition.textContent = ip.substring(6) + "\nLocating…";
    navigator.geolocation.watchPosition(success, error, options);
  }
}
geoFindMe();
