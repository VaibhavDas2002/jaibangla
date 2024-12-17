<style type="text/css">
    .required-field::after {
        content: "*";
        color: red;
    }

    .has-error {
        border-color: #cc0000;
        background-color: #ffff99;
    }

    .preloader1 {
        position: fixed;
        top: 40%;
        left: 52%;
        z-index: 999;
    }

    .preloader1 {
        background: transparent !important;
    }

    .panel-heading {
        padding: 0;
        border: 0;
    }

    .panel-title>a,
    .panel-title>a:active {
        display: block;
        padding: 10px;
        color: #555;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        word-spacing: 3px;
        text-decoration: none;
    }

    .panel-heading a:before {
        font-family: 'Glyphicons Halflings';
        content: "\e114";
        float: right;
        transition: all 0.5s;
    }

    .panel-heading.active a:before {
        -webkit-transform: rotate(180deg);
        -moz-transform: rotate(180deg);
        transform: rotate(180deg);
    }

    #enCloserTable tbody tr td {
        padding: 10px 10px 10px 10px;
    }

    .modal-open {
        overflow: visible !important;
    }
</style>

@extends('layouts.app-template-datatable_new')
@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            District Wise Application Report
        </h1>

    </section>
    <section class="content">
        <div class="box box-default">
            <div class="box-body">
              

                <div class="panel panel-default">
                    <div class="panel-heading"><span id="panel-icon">Filter Section</div>
                    <div class="panel-body" style="padding: 5px;">
                        <div class="row">
                            @if ( ($message = Session::get('success')))
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ $message }}</strong>

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

                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <label class="required control-label">Scheme Name </label>
                                    <select name="schemeId" id="schemeId" class="form-control">
                                        
                                  <option value="">-----Select----</option>
                                   @foreach($reports as $reportsValue)
                                   @if($reportsValue->id==1 || $reportsValue->id==2 || $reportsValue->id==3)
                                        <option value="{{$reportsValue->id}}"> {{$reportsValue->scheme_name}}</option>
                                        @endif
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-4">
                                    <label class=" control-label">District </label>
                                    <select name="districtid" id="districtid" class="form-control">
                                        <option value="">-----Select----</option>
                                        @foreach ($districts as $key=>$value)
                                        <option value="{{$value->district_code}}"> {{$value->district_name}}</option>
                                        @endforeach
                                    </select>

                                </div>
                                {{-- <div class="col-md-4">
                                    <label class=" control-label">Urban/Rural </label>
                                    <select name="rural_urbanid" id="rural_urbanid" class="form-control">
                                        <option value="">-----Select----</option>
                                        @foreach (Config::get('constants.rural_urban') as $key=>$value)
                                        <option value="{{$key}}"> {{$value}}</option>
                                        @endforeach
                                    </select>

                                </div> --}}
                                {{-- <div class="col-md-4">
                                    <label class=" control-label">Block/Subdivision </label>
                                    <select name="blockid" id="blockid" class="form-control">
                                        <option value="">-----Select----</option>

                                    </select>

                                </div> --}}
                                {{-- <div class="col-md-4">
                                    <label class=" control-label">Gp </label>
                                    <select name="gpid" id="gpid" class="form-control">
                                        <option value="">-----Select----</option>
                                    </select>

                                </div> --}}
                            </div>
                            <br>
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <label class=" control-label">From Date </label>
                                    <input type="text" class="form-control" id="fromdate" name="fromdate"
                                        autocomplete="off" placeholder="DD/MM/YYYY">

                                </div>
                                <div class="col-md-4">
                                    <label class=" control-label">To Date </label>
                                    <input type="text" class="form-control" id="todate" name="todate" autocomplete="off"
                                        placeholder="DD/MM/YYYY">

                                </div>
                                <div class=" col-md-2" style="margin-top: 28px;">
                                    <label class=" control-label">&nbsp; </label>
                                    <button type="button" name="filter" id="filter"
                                        class="btn btn-success">Search</button>


                                </div>
                                <div class="col-md-offset-1" style="margin-top: 28px;">
                                    <label class=" control-label">&nbsp; </label>

                                    <button type="button" name="reset" id="reset" class="btn btn-warning">Reset</button>

                                </div>

                            </div>
                        </div>
                        <br>

                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" id="panel_head">Filter Records</div>
                    <div class="panel-body" style="padding: 5px; font-size: 14px;">
                        <div class="table-responsive">
                            <table id="example" class="display" cellspacing="0" width="100%">
                                <thead style="font-size: 12px;">
                                    <th>District Name</th>
                                    <th>Total Application</th>
                                    <th>Pending Application For Action</th>
                                    <th>Verified</th>
                                   <th>Approved</th>
                                    <th>Rejected</th>
                                    <th>Faulty</th>

                                </thead>
                                <tbody style="font-size: 14px;"></tbody>
                                <tfoot><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>
</div>


@endsection
@section('script')
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#fromdate').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            //  endDate: new Date(),
            autoclose: true
    });
    $('#todate').datepicker({
        format: 'dd/mm/yyyy',
            todayHighlight: true,
            //  endDate: new Date(),
            autoclose: true
    });
   
     var base_url='{{ url('/') }}';
     
  
    $('.loader_img').hide();
   // $('.sidebar-menu li').removeClass('active');
   // $('.sidebar-menu #lk-main').addClass("active"); 
   // $('.sidebar-menu #processApplication').addClass("active"); 
  
    var base_url='{{ url('/') }}';
    $('#opreation_type').val('A');
    $("#verifyReject").html("Approve");
    $('#div_rejection').hide();
    var dataTable =$('#example').DataTable();
           
     
          $('#districtid').change(function() {
       var rural_urbanid=$('#rural_urbanid').val();
     
       
        $('#blockid').html('<option value="">--Select --</option>');
        select_district_code= $('#districtid').val();
       
        var htmlOption='<option value="">--Select--</option>';
        if(rural_urbanid==1){
            $.each(subDistricts, function (key, value) {
                if((value.district_code==select_district_code)){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });

        }
        else{
      
          $.each(blocks, function (key, value) {
                if((value.district_code==select_district_code)){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }
           
         
        $('#blockid').html(htmlOption);
    
    });
      
  
          $('#rural_urbanid').change(function() {
       var rural_urbanid=$(this).val();
     
       
        $('#blockid').html('<option value="">--Select --</option>');
        select_district_code= $('#districtid').val();
       
        var htmlOption='<option value="">--Select--</option>';
        if(rural_urbanid==1){
            $.each(subDistricts, function (key, value) {
                if((value.district_code==select_district_code)){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });

        }
        else{
      
          $.each(blocks, function (key, value) {
                if((value.district_code==select_district_code)){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }
           
         
        $('#blockid').html(htmlOption);
    
    });
      $('#filter').click(function(){
        var schemeId=$('#schemeId').val();
          if(schemeId==""){
            $.confirm({
                    title: 'Error!!',
                    type: 'red',
                    icon: 'fa fa-warning',
                    content: 'Please select scheme.',
                });
                return false;
          }
          else{
            $('.preloader1').show();
           datatableList();
          
          }
          
      });
  
        $('#reset').click(function(){
            $('#schemeId').val('');
          $('#districtid').val('');
       
          $('#fromdate').val('');
          $('#todate').val('');
           dataTable.ajax.reload();
      });
    
   
  
  
       
    });
  
  
    function datatableList(){
        if ( $.fn.DataTable.isDataTable('#example') ) {
            $('#example').DataTable().destroy();
          }
          var dataTable="";
        dataTable=$('#example').DataTable( {
            dom: 'Blfrtip',
            "scrollX": true,
            "paging": true,
            "searchable": true,
            "ordering":true,
            "bFilter": true,
            "bInfo": true,
            "pageLength":25,
            'lengthMenu': [[25,  50,100], [25,  50,100]],
            "serverSide": true,
            "processing":true,
            "bRetrieve": true,
            "oLanguage": {
              "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="150px"></div>'
            },
            "ajax": 
            {
              url: "{{ route('datatableDistrictApplicationReport') }}",
              type: "post",
              data:function(d){
                d.districtid= $("#districtid").val(),
                d.schemeId= $("#schemeId").val(),
                
                  // d.rural_urbanid= $("#rural_urbanid").val(),
                  // d.blockid= $("#blockid").val(),
                 //  d.gpid= $("#gpid").val(),
                   d.fromdate= $("#fromdate").val(),
                   d.todate= $("#todate").val(),
                   d._token= "{{csrf_token()}}"
              },
              error: function (jqXHR, textStatus, errorThrown) {
                $('#filter').removeAttr('disabled',true);
                $('.preloader1').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
           //   alert(sessiontimeoutmessage);
           // window.location.href=base_url;
              }
            },
            "initComplete":function(){
                $('#filter').removeAttr('disabled',true);
              //console.log('Data rendered successfully');
            },
            "columns": [
                { "data": "district_name" },
              { "data": "total_applicant" },
              { "data": "fresh_application" },
            
              { "data": "verified" },
              { "data": "approved" },
              { "data": "rejected" },
              { "data": "faulty" },
            ],
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
            total_applicant = api
              .column( 1, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              fresh_application = api
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
              rejected = api
              .column( 5, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 ); 
              faulty = api
              .column( 6, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 ); 
      
            // Update footer
            $( api.column( 0 ).footer() ).html(
              "Total: "
            );
            $( api.column( 1 ).footer() ).html(
                total_applicant
            );
            $( api.column( 2 ).footer() ).html(
                fresh_application
            );
           
            $( api.column( 3).footer() ).html(
                verified
            );
            
            $( api.column( 4 ).footer() ).html(
                approved

            );
            $( api.column( 5 ).footer() ).html(
                rejected
            );
            $( api.column( 6 ).footer() ).html(
                faulty
            );
        },   
        buttons: [
       {
           extend: 'pdf',
         
           title: 'DS MIS Report - <?php echo date('d-m-Y');  ?>',
           messageTop:'Date:<?php echo date('d/m/Y');  ?>',
           footer: true,
           pageSize:'A4',
          // orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6],

            }
       },
       {
           extend: 'excel',
         
           title: 'DS MIS Report Report -  <?php echo date('d-m-Y');  ?>',
           messageTop:'Date:<?php echo date('d/m/Y');  ?>',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                 columns: [0,1,2,3,4,5,6],
                stripHtml: false,
            }
       },
        ]
          });
    }
    
</script>
@stop