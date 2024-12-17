@extends('push-ifms.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of Lots</h3>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">     
      
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th class="sorting" tabindex="0" aria-controls="example2" colspan="1" aria-label="scheme: activate to sort column ascending">Lot No</th>

                <th class="sorting" tabindex="0" aria-controls="example2" colspan="1" aria-label="scheme: activate to sort column ascending">Scheme</th>
                
                <th tabindex="0" aria-controls="example2" colspan="2" aria-label="Action: activate to sort column ascending">Sent to IFMS Status</th>

               
                <th tabindex="0" aria-controls="example2" colspan="2" aria-label="Action: activate to sort column ascending">Processed by IFMS Status</th>

                <th tabindex="0" aria-controls="example2" colspan="2" aria-label="Action: activate to sort column ascending">Payment Status</th>
              </tr>
            </thead>
            <tbody>
            @foreach($datas as $result)  
            <tr role="row" class="odd">
              <td>{{$result->lot_no}}</td>
              <td>{{$result->Scheme->scheme_name}}</td>
              <td colspan="2">@if($result->dotdone_status==1) Success @else No Status @endif</td>
              <td colspan="2">
                @if($result->ack_status==1) Success<br/> 
                {{$result->ref_no}}
                @elseif($result->lot_status==0) 
                Failed
                @else 
                No Status 
                @endif</td>
              <td colspan="2">
                @if($result->ack_status==1) 
                Yes 
                @elseif($result->lot_status==0) 
                Failed
                @else No Status @endif
              </td>              
            </tr>
            @endforeach
            
            
            </tbody>
            
          </table>
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