  <style type="text/css">
    .full-width{
      width:100%!important;
    }
  .bg-blue{
    background-image: linear-gradient(to right top, #0073b7, #0086c0, #0097c5, #00a8c6, #00b8c4)!important;
  }
  .bg-red{
    /*background-image: linear-gradient(to right bottom, #dd4b39, #db4546, #d74052, #d13d5e, #c93d68)!important;*/
  /* background-image: linear-gradient(to right bottom, #dd4b39, #e65347, #ef5b55, #f76463, #ff6d71)!important;*/
  background-image: linear-gradient(to right bottom, #dd4b39, #ec6f65, #d21a13, #de0d0b, #f3060d)!important;
  }
  .bg-yellow{
    background-image: linear-gradient(to right bottom, #dd4b39, #e65f31, #ed7328, #f1881e, #f39c12)!important;
  }
  .bg-green{
  /*background-image: linear-gradient(to right bottom, #00837d, #008d7b, #009674, #009e69, #00a65a)!important;*/
  background-image: linear-gradient(to right bottom, #04736d, #008f73, #00ab6a, #00c44f, #5ddc0c)!important;
  }

  .bg-verify{
    background-image: linear-gradient(to right top, #f39c12, #f8b005, #fac400, #fad902, #f8ee15)!important;
  }
  .info-box {
      display: block;
      min-height: 90px;
      background: #b6d0ca33!important;
      width: 100%;
      box-shadow: 0px 0px 15px 0px rgba(0, 0, 0, 0.30)!important;
      border-radius: 2px;
      margin-bottom: 15px;
  }
  .small-box .icon{
    margin-top: 7%;
  }
  .small-box>.inner {
      padding: 10px;
      color: white;
  }

  .small-box p {
      font-size: 18px!important;
  }
  .select2 .select2-container{
    /*//width:100%!important;*/
  } 

  .link-button {
    background: none;
    border: none;
    color: blue;
    text-decoration: underline;
    cursor: pointer;
    font-size: 1em;
    font-family: serif;
  }
  .link-button:focus {
    outline: none;
  }
  .link-button:active {
    color:red;
  }
  .small-box-footer-custom{
    position: relative;
      text-align: center;
      padding: 3px 0;
      color: #fff;
      color: rgba(255,255,255,0.8);
      display: block;
      z-index: 10;
      background: rgba(0,0,0,0.1);
      text-decoration: none;
      font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;
      font-weight: 400;
      width:100%;
  }
  .small-box-footer-custom:hover {
      color: #fff;
      background: rgba(0,0,0,0.15);
  }
  th.sorting::after,
  th.sorting_asc::after,
  th.sorting_desc::after {
    content:"" !important;
  }
  .errorField{
      border-color: #990000;
    }
    .searchPosition{
      margin:70px;
    }
    .submitPosition{
      margin: 25px 0px 0px 0px;
    }

    
    .typeahead { border: 2px solid #FFF;border-radius: 4px;padding: 8px 12px;max-width: 300px;min-width: 290px;background: rgba(66, 52, 52, 0.5);color: #FFF;}
    .tt-menu { width:300px; }
    ul.typeahead{margin:0px;padding:10px 0px;}
    ul.typeahead.dropdown-menu li a {padding: 10px !important;  border-bottom:#CCC 1px solid;color:#FFF;}
    ul.typeahead.dropdown-menu li:last-child a { border-bottom:0px !important; }
    .bgcolor {max-width: 550px;min-width: 290px;max-height:340px;background:url("world-contries.jpg") no-repeat center center;padding: 100px 10px 130px;border-radius:4px;text-align:center;margin:10px;}
    .demo-label {font-size:1.5em;color: #686868;font-weight: 500;color:#FFF;}
    .dropdown-menu>.active>a, .dropdown-menu>.active>a:focus, .dropdown-menu>.active>a:hover {
      text-decoration: none;
      background-color: #1f3f41;
      outline: 0;
    }
    table.dataTable thead th{
      padding: 1px 1px 1px 1px;
      border-right: 1px solid #dddddd;
    }
    table.dataTable tfoot th{
      padding: 1px 1px 1px 1px;
      white-space: nowrap;
      border-right: 1px solid #dddddd;
    }
    table.dataTable tbody td {
      padding: 1px 1px 1px 1px;
      border-right: 1px solid #dddddd;
      /* //white-space: nowrap; */
      -webkit-box-sizing: content-box;
      -moz-box-sizing: content-box;
      box-sizing: content-box;
    }
    .criteria1{
      text-transform: uppercase;
      font-weight: bold;
    }
    
    #example_length{
      margin-left: 10%;
      margin-top: 1px;
    }
    @keyframes spinner {
    to {transform: rotate(360deg);}
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
  #preloader1{
    position: fixed;
    top:40%;
    left: 52%;
    z-index: 999;
  }
  #preloader1 {
    background: transparent !important;
  }
  </style>

  @extends('monthwise-lot-status-report.base')
  @section('action-content')
  <div id="preloader1" align="center" style="display: none;"><img src="images/ZKZg.gif" width="100px"></div>
      <!-- Main content -->
      <section class="content">
        <div class="box box-default">
          <!-- <div class="box-header with-border">
            <div class="row">
                <div class="col-sm-8">
                  <h3 class="box-title">Lot To be Pushed To IFMS</h3>
                </div>
                
            </div>
          </div> -->
          <div class="box-body">
            <div class="row" style="margin-bottom:1%">
              <div class="form-group col-md-3">
                  <label class="control-label">Scheme</label>
                  <select class="form-control select2"  name="scheme" id='scheme'>
                      <option value="">--Select Scheme--</option>
                      <option value="All">All</option>
                      @foreach($schemes as $scheme)
                      <option value="{{$scheme->Scheme->id}}">{{$scheme->Scheme->scheme_name}}</option>
                      @endforeach
                  </select>
                  <span id="error_scheme" class="text-danger"></span>
              </div>
              <div class="form-group col-md-3">
                  <label class="control-label">Financial Year</label>
                  <select class="form-control select2" name="lot_year" id="lot_year">
                      <option value="">--Select--</option>
                      <option value="2020-2021">2020-2021</option>
                      <option value="2021-2022" disabled>2021-2022</option>
                  </select>
                  <span id="error_lot_year" class="text-danger"></span>
              </div>
              <div class="form-group col-md-3">
                  <label class=" control-label">Payment Mode</label>
                  <select class="form-control select2" name="pay_mode"  id="pay_mode">
                      <option value="">--Select--</option>
                      <option value="IFMS">IFMS</option>
                      <option value="SBI">SBI</option>  
                                            
                  </select>
                  <span id="error_pay_mode" class="text-danger"></span>
              </div>
              <div class="form-group col-md-3">
                  <label class="control-label">Month</label>
                  <select class="form-control select2" name="lot_month" id="lot_month" multiple>
                      <option value="">--Select--</option>
                      <option value="January">January</option>
                      <option value="February">February</option>
                      <option value="March">March</option> 
                      <option value="April">April</option>
                      <option value="May">May</option>   
                      <option value="June">June</option>   
                      <option value="July">July</option>   
                      <option value="August">August</option>      
                      <option value="September">September</option>   
                      <option value="October">October</option>   
                      <option value="November">November</option>   
                      <option value="December">December</option>  
                                            
                  </select>
                  <span id="error_lot_month" class="text-danger"></span>
              </div>
              
              <div class="form-group col-md-12" align="center">
                <button type="button" name="filter" id="filter" class="btn btn-info btn-lg">Filter</button>
                <!-- <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button> -->
              </div>
            </div>
            
            <div style="background-color: #e6e6e6; padding: 8px; ">
              <font size="5"><b>Consolidated Lot Status Report</b></font>
              <font size="4" class="text-primary" style="float: right; "><b>Date : <?php echo date('d/m/Y'); ?></b></font>
            </div>
            <br />
            <div class="table-responsive">
            <table id="example" class="display" cellspacing="0" width="100%" style="border-top: 1px solid #e6e6e6;">

            <thead>

              <tr role="row" style="font-size: 14px;">
                <th  width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Scheme Name</th>
                <th  width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Lot Month</th>
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">No .of Lot</th>
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Get All Lot</th>
                <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Lot Status Description</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Total Beneficiary</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of Success Beneficiary</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of Failed Beneficiary</th>                
              </tr>
            </thead>
            <tbody style="font-size: 16px;"></tbody>  
          </table>
          </div>
          </div>
        </div>
        
  <!-- </div> -->
<!-- </div> -->

<!-- /.row -->
</section>
<!-- /.content -->

  <!-- Modal -->
  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Lot Details</h4>
        </div>
        <div class="modal-body">
          <div id="lot_details"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left btn-lg" data-dismiss="modal">Close</button>
          <!-- <button type="button" class="btn btn-primary"><i class="fa fa-print"></i> Print</button> -->
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

  @endsection
  <script src='{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}'></script>


<script>

  $(document).ready(function() {
    var table = $('#example').DataTable( {
      //dom: 'Bfrtip',
      dom: 'Blfrtip',
      "paging": false,
      "pageLength":20,
      'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      "serverSide": true,
      "processing":true,
      "bRetrieve": true,
      "oLanguage": {
          "sProcessing": '<div id="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>' 
      },
      
      "ajax": 
      {
        url: "{{ url('monthly_lot_status_report') }}",
        type: "POST",
        data:function(d){
            d.scheme=   $('#scheme').val(),
            d.lot_year=   $('#lot_year').val(),
            d.lot_month=   $('#lot_month').val(),
            d.pay_mode=   $('#pay_mode').val(),
            d._token= "{{csrf_token()}}"
          }
        } ,
        "deferLoading": 0,
        // "deferRender": true,
        "columns": [
            { "data": "scheme_name" },
            { "data": "lot_month" },
            { "data": "lot_count" },
            { "data": "get_lot" },
            { "data": "status_description" },
            { "data": "ben_count" },
            { "data": "success_count" },
            { "data": "failed_count" },
          ],             
     
      buttons: [
       {
           extend: 'pdf',
           title: 'Monthly Lot Status Report Date: <?php echo date('d/m/Y'); ?>',
           text: '<b><i class="fa fa-file-pdf-o" style="color: #3e943d;"></i>PDF</b>',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,4,5,6,7],

            }
       },
       {
           extend: 'print',
           title: 'Monthly Lot Status Report Date: <?php echo date('d/m/Y'); ?>',
           text: '<b><i class="fa fa-print" style="color: #d44317;"></i>Print</b>',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                 columns: [0,1,2,4,5,6,7],
                stripHtml: false,
            }
       },
       {
           extend: 'excel',
           title: 'Monthly Lot Status Report Date: <?php echo date('d/m/Y'); ?>',
           text: '<b><i class="fa fa-file-excel-o" style="color: #161c9c;"></i>Excel</b>',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                 columns: [0,1,2,4,5,6,7],
                stripHtml: false,
            }
       },
       //  {
       //     extend: 'copy',
       //     title: 'Lot To be Pushed To IFMS',
       //     footer: true,
       //     pageSize:'A4',
       //     //orientation: 'landscape',
       //     pageMargins: [ 40, 60, 40, 60 ],
       //     exportOptions: {
       //          columns: [0,1,2,3,4,5],
       //          stripHtml: false,
       //      }
       // },
       {
           extend: 'csv',
           title: 'Monthly Lot Status Report Date: <?php echo date('d/m/Y'); ?>',
           text: '<b><i class="fa fa-file-text" style="color: #d1ab00;"></i>CSV</b>',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,4,5,6,7],
                stripHtml: false,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    } );


    error_lot_year='';
    error_lot_month='';
    error_scheme='';
    error_pay_mode='';
    $('#filter').click(function(){
    if($.trim($('#lot_year').val()).length == 0)
    {
        error_lot_year = 'Financial Year is Required';
        $('#error_lot_year').text(error_lot_year);
        $('#error_lot_year').next().find('.select2-selection').addClass('has-error');
    }
    else
    {
    error_lot_year = '';
    $('#error_lot_year').text(error_lot_year);
    $('#error_lot_year').next().find('.select2-selection').removeClass('has-error');
    }

    if($.trim($('#lot_month').val()).length == 0)
    {
        error_lot_month = 'Month is Required';
        $('#error_lot_month').text(error_lot_month);
        $('#error_lot_month').next().find('.select2-selection').addClass('has-error');
    }
    else
    {
    error_lot_month = '';
    $('#error_lot_month').text(error_lot_month);
    $('#error_lot_month').next().find('.select2-selection').removeClass('has-error');
    }

    if($.trim($('#scheme').val()).length == 0)
    {
        error_scheme = 'Scheme is Required';
        $('#error_scheme').text(error_scheme);
        $('#error_scheme').next().find('.select2-selection').addClass('has-error');
    }
    else
    {
    error_scheme = '';
    $('#error_scheme').text(error_scheme);
    $('#error_scheme').next().find('.select2-selection').removeClass('has-error');
    }

    if($.trim($('#pay_mode').val()).length == 0)
    {
        error_pay_mode = 'Payment mode is Required';
        $('#error_pay_mode').text(error_pay_mode);
        $('#error_pay_mode').next().find('.select2-selection').addClass('has-error');
    }
    else
    {
    error_pay_mode = '';
    $('#error_pay_mode').text(error_pay_mode);
    $('#error_pay_mode').next().find('.select2-selection').removeClass('has-error');
    }

    if( error_lot_year != '' || error_lot_month !='' || error_scheme!='' || error_pay_mode!='')
    {
       return false;
    }
    else
    {
    // return true;
    table.clear().draw();
    table.ajax.reload();
    }

    });

    $('#reset').click(function(){
      $('#scheme').val('');
      $('#lot_year').val('');
      $('#lot_month').val('');
      $('#pay_mode').val('');
      table.clear().draw();
    });

    
  });

  function getLotFunction(value){
    document.getElementById('preloader1').style.display = '';
    var val = value;
    //alert(val);
    var myarr = val.split("_");
    var scheme = myarr[0];
    var lot_month = myarr[1];
    var lot_status = myarr[2];
    var pay_mode = myarr[3];
    var lot_year = myarr[4];
    $.ajax({
      url: 'get_lot_of_monthly_lot_status_report',
      type: 'POST',
      data: { 
        'scheme_id' : scheme, 
        'lot_month' : lot_month,
        'lot_status' : lot_status,
        'pay_mode' : pay_mode,
        'lot_year' : lot_year,
        '_token' : "{{csrf_token()}}" 
      },
      success: function(datas)
      {
        $('#lot_details').text('');
        html = '';
        html = '<table class="table table-bordered table-condensed">';

        if (datas.pay_mode == 'IFMS') {
          html += '<thead><tr><th>Lot No</th><th>Lot Created</th><th>Total Beneficiary</th></tr></thead><tbody>';
        }
        else if (datas.pay_mode == 'SBI') {
          html += '<thead><tr><th>Debit Reference</th><th>Lot Created</th><th>Total Beneficiary</th></tr></thead><tbody>';
        }
        for (var i = 0; i < datas.data.length; i++) {
          //console.log(datas.data[i].lot_no);
          if (datas.pay_mode == 'IFMS') {
           // console.log(format(datas.data[i].created_at));
            html += '<tr><td>'+datas.data[i].lot_no+'</td><td>'+format(datas.data[i].created_at)+'</td><td>'+datas.data[i].ben_count+'</td></tr>';
          }
          else if (datas.pay_mode == 'SBI') {
            html += '<tr><td>'+datas.data[i].debit_reference+'</td><td>'+format(datas.data[i].created_at)+'</td><td>'+datas.data[i].credit_count+'</td></tr>';
          }
          else {
            html += '<tr><td colspan="2">No Data Found</td></tr>';
          }
          
        }
        html += '</tbody></table>';
        $('#lot_details').html(html);
        document.getElementById('preloader1').style.display = 'none';
        $('#modal-default').modal('show');

        // $.confirm({
        //   type: 'green',
        //   icon: 'fa fa-check',
        //   title: 'Lot Details',
        //   boxWidth: '50%',
        //   useBootstrap: false,
        //   smoothContent: true, 
        //   content: html,
        // });
        
      },
      error: function (jqXHR, textStatus, errorThrown) {
        var msg = "";
        if (jqXHR.status !== 422 && jqXHR.status !== 400) {
            msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
        } else {
            if (jqXHR.responseJSON.hasOwnProperty('exception')) {
                if (jqXHR.responseJSON.exception_code == 23000) {
                    msg += "Some Sql Exception Occured";
                } else {
                    msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
                }
            } else {
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
      
    });
  }
  function format(inputDate) {
    var date = new Date(inputDate);
    if (!isNaN(date.getTime())) {
        // Months use 0 index.
        var month="";
        
        month= date.getMonth() + 1;
        return  date.getDate() + '/' + month + '/' + date.getFullYear();
    }
}
</script>
