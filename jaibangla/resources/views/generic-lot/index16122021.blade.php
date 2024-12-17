@extends('generic-lot.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if (($message = Session::get('success')) && ($id =Session::get('id')))
            <div class="alert alert-success alert-block successErrorMessage">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }} with Lot No: {{$id}}</strong>
            </div>

            @endif
            @if (($message = Session::get('error')))
            <div class="alert alert-danger alert-block successErrorMessage">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
            @if (($message = Session::get('msg1')))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif


            <div class="panel panel-default">
                <div class="panel-heading">Generic Lot</div>
                <div class="panel-body">
                    <div id="loadingDiv">
                    </div>
                    <form class="form-horizontal" role="form" id="repeat_lot_form" method="POST"
                        action="{{ route('store-generic-lot') }}">
                        {{ csrf_field() }}
                        <input type="hidden" id="hiddenBencount" value="" />

                        <div class="form-group{{ $errors->has('select_scheme') ? ' has-error' : '' }}">
                            <label for="select_scheme" class="col-md-4 control-label required">Select Scheme</label>
                            <div class="col-md-6">
                                <select name="select_scheme" id="select_scheme" required class="form-control select2"
                                    onchange="pendingBeneficiary(),categoryChange(),getPaymentMode();">
                                    <option value="" selected>---Select Scheme---</option>
                                    @foreach($reports as $report)
                                   
                                    <option value="{{$report->id}}" @if (Session::get('schemeSession')==$report->id) selected @endif>{{$report->scheme_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('select_lot_type') ? ' has-error' : '' }}">
                            <label for="select_lot_type" class="col-md-4 control-label required">Select Lot Type</label>
                            <div class="col-md-6">
                                <select name="select_lot_type" id="select_lot_type"
                                    onchange="lot_type_change(),pendingBeneficiary(),categoryChange(),monthChange();" required
                                    class="form-control select2">
                                    <option value="" selected>---Select Lot Type---</option>
                                    @foreach($lottype_master as $lot_type)
                                    {{-- <option value="{{ $lot_type->id}}" @if($lot_type->id!=3) disabled
                                    @endif>{{$lot_type->lot_type}}</option> --}}
                                    @if($lot_type->id!='7' && $lot_type->id!='8')
                                   {{-- @php
                                   $arr_scheme=[];
                                       foreach($reports as $report){
                                           array_push($arr_scheme,$report->id);
                                       }
                                   @endphp 
                                   @if((in_array(2,$arr_scheme) || in_array(10,$arr_scheme) || in_array(11,$arr_scheme)) && $lot_type->id==2 )
                                    <option value="" disabled>{{$lot_type->lot_type}}</option>
                                   @else 
                                   <option value="{{ $lot_type->id}}" @if (Session::get('lotTypeSession')==$lot_type->id) selected @endif>{{$lot_type->lot_type}}</option>
                                    @endif --}}
                                    <option value="{{ $lot_type->id}}" @if (Session::get('lotTypeSession')==$lot_type->id) selected @endif @if ($lot_type->id==3) disabled @endif>{{$lot_type->lot_type}}</option>
                                    @endif
                                    @endforeach
                                   
                                </select>

                            </div>
                        </div>

                        <div style="display: none"
                            class="form-group{{ $errors->has('select_category') ? ' has-error' : '' }}"
                            id="category_lot">
                            <label for="select_category" class="col-md-4 control-label required">Select Category</label>
                            <div class="col-md-6">
                                <select name="select_category" id="select_category" onchange="pendingBeneficiary()"
                                    required class="form-control ">

                                    @foreach(Config::get('constants.category') as $key=>$caste)
                                    <option value="{{ $key}}">{{$caste}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('select_year') ? ' has-error' : '' }}">
                            <label for="select_year" class="col-md-4 control-label required">Select Financial
                                Year</label>
                            <div class="col-md-6">
                                <select name="select_year" id="select_year" required class="form-control select2"
                                    onchange="pendingBeneficiary(),monthChange()">
                                    <option value="" selected>---Select Year---</option>
                                    <option value="2020-2021" @if (Session::get('yearSession')=='2020-2021') selected @endif>2020-2021</option>
                                    <option value="2021-2022" @if (Session::get('yearSession')=='2021-2022') selected @endif>2021-2022</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('select_month') ? ' has-error' : '' }}" id="financial_month">

                            <label for="select_month" class="col-md-4 control-label required">Select Financial
                                Month</label>

                            <div class="col-md-6">


                                <select name="select_month" id="select_month" required class="form-control select2"
                                    onchange="pendingBeneficiary()">
                                    <option value="" selected>---Select Month---</option>
                                    {{-- @foreach(Config::get('constants.monthlist') as $key=>$month)
                                    <option value="{{ $key}}">{{$month}}</option>
                                    @endforeach --}}
                                </select>
                                @if ($errors->has('select_month'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('select_month') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                      
                        <div id="clubbing_from_month" style="display: none" class="form-group{{ $errors->has('from_month') ? ' has-error' : '' }}" >

                            <label for="from_month" class="col-md-4 control-label required">From Month</label>

                            <div class="col-md-6">


                                <select name="from_month" id="from_month" required class="form-control "
                                    onchange="pendingBeneficiary()">
                                    <option value="" selected>---Select Month---</option>
                                   
                                    @foreach(Config::get('constants.monthlist') as $key=>$month)
                                 @if($key=='April')
                                 <option value="{{ $key}}">{{$month}}</option>
                                     
                                 @endif
                                    
                                    @endforeach
                                </select>
                                @if ($errors->has('from_month'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('from_month') }}</strong>
                                </span>
                                @endif
                            </div>


                            
                        </div>

                        <div id="clubbing_to_month" style="display: none"  class="form-group{{ $errors->has('to_month') ? ' has-error' : '' }}" >
                        <label for="to_month" class="col-md-4 control-label required">To Month</label>

                        <div class="col-md-6">


                            <select name="to_month" id="to_month" required class="form-control "
                                onchange="pendingBeneficiary()">
                                <option value="" selected>---Select Month---</option>
                                @foreach(Config::get('constants.monthlist') as $key=>$month)
                                @if( $key=='May' || $key=='June')
                                <option value="{{ $key}}">{{$month}}</option>
                                    
                                @endif
                                
                                @endforeach
                            </select>
                            @if ($errors->has('to_month'))
                            <span class="help-block">
                                <strong>{{ $errors->first('to_month') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                


                    

                        <div style="display: none"
                            class="form-group {{ $errors->has('select_pmt_mode') ? ' has-error' : '' }}"
                            id="previous_lot">
                            <label for="select_pmt_mode" class="col-md-4 control-label required">Select Source Lots
                                Payment Mode</label>
                            <div class="col-md-6">
                                <select name="select_pmt_mode" id="select_pmt_mode" required class="form-control ">
                                    <option value="">---Select Source Payment Mode---</option>
                                    {{-- <option value="SBI">SBI</option>
                                    <option value="IFMS">IFMS</option> --}}
                                </select>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('select_target_mode') ? ' has-error' : '' }}">
                            <label for="select_target_mode" class="col-md-4 control-label required">Select Target
                                Payment Mode</label>
                            <div class="col-md-6">
                                <select name="select_target_mode" id="select_target_mode" required
                                    class="form-control ">
                                    <option value="">---Select Target Payment Mode---</option>
                                    {{-- <option value="SBI">SBI</option>
                                    <option value="IFMS">IFMS</option> --}}
                                </select>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('lot_size') ? ' has-error' : '' }}" id="lotsize_div">
                            <label for="lot_size" class="col-md-4 control-label required">Choose Lot Size</label>
                            <div class="col-md-6">
                                <select class="form-control select2" name="lot_size" required id="lot_size">
                                    <option value="">--Select Size--</option>

                                    @foreach(Config::get('constants.lot_size') as $key=>$lot)
                                    <option value="{{ $key}}">{{$lot}}</option>
                                    @endforeach

                                </select>
                                @if ($errors->has('lot_size'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('lot_size') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('lot_size') ? ' has-error' : '' }}" id="pending_div">
                            <label class="col-md-4 control-label">Beneficiary Pending</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="beneficiary_count" disabled>
                                <span id="spanpeningid" aria-hidden="true"></span>
                            </div>

                        </div>
                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-4">
                                <button type="submit" class="btn btn-primary btn-block">
                                    Submit
                                </button>
                            </div>
                        </div>

                    </form>
                    <div class="text-primary"><b></b></div>
                </div>
            </div>
            {{-- @php if($new_lot_no !='')
            { @endphp
            <div class="alert alert-success">
                <strong>New lot with Lot No:- {{$new_lot_no}} and number of Beneficiary : {{$ben_count}} has been
            created.</strong>
        </div>
        @php } @endphp --}}
    </div>
</div>
</div>

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
                                        let select_lot_type = $("#select_lot_type").val();
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
                        from_month:{
                            validators: {
                               
                                callback: {
                                    
                                    message: 'Please select from month',
                                    callback: function (value, validator, $field) {
                                        let select_lot_type = $("#select_lot_type").val();
                                        if (select_lot_type== "8" && value == '') {
                                            return {
                                                message: 'Select From Month',
                                                valid: false,
                                                
                                               
                                            };
                                       
                                        }
                                        
                                        return true;
                                    }
                                    
                                }
                            }
                        },

                        to_month:{
                            validators: {
                               
                                callback: {
                                    
                                    message: 'Please select to month',
                                    callback: function (value, validator, $field) {
                                        let select_lot_type = $("#select_lot_type").val();
                                        if (select_lot_type== "8" && value == '') {
                                            return {
                                                message: 'Select To Month',
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
                    var hiddenBencount=$('#hiddenBencount').val();
                var lotType=$('#select_lot_type').val();
              
         
                var lotScheme=$('#select_scheme').val();
                var lotMonth=$('#select_month').val();
                var lotYear=$('#select_year').val();
                var lotTargetMode=$('#select_target_mode').val();
                if(lotType!=2 && lotType!=8){
                if(hiddenBencount==0){
                $.confirm({
                type: 'red',
                icon: 'fa fa-warning',
                title: 'Error!!',
                content: 'You can not generate lot when beneficiary pending is 0.',
                });
                return false;
                }
// console.log(lotType);
// console.log(lotYear);
// console.log(lotMonth);
// console.log(lotScheme);
// console.log(lotTargetMode);
                if((lotType!="") && (lotYear!="") && (lotMonth!="") && (lotScheme!="") &&  (lotTargetMode!="") ){
                    e.preventDefault();
                    $('#loadingDiv').show();
            document.getElementById("repeat_lot_form").submit();
            console.log(1);
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

            // Prevent form submission
           

        });

       $('#from_month').change(function(){
        monthchecking();
       });

       $('#to_month').change(function(){
        monthchecking();
});
$('#select_year').change(function(){
        monthchecking();
});

    });
    // function validate() {
    //     if (document.getElementById('select_month').value == 0) {
    //         alert('Please select month');
    //         return false;
    //     }
    //     return true;
    // }

function monthchecking(){
                var yearval='';
                var finalyear='';
                var from_month=$('#from_month').val();
                var to_month=$('#to_month').val();
                var from_month_key= moment().month(from_month).format("M");
                var to_month_key= moment().month(to_month).format("M");
                
                var select_year=$('#select_year').val();
                var split_year = select_year.split("-");
               
                if((select_year!='') && (from_month!='') && (to_month!='')){
                // if(from_month_key==4){

                // finalyear=(split_year[0] -1) + '-' + split_year[0];
                // }
                // else{

                // finalyear=select_year;
                // }
                // console.log(finalyear);
                // if(finalyear!=select_year){
                // $.alert({
                // title: 'Error!!',
                // type: 'red',
                // icon: 'fa fa-warning',
                // content: 'Please select proper from month and financial year',
                // });
                // $('#to_month').val('').trigger('change');
                // $('#from_month').val('').trigger('change');
                // return false;
                // }
/***********************************************/
                // if(from_month_key>to_month_key){
                // $.alert({
                // title: 'Error!!',
                // type: 'red',
                // icon: 'fa fa-warning',
                // content: 'Please select proper from and to month.',
                // });
                // $('#to_month').val('').trigger('change');
                // $('#from_month').val('').trigger('change');
                // return false;
                // }
               
                }
         
}
    
function lot_type_change(){
    let select_lot_type=$('#select_lot_type').val();
    let scheme_id=$('#select_scheme').val();

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
            else if (select_lot_type==8){
                $('#previous_lot').show();
                $('#financial_month').hide();
                $('#clubbing_from_month').show();
                $('#clubbing_to_month').show();
                $('#pending_div').hide();
                $('#lotsize_div').hide();
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
    let select_lot_type=$('#select_lot_type').val();
    let scheme_id=$('#select_scheme').val();/*
  if(select_lot_type==2){
    $('#category_lot').hide();
  } else {
    if(scheme_id==-1 || scheme_id==7){
        $('#select_category').val('ALL').trigger('change');
        $('#category_lot').show();
    } else {
        $('#category_lot').hide();
    }
  }*/
  if(select_lot_type!=2 && (scheme_id==7 || scheme_id==-1)){
    
    $('#select_category').val('ALL').trigger('change');
    $('#category_lot').show();
  }
  else{
    $('#category_lot').hide();
  }
   
}
function pendingBeneficiary(){
            let select_category = $('#select_category').val();
            let select_lot_type = $('#select_lot_type').val();
            let select_scheme=$('#select_scheme').val();
            let select_month=$('#select_month').val();
            let select_year=$('#select_year').val();
           
            let token = $("input[name='_token']").val();

         
          if((select_lot_type!="") && (select_year!="") && (select_month!="") && (select_scheme!="") && (select_lot_type!="2")){ 
            let fd = new FormData();
            let action_url = '{{ route("getPendingBeneficiaryCount") }}';

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
            $('#lot_size').append('<option value="">--Select Size--</option>');
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
   
        let select_year=$('#select_year').val();
        let select_month=$('#select_month').val();
        let select_lot_type=$('#select_lot_type').val();
        let token = $("input[name='_token']").val();
        let fd = new FormData();

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
            let select_scheme = '{{Session::get('schemeSession')}}';
            @else 
            let select_scheme = $('#select_scheme').val();
            @endif
                
            let token = $("input[name='_token']").val();
            let fd = new FormData();
            let action_url = '{{ route("getPaymentMode") }}';

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
@endsection