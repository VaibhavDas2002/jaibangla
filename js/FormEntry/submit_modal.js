$(document).ready(function () {
  var scheme_id = $("#scheme_id").val();
  $(".caste_certificate_no_section").hide();
  $("#btn_submit_preview").click(function () {
    ////////////////////////////////Personal Details////////////////////////////////
    $("#entry_type_modal").text($("#entry_type").val());
    if ($("#entry_type").val() == "Form through Duare Sarkar camp") {
      $("#ds_registration_no_modal").text($("#ds_registration_no").val());
      var duare_sarkar_date = $("#ds_date").val();
      var dArr = duare_sarkar_date.split("-");
      var today1 = dArr[2] + "/" + dArr[1] + "/" + dArr[0];
      $("#ds_date_modal").text(today1);
      $(".modalDuareSarkar").show();
    } else {
      $(".modalDuareSarkar").hide();
    }

    $("#name_modal").text(
      $("#first_name").val() +
        " " +
        $("#middle_name").val() +
        " " +
        $("#last_name").val()
    );
    $("#gender_modal").text($("#gender").val());
    $("#dob_modal").text($("#dob").val());
    $("#father_name_modal").text(
      $("#father_first_name").val() +
        " " +
        $("#father_middle_name").val() +
        " " +
        $("#father_last_name").val()
    );
    $("#mother_name_modal").text(
      $("#mother_first_name").val() +
        " " +
        $("#mother_middle_name").val() +
        " " +
        $("#mother_last_name").val()
    );

    $("#caste_category_modal").text($("#caste_category").val());

    // Check the value of #caste_category
    if (
      $("#caste_category").val() === "SC" ||
      $("#caste_category").val() === "ST"
    ) {
      $(".caste_certificate_no_section").show();
      $("#caste_certificate_no_modal").text($("#caste_certificate_no").val()); // Update the modal text
    } else {
      $(".caste_certificate_no_section").hide(); // Hide the caste certificate field
      $("#caste_certificate_no_modal").text(""); // Clear the modal text if hidden
    }

    $("#disablity_type_modal").text(
      $("#disablity_type option:selected").text()
    );
    $("#disablity_type_percentage_modal").text(
      $("#disablity_type_percentage").val()
    );
    $("#disablity_type_authority_modal").text(
      $("#disablity_type_authority").val()
    );
    $("#disability_designation_authority_modal").text(
      $("#disability_designation").val()
    );

    $("#marital_status_modal").text($("#marital_status").val());
    // $('#fisherman_comm_modal').text($('#fisherman_comm').val());
    $("#spouse_name_modal").text(
      $("#spouse_first_name").val() +
        " " +
        $("#spouse_middle_name").val() +
        " " +
        $("#spouse_last_name").val()
    );

    $("#monthly_income_modal").text($("#monthly_income").val());
    ////////////////////////////////Personal Identification////////////////////////////////
    $("#bpl_seq_no_modal").text($("#bpl_seq_no").val());
    $("#bpl_id_no_modal").text($("#bpl_id_no").val());
    $("#bpl_total_score_modal").text($("#bpl_total_score").val());

    $("#ration_card_no_modal").text(
      $("#ration_card_cat").val() + "-" + $("#ration_card_no").val()
    );
    $("#ahl_tin_modal").text($("#ahl_tin").val());

    if (scheme_id == 2) {
      $("#aadhar_exits_modal").text($("#aadhar_exits :selected").text());
      if ($("#aadhar_exits").val() == 1) {
        $("#withoutaadhar_div_modal").hide();
        $("#aadhar_exits_div_modal").show();
        $("#aadhar_no_modal").text($("#aadhar_no").val());
      } else {
        $("#aadhar_exits_div_modal").hide();
        var withoutaadhar_cause = $("#withoutaadhar_cause :selected").text();
        if (withoutaadhar_cause == "Others") {
          $("#withoutaadhar_cause_modal").text(
            $("#withoutaadhar_cause_other").val()
          );
        } else $("#withoutaadhar_cause_modal").text(withoutaadhar_cause);
        $("#withoutaadhar_div_modal").show();
      }
    } else {
      $("#aadhar_no_modal").text($("#aadhar_no").val());
    }
    ////////////////////////////////Contact Details////////////////////////////////
    $("#epic_voter_id_modal").text($("#epic_voter_id").val());
    $("#pan_no_modal").text($("#pan_no").val());

    $("#state_modal").text($("#state").val());
    $("#asmb_cons_modal").text($("#asmb_cons :selected").text());
    $("#district_modal").text($("#district :selected").text());
    $("#police_station_modal").text($("#police_station").val());
    $("#block_modal").text($("#block :selected").text());
    $("#gp_ward_modal").text($("#gp_ward :selected").text());
    $("#village_modal").text($("#village").val());
    $("#house_modal").text($("#house").val());
    $("#post_office_modal").text($("#post_office").val());
    $("#pin_code_modal").text($("#pin_code").val());
    $("#mobile_no_modal").text($("#mobile_no").val());
    $("#email_modal").text($("#email").val());
    $("#residency_period_modal").text($("#residency_period").val());
    //////////////////////////////Bank Details///////////////////////////////////////////
    $("#bank_account_number_modal").text($("#bank_account_number").val());
    $("#name_of_bank_modal").text($("#name_of_bank").val());
    $("#bank_branch_modal").text($("#bank_branch").val());
    $("#bank_ifsc_code_modal").text($("#bank_ifsc_code").val());
    /////////////////////////////////Land details////////////////////////////////////////
    if(scheme_id == 17)
    {
      $('#mouza_name_modal').text($('#mouza_name').val());
      $('#land_jlno_modal').text($('#land_jlno').val());
      $('#khatian_no_modal').text($('#khatian_no').val());
      $('#plot_no_modal').text($('#plot_no').val());
      $('#land_area_modal').text($('#land_area').val());
      $('#land_holdername_modal').text($('#land_holdername').val());
    }
    $("#cultivation_by_applicant_modal").text(
      $("#cultivation_by_applicant").val()
    );
    $("#source_income_modal").text($("#source_income").val());
    $("#any_other_benefitis_modal").text($("#any_other_benefitis").val());

    $("#landListModal tbody").html("");
    var tr = $("#landList").find("TR:has(td:not(:last-child))").clone();
    $("#landListModal tbody").append(tr);
    $("#landListModal tbody tr td.del-lnd").hide();
    /////////////////////////////////Family Details/////////////////////////////////////////

    $("#memberListModal tbody").html("");
    var tr = $("#memberList").find("TR:has(td:not(:last-child))").clone();
    $("#memberListModal tbody").append(tr);
    $("#memberListModal tbody tr td.del-mem").hide();

    //////////////////////////////Self Decleration///////////////////////////////////////////
    // if (scheme_id != 2) {
    $("#av_status_modal").text($("#av_status option:selected").text());
    // }

    $(".receive-pension").click(function(){        

      var selectedRP = new Array();
      var n1 = jQuery(".receive-pension:checked").length;
      if (n1 > 0){
       
          jQuery(".receive-pension:checked").each(function(){
              selectedRP.push( $(this).val());
          });
      }  

      $("#receive-pension-modal").text(selectedRP)
      
  });


  $(".social-security-pension").click(function(){ 

    var selectedCategory = new Array();
    var n2 = jQuery(".social-security-pension:checked").length;
    if (n2 > 0){
     
        jQuery(".social-security-pension:checked").each(function(){
            selectedCategory.push($(this).val());
        });
    }  

    $("#checkbox-tick-modal").text(selectedCategory)

   
});

    $("#text_1_modal").text($("#text_1").val());
    $("#text_2_modal").text($("#text_2").val());
    $("#receiving_pension_other_source_1_txt").text(
      $("#receiving_pension_other_source_1").val()
    );
    $("#receiving_pension_other_source_2_txt").text(
      $("#receiving_pension_other_source_2").val()
    );
  });
  $("#nominate_name_modal").text($("#nominate_name").val());
  // alert($('#nominate_name').val());
  $("#nominate_address_modal").text($("#nominate_address").val());
  $("#nominate_relationship_modal").text($("#nominate_relationship").val());
  $("#org_val_modal").text($("#org_val").val());
  $("#ssp_y_n_modal").text($("#ssp_y_n option:selected").text());
  $("#pucca_house_y_n_modal").text(
    $("#pucca_house_y_n option:selected").text()
  );

  $(".modal-submit").on("click", function () {
    //$(".modal-submit").attr("disabled", true);
    $(".modal-submit").hide();
    $("#submitting").show();
    $("#submit_loader").show();
    //$("#register_form").submit();
  });
});

if (scheme_id === 13) {
  $(".modal-submit").on("click", function () {
    var table2 = document.getElementById("landListModal");
    var jsonArr2 = [];
    for (var i = 0, row; (row = table2.rows[i]); i++) {
      if (i == 0) continue;
      var col2 = row.cells;
      var jsonObj2 = {
        slno: col2[0].innerHTML,
        block_name: col2[1].innerHTML,
        mouza: col2[2].innerHTML,
        jl_no: col2[3].innerHTML,
        khatian_no: col2[4].innerHTML,
        daag_no: col2[5].innerHTML,
        quantity: col2[6].innerHTML,
      };

      jsonArr2.push(jsonObj2);
    }

    $("#f_land_array").val(JSON.stringify(jsonArr2));

    var table = document.getElementById("memberListModal");
    var jsonArr = [];
    for (var i = 0, row; (row = table.rows[i]); i++) {
      if (i == 0) continue;
      var col = row.cells;
      var jsonObj = {
        slno: col[0].innerHTML,
        f_member_name: col[1].innerHTML,
        f_member_address: col[2].innerHTML,
        f_member_age: col[3].innerHTML,
        f_member_profession: col[4].innerHTML,
        f_member_monthly_income: col[5].innerHTML,
        f_member_relationship: col[6].innerHTML,
        f_member_dependent_by_applicant: col[6].innerHTML,
      };

      jsonArr.push(jsonObj);
    }
    $("#f_member_array").val(JSON.stringify(jsonArr));

    console.log(JSON.stringify(jsonArr));
    $(".modal-submit").hide();
    $("#submitting").show();
    $("#submit_loader").show();
    //$("#register_form").submit();
  });
}
