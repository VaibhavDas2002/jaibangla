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

  @extends('report-stop-payment.base')
  @section('action-content')
  <div id="preloader1" align="center" style="display: none;"><img src="images/ZKZg.gif" width="100px"></div>
      <!-- Main content -->
      <section class="content">
        <div class="box box-danger">
          <!-- <div class="box-header with-border">
            <div class="row">
                <div class="col-sm-8">
                  <h3 class="box-title">Lot To be Pushed To IFMS</h3>
                </div>
                
            </div>
          </div> -->
          <div class="box-body">
            <div class="row" style="margin-bottom:1%">
              <div class="form-group col-md-5">
                  <label class="control-label">Scheme</label>
                  <select class="form-control"  name="scheme" id='scheme'>
                      <option value="">--Select Scheme--</option>
                      <!-- <option value="all">All</option> -->
                      @foreach($schemes as $scheme)
                      <option value="{{$scheme->Scheme->id}}">{{$scheme->Scheme->scheme_name}}</option>
                      @endforeach
                  </select>
                  <span id="error_scheme" class="text-danger"></span>
              </div>
              <div class="form-group col-md-5" style="margin-top: 20px;">
                <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
                <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
              </div>
              </div>
              
            <!-- </div> -->
            
            <div style="background-color: #e6e6e6; padding: 5px; ">
              <font size="4" class="text-default"><b>De-activated Beneficiary</b></font>
              <font size="4" class="text-primary" style="float: right; "><b>Date : <?php echo date('d/m/Y'); ?></b></font>
            </div>
            <br />
            <div class="table-responsive">
              <table id="example" class="display" cellspacing="0" width="100%" style="border-top: 1px solid #e6e6e6;">
                <thead>
                  <tr role="row" style="font-size: 14px;">
                    <th  width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Pension Id</th>
                    <th  width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Scheme</th>
                    <th  width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Name</th>
                    <th  width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Block/Municipality</th>
                    <th  width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">GP/Ward</th>
                  </tr>
                </thead>
                <tbody style="font-size: 14px;"></tbody>  
              </table>
            </div>
          </div>
        </div>
        
  <!-- </div> -->
<!-- </div> -->

<!-- /.row -->
</section>
<!-- /.content -->

@endsection
<script src='{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}'></script>

<script>

  $(document).ready(function() {
    fill_datatable();

    function fill_datatable(scheme_id = '')
    {
      var dataTable = $('#example').DataTable({
        //dom: 'Bfrtip',
        dom: 'Blfrtip',
        "paging": true,
        "pageLength":50,
        'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
        "serverSide": true,
        "processing":true,
        "bRetrieve": true,
        "oLanguage": {
            "sProcessing": '<div id="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>' 
        },
        
        "ajax": 
        {
          url : "{{ url('de_activated_ben') }}",
          type : "GET",
          data : {scheme : scheme_id},
        },
        "columns": [
            { "data": "id" },
            { "data": "scheme_name" },
            { "data": "name" },
            { "data": "block_ulb_name" },
            { "data": "gp_ward_name" },
          ],             
        buttons: [
        'pdf','excel','print'
        ]
      });
    }
  
    $('#filter').click(function(){
        var scheme_id = $('#scheme').val();

        if(scheme_id != '')
        {
            $('#example').DataTable().destroy();
            fill_datatable(scheme_id);
        }
        else
        {
            alert('Select filter option');
        }
    });

    $('#reset').click(function(){
        $('#scheme').val('');
        $('#example').DataTable().destroy();
        fill_datatable();
    });
  });
</script>
