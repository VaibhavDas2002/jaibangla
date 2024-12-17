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
  #loadingDi {
    position:absolute;
    top:0px;
    right:0px;
    width:100%;
    height:100%;
    background-color:#fff;
    background-image:url('../images/ajaxgif.gif');
    background-repeat:no-repeat;
    background-position:center;
    z-index:10000000;
    opacity: 0.4;
    filter: alpha(opacity=40); /* For IE8 and earlier */
  }
  .panel-heading {
    font-size: 15px; 
    font-weight: bold; 
    font-style: italic;
  }
  select {
    font-size: 14px; 
    height: 33px; 
    border-radius: 3px; 
    width: 230px;
    background-color: whitesmoke;
  }
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <!-- <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        De-duplicate Bank A/c Report
      </h1>
      <ol class="breadcrumb">
        <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
      </ol>
    </section>
    <section class="content">
      <div class="box box-default">
        <div class="box-body">
          <div id="loadingDi"></div>
          <div class="panel panel-default">
            <div class="panel-heading">Enter Filter Criteria</div>
            <div class="panel-body">
              <div class="row">
              
                <div class="form-group col-md-3">
                 <label class="required-field">Select Scheme <span class="text-danger">*</span></label><br>
                 <select name="scheme_code" id="scheme_code" class="" tabindex="1" >
                  <option value="">-- Select --</option>
                  @foreach ($schemes as $scheme)
                  <option value="{{$scheme->id}}"  @if(old('scheme_code')== $scheme->id)  selected  @endif> {{$scheme->scheme_name}}</option>
                  @endforeach
                 
                </select>
                 <span id="error_scheme_code" class="text-danger"></span>

                </div>
               
              
                            
             
              @if($district_visible)
               <div class="form-group col-md-3">
                 <label class="">District</label>
                 <select name="district" id="district" class="" tabindex="6" >
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
                @if($is_urban_visible)
              <div class="form-group col-md-3" id="divUrbanCode">
                <label class="">Rural/ Urban</label>
                
                <select name="urban_code" id="urban_code" class="" tabindex="11" >
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
              <div class="form-group col-md-3" style="margin-top: 23px;">
                <button type="button"  id="submitting" value="Submit" class="btn btn-success success modal-search form-submitted" ><i class="fa fa-search"></i> Search </button>
                 
                 <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" id="submit_loader1" width="50px" height="50px" style="display:none;" ></div>
              </div>
              {{-- @if($block_visible)
                <div class="form-group col-md-4" id="divBodyCode">
                <label class="" id="blk_sub_txt">Block/Sub Division.</label>
                
                <select name="block" id="block" class="" tabindex="16" >
                  <option value="">--All --</option>
                  
                   
                </select>
                  <span id="error_block" class="text-danger"></span>
              </div>
               @else
              <input type="hidden" name="block" id="block" value="{{$block_munc_corp_code_fk}}"/>

               @endif
              
                <div class="form-group col-md-4" id="municipality_div" style="{{$municipality_visible?'':'display:none'}}">
                <label class="">Municipality</label>
                
                <select name="muncid" id="muncid" class="" tabindex="16" >
                  <option value="">--All --</option>
                    @foreach ($muncList as $munc)
                  <option value="{{$munc->urban_body_code}}"> {{$munc->urban_body_name}}</option>
                  @endforeach
                   
                </select>
                  <span id="error_muncid" class="text-danger"></span>
              </div>
               
                
            <div class="form-group col-md-4" id="gp_ward_div" style="{{$gp_ward_visible?'':'display:none'}}">
                <label class="" id="gp_ward_txt">GP/Ward</label>
                
                <select name="gp_ward" id="gp_ward" class="" tabindex="17" >
                  <option value="">--All --</option>
                   @foreach ($gpList as $gp)
                  <option value="{{$gp->gram_panchyat_code}}"> {{$gp->gram_panchyat_name}}</option>
                  @endforeach
                   
                </select>
                  <span id="error_gp_ward" class="text-danger"></span>
              </div> --}}
              
             
              </div>
              {{-- <div class="row">
                <div class="col-md-12" align="center">

                  <button type="button"  id="submitting" value="Submit" class="btn btn-success success modal-search form-submitted" >Search </button>
                 
                 <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" id="submit_loader1" width="50px" height="50px" style="display:none;" ></div>
                
                 <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                </div>
                <br />
               </div> --}}
            </div>
          </div>

          <div class="alert print-error-msg"  style="display:none;" id="errorDiv">
               <button type="button" class="close"  aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
               <ul></ul>
          </div>

          <div class="tab-pane active" id="search_details" style="display:none;">
              <div class="panel panel-default">
               <div class="panel-heading" id="heading_msg">Search Result</div>
               <div class="panel-body">

                <div class="pull-right" style="font-size: 12px;">Report Generated on:<b><?php echo date("l jS \of F Y h:i:s A"); ?></b></div>


             <table id="example" class="table table-striped table-bordered" style="width:100%; font-size: 14px;">
        <thead style="font-size: 12px;">
            <tr>
                <th id="">Sl No.(A)</th>
                <th id="location_id" width="25%">District(B)</th>
                <th>Total beneficiaries on which payment has been stopped on duplicate bank account(C)</th>
                <th>Total Edited with different Bank Account(D)</th>
                <th>Total Edited with same Bank Account(E)</th>
                <th>Total Rejected(F)</th>
                <th>Payment has been Initiated among Duplicate Bank Edited Beneficiaries(G)</th>
            </tr>
            
        </thead>
        <tbody>
            
        </tbody>
        <tfoot>
            <tr>
               <th></th>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
                
              
                 
              
               </div>
              </div>
             </div> 
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
@endsection
<script src="{{ URL::asset('js/site.js') }}"></script>
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
  $(document).ready(function(){
    // Live Clock
    var interval = setInterval(function () {
    var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);

    $('#loadingDi').hide();

    /*----------  master data section ---------------*/
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
    /*---------- End master data section ---------*/

    $('.modal-search').on('click',function(){
      var scheme_code=$('#scheme_code').val();
      if(scheme_code!=''){
        loadDataTable();
      } else{
        alert('Please Select Scheme');
        $('#scheme_code').focus();
      }
    });
  });
  
  function loadDataTable(){
  var scheme_code=$('#scheme_code').val();
   var district=$('#district').val();
  var urban_code=$('#urban_code').val();
  var block=$('#block').val();
  var gp_ward=$('#gp_ward').val();
  var muncid=$('#muncid').val();

     $("#submit_loader1").show();
     $("#submitting").hide();
     $('#search_details').hide();
        $.ajax({
                type: 'post',
                dataType:'json',
                url: '{{ url('deDuplicateBankReportGetData') }}',
                data: {
                  scheme_code: scheme_code,
                  district: district,
                  urban_code: urban_code,
                  block: block,
                  gp_ward: gp_ward,
                  muncid: muncid,
                  _token: '{{ csrf_token() }}',
                },
                success: function (data) {
                 
                  //alert(data.title);
                  if(data.return_status){
                     $('#search_details').show();
                    $("#heading_msg").html(data.heading_msg);
                    $("#location_id").text(data.column+'(B)');
                    if ( $.fn.DataTable.isDataTable('#example') ) {
                    $('#example').DataTable().destroy();
                  }
                    $("#example > tbody").html("");
                   var table = $("#example tbody");
                   var slno=1;
                   $.each(data.row_data, function(i, item) {
                    var tot_dup = isNaN(parseInt(item.tot_dup)) ? 0 : parseInt(item.tot_dup);
                     var total_edit_differ = isNaN(parseInt(item.total_edit_differ)) ? 0 : parseInt(item.total_edit_differ);
                     var total_edit_same = isNaN(parseInt(item.total_edit_same)) ? 0 : parseInt(item.total_edit_same);
                     var total_rejected = isNaN(parseInt(item.total_rejected)) ? 0 : parseInt(item.total_rejected);
                     var tot_initiated = isNaN(parseInt(item.tot_initiated)) ? 0 : parseInt(item.tot_initiated);
                     table.append("<tr><td>"+(i+1)+"</td><td>"+item.location_name+"</td><td>"+tot_dup+"</td><td>"+total_edit_differ+"</td><td>"+total_edit_same+"</td><td>"+total_rejected+"</td><td>"+tot_initiated+"</td></tr>");
                      //slno++;

                  });
                  

                  //$('#example tbody').empty();
                   $("#example").show();
                   $('#example').dataTable({
                     "paging":   false,
                     "ordering": false,
                     // "searching": false,
                     "info":     false,
                      "scrollX": true,
                      "dom": 'Bfrtip',
                      "buttons": [
                                // 'copy',
                                {
                                    extend: 'excel',
                                    text: 'Export To Excel',
                                    className: "btn btn-info",
                                    footer: true ,
                                    title: data.title,
                                    messageTop: data.heading_msg
                                }
                                // {
                                //     extend: 'pdf',
                                //     title: data.title,
                                //     footer: true ,
                                //     messageTop: data.heading_msg
                                // }
                                
                            ],
                  "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api(), data;
            
                        // converting to interger to find total
                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };
            
                        // computing column Total of the complete result 
                 var fotter_2 = api
                            .column( 2 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                    
                  var fotter_3 = api
                            .column( 3 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                  var fotter_4 = api
                            .column( 4 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                    
                  var fotter_5 = api
                            .column( 5 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                  
                    var fotter_6 = api
                            .column( 6 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                        // Update footer by showing the total with the reference of the column index 
                        $( api.column( 0 ).footer() ).html('');
                        $( api.column( 1 ).footer() ).html('Total');
                        $( api.column( 2 ).footer() ).html(fotter_2);
                        $( api.column( 3 ).footer() ).html(fotter_3);
                        $( api.column( 4 ).footer() ).html(fotter_4);
                        $( api.column( 5 ).footer() ).html(fotter_5);
                        $( api.column( 6 ).footer() ).html(fotter_6);
                    }
                } );
                   $('.buttons-excel').removeClass('dt-button');
                  }
                  else{
                     $('#search_details').hide();
                     $("#example").hide();
                     printMsg(data.return_msg,'0','errorDiv');
                  }
                  $("#submit_loader1").hide();
                  $("#submitting").show();

                },
                error: function (jqXHR, textStatus, errorThrown) {
                  $("#submit_loader1").hide();
                  //$("#submitting").hide();
                  $("#submitting").show();
                 /// alert('Something wrong..may be session timeout. please logout and then login again');
                //  location.reload();
                   ajax_error(jqXHR, textStatus, errorThrown)
                }
              });
   
  }

  function printMsg (msg,msgtype,divid) {
    $("#"+divid).find("ul").html('');
    $("#"+divid).css('display','block');
    if(msgtype=='0'){
      //alert('error');
      $("#"+divid).removeClass('alert-success');
      //$('.print-error-msg').removeClass('alert-warning');
      $("#"+divid).addClass('alert-warning');
    }
    else{
      $("#"+divid).removeClass('alert-warning');
      $("#"+divid).addClass('alert-success');
    }
    if(Array.isArray(msg)){
      $.each( msg, function( key, value ) {
        $("#"+divid).find("ul").append('<li>'+value+'</li>');
      });
    }
    else{
      $("#"+divid).find("ul").append('<li>'+msg+'</li>');
    }
  }

  function closeError(divId){
    $('#'+divId).hide();
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