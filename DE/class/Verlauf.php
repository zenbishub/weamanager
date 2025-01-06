<?php

class Verlauf
{
    public function verlaufTabelle($OrderList)
    {
        if (empty($OrderList)) {
            exit;
        }
        echo '<div class="table-responsive">
        <table class="table table-striped table-bordered rounded dataTable" id="dataTable"  width="100%" cellspacing="0">
            <thead>
            <tr class="small bg-dark text-light">
                    <th class="p-2 font-weight-light">#</th>
                    <th class="p-2 font-weight-light">WN</th>
                    <th class="p-2 font-weight-light">Anmeldung</th>
                    <th class="p-2 font-weight-light">Firma</th>
                    <th class="p-2 font-weight-light">Kennzeichen</th>
                    <th class="p-2 font-weight-light">Ladung</th>
                    <th class="p-2 font-weight-light">Dokumente</th>
                    <th class="p-2 font-weight-light">Zollgut</th>
                    <th class="p-2 font-weight-light">GG</th>
                    <th class="p-2 font-weight-light">Eing.</th>
                    <th class="p-2 font-weight-light">Einfahrt</th>
                    <th class="p-2 font-weight-light">Abladestelle</th>
                    <th class="p-2 font-weight-light">Abfertigung</th>
                    <th class="p-2 font-weight-light">Signiert</th>
                    <th class="p-2 font-weight-light">Verlassen</th>
                    <th class="p-2 font-weight-light">Pforte</th>
                    <th class="p-2 font-weight-light">Status</th>
                </tr>
            </thead>
            <tbody class="bg-light">';
        krsort($OrderList);
        $lf = 1;
        foreach ($OrderList as $key => $array):
            $link = null;
            $Pforte = null;
            $eingesteuert = null;
            $protokoll_WA = json_decode($array['Protokoll_WA']);
            if (!empty($array['Lieferdokument'])) {
                $link = "<a href='ScannFolder/" . $array['Lieferdokument'] . "' target='_blank'>Lieferdokument</a>";
            }
            if (empty($array['Einfahrt'])) {
                $array['Einfahrt'] = $array['Abfertigung'];
            }
            if (!empty($array['Pforte'])) {
                $Pforte =  "<span title='" . $array['Pforte'] . "'>QT</span>";
            }
            if (!empty($array['Autoquitt'])) {
                $Pforte =  "<span title='wurde durch Job automatisch quittiert'>Autoquitt</span>";
            }
            if (!empty($array['eingesteuert'])) {
                $eingesteuert =  $array['eingesteuert'];
            }
            if (!empty($array['Prozessname'])) {
                $eingesteuert =  $array['Prozessname'];
            }
            switch ($array['Zollgut']) {
                case "PASSIERT":
                    $array['Zollgut'] = "Entladeerlaubnis erteilt";
                    break;
            }
            echo '<tr class="small text-dark verlauf-row pointer">
                     <td class="p-2">' . $lf++ . '</td>
                     <td class="p-2">' . $array['rfnum'] . '</td>
                    <td class="p-2">' . $array['Anmeldung'] . '</td>
                    <td class="p-2">' . $array['Firma'] . '</td>
                    <td class="p-2">' . $array['Nummer'] . '</td>
                    <td class="p-2">' . $array['Ladung'] . '</td>
                    <td class="p-2">' . $link . '</td>
                    <td class="p-2">' . $array['Zollgut'] . '</td>
                    <td class="p-2">' . $array['Gefahrgut'] . '</td>
                    <td class="p-2">' . $eingesteuert . '</td>
                    <td class="p-2">' . $array['Einfahrt'] . '</td>
                    <td class="p-2">' . $array['Platz'] . '</td>
                    <td class="p-2">' . $array['Abfertigung'] . '</td>
                    <td class="p-2">' . $protokoll_WA->signed . '</td>
                    <td class="p-2">';
            $array['gone'] ? $gone = date("d.m.y, H:i", $array['gone']) : $gone = null;
            echo $gone;
            echo '</td>';
            echo '<td class="p-2 text-center">' . $Pforte . '</td>';
            echo '<td class="p-2 text-center">' . $array['Status'] . '</td>
                </tr>';
        endforeach;
        echo '</tbody>
        </table>
    </div>
    ';
    }
}