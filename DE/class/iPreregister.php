<?php

interface IPreregister{
    public function insertTableInformations($kennzeichen);
    public function uploadInformation($kennzeichen);
    public function referenceTable($kennzeichen);
}