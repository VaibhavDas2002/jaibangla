<html>
    <body>
        <div>
		@php
			$split_pos = stripos($msg,",");
			print substr($msg,0,$split_pos+1);
			print '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			print substr($msg,$split_pos+1);
		@endphp
        </div>
		<div>
		<br/><br/>
		Note: This is an automated system generated email message, please do not reply.
		<br/><br/><b>Jai Bangla</b>
		<br/><b>Government of West Bengal</b>
		</div>
    </body>
</html>