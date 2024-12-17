@extends('generic-lot.base')
@section('action-content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Generic Lot
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Generic Lot</a></li>
    <!-- <li class="active">Duplicate Approve</li> -->
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">

    <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
        {{-- <div class="box-header with-border">
          <h3 class="box-title">Lot creation form</h3>
        </div> --}}
        <!-- /.box-header -->
        <!-- form start -->
        <form class="form-horizontal" role="form" id="repeat_lot_form">
          {{csrf_field()}}
          <input type="hidden" id="hiddenBencount" value="" />
          <div class="box-body">
            <div id="loadingDiv">
            </div>
            <div class="form-group">
              <label for="select_scheme" class="col-md-3 control-label required">Select Scheme</label>

              <div class="col-md-9">
                <select name="select_scheme" id="select_scheme" required class="form-control"
                  onchange="pendingBeneficiary(),categoryChange(),getPaymentMode(),getSchemeWiseLot();">
                  <option value="" selected>---Select Scheme---</option>
                  @foreach($reports as $report)

                  <option value="{{$report->id}}" @if (Session::get('schemeSession')==$report->id) selected
                    @endif>{{$report->scheme_name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="select_lot_type" class="col-md-3 control-label required">Select Lot Type</label>

              <div class="col-md-9">
                <select name="select_lot_type" id="select_lot_type"
                  onchange="lot_type_change(),pendingBeneficiary(),categoryChange(),monthChange();" required
                  class="form-control ">
                  <option value="" selected>---Select Lot Type---</option>
                </select>
              </div>
            </div>
            <div style="display: none" class="form-group" id="category_lot">
              <label for="select_category" class="col-md-3 control-label required">Select Category</label>

              <div class="col-md-9">
                <select name="select_category" id="select_category" onchange="pendingBeneficiary()" required
                  class="form-control ">

                  @foreach(Config::get('constants.category') as $key=>$caste)
                  <option value="{{ $key}}">{{$caste}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="select_year" class="col-md-3 control-label required">Select Financial Year</label>

              <div class="col-md-9">
                <select name="select_year" id="select_year" required class="form-control "
                  onchange="pendingBeneficiary(),monthChange()">
                  <option value="" selected>---Select Financial Year---</option>
                  <option value="2020-2021" @if (Session::get('yearSession')=='2020-2021' ) selected @endif>2020-2021
                  </option>
                  <option value="2021-2022" @if (Session::get('yearSession')=='2021-2022' ) selected @endif>2021-2022
                  </option>
                  <option value="2022-2023" @if (Session::get('yearSession')=='2022-2023' ) selected @endif>2022-2023
                  </option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="select_month" class="col-md-3 control-label required">Select Financial Month</label>

              <div class="col-md-9">
                <select name="select_month" id="select_month" required class="form-control "
                  onchange="pendingBeneficiary()">
                  <option value="" selected>---Select Financial Month---</option>
                  {{-- @foreach(Config::get('constants.monthlist') as $key=>$month)
                  <option value="{{ $key}}">{{$month}}</option>
                  @endforeach --}}
                </select>
              </div>
            </div>
            <div class="form-group" style="display: none">
              <label for="select_pmt_mode" class="col-md-3 control-label required">Select Source Lots Payment
                Mode</label>

              <div class="col-md-9">
                <select name="select_pmt_mode" id="select_pmt_mode" required class="form-control ">
                  <option value="">---Select Source Payment Mode---</option>

                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="select_target_mode" class="col-md-3 control-label required">Select Target Payment Mode</label>

              <div class="col-md-9">
                <select name="select_target_mode" id="select_target_mode" required class="form-control ">
                  <option value="">---Select Target Payment Mode---</option>
                  {{-- <option value="SBI">SBI</option>
                  <option value="IFMS">IFMS</option> --}}
                </select>
              </div>
            </div>
            <div class="form-group" id="lotsize_div">
              <label for="lot_size" class="col-md-3 control-label required">Select Lot Size</label>

              <div class="col-md-9">
                <select class="form-control " name="lot_size" required id="lot_size">
                  <option value="">--Select Lot Size--</option>

                  @foreach(Config::get('constants.lot_size') as $key=>$lot)
                  <option value="{{ $key}}">{{$lot}}</option>
                  @endforeach

                </select>
              </div>
            </div>
            <div class="form-group" id="pending_div">
              <label for="beneficiary_count" class="col-md-3 control-label required ">Beneficiary Pending</label>

              <div class="col-md-9">
                <input type="text" class="form-control" id="beneficiary_count" disabled>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer" style="text-align: center">
            {{-- <button type="button" class="btn btn-warning">Reset</button> --}}
            <button type="button" class="btn btn-success" id="btnSubmit">Create</button>
          </div>
          <!-- /.box-footer -->
        </form>
      

      </div>
      <!-- /.box -->
      <!-- general form elements disabled -->

      <!-- /.box -->
    </div>
    <!--/.col (right) -->
  </div>
  <!-- /.row -->
</section>

@endsection
@section('script')

<script>
  $(function(){
    @if (Session::has('schemeSession')) 
        getPaymentMode();
        @endif
        @if (Session::has('yearSession')) 
        monthChange();
        @endif
        @if (Session::has('lotTypeSession')) 
        lot_type_change();
        categoryChange();
       
      
        @endif
    
        
        $('.successErrorMessage').delay(30000).slideUp(300);
$('#btnSubmit').click(function(){
  validate_form();
  var validator = $('#repeat_lot_form').data('bootstrapValidator');

            validator.validate();

            if (validator.isValid()) { 
              saveLotCreation();
              
               
            }
   
});
        
       


       
        
       
});
function validate_form() {
          $('#repeat_lot_form')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        select_lot_type: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select lot type'
                                },
                               

                            }
                        },
                        select_category:{
                            validators: {
                                notEmpty: {
                                    message: 'please select category'
                                },
                               

                            }
                        },
                        select_scheme: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select scheme'
                                }

                            }
                        },
                        select_year:{
                          validators: {
                                notEmpty: {
                                    message: 'Please select financial year'
                                }
                            }
                        },

                        select_month:{
                            validators: {
                                notEmpty: {
                                    message: 'Please select month'
                                }
                            }
                        },
                        select_pmt_mode:{
                            validators: {
                               
                                callback: {
                                    
                                    message: 'Please select previously payment mode',
                                    callback: function (value, validator, $field) {
                                        var select_lot_type = $("#select_lot_type").val();
                                        if (select_lot_type!= "3" && value == '') {
                                            return {
                                                message: 'Select Previously Payment Mode',
                                                valid: false,
                                                
                                               
                                            };
                                       
                                        }
                                        
                                        return true;
                                    }
                                    
                                }
                            }
                        },
                       select_target_mode:{
                            validators: {
                                notEmpty: {
                                    message: 'Please select target mode'
                                }
                            }
                        },
                        lot_size:{
                            validators: {
                                notEmpty: {
                                    message: 'Please select lot size'
                                }
                            }
                        }
                    
                                
                    }
                }).on('success.form.bv', function (e) {
                  
                });
        }
function saveLotCreation(){
                var hiddenBencount=$('#hiddenBencount').val();
                var lotType=$('#select_lot_type').val();
                var lotScheme=$('#select_scheme').val();
                var lotMonth=$('#select_month').val();
                var lotYear=$('#select_year').val();
                var lotTargetMode=$('#select_target_mode').val();
                if(lotType!=2 ){
                if(hiddenBencount==0){
                $.confirm({
                type: 'red',
                icon: 'fa fa-warning',
                title: 'Error!!',
                content: 'You can not generate lot when beneficiary pending is 0.',
                });
                return false;
                }
              console.log(lotType);
              console.log(lotYear);
              console.log(lotMonth);
              console.log(lotScheme);
              console.log(lotTargetMode);
                if((lotType!="") && (lotYear!="") && (lotMonth!="") && (lotScheme!="") &&  (lotTargetMode!="") ){
                  //  e.preventDefault();
                    var select_lot_type=$('#select_lot_type').val();
    var select_category=$('#select_category').val();
    var select_scheme=$('#select_scheme').val();
    var select_year=$('#select_year').val();
    var select_month=$('#select_month').val();
    var select_pmt_mode=$('#select_pmt_mode').val();
    var select_target_mode=$('#select_target_mode').val();
    var lot_size=$('#lot_size').val();
    var token = $("input[name='_token']").val();

    var fd = new FormData();
    fd.append('select_lot_type', select_lot_type);
    fd.append('select_category', select_category);
    fd.append('select_scheme', select_scheme);
    fd.append('select_year', select_year);
    fd.append('select_month', select_month);
    fd.append('select_pmt_mode', select_pmt_mode);
    fd.append('select_target_mode', select_target_mode);
    fd.append('lot_size', lot_size);

    fd.append('_token', token);
    $('#loadingDiv').show();
    $.ajax({
    type: 'post',
    url: "{{ route('store-generic-lot') }}",
    data: fd,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function (response) {
      $('#loadingDiv').hide();
    
        $.confirm({
    title:response.title,
    type: response.type,
    icon: response.icon,
    content: response.content,
    buttons: {
              Ok: {
              text: 'Ok',
              btnClass: 'btn-green',
              keys: ['enter', 'shift'],
              // action: function(){
              //   reload_table();
              // }

              }
            
            }
    });
   
  
     

    },
    complete: function(){
      pendingBeneficiary();
    },
    error: function (jqXHR, textStatus, errorThrown) {
 $('#loadingDiv').hide();
    ajax_error(jqXHR, textStatus, errorThrown); 
    }
    });
                }
                else{
                    $.confirm({
                type: 'red',
                icon: 'fa fa-warning',
                title: 'Error!!',
                content: 'Please select all the (*) mark fields.',
                });
                console.log(2);
                return false;
                }

                }



    
}
function getSchemeWiseLot(){
var select_scheme=$('#select_scheme').val();
if(select_scheme!=''){
    $('#loadingDiv').show();
    $.post("{{route('getSchemeWiseLot')}}", { select_scheme: select_scheme,'_token':"{{csrf_token()}}" })
  .done(function( data ) {
    $('#loadingDiv').hide();
      console.log(data);
    $('#select_lot_type').html('')
      $('#select_lot_type').html('<option value="" selected>---Select Lot Type---</option>');
     // $('#select_lot_type').html('<option value="9" selected>Standard Lot</option>');
    
      $.each(data.getLotMasters, function (key, value) {
        $('#select_lot_type').append('<option value=' + value.id + '>' +value.lot_type+ ' </option>');
    });
  })
  .fail(function( data ){
    $('#loadingDiv').hide();
    $.alert({
    title: 'Error!!',
    type: 'red',
    icon: 'fa fa-warning',
    content: data.responseText,
    });
  });
  }
}



function lot_type_change(){
    var select_lot_type=$('#select_lot_type').val();
    var scheme_id=$('#select_scheme').val();

            if(select_lot_type==2){
             //   getPaymentMode();
      
            $('#previous_lot').show();
            $('#category_lot').hide();
            $('#lotsize_div').hide();
            $('#pending_div').hide();
            $('#lot_size').val('');
            $('#beneficiary_count').val('');
            $('#select_category').val('ALL').trigger('change');
            }
  
            else{
                $('#category_lot').show();
                $('#previous_lot').hide();
                $('#lotsize_div').show();
                $('#pending_div').show();
              
            }
}
function categoryChange(){
    var select_lot_type=$('#select_lot_type').val();
    var scheme_id=$('#select_scheme').val();
  if(select_lot_type!=2 && (scheme_id==7 || scheme_id==-1)){
    
    $('#select_category').val('ALL').trigger('change');
    $('#category_lot').show();
  }
  else{
    $('#category_lot').hide();
  }
   
}
function pendingBeneficiary(){
            var select_category = $('#select_category').val();
            var select_lot_type = $('#select_lot_type').val();
            var select_scheme=$('#select_scheme').val();
            var select_month=$('#select_month').val();
            var select_year=$('#select_year').val();
           
            var token = $("input[name='_token']").val();

         
          if((select_lot_type!="") && (select_year!="") && (select_month!="") && (select_scheme!="") && (select_lot_type!="2")){ 
            var fd = new FormData();
            var action_url = '{{ route("getPendingBeneficiaryCount") }}';

            fd.append('select_category', select_category);
            fd.append('select_lot_type', select_lot_type);
            fd.append('select_scheme', select_scheme);
            fd.append('select_month', select_month);
            fd.append('select_year', select_year);
           

            fd.append('_token', token);
            $('#loadingDiv').show();
            $.ajax({
            type: 'post',
            url: action_url,
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                
            $('#pending_div').removeClass('has-feedback has-success');
            $('#pending_div #spanpeningid').removeClass('glyphicon glyphicon-ok form-control-feedback');
            $("#beneficiary_count").css({ 'color' : '', 'font-weight' : '' });
            $('#loadingDiv').hide();
            if(response.lot!=2){
                $('#hiddenBencount').val(response.bencount);
            $('#lot_size').html('');
            // $('#lot_size').append('<option value="">--Select Size--</option>');
            if(response.bencount>0 && response.bencount<10000){
            $('#lot_size').append('<option value='+response.bencount+' selected>'+response.bencount+'</option>');
            }   

            @foreach(Config::get('constants.lot_size') as $key=>$lot)
            $('#lot_size').append('<option value="{{ $key}}">{{$lot}}</option>');
            @endforeach
            $('#beneficiary_count').val(response.bencount);
            if(response.bencount>0){
                $('#beneficiary_count').css({
            "color":"green",
            "font-weight":"bold" 

            });
            $('#pending_div').addClass('has-feedback has-success');
            $('#pending_div #spanpeningid').addClass('glyphicon glyphicon-ok form-control-feedback');
            }
           
            
        
         
                }
        

            },
            complete: function(){
              // getPaymentMode();
            },
            error: function (jqXHR, textStatus, errorThrown) {

            ajax_error(jqXHR, textStatus, errorThrown); 
            }
            });
          }
         
              
            
          
          
            
}

function monthChange(){
   
        var select_year=$('#select_year').val();
        var select_month=$('#select_month').val();
        var select_lot_type=$('#select_lot_type').val();
        var token = $("input[name='_token']").val();
        var fd = new FormData();

        fd.append('select_month', select_month);
        fd.append('select_year', select_year);
        fd.append('select_lot_type', select_lot_type);
        fd.append('_token', token);
        $('#loadingDiv').show();
        $.ajax({
        type: 'post',
        url: "{{route('getMonthData')}}",
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            $('#loadingDiv').hide();
            $('#select_month').val('');
        $('#select_month').html('');

        $('#select_month').html(response.monthData);


        },
        complete: function(){
        
        },
        error: function (jqXHR, textStatus, errorThrown) {

        ajax_error(jqXHR, textStatus, errorThrown); 
        }
        });
             
}
function getPaymentMode(){
            @if (Session::has('schemeSession')) 
            var select_scheme = '{{Session::get('schemeSession')}}';
            @else 
            var select_scheme = $('#select_scheme').val();
            @endif
                
            var token = $("input[name='_token']").val();
            var fd = new FormData();
            var action_url = '{{ route("getPaymentMode") }}';

            fd.append('select_scheme', select_scheme);
            fd.append('_token', token);
            $.ajax({
            type: 'post',
            url: action_url,
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                $('#select_pmt_mode').html('');
                $('#select_target_mode').html('');
                
            $('#select_pmt_mode').html(response.sourcecData);
            $('#select_target_mode').html(response.targetData);
           
            },
            complete: function(){
                @if(Session::has('sourcePaymentSession'))
            $('#select_pmt_mode').val("{{Session::get('sourcePaymentSession')}}");

            @endif
            @if(Session::has('sourceTargettSession'))
            $('#select_target_mode').val("{{Session::get('sourceTargettSession')}}");

            @endif
            },
            error: function (jqXHR, textStatus, errorThrown) {

            ajax_error(jqXHR, textStatus, errorThrown); 
            }
            });
}
</script>
@stop