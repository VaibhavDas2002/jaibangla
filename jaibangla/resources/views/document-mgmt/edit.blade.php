@extends('document-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update Document Type </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('document-mgmt.update', ['id' => $docs->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">


                         <div class="form-group{{ $errors->has('doc_name') ? ' has-error' : '' }}">
                            <label for="doc_name" class="col-md-4 control-label">Document Name</label>

                            <div class="col-md-6">
                                <input id="doc_name" type="text" class="form-control" name="doc_name" value="{{ $docs->doc_name }}" required autofocus>

                                @if ($errors->has('doc_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('doc_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('doc_type') ? ' has-error' : '' }}">
                            <label for="doc_type" class="col-md-4 control-label">Document Type</label>

                            <div class="col-md-6">
                                <input id="doc_type" type="text" class="form-control" name="doc_type" value="{{ $docs->doc_type }}" required autofocus>

                                @if ($errors->has('doc_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('doc_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('doc_size_kb') ? ' has-error' : '' }}">
                            <label for="doc_size_kb" class="col-md-4 control-label">Document Max Size</label>

                            <div class="col-md-6">
                                <input id="doc_size_kb" type="text" class="form-control" name="doc_size_kb" value="{{ $docs->doc_size_kb }}" required autofocus>

                                @if ($errors->has('doc_size_kb'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('doc_size_kb') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                         <div class="form-group{{ $errors->has('is_active') ? ' has-error' : '' }}">
                            <label for="is_active" class="col-md-4 control-label">Active Status</label>
                            <div class="col-md-6">
                                <input type="checkbox" name="is_active" id="is_active" checked="{{$docs->is_active==1?'checked':''}}"/>  

                                @if ($errors->has('is_active'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('is_active') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('doucument_group') ? ' has-error' : '' }}">
                            <label for="doucument_group" class="col-md-4 control-label">Document Group</label>

                            <div class="col-md-6">
                            <select name="doucument_group" id="doucument_group" class="form-control" tabindex="4">
                                <option value="">--NA  --</option>
                                @foreach(Config::get('constants.document_group') as $key=>$val)
                                <option value="{{$key}}" {{$docs->doucument_group==$key?'selected':''}} >{{$val}}</option>
                                @endforeach      
                            </select>

                                @if ($errors->has('doucument_group'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('doucument_group') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
