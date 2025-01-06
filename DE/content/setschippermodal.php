  <?php 
  require_once 'class/Controller.php';
  $controller = new Controller();
  $bmis = $controller->getBMIData();
  //$online = $controller->getOnlineData();

  ?>
  <div class="modal fade" id="setSchipperModal" tabindex="-1" aria-labelledby="setSchipperModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-defaultsize">
          <div class="modal-content">
              <div class="modal-header border-0">
                  <h5 class="modal-title h4" id="exampleModalSmLabel">Staplerfahrer zuweisen</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="post" action="class/action.php">

                  <div class="modal-body">
                      <div class="overflow-auto">
                          <table class="table table-hover customized">
                              <tr>
                                  <th></th>
                                  <th>Typ</th>
                                  <th>Herst.</th>
                                  <th>Gew.</th>
                                  <th>INV</th>
                                  <th>BMI</th>
                                  <th>Beschreibung</th>
                                  <th>Bild</th>
                              </tr>
                              <tbody>
                                  <?php 
        
       if(!empty($bmis)):
          foreach($bmis as $bmi):
            $expl = explode("&",$bmi['Plant']);
            $inummer = $expl[2];
            if($_SESSION['INUMMER']==$inummer):
              $styleRow =""; 
                // if(in_array($bmi['BMI-Nummer'], $online)){
                //   $styleRow = "style='background:lightgreen' title='Stapler angemeldet'";
                // }
                ?>
                                  <tr <?=$styleRow?>>
                                      <td>
                                          <div class="form-check form-check-flat">
                                              <label class="form-check-label">
                                                  <input class="radio" type="radio" name="set_for_unload"
                                                      value="<?=$bmi['BMI-Nummer']?>" required>
                                                  <i class="input-helper"></i>
                                              </label>
                                          </div>
                                      </td>
                                      <td class="align-middle p-1"><?=$bmi['BMI-Typ']?></td>
                                      <td class="align-middle p-1"><?=$bmi['Hersteller']?></td>
                                      <td class="align-middle p-1"><?=$bmi['Gewicht']?></td>
                                      <td class="align-middle p-1"><?=$bmi['INV-Nummer']?></td>
                                      <td class="align-middle p-1"><?=$bmi['BMI-Nummer']?></td>
                                      <td class="align-middle p-1"><?=$bmi['Beschreibung']?></td>
                                      <td class="align-middle p-1">
                                          <?php if(!empty($bmi['BMI-Bild'])):?>
                                          <img class="img-thumbnail pointer picturehover-show"
                                              title="<?=$bmi['BMI-Typ']?> <?=$bmi['Hersteller']?> <?=$bmi['BMI-Nummer']?>"
                                              alt="db/<?=$_SESSION['werknummer']."/bmi/".$bmi['BMI-Bild']?>"
                                              data-bs-toggle="modal" data-bs-target="#pictureviwever"
                                              src="db/<?=$_SESSION['werknummer']."/bmi/TN".$bmi['BMI-Bild']?>">
                                      </td>
                                      <?php endif;?>
                                      </td>
                                  </tr>
                                  <?php endif;?>
                                  <?php endforeach;?>
                                  <?php endif;?>

                              </tbody>
                          </table>
                      </div>
                  </div>
                  <div class="card-footer border-0">
                      <button type="submit" class="btn btn-dark" id="submit-button">Stapler für Abladen
                          zuweisen</button>
                  </div>
          </div>
          </form>
      </div>
  </div>
  </div>