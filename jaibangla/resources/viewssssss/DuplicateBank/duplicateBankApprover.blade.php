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
    padding: 10px;
    color: #555;
    font-size: 14px;
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

  .modal-confirm {
    color: #636363;
    width: 400px;
  }

  .modal-confirm .modal-content {
    padding: 20px;
    border-radius: 5px;
    border: none;
    text-align: center;
    font-size: 14px;
  }

  .modal-confirm .modal-header {
    border-bottom: none;
    position: relative;
  }

  .modal-confirm h4 {
    text-align: center;
    font-size: 26px;
    margin: 30px 0 -10px;
  }

  .modal-confirm .close {
    position: absolute;
    top: -5px;
    right: -2px;
  }

  .modal-confirm .modal-body {
    color: #999;
  }

  .modal-confirm .modal-footer {
    border: none;
    text-align: center;
    border-radius: 5px;
    font-size: 13px;
    padding: 10px 15px 25px;
  }

  .modal-confirm .modal-footer a {
    color: #999;
  }

  .modal-confirm .icon-box {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    border-radius: 50%;
    z-index: 9;
    text-align: center;
    border: 3px solid #f15e5e;
  }

  .modal-confirm .icon-box i {
    color: #f15e5e;
    font-size: 46px;
    display: inline-block;
    margin-top: 13px;
  }

  .modal-confirm .btn,
  .modal-confirm .btn:active {
    color: #fff;
    border-radius: 4px;
    background: #60c7c1;
    text-decoration: none;
    transition: all 0.4s;
    line-height: normal;
    min-width: 120px;
    border: none;
    min-height: 40px;
    border-radius: 3px;
    margin: 0 5px;
  }

  .modal-confirm .btn-secondary {
    background: #c1c1c1;
  }

  .modal-confirm .btn-secondary:hover,
  .modal-confirm .btn-secondary:focus {
    background: #a8a8a8;
  }

  .modal-confirm .btn-danger {
    background: #f15e5e;
  }

  .modal-confirm .btn-danger:hover,
  .modal-confirm .btn-danger:focus {
    background: #ee3535;
  }

  .trigger-btn {
    display: inline-block;
    margin: 100px auto;
  }
</style>

@extends('layouts.app-template-datatable_new')
@section('content')

<div class="content-wrapper">
  <!-- Content Header (Page header) -->

  <section class="content">
    <div class="box box-default">
      <div class="box-body">




        <div class="panel panel-default">
          <div class="panel-heading"><span id="panel-icon"><h4><b>Duplicate Bank Account and IFSC</b></h4></div>
          <div class="panel-body" style="padding: 5px; font-size: 14px;">

            @if ( ($message = Session::get('success')))
            <div class="row">
              <div class="alert alert-success alert-block" style="margin:10px 30px 10px 30px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>

              </div>
            </div>
            @endif
            @if ( ($error = Session::get('error')))
            <div class="row">
              <div class="alert alert-danger alert-block" style="margin:10px 30px 10px 30px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $error }}</strong>

              </div>
            </div>
            @endif
            <div class="alert print-error-msg" style="display:none;" id="errorDiv">
              <button type="button" class="close" aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
              <ul></ul>
            </div>
            <div class="table-responsive">
              <table id="example" class="display" cellspacing="0" width="100%">
                <thead style="font-size: 12px;">
                  <th>Bank IFSC.</th>
                  <th>Bank Account No.</th>
                  <th>Duplicate Count</th>
                  <th>Action</th>
                </thead>
                <tbody style="font-size: 14px;">
                  @if(count($data)>0)
                  @foreach ($data as $arr )

                  <tr>
                    <td>{{$arr->last_ifsc}}</td>
                    <td>{{$arr->last_accno}}</td>
                    <td>{{$arr->cnt}}</td>
                     <td>{{$arr->cnt}}</td>
                  </tr>
                  @endforeach
                  @else
                  <tr>
                    <td colspan="4">No Duplicate Record Found.</td>
                  </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>
</div>


@endsection
@section('script')
<script>
  $(document).ready(function() {
    $('.sidebar-menu li').removeClass('active');
    $('.sidebar-menu #lk-main').addClass("active");
    $('.sidebar-menu #dup_aadhar_approved').addClass("active");
    var sessiontimeoutmessage = '{{$sessiontimeoutmessage}}';
    var base_url = '{{ url(' / ') }}';
    $('#example').DataTable({
      "paging": true,
      "searchable": false,
      "paging": true,
      "ordering": false,
      "bFilter": false,
      "bInfo": true,
      "pageLength": 20
    });
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