<?php 

class Information{

    public function trucksOnParking($onParking){
       
        echo '
        <div class="row mt-2 mb-2">';
            if(empty($onParking)):
                echo '<div class="col-12 p-1 text-center">keine angemeldete Transporte</div>';
            endif;
                foreach($onParking as $array):
                    switch($array['Zollgut']){
                        case "JA":
                            $zollgut = '<span class="badge badge-danger float-end rounded font-large me-2">Zoll</span>';
                            break;
                        case "PASSIERT":
                            $zollgut = '<span class="badge badge-success float-end rounded font-large me-2">Zoll</span>';
                            break;

                }
                echo '<div class="col-12 col-md-6 col-lg-4 col-xl-3 p-1">
                        <div class="card small p-0 border">
                            <div class="card-header p-1  row">
                                <div class="col-9 p-0">'.$this->statusPrio($array).'</div>
                                    <div class="col-3 p-0 dropdown no-arrow text-end">
                                        <a class="dropdown-toggle font-large" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-primary"></i>
                                                    </a>
                                                    <div
                                                        class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                                        <div class="dropdown-header">Actions</div>
                                                        <div class="dropdown-divider"></div>
                                                        <a href="#" class="dropdown-item set-prio"
                                                            alt="'.$array['rfnum'].'&'.$array['Firma'].'&'.$array['Nummer'].'"
                                                            data-bs-toggle="modal" data-bs-target="#prio">
                                                            <span class="me-2"><i class="ti-location-arrow"></i></span>
                                                            Prio ändern
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                <span class="badge badge-info float-end rounded font-large">'.$array['rfnum'].'</span>
                                                '.$this->statusZoll($array).'
                                                <span class="d-block fs-4">'.$array['Nummer'].'</span>
                                                <span class="d-block">'.$array['Firma'].'</span>
                                                <span class="d-block"></span>
                                                <span class="d-block small"><img src="assets/img/flage'.$array['Sprache'].'.JPG"
                                                        class="img-fluid rounded border d-inline me-2"
                                                        style="width:10%">'.$array['Anmeldung'].'</span>
                                                <span class="d-block small">Scanner: '.$array['scanner'].'</span>
                                            </div>
                                        </div>
                                    </div>';
                                    endforeach;
                                echo '</div>';
    }
    public function trucksImProcess($onProzess){
        echo '<div class="row">';
        if(empty($onProzess)):
            echo '<div class="col-12 p-1 text-center">keine angemeldete Transporte</div>';
            endif;
            foreach($onProzess as $array):
                    echo '<div class="col-6 p-1">
                            <div class="card small p-0 border">
                                <div class="card-body p-2">
                                    <span class="badge badge-info float-end rounded font-large">'.$array['rfnum'].'</span>
                                    '.$this->statusZoll($array).'
                                    <span class="d-block fs-4">'.$array['Nummer'].'</span>
                                    <span class="d-block">'.$array['Firma'].'</span>
                                    <span class="d-block"></span>
                                    <span class="d-block small"><img src="assets/img/flage'.$array['Sprache'].'.JPG"
                                            class="img-fluid rounded d-inline me-2"
                                            style="width:10%">'.$array['Anmeldung'].'</span>
                                    <span class="d-block small">Scanner: '.$array['scanner'].'</span>
                                </div>
                            </div>
                        </div>';
                endforeach;
            echo '</div>';
    }
    private function statusPrio($array){
        switch($array['Prio']){
                case "SD":
                    $prio = "Prio: <span title='sehr dringend' class='pointer badge badge-danger border rounded'>".$array['Prio']."</span>
                    <span class='smaller'>".$array['Prio-Melder']."</span>";
                break;
                case "D":
                    $prio = "Prio: <span title='dringend' class='pointer badge badge-warning border rounded'>".$array['Prio']."</span>
                    <span class='smaller'>".$array['Prio-Melder']."</span>";
                break;
                default:
                    $prio = "Prio: <span title='normal' class='pointer badge badge-light text-secondary border rounded'>N</span>";
                break;
            }
        return $prio;
    }
    private function statusZoll($array){
          switch($array['Zollgut']){
            case "JA":
                $zollgut = '<span class="badge badge-danger float-end rounded font-large me-2">Zoll</span>';
                break;
            case "PASSIERT":
                $zollgut = '<span class="badge badge-success float-end rounded font-large me-2">Zoll</span>';
                break;
            }
        return $zollgut;
    }
}