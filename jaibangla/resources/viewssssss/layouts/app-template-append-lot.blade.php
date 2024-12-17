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
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("images/favicon.ico") }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    <!-- Bootstrap 3.3.6 -->
    <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!--link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet"-->

    <!-- Select2 -->
   
    <!-- Ionicons -->
    <!--link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"-->
    <link href="{{ asset('css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/AdminLTE/plugins/datepicker/datepicker3.css")}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/bootstrapValidator.css') }}" />
    <link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet">
    
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/select2/select2.min.css")}}">
    <!-- Theme style -->
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
      page. However, you can choose any other skin. Make sure you
      apply the skin class to the body tag so the changes take effect.
      -->
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-template.css') }}" rel="stylesheet">
    
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/iCheck/flat/blue.css")}}">


    <!-- fancybox -->
    
    <!--  <link rel="stylesheet" href="{{ asset ("/bower_components/AdminLTE/dist/css/jquery.fancybox.css") }}"  type="text/css" >
      <link rel="stylesheet" href="{{ asset ("/bower_components/AdminLTE/dist/css/prettyPhoto.css") }}"  type="text/css" >
 -->
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}">
    <link href="{{ asset("/bower_components/AdminLTE/dist/new_css/lightbox.min.css")}}" rel="stylesheet" type="text/css" />
    <style>
       .imageSize{
        font-size: 9px;
        color: #333;
     }
     label.required:after {
                color: red;
                content:'*';
                font-weight: bold;
                margin-left: 5px;
                float:right;
                margin-top: 5px;
            }
   </style>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
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
  <body class="hold-transition skin-blue sidebar-mini"  >
    <div class="wrapper">
    <!-- Main Header -->
    @include('layouts.header')
    <!-- Sidebar -->
    @include('layouts.sidebar')
    @yield('content')
    <!-- /.content-wrapper -->
    <!-- Footer -->
    @include('layouts.footer')
    <!-- ./wrapper -->
    <!-- REQUIRED JS SCRIPTS -->
    <!-- jQuery 2.1.3 -->
   
    <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>

    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/fastclick/fastclick.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/moment/moment.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/input-mask/jquery.inputmask.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/input-mask/jquery.inputmask.date.extensions.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/input-mask/jquery.inputmask.extensions.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/datepicker/bootstrap-datepicker.js") }}" type="text/javascript" ></script>
    <script src="{{ asset('js/bootstrapValidator.js') }}"></script>

    <script src="{{ asset('js/jquery-confirm.min.js') }}"></script>

    <!-- AdminLTE App -->
    <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

    <script src="{{ asset("js/select2.full.min.js") }}"></script>
    <!-- iCheck -->
    <script src="{{ asset("/bower_components/AdminLTE/plugins/iCheck/icheck.min.js") }}"></script>

    <!-- Bootstrap WYSIHTML5 -->
    <script src="{{ asset("/bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js") }}"></script>


    <script src="{{ asset ("/bower_components/AdminLTE/dist/js/demo.js") }}" type="text/javascript"></script>
    
    <!-- Select2 -->
    <script src="{{ asset ("/bower_components/AdminLTE/plugins/select2/select2.full.min.js") }}"></script>

    <!-- fancybox -->

  <!--   <script src="{{ asset ("/bower_components/AdminLTE/dist/js/jquery.fancybox.min.js") }}" type="text/javascript"></script> -->

    <script src="{{ asset ("/bower_components/AdminLTE/dist/js/jquery.prettyPhoto.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/AdminLTE/dist/js/validation_backend.js") }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset("/frontend/js/bootstrap-filestyle.min.js") }}"></script>

    <script src="{{ asset ("js/document_mgmt.js") }}" type="text/javascript"></script>


    <!--   <script src="{{ asset ("/bower_components/AdminLTE/dist/new/lightbox-plus-jquery.min.js") }}" type="text/javascript"></script> -->
        <script src="{{ asset ("/bower_components/AdminLTE/dist/new/lightbox.js") }}" type="text/javascript"></script>
     

    <!-- Optionally, you can add Slimscroll and FastClick plugins.
      Both of these plugins are recommended to enhance the
      user experience. Slimscroll is required when using the
      fixed layout. -->
      @yield('script')
      <script>
      $(document).ready(function() {
        //Date picker
        $('#birthDate').datepicker({
          autoclose: true,
          format: 'yyyy/mm/dd'
        });
        $('#hiredDate').datepicker({
          autoclose: true,
          format: 'yyyy/mm/dd'
        });
        $('#from').datepicker({
          autoclose: true,
          format: 'yyyy/mm/dd'
        });
        $('#to').datepicker({
          autoclose: true,
          format: 'yyyy/mm/dd'
        });


        // Mailbox
        $('.mailbox-messages input[type="checkbox"]').iCheck({
          checkboxClass: 'icheckbox_flat-blue',
          radioClass: 'iradio_flat-blue'
        });

        //Enable check and uncheck all functionality
        $(".checkbox-toggle").click(function () {
          var clicks = $(this).data('clicks');
          if (clicks) {
            //Uncheck all checkboxes
            $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
            $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
          } else {
            //Check all checkboxes
            $(".mailbox-messages input[type='checkbox']").iCheck("check");
            $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
          }
          $(this).data("clicks", !clicks);
        });

      //bootstrap WYSIHTML5 - text editor
      $('#policeverificationnote').wysihtml5()
      $('.select2').select2()

      $(".gallery a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'normal',theme:'light_square',slideshow:3000, autoplay_slideshow: true});

    });
</script>

<script>
  

// $(".fancybox")
//     .attr('rel', 'gallery')
//     .fancybox({
//         openEffect  : 'none',
//         closeEffect : 'none',
//         nextEffect  : 'none',
//         prevEffect  : 'none',
//         padding     : 0,
//         margin      : [20, 60, 20, 60] // Increase left/right margin
//     });
</script>
<script type="text/javascript">
// $("[data-fancybox]").fancybox({
//     // Options will go here
//   });
</script>
<script src="{{ asset('js/site.js') }}"></script>
<!--<script src="{{ asset('js/bank_edit.js') }}"></script>-->

<script type="text/javascript">
$('#btn_sendotp').click(function(){
  alert("good");
});  

</script>

<script type = "text/javascript" >
    function preventBack() { window.history.forward(); }
    setTimeout("preventBack()", 0);
    window.onunload = function () { null };
</script>
<script type = "text/javascript" >
//$("#formID").jCryption();
</script>


<script>
  $('.reProceePayment').click(function(){
   var id  = $(this).data("id"); 
   $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $.ajax({
    type: "GET",
    url:"{{url('/payment_query')}}/"+id,

    success: function(data) {
        console.log(data);
        $('.app_id').text("sucess");
    }
    });
  });

</script>
<script>
   $('#page_refresh').click(function() {
    location.reload();
});
</script>
<script>
 /* $('.policelist').click(function(){
    //var applictionno  = $(".policestationlist").data("applictionno"); 
    //alert(applictionno);
    $.ajax({
      type:"GET",
      url:"{{url('/policestationList')}}",
      success:function(data){
        //console.log(data);

        $.each(data, function( index, obj ) {
          $('#ul_assignment').html('');
          var showdata = "";
          for(var i=0;i< data.length;i++){
            showdata+= "<li> <a data-applicationNo="+data[i].id
+" href='{{url('/applicationList/assign')}}?agent="+data[i].id+"&applications='>"+data[i].name+"</a></li><li class='dividerlist'></li>";
            //"+data[i].policestation_id+"
          }
          $('#ul_assignment').append(showdata);
        });
        
       
      },
      error:function(e){
        console.log(e);
      }
    });
  });*/
</script>

<script>
  $("#ddl_application_assignment").click(function(){
    var application_no="";
    $('#tbl_application_list > tbody  > tr').each(function() {
      if($(this).find( 'input[type=checkbox]' ).is(':checked')){        
        //alert($(this).find( 'input[type=checkbox]' ).data("applictionno"));
      application_no=application_no+$(this).find( 'input[type=checkbox]' ).data("applictionno")+"^";  
      }
      
    });
    $('#ul_assignment > li ').each(function() {
      var href_val=$(this).find('a').attr('href');
      $(this).find('a').attr('href',href_val+application_no);
    });
}); 
</script>



  </body>
</html>