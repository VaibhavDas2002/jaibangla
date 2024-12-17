$(document).ready(function () {
  var scheme_id = $("#scheme_id").val();
  if (scheme_id == 17) {
    $("#btn_land_details").click(function () {
      $("#list_land_details_p").removeClass("active active_tab1");
      $("#list_land_details_p").removeAttr("href data-toggle");
      $("#land_details_p").removeClass("active");
      $("#list_land_details_p").addClass("inactive_tab1");

      $("#list_experience_details").removeClass("inactive_tab1");
      $("#list_experience_details").addClass("active_tab1 active");
      $("#list_experience_details").attr("href", "#bank_details");
      $("#list_experience_details").attr("data-toggle", "tab");
      $("#experience_details").addClass("active in");
    });
    $("#previous_btn_land_details").click(function () {
      $("#list_land_details_p").removeClass("active active_tab1");
      $("#list_land_details_p").removeAttr("href data-toggle");
      $("#land_details_p").removeClass("active in");
      $("#list_land_details_p").addClass("inactive_tab1");
      $("#list_bank_details").removeClass("inactive_tab1");
      $("#list_bank_details").addClass("active_tab1 active");
      $("#list_bank_details").attr("href", "#contact_details");
      $("#list_bank_details").attr("data-toggle", "tab");
      $("#bank_details").addClass("active in");
    });
  }
});
