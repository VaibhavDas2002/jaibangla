@extends('queryAdmin.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">{{$report_type_name}}</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="queryexecutionpost" id="usrform">
                        {{ csrf_field() }} 

                        <div class="form-group">
                            <label for="scheme" class="col-md-4 control-label">Query: </label>

                            <div class="col-md-6">
                                
                                <textarea rows="4" cols="60" name="query_user" ></textarea>
                            </div>
                            
                           
                        {{-- <script>	
                            function la(src)
                            {
                                window.location=src;
                            }
                            
                        </script> --}}

                       
        
                        
                </div>
               
                <input type="submit" name="submit" id="submit" class="btn btn-info" value="submit"/> 
            </form>
                    
                </div>
                
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-12" style="overflow: scroll">
            <table
            id="example2"
            class="table table-bordered table-hover dataTable"
            role="grid"
            aria-describedby="example2_info"
        >
            <thead>
                <tr role="row">
                    {{-- Generate table headers dynamically --}}

                    @if(!empty($results) && is_array($results))
                    @foreach($results[0] as $key => $value)
                        <th class="sorting_asc"
                        tabindex="0"
                        aria-controls="example2"
                        rowspan="1"
                        colspan="1">{{ $key }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{-- Generate table rows dynamically --}}
                @foreach($results as $result)
                    <tr>
                        @foreach($result as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach

                @else
                <p>No results found.</p>
                @endif
            </tbody>
        </table>
        </div>
    </div>


 


    

    


</div>
<script>
function display_c(){
    var refresh=1000; // Refresh rate in milli seconds
    mytime=setTimeout('display_ct()',refresh)
}

function display_ct() {
    var x = new Date()
    document.getElementById('ct').innerHTML = x.toUTCString();
    display_c();
} 

$(document).ready(function(){ 
    display_ct();
});
</script>
@endsection



