$(document).ready(function () {
  var scheme_id = $("#scheme_id").val();
  var error_contact = 0; // Initialize error_contact here

  $("#btn_contact_details").click(function () {
    // Reset the error_contact flag before validating
    error_contact = 0;

    // Validate each field
    if ($.trim($("#district").val()).length == 0) {
      error_contact = 1;
      $("#error_district").text("District is required");
      $("#district").addClass("has-error");
    } else {
      $("#error_district").text("");
      $("#district").removeClass("has-error");
    }

    if ($.trim($("#asmb_cons").val()).length == 0) {
      error_contact = 1;
      $("#error_asmb_cons").text("Assembly Constitution is required");
      $(".js-assembly").addClass("has-error");
    } else {
      $("#error_asmb_cons").text("");
      $(".js-assembly").removeClass("has-error");
    }

    if ($.trim($("#urban_code").val()).length == 0) {
      error_contact = 1;
      $("#error_urban_code").text("Rural/Urban is required");
      $("#urban_code").addClass("has-error");
    } else {
      $("#error_urban_code").text("");
      $("#urban_code").removeClass("has-error");
    }

    if ($.trim($("#block").val()).length == 0) {
      error_contact = 1;
      $("#error_block").text("Block/Municipality is required");
      $("#block").addClass("has-error");
    } else {
      $("#error_block").text("");
      $("#block").removeClass("has-error");
    }

    if ($.trim($("#gp_ward").val()).length == 0) {
      error_contact = 1;
      $("#error_gp_ward").text("GP/Ward No. is required");
      $("#gp_ward").addClass("has-error");
    } else {
      $("#error_gp_ward").text("");
      $("#gp_ward").removeClass("has-error");
    }

    if ($.trim($("#village").val()).length == 0) {
      error_contact = 1;
      $("#error_village").text("Village/Town/City is required");
      $("#village").addClass("has-error");
    } else {
      $("#error_village").text("");
      $("#village").removeClass("has-error");
    }

    if ($.trim($("#post_office").val()).length == 0) {
      error_contact = 1;
      $("#error_post_office").text("Post Office is required");
      $("#post_office").addClass("has-error");
    } else {
      $("#error_post_office").text("");
      $("#post_office").removeClass("has-error");
    }

    if ($.trim($("#pin_code").val()).length == 0) {
      error_contact = 1;
      $("#error_pin_code").text("Pin Code is required");
      $("#pin_code").addClass("has-error");
    } else {
      if ($.trim($("#pin_code").val()).length != 6) {
        error_contact = 1;
        $("#error_pin_code").text("Pin Code must be 6 digit");
        $("#pin_code").addClass("has-error");
      } else {
        $("#error_pin_code").text("");
        $("#pin_code").removeClass("has-error");
      }
    }

    if ($.trim($("#police_station").val()).length == 0) {
      error_contact = 1;
      $("#error_police_station").text("Police Station is required");
      $("#police_station").addClass("has-error");
    } else {
      $("#error_police_station").text("");
      $("#police_station").removeClass("has-error");
    }

    if ($.trim($("#residency_period").val()).length != 0) {
      if ($.trim($("#residency_period").val()) > 120) {
        error_contact = 1;
        $("#error_residency_period").text("Number of years is not properly");
        $("#residency_period").addClass("has-error");
      } else {
        $("#error_residency_period").text("");
        $("#residency_period").removeClass("has-error");
      }
    }

    if ($.trim($("#mobile_no").val()).length == 0) {
      error_contact = 1;
      $("#error_mobile_no").text("Mobile Number is required");
      $("#mobile_no").addClass("has-error");
    } else {
      if ($.trim($("#mobile_no").val()).length != 10) {
        error_contact = 1;
        $("#error_mobile_no").text("Mobile Number must be 10 digit");
        $("#mobile_no").addClass("has-error");
      } else {
        $("#error_mobile_no").text("");
        $("#mobile_no").removeClass("has-error");
      }
    }

    if ($.trim($("#email").val()).length == 0) {
      $("#error_email").text("");
      $("#email").removeClass("has-error");
    } else {
      if (
        /^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z.]{2,5}$/.exec(
          $.trim($("#email").val())
        ) == null
      ) {
        error_contact = 1;
        $("#error_email").text("Email Id is invalid");
        $("#email").addClass("has-error");
      } else {
        $("#error_email").text("");
        $("#email").removeClass("has-error");
      }
    }

    error_contact = 0; 

    if (error_contact == 1) {
      return false;
    } else {
      $("#contact_details").removeClass("active");
      $("#list_contact_details").addClass("inactive_tab1");
      $("#list_bank_details").removeClass("inactive_tab1");
      $("#list_bank_details").addClass("active_tab1 active");
      $("#list_bank_details").attr("href", "#bank_details");
      $("#list_bank_details").attr("data-toggle", "tab");
      $("#bank_details").addClass("active in");
    }
  });

  // Handle previous button
  $("#previous_btn_bank_details").click(function () {
    $("#list_bank_details").removeClass("active active_tab1");
    $("#list_bank_details").removeAttr("href data-toggle");
    $("#bank_details").removeClass("active in");
    $("#list_bank_details").addClass("inactive_tab1");
    $("#list_contact_details").removeClass("inactive_tab1");
    $("#list_contact_details").addClass("active_tab1 active");
    $("#list_contact_details").attr("href", "#contact_details");
    $("#list_contact_details").attr("data-toggle", "tab");
    $("#contact_details").addClass("active in");
  });

  if (scheme_id == 17) {
    $("#district_cur").change(function () {
      select_district_code = $("#district_cur").val();
      var htmlOption = '<option value="">--Select--</option>';
      $.each(assemblies, function (key, value) {
        if (value.district_code == select_district_code) {
          htmlOption +=
            '<option value="' + value.id + '">' + value.text + "</option>";
        }
      });
      $("#asmb_cons_cur").html(htmlOption);
    });

    $("#urban_code_cur").change(function () {
      //alert('ok');
      select_district_code = $("#district_cur").val();
      select_body_type = $("#urban_code_cur").val();
      var htmlOption = '<option value="">--Select--</option>';
      if (select_body_type == 2) {
        $.each(blocks, function (key, value) {
          if (value.district_code == select_district_code) {
            htmlOption +=
              '<option value="' + value.id + '">' + value.text + "</option>";
          }
        });
      } else if (select_body_type == 1) {
        $.each(ulbs, function (key, value) {
          if (value.district_code == select_district_code) {
            htmlOption +=
              '<option value="' + value.id + '">' + value.text + "</option>";
          }
        });
      }

      $("#block_cur").html(htmlOption);
    });

    $("#block_cur").change(function () {
      select_district_code = $("#district_cur").val();
      select_body_type = $("#urban_code_cur").val();
      selected_body_code = $("#block_cur").val();
      var htmlOption = '<option value="">--Select--</option>';
      if (select_body_type == 2) {
        $.each(gps, function (key, value) {
          if (
            value.district_code == select_district_code &&
            value.block_code == selected_body_code
          ) {
            htmlOption +=
              '<option value="' + value.id + '">' + value.text + "</option>";
          }
        });
      } else if (select_body_type == 1) {
        $.each(ulb_wards, function (key, value) {
          if (value.urban_body_code == selected_body_code) {
            htmlOption +=
              '<option value="' + value.id + '">' + value.text + "</option>";
          }
        });
      }
      $("#gp_ward_cur").html(htmlOption);
    });

    
    $("#cur_per_same").change(function () {
      var isChecked = $("#cur_per_same").is(":checked");
      if (isChecked) {
        $("#district_cur").attr("disabled", false);
        $("#asmb_cons_cur").attr("disabled", false);
        $("#urban_code_cur").attr("disabled", false);
        $("#block_cur").attr("disabled", false);
        $("#gp_ward_cur").attr("disabled", false);
        $("#village_cur").attr("readonly", false);
        $("#house_cur").attr("readonly", false);
        $("#post_office_cur").attr("readonly", false);
        $("#pin_code_cur").attr("readonly", false);
        $("#police_station_cur").attr("readonly", false);

        $("#district_cur").val($("#district").val());
        $("#district_cur").attr("disabled", true);

        $("#asmb_cons_cur").empty();
        $("#asmb_cons").find("option").clone().appendTo("#asmb_cons_cur");
        $("#asmb_cons_cur").val($("#asmb_cons").val());
        $("#asmb_cons_cur").attr("disabled", true);

        $("#urban_code_cur").val($("#urban_code").val());
        $("#urban_code_cur").attr("disabled", true);

        $("#block_cur").empty();
        $("#block").find("option").clone().appendTo("#block_cur");
        $("#block_cur").val($("#block").val());
        $("#block_cur").attr("disabled", true);

        $("#gp_ward_cur").empty();
        $("#gp_ward").find("option").clone().appendTo("#gp_ward_cur");
        $("#gp_ward_cur").val($("#gp_ward").val());
        $("#gp_ward_cur").attr("disabled", true);

        $("#village_cur").val($("#village").val());
        $("#village_cur").attr("readonly", true);
        $("#house_cur").val($("#house").val());
        $("#house_cur").attr("readonly", true);
        $("#post_office_cur").val($("#post_office").val());
        $("#post_office_cur").attr("readonly", true);
        $("#pin_code_cur").val($("#pin_code").val());
        $("#pin_code_cur").attr("readonly", true);
        $("#police_station_cur").val($("#police_station").val());
        $("#police_station_cur").attr("readonly", true);
      } else {
        $("#district_cur").attr("disabled", false);
        $("#asmb_cons_cur").attr("disabled", false);
        $("#urban_code_cur").attr("disabled", false);
        $("#block_cur").attr("disabled", false);
        $("#gp_ward_cur").attr("disabled", false);
        $("#village_cur").attr("readonly", false);
        $("#house_cur").attr("readonly", false);
        $("#post_office_cur").attr("readonly", false);
        $("#pin_code_cur").attr("readonly", false);
        $("#police_station_cur").attr("readonly", false);

        $("#district_cur").val("");
        $("#asmb_cons_cur").val("");
        $("#urban_code_cur").val("");
        $("#block_cur").val("");
        $("#gp_ward_cur").val("");
        $("#village_cur").val("");
        $("#house_cur").val("");
        $("#post_office_cur").val("");
        $("#pin_code_cur").val("");
        $("#police_station_cur").val("");
      }
    });
  }
});
