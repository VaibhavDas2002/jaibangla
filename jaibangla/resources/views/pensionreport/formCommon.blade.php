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
   #searchbtn
  {
   margin:20px auto;
  }
  </style>

  @extends('pensionreport.base')
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
				         
				
			
				
        <div class="col-md-12 text-center" id="loaderdiv" hidden>
          <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px"/>
        </div>  
        @if($error_found==0)
          <form method="get" id="appList" action="{{url('ajaxapplicantList')}}"  class="submit-once" >
         <input type="hidden" name="scheme_code" id="scheme_code" value="{{$scheme_id}}">
         <input type="hidden" name="application_type" id="application_type" value="{{$type}}">
         <input type="hidden" name="export_excel" id="export_excel" value="">
          @if($location_arr['rural_urban']['is_visible']==FALSE)
          <input type="hidden" name="rural_urban" id="rural_urban" value="{{$location_arr['rural_urban']['code']}}">
          @endif
          @if($location_arr['district']['is_visible']==FALSE)
          <input type="hidden" name="district_code" id="district_code" value="{{$location_arr['district']['code']}}">
          @endif
        <div class="row">
        <div class="form-group col-md-4" id="district_div" style="display:{{ $location_arr['district']['is_visible']==TRUE ? '' : 'none' }}">
             <label class="required-field">District</label>
           <select  class="form-control" name="district_code" id="district_code" tabindex="3">
                                                                                            
           <option value="">--ALL--</option>
             @foreach($location_arr['district']['array'] as $key=>$val)
              <option value="{{$key}}" >{{$val}}</option>
              @endforeach                                               
          </select>                                                                
          <span id="error_district_code" class="text-danger"></span>
        </div>
         <div class="form-group col-md-4" id="rural_urban_div"  style="display:{{ $location_arr['rural_urban']['is_visible']==TRUE ? '' : 'none' }}">
             <label class="">Rural/Urban</label>
           <select  class="form-control client-js-urban" name="rural_urban" id="rural_urban" tabindex="4">
                                                                                            
           <option value="">--ALL--</option>
            @foreach($rural_urban_arr as $key=>$val)
                                    <option value="{{$key}}" >{{$val}}</option>
            @endforeach                                                
          </select>                                                                
          <span id="error_district_code" class="text-danger"></span>
        </div> 
          <div class="form-group col-md-4" id="block_subDiv_div" style="display:{{ $location_arr['blocksubDiv']['is_visible']==TRUE ? '' : 'none' }}">
             <label class="" id="blkSubdiv">Block/Sub Division</label>
           <select class="form-control client-js-localbody" name="block_subDiv" id="block_subDiv" tabindex="5" >
                <option value="">--ALL--</option>                                                  
                 @foreach($location_arr['blocksubDiv']['array'] as $key=>$val)
                 <option value="{{$key}}" >{{$val}}</option>
                 @endforeach                                            
          </select>                                                                
          <span id="error_block_munc_corp_code" class="text-danger"></span>
        </div> 
       <div class="form-group col-md-4" id="munc_corp_div" style="display:{{ $location_arr['munccorp']['is_visible']==TRUE ? '' : 'none' }}">
             <label class="">Municipality</label>
           <select class="form-control client-js-munc" name="munc_corp_code" id="munc_corp_code" tabindex="6" >
                <option value="">--ALL--</option>                                                 
                  @foreach($location_arr['munccorp']['array'] as $key=>$val)
                 <option value="{{$key}}" >{{$val}}</option>
                 @endforeach                                    
          </select>                                                                
          <span id="error_munc_corp_code" class="text-danger"></span>
        </div> 
      <div class="form-group col-md-4" id="gp_ward_div" style="display:{{ $location_arr['gpward']['is_visible']==TRUE ? '' : 'none' }}">
             <label class="" id="gsWard">GP/Ward</label>
           <select class="form-control client-js-gpward" name="gp_ward_code" id="gp_ward_code" tabindex="7" >
                <option value="">--ALL--</option>                                                 
                  @foreach($location_arr['gpward']['array'] as $key=>$val)
                 <option value="{{$key}}" >{{$val}}</option>
                 @endforeach                                         
          </select>                                                                
          <span id="error_gp_ward_code" class="text-danger"></span>
        </div> 
           <div class="form-group col-md-4">
                 <label class="">From Date</label>
                 @php
                 $max_from = $base_date; // Or can put $today = date ("Y-m-d");
                 $min_from = $c_date;
                 @endphp
                 <input type="date" name="from_date" id="from_date" class="form-control"  tabindex="21"  min="{{$max_from}}"  max="{{$min_from}}"/>
                 <span id="error_from_date" class="text-danger"></span>

        </div>
        <div class="form-group col-md-4">
                 <label class="">To Date</label>
                @php
                 $max_to = $c_date; // Or can put $today = date ("Y-m-d");
                 $min_to = $base_date;
                 @endphp
                  <input type="date" name="to_date" id="to_date" class="form-control"  tabindex="26" min="{{$min_to}}" max="{{$max_to}}" />
                 <span id="error_to_date" class="text-danger"></span>
                

       </div>
        <div class="form-group col-md-4"  id="search_div" >
        <button type="button" class="btn btn-primary btn-lg" id="searchbtn">Search</button> 
        </div>  
        </div>
        </form>
        <div class="row pull-right">
         <div class="col-md-6 btn-group" id="btn-export-excel" style="display:none;">
                    <button class="btn btn-info" id="export-excel-btn">Export All Data to Excel</button>
				  </div>
       
     </div>
        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
        <table id="example" class="display" cellspacing="0" width="100%">
          <thead>
                <tr role="row"> 
                  <th width="12%" class="text-left">Application ID</th>
                  <th width="20%" class="text-left">Beneficiary Name</th>  
                  <th width="12%" class="text-left">Block/ Municipality Name</th>
                  <th width="12%" class="text-left">GP/ Ward Name</th>
                  <th width="10%" class="text-left">Bank IFSC</th>
                  <th width="12%" class="text-left">Bank Account No</th>
				          <th width="12%">Action</th>  
				          
              </tr>
          </thead>
          <tfoot>
              <tr>
                <th width="12%" class="text-left">Application ID</th>
                <th width="20%" class="text-left">Beneficiary Name</th>
                <th width="12%" class="text-left">Block/ Municipality Name</th>
                <th width="12%" class="text-left">GP/ Ward Name</th>
                <th width="10%" class="text-left">Bank IFSC</th>
                <th width="12%" class="text-left">Bank Account No</th>
				        <th width="12%">Action</th> 
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
      @else
      	<div class="alert alert-danger alert-block">
        {{$error_msg}}  
        </div>
      @endif
    <!--   </div> -->
      </section>
      <!-- /.content -->
    </div>

		<!-- Start Reject Model -->


	
		<!-- End Revert Model -->
		@endsection
  
