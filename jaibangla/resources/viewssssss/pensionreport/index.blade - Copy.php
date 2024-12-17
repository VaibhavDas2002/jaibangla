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
					<div id="example2_wrapper" class="col-md-12 dataTables_wrapper form-inline dt-bootstrap js-report-form">
						<div class="row" style="margin-bottom:1%">
							
						</div>
					</div>          
				
					<div class="col-md-offset-3 col-md-3">
						
					<h4><span class="label label-primary">{{$report_type_name}}</span></h4>
			
					</div>
				
        <div class="col-md-12 text-center" id="loaderdiv" hidden>
          <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px"/>
        </div>  
        @if($error_found==0)
        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
        <table id="example" class="display" cellspacing="0" width="100%">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <input type="hidden" name="district_code" id="district_code" value="{{ $district_code }}">
          <thead>
                <tr role="row"> 
                  <th width="12%" class="text-left">Application ID</th>
                  <th width="20%" class="text-left">Beneficiary Name</th>
                  <th width="5%" class="text-left">Age</th>   
                  <th width="10%" class="text-left">Gender</th>    
                  <th width="10%" class="text-left">Bank IFSC</th>
                  <th width="12%" class="text-left">Bank Account No</th>
                  <th width="12%" class="text-left">Block/ Municipality Name</th>
                  <!-- <th width="12%" class="text-left">Assembly Name</th> -->
				          <th width="12%">Action</th>  
				          
              </tr>
          </thead>
          <tfoot>
              <tr>
                <th width="12%" class="text-left">Application ID</th>
                  <th width="20%" class="text-left">Beneficiary Name</th>
                  <th width="5%" class="text-left">Age</th>     
                  <th width="8%" class="text-left">Gender</th>   
                  <th width="10%" class="text-left">Bank IFSC</th>
                  <th width="12%" class="text-left">Bank Account No</th>
                  <th width="10%" class="text-left">Block/ Municipality Name</th>
                  <!-- <th width="10%" class="text-left">Assembly Name</th> -->
				          <th width="13%">Action</th> 
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
      @endif
    <!--   </div> -->
      </section>
      <!-- /.content -->
    </div>

		<!-- Start Reject Model -->

		<div class="modal fade" id="ben_reject_modal" tabindex="-1">
			<div class="modal-dialog ">
				<div class="modal-content">
					<div class="modal-header btn-danger">
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

    <!-- Start Revert Model -->

		<div class="modal fade" id="ben_revert_modal" tabindex="-1">
			<div class="modal-dialog ">
				<div class="modal-content">
					<div class="modal-header btn-success">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title ">Revert Beneficiary Application</h4>
					</div>
					<div class="modal-body">
						<h4>Are you sure you want to revert the application back to <b>Verifier</b> level with the beneficiary details mentioned below?</h4><hr/>

						<table style="width:100%">
							<tr>
								<td style="width:30%;"><span class="item_header">Beneficiary Id:</span></td>
								<td><span class="item_value" id="revert_ben_id"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Beneficiary Name:</span></td>
								<td><span class="item_value" id="revert_ben_name"></span></td>
							</tr>
              <tr>
								<td><span class="item_header">Father's Name:</span></td>
								<td><span class="item_value" id="revert_ben_father_name"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Bank IFSC:</span></td>
								<td><span class="item_value" id="revert_ben_ifsc"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Account Number:     </span></td>
								<td><span class="item_value" id="revert_ben_accno"></span></td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
						</table>
						<input type="hidden" id="revert_beneficiary_id"/>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-success" id="revert_Button" data-dismiss="modal">Revert</button>
					</div>
				</div>
			</div>
		</div>
		<!-- End Revert Model -->

   
		@endsection
	



	<script src='{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}'></script>
  <script >


  function display_c(){
    var refresh=1000; // Refresh rate in milli seconds
    mytime=setTimeout('display_ct()',refresh)
  }

  function display_ct() {
    var x = new Date()
    document.getElementById('ct').innerHTML = x.toUTCString();
    display_c();
  } 
	
  $(document).ready(function(){ 
  display_ct();	
	 
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

  $('#location_type').change(function(){
    var location_type = $("#location_type").val();
    var district_code = "{{$district_code}}";
    $('#local_body').empty();
    $('#local_body').append('<option value="">--Please Select--</option>');

    if(location_type !=""){
      $.ajax({
        type: 'POST',
        url: '{{ url('singleStepBenLocationDetails') }}',
        dataType: 'json',
        data: {
          _token: '{{ csrf_token() }}',
          urban_rural: location_type,
          district_code: district_code,
        },
        beforeSend: function(){
          $('#loaderdiv').show();
        },
        success: function (datas) {
          $.each(datas, function (index, value) {
              $('#local_body').append('<option value="' + value.id + '">' + value.name + '</option>');
          });
        },
        complete: function(){
          $('#loaderdiv').hide();
        },
        error: function (ex) {
          $('#loaderdiv').hide();
        }
      });
    }
  });
  $('#reject_Button').click(function(e){
    e.preventDefault();

    $.ajax({
      type: 'POST',
      url: '{{ url('benReject-common') }}',
      data: {
        ben_id: $('#reject_beneficiary_id').val(),
        scheme: "{{ $scheme_id }}",
        _token: '{{ csrf_token() }}',
      },
      success: function (datas) {
        alert('Beneficiary with id '+$('#reject_beneficiary_id').val()+' rejected');
        $('#example').DataTable().ajax.reload();
      },
      error: function (ex) {

      }
    });
  });
  $('#revert_Button').click(function(e){
    e.preventDefault();

    $.ajax({
      type: 'POST',
      url: '{{ url('benRevert-common') }}',
      data: {
        ben_id: $('#revert_beneficiary_id').val(),
        scheme: "{{ $scheme_id }}",
        _token: '{{ csrf_token() }}',
      },
      success: function (datas) {
        alert('Beneficiary with id '+$('#revert_beneficiary_id').val()+' reverted');
        $('#example').DataTable().ajax.reload();
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
        "scrollX": true,
        "ordering":false,
        "language": {
          "processing": '<img src="{{ asset('images/ZKZg.gif') }}" width="150px" height="150px"/>'
        },
        "ajax": 
        {
			url: "{{ url('application-list-common') }}",
			type: "POST",
        data:function(d){
          d.level1= "{{ $district_code }}",
          d.level1a = $('#level1adata').val(),
          d.level3=   $('#level3data').val(),
          d._token= "{{csrf_token()}}",
          d.scheme = "{{ $scheme_id }}",
          d.type = "{{$type}}"
			  }
		  } ,
        "columns": [
            { "data": "application_id","defaultContent":""},
            { "data": "ben_name","defaultContent":"" },
            { "data": "ben_age","defaultContent":"0" },
            { "data": "gender","defaultContent":"0" },
            { "data": "bank_ifsc","defaultContent":"" },
            { "data": "bank_code","defaultContent":"0" },
            { "data": "block_ulb_name","defaultContent":"" },
            // { "data": "assembly_name","defaultContent":"" },
            { "data": "action","defaultContent":"0" }      			 
          ], 
          "columnDefs": [
            { targets: "_all","orderable": false, },
            { targets: 0, "className": "text-center", },
				    { targets: 7, "className": "text-left", },
          ],         
      
        "buttons": [
        {
		  extend: 'pdf',
		  exportOptions: {
                columns: [0,1,2,3,4,5,6]
			},	
          title: 'Beneficiaries List for scheme: "{{$scheme_name}}"',
          messageTop: function () {
            var message = "{{$report_type_name}} generated on: <?php echo date('d/m/Y'); ?>";            
            return message;
          },
          footer: true,
          pageSize:'A4',
          orientation: 'portrait',
          pageMargins: [ 40, 60, 40, 60 ],
        },
        {
		  extend: 'excel',
		  exportOptions: {
        columns: [0,1,2,3,4,5,6]
			},
          title: 'Beneficiaries List for scheme: "{{$scheme_name}}"',
          messageTop: function () {
            var message = "{{$report_type_name}} generated on: <?php echo date('d/m/Y'); ?>";            
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
        columns: [0,1,2,3,4,5,6]
			},
          title: 'Beneficiaries List for scheme: "{{$scheme_name}}"',
          messageTop: function () {
            var message = "{{$report_type_name}} generated on: <?php echo date('d/m/Y'); ?>";            
            return message;
          },
          footer: true,
          pageSize:'A4',
          //orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
        },
        ],
      } );
      table.on('click','.ben_reject_button',function(){
        $tr = $(this).closest('tr');
        if(($tr).hasClass('child')){
          $tr = $tr.prev('parent');
        }
        var data = table.row($tr).data();
        $('#reject_beneficiary_id').val(data['id']);
        $('#reject_ben_id').html(data['application_id']);
        $('#reject_ben_name').html(data['ben_name']);
        $('#reject_ben_father_name').html(data['benf_name']);
        $('#reject_ben_ifsc').html(data['bank_ifsc']);
        $('#reject_ben_accno').html(data['bank_code']);
        $('#ben_reject_modal').modal('show');
      });
      table.on('click','.ben_revert_button',function(){
        $tr = $(this).closest('tr');
        if(($tr).hasClass('child')){
          $tr = $tr.prev('parent');
        }
        var data = table.row($tr).data();
        $('#revert_beneficiary_id').val(data['id']);
        $('#revert_ben_id').html(data['application_id']);
        $('#revert_ben_name').html(data['ben_name']);
        $('#revert_ben_father_name').html(data['benf_name']);
        $('#revert_ben_ifsc').html(data['bank_ifsc']);
        $('#revert_ben_accno').html(data['bank_code']);
        $('#ben_revert_modal').modal('show');
      });
  });

  </script>
