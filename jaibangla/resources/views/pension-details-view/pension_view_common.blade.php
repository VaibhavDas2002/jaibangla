<input id="designation_id" type="hidden" value="{{$designation_id}}">
@extends('pension-details-view.pension_view_details')
@if ($is_verifier && $view_type == 1)
    @section('form_section')
    @if($row->next_level_role_id == null)
        <form method="post" action="{{ route('jb-forward')}}">
            {{ csrf_field() }}
            <input type="hidden" name="benId" value="{{$row->id}}">
            <input type="hidden" name="scheme_id" value="{{$row->scheme_id}}">
            <div class="section1  example-screen">
                <div class="row">
                    <div class="col-md-12">
                        <input style="width:100%; padding: 2%; margin:1%;" type="text" name="comments" id="comments"
                            class="form-control" placeholder="Comments" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4" style="text-align: center;"><input type="submit" name="submit" value="Reject"
                            id="Rejectsubmit" class="btn btn-danger btn-lg"></div>
                    <div class="col-md-4" style="text-align: center;"><input type="submit" name="submit" value="Revert"
                            id="Revertsubmit" class="btn btn-info btn-lg"></div>
                    @if($verifyBtnvisible == 1)
                        <div class="col-md-4" style="text-align: center;"><input type="submit" name="submit" value="Verify"
                                id="Verifysubmit" class="btn btn-success btn-lg"></div>
                    @endif
                </div>
            </div>
        </form>
    @endif
    @endsection
@elseif($is_approver && $view_type == 1)
    @section('form_section')
    <form method="post" action="{{ route('jb-forward-approve') }}">
        {{ csrf_field() }}
        <input type="hidden" name="scheme_id" value="{{ $row->scheme_id }}">
        <input type="hidden" name="benId" value="{{ $row->id }}">
        <div class="row">
            <div class="col-md-3 text-center">
                <input type="submit" name="submit" value="Reject" id="Rejectsubmit"
                    class="btn btn-danger btn-lg btn-action">
            </div>
            <div class="col-md-3 text-center">
                <input type="submit" name="submit" value="Revert" id="Revertsubmit"
                    class="btn btn-primary btn-lg btn-action">
            </div>
            @if ($approveBtnvisible == 1)
                <div class="col-md-3 text-center">
                    <input type="submit" name="submit" value="Approve" id="Approvesubmit"
                        class="btn btn-success btn-lg btn-action">
                </div>
            @endif
        </div>
    </form>

    @endsection
@endif