@extends('document-mgmt.assign.base')
@section('action-content')

<section class="content">
    <div class="box">
      <div class="box-header">
        <div class="row">
          <div class="col-sm-12">
            <h3 class="box-title"> Scheme wise Document List</h3>
          </div>
          <div>


              <!--@if(session('message'))
                  <h4 class="col-sm-6 col-sm-offset-3 alert alert-success" id="id">
                      {{session('message')}}
                  </h4>
             @endif-->
          </div>
          
        </div>
      </div>

        <div class="box-body">
        <form action="{{url('/doc_to_scheme_maping')}}" method="post">
           {{ csrf_field() }}
          <div class="row">
              <div class="form-group{{ $errors->has('scheme_type') ? ' has-error' : '' }} col-sm-12">
                <div class="col-md-4">
                  <label for="scheme_type" class="control-label">Select Documents to Upload</label>
                </div>
                <div class="col-md-8">
                  <select  id="scheme_type" class="form-control select2" name="scheme_type" required>
                    <option value="">--Select Scheme Type--</option>
                    @foreach ($scheme_type as $sch_type)
                    <option value="{{$sch_type->id}}">{{$sch_type->scheme_type}}</option>
                    @endforeach
                  </select>
                    @if ($errors->has('scheme_type'))
                        <span class="help-block">
                            <strong>{{ $errors->first('scheme_type') }}</strong>
                        </span>
                    @endif
                </div>    
              </div>
          </div> 
          <div>
            <hr/>
          </div>
          <div class="row">
            <div class="col-md-4">
              <label for="scheme" class="control-label">  Scheme Name</label>
            </div>
            <div class="col-md-3">
              <label for="doc" class="control-label">Select Documents to Upload (Mandatory)</label>
            </div>
            <div class="col-md-3">
              <label for="doc" class="control-label">Select Documents to Upload (Optional)</label>
            </div>
          </div>
          <div class="form-group{{ $errors->has('doc') ? ' has-error' : '' }} col-sm-12">
         @foreach ($schemes as $scheme)
          <div class="row" >
             
              <div class="col-md-4">
                  {{$scheme->scheme_name}}
              </div>
              <div class="col-md-3">
                <select  id="scheme" class="form-control select2" name="schemelist[]" multiple="multiple"required>
                  
                  @foreach ($docs as $doc)
                  <option value="{{$doc->id}}">{{$doc->doc_name}}</option>
                  @endforeach
                </select>
                  @if ($errors->has('doc'))
                      <span class="help-block">
                          <strong>{{ $errors->first('doc') }}</strong>
                      </span>
                  @endif
              </div>
              <div class="col-md-3">
                <select  id="scheme" class="form-control select2" name="schemelist[]" multiple="multiple"required>
          
                  @foreach ($docs as $doc)
                  <option value="{{$doc->id}}">{{$doc->doc_name}}</option>
                  @endforeach
                </select>
                  @if ($errors->has('doc'))
                      <span class="help-block">
                          <strong>{{ $errors->first('doc') }}</strong>
                      </span>
                  @endif
              </div>
              <div class="col-md-2">
                 <button type="submit" class="btn btn-success"><i class="fa fa-save"> <b>Save</b> </i></button>
              </div>     
            </div>
          @endforeach
  </div>
</section>
@endsection

<script type="text/javascript">
      //$document.ready(function(){
        $("#scheme_type").change(function(){
            alert("Scheme Changed");
        });
      //});
      // $(document).ready(function() {
      //       $("#scheme_type").mouseover(function() { alert($(this).val()); });
      // });


</script>