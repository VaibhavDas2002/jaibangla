@extends('markds.base')

@section('action-content')
  <style>
    * {
    font-size: 15px;
    }

    .field-name {
    float: left;
    font-weight: 600;
    font-size: 17px;
    margin-right: 3%;
    padding-top: 1%;
    }

    .field-value {


    font-size: 17px;
    padding-top: 1%;

    }

    .row {
    margin-right: 0px !important;
    margin-left: 0px !important;
    }

    .section1 {
    border: 1.5px solid #9187878c;
    overflow: hidden;
    padding-bottom: 10px;


    }

    .color1 {

    background-color: #dcdfdf;
    }

    .color1 h3 {
    margin: 10px 0px 10px 0px !important;
    }

    .setPos {
    padding: 0px 0px 10px 0px;
    margin: 10px 0px 10px 0px;
    border: 1px solid #dcdfdf;
    overflow: hidden;
    }

    .modal_field_name {
    float: left;
    font-weight: 700;
    margin-right: 1%;
    padding-top: 1%;
    margin-top: 1%;
    }

    .modal_field_value {
    margin-right: 1%;
    padding-top: 1%;
    margin-top: 1%;
    }

    .modal-header {
    background-color: #7fffd4;
    }

    .required-field::after {
    content: "*";
    color: red;
    }

    @media print {
    .example-screen {
      display: none;
    }

    * {
      font-size: 15px;
    }

    .field-name {
      float: left;
      font-weight: 600;
      font-size: 17px;
      margin-right: 3%;
      padding-top: 1%;
    }

    .field-value {


      font-size: 17px;
      padding-top: 1%;

    }

    .row {
      margin-right: 0px !important;
      margin-left: 0px !important;
    }

    .section1 {
      border: 1.5px solid #9187878c;
      overflow: hidden;
      padding-bottom: 10px;


    }

    .color1 {

      background-color: #dcdfdf;

    }

    .color1 h3 {
      margin: 10px 0px 10px 0px !important;
    }

    .setPos {
      padding: 0px 0px 10px 0px;
      margin: 10px 0px 10px 0px;
      border: 1px solid #dcdfdf;
      overflow: hidden;
    }

    .modal_field_name {
      float: left;
      font-weight: 700;
      margin-right: 1%;
      padding-top: 1%;
      margin-top: 1%;
    }

    .modal_field_value {
      margin-right: 1%;
      padding-top: 1%;
      margin-top: 1%;
    }

    .modal-header {
      background-color: #7fffd4;
    }

    /*.row{
    margin-right: 0px!important;
    margin-left: 0px!important;
    }
    .section1{
    border: 1.5px solid #9187878c!important;
    margin: 0.25cm!important;
    padding: 0.25cm!important;
    page-break-inside : avoid;
    }
    .color1{
    margin: 0%!important;
    background-color: #5f9ea061!important;
    -webkit-print-color-adjust: exact; 
    }
    .modal_field_name{
    float:left!important;
    font-weight: 700!important;
    margin-right:0.5cm!important;

    }

    .modal_field_value{
    padding-top:0.30cm!important;

    }
    .color1{
    margin: 0%!important;
    background-color: #7fffd4!important;
     -webkit-print-color-adjust: exact; 
    }

    .modal-header{
    background-color: #7fffd4!important;
     -webkit-print-color-adjust: exact; 
    }
    #divToPrint{
    }*/
    }
  </style>
  <section>
    <div class="modal-fade" tabindex="-1" role="document">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
      <div class="example-screen">

      </div>
      <div class="modal-body">
        <a href="markcmolist?type={{$type}}&scheme_id={{$scheme_id}}&grievance_id={{ $grievance_id }}">
        <img width="50px;" style="pull-right" src="{{ asset("images/back.png") }}" alt="Back" /></a>
        <div class="section1">
        <div class="row">
          <div class="col-md-12">
          <h3 style="text-align: center; color:red;">Beneficiary ID:{{$row->id}}

          </h3>
          </div>


        </div>


        @include('pension-details-view.personal_details')
        @include('pension-details-view.personal_identification')
        @include('pension-details-view.contact_details')
        @include('pension-details-view.bank_details')
        @include('pension-details-view.enclosure_list')
        @php

        $visible = 1;
        $btntext = 'Mark as CMO ENTRY';


      @endphp
        @if($already_mark == 0 && (intval($row->is_approved) == 0))
      <div class="row">
        <form method="POST" action="{{route('CMOmarkPost')}}" name="formReject" id="formReject">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" id="scheme_id" name="scheme_id" value="{{$row->scheme_id}}">
        <input type="hidden" id="beneficiary_id" name="beneficiary_id" value="{{$row->id}}" />
        <input type="hidden" id="grievance_id" name="grievance_id" value="{{$grievance_id}}" />
        <input type="hidden" id="type" name="type" value="{{$type}}" />

        <h4 class="modal-title w-100">Do you really want to Mark as CMO Entry for the
        Beneficiary({{ $row->id }})?</h4>
        <button type="button" class="btn btn-success success btn-lg" id="modal-submit"
        style="margin-top:20px;">{{$btntext}}</button>
        <button type="button" id="submitting" value="Submit" class="btn btn-danger btn-lg" disabled
        style="display:none;">Submitting please wait</button>
      </div>


      </form>

      </div>
    @endif

      </div>




      </div>


    </div>


    </div>
  </section>
@endsection
<script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
  $(document).ready(function () {
    $(".NumOnly").keyup(function (event) {

      $(this).val($(this).val().replace(/[^\d].+/, ""));
      if ((event.which < 48 || event.which > 57)) {
        event.preventDefault();
      }
    });

    $('#modal-submit').on('click', function (e) {

      $("#modal-submit").hide();
      $("#submitting").show();
      $("#submit_loader").show();
      $("#formReject").submit();


      //$("#register_form").submit();
    });
  });
</script>
<!-- <script>
function printfunction() {
  // var content=document.getElementById('divToPrint');
  // window.document.write('<html><head><style>.row{ margin-right: 0px!important; margin-left: 0px!important; margin-top: 1%!important;}.section1{border:1.5pxsolid#9187878c;margin:2%;padding:2%;}.color1{margin:0%!important;background-color: #5f9ea061;}.modal_field_name{ float:left;font-weight: 700;margin-right:1%;padding-top:1%;margin-top:1%;}.modal_field_value{margin-right:1%;padding-top:1%;margin-top:1%;}</style></head><body>'+content.innerHTML+'</body></html>');
  window.print();
}
</script> -->