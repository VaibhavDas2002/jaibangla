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
        padding: 5px;
        color: #555;
        font-size: 12px;
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

    .required:after {
        color: red;
        content: '*';
        font-weight: bold;
        margin-left: 5px;
        float: right;
        margin-top: 5px;
    }

    #loadingDivModal {
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

    .disabledcontent {
        pointer-events: none;
        opacity: 0.4;
    }
</style>

@extends('layouts.app-template-datatable_new')
@section('content')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
              Bank Info Change Log
            </h1>

        </section>
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div>
                        <!-- class="box box-primary" -->
                        {{-- <div class="box-header with-border">
                            <h3 class="box-title"><b>

                                    Mis Report

                                </b></h3>
                            <!-- <p><h3 class="box-title"><b>Bandhu Prakalpa (for SC)</b></h3></p> -->
                        </div> --}}

                        <div>
                            @if (($message = Session::get('success')))
                                <div class="alert alert-success alert-block">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>{{ $message }}</strong>


                                </div>
                            @endif
                           
                            @if (count($errors) > 0)
                                <div class="alert alert-danger alert-block">
                                    <ul>
                                        @foreach ($errors as $error)
                                            <li><strong> {{ $error }}</strong></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>





                        <div class="tab-content" style="margin-top:16px;">




                            <div class="tab-pane active" id="personal_details">
                                <div class="panel panel-default">
                                    
                                    <div class="panel-body">
                                        <div class="row">
                                            
                                            <div class="form-group col-md-4">
                                                <label class=" control-label">Police Case Number</label>
                                            </div>
                                            <div class="form-group col-md-8">
                                                <span  class="text-info">{{$ps_master_data->case_no}}</span>
                                            </div>
                                          </div>
                                         
                                         
                                           
                                       

                                          

                                            <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                                        </div>
                                        <br />
                                    </div>
                                    <table class="table table-striped">
                                        <thead>
                                          <tr>
                                            <th scope="col">Application Id</th>
                                            <th scope="col"></th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($distinct_app_id_arr)>0)
                                            @foreach ($distinct_app_id_arr as $item)
                                            <tr>
                                                <td>{{$item}}</td>
                                                <td>
                                                    <form method="post" id="banklog" name="banklog" action="{{url('bank-info-change-log-post')}}"  class=""  autocomplete="off">

                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="case_id" value="{{$ps_master_data->id}}"/>
                                                        <input type="hidden"  name="application_id" id="application_id" value="{{$item}}" >
                                                    <button  type="submit"  class="btn btn-success tagNew">Download  PDF</button>
                                                    </form>
                                                </td>
                                              </tr> 
                                            @endforeach
                                         @else
                                         <tr>
                                            <td colspan="2">No Record Found</td>
                                         </tr>
                                          @endif
                                        </tbody>
                                      </table>
                                </div>
                            </div>
                        
                            
                        </div>
                    </div>





                </div>






            </div>
            <!-- /.box -->
    </div>
    <!--/.col (left) -->

    </div>
  </div>
    </section>
    </div>


@endsection
@section('script')
    <script src="{{ URL::asset('js/validateAdhar.js') }}"></script>
    <script>
        //alert(base_date);

        $(document).ready(function() {
            $('.sidebar-menu li').removeClass('active');
            // $('.sidebar-menu #lk-main').addClass("active");
            $('.sidebar-menu #bankModification').addClass("active");
            $(".NumOnly").keyup(function(event) {
              
              $(this).val($(this).val().replace(/[^\d].+/, ""));
                  if ((event.which < 48 || event.which > 57)) {
                      event.preventDefault();
                  }
              }); 
            
             
        });

        $(document).on('click', '#submitting', function(e) {
            e.preventDefault()
            var error_application_id = '';
            var application_id = $('#application_id').val();
            if($.trim($('#application_id').val()).length == 0)
            {
                error_application_id = 'Application Id is required';
                $('#error_application_id').text(error_application_id);
                $('#application_id').addClass('has-error');
            } 
            else{
               
                    error_application_id = '';
                    $('#error_application_id').text(error_application_id);
                    $('#aadhar_no').removeClass('has-error');
                
            }
            //error_application_id='';
            if (error_application_id != '') {
                return false;
            } else {
                // alert('OK');
               // $('#submit_loader1').show();
                $("#banklog").submit();

               
            }
        });

       

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
    </script>
@stop
