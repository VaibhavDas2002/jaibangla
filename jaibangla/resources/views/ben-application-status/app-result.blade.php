<style type="text/css">
  .has-error
  {
    border-color:#cc0000;
    background-color:#ffff99;
  }
  .preloader1{
    position: fixed;
    top:40%;
    left: 52%;
    z-index: 999;
  }
  .preloader1 {
    background: transparent !important;
  }
  .collapsable-head {
    /*border-radius: 5px; */
    border-bottom: 1px solid powderblue;
    background-color: antiquewhite; 
    text-align: center; 
    padding: 5px; 
    cursor: pointer;
  }
  .collapsable-head span {
    float: left; 
    margin-top: 4px;
    margin-left: 10px;
  }
  .collapsable-head font {
    font-size: 15px; 
    font-weight: bold;
  }
  .main-collapsable {
    border: 1px solid powderblue; 
    border-radius: 4px;
    margin-top: 5px;
  }
  table {
    border-top: 1px solid #e6e6e6;
  }
  .collapsable-body {
    padding: 5px;
  }
  .tbl {
    border: 1px solid #000;
    font-size: 14px;
    margin-top: 5px; 
    width: 100%; 
    background-color: #fff;
  }
  .tbl tr td {
    width: 25%;
    padding: 1px;
  }
  tbody{
    font-size: 14px;
  }
</style>
<section>
  <!-- Personal Details Tab -->
  <div class="main-collapsable">
    <div class="collapsable-head" id="head_div">
      <span id="icon"><i class="fa fa-plus"></i></span><font>Beneficiary Details</font>
    </div>
    <div id="details_div" class="table-responsive">
      @if(count($results)>0)
      <table id="personal_details" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 14px; text-align: center;">
            <th colspan="9">Personal Details</th>
            <th colspan="6">Payment Related Details</th>
            <th colspan="4">Banking Details</th>
          </tr>
          <tr style="font-size: 12px;">
            <th>Pension Id</th>
            <th>Name</th>
            <th>Father's Name</th>
            <th>Voter ID</th>
            <th>Ration Card</th>
            <th>District</th>
            <th>Scheme</th>
            <th>Block/Municipality</th>
            <th>GP/Ward</th>
            <th>Application Date</th>
            <th>Next Level Role Id</th>
            <th>Lot Generated</th>
            <th>Bank Edited</th>
            <th>Payment Count</th>
            <th>Last Paid YYMM</th>
            <th>Bank Name</th>
            <th>Branch Name</th>
            <th>Account No</th>
            <th>Ifsc Code</th>
          </tr>
        </thead>
        <tbody>
          
        </tbody>
      </table>
      @else
        <div class="text-danger" align="center"><h4>No record found!!</h4></div>
      @endif
    </div>
  </div>
  <!-- End Tab -->

  <!-- Duplicate Beneficiary Tab -->
  @if($duplicate_ben > 0)
  <div class="main-collapsable">
    <div class="collapsable-head" id="duplicate_ben_details">
      <span id="icon6"><i class="fa fa-plus"></i></span><font>Duplicate Beneficiary Details [Duplicate Approve Reject]</font>
    </div>
    <div id="duplicate_ben_tab" class="table-responsive">
      <table id="duplicate_ben_table" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Pension Id</th>
            <th>Name</th>
            <th>Father's Name</th>
            <th>Voter ID</th>
            <th>Ration Card</th>
            <th>District</th>
            <th>Scheme</th>
            <th>Block/Municipality</th>
            <th>Next Level Role Id</th>
            <th>Lot Generated</th>
            <th>Bank Edited</th>
            <th>Payment Count</th>
            <th>Last Paid YYMM</th>
            <th>Rejected User</th>
          </tr>
        </thead>
        <tbody>
          
        </tbody>
      </table>
    </div>
  </div>
  @endif
  <!-- End Tab -->

  <!-- Update Ben Details Tab -->
  @if($update_details > 0)
  <div class="main-collapsable">
    <div class="collapsable-head" id="update_ben_details">
      <span id="icon7"><i class="fa fa-plus"></i></span><font>Update Beneficiary Details [Update Ben Details]</font>
    </div>
    <div id="details_div7" class="table-responsive">
      <table id="update_ben_table" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Pension Id</th>
            <th>District</th>
            <th>Scheme</th>
            <th>Old Data</th>
            <th>New Data</th>
            <th>Remarks</th>
            <th>Done By User</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>
  </div>
  @endif
  <!-- End Tab -->
  
  <!-- IFMS Payment Tab -->
  @if($ifms_results > 0)
  <div class="main-collapsable">
    <div class="collapsable-head" id="ifms_payment">
      <span id="icon1"><i class="fa fa-plus"></i></span><font>IFMS Payment Details [Transaction Lot Details]</font>
    </div>
    <div id="ifms_payment_tab" class="table-responsive">
      <table id="ifms_payment_table" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Sl No</th>
            <th>Lot No</th>
            <th>Pension Id</th>
            <th>Name</th>
            <th>Scheme</th>
            <th>Account No</th>
            <th>Ifsc Code</th>
            <th>Mobile No</th>
            <th>IFMS Status</th>
            <th>Is Active</th>
            <th>Wrongdata Flag</th>
            <th>UTR No</th>
            <th>Paid YYMM</th>
            <th>Updated_at</th>
          </tr>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>
  </div>
  @endif
  <!-- End Tab -->

  <!-- IFMS Transaction Lot Tab -->
  @if($ifms_lot_count > 0)
  <div class="main-collapsable">
    <div class="collapsable-head" id="head_div3">
      <span id="icon3"><i class="fa fa-plus"></i></span><font>IFMS Payment Details [Transaction Lot]</font>
    </div>
    <div id="details_div3" class="table-responsive">
      <table id="ifms_transaction_lot_table" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Lot No</th>
            <th>Lot Month</th>
            <th>Lot Year</th>
            <th>IFMS Failed Count</th>
            <th>RBI Success Count</th>
            <th>RBI Failed Count</th>
            <th>Lot Status</th>
            <th>File Name</th>
          </tr>
        </thead>
        <tbody>
          
        </tbody>
      </table>
    </div>
  </div>
  @endif
  <!-- End Tab -->

  <!-- SBI Payment Tab -->
  @if($sbi_result > 0)
  <div class="main-collapsable">
    <div class="collapsable-head" id="sbi_payment">
      <span id="icon2"><i class="fa fa-plus"></i></span><font>SBI Payment Details [Transaction Lot Details]</font>
    </div>
    <div id="sbi_payment_tab" class="table-responsive">
      <table id="sbi_payment_table" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Sl No.</th>
            <th>Lot No</th>
            <th>Pension Id</th>
            <th>Name</th>
            <th>Scheme</th>
            <th>Account No</th>
            <th>Ifsc Code</th>
            <th>Status Code</th>
            <th>Is Active</th>
            <th>Paid YYMM</th>
            <th>Updated_at</th>
          </tr>
        </thead>
        <tbody>
          
        </tbody>
      </table>
    </div>
  </div>
  @endif
  <!-- End Tab -->

  <!-- SBI Transaction Lot Tab -->
  @if($sbi_lot_count > 0)
  <div class="main-collapsable">
    <div class="collapsable-head" id="head_div4">
      <span id="icon4"><i class="fa fa-plus"></i></span><font>SBI Payment Details [Transaction Lot]</font>
    </div>
    <div id="details_div4" class="table-responsive">
      <table id="sbi_transaction_lot_table" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Lot No</th>
            <th>Lot Month</th>
            <th>Lot Year</th>
            <th>Success Count</th>
            <th>Failed Count</th>
            <th>Lot Status</th>
          </tr>
        </thead>
        <tbody>
          
        </tbody>
      </table>
    </div>
  </div>
  @endif
  <!-- End Tab -->

  <!-- Lot Master Tab -->
  @if($lotMasterCount > 0)
  <div class="main-collapsable">
    <div class="collapsable-head" id="lot_master">
      <span id="icon5"><i class="fa fa-plus"></i></span><font>Lot Master</font>
    </div>
    <div id="lot_master_tab" class="table-responsive">
      <table id="lot_master_table" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Lot No</th>
            <th>Lot Month</th>
            <th>Lot Year</th>
            <th>Success Count</th>
            <th>Failed Count</th>
            <th>Ref No</th>
            <th>Lot Type</th>
            <th>Payment Mode</th>
            <th>Repeat Lot</th>
            <th>Repeat DRN Part</th>
          </tr>
        </thead>
        <tbody>
          
        </tbody>
      </table>
    </div>
  </div>
  @endif
  <!-- End Tab -->
</section>
<script>
  var ben_id = $('#ben_id').val();
  $("#details_div").hide();
  $('#details_div7').hide();
  $('#ifms_payment_tab').hide();
  $('#sbi_payment_tab').hide();
  $('#lot_master_tab').hide();
  $('#duplicate_ben_tab').hide();
  $('#details_div4').hide();
  $('#details_div3').hide();

  //######## Personal Details ########//
  $("#head_div").on("click", function () {
    var PersonalDetails = "";
     $("#details_div").toggle("slow");
    //$("#details_div").show();
    if ($('#icon').html() == "<i class=\"fa fa-plus\"></i>") {
      if ( $.fn.DataTable.isDataTable('#personal_details')) {
        $('#personal_details').DataTable().destroy();
      }
      PersonalDetails= $('#personal_details').DataTable({
          dom: 'Blfrtip',
          //"scrollX": true,
          "paging": false, // Disable Pagination
          "searchable": false,
          "ordering":false, // Disable Ordering of all column
          "bFilter": false,
          "bInfo": false, // Disable Showing 1 to 20 of 2000 entries
          "pageLength":20,
          'lengthMenu': [[10, 20, 30, 50,100], [10, 20, 30, 50,100]],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>'
          },
          ajax: {
              url: "{{ url('personal-details') }}",
              type: "POST",
              data: { ben_id : ben_id, _token : "{{ csrf_token() }}" },
              error: function (jqXHR, textStatus, errorThrown) {
                //alert("Error!!");
                $('.preloader1').hide();
                $('#loadingDiv').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
              }
            },
            initComplete: function(){
              $('#loadingDiv').hide();
              //console.log('Data rendered successfully');
            },
            columns: [
              {"data": "pension_id"},
              {
                "data": "-",
                render: function (data, type, row) {
                  if (row.mname == null) {
                    var fullName = row.fname + " " + row.lname;
                  }else if(row.fname == null && row.mname == null && row.lname == null){
                    var fullName = document.write("-");
                  }else{
                    var fullName = row.fname + " " + row.mname + " " + row.lname;
                  }
                  return fullName;
                }
              },
              {
                "data": "-",
                render: function (data, type, row) {
                  if (row.father_mname == null) {
                    var fatherName = row.father_fname + " " + row.father_lname;
                  }else{
                    var fatherName = row.father_fname + " " + row.father_mname + " " + row.father_lname;
                  }
                  return fatherName;
                }
              },
              {"data": "voter_id"},
              {
                "data": "-",
                render: function (data, type, row) {
                  var rationCard = row.ration_card_cat + "-" + row.ration_card;
                  return rationCard;
                }
              },
              {"data": "district"},
              {"data": "scheme"},
              {"data": "block_municipality"},
              {"data": "gp_ward"},
              {"data": "created_at"},
              {"data": "next_level_role_id"},
              {"data": "lot_generated"},
              {"data": "bank_edited"},
              {"data": "payment_count"},
              {"data": "last_paid_yymm"},
              {"data": "bank_name"},
              {"data": "branch_name"},
              {"data": "bank_code"},
              {"data": "ifsc"},
            ],
            buttons: [

            ],
      });
      //alert(ben_id);
      $('#icon').html("<i class=\"fa fa-minus\"></i>");
    }
    else {
      $('#icon').html("<i class=\"fa fa-plus\"></i>");
    }
  });
  //######## Update Ben Details ########//
  $("#update_ben_details").on("click", function () {
    var updateDetails = "";
    $("#details_div7").toggle("slow");
    if ($('#icon7').html() == "<i class=\"fa fa-plus\"></i>"){
      //alert('Update Ben Details');
      if ( $.fn.DataTable.isDataTable('#update_ben_table')) {
        $('#update_ben_table').DataTable().destroy();
      }
      updateDetails= $('#update_ben_table').DataTable({
          dom: 'Blfrtip',
          //"scrollX": true,
          "paging": false, // Disable Pagination
          "searchable": false,
          "ordering":false, // Disable Ordering of all column
          "bFilter": false,
          "bInfo": false, // Disable Showing 1 to 20 of 2000 entries
          "pageLength":20,
          'lengthMenu': [[10, 20, 30, 50,100], [10, 20, 30, 50,100]],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>'
          },
          ajax: {
              url: "{{ url('update-ben-details') }}",
              type: "POST",
              data: { ben_id : ben_id, _token : "{{ csrf_token() }}" },
              error: function (jqXHR, textStatus, errorThrown) {
                //alert("Error!!");
                $('.preloader1').hide();
                $('#loadingDiv').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
              }
            },
            initComplete: function(){
              $('#loadingDiv').hide();
              //console.log('Data rendered successfully');
            },
            columns: [
              {"data": "pension_id"},
              {"data": "district"},
              {"data": "scheme"},
              {"data": "old_data"},
              {"data": "new_data"},
              {"data": "remarks"},
              {"data": "user_id"},
              {"data": "date"},
            ],
            buttons: [

            ],
      });
      $('#icon7').html("<i class=\"fa fa-minus\"></i>");
    }
    else{
      //alert('Update Ben Details');
      $('#icon7').html("<i class=\"fa fa-plus\"></i>");
    }
  });
  //######## IFMS Payment ########//
  $('#ifms_payment').on("click", function(){
    var ifmsPayment = "";
    $("#ifms_payment_tab").toggle("slow");
    if ($('#icon1').html() == "<i class=\"fa fa-plus\"></i>"){
      //alert('Update Ben Details');
      if ( $.fn.DataTable.isDataTable('#ifms_payment_table')) {
        $('#ifms_payment_table').DataTable().destroy();
      }
      ifmsPayment= $('#ifms_payment_table').DataTable({
          dom: 'Blfrtip',
          //"scrollX": true,
          "paging": false, // Disable Pagination
          "searchable": false,
          "ordering":false, // Disable Ordering of all column
          "bFilter": false,
          "bInfo": false, // Disable Showing 1 to 20 of 2000 entries
          "pageLength":20,
          'lengthMenu': [[10, 20, 30, 50,100], [10, 20, 30, 50,100]],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>'
          },
          ajax: {
              url: "{{ url('ifms-payment') }}",
              type: "POST",
              data: { ben_id : ben_id, _token : "{{ csrf_token() }}" },
              error: function (jqXHR, textStatus, errorThrown) {
                //alert("Error!!");
                $('.preloader1').hide();
                $('#loadingDiv').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
              }
            },
            initComplete: function(){
              $('#loadingDiv').hide();
              //console.log('Data rendered successfully');
            },
            columns: [
              {"data": "DT_RowIndex"},
              {"data": "drn_part"},
              {"data": "pension_id"},
              {"data": "name"},
              {"data": "scheme"},
              {"data": "acc_no"},
              {"data": "ifsc_code"},
              {"data": "mobile_no"},
              {"data": "ifms_status"},
              {"data": "is_active"},
              {"data": "wrongdata_flag"},
              {"data": "utr_no"},
              {"data": "paid_yymm"},
              {"data": "updated_at"},
            ],
            buttons: [

            ],
      });
      $('#icon1').html("<i class=\"fa fa-minus\"></i>");
    }
    else{
      //alert('Update Ben Details');
      $('#icon1').html("<i class=\"fa fa-plus\"></i>");
    }
  });
  //######## SBI Payment ########//
  $("#sbi_payment").on("click", function () {
    var sbiPayment = "";
     $("#sbi_payment_tab").toggle("slow");
    //$("#details_div").show();
    if ($('#icon2').html() == "<i class=\"fa fa-plus\"></i>") {
      if ( $.fn.DataTable.isDataTable('#sbi_payment_table')) {
        $('#sbi_payment_table').DataTable().destroy();
      }
      sbiPayment= $('#sbi_payment_table').DataTable({
          dom: 'Blfrtip',
          //"scrollX": true,
          "paging": false, // Disable Pagination
          "searchable": false,
          "ordering":false, // Disable Ordering of all column
          "bFilter": false,
          "bInfo": false, // Disable Showing 1 to 20 of 2000 entries
          "pageLength":20,
          'lengthMenu': [[10, 20, 30, 50,100], [10, 20, 30, 50,100]],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>'
          },
          ajax: {
              url: "{{ url('sbi-payment') }}",
              type: "POST",
              data: { ben_id : ben_id, _token : "{{ csrf_token() }}" },
              error: function (jqXHR, textStatus, errorThrown) {
                //alert("Error!!");
                $('.preloader1').hide();
                $('#loadingDiv').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
              }
            },
            initComplete: function(){
              $('#loadingDiv').hide();
              //console.log('Data rendered successfully');
            },
            columns: [
              {"data": "DT_RowIndex"},
              {"data": "lot_no"},
              {"data": "pension_id"},
              {"data": "name"},
              {"data": "scheme"},
              {"data": "acc_no"},
              {"data": "ifsc_code"},
              {"data": "status_code"},
              {"data": "is_active"},
              {"data": "paid_yymm"},
              {"data": "updated_at"},
            ],
            buttons: [

            ],
      });
      //alert(ben_id);
      $('#icon2').html("<i class=\"fa fa-minus\"></i>");
    }
    else {
      $('#icon2').html("<i class=\"fa fa-plus\"></i>");
    }
  });
  //######## Lot Master ########//
  $("#lot_master").on("click", function () {
    var lotMaster = "";
     $("#lot_master_tab").toggle("slow");
    //$("#details_div").show();
    if ($('#icon5').html() == "<i class=\"fa fa-plus\"></i>") {
      //alert();
      if ( $.fn.DataTable.isDataTable('#lot_master_table')) {
        $('#lot_master_table').DataTable().destroy();
      }
      lotMaster= $('#lot_master_table').DataTable({
          dom: 'Blfrtip',
          //"scrollX": true,
          "paging": false, // Disable Pagination
          "searchable": false,
          "ordering":false, // Disable Ordering of all column
          "bFilter": false,
          "bInfo": false, // Disable Showing 1 to 20 of 2000 entries
          "pageLength":20,
          'lengthMenu': [[10, 20, 30, 50,100], [10, 20, 30, 50,100]],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>'
          },
          ajax: {
              url: "{{ url('lot-master') }}",
              type: "POST",
              data: { ben_id : ben_id, _token : "{{ csrf_token() }}" },
              error: function (jqXHR, textStatus, errorThrown) {
                //alert("Error!!");
                $('.preloader1').hide();
                $('#loadingDiv').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
              }
            },
            initComplete: function(){
              $('#loadingDiv').hide();
              //console.log('Data rendered successfully');
            },
            columns: [
              {"data": "lot_no"},
              {"data": "lot_month"},
              {"data": "lot_year"},
              {"data": "rbi_success_count"},
              {"data": "rbi_failed_count"},
              {"data": "ref_no"},
              {"data": "lot_type_id"},
              {"data": "payment_mode"},
              {"data": "repeat_lot"},
              {"data": "repeat_drn_part"},
            ],
            buttons: [

            ],
      });
      //alert(ben_id);
      $('#icon5').html("<i class=\"fa fa-minus\"></i>");
    }
    else {
      $('#icon5').html("<i class=\"fa fa-plus\"></i>");
    }
  });
  //######## Duplicate Beneficiary ########//
  $("#duplicate_ben_details").on("click", function () {
    var duplicateBen = "";
    $("#duplicate_ben_tab").toggle("slow");
    if ($('#icon6').html() == "<i class=\"fa fa-plus\"></i>"){
      alert('Update Ben Details');
      if ( $.fn.DataTable.isDataTable('#duplicate_ben_table')) {
        $('#duplicate_ben_table').DataTable().destroy();
      }
      duplicateBen= $('#duplicate_ben_table').DataTable({
          dom: 'Blfrtip',
          //"scrollX": true,
          "paging": false, // Disable Pagination
          "searchable": false,
          "ordering":false, // Disable Ordering of all column
          "bFilter": false,
          "bInfo": false, // Disable Showing 1 to 20 of 2000 entries
          "pageLength":20,
          'lengthMenu': [[10, 20, 30, 50,100], [10, 20, 30, 50,100]],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>'
          },
          ajax: {
              url: "{{ url('duplicate-beneficiary') }}",
              type: "POST",
              data: { ben_id : ben_id, _token : "{{ csrf_token() }}" },
              error: function (jqXHR, textStatus, errorThrown) {
                //alert("Error!!");
                $('.preloader1').hide();
                $('#loadingDiv').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
              }
            },
            initComplete: function(){
              $('#loadingDiv').hide();
              //console.log('Data rendered successfully');
            },
            columns: [
              {"data": "pension_id"},
              {
                "data": "-",
                render: function (data, type, row) {
                  if (row.ben_mname == null) {
                    var fullName = row.ben_fname + " " + row.ben_lname;
                  }else{
                    var fullName = row.ben_fname + " " + row.ben_mname + " " + row.ben_lname;
                  }
                  return fullName;
                }
              },
              {
                "data": "-",
                render: function (data, type, row) {
                  if (row.father_mname == null) {
                    var fatherName = row.father_fname + " " + row.father_lname;
                  }else{
                    var fatherName = row.father_fname + " " + row.father_mname + " " + row.father_lname;
                  }
                  return fatherName;
                }
              },
              {"data": "epic_voter_id"},
              {
                "data": "-",
                render: function (data, type, row) {
                  var rationCard = row.ration_card_cat + "-" + row.ration_card;
                  return rationCard;
                }
              },
              {"data": "dist_code"},
              {"data": "scheme"},
              {"data": "block_ulb_name"},
              {"data": "next_level_role_id"},
              {"data": "bank_edited"},
              {"data": "payment_count"},
              {"data": "last_paid_yymm"},
              {
                "data": "-",
                render: function (data, type, row) {
                  var rationCard = row.ration_card_cat + "-" + row.ration_card;
                  return rationCard;
                }
              },
            ],
            buttons: [

            ],
      });
      $('#icon6').html("<i class=\"fa fa-minus\"></i>");
    }
    else{
      //alert('Update Ben Details');
      $('#icon6').html("<i class=\"fa fa-plus\"></i>");
    }
  });
  //######## SBI Transaction Lot ########//
  $("#head_div4").on("click", function () {
    var sbiTransaction = "";
    $("#details_div4").toggle("slow");
    if ($('#icon4').html() == "<i class=\"fa fa-plus\"></i>"){
      //alert('Update Ben Details');
      if ( $.fn.DataTable.isDataTable('#sbi_transaction_lot_table')) {
        $('#sbi_transaction_lot_table').DataTable().destroy();
      }
      sbiTransaction= $('#sbi_transaction_lot_table').DataTable({
          dom: 'Blfrtip',
          //"scrollX": true,
          "paging": false, // Disable Pagination
          "searchable": false,
          "ordering":false, // Disable Ordering of all column
          "bFilter": false,
          "bInfo": false, // Disable Showing 1 to 20 of 2000 entries
          "pageLength":20,
          'lengthMenu': [[10, 20, 30, 50,100], [10, 20, 30, 50,100]],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>'
          },
          ajax: {
              url: "{{ url('sbi-transaction-lot') }}",
              type: "POST",
              data: { ben_id : ben_id, _token : "{{ csrf_token() }}" },
              error: function (jqXHR, textStatus, errorThrown) {
                //alert("Error!!");
                $('.preloader1').hide();
                $('#loadingDiv').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
              }
            },
            initComplete: function(){
              $('#loadingDiv').hide();
              //console.log('Data rendered successfully');
            },
            columns: [
              {"data": "lot_no"},
              {"data": "lot_month"},
              {"data": "lot_year"},
              {"data": "success_count"},
              {"data": "failed_count"},
              {"data": "lot_status"},
            ],
            buttons: [

            ],
      });
      $('#icon4').html("<i class=\"fa fa-minus\"></i>");
    }
    else{
      //alert('Update Ben Details');
      $('#icon4').html("<i class=\"fa fa-plus\"></i>");
    }
  });
  //######## IFMS Transaction Lot ########//
  $("#head_div3").on("click", function () {
    var ifmsTransaction = "";
    $("#details_div3").toggle("slow");
    if ($('#icon3').html() == "<i class=\"fa fa-plus\"></i>"){
      //alert('Update Ben Details');
      if ( $.fn.DataTable.isDataTable('#ifms_transaction_lot_table')) {
        $('#ifms_transaction_lot_table').DataTable().destroy();
      }
      ifmsTransaction= $('#ifms_transaction_lot_table').DataTable({
          dom: 'Blfrtip',
          //"scrollX": true,
          "paging": false, // Disable Pagination
          "searchable": false,
          "ordering":false, // Disable Ordering of all column
          "bFilter": false,
          "bInfo": false, // Disable Showing 1 to 20 of 2000 entries
          "pageLength":20,
          'lengthMenu': [[10, 20, 30, 50,100], [10, 20, 30, 50,100]],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>'
          },
          ajax: {
              url: "{{ url('ifms-transaction-lot') }}",
              type: "POST",
              data: { ben_id : ben_id, _token : "{{ csrf_token() }}" },
              error: function (jqXHR, textStatus, errorThrown) {
                //alert("Error!!");
                $('.preloader1').hide();
                $('#loadingDiv').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
              }
            },
            initComplete: function(){
              $('#loadingDiv').hide();
              //console.log('Data rendered successfully');
            },
            columns: [
              {"data": "lot_no"},
              {"data": "lot_month"},
              {"data": "lot_year"},
              {"data": "ifms_wrongdata_count"},
              {"data": "rbi_success_count"},
              {"data": "rbi_failed_count"},
              {"data": "lot_status"},
              {"data": "file_name"},
            ],
            buttons: [

            ],
      });
      $('#icon3').html("<i class=\"fa fa-minus\"></i>");
    }
    else{
      //alert('Update Ben Details');
      $('#icon3').html("<i class=\"fa fa-plus\"></i>");
    }
  });
</script>