@extends('JBformEntry.base')
@section('main-content')
<section class="content">


    <div class="row">
        <div class="col-md-12">
            <div>
                <div class="box-header with-border">
                    <h3 class="box-title"><b>Government of West Bengal Jai Bangla Pension Scheme ({{$scheme_name}})</b>
                    </h3>
                </div>
            </div>
        </div>
        @if(count($errors) > 0)
            <div class="alert alert-danger alert-block">
                <ul>
                    @foreach($errors as $error)
                        <li><strong> {{ $error }}</strong></li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if ((Session::get('dup_btn_visible')))
        <div style="float:right">
        <form method="get" id="ds_phase_marking" action="{{url('markdslist')}}">
           
            <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}" />
            <input type="hidden" name="type" id="type" value="3" />
            <input type="hidden" name="ds_mark_phase" id="ds_mark_phase"  value="{{$cur_ds_phase_arr->phase_code}}" />
            <input type="submit" class="btn btn-info" value="Mark as {{$cur_ds_phase_arr->phase_des}} Camp"/>
         </form>
        </div>
         <br/>
        @endif

        @if((Session::get('cmo_dup_btn_visible')))
        <div style="float:right">
        <form method="get" id="cmo_marking" action="{{url('markcmolist')}}">
           
            <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}" />
            <input type="hidden" name="type" id="type" value="3" />
            <input type="hidden" name="grievance_id" value="{{ $grievance_id }}" />
            <input type="submit" class="btn btn-info" value="Mark as CMO Entry"/>
         </form>
        </div>
         <br/>
         @endif
        @if (($message = Session::get('success')))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <form method="post" id="register_form" action="{{url('JBEntryForm')}}" enctype="multipart/form-data"
            class="submit-once">
            {{ csrf_field() }}
            <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}" />
            <input type="hidden" name="type" id="type" value="{{$type}}" />
            <input type="hidden" name="app_id" id="app_id"
                value="{{ $type == $op_type && isset($row->id) ? $row->id : '' }}" />
            <input type="hidden" name="op_type" id="op_type" value="{{$op_type}}" />

            @if($type == 3)
                <center>
                    <h2 style="color: green">Application Id:{{$row->getBenidAttribute()}}</h2>
                </center>
                <center>
                    <h3 style="color: #cc0000">Application Status:{{$next_level_status}}</h3>
                </center>
                <center>
                    <h3 style="color: #cc0000">Issue:{{$issue_text}}</h3>
                </center>
            @endif
            <ul class="nav nav-tabs">

                <li class="nav-item">
                    <a class="nav-link active_tab1" style="border:1px solid #ccc" id="list_personal_details"><b>Personal
                            Details</b></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link inactive_tab1" id="list_id_details" style="border:1px solid #ccc"><b>Personal
                            Identification Number(S)</b></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link inactive_tab1" id="list_contact_details" style="border:1px solid #ccc"><b>Contact
                            Details</b></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link inactive_tab1" id="list_bank_details" style="border:1px solid #ccc"><b>Bank
                            Account Details</b></a>
                </li>
                @if ($scheme_id == 13)
                    <li class="nav-item">
                        <a class="nav-link inactive_tab1" id="list_land_details" style="border:1px solid #ccc"><b>Land
                                Details</b></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link inactive_tab1" id="list_fm_details" style="border:1px solid #ccc"><b>Family
                                Members</b></a>
                    </li>
                @endif
                @if ($scheme_id == 17)
                    <li class="nav-item">
                        <a class="nav-link inactive_tab1" id="list_land_details_p" style="border:1px solid #ccc"><b>Land
                                Details (In case of Dwelling House)</b></a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link inactive_tab1" id="list_experience_details"
                        style="border:1px solid #ccc"><b>Enclosure List (Self Attested)</b></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link inactive_tab1" id="list_decl_details" style="border:1px solid #ccc"><b>Self
                            Declaration</b></a>
                </li>
            </ul>
            <div class="tab-content" style="margin-top:16px;">
                @include('JBformEntry.personal')
                @include('JBformEntry.personal_id')
                @include('JBformEntry.contact')
                @include('JBformEntry.bank')
                @if ($scheme_id == 13 || $scheme_id == 17)
                    @if($scheme_id == 13)
                        @include('JBformEntry.land_oap')
                        @include('JBformEntry.family')
                    @elseif ($scheme_id == 17)
                        @include('JBformEntry.land_p')
                    @endif
                @endif
                @include('JBformEntry.enclosure')
                @include('JBformEntry.self_decleration')
                @include('JBformEntry.final_submit')





            </div>
        </form>
    </div>
</section>
<!-- jQuery 2.1.3 -->

<script>
    $(document).ready(function () {
        var scheme_id = $('#scheme_id').val();
        // var error_personal = 0;
        var error_identificacion = 0;
        var error_contact = 0;
        var error_bank = 0;
        var error_land = 0;
        var error_family = 0;
        var error_land_p = 0;
        var error_enclosure = 0;
        var error_sef_dec = 0;
        var add_land = 0;
        var specialKeys = [8]; // Backspace

        function IsNumeric(e) {
            var keyCode = e.which ? e.which : e.keyCode;
            var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) !== -1);
            document.getElementById("error").style.display = ret ? "none" : "inline";
            return ret;
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#passport_image_view').attr('src', e.target.result);
                    $('#passport_image_view_modal').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Document Dynamic
        $("#doc_{{$profile_img}}").change(function () {
            $("#passport_image_view").show();
            readURL(this);
        });

        @if (old('district'))
            var old_districtValue = "{{ old('district') }}";
        @endif
        @if (old('asmb_cons'))
            var old_assemblyValue = "{{ old('asmb_cons') }}";
        @endif
        @if (old('urban_code'))
            var old_urbanValue = "{{ old('urban_code') }}";
        @endif
        @if (old('block'))
            var old_blockValue = "{{ old('block') }}";
        @endif
        @if (old('gp_ward'))
            var old_gpValue = "{{ old('gp_ward') }}";
        @endif

        @if (old('district'))
            var event = new Event('change');
            $("#district").val(old_districtValue).trigger('change');
            $("#asmb_cons").val(old_assemblyValue);
            $("#urban_code").val(old_urbanValue).trigger('change');
            $("#block").val(old_blockValue).trigger('change');
            $("#gp_ward").val(old_gpValue);
        @endif

        $('.txtOnly').keypress(function (e) {
            var regex = /^[a-zA-Z\s]+$/;
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (!regex.test(str)) {
                e.preventDefault();
            }
        });

        $(".NumOnly").keyup(function (event) {
            $(this).val($(this).val().replace(/[^\d]+/, ""));
            if (event.which < 48 || event.which > 57) {
                event.preventDefault();
            }
        });

        $('.special-char').keyup(function () {
            var yourInput = $(this).val();
            var re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
            $(this).val(yourInput.replace(re, ''));
        });

        $(".price-field").keyup(function () {
            var val = $(this).val();
            if (isNaN(val)) {
                val = val.replace(/[^0-9\.]/g, '');
                if (val.split('.').length > 2) {
                    val = val.replace(/\.+$/, "");
                }
            }
            $(this).val(val);
        });

        $('#bank_ifsc_code').blur(function () {
            var $ifsc_data = $.trim($('#bank_ifsc_code').val());
            var $ifscRGEX = /^[a-zA-Z]{4}0[a-zA-Z0-9]{6}$/;
            if ($ifscRGEX.test($ifsc_data)) {
                $('#bank_ifsc_code').removeClass('has-error');
                $('#error_bank_ifsc_code').text('');
                $('#error_name_of_bank').html('<img src="{{ asset('images/ZKZg.gif') }}" width="50px" height="50px"/>');
                $('#error_bank_branch').html('<img src="{{ asset('images/ZKZg.gif') }}" width="50px" height="50px"/>');

                $.ajax({
                    type: 'POST',
                    url: '{{ url('legacy/getBankDetails') }}',
                    data: {
                        ifsc: $ifsc_data,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        if (data == 'null') {
                            alert("The IFSC within the West Bengal only be accepted.");
                            $('#name_of_bank').val('');
                            $('#bank_branch').val('');
                            $('#error_name_of_bank').html('');
                            $('#error_bank_branch').html('');
                        } else {
                            data = JSON.parse(data);
                            $('#name_of_bank').val(data.bank);
                            $('#bank_branch').val(data.branch);
                            $('#error_name_of_bank').html('');
                            $('#error_bank_branch').html('');
                        }
                        // if (!data || data.length === 0) {
                        //     $('#error_bank_ifsc_code').text('No data found with the IFSC');
                        //     $('#bank_ifsc_code').addClass('has-error');
                        //     $('#error_name_of_bank').html('');
                        //     $('#error_bank_branch').html('');
                        //     return;
                        // }
                        // data = JSON.parse(data);
                        // $('#name_of_bank').val(data.bank);
                        // $('#bank_branch').val(data.branch);
                        // $('#error_name_of_bank').html('');
                        // $('#error_bank_branch').html('');
                    },
                    error: function () {
                        $('#error_bank_ifsc_code').text('Data fetch error');
                        $('#bank_ifsc_code').addClass('has-error');
                        $('#error_name_of_bank').html('');
                        $('#error_bank_branch').html('');
                    }
                });
            } else {
                $('#error_bank_ifsc_code').text('IFSC format invalid, please check the code');
                $('#bank_ifsc_code').addClass('has-error');
            }
        });


        if (scheme_id == 13) {
            $('.modal-submit').on('click', function () {

                var table2 = document.getElementById('landListModal');
                var jsonArr2 = [];
                for (var i = 0, row; row = table2.rows[i]; i++) {
                    if (i == 0)
                        continue;
                    var col2 = row.cells;
                    var jsonObj2 = {
                        slno: col2[0].innerHTML,
                        block_name: col2[1].innerHTML,
                        mouza: col2[2].innerHTML,
                        jl_no: col2[3].innerHTML,
                        khatian_no: col2[4].innerHTML,
                        daag_no: col2[5].innerHTML,
                        quantity: col2[6].innerHTML
                    }

                    jsonArr2.push(jsonObj2);
                }

                $('#f_land_array').val(JSON.stringify(jsonArr2));

                var table = document.getElementById('memberListModal');
                var jsonArr = [];
                for (var i = 0, row; row = table.rows[i]; i++) {
                    if (i == 0)
                        continue;
                    var col = row.cells;
                    var jsonObj = {
                        slno: col[0].innerHTML,
                        f_member_name: col[1].innerHTML,
                        f_member_address: col[2].innerHTML,
                        f_member_age: col[3].innerHTML,
                        f_member_profession: col[4].innerHTML,
                        f_member_monthly_income: col[5].innerHTML,
                        f_member_relationship: col[6].innerHTML,
                        f_member_dependent_by_applicant: col[6].innerHTML

                    }

                    jsonArr.push(jsonObj);
                }
                ;
                $('#f_member_array').val(JSON.stringify(jsonArr));


                console.log(JSON.stringify(jsonArr))
                $(".modal-submit").hide();
                $("#submitting").show();
                $("#submit_loader").show();
                //$("#register_form").submit();
            });



        }

    });

</script>
@endsection