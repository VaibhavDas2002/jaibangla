@extends('JBProcessApplication.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="">
                        {{csrf_field()}}

                        <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }}">
                            <label for="scheme" class="col-md-4 control-label">Select Scheme</label>

                            <div class="col-md-6">
                                <select onchange="navigateToScheme(this.value)" class="form-control" name="scheme" id="scheme">
                                    <option value="">--Select--</option>
                                    @foreach ($return_arr as $arr)
                                        <option value="{{ $arr->url }}?scheme_id={{ $arr->id }}&type={{ $type }}">
                                            {{ $arr->display_name }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('scheme'))
                                    <span class="help-block text-danger">
                                        {{ $errors->first('scheme') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <script>
                            // Function to redirect to the selected scheme's URL
                            function navigateToScheme(url) {
                                if (url) {
                                    window.location.href = url;
                                }
                            }
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
