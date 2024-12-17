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
  .required-field::after {
    content: "*";
    color: red;
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

  @extends('singlestep.base')
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
							
						</div>
					</div>          
				
					<div class="col-md-offset-3 col-md-3">
						
					<h4><span class="label label-primary">Approved Applications</span></h4>
			
					</div>
					
        <div class="col-md-12 text-center" id="loaderdiv" hidden>
          <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px"/>
        </div>  

        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
         <div class="alert print-error-msg"  style="display:none;" id="errorDiv">
      <button type="button" class="close"  aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
      <ul></ul></div>
        <table id="example" class="display" cellspacing="0" width="100%">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          
          <thead>
                <tr role="row">
                 
                  <th width="12%" class="text-left">Application ID</th>
                  <th width="20%" class="text-left">Beneficiary Name</th>
                  <th width="10%" class="text-left">Bank IFSC</th>
                  <th width="12%" class="text-left">Bank Account No</th>
                  @if($is_subdiv)
                  <th width="15%" class="text-left">Municipality</th>
                  @else
                  <th width="15%" class="text-left">GP</th>
                  @endif
				          <th width="16%">Action</th>  
				          
              </tr>
          </thead>
          <tfoot>
              <tr>
                
                <th width="12%" class="text-left">Application ID</th>
                  <th width="20%" class="text-left">Beneficiary Name</th>  
                  <th width="10%" class="text-left">Bank IFSC</th>
                  <th width="12%" class="text-left">Bank Account No</th>
                   @if($is_subdiv)
                  <th width="15%" class="text-left">Municipality</th>
                   @else
                  <th width="15%" class="text-left">GP</th>
                  @endif
				          <th width="16%">Action</th> 
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
              <div class="col-md-offset-2 col-md-8 panel panel-info">
                  <div class="row panel-heading">
                    <div class="col-md-12"><h4>Personal Details</h4></div>
                  </div>
                  <div class="row penel-body">
                  <div class="col-md-12"><span class="item_header">Application ID: </span>
                    <span class="item_value" id="view_ben_id"></span></div>
                  <div class="col-md-12"><span class="item_header">OLD Application ID: </span></span>
                    <span class="item_value label label-warning" style="font-size:13px;" id="view_ben_old_id"></span></div>
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
                <span class="item_header">Aadhar No:</span> <span class="item_value" id="ben_aadhar"></span>
              </div>
              <div class="col-md-3">
                <span class="item_header">Voter ID :</span> <span class="item_value" id="ben_voterid"></span>
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
            
            </div>
            
            <div class="row">
              <div class="col-md-12"><hr/></div>
            </div>
               
          </div>
          <div class="modal-footer">
            <form method="POST" action="{{ route('printSingleBenf') }}" target="_blank">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="ben_id" name="ben_id"/>
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            <!-- <button type="submit" class="btn btn-primary" id="print_Button">Print Document</button> -->
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
								<td><span class="item_header">Father's Name:</span></td>
								<td><span class="item_value" id="reject_ben_father_name"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Bank IFSC:</span></td>
								<td><span class="item_value" id="reject_ben_ifsc"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Account Number:     </span></td>
								<td><span class="item_value" id="reject_ben_accno"></span></td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
						</table>
						<input type="hidden" id="reject_beneficiary_id"/>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-danger" id="reject_Button" data-dismiss="modal">Reject</button>
					</div>
				</div>
			</div>
		</div>
		<!-- End Reject Model -->

    <!-- Start Edit Model-->


    <div class="modal fade" id="ben_edit_modal" tabindex="-1">
			<div class="modal-dialog ">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Edit & Approve Beneficiary Application</h4>
					</div>
					<div class="modal-body">
						<h4>Are you sure you want to edit and approve the application with the beneficiary details mentioned below?</h4><hr/>

						<table style="width:100%">
							<tr>
								<td style="width:30%;"><span class="item_header">Beneficiary Id:</span></td>
								<td><span class="item_value" id="edit_ben_id"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Beneficiary Name:</span></td>
								<td><span class="item_value" id="edit_ben_name"></span></td>
							</tr>
              <tr>
								<td><span class="item_header">Father's Name:</span></td>
								<td><span class="item_value" id="edit_ben_father_name"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Bank IFSC:</span></td>
								<td><span class="item_value" id="edit_ben_ifsc"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Account Number:     </span></td>
								<td><span class="item_value" id="edit_ben_accno"></span></td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
						</table>
					</div>
					<div class="modal-footer">
          <form class="row" method="POST" action="{{ route('singlestep.application-edit-view') }}">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						    <input type="hidden" id="edit_beneficiary_id" name="id"/>
                <input type="hidden" name="scheme_id" value="{{$scheme_id}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-success btn-margin" >
                  Edit & Approve
                </button>
            </form>
						<!-- <button type="button" class="btn btn-danger" id="edit_Button" data-dismiss="modal">Edit & Approve</button> -->
					</div>
				</div>
			</div>
		</div>
  <!--Edit-->

  <!--Selective Edit-->
   <div class="modal fade" id="ben_selective_edit_modal" tabindex="-1">
      <div class="modal-dialog modal-lg" id="printArea">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h3 class="modal-title">Beneficiay Edit Details</h3>
          </div>
          <div class="modal-body">
            <div class="modal-body">
        
          <div class="form-group col-md-12">
            <label class="">Beneficiary Name</label>

          </div>
          <div class="row">
            <div class="form-group col-md-4">
              <label class="required-field">First Name</label>
              <input type="text" name="first_name" id="first_name" class="form-control txtOnly" placeholder="First Name" maxlength="200" value="{{ old('first_name') }}" tabindex="2" />
              <span id="error_first_name" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
              <label>Middle Name</label>
              <input type="text" name="middle_name" id="middle_name" class="form-control txtOnly" placeholder="Middle Name" maxlength="100" value="{{ old('middle_name') }}" tabindex="3" />
              <span id="error_middle_name" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
              <label class="required-field">Last Name</label>
              <input type="text" name="last_name" id="last_name" class="form-control txtOnly" placeholder="Last Name" maxlength="200" value="{{ old('last_name') }}" tabindex="4" />
              <span id="error_last_name" class="text-danger"></span>
            </div>
          </div>
           <div class="form-group col-md-12">
            <label class="">Beneficiary Father Name</label>

          </div>
          <div class="row">
            <div class="form-group col-md-4">
              <label class="required-field">First Name</label>
              <input type="text" name="father_first_name" id="father_first_name" class="form-control txtOnly" placeholder="First Name" maxlength="200" value="{{ old('first_name') }}" tabindex="2" />
              <span id="error_father_first_name" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
              <label>Middle Name</label>
              <input type="text" name="father_middle_name" id="father_middle_name" class="form-control txtOnly" placeholder="Middle Name" maxlength="100" value="{{ old('middle_name') }}" tabindex="3" />
              <span id="error_father_middle_name" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
              <label class="required-field">Last Name</label>
              <input type="text" name="father_last_name" id="father_last_name" class="form-control txtOnly" placeholder="Last Name" maxlength="200" value="{{ old('last_name') }}" tabindex="4" />
              <span id="error_father_last_name" class="text-danger"></span>
            </div>
          </div>

          <div class="row">
            <div class="form-group col-md-4">
              <label class="">Mobile Number</label>
              <input type="text" id="mobile_no" name="mobile_no" class="form-control NumOnly" placeholder="Mobile No" maxlength="10" value="{{ old('mobile_no') }}" tabindex="5">
              <span id="error_mobile_no" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
              <label class="">Date of Birth</label>
              @php
              
              $max='2020-01-01';
              
              @endphp
              <input type="date" name="dob" id="dob" class="form-control" tabindex="6" value="{{old('dob')}}" max="{{$max}}" />
              <!-- <input type="text" id="dob" name="dob"class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask placeholder="dd/mm/yyyy"> -->
              <span id="error_dob" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
              <label>Age<span style=""> (as on 01/01/2020)</span></label>
              <input type="hidden" name="hidden_age" id="hidden_age" val="">
              <input type="text" name="txt_age" id="txt_age" class="form-control NumOnly" placeholder="Age" value="{{ old('txt_age') }}" maxlength="3" tabindex="7" />
              <span id="error_txt_age" class="text-danger"></span>

            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-4">
              <label class="required-field">State</label>
              <input type="text" id="state" name="state" class="form-control" placeholder="" value="WEST BENGAL" readonly="true" tabindex="8">
              <span id="error_state" class="text-danger"></span>
            </div>


            <div class="form-group col-md-4">
              <label>District</label>
              <select name="district_edit" id="district_edit" class="form-control" tabindex="9">
                <option value="">--Select --</option>
                @foreach ($districts as $district)
                <option value="{{$district->district_code}}" @if(old('district')==$district->district_code) selected @endif> {{$district->district_name}}</option>
                @endforeach
              </select>
              <span id="error_district_edit" class="text-danger"></span>

            </div>
            <div class="form-group col-md-4" id="divUrbanCode">
              <label >Rural/ Urban</label>

              <select name="urban_code_edit" id="urban_code_edit" class="form-control" tabindex="10">
                <option value="">--Select --</option>
                @foreach(Config::get('constants.rural_urban') as $key=>$val)
                <option value="{{$key}}" @if( old('urban_code')==$key) selected @endif>{{$val}}</option>
                @endforeach

              </select>
              <span id="error_urban_code_edit" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4" id="divBodyCode">
              <label>Block/Municipality/Corp.</label>

              <select name="block_edit" id="block_edit" class="form-control" tabindex="11">
                <option value="">--Select --</option>


              </select>
              <span id="error_block_edit" class="text-danger"></span>
            </div>


            <div class="form-group col-md-4" id="divBodyCode">
              <label>GP/Ward No</label>

              <select name="gp_ward_edit" id="gp_ward_edit" class="form-control" tabindex="12">
                <option value="">--Select --</option>


              </select>
              <span id="error_gp_ward_edit" class="text-danger"></span>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label class="required-field">IFS Code</label>
              <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control special-char" placeholder="IFSC Code" onkeyup="this.value = this.value.toUpperCase();" value="{{ old('bank_ifsc_code') }}" maxlength='12' tabindex="3" />
              <span id="error_bank_ifsc_code" class="text-danger"></span>
            </div>

            <div class="form-group col-md-6">
              <label class="required-field">Bank Name</label>
              <input type="text" name="name_of_bank" id="name_of_bank" class="form-control special-char" placeholder="Bank Name" value="{{ old('name_of_bank') }}" maxlength="200" tabindex="4" readonly />
              <span id="error_name_of_bank" class="text-danger"></span>
            </div>



            <div class="form-group col-md-6">
              <label class="required-field">Bank Branch Name</label>
              <input type="text" name="bank_branch" id="bank_branch" class="form-control special-char" placeholder="Bank Branch Name" value="{{ old('bank_branch') }}" maxlength="300" tabindex="5" readonly />
              <span id="error_bank_branch" class="text-danger"></span>
            </div>

            <div class="form-group col-md-6">
              <label class="required-field">Bank Account Number</label>
              <input type="text" name="bank_account_number" id="bank_account_number" class="form-control NumOnly" placeholder="Bank Account No" value="{{ old('bank_account_number') }}" maxlength='16' tabindex="6" />
              <span id="error_bank_account_number" class="text-danger"></span>

            </div>
          </div>
        </div>

          <div class="modal-footer">
            <!-- <form method="POST" action="{{ route('printSingleBenf') }}" target="_blank">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
            <input type="hidden" id="applicant_id" name="applicant_id"/>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" id="edit_approve_button" data-dismiss="modal">Edit&Approve</button>
            <!-- <button type="submit" class="btn btn-primary" id="print_Button">Print Document</button> -->
            <!-- </form>  -->
          </div>
        </div>
      </div>
    </div>
    <!--End Selective Edit-->
	
		@endsection
	



	<script src='{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}'></script>
  <script src="{{ asset("js/select2.full.min.js") }}"></script>

  <script src="{{ URL::asset('js/master-data-v2.js') }}"></script>

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
  
	
  $(document).ready(function(){ 
       
 $('#urban_code').change(function() {
        var urban_code = $(this).val();

        $('#block').html('<option value="">--Select --</option>');
        select_district_code = $('#district_code_fk').val();
        if (select_district_code == '') {
          alert('Please Select District First');
          $("#district").focus();
          $("#urban_code").val('');
        } else {
          select_body_type = urban_code;
          var htmlOption = '<option value="">--Select--</option>';
          if (select_body_type == 2) {
            $.each(blocks, function(key, value) {
              if (value.district_code == select_district_code) {
                htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
              }
            });
          } else if (select_body_type == 1) {
            $.each(ulbs, function(key, value) {
              if (value.district_code == select_district_code) {
                htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
              }
            });
          }
          $('#block').html(htmlOption);
        }
      });
       $('#district_edit').change(function() {
        var district = $(this).val();
        //alert(district);
        $('#urban_code_edit').val('');
        $('#block').html('<option value="">--Select --</option>');

      });
         $('#urban_code_edit').change(function() {
        var urban_code = $(this).val();

        $('#block').html('<option value="">--Select --</option>');
        select_district_code = $('#district_edit').val();
        if (select_district_code == '') {
          alert('Please Select District First');
          $("#district_edit").focus();
          $("#urban_code_edit").val('');
        } else {
          select_body_type = urban_code;
          var htmlOption = '<option value="">--Select--</option>';
          if (select_body_type == 2) {
            $.each(blocks, function(key, value) {
              if (value.district_code == select_district_code) {
                htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
              }
            });
          } else if (select_body_type == 1) {
            $.each(ulbs, function(key, value) {
              if (value.district_code == select_district_code) {
                htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
              }
            });
          }
          $('#block_edit').html(htmlOption);
        }
      });
        $('#bank_ifsc_code').blur(function() {
        $ifsc_data = $.trim($('#bank_ifsc_code').val());
        $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
        if ($ifscRGEX.test($ifsc_data)) {
          $('#bank_ifsc_code').removeClass('has-error');
          $('#error_bank_ifsc_code').text('');
          $('#error_name_of_bank').html('<img  src="{{ asset('images/ZKZg.gif')}}" width="50px" height="50px"/>');
          $('#error_bank_branch').html('<img  src="{{ asset('images/ZKZg.gif')}}" width="50px" height="50px"/>');
          $.ajax({
            type: 'POST',
            url: '{{ url('legacy/getBankDetails') }}',
            data: {
              ifsc: $ifsc_data,
              _token: '{{ csrf_token() }}',
            },
            success: function(data) {
              if (!data || data.length === 0) {
                $('#error_bank_ifsc_code').text('No data found with the IFSC');
                $('#bank_ifsc_code').addClass('has-error');
                return;
              }
              data = JSON.parse(data);
              // console.log(data);
              $('#name_of_bank').val(data.bank);
              $('#bank_branch').val(data.branch);
              $('#error_name_of_bank').html('');
              $('#error_bank_branch').html('');
            },
            error: function(ex) {
              $('#error_bank_ifsc_code').text('Data fetch error');
              $('#bank_ifsc_code').addClass('has-error');
              $('#error_name_of_bank').html('');
              $('#error_bank_branch').html('');
              alert('Something wrong..may be session timeout. please logout and then login again');
              location.reload();
            }
          });

        } else {
          $('#error_bank_ifsc_code').text('IFSC format invalid please check the code');
          $('#bank_ifsc_code').addClass('has-error');
          $('#error_name_of_bank').html('');
          $('#error_bank_branch').html('');
        }
      });
        $('#block_edit').change(function() {
        var block = $(this).val();
        var district = $("#district_edit").val();
        var urban_code = $("#urban_code_edit").val();
        if (district == '') {
          alert('Please Select District First');
          $("#block_edit").val('');
          //$("#block_munc_corp_code_fk").val('');
          $("#district_edit").focus();
          $('#gp_ward_edit').html('<option value="">--Select--</option>');
        }
        if (urban_code == '') {
          alert('Please Select Rural/Urban First');
          $("#block_edit").val('');
          $("#urban_code_edit").focus();
          $('#gp_ward_edit').html('<option value="">--Select--</option>');
        }

        var htmlOption = '<option value="">--Select--</option>';
        if (urban_code == 2) {
          $.each(gps, function(key, value) {
            if ((value.district_code == district) && (value.block_code == block)) {
              htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
            }
          });
        } else if (urban_code == 1) {
          $.each(ulb_wards, function(key, value) {
            if ((value.urban_body_code == block)) {
              htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
            }
          });
        }
        $('#gp_ward_edit').html(htmlOption);
      });
     
	 $('#edit_approve_button').on('click', function() {
        
        var error_applicant_id = '';
        var error_first_name = '';
        var error_last_name = '';
        var error_father_first_name = '';
        var error_father_last_name = '';
        var error_mobile_no = '';
        var error_district_edit = '';
        var error_urban_code_edit = '';
        var error_block_edit = '';
        var error_gp_ward_edit = '';

        var error_name_of_bank = '';
        var error_bank_branch = '';
        var error_bank_account_number = '';
        var error_bank_ifsc_code = '';

        
        if ($.trim($('#first_name').val()).length == 0) {
          error_first_name = 'First Name is required';
          $('#error_first_name').text(error_first_name);
          $('#first_name').addClass('has-error');
        } else {
          error_first_name = '';
          $('#error_first_name').text(error_first_name);
          $('#first_name').removeClass('has-error');
        }
        if ($.trim($('#last_name').val()).length == 0) {
          error_last_name = 'Last Name is required';
          $('#error_last_name').text(error_last_name);
          $('#last_name').addClass('has-error');
        } else {
          error_last_name = '';
          $('#error_last_name').text(error_last_name);
          $('#last_name').removeClass('has-error');
        }
        if ($.trim($('#father_first_name').val()).length == 0) {
          error_father_first_name = 'First Name is required';
          $('#error_father_first_name').text(error_father_first_name);
          $('#father_first_name').addClass('has-error');
        } else {
          error_father_first_name = '';
          $('#error_father_first_name').text(error_father_first_name);
          $('#father_first_name').removeClass('has-error');
        }
        if ($.trim($('#father_last_name').val()).length == 0) {
          error_father_last_name = 'Last Name is required';
          $('#error_father_last_name').text(error_father_last_name);
          $('#father_last_name').addClass('has-error');
        } else {
          error_father_last_name = '';
          $('#error_father_last_name').text(error_father_last_name);
          $('#father_last_name').removeClass('has-error');
        }
        



        if ($.trim($('#applicant_id').val()).length == 0) {
          error_applicant_id = 'Applicant Id is required';
          $('#error_applicant_id').text(error_applicant_id);
          $('#applicant_id').addClass('has-error');
        } else {
          error_applicant_id = '';
          $('#error_applicant_id').text(error_applicant_id);
          $('#applicant_id').removeClass('has-error');
        }
        if ($.trim($('#name_of_bank').val()).length == 0) {
          error_name_of_bank = 'Name of Bank is required';
          $('#error_name_of_bank').text(error_name_of_bank);
          $('#name_of_bank').addClass('has-error');
        } else {
          error_name_of_bank = '';
          $('#error_name_of_bank').text(error_name_of_bank);
          $('#name_of_bank').removeClass('has-error');
        }

        if ($.trim($('#bank_branch').val()).length == 0) {
          error_bank_branch = 'Bank Branch is required';
          $('#error_bank_branch').text(error_bank_branch);
          $('#bank_branch').addClass('has-error');
        } else {
          error_bank_branch = '';
          $('#error_bank_branch').text(error_bank_branch);
          $('#bank_branch').removeClass('has-error');
        }

        if ($.trim($('#bank_account_number').val()).length == 0) {
          error_bank_account_number = 'Bank Account Number is required';
          $('#error_bank_account_number').text(error_bank_account_number);
          $('#bank_account_number').addClass('has-error');
        } else {
          error_bank_account_number = '';
          $('#error_bank_account_number').text(error_bank_account_number);
          $('#bank_account_number').removeClass('has-error');
        }

        if ($.trim($('#bank_ifsc_code').val()).length == 0) {
          error_bank_ifsc_code = 'IFS Code is required';
          $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
          $('#bank_ifsc_code').addClass('has-error');
        } else {
          error_bank_ifsc_code = '';
          $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
          $('#bank_ifsc_code').removeClass('has-error');
        }

        $ifsc_data = $.trim($('#bank_ifsc_code').val());
        $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
        if ($ifscRGEX.test($ifsc_data)) {
          error_bank_ifsc_code = '';
          $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
          $('#bank_ifsc_code').removeClass('has-error');
        } else {
          error_bank_ifsc_code = 'Please check IFS Code format';
          $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
          $('#bank_ifsc_code').addClass('has-error');
        }
        //alert(error_type_of_penstion);
        if (error_first_name != '' || error_last_name != '' ||
         error_father_first_name != '' || error_father_last_name != '' || error_name_of_bank != '' ||
          error_bank_branch != '' || error_bank_account_number != '' || error_bank_ifsc_code != '') {
          return false;
        } else {
          //  $(".modal-submit").hide();
          //  $("#submitting").show();
          // $("#submit_loader").show();
          //  $("#search").hide();
          var applicant_id = $('#applicant_id').val();
          //alert(applicant_id);
          var first_name = $('#first_name').val();
          var middle_name = $('#middle_name').val();
          var last_name = $('#last_name').val();

          var father_first_name = $('#father_first_name').val();
          var father_middle_name = $('#father_middle_name').val();
          var father_last_name = $('#father_last_name').val();

          var mobile_no = $('#mobile_no').val();
          var dob = $('#dob').val();
          var hidden_age = $('#hidden_age').val();
          var txt_age = $('#txt_age').val();
          var district = $('#district_edit').val();
          var urban_code = $('#urban_code_edit').val();
          var block = $('#block_edit').val();
          var gp_ward = $('#gp_ward_edit').val();
          //alert(hidden_age);

          var bank_ifsc_code = $('#bank_ifsc_code').val();
          var bank_account_number = $('#bank_account_number').val();
          $.ajax({
            type: 'get',
            url: '{{ url('farmerApproval')}}',
            data: {
              applicant_id: applicant_id,
              first_name: first_name,
              middle_name: middle_name,
              last_name: last_name,
              father_first_name: father_first_name,
              father_middle_name: father_middle_name,
              father_last_name: father_last_name,
              mobile_no: mobile_no,
              dob: dob,
              hidden_age: hidden_age,
              txt_age: txt_age,
              district: district,
              urban_code: urban_code,
              block: block,
              gp_ward: gp_ward,
              bank_ifsc_code: bank_ifsc_code,
              bank_account_number: bank_account_number,
              _token: '{{ csrf_token() }}',
            },
            success: function(data) {
              //$("#search").show();
              // $(".searchResult").hide();
              // console.log(data);
              if (data.return_status) {
                $(".searchResult").show();
                $('#applicant_id').val('');
                //  $("#submitting").hide();
                $("#submit_loader").hide();
                // $(".searchResult").hide();
                $('#first_name').val('');
                $('#middle_name').val('');
                $('#last_name').val('');
                $('#father_first_name').val('');
                $('#father_middle_name').val('');
                $('#father_last_name').val('');
                $('#mobile_no').val('');
                $('#dob').val('');
                $('#hidden_age').val(0);
                $('#txt_age').val(0);
                $('#district_edit').val('');
                $('#urban_code_edit').val('');
                $('#block_edit').val('');
                $('#gp_ward_edit').val('');
                $('#bank_ifsc_code').val('');
                $('#name_of_bank').val('');
                $('#bank_branch').val('');
                $('#bank_account_number').val('');
                $('#name_of_bank').val('');
                $("#ben_selective_edit_modal").modal('hide');
                printMsg(data.return_msg, '1', 'errorDiv');
                $('#example').DataTable().ajax.reload();
              } else {
                printMsg(data.return_msg, '0', 'errorDiv');
              }
            },
            error: function(ex) {
              alert('Something wrong..may be session timeout. please logout and then login again');
              //location.reload();
            }
          });
        }
      });  

  $('#selective_edit_ben_ifsc').blur(function(){
    $ifsc_data = $.trim($('#selective_edit_ben_ifsc').val());
    $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
    if($ifscRGEX.test($ifsc_data))
    {
      $('#selective_edit_ben_ifsc').removeClass('has-error');
      $('#error_selective_edit_ben_ifsc').text('');

      $.ajax({
        type: 'POST',
        url: '{{ url('legacy/getBankDetails') }}',
        data: {
          ifsc: $ifsc_data,
          _token: '{{ csrf_token() }}',
        },
        success: function (data) {
          if (!data || data.length === 0) {
            $('#error_selective_edit_ben_ifsc').text('No data found with the IFSC');
            $('#selective_edit_ben_bank').val("");
            $('#selective_edit_ben_branch').val("");
            $('#selective_edit_ben_ifsc').addClass('has-error');
            return;
          }
          data = JSON.parse(data);
         // console.log(data);
          $('#selective_edit_ben_bank').val(data.bank);
          $('#selective_edit_ben_branch').val(data.branch);
        },
        error: function (ex) {
          $('#error_selective_edit_ben_ifsc').text('Data fetch error');
          $('#selective_edit_ben_bank').val("");
          $('#selective_edit_ben_branch').val("");
          $('#selective_edit_ben_ifsc').addClass('has-error');
        }
      });

    }else{
      $('#error_selective_edit_ben_ifsc').text('IFSC format invalid please check the code');
      $('#selective_edit_ben_bank').val("");
      $('#selective_edit_ben_branch').val("");
      $('#selective_edit_ben_ifsc').addClass('has-error');
    }
 });  
	 
  $(".dataTables_scrollHeadInner").css({"width":"100%"});

  $(".table ").css({"width":"100%"});  



  //CLOSED TEMPORARY

  $('#bulk_approve').click(function(){
   
    $(".select_all").prop("checked",false);
    //benBulkApprove
    var selected = new Array();
    $("input[type=checkbox]").each(function() {
      if($(this).is(":checked"))
      selected.push($(this).val());
    });
    var selected_json = JSON.stringify(selected);
    $.ajax({
      type: 'POST',
      url: '{{ url('singleStepBenBulkApproveFarmer') }}',
      dataType: 'json',
      data: {
        _token: '{{ csrf_token() }}',
        scheme_id: "{{ $scheme_id }}",
        approvalcheck: selected_json,
      },
      beforeSend: function(){
        $('#loaderdiv').show();
      },
      success: function (data) {
        if(data.return_status){
        $('#example').DataTable().ajax.reload();
        alert("Bulk approval of selected beneficiaries has been successfully done.");
        $('#loaderdiv').hide();
        }
        else{
          $('#loaderdiv').hide();
           printMsg(data.return_msg,'0','errorDiv');
        }
      },
      complete: function(){
        $('#loaderdiv').hide();
      },
      error: function (ex) {
         $('#loaderdiv').hide();
         location.reload();
      }
    });
  });

  $('.select_all').click(function(e){
      var table= $(e.target).closest('table');
      $('td input:checkbox',table).prop('checked',this.checked);
  });



  $('#reject_Button').click(function(e){
    e.preventDefault();

    $.ajax({
      type: 'POST',
      url: '{{ url('singleStepBenRejectFarmer') }}',
      data: {
        ben_id: $('#reject_beneficiary_id').val(),
        _token: '{{ csrf_token() }}',
      },
      success: function (data) {
          if(data.return_status){
           alert('Beneficiary with id '+$('#reject_beneficiary_id').val()+' rejected');
            $('#example').DataTable().ajax.reload();
          }
        else{
          //$('#loaderdiv').hide();
           printMsg(data.return_msg,'0','errorDiv');
        }
      },
      error: function (ex) {
      }
    });

  }) ; 
 
	
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
       
        "pageLength":20,
        "lengthMenu": [[20, 50], [20, 50]],
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
			url: "{{ url('getApprovedFarmer') }}",
			type: "GET",
          	data:function(d){
				d._token= "{{csrf_token()}}"
			}
		} ,
        "columns": [
                
                  { "data": "application_id","defaultContent":""},
                  { "data": "ben_fullname","defaultContent":"" },
                  { "data": "bank_ifsc","defaultContent":"" },
                  { "data": "bank_code","defaultContent":"0" },
                  @if($is_subdiv)
                  { "data": "block_ulb_name","defaultContent":"" },
                  @else
                   { "data": "gp_ward_name","defaultContent":"" },
                  @endif
                  { "data": "action","defaultContent":"0" }      
        				 
              ]
			} );
     //console.log(table);
      
	   table.on('click','.ben_view_button',function(){
          $tr = $(this).closest('tr');
          if(($tr).hasClass('child')){
            $tr = $tr.prev('parent');
          }
          var data = table.row($tr).data();
          $('#view_ben_id').html(data['application_id']);
          $('#view_ben_old_id').html(data['old_beneficiary_id']);
          $('#ben_id').val(data['id']);
          $('#ben_name').html(data['ben_name']);
          $('#benf_name').html(data['benf_name']);
          $('#ben_dob').html(data['dob']);
          $('#ben_gender').html(data['gender']);
          
          
          $('#ben_addr').html(data['village_town_city']);
          $('#ben_mobile').html(data['mobile_no']);
          $('#ben_aadhar').html(data['aadhar_no']);
          $('#ben_voterid').html(data['epic_voter_id']);
            $('#ben_account').html(data['bank_code']);
          $('#ben_bank').html(data['bank_name']);
          $('#ben_branch').html(data['branch_name']);
          $('#ben_ifsc').html(data['bank_ifsc']);
            
          $('#ben_view_modal').modal('show');
      });
      table.on('click','.ben_selective_edit_approve_button',function(){
          $tr = $(this).closest('tr');
          if(($tr).hasClass('child')){
            $tr = $tr.prev('parent');
          }
          var data = table.row($tr).data();
          $('#selective_edit_id').val(data['ben_id']);
          $('#selective_edit_ben_old_id').html(data['old_beneficiary_id']);
          $('#selective_edit_ben_id').html(data['application_id']);
          $('#selective_edit_ben_fname').val(data['ben_fname']);
          $('#selective_edit_ben_mname').val(data['ben_mname']);
          $('#selective_edit_ben_lname').val(data['ben_lname']);

          $('#selective_edit_benf_name').html(data['benf_name']);
          $('#selective_edit_ben_dob').html(data['dob']);
          $('#selective_edit_ben_gender').html(data['gender']);
          
          
          $('#selective_edit_ben_addr').html(data['village_town_city']);
          $('#selective_edit_ben_mobile').html(data['mobile_no']);
          $('#selective_edit_ben_aadhar').html(data['aadhar_no']);
          $('#selective_edit_ben_voterid').html(data['epic_voter_id']);
            $('#selective_edit_ben_account').val(data['bank_code']);
          $('#selective_edit_ben_bank').val(data['bank_name']);
          $('#selective_edit_ben_branch').val(data['branch_name']);
          $('#selective_edit_ben_ifsc').val(data['bank_ifsc']);
            
          $('#ben_selective_edit_modal').modal('show');
      });
      table.on('click','.ben_edit_approve_button',function(){
        $tr = $(this).closest('tr');
        if(($tr).hasClass('child')){
          $tr = $tr.prev('parent');
        }
        var data = table.row($tr).data();
        $('#edit_beneficiary_id').val(data['ben_id']);
        $('#edit_ben_id').html(data['application_id']);
        $('#edit_ben_name').html(data['ben_name']);
        $('#edit_ben_father_name').html(data['benf_name']);
        $('#edit_ben_ifsc').html(data['bank_ifsc']);
        $('#edit_ben_accno').html(data['bank_code']);
        $('#ben_edit_modal').modal('show');
      });

      table.on('click','.ben_reject_button',function(){
        $tr = $(this).closest('tr');
        if(($tr).hasClass('child')){
          $tr = $tr.prev('parent');
        }
        var data = table.row($tr).data();
        $('#reject_beneficiary_id').val(data['ben_id']);
        $('#reject_ben_id').html(data['application_id']);
        $('#reject_ben_name').html(data['ben_name']);
        $('#reject_ben_father_name').html(data['benf_name']);
        $('#reject_ben_ifsc').html(data['bank_ifsc']);
        $('#reject_ben_accno').html(data['bank_code']);
        $('#ben_reject_modal').modal('show');
      });

  });
   function editApproveModal(id) {
            $('#first_name').val('');
            $('#middle_name').val('');
            $('#last_name').val('');
            $('#father_first_name').val('');
            $('#father_middle_name').val('');
            $('#father_ast_name').val('');
            $('#mobile_no').val('');
            $('#dob').val('');
            $('#hidden_age').val(0);
            $('#txt_age').val(0);
            $('#district_edit').val('');
            $('#urban_code_edit').val('');
            $('#block_edit').val('');
            $('#gp_ward_edit').val('');
            $('#bank_ifsc_code').val('');
            $('#name_of_bank').val('');
            $('#bank_branch').val('');
            $('#bank_account_number').val('');
            $('#applicant_id').val('');
       $('#edit_'+id).html('<img  src="{{ asset('images/ZKZg.gif')}}" width="25px" height="25px"/>');
    // $('#edit_'+id).html('<option value="">--Select --</option>');
      $.ajax({
        type: 'GET',
        url: '{{ url('getApplicant_Farmer')}}',
        data: {
          applicant_id: id,
          _token: '{{ csrf_token() }}',
        },
        success: function(data) {
           $('#edit_'+id).html('Edit&Approve');
          //console.log(data);
          if (data.return_status) {
           

              document.getElementById("dob").setAttribute("max", '1960-01-01');
            
            //alert(data.applicant_row['ben_age']);
           
            $('#first_name').val(data.applicant_row['first_name']);
            $('#middle_name').val(data.applicant_row['middle_name']);
            $('#last_name').val(data.applicant_row['last_name']);
            $('#father_first_name').val(data.applicant_row['father_first_name']);
            $('#father_middle_name').val(data.applicant_row['father_middle_name']);
            $('#father_last_name').val(data.applicant_row['father_last_name']);
            $('#mobile_no').val(data.applicant_row['mobile_no']);
            $('#dob').val(data.applicant_row['dob']);
            $('#hidden_age').val(data.applicant_row['ben_age']);
            $('#txt_age').val(data.applicant_row['ben_age']);
            $('#district_edit').val(data.applicant_row['district']).trigger('change');
            //alert(data.applicant_row['district']);
            $('#district_edit').val(data.applicant_row['district']);
            $('#urban_code_edit').val(data.applicant_row['urban_code']);
            var event = new Event('change');
            var element1 = document.getElementById('urban_code_edit');
            element1.dispatchEvent(event);
            $('#block_edit').val(data.applicant_row['block']);
            var element2 = document.getElementById('block_edit');
            element2.dispatchEvent(event);
            $('#gp_ward_edit').val(data.applicant_row['gp_ward']);
            $('#bank_ifsc_code').val(data.applicant_row['bank_ifsc_code']);
            var element3 = document.getElementById('bank_ifsc_code');
            var event2 = new Event('blur');
            element3.dispatchEvent(event2);
            $('#name_of_bank').val(data.applicant_row['name_of_bank']);
            $('#bank_branch').val(data.applicant_row['bank_branch']);
            $('#bank_account_number').val(data.applicant_row['bank_account_number']);
            $('#applicant_id').val(id);
            $("#ben_selective_edit_modal").modal();
            
             $("#dob").on('blur',function(){ 
         //alert('ok');
      var age_base = '2020-01-01';
      var today = new Date(age_base);
      var birthDate = new Date($('#dob').val());
      var diff_ms = today.getTime() - birthDate.getTime();
      var age_dt = new Date(diff_ms); 
      var age = Math.ceil(age_dt.getUTCFullYear() - 1970);
      if(isNaN(age)){
        age = 0;
      }
      $('#hidden_age').val(age); 
      $('#txt_age').val(age); 
     // alert($('#hidden_age').val());
    });
    
 

    $('.txtOnly').keypress(function (e) {
            var regex = new RegExp(/^[a-zA-Z\s]+$/);
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            else {
                e.preventDefault();
                return false;
            }
    });

   
  $(".NumOnly").keyup(function(event) {
              
        $(this).val($(this).val().replace(/[^\d].+/, ""));
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        }); 
        $('.special-char').keyup(function()
        {
          var yourInput = $(this).val();
          re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
          var isSplChar = re.test(yourInput);
          if(isSplChar)
          {
            var no_spl_char = yourInput.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
          }
        });
          } else {
            printMsg(data.return_msg, '0', 'errorDiv');
          }
        },
        error: function(ex) {
           $('#edit_'+id).html('Edit&Approve');
          alert('Something wrong..may be session timeout. please logout and then login again');
          //location.reload();
        }
      });
      
    }
function printMsg (msg,msgtype,divid) {
            $("#"+divid).find("ul").html('');
            $("#"+divid).css('display','block');
			if(msgtype=='0'){
				//alert('error');
				$("#"+divid).removeClass('alert-success');
				//$('.print-error-msg').removeClass('alert-warning');
				$("#"+divid).addClass('alert-warning');
			}
			else{
				$("#"+divid).removeClass('alert-warning');
				$("#"+divid).addClass('alert-success');
			}
			if(Array.isArray(msg)){
            $.each( msg, function( key, value ) {
                $("#"+divid).find("ul").append('<li>'+value+'</li>');
            });
			}
			else{
				$("#"+divid).find("ul").append('<li>'+msg+'</li>');
			}
  }
   function closeError(divId){
   $('#'+divId).hide();
  }
  </script>
