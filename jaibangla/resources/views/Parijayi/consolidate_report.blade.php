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

  @extends('Parijayi.base')
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
                            <label class=" control-label">District Name</label>
                           <!--  <div class=""> js-reportlevel1a-->
                                <select class="form-control select2 full-width "  name="level1a" id='level1a'>
                                    <option value="">--All--</option>
                                    @foreach($districts as $district)
                                    <option value="{{$district->district_code}}">{{$district->district_name}}</option>
                                    @endforeach                                       
                                </select>
                           <!--  </div> -->
          </div>
            <div class="form-group col-md-2">
                <label class=" control-label">Level</label>
                <!--  <div class=""> -->
                    <select class="form-control select2 full-width "  name="level3" id='level3'>
                        <option value="">--All--</option>
                        <option value="Rural">Rural</option>
                        <option value="Urban">Urban</option>
                        <!-- <option value="State">State</option> -->    
                    </select>
                <!--  </div> -->
            </div>
          
            <div class="form-group col-md-12 text-center"style="margin-top:1%" >
              <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
              <!-- <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button> -->
            </div>

            </div>

            <div class="col-md-4"  style="margin-top: 2%;">
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
        <!--  </div> -->            
        <div class="col-md-12 text-center" id="loaderdiv" hidden>
          <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px"/>
        </div>
        <div class="col-md-12">
          <h5 style="color: red"> *] This report is generated periodically (Every hour)</h5>
        </div>  
        <div class="col-md-12" style="margin-top: 2%;">
         <h3 style="background-color:whitesmoke; padding:10px 5px;"><b> Consolidated Beneficiary Report </h3>
        </div>  
        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
        <table id="example" class="display" cellspacing="0" width="100%">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <thead>
            <tr role="row">
              <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
              <th width="10%" class="text-left">Level</th>
              <th width="30%" class="text-left" id="body_type">District Name</th>
              <th width="12%" class="text-left">Application Uploaded</th>     
              <th width="12%" class="text-left">Application Approved</th>
              <th width="12%" class="text-left">Application Rejected</th>
              <th width="12%" class="text-left">Payment Mandate Generated</th>
              <th width="12%" class="text-left">Pending for processing at District</th>
<!--               <th width="17%" class="text-left">Payment Confirmed by Bank</th>                   -->

            </tr>
          </thead>
          <tfoot>
            <tr>
              <th width="10%" class="text-left"></th>
              <th width="30%" class="text-left"></th>
              <th width="12%" class="text-left"></th>     
              <th width="12%" class="text-left"></th>
              <th width="12%" class="text-left"></th>
              <th width="12%" class="text-left"></th>                  
              <th width="12%" class="text-left"></th>                                
<!--               <th width="17%" class="text-left"></th> -->
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

  level1a_val="";
  level3_val="";

  $('#filter').click(function(){
    
    //District
    level1a_val=$('#level1a').children('option:selected').val();
    //Rural/ Urban
    level3_val=$('#level3').children('option:selected').val();
    
    $('#level1adata').val(level1a_val);
    $('#level3data').val(level3_val);

    if(level1a_val!="")
    $('#body_type').html('Block/Municipality Name');
    else
    $('#body_type').html('District Name');

    table.clear().draw();
    table.ajax.reload();
  
  });

  var table=$('#example').DataTable( {
        dom: 'Bfrtip',
        "paging": true,
        "pageLength":50,
        'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
        "serverSide": true,
        "processing":true,
        "bRetrieve": true,
        "language": {
          "processing": '<img src="{{ asset('images/ZKZg.gif') }}" width="150px" height="150px"/>'
        },
        "ajax": 
        {
          url: "{{ url('getPrijiyiMISData') }}",
          type: "POST",
          data:function(d){
              d.level1a=   $('#level1adata').val(),
              d.level3=   $('#level3data').val(),
              d._token= "{{csrf_token()}}"
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
            approved = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            mandate = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // payment = api
            //     .column( 6, { page: 'current'} )
            //     .data()
            //     .reduce( function (a, b) {
            //         return intVal(a) + intVal(b);
            //     }, 0 );    
            rejected = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 ); 
             pending_process = api
                .column( 6, { page: 'current'} )
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
              approved
            );
            $( api.column( 5 ).footer() ).html(
              mandate
            );
            $( api.column( 6 ).footer() ).html(
               pending_process
            );
            $( api.column( 4 ).footer() ).html(
              rejected
            );
                  
        },
        "columns": [
                  { "data": "level","defaultContent":"Null"},
                  { "data": "levelname","defaultContent":"Null" },
                  { "data": "applied","defaultContent":"0" },
                  { "data": "approved","defaultContent":"0" },
                  { "data": "rejected","defaultContent":"0" },
                  { "data": "mandate","defaultContent":"0" },
                  { "data": "process","defaultContent":"0" },
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
          title: 'Sneher Paras Beneficiary report',
          messageTop: function () {
              //return(level1a_val);
              if(level1a_val == ""){
                return "Date:<?php echo date('d/m/Y');  ?>";
              }
              else {
                return 'Date:<?php echo date('d/m/Y');  ?>\n District: '+ $('#level1a').children('option:selected').text();
              }
          },
          footer: true,
          pageSize:'A4',
          //orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
        },
        {
          extend: 'excel',
          title: 'Sneher Paras Beneficiary report',
          messageTop: function () {
              //return(level1a_val);
               if(level1a_val == ""){
                return "Date:<?php echo date('d/m/Y');  ?>";
              }
              else {
                return 'Date:<?php echo date('d/m/Y');  ?>\n District: '+ $('#level1a').children('option:selected').text();
              }
          },
          footer: true,
          pageSize:'A4',
          //orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
        },
        ],
      } );



  });

  </script>
