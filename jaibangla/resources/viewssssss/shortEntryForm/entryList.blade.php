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

  @extends('shortEntryForm.base')
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
         @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
                <div class="alert alert-success alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }} with Application ID: {{$id}}</strong>
                 

                </div>
          @endif
          	@if(!empty($error))
					<div class="alert alert-danger alert-block">
						<ul>
					
						<li><strong> {{ $error }}</strong></li>
						</ul>
					</div>
					@endif        
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
								<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
								
								<input type="hidden" name="_token" id="token1" value="{{ csrf_token() }}">
								<input type="hidden" id="level1data" name="level1data">
								<input type="hidden" id="level2data" name="level2data">
								<input type="hidden" id="level3data" name="level3data">
								<input type="hidden" id="level4data" name="level4data">
								<input type="hidden" id="level1adata" name="level1adata">
								<input type="hidden" id="level1bdata" name="level1bdata">
								<input type="hidden" id="level1cdata" name="level1cdata">
						</div>
					</div>          
				
					<div class="col-md-offset-3 col-md-3">
						
					<h4><span class="label label-primary">{{$report_type_name}}</span></h4>
			
					</div>
					<!-- <div class="col-md-offset-1 col-md-5 btn-group" role="group" >
						<button class="btn btn-success clsbulk_approve" id="bulk_approve" disabled>Approve Selected Beneficiaries</button>
					</div> -->
        <div class="col-md-12 text-center" id="loaderdiv" hidden>
          <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px"/>
        </div>  
        <br/> <br/> <br/>
       <div class="alert print-error-msg" style="display:none" id="errorDivMain">
        <button type="button" class="close"  aria-label="Close" onclick="closeError('errorDivMain')">
        <span aria-hidden="true">&times;</span></button><ul></ul>
        </div>
        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
        <table id="example" class="display" cellspacing="0" width="100%">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <input type="hidden" name="district_code" id="district_code" value="{{ $district_code }}">
          <thead>
                <tr role="row"> 
                  <th  class="text-left">Application ID</th>
                  <th class="text-left">Applicant Name</th>  
                  <th  class="text-left">Duare Sarkar Application Id</th>
                 
                  @if($is_urban==1)
                  <th  class="text-left">Block/ Municipality Name</th>
                  @endif
                  <th  class="text-left">GP/Ward Name</th>
				          <th >Action</th>  
				          
              </tr>
          </thead>
          <tfoot>
              <tr>
                <th width="12%" class="text-left">Application ID</th>
                  <th width="20%" class="text-left">Applicant Name</th> 
                  <th width="10%" class="text-left">Duare Sarkar Application Id</th>
                  
                   @if($is_urban==1)
                  <th width="10%" class="text-left">Block/ Municipality Name</th>
                   @endif
                  <th width="10%" class="text-left">GP/Ward Name</th>
				          <th width="13%">Action</th> 
              </tr>
          </tfoot>   
            
      </table>  
      <div class="row">
              
              <div class="col-sm-7">
                
              </div>
        </div>  

        </div>

      </div>
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
						<h4 class="modal-title">Reject Application</h4>
					</div>
					<div class="modal-body">
						<h4>Are you sure you want to reject the application details mentioned below?</h4><hr/>

						<table style="width:100%">
							<tr>
								<td style="width:50%;"><span class="item_header">Application Id:</span></td>
								<td><span class="item_value" id="reject_ben_id"></span></td>
							</tr>
							<tr>
								<td style="width:50%;"><span class="item_header">Beneficiary Name:</span></td>
								<td><span class="item_value" id="reject_ben_name"></span></td>
							</tr>
              <tr>
								<td style="width:50%;"><span class="item_header">Duare Sarkar Application Id :</span></td>
								<td><span class="item_value" id="reject_ben_ds_application_id"></span></td>
							</tr>
              <tr>
								<td colspan="2"><hr/></td>
							</tr>
							 <tr>
								<td style="width:50%;"><span class="item_header">Rejection Cause :</span></td>
								<td style="width:50%;">
               
                 <select class="form-control" name="rejection_cause" id="rejection_cause"  >
                    <option value="">--Select--</option>
                    @foreach(Config::get('constants.ds_rejection_cause') as $key=>$val)
                    <option value="{{$key}}">{{$val}}</option>
                    @endforeach
                  </select>
                 <span id="error_marital_status" class="text-danger"></span>
                </td>
							</tr>
							
						</table>
						<input type="hidden" id="reject_beneficiary_id"/>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" id="reject_Button">Reject</button>
            <img src="{{ asset('images/ZKZg.gif')}}" id="submit_loader" style="display:none;" width="50px" height="50px">
					</div>
				</div>
			</div>
		</div>
		<!-- End Reject Model -->
<div class="modal" tabindex="-1" role="dialog" id="rejection_cause">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Modal body text goes here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
   

   
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

   $("#submit_loader").hide();
    var base_url='{{ url('/') }}';

  
  $('#reject_Button').click(function(e){
    e.preventDefault();
    var application_id=$('#reject_beneficiary_id').val();
    var rejection_cause=$('#rejection_cause').val();
    if(rejection_cause==''){
     alert('Please Select Rejection Cause');
     $("#rejection_cause").focus();
    }
    else{
     $('#btn_'+application_id).hide();
     $('#reject_Button').hide();
    $("#submit_loader").show();
     $.ajax({
      type: 'post',
      dataType:'json',
      url: '{{ url('DuareFormReject') }}',
      data: {
        application_id: application_id,
        rejection_cause: rejection_cause,
        scheme: "{{ $scheme }}",
        _token: '{{ csrf_token() }}',
      },
      success: function (data) {
        if(data.return_status){
          $("#submit_loader").hide();
              $('#btn_'+application_id).show();
              $('#reject_Button').show();
          $('#example').DataTable().ajax.reload();
          $("#ben_reject_modal").modal('hide');
          $('#rejection_cause').val('');
          printMsg(data.return_msg,'1','errorDivMain');
          $("html, body").animate({ scrollTop: 0 }, "slow");
        }
        else{
              printMsg(data.return_msg,'0','errorDivMain');
              $("#submit_loader").hide();
              $('#btn_'+application_id).show();
              $('#reject_Button').show();
               $("#ben_reject_modal").modal('hide');
              $("html, body").animate({ scrollTop: 0 }, "slow");
        }
       
      },
      error: function (ex) {
         alert('Something Wrong..may be session time out..please login again.');
           window.location.href=base_url;
      }
    });
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
        "lengthMenu": [[20, 50, 80], [20, 50, 80]],
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
			url: "{{ url('shortEntryList') }}",
			type: "POST",
        data:function(d){
          d.level1= "{{ $district_code }}",
          d.level1a = $('#level1adata').val(),
          d.level3=   $('#level3data').val(),
          d._token= "{{csrf_token()}}",
          d.scheme = "{{ $scheme }}"
          d.pr1 = "{{ $pr1 }}"

			  },
        error: function (jqXHR, textStatus, errorThrown) {
           
           alert('Something Wrong..may be session time out..please login again.');
           window.location.href=base_url;
        }
		  } ,
        "columns": [
            { "data": "application_id","defaultContent":""},
            { "data": "ben_name","defaultContent":"" },
            { "data": "ds_registration_no","defaultContent":"" },
            @if($is_urban==1)
            { "data": "block_ulb_name","defaultContent":"" },
            @endif
            { "data": "gp_ward_name","defaultContent":"" },
            { "data": "action","defaultContent":"0" }      			 
          ], 
        
      
        "buttons": [
        {
		  extend: 'pdf',
		 
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
       $('#reject_beneficiary_id').val('');
        $tr = $(this).closest('tr');
        if(($tr).hasClass('child')){
          $tr = $tr.prev('parent');
        }
        var data = table.row($tr).data();
        $('#reject_beneficiary_id').val(data['id']);
        $('#rejection_cause').val('');
        $('#reject_ben_id').html(data['application_id']);
        $('#reject_ben_name').html(data['ben_name']);
        $('#reject_ben_ds_application_id').html(data['ds_registration_no']);
        $('#ben_reject_modal').modal('show');
      });
  
 
  });
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
