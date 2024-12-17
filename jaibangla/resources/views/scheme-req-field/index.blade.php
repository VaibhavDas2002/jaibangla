@extends('layouts.app-template')
@section('content')
<div class="content-wrapper d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <!-- Content Header -->
    <section class="content-header text-center">
        <h1>Scheme-Based Required Fields Settings</h1>
        <ol class="breadcrumb text-center">
            <li><i class="fa fa-clock-o"></i> Date:
                <span style="font-size: 12px; font-weight: bold;">
                    <span class="date-part"></span>&nbsp;&nbsp;
                    <span class="time-part"></span>
                </span>
            </li>
        </ol>
    </section>

    <!-- Main Content -->
    <section class="content">
        @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible show" role="alert">
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

        <div id="loadingDiv" class="text-center">
            <p>Loading, please wait...</p>
        </div>

        <!-- Form Section -->
        <form method="POST" action="{{ route('scheme-req-field.store') }}" id="schemeReqFieldForm" role="form">
            {{ csrf_field() }}
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title text-center">Settings Form</h3>
                        </div>
                        <div class="box-body">
                            <!-- Scheme Dropdown -->
                            <div class="form-group">
                                <label for="scheme" class="form-label required-field">Scheme</label>
                                <select class="form-control select2" name="scheme_id" id="scheme">
                                    <option value="">Select a Scheme</option>
                                    @foreach ($schemes as $scheme)
                                        <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Normal Fields -->
                            <div class="form-group">
                                <label for="normalFields" class="form-label">Normal Fields</label>
                                <select class="form-control select2" name="normal_fields[]" id="normalFields" multiple>
                                    @foreach ($normal_fields as $normal_field)
                                        <option value="{{ $normal_field->id }}">{{ $normal_field->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Document Fields -->
                            <div class="form-group">
                                <label for="docFields" class="form-label">Document Fields</label>
                                <select class="form-control select2" name="doc_fields[]" id="docFields" multiple>
                                    @foreach ($doc_fields as $doc_field)
                                        <option value="{{ $doc_field->id }}">{{ $doc_field->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="box-footer text-center">
                            <button type="submit" class="btn btn-info">Submit</button>
                            <button type="reset" class="btn btn-default">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- DataTable Section -->
        <div class="datatable">
            <table id="scheme-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sl. No.</th>
                        <th>Scheme Name</th>
                        <th>Required Fields</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
</div>
@endsection

<!-- Scripts -->
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2();

        // Update Date and Time
        setInterval(function () {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);

        // Initialize DataTable
        $('#scheme-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('get-scheme-data-required') }}",
            columns: [
                { data: "sl_no" },
                { data: "scheme_name" },
                { data: "required_fields" },
                { data: "action", orderable: false, searchable: false }
            ],
            order: [[0, 'asc']],
        });

        // Hide Loading Div
        $('#loadingDiv').hide();
    });
</script>
@endpush
