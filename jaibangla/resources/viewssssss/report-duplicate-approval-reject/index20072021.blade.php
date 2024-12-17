@extends('report-duplicate-approval-reject.base')
@section('action-content')
<div class="container">
    <!-- <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Report Duplicate Approval</div>
                <div class="panel-body">
                    <form method="POST" action="{{ url('show-report-dup-reject') }}">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6{{ $errors->has('scheme_name') ? ' has-error' : '' }}">
                                <label for="scheme_name">Select Scheme</label><br>
                                <select name="scheme_name" id="scheme_name" required class="form-control select2">
                                    <option value="0">--Select--</option>
                                    @foreach($schemes as $scheme)
                                        <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('scheme_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scheme_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-6{{ $errors->has('dist_name') ? ' has-error' : '' }}">
                                <label for="dist_name">Select District</label><br>
                                <select name="dist_name" id="dist_name" required class="form-control select2">
                                    <option value="0">--Select--</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->district_code }}">{{ $district->district_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('dist_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('dist_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row" align="center" style="margin-top: 10px;">
                            <input type="submit" name="filter" id="filter" value="Submit" class="btn btn-primary">
                            
                        </div>
                    </form>
                </div>  
            </div>
        </div>
    </div> -->

    <div class="" style="padding-right: 30px; ">
        <table id="#example" class="dataTables table table-borderlesstable-hover" cellspacing="0" width="100%" style="background-color: #fff;">
            <thead>
                <tr role="row">
                    <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Beneficiary ID</th>
                    <th width="30%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Name & Father's Name</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Voter ID Card</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Ration Card</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Block/Municipllity</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">GP/Ward</th>
                    <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank Details</th>

                </tr>
            </thead>
            <tbody>
            @php $i=1; @endphp
            @php if($flag == 1) { @endphp    
            @foreach($reports as $report)
                <tr role="row" class="odd">
                    <td class="sorting_1">{{ $report->original_application_id }}</td>
                    <td>
                        <div align="center" class="text-primary"><b>{{ $report->ben_fname }} {{ $report->ben_mname }} {{ $report->ben_lname }} </b></div>
                        <div align="center" style="border: 1px solid #000;padding: 5px;border-radius: 5px; background-color: #fffaeb;"><b>S/O: {{ $report->father_fname }} {{ $report->father_mname }} {{ $report->father_lname }}</b></div>
                    </td>
                    <td>{{ $report->epic_voter_id }}</td>
                    <td>{{ $report->ration_card_cat}} - {{ $report->ration_card_no }}</td>
                    <td>{{ $report->block_ulb_name }}</td>
                    <td>{{ $report->gp_ward_name }}</td>
                    <td>
                        <div align="center" class="text-success"><b>IFSC: {{ $report->bank_ifsc }} </b></div>
                        <div align="center" style="border: 1px solid #000;padding: 5px;border-radius: 5px; background-color: #fffaeb;"><b>Acc No: {{ $report->bank_code }}</b></div>
                    </td>
                </tr>
            @endforeach
            @php } @endphp  
            </tbody>
            <tfoot>
                <tr>
                    <th width="10%" rowspan="1" colspan="1">Beneficiary Id</th>
                    <th width="30%" rowspan="1" colspan="1">Name & Father's Name</th>
                    <th width="10%" rowspan="1" colspan="1">Voter Id Card</th>
                    <th width="10%" rowspan="1" colspan="1">Ration Card</th>
                    <th width="10%" rowspan="1" colspan="1">Block/Municipality</th>
                    <th width="10%" rowspan="1" colspan="1">GP/Ward</th>
                    <th width="20%" rowspan="1" colspan="1">Bank Details</th>
                </tr>
            </tfoot>       
        </table>
        
    </div>
</div>

@endsection


