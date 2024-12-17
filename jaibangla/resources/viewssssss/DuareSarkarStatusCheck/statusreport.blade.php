
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, minimum-scale=0.1" />
<title>Jai Bangla | Government of West Bengal</title>
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset("/images/biswofab.png") }}" />
<link
    href="https://fonts.googleapis.com/css?family=Open+Sans:400,600&amp;display=swap"
    rel="stylesheet" />
<!-- <link href="{{ asset("/css/boostrap-new.min.css") }}" rel="stylesheet" /> -->
<link rel="stylesheet" href="{{ asset ("/css/boostrap-new.min.css") }}"  type="text/css" >
<link rel="stylesheet" href="{{ asset ("/css/Boostrrap.css") }}"  type="text/css" >
<!-- <link href="{{ asset("/css/Boostrrap.css") }}" rel="stylesheet" /> -->
<style>
body {
    background-size: auto;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
    font-family: 'Open Sans', sans-serif;
}

.adminlogintable {
    margin-top: 30px;
}

.inner-container1 {
    margin-top: 50px;
    width: 100%;
    background-color: white;
    background-size: auto;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
    overflow-Y:scroll;
}

.form-control {
    padding: 6px 4px;
}

.adminlogintable tbody tr td input, .adminlogintable tbody tr td img {
    margin-bottom: 10px;
    font-size: 13px;
}

.list-row {
    position: relative;
    top: -30px;
    margin-top: 30px;
}

.list-text {
    top: -10px;
}

.adminlogintable tbody tr td input.btnotp {
    margin-bottom: 3px;
}

.admintextnumber {
    letter-spacing: 2px;
    margin-bottom: 30px;
    font-size: 18px;
    font-weight: 600;
}

/* */

.panel-default>.panel-heading {
  color: #333;
  background-color: #fff;
  border-color: #e4e5e7;
  padding: 0;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

.panel-default>.panel-heading a {
  display: block;
  padding: 10px 15px;
}

.panel-default>.panel-heading a:after {
  content: "";
  position: relative;
  top: 1px;
  display: inline-block;
  font-family: 'Glyphicons Halflings';
  font-style: normal;
  font-weight: 400;
  line-height: 1;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  float: right;
  transition: transform .25s linear;
  -webkit-transition: -webkit-transform .25s linear;
}

.panel-default>.panel-heading a[aria-expanded="true"] {
  background-color: #eee;
}

.panel-default>.panel-heading a[aria-expanded="true"]:after {
  content: "\2212";
  -webkit-transform: rotate(180deg);
  transform: rotate(180deg);
}

.panel-default>.panel-heading a[aria-expanded="false"]:after {
  content: "\002b";
  -webkit-transform: rotate(90deg);
  transform: rotate(90deg);
}
</style>

</head>
<body>
    <!-- <form name="form1" method="post" action="#" onsubmit="#" id="form1"> -->
        <div class="container">
            <div class="inner-container1">
                
                <div class="row">
                    <div class="col-xs-3 col-sm-3 col-md-2" style="margin-top: 20px; margin-bottom: 10px;">
                        <img class="biswo" src="{{ asset("images/biswo.png") }}" alt="Alternate Text" />
                    </div>
                    <div class="col-xs-9  col-sm-9 col-md-10" style="margin-top: 20px; ">
                            <!-- <img class="e-sahayR"
                            style="float: right; margin-right: 20px; position: relative; top: 40px; width: 450px;"
                            src="jaibangla.png" alt="Jai Bangla" /> -->
                            <img class="e-sahayR"
                            style="float: right; margin-right: 20px; position: relative; width: 450px;"
                            src="{{ asset("images/jaibangla_dtl.png") }}" alt="Jai Bangla" />
                        <!-- <h4 class="first-heading">Government of West Bengal</h4> -->
                            <img src="{{ asset("images/bangla.png") }}" style="margin-top: 40px;">
                    </div>
                </div>
                <div class="row">
                
                <div class="col-xs-11">
                <div class="col-xs-8" style="text-align:right">
                Scheme:<span style="font-weight:bold;font-size:20px;">{{$scheme_name}}</span>
                </div>
                <div style="text-align:right;padding-top:6px;">
                <?php
                  date_default_timezone_set('Asia/Kolkata');
                  $date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));
                  $date = $date->format('F j, Y g:i:a');
                  echo $date;
                ?>
                </div>
                </div>
                <div class="col-xs-1">
                  <a href="ds_status_check/{{$scheme_id}}"><img  style="width:50px;"  src="{{ asset("images/back_btn.png") }}" 
              alt="Back" /></a>
                </div>
                </div>
              <div  style="margin-left: 1050px" >
           
              
        
              </div>
             
  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="margin:10px 20px 10px 20px;">
   @if(count($list_arr)>0)
    <div class="panel-group wrap" id="bs-collapse">
    @php
      $p=1;
    @endphp
     @foreach($list_arr as $row)
    <div class="panel panel-default" style="margin-bottom:20px;">
      <div class="panel-heading" role="tab" id="heading_{{$row['beneficiary_id']}}">
        <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_{{$row['beneficiary_id']}}" aria-expanded="true" aria-controls="collapse_{{$row['beneficiary_id']}}">
         Application Id:{{$row['application_id']}}
        </a>
      </h4>
      </div>
      <div id="collapse_{{$row['beneficiary_id']}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_{{$row['beneficiary_id']}}">
        <div class="panel-body" >
        <table class="table table-bordered">  
           <tbody>
           <tr>       
           <th scope="row">Name</th>
           <td>{{$row['ben_fullname']}}</td>
           </tr>
          <tr>
           <th scope="row">Father Name</th>         
           <td>{{$row['father_fullname']}}</td>
           </tr>
           <tr>
           <th scope="row">Address</th>
           <td>District:{{$row['district_name']}},Block/Municipality:{{$row['block_ulb_name']}},GP/Ward:{{$row['gp_ward_name']}}</td> 
           </tr>
          <tr>
           <th scope="row">Age</th>
           <td>{{$row['ben_age']}}</td> 
           </tr>
          <tr>       
           <th scope="row">Current Application Status</th>
           <td>{{$row['message']}}</td>
           </tr>
            </tbody>
            </table>
        </div>
      </div>
    </div>
     @php
        $p++;
      @endphp
    @endforeach
    @endif
  
   
  </div>
</div>
                   
            
            </div>
           
        </div>
    <!-- </form> -->
    <!-- jQuery 2.1.3 -->
    <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
       <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

</body>

</html>


