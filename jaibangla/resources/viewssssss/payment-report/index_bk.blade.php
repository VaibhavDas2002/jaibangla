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
      background-image:url('images/ajaxgif.gif');
      background-repeat:no-repeat;
      background-position:center;
      z-index:10000000;
      opacity: 0.4;
      filter: alpha(opacity=40); /* For IE8 and earlier */
    }
    #updateDiv {
      border: 1px solid #d9d9d9;
      padding: 8px;  
      box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
    }
  </style>
  @extends('layouts.app-template-datatable_new')
  @section('content')
    <div class="content-wrapper">
      <!-- <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div> -->
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Payment Report
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
              <div class="panel-heading" style="font-size: 14px; font-weight: bold; font-style: italic;"><span id="panel-icon">Enter Filter Criteria</div>
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
                    <div class="row">
                      <div class="col-md-12" style="margin-bottom: 5px;">
                        <div class="col-md-3">
                          <label class="control-label">Scheme <span class="text-danger">*</span></label>
                          <select class="form-control select2" name="scheme_type" id='scheme_type' required >
                            <option value="">--Select Scheme--</option>
                            @foreach ($schemes as $scheme)
                            <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                            @endforeach
                          </select>
                          <span class="text-danger" id="error_scheme_type"></span>
                        </div>
                        <div class="col-md-3">
                          <label class="control-label">Payment Mode: <span class="text-danger">*</span></label>
                          <select class="form-control select2" name="mode" id='mode' required >
                            <option value="">--Select Mode--</option>
                            <option value="sbi">SBI</option>
                            <option value="ifms">IFMS</option>
                          </select> 
                          <span class="text-danger" id="error_payment_mode"></span>
                        </div>
                        @if ($district_visible)
                            <div class="col-md-3">
                                <label class="">District</label>
                                <select class="form-control select2" name="district" id='district'>
                                    <option value="">--All --</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->district_code }}"
                                            @if (old('district') == $district->district_code) selected @endif>
                                            {{ $district->district_name }}</option>
                                    @endforeach
                                </select> 
                                <span class="text-danger" id="error_payment_mode"></span>
                            </div>
                        @else
                            <input type="hidden" name="district" id="district"
                            value="{{ $district_code_fk }}" />
                        @endif

                        @if ($is_urban_visible)
                            <div class="col-md-3" id="divUrbanCode">
                                <label class="">Rural/ Urban</label>
                                <select class="form-control select2" name="urban_code" id='urban_code'>
                                    <option value="">--All --</option>
                                        @foreach (Config::get('constants.rural_urban') as $key => $val)
                                            <option value="{{ $key }}"
                                                @if (old('urban_code') == $key) selected @endif>
                                                {{ $val }}</option>
                                        @endforeach
                                </select> 
                                <span class="text-danger" id="error_urban_code"></span>
                            </div>
                        @else
                            <input type="hidden" name="urban_code" id="urban_code"
                            value="{{ $rural_urban_fk }}" />
                        @endif

                       
                       
                        <div class="col-md-3" style="margin-top: 23px;">
                          <button class="btn btn-primary" name="submit_btn" id="submit_btn" type="button" disabled><i class="fa fa-search"></i> Search</button> &nbsp;&nbsp;&nbsp;&nbsp;

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div id="res_div" style="display: none;">
              <div class="panel panel-default">
                <div class="panel-heading" id="panel_head" style="font-size: 14px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
                <div class="panel-body" style="padding: 5px; font-size: 14px;">
                  <div class="table-responsive">
                    <table id="example" class="table display" cellspacing="0" width="100%"> 
                      <thead style="font-size: 12px;">
                        <th>Applicant ID</th>
                        <th>Applicant Name</th>
                        <th>Father's Name</th>
                        <th>Mobile No</th>
                        <th>Block/Municipality</th>
                        <th>GP/Ward</th> 
                        <th>Age Group</th>
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
  <script>
    $(document).ready(function(){
      // Live Clock
      var interval = setInterval(function () {
      var momentNow = moment();
        $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
        $('.time-part').html(momentNow.format('hh:mm:ss A'));
      }, 100);
  
      $('#loadingDi').hide();
      $('#submit_btn').removeAttr('disabled');

      
      var error_scheme_type = '';
      var error_payment_mode = '';
      $('#submit_btn').click(function(){
        if($.trim($('#scheme_type').val()).length == 0){
          error_scheme_type = 'Scheme name is required';
          $('#error_scheme_type').text(error_scheme_type);
        }
        else{
          error_scheme_type = '';
          $('#error_scheme_type').text(error_scheme_type);
        }
  
        if($.trim($('#mode').val()).length == 0){
            error_payment_mode = 'Payment Mode is required';
          $('#error_payment_mode').text(error_payment_mode);
        }
        else{
            error_payment_mode = '';
          $('#error_payment_mode').text(error_payment_mode);
        }
  
        if( error_scheme_type != '' || error_payment_mode != ''){
          return false;
        }
        else{
          $('#loadingDi').show();
          $('#res_div').show();
          var msg = 'Scheme : '+$( "#scheme_type option:selected" ).text();
          $('#panel_head').text(msg);
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
            "pageLength":20,
            'lengthMenu': [[20, 50, 100, 1000], [20, 50, 100, 1000]],
            "serverSide": true,
            "processing":true,
            "bRetrieve": true,
            "oLanguage": {
              "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
            },
            "ajax": 
            {
              url: "{{ url('get-payment-data') }}",
              type: "POST",
              data:function(d){
                d.scheme_id = $('#scheme_type').val(),
                d.mode = $('#mode').val(),
                d.dist_code=$('#district').val(),
                d.rural_urban=$('#urban_code').val(),
                d._token= "{{csrf_token()}}"
              },
              error: function (jqXHR, textStatus, errorThrown) {
                $('#loadingDi').hide();
                $('.preloader1').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
              }
            },
            "initComplete":function(){
              $('#loadingDi').hide();
              //console.log('Data rendered successfully');
            },
            "columns": [
              { "data": "id" },
              { "data": "name" },
              { "data": "father_name" },
              { "data": "mobile_no" },
              { "data": "block_ulb_name"},
              { "data": "gp_ward_name"},
              { "data": "age_group" },
            ],
        
            "buttons": [
              {
                 extend: 'pdf',
                 footer: true,
                 pageSize:'A4',
                 //orientation: 'landscape',
                 pageMargins: [ 40, 60, 40, 60 ],
                 exportOptions: {
                      columns: [0,1,2,3,4,5,6],
  
                  }
                 },
                 {
                     extend: 'excel',
                     footer: true,
                     pageSize:'A4',
                     //orientation: 'landscape',
                     pageMargins: [ 40, 60, 40, 60 ],
                     exportOptions: {
                          columns: [0,1,2,3,4,5,6],
                          stripHtml: false,
                      }
                  },
              //'pdf','excel','print'
            ],
          });
        }
      });

    });
  
    // function getAgeGroup(val) {
    //   $.ajax({
    //     url: "{{ url('getAgeCohortGroupList') }}",
    //     type: "POST",
    //     data: { scheme_id:val, _token: '{{ csrf_token() }}' },
    //     success: function (response) {
    //       $('#age_group').html('');
    //       htmlOption = '<option value="">--Select--</option>';
    //       $.each(response, function (key, value) {
    //         htmlOption += '<option value="' + key + '">' + value + '</option>';
    //       });
    //       $('#age_group').append(htmlOption);
    //     },
    //     error: function (jqXHR, textStatus, errorThrown) {
    //       ajax_error(jqXHR, textStatus, errorThrown); 
    //     }
    //   }); 
    // }
  
    function redirectPost(url, data , method = 'post'){
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