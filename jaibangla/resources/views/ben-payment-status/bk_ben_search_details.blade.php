@extends('ben-payment-status.base')
@section('action-content')
<div class="container">
    
    <div class="" style="padding-right: 30px; ">
        <table id="example" class="dataTables table table-bordered table-hover" cellspacing="0" width="100%" style="background-color: #fff; ">
            <thead>
                <tr role="row">
                    <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">ID</th>
                    <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Beneficiary Name</th>
                    <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Father Name</th>
                    <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Block/ULB</th> 
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Voter ID Card</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Ration Card</th>
                    <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank Details</th>
                    <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>
                    <!-- <th width="7%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Status</th> -->
                </tr>
            </thead>
            @foreach($results as $result)
                <tr role="row" class="odd">
                    <td>{{ $result->id }}</td>
                    <td>{{ $result->ben_fname }} {{ $result->ben_mname }} {{ $result->ben_lname }}</td>
                    <td>{{ $result->father_fname }} {{ $result->father_mname }} {{ $result->father_lname }}</td>
                    <td>{{ $result->block_ulb_name }}</td>
                    <td>{{ $result->epic_voter_id }}</td>
                    <td>{{ $result->ration_card_cat}} - {{ $result->ration_card_no }}</td>
                    <td>
                        <div align="center" class="text-success"><b>IFSC: {{ $result->bank_ifsc }} </b></div>
                        <div align="center" style="border: 1px solid #000;padding: 5px;border-radius: 5px; background-color: #fffaeb;"><b>Acc No: {{ $result->bank_code }}</b></div>
                    </td>
                    <td>
                        <!-- @if(Auth::user()->designation_id_old == 'Verifier') -->
                        <form method="POST" action="{{ url('view-status/'.$result->id) }}" onsubmit="return confirm('Are you sure?');" name="myForm">
                            {{ csrf_field() }}
                            <button class="btn btn-warning">
                                Status
                            </button>
                        </form>
                        <!-- @endif   -->
                    </td>
                </tr>
            @endforeach    
            </tbody>
            <tfoot>
                <tr>
                    <th width="5%" rowspan="1" colspan="1">ID</th>
                    <th width="15%" rowspan="1" colspan="1">Beneficiary Name</th>
                    <th width="15%" rowspan="1" colspan="1">Father Name</th>
                    <th width="10%" rowspan="1" colspan="1">Block/ULB</th>
                    <th width="10%" rowspan="1" colspan="1">Voter Id Card</th>
                    <th width="10%" rowspan="1" colspan="1">Ration Card</th>
                    <th width="20%" rowspan="1" colspan="1">Bank Details</th>
                    <th width="15%" rowspan="1" colspan="1">Action</th>
                    <!-- <th width="7%" rowspan="1" colspan="1">Status</th> -->
                </tr>
            </tfoot>       
        </table>
        <a href="{{ route('update-ben-details') }}"><input type="submit" name="back" value="Back To Search" class="btn btn-success"></a>    
    </div>
</div>

@endsection