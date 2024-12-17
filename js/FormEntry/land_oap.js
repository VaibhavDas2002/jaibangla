$(document).ready(function () {
    var add_land = 0, error_land = 0;
    var scheme_id = $("#scheme_id").val();
  
    $(document).on("click", ".delete-land", function () {
        var sl_no = $(this).data("row-id");
        if (confirm("Are you sure?")) {
          var row = $("#landList tbody tr#id_" + sl_no);
          row.remove();
          $("#landList tbody tr").each(function (index) {
            $(this).attr("id", "id_" + (index + 1)); 
            $(this).find("td:first").text(index + 1); 
          });
        }
      });
      
      
    $("#btn_addLand").click(function () {
      // Reset validation flag
      // add_land = 0;
  
      // Validation checks
      if ($.trim($("#block_name").val()).length === 0) {
        add_land = 1;
        $("#error_block_name").text("Block Name is required");
        $("#block_name").addClass("has-error");
      } else {
        $("#error_block_name").text("");
        $("#block_name").removeClass("has-error");
      }
  
      if ($.trim($("#mouza").val()).length === 0) {
        add_land = 1;
        $("#error_mouza").text("Mouza is required");
        $("#mouza").addClass("has-error");
      } else {
        $("#error_mouza").text("");
        $("#mouza").removeClass("has-error");
      }
  
      if ($.trim($("#jl_no").val()).length === 0) {
        add_land = 1;
        $("#error_jl_no").text("JL No. is required");
        $("#jl_no").addClass("has-error");
      } else {
        $("#error_jl_no").text("");
        $("#jl_no").removeClass("has-error");
      }
  
      if ($.trim($("#khatian_no").val()).length === 0) {
        add_land = 1;
        $("#error_khatian_no").text("Khatian No. is required");
        $("#khatian_no").addClass("has-error");
      } else {
        $("#error_khatian_no").text("");
        $("#khatian_no").removeClass("has-error");
      }
  
      if ($.trim($("#daag_no").val()).length === 0) {
        add_land = 1;
        $("#error_daag_no").text("Daag No. is required");
        $("#daag_no").addClass("has-error");
      } else {
        $("#error_daag_no").text("");
        $("#daag_no").removeClass("has-error");
      }
  
      if ($.trim($("#quantity").val()).length === 0) {
        add_land = 1;
        $("#error_quantity").text("Quantity is required");
        $("#quantity").addClass("has-error");
      } else {
        $("#error_quantity").text("");
        $("#quantity").removeClass("has-error");
      }
  
      if (add_land === 1) {
        return false; // Exit if validation fails
      } else {
        // Gather input values
        var block_name = $("#block_name").val();
        var mouza = $("#mouza").val();
        var jl_no = $("#jl_no").val();
        var khatian_no = $("#khatian_no").val();
        var daag_no = $("#daag_no").val();
        var quantity = $("#quantity").val();
  
        var rowCount = $("#landList tbody tr").length;
        var i = rowCount + 1;
  
        // Append new row
        var markup = `
          <tr id='id_${i}'>
            <td>${i}</td>
            <td>${block_name}</td>
            <td>${mouza}</td>
            <td>${jl_no}</td>
            <td>${khatian_no}</td>
            <td>${daag_no}</td>
            <td>${quantity}</td>
            <td class='del-lnd'>
              <a class='btn btn-danger delete-land' data-row-id='${i}' href='javascript:void(0)'>Delete</a>
            </td>
          </tr>`;
  
        $("#landList tbody").append(markup);
  
        // Reset form fields
        $("#block_name").val("");
        $("#mouza").val("");
        $("#jl_no").val("");
        $("#khatian_no").val("");
        $("#daag_no").val("");
        $("#quantity").val("");
        $("#addLandModal").modal("hide");
      }
    });






    if (scheme_id == 13) {
      $("#btn_land_details").click(function () {
        error_land = 0;
  
        // Validation check
        if ($.trim($("#cultivation_by_applicant").val()).length === 0) {
          error_land = 1;
          $("#error_cultivation_by_applicant").text("Select Cultivation by Applicant is required");
          $("#cultivation_by_applicant").addClass("has-error");
        } else {
          $("#error_cultivation_by_applicant").text("");
          $("#cultivation_by_applicant").removeClass("has-error");
        }
  
        if (error_land === 1) {
          return false; // Exit if validation fails
        } else {
          // Update tab navigation
          $("#list_land_details").removeClass("active active_tab1")
            .removeAttr("href data-toggle")
            .addClass("inactive_tab1");
  
          $("#list_fm_details").removeClass("inactive_tab1")
            .addClass("active_tab1 active")
            .attr("href", "#experience_details")
            .attr("data-toggle", "tab");
  
          $("#land_details").removeClass("active");
          $("#fm_details").addClass("active in");
        }
      });
  
      $("#previous_btn_land_details").click(function () {
        // Navigate to the previous tab
        $("#list_land_details").removeClass("active active_tab1")
          .removeAttr("href data-toggle")
          .addClass("inactive_tab1");
  
        $("#list_bank_details").removeClass("inactive_tab1")
          .addClass("active_tab1 active")
          .attr("href", "#bank_details")
          .attr("data-toggle", "tab");
  
        $("#land_details").removeClass("active in");
        $("#bank_details").addClass("active in");
      });
    }
  });
  