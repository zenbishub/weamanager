function loadDGSVOConditions(){
    $("#show-dgsvo").click(function(){
        var body = $("#diverse-modal-body");
        body.load("content/dgsvo-conditions.php");
    });
}
$(function(){
    loadDGSVOConditions();
});