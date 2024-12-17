  <style type="text/css">
    .full-width{
      width:100%!important;
    }
  .bg-blue{
    background-image: linear-gradient(to right top, #0073b7, #0086c0, #0097c5, #00a8c6, #00b8c4)!important;
  }
  .bg-red{
  background-image: linear-gradient(to right bottom, #dd4b39, #ec6f65, #d21a13, #de0d0b, #f3060d)!important;
  }
  .bg-yellow{
    background-image: linear-gradient(to right bottom, #dd4b39, #e65f31, #ed7328, #f1881e, #f39c12)!important;
  }
  .bg-green{
  background-image: linear-gradient(to right bottom, #04736d, #008f73, #00ab6a, #00c44f, #5ddc0c)!important;
  }

  .bg-verify{
    background-image: linear-gradient(to right top, #f39c12, #f8b005, #fac400, #fad902, #f8ee15)!important;
  }
  .info-box {
      display: block;
      min-height: 90px;
      background: #b6d0ca33!important;
      width: 100%;
      box-shadow: 0px 0px 15px 0px rgba(0, 0, 0, 0.30)!important;
      border-radius: 2px;
      margin-bottom: 15px;
  }
  .small-box .icon{
    margin-top: 7%;
  }
  .small-box>.inner {
      padding: 10px;
      color: white;
  }

  .small-box p {
      font-size: 18px!important;
  }
  .select2 .select2-container{
  } 

  .link-button {
    background: none;
    border: none;
    color: blue;
    text-decoration: underline;
    cursor: pointer;
    font-size: 1em;
    font-family: serif;
  }
  .link-button:focus {
    outline: none;
  }
  .link-button:active {
    color:red;
  }
  .small-box-footer-custom{
    position: relative;
      text-align: center;
      padding: 3px 0;
      color: #fff;
      color: rgba(255,255,255,0.8);
      display: block;
      z-index: 10;
      background: rgba(0,0,0,0.1);
      text-decoration: none;
      font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;
      font-weight: 400;
      width:100%;
  }
  .small-box-footer-custom:hover {
      color: #fff;
      background: rgba(0,0,0,0.15);
  }
  th.sorting::after,
  th.sorting_asc::after,
  th.sorting_desc::after {
    content:"" !important;
  }
  .errorField{
      border-color: #990000;
    }
    .searchPosition{
      margin:70px;
    }
    .submitPosition{
      margin: 25px 0px 0px 0px;
    }

    
    .typeahead { border: 2px solid #FFF;border-radius: 4px;padding: 8px 12px;max-width: 300px;min-width: 290px;background: rgba(66, 52, 52, 0.5);color: #FFF;}
    .tt-menu { width:300px; }
    ul.typeahead{margin:0px;padding:10px 0px;}
    ul.typeahead.dropdown-menu li a {padding: 10px !important;  border-bottom:#CCC 1px solid;color:#FFF;}
    ul.typeahead.dropdown-menu li:last-child a { border-bottom:0px !important; }
    .bgcolor {max-width: 550px;min-width: 290px;max-height:340px;background:url("world-contries.jpg") no-repeat center center;padding: 100px 10px 130px;border-radius:4px;text-align:center;margin:10px;}
    .demo-label {font-size:1.5em;color: #686868;font-weight: 500;color:#FFF;}
    .dropdown-menu>.active>a, .dropdown-menu>.active>a:focus, .dropdown-menu>.active>a:hover {
      text-decoration: none;
      background-color: #1f3f41;
      outline: 0;
    }
    table.dataTable thead{
      padding-right: 20px;
    }
    table.dataTable thead > tr > th{
      padding-right: 20px;
    }
    table.dataTable thead th{
      padding: 10px 18px 10px 18px;
      white-space: nowrap;
      border-right: 1px solid #dddddd;
    }
    table.dataTable tfoot th{
      padding: 10px 18px 10px 18px;
      white-space: nowrap;
      border-right: 1px solid #dddddd;
    }
    table.dataTable tbody td {
      padding: 10px 18px 10px 18px;
      border-right: 1px solid #dddddd;
      white-space: nowrap;
      -webkit-box-sizing: content-box;
      -moz-box-sizing: content-box;
      box-sizing: content-box;
    }
    .criteria1{
      text-transform: uppercase;
      font-weight: bold;
    }
    .item_header{
			font-weight: bold;
		}
    #example_length{
      margin-left: 40%;
      margin-top: 2px;
    }
    @keyframes spinner {
    to {transform: rotate(360deg);}
  }
  
  .spinner:before {
    content: '';
    box-sizing: border-box;
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin-top: -10px;
    margin-left: -10px;
    border-radius: 50%;
    border: 2px solid #ccc;
    border-top-color: #333;
    animation: spinner .6s linear infinite;
  }
  @media print {
    body * {
        visibility: hidden;
    }
    #ben_view_modal #ben_view_modal * {
        visibility: visible;
    }
		#ben_view_modal{
			position:absolute;
    		left:0;
    		top:0;
		}
		[class*="col-md-"] {
			float: none;
			display:table-cell;
		}

		[class*="col-lg-"] {
			float: none;
			display:table-cell;
		}
		.pagebreak { page-break-before: always; } 
	}
  </style>

  @extends('Parijayi.base')
  @section('action-content')

      <!-- Main content -->
      <section class="content">
        <div class="box">
        <div class="box-header">
          <div class="row">
              <div class="col-sm-8">
	
              </div>
          </div>
        </div>
        <div class="box-body">
					@if(count($errors) > 0)
					<div class="alert alert-danger alert-block">
						<ul>
						@foreach($errors->all() as $error)
						<li><strong> {{ $error }}</strong></li>
						@endforeach
						</ul>
					</div>
					@endif        
					<div id="example2_wrapper" class="col-md-12 dataTables_wrapper form-inline dt-bootstrap js-report-form">
						<div class="row" style="margin-bottom:1%">
							<form method="POST" role="form" action="{{ route('employeereport.fetch') }}">
								<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
								<div class="input-group col-md-5">
									<label class=" input-group-addon">Select Level</label> 
									<select class="form-control select2 full-width urban"  name="level3" id='level3'>
										<option value="">--All--</option>
										<option value="2">Rural</option>
										<option value="1">Urban</option>
									</select>
								</div>
								<div class="input-group col-md-6">
									<label class=" input-group-addon">Block/Municipality/Corporation</label> 
									<select class="form-control select2 full-width localbody"  name="level1a" id='level1a'>
										<option value="">--All--</option>
										
									</select>
								</div>
								<div class="form-group col-md-12 text-left" style="margin-top:1%" >
									<button type="button" name="filter" id="filter" class="btn btn-warning">Filter Records</button>
								</div>
								<input type="hidden" name="_token" id="token1" value="{{ csrf_token() }}">
								<input type="hidden" id="level1data" name="level1data">
								<input type="hidden" id="level2data" name="level2data">
								<input type="hidden" id="level3data" name="level3data">
								<input type="hidden" id="level4data" name="level4data">
								<input type="hidden" id="level1adata" name="level1adata">
								<input type="hidden" id="level1bdata" name="level1bdata">
								<input type="hidden" id="level1cdata" name="level1cdata">
							</form>
						</div>
					</div>          
					<div class="col-md-12">
						<h2><span class="label label-default">District: &nbsp;&nbsp;&nbsp; {{ucwords(strtolower($district_name))}}</span></h2>
					</div>  
					<div class="col-md-2">
						<h4 style="color: #d21a13; font: bold">* Full details available in Excel Export only</h4>
					</div>
					<div class="col-md-offset-2 col-md-3">
						@if($app_type == 'F')
							<h4><span class="label label-primary">Fresh Applications</span></h4>
						@elseif($app_type == 'A')
							<h4><span class="label label-success">Approved Applications</span></h4>
						@elseif($app_type == 'R')
							<h4><span class="label label-danger">Rejected Applications</span></h4>
						@endif

					</div>
					@if($app_type == 'F')
					<div class="col-md-offset-1 col-md-4 btn-group" role="group" >
            <!-- CLOSED TEMPORARY -->
						<button class="btn btn-success clsbulk_approve" id="bulk_approve" disabled>Approve Selected Beneficiaries</button>
            <a type="button" class="btn btn-warning" id="duplicate_list" href="{{url('/parijayi_duplicate_mis')}}" target="_blank">Duplicate List</a>
					</div>
					@endif
        <div class="col-md-12 text-center" id="loaderdiv" hidden>
          <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px"/>
        </div>  
        @if($app_type == 'F')
        <div class="col-md-offset-8 col-md-4">
          <span class="badge">S</span> Single Record  &nbsp;&nbsp;&nbsp; <span class="badge">D</span> Duplicate Record
           &nbsp;&nbsp;&nbsp; <span class="badge">R</span> Duplicate Resolved  
        </div>
        @endif
        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
        <table id="example" class="display" cellspacing="0" width="100%">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <input type="hidden" name="district_code" id="district_code" value="{{ $district_code }}">
          <thead>
                <tr role="row">
                  <td width="3%" class="text-right">
                    <input type="checkbox" id="select_all" name="select_all" onchange="document.getElementById('bulk_approve').disabled = !this.checked;">
                  </td>
                  <th width="8%" class="text-left">Beneficiary ID</th>
                  <th width="26%" class="text-left">Beneficiary Name</th>
                  <th width="9%" class="text-left">DOB</th>     
                  <th width="4%" class="text-left">Gender</th>
                  <th width="10%" class="text-left">Mobile No</th>
                  <th width="10%" class="text-left">Aadhar No</th>
                  <th width="12%">Voter Id</th>
                  <th width="4%" class="text-left">Status</th>     
				          <th width="14%">Action</th>  
				          <th>Fathers Name</th>
                  <th>Family Mobile No</th>
                  <th>Payment Type</th>
                  <th>Bank Name</th>
                  <th>Branch Name</th>
                  <th>IFSC</th>
                  <th>Account No</th>
                  <th>UPI</th>
                  <th>Latitude</th>
                  <th>Longitude</th>
				          <th>District</th>
				          <th>Block</th>
				          <th>Municipality</th>
				          <th>Address</th>
				          <th>PinCode</th> 
				          <th>Record Verified (Y/N)</th>
              </tr>
          </thead>
          <tfoot>
              <tr>
                <th width="3%" class="text-left">
                  <input type="checkbox" id="select_all" name="select_all disabled">
                </th>
                <th width="8%" class="text-left">Beneficiary ID</th>
                <th width="26%" class="text-left">Beneficiary Name</th>
                <th width="9%" class="text-left">DOB</th>     
                <th width="4%" class="text-left">Gender</th>
                <th width="16%" class="text-left">Mobile No</th>
                <th width="16%" class="text-left">Aadhar No</th>
                <th width="12%" class="text-left">Voter Id</th>
                <th width="4%" class="text-left">Status</th>     
                <th width="14%">Action</th>  
                <th>Fathers Name</th>
                <th>Family Mobile No</th>
                <th>Payment Type</th>
                <th>Bank Name</th>
                <th>Branch Name</th>
                <th>IFSC</th>
                <th>Account No</th>
                <th>UPI</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>District</th>
                <th>Block</th>
                <th>Municipality</th>
                <th>Address</th>
                <th>PinCode</th> 
                <th>Record Verified (Y/N)</th>
              </tr>
          </tfoot>   
            
      </table>  
      <div class="row">
              
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  
                </div>
              </div>
        </div>  

        </div>

      </div>
    <!--   </div> -->
      </section>
      <!-- /.content -->
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="ben_view_modal" tabindex="-1">
      <div class="modal-dialog modal-lg" id="printArea">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Beneficiay Detail View</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-3 text-center">
                <img src="{{ asset('upload/img_avatar.png') }}" id="profile_pic" alt="" width="150px" height="150px" />
                <br/><br/><span class="label label-success" style="font-size:13px;"><b>Beneficiary ID:</b></span><br/><br/> <span class=" badge item_value" id="view_ben_id"></span>
              </div>
              <div class="col-md-8 panel panel-info">
                  <div class="row panel-heading">
                    <div class="col-md-12"><h4>Personal Details</h4></div>
                  </div>
                  <div class="row penel-body">
                    <div class="col-md-12"><span class="item_header">Full Name: </span>
                    <span class="item_value" id="ben_name"></span></div>
                  </div>
                  <div class="row">
                    <div class="col-md-12"><span class="item_header">Fathers Name: </span>
                    <span class="item_value" id="benf_name"></span></div>
                  </div>
                  <div class="row">
                    <div class="col-md-5"><span class="item_header">Date of Birth: </span>
                      <span class="item_value" id="ben_dob"></span></div>
                    <div class="col-md-7"><span class="item_header">Gender: </span>
                      <span class="item_value" id="ben_gender"></span></div>
                  </div>
                  <div class="row">
                    <div class="col-md-5"><span class="item_header">Latitude: </span>
                      <span class="item_value" id="ben_lat"></span></div>
                    <div class="col-md-7"><span class="item_header">Longitude: </span>
                      <span class="item_value" id="ben_long"></span></div>                    
                  </div>
                  <div class="row">
                    <div class="col-md-12"><span class="item_header">State: </span>
                      <span class="item_value" id="ben_state"></span></div>
                  </div>
                  <div class="row"> 
                    <div class="col-md-12"><span class="item_header">District: </span>
                      <span class="item_value" id="ben_dist"></span></div>                    
                  </div>
                  <div class="row">
                    <div class="col-md-5"><span class="item_header">Rural/Urban: </span>
                      <span class="item_value" id="ben_type"></span></div>
                    <div class="col-md-7"><span class="item_header">Block/Municipality: </span>
                      <span class="item_value" id="ben_block"></span></div>                   
                  </div>
                  <div class="row">
                    <div class="col-md-5"><span class="item_header">Pincode: </span>
                    <span class="item_value" id="ben_pin"></span></div>
                    <div class="col-md-7"><span class="item_header">GP/Ward: </span>
                      <span class="item_value" id="ben_gp"></span></div>                
                  </div>
                  <div class="row">
                    <div class="col-md-12"><span class="item_header">Address Line 1: </span>
                      <span class="item_value" id="ben_addr"></span></div>

                  </div>
              </div>
            </div>
            <div class="row">
              <hr/>
            </div>
            <div class="row">
              <div class="col-md-3">
                <span class="item_header">Mobile No:</span> <span class="item_value" id="ben_mobile"></span>
              </div>
              <div class="col-md-3">
                <span class="item_header">Family Mobile No:</span> <span class="item_value" id="benf_mobile"></span>
              </div>
              <div class="col-md-3">
                <span class="item_header">Aadhar No:</span> <span class="item_value" id="ben_aadhar"></span>
              </div>
              <div class="col-md-3">
                <span class="item_header">Voter ID Number:</span> <span class="item_value" id="ben_voterid"></span>
              </div>
            </div>  
            <div class="row">
              <div class="col-md-3">
                <span class="item_header">Account No:</span> <span class="item_value" id="ben_account"></span>
              </div>
              <div class="col-md-3">
                <span class="item_header">Bank Name:</span> <span class="item_value" id="ben_bank"></span>
              </div>
              <div class="col-md-6">
                <span class="item_header">Bank Branch:</span> <span class="item_value" id="ben_branch"></span>
              </div>              
            </div>

            <div class="row">  
              <div class="col-md-3">
                <span class="item_header">IFSC:</span> <span class="item_value" id="ben_ifsc"></span>             
              </div>
              <div class="col-md-3">
                 <span class="item_header">UPI:</span> <span class="item_value" id="ben_upi"></span>
              </div>

            </div>
            <div class="row">
              <div class="col-md-12">
                <span class="item_header">Application Date:</span><span class="item_value" id="ben_appdate"> </span>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12"><hr/></div>
            </div>
            <div class="row pagebreak">
              <div class="col-md-4 text-center" id="aadhar_img_div">
                <div class="row">
                  <div class="col-md-12 text-center">
                    <img id="aadhar_img" src="" alt="" width="250px" height="150px" />
                  </div>
                  <div class="col-md-12 text-center">
                    <span class="item_header">AADHAR CARD COPY</span>
                  </div>
                </div>
              </div>
              <div class="col-md-4 text-center" id="voter_f_img_div">
                <div class="row">
                  <div class="col-md-12 text-center">
                    <img id="voter_f_img" src="" alt="" width="250px" height="150px" />
                  </div>
                  <div class="col-md-12 text-center">
                    <span class="item_header">VOTER CARD COPY (FRONT)</span>
                  </div>
                </div>
              </div>
              <div class="col-md-4 text-center" id="voter_b_img_div">
                <div class="row">
                  <div class="col-md-12 text-center">
                    <img id="voter_b_img" src="" alt=""  width="250px" height="150px"/>
                  </div>
                  <div class="col-md-12 text-center">
                    <span class="item_header">VOTER CARD COPY (BACK)</span>
                  </div>
                </div>
              </div>
            </div>    
          </div>
          <div class="modal-footer">
            <form method="POST" action="{{ route('printSingleBenf') }}" target="_blank">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="ben_id" name="ben_id"/>
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="print_Button">Print Document</button>
            </form> 
          </div>
        </div>
      </div>
    </div>
  
    <!-- End View Modal -->	

		<!-- Start Reject Model -->

    <!-- CLOSED TEMPORARY -->
		<div class="modal fade" id="ben_reject_modal" tabindex="-1">
			<div class="modal-dialog ">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Reject Beneficiary Application</h4>
					</div>
					<div class="modal-body">
						<h4>Are you sure you want to reject the application with the beneficiary details mentioned below?</h4><hr/>

						<table style="width:100%">
							<tr>
								<td style="width:30%;"><span class="item_header">Beneficiary Id:</span></td>
								<td><span class="item_value" id="reject_ben_id"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Beneficiary Name:</span></td>
								<td><span class="item_value" id="reject_ben_name"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Date of Birth:</span></td>
								<td><span class="item_value" id="reject_ben_dob"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Mobile No:     </span></td>
								<td><span class="item_value" id="reject_ben_mobile"></span></td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
								<td colspan="2">
									<span class="item_header">Please select reject reason:</span>
									<select id="reject_reason">
										<option value="">Select Option</option>
									</select>
								</td>
							</tr>	
						</table>
						<input type="hidden" id="reject_beneficiary_id"/>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-danger" id="reject_Button" data-dismiss="modal" disabled>Reject</button>
					</div>
				</div>
			</div>
		</div>
		<!-- End Reject Model -->

	
		@endsection
	



	<script src='{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}'></script>
  <script >

    // CLOSED TEMPORARY 

   function controlCheckBox(){
    var anyBoxesChecked = false;
    $(' input[type="checkbox"]').each(function() {
      if ($(this).is(":checked")) {
        anyBoxesChecked = true;
      }
    });
    if (anyBoxesChecked == true) {
      document.getElementById('bulk_approve').disabled = false;
    } else{
      document.getElementById('bulk_approve').disabled = true;
    }
  }
  function loadStatusData(){
    $.ajax({
      type: 'POST',
      url: '{{ url('getStatusCode') }}',
      data: {
        _token: '{{ csrf_token() }}',
      },
      success: function (datas) {
        if (!datas || datas.length === 0) {
          return;
        }
        for (var  i = 0; i < datas.length; i++) {
          $('#reject_reason').append($('<option>', {
            value: datas[i].code,
            text: datas[i].message,
          }));
        }
      },
      error: function (ex) {
      }
    });
  } 
	
  $(document).ready(function(){ 
	 
  $(".dataTables_scrollHeadInner").css({"width":"100%"});

  $(".table ").css({"width":"100%"});  

  $('.urban').change(function() {
      $('.localbody').empty().append('<option value="">--  All  --</option>'); 
      var selectedVal = $('.urban').val();
      if (selectedVal == -1) {
        return;
      }
      $.ajax({
        type: 'POST',
        url: '{{ url('loadLocalBody') }}',
        data: {
          _token: '{{ csrf_token() }}',
          district_code: '{{$district_code}}', 
          urban_rural: selectedVal,
        },
        success: function (datas) {
          if (!datas || datas.length === 0) {
            return;
          }
          for (var  i = 0; i < datas.length; i++) {
            $('.localbody').append($('<option>', {
              value: datas[i].id,
              text: datas[i].name,
              id: datas[i].id
            }));
          }
        },
        error: function (ex) {
        }
      });
  });

  //CLOSED TEMPORARY

  $('#bulk_approve').click(function(){
   
    $("#select_all").prop("checked",false);
    //benBulkApprove
    var selected = new Array();
    $("input[type=checkbox]").each(function() {
      if($(this).is(":checked"))
      selected.push($(this).val());
    });
    var selected_json = JSON.stringify(selected);
    $.ajax({
      type: 'POST',
      url: '{{ url('benBulkApprove') }}',
      dataType: 'json',
      data: {
        _token: '{{ csrf_token() }}',
        approvalcheck: selected_json,
      },
      beforeSend: function(){
        $('#loaderdiv').show();
      },
      success: function (datas) {
        $('#example').DataTable().ajax.reload();
        alert("Bulk approval of selected beneficiaries successful.");
      },
      complete: function(){
        $('#loaderdiv').hide();
      },
      error: function (ex) {
      }
    });
  });

  $('#select_all').click(function(e){
      var table= $(e.target).closest('table');
      $('td input:checkbox',table).prop('checked',this.checked);
  });

  //  CLOSED TEMPORARY

  $('#reject_Button').click(function(e){
    e.preventDefault();

    $.ajax({
      type: 'POST',
      url: '{{ url('benReject') }}',
      data: {
        ben_id: $('#reject_beneficiary_id').val(),
        reject_reason: $('#reject_reason').children('option:selected').val(),
        _token: '{{ csrf_token() }}',
      },
      success: function (datas) {
        alert('Beneficiary with id '+$('#reject_beneficiary_id').val()+' rejected');
        $('#example').DataTable().ajax.reload();
      },
      error: function (ex) {
      }
    });

  }) ; 
  $('#reject_reason').change(function(){
    var reasonCode = $('#reject_reason').children('option:selected').val();

    if(reasonCode!=""){
      document.getElementById('reject_Button').disabled=false;
    }else{
      document.getElementById('reject_Button').disabled=true;
    }
  }); 
	
  $('#filter').click(function(){
    
    //Urban/Rural
    level3_val=$('#level3').children('option:selected').val();
    $('#level3data').val(level3_val);

    // LocalBody
    level1a_val=$('#level1a').children('option:selected').val();
    $('#level1adata').val(level1a_val);
    
      table.clear().draw();
      table.ajax.reload();
  
  });

  var table=$('#example').DataTable( {
        dom: "Blfrtip",
        "paging": true,
        "pageLength":20,
        "lengthMenu": [[20, 50, 80, 120, 150, 180, 500,1000, 2000], [20, 50, 80, 120, 150, 180, 500,1000, 2000]],
		"serverSide": true,
		"deferRender": true,
        "processing":true,
        "bRetrieve": true,
        "ordering":false,
        "language": {
          "processing": '<img src="{{ asset('images/ZKZg.gif') }}" width="150px" height="150px"/>'
        },
        "ajax": 
        {
			url: "{{ url('getPrijiyiData') }}",
			type: "POST",
          	data:function(d){
              	d.level1= "{{ $district_code }}",
				d.level2= "{{ $district_name}}",
				d.level1a = $('#level1adata').val(),
              	d.level3=   $('#level3data').val(),
				d._token= "{{csrf_token()}}",
				d.application_type = "{{ $app_type }}"
			}
		} ,
        "columns": [
                  { "data": "check" },
                  { "data": "ben_id","defaultContent":""},
                  { "data": "ben_name","defaultContent":"" },
                  { "data": "dob","defaultContent":"0" },
                  { "data": "gender","defaultContent":"" },
                  { "data": "mob_no","defaultContent":"0" },
                  { "data": "aadhar","defaultContent":"0" },
                  { "data": "voterid_no","defaultContent":"" },                   
                  { "data": "status","defaultContent":"" },
                  { "data": "action","defaultContent":"0" },       
        				  { "data": "ben_father","defaultContent":"" },
                  { "data": "family_mobile_no","defaultContent":"" },

                  { "data": "payment_type","defaultContent":"" },
                  { "data": "bank_name","defaultContent":"" },                   
                  { "data": "bank_branch","defaultContent":"" },
                  { "data": "ifsc_code","defaultContent":"0" },       
                  { "data": "account_no","defaultContent":"" },
                  { "data": "upi_id","defaultContent":"" },

                  { "data": "present_lat","defaultContent":"" },
                  { "data": "present_long","defaultContent":"" },
        				  { "data": "district_name","defaultContent":"" },
        				  { "data": "block_name","defaultContent":"" },
        				  { "data": "municipality_name","defaultContent":"" },
        				  { "data": "address_line","defaultContent":"0" },
        				  { "data": "pincode","defaultContent":"0" },
        				  { "data": "","defaultContent":"" },
              ], 
          "columnDefs": [
                  { targets: "_all","orderable": false, },
                  { targets: 0, "className": "text-center", },
				  { targets: 7, "className": "text-center", },
				  { targets: [10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25], "visible":false}
                ],         
      
        "buttons": [
        {
		  extend: 'pdf',
		  exportOptions: {
                columns: [1,2,3,4,5,6,7,10,11]
			},	
          title: 'Sneher Paras Beneficiaries List',
          messageTop: function () {
                var message = "";
                if("{{ $district_name}}" != ""){
                  var message = message +"District: {{ $district_name}}, ";
                }  
                if($('#level3data').val()!="")
                  var message = message +"Block/Municipality/Corporation: "+$('#level1a').children('option:selected').text()+", ";
                var message = message + "Date: <?php echo date('d/m/Y');  ?>";
                      return message;
          },
          footer: true,
          pageSize:'A4',
          orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
        },
        {
		  extend: 'excel',
		  exportOptions: {
                columns: [1,2,3,4,5,6,7,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25]
			},
          title: 'Sneher Paras Beneficiaries List',
          messageTop: function () {
            var message = "";
            if("{{ $district_name}}" != ""){
              var message = message +"District: {{ $district_name}}, ";
            }  
            if($('#level3data').val()!="")
              var message = message +"Block/Municipality/Corporation: "+$('#level1a').children('option:selected').text()+", ";
            var message = message + "Date: <?php echo date('d/m/Y');  ?>";
                  return message;
          },
          footer: true,
          pageSize:'A4',
          //orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
        },
        {
		  extend: 'print',
		  exportOptions: {
                columns: [1,2,3,4,5,6,7,10,11]
			},
          title: 'Sneher Paras Beneficiaries List',
          messageTop: function () {
            var message = "";
            if("{{ $district_name}}" != ""){
              var message = message +"District: {{ $district_name}}, ";
            }  
            if($('#level3data').val()!="")
              var message = message +"Block/Municipality/Corporation: "+$('#level1a').children('option:selected').text()+", ";
            var message = message + "Date: <?php echo date('d/m/Y');  ?>";
                  return message;
          },
          footer: true,
          pageSize:'A4',
          //orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
        },
        ],
			} );
	   table.on('click','.ben_view_button',function(){
          $tr = $(this).closest('tr');
          if(($tr).hasClass('child')){
            $tr = $tr.prev('parent');
          }
          var data = table.row($tr).data();
          $('#view_ben_id').html(data['ben_id']);
          $('#ben_id').val(data['id']);
          $('#ben_name').html(data['ben_name']);
          $('#benf_name').html(data['ben_father']);
          $('#ben_dob').html(data['dob']);
          $('#ben_gender').html(data['gender']);
          $('#ben_lat').html(data['present_lat']);
          $('#ben_long').html(data['present_long']);
          $('#ben_state').html('West Bengal');
          $('#ben_dist').html(data['district_name']);
          $('#ben_type').html(data['rural_urban']);
          if(data['rural_urban']=='Rural')
            $('#ben_block').html(data['block_name']);
          else if(data['rural_urban']=='Urban') 
            $('#ben_block').html(data['municipality_name']);
          $('#ben_gp').html(data['gp_code']);
          $('#ben_pin').html(data['pincode']);
          $('#ben_addr').html(data['address_line']);
          $('#ben_mobile').html(data['mobile_no']);
          $('#benf_mobile').html(data['family_mobile_no']);
          $('#ben_aadhar').html(data['aadhar_no']);
          $('#ben_voterid').html(data['voterid_no']);
            $('#ben_upi').html(data['upi_id']);
            $('#ben_account').html(data['account_no']);
          $('#ben_bank').html(data['bank_name']);
          $('#ben_branch').html(data['bank_branch']);
          $('#ben_ifsc').html(data['ifsc_code']);
          $('#ben_appdate').html(data['created_at']);

          if(data['profile_pic']!=null)
            $('#profile_pic').attr("src","http://jaibanglamw.wb.gov.in/storage/workers_doc/"+data['profile_pic']);
          if(data['aadhar_img']!=null){          
            $('#aadhar_img').attr("src","http://jaibanglamw.wb.gov.in/storage/workers_doc/"+data['aadhar_img']);
            $('#aadhar_img_div').show();
          }
          else
            $('#aadhar_img_div').hide();//Hide DIV
          if(data['voterid_img_front']!=null){   
            $('#voter_f_img').attr("src","http://jaibanglamw.wb.gov.in/storage/workers_doc/"+data['voterid_img_front']);
            $('#voter_f_img_div').show();
          }
          else
            $('#voter_f_img_div').hide();//Hide Div
          if(data['voterid_img_back']!=null){        
            $('#voter_b_img').attr("src","http://jaibanglamw.wb.gov.in/storage/workers_doc/"+data['voterid_img_back']);
            $('#voter_b_img_div').show();//Hide Div 
          }
          else
            $('#voter_b_img_div').hide();//Hide Div 
            
          $('#ben_view_modal').modal('show');
      });
     //ben_reject_button

     // CLOSED TEMPORARY
        table.on('click','.ben_reject_button',function(){
          $tr = $(this).closest('tr');
          if(($tr).hasClass('child')){
            $tr = $tr.prev('parent');
          }
          loadStatusData();
          var data = table.row($tr).data();
          $('#reject_beneficiary_id').val(data['ben_id']);
          $('#reject_ben_id').html(data['ben_id']);
          $('#reject_ben_name').html(data['ben_name']);
          $('#reject_ben_dob').html(data['dob']);
          $('#reject_ben_mobile').html(data['mobile_no']);
          $('#ben_reject_modal').modal('show');
        });

  });

  </script>
