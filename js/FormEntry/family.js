$(document).ready(function () {
  var add_family = 0,
    error_land = 0;
  var scheme_id = $("#scheme_id").val();
  var error_family = 0;

  // Add family member
  $("#btn_addMember").click(function () {
    add_family = 0; // Reset error flag

    if ($.trim($("#f_member_name").val()).length == 0) {
      add_family = 1;
      $("#error_f_member_name").text("Name is required");
      $("#f_member_name").addClass("has-error");
    } else {
      $("#error_f_member_name").text("");
      $("#f_member_name").removeClass("has-error");
    }

    if ($.trim($("#f_member_address").val()).length == 0) {
      add_family = 1;
      $("#error_f_member_address").text("Address is required");
      $("#f_member_address").addClass("has-error");
    } else {
      $("#error_f_member_address").text("");
      $("#f_member_address").removeClass("has-error");
    }

    if ($.trim($("#f_member_age").val()).length == 0) {
      add_family = 1;
      $("#error_f_member_age").text("Age is required");
      $("#f_member_age").addClass("has-error");
    } else {
      $("#error_f_member_age").text("");
      $("#f_member_age").removeClass("has-error");
    }

    // Prevent form submission if validation fails
    if (add_family == 1) {
      return false;
    } else {
      // Collect input values
      var f_member_name = $("#f_member_name").val();
      var f_member_address = $("#f_member_address").val();
      var f_member_age = $("#f_member_age").val();
      var f_member_profession = $("#f_member_profession").val();
      var f_member_monthly_income = $("#f_member_monthly_income").val();
      var f_member_relationship = $("#f_member_relationship").val();
      var f_member_dependent_by_applicant = $(
        "#f_member_dependent_by_applicant option:selected"
      ).text();

      $("#hidden_land_count").val(1);

      // Calculate new row index
      var rowCount = $("#memberList tbody tr").length;
      var i = rowCount + 1;

      // Create the new row
      var markup = `
          <tr id="id_${i}">
            <td>${i}</td>
            <td>${f_member_name}</td>
            <td>${f_member_address}</td>
            <td>${f_member_age}</td>
            <td>${f_member_profession}</td>
            <td>${f_member_monthly_income}</td>
            <td>${f_member_relationship}</td>
            <td>${f_member_dependent_by_applicant}</td>
            <td class='del-mem'>
              <a class='btn btn-danger delete-member' data-row-id='${i}' href='javascript:void(0)'>Delete</a>
            </td>
          </tr>`;

      // Append the row to the table body
      var tableBody = $("#memberList tbody");
      tableBody.append(markup);

      // Reset input fields
      $("#f_member_name").val("");
      $("#f_member_address").val("");
      $("#f_member_age").val("");
      $("#f_member_profession").val("");
      $("#f_member_monthly_income").val("");
      $("#f_member_relationship").val("");
      $("#f_member_dependent_by_applicant").val("Yes");
      $("#addMemberModal").modal("hide");
    }
  });

  // Delete family member
  $(document).on("click", ".delete-member", function () {
    var sl_no = $(this).data("row-id");

    if (confirm("Are you sure?")) {
      var row = $("#memberList tr#id_" + sl_no);
      if (row.length > 0) {
        row.remove();

        // Update row numbers and IDs
        $("#memberList tbody tr").each(function (index) {
          $(this).attr("id", "id_" + (index + 1));
          $(this)
            .find("td:first")
            .text(index + 1); // Update row number
        });
      } else {
        console.error(`Row with ID id_${sl_no} not found.`);
      }
    }
  });



  if (scheme_id == 13) {
    $("#previous_btn_fm_details").click(function () {
      $("#list_fm_details").removeClass("active active_tab1");
      $("#list_fm_details").removeAttr("href data-toggle");
      $("#fm_details").removeClass("active in");
      $("#list_fm_details").addClass("inactive_tab1");
      $("#list_land_details").removeClass("inactive_tab1");
      $("#list_land_details").addClass("active_tab1 active");
      $("#list_land_details").attr("href", "#bank_details");
      $("#list_land_details").attr("data-toggle", "tab");
      $("#land_details").addClass("active in");
    });

    $("#btn_fm_details").click(function () {
      $("#list_fm_details").removeClass("active active_tab1");
      $("#list_fm_details").removeAttr("href data-toggle");
      $("#fm_details").removeClass("active");
      $("#list_fm_details").addClass("inactive_tab1");
      $("#list_experience_details").removeClass("inactive_tab1");
      $("#list_experience_details").addClass("active_tab1 active");
      $("#list_experience_details").attr("href", "#experience_details");
      $("#list_experience_details").attr("data-toggle", "tab");
      $("#experience_details").addClass("active in");
    });
  }
});
