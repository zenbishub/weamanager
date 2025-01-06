<?php
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});


$controller = new Controller();
$controller->resetOldSession();
$controller->setLKWfilter();
$evochat = new Evochat($_SESSION['werknummer'], "../");
$sonderfahrt = new Sonderfahrt($_SESSION['werknummer']);
$zollgut = new Zollgut($_SESSION['werknummer']);

$sonderfahrt->deleteSonderfahrtenByTimelimit("../");
$sonderfahrt->addSonderfahrt("../");
$zollgut->addZollNotice();
$controller->sendungsinfoErfassen();
$controller->logout();
$controller->login();
$controller->setLanguage();
$controller->requestFRZdata();
$controller->requestFRZdataByScanQRcode();
$controller->registerLiferant();
$controller->setToWaitOrder();
$controller->updateRFnumData();
$controller->passZollgut();
$controller->setToProzess();
$controller->setToProzessWerksverkehr();
$controller->entryPassed();
$controller->prozessDone();
$controller->removeFromOrder();
$controller->vehicleGone();
$controller->setToWaitOrderByNummber();
$controller->setToWaitOrderByWarteNummber();
$controller->addBMIData("../");
$controller->addUnloadplace("../");
$controller->updateBMIData("../");
$controller->addPersonData("../");
$controller->addReminder("../");
$controller->updatePersonalData("../");
$controller->updateEntladestelleData("../");
$controller->deleteData();
$controller->setStaplerForUnload();
$controller->backToWaitList();
$controller->backToPreviosStatus();
$controller->sendToNextStep();
$controller->addReklamation("../");
$controller->setToLeergutUnload();
$controller->addManuellerAuftrag("../");
$controller->addUnloadprio("../");
$controller->doneManuellerAuftrag("../");
$controller->setSessionReminderID();
$controller->addCustomIncommingTime("../");
//$controller->getFlowBackup("../");
$controller->downloadScript();
$controller->onoff();
$controller->downloadAPKupdate();
$controller->adittionalJob();
if (isset($_REQUEST['addnewmessage'])) {
    $controller->addnewmessage("../");
}
$evochat->startChat("../");
$extern = new Externe($_SESSION['werknummer'], "../");
$extern->uploadPreregisterInformationFromDashboard($_SESSION['werknummer']);