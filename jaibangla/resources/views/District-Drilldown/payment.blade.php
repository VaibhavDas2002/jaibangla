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
    table.dataTable thead th, table.dataTable thead td{
      padding:10px 13px;
    }
    table.dataTable tfoot th, table.dataTable tfoot td{
      padding:10px 5px;
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
  <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css"> -->
  <!--data table--->
  <!-- <link rel="stylesheet" href="{{ asset("/css/dataTables.min.css")}}">
  <link rel="stylesheet" href="{{ asset("/css/buttons.dataTables.min.css")}}"> -->

  <!---data table end---->

  @extends('District-Drilldown.base')
  @section('action-content')
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
            <form method="POST" role="form" action="{{ route('employeereport.fetch') }}">
              <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
              <div class="form-group col-md-3">
                <label class=" control-label">Scheme Name</label>
                <select class="form-control select2 full-width js-reportlevel1a"  name="level1a" id='scheme'>
                  <option value="">--All--</option>
                  @foreach($schemes as $scheme)
                    <option value="{{$scheme->id}}">{{$scheme->name}}</option>
                  @endforeach    
                </select>
              </div>
              @if($district_visible)
              <div class="form-group col-md-3">
                <label class=" control-label">District</label>
                  <select class="form-control select2 full-width js-reportlevel1a"  name="level1a" id='district'>
                    <option value="">--All--</option>
                    @foreach ($districts as $district)
                      <option value="{{$district->district_code}}"  @if(old('district')== $district->district_code)  selected  @endif> {{$district->district_name}}</option>
                    @endforeach   
                  </select>
                  <span id="error_district" class="text-danger"></span>
              </div>
              @else
                <input type="hidden" name="district" id="district" value="{{$district_code_fk}}"/>
              @endif

              {{-- @if($is_urban_visible)
              <div class="form-group col-md-3" id="divUrbanCode">
                <label class="">Rural/ Urban</label>
                
                <select name="urban_code" id="urban_code" class="form-control  full-width js-reportlevel1a" tabindex="11" >
                  <option value="">--All  --</option>
                  @foreach(Config::get('constants.rural_urban') as $key=>$val)
                  <option value="{{$key}}" @if( old('urban_code') == $key)  selected  @endif >{{$val}}</option>
                  @endforeach     
                   
                </select>
                  <span id="error_urban_code" class="text-danger"></span>
              </div>
              @else
            <input type="hidden" name="urban_code" id="urban_code" value="{{$rural_urban_fk}}"/>

              @endif
              @if($block_visible)
                <div class="form-group col-md-4" id="divBodyCode">
                <label class="" id="blk_sub_txt">Block/Sub Division.</label>
                
                <select name="block" id="block" class="form-control select2 full-width js-reportlevel1a" tabindex="16" >
                  <option value="">--All --</option>
                  
                   
                </select>
                  <span id="error_block" class="text-danger"></span>
              </div>
               @else
              <input type="hidden" name="block" id="block" value="{{$block_munc_corp_code_fk}}"/>

               @endif

              <div class="form-group col-md-3" id="municipality_div">
                <label class="control-label">Municipality</label>
                
                <select name="muncid" id="muncid" class="form-control select2 full-width js-reportlevel1a" tabindex="16" >
                  <option value="">--All --</option>
                    @foreach ($muncList as $munc)
                  <option value="{{$munc->urban_body_code}}"> {{$munc->urban_body_name}}</option>
                  @endforeach
                   
                </select>
                  <span id="error_muncid" class="text-danger"></span>
              </div>

              <div class="form-group col-md-3" id="gp_ward_div" >
                <label class="" id="gp_ward_txt">GP/Ward</label>
                
                <select name="gp_ward" id="gp_ward" class="form-control select2 full-width js-reportlevel1a" tabindex="17" >
                  <option value="">--All --</option>
                   @foreach ($gpList as $gp)
                  <option value="{{$gp->gram_panchyat_code}}"> {{$gp->gram_panchyat_name}}</option>
                  @endforeach
                   
                </select>
                  <span id="error_gp_ward" class="text-danger"></span>
              </div>
               --}}
              
              <div class="form-group col-md-12 text-left"style="margin-top:1%" >
                <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
                
              </div>
             
            </div>
            <div class="col-md-4"  style="margin-top: 2%;">
              <input type="hidden" name="_token" id="token1" value="{{ csrf_token() }}">
              <input type="hidden" id="level1data" name="level1data">
              <input type="hidden" id="level2data" name="level2data" value="{{$type}}">
              <input type="hidden" id="level3data" name="level3data">
              <input type="hidden" id="level4data" name="level4data">
              <input type="hidden" id="level1adata" name="level1adata">
              <input type="hidden" id="level1bdata" name="level1bdata">
              <input type="hidden" id="level1cdata" name="level1cdata">
            </form>
          </div>
        </div>
        <!--  </div> -->            
        <button type="excel" id='excel' class="btn btn-success">
          Generate Beneficiary List Excel
      </button>
        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
        <table id="example" class="display" cellspacing="0" width="100%">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <thead>
                <tr role="row">
                  <th></th>
                  <th class="sorting_asc" tabindex="0" aria-controls="example2" colspan="3">Failure from {{$type}} due to Wrong Account No</th>
                </tr>
                <tr role="row">
                  <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                  <th width="55%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">District Name</th>
                  <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Total</th>     
                  <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Corrected</th>
                  <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Pending</th>       
                </tr>
          </thead>
          <tfoot>
              <tr>
                <th></th><th></th><th></th><th></th>  
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

  $('#level1').change(function () {

    level1_val=$('option:selected', this).val();
    $('#level1data').val(level1_val);

  });

  $('#district').change(function() {
      var district=$(this).val();
      //alert(district);
        $('#urban_code').val('');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
    });
    $('#urban_code').change(function() {
       var urban_code=$(this).val();
        if(urban_code==''){
          $('#muncid').html('<option value="">--All --</option>'); 
        }
        $('#muncid').html('<option value="">--All --</option>'); 
        $('#block').html('<option value="">--All --</option>');
        $('#gp_ward').html('<option value="">--All --</option>');
        select_district_code= $('#district').val();
        if(select_district_code==''){
               alert('Please Select District First');
               $("#district").focus();
               $("#urban_code").val('');
        }
        else{
        select_body_type= urban_code;
        var htmlOption='<option value="">--All--</option>';
        $("#gp_ward_div").show();
        if(select_body_type==2){
            $("#blk_sub_txt").text('Block');
            $("#gp_ward_txt").text('GP');
            $("#municipality_div").hide();
            $.each(blocks, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }else if(select_body_type==1){
            $("#blk_sub_txt").text('Subdivision');
            $("#gp_ward_txt").text('Ward');
            $("#municipality_div").show();
            $.each(subDistricts, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        } 
        else{
          $("#blk_sub_txt").text('Block/Subdivision');
        }   
        $('#block').html(htmlOption);
        }

    });
    $('#block').change(function() {
      var block=$(this).val();
      var district=$("#district").val();
      var urban_code=$("#urban_code").val();
      if(district==''){
        $('#urban_code').val('');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
        alert('Please Select District First');
        $("#district").focus();
        
    }
    if(urban_code==''){
        alert('Please Select Rural/Urban First');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
        $("#urban_code").focus();
    }
    if(block!=''){
        var rural_urbanid= $('#urban_code').val();
      if(rural_urbanid==1){
       var sub_district_code=$(this).val();
       if(sub_district_code!=''){
        $('#muncid').html('<option value="">--All --</option>');
        select_district_code= $('#district').val();
        var htmlOption='<option value="">--All--</option>';
          $.each(ulbs, function (key, value) {
                if((value.district_code==select_district_code) && (value.sub_district_code==sub_district_code)){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        $('#muncid').html(htmlOption);
       }
       else{
          $('#muncid').html('<option value="">--All --</option>');
       }   
       } 
       else if(rural_urbanid==2){
          $('#muncid').html('<option value="">--All --</option>');
          $("#municipality_div").hide();
          var block_code=$(this).val();
          select_district_code= $('#district').val();

          var htmlOption='<option value="">--All--</option>';
          $.each(gps, function (key, value) {
                if((value.district_code==select_district_code) && (value.block_code==block_code)){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
          });
          $('#gp_ward').html(htmlOption);
          $("#gp_ward_div").show();


       }
       else{
          $('#muncid').html('<option value="">--All --</option>');
          $("#municipality_div").hide();
       } 
    }
    else{
        $('#muncid').html('<option value="">--All --</option>');
         $('#gp_ward').html('<option value="">--All --</option>');
    }
    
    });
    $('#muncid').change(function() {
      var muncid=$(this).val();
      var district=$("#district").val();
      var urban_code=$("#urban_code").val();
      if(district==''){
        $('#urban_code').val('');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
        alert('Please Select District First');
        $("#district").focus();
        
    }
    if(urban_code==''){
        alert('Please Select Rural/Urban First');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
        $("#urban_code").focus();
    }
    if(muncid!=''){
        var rural_urbanid= $('#urban_code').val();
      if(rural_urbanid==1){
       var municipality_code=$(this).val();
       if(municipality_code!=''){
        $('#gp_ward').html('<option value="">--All --</option>');
        var htmlOption='<option value="">--All--</option>';
          $.each(ulb_wards, function (key, value) {
                if(value.urban_body_code==municipality_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        $('#gp_ward').html(htmlOption);
       }
       else{
          $('#gp_ward').html('<option value="">--All --</option>');
       }   
       } 
    
       else{
          $('#gp_ward').html('<option value="">--All --</option>');
          $("#gp_ward_div").hide();
       } 
    }
    else{
       $('#gp_ward').html('<option value="">--All --</option>');
    }
    
    });
    $('#excel').click(function(){
      var scheme=$('#scheme').val();
      var mode=$('#level2data').val();
      var district=$('#district').val();
      // var urban_code=$('#urban_code').val();
      // var block=$('#block').val();
      // var gp_ward=$('#gp_ward').val();
      // var muncid=$('#muncid').val();
      if (scheme == '' || mode == '' || district == '') {
        alert('Scheme and district is mandatory for download excel.');
      } else {
        var  data= {'_token': '{{csrf_token()}}', 'scheme': scheme, 'mode': mode,  'district': district };
        redirectPostExcel('{{route("excelDownloadFailedPaymentList")}}', data, 'post');
      }
      
    });
    function redirectPostExcel(url, data , method = 'post'){
  var form = document.createElement('form');
  form.method = method;
  form.action = url;
  for (var name in data) {
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = data[name];
    form.appendChild(input);
  }
  $('body').append(form);
  form.submit();
}




















  level1a_val="";
  $('#filter').click(function(){
    
    level1a_val=$('#level1a').children('option:selected').val();
    // level1b_val=$('#level1b').children('option:selected').val();
    // level1c_val=$('#level1c').children('option:selected').val();
    
    $('#level1adata').val(level1a_val);
    // $('#level1bdata').val(level1b_val);
    // $('#level1cdata').val(level1c_val);

    table.clear().draw();
    table.ajax.reload();
  
  });
  
 

  var table=$('#example').DataTable( {
        dom: 'Blfrtip',
        "paging": true,
        "pageLength":25,
        'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
        "serverSide": true,
        "processing":true,
        "bRetrieve": true,
        "ajax": 
        {
          url: "{{ url('getpaymentdata-district') }}",
          type: "POST",
          data:function(d){
              d.level1a=   $('#level1adata').val(),
              d.type=$('#level2data').val(),
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
            total = api
                .column( 1, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            rectified = api
                .column( 2, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            pending = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );    

 
            // Update footer
            $( api.column( 0 ).footer() ).html(
                "Total: "
            );
            $( api.column( 1 ).footer() ).html(
                total
            );
            $( api.column( 2 ).footer() ).html(
              rectified
            );
            $( api.column( 3 ).footer() ).html(
              pending
            );
        },
        "columns": [
                  { "data": "district_name","defaultContent":"Null" },
                  { "data": "failed","defaultContent":"0" },
                  { "data": "rectified","defaultContent":"0" },
                  { "data": "pending","defaultContent":"0" },
              ], 
          "columnDefs": [
                  { "orderable": false, },
                ],         
      
        "buttons": [
        {
            extend: 'pdf',
            title: 'Failure from {{$type}} due to Wrong Account No',
              messageTop: function () {
              //return(level1a_val);
              if ( level1a_val == 1 ) {
                return 'Date:<?php echo date('d/m/Y');  ?>\n Filter Criteria:\n Scheme Name: Jai Johar (for ST)';
              }
              else if ( level1a_val == 2 ) {                     
                return 'Date:<?php echo date('d/m/Y');  ?>\n Filter Criteria:\n Scheme Name: Manabik';
                        //return 'Manabik';
              }
              else if( level1a_val ==3){
                return 'Date:<?php echo date('d/m/Y');  ?>\n Filter Criteria:\n Scheme Name: Taposili Bandhu(for SC)';
                        //return 'Taposili Bandhu(for SC)';
              }
          },
          footer: true,
          pageSize:'A4',
          //orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
          exportOptions: {
            columns: [0,1,2,3],
          }
        },
        ],

      } );



  });

  </script>
