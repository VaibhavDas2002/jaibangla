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

                
                
                <th tabindex="0" aria-controls="example2" colspan="4" aria-label="Action: activate to sort column ascending">Lot Master Status</th>

               <th tabindex="0" aria-controls="example2" colspan="1" aria-label="Action: activate to sort column ascending">Status</th>
                


              </tr>
            </thead>
            <tbody>
            @foreach($datas as $result)  
            <tr role="row">
              <td>{{$result->lot_no}}-{{$result->lot_month}}( {{$result->Scheme->scheme_name}})-Count({{$result->ben_count}})</td>              
              <td colspan="4">F:{{$result->file_name}}&nbsp;L_S:{{$result->lot_status}}&nbsp;Ack:{{$result->ack_status}}&nbsp;Ref:{{$result->ref_no}}</td>

              <td>
                @if($result->ref_no) 
                 <span class="label label-success">Paid</span>
                @elseif(($result->ack_status == 1 )&& ($result->ref_no ==null)) 
                <span class="label label-danger">Failed</span>
                @elseif(($result->ack_status == null )&& ($result->file_name != null)) 
                <span class="label label-warning">Pending</span>
                @elseif(($result->lot_status == 1 )) 
                <span class="label label-primary">Not Known Open For Payment</span>
                @else
                <span class="label label-primary">Not Known</span>
                @endif
               
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