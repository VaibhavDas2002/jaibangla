$(document).ready(function () {
  var scheme_id = $("#scheme_id").val();
  var error_bank = 0;
  $("#btn_bank_details").click(function () {
    if ($.trim($("#name_of_bank").val()).length == 0) {
      error_bank = 1;
      $("#error_name_of_bank").text("Name of Bank is required");
      $("#name_of_bank").addClass("has-error");
    } else {
      $("#error_name_of_bank").text("");
      $("#name_of_bank").removeClass("has-error");
    }

    if ($.trim($("#bank_branch").val()).length == 0) {
      error_bank = 1;
      $("#error_bank_branch").text("Bank Branch is required");
      $("#bank_branch").addClass("has-error");
    } else {
      $("#error_bank_branch").text("");
      $("#bank_branch").removeClass("has-error");
    }

    if ($.trim($("#bank_account_number").val()).length == 0) {
      error_bank = 1;
      $("#error_bank_account_number").text("Bank Account Number is required");
      $("#bank_account_number").addClass("has-error");
    } else {
      $("#error_bank_account_number").text("");
      $("#bank_account_number").removeClass("has-error");
    }

    if ($.trim($("#bank_ifsc_code").val()).length == 0) {
      error_bank = 1;
      $("#error_bank_ifsc_code").text("IFS Code is required");
      $("#bank_ifsc_code").addClass("has-error");
    } else {
      $("#error_bank_ifsc_code").text("");
      $("#bank_ifsc_code").removeClass("has-error");
    }

    $ifsc_data = $.trim($("#bank_ifsc_code").val());
    $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
    if ($ifscRGEX.test($ifsc_data)) {
      $("#error_bank_ifsc_code").text("");
      $("#bank_ifsc_code").removeClass("has-error");
    } else {
      error_bank = 1;
      $("#error_bank_ifsc_code").text("Please check IFS Code format");
      $("#bank_ifsc_code").addClass("has-error");
    }

    // error_bank = 0;

    if (error_bank == 1) {
      return false;
      error_bank = 0;
    } else {
      $("#list_bank_details").removeClass("active active_tab1");
      $("#list_bank_details").removeAttr("href data-toggle");
      $("#bank_details").removeClass("active");
      $("#list_bank_details").addClass("inactive_tab1");
      if (scheme_id == 17) {
        $("#list_land_details_p").removeClass("inactive_tab1");
        $("#list_land_details_p").addClass("active_tab1 active");
        $("#list_land_details_p").attr("href", "#bank_details");
        $("#list_land_details_p").attr("data-toggle", "tab");
        $("#land_details_p").addClass("active in");
      } else if (scheme_id == 13) {
        $("#list_land_details").removeClass("inactive_tab1");
        $("#list_land_details").addClass("active_tab1 active");
        $("#list_land_details").attr("href", "#experience_details");
        $("#list_land_details").attr("data-toggle", "tab");
        $("#land_details").addClass("active in");
      } else {
        $("#list_experience_details").removeClass("inactive_tab1");
        $("#list_experience_details").addClass("active_tab1 active");
        $("#list_experience_details").attr("href", "#experience_details");
        $("#list_experience_details").attr("data-toggle", "tab");
        $("#experience_details").addClass("active in");
      }
    }
  });

  $("#previous_btn_experience_details").click(function () {
    $("#list_experience_details").removeClass("active active_tab1");
    $("#list_experience_details").removeAttr("href data-toggle");
    $("#experience_details").removeClass("active in");
    $("#list_experience_details").addClass("inactive_tab1");
    if (scheme_id == 17) {
      $("#list_land_details_p").removeClass("inactive_tab1");
      $("#list_land_details_p").addClass("active_tab1 active");
      $("#list_land_details_p").attr("href", "#land_details_p");
      $("#list_land_details_p").attr("data-toggle", "tab");
      $("#land_details_p").addClass("active in");
    } else if (scheme_id == 13) {
      $("#list_fm_details").removeClass("inactive_tab1");
      $("#list_fm_details").addClass("active_tab1 active");
      $("#list_fm_details").attr("href", "#bank_details");
      $("#list_fm_details").attr("data-toggle", "tab");
      $("#fm_details").addClass("active in");
    } else {
    $("#list_bank_details").removeClass("inactive_tab1");
    $("#list_bank_details").addClass("active_tab1 active");
    $("#list_bank_details").attr("href", "#bank_details");
    $("#list_bank_details").attr("data-toggle", "tab");
    $("#bank_details").addClass("active in");
    }
  });
});
