<div class="modal fade" id="targetPlace" tabindex="-1" aria-labelledby="targetPlaceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 p-2">
                <h5 class="modal-title" id="targetPlaceModalLabel"><i class="fa fa-warning fa-fw"></i> Abladestelle
                    zuweisen</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="class/action.php">
                <div class="modal-body" id="modal-places">
                    <div class="table-responsive">
                        <table class="table table-hover" id="table-select-place">
                            <?php  foreach($places as $place):?>
                            <tr>
                                <td class="ps-3 pt-1 pb-1">
                                    <div class="form-check form-check-flat">
                                        <label class="form-check-label"><input class="radio" type="radio" name="Platz"
                                                value="<?=$place['Platz']?>" required>
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </td>
                                <td class="pt-1 pb-1 align-middle"><?=$place['Platz']?></td>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </table>
                    </div>
                </div>
                <div class="row p-2">
                    <div class="col-6 pt-2 text-left"></div>
                    <div class="col-6 pt-2 text-right">
                        <input type="hidden" name="add_to_prozess" value="1">
                        <input type="hidden" name="rfnum" id="set-place-for-rfnum">
                        <button type="submit" class="btn btn-primary" id="change-position-status">bestätigen</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>