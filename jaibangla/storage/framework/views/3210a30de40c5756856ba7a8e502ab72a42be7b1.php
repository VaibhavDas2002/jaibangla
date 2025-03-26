<style type="text/css">
    .has-error {
        border-color: #cc0000;
        background-color: #ffff99;
    }

    .preloader1 {
        position: fixed;
        top: 40%;
        left: 52%;
        z-index: 999;
    }

    .preloader1 {
        background: transparent !important;
    }

    #loadingDi {
        position: absolute;
        top: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7); /* Transparent background */
        background-image: url('<?php echo e(asset('images/ajaxgif.gif')); ?>');
        background-repeat: no-repeat;
        background-position: center;
        z-index: 10000000;
        opacity: 1;
    }

    .loadingDivModal {
        position: absolute;
        top: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        background-image: url('<?php echo e(asset('images/ajaxgif.gif')); ?>');
        background-repeat: no-repeat;
        background-position: center;
        z-index: 10000000;
        opacity: 1;
    }

    #updateDiv {
        border: 1px solid #d9d9d9;
        padding: 8px;
        box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
    }

    /* Improved Button Styles */
    .btn-success {
        font-weight: bold;
        background-color: #28a745;
        border-color: #28a745;
    }
    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    /* Table Styling */
    .table th, .table td {
        text-align: center;
    }
    .table th {
        background-color: #f8f9fa;
    }
    .table-striped tbody tr:nth-child(odd) {
        background-color: #f2f2f2;
    }
    .table-bordered {
        border: 1px solid #ddd;
    }
    .table-responsive {
        overflow-x: auto;
    }

</style>


<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Scheme Wise MIS Report</h1>
        <ol class="breadcrumb">
            <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;">
                <span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
        </ol>
    </section>

    <section class="content">
        <div class="box box-default">
            <div class="box-body">
                <!-- Loading Indicator -->
                <div id="loadingDi" style="display: none;"></div>

                <!-- Download Button Section -->
                <div class="col-md-12" style="text-align: right; margin-top: 10px;">
                    <form action="<?php echo e(route('scheme_mis_report_excel')); ?>" method="post">
                        <?php echo e(csrf_field()); ?>

                        <button class="btn btn-success" name="excel_btn" id="excel_btn" type="submit" disabled>
                            <i class="fa fa-file-excel-o"></i> Download List
                        </button>
                    </form>
                </div>

                <!-- Results Section -->
                <div id="res_div" style="display: none;">
                    <div class="panel panel-default">
                        <div class="panel-heading" id="panel_head" style="font-size: 14px; font-weight: bold; font-style: italic;">
                            List of Beneficiary
                        </div>
                        <div class="panel-body" style="padding: 5px; font-size: 14px;">
                            <!-- Table Container -->
                            <div class="table-responsive">
                                <table id="example" class="table table-bordered table-striped display" cellspacing="0" width="100%">
                                    <thead style="font-size: 12px;">
                                        <tr>
                                            <th>Scheme Name</th>
                                            <th>Incomplete Details</th>
                                            <th>No Aadhar Number</th>
                                            <th>Duplicate Aadhar Number</th>
                                            <th>Bank Failure</th>
                                            <th>Duplicate Bank Account Number</th>
                                            <th>No Mobile Number</th>
                                            <th>Duplicate Mobile Number</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px;">
                                        <!-- Data rows will be dynamically populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Results Section -->
            </div>
        </div>
    </section>

</div>
<?php $__env->stopSection(); ?>

<script src="<?php echo e(asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
<script>
    $(document).ready(function () {
        var interval = setInterval(function () {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);

        $('#loadingDi').hide();  // Hide loading indicator initially
       
        $('#submit_btn').removeAttr('disabled');  // Enable submit button
        $('#excel_btn').removeAttr('disabled');  // Enable download button

        loadDatatable();

        function loadDatatable() {
            $('#loadingDi').show();  // Show loading spinner
            $('#res_div').show();  // Show the results section
            $('#panel_head').text('Count of Beneficiaries based on Scheme');

            // Destroy any existing DataTable to refresh it
            if ($.fn.DataTable.isDataTable('#example')) {
                $('#example').DataTable().destroy();
            }

            // Initialize the DataTable with AJAX and configuration options
            $('#example').DataTable({
                dom: 'Blfrtip',
                "scrollX": true,
                "paging": true,
                "searchable": true,
                "ordering": false,
                "bFilter": true,
                "bInfo": true,
                "pageLength": 20,
                'lengthMenu': [
                    [10, 20, 25, 50, 100, -1],
                    [10, 20, 25, 50, 100, 'All']
                ],
                "serverSide": true,
                "processing": true,
                "ajax": {
                    url: "<?php echo e(route('scheme_mis_report_post')); ?>",
                    type: "post",
                    data: function (d) {
                        d._token = "<?php echo e(csrf_token()); ?>";
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('#loadingDi').hide();
                        $.alert({
                            title: 'Error!',
                            type: 'red',
                            icon: 'fa fa-warning',
                            content: 'Loading Error! Session timeout, please logout and login again.'
                        });
                    }
                },
                "initComplete": function () {
                    $('#loadingDi').hide();
                    $('.dt-buttons').addClass('fade');      
                     // Hide the loading spinner after DataTable is initialized
                },
                "columns": [
                    { "data": "scheme_name" },
                    { "data": "incomplete_data" },
                    { "data": "no_aadhar" },
                    { "data": "dup_aadhar" },
                    { "data": "bank_failure" },
                    { "data": "dup_bank" },
                    { "data": "no_mobile" },
                    { "data": "dup_mobile" },
                ]
            });
        }
    });
</script>

<?php echo $__env->make('layouts.app-template-datatable_new', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>