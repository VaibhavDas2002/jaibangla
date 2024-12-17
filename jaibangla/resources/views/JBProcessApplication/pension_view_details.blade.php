@extends('JBProcessApplication.view_template')
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
    }
</style>

<section>
    <div class="modal-fade" tabindex="-1" role="document">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="example-screen">
                    <h2 class="modal-title " style="text-align: center;">View Applicant Details</h2>
                </div>
                <div class="modal-body">
                    <div class="section1">
                        @if(count($is_dup_msg) > 0)
                            <div class="alert alert-danger alert-block">
                                <ul>
                                    @foreach($is_dup_msg as $msg)
                                        <li><strong> {{ $msg }}</strong></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                        @include('JBProcessApplication.pension_view_details_common')



                        @yield('form_section')
                        <!-- buttons -->

                       

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#submitting").hide();
        $("#action_type").val('');
        $("#action_txt").text('');
        $("#id_txt").text('');
        $('.btn-action').click(function () {
            $("#submit").val('');
            $("#action_type").val('');
            $("#action_type").val($(this).val());
            //alert($("#action_type").val());
            $("#action_txt").text($(this).val());
            $("#id_txt").text($("#ben_id").val());
            $("#modal-submit").val($(this).val());
            $('#myModal').modal('show');
        });
        $('#modal-submit').on('click', function () {
            var action_type = $("#action_type").val();
            $("#modal-submit").hide();
            $("#submitting").show();
            $("#submit_loader").show();
            $("#form1").submit();



        });
        $(".NumOnly").keyup(function (event) {

            $(this).val($(this).val().replace(/[^\d].+/, ""));
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });
    });

    $('.txtOnly').keypress(function (e) {
        var regex = new RegExp(/^[a-zA-Z\s]+$/);
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        else {
            e.preventDefault();
            return false;
        }
    });
</script>
@endsection