<style type="text/css">
  .preloader1 {
    position: fixed;
    top: 40%;
    left: 52%;
    z-index: 999;
  }

  .preloader1 {
    background: transparent !important;
  }

  .disabledcontent {
    pointer-events: none;
    opacity: 0.4;
  }

  .has-error {
    border-color: #cc0000;
    background-color: #ffff99;
  }

  .modal {
    text-align: center;
    padding: 0 !important;
  }

  .modal:before {
    content: '';
    display: inline-block;
    height: 100%;
    vertical-align: middle;
    margin-right: -4px;
  }

  .modal-dialog {
    display: inline-block;
    text-align: left;
    vertical-align: middle;
  }

  label.required:after {
    color: red;
    content: '*';
    font-weight: bold;
    margin-left: 5px;
    float: right;
    margin-top: 5px;
  }
  .filterDiv {
    border: 1px solid #d9d9d9; 
    border-left: 3px solid deepskyblue; 
    margin-bottom: 10px; 
    padding: 8px; 
    box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
  }
  .resultDiv {
    border: 1px solid #d9d9d9; 
    /*border-left: 3px solid seagreen; */
    /*margin-bottom: 10px; */
    padding: 8px;  
    box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
  }
  .excelButton{
    background-color: red
  }
</style>

@extends('layouts.app-template-datatable_new')
@section('content')

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Faulty Aadhar Approved Application Report
    </h1>
    
  </section>
  <section class="content">
    <div class="box box-default">
      <div class="box-body">
        <div id="loadingDiv" style="display: none;">
        </div>

        <!-- <div class="panel panel-default">
          <div class="panel-heading">Search By District</div>
          <div class="panel-body" style="padding: 5px;"> -->
          <div class="filterDiv">
            <div class="row">
              @if ( ($message = Session::get('success')))
              <div class="alert alert-success alert-block">
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

            <div class="row">
              <div class="col-md-12">
                <div class="col-md-3">
                  <label class="control-label">Select Scheme<span class="text-danger"> *</span></label>
                  <select class="form-control" name="scheme_name" id='scheme_name' required >
                    <option value="">--Select Scheme--</option>
                    @foreach($userObj as $user)
                    <option value="{{ $user->scheme_id }}">{{ $user->scheme_name }}</option>
                   @endforeach
                  </select>
                  <span id="err_scheme" style="color: firebrick;"></span>
                </div>
                <div class="col-md-3">
                  <label class="control-label">District</label>
                  <select class="form-control" name="district" id='district'>
                    <option value="">--Select District--</option>
                    @foreach($district_name as $district)
                    <option value="{{$district->district_code}}">{{$district->district_name}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3" style="display: none;" id="rural_urban_div">
                  <label class="control-label">Rural/Urban</label>
                  <select class="form-control" name="rural_urban" id='rural_urban'>
                    <option value="">--Select Rural/Urban--</option>
                    <option value="all">All</option>
                    <option value="rural">Rural</option>
                    <option value="urban">Urban</option>
                  </select>
                </div>
                <div class="col-md-3" style="margin-top: 26px;">
                  <label class=" control-label">&nbsp; </label>
                  <button type="button" name="filter" id="filter" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                  &nbsp;  
                </div>
              </div>
            </div>
          </div>
        <!-- </div> -->

            <div class="table-responsive resultDiv" id="updateAadhar_report_div" style="display: none;">
              <table id="updateAadharReport" class="display" cellspacing="0" width="100%" style="border: 1px solid ghostwhite;">
                <thead style="font-size: 12px;">
                  <tr role="row">
                    <!-- <th>Serial No</th> -->
                    <th>District/Block/Sub-Division</th>                    
                    <th>Blank/Invalid Aadhar NO. of Total Approved Beneficiary</th>
                    <th>Aadhar NO. Edited by Operator</th>
                    <th>Aadhar NO. Verified by Verifier</th>
                    <th>Aadhar NO. Approved by Approver</th>
                    <!-- <th></th> -->
                  </tr>
                </thead>
                <tbody style="font-size: 14px;"></tbody>
                <tfoot style="font-size: 12px;">
                  <tr>
                    <!-- <th>Serial No</th> -->
                    <th>District/Block/Sub-Division</th>                    
                    <th>Blank/Invalid Aadhar NO. of Total Approved Beneficiary</th>
                    <th>Aadhar NO. Edited by Operator</th>
                    <th>Aadhar NO. Verified by Verifier</th>
                    <th>Aadhar NO. Approved by Approver</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          <!-- </div>
        </div> -->
      </div>
    </div>
  </section>
</div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#filter').click(function(e) {
      var err_scheme = '';
      var updateAadharReport = "";
      var scheme_id = $('#scheme_name').val();
      var district_code = $('#district').val();
      var rural_urban = $('#rural_urban').val();
      // alert(district_code);

      if ($.trim($('#scheme_name').val()).length == 0) {
        err_scheme = 'Field is required';
        $('#err_scheme').text(err_scheme);
        $('#scheme_name').addClass('has-error');
      }else{
        err_scheme = '';
        $('#err_scheme').text(err_scheme);
        $('#scheme_name').removeClass('has-error');
        $('#updateAadhar_report_div').show();
      }
			if ( $.fn.DataTable.isDataTable('#updateAadharReport')) {
        $('#updateAadharReport').DataTable().destroy();
      }
      updateAadharReport= $('#updateAadharReport').DataTable({
        dom: 'Bfrtip',
        // "scrollX": true,
        "paging": false, // Disable Pagination
        "searchable": false,
        //"scrollX": true,
        "ordering":false, // Disable Ordering of all column
        "bFilter": false,
        "bInfo": false, // Disable Showing 1 to 20 of 2000 entries
        "pageLength":20,
        'lengthMenu': [[10, 20, 30, 50,100], [10, 20, 30, 50,100]],
        "serverSide": true,
        "processing":true,
        "bRetrieve": true,
        "oLanguage": {
          "sProcessing": '<div id="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>'
        },
        ajax: {
          url: "{{ url('aadhar-update-count-list-hod') }}",
          type: 'POST',
          data: {'_token' : "{{ csrf_token() }}", scheme_id : scheme_id, district_code : district_code, rural_urban : rural_urban},
          error: function(){
            $('#preloader1').hide();
          },
        },
        initComplete: function(){
            $('.preloader1').hide();
            // console.log('Data rendered successfully');
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            // Total over this page
            blank_aadhar_count = api
                .column( 1, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            edited = api
                .column( 2, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            verified = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 ); 
            approved = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            $( api.column( 0 ).footer() ).html(
                "Total: "
            );
            $( api.column( 1 ).footer() ).html(
              blank_aadhar_count
            );
            $( api.column( 2 ).footer() ).html(
              edited
            );
            $( api.column( 3 ).footer() ).html(
              verified
            );
            $( api.column( 4 ).footer() ).html(
              approved
            );                          
        },
        columns: [
          {"data": 'bsm'},
          {"data": 'total_blank_aadhar_count'},
          {"data": 'edited'},
          {"data": 'verified'},
          {"data": 'approved'},
        ],
        buttons: [
           {
               extend: 'excel',
               className: 'excelButton',
               title: 'Aadhar Update MIS Report',
               text: 'Export To Excel',
               footer: true,
               pageSize:'A4',
               //orientation: 'landscape',
               pageMargins: [ 40, 60, 40, 60 ],
               exportOptions: {
                     columns: [0,1,2,3,4],
                    stripHtml: false,
                }
           },
            //'pdf','excel','csv','print','copy'
          ],
      });
		});
    $(document).on('change', '#district', function(event) {
      // $('#rural_urban').show();
      var district_name = $('#district').val();
      if (district_name != null) {
        $('#rural_urban_div').show();
      }else{
        $('#rural_urban_div').hide();
      }
    });
	});
</script>
