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
          <div class="form-group col-md-6">
                            <label class=" control-label">Scheme Name</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 full-width js-reportlevel1a"  name="level1a" id='level1a'>
                                    <option value="">--Select--</option>
                                    @foreach($schemes as $scheme)
                                    <option value="{{$scheme->id}}">{{$scheme->name}}</option>
                                    @endforeach
                                    <!-- <option value="State">State</option> -->
                                    
                                         
                                </select>
                           <!--  </div> -->
          </div>
         
                          <!--   </div> -->
          </div>

          <div class="col-md-4"  style="margin-top: 2%;">
          <input type="hidden" name="_token" id="token1" value="{{ csrf_token() }}">
          <input type="hidden" id="level1data" name="level1data">
          <input type="hidden" id="level2data" name="level2data">
          <input type="hidden" id="level3data" name="level3data">
          <input type="hidden" id="level4data" name="level4data">
          <input type="hidden" id="level1adata" name="level1adata">

          </form>
        </div>
      </div>
       <!--  </div> -->

                 
        
      <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
       <table id="example" class="display" cellspacing="0" width="100%">
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <thead>

              <tr role="row" style="font-size: 12px;">
                <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                <th  width="7%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">District Names</th>
                <th width="12%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Applications Submitted</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Applications Verified </th>
                <th width="9%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Applications Approved</th>
                

                
                            
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
<!-- <script src="{{ asset("js/select2.full.min.js") }}"></script> -->
<!---data table------->
<!-- <script src="{{ asset("js/jquery-1.12.4.js") }}"></script>
<script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("js/dataTables.buttons.min.js") }}"></script>
<script src="{{ asset("js/buttons.flash.min.js") }}"></script>
<script src="{{ asset("js/jszip.min.js") }}"></script>
<script src="{{ asset("js/pdfmake.min.js") }}"></script>
<script src="{{ asset("js/vfs_fonts.js") }}"></script>
<script src="{{ asset("js/buttons.html5.min.js") }}"></script>
<script src="{{ asset("js/buttons.print.min.js") }}"></script> -->

<!---data table end--->

<!-- Bootstrap 3.3.2 JS -->
<!-- <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script> -->
<!-----site.js-------------------->
<!-- <script src="{{ URL::asset('js/site.js') }}"></script>
 -->
<!-------------------------------->

<!-- AdminLTE App -->
<!-- <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script> -->




<script >
$(document).ready(function(){

$('#level1').change(function () {

  level1_val=$('option:selected', this).val();
  $('#level1data').val(level1_val);

});


$('#level1a').change(function(){
  
  level1a_val=$('option:selected', this).val();
  $('#level1adata').val(level1a_val);
  console.log(level1a_val);
   table.clear().draw();
  table.ajax.reload();
 
});

var table=$('#example').DataTable( {
      dom: 'Brtip',
      "paging": true,
      "pageLength":100,
      //'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      "serverSide": true,
       //"bRetrieve": true,
      "ajax": 
      {
        url: "{{ url('getdatas-district') }}",
        type: "POST",
        data:function(d){
            d.level1a=   $('#level1adata').val(),
            //d.level2= $('#level2data').val(),
            //d.level3= $('#level3data').val(),
            //d.level4= $('#level4data').val(),
            d._token= "{{csrf_token()}}"
          }
        } ,
        footerCallback: function ( row, data, start, end, display ) {
            var api = this.api(), data;
             sum_Sub=0;
             sum_ver=0;
             sum_approved=0;
 
            // converting to interger to find total
            var intVal = function ( i ) {
                    if (typeof i==='string'){
                      //console.log(i);
                      var mySubString = i.substring(
                      i.lastIndexOf("\'>") +2, 
                      i.lastIndexOf("</a>")
                  );
                    var final_val=Number(mySubString);
                }
                    return final_val;
            };
 
            // computing column Total of the complete result 
            //var appSubTotal = api
                api.column( 1 )
                .data()
                .each( function (a) {
                  a = intVal(a);
                  //console.log("a:" +a);
                  if(a!=undefined){
                    sum_Sub=sum_Sub+a;
                  }
                
                  
                  console.log("sum_Sub: "+sum_Sub);
                 
                });
              var appSubTotal= sum_Sub;
        
           api.column( 2 )
                .data()
                .each( function (a) {
                  a = intVal(a);
                  if(a!=undefined){
                    sum_ver=sum_ver+a;
                  }
                
                  
                  console.log("sum_ver: "+sum_ver);
                 
                });
              var appVerTotal= sum_ver;

          api.column( 3 )
                .data()
                .each( function (a) {
                  a = intVal(a);
                  if(a!=undefined){
                    sum_approved=sum_approved+a;
                  }
                
                  
                  console.log("sum_approved: "+sum_approved);
                 
                });
              var appApprovedTotal= sum_approved;
        /**old sample code
            var appApprovedTotal = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
       *****/
        
            // Update footer by showing the total with the reference of the column index 
        $( api.column( 0 ).footer() ).html('Total');
            $( api.column( 1 ).footer() ).html(appSubTotal);
            $( api.column( 2 ).footer() ).html(appVerTotal);
            $( api.column( 3 ).footer() ).html(appApprovedTotal);
      },

       "columns": [

                
                { "data": "district_name","defaultContent":"Null" },
                { "data": "application_submitted","defaultContent":"0" },
                { "data": "application_verified","defaultContent":"0" },
                { "data": "application_approved","defaultContent":"0" }
               

            ], 
        "columnDefs": [
                { "orderable": false, }
              ],         
     
      "buttons": [
       {
           extend: 'pdf',
           title: 'District Wise Report of  Application Status',
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
       /*********print,excel,csv,copy buttons
       {
           extend: 'print',
           title: 'District Wise Report of  Application Status',
           // messageTop:'Filter Criteria:\n District:{{}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: $("#level1adata").val(),',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3],
                stripHtml: false,
            }
       },
       {
           extend: 'excel',
           title: 'District Wise Report of  Application Status',
            // messageTop:'Filter Criteria:\n District:{{}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: $("#level1adata").val(),',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3],
                stripHtml: false,
            }
       },
        {
           extend: 'copy',
           title: 'District Wise Report of  Application Status',
            // messageTop:'Filter Criteria:\n District:{{}}\n  Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: $("#level1adata").val(),',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3],
                stripHtml: false,
            }
       },
       {
           extend: 'csv',
           title: 'District Wise Report of  Application Status',
            // messageTop:'Filter Criteria:\n District:{{}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: $("#level1adata").val(),',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3],
                stripHtml: false,
            }
       },******/
      //'pdf','excel','csv','print','copy'
      ],

    } );



});

</script>


<script>

//$('#level1').change(function(){
 // $(document).ready(function() {
    
 //  } );
</script>
