@extends('employees-mgmt.base')

@section('action-content')

<div> <div>  @foreach ($programmeHeads as $programmeHead)
                                <option value="{{$programmeHead->id}}">{{$programmeHead->name}}</option>
                      @endforeach 
                  </div></div>

@endsection
