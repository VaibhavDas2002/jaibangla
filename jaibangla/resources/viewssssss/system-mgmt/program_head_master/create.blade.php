@extends('system-mgmt.program_head_master.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new program head master</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('program_head_master.store') }}">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label class="col-md-4 control-label">Major Program Head Name</label>
                            <div class="col-md-6">
                                <select class="form-control" name="major_programme_head_id" required>
                                    @foreach ($major_head_names as $major_head_name)
                                        <option value="{{$major_head_name->id}}">{{$major_head_name->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Service Category Name</label>
                            <div class="col-md-6">
                                <select class="form-control" name="service_category_id" required>
                                    @foreach ($service_categorys as $service_category)
                                        <option value="{{$service_category->id}}">{{$service_category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Program Head Master Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Create
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

<script type="text/javascript">
    $(document).ready(function() {
        $('select[name="major_programme_head_id"]').on('change', function() {
            var major_programme_head_id = $(this).val();
            if(major_programme_head_id) {
                $.ajax({
                    url: '/myform/ajax/'+major_programme_head_id,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {

                        
                        $('select[name="service_category_id"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="service_category_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                        });
                    }
                });
            }else{
                $('select[name="service_category_id"]').empty();
            }
        });
    });
</script>
