$(document).ready(function () {
  var scheme_id = $("#scheme_id").val();
  $("#btn_experience_details").click(function () {
    var file_size = 2097152;
    var image_mime = ["image/jpg", "image/jpeg", "image/png", "image/gif"];
    var image_pdf_mime = [
      "image/jpg",
      "image/jpeg",
      "image/png",
      "image/gif",
      "application/pdf",
    ];

    $("#list_experience_details").removeClass("active active_tab1");
    $("#list_experience_details").removeAttr("href data-toggle");
    $("#experience_details").removeClass("active");
    $("#list_experience_details").addClass("inactive_tab1");

    $("#list_decl_details").removeClass("inactive_tab1");
    $("#list_decl_details").addClass("active_tab1 active");
    $("#list_decl_details").attr("href", "#decl_details");
    $("#list_decl_details").attr("data-toggle", "tab");
    $("#decl_details").addClass("active in");
    //}
  });

  $("#previous_btn_decl_details").click(function () {
    $("#list_decl_details").removeClass("active active_tab1");
    $("#list_decl_details").removeAttr("href data-toggle");
    $("#decl_details").removeClass("active in");
    $("#list_decl_details").addClass("inactive_tab1");
    
      $("#list_experience_details").removeClass("inactive_tab1");
      $("#list_experience_details").addClass("active_tab1 active");
      $("#list_experience_details").attr("href", "#experience_details");
      $("#list_experience_details").attr("data-toggle", "tab");
      $("#experience_details").addClass("active in");
  });
});
