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

  @extends('location_change.base')
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
          @if ( ($message = Session::get('success')))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
        
              </div>
          @endif
          @if ( ($error = Session::get('error')))
              <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $error }}</strong>
        
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
          	<div class="row" style="margin-bottom:1%">
             
          
            <div class="col-md-2">
            <label class="control-label">Rural/Urban </label>
            <select name="rural_urbanid" id="rural_urbanid" class="form-control">
                                        <option value="">-----All----</option>
                                        @foreach (Config::get('constants.rural_urban') as $key=>$value)
                                        <option value="{{$key}}"> {{$value}}</option>
                                        @endforeach
                </select>

          </div>
             
              
        <div class="col-md-3">
                                    <label class="control-label" id="blk_sub_txt">Block/Subdivision</label>
                                    <select name="urban_body_code" id="urban_body_code" class="form-control">
                                        <option value="">-----All----</option>

              </select>

      </div>
           
      <div class="col-md-3">
                                    <label class="control-label" id="">GP/WARD</label>
                                    <select name="gp_ward_code" id="gp_ward_code" class="form-control">
                                        <option value="">-----All----</option>

              </select>

      </div> 
             
    
           
            <div class="col-md-4" style="margin-top: 28px;">
                  <label class=" control-label">&nbsp; </label>

              <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <button type="button" name="reset" id="reset" class="btn btn-warning">Reset</button>

            </div>
            
         </div>
          
          <div class="row">
					<div class="col-md-offset-3 col-md-3">
						
					<h4><span class="label label-primary">{{$report_type_name}}</span></h4>
          </div>
         
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
                  <th width="12%" class="text-left">created_by_local_body_code_name</th>
                  <th width="12%" class="text-left">Beneficiary ID</th>
                  <th width="20%" class="text-left">Beneficiary Name</th>
                  <th width="12%" class="text-left">Mobile No</th>
                  <th width="10%" class="text-left">Bank IFSC</th>
                  <th width="12%" class="text-left">Bank Account No</th>
                  <th width="12%" class="text-left">Block Name</th>
                  <th width="12%" class="text-left">GP/Ward Name</th>
				          <th width="12%">Action</th>  
				          
              </tr>
          </thead>
          <tfoot>
              <tr>
              <th width="12%" class="text-left">created_by_local_body_code_name</th>
                <th width="12%" class="text-left">Beneficiary ID</th>
                  <th width="20%" class="text-left">Beneficiary Name</th>
                  <th width="10%" class="text-left">Mobile No</th>
                  <th width="10%" class="text-left">Bank IFSC</th>
                  <th width="12%" class="text-left">Bank Account No</th>
                  <th width="10%" class="text-left">Block Name</th>
                  <th width="12%" class="text-left">GP/Ward Name</th>
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
    <!--   </div> -->
      </section>
      <!-- /.content -->
    </div>

	  		<!-- Start View Model -->

		<div class="modal fade" id="ben_view_modal" tabindex="-1">
			<div class="modal-dialog ">
				<div class="modal-content">
        <form method="POST" role="form" id="modal_form" action="{{route('location_change_post') }}">
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme}}">
					<div class="modal-header btn-danger">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Change Block</h4>
					</div>
					<div class="modal-body">
						
          <span id="error_same" class="text-danger"></span><br/>
						<table style="width:100%">
							<tr>
								<td style="width:30%;"><span class="item_header">Beneficiary Id:</span></td>
								<td><span class="item_value" id="modal_ben_id"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Beneficiary Name:</span></td>
								<td><span class="item_value" id="modal_ben_name"></span></td>
							</tr>
              <tr>
								<td><span class="item_header">Existing Block:</span></td>
								<td><span class="item_value" id="modal_pre_block_subdiv"></span></td>
							</tr>
						  
              <tr>
								<td><span class="item_header required-field">New Block</span></td>
								<td>  
                  <select name="new_block_ulb_code" id="modal_block_ulb_code" class="form-control">
                  <option value="">-----Select New Block----</option>
                  @foreach ($map_block_list as $block_item)
                  <option value="{{$block_item->block_code}}"> {{$block_item->block_name}}</option>
                  @endforeach
                </select>  
                <span id="error_modal_block_ulb_code" class="text-danger"></span>
                <td>                
							</tr>
              
					
							
						</table>
						<input type="hidden" id="modal_beneficiary_id" name="beneficiary_id"/>
            <input type="hidden" id="pre_block_subdiv"/>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-danger" id="change_button">Change</button>
            <button type="button"  id="submitting" value="Submit" class="btn btn-success success btn-lg" disabled >Submitting please wait</button>

					</div>
         </form>
				</div>
			</div>
		</div>
		<!-- End View Model -->


  

   
		@endsection
	



	<script src='{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}'></script>
 <script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
  <script >
  $(document).ready(function(){ 
   $(".dataTables_scrollHeadInner").css({"width":"100%"});
   $("#submitting").hide();
   $(".table ").css({"width":"100%"});
   var allowded_blk_ulb=<?php echo json_encode($allowded_blk_ulb); ?>;
   //console.log(allowded_blk_ulb);
   var base_url='{{ url('/') }}';  
   var sessiontimeoutmessage='{{$sessiontimeoutmessage}}';
   $('#modal_form #modal_beneficiary_id').val('');
   $('#modal_form #pre_block_subdiv').val('');
 
    $('#rural_urbanid').change(function() {
          var rural_urbanid=$(this).val();
          if(rural_urbanid!=''){
            $('#urban_body_code').html('<option value="">--All --</option>');
            $('#block_ulb_code').html('<option value="">--All --</option>');
            $('#gp_ward_code').html('<option value="">--All --</option>');

            select_district_code= $('#district_code').val();
            //console.log(select_district_code);
            var htmlOption='<option value="">--All--</option>';
            if(rural_urbanid==1){
                $("#blk_sub_txt").text('Subdivision');
                $.each(subDistricts, function (key, value) {
                    if((value.district_code==select_district_code && allowded_blk_ulb.includes(value.id))){
                        htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                    }
                });
            }
            else if(rural_urbanid==2){
              $("#blk_sub_txt").text('Block');
              //console.log(select_district_code);
              $.each(blocks, function (key, value) {
                    if((value.district_code==select_district_code && allowded_blk_ulb.includes(value.id))){
                        htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                    }
                });
            }
            $('#urban_body_code').html(htmlOption);
          }
          else{
              $("#blk_sub_txt").text('Block/Subdivision');
              $("#gp_ward_txt").text('GP/Ward');
              $('#urban_body_code').html('<option value="">--All --</option>');
              $('#block_ulb_code').html('<option value="">--All --</option>');
              $('#gp_ward_code').html('<option value="">--All --</option>');
          }     
      });

      $('#urban_body_code').change(function() {
          var block_code=$(this).val();
          select_district_code= $('#district_code').val();
          var htmlOption='<option value="">--Plese Select--</option>';
          $.each(gps, function (key, value) {
                if((value.district_code==select_district_code) && (value.block_code==block_code)){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
          });
          //console.log(htmlOption);
          $('#gp_ward_code').html(htmlOption);
        
      });
      $("#select_all").click(function(){
        if(this.checked){
            $('.checkboxall').each(function(){
                $(".checkboxall").prop('checked', true);
            })
        }else{
            $('.checkboxall').each(function(){
                $(".checkboxall").prop('checked', false);
            })
        }
      });
 

	
  $('#filter').click(function(){
      table.clear().draw();
      table.ajax.reload();
  
  });
  $('#reset').click(function() {
     location.reload();
  });
  var table=$('#example').DataTable( {
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
			     url: "{{ url('location_change') }}",
		  	   type: "GET",
           data:function(d){
              d._token= "{{csrf_token()}}",
              d.scheme_id = "{{ $scheme }}",
              d.rural_urbanid= $("#rural_urbanid").val(),
              d.urban_body_code= $("#urban_body_code").val(),
              d.gp_ward_code= $("#gp_ward_code").val()
			     },
           error:function (xhr, ajaxOptions, thrownError) {
            alert(sessiontimeoutmessage);
            window.location.href=base_url;
            //$("#msgModal").modal('show');
           }
		  }, 
      "columns": [
            { "data": "created_by_local_body_code_name","defaultContent":""},
            { "data": "beneficiary_id","defaultContent":""},
            { "data": "ben_name","defaultContent":"" },
            { "data": "mobile_no","defaultContent":"" },
            { "data": "bank_ifsc","defaultContent":"" },
            { "data": "bank_code","defaultContent":"" },
            { "data": "created_by_local_body_code_name","defaultContent":"" },
            { "data": "gp_ward_name","defaultContent":"" },
            { "data": "action","defaultContent":"0" }      			 
          ], 
    "columnDefs": [
            { targets: "_all","orderable": false,"className": "text-center", },
				    { targets: 0, "visible": false, },
      ], 
      } );
      table.on('click','.ben_view_button',function(){
        $('#modal_form #modal_beneficiary_id').val('');
        $('#modal_form #pre_block_subdiv').val('');
          $tr = $(this).closest('tr');
          if(($tr).hasClass('child')){
            $tr = $tr.prev('parent');
          }
          var data = table.row($tr).data();
          //alert(data['beneficiary_id']);
          $('#modal_form #modal_beneficiary_id').val(data['beneficiary_id']);
          $('#modal_ben_id').html(data['beneficiary_id']);
          $('#modal_ben_name').html(data['ben_name']);
          $('#modal_pre_block_subdiv').html(data['created_by_local_body_code_name']);
          $('#modal_form #pre_block_subdiv').val(data['created_by_local_body_code_name']);
          $('#ben_view_modal').modal('show');
      });
      $('#change_button').on('click',function(){
        var error_modal_block_ulb_code='';
        var error_modal_gp_ward_code='';
        if($.trim($('#modal_block_ulb_code').val()).length == 0)
        {
           error_modal_block_ulb_code = 'New Block is required';
           $('#error_modal_block_ulb_code').text(error_modal_block_ulb_code);
           $('#modal_block_ulb_code').addClass('has-error');
        }
        else
        {
            error_modal_block_ulb_code = '';
            $('#error_modal_block_ulb_code').text(error_modal_block_ulb_code);
            $('#modal_block_ulb_code').removeClass('has-error');
        }
        
        if( error_modal_block_ulb_code == '')
        {
              var pre_block_subdiv=$('#modal_form #pre_block_subdiv').val();
              if($.trim($("#modal_block_ulb_code option:selected" ).text())==$.trim(pre_block_subdiv)){
                $('#error_same').text('You Have Choose Same Block.Please Try Different');
                return false;
              }
              else{
                    $('#error_same').text('');
                    //$('#modal_form #modal_beneficiary_id').val('');
                    $('#modal_form #pre_block_subdiv').val('');
                    $('#modal_ben_name').html(data['ben_name']);
                    $('#modal_pre_block_subdiv').html('');
                    $('#modal_pre_gp_ward').html('');
                    $("#change_button").hide();
                    $("#submitting").show();
              }
        }
        else{
          return false;
        }
      });
  });
  function controlCheckBox(){
    var anyBoxesChecked = false;
    $(' input[type="checkbox"]').each(function() {
      if ($(this).is(":checked")) {
        anyBoxesChecked = true;
      }
    });
    if (anyBoxesChecked == true) {
      document.getElementById('bulk_approve').disabled = false;
      // document.getElementById('bulk_blkchange').disabled = false;
    } else{
      document.getElementById('bulk_approve').disabled = true;
      // document.getElementById('bulk_blkchange').disabled = true;
    }
  }
  </script>
