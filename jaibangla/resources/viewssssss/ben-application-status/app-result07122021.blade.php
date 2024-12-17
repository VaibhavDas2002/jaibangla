<style type="text/css">
  .has-error
  {
    border-color:#cc0000;
    background-color:#ffff99;
  }
  .preloader1{
    position: fixed;
    top:40%;
    left: 52%;
    z-index: 999;
  }
  .preloader1 {
    background: transparent !important;
  }
  .collapsable-head {
    /*border-radius: 5px; */
    border-bottom: 1px solid powderblue;
    background-color: antiquewhite; 
    text-align: center; 
    padding: 5px; 
    cursor: pointer;
  }
  .collapsable-head span {
    float: left; 
    margin-top: 4px;
    margin-left: 10px;
  }
  .collapsable-head font {
    font-size: 15px; 
    font-weight: bold;
  }
  .main-collapsable {
    border: 1px solid powderblue; 
    border-radius: 4px;
    margin-top: 5px;
  }
  table {
    border-top: 1px solid #e6e6e6;
  }
  .collapsable-body {
    padding: 5px;
  }
  .tbl {
    border: 1px solid #000;
    font-size: 14px;
    margin-top: 5px; 
    width: 100%; 
    background-color: #fff;
  }
  .tbl tr td {
    width: 25%;
    padding: 1px;
  }
</style>
<section>
  @if($mapping_level == 'State' || $mapping_level == 'Department')
  <div class="main-collapsable">
    <div class="collapsable-head" id="head_div">
      <span id="icon"><i class="fa fa-minus"></i></span><font>Beneficiary Details</font>
    </div>
    <div id="details_div" class="table-responsive">
      @if(count($results)>0)
      <table id="example" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 14px; text-align: center;">
            <th colspan="9">Personal Details</th>
            <th colspan="6">Payment Related Details</th>
            <th colspan="4">Banking Details</th>
          </tr>
          <tr style="font-size: 12px;">
            <th>Pension Id</th>
            <th>Name</th>
            <th>Father's Name</th>
            <th>Voter ID</th>
            <th>Ration Card</th>
            <th>District</th>
            <th>Scheme</th>
            <th>Block/Municipality</th>
            <th>GP/Ward</th>
            <th>Application Date</th>
            <th>Next Level Role Id</th>
            <th>Lot Generated</th>
            <th>Bank Edited</th>
            <th>Payment Count</th>
            <th>Last Paid YYMM</th>
            <th>Bank Name</th>
            <th>Branch Name</th>
            <th>Account No</th>
            <th>Ifsc Code</th>
          </tr>
          @foreach($results as $r)
          <tr style="font-size: 14px;">
            <td>{{ $r->id }}</td>
            <td>{{ $r->ben_fname }} {{ $r->ben_mname }} {{ $r->ben_lname }}</td>
            <td>{{ $r->father_fname }} {{ $r->father_mname }} {{ $r->father_lname }}</td>
            <td>{{ $r->epic_voter_id }}</td>
            <td>{{ $r->ration_card_cat}} - {{ $r->ration_card_no }}</td>
            <td>
              @php $dist_code = $r->dist_code;
                $sobj = DB::table('m_district')->where('district_code',$dist_code)->first();
                print $sobj->district_name;
              @endphp
            </td>
            <td>
              @php $sc_id = $r->scheme_id;
                $sobj = DB::table('m_scheme')->where('id',$sc_id)->first();
                print $sobj->scheme_name;
              @endphp
            </td>
            <td>{{ $r->block_ulb_name }}</td>
            <td>{{ $r->gp_ward_name }}</td>
            <td><?php print date("d-m-Y", strtotime($r->created_at)); ?></td>
            <td>{{ $r->next_level_role_id }}</td>
            <td>{{ $r->lot_generated }}</td>
            <td>{{ $r->bank_edited }}</td>
            <td>{{ $r->payment_count }}</td>
            <td>{{ $r->last_paid_yymm }}</td>
            <td>{{ $r->bank_name }}</td>
            <td>{{ $r->branch_name }}</td>
            <td>{{ $r->bank_code }}</td>
            <td>{{ $r->bank_ifsc }}</td>
          </tr>
          @endforeach
        </thead>
      </table>
      @else
        <div class="text-danger" align="center"><h4>No record found!!</h4></div>
      @endif
    </div>
  </div>

@if(count($results)>0)
  @if(count($duplicate)>0)
  <div class="main-collapsable">
    <div class="collapsable-head" id="head_div6">
      <span id="icon6"><i class="fa fa-plus"></i></span><font>Duplicate Beneficiary Details [Duplicate Approve Reject]</font>
    </div>
    <div id="details_div6" class="table-responsive">
      <table id="example" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Pension Id</th>
            <th>Name</th>
            <th>Father's Name</th>
            <th>Voter ID</th>
            <th>Ration Card</th>
            <th>District</th>
            <th>Scheme</th>
            <th>Block/Municipality</th>
            <th>Next Level Role Id</th>
            <th>Lot Generated</th>
            <th>Bank Edited</th>
            <th>Payment Count</th>
            <th>Last Paid YYMM</th>
            <th>Rejected User</th>
          </tr>
        </thead>
        <tbody>
          @foreach($duplicate as $d)
          <tr style="font-size: 14px;">
            <td>{{ $d->original_application_id }}</td>
            <td>{{ $d->ben_fname }} {{ $d->ben_mname }} {{ $d->ben_lname }}</td>
            <td>{{ $d->father_fname }} {{ $d->father_mname }} {{ $d->father_lname }}</td>
            <td>{{ $d->epic_voter_id }}</td>
            <td>{{ $d->ration_card_cat}} - {{ $d->ration_card_no }}</td>
            <td>
              @php $dist_code = $d->dist_code;
                $sobj = DB::table('m_district')->where('district_code',$dist_code)->first();
                print $sobj->district_name;
              @endphp
            </td>
            <td>
              @php $sc_id = $d->scheme_id;
                $sobj = DB::table('m_scheme')->where('id',$sc_id)->first();
                print $sobj->scheme_name;
              @endphp
            </td>
            <td>{{ $d->block_ulb_name }}</td>
            <td>{{ $d->next_level_role_id }}</td>
            <td>{{ $d->lot_generated }}</td>
            <td>{{ $d->bank_edited }}</td>
            <td>{{ $d->payment_count }}</td>
            <td>{{ $d->last_paid_yymm }}</td>
            <td>{{ $d->username }}( {{$d->rejected_user_id}} )</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  @if(count($update_details)>0)
  <div class="main-collapsable">
    <div class="collapsable-head" id="head_div7">
      <span id="icon7"><i class="fa fa-plus"></i></span><font>Update Beneficiary Details [Update Ben Details]</font>
    </div>
    <div id="details_div7" class="table-responsive">
      <table id="example" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          
          <tr style="font-size: 12px;">
            <th>Pension Id</th>
            <th>District</th>
            <th>Scheme</th>
            <th>Old Data</th>
            <th>New Data</th>
            <th>Remarks</th>
            <th>Done By User</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @foreach($update_details as $up)
          <tr style="font-size: 14px;">
            <td>{{ $up->original_application_id }}</td>
            <td>
              @php $dist_code = $up->dist_code;
                $sobj = DB::table('m_district')->where('district_code',$dist_code)->first();
                print $sobj->district_name;
              @endphp
            </td>
            <td>
              @php $sc_id = $up->scheme_id;
                $sobj = DB::table('m_scheme')->where('id',$sc_id)->first();
                print $sobj->scheme_name;
              @endphp
            </td>
            <td>
	      @if((json_decode($up->old_data)) !== null)
              @foreach(json_decode($up->old_data) as $key => $val)
              <li>{{ $key }}: {{ $val}}</li>
              @endforeach
	      @endif
            </td>
            <td>
	      @if((json_decode($up->new_data)) !== null)
              @foreach(json_decode($up->new_data) as $key => $val)
              <li>{{ $key }}: {{ $val}}</li>
              @endforeach
	      @endif
            </td>
            <td>{{ $up->remarks }}</td>
            <td>{{ $up->user_id }}</td>
            <td>@php print date("d-m-Y", strtotime($up->created_at)); @endphp</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  @if(count($ifms_r)>0 || count($ifms)>0)
  <div class="main-collapsable">
    <div class="collapsable-head" id="head_div1">
      <span id="icon1"><i class="fa fa-plus"></i></span><font>IFMS Payment Details [Transaction Lot Details]</font>
    </div>
    <div id="details_div1" class="table-responsive">
      <table id="example" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Sl No</th>
            <th>Lot No</th>
            <th>Pension Id</th>
            <th>Name</th>
            <th>Scheme</th>
            <th>Account No</th>
            <th>Ifsc Code</th>
            <th>Mobile No</th>
            <th>IFMS Status</th>
            <th>Is Active</th>
            <th>Wrongdata Flag</th>
            <th>UTR No</th>
            <th>Paid YYMM</th>
          </tr>
          @php $j=1; @endphp
          @foreach($ifms_r as $ir)
          <tr style="font-size: 14px;">
            <td>@php print $j++; @endphp</td>
            <td>{{ $ir->drn_part }}</td>
            <td>{{ $ir->pension_id }}</td>
            <td>{{ $ir->name }}</td>
            <td>
              @php $sc_id = $ir->scheme_id;
                $sobj = DB::table('m_scheme')->where('id',$sc_id)->first();
                print $sobj->scheme_name;
              @endphp
            </td>
            <td>{{ $ir->acc_no }}</td>
            <td>{{ $ir->ifsc }}</td>
            <td>{{ $ir->mobile_no }}</td>
            <td>{{ $ir->ifms_status }}</td>
            <td>{{ $ir->is_active }}</td>
            <td>{{ $ir->wrongdata_flag }}</td>
            <td>{{ $ir->utr_no }}</td>
            <td>{{ $ir->paid_yymm }}</td>
          </tr>
          @endforeach
          @foreach($ifms as $i)
          <tr style="font-size: 14px;">
            <td>@php print $j++; @endphp</td>
            <td>{{ $i->drn_part }}</td>
            <td>{{ $i->pension_id }}</td>
            <td>{{ $i->name }}</td>
            <td>
              @php $sc_id = $i->scheme_id;
                $sobj = DB::table('m_scheme')->where('id',$sc_id)->first();
                print $sobj->scheme_name;
              @endphp
            </td>
            <td>{{ $i->acc_no }}</td>
            <td>{{ $i->ifsc }}</td>
            <td>{{ $i->mobile_no }}</td>
            <td>{{ $i->ifms_status }}</td>
            <td>{{ $i->is_active }}</td>
            <td>{{ $i->wrongdata_flag }}</td>
            <td>{{ $i->utr_no }}</td>
            <td>{{ $i->paid_yymm }}</td>
          </tr>
          @endforeach
        </thead>
      </table>
    </div>
  </div>
  @endif

  @if(isset($ifms_l))
  <div class="main-collapsable">
    <div class="collapsable-head" id="head_div3">
      <span id="icon3"><i class="fa fa-plus"></i></span><font>IFMS Payment Details [Transaction Lot]</font>
    </div>
    <div id="details_div3" class="table-responsive">
      <table id="example" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Lot No</th>
            <th>Lot Month</th>
            <th>Lot Year</th>
            <th>IFMS Failed Count</th>
            <th>RBI Success Count</th>
            <th>RBI Failed Count</th>
            <th>Lot Status</th>
            <th>File Name</th>
          </tr>
          @foreach($ifms_l as $il)
            <tr style="font-size: 14px;">
              <td>{{ $il->lot_no }}</td>
              <td>{{ $il->lot_month }}</td>
              <td>{{ $il->lot_year }}</td>
              <td>{{ $il->ifms_wrongdata_count }}</td>
              <td>{{ $il->rbi_success_count }}</td>
              <td>{{ $il->rbi_failed_count }}</td>
              <td>{{ $il->lot_status }}</td>
              <td>{{ $il->file_name }}</td>
            </tr>
          @endforeach
        </thead>
      </table>
    </div>
  </div>
  @endif

  @if(count($sbi_r)>0 || count($sbi)>0)
  <div class="main-collapsable">
    <div class="collapsable-head" id="head_div2">
      <span id="icon2"><i class="fa fa-plus"></i></span><font>SBI Payment Details [Transaction Lot Details]</font>
    </div>
    <div id="details_div2" class="table-responsive">
      <table id="example" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Sl No.</th>
            <th>Lot No</th>
            <th>Pension Id</th>
            <th>Name</th>
            <th>Scheme</th>
            <th>Account No</th>
            <th>Ifsc Code</th>
            <th>Status Code</th>
            <th>Is Active</th>
            <th>Paid YYMM</th>
          </tr>
          @php $i=1; @endphp
          @foreach($sbi_r as $sr)
          <tr style="font-size: 14px;">
            <td>@php print $i++; @endphp</td>
            <td>{{ $sr->lot_no }}</td>
            <td>{{ $sr->pension_id }}</td>
            <td>{{ $sr->name }}</td>
            <td>
              @php $sc_id = $sr->scheme_id;
                $sobj = DB::table('m_scheme')->where('id',$sc_id)->first();
                print $sobj->scheme_name;
              @endphp
            </td>
            <td>{{ $sr->account_credit }}</td>
            <td>{{ $sr->ifsc_code_credit }}</td>
            <td>{{ $sr->status_code }}</td>
            <td>{{ $sr->is_active }}</td>
            <td>{{ $sr->paid_yymm }}</td>
          </tr>
          @endforeach
          @foreach($sbi as $s)
          <tr style="font-size: 14px;">
            <td>@php print $i++; @endphp</td>
            <td>{{ $s->lot_no }}</td>
            <td>{{ $s->pension_id }}</td>
            <td>{{ $s->name }}</td>
            <td>
              @php $sc_id = $s->scheme_id;
                $sobj = DB::table('m_scheme')->where('id',$sc_id)->first();
                print $sobj->scheme_name;
              @endphp
            </td>
            <td>{{ $s->account_credit }}</td>
            <td>{{ $s->ifsc_code_credit }}</td>
            <td>{{ $s->status_code }}</td>
            <td>{{ $s->is_active }}</td>
            <td>{{ $s->paid_yymm }}</td>
          </tr>
          @endforeach
        </thead>
      </table>
    </div>
  </div>
  @endif

  @if(isset($sbi_l))
  <div class="main-collapsable">
    <div class="collapsable-head" id="head_div4">
      <span id="icon4"><i class="fa fa-plus"></i></span><font>SBI Payment Details [Transaction Lot]</font>
    </div>
    <div id="details_div4" class="table-responsive">
      <table id="example" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Lot No</th>
            <th>Lot Month</th>
            <th>Lot Year</th>
            <th>Success Count</th>
            <th>Failed Count</th>
            <th>Lot Status</th>
          </tr>
          @foreach($sbi_l as $sl)
            <tr style="font-size: 14px;">
              <td>{{ $sl->lot_no }}</td>
              <td>{{ $sl->lot_month }}</td>
              <td>{{ $sl->lot_year }}</td>
              <td>{{ $sl->success_count }}</td>
              <td>{{ $sl->failed_count }}</td>
              <td>{{ $sl->lot_status }}</td>
            </tr>
          @endforeach
        </thead>
      </table>
    </div>
  </div>
  @endif

  @if(isset($lot_m))
  <div class="main-collapsable">
    <div class="collapsable-head" id="head_div5">
      <span id="icon5"><i class="fa fa-plus"></i></span><font>Lot Master</font>
    </div>
    <div id="details_div5" class="table-responsive">
      <table id="example" class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead>
          <tr style="font-size: 12px;">
            <th>Lot No</th>
            <th>Lot Month</th>
            <th>Lot Year</th>
            <th>Success Count</th>
            <th>Failed Count</th>
            <th>Ref No</th>
            <th>Lot Type</th>
            <th>Payment Mode</th>
            <th>Repeat Lot</th>
            <th>Repeat DRN Part</th>
          </tr>
          @foreach($lot_m as $lm)
            <tr style="font-size: 14px;">
              <td>{{ $lm->lot_no }}</td>
              <td>{{ $lm->lot_month }}</td>
              <td>{{ $lm->lot_year }}</td>
              <td>{{ $lm->rbi_success_count }}</td>
              <td>{{ $lm->rbi_failed_count }}</td>
              <td>{{ $lm->ref_no }}</td>
              <td>
              @php $sc_id = $lm->lot_type_id;
                $sobj = DB::table('m_lot_type')->where('id',$sc_id)->first();
                print $sobj->lot_type;
              @endphp</td>
              <td>{{ $lm->payment_mode }}</td>
              <td>{{ $lm->repeat_lot }}</td>
              <td>{{ $lm->repeat_drn_part }}</td>
            </tr>
          @endforeach
        </thead>
      </table>
    </div>
  </div>
  @endif
@endif

  @elseif($mapping_level == 'District' || $mapping_level == 'Block' || $mapping_level == 'Subdiv')
  <div class="main-collapsable">
    <div class="collapsable-head">
      <font>Beneficiary Details</font>
    </div>
    <div class="table-responsive" style="margin: 5px;">
      @if(count($results)>0)
      @foreach($results as $result)
      <table class="tbl" width="100%" cellspacing="0">
        <tr align="center" style="border: 1px solid #000;">
          <td colspan="2" class="text-primary"><font size="3"><b>Beneficiary Personal Details (Pension Id: {{$result->id}})</b></font></td>
          <td align="right" style="font-size: 16px; font-weight: bold;">
            @php 
              if(isset($result->next_level_role_id) && $result->lot_generated >= 0 && $result->next_level_role_id == 0){
                print '<span class="label label-success">Approved</span>';
              }
              else if($result->next_level_role_id === null){
                print '<span class="label label-info">Pending for Verification</span>';
              }
              else if(isset($result->next_level_role_id) && $result->next_level_role_id > 0){
                print'<span class="label label-warning">Pending for Approval</span>';
              }
              else if(isset($result->next_level_role_id) && $result->next_level_role_id == 0 && $result->lot_generated < 0){
                print'<span class="label label-warning">Under Bank Details Rectification</span>';
              }
              else if($result->next_level_role_id < 0 && $result->next_level_role_id != -99){
                print'<span class="label label-danger">Beneficiary Rejected</span>';
              }
              else if($result->next_level_role_id == -99){
                print'<span class="label label-danger">Beneficiary Expired</span>';
              }
              else{
                print '';
              }
            @endphp
          </td>
          <td><button class="btn btn-link btn-sm pdf_btn" title="Print Status" value="{{$result->id}}" style="float: right;">Print <i class="fa fa-print"></i></button></td>
          
        </tr>
        <tr>
          <td><b>Name </b></td>
          <td>{{ $result->ben_fname }} {{ $result->ben_mname }} {{ $result->ben_lname }}</td>
          <th>Block/Municipality</th>
          <td>{{ $result->block_ulb_name }}</td>
        </tr>
        <tr>
          <td><b>Father's Name </b></td>
          <td>{{ $result->father_fname }} {{ $result->father_mname }} {{ $result->father_lname }}</td>
          <th>Village/City </th>
          <td>{{ $result->village_town_city }}</td>
        </tr>

        @if($result->last_paid_yymm != null)
        <tr>
          <td><b>Last Payment (Month-Year) </b></td>
          <td><!-- {{ $result->last_paid_yymm }} -->
            <?php
              $month_arr=[];
              $month_arr = ['01'=> 'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'];
              $date = $result->last_paid_yymm;
              $arr = str_split($date, 2);
              $year = $arr[0];
              $month = $arr[1];
              foreach($month_arr as $key=>$value){
                if($month == $key){
                  $month_final = $value;
                }
              }
              print $month_final.'-'.$year;
            ?>
          </td>
          <th>GP/Ward</th>
          <td>{{ $result->gp_ward_name }}</td>
        </tr>
        @endif
        <tr>
          <td><b>No. of Payment </b></td>
          <td>{{ $result->payment_count }}</td>
          <td><b>P.S </b></td>
          <td>{{ $result->police_station }}</td>
        </tr>
        <tr>
          <td><b>Application Date </b></td>
          <td><?php print date("d-m-Y", strtotime($result->created_at)); ?></td>
          <td><b>P.O </b></td>
          <td>{{ $result->post_office }}</td>
        </tr>
        <tr>
          <td><b>Scheme </b></td>
          <td>
            @php $sc_id = $result->scheme_id;
              $sobj = DB::table('m_scheme')->where('id',$sc_id)->first();
              print $sobj->scheme_name;
            @endphp
          </td>
          <td><b>PIN </b></td>
          <td>{{ $result->pincode }}</td>
        </tr>
        <tr>
          <td><b>Bank Name </b></td>
          <td>{{ $result->bank_name }}</td>
          <td><b>Branch Name </b></td>
          <td>{{ $result->branch_name }}</td>
        </tr>
        <tr>
          <td><b>A/c No </b></td>
          <td>{{ $result->bank_code }}</td>
          <td><b>IFSC </b></td>
          <td>{{ $result->bank_ifsc }}</td>
        </tr>
        <tr>
          <td><b>District<b></td>
          <td>
            @php $dist_code = $result->created_by_dist_code;
              $sobj = DB::table('m_district')->where('district_code',$dist_code)->first();
              print $sobj->district_name;
            @endphp
          </td>
          <td></td>
          <td></td>
        </tr>
      </table>
      
      @endforeach
      @else
      <div class="text-danger" align="center"><h4>No record found!!</h4></div>
      @endif
    </div>
  </div>
  @endif
    
</section>
<script>
  $('.pdf_btn').click(function(){
    var val = $(this).val();
    var  data= {'_token': '{{csrf_token()}}', 'id': val};
    redirectPostPDF('{{route("app-status-savePdf")}}', data, 'get');
  });

  function redirectPostPDF(url, data , method = 'get'){
    var form = document.createElement('form');
    form.method = method;
    form.action = url;
    for (var name in data) {
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = name;
      input.value = data[name];
      form.appendChild(input);
    }
    $('body').append(form);
    form.submit();
  }

  $("#details_div").show();
  $("#details_div1").hide();
  $("#details_div2").hide();
  $("#details_div3").hide();
  $("#details_div4").hide();
  $("#details_div5").hide();
  $("#details_div6").hide();
  $("#details_div7").hide();
  $("#head_div").on("click", function () {
    $("#details_div").toggle("slow");
    if ($('#icon').html() == "<i class=\"fa fa-plus\"></i>")
      $('#icon').html("<i class=\"fa fa-minus\"></i>");
    else
      $('#icon').html("<i class=\"fa fa-plus\"></i>");

  });
  $("#head_div1").on("click", function () {
    $("#details_div1").toggle("slow");
    if ($('#icon1').html() == "<i class=\"fa fa-plus\"></i>")
      $('#icon1').html("<i class=\"fa fa-minus\"></i>");
    else
      $('#icon1').html("<i class=\"fa fa-plus\"></i>");
  });
  $("#head_div2").on("click", function () {
    $("#details_div2").toggle("slow");
    if ($('#icon2').html() == "<i class=\"fa fa-plus\"></i>")
      $('#icon2').html("<i class=\"fa fa-minus\"></i>");
    else
      $('#icon2').html("<i class=\"fa fa-plus\"></i>");
  });
  $("#head_div3").on("click", function () {
    $("#details_div3").toggle("slow");
    if ($('#icon3').html() == "<i class=\"fa fa-plus\"></i>")
      $('#icon3').html("<i class=\"fa fa-minus\"></i>");
    else
      $('#icon3').html("<i class=\"fa fa-plus\"></i>");
  });
  $("#head_div4").on("click", function () {
    $("#details_div4").toggle("slow");
    if ($('#icon4').html() == "<i class=\"fa fa-plus\"></i>")
      $('#icon4').html("<i class=\"fa fa-minus\"></i>");
    else
      $('#icon4').html("<i class=\"fa fa-plus\"></i>");
  });
  $("#head_div5").on("click", function () {
    $("#details_div5").toggle("slow");
    if ($('#icon5').html() == "<i class=\"fa fa-plus\"></i>")
      $('#icon5').html("<i class=\"fa fa-minus\"></i>");
    else
      $('#icon5').html("<i class=\"fa fa-plus\"></i>");
  });
  $("#head_div6").on("click", function () {
    $("#details_div6").toggle("slow");
    if ($('#icon6').html() == "<i class=\"fa fa-plus\"></i>")
      $('#icon6').html("<i class=\"fa fa-minus\"></i>");
    else
      $('#icon6').html("<i class=\"fa fa-plus\"></i>");
  });
  $("#head_div7").on("click", function () {
    $("#details_div7").toggle("slow");
    if ($('#icon7').html() == "<i class=\"fa fa-plus\"></i>")
      $('#icon7').html("<i class=\"fa fa-minus\"></i>");
    else
      $('#icon7').html("<i class=\"fa fa-plus\"></i>");
  });

</script>