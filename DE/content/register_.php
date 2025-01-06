<div class="col-12 col-md-6 mb-5 d-none border-left border-right" id="register-form">
                        <h4>Registrieren</h4>
                                <hr>
                            <div class="row">
                                <div class="col-md-6">
                                        <?php if(isset($_REQUEST['addnewfrz'])): 
                                            echo $o->alerts("erfolgreich hinzugefügt");?>
                                        <?php endif;?>
                                        <h5 class="small font-weight-bold">Neues Lieferfahrzeug</h5>
                                        <form action="class/action.php" method="POST">
                                            <div class="mb-1">
                                                <div class="form-text small">Firma / Lieferant*</div>
                                              <input type="search" class="form-control" id="firma" name="firma" required>
                                            </div>
                                            <div class="mb-1">
                                                <div  class="form-text small">Autokennzeichen*</div>
                                              <input type="search" class="form-control" id="knznummer" name="knznummer" required>
                                            </div>
                                            <div class="mb-1">
                                                <div  class="form-text small">Farzeugtyp*</div>
                                                <select type="password" class="form-control" id="frztyp" name="frztyp" required>
                                                    <option value="">FRZ-Typ</option>
                                                    <option value="7,5 Tonn">3,5 Tonner</option>
                                                    <option value="7,5 Tonn">7,5 Tonner</option>
                                                    <option value="40 Tonn">40 Tonner</option>
                                                    <option value="Sprinter">Sprinter</option>
                                                </select>
                                              </div>
                                             
                                              <div class="mt-3">
                                                <input type="hidden" name="lftnid" value="<?=$lfrid?>">
                                                <input type="hidden" name="return_uri" value="wea">
                                                <input type="hidden" name="add_lieferant" value="mobile">
                                                <button type="submit" class="btn btn-primary">eintragen</button>
                                              </div>
                                          </form>
                                    </div>
                                </div>
                            </div>