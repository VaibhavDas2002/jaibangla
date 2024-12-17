<style type="text/css">
  .full-width{
    width:100%!important;
  }
.bg-blue{
  background-image: linear-gradient(to right top, #0073b7, #0086c0, #0097c5, #00a8c6, #00b8c4)!important;
}
.select2-container{
    width:100%!important;
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
.dataTables_wrapper .dataTables_filter input {
    margin-left: 0.5em;
    margin-right: 10px;
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
 
/*.spinner:before {
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
}*/
#spinner{
   position: fixed;
  top:50%;
  left: 52%;
  z-index: 999;
  border:3px solid #000000;
  padding: 2%;
  background: aliceblue;
  /*position: fixed;
  top:50%;
  left: 52%;
  z-index: 999;
  border:3px solid #000000; 
  padding: 2%;
  background: #ffffff;*/
}
#overlay {
  background-color: rgba(0, 0, 0, 0.8);
  z-index: 999;
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
 /* display: none;*/
}
#MessageContainer{
  background-color: rgba(0, 0, 0, 0.8);
  z-index: 999;
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
 /* display: none;*/
}
#preloader{
  position: fixed;
  top:50%;
  left: 52%;
  z-index: 999;
  border:3px solid #000000;
  padding: 2%;
  background: aliceblue;
}
#preloader h3{
  color: #000000;
}  
#preloader h5{
  color: #000000;
}

#spinner h3{
  color: #000000;
}
#spinner h5{
  color: #000000;
}
</style>
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css"></style> -->

@extends('lot-generation.base')
@section('action-content')

    <!-- Main content -->
    <section class="content">
      <div class="box">
      <div class="box-header">
        <div class="row">
            <div class="col-sm-8">
              <h3 class="box-title"></h3>
            </div>
            <!-- <div class="col-sm-4">
              <a class="btn btn-primary" href="{{ route('user-management.create') }}">Add new user</a>
            </div> -->
        </div>
      </div>
      <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
     
            <div>
             @if ( ($message = Session::get('success')) && ($id =Session::get('id')) )
              <div class="alert alert-success alert-block">
                <?php //dd($message);?>
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }} with Lot Number: {{$id}} </strong>
               
               
              </div>
              @endif
           
             <!--   @if ($message = Session::get('failure'))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
              </div>
              @endif -->
            </div>



      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap js-report-form">
      
       <!--  <div class="col-md-12"> -->
        <!-- <form method="POST" role="form" action="{{ route('employeereport.fetch') }}"> -->
           <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <div class="row" style="margin-bottom:1%">
            <div class="form-group col-md-3">
                            <label class=" control-label " id="level4label">Financial Year</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 col-md-12 full-width year js-year-lot" name="year" id="year"><!--js-reportlevel4-->
                                    
                                   <option value="">--Select Option--</option>
                                   <option value="2020-2021">2020-2021</option>
                                    
                                </select>
                           <span id="error_year" class="text-danger"></span>
                          <!--   </div> -->
          </div>

         
         
            <div class="form-group col-md-3">
                            <label class=" control-label " id="level5label">Month</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 col-md-12 full-width js-month-lot month" name="month" id="month"><!--js-reportlevel4-->
                                    <option value="">--Select Option--</option>
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option> 
                                    <option value="April">April</option>
                                    <option value="May">May</option>   
                                    <option value="June">June</option>   
                                    <option value="July">July</option>   
                                    <option value="August">August</option>      
                                    <option value="September">September</option>   
                                    <option value="October">October</option>   
                                    <option value="November">November</option>   
                                    <option value="December">December</option>
                                    
                                </select>
                                <span id="error_month" class="text-danger"></span>
                          <!--   </div> -->
          <!-- </div> -->
           </div>


        <!--  <div class="row" style="margin-bottom:1%"> -->
          <div class="form-group col-md-3">
                            <label class=" control-label">Scheme</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 full-width js-reportlevel1 level1"  name="level1" id='level1'>
                                  <option value="">--Select--</option>
                                    @foreach ($schemes as $scheme)
                                   <option value="{{$scheme->Scheme->id}}">{{$scheme->Scheme->scheme_name}}</option>
                                   @endforeach 
                                         
                                </select>
                            <span id="error_level1" class="text-danger"></span>
                           <!--  </div> -->
          </div>
          <div class="form-group col-md-3" id="">
                            <label class=" control-label">Select Level</label><br>
                           <!--  <div class=""> -->
                                <select class="form-control select2  level2d col-md-12 full-width js-reportlevel2d" name="level2d" id="level2d"  style="width:100%">
                                  <option value="">-----Select Option-----</option>
                                 
                                 
                                    <option value="All">All</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>       
                                </select>
                              <span id="error_level2d" class="text-danger"></span>
                           <!--  </div> -->
          </div>
        </div>
       <!--  </div> -->
         <div class="row" style="margin-bottom:1%" >
           <div class="form-group col-md-6" id="posting_place_div" style="margin-top:1%">
                            <label class=" control-label" id="reportdistrictlabel">List of Districts</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 col-md-12 full-width js-reportdistrict reportdistrictlabel" name="reportdistrict" id="reportdistrict"><!--js-reportlevel2"--> 
                                  <option value="">-----Select Option-----</option>
                                  @foreach ($districts as $district)
                                   <option value="{{$district->district_code}}">{{$district->district_name}}</option>
                                   @endforeach 
                                  
                                </select>
                              <span id="error_reportdistrict" class="text-danger"></span>
                           <!--  </div> -->
          </div>
        <!-- </div> -->
       <!--  <div class="row" style="margin-bottom:1%" > -->
           <div class="form-group col-md-6" id="hide" style="margin-top:1%">
                            <label class=" control-label" id="level2label">List of State/District/Block</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 col-md-12 full-width  level2 js-reportlevel2" name="level2" id="level2"><!--js-reportlevel2"--> 
                                  <option value="">-----Select Option-----</option>
                                  
                                </select>
                             <span id="error_level2" class="text-danger"></span>  
                           <!--  </div> -->
          </div>
        </div>
       
        </div>
        <div class="row"><div class="col-md-9"></div>
        <div class="col-md-3">
          <strong>Total Data To be Processed:</strong><span style='padding-left:8px;' class="js-data_to_process">0</span>
        </div>
      </div>

      <div id="overlay" style="display: none">
      </div>
      <div id="spinner" style="display: none;" >
       <!--  <img id="img-spinner"  src="{{ asset ("/images/ZKZg.gif") }}"> -->
       <h3><strong>Please Wait...</strong></h3>
       <h5><strong>Lot Generation takes time...</strong></h5> 
      </div>
      
      <div id="MessageContainer" style="display: none">
      </div>
      <div id="preloader" style="display: none;">
        <h3><strong>Please Wait...</strong></h3>
       <h5><strong>Lot Data is Loading...</strong></h5> 
      </div>        
      </div>
     
       <!--  </div> -->

                 
       <form class="row" method="POST" action="{{ route('pension.generatelot') }}" class="submit-once" onSubmit="return checkCount();">
       <div class="col-md-4"></div>
       <div class="col-md-8"style="">
             <input type="hidden" id="year_data" name="year_data">
             <input type="hidden" id="scheme_id" name="scheme_id">
             <input type="hidden" id="month_data" name="month_data">
              <!-- <button  style="border:1px solid black ;margin: 0% 0% 2% 0%;" type="submit" name="bulk_approve" id="bulk_approve" class="btn btn-info col-sm-3 col-md-5 btn-margin" >
                         Generate LOT
              </button> -->
        </div>  
      <div class="col-md-12" id="reportbody" style="margin-top: 2%;">

       <table id="example" class="display" cellspacing="0" width="100%">
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <thead>

              <tr role="row" style="font-size: 12px;">
                <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                <th  width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Aplication ID</th>
                <th width="25%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Applicant Name</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">DOB</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Gender</th>

                
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Mobile</th>
               
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank Name</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank IFSC</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>
                
              </tr>
        
      </form>  
    </table>  
    <div class="row" style="margin-bottom: 20px">
            
            <div class="col-sm-7">
               <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                
              </div>
            </div>
    </div>  






      </div>
      <?php //if($flag==1):?>
     
    <?php// endif;?>
    </div>
  <!--   </div> -->
    </section>
    <!-- /.content -->
  </div>
@endsection
<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script> -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>
<script>
    $('.select2').select2();
</script>


<!-- Bootstrap 3.3.2 JS -->
<!-- <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script> -->
<!-----site.js-------------------->
<!-- <script src="{{ URL::asset('js/site.js') }}"></script> -->

<!-------------------------------->

<!-- AdminLTE App -->
<!-- <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script> -->




<!-- <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script> -->
<!-- <script src="{{ asset("js/select2.full.min.js") }}"></script> -->

<!-- Bootstrap 3.3.2 JS -->
<!-- <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script> -->
<!-----site.js-------------------->
<!-- <script src="{{ URL::asset('js/site.js') }}"></script> -->

<!-------------------------------->

<!-- AdminLTE App -->
<!-- <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script> -->
<script>
function checkCount(){
var count=$('.js-data_to_process').text();
//alert("count"+count);
if(count=='00'){
  alert("Lot Cannot be Generated with 0 data");
  return false;
}
$('#bulk_approve').attr('disabled',true);

$('#overlay').show();
$('#spinner').show();
}
</script>

<script >
$(document).ready(function(){
level1_val=0;
reportdistrict_data=0;
level2_val=0;
level3_val=0;
year=0;
month=0;
level2d_val=0;



$('#reportdistrict_data').change(function () {

  
   reportdistrict_data=$('option:selected', this).val();
 
    
   
});
//reportdistrict_data=$('.reportdistrict_data').val();


$('#posting_place_div').hide();
$('#level1').change(function () {

  
  level1_val=$('option:selected', this).val();
   $('#scheme_id').val(level1_val);
 console.log(level1_val);

    
   
});

$('#level2').change(function(){
  
  level2_val=$('option:selected', this).val();
  table.clear().draw();
  table.ajax.reload();
  //$('#bulk_approve').attr('disabled',false);
  //document.getElementById("bulk_approve").enable;



});

$('#level3').change(function(){
  
   level3_val=$('option:selected', this).val();
  



});

$('#year').change(function(){
  
   year=$('option:selected', this).val();
   $('#year_data').val(year);
  



});

$('#month').change(function(){
  
   month=$('option:selected', this).val();
   $('#month_data').val(month);
  



});

$('#hide').hide();
$('#level2d').change(function(){
  
   level2d_val=$('option:selected', this).val();
  


  if ($('option:selected', this).val() == "Block")  {
        //$('form').hide();
        $('#posting_place_div').show();
        $('#level2label').text('List of Blocks');
        $('#hide').show();
        $('#error_level2').text('');
        $('#error_reportdistrict').text('');
        // $('#level2d').addClass("js-reportlevel2d"); 
        // $('#level2').addClass("js-reportlevel2");
        // $('#level3').addClass("js-reportlevel3");
        // $('#level4').addClass("js-reportlevel4");
        
    }else if($('option:selected', this).val() =="ULB"){
       $('#posting_place_div').show();
       $('#level2label').text('List of ULBs');
       $('#hide').show();
       $('#error_level2').text('');
       $('#error_reportdistrict').text('');
    }
    else if($('option:selected', this).val() =="District"){
       $('#posting_place_div').hide();
       $('#level2label').text('List of Districts');
       $('#hide').show();
       $('#error_level2').text('');
       $('#error_reportdistrict').text('');
        // $('#level2').addClass("js-reportlevel2");
        // $('#level3').addClass("js-reportlevel3");
        // $('#level4').addClass("js-reportlevel4");
       
       
    }else{
      $('#posting_place_div').hide();
      $('#hide').hide();
      table.clear().draw();
      table.ajax.reload();
     // $('#bulk_approve').attr('disabled',false);
     // $('#level2label').text('List of States');
    }

});
////////////////////////////////////04-04-2020////////////////////////////////////////////////////
error_year='';
error_month='';
error_level1='';//for scheme
error_level2d='';//for level
error_reportdistrict='';//for BLOCK ULB name of district (reportdistrict)
error_level2='';//for list of district in case of level:district, for list of block/uld in case of level:block/ulb
$('#bulk_approve').click(function(){
if($.trim($('#year').val()).length == 0)
{
    error_year = 'Financial Year is Required';
    $('#error_year').text(error_year);
    $('#error_year').next().find('.select2-selection').addClass('has-error');
}
else
{
error_year = '';
$('#error_year').text(error_year);
$('#error_year').next().find('.select2-selection').removeClass('has-error');
}

if($.trim($('#month').val()).length == 0)
{
    error_month = 'Month is Required';
    $('#error_month').text(error_month);
    $('#error_month').next().find('.select2-selection').addClass('has-error');
}
else
{
error_month = '';
$('#error_month').text(error_month);
$('#error_month').next().find('.select2-selection').removeClass('has-error');
}

if($.trim($('#level1').val()).length == 0)
{
    error_level1 = 'Scheme is Required';
    $('#error_level1').text(error_level1);
    $('#error_level1').next().find('.select2-selection').addClass('has-error');
}
else
{
error_level1 = '';
$('#error_level1').text(error_level1);
$('#error_level1').next().find('.select2-selection').removeClass('has-error');
}

if($.trim($('#level2d').val()).length == 0)
{
    error_level2d = 'Level is Required';
    $('#error_level2d').text(error_level2d);
    $('#error_level2d').next().find('.select2-selection').addClass('has-error');
}
else
{
error_level2d = '';
$('#error_level2d').text(error_level2d);
$('#error_level2d').next().find('.select2-selection').removeClass('has-error');
}

if(level2d_val=="Block"){

  if($.trim($('#reportdistrict').val()).length == 0)
  {
    error_reportdistrict = 'District is Required';
    $('#error_reportdistrict').text(error_reportdistrict);
    $('#error_reportdistrict').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
  error_reportdistrict = '';
  $('#error_reportdistrict').text(error_reportdistrict);
  $('#error_reportdistrict').next().find('.select2-selection').removeClass('has-error');
  }

  if($.trim($('#level2').val()).length == 0)
  {
    error_level2 = 'Block is Required';
    $('#error_level2').text(error_level2);
    $('#error_level2').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
  error_level2 = '';
  $('#error_level2').text(error_level2);
  $('#error_level2').next().find('.select2-selection').removeClass('has-error');
  }

}else if(level2d_val=="ULB"){

  if($.trim($('#reportdistrict').val()).length == 0)
  {
    error_reportdistrict = 'District is Required';
    $('#error_reportdistrict').text(error_reportdistrict);
    $('#error_reportdistrict').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
  error_reportdistrict = '';
  $('#error_reportdistrict').text(error_reportdistrict);
  $('#error_reportdistrict').next().find('.select2-selection').removeClass('has-error');
  }

  if($.trim($('#level2').val()).length == 0)
  {
    error_level2 = 'ULB is Required';
    $('#error_level2').text(error_level2);
    $('#error_level2').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
  error_level2 = '';
  $('#error_level2').text(error_level2);
  $('#error_level2').next().find('.select2-selection').removeClass('has-error');
  }

}else if(level2d_val=="District"){
  if($.trim($('#level2').val()).length == 0)
  {//console.log("district_code");

    error_level2 = 'District is Required';
    $('#error_level2').text(error_level2);
    $('#error_level2').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
  error_level2 = '';
  $('#error_level2').text(error_level2);
  $('#error_level2').next().find('.select2-selection').removeClass('has-error');
  }



}else if(level2d_val=="All"){

}


if( error_year != '' || error_month != '' || error_level1!= '' || error_level2d!= '' ||error_reportdistrict!= '' || error_level2!= '')
{
   return false;
}
else
{
return true;
}

});

$('.submit-once').submit(function(e){
    if( $(this).hasClass('form-submitted') ){
        e.preventDefault();
        return;
    }
    $(this).addClass('form-submitted');
});
///////////////////////////////////////////04-04-2020 end///////////////////////////////////////////////
// $('#level3').change(function(){
  
//    level3_val=$('option:selected', this).val();
//   //console.log(level1_data);//level2_data,level3_data,level2d_data,reportdistrict_data);

//   table.clear().draw();
//   table.ajax.reload();
// });
$.ajax({
  type: 'POST',
  url: "{{ url('getloadcount') }}",
  data: {
   'reportlevel1_data':level1_val,
   'reportlevel2_data':level2_val,
   'reportlevel2d_data':level2d_val,
   //'reportlevel3_data':reportlevel3_data,
   'reportdistrict_data':reportdistrict_data,
  },
  headers: {
        'X-CSRF-TOKEN': $('#token').val()
  }, 
  success: function (datas) {
    if (!datas || datas.length === 0) {
      return;
    }
    $('.js-data_to_process').append(
       datas.count,
      );   
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
});



 table=$('#example').on( 'preDraw.dt', function () {
      console.log( 'Loading' );
      //Here show the loader.
      $('#bulk_approve').attr('disabled',true);
      $("#MessageContainer").show();
      $("#preloader").show();
      //$("#MessageContainer").html("Please wait.... Lot Data is loading");
      } ).on( 'draw.dt', function () {
            console.log( 'Loaded' );
           //Here hide the loader.
          $('#bulk_approve').attr('disabled',false);
          $("#MessageContainer").hide();
          $("#preloader").hide();
        } )
      .DataTable( {
      //dom: 'Bfrtip',
      dom: 'Blfrtip',
      "paging": true,
      "lengthChange":true,
      "oLanguage": {
      "sLengthMenu": "Select Lot Size _MENU_"
      },
      //"pageLength":20,
      // 'lengthMenu': [[0,2, 3000,4000,5000], ['--Select--',2, 3000, 4000, 5000]],
      'lengthMenu': [[20,200,300,500,2000,3000,4000,5000], [20,200,300,500,2000,3000, 4000, 5000]],
       "serverSide": true,
       "bRetrieve": true,
      "ajax": {
        "url": "{{ url('getlotdata') }}",
        "type": "POST",
        "data":function(d){
            d.level1=  level1_val,
            d.level2= level2_val,
            d.level2d= level2d_val,
            d.reportdistrict= reportdistrict_data,
            //d.level3= level3_val,
            d.year= year,
            d.month=month,
            d._token= "{{csrf_token()}}"
          }
        } ,
        "processing":true,
      // initComplete: function () {
       
      //           table.buttons().container()
      //               .appendTo( $('.col-md-6:eq(0)', table.table().container() ) );
      //       },
       "columns": [

                { "data": "id" },
                { "data": "name" },
                { "data": "dob" },
                { "data": "gender" },
                { "data": "mobile_no" ,"orderable":false},
                //{ "data": "email" },
                { "data": "bank_name","orderable":false },
                { "data": "bank_ifsc" ,"orderable":false},
                { "data": "check","orderable":false },

            ],          
      
      buttons: [
       {
           extend: 'pdf',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6],

            }
       },
       {
           extend: 'print',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6],
                stripHtml: false,
            }
       },
       {
           extend: 'excel',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6],
                stripHtml: false,
            }
       },
        {
           extend: 'copy',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6],
                stripHtml: false,
            }
       },
       {
           extend: 'csv',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6],
                stripHtml: false,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    } );


});






</script>