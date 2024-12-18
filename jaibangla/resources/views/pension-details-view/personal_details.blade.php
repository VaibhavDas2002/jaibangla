<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Personal Details</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div><strong>Name :</strong> {{$row->ben_fname}} {{$row->ben_mname}}
                    {{$row->ben_lname}}
                </div>
            </div>
            <div class="col-md-6">
                <div><strong>Gender:</strong> {{$row->gender}}</div>
            </div>
            @if(!is_null($row->dob))
                <div class="col-md-6">
                    <div><strong>Date of Birth (DD-MM-YYYY):</strong>
                        {{date('d/m/Y', strtotime($row->dob)) }}</div>

                </div>
            @endif
            <div class="col-md-6">
                <div><strong>Father's Name :</strong> {{$row->father_fname}}
                    {{$row->father_mname}}
                    {{$row->father_lname}}
                </div>
            </div>

            <div class="col-md-6">
                <div><strong>Mother's Name :</strong> {{$row->mother_fname}}
                    {{$row->mother_mname}}
                    {{$row->mother_lname}}
                </div>
            </div>
            <div class="col-md-6">
                <div><strong>Caste:</strong> {{$row->caste}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Marital Status:</strong> {{$row->marital_status}}</div>
            </div>
            @if ($scheme_id == 11)
                <div class="col-md-6">
                    <div><strong>Husband's Name :</strong> {{$row->husband_fname}}
                        {{$row->husband_mname}}
                        {{$row->husband_lname}}
                    </div>
                </div>
            @endif

            <div class="col-md-6">
                <div><strong>Spouse Name :</strong> {{$row->spouse_fname}}
                    {{$row->spouse_mname}}
                    {{$row->spouse_lname}}
                </div>
            </div>

            <div class="col-md-6">
                <div><strong>Monthly Family Income(Rs.):</strong>
                    {{$row->mothly_income}}
                </div>
            </div>
        </div>

        @yield('personal-add')
        
    </div>
</div>