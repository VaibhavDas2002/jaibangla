@extends('Sarasori_Mukhyamantri.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
            <div class="panel panel-default">
               
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="">
                        {{ csrf_field() }} 

                        <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }}">
                            <label for="scheme" class="col-md-4 control-label">Select Scheme</label>

                            <div class="col-md-6">
                                <select onchange="la(this.value)" class="form-control " name="scheme"  id="scheme">
                                    <option value="">--Select--</option>
                                    @foreach ($scheme_list as  $arr)
                                    @if($arr->is_active==1)
                                     <option value="mark-sm?scheme_id={{$arr->id}}">{{$arr->display_name}}</option>
                                     @else
                                      <option value="#" disabled >{{$arr->display_name}}</option>
                                     @endif   
                                    @endforeach
                                   
                                                          
                                </select>
                                <span id="error_construction" class="text-danger"></span>
                            </div>
                        </div>

                        <script>
                            function la(src)
                            {
                                window.location=src;
                            }
                            
                        </script>

                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


