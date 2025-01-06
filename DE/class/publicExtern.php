<?php 
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

$lieferant = new classPublic();
$bereichOffline = new StaplerAufgaben();
$bereichOffline->setLKWnummerToSession();
$bereichOffline->offlineLoad();
$bereichOffline->auftragFertig();
$bereichOffline->sequenzierungsLager();
$bereichOffline->openPlatzBelegung();
$bereichOffline->openStartBeladen();
$bereichOffline->openHoflagerBeladen();
$bereichOffline->addToSeqLager();
$bereichOffline->offlineversandBuchen();
$bereichOffline->inhoflagerBuchen();