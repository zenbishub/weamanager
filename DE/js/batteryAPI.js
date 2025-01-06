function batteryStatus() {
  navigator.getBattery().then((battery) => {
    function updateAllBatteryInfo() {
      var chargeStatus = updateChargeInfo();
      var levelStatus = updateLevelInfo();
      //   var chargingTime = updateChargingInfo();
      //   var disChargingTime = updateDischargingInfo();
      setInterval(() => {
        $.post(
          "class/publicAjax",
          {
            handleScannerInfo: 1,
            chargeStatus: chargeStatus,
            levelStatus: levelStatus,
          },
          function (data) {
            // console.log(data);
          }
        );
      }, 15000);
    }
    updateAllBatteryInfo();
    battery.addEventListener("chargingchange", () => {
      updateChargeInfo();
    });
    function updateChargeInfo() {
      //console.log(`Battery charging? ${battery.charging ? "Yes" : "No"}`);
      return `${battery.charging ? "Ja" : "Nein"}`;
    }
    battery.addEventListener("levelchange", () => {
      updateLevelInfo();
    });
    function updateLevelInfo() {
      //console.log(`Battery level: ${battery.level * 100}%`);
      var value = Math.ceil(battery.level * 100);
      document.getElementById("battery-status").innerText = value + "%";
      return `${value}`;
    }
    // battery.addEventListener("chargingtimechange", () => {
    //   updateChargingInfo();
    // });
    // function updateChargingInfo() {
    //   console.log(`Battery charging time: ${battery.chargingTime} seconds`);
    //   return `Battery charging time: ${battery.chargingTime} seconds`;
    // }
    // battery.addEventListener("dischargingtimechange", () => {
    //   updateDischargingInfo();
    // });
    // function updateDischargingInfo() {
    //   console.log(
    //     `Battery discharging time: ${battery.dischargingTime} seconds`
    //   );
    //   return `Battery discharging time: ${battery.dischargingTime} seconds`;
    // }
  });
}
$(function () {
  batteryStatus();
});
