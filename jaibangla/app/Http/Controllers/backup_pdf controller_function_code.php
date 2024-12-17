public function printCertificate($id)
    {
    	$application = applicationModel::where('application_id','=',$id)->where('current_status', '=', 'APPROVEDBYDCP')->first();

    	$police_station = Policestation::where('id','=',$application->police_station_name )->first();
    	$gendar ='';
    	$spouse_name = '';
    	 if($application->gender == 'm'){
    	 	$gendar = 'S/O';
    	 	$spouse_name = '';
    	 }
    	 if($application->gender == 'f'){
    	 	$gendar .= 'D/O';
    	 	//$wo .= 'W/O';
    	 	$spouse_name .= 'W/O '. $application->spouse_name .',';
    	 }

         //echo $image = URL::to('/').'/images/'.$application->application_id.'/'.$application->profile_img;

        //echo URL::to('/').'/'.$application->profile_img;
       //$image = URL::to('/').'/'.$application->application_id.'/'.$application->profile_img;
       //echo $imgsrc = '<img src="'.$image.'" alt="Smiley face" height="42" width="42">';

         

        $application_images = PicUpload::where('pcc_appliction_id','=',$id)->first();
        $html ='<div style="width: 1000px; margin:0px auto; overflow: hidden;">
		<table cellpadding="0" cellspacing="0">';
        $html .='<tr>
            <td style=" text-align: center; font-size: 14px;">Government of West Bengal<br>
			Office of the Deputy Commissioner of Police,<br>
			Special Branch, Bidhannagar Police Commissionerate <br>
			Araksha Bhawan, Ground Floor, Sector-II, Solt Lake <br>
			Kolkata-700091<br>
			<b>PHONE & FAX NO.033-2334-3080</b><br>
			Email-ID No.desbcontrol@gmail.com<br></td>
			</tr>';
        $html .='<tr>
				<td style="padding:40px 0px;">
					<table>
						<tr>
							<td width="20%" >PCC No.' .$application->application_id.'</td>
							<td width="50%">&nbsp;</td>
							<td width="30%" >Dated: '.$application->updated_at.'</td>
						</tr>
					</table>
				</td>
			</tr><br>';
        $html .='<tr>
				<td style="padding:50px 0px 30px 0px;"><div style="text-decoration: underline;  text-align: center;">POLICE CLEARANCE CERTIFICATE IN RESPECT OF '.strtoupper($application->first_name).' '.strtoupper($application->middle_name).' '. strtoupper($application->last_name).'</div></td>
			</tr><br>';
        $html .='<tr>
			<td><p style="text-indent: 50px; font-size: 13px; line-height: 24px; ">This is to certify that available records of the local Police Station and Special Branch, Bidhannagar have been consulted but nothing adverse found against '.$application->first_name. ' '. $application->middle_name.' '. $application->last_name.', '.$gendar.' '.$application->father_name.' and '.$spouse_name.'  a resident of '.$application->present_address_line1.', '.$application->present_address_line2.', P.O -'.$application->present_address_line2.' P.S-'.$police_station->name.', Dist - North 24 Parganas, Kolkata - '.$application->present_pincode.'.</p>
		    <p style="text-indent: 50px; font-size: 13px; line-height: 24px;">She is the holder of Indian Passport bearing No. M 3918885 issued on 28/11/2014 form Kolkata and valid till 27/11/2024.</p></td>
			</tr>';
        $html .='<br><tr><td><table >
            <tr>';
        $html .='<td width="20%">';

     
		$image = URL::to('/').'/images/'.$application->application_id.'/'.$application->profile_img;
        Log::info('URL:'.$image);
        $html .= '<img src="'.$image.'" alt="'.$image.'" height="130" width="120" style="padding-top:20px">';



								
        $html .='</td>
        <td width="40%"></td>
							<td width="40%" align="center"><p>Signature</p>
                            <p></p>';
       

        //$certificate = URL::to('/').'/public/images/tcpdf.crt';
        //$certificate = URL::to('/').'/public/images/signature.jpg';

        /*$certificate = __DIR__.'/public/images/tcpdf.crt';
                                    
        // set additional information
        $info = array(
            'Name' => 'Bidhannagar Police Commissionerate',
            'Location' => 'Office',
            'Reason' => 'Signature',
            'ContactInfo' => 'http://www.pcc.bidhannagarpolice.nic.in',
            );

        PDF::setSignature($certificate, $certificate, 'tcpdfdemo', '', 2, $info); */    

         $html .= ' <span style="font-size: 13px;">Deputy Commissioner of Police,<br> Special Branch , Bidhannagar,</span> <span style="font-size: 14px;">Deputy Commissioner of Police <br> Special Branch Bidhannagar</span></td>
        						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
			</tr>
		</table>
	    </div>';	
        //Log::info($html);
        PDF::Image($image);
        PDF::SetTitle('Certificate');
        PDF::SetFont('times', 'N', 12);
        PDF::AddPage('P', 'A4');
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('Certificate.pdf');
    }