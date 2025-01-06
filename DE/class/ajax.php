<?php

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});
$abteilung = "";
$controller     = new Controller();
$public         = new classPublic();
$lieferant      = new Lieferant($controller->scanFolder);
$verlauf        = new Verlauf();
$maingate       = new Maingate();
$evochat        = new Evochat($_SESSION['werknummer']);
$extern         = new Externe($_SESSION['werknummer']);
$zollgut        = new Zollgut($_REQUEST['werknummer']);
$information    = new Information();
$registeredData = new Sonderfahrt($_REQUEST['werknummer']);
$frachten       = new Frachten($_SESSION['werknummer']);
$sounds         = new Sounds("assets/sound/", "pinging.mp3");
$controller->stopSound();
$controller->setSoundModus();
$controller->setToProzess();
$controller->saveNeworder();
$controller->checkRemider();
$controller->setOnlineUser("../");
$extern->uploadPreregisterInformation($_SESSION['werknummer']);
$extern->getSendungsData();
$extern->getSendungsDataByNumber();
$frachten->tracksByDepartment();

if (isset($_REQUEST['check_anmelde_id'])) {
    $registeredData->checkAnmeldeID($_REQUEST['check_anmelde_id'], $_REQUEST['route'], $_REQUEST['werknummer']);
}
if (isset($_REQUEST['ajaxZollwatreListe'])) {
    $zollgut->zollWarteListe("../");
}
if (isset($_REQUEST['ajaxZollDoneListe'])) {
    $zollgut->zollDoneListe("../");
}
if (isset($_REQUEST['ajaxWarteschlange'])) {
    $lieferant->warteschlange($controller->getOrderList("../"));
}
if (isset($_REQUEST['ajaxImProzess'])) {
    $lieferant->imProzess($controller->getOrderList("../"));
}
if (isset($_REQUEST['ajaxVerlaufTabelle'])) {
    $verlauf->verlaufTabelle($controller->getOrderListToday("../"));
}
if (isset($_REQUEST['ajaxVerlaufTabelleTen'])) {
    //846000 10 Tage
    $verlauf->verlaufTabelle($controller->getOrderListTotal(846000));
}
if (isset($_REQUEST['ajaxVerlaufTabelleTwenty'])) {
    //25380000 30 Tage
    $verlauf->verlaufTabelle($controller->getOrderListTotal(2592000));
}
if (isset($_REQUEST['getankommendlist'])) {
    $lieferant->getAnkommendList();
}
if (isset($_REQUEST['getPreRegisterData'])) {
    $lieferant->getPreRegisterData("../");
}
if (isset($_REQUEST['getEimalUploadForm'])) {
    $lieferant->preRegisterEmailForm();
}
if (isset($_REQUEST['addnewmessage'])) {
    $controller->addnewmessage("../");
}
if (isset($_REQUEST['loadEditFormBMI'])) {
    $controller->editFormBMI();
}
if (isset($_REQUEST['loadEditFormPersonal'])) {
    $controller->editFormPersonal();
}
if (isset($_REQUEST['loadEditFormEntladestellen'])) {
    $controller->editFormEntladestellen();
}
if (isset($_REQUEST['saveMyPortview'])) {
    $controller->saveMyPortview();
}
if (isset($_REQUEST['checkUserRole'])) {
    $controller->checkUserRole("../");
}
if (isset($_REQUEST['confirmWerkschutz'])) {
    $controller->confirmWerkschutz("../");
}
if (isset($_REQUEST['changeZollUnloadPlant'])) {
    $controller->changeZollUnloadPlant();
}
if (isset($_REQUEST['ajaxPforteMonitorEinfahrt'])) {
    $inaktive = $public->checkInactiveTime($public->inactivetimeStart, $public->inactivetimeEnd);
    $maingate->monitorEinfahrt($controller->getOrderList("../"), $inaktive);
}
if (isset($_REQUEST['ajaxPforteMonitorAusfahrt'])) {
    $inaktive = $public->checkInactiveTime($public->inactivetimeStart, $public->inactivetimeEnd);
    $maingate->monitorAusfahrt($controller->getOrderList("../"), $inaktive);
}
if (isset($_REQUEST['checkScannerOnline'])) {
    $maingate->checkScannerOnline();
}
if (isset($_REQUEST['callOnceByClick'])) {
    $maingate->callOnceByClick();
}
if (isset($_REQUEST['getChatVerlauf'])) {
    $evochat->showChat($_REQUEST['user'], $_REQUEST['empfaenger'], $_REQUEST['absender'], "../");
}
if (isset($_REQUEST['getBMINummForChatList'])) {
    $evochat->listOfEmpfaenger("../");
}
if (isset($_REQUEST['ajaxGetInformationOnParking'])) {
    $onParking = $controller->getOnParking($controller->getOrderList("../"), "parking");
    $information->trucksOnParking($onParking);
}
if (isset($_REQUEST['ajaxGetInformationImProzess'])) {
    $onProzess = $controller->getOnParking($controller->getOrderList("../"), "process");
    $information->trucksImProcess($onProzess);
}
if (isset($_REQUEST['ajaxGetGoodsInformation'])) {
    $extern->ajaxGetGoodsInformation();
}
if (isset($_REQUEST['getInformFromPreRegisterData'])) {
    $extern->ajaxGetInformFromPreRegisterData();
}
if (isset($_REQUEST['ajaxFindSendungsnummer'])) {
    $extern->ajaxFindSendungsnummer();
}
if (isset($_REQUEST['soundListener'])) {
    $sounds->conditionsListener("../");
}
if (isset($_REQUEST['soundListenerWeiterleitung'])) {
    $sounds->conditionsListenerWeiterleitung("../");
}

if (isset($_REQUEST['setgeolocation'])) {
    $public->setGeolocation("../");
}