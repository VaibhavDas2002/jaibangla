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
    top:40%;
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
      color: #d9534f;
      content:'*';
      font-weight: bold;
      margin-left: 5px;
      float:right;
      margin-top: 5px;
  }
  #loadingDivModal{
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
  .disabledcontent {
    pointer-events: none;
    opacity: 0.4;
  }
  .has-error {
      border-color: #cc0000;
      background-color: #ffff99;
    }
    
</style>

<!--
  This is a starter template page. Use this page to start your new project from
  scratch. This page gets rid of all links and provides the needed markup only.
  -->
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>JB | Jai Bangla</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("frontend/img/favicon.ico") }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    <!-- Bootstrap 3.3.6 -->
    <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
   
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
   
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-template.css') }}" rel="stylesheet">
   
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

<script type="text/javascript">
  window.history.forward();
</script>
  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
      @include('layouts.header')
      @include('layouts.sidebar')
      <div class="content-wrapper">
        <section class="content-header">
          <h1>
            Chart
          </h1>
        </section>
        <section class="content">
          <div class="box box-default" id="full-content">
            <div class="box-body">
              <div class="panel panel-default">
                <div class="panel-body" style="padding: 5px;">
                  <div class="row">
                    @if ( ($message = Session::get('success')))
                      <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                      </div>
                    @endif
                    @if(count($errors) > 0)
                      <div class="col-md-12">
                        <div class="alert alert-danger alert-block">
                          <ul>
                            @foreach($errors->all() as $error)
                              <li><strong> {{ $error }}</strong></li>
                            @endforeach
                          </ul>
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
                  </div>
             
                  <div class="row">
                    <div class="col-md-6">
                      <!-- AREA CHART -->
                      <div class="box box-primary">
                        <div class="box-header with-border">
                          <div class="box-tools pull-right">
                          </div>
                        </div>
                        <div class="box-body">
                          <div class="form-group col-md-4 " >
                          <label class="required-field">Scheme</label>
                          </div>
                          <div class="form-group col-md-4 ">
                            <!-- <label class="required-field">Scheme</label> -->
                            <select name="scheme_id" id="scheme_id" class="form-control" >
                              @foreach ($sceme_list as $scheme)
                                <option value="{{$scheme->id}}" @if($selected_scheme== $scheme->id)  selected  @endif>{{$scheme->scheme_name}}</option>
                              @endforeach
                            </select>
                            <span id="error_district" class="text-danger"></span>
                          </div>
                        <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" id="submit_loader1" width="50px" height="50px" style="display:none;" ></div>

                        <table class="table table-condensed" style="font-size:14px;" id="example">
                          <thead class="table-primary">
                            <tr>
                              <th>District</th>
                              <th>Total No. of Approved Beneficiaries</th>
                              <th>Aadhaar Capture</th>
                              <th>Aadhaar Capture (%)</th>
                            </tr>
                          </thead>
                          <tbody>
            
                                @if(count($result1)>0)
                                @php
                                $fotter_1=0;
                                $fotter_2=0;
                                $fotter_3=0;
                                @endphp
                                @foreach($result1 as $arr1)
                                @php
                                $percent=0;
                                if($arr1->aadhar_capture==$arr1->approved){
                                $percent=100;

                                }
                                else
                                $percent=round($arr1->aadhar_capture*100/$arr1->approved,2);
                                if($percent==100)
                                $class='success';
                                  else if($percent>50 and $percent<100)
                                  $class='info';
                                  else if($percent<=50)
                                  $class='danger';
                                  $fotter_1=$fotter_1+$arr1->approved;
                                  $fotter_2=$fotter_2+$arr1->aadhar_capture;
                                  $fotter_3=round($fotter_3+$percent,2);
                                  @endphp
                                  <tr   class="{{$class}}" >
                                    <td>{{$arr1->location_name}}</td>
                                    <td>{{$arr1->approved}}</td>
                                    <td>{{$arr1->aadhar_capture}}</td>
                                    <td>{{$percent}}</td>
                                  </tr>
                                @endforeach 
                                @endif     
                          </tbody>
                        </table>
                      </div>
                    </div>
                   

                    <!-- DONUT CHART -->
                     <!-- <div class="box box-danger">
                      <div class="box-header with-border">
                        <h3 class="box-title">Donut Chart</h3>

                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                          </button>
                          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                      </div>
                       <div class="box-body">
                       <canvas id="pieChart" style="height:250px"></canvas>
                      </div> 
                    </div>  -->
                  </div>
                
                  <div class="col-md-6">
                    <div class="col-md-3"style="width:330px">
                      <!-- LINE CHART -->
                      <div class="box box-info">
                        <div class="box-header with-border">
                          <h3 class="box-title">Aadhaar Capture Percentage</h3>
                        </div>
                        <div class="box-body">
                          <div class="chart">
                            <canvas id="barChart1" style="height:330px"></canvas>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3"style="width:330px">
                      <!-- BAR CHART -->
                      <div class="box box-success">
                        <div class="box-header with-border">
                          <h3 class="box-title">Approved vs Aadhaar Capture</h3>
                        </div>
                      <div class="box-body">
                      <div class="chart">
                        <canvas id="barChart2" style="height:330px"></canvas>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6"style="width:750px">
                  <div class="box box-danger">
                    <div class="box-header with-border">
                      <h3 class="box-title">Yet to Verified/Yet to Approved/Approved </h3>
                      <div class="box-body">
                      <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" id="submit_loader2" width="50px" height="50px" style="display:none;" ></div>

                        <canvas id="pieChart" style="height:250px"></canvas>
                      </div> 
                    </div> 
                  </div>
                </div>

                

                

              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  @include('layouts.footer')
  <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
  <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
  <script src="{{ asset ("/bower_components/AdminLTE/plugins/chartjs/Chart.min.js") }}" type="text/javascript"></script>
  <script>
  $(function () {
    $("#submit_loader1").hide();
    $("#submit_loader2").hide();
    var PieData = <?php echo json_encode($pieChart); ?>;
    var AadharCapturePercent = <?php echo json_encode(
        $AadharCapturePercent
    ); ?>;
    var approveData = <?php echo json_encode($approveData); ?>;
    var aadharCaptureData = <?php echo json_encode($aadharCaptureData); ?>;

     // console.log(PieData);
    //-------------
    //- PIE CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
     var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
     var pieChart = new Chart(pieChartCanvas);
   
    var pieOptions = {
      //Boolean - Whether we should show a stroke on each segment
      segmentShowStroke: true,
      //String - The colour of each segment stroke
      segmentStrokeColor: "#fff",
      //Number - The width of each segment stroke
      segmentStrokeWidth: 2,
      //Number - The percentage of the chart that we cut out of the middle
      percentageInnerCutout: 50, // This is 0 for Pie charts
      //Number - Amount of animation steps
      animationSteps: 100,
      //String - Animation easing effect
      animationEasing: "easeOutBounce",
      //Boolean - Whether we animate the rotation of the Doughnut
      animateRotate: true,
      //Boolean - Whether we animate scaling the Doughnut from the centre
      animateScale: false,
      //Boolean - whether to make the chart responsive to window resizing
      responsive: true,
      // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio: true,
      //String - A legend template
      legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
    };
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
     pieChart.Doughnut(PieData, pieOptions);


     //-------------
    //- BAR CHART 1 -
    var barChartCanvas1 = $("#barChart1").get(0).getContext("2d");
    var barChart1 = new Chart(barChartCanvas1);
    var barChartData1 = {
      labels: ["OAP", "WP", "Manabik"],
      datasets: [
        {
          fillColor: ["#ebdb34", "orange", "#145889"],

          data: AadharCapturePercent
        }
      
      ]
    };
    
    var barChartOptions1 = {
      //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
      scaleBeginAtZero: true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines: true,
      //String - Colour of the grid lines
      scaleGridLineColor: "rgba(0,0,0,.05)",
      //Number - Width of the grid lines
      scaleGridLineWidth: 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines: true,
      //Boolean - If there is a stroke on each bar
      barShowStroke: true,
      //Number - Pixel width of the bar stroke
      barStrokeWidth: 2,
      //Number - Spacing between each of the X value sets
      barValueSpacing: 5,
      //Number - Spacing between data sets within X values
      barDatasetSpacing: 1,
      //String - A legend template
      legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
      //Boolean - whether to make the chart responsive
      responsive: true,
      maintainAspectRatio: true
    };
    
    barChartOptions1.datasetFill = false;
    barChart1.Bar(barChartData1, barChartOptions1);
    
    //-------------

    //-------------
    //- BAR CHART 2 -
    //-------------
    var barChartCanvas2 = $("#barChart2").get(0).getContext("2d");
    var barChart2 = new Chart(barChartCanvas2);
    var barChartData2 = {
      labels: ["OAP", "WP", "Manabik"],
      datasets: [
        {
          label: "Approved",
          fillColor: "rgba(255, 99,71)",
          strokeColor: "rgba(210, 214, 222, 1)",
          pointColor: "rgba(210, 214, 222, 1)",
          pointStrokeColor: "#c1c7d1",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(220,220,220,1)",
          data: approveData
        },
        {
          label: "Aadhaar Capture",
          fillColor: "rgba(60,141,188,0.9)",
          strokeColor: "rgba(60,141,188,0.8)",
          pointColor: "#3b8bba",
          pointStrokeColor: "rgba(60,141,188,1)",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(60,141,188,1)",
          data: aadharCaptureData
        }
      ]
    };
    barChartData2.datasets[1].fillColor = "#00a65a";
    barChartData2.datasets[1].strokeColor = "#00a65a";
    barChartData2.datasets[1].pointColor = "#00a65a";
    var barChartOptions2 = {
      //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
      scaleBeginAtZero: true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines: true,
      //String - Colour of the grid lines
      scaleGridLineColor: "rgba(0,0,0,.05)",
      //Number - Width of the grid lines
      scaleGridLineWidth: 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines: true,
      //Boolean - If there is a stroke on each bar
      barShowStroke: true,
      //Number - Pixel width of the bar stroke
      barStrokeWidth: 2,
      //Number - Spacing between each of the X value sets
      barValueSpacing: 5,
      //Number - Spacing between data sets within X values
      barDatasetSpacing: 1,
      //String - A legend template
      legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
      //Boolean - whether to make the chart responsive
      responsive: true,
      maintainAspectRatio: true
    };

    barChartOptions2.datasetFill = false;
    barChart2.Bar(barChartData2, barChartOptions2);
    $("#scheme_id").change(function(){
    $("#submit_loader1").show();
    $("#submit_loader2").show();
     $('#example').hide();
     $('#pieChart').hide();
     var scheme_id=$(this).val();
      $.ajax({
                type: 'get',
                url: '{{url('jb-chart-dist-aadhar-capture')}}',
                data: {
                  scheme_id: scheme_id,
                  _token: '{{ csrf_token() }}',
                },
                success: function (data) {
                  var table = $("#example tbody");
                    $('#submit_loader1').hide();
                    $('#submit_loader2').hide();
                    $("#example > tbody").html("");
                    table.append(data.tr1);
                   // console.log(data.pieChart)
                    pieChart.Doughnut(data.pieChart, pieOptions);
                    $("#example").show(); 
                    $('#pieChart').show();             
                },
                error: function (ex) {
                  //console.log(ex);
                  $("#submit_loader1").hide();
                  $("#submit_loader2").hide();
                  //$("#submitting").hide();
                  $("#submitting").show();
                 /// alert('Something wrong..may be session timeout. please logout and then login again');
                //  location.reload();
                   
                }
              });
	  });
  });
</script>

</body>
</html>
