 <style type="text/css">
   #searchbtn {
     margin: 20px auto;
   }

   .full-width {
     width: 100% !important;
   }

   .bg-blue {
     background-image: linear-gradient(to right top, #0073b7, #0086c0, #0097c5, #00a8c6, #00b8c4) !important;
   }

   .bg-red {
     background-image: linear-gradient(to right bottom, #dd4b39, #ec6f65, #d21a13, #de0d0b, #f3060d) !important;
   }

   .bg-yellow {
     background-image: linear-gradient(to right bottom, #dd4b39, #e65f31, #ed7328, #f1881e, #f39c12) !important;
   }

   .bg-green {
     background-image: linear-gradient(to right bottom, #04736d, #008f73, #00ab6a, #00c44f, #5ddc0c) !important;
   }

   .bg-verify {
     background-image: linear-gradient(to right top, #f39c12, #f8b005, #fac400, #fad902, #f8ee15) !important;
   }

   .info-box {
     display: block;
     min-height: 90px;
     background: #b6d0ca33 !important;
     width: 100%;
     box-shadow: 0px 0px 15px 0px rgba(0, 0, 0, 0.30) !important;
     border-radius: 2px;
     margin-bottom: 15px;
   }

   .small-box .icon {
     margin-top: 7%;
   }

   .small-box>.inner {
     padding: 10px;
     color: white;
   }

   .small-box p {
     font-size: 18px !important;
   }

   .select2 .select2-container {}

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
     color: red;
   }

   .small-box-footer-custom {
     position: relative;
     text-align: center;
     padding: 3px 0;
     color: #fff;
     color: rgba(255, 255, 255, 0.8);
     display: block;
     z-index: 10;
     background: rgba(0, 0, 0, 0.1);
     text-decoration: none;
     font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
     font-weight: 400;
     width: 100%;
   }

   .small-box-footer-custom:hover {
     color: #fff;
     background: rgba(0, 0, 0, 0.15);
   }

   th.sorting::after,
   th.sorting_asc::after,
   th.sorting_desc::after {
     content: "" !important;
   }

   .errorField {
     border-color: #990000;
   }

   .searchPosition {
     margin: 70px;
   }

   .submitPosition {
     margin: 25px 0px 0px 0px;
   }


   .typeahead {
     border: 2px solid #FFF;
     border-radius: 4px;
     padding: 8px 12px;
     max-width: 300px;
     min-width: 290px;
     background: rgba(66, 52, 52, 0.5);
     color: #FFF;
   }

   .tt-menu {
     width: 300px;
   }

   ul.typeahead {
     margin: 0px;
     padding: 10px 0px;
   }

   ul.typeahead.dropdown-menu li a {
     padding: 10px !important;
     border-bottom: #CCC 1px solid;
     color: #FFF;
   }

   ul.typeahead.dropdown-menu li:last-child a {
     border-bottom: 0px !important;
   }

   .bgcolor {
     max-width: 550px;
     min-width: 290px;
     max-height: 340px;
     background: url("world-contries.jpg") no-repeat center center;
     padding: 100px 10px 130px;
     border-radius: 4px;
     text-align: center;
     margin: 10px;
   }

   .demo-label {
     font-size: 1.5em;
     color: #686868;
     font-weight: 500;
     color: #FFF;
   }

   .dropdown-menu>.active>a,
   .dropdown-menu>.active>a:focus,
   .dropdown-menu>.active>a:hover {
     text-decoration: none;
     background-color: #1f3f41;
     outline: 0;
   }

   table.dataTable thead {
     padding-right: 20px;
   }

   table.dataTable thead>tr>th {
     padding-right: 20px;
   }

   table.dataTable thead th {
     padding: 10px 18px 10px 18px;
     white-space: nowrap;
     border-right: 1px solid #dddddd;
   }

   table.dataTable tfoot th {
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

   .criteria1 {
     text-transform: uppercase;
     font-weight: bold;
   }

   .item_header {
     font-weight: bold;
   }

   #example_length {
     margin-left: 40%;
     margin-top: 2px;
   }

   @keyframes spinner {
     to {
       transform: rotate(360deg);
     }
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

     #ben_view_modal {
       position: absolute;
       left: 0;
       top: 0;
     }

     [class*="col-md-"] {
       float: none;
       display: table-cell;
     }

     [class*="col-lg-"] {
       float: none;
       display: table-cell;
     }

     .pagebreak {
       page-break-before: always;
     }
   }
 </style>
 @extends('pensionreport.base')

 @section('action-content')

 <!-- Main content -->
 <section class="content">

   <div class="box">
     @if ( ($crud_status = Session::get('crud_status')))
     <div class="alert alert-{{$crud_status=='success'?'success':'danger'}} alert-block">
       <button type="button" class="close" data-dismiss="alert">×</button>
       <strong>{{ Session::get('crud_msg') }} @if($crud_status=='success') with Application ID: {{ Session::get('id') }}@endif</strong>


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
     <div class="box-header">
       <form method="post" id="appList" action="{{url('ajaxgetApplicationList')}}" class="submit-once">
         <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
         <input type="hidden" name="applicant_id" id="applicant_id" value="">

         <input type="hidden" name="user_id" id="user_id" value="{{$user_id}}">
         <input type="hidden" name="state_code_fk" id="state_code_fk" value="">
         <input type="hidden" name="district_code_fk" id="district_code_fk" value="">
         <input type="hidden" name="subdiv_code_fk" id="subdiv_code_fk" value="">
         <input type="hidden" name="rural_urban_fk" id="rural_urban_fk" value="">
         <input type="hidden" name="block_munc_corp_code_fk" id="block_munc_corp_code_fk" value="">
         <input type="hidden" name="gp_ward_code_fk" id="gp_ward_code_fk" value="">
         <input type="hidden" name="mapping_column" id="mapping_column" value="">
         <input type="hidden" name="mapping_column_val" id="mapping_column_val" value="">
         <input type="hidden" name="mapping_rural_urban" id="mapping_rural_urban" value="">
         <input type="hidden" name="role_code" id="role_code" value="{{$role_code}}">
         <input type="hidden" name="application_type" id="application_type" value="{{$application_type}}">
         <input type="hidden" name="approve_reject" id="approve_reject" value="">
         <input type="hidden" name="is_first" id="is_first" value="">
         <input type="hidden" name="is_last" id="is_last" value="">
         <input type="hidden" name="next_level_role_id" id="next_level_role_id" value="">
         <input type="hidden" name="export_excel" id="export_excel" value="">

         <div class="row">

           <div class="form-group col-md-4">
             <label class="required-field">@lang('lang.Scheme')</label>
             <select class="form-control" name="scheme_code" id="scheme_code" tabindex="1">

               <option value="">--Select--</option>
               @foreach($schemelist as $schemearr)
               <option value="{{$schemearr['scheme_code']}}" {{ $schemearr['is_enable']==0 ? 'disabled' : 'no' }}>{{$schemearr['scheme_name']}}</option>
               @endforeach
             </select>
             <span id="error_scheme_code" class="text-danger"></span>
           </div>



           <div class="form-group col-md-4" id="district_div" style="display:none;">
             <label>District</label>
             <select class="form-control client-js-district" name="district_code" id="district_code" tabindex="1" onChange="return setDistrict(this.value)">

               <option value="">--Select--</option>

             </select>
             <span id="error_district_code" class="text-danger"></span>
           </div>



         </div>
         <div class="row" id="filter_row">
           <div class="form-group col-md-4" id="rural_urban_div" style="display:none;">
             <label>Rural/Urban</label>
             <select class="form-control client-js-urban" name="rural_urban" id="rural_urban" tabindex="1" onChange="return setruralUrban(this.value)">

               <option value="">--Select--</option>
               @foreach($rural_urban_arr as $key=>$val)
               <option value="{{$key}}">{{$val}}</option>
               @endforeach
             </select>
             <span id="error_district_code" class="text-danger"></span>
           </div>

           <div class="form-group col-md-4" id="block_munc_corp_div" style="display:none;">
             <label>Block/Munc/Corp</label>
             <select class="form-control client-js-localbody" name="block_munc_corp_code" id="block_munc_corp_code" tabindex="2" onChange="return setblock(this.value)">
               <option value="" selected>--Please Select--</option>

             </select>
             <span id="error_block_munc_corp_code" class="text-danger"></span>
           </div>

           <div class="form-group col-md-4" id="gp_ward_div" style="display:none;">
             <label>GS/Ward</label>
             <select class="form-control client-js-gpward" name="gp_ward_code" id="gp_ward_code" tabindex="2" onChange="return setgsward(this.value)">
               <option value="" selected>--Please Select--</option>

             </select>
             <span id="error_gp_ward_code" class="text-danger"></span>
           </div>



           <div class="form-group col-md-4" style="display:none;" id="search_div">
             <button type="button" class="btn btn-primary btn-lg" id="searchbtn">@lang('lang.Search')</button>
           </div>
         </div>
     </div>
     <div class="alert print-error-msg" style="display:none;" id="crud_msg_Crud">
       <button type="button" class="close" aria-label="Close" onclick="closeError('crud_msg_Crud')"><span aria-hidden="true">&times;</span></button>
       <ul></ul>
     </div>
     </form>

     <!-- /.box-header -->
     <div class="box-body">
       <div id="ajax_loader"></div>

       <img src="{{ asset('images/ZKZg.gif')}}" id="loader" width="150px" height="150px" style="display:none;">
       <div id="btn-export-excel" style="display:none;"><a onclick="exportHandler()" href="javascript:void(0)"></a></div>
       <div class="col-md-12" id="searchDiv" style="margin-top: 2%;display:none;">
         <div class="row">

           <div class="col-md-9"></div>
           <div class="col-md-3 pull-right">
             <input id="dtSearch" type="text" class="form-control" name="dtSearch" value="" placeholder="search" autocomplete="off">


           </div>
         </div>
         <br />
         <table id="example" class="table table-bordered table-hover dataTable" cellspacing="0" width="100%">

           <thead>
             <tr role="row">


               <th width="5%" class="text-left">@lang('lang.ApplicantId')</th>
               <th width="10%">@lang('lang.BeneficiaryName')</th>
               <th width="10%">@lang('lang.Age')</th>
               <th width="10%">@lang('lang.gender')</th>

               <th width="10%">@lang('lang.asmb_cons')</th>
               <th width="10%">@lang('lang.bank_ifsc_code')</th>
               <th width="10%">@lang('lang.bank_account_number')</th>
               @if($application_type=='T')
               <th width="10%">@lang('lang.RejectionCause')</th>
               @endif
               <th width="20%" class="text-left">@lang('lang.Action')</th>
             </tr>
           </thead>
           <tfoot>
             <tr>


               <th width="5%" class="text-left">@lang('lang.ApplicantId')</th>
               <th width="10%">@lang('lang.BeneficiaryName')</th>
               <th width="10%">@lang('lang.Age')</th>
               <th width="10%">@lang('lang.gender')</th>
               <th width="10%">@lang('lang.asmb_cons')</th>
               <th width="10%">@lang('lang.bank_ifsc_code')</th>
               <th width="10%">@lang('lang.bank_account_number')</th>

               @if($application_type=='R')
               <th width="10%">@lang('lang.RejectionCause')</th>
               @endif
               <th width="20%" class="text-left">@lang('lang.Action')</th>
             </tr>
           </tfoot>
         </table>

         <div class="row">
           <div class="col-sm-5">

           </div>
           <div class="col-sm-7">
             <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

             </div>
           </div>
         </div>
       </div>
     </div>
   </div>
   <!-- /.box-body -->
   </div>
   <div class="modal fade" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-lg">
       <div class="modal-content">
         <div class="modal-header">
           <h2>Application Id:<span id="applicationIdtxt"></span></h2>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">

             <span aria-hidden="true">&times;</span>
           </button>


         </div>

         <div class="modal-body" id="modal_data">


         </div>






         <div class="modal-footer" style="text-align: center;">

           <button type="button" class="btn btn-default btn-lg" data-dismiss="modal" modal-cancel>Cancel</button>


         </div>
       </div>
     </div>
   </div>
   </form>

   <!-- /.content -->
   </div>
   <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>

   <script type="text/javascript">
     var application_type = '{{$application_type}}';
     $('.sidebar-menu li').removeClass('active');
     $('.sidebar-menu #ApplicationList').addClass("active");
     if (application_type == 'F') {
       $('.sidebar-menu #PendingApplication').addClass("active");
     } else if (application_type == 'V') {
       $('.sidebar-menu #VerifiedApplication').addClass("active");
     } else if (application_type == 'A') {
       $('.sidebar-menu #ApprovedApplication').addClass("active");
     } else if (application_type == 'T') {
       $('.sidebar-menu #RejectedApplication').addClass("active");
     } else if (application_type == 'R') {
       $('.sidebar-menu #RecomendedApplication').addClass("active");
     }
     $(".dataTables_scrollHeadInner").css({
       "width": "100%"
     });
     $(".table ").css({
       "width": "100%"
     });
     var table = "";
     if (table != null && table != '') {
       $('#example').DataTable().destroy();
       //alert(service_designation_id);
     }
     $.ajaxSetup({
       headers: {
         'X-CSRF-TOKEN': "{{csrf_token()}}"
       }
     });
     var role_code = $("#role_code").val();


     table = $('#example').DataTable({
       "paging": true,
       "pageLength": 10,
       "lengthMenu": [
         [10, 25, 50, -1],
         [10, 25, 50]
       ],
       "serverSide": true,
       "deferRender": true,
       "processing": true,
       "bRetrieve": true,
       "ordering": false,
       "searching": false,
       "scrollX": true,
       "language": {
         "processing": '<img src="{{ asset('
         images / ZKZg.gif ') }}" width="150px" height="150px"/>'
       },
       "ajax": {
         url: "{{ url('ajaxgetApplicationList') }}",
         type: "POST",
         data: {
           scheme_code: scheme_code,
           application_type: application_type,
           district_code_fk: district_code,
           rural_urban_fk: rural_urban,
           block_munc_corp_code_fk: block_munc_corp_code,
           gp_ward_code_fk: gp_ward_code,
           mapping_column: mapping_column,
           mapping_column_val: mapping_column_val,
           mapping_rural_urban: mapping_rural_urban,
           role_code: role_code,
           s_value: search_value,
           export_excel: null
         },
         complete: function(json, type) {
           if (type == "parsererror") {
             alert("parsererror");
           } else {
             $("#btn-export-excel").show();
             $("#searchDiv").show();
           }
         }
       },
       "columns": columns
     });

     $("#searchDiv").hide();

     function fill_datatable(scheme_code = '', application_type = '', district_code = '', rural_urban = '', block_munc_corp_code = '', gp_ward_code = '',
       mapping_column = '', mapping_column_val = '', mapping_rural_urban = '', search_value = '') {

       //console.log( table.data() );
     }
     $('#scheme_code').change(function() {
       if ($(this).val() != '') {
         $("#searchDiv").hide();
         $('#ajax_loader').html('<img  src="{{ asset('
           images / ZKZg.gif ') }}" width="50px" height="50px"/>');
         var scheme_code = $(this).val();
         var role_code = $("#role_code").val();
         var user_id = $("#user_id").val();
         $('#state_code').val('');
         $('#district_code').val('');
         $('#subdiv_code').val('');
         $('#rural_urban').val('');
         $('#block_munc_corp_code').val('');
         $('#gp_ward_code').val('');
         $('#mapping_column').val('');
         $('#mapping_column_val').val('');
         $('#mapping_rural_urban').val('');
         $.ajax({
           type: 'POST',
           url: '{{ url("masterDataAjax/getMastersfromScheme") }}',
           data: {
             scheme_code: scheme_code,
             role_code: role_code,
             _token: '{{ csrf_token() }}',
           },
           success: function(data) {

             if (data.district.is_visible) {
               $("#district_div").show();
               var count = Object.keys(data.district.array).length;
               if (count) {
                 var htmlOption = '<option value="">--Select--</option>';
                 $.each(data.district.array, function(key, value) {
                   htmlOption += '<option value="' + key + '">' + value + '</option>';
                 });
                 //console.log(htmlOption);
                 $('#district_code').html(htmlOption);
               }

             } else {
               $("#district_div").hide();
               $("#district_code_fk").val(data.district.code);
               // alert(data.district.code);
             }
             if (data.rural_urban.is_visible) {
               $("#rural_urban_div").show();
             } else {
               $("#rural_urban_div").hide();
               $("#rural_urban_code").val(data.rural_urban.code);
             }
             if (data.subdiv.is_visible) {
               $("#subdiv_div").show();
               var count = Object.keys(data.subdiv.array).length;
               if (count) {
                 var htmlOption = '<option value="">--Select--</option>';
                 $.each(data.subdiv.array, function(key, value) {
                   htmlOption += '<option value="' + key + '">' + value + '</option>';
                 });
                 $('#subdiv_div').html(htmlOption);
               }
             } else {
               $("#subdiv_div").hide();
               $("#subdiv_code").val(data.subdiv.code);
             }
             if (data.blockmunccorp.is_visible) {
               $("#block_munc_corp_div").show();
               var count = Object.keys(data.blockmunccorp.array).length;
               if (count) {
                 var htmlOption = '<option value="">--Select--</option>';
                 $.each(data.blockmunccorp.array, function(key, value) {
                   htmlOption += '<option value="' + key + '">' + value + '</option>';
                 });
                 $('#block_munc_corp_code').html(htmlOption);
               }
             } else {
               $("#block_munc_corp_div").hide();
               $("#block_munc_corp_code").val(data.blockmunccorp.code);
             }
             if (data.gpward.is_visible) {
               $("#gp_ward_div").show();
               var count = Object.keys(data.gpward.array).length;
               if (count) {
                 var htmlOption = '<option value="">--Select--</option>';
                 $.each(data.gpward.array, function(key, value) {
                   htmlOption += '<option value="' + key + '">' + value + '</option>';
                 });
                 $('#gp_ward_code').html(htmlOption);
               }
             } else {
               $("#gp_ward_div").hide();
               $("#gp_ward_code").val(data.gpward.code);
             }
             $('#mapping_column').val(data.mapping.db_column);
             $('#mapping_column_val').val(data.mapping.val);
             $('#mapping_rural_urban').val(data.mapping.rural_urban);
             //console.log(data.is_first);
             $("#is_first").val(data.is_first);
             $("#is_last").val(data.is_last);
             $("#next_level_role_id").val(data.next_level_role_id);
             $("#search_div").show();
             $('#ajax_loader').html('');
           },
           error: function(ex) {
             $('#ajax_loader').html('');
             // alert('Something wrong..may be session timeout. please logout and then login again');
             // location.reload();
           }
         });
       }
     });
     $('.client-js-urban').change(function() {
       if ($(this).val() != '') {
         var district_code = $('#district_code_fk').val();
         var rural_urban = $(this).val();
         var error_found = 1;
         if (rural_urban == 2) {
           error_found = 0;
           var url = '{{ url('
           masterDataAjax / getTaluka ') }}';
         } else if (rural_urban == 1) {
           error_found = 0;
           var url = '{{ url('
           masterDataAjax / getUrban ') }}';
         }
         //alert(district_code_fk);
         if (error_found == 0) {
           $('#block_munc_corp_code').val('');
           $('#gp_ward').val('');
           $('#error_block_munc_corp_code').html('<img  src="{{ asset('
             images / ZKZg.gif ') }}" width="50px" height="50px"/>');
           $.ajax({
             type: 'POST',
             url: url,
             data: {
               district_code: district_code,
               _token: '{{ csrf_token() }}',
             },
             success: function(data) {
               //console.log(data);
               var htmlOption = '<option value="">--Select--</option>';
               $.each(data, function(key, value) {
                 htmlOption += '<option value="' + key + '">' + value + '</option>';
               });
               $('.client-js-localbody').html(htmlOption);
               $('.client-js-gpward').html('<option value="">--Select--</option>');
               $('#error_block_munc_corp_code').html('');
             },
             error: function(ex) {
               console.log(ex);
               $('#error_block_munc_corp_code').html('');
               alert('Something wrong..may be session timeout. please logout and then login again');
               location.reload();
             }
           });
         }

       }
     });
     $('.client-js-localbody').change(function() {
       if ($(this).val() != '') {
         var block_code = $(this).val();
         var rural_urban = $('.client-js-urban').val();
         var error_found = 1;
         if (rural_urban == 2) {
           error_found = 0;
           var url = '{{ url('
           masterDataAjax / getGp ') }}';
         } else if (rural_urban == 1) {
           error_found = 0;
           var url = '{{ url('
           masterDataAjax / getWard ') }}';
         }

         if (error_found == 0) {
           $('#gp_ward_code').val('');
           $('#error_gp_ward_code').html('<img  src="{{ asset('
             images / ZKZg.gif ') }}" width="50px" height="50px"/>');
           $.ajax({
             type: 'POST',
             url: url,
             data: {
               block_code: block_code,
               _token: '{{ csrf_token() }}',
             },
             success: function(data) {
               //console.log(data);
               var htmlOption = '<option value="">--Select--</option>';
               $.each(data, function(key, value) {
                 htmlOption += '<option value="' + key + '">' + value + '</option>';
               });
               $('.client-js-gpward').html(htmlOption);
               $('#error_gp_ward_code').html('');
             },
             error: function(ex) {
               $('#error_gp_ward_code').html('');
               alert('Something wrong..may be session timeout. please logout and then login again');
               location.reload();
             }
           });
         }

       }
     });
     $("#searchbtn").click(function() {
       var scheme_code = $("#scheme_code").val();
       var application_type = $("#application_type").val();
       //console.log(application_type); 
       var status1 = status2 = status3 = 0;
       if (scheme_code == '' || typeof(scheme_code) === "undefined" || scheme_code === null) {
         $('#error_scheme_code').text('Please Select Scheme');
         status1 = 0;
       } else {
         $('#error_scheme_code').text('');
         status1 = 1;
       }
       if (application_type == '' || typeof(application_type) === "undefined" || application_type === null) {
         $('#error_application_type').text('Please Select Application Type');
         status1 = 0;
       } else {
         $('#error_application_type').text('');
         status2 = 1;
       }
       if (status1 && status2) {
         var district_code_fk = $("#district_code_fk").val();
         var rural_urban_fk = $("#rural_urban_fk").val();
         var block_munc_corp_code_fk = $("#block_munc_corp_code_fk").val();
         var gp_ward_code_fk = $("#gp_ward_code_fk").val();
         var mapping_column = $("#mapping_column").val();
         var mapping_column_val = $("#mapping_column_val").val();
         var mapping_rural_urban = $("#mapping_rural_urban").val();
         $("input[name='dtSearch']").val('');
         fill_datatable(scheme_code, application_type, district_code_fk, rural_urban_fk, block_munc_corp_code_fk, gp_ward_code_fk,
           mapping_column, mapping_column_val, mapping_rural_urban, '');
         return false;
         //$("#filter_row").show();
       }
     });



     function ViewApplicantModal(scheme_code, applicant_id, role_code, code = NULL) {
       $("#modal_data").html('');
       $("#applicationIdtxt").text('');
       $("#applicant_id").val('');
       $("#applicationIdtxt").text(applicant_id);
       $('#modal_data').html('<img align="center" src="{{ asset('
         images / ZKZg.gif ') }}" width="50px" height="50px"/>');
       $.ajaxSetup({
         headers: {
           'X-CSRF-TOKEN': "{{csrf_token()}}"
         }
       });
       var is_first = $("#is_first").val();
       var is_last = $("#is_last").val();
       //console.log(is_first);
       //console.log(is_last);
       $.ajax({
         url: "{{url('ajaxgetApplicationModal') }}",
         type: 'POST',
         data: {
           scheme_code: scheme_code,
           role_code: role_code,
           applicant_id: applicant_id,
           is_first: is_first,
           is_last: is_last,
           code: code
         },
         success: function(data) {
           // console.log(data);
           $("#modal_data").html('');
           $("#applicant_id").val(applicant_id);
           $("#modal_data").html(data);

         },
         error: function(ex) {
           $("#modal_data").html('');
           alert('Something wrong..may be session timeout. please logout and then login again');
           //location.reload();
         }
       });
       // console.log(scheme_code);
       // console.log(applicant_id);
       // console.log(status);
       $("#view_modal").modal();

       return false;
     }

     function printMsg(msg, msgtype, divid) {
       $("#" + divid).find("ul").html('');
       $("#" + divid).css('display', 'block');
       if (msgtype == '0') {
         //alert('error');
         $("#" + divid).removeClass('alert-success');
         //$('.print-error-msg').removeClass('alert-warning');
         $("#" + divid).addClass('alert-warning');
       } else {
         $("#" + divid).removeClass('alert-warning');
         $("#" + divid).addClass('alert-success');
       }
       if (Array.isArray(msg)) {
         $.each(msg, function(key, value) {
           $("#" + divid).find("ul").append('<li>' + value + '</li>');
         });
       } else {
         $("#" + divid).find("ul").append('<li>' + msg + '</li>');
       }
     }

     function closeError(divId) {
       $('#' + divId).hide();
     }

     function setDistrict(code) {
       // alert(code);
       if (code != '') {
         $("#district_code_fk").val(code);
       } else {
         $("#district_code_fk").val('');
       }

     }

     function setruralUrban(code) {
       if (code != '') {
         $("#rural_urban_fk").val(code);
       } else {
         $("#rural_urban_fk").val('');
       }
     }

     function setblock(code) {
       if (code != '') {
         $("#block_munc_corp_code_fk").val(code);
       } else {
         $("#block_munc_corp_code_fk").val('');
       }
     }

     function setgsward(code) {
       if (code != '') {
         $("#gp_ward_code_fk").val(code);
       } else {
         $("#gp_ward_code_fk").val('');
       }
     }
     $('#reject_Button').click(function(e) {
       e.preventDefault();

       $.ajax({
         type: 'POST',
         url: '{{ url('
         benReject - common ') }}',
         data: {
           ben_id: $('#reject_beneficiary_id').val(),
           _token: '{{ csrf_token() }}',
         },
         success: function(datas) {
           alert('Beneficiary with id ' + $('#reject_beneficiary_id').val() + ' rejected');
           $('#example').DataTable().ajax.reload();
         },
         error: function(ex) {

         }
       });
     });
     $('#revert_Button').click(function(e) {
       e.preventDefault();

       $.ajax({
         type: 'POST',
         url: '{{ url('
         benRevert - common ') }}',
         data: {
           ben_id: $('#revert_beneficiary_id').val(),
           _token: '{{ csrf_token() }}',
         },
         success: function(datas) {
           alert('Beneficiary with id ' + $('#revert_beneficiary_id').val() + ' reverted');
           $('#example').DataTable().ajax.reload();
         },
         error: function(ex) {

         }
       });
     });
     $("#dtSearch").keyup(function() {
       var s_val = $("input[name='dtSearch']").val();
       var scheme_code = $("#scheme_code").val();
       var application_type = $("#application_type").val();
       var district_code_fk = $("#district_code_fk").val();
       var rural_urban_fk = $("#rural_urban_fk").val();
       var block_munc_corp_code_fk = $("#block_munc_corp_code_fk").val();
       var gp_ward_code_fk = $("#gp_ward_code_fk").val();
       var mapping_column = $("#mapping_column").val();
       var mapping_column_val = $("#mapping_column_val").val();
       var mapping_rural_urban = $("#mapping_rural_urban").val();
       var status1 = status2 = status3 = 0;
       if (scheme_code == '' || typeof(scheme_code) === "undefined" || scheme_code === null) {
         $('#error_scheme_code').text('Please Select Scheme');
         status1 = 0;
       } else {
         $('#error_scheme_code').text('');
         status1 = 1;
       }
       if (application_type == '' || typeof(application_type) === "undefined" || application_type === null) {
         $('#error_application_type').text('Please Select Application Type');
         status1 = 0;
       } else {
         $('#error_application_type').text('');
         status2 = 1;
       }
       if (status1 && status2) {
         fill_datatable(scheme_code, application_type, district_code_fk, rural_urban_fk, block_munc_corp_code_fk, gp_ward_code_fk,
           mapping_column, mapping_column_val, mapping_rural_urban, s_val);
       }
       //fill_datatable(department_id,service_designation_id,stake_level,district_code,subdiv_code,block_munc_corp_code,s_val);

     });
     table.on('click', '.ben_reject_button', function() {
       $tr = $(this).closest('tr');
       if (($tr).hasClass('child')) {
         $tr = $tr.prev('parent');
       }
       var data = table.row($tr).data();
       $('#reject_beneficiary_id').val(data['id']);
       $('#reject_ben_id').html(data['application_id']);
       $('#reject_ben_name').html(data['ben_name']);
       $('#reject_ben_father_name').html(data['benf_name']);
       $('#reject_ben_ifsc').html(data['bank_ifsc']);
       $('#reject_ben_accno').html(data['bank_code']);
       $('#ben_reject_modal').modal('show');
     });
     table.on('click', '.ben_revert_button', function() {
       $tr = $(this).closest('tr');
       if (($tr).hasClass('child')) {
         $tr = $tr.prev('parent');
       }
       var data = table.row($tr).data();
       $('#revert_beneficiary_id').val(data['id']);
       $('#revert_ben_id').html(data['application_id']);
       $('#revert_ben_name').html(data['ben_name']);
       $('#revert_ben_father_name').html(data['benf_name']);
       $('#revert_ben_ifsc').html(data['bank_ifsc']);
       $('#revert_ben_accno').html(data['bank_code']);
       $('#ben_revert_modal').modal('show');
     });
   </script>

   @endsection