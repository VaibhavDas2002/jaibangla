@extends('payment-reprocess.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of SMS</h3>
        </div>
        
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <!--form method="POST" action="{{ route('smsTemplate.search') }}">

        {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Search'])
          @component('layouts.two-cols-search-row', ['items' => ['Application_id'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['reason'] : '']])
          @endcomponent
        @endcomponent
         
      </form-->
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
               <tr>
                 <th width="13%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Application Id</th>
                <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Applicant's Name </th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Address</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Application Date & Time</th>
                <th width="14%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Status</th>
                <th width="18%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Process Payment</th>
              </tr>
            </thead>
            <tbody>

              @foreach($repayments as $repayment)

                <tr role="row" class="odd">
                  <td class="sorting_1">{{$repayment->application_no}}</td>
                  <td class="sorting_1">{{$repayment->first_name}} {{$repayment->middle_name}} {{$repayment->last_name}}</td>
                  <td class="sorting_1">{{$repayment->present_address_line1}}{{$repayment->present_address_line2}}{{$repayment->present_address_landmark}}</td>
                  <td class="sorting_1">{{ date('d-m-Y H:i:s', strtotime($repayment->application_datetime))}}</td>

                  <td class="sorting_1">Pending Mode</td>
                  
                  <td>
                        <a  class="btn btn-warning  btn-margin reProceePayment" data-id="{{$repayment->application_no}}">
                        Process Payment
                        </a>
                        <div class="app_id"></div>
                    </form>
                  </td>
              </tr>
              @endforeach
             
           
            </tbody>
            <tfoot>
              <tr>
                 <th width="13%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Application Id</th>
                <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Applicant's Name </th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Address</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Application Date & Time</th>
                <th width="14%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Status</th>
                <th width="18%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">ReProcess Payment</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($repayments)}} of {{count($repayments)}}  entries</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
             {{ $repayments->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
@endsection