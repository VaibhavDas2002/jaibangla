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
      padding: 1px 1px 1px 1px;
      border-right: 1px solid #dddddd;
    }
    table.dataTable tfoot th{
      padding: 1px 1px 1px 1px;
      white-space: nowrap;
      border-right: 1px solid #dddddd;
    }
    table.dataTable tbody td {
      padding: 1px 1px 1px 1px;
      border-right: 1px solid #dddddd;
      /* //white-space: nowrap; */
      -webkit-box-sizing: content-box;
      -moz-box-sizing: content-box;
      box-sizing: content-box;
    }
    .criteria1{
      text-transform: uppercase;
      font-weight: bold;
    }
    
    #example_length{
      margin-left: 10%;
      margin-top: 1px;
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
  #preloader1{
    position: fixed;
    top:40%;
    left: 52%;
    z-index: 999;
  }
  #preloader1 {
    background: transparent !important;
  }
  </style>

  @extends('Identity-Drilldown.base_block')
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
          <form method="POST" role="form" action="#">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
              <input type="hidden" name="district_code" id="district_code" value="{{ $district_code }}">

            <div class="form-group col-md-3">
                <label class=" control-label">Select Scheme</label>
              <!--  <div class=""> -->
                <select class="form-control full-width"  name="scheme_id" id='scheme_id'>
                    <option value="">--All--</option>
                    @foreach($schemes as $scheme)
                    <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                    @endforeach
                    <!-- <option value="State">State</option> -->
                    
                        
                </select>
            </div>
            <div class="form-group col-md-3">
                <label class=" control-label">Select ID Type</label>
              <!--  <div class=""> -->
                <select class="form-control full-width "  name="identity_no" id='identity_no'>
                      <option value="">--All--</option>
              
                    <option value="aadhar_no">AAdhar Card</option>
                    <option value="ration_card_no"> Ration Card</option>
                    <option value="epic_voter_id">Epic Card</option>
                   
                    <!-- <option value="State">State</option> -->
                    
                        
                </select>
            </div>

             <div class="form-group col-md-3">
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
           
             
            <div class="form-group col-md-3 " style="margin-top:20px;">
              <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
              <!-- <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button> -->
            </div>

            </div>

            <div class="col-md-4"  style="margin-top: 2%;">
          

            </form>
          </div>
        </div>
        <!--  </div> -->            
          
        <div class="col-md-12" id="reportbody" style="margin-top: 1%;">
        <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <thead>
                <tr role="row">
                  <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                  <th width="20%" class="text-left">Level</th>
                   <th width="30%" class="text-left">Block/Municipality Name</th>
                  <th width="25%" class="text-left">Total Beneficiary</th>     
                  <th width="25%" class="text-left">ID Details Exists </th>
                 
                         
                </tr>
          </thead>
          <tfoot>
              <tr>
                <th width="20%" class="text-left"></th>
                <th width="30%" class="text-left"></th>
                <th width="25%" class="text-left"></th>
                <th width="25%" class="text-left"></th>
                
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
    //$('#loadingDiv').hide(); 
       $(".dataTables_scrollHeadInner").css({"width":"100%"});

       $(".table ").css({"width":"100%"});  


    $('#filter').click(function(){ 
      
      
      var scheme_id      = $('#scheme_id').val(); 
      var identity_no    = $('#identity_no').val(); 

      if(scheme_id == ''|| identity_no =='')    
      {
        alert('Please select scheme and id type ');
       
      } 
      else
      {
        //$('#payment_div').show();
        list_table();
      

      }
    });

    $('#reset').click(function(){ 
      //$('#loadingDiv').show();    
     // $('#district_code').val(""); 
      $('#lot_month').val(""); 
      $('#lot_year').val(""); 
      $('#tableForPayment').DataTable().ajax.reload();
     
    });

});

 
  
 

    
  function list_table(){
    var table = "";
    if ( $.fn.DataTable.isDataTable('#example') ) {
      $('#example').DataTable().destroy();
     }
     var table=$('#example').DataTable( {
        dom: 'Bfrtip',
        "scrollX": true,
        "ordering":false, // Disable Ordering of all column
        "paging": false,
        "pageLength":100,
        'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
        "serverSide": true,
        "processing":true,
         "bRetrieve": true,
  "oLanguage": {
          "sProcessing": '<div id="preloader1" align="center"><img src="../images/ZKZg.gif" width="100px"></div>'
        },
        "ajax": 
        {
          url: "{{ url('get-block-subdiv-identity-report') }}",
          type: "POST",
          data:function(d){
              d.scheme_id= $('#scheme_id').val(),
              d.identity_no=$('#identity_no').val(),
              d.rural_urban =   $('#rural_urban').val(), 
              d.district_code= "{{ $district_code }}",             
              d._token= "{{csrf_token()}}"
            },
        error: function (ex) {
         alert('Something wrong..may be session timeout. please logout and then login again');
         //window.location.href='./backendlogin';
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
            total_ben = api
                .column( 2, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            identity_count = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
           
           
           
                     
            
           
            
               
      
            // Update footer
            $( api.column( 0 ).footer() ).html(
                "Total: "
            );
            $( api.column( 2 ).footer() ).html(
                total_ben
            );
            $( api.column( 3 ).footer() ).html(
              identity_count
            );
           
                      
                      
                    
        },
        "columns": [
                  { "data": "rural_urban","defaultContent":"Null" },
                  { "data": "block","defaultContent":"Null" },
                  { "data": "total_ben","defaultContent":"0" },
                  { "data": "identity_count","defaultContent":"0" },
                 
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
          title: 'Identity  Report- Block/Municipality Wise @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp',
           messageTop:'Date:@php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp',
          footer: true,
          orientation: 'landscape',
                        pageSize: 'LEGAL',
          pageMargins: [ 40, 60, 40, 60 ],
          exportOptions: {
            columns: [0,1,2,3],

  }
        },
        {
          extend: 'excel',
          title: 'Identity  Report- Block/Municipality Wise @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp',
          messageTop:'Date:@php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp',
          footer: true,
          pageSize:'A4',
          orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
          exportOptions: {
               columns: [0,1,2,3],
              stripHtml: true,
  }
        },
        ],
      });
  
 }

  </script>
