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
					<div class="alert alert-warning">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<ul>
						@foreach($errors->all() as $error)
						<li><strong> {{ $error }}</strong></li>
						@endforeach
						</ul>
					</div>
					@endif        
				   <div class="col-md-6">         
              <div id="example2_wrapper" class="col-md-12 dataTables_wrapper form-inline dt-bootstrap js-report-form">
                
                <form method="post" action="{{url('import_bank_response')}}" enctype="multipart/form-data">
                  {{csrf_field()}}
                  <div class="form-group">
                    <table class="table">
                      <tr>
                          <td width="40%" align="right">
                            <label>Upload Response File: </label>
                          </td>
                          <td width="30">
                            <input type="file" name="select_file" />
                          </td>
                          <td width="30%" align="left" >
                            <input type="submit" name="upload" class="btn btn-primary" value="upload">
                          </td>

                      </tr>
                      <tr>
                        <td width="40%" align="right"></td>
                        <td width="20"><span class="text-muted" style="font-size: 15px; font-weight: bold;">File type .xls, .xlsx only</span></td>
                        <td width="50%" align="left"></td>
                      </tr>
                    </table>
                  </div>

                </form>
                <br>  
              </div>          
            
              <div class="col-md-7 form-group">
                <div class="input-group col-md-12">
                  <span class="label-primary"> Lot Number: </span>
                  <select class="form-control select2"  name="level3" id='level3'>
                    <option value="">--Select--</option>
                    @foreach ($lot_nos as $lotno)
                    <option value="{{$lotno}}">{{$lotno}}</option>    
                    @endforeach
                  </select>
                  <button type="button" name="filter" id="filter" class="btn btn-warning">Filter Records</button>
                </div>
              </div>
            </div> 
            <div class="col-md-6">  
              <h4 style="color: red"><u>Guidelines:</u><h4>
                <ul>
                  <li>File Name must be of Format <b>'Response-<code>&ltLOTNUMBER&gt.&ltxls/xlsx&gt</code>'</b> [e.g. Response-SP000001.xlsx]</li>

                  <li>File Should contain <b>Single Sheet</b> only and data in prescribed format</li>

                  <li>Response file to be uploaded once per lot</li>

                  <li>Once uploaded successfully, select <b>Lot Number</b> from <b>dropdown</b> and <b>Filter Records</b></li>

                  <li>Review response data in table and click on <b>Process Response</b></li>

                  <li>Once completing <b>Process Response</b>, that LOT will not be available for any further modification</li>
                </ul>
            </div>       
          </div>  	
        <div class="col-md-7"></div>
        <div class="col-md-12 text-center" id="loaderdiv" hidden>
          <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px"/>
        </div>  
        <div class="col-md-12">
          <h4 style="color: red">* Please Select <b>LOT</b> number for getting bank Response</h4>
        </div>
        <div class="col-md-2 col-md-offset-5">
          <button class="btn btn-primary" id="processLotBtn">Process Response</button>
        </div> 
        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
        
          <table id="example" class="display" style="width:100%">
            <thead>
            <tr>
              <th>Lot Number</th>
              <th>SL No.</th>
              <th>Sequence No.</th>
              <th>Transaction Ref</th>
              <th>Amount</th>
              <th>Value Date</th>
              <th>Sending Branch IFSC</th>
              <th>Sender A/c Type</th>
              <th>Sender A/c No</th>
              <th>Sender A/c Name</th>
              <th>Benf. Branch</th>
              <th>Benf A/c Type</th>
              <th>Benf. A/c No</th>
              <th>Benf. A/c Name</th>
              <th>Txn. Status</th>
              <th>Originator of Remittance</th>
              <th>Sender To Receiver Information</th>
              <th>Reason Code</th>
              <th>Reason</th>            
            </tr>
            </thead>
           
            <tfoot>
              <tr>
                <th>Lot Number</th>
                <th>SL No.</th>
                <th>Sequence No.</th>
                <th>Transaction Ref</th>
                <th>Amount</th>
                <th>Value Date</th>
                <th>Sending Branch IFSC</th>
                <th>Sender A/c Type</th>
                <th>Sender A/c No</th>
                <th>Sender A/c Name</th>
                <th>Benf. Branch</th>
                <th>Benf A/c Type</th>
                <th>Benf. A/c No</th>
                <th>Benf. A/c Name</th>
                <th>Txn. Status</th>
                <th>Originator of Remittance</th>
                <th>Sender To Receiver Information</th>
                <th>Reason Code</th>
                <th>Reason</th>            
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
    
    $('#processLotBtn').click(function(){
      $.ajax({
        type: 'POST',
        url: '{{ url('process_bank_response_lot') }}',
        data: {
          lot_no:  $('#level3').children('option:selected').val(),
          _token: '{{ csrf_token() }}',
        },
        success: function (datas) {
          alert('Lot processsed successfully');
        },
        error: function (ex) {
          alert('Error while processing Lot');
        }
      });
    });

    $('#filter').click(function(){
      table.clear().draw();
      table.ajax.reload();
    });
	 
  $(".dataTables_scrollHeadInner").css({"width":"100%"});

  $(".table ").css({"width":"100%"});  

  var table=$('#example').DataTable( {
        dom: "Blfrtip",
        "paging": true,
        "pageLength":20,
        "lengthMenu": [[20, 50, 80, 120, 150, 180, 500,1000, 2000,20000], [20, 50, 80, 120, 150, 180, 500,1000, 2000,20000]],
		    "serverSide": true,
		    "deferRender": true,
        "processing":true,
        "bRetrieve": true,
        "ordering":false,
        "scrollX":true,
        "language": {
          "processing": '<img src="{{ asset('images/ZKZg.gif') }}" width="150px" height="150px"/>'
        },
        "ajax": 
        {
			    url: "{{ url('bank_response_lot') }}",
			    type: "POST",
          	data:function(d){
              d._token= "{{csrf_token()}}",
              d.lotno=   $('#level3').children('option:selected').val()
			      },
            
		    } ,
        "columns": [
                  { "data": "lot_no","defaultContent":""},
                  { "data": "sr_no","defaultContent":"" },
                  { "data": "sequence_no","defaultContent":"0" },
                  { "data": "transaction_ref","defaultContent":"" },
                  { "data": "amount","defaultContent":"0" },
                  { "data": "value_date","defaultContent":"0" },
                  { "data": "sending_branch_ifsc","defaultContent":"" },                   
                  { "data": "sender_ac_type","defaultContent":"" },
                  { "data": "sender_ac_no","defaultContent":"0" },       
        				  { "data": "sender_ac_name","defaultContent":"" },
                  { "data": "benf_branch","defaultContent":"" },
                  { "data": "benf_ac_type","defaultContent":"" },
                  { "data": "benf_ac_no","defaultContent":"" },
        				  { "data": "benf_ac_name","defaultContent":"" },
        				  { "data": "txn_status","defaultContent":"" },
        				  { "data": "originator_of_remittance","defaultContent":"" },
        				  { "data": "sender_to_receiver_information","defaultContent":"0" },
        				  { "data": "reason_code","defaultContent":"0" },
                  { "data": "reason","defaultContent":"0" }
              ],
  		} );
	  

  });

  </script>
