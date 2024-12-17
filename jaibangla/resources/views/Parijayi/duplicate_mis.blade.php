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
					
        <div class="col-md-12 text-center" id="loaderdiv" hidden>
          <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px"/>
        </div>  
        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
        <table id="example" class="display" cellspacing="0" width="100%">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <input type="hidden" name="district_code" id="district_code" value="{{ $district_code }}">
          <thead>
                <tr role="row">
                  <th width="8%" class="text-left">Beneficiary ID</th>
                  <th width="20%" class="text-left">Beneficiary Name</th>
                  <th width="14%" class="text-left">Fathers Name</th>
                  <th width="6%" class="text-left">DOB</th>     
                  <th width="4%" class="text-left">Gender</th>
                  <th width="16%" class="text-left">Mobile No</th>
                  <th width="16%" class="text-left">ID Type</th>
                  <th width="12%" class="text-left">ID Number</th>
                  <th width="4%" class="text-left">Status</th>       
                  <th>District</th>
                  <th>Block</th>
                  <th>Municipality</th>
                  <th>Address</th>
                  <th>PinCode</th>
              </tr>
          </thead>
          <tfoot>
              <tr>
                <th width="8%" class="text-left">Beneficiary ID</th>
                <th width="20%" class="text-left">Beneficiary Name</th>
                <th width="14%" class="text-left">Fathers Name</th>
                <th width="6%" class="text-left">DOB</th>     
                <th width="4%" class="text-left">Gender</th>
                <th width="16%" class="text-left">Mobile No</th>
                <th width="16%" class="text-left">ID Type</th>
                <th width="12%" class="text-left">ID Number</th>
                <th width="4%" class="text-left">Status</th>       
                <th>District</th>
                <th>Block</th>
                <th>Municipality</th>
                <th>Address</th>
                <th>PinCode</th> 
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
	
		@endsection
	



	<script src='{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}'></script>
  <script >
	
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
          url: "{{ url('parijayi_duplicate_getdata') }}",
          type: "POST",
          data:function(d){
              d.level1= "{{ $district_code }}",
              d.level2= "{{ $district_name}}",
              d.level1a = $('#level1adata').val(),
              d.level3=   $('#level3data').val(),
              d._token= "{{csrf_token()}}"
          }
        } ,
        "columns": [
                  { "data": "beneficiary_id","defaultContent":""},
                  { "data": "ben_name","defaultContent":"" },
                  { "data": "ben_father","defaultContent":"0" },
                  { "data": "dob","defaultContent":"" },
                  { "data": "gender","defaultContent":"0" },
                  { "data": "mobile_no","defaultContent":"0" },
                  { "data": "id_type","defaultContent":"" },                   
                  { "data": "id_number","defaultContent":"" },
                  { "data": "case_type","defaultContent":"0" },       
        				  { "data": "district_name","defaultContent":"" },
                  { "data": "block_name","defaultContent":"" },
                  { "data": "municipality_name","defaultContent":"" },
                  { "data": "address_line","defaultContent":"0" },
        				  { "data": "pincode","defaultContent":"0" },
              ], 
          "columnDefs": [
                  { targets: "_all","orderable": false, },
                  { targets: 0, "className": "text-center", },
				          { targets: 7, "className": "text-center", },
				          { targets: [9,10,11,12,13], "visible":false}
                ],         
        "buttons": [{
            extend: 'excel',
            exportOptions: {
                      columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13]
            },
            title: 'Sneher Paras Duplicate Beneficiaries List',
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
        }],
			});

  });

  </script>
