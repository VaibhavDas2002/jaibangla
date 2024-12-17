  <style type="text/css">   
    .full-width{
      width:100%!important;
    }
  .bg-blue{
    background-image: linear-gradient(to right top, #0073b7, #0086c0, #0097c5, #00a8c6, #00b8c4)!important;
  }
  .bg-red{
    /*background-image: linear-gradient(to right bottom, #dd4b39, #db4546, #d74052, #d13d5e, #c93d68)!important;*/
  /* background-image: linear-gradient(to right bottom, #dd4b39, #e65347, #ef5b55, #f76463, #ff6d71)!important;*/
  background-image: linear-gradient(to right bottom, #dd4b39, #ec6f65, #d21a13, #de0d0b, #f3060d)!important;
  }
  .bg-yellow{
    background-image: linear-gradient(to right bottom, #dd4b39, #e65f31, #ed7328, #f1881e, #f39c12)!important;
  }
  .bg-green{
  /*background-image: linear-gradient(to right bottom, #00837d, #008d7b, #009674, #009e69, #00a65a)!important;*/
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
    /*//width:100%!important;*/
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
  } 
  .lot_block{
	  margin-bottom: 5px;
  }
  </style>
  @extends('Parijayi.base')
  @section('action-content')

      <!-- Main content -->
      <section class="content">
		  
		<div class="box">
			<div class="box-header">
			  <div class="row">
				  	{{-- Lot 1 --}}
					<div class=" col-sm-2">
						<ul class="list-group list-group-flush lot_block">
							<li class="list-group-item label label-success">
								<h4 style=" font-weight: bold">LOT #3546</h4>
							</li>
							<li class="list-group-item">
								<h5><span style="color: firebrick; font-weight: bold">Lot Size: </span><span class=" badge">5678 </span>
								{{-- <h5><span style="color: darkorange; font-weight: bold">Click below button</span></h5> --}}
							</li>
							<li class="list-group-item text-center"><button style="width: 100%;" class=" btn btn-warning"> Process LOT</button></li>
						</ul>
					</div>
					{{-- Lot 2 --}}
					<div class="col-md-offset-1 col-sm-2">
						<ul class="list-group list-group-flush lot_block">
							<li class="list-group-item label label-success">
								{{-- <h5>LOT #3</h5> --}}
								<h4 style=" font-weight: bold">New LOT</h4>
							</li>
							<li class="list-group-item">
								{{-- Lot Size: <span class=" badge">5678 </span> --}}
								<h5><span style="color: darkorange; font-weight: bold">Click below button</span></h5>
							</li>
							<li class="list-group-item text-center"><button style="width: 100%;" class=" btn btn-primary"> Generate LOT</button></li>
						</ul>
					</div>
					{{-- Lot 3 --}}
					<div class="col-md-offset-1 col-sm-2">
						<ul class="list-group list-group-flush lot_block">
							<li class="list-group-item label label-success">
								{{-- <h5>LOT #3</h5> --}}
								<h4 style=" font-weight: bold">New LOT</h4>
							</li>
							<li class="list-group-item">
								{{-- Lot Size: <span class=" badge">5678 </span> --}}
								<h5><span style="color: darkorange; font-weight: bold">Click below button</span></h5>
							</li>
							<li class="list-group-item text-center"><button style="width: 100%;" class=" btn btn-primary"> Generate LOT</button></li>
						</ul>
					</div>
					{{-- Lot 4 --}}
					<div class="col-md-offset-1 col-sm-2 lot_block">
						<ul class="list-group list-group-flush">
							<li class="list-group-item label label-success">
								{{-- <h5>LOT #3</h5> --}}
								<h4 style=" font-weight: bold">New LOT</h4>
							</li>
							<li class="list-group-item">
								{{-- Lot Size: <span class=" badge">5678 </span> --}}
								<h5><span style="color: darkorange; font-weight: bold">Click below button</span></h5>
							</li>
							<li class="list-group-item text-center"><button style="width: 100%;" class=" btn btn-primary"> Generate LOT</button></li>
						</ul>
					</div>
			  </div>
			</div>
			<div class="box-body" style="background-color: lightgray">
				<div class="row">
					<div class="col-md-12">
						<h4><u>Filter Parameters: </u></h4>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						District: 
						<select id="district_id">
							<option id="">All</option>
						</select>
					</div>
					<div class="col-md-3">
						Rural/Urban:  
						<select id="urban_rural">
							<option id="">All</option>
						</select>
					</div>
					<div class="col-md-5">
						Block/Municipality:  
						<select id="localbody">
							<option id="">All</option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-7">
						Date Range (From Date:  
						<input type="text" id="from_date"/>
						Upto Date:   
						<input type="text" id="upto_date">
						)
					</div>
					<div class="col-md-5">
						Beneficiary Id: <input type="text" id="ben_id"/>
					</div>
				</div>
				<div class="row"><div class="col-md-12"><button class="btn btn-primary" id="filter">Filter Records</button></div></div>
			</div>
			<div class="col-md-12" id="reportbody" style="margin-top: 2%;">
				<table id="example" class="display" cellspacing="0" width="100%">
				  <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
				  <thead>
						<tr role="row">
						  <td width="3%" class="text-right">
							<input type="checkbox" id="select_all" name="select_all" onchange="document.getElementById('bulk_approve').disabled = !this.checked;">
						  </td>
						  <th width="8%" class="text-left">Beneficiary ID</th>
						  <th width="26%" class="text-left">Beneficiary Name</th>
						  <th width="9%" class="text-left">DOB</th>     
						  <th width="4%" class="text-left">Gender</th>
						  <th width="16%" class="text-left">Mobile No</th>
						  <th width="16%" class="text-left">Aadhar No</th>
						  <th width="4%" class="text-left">Status</th>     
						  <th width="14%">Action</th>   
						</tr>
				  </thead>
				  <tfoot>
					  <tr>
						<th width="3%" class="text-left">
						  <input type="checkbox" id="select_all" name="select_all">
						</th>
						<th width="8%" class="text-left">Beneficiary ID</th>
						<th width="26%" class="text-left">Beneficiary Name</th>
						<th width="9%" class="text-left">DOB</th>     
						<th width="4%" class="text-left">Gender</th>
						<th width="16%" class="text-left">Mobile No</th>
						<th width="16%" class="text-left">Aadhar No</th>
						<th width="4%" class="text-left">Status</th>     
						<th width="14%">Action</th>
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
		
      </section>  

	
  @endsection
	



	<script src='{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}'></script>
<!---data table end--->
<!-- Bootstrap 3.3.2 JS -->
<!-- <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script> -->
<!-----site.js-------------------->
<!-- <script src="{{ URL::asset('js/site.js') }}"></script>
 -->
<!-------------------------------->

<!-- AdminLTE App -->
<!-- <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script> -->


  	<script>
	
		$(document).ready(function(){
			// $('#print_Button').click(function(){
			// 	var panel = document.getElementById("printArea");
			// 	var printWindow = window.open('', '', '');
			// 	printWindow.document.write('<html><head><title>Print Invoice</title>');
				
			// 	// Make sure the relative URL to the stylesheet works:
			// 	printWindow.document.write('<base href="' + location.origin + location.pathname + '">');
				
			// 	// Add the stylesheet link and inline styles to the new document:
			// 	printWindow.document.write('<link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />');
			// 	printWindow.document.write('<style type="text/css">.style1{width: 100%;}</style>');
				
			// 	printWindow.document.write('</head><body >');
			// 	printWindow.document.write(panel.innerHTML);
			// 	printWindow.document.write('</body></html>');
			// 	printWindow.document.close();
			// 	setTimeout(function () {
			// 		printWindow.print();
			// 	}, 500);
			// 	return false;
			// });
		});


  </script>
