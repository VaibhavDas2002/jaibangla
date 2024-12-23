<!DOCTYPE html>

<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>JB | Jai Bangla</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet"
    type="text/css" />
  <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
  <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet"
    type="text/css" />

  <!-- bootstrap wysihtml5 - text editor -->
  <!-- <link rel="stylesheet" href="{{ asset("/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}"> -->

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet"
    type="text/css" />

  <style>
    * {
      font-size: 15px;
    }

    .field-name {
      float: left;
      font-weight: 600;
      font-size: 17px;
      margin-right: 3%;
      padding-top: 1%;
    }

    .field-value {


      font-size: 17px;
      padding-top: 1%;

    }

    .required-field::after {
      content: "*";
      color: red;
    }

    .row {
      margin-right: 0px !important;
      margin-left: 0px !important;
    }

    .section1 {
      border: 1.5px solid #9187878c;
      overflow: hidden;
      padding-bottom: 10px;


    }

    .color1 {

      background-color: #dcdfdf;
    }

    .color1 h3 {
      margin: 10px 0px 10px 0px !important;
    }

    .setPos {
      padding: 0px 0px 10px 0px;
      margin: 10px 0px 10px 0px;
      border: 1px solid #dcdfdf;
      overflow: hidden;
    }

    .modal_field_name {
      float: left;
      font-weight: 700;
      margin-right: 1%;
      padding-top: 1%;
      margin-top: 1%;
    }

    .modal_field_value {
      margin-right: 1%;
      padding-top: 1%;
      margin-top: 1%;
    }

    .modal-header {
      background-color: #7fffd4;
    }

    @media print {
      .example-screen {
        display: none;
      }

      * {
        font-size: 15px;
      }

      .field-name {
        float: left;
        font-weight: 600;
        font-size: 17px;
        margin-right: 3%;
        padding-top: 1%;
      }

      .field-value {


        font-size: 17px;
        padding-top: 1%;

      }

      .row {
        margin-right: 0px !important;
        margin-left: 0px !important;
      }

      .section1 {
        border: 1.5px solid #9187878c;
        overflow: hidden;
        padding-bottom: 10px;


      }

      .color1 {

        background-color: #dcdfdf;

      }

      .color1 h3 {
        margin: 10px 0px 10px 0px !important;
      }

      .setPos {
        padding: 0px 0px 10px 0px;
        margin: 10px 0px 10px 0px;
        border: 1px solid #dcdfdf;
        overflow: hidden;
      }

      .modal_field_name {
        float: left;
        font-weight: 700;
        margin-right: 1%;
        padding-top: 1%;
        margin-top: 1%;
      }

      .modal_field_value {
        margin-right: 1%;
        padding-top: 1%;
        margin-top: 1%;
      }

      .modal-header {
        background-color: #7fffd4;
      }

      /*.row{
  margin-right: 0px!important;
  margin-left: 0px!important;
}
.section1{
    border: 1.5px solid #9187878c!important;
    margin: 0.25cm!important;
    padding: 0.25cm!important;
    page-break-inside : avoid;
}
.color1{
  margin: 0%!important;
  background-color: #5f9ea061!important;
  -webkit-print-color-adjust: exact; 
}
.modal_field_name{
  float:left!important;
  font-weight: 700!important;
  margin-right:0.5cm!important;

}

.modal_field_value{
  padding-top:0.30cm!important;

}
.color1{
  margin: 0%!important;
  background-color: #7fffd4!important;
 -webkit-print-color-adjust: exact; 
}

.modal-header{
  background-color: #7fffd4!important;
 -webkit-print-color-adjust: exact; 
}
#divToPrint{
}*/
    }

    .btnJb {
      margin: 20px;
    }
  </style>


</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <!-- Main Header -->
    @include('layouts.header')
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">

            @if (($message = Session::get('success')) && ($id = Session::get('lb_id')))
        <div class="alert alert-success alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ $message }} with LB Application ID: {{$id}}</strong>


        </div>
      @endif
            @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ $message }}</strong>


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
            <!--   @if ($message = Session::get('failure'))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
              </div>
              @endif -->
          </div>
          <!-- /.box-header -->
          <!-- form start -->


          <div class="tab-content" style="margin-top:16px;">






            <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4><b>Application Details </b></h4>
                </div>
                <div class="panel-body">



                  <div class="row">
                    <div class="col-md-12">
                      <h3 style="text-align: center; color:red;">Beneficiary ID:{{$row->id}}<a
                          href="{{ route('jbdocuploadlist', ['scheme_id' => $row->scheme_id])}}">
                          <img width="50px;" style="pull-right" src="{{ asset("images/back.png") }}" alt="Back" /></a>
                      </h3>
                    </div>
                  </div>

                  <div class="row color1">
                    <div class="col-md-12">
                      <h3>Personal Details</h3>
                    </div>
                  </div>


                  <div class="row">
                    <div class="col-md-6">
                      <div><strong>Name :</strong> {{$row->ben_fname}} {{$row->ben_mname}} {{$row->ben_lname}}</div>
                    </div>








                    @if(!is_null($row->dob))
            <div class="col-md-6">
              <div><strong>Date of Birth (DD-MM-YYYY):</strong> {{date('d/m/Y', strtotime($row->dob)) }}</div>

            </div>
          @endif







                    <div class="col-md-6">
                      <div><strong>Father's Name :</strong> {{$row->father_fname}} {{$row->father_mname}}
                        {{$row->father_lname}}</div>
                    </div>

                    <div class="col-md-6">
                      <div><strong>Mother's Name :</strong> {{$row->mother_fname}} {{$row->mother_mname}}
                        {{$row->mother_lname}}</div>
                    </div>








                    <div class="col-md-6">
                      <div><strong>Caste:</strong> {{trim($row->caste)}}</div>
                    </div>


                    <div class="col-md-6">
                      <div><strong>Marital Status:</strong> {{$row->marital_status}}</div>
                    </div>

                    <div class="col-md-6">
                      <div><strong>Spouse Name :</strong> {{$row->spouse_fname}} {{$row->spouse_mname}}
                        {{$row->spouse_lname}}</div>
                    </div>

                    <div class="col-md-6">
                      <div><strong>Monthly Family Income(Rs.):</strong> {{$row->mothly_income}}</div>
                    </div>
                    <div class="col-md-6">
                      <div><strong>Aadhaar No.:</strong> {{$row->aadhar_no}}</div>
                    </div>




                  </div>
                  <div class="row">
                    <div class="col-md-12 color1" style="margin:10px 0px">
                      <h3>Bank Details</h3>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div><strong>Bank Name:</strong> {{$row->bank_name}}</div>

                    </div>




                    <div class="col-md-6">
                      <div><strong>Bank Branch Name:</strong> {{$row->branch_name}}</div>

                    </div>


                    <div class="col-md-6">
                      <div><strong>Bank Account No.:</strong> {{$row->bank_code}}</div>

                    </div>

                    <div class="col-md-6">
                      <div><strong>IFS Code:</strong>{{$row->bank_ifsc}}</div>

                    </div>

                  </div>
                  <div class="row">
                    <div class="col-md-12 color1" style="margin:10px 0px">
                      <h3>Contact Details</h3>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div><strong>Block/Municipality/Corp:</strong> {{$row->block_ulb_name}}</div>

                    </div>




                    <div class="col-md-6">
                      <div><strong>GP/Ward Name:</strong> {{$row->gp_ward_name}}</div>

                    </div>
                  </div>



                  <div class="row">
                    <div class="col-md-12 color1" style="margin:10px 0px">
                      <h3>Enclosure List</h3>
                    </div>
                  </div>
                  @if(count($encloser_list) > 0)
            @foreach ($encloser_list as $doc_all)

        <div class="form-group col-md-4">

        <label
          class="fileLable_{{$doc_all['id']}} {{$doc_all['required'] == 1 && $is_verifier ? 'required-field' : ''}}">{{ $doc_all['doc_name'] }}</label>

        <div class="imageSize">(Image type must be {{ $doc_all['doc_type'] }} and image size max
          {{ $doc_all['doc_size_kb'] }}KB)</div>
        <button type="button" id="doc_{{ $doc_all['id'] }}" name="encolerModal"
          class="btn btn-info encloserModal btnEnc">Upload</button>


        <span id="download_{{ $doc_all['id']}}" style="{{$doc_all['can_download'] == 1 ? '' : 'display:none'}}">
          &nbsp;&nbsp;<button type="button" id="docDownload_{{ $doc_all['id'] }}"
          class="btn btn-danger downloadEncloser btnEnc">Download</button>
        </span>
        </div>

      @endforeach

          @endif

                  <br /> <br /> <br /> <br />
                </div>

              </div>

              <div class="col-md-12" align="center">

                <div class="btn-group">


                </div>

              </div>
              <br />
            </div>
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
  <!--  @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
      @endif -->
  <!-- /.row -->



  <div class="modal" id="encolser_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="encolser_name">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="uploadForm" enctype="multipart/form-data">
          <input type="hidden" name="document_type" id="document_type" />
          <input type="hidden" name="is_verifier" id="is_verifier" value="{{$is_verifier}}" />
          <input type="hidden" name="is_approver" id="is_approver" value="{{$is_approver}}" />
          <input type="hidden" name="is_hod" id="is_hod" value="{{$is_hod}}" />
          <input type="hidden" name="id" id="id" value="{{$row->id}}" />
          <input type="hidden" name="scheme_id" id="scheme_id" value="{{$row->scheme_id}}" />
          <input type="hidden" name="action_type" id="action_type" value="" />
          <input type="hidden" name="action_msg" id="action_msg" value="" />
          <input type="hidden" name="district_code" id="district_code" value="{{$row->created_by_dist_code}}" />

          {{ csrf_field() }}
          <div class="modal-body">
            <label>Choose File:</label>
            <input type="file" name="file" id="fileInput">

            <div class="progress">
              <div class="progress-bar"></div>
            </div>
            <div id="uploadStatus"></div>
          </div>
          <div class="modal-footer">
            <button type="submit" id="submitButton" name='btnSubmit' class="btn btn-primary">Upload</button>
            <img style="display:none;" src="{{ asset('images/ZKZg.gif')}}" id="btn_encolser_loader" width="150px">

          </div>
        </form>
      </div>
    </div>
  </div>
  </section>

  <!-- Main content -->
  <!--  <section class="content">

      Your Page Content Here



    </section> -->
  <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Footer -->
  @include('layouts.footer')

  <!-- ./wrapper -->

  <!-- REQUIRED JS SCRIPTS -->

  <!-- jQuery 2.1.3 -->
  <script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
  <script src="{{ asset("/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js") }}"
    type="text/javascript"></script>
  <script src="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js") }}"
    type="text/javascript"></script>

  <!-- Bootstrap 3.3.2 JS -->
  <script src="{{ asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}"
    type="text/javascript"></script>

  <!-- AdminLTE App -->
  <script src="{{ asset("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
  <script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
  <script src="{{ URL::asset('js/validateAdhar.js') }}"></script>
  <script type="text/javascript">
    $(document).ready(function () {
      $("#submittingapprove").hide();
      $(".NumOnly").keyup(function (event) {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
          event.preventDefault();
        }
      });
      $('.confirmBtn').click(function () {
        var is_verifer = $("#is_verifer").val();
        var is_approver = $("#is_approve").val();
        var is_hod = $("#is_hod").val();

        var ButtonText = $(this).text();
        var clickval = $(this).val();
        // alert(clickval);
        $("#action_type").val('');
        $('.verify_reject').text('');
        var op_text = $(this).attr("op_text");
        $('#op_text').text(op_text);
        $('#action_msg').val(op_text);
        // alert(op_text);
        if (is_verifer && clickval == 5) {
          $("#action_type").val(clickval);
          var error_new_mobile_no = '';
          var error_new_aadhar_no = '';
          if ($.trim($('#new_mobile_no').val()) != "") {
            if ($.trim($('#new_mobile_no').val()).length != 10) {
              error_new_mobile_no = 'Mobile Number must be 10 digit';
              $('#error_new_mobile_no').text(error_new_mobile_no);
              $('#new_mobile_no').addClass('has-error');
            }
            else {
              error_new_mobile_no = '';
              $('#error_mobile_no').text(error_new_mobile_no);
              $('#new_mobile_no').removeClass('has-error');

            }
          }
          if ($.trim($('#new_aadhar_no').val()) != "") {
            if ($.trim($('#new_aadhar_no').val()).length != 12) {

              error_new_aadhar_no = 'Aadhar No should be 12 digit ';
              $('#error_new_aadhar_no').text(error_new_aadhar_no);
              $('#new_aadhar_no').addClass('has-error');
            }
            else {
              var new_aadhar_no = $('#new_aadhar_no').val();
              if (new_aadhar_no != '') {
                var aadhar_valid = validate_adhar(new_aadhar_no);
                // aadhar_valid=1;
                if (aadhar_valid) {
                  error_new_aadhar_no = '';
                  $('#error_new_aadhar_no').text(error_new_aadhar_no);
                  $('#new_aadhar_no').removeClass('has-error');
                }
                else {
                  error_new_aadhar_no = 'Invalid Aadhar No.';
                  $('#error_new_aadhar_no').text(error_new_aadhar_no);
                  $('#new_aadhar_no').addClass('has-error');
                }
              }
              else {
                error_new_aadhar_no = '';
                $('#error_new_aadhar_no').text(error_new_aadhar_no);
                $('#new_aadhar_no').removeClass('has-error');
              }
            }
          }
          if (error_new_mobile_no == '' && error_new_aadhar_no == '') {
            $('#modalReject').modal();
          }
        }
        $("#action_type").val(clickval);
        if (is_verifer) {
          if (clickval == 70 || clickval == 75) {
            $("#note_sc_st").show();
          }
          else {
            $("#note_sc_st").hide();
          }
          if (clickval == 7) {
            $("#dob_change_div").show();
          }
          else {
            $("#dob_change_div").hide();
          }
          if (clickval == 80) {
            $("#reason_order_div").show();
          }
          else {
            $("#reason_order_div").hide();
          }
        }
        else {
          $("#note_sc_st").hide();
          $("#dob_change_div").hide();
          $("#reason_order_div").hide();
        }
        $('#modalReject').modal();


      });
      $('#reject').click(function () {
        $('.verify_reject').text('Reject');
        $("#action_type").val(4);
        $('#modalReject').modal();
      });

      $(".downloadEncloser").click(function () {
        var id = $(this).attr("id");
        var id_split = id.split('_');
        var application_id = $("#uploadForm #id").val();
        var scheme_id = $("#uploadForm #scheme_id").val();
        var district_code = $("#uploadForm #district_code").val();

        window.open("jbDownload?created_by_dist_code=" + district_code + "&document_type=" + id_split[1] + "&scheme_id=" + scheme_id + "&beneficiary_id=" + application_id);
      });
      $('.encloserModal').click(function () {
        $("#encolser_name").html('');
        $('#uploadStatus').html('');
        $('.progress-bar').html('');
        $("#uploadForm #document_type").val('');
        $('#btn_encolser_loader').hide();
        var label = $(this).parent().find('label').text();
        $("#encolser_name").html(label);
        var id = $(this).attr("id");
        var id_split = id.split('_');
        //console.log(id_split);
        $("#uploadForm #document_type").val(id_split[1]);
        $("#encolser_modal").modal("show");

      });
      $("#uploadForm").on('submit', function (e) {
        $('#submitButton').hide();
        $('#btn_encolser_loader').show();
        e.preventDefault();
        var form = $('#uploadForm')[0];
        var formData = new FormData(form);
        var ben_id = $("#uploadForm #id").val();
        var scheme_id = $("#uploadForm #scheme_id").val();
        //alert(scheme_id);
        formData.append('ben_id', ben_id);
        formData.append('scheme_id', scheme_id);
        $.ajax({
          xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
              if (evt.lengthComputable) {
                var percentComplete = ((evt.loaded / evt.total) * 100);
                var percentComplete = Math.ceil(percentComplete);
                $(".progress-bar").width(percentComplete + '%');
                $(".progress-bar").html(percentComplete + '%');
              }
            }, false);
            return xhr;
          },
          type: 'POST',
          dataType: 'json',
          url: '{{ url('jb_ajax_encloser_entry') }}',
          data: formData,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function () {
            $(".progress-bar").width('0%');
            //$('#uploadStatus').html('<img   width="50px" height="50px" src="images/ZKZg.gif"/>');
          },
          error: function (ex) {
            //console.log(ex);
            $('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
            $('#btn_encolser_loader').hide();
            $('#submitButton').show();


          },
          success: function (resp) {
            //console.log(resp);
            if (resp.return_status == 1) {
              var id = $("#uploadForm #document_type").val();
              $('#uploadForm')[0].reset();
              $('#download_' + id).show();
              $('#uploadStatus').html('<p style="color:#28A74B;">File has uploaded successfully!</p>');
              //$(".progress-bar").width('0%');

            } else if (resp.return_status == 0) {
              $('#uploadStatus').html('<p style="color:#EA4335;">' + resp.return_msg + '</p>');
            }
            $('#btn_encolser_loader').hide();
            $('#submitButton').show();


          }
        });


      });


      $('#encolser_modal').on('hidden.bs.modal', function (e) {
        $("#uploadForm #document_type").val('');
        $(".progress-bar").html('');

      });
      $('.confirmBtnCaste').click(function () {
        var clickval = $(this).val();
        var application_id = $("#commonfield #id").val();
        var scheme_id = $("#commonfield #scheme_id").val();
        window.location = "changeCastelb?scheme_id=" + scheme_id + "&id=" + application_id + "&type=" + clickval;
      });
    });

  </script>
</body>

</html>