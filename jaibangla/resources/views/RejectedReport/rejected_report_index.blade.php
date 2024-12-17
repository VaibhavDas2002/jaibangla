<style type="text/css">
  .has-error
  {
    border-color:#cc0000;
    background-color:#ffff99;
  }
  .preloader1{
    position: fixed;
    top:40%;
    left: 52%;
    z-index: 999;
  }
  .preloader1 {
    background: transparent !important;
  }
  .loadingDivModal{
    position:absolute;
    top:0px;
    right:0px;
    width:100%;
    height:100%;
    background-color:#fff;
    background-image:url('images/ajaxgif.gif');
    background-repeat:no-repeat;
    background-position:center;
    z-index:10000000;
    opacity: 0.4;
    filter: alpha(opacity=40); /* For IE8 and earlier */
  }
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <!-- <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Rejected Beneficiary List
      </h1>
      <ol class="breadcrumb">
        <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
      </ol>
    </section>
    <section class="content">
      <div class="box box-default">
        <div class="box-body">
          <div id="loadingDiv"></div>
          <div class="panel panel-default">
            <div class="panel-heading"><span id="panel-icon">Filter Here</div>
            <div class="panel-body" style="padding: 5px;">
              <div class="row">
                <div class="col-md-12">
                  @if (($message = Session::get('success')) )
                  <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }} </strong>
                  </div>
                  @endif
                  @if (($message = Session::get('message')))
                  <div class="alert alert-danger alert-block">
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
                  <!-- <form class="form-horizontal" role="form" method="POST" action="{{ route('search-by-name') }}" id="submit_form"> -->
                    {{csrf_field()}}
                    <div class="row">
              
                
                 <div class="form-group col-md-4">
                 <label class="required-field">Scheme</label>
                 <select name="scheme_id" id="scheme_id" class="form-control" tabindex="6" >
                   @foreach ($sceme_list as $scheme)
                  <option value="{{$scheme->id}}"> {{$scheme->scheme_name}}</option>
                  @endforeach
                </select>
                 <span id="error_scheme_id" class="text-danger"></span>

                </div>

              @if($district_visible)
               <div class="form-group col-md-4">
                 <label class="">District</label>
                 <select name="district" id="district" class="form-control" tabindex="6" >
                  <option value="">--All  --</option>
                   @foreach ($districts as $district)
                  <option value="{{$district->district_code}}"  @if(old('district')== $district->district_code)  selected  @endif> {{$district->district_name}}</option>
                  @endforeach
                </select>
                 <span id="error_district" class="text-danger"></span>

                </div>
                @else
                <input type="hidden" name="district" id="district" value="{{$district_code_fk}}"/>
                @endif

                {{-- 
                @if($is_urban_visible)
              <div class="form-group col-md-4" id="divUrbanCode">
                <label class="">Rural/ Urban</label>
                
                <select name="urban_code" id="urban_code" class="form-control" tabindex="11" >
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
                
                <select name="block" id="block" class="form-control" tabindex="16" >
                  <option value="">--All --</option>
                  
                   
                </select>
                  <span id="error_block" class="text-danger"></span>
              </div>
               @else
              <input type="hidden" name="block" id="block" value="{{$block_munc_corp_code_fk}}"/>

               @endif
              
                <div class="form-group col-md-4" id="municipality_div" style="{{$municipality_visible?'':'display:none'}}">
                <label class="">Municipality</label>
                
                <select name="muncid" id="muncid" class="form-control" tabindex="16" >
                  <option value="">--All --</option>
                    @foreach ($muncList as $munc)
                  <option value="{{$munc->urban_body_code}}"> {{$munc->urban_body_name}}</option>
                  @endforeach
                   
                </select>
                  <span id="error_muncid" class="text-danger"></span>
              </div>
               
                
            <div class="form-group col-md-4" id="gp_ward_div" style="{{$gp_ward_visible?'':'display:none'}}">
                <label class="" id="gp_ward_txt">GP/Ward</label>
                
                <select name="gp_ward" id="gp_ward" class="form-control" tabindex="17" >
                  <option value="">--All --</option>
                   @foreach ($gpList as $gp)
                  <option value="{{$gp->gram_panchyat_code}}"> {{$gp->gram_panchyat_name}}</option>
                  @endforeach
                   
                </select>
                  <span id="error_gp_ward" class="text-danger"></span>
              </div>
               --}}
            
               </div>
                    <div class="row">
                      <div class="col-md-12" align="center">
                        <button class="btn btn-primary" id="submit_btn" type="button" style="width: 200px;" disabled><i class="fa fa-search"></i> Search</button> &nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-info" id="excel_btn" type="button" style="width: 200px;"><i class="fa fa-file-excel-o"></i> Export All Data to Excel</button>
                      </div>
                    </div>
                  <!-- </form> -->
                </div>
              </div>
            </div>
          </div>
          
          <div id="res_div" style="display: none;">
            <div class="panel panel-default">
              <div class="panel-heading" id="panel_head">List of Beneficiary</div>
              <div class="panel-body" style="padding: 5px; font-size: 14px;">
                <div class="table-responsive">
                  <table id="example" class="table display" cellspacing="0" width="100%"> 
                    <thead style="font-size: 12px;">
                      <th>Sl No.</th>
                      <th>Pension ID</th>
                      <th>Name</th>
                      <th>Father's Name</th>
                      <th>Mother's Name</th>
                      <th>Ration Card No</th>
                      <th>Voter ID</th> 
                      <th>Address</th>
                      <th>Mobile No</th>
                      <th>IFSC</th>
                      <th>Account No</th>
                      <th>Remarks</th>
                      <th>Rejection Date</th>
                    </thead>
                    <tbody style="font-size: 14px;"></tbody>   
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->

  </div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script>
  $(document).ready(function(){
    // Live Clock
    var interval = setInterval(function () {
    var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);

    $('#loadingDiv').hide();
    $('#submit_btn').removeAttr('disabled');

    $('#excel_btn').click(function(){
      var scheme_id=$('#scheme_id').val();
      var district=$('#district').val();
      // var urban_code=$('#urban_code').val();
      // var block=$('#block').val();
      // var gp_ward=$('#gp_ward').val();
      // var muncid=$('#muncid').val();
      if (scheme_id == '') {
        alert('Scheme for download excel.');
      } else {
        var  data= {'_token': '{{csrf_token()}}', 'scheme_id': scheme_id, 'district': district };
        redirectPostExcel('{{route("getAllRejectedDataListExcelData")}}', data, 'post');
      }
      
    });

    /*============ Main Drop Down section =============*/
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
    /*============= End Main Drop Down section ============*/

    error_scheme_id='';
    error_lot_month='';
    error_lot_year='';
    $('#submit_btn').click(function(){
      if($.trim($('#scheme_id').val()).length == 0){
        error_scheme_id = 'Scheme is required';
        $('#error_scheme_id').text(error_scheme_id);
      }
      else{
        error_scheme_id = '';
        $('#error_scheme_id').text(error_scheme_id);
      }

      if( error_scheme_id != ''){
        return false;
      }
      else{
        $('#loadingDiv').show();
        $('#res_div').show();
        var scheme_id=$('#scheme_id').val();
        var district=$('#district').val();
        var urban_code=$('#urban_code').val();
        var block=$('#block').val();
        var gp_ward=$('#gp_ward').val();
        var muncid=$('#muncid').val();

        if ( $.fn.DataTable.isDataTable('#example') ) {
          $('#example').DataTable().destroy();
        }
        var table=$('#example').DataTable( {
          dom: 'Blfrtip',
          "scrollX": true,
          "paging": true,
          "searchable": true,
          "ordering":false,
          "bFilter": true,
          "bInfo": true,
          "pageLength":100,
          'lengthMenu': [[100, 500, 1000, 2000], [100, 500, 1000, 2000]],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><span class="text-success" style="font-size: 20px; font-weight: bold;">Processing..</span></div>'
          },
          "ajax": 
          {
            url: "{{ route('getAllRejectedDataList') }}",
            type: "post",
            data:function(d){
              d.scheme_id= scheme_id,
              d.district= district,
              d.urban_code= urban_code,
              d.block= block,
              d.gp_ward= gp_ward,
              d.muncid= muncid,
              d._token= '{{ csrf_token() }}'
            },
            error: function (jqXHR, textStatus, errorThrown) {
              $('#loadingDiv').hide();
              // ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete":function(){
            $('#loadingDiv').hide();
            //console.log('Data rendered successfully');
          },
          "columns": [
            { "data": 'DT_RowIndex'},
            { "data": "pension_id"},
            { "data": "name"},
            { "data": "fathers_name"},
            { "data": "mothers_name"},
            { "data": "ration_card_no"},
            { "data": "voter_id"},
            { "data": "address"},
            { "data": "mobile_no"},
            { "data": "ifsc"},
            { "data": "account_no"},
            { "data": "remarks"},
            { "data": "rejection_date"}
          ],
      
          "buttons": [
            'excel'
          ],
        });
      }
    });
    
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

  function ajax_error(jqXHR, textStatus, errorThrown){
    var msg = "<strong>Failed to Load data.</strong><br/>";
    if (jqXHR.status !== 422 && jqXHR.status !== 400) {
      msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
    } 
    else {
      if (jqXHR.responseJSON.hasOwnProperty('exception')) {
        msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
      } 
      else {
        msg += "Error(s):<strong><ul>";
        $.each(jqXHR.responseJSON, function (key, value) {
          msg += "<li>" + value + "</li>";
        });
        msg += "</ul></strong>";
      }
    }
    $.alert({
      title: 'Error!!',
      type: 'red',
      icon: 'fa fa-warning',
      content: msg,
    });
  }
</script>