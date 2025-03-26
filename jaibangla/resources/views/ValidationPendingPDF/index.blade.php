@extends('employees-mgmt.base_pension')

@section('action-content')
    <!-- Main content -->
    <section class="content">
        <div class="box">
            <div class="box-header">
                <div class="row">
                    <div class="col-sm-8">
                        <h3 class="box-title">Application List</h3>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <button class="btn btn-default" id="attendence_sheet">
                    <i class="fa fa-print"></i> Print Attendance Sheet
                </button>
            </div>
            <!-- /.box-body -->
        </div>
    </section>
    <!-- /.content -->
@endsection
<script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ URL::asset('js/legacy_attendance_sheet.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>

<script>
    $(document).ready(function () {
        $('#attendence_sheet').click(function () {
            var data = [
                {
                    disrict: 'Birbhum',
                    blk_ulb_name: 'SURI',
                    on_date: '25-03-2025',
                    on_time: '12:00 AM',
                    ben_name: 'Vaibhav Das',
                    ben_id: '12542',
                    bank_details: 'Bank Response Name: Vaibhav Das\nBank Name: XYZ Bank\nAcc No: 987654321\nBranch: ABC Branch\nIFSC: SBIN0003242',
                    bank_code: '12545442158',
                    bank_ifsc: 'UNDIN7891054',
                    bank_name: 'UNION BANK OF INDIA',
                    branch_name: 'SURI - BIRBHUM',
                    bank_response: 'VAIBHAV DAS'
                },
                {
                    disrict: 'Birbhum',
                    blk_ulb_name: 'SURI',
                    on_date: '25-03-2025',
                    on_time: '12:00 AM',
                    ben_name: 'Rahul Sen',
                    ben_id: '12543',
                    bank_details: 'Bank Response Name: Rahul Sen\nBank Name: ABC Bank\nAcc No: 123456789\nBranch: DEF Branch\nIFSC: HDFC0001234',
                    bank_code: '12545442158',
                    bank_ifsc: 'UNDIN7891054',
                    bank_name: 'UNION BANK OF INDIA',
                    branch_name: 'SURI - BIRBHUM',
                    bank_response: 'VAIBHAV DAS'
                }
            ];


            let date = new Date().toLocaleDateString();
            let time = new Date().toLocaleTimeString();



            legacy_attendance_sheet(data)
        });
    });
</script>