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
  
  @extends('commonView.base')
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
    
        
         @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
                <div class="alert alert-success alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }} with Beneficiary ID: {{$id}}</strong>
                 
  
                </div>
          @endif
          @if(count($errors) > 0)
          <div class="alert alert-danger alert-block">
            <ul>
            @foreach($errors as $error)
            <li><strong> {{ $error }}</strong></li>
            @endforeach
            </ul>
          </div>
         
          @endif   
          <button type="button" class="btn btn-info" id="revert_list" value="1">Reverted List</button> 
          <div id="example2_wrapper" class="col-md-12 dataTables_wrapper form-inline dt-bootstrap js-report-form">
            <div class="row" style="margin-bottom:1%">
            
            </div>
          </div> 
                   
        
          <div class="col-md-offset-3 col-md-3">
            
          <h4><span class="label label-primary">{{$report_type_name}}</span></h4>
      
          </div>
          <!-- <div class="col-md-offset-1 col-md-5 btn-group" role="group" >
            <button class="btn btn-success clsbulk_approve" id="bulk_approve" disabled>Approve Selected Beneficiaries</button>
          </div> -->
        <div class="col-md-12 text-center" id="loaderdiv" hidden>
          <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px"/>
        </div>  
  
        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
        <table id="example" class="display" cellspacing="0" width="100%">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <input type="hidden" name="district_code" id="district_code" value="{{ $district_code }}">
          <input type="hidden" name="is_reverted" id="is_reverted" value="">
          <thead>
                <tr role="row"> 
                  <th  width="5%" class="text-left">Application ID</th>
                  <th width="20%" class="text-left">Applicant Name</th>  
                  <th  width="10%" class="text-left">Bank Account No.</th>
                  @if($is_urban==1)
                  <th  width="20%" class="text-left">Block/ Municipality Name</th>
                  @endif
                  <th  width="20%" class="text-left">GP/Ward Name</th>
                  <th width="30%" >Action</th>  
                  
              </tr>
          </thead>
          <tfoot>
              <tr>
                 <th  width="5%" class="text-left">Application ID</th>
                  <th width="20%" class="text-left">Applicant Name</th>  
                  <th  width="10%" class="text-left">Bank Account No.</th>
                  @if($is_urban==1)
                  <th  width="20%" class="text-left">Block/ Municipality Name</th>
                  @endif
                  <th  width="20%" class="text-left">GP/Ward Name</th>
                  <th width="30%" >Action</th>  
              </tr>
          </tfoot>   
            
      </table>  
      <div class="row">
              
              <div class="col-sm-7">
               
              </div>
        </div>  
  
        </div>
  
      </div>
    <!--   </div> -->
      </section>
      <!-- /.content -->
    </div>
  
  
  <form class="row" method="POST" name="view_form" id="view_form">
     
   <input type="hidden" name="_token" value="{{ csrf_token() }}">
   <input type="hidden" name="scheme_id" value="{{$scheme}}">
   <input type="hidden" name="parameter" value="{{$parameter}}">
                      
                       
  </form>
  
  {{-- <form class="row" method="POST" name="update_form" id="update_form" action="{{ route('pensionform.application-edit-view') }}"> --}}
    <form class="row" method="POST" name="update_form" id="update_form" action="{{ !empty($parameter) ? route('pensionform.application-edit-view', ['parameter' => $parameter]) : route('pensionform.application-edit-view-lpp') }}">
     
   <input type="hidden" name="_token" value="{{ csrf_token() }}">
   <input type="hidden" name="scheme_id" value="{{$scheme}}">
   <input type="hidden" name="id" id="app_id" >
                
                       
  </form>
  
  <div id="modalReject" class="modal fade">
  <div class="modal-dialog modal-update">
      <div class="modal-content">
          <div class="modal-header flex-column">
          </div>
          <div class="modal-body">
              <h4 class="modal-title w-100">Do you really want to <span id="verify_revert_reject">Reject</span>?</h4>	
          </div>
          <div class="modal-footer justify-content-center">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-info" id="confirm_reject" >OK</button>
              <button type="button" id="Rejectting" value="Submit" class="btn btn-success btn-lg"
                  disabled>Rejectting please wait</button>
          </div>
      </div>
  </div>
  </div>
  <form class="row" method="POST" name="reject_form" id="reject_form" action="{{ route('application-reject-lpp') }}">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input type="hidden" name="scheme_id" value="{{$scheme}}">
  <input type="hidden" name="id" id="app_id" >
  <input type="hidden" name="district_code" id="district_code" value="{{ $district_code }}">
  {{-- <input type="hidden" name="block_code" id="block_code" value="{{ $block_code }}"> --}}
                               
  </form>
  
   
    @endsection
  
  
  
  
  <script src='{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}'></script>
  <script >
  
  
  function display_c(){
    var refresh=1000; // Refresh rate in milli seconds
    mytime=setTimeout('display_ct()',refresh)
  }
  
  function display_ct() {
    var x = new Date()
    document.getElementById('ct').innerHTML = x.toUTCString();
    display_c();
  } 
  
  $(document).ready(function(){ 
  display_ct();	
  $("#is_reverted").val('');
  $(".dataTables_scrollHeadInner").css({"width":"100%"});
  
  $(".table ").css({"width":"100%"});  
  
 
     $("#Rejectting").hide();

  
  var table=$('#example').DataTable( {
        dom: "Blfrtip",
        "paging": true,
        "pageLength":20,
        "lengthMenu": [[20, 50, 80], [20, 50, 80]],
    "serverSide": true,
    "deferRender": true,
        "processing":true,
        "bRetrieve": true,
        "scrollX": true,
        "ordering":false,
        "language": {
          "processing": '<img src="{{ asset('images/ZKZg.gif') }}" width="150px" height="150px"/>'
        },
        "ajax": 
        {
      url: "{{ url('application-list-read-only-edit-lpp') }}",
      type: "POST",
        data:function(d){
          d._token= "{{csrf_token()}}",
          d.scheme = "{{ $scheme }}",
          d.pr1 = "{{ $pr1 }}",
          d.parameter = "{{ $parameter }}",
          d.is_reverted= $('#is_reverted').val()
        }
      } ,
        "columns": [
            { "data": "application_id","defaultContent":""},
            { "data": "ben_name","defaultContent":"" },
            { "data": "bank_code","defaultContent":"" },
            @if($is_urban==1)
            { "data": "block_ulb_name","defaultContent":"" },
            @endif
            { "data": "gp_ward_name","defaultContent":"" },
            { "data": "action","defaultContent":"0" }      			 
          ], 
        
      
        "buttons": [
        {
      extend: 'pdf',
     
          title: 'Application List for scheme: "{{$scheme_name}}"',
          messageTop: function () {
            var message = "{{$report_type_name}} generated on: <?php echo date('d/m/Y'); ?>";            
            return message;
          },
          footer: true,
          pageSize:'A4',
          orientation: 'portrait',
          pageMargins: [ 40, 60, 40, 60 ],
        },
        {
      extend: 'excel',
     
          title: 'Application List for scheme: "{{$scheme_name}}"',
          messageTop: function () {
            var message = "{{$report_type_name}} generated on: <?php echo date('d/m/Y'); ?>";            
            return message;
          },
          footer: true,
          pageSize:'A4',
          //orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
        },
        {
      extend: 'print',
     
          title: 'Application List for scheme: "{{$scheme_name}}"',
          messageTop: function () {
            var message = "{{$report_type_name}} generated on: <?php echo date('d/m/Y'); ?>";            
            return message;
          },
          footer: true,
          pageSize:'A4',
          //orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
        },
        ],
      } );

     
    table.on('click','.btn-view',function(){
      // alert('view');
       $tr = $(this).closest('tr');
        if(($tr).hasClass('child')){
          $tr = $tr.prev('parent');
        }
        var data = table.row($tr).data();
       //alert(data['id']);
       var page='application-details-read_only-lpp/'+data['id'];
       $('#view_form').attr('action', page);
        $('#view_form').submit();
      });
   table.on('click','.btn-update',function(){
       //alert('update');
       $tr = $(this).closest('tr');
        if(($tr).hasClass('child')){
          $tr = $tr.prev('parent');
        }
        var data = table.row($tr).data();
        $('#update_form #app_id').val(data['id']);
        $('#update_form').submit();
      });
      table.on('click','.btn-reject',function(){
       //alert('update');
       $tr = $(this).closest('tr');
        if(($tr).hasClass('child')){
          $tr = $tr.prev('parent');
        }
        var data = table.row($tr).data();
        $('#reject_form #app_id').val(data['id']);
        //$('#update_form').submit();
        $('#modalReject').modal();
      });
      $('#confirm_reject').on('click',function(){
        $("#confirm_reject").hide();
        
        $("#Rejectting").show();
        $("#reject_form").submit();
      });
  $('#revert_list').click(function(){  
      // alert('ok');
       $("#is_reverted").val(1);
       table.clear().draw();
        table.ajax.reload();
    });
  });
  
  </script>
  