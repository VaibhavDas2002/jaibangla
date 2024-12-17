$(document).ready(function () {
  var scheme_id = $("#scheme_id").val();
  var error_personal = 0;
  if (scheme_id == 5 || scheme_id == 6 || scheme_id == 7 || scheme_id == 17) {
    $("#caste_certificate_no_section").hide(); // Hide the section if condition is met
  } else {
    $("#caste_certificate_no_section").show(); // Show the section otherwise
  }

  var entry_type = $("#entry_type").val();
  if (entry_type == "Form through Duare Sarkar camp") {
    $(".duareSarkar").show();
  } else {
    $(".duareSarkar").hide();
  }

  var marital_status = $("#marital_status").val();
  if (marital_status == "Married") {
    $("#spouse_section").show();
  } else {
    $("#spouse_section").hide();
  }

  $("#entry_type").on("change", function () {
    var entry_type = $(this).val(); // Use `this` for better context and readability
    if (entry_type === "Form through Duare Sarkar camp") {
      $(".duareSarkar").show();
    } else {
      $(".duareSarkar").hide();
    }
  });
  $("#spouse_section").hide();
  $("#marital_status").on("change", function () {
    var marital_status = $("#marital_status").val();
    if (marital_status == "Married") {
      $("#spouse_section").show();
    } else {
      $("#spouse_section").hide();
    }
  });

  // console.log(error_personal);

  $("#dob").on("blur", function () {
    var today = new Date();
    var birthDate = new Date($("#dob").val());

    var diff_ms = today.getTime() - birthDate.getTime();
    var age_dt = new Date(diff_ms);
    var age = Math.ceil(age_dt.getUTCFullYear() - 1970);

    if (isNaN(age)) {
      age = 0;
    }
    $("#hidden_age").val(age);
    $("#txt_age").val(age);
  });

  $("#btn_personal_details").click(function () {
    if ($.trim($("#entry_type").val()).length == 0) {
      error_personal = 1;
      $("#error_entry_type").text("Please Select Application Type");
      $("#entry_type").addClass("has-error");
    } else {
      $("#error_entry_type").text("");
      $("#entry_type").removeClass("has-error");
    }
    if ($("#entry_type").val() == "Form through Duare Sarkar camp") {
      // console.log('ds');
      if ($.trim($("#ds_registration_no").val()).length == 0) {
        error_personal = 1;
        $("#error_ds_registration_no").text(
          "Duare Sarkar Registration no. is required"
        );
        $("#ds_registration_no").addClass("has-error");
      } else {
        $("#error_ds_registration_no").text("");
        $("#ds_registration_no").removeClass("has-error");
      }
      if ($.trim($("#ds_date").val()).length == 0) {
        error_personal = 1;
        $("#error_ds_date").text("Duare Sarkar Date is required");
        $("#ds_date").addClass("has-error");
      } else {
        $("#error_ds_date").text("");
        $("#ds_date").removeClass("has-error");
      }
    }

    if ($.trim($("#first_name").val()).length == 0) {
      error_personal = 1;
      $("#error_first_name").text("First Name is required");
      $("#first_name").addClass("has-error");
    } else {
      $("#error_first_name").text("");
      $("#first_name").removeClass("has-error");
    }

    if ($.trim($("#last_name").val()).length == 0) {
      error_personal = 1;
      $("#error_last_name").text("Last Name is required");
      $("#last_name").addClass("has-error");
    } else {
      $("#error_last_name").text("");
      $("#last_name").removeClass("has-error");
    }

    if ($.trim($("#gender").val()).length == 0) {
      error_personal = 1;
      $("#error_gender").text("Gender is required");
      $("#gender").addClass("has-error");
    } else {
      $("#error_gender").text("");
      $("#gender").removeClass("has-error");
    }
    if (scheme_id == 11) {
      if ($("#gender").val() != "Female") {
        error_personal = 1;
        $("#error_gender").text(" Gender should be Female");
        $("#gender").addClass("has-error");
      } else {
        $("#error_gender").text("");
        $("#gender").removeClass("has-error");
      }
    }

    if ($.trim($("#dob").val()).length > 0) {
      var string = $.trim($("#dob").val());
      var result = string.split("-");
      var year = result[result.length - 3];
      if (year < 1900 || year > 2000) {
        error_personal = 1;
        $("#error_dob").text("Date of Birth range is not properly");
        $("#dob").addClass("has-error");
      } else {
        $("#error_dob").text("");
        $("#dob").removeClass("has-error");
      }
    }

    if ($.trim($("#dob").val()).length == 0) {
      error_personal = 1;
      $("#error_dob").text("Date of Birth is Required");
      $("#dob").addClass("has-error");
    } else {
      $("#error_dob").text("");
      $("#dob").removeClass("has-error");
    }

    if ($.trim($("#txt_age").val()).length == 0) {
      error_personal = 1;
      $("#error_txt_age").text("Age is required");
      $("#txt_age").addClass("has-error");
    } else {
      if (
        $.trim($("#txt_age").val()) < 60 ||
        $.trim($("#txt_age").val()) > 120
      ) {
        error_personal = 1;
        $("#error_txt_age").text("Age range is not properly");
        $("#txt_age").addClass("has-error");
        return false;
      } else {
        $("#error_txt_age").text("");
        $("#txt_age").removeClass("has-error");
      }

      if ($.trim($("#dob").val()).length > 0) {
        if ($("#hidden_age").val() != $("#txt_age").val()) {
          error_personal = 1;
          $("#error_txt_age").text(
            "Age should be equal according to date of birth"
          );
          $("#txt_age").addClass("has-error");
        }
      }
    }
    if ($.trim($("#father_first_name").val()).length == 0) {
      error_personal = 1;
      $("#error_father_first_name").text("First Name is required");
      $("#father_first_name").addClass("has-error");
    } else {
      $("#error_father_first_name").text("");
      $("#father_first_name").removeClass("has-error");
    }

    if ($.trim($("#father_last_name").val()).length == 0) {
      error_personal = 1;
      $("#error_father_last_name").text("Last Name is required");
      $("#father_last_name").addClass("has-error");
    } else {
      $("#error_father_last_name").text("");
      $("#father_last_name").removeClass("has-error");
    }

    if ($.trim($("#mother_first_name").val()).length == 0) {
      error_personal = 1;
      $("#error_mother_first_name").text("First Name is required");
      $("#mother_first_name").addClass("has-error");
    } else {
      $("#error_mother_first_name").text("");
      $("#mother_first_name").removeClass("has-error");
    }

    if ($.trim($("#mother_last_name").val()).length == 0) {
      error_personal = 1;
      $("#error_mother_last_name").text("Last Name is required");
      $("#mother_last_name").addClass("has-error");
    } else {
      $("#error_mother_last_name").text("");
      $("#mother_last_name").removeClass("has-error");
    }

    if ($.trim($("#caste_category").val()).length == 0) {
      error_personal = 1;
      $("#error_caste_category").text("Caste is required");
      $("#caste_category").addClass("has-error");
    } else {
      $("#error_caste_category").text("");
      $("#caste_category").removeClass("has-error");
    }

    $("#caste_category").change(function () {
      if (
        $("#caste_category").val() === "SC" ||
        $("#caste_category").val() === "ST"
      ) {
        if ($.trim($("#caste_certificate_no").val()).length == 0) {
          error_personal = 1;
          $("#error_caste_certificate_no").text(
            "Caste Certificate is required"
          );
          $("#caste_certificate_no").addClass("has-error");
        } else {
          $("#error_caste_certificate_no").text("");
          $("#caste_certificate_no").removeClass("has-error");
        }
      } else {
        $("#error_caste_certificate_no").text("");
        $("#caste_certificate_no").removeClass("has-error");
      }
    });

    if ($.trim($("#marital_status").val()).length == 0) {
      error_personal = 1;
      $("#error_marital_status").text("Marital Status is required");
      $("#marital_status").addClass("has-error");
    } else {
      $("#error_marital_status").text("");
      $("#marital_status").removeClass("has-error");
    }

    if ($.trim($("#monthly_income").val()).length == 0) {
      error_personal = 1;
      $("#error_monthly_income").text("Monthly Family Income is required");
      $("#monthly_income").addClass("has-error");
    } else {
      $("#error_monthly_income").text("");
      $("#monthly_income").removeClass("has-error");
    }
    if (scheme_id == 17) {
      if ($.trim($("#app_phase").val()).length == 0) {
        error_personal = 1;
        $("#error_app_phase").text("Application Phase is required");
        $("#app_phase").addClass("has-error");
      } else {
        $("#error_app_phase").text("");
        $("#app_phase").removeClass("has-error");
      }

      if ($.trim($("#temple_type").val()).length == 0) {
        error_personal = 1;
        $("#error_temple_type").text("Temple Type is required");
        $("#temple_type").addClass("has-error");
      } else {
        $("#error_temple_type").text("");
        $("#temple_type").removeClass("has-error");
      }
    }
    if (scheme_id == 2) {
      if ($.trim($("#disablity_type").val()).length == 0) {
        error_personal = 1;
        $("#error_disablity_type").text("Disability Type is required");
        $("#disablity_type").addClass("has-error");
      } else {
        $("#error_disablity_type").text("");
        $("#disablity_type").removeClass("has-error");
      }

      if ($.trim($("#disablity_type_percentage").val()).length == 0) {
        error_personal = 1;
        $("#error_disablity_type_percentage").text(
          "Disability Type Percentage is required"
        );
        $("#disablity_type_percentage").addClass("has-error");
      } else {
        var val = $("#disablity_type_percentage").val();
        var regex = /^((0|[1-9]\d?)(\.\d{1,2})?|100(\.00?)?)$/;

        if (!val.match(regex)) {
          error_personal = 1;
          $("#error_disablity_type_percentage").text(
            "Disability Type Percentage must be a valid number between 0 and 100"
          );
          $("#disablity_type_percentage").addClass("has-error");
        } else if (parseFloat(val) < 40) {
          error_personal = 1;
          $("#error_disablity_type_percentage").text(
            "Disability Percentage should be >= 40"
          );
          $("#disablity_type_percentage").addClass("has-error");
        } else {
          $("#error_disablity_type_percentage").text("");
          $("#disablity_type_percentage").removeClass("has-error");
        }
      }

      if ($.trim($("#disablity_type_authority").val()).length == 0) {
        error_personal = 1;
        $("#error_disablity_type_authority").text(
          "Certifying Authority is required"
        );
        $("#disablity_type_authority").addClass("has-error");
      } else {
        $("#error_disablity_type_authority").text("");
        $("#disablity_type_authority").removeClass("has-error");
      }

      if ($.trim($("#disability_designation").val()).length == 0) {
        error_personal = 1;
        $("#error_disability_designation").text("Designation name is required");
        $("#disability_designation").addClass("has-error");
      } else {
        $("#error_disability_designation").text("");
        $("#disability_designation").removeClass("has-error");
      }
    }

    if (scheme_id == 5) {
      if ($.trim($("#phy_hadi_status").val()) == "Yes") {
        if (
          $.trim($("#txt_age").val()) < 55 ||
          $.trim($("#txt_age").val()) > 120
        ) {
          error_personal = 1;
          $("#error_txt_age").text("Age range is not properly");
          $("#txt_age").addClass("has-error");
          return false;
        } else {
          $("#error_txt_age").text("");
          $("#txt_age").removeClass("has-error");
        }
      } else {
        if (
          $.trim($("#txt_age").val()) < 60 ||
          $.trim($("#txt_age").val()) > 120
        ) {
          error_personal = 1;
          $("#error_txt_age").text("Age range is not properly");
          $("#txt_age").addClass("has-error");
          return false;
        } else {
          $("#error_txt_age").text("");
          $("#txt_age").removeClass("has-error");
        }
      }
    }

    if (scheme_id == 11) {
      if ($.trim($("#husband_first_name").val()).length == 0) {
        error_personal = 1;
        $("#error_husband_first_name").text("Husband's First Name is required");
        $("#husband_first_name").addClass("has-error");
      } else {
        $("#error_husband_first_name").text("");
        $("#husband_first_name").removeClass("has-error");
      }

      if ($.trim($("#husband_last_name").val()).length == 0) {
        error_personal = 1;
        $("#error_husband_last_name").text("Husband's Last Name is required");
        $("#husband_last_name").addClass("has-error");
      } else {
        $("#error_husband_last_name").text("");
        $("#husband_last_name").removeClass("has-error");
      }
    }
    if (scheme_id == 1 || scheme_id == 3) {
      if ($.trim($("#caste_certificate_no").val()).length == 0) {
        error_personal = 1;
        $("#error_caste_certificate_no").text(
          "Caste Certificate No is required"
        );
        $("#caste_certificate_no").addClass("has-error");
      } else {
        $("#error_caste_certificate_no").text("");
        $("#caste_certificate_no").removeClass("has-error");
      }
    }

    error_personal = 0;
    if (error_personal == 1) {
      error_personal = 0;
      return false;
    } else {
      /*******SD**********/
      $("#list_personal_details").removeClass("active active_tab1");
      $("#list_personal_details").removeAttr("href data-toggle");
      $("#personal_details").removeClass("active");
      $("#list_personal_details").addClass("inactive_tab1");
      $("#list_id_details").removeClass("inactive_tab1");
      $("#list_id_details").addClass("active_tab1 active");
      $("#list_id_details").attr("href", "#id_details");
      $("#list_id_details").attr("data-toggle", "tab");
      $("#id_details").addClass("active in");
      /*******************/
    }
  });
  $("#previous_btn_id_details").click(function () {
    $("#list_id_details").removeClass("active active_tab1");
    $("#list_id_details").removeAttr("href data-toggle");
    $("#id_details").removeClass("active in");
    $("#list_id_details").addClass("inactive_tab1");
    $("#list_personal_details").removeClass("inactive_tab1");
    $("#list_personal_details").addClass("active_tab1 active");
    $("#list_personal_details").attr("href", "#personal_details");
    $("#list_personal_details").attr("data-toggle", "tab");
    $("#personal_details").addClass("active in");
  });
});
