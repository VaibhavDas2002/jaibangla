<style>
    .row {
        margin-right: 0px !important;
        margin-left: 0px !important;
        margin-top: 1% !important;
    }

    .applnlbl {
        color: #006600;
        font-size: 20px;

    }

    .select2 {
        width: 400px !important;
    }
</style>
@extends('layouts.app-template')
@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <h1>Scheme General Setting</h1>
        <ol class="breadcrumb">
            <i class="fa fa-clock-o"></i> Date:
            <span style="font-size: 12px; font-weight: bold;">
                <span class="date-part"></span>&nbsp;&nbsp;<span class="time-part"></span>
            </span>
        </ol>
    </section>

    <!-- Main Content -->
    <section class="content">
        <div id="loadingDiv"></div>
        <form method="POST" action="{{ route('scheme_general_setting_store') }}" id="schemeGenralSettingForm"
            role="form">
            {{ csrf_field() }}
            <div class="row ">
                <div class="col-md-8 justify-content-center">
                    <div class="panel panel-default">
                        <div class="panel-heading text-center"
                            style="font-size: 18px; font-weight: bold; padding: 10px;">
                            <i class="glyphicon glyphicon-cog"></i> General Setting Based On Scheme
                        </div>
                        <div class="panel-body" style="padding: 20px;">
                            @if (Session::has('success'))
                                <div class="alert alert-success alert-dismissible  show" role="alert">
                                    <strong>{{ Session::get('success') }}</strong>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (Session::has('error'))
                                <div class="alert alert-danger alert-dismissible show" role="alert">
                                    <strong>{{ Session::get('error') }}</strong>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif


                            <div class="form-group">
                                <label for="scheme_id" class="control-label">Scheme <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" name="scheme_id" id="scheme_id" required>
                                    <option value="">-- Select Scheme --</option>
                                    @foreach ($schemes as $scheme)
                                        <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="error_scheme_id"></span>
                            </div>

                            <div class="form-group text-center">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#generalSchemeModal" id="create">
                                    <i class="glyphicon glyphicon-plus"></i> Create
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="generalSchemeModal" tabindex="-1" role="dialog"
                aria-labelledby="generalSchemeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="generalSchemeModalLabel">Scheme General Settings</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- General Entry -->
                            <div class="box box-primary box-solid mb-3">
                                <div class="box-header with-border">
                                    <h3 class="box-title">General Entry</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="entry">Allow Entry</label>
                                                <div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="entry" value="1" id="entry_yes"> Yes
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="entry" value="0" id="entry_no"> No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="verify">Allow Verification</label>
                                                <div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="verify" value="1" id="verify_yes"> Yes
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="verify" value="0" id="verify_no"> No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="approve">Allow Approve</label>
                                                <div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="approve" value="1" id="approve_yes">
                                                        Yes
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="approve" value="0" id="approve_no"> No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Duare Sarkar Settings -->
                            <div class="box box-primary box-solid mb-3">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Duare Sarkar Settings</h3>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="ds_entry">
                                            Allow Duare Sarkar Entry
                                            <input type="checkbox" id="ds_entry" name="ds_entry" value="1">
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="ds_phase" class="control-label">Duare Sarkar Phase</label>
                                        <select class="form-control select2" name="ds_phase[]" multiple="multiple"
                                            id="ds_phase">
                                            @foreach ($ds_phases as $ds_phase)
                                                <option value="{{ $ds_phase->phase_code }}">{{ $ds_phase->phase_des }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="normal_entry">
                                            Allow Normal Entry
                                            <input type="checkbox" id="normal_entry" name="normal_entry" value="1">
                                        </label>
                                    </div>
                                </div>
                            </div>

                                   <!-- CMO Settings -->
                        <div class="box box-primary box-solid mb-3">
                            <div class="box-header with-border">
                                <h3 class="box-title">CMO Grivance</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="ds_entry">Allow CMO Grievance Check
                                        <div>
                                            <label class="radio-inline">
                                                <input type="radio" name="cmo_check" value="1" id="entry_yes"> Yes
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="cmo_check" value="0" id="entry_no"> No
                                            </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                            <!-- Scheme Capacity -->
                            <div class="box box-primary box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Scheme Capacity</h3>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="scheme_cap" name="scheme_cap" value="1">
                                            Enable Scheme Capacity
                                        </label>
                                    </div>
                                    <div class="form-group special-quota">
                                        <label>Special Quota Enable</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="special_quota" value="1" id="quota_yes"> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="special_quota" value="0" id="quota_no"> No
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" form="schemeGenralSettingForm" class="btn btn-primary">
                                <i class="glyphicon glyphicon-ok"></i> Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Table Section -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Scheme General Settings</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Scheme Name</th>
                                        <th>Allow Entry</th>
                                        <th>Allow Verification</th>
                                        <th>Allow Approve</th>
                                        <th>Duare Sarkar Entry</th>
                                        <th>Scheme Capacity</th>
                                        <th>Allow CMO</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be populated via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h3 class="modal-title" id="updateModalLabel">Update Scheme Settings - <span id="scheme_name"
                            class="text-green"></span></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <form id="updateForm" action="{{ route('scheme_general_setting_update') }}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="scheme_id" id="scheme_id">

                        <!-- General Entry -->
                        <div class="box box-primary box-solid mb-3">
                            <div class="box-header with-border">
                                <h3 class="box-title">General Entry</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="entry">Allow Entry</label>
                                            <div>
                                                <label class="radio-inline">
                                                    <input type="radio" name="entry" value="1" id="entry_yes"> Yes
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="entry" value="0" id="entry_no"> No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="verify">Allow Verification</label>
                                            <div>
                                                <label class="radio-inline">
                                                    <input type="radio" name="verify" value="1" id="verify_yes"> Yes
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="verify" value="0" id="verify_no"> No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="approve">Allow Approve</label>
                                            <div>
                                                <label class="radio-inline">
                                                    <input type="radio" name="approve" value="1" id="approve_yes"> Yes
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="approve" value="0" id="approve_no"> No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Duare Sarkar Settings -->
                        <div class="box box-primary box-solid mb-3">
                            <div class="box-header with-border">
                                <h3 class="box-title">Duare Sarkar Settings</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="ds_entry">Allow Duare Sarkar Entry
                                        <input type="checkbox" id="ds_entry" name="ds_entry" value="1">
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="ds_phase" class="control-label">Duare Sarkar Phase</label>
                                    <select class="form-control select2" name="ds_phase[]" multiple="multiple"
                                        id="ds_phase">
                                        @foreach ($ds_phases as $ds_phase)
                                            <option value="{{ $ds_phase->phase_code }}">{{ $ds_phase->phase_des }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="normal_entry">Allow Normal Entry
                                        <input type="checkbox" id="normal_entry" name="normal_entry" value="1">
                                    </label>
                                </div>
                            </div>
                        </div>


                        <!-- CMO Settings -->
                        <div class="box box-primary box-solid mb-3">
                            <div class="box-header with-border">
                                <h3 class="box-title">CMO Grivance</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="ds_entry">Allow CMO Grievance Check
                                        <div>
                                            <label class="radio-inline">
                                                <input type="radio" name="cmo_check" value="1" id="entry_yes"> Yes
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="cmo_check" value="0" id="entry_no"> No
                                            </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Scheme Capacity -->
                        <div class="box box-primary box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title">Scheme Capacity</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="scheme_cap" name="scheme_cap" value="1"> Enable
                                        Scheme Capacity
                                    </label>
                                </div>
                                <div class="form-group special-quota">
                                    <label>Special Quota Enable</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="special_quota" value="1" id="quota_yes"> Yes
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="special_quota" value="0" id="quota_no"> No
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="updateForm" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        setInterval(function () {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);

        $('#loadingDiv').hide();

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            lengthChange: false,
            pageLength: 10,
            dom: 'lrtip',

            responsive: true,
            ajax: {
                url: "{{ route('scheme_general_setting_data') }}",
                type: "GET",
                dataSrc: function (json) {
                    return json.data;
                }
            },
            columns: [
                { data: 'scheme_name', name: 'scheme_name' },
                {
                    data: 'allow_entry', name: 'allow_entry', render: function (data) {
                        return data ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Deactive</span>';
                    }
                },
                {
                    data: 'allow_verify', name: 'allow_verify', render: function (data) {
                        return data ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Deactive</span>';
                    }
                },
                {
                    data: 'allow_approve', name: 'allow_approve', render: function (data) {
                        return data ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Deactive</span>';
                    }
                },
                {
                    data: 'allow_ds_entry', name: 'allow_ds_entry', render: function (data) {
                        return data ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Deactive</span>';
                    }
                },
                {
                    data: 'cap_exists', name: 'cap_exists', render: function (data) {
                        return data ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Deactive</span>';
                    }
                },
                {
                    data: 'allow_cmo', name: 'allow_cmo', render: function (data) {
                        return data ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Deactive</span>';
                    }
                },
                {
                    data: 'actions',
                    name: 'actions',
                    render: function (data, type, row) {
                        return '<button type="button" id="update_btn" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#updateModal" data-scheme-id="' + row.scheme_id + '">Update</button>';
                    }
                }
            ],
            paging: false,
            pageLength: 10,
            lengthMenu: [5, 10, 20],
            ordering: false
        });






        $('#scheme_id').change(function () {

            if ($(this).val() != '') {
                $('#create').prop('disabled', false);
            } else {
                $('#create').prop('disabled', true);
            }
        });

        if ($('#scheme_id').val() != '') {
            $('#create').prop('disabled', false);
        } else {
            $('#create').prop('disabled', true);
        }
        toggleDsPhase();
        $('#ds_entry').change(function () {
            toggleDsPhase();
        });


        function toggleDsPhase() {
            if ($('#ds_entry').prop('checked')) {
                $('#ds_phase').prop('disabled', false);
            } else {

                $('#ds_phase').prop('disabled', true);
            }
        }


        $('#scheme_cap').change(function () {
            if ($(this).prop('checked')) {
                $('.special-quota').show();
            } else {
                $('.special-quota').hide();
            }
        });


        if ($('#scheme_cap').prop('checked')) {
            $('.special-quota').show();
        } else {
            $('.special-quota').hide();
        }


        // Toggle visibility of special-quota section based on 'scheme_cap' checkbox
        $('#updateModal #scheme_cap').change(function () {
            if ($(this).prop('checked')) {
                $('.special-quota').show();
            } else {
                $('.special-quota').hide();
            }
        });

        // Initially set the visibility of special-quota when the modal is loaded
        if ($('#updateModal #scheme_cap').prop('checked')) {
            $('.special-quota').show();
        } else {
            $('.special-quota').hide();
        }

        // Handle the click event to open the modal and load the scheme details via AJAX
        $(document).on('click', '#update_btn', function () {
            // Get the scheme ID from the data attribute
            let schemeId = $(this).data('scheme-id');
            if (!schemeId) {
                console.error('Scheme ID is missing.');
                alert('Unable to identify the scheme. Please try again.');
                return;
            }

            // Set the scheme ID in the modal
            $('#updateModal #scheme_id').val(schemeId);

            // Make the AJAX call
            $.ajax({
                url: '/get-scheme-details/' + schemeId, // Ensure the route matches your Laravel route
                method: 'GET',
                success: function (data) {
                    // Check if the data contains expected properties
                    if (!data || data.error) {
                        alert(data.error || 'Unexpected response from the server.');
                        return;
                    }

                    // Populate the modal with received data
                    $('#updateModal #scheme_name').text(data.scheme_name || '');
                    $('input[name="entry"][value="' + (data.entry ? 1 : 0) + '"]').prop('checked', true);
                    $('input[name="verify"][value="' + (data.verify ? 1 : 0) + '"]').prop('checked', true);
                    $('input[name="approve"][value="' + (data.approve ? 1 : 0) + '"]').prop('checked', true);
                    $('#updateModal #ds_entry').prop('checked', data.ds_entry ? true : false);
                    $('#updateModal #normal_entry').prop('checked', data.normal_entry ? true : false);
                    $('#updateModal #scheme_cap').prop('checked', data.capacity ? true : false);
                    $('input[name="cmo_check"][value="' + (data.allow_cmo ? 1 : 0) + '"]').prop('checked', true);
                    // $('#updateModal #allow_cmo').prop('checked', data.allow_cmo ? true : false);

                    // Handle multi-select for ds_phase
                    if (data.ds_phase && Array.isArray(data.ds_phase)) {
                        $('#updateModal #ds_phase').val(data.ds_phase).trigger('change');
                    } else {
                        $('#updateModal #ds_phase').val([]).trigger('change');
                    }

                    // Show/hide special quota based on capacity
                    if (data.capacity) {
                        $('.special-quota').show();
                        $('input[name="special_quota"][value="' + (data.special_quota ? 1 : 0) + '"]').prop('checked', true);
                    } else {
                        $('.special-quota').hide();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Failed to fetch scheme details. Please try again.');
                }
            });
        });


    });
</script>