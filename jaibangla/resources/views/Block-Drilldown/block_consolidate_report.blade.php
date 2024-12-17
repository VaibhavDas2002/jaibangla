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
    table.dataTable thead th{
      padding: 10px 18px 10px 18px;
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
  
  </style>

  @extends('Block-Drilldown.base')
  @section('action-content')

      <!-- Main content -->
      <section class="content">
        <div class="box">
        <div class="box-header">
          <div class="row">
              <div class="col-sm-8">
                <h3 class="box-title"></h3>
              </div>

          </div>
        </div>
        <div class="box-body">
        <div class="row">
          <div class="col-sm-6"></div>
          <div class="col-sm-6"></div>
        </div>
        <div>
              @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
                <div class="alert alert-success alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button> 
                        <strong>{{ $message }} </strong>
                  <form method="POST" action="{{ route('nhmemployee.showSingleEmployeeReport', ['id' => $id]) }}">
                        
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        
                          <button type="submit" class="btn btn-danger col-md-2 btn-lg" style="float: right; margin-top:-33px; margin-right:15px;">
                            Print
                          </button>
                  </form>      
                
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

  </div>
        
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap js-report-form">
        <div class="row" style="margin-bottom:1%">
        <!--  <div class="col-md-12"> -->
          <form method="POST" role="form" action="{{ route('employeereport.fetch') }}">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            
            <div class="form-group col-md-4">
                            <label class=" control-label">Scheme Name</label>
                           <!--  <div class=""> js-reportlevel1a-->
                                <select class="form-control full-width "  name="scheme_id" id='scheme_id'>
                                    <option value="">--All--</option>
                                    @foreach($schemes as $scheme)
                                    <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                                    @endforeach
                                    <!-- <option value="State">State</option> -->
                                    
                                         
                                </select>
                           <!--  </div> -->
          </div>
          <div class="form-group col-md-2">
                            <label class=" control-label">Level</label>
                           <!--  <div class=""> -->
                                <select class="form-control full-width "  name="rural_urban" id='rural_urban'>
                                    <option value="">--All--</option>
                                    <option value="Rural">Rural</option>
                                    <option value="Urban">Urban</option>
                                    <!-- <option value="State">State</option> -->
                                    
                                         
                                </select>
                           <!--  </div> -->
          </div>
          <div class="form-group col-md-3">
                <label class=" control-label">Select Year</label>
              <!--  <div class=""> -->
                <select class="form-control full-width js-reportlevel1a"  name="fin_year" id='fin_year'>
                    <!-- <option value="">--Select--</option> -->
                    @foreach(Config::get('constants.fin_year') as $key=>$val)
                    <option value="{{$key}}" @if($selected_year==$key) selected @endif>{{$val}}</option>
                    @endforeach
                    <!-- <option value="State">State</option> -->
                    
                        
                </select>
            </div>
            <div class="form-group col-md-3">
                <label class=" control-label">Select Month</label>
              <!--  <div class=""> -->
                <select class="form-control full-width js-reportlevel1a"  name="month" id='month'>
                    <!-- <option value="">--Select--</option> -->
                    @foreach(Config::get('constants.monthlist') as $key=>$val)
                    <option value="{{$key}}" @if($selected_month==$key) selected @endif>{{$val}}</option>
                    @endforeach
                    <!-- <option value="State">State</option> -->
                    
                        
                </select>
            </div>
           <div class="form-group col-md-4">
                <label class=" control-label" style="padding-top:20px;">Select Payment Option</label>
              <!--  <div class=""> -->
                <select class="form-control  full-width js-reportlevel1a"  name="payment_option" id='payment_option'>
                    <!-- <option value="">--Select--</option> -->
                   
                    <option value="1" selected>Current Payments</option>
                    <option value="2" >Old Payments</option>
                   
                    <!-- <option value="State">State</option> -->
                        
                </select>
            </div>
            <div class="form-group col-md-4 text-center" style="margin-top:48px;">
              <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
              <!-- <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button> -->
            </div>

            </div>

            <div class="col-md-4"  style="margin-top: 2%;">
            

            </form>
          </div>
        </div>
        <!--  </div> -->            
          
        <div class="col-md-12" style="margin-top: 2%;">
         <h3 style="background-color:whitesmoke; padding:10px 5px;"><b> District Name: </b> {{$district_name}}</h3>
        </div>  
        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
        <table id="example" class="display" cellspacing="0" width="100%">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <input type="hidden" name="district_code" id="district_code" value="{{ $district_code }}">
          <thead>
                <tr role="row">
                  <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                  <th width="8%" class="text-left">Level</th>
                  <th width="20%" class="text-left">Block/Municipality Name</th>
                  <th width="12%" class="text-left">Application Uploaded</th>     
                  <th width="12%" class="text-left">Application Verified</th>
                  <th width="12%" class="text-left">Application Approved</th>
                  <th width="12%" class="text-left">Pushed to IFMS</th>
                  <th width="12%" class="text-left">Payment Mandate Generated</th>       
                  <th width="12%" class="text-left">Amount Booked (In Rs.)</th>       
                </tr>
          </thead>
          <tfoot>
              <tr>
                <th width="8%" class="text-left"></th>
                <th width="20%" class="text-left"></th>
                <th width="12%" class="text-left"></th>
                <th width="12%" class="text-left"></th>
                <th width="12%" class="text-left"></th>
                <th width="12%" class="text-left"></th>
                <th width="12%" class="text-left"></th>
                <th width="12%" class="text-left"></th>
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

 
  $('#filter').click(function(){
     if ( $.fn.DataTable.isDataTable('#example') ) {
      $('#example').DataTable().destroy();
     }
  var table=$('#example').DataTable( {
        dom: 'Bfrtip',
        "paging": true,
        "pageLength":50,
        'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
        "serverSide": true,
        "processing":true,
        "bRetrieve": true,
        "ajax": 
        {
          url: "{{ url('getconsol_report-block') }}",
          type: "POST",
          data:function(d){
              d.scheme_id=   $('#scheme_id').val(),
              d.rural_urban=   $('#rural_urban').val(),
              d.fin_year=   $('#fin_year').val(),
              d.month=   $('#month').val(),
              d.payment_option=   $('#payment_option').val(),
              d.district_code= "{{ $district_code }}",
              d._token= "{{csrf_token()}}"
            },
         error: function (ex) {
         alert('Something wrong..may be session timeout. please logout and then login again');
         window.location.href='./backendlogin';
        }
          } ,
          "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over this page
            applied = api
                .column( 2, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            verified = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            approved = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 ); 
            pushed_ifms = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );        
            mandate_generated = api
                .column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );     
            amount_booked = api
                .column( 7, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );  
      
            // Update footer
            $( api.column( 1 ).footer() ).html(
                "Total: "
            );
            $( api.column( 2 ).footer() ).html(
                applied
            );
            $( api.column( 3 ).footer() ).html(
              verified
            );
            $( api.column( 4 ).footer() ).html(
              approved
            );
            $( api.column( 5 ).footer() ).html(
              pushed_ifms
            );
            $( api.column( 6 ).footer() ).html(
              mandate_generated
            );
            $( api.column( 7 ).footer() ).html(
              amount_booked
            );            
        },
        "columns": [
                  { "data": "level","defaultContent":"Null"},
                  { "data": "block_name","defaultContent":"Null" },
                  { "data": "applied","defaultContent":"0" },
                  { "data": "verified","defaultContent":"0" },
                  { "data": "approved","defaultContent":"0" },
                  { "data": "pushed_ifms","defaultContent":"0" },
                  { "data": "mandate_generated","defaultContent":"0" },
                  { "data": "amount_booked","defaultContent":"0" },
              ], 
          "columnDefs": [
                  { "orderable": false, },
                  // { "targets": 1, "className": "text-center", },
                  // { "targets": 2, "className": "text-center", },
                  // { "targets": 3, "className": "text-left", },
                  // { "targets": 4, "className": "text-left", },
                ],         
      
        "buttons": [
        {
          extend: 'pdf',
          title: 'Block/Municipality wise Beneficiaries payment mandate report',
          messageTop: function () {
                var year_text = $( "#fin_year option:selected" ).text();
                var month_text = $( "#month option:selected" ).text();
                var scheme_name = $( "#scheme_id option:selected" ).text();
                var payment_option_text = $( "#payment_option option:selected" ).text();
                return "Date:<?php echo date('d/m/Y');  ?>\n Filter Criteria:\n Year: "+year_text+"\n Month: "+month_text+'\n Scheme Name: '+scheme_name+'\n Payment Option: '+payment_option_text;
          },
          footer: true,
          pageSize:'A4',
          //orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
        },
        {
          extend: 'excel',
          title: 'Block/Municipality Wise Beneficiaries payment mandate report',
          messageTop: function () {
              //return(level1a_val);
               var year_text = $( "#fin_year option:selected" ).text();
                var month_text = $( "#month option:selected" ).text();
                var scheme_name = $( "#scheme_id option:selected" ).text();
                var payment_option_text = $( "#payment_option option:selected" ).text();
                return "Date:<?php echo date('d/m/Y');  ?>\n Filter Criteria:\n Year: "+year_text+"\n Month: "+month_text+'\n Scheme Name: '+scheme_name+'\n Payment Option: '+payment_option_text;
          },
          footer: true,
          pageSize:'A4',
          //orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
        },
        ],
      } );

  });

  });

  </script>
