<?php
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});
if (isset($_REQUEST['ConnectionToWiFi'])) {
    echo "stable";
}
$controller         = new Controller();
$lieferant          = new classPublic($controller->scanFolder);
$array              = $controller->getOrderList("../");
$arrayMA            = $controller->getOrderListManuellerAuftrag("../");
$arrayKnum          = $controller->kennZeichenListe("../");
$arrayVoranmeldung  = $controller->kennZeichenVoranmeldungListe("../");


//Actions
if (isset($_REQUEST['ajaxWarteschlange'])) {
    if (empty($array)) {
        $lieferant->chooseLanguage();
        exit;
    }
    if (empty($_SESSION['frzlaufnummer']) && $_REQUEST['ajaxWarteschlange'] == "wea") {
        // Wenn die Anzeige resetet wurde
        $lieferant->chooseLanguage();
        exit;
    }
    // Anzeige für LKW Fahrer am Smartphone
    $lieferant->warteschlange($array, "../", "", $_REQUEST['ajaxWarteschlange']);
}

switch ($_SESSION['INUMMER']) {
    case 1:
        if (isset($_REQUEST['ajaxStaplerWarteschlange']) && !empty($array)) {
            if (empty($_SESSION['weamanageruser']) && $_REQUEST['ajaxStaplerWarteschlange'] == "stapler") {
                echo "redirecttologin";
                exit;
            }
            // Anzeige für Stapler im Werk 5
            krsort($array); // Stapler bekommt die Aufträge in umgekehrter Reihenfolge
            $lieferant->warteschlange($array, $arrayMA, "../", $_REQUEST['ajaxStaplerWarteschlange']);
        }
        break;
    case 2:
    case 3:
        if (isset($_REQUEST['ajaxStaplerWarteschlange']) && !empty($array)) {
            $lieferant->warteschlange($array, "../", $arrayMA, $_REQUEST['ajaxStaplerWarteschlange']);
        }
        // Anzeige für Stapler im Werk 9
        if (isset($_REQUEST['ajaxStaplerWarteschlange'])) {
            $bereichOffline = new StaplerAufgaben();
            $bereichOffline->meineAufgaben($_SESSION['weamanageruser'], $_SESSION['INUMMER']);
        }
        break;
}
if (isset($_REQUEST['changeRessourseStatus'])) {
    $lieferant->changeRessourseStatus("../");
}
if (isset($_REQUEST['ajaxScannerData'])) {
    $lieferant->scannerData("../");
}
if (isset($_REQUEST['ajaxMonitorPforte'])) {
    $lieferant->monitorPforte($array);
}
if (isset($_REQUEST['ajaxMonitorSchichtLeiter'])) {
    $lieferant->monitorSchichtLeiter($array);
}
if (isset($_REQUEST['autoChangeStatusWerksverkehr'])) {
    $lieferant->autoChangeStatusWerksverkehr($array, $_REQUEST['range']);
}
if (isset($_REQUEST['ajaxWerksVerkehrCounter'])) {
    $lieferant->werksVerkehrCounter("../");
}
if (isset($_REQUEST['ajaxWerksVerkehrBox'])) {
    $lieferant->werksverkehrBox("../");
}
if (isset($_REQUEST['ajaxCheckStausForSoundStart'])) {
    $lieferant->startSound($_REQUEST['rfnum'], "../");
}
if (isset($_REQUEST['ajaxCheckStausForSoundByCall'])) {
    $lieferant->startSoundByCall($_REQUEST['rfnum'], "../");
}
if (isset($_REQUEST['ajaxCheckStausForSoundStartZoll'])) {
    $lieferant->startSoundZoll($_REQUEST['rfnum'], "../");
}
if ($_REQUEST['requestNummer']) {
    $lieferant->checkQRCodeBeforeRegester("../");
}
if (isset($_REQUEST['save_image_wea'])) {
    $lieferant->ajaxSaveCanvasImage();
}
if (isset($_REQUEST['checkConnection'])) {
    echo $_REQUEST['checkConnection'];
}
if (isset($_REQUEST['getRFID'])) {
    echo $controller->maxIDinOrerList("../") + 1;
}
if (isset($_REQUEST['autoSuggesting'])) {
    $lieferant->autoSuggesting($_REQUEST['autoSuggesting']);
}
if (isset($_REQUEST['ajax_knum']) && $_REQUEST['responseData'] == "kennzeichen") {
    $lieferant->getAllKnumm($arrayKnum);
}
if (isset($_REQUEST['ajax_knum']) && $_REQUEST['responseData'] == "voranmeldung") {
    $lieferant->getAllKnumm($arrayVoranmeldung);
}
$controller->stopSound();
$controller->setSoundModus();
$lieferant->chatConversation("../");
$lieferant->messageReaded("../");
$lieferant->checkInactiveTime($lieferant->inactivetimeStart, $lieferant->inactivetimeEnd, "../");
$lieferant->checkLegitimationConfirmation("../");
$evochat = new Evochat($_SESSION['werknummer']);
$evochat->evoChatLastShowMessage("../");
$evochat->evoChatReadedLastMessage("../");
$scanner = new handScanner();
$scanner->handleScannerInfo("../");