@extends('update-ben-details.base1')
@section('action-content')
<section class="content">
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <form method="POST" action="{{ url('edit-details') }}" onsubmit="return validate();" name="myForm">
                    {{ csrf_field() }}
                    <input type="hidden" name="select_item" id="select_item" class="itemUpdate">

                    <div class="form-group col-md-6">
                        <label for="select_item_item" class="control-label">Select Any Item For Update</label>

                        <select class="form-control select2" name="select_item_update" id="select_item_update" required>
                            <option value="0" selected>--Select Update Details--</option>
                            <option value="bank">Update Bank Details</option>
                            <option value="stop_payment">Stop Payment</option>
                        </select>
                        <span id="text_error" class="text-danger"></span>
                    </div>
                    <div>
                        <input type="submit" class="btn btn-primary col-md-1" name="ben_edit" id="ben_edit" value="Edit" disabled>
                    </div>
                </form>
            </div>
            
            <div class="panel panel-default">
                <div class="panel-heading">List of Beneficiary</div>
                <div class="panel-body">
                    <table class="dataTables table table-bordered table-hover" cellspacing="0" width="100%" style="background-color: #fff; ">
                        <thead>
                            <tr role="row">
                                <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">ID</th>
                                <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Beneficiary Name</th>
                                <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Father Name</th>
                                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Block/ULB</th> 
                                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Voter ID Card</th>
                                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Ration Card</th>
                                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank Details</th>
                                <th width="7%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Edit</th>
                                <th width="8%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($results as $result)
                            <tr role="row" class="odd">
                                <td>{{ $result->id }}</td>
                                <td>{{ $result->ben_fname }} {{ $result->ben_mname }} {{ $result->ben_lname }}</td>
                                <td>{{ $result->father_fname }} {{ $result->father_mname }} {{ $result->father_lname }}</td>
                                <td>{{ $result->block_ulb_name }}</td>
                                <td>{{ $result->epic_voter_id }}</td>
                                <td>{{ $result->ration_card_cat}} - {{ $result->ration_card_no }}</td>
                                <td>
                                    <div align="center" class="text-success"><b>IFSC: {{ $result->bank_ifsc }} </b></div>
                                    <div align="center" style="border: 1px solid #000;padding: 5px;border-radius: 5px; background-color: #fffaeb;"><b>Acc No: {{ $result->bank_code }}</b></div>
                                    <div align="center" class="text-danger"><i><b>
                                    @if ($result->lot_generated == -1) Under IFMS Modification
                                    @elseif ($result->lot_generated == -2) Under RBI Modification
                                    @elseif ($result->lot_generated == -3) Under SBI Modification
                                    @else
                                    @endif
                                    </b></i></div>
                                </td>
                                <td> 
                                      @if($result->next_level_role_id==0)
                                          <input type="checkbox" name="ben_{{$result->id}}" id="ben_{{$result->id}}" value="{{$result->id}}_{{$result->lot_generated}}" class="ben_checkbox" onchange="myFunction(this.checked,this.value);"> <font class="text-primary"><b>Checked For Edit</b></font>
                                      @elseif($result->next_level_role_id < 0)
					                                <font color="red">Inactive Beneficiary</font>
				                              @elseif($result->next_level_role_id > 0 or $result->next_level_role_id == '')
					                                <font color="red">Under Approval</font>
				                              @endif                                        
                                </td>
                                <td>
                                    @if($result->next_level_role_id == -98)
                                    <button class="btn btn-info" id="resume_button" value="{{$result->id}}_{{$result->lot_generated}}" onclick="resumeFun(this.value)">Resume</button>
                                    @elseif($result->next_level_role_id ==0 and ($result->scheme_id ==8 or $result->scheme_id==9 or $result->scheme_id==17))
                                    <a href="{{ url('pause-ben-payment/'.$result->id) }}" class="btn btn-success"> Pause</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach    
                        </tbody>
                        <tfoot>
                            <tr>
                                <th width="5%" rowspan="1" colspan="1">ID</th>
                                <th width="15%" rowspan="1" colspan="1">Beneficiary Name</th>
                                <th width="15%" rowspan="1" colspan="1">Father Name</th>
                                <th width="10%" rowspan="1" colspan="1">Block/ULB</th>
                                <th width="10%" rowspan="1" colspan="1">Voter Id Card</th>
                                <th width="10%" rowspan="1" colspan="1">Ration Card</th>
                                <th width="20%" rowspan="1" colspan="1">Bank Details</th>
                                <th width="7%" rowspan="1" colspan="1">Edit</th>
                                <th width="8%" rowspan="1" colspan="1">Action</th>
                            </tr>
                        </tfoot>     
                    </table>
                </div>
            </div>
            <br>
            <a href="{{ route('update-ben-details') }}"><input type="submit" name="back" value="Back To Search" class="btn btn-success"></a>

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Resume Beneficiary Payment</h4>
            </div>
            <div class="modal-body">
              <form method="POST" action="{{ url('resume-ben-payment') }}" onsubmit="return resume_validate();">
                {{csrf_field()}}
                <input type="hidden" name="ben_id" id="ben_id">
                <input type="hidden" name="lot_generate_no" id="lot_generate_no">
                <div class="form-group">
                    <div style="font-size: 15px; font-weight: bold; font-style: italic; text-align: right;" id="modify_div_display" class="text-danger">This beneficiary under RBI modification</div>
                </div>

                  <div class="form-group">
                      <label for="resume_month">From which month you want to resume ?</label>
                      <select class="form-control" id="resume_month" name="resume_month" required>
                          <option value="">--Select month--</option>
                          @php $month = date("Y-m-d"); @endphp
                          <option value='@php print date("ym", strtotime("$month -1 month")); @endphp'>@php print date("F-Y", strtotime("$month +0 month")); @endphp</option>
                          <option value='@php print date("ym", strtotime("$month +0 month")); @endphp'>@php print date("F-Y", strtotime("$month +1 month")); @endphp</option>
                          <option value='@php print date("ym", strtotime("$month +1 month")); @endphp'>@php print date("F-Y", strtotime("$month +2 month")); @endphp</option>
                      </select>
                  </div>
                  <div class="form-group" align="center">
                      <button class="btn btn-primary">Resume Payment</button>
                  </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              <!-- <button type="button" class="btn btn-primary"><i class="fa fa-print"></i> Resume</button> -->
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
</section>

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
    function validate(){
        if (document.getElementById('select_item_update').value == 0){
            alert('Please Select One Item for Update');
            document.getElementById('text_error').innerHTML = 'Please Select One Item for Update';
            return false;
        }
    }

    //select only one checkbox
      $(document).ready(function() {
      $('.ben_checkbox').each(function() {
        $(this).addClass('unselected');
      });
      $('.ben_checkbox').on('click', function() {
        $(this).toggleClass('unselected');
        $(this).toggleClass('selected');
        $('.ben_checkbox').not(this).prop('checked', false);
        $('.ben_checkbox').not(this).removeClass('selected');
        $('.ben_checkbox').not(this).addClass('unselected');
      });
    });
     function myFunction(check, value){
        //Splitting
        var myarr = value.split("_");
        var ben_id = myarr[0];
        var lot_generate = myarr[1];
        //alert(lot_generate);

        if (check == true){
          if (lot_generate <-10) {
            $("#select_item_update option[value='bank']").remove();
          }
          else{
            if ($("#select_item_update option[value='bank']").length == 0) {
              $("#select_item_update").append('<option value="bank">Update Bank Details</option>');
            }
          }
          document.getElementById('select_item').value = ben_id;
          document.getElementById("ben_edit").disabled=false;
        }
        else{
          if (lot_generate <-10) {
            if ($("#select_item_update option[value='bank']").length == 0) {
              $("#select_item_update").append('<option value="bank">Update Bank Details</option>');
            }
          }
          document.getElementById('select_item').value = '';
          document.getElementById("ben_edit").disabled=true;
        }  
      }

    function resume_validate(){
        if (document.getElementById('resume_month').value == ''){
            alert('Please select month');
            return false;
        }
        return true;
    }  

    function resumeFun(val){
        $('#show_month').text('');
        $('#modal-default').modal('show');
        var arr = val.split('_');
        $('#ben_id').val(arr[0]);
        $('#lot_generate_no').val(arr[1]);
        if (arr[1] == -1) {
            document.getElementById('modify_div_display').style.display = '';
            $('#modify_div_display').text('*This beneficiary under IFMS modification');
        }
        else if (arr[1] == -2) {
            document.getElementById('modify_div_display').style.display = '';
            $('#modify_div_display').text('*This beneficiary under RBI modification');
        }
        else if (arr[1] == -3) {
            document.getElementById('modify_div_display').style.display = '';
            $('#modify_div_display').text('*This beneficiary under SBI modification');
        }
        else{
            document.getElementById('modify_div_display').style.display = 'none';
            $('#modify_div_display').text('');   
        }
    }

</script>
@endsection