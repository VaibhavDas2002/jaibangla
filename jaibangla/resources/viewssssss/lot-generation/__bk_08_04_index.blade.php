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
                           <!--  </div> -->
          </div>
          <div class="form-group col-md-3" id="">
                            <label class=" control-label">Select Option</label><br>
                           <!--  <div class=""> -->
                                <select class="form-control select2  level2d col-md-12 full-width js-reportlevel2d" name="level2d" id="level2d"  style="width:100%">
                                  <option value="">-----Select Option-----</option>
                                 
                                 
                                    <option value="All">All</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>       
                                </select>
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
                           <!--  </div> -->
          </div>
        </div>
       
        </div>
        <div class="row"><div class="col-md-9"></div>
        <div class="col-md-3">
          <strong>Total Data To be Processed:</strong><span style='padding-left:8px;' class="js-data_to_process">0</span>
        </div>
      </div>
        
      </div>
       <!--  </div> -->

                 
       <form class="row" method="POST" action="{{ route('pension.generatelot') }}" class="submit-once">
       <div class="col-md-4"></div>
       <div class="col-md-8"style="">
             <input type="hidden" id="year_data" name="year_data">
             <input type="hidden" id="scheme_id" name="scheme_id">
             <input type="hidden" id="month_data" name="month_data">
              <button  style="border:1px solid black ;margin: 0% 0% 2% 0%;" type="submit" name="bulk_approve" id="bulk_approve" class="btn btn-info col-sm-3 col-md-5 btn-margin">
                         Generate LOT
              </button>
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

$('#level2d').change(function(){
  
   level2d_val=$('option:selected', this).val();
  


  if ($('option:selected', this).val() == "Block")  {
        //$('form').hide();
        $('#posting_place_div').show();
        $('#level2label').text('List of Blocks');
        $('#hide').show();
        // $('#level2d').addClass("js-reportlevel2d");
        // $('#level2').addClass("js-reportlevel2");
        // $('#level3').addClass("js-reportlevel3");
        // $('#level4').addClass("js-reportlevel4");
        
    }else if($('option:selected', this).val() =="ULB"){
       $('#posting_place_div').show();
       $('#level2label').text('List of ULBs');
       $('#hide').show();
    }
    else if($('option:selected', this).val() =="District"){
       $('#posting_place_div').hide();
       $('#level2label').text('List of Districts');
       $('#hide').show();
       
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



 table=$('#example').DataTable( {
      //dom: 'Bfrtip',
      dom: 'Blfrtip',
      "paging": true,
      "lengthChange":true,
      "oLanguage": {
      "sLengthMenu": "Select Lot Size _MENU_"
    },
      //"pageLength":20,
      // 'lengthMenu': [[0,2, 3000,4000,5000], ['--Select--',2, 3000, 4000, 5000]],
      'lengthMenu': [[20,2,200,300,500,2000,3000,4000,5000], [20,2,200,300,500,2000,3000, 4000, 5000]],
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
      // initComplete: function () {
       
      //           table.buttons().container()
      //               .appendTo( $('.col-md-6:eq(0)', table.table().container() ) );
      //       },
       "columns": [

                { "data": "id" },
                { "data": "name" },
                { "data": "dob" },
                { "data": "gender" },
                { "data": "mobile_no" },
                //{ "data": "email" },
                { "data": "bank_name" },
                { "data": "bank_ifsc" },
                { "data": "check" },

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