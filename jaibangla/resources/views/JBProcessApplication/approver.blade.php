<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Jb | Jai Bangla</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet"
    type="text/css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
  <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">
  <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet"
    type="text/css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
  <!-- Google Font -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .errorField {
      border-color: #990000;
    }

    .searchPosition {
      margin: 70px;
    }

    .submitPosition {
      margin: 25px 0px 0px 0px;
    }

    .typeahead {
      border: 2px solid #FFF;
      border-radius: 4px;
      padding: 8px 12px;
      max-width: 300px;
      min-width: 290px;
      background: rgba(66, 52, 52, 0.5);
      color: #FFF;
    }

    .tt-menu {
      width: 300px;
    }

    ul.typeahead {
      margin: 0px;
      padding: 10px 0px;
    }

    ul.typeahead.dropdown-menu li a {
      padding: 10px !important;
      border-bottom: #CCC 1px solid;
      color: #FFF;
    }

    ul.typeahead.dropdown-menu li:last-child a {
      border-bottom: 0px !important;
    }

    .bgcolor {
      max-width: 550px;
      min-width: 290px;
      max-height: 340px;
      background: url("world-contries.jpg") no-repeat center center;
      padding: 100px 10px 130px;
      border-radius: 4px;
      text-align: center;
      margin: 10px;
    }

    .demo-label {
      font-size: 1.5em;
      color: #686868;
      font-weight: 500;
      color: #FFF;
    }

    .dropdown-menu>.active>a,
    .dropdown-menu>.active>a:focus,
    .dropdown-menu>.active>a:hover {
      text-decoration: none;
      background-color: #1f3f41;
      outline: 0;
    }

    table.dataTable thead th,
    table.dataTable thead td {
      padding: 10px 13px;
    }

    table.dataTable tfoot th,
    table.dataTable tfoot td {
      padding: 10px 5px;
    }

    .criteria1 {
      text-transform: uppercase;
      font-weight: bold;
    }

    #example_length {
      margin-left: 40%;
      margin-top: 2px;
    }

    @keyframes spinner {
      to {
        transform: rotate(360deg);
      }
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

    .select2 {
      width: 100% !important;
    }

    .select2 .has-error {
      border-color: #cc0000;
      background-color: #ffff99;
    }

    .required-field::after {
      content: "*";
      color: red;
    }
  </style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    @include('layouts.header')
    @include('layouts.sidebar')
    <div class="content-wrapper">
      <section class="content-header">
        @if ($report_name)
      <b>{{$report_name}}</b>
      <br>
    @endif
        <div class='row'>
          <div>
            @if (($message = Session::get('message')))
        <div class="alert alert-success alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ $message }}</strong>

        </div>
      @endif
            @if (($error = Session::get('error')))
        <div class="alert alert-danger alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ $error }}</strong>

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
        </div>
      </section>
      <!-- Main content -->
      <section class="content">
        <div class="row">
        </div>
        <input type="hidden" name="dist_code" value="{{ $dist_code }}" class="client-js-district1">
        <input type="hidden" name="scheme_id" id="scheme_id" value="{{ $scheme_id }}">
        <inpyt type="hidden" name="type" name="type" value="{{$type}}" />
        <div class="row">
          @if ($type == 2)

        <div class="form-group col-md-4">
        <label class=" control-label">Application Type</label>
        <select name="application_type" id="application_type" class="form-control full-width">
          <option value="1" selected>Pending</option>
          @if($designation_id == 'Verifier')
        <option value="2">Verified but Approval Pending</option>
      @endif
          <option value="3">Verified and Approved</option>
          <option value="4">Rejected</option>
          <option value="5">Probable duplicate list</option>
          @if($scheme_id == 1)
        <option value="6">Received from Bandhu</option>
        <option value="7">Transfer to Bandhu</option>
        <option value="11">Transfer to OAP</option>
      @endif
          @if($scheme_id == 3)
        <option value="9">Received from Johar</option>
        <option value="10">Transfer to Johar</option>
        <option value="11">Transfer to OAP</option>
      @endif
          @if($scheme_id == 10)
        <option value="12">Received from Bandhu</option>
        <option value="13">Received from Johar</option>
      @endif
          <option value="14">Back to LB</option>
        </select>
        </div>

      @endif

          <div class="form-group col-md-3">
            <label class=" control-label required-field">Select Filter Criteria :Urban/Rural</label>
            <select name="filter_1" id="filter_1" class="form-control client-js-urban1">
              <option value="">-----Select----</option>
              @foreach ($levels as $key => $value)
          <option value="{{$key}}"> {{$value}}</option>
        @endforeach
            </select>
          </div>
          <div class="form-group col-md-3">
            <label class=" control-label required-field">Select Filter Criteria :Block/Sub Division</label>
            <select name="filter_2" id="filter_2" class="form-control client-js-localbody1">
              <option value="">-----Select----</option>
            </select>
          </div>
          @if($special_approval_allowded == 1)
        <div class="form-group col-md-3">
        <label class="required-field">Select Filter Criteria :Quota</label>
        <select name="filter_quota" id="filter_quota" class="form-control full-width">
          @if($main_approval_allowded == 1)
        <option value="0" selected>Normal Quota</option>
      @endif
          @if($special_approval_allowded == 1)
        <option value="1">Special Quota</option>
      @endif

        </select>
        </div>
      @else
      <input type="hidden" name="filter_quota" id="filter_quota" value="0" />
    @endif
          @if($aadhar_filer_visible == 1)
        <div class="form-group col-md-4">
        <label class="">Select Filter Criteria :Aadhaar</label>
        <select name="aadhar_exists" id="aadhar_exists" class="form-control full-width">
          <option value="1" selected>Applications with Aadhaar Number</option>
          <option value="0">Applications without Aadhaar Number</option>

        </select>
        </div>

      @else
      <input type="hidden" name="aadhar_exists" id="aadhar_exists" value="1" />
    @endif
          <div class="form-group col-md-3" style="margin-top:25px;">
            <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
            <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
          </div>
        </div>
        <form class="row" method="POST" action="{{ route('nhmemployee.MassEmployeeApproval') }}" class="submit-once">
          <div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">-
            <input type="hidden" name="wq" id="wq" />
            <input type="hidden" name="created_by_local_body_code" id="created_by_local_body_code" />
            <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}" />
            <input type="hidden" name="oap_smsd" id="oap_smsd" value="0" />
            @if($approveBtnvisible == 1)
        <button style="border:1px solid black ;margin: 0% 0% 2% 0%;" type="submit" name="bulk_approve"
          id="bulk_approve" value="approve" class="btn btn-info col-sm-3 col-xs-5 btn-margin" disabled>
          Approve
        </button>
      @endif
          </div>
          <table id="example" class="display" cellspacing="0" width="100%">
            <thead>
              <tr role="row" class="sorting_asc" style="font-size: 12px;">
                <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                <th width="7%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                  aria-label="Name: activate to sort column descending" aria-sort="ascending">Beneficiary ID</th>
                <th width="12%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                  aria-label="Name: activate to sort column descending" aria-sort="ascending">Applicant Name</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                  aria-label="Email: activate to sort column ascending">DOB</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                  aria-label="Email: activate to sort column ascending">Gender</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                  aria-label="Email: activate to sort column ascending">Assembly Name</th>
                <th width="17%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                  aria-label="Email: activate to sort column ascending">Action</th>
                <th width="17%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                  aria-label="Email: activate to sort column ascending">Check</th>

              </tr>
            </thead>
            <tbody>
            </tbody>
            <!-- <tfoot> -->
        </form>
        <!-- </tfoot> -->
        </table>
        <div class="row">

          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            </div>
          </div>
        </div>
    </div>
  </div>
  <!-- /.row -->
  </section>
  <!-- /.content -->
  </div>

  <script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
  <script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
  <script src="{{ asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
  <script src="{{ asset("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
  <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
  <!-- <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script> -->
  <!-- <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

  <script>
    $(document).ready(function () {
      var base_url = '{{ url('/') }}';
      $("#bulk_approve").hide();
      $("#created_by_local_body_code").val('');
      $("#wq").val('');

      //fill_datatable();
      function fill_datatable(filter_1 = '', filter_2 = '', filter_quota = 0, aadhar_exists = 1, scheme_id) {
        var scheme_id = $("#scheme_id").val();
        var dataTable = $('#example').DataTable({
          //dom: 'Bfrtip',
          dom: 'Blfrtip',
          paging: true,
          pageLength: 100,
          lengthMenu: [[20, 50, 100, 500, 1000, -1], [20, 50, 100, 500, 1000, 'All']],
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ url('ApproverDataAjax') }}",
            type: "GET",
            data: function (d) {
              d.filter_1 = filter_1,
                d.scheme_id = scheme_id,
                d.filter_2 = filter_2,
                d.filter_quota = filter_quota,
                d.aadhar_exists = aadhar_exists,
                d._token = "{{csrf_token()}}"
            },
            error: function (ex) {
              alert('Session time out..Please login again');
            }
          },
          columns: [
            { "data": "id" },
            { "data": "name" },
            { "data": "dob" },
            { "data": "gender" },
            { "data": "assembly_name" },
            { "data": "view" },
            { "data": "check" },
          ],
        });


      }

      $('#filter').click(function () {
        var filter_1 = $('#filter_1').val();
        var filter_2 = $('#filter_2').val();
        var filter_quota = $('#filter_quota').val();
        var aadhar_exists = $("#aadhar_exists").val();
        var scheme_id = $("#scheme_id").val();
        // console.log(filter_1, filter_2, filter_quota, aadhar_exists, scheme_id);
        if (filter_1 != '' && filter_2 != '') {

          $('#example').DataTable().destroy();
          fill_datatable(filter_1, filter_2, filter_quota, aadhar_exists);
          $("#wq").val(filter_quota);
          $("#created_by_local_body_code").val(filter_2);
          $("#bulk_approve").show();
        }
        else {

          alert('Please select three Filter Criterias');
        }
      });

      $('#reset').click(function () {
        location.reload();
      });
      $('.client-js-urban1').change(function () {
        select_district_code = $('.client-js-district1').val();
        select_body_type = $('.client-js-urban1').val();
        var htmlOption = '<option value="">--Select--</option>';
        if (select_body_type == 2) {
          $.each(blocks, function (key, value) {
            if (value.district_code == select_district_code) {
              htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
            }
          });
        } else if (select_body_type == 1) {
          $.each(subDistricts, function (key, value) {
            if (value.district_code == select_district_code) {
              htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
            }
          });
        }
        else {
          $('.client-js-localbody1').html('<option value="">--Select--</option>');
        }

        $('.client-js-localbody1').html(htmlOption);
      });

    });
  </script>

</body>

</html>