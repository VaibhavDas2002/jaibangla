@include('jbwbpds.base')
<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    @include('layouts.header')
    @include('layouts.sidebar')
    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            @if (($message = Session::get('success')) && ($id = Session::get('id')))
        <div class="alert alert-success alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ $message }} with Application ID: {{$id}}</strong>
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
          @foreach($errors->all() as $error)
        <li><strong> {{ $error }}</strong></li>
      @endforeach
          </ul>
        </div>
      @endif
          </div>
          <form method="post" id="register_form" class="submit-once">
            {{ csrf_field() }}
            <input type="hidden" name="desgisnation_id" id="desgisnation_id" value="{{$designation_id}}" />
            <input type="hidden" name="type" id="type" value="2"/>
            <div class="tab-content" style="margin-top:16px;">
              <div class="tab-pane active" id="personal_details">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4><b>Aadhaar POC WITH WBPDS </b></h4>
                  </div>
                  <div class="panel-body">
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label class="required-field">Scheme</label>
                        <select name="scheme_id" id="scheme_id" class="form-control" tabindex="6">
                          @foreach ($scheme_list as $scheme)
                <option value="{{$scheme->id}}"> {{$scheme->scheme_name}}</option>
              @endforeach
                        </select>
                        <span id="error_scheme_id" class="text-danger"></span>
                      </div>
                      <div class="col-md-4">
                        <button type="button" id="submitting" value="Submit"
                          class="btn btn-success success btn-lg modal-search form-submitted" style="margin-top:20px;">GO
                        </button>
                      </div>
                      <br />
                    </div>
                  </div>
                </div>
              </div>
          </form>
        </div>
    </div>
  </div>
  </section>
  </div>

  @include('layouts.footer')
  <script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
  <script src="{{ asset("/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js") }}"
    type="text/javascript"></script>
  <script src="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js") }}"
    type="text/javascript"></script>
  <script src="{{ asset("js/select2.full.min.js") }}"></script>

  <!-- Bootstrap 3.3.2 JS -->
  <script src="{{ asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
  <script src="{{ URL::asset('js/site.js') }}"></script>

  <script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
  <script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
  <script src="{{ asset("js/jquery.table2excel.js") }}"></script>

  <script>

    $(document).ready(function () {
      $('.sidebar-menu li').removeClass('active');
      $('.sidebar-menu #lk-main').addClass("active");
      $('.sidebar-menu #dupBankmis').addClass("active");
      $('.modal-search').on('click', function () {
        var designation_id_old = $("#desgisnation_id").val();
        var scheme_id = $("#scheme_id").val();
        var type = $("#type").val();
        if ($.trim($('#scheme_id').val()).length == 0) {
          error_scheme_id = 'Scheme is required';
          $('#error_scheme_id').text(error_scheme_id);
          $('#scheme_id').addClass('has-error');
        }
        else {
          error_scheme_id = '';
          $('#error_scheme_id').text(error_scheme_id);
          $('#scheme_id').removeClass('has-error');
          var src = '';
          src = 'jbpdsnamemismatchlist';
          window.location = src + '?scheme_id=' + scheme_id + '&type=' + type;

        }
      });
    });

    function printMsg(msg, msgtype, divid) {
      $("#" + divid).find("ul").html('');
      $("#" + divid).css('display', 'block');
      if (msgtype == '0') {
        $("#" + divid).removeClass('alert-success');
        $("#" + divid).addClass('alert-warning');
      }
      else {
        $("#" + divid).removeClass('alert-warning');
        $("#" + divid).addClass('alert-success');
      }
      if (Array.isArray(msg)) {
        $.each(msg, function (key, value) {
          $("#" + divid).find("ul").append('<li>' + value + '</li>');
        });
      }
      else {
        $("#" + divid).find("ul").append('<li>' + msg + '</li>');
      }
    }
    function closeError(divId) {
      $('#' + divId).hide();
    }

  </script>
</body>
</html>