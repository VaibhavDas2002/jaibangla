@extends('document-mgmt.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of Documents</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('document-mgmt.create') }}">Add New Document</a>
        </div>

        <br/>
        <div class="clearfix"></div>
         <div class="col-sm-12">@if(session()->has('message'))
              <div class="alert alert-success">
                  {{ session()->get('message') }}
              </div>
          @endif
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="docs: activate to sort column ascending">Document Name</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="docs: activate to sort column ascending">Document Type</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="docs: activate to sort column ascending">Document Size(KB)</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="docs: activate to sort column ascending">Active Status</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
              </tr>
            </thead>
            <tbody>
           
            @foreach ($docs as $doc)
                <tr role="row" class="odd">

                  <td>{{$doc->doc_name }}</td>
                  <td>{{$doc->doc_type }}</td>
                  <td>{{$doc->doc_size_kb }}</td>
                  <td>{{$doc->is_active?'True':'False' }}</td>
                  <td>
                    <form class="row" method="POST" action="{{ route('document-mgmt.destroy', ['id' =>$doc->id]) }}" onsubmit = "return confirm('Are you sure?')">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('document-mgmt.edit', ['id' => $doc->id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
                        Update
                        </a>
                        <button type="submit" class="btn btn-danger col-sm-3 col-xs-5 btn-margin">
                          Delete
                        </button>
                    </form>
                  </td>
                
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th width="20%" rowspan="1" colspan="1">Document Name</th>
                <th width="20%" rowspan="1" colspan="1">Document Type</th>
                <th width="20%" rowspan="1" colspan="1">Document Size(KB)</th>
                 <th width="20%" rowspan="1" colspan="1">Active Status</th>
                <th rowspan="1" colspan="2">Action</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($docs)}} of {{count($docs)}} entries</div>
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
