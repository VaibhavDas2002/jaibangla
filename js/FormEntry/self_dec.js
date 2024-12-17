/***************************SD*********************************/
$(document).ready(function () {
  var scheme_id = $('#scheme_id').val();
  $("#btn_submit_preview").click(function () {
    $(".modal-submit").show();
    $("#submitting").hide();
    $("#submit_loader").hide();
    $("#confirm-submit").modal("show");
  });
});
