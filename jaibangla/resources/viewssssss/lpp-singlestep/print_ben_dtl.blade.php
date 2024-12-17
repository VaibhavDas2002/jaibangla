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
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("frontend/img/favicon.ico") }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    <!-- Bootstrap 3.3.6 -->
    <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome -->
   <!--  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"> -->
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">

    <!-- Select2 -->
   
    <!-- Ionicons -->
    <!--link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"-->
    <link href="{{ asset('css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/AdminLTE/plugins/datepicker/datepicker3.css")}}" rel="stylesheet" type="text/css" />
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/select2/select2.min.css")}}">
    <!-- Theme style -->
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-template.css') }}" rel="stylesheet">
    
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/iCheck/flat/blue.css")}}">


    <!-- fancybox -->
    
     <link rel="stylesheet" href="{{ asset ("/bower_components/AdminLTE/dist/css/jquery.fancybox.css") }}"  type="text/css" >
      <link rel="stylesheet" href="{{ asset ("/bower_components/AdminLTE/dist/css/prettyPhoto.css") }}"  type="text/css" >

    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}">

     <style type="text/css">
   
  #ben_view_modal{
    width: 800px;
  }
  @media print {
    .link{
      visibility: hidden;
    }
    a[href]:after {
      visibility: hidden;
    }
	}
</style>


  </head>

<script type="text/javascript">
  window.history.forward();
</script>
  <body onload="PrintPanel()">
    <div class="">        
        <div class="" id="ben_view_modal" tabindex="-1">
			<div class="" id="printArea">
				<div class="">
					<div class="">
						<h4 class="">Beneficiay Detail View</h4>
					</div>
					<div class="">
						<div class="row">
							<div class="col-md-3 text-center">
								@if($ben->profile_pic != null)
									<img src="http://jaibanglamw.wb.gov.in/storage/workers_doc/{{$ben->profile_pic}}" id="profile_pic" alt="Profile picture" width="150px" height="150px" />
								@else
									<img src="{{ asset('upload/img_avatar.png') }}" id="profile_pic" alt="" width="150px" height="150px" />
								@endif
							<br/><br/><span class="label label-success" style="font-size:13px;"><b>Beneficiary ID:</b></span><br/><br/> <span class=" badge item_value" id="view_ben_id">{{$ben->beneficiary_id}}</span>
							</div>
							<div class="col-md-8 panel panel-info">
									<div class="row panel-heading">
										<div class="col-md-12"><h4>Personal Details</h4></div>
									</div>
									<div class="row penel-body">
										<div class="col-md-12"><span class="item_header">Full Name: </span>
										<span class="item_value" id="ben_name">{{$ben->ben_name}}</span></div>
									</div>
									<div class="row">
										<div class="col-md-12"><span class="item_header">Fathers Name: </span>
										<span class="item_value" id="benf_name">{{$ben->ben_father}}</span></div>
									</div>
									<div class="row">
										<div class="col-md-5"><span class="item_header">Date of Birth: </span>
											<span class="item_value" id="ben_dob">{{$ben->dob}}</span></div>
										<div class="col-md-7"><span class="item_header">Gender: </span>
											<span class="item_value" id="ben_gender">{{$ben->gender}}</span></div>
									</div>
									<div class="row">
										<div class="col-md-5"><span class="item_header">Latitude: </span>
											<span class="item_value" id="ben_lat">{{$ben->present_lat}}</span></div>
										<div class="col-md-7"><span class="item_header">Longitude: </span>
											<span class="item_value" id="ben_long">{{$ben->present_long}}</span></div>										
									</div>
									<div class="row">
										<div class="col-md-12"><span class="item_header">State: </span>
											<span class="item_value" id="ben_state">West Bengal</span></div>
									</div>
									<div class="row">	
										<div class="col-md-12"><span class="item_header">District: </span>
											<span class="item_value" id="ben_dist">{{$ben->district_name}}</span></div>										
									</div>
									<div class="row">
										<div class="col-md-5"><span class="item_header">Rural/Urban: </span>
											<span class="item_value" id="ben_type">{{$ben->rural_urban}}</span></div>
										<div class="col-md-7"><span class="item_header">Block/Municipality: </span>
											<span class="item_value" id="ben_block">{{$ben->block_name}}</span>
											<span class="item_value" id="ben_block">{{$ben->municipality_name}}</span></div>										
									</div>
									<div class="row">
										<div class="col-md-5"><span class="item_header">Pincode: </span>
										<span class="item_value" id="ben_pin">{{$ben->pincode}}</span></div>
										<div class="col-md-7"><span class="item_header">GP/Ward: </span>
											<span class="item_value" id="ben_gp">{{$localBody}}</span></div>								
									</div>
									<div class="row">
										<div class="col-md-12"><span class="item_header">Address Line: </span>
											<span class="item_value" id="ben_addr">{{$ben->address_line}}</span></div>

									</div>
							</div>
						</div>
						<div class="row">
							<hr/>
						</div>
						<div class="row">
							<div class="col-md-3">
								<span class="item_header">Mobile No:</span> <span class="item_value" id="ben_mobile">{{$ben->mobile_no}}</span>
							</div>
							<div class="col-md-3">
								<span class="item_header">Family Mobile No:</span> <span class="item_value" id="benf_mobile">{{$ben->family_mobile_no}}</span>
							</div>
							<div class="col-md-3">
								<span class="item_header">Aadhar No:</span> <span class="item_value" id="ben_aadhar">{{$ben->aadhar_no}}</span>
							</div>
							<div class="col-md-3">
								<span class="item_header">Voter ID Number:</span> <span class="item_value" id="ben_voterid">{{$ben->voterid_no}}</span>
							</div>
						</div>	
						<div class="row">
							<div class="col-md-3">
								<span class="item_header">Account No:</span> <span class="item_value" id="ben_account">{{$ben->account_no}}</span>
							</div>
							<div class="col-md-3">
								<span class="item_header">Bank Name:</span> <span class="item_value" id="ben_bank">{{$ben->bank_name}}</span>
							</div>
							<div class="col-md-3">
								<span class="item_header">Bank Branch:</span> <span class="item_value" id="ben_branch">{{$ben->bank_branch}}</span>
							</div>
						</div>
						<div class="row">								
							<div class="col-md-3">
								<span class="item_header">IFSC:</span> <span class="item_value" id="ben_ifsc">{{$ben->ifsc_code}}</span>
							</div>
							<div class="col-md-3">
								<span class="item_header">UPI:</span> <span class="item_value" id="ben_ifsc">{{$ben->upi_id}}</span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<span class="item_header">Application Date:</span><span class="item_value" id="ben_appdate"> {{$ben->created_at}}</span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12"><hr/></div>
						</div>
						<div class="row pagebreak">
							@if($ben->aadhar_img!=null)	
							<div class="col-md-4 text-center" id="aadhar_img_div">
								<div class="row">
									<div class="col-md-12 text-center">
										<img id="aadhar_img" src="http://jaibanglamw.wb.gov.in/storage/workers_doc/{{$ben->aadhar_img}}" alt="Aadhar Image" width="250px" height="150px" />
									</div>
									<div class="col-md-12 text-center">
										<span class="item_header">AADHAR CARD COPY</span>
									</div>
								</div>
							</div>
							@endif
							@if($ben->voterid_img_front!=null)
							<div class="col-md-4 text-center" id="voter_f_img_div">
								<div class="row">
									<div class="col-md-12 text-center">
										<img id="voter_f_img" src="http://jaibanglamw.wb.gov.in/storage/workers_doc/{{$ben->voterid_img_front}}" alt="Voter ID back" width="250px" height="150px" />
									</div>
									<div class="col-md-12 text-center">
										<span class="item_header">VOTER CARD COPY (FRONT)</span>
									</div>
								</div>
							</div>
							@endif
							@if($ben->voterid_img_back!=null)
							<div class="col-md-4 text-center" id="voter_b_img_div">
								<div class="row">
									<div class="col-md-12 text-center">
										<img id="voter_b_img" src="http://jaibanglamw.wb.gov.in/storage/workers_doc/{{$ben->voterid_img_back}}" alt="Voter ID back"  width="250px" height="150px"/>
									</div>
									<div class="col-md-12 text-center">
										<span class="item_header">VOTER CARD COPY (BACK)</span>
									</div>
								</div>
							</div>
							@endif
						</div>		
					</div>
				</div>
			</div>
        </div>
<!-- jQuery 2.1.3 -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js") }}" type="text/javascript" ></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>

<!-- Bootstrap WYSIHTML5 -->
<script src="{{ asset("/bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js") }}"></script>
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/demo.js") }}" type="text/javascript"></script>

<!-- Select2 -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/select2/select2.full.min.js") }}"></script>

<!-- fancybox -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/jquery.fancybox.min.js") }}" type="text/javascript"></script>

<script src="{{ asset ("/bower_components/AdminLTE/dist/js/jquery.prettyPhoto.js") }}" type="text/javascript"></script>

<script src="{{ asset("js/buttons.flash.min.js") }}"></script>
<script src="{{ asset("js/jszip.min.js") }}"></script>
<script src="{{ asset("js/pdfmake.min.js") }}"></script>
<script src="{{ asset("js/vfs_fonts.js") }}"></script>
<script src="{{ asset("js/buttons.html5.min.js") }}"></script>
<script src="{{ asset("js/buttons.print.min.js") }}"></script>

  {{-- <script>
  $(document).ready(function() {
    $('#ben_view_modal').modal('show');
  });
</script> --}}


<script type = "text/javascript" >
function preventBack() { window.history.forward(); }
setTimeout("preventBack()", 0);
window.onunload = function () { null };


function PrintPanel() {
    var panel = document.getElementById("printArea");
    var printWindow = window.open('', '', '');
    printWindow.document.write('<html><head><title>Print Invoice</title>');
    
    // Make sure the relative URL to the stylesheet works:
    printWindow.document.write('<base href="' + location.origin + location.pathname + '">');
    
    // Add the stylesheet link and inline styles to the new document:
    printWindow.document.write('<link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />');
    printWindow.document.write('<style type="text/css">.style1{width: 100%;}</style>');
    
    printWindow.document.write('</head><body >');
    printWindow.document.write(panel.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    setTimeout(function () {
        printWindow.print();
    }, 500);
    return false;
}

</script>

</body>
</html>
	