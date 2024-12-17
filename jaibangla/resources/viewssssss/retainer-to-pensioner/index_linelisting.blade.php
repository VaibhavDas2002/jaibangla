<style type="text/css">
  .has-error
  {
    border-color:#cc0000;
    background-color:#ffff99;
  }
  .preloader1{
    position: fixed;
    top:40%;
    left: 52%;
    z-index: 999;
  }
  .preloader1 {
    background: transparent !important;
  }
  #loadingDi {
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

</style>
@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <!-- <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Retainer To Pensioner
      </h1>
      <ol class="breadcrumb">
        <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
      </ol>
    </section>
    <section class="content">
      <div class="box box-default">
        <div class="box-body">
          <div id="loadingDi"></div>
          <div class="panel panel-default">
            <div class="panel-heading" id="panel_head" style="font-size: 14px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
            <div class="panel-body" style="padding: 5px; font-size: 14px;">
              <div id="res_div">
                <div class="table-responsive">
                  <table id="example" class="table display" cellspacing="0" width="100%"> 
                    <thead style="font-size: 12px;">
                      <th>ID</th>
                      <th>Beneficiary Name</th>
                      <th>Father's Name</th>
                      <th>Block/Municipality</th> 
                      <th>Voter Id Card</th>
                      <th>Ration Card</th>
                      <th>Date-of-Birth</th>
                      <th>Action</th>
                    </thead>
                    <tbody style="font-size: 14px;"></tbody>   
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->

  </div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
  $(document).ready(function(){
    // Live Clock
    var interval = setInterval(function () {
    var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);
    $('#res_div').show();
    var table = "";
    loadDatatable();
    
  });

  function loadDatatable() {
    if ( $.fn.DataTable.isDataTable('#example') ) {
      $('#example').DataTable().destroy();
    }
    var table=$('#example').DataTable( {
      dom: 'Blfrtip',
      "scrollX": true,
      "paging": true,
      "searchable": true,
      "ordering":false,
      "bFilter": true,
      "bInfo": true,
      "pageLength":25,
      'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      "serverSide": true,
      "processing":true,
      "bRetrieve": true,
      "oLanguage": {
        "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
      },
      "ajax": 
      {
        url: "{{ url('retainerToPensionerList') }}",
        type: "post",
        data:function(d){
          d._token= "{{csrf_token()}}"
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('#loadingDi').hide();
          $('.preloader1').hide();
          ajax_error(jqXHR, textStatus, errorThrown);
        }
      },
      "initComplete":function(){
        $('#loadingDi').hide();
        //console.log('Data rendered successfully');
      },
      "columns": [
        { "data": "id" },
        { "data": "name" },
        { "data": "father_name" },
        { "data": "block_ulb_name"},
        { "data": "epic_voter_id"},
        { "data": "ration_card"},
        { "data": "date_of_birth" },
        { "data": "view" },
      ],
  
      "buttons": [
        {
           extend: 'pdf',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6],

            }
           },
           {
             extend: 'excel',
             footer: true,
             pageSize:'A4',
             //orientation: 'landscape',
             pageMargins: [ 40, 60, 40, 60 ],
             exportOptions: {
                  columns: [0,1,2,3,4,5,6],
                  stripHtml: false,
              }
            },
        //'pdf','excel','print'
      ],
    });
  }

  function editFunction(value){
    $.confirm({
      type: 'orange',
      title: 'Confirmation!',
      content: 'Are you sure want to transferred this beneficiary to pensioner ?',
      icon: 'fa fa-warning',
      buttons: {
        confirm: {
          text: 'Confirm',
          btnClass: 'btn-blue',
          keys: ['enter', 'shift'],
          action: function(){
            $('#loadingDi').show();
            $.ajax({
              type: 'post',
              url: "{{ route('retainter-to-pensioner-store') }}",
              data: { ben_id:value, _token: '{{ csrf_token() }}' },
              success: function (response) {
                $('#loadingDi').hide();
                // console.log(response);
                if (response.status == 1) {
                  $.alert({
                    title: response.title,
                    type: response.type,
                    icon: response.icon,
                    content: response.msg,
                    buttons: {
                      ok: {
                        action: function(){
                          // loadDatatable();
                          $('#res_div').hide();
                          location.reload();
                        }
                      }
                    }
                  });
                }
                else {
                  $.alert({
                    title: response.title,
                    type: response.type,
                    icon: response.icon,
                    content: response.msg,
                    buttons: {
                      ok: {
                        action: function(){
                          // loadDatatable();
                          $('#res_div').hide();
                          location.reload();
                        }
                      }
                    }
                  });
                }
              },
              complete: function(){
              },
              error: function (jqXHR, textStatus, errorThrown) {
                $('#loadingDi').hide();
                ajax_error(jqXHR, textStatus, errorThrown); 
              }
            });
          }
        },
        cancel: function () {
        },
      }
    });
    
    
  }

  function ajax_error(jqXHR, textStatus, errorThrown){
    var msg = "<strong>Failed to Load data.</strong><br/>";
    if (jqXHR.status !== 422 && jqXHR.status !== 400) {
      msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
    } 
    else {
      if (jqXHR.responseJSON.hasOwnProperty('exception')) {
        msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
      } 
      else {
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
</script>