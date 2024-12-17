$(document).ready(function () {
  var scheme_id = $("#scheme_id").val();
  var error_identification = 0;

  if (scheme_id == 2) {
    $("#aadhar_exits").change(function () {
      if ($.trim($("#aadhar_exits").val()).length == 0) {
        error_identification = 1;
        $("#error_aadhar_exits").text("This field is required");
        $("#aadhar_exits").addClass("has-error");
      } else {
        $("#error_aadhar_exits").text("");
        $("#aadhar_exits").removeClass("has-error");

        if ($("#aadhar_exits").val() == "1") {
          // User selected "Yes"
          if ($.trim($("#aadhar_no").val()).length == 0) {
            error_identification = 1;
            $("#error_aadhar_no").text("Aadhaar No. is required");
            $("#aadhar_no").addClass("has-error");
          } else {
            $(".aadhar-text, .aadhar-text-modal").show();

            if ($.trim($("#aadhar_no").val()).length != 12) {
              error_identification = 1;
              $("#error_aadhar_no").text("Aadhaar No should be 12 digits");
              $("#aadhar_no").addClass("has-error");
            } else {
              var aadhar_no = $("#aadhar_no").val();
              if (validate_adhar(aadhar_no)) {
                $("#error_aadhar_no").text("");
                $("#aadhar_no").removeClass("has-error");
              } else {
                error_identification = 1;
                $("#error_aadhar_no").text("Invalid Aadhaar No.");
                $("#aadhar_no").addClass("has-error");
              }
            }
          }
          $("#aadhar_div").show();
          $("#withoutaadhar_div, #withoutaadhar_cause_other_div").hide();
        } else {
          // User selected "No"
          $("#error_aadhar_no").text("");
          $("#aadhar_no").removeClass("has-error").val("");

          if ($.trim($("#withoutaadhar_cause").val()).length == 0) {
            error_identification = 1;
            $("#error_withoutaadhar_cause").text(
              "Please select a reason for not having Aadhaar"
            );
            $("#withoutaadhar_cause").addClass("has-error");
          } else {
            $("#error_withoutaadhar_cause").text("");
            $("#withoutaadhar_cause").removeClass("has-error");

            if ($("#withoutaadhar_cause").val() == "Others") {
              if ($.trim($("#withoutaadhar_cause_other").val()).length == 0) {
                error_identification = 1;
                $("#error_withoutaadhar_cause_other").text(
                  "Please specify the reason"
                );
                $("#withoutaadhar_cause_other").addClass("has-error");
              } else {
                $("#error_withoutaadhar_cause_other").text("");
                $("#withoutaadhar_cause_other").removeClass("has-error");
              }
              $("#withoutaadhar_cause_other_div").show();
            } else {
              $("#withoutaadhar_cause_other_div").hide();
            }
          }
          $("#aadhar_div").hide();
          $("#withoutaadhar_div").show();
        }
      }
    });
  }

  $("#btn_id_details").click(function () {
    if ([1, 13, 17].includes(parseInt(scheme_id))) {
      if ($.trim($("#ration_card_cat").val()).length == 0) {
        error_identification = 1;
        $("#error_ration_card_cat").text(
          "Digital Ration Card Category is required"
        );
        $("#ration_card_cat").addClass("has-error");
      } else {
        $("#error_ration_card_cat").text("");
        $("#ration_card_cat").removeClass("has-error");
      }

      if ($.trim($("#ration_card_no").val()).length == 0) {
        error_identification = 1;
        $("#error_ration_card_no").text("Digital Ration Card No. is required");
        $("#ration_card_no").addClass("has-error");
      } else if ($.trim($("#ration_card_no").val()).length > 10) {
        error_identification = 1;
        $("#error_ration_card_no").text(
          "Digital Ration Card No should not exceed 10 digits"
        );
        $("#ration_card_no").addClass("has-error");
      } else {
        $("#error_ration_card_no").text("");
        $("#ration_card_no").removeClass("has-error");
      }
    }

    if (scheme_id != 2) {
      if ($.trim($("#aadhar_no").val()).length != 0) {
        if ($.trim($("#aadhar_no").val()).length != 12) {
          error_identification = 1;
          $("#error_aadhar_no").text("Aadhaar No should be 12 digits");
          $("#aadhar_no").addClass("has-error");
        } else {
          var aadhar_no = $("#aadhar_no").val();
          if (validate_adhar(aadhar_no)) {
            $("#error_aadhar_no").text("");
            $("#aadhar_no").removeClass("has-error");
          } else {
            error_identification = 1;
            $("#error_aadhar_no").text("Invalid Aadhaar No.");
            $("#aadhar_no").addClass("has-error");
          }
        }
      } else {
        error_identification = 1;
        $("#error_aadhar_no").text("Aadhaar Number is required");
        $("#aadhar_no").addClass("has-error");
      }
    }

    if ([1, 3, 5, 10, 11, 13].includes(parseInt(scheme_id))) {
      if ($.trim($("#epic_voter_id").val()).length == 0) {
        error_identification = 1;
        $("#error_epic_voter_id").text("EPIC/Voter Id.No is required");
        $("#epic_voter_id").addClass("has-error");
      } else {
        $("#error_epic_voter_id").text("");
        $("#epic_voter_id").removeClass("has-error");
      }
    }

    if (error_identification == 1) {
      error_identification = 0;
      return false;
    } else {
      $("#list_id_details").removeClass("active active_tab1");
      $("#list_id_details").removeAttr("href data-toggle");
      $("#id_details").removeClass("active");
      $("#list_id_details").addClass("inactive_tab1");
      $("#list_contact_details").removeClass("inactive_tab1");
      $("#list_contact_details").addClass("active_tab1 active");
      $("#list_contact_details").attr("href", "#contact_details");
      $("#list_contact_details").attr("data-toggle", "tab");
      $("#contact_details").addClass("active in");
    }
  });

  $("#previous_btn_contact_details").click(function () {
    $("#list_contact_details").removeClass("active active_tab1");
    $("#list_contact_details").removeAttr("href data-toggle");
    $("#contact_details").removeClass("active in");
    $("#list_contact_details").addClass("inactive_tab1");

    $("#list_id_details").removeClass("inactive_tab1");
    $("#list_id_details").addClass("active_tab1 active");
    $("#list_id_details").attr("href", "#id_details");
    $("#list_id_details").attr("data-toggle", "tab");
    $("#id_details").addClass("active in");
  });
});
