<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<style>
		.borderClass {
	    	border: 1px solid black;
	    	/*text-align: center;*/
	    	width: 25%;
	    	font-weight: bold;
	    }
	    .borderClass1 {
	    	border: 1px solid black;
	    	/*text-align: center;*/
	    	width: 75%;
	    }
	    .tableclass {
	    	margin-top: 20px;
	    	margin-left: auto;
	    	margin-right: auto;
	    	width: 100%;
	    	overflow: hidden;
	    }
	    page {
	    	background: white;
	    	display: block;
	    	margin: 0 auto;
	    	margin-bottom: 0.5cm;
	    /* box-shadow: 0 0 0.5cm rgba(0,0,0,0.5); */
	    }
	    page[size="A4"] {  
	    	width: 21cm;
	    	height: 29.7cm; 
	    }
	    page[size="A4"][layout="landscape"] {
	    	width: 29.7cm;
	    	height: 21cm;  
	    }
	    @media print {
	        body, page {
	            margin: 0;
	            box-shadow: 0;
	        }
	    }
	</style>
</head>
<body>
	<div style="margin-top: 20px;font-size: 14px; font-weight: bold; text-align: center;">
		<u>Jai Bangla Scheme Application Status</u>
	</div>
	<table><tr><td>&nbsp;</td></tr></table>
	@foreach($results as $result)
    <table class="tableClass">
        <tr><td colspan="2" style="text-align: center;"><b>Beneficiary Personal Details</b></td></tr>
        <tr><td colspan="2"></td></tr>
    	<tr>
    		<td class="borderClass"><b>Pension Id: </b></td>
    		<td class="borderClass1">{{$result->pension_id}}</td>
    	</tr>
    	<tr>
          <td class="borderClass">Scheme </td>
          <td class="borderClass1">{{$result->scheme_name}}</td>
      	</tr>
    	<tr>
    		<td class="borderClass">Status</td>
    		<td class="borderClass1"><b>
    			@php 
	              if(isset($result->next_level_role_id) && $result->lot_generated >= 0 && $result->next_level_role_id == 0){
	                print 'Approved';
	              }
	              else if($result->next_level_role_id === null){
	                print 'Pending for Verification';
	              }
	              else if(isset($result->next_level_role_id) && $result->next_level_role_id > 0){
	                print 'Pending for Approval';
	              }
	              else if(isset($result->next_level_role_id) && $result->next_level_role_id == 0 && $result->lot_generated < 0){
	                print 'Under Bank Details Rectification';
	              }
	              else if($result->next_level_role_id < 0 && $result->next_level_role_id != -99){
	                print 'Beneficiary Rejected';
	              }
	              else if($result->next_level_role_id == -99){
	                print 'Beneficiary Expired';
	              }
	              else{
	                print '';
	              }
	            @endphp</b>
    		</td>
    	</tr>
    	<tr>
          	<td class="borderClass">Name </td>
          	<td class="borderClass1">{{ $result->ben_fname }} {{ $result->ben_mname }} {{ $result->ben_lname }}</td>
        </tr>
        <tr>
          	<td class="borderClass">Father's Name </td>
          	<td class="borderClass1">{{ $result->father_fname }} {{ $result->father_mname }} {{ $result->father_lname }}</td>
        </tr>
        <tr>
          <td class="borderClass">District</td>
          <td class="borderClass1">{{ $result->district_name }}</td>
        </tr>
        <tr>
        	<td class="borderClass">Block/Municipality</td>
          	<td class="borderClass1">{{ $result->block_ulb_name }}</td>
        </tr>
        <tr>
        	<td class="borderClass">GP/Ward</td>
          	<td class="borderClass1">{{ $result->gp_ward_name }}</td>
        </tr>
        <tr>
        	<td class="borderClass">Village/City </td>
          	<td class="borderClass1">{{ $result->village_town_city }}</td>
        </tr>
        <tr>
        	<td class="borderClass">P.S </td>
          	<td class="borderClass1">{{ $result->police_station }}</td>
        </tr>
        <tr>
        	<td class="borderClass">P.O </td>
          	<td class="borderClass1">{{ $result->post_office }}</td>
        </tr>
        <tr>
        	<td class="borderClass">PIN </td>
          	<td class="borderClass1">{{ $result->pincode }}</td>
        </tr>
        @if($result->last_paid_yymm != null)
        <tr>
          <td class="borderClass">Last Payment (Month-Year) </td>
          <td class="borderClass1">
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
        </tr>
        @endif
        <tr>
          <td class="borderClass">No. of Payment </td>
          <td class="borderClass1">{{ $result->payment_count }}</td>
        </tr>  
        <tr>
          <td class="borderClass">Application Date </td>
          <td class="borderClass1"><?php print date("d-m-Y", strtotime($result->created_at)); ?></td>
        </tr>
        <tr><td colspan="2"></td></tr>
        <tr><td colspan="2" style="text-align: center;"><b>Beneficiary Banking Details</b></td></tr>
        <tr><td colspan="2"></td></tr>
        <tr>
          <td class="borderClass">Bank Name </td>
          <td class="borderClass1">{{ $result->bank_name }}</td>
        </tr>
        <tr>
        	<td class="borderClass">Branch Name </td>
          	<td class="borderClass1">{{ $result->branch_name }}</td>
        </tr>
        <tr>
          <td class="borderClass">A/c No </td>
          <td class="borderClass1">{{ $result->bank_code }}</td>
        </tr>
        <tr>
        	<td class="borderClass">IFSC </td>
          	<td class="borderClass1">{{ $result->bank_ifsc }}</td>
        </tr>
        
    </table>
    @endforeach
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <div style="font-size: 8px; border-top: 1px solid black;">
    	<span>@php echo 'https://'. $_SERVER['SERVER_NAME']; @endphp &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; | This is system generated pdf.</span>
    </div>
</body>
</html>
        