<style type="text/css">
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

    .loadingDivModal {
        position: absolute;
        top: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        background-color: #fff;
        background-image: url('images/ajaxgif.gif');
        background-repeat: no-repeat;
        background-position: center;
        z-index: 10000000;
        opacity: 0.4;
        filter: alpha(opacity=40);
        /* For IE8 and earlier */
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
                Janma Mrityu Marked
            </h1>
            <ol class="breadcrumb">
                <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span
                        class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
            </ol>
        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <div id="loadingDiv"></div>
                    <div class="panel panel-default">
                        <div class="panel-heading" style="font-size: 15px; font-weight: bold; font-style: italic;"><span
                                id="panel-icon">Enter Filter Criteria</div>
                        <div class="panel-body" style="padding: 5px;">
                            <div class="row">
                                <div class="col-md-12">
                                    @if ($message = Session::get('success'))
                                        <div class="alert alert-success alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }} </strong>
                                        </div>
                                    @endif
                                    @if ($message = Session::get('message'))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    @if ($message = Session::get('msg1'))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            <div class="col-md-4">
                                                <label class=" control-label">Scheme <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="scheme_id" id='scheme_id' required>
                                                    <option value="">--Select Scheme--</option>
                                                    @foreach ($schemes as $scheme)
                                                        <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="error_scheme_id"></span>
                                            </div>
                                           
                                            <div class="col-md-4" style="margin-top: 24px;">
                                                <button class="btn btn-success" name="submit_btn" id="submit_btn" type="button"><i class="fa fa-check"></i>
                                                    Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert print-error-msg" style="display:none;" id="errorDiv">
                        <button type="button" class="close" aria-label="Close" onclick="closeError('errorDiv')"><span
                                aria-hidden="true">&times;</span></button>
                        <ul></ul>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
<script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script>
    $(document).ready(function() {
        // Live Clock
        var interval = setInterval(function() {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);
        $('#loadingDiv').hide();
        $('#submit_btn').removeAttr('disabled');
        $('#reset_btn').removeAttr('disabled');

        // Master drop down 
        $('#district').change(function() {
            var district = $(this).val();
            //alert(district);
            $('#urban_code').val('');
            $('#block').html('<option value="">--All --</option>');
            $('#muncid').html('<option value="">--All --</option>');
        });

        // End Master drop down
        var error_scheme_id = '';
        var error_search_for = '';
        var error_district = '';
        $('#submit_btn').click(function() {
            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }
            if (error_scheme_id != '') {
                return false;
            } else {
                $('#loadingDiv').show();
                var scheme_code = $('#scheme_id').val();
                // alert(scheme_code); 
                $.ajax({
                type: 'POST',
                url: "{{ route('schemeWiseJnmpMarked') }}",
                data: {
                    scheme_id: scheme_code,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loadingDiv').hide();
                    if (response.status == 1) {
                        $.alert({
                            title: response.title,
                            type: response.type,
                            icon: response.icon,
                            content: response.msg
                        });
                    } 
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loadingDiv').hide();
                    ajax_error(jqXHR, textStatus, errorThrown);
                }
                });
            }
                
        });
        // Export Excel
        $('#excel_btn').click(function() {
            var error_scheme_id = '';
            var error_search_for = '';
            var error_district = '';
            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }
            if (error_scheme_id != '') {
                return false;
            } else {
                // var search_option = $('#search_for').val();
                var scheme_code = $('#scheme_id').val();
                var district = $('#district').val();
                var urban_code = $('#urban_code').val();
                var block = $('#block').val();
                var gp_ward = $('#gp_ward').val();
                var muncid = $('#muncid').val();
                var token = "{{ csrf_token() }}";
                var data = {
                    '_token': token,
                    scheme_id: scheme_code,
                    district: district,
                    urban_code: urban_code,
                    block: block,
                    gp_ward: gp_ward,
                    $muncid: muncid
                };
                redirectPost('generateExcel', data);
            }
        });
    });

    function viewModalFunction(value,scheme_id){
        $('.loadingDivModal').show();
        $.ajax({
        type: 'POST',
        url: "{{ route('modalViewData') }}",
        data: {
            scheme_id: scheme_id,
            id: value,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('.loadingDivModal').hide();
            if (response.status == 1) {
            $.alert({
                title: response.title,
                type: response.type,
                icon: response.icon,
                content: response.msg
            });
            $('#submit_btn').trigger('click');
            $("html, body").animate({
                scrollTop: 0
            }, "slow");
            } else {
            $('#update_scheme_id').val('');
            $('#pension_id').val('');
            $('#old_aadhar_no').val('');
            $('#doc_117').val('');
            $('#remarks').val('');
            $('#doc_117').removeClass('has-error');
            $('#error_doc_117').text('');
            $('#name_div').text(response.ben_name);
            $('#father_div').text(response.father_name);
            $('#mobile_div').text(response.mobile_no);
            $('#gender_div').text(response.gender);
            $('#update_scheme_id').val(response.scheme_id);
            $('#pension_id').val(response.id);
            $('#application_id').text(response.id);
            $('#name_jnmp_div').text(response.jnmp_fullname);
            $('#death_div').text(response.jnmp_date_of_death);
            var file_msg = '(Image type must be '+response.doc_type+' and image size max '+response.doc_size_kb+' KB)';
            $('#file_msg').text(file_msg);
            $('.loadingDivModal').hide();
            $('#modalUpdateAadhar').modal('show');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $('.loadingDivModal').hide();
            ajax_error(jqXHR, textStatus, errorThrown);
        }
        });
    }

    $(document).on('click', '#verifySubmit', function() {
    var error_doc_117 = '';
    var error_remarks = '';
    var error_reactive_reason = '';
    var remarks = $('#remarks').val();
    var reactive_reason = $('#reactive_reason').val();
    var file_sp = document.getElementById("doc_117");
    var file_attachment = file_sp.files[0];

    if(file_sp.value!='') {
      error_doc_117 = '';
        $('#error_doc_117').text(error_doc_117);
        $('#doc_117').removeClass('has-error');
    } else {
      error_doc_117 = 'Supproting Document is required';
      $('#error_doc_117').text(error_doc_117);
      $('#doc_117').addClass('has-error');
    }

    if (reactive_reason != '') {
        error_reactive_reason = '';
        $('#error_reactive_reason').text(error_reactive_reason);
        $('#reactive_reason').removeClass('has-error');
    } else {
        error_reactive_reason = 'Re-active reason is required.';
        $('#error_reactive_reason').text(error_reactive_reason);
        $('#reactive_reason').addClass('has-error');
    }

    if (remarks != '') {
        error_remarks = '';
        $('#error_remarks').text(error_remarks);
        $('#remarks').removeClass('has-error');
    } else {
        error_remarks = 'Remarks is required.';
        $('#error_remarks').text(error_remarks);
        $('#remarks').addClass('has-error');
    }

    if (error_doc_117 != '' || error_reactive_reason != '' || error_remarks != '') {
      return false;
    } else {
      // alert('OK');
      $.confirm({
        type: 'orange',
        title: 'Confirmation!',
        content: 'Are you want to active this beneficiary ? <br> <span style="color: black;"><b>Note: After activation this beneficiary will started to get payment.</b></span>',
        icon: 'fa fa-warning',
        buttons: {
          confirm: {
            text: 'Confirm',
            btnClass: 'btn-blue',
            keys: ['enter', 'shift'],
            action: function() {
              // alert('OK');
              var beneficiary_Id = $('#pension_id').val();
              var updateSchemeId = $('#update_scheme_id').val();
              var reactive_reason = $('#reactive_reason').val();
            //   alert(updateSchemeId);
              var remarks = $('#remarks').val();
              var formData = new FormData();
              var files = $('#doc_117')[0].files;
              formData.append('doc_117', files[0]);
              formData.append('id', beneficiary_Id);
              formData.append('scheme_id', updateSchemeId);
              formData.append('reactive_reason', reactive_reason);
              formData.append('remarks', remarks);
              formData.append('_token', '{{ csrf_token() }}');
              $('.loadingDivModal').show();
              $.ajax({
                type: 'POST',
                url: "{{ route('activeBeneficiary') }}",
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response) {
                  $('.loadingDivModal').hide();
                  if (response.status == 1) {
                    $.alert({
                      title: response.title,
                      type: response.type,
                      icon: response.icon,
                      content: response.msg
                    });
                    $('#modalUpdateAadhar').modal('hide');
                    $('#res_div').hide();
                    // $('#scheme_type').val('').trigger('change');
                    $('#submit_btn').trigger('click');
                    $("html, body").animate({
                      scrollTop: 0
                    }, "slow");
                  } else {
                    var html = '';
                    html += '<ul>';
                    if (Array.isArray(response.msg)) {
                      $.each(response.msg, function(key, value) {
                        html += '<li>' + value + '</li>';
                      });
                    } else {
                      html = '<li>' + response.msg + '</li>';
                    }
                    html += '<ul>';
                    $.alert({
                      title: response.title,
                      type: response.type,
                      icon: response.icon,
                      content: html
                    });
                  }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                  $('.loadingDivModal').hide();
                  ajax_error(jqXHR, textStatus, errorThrown);
                }
              });
            }
          },
          cancel: function() {},
        }
      });
    }
  });

    function redirectPost(url, data, method = 'post') {
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

    function ajax_error(jqXHR, textStatus, errorThrown) {
        var msg = "<strong>Failed to Load data.</strong><br/>";
        if (jqXHR.status !== 422 && jqXHR.status !== 400) {
            msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
        } else {
            if (jqXHR.responseJSON.hasOwnProperty('exception')) {
                msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
            } else {
                msg += "Error(s):<strong><ul>";
                $.each(jqXHR.responseJSON, function(key, value) {
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
