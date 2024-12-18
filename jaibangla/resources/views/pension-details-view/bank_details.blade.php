<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title">Bank Details</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div><strong>Bank Name:</strong> {{$row->bank_name}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Bank Branch Name:</strong> {{$row->branch_name}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Bank Account No.:</strong> {{$row->bank_code}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>IFS Code:</strong>{{$row->bank_ifsc}}</div>
            </div>
        </div>

        @yield('bank-add')
    </div>
</div>