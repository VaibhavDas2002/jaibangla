$('.select2').select2()
 var count = 1;
$("#add").click(function(){
      //alert("check me");
      count = count + 1;

      var html_code ='<div class="col-md-12" id="row'+count+'">'; 
      html_code +=`<div class="row">`;
      html_code +=`<div class="form-group col-md-4" >
                        <select class="form-control doc_name" name="doc_name[]" >
                              <option value="aadharCard">Aadhar Card (both side)</option>
                              <option value="drivingLience">Driving Licence(both side)</option>
                              <option value="passport">Passport</option>
                              <option value="voterId">Voter ID (both side)</option>
                        </select>
                    </div>`; 
      html_code +=`<div class="form-group col-md-4 input-group-cus">
      <input type="file" id="demo`+count+`" name="doc_type[]" class="filestyle doc_type" placeholder="No file"  ></div>`; 
      html_code +='<div class="form-group col-md-3 doc_number doc_no"><input type="text" class="form-control" name="doc_number[]" id="doc_no" placeholder="Input Number"></div>'; 
     
      html_code +=`<div  ><button type='button' name='remove' style='float:right; width:30px' data-row='row`+count+`' class='btn btn-danger btn-xs remove'>-</button></div>`; 
      html_code +="</div></div>";
      $('#crud_table').append(html_code); 
      $("#demo" + count).filestyle();
  });

  $(document).on('click','.remove',function(){
        var delete_row = $(this).data("row");
        $('#' + delete_row).remove();
  });

$('#purposeType').on('change',function(){
    var selection = $(this).val();
  
   if(selection == 'others'){

    $('#otherType').show();
   }else{
    $('#otherType').hide();
   }
  });


//Present address and parmanent address copy

      $('#addressCheckbox').click(function(){
          if($(this).is(':checked')) {
          var in_present_address_line1 = $('#in_present_address_line1').val();
          var in_present_address_line2 = $('#in_present_address_line2').val();
          var in_present_address_landmark = $('#in_present_address_landmark').val();
          var in_present_pincode = $('#in_present_pincode').val();
          var in_present_city = $('#in_present_city').val();
          var in_present_state = $('#in_present_state').val();
          var in_present_country = $('#in_present_country').val();
          $('#in_permanent_address_line1').val(in_present_address_line1);
          $('#in_permanent_address_line2').val(in_present_address_line2);
          $('#in_permanent_address_landmark').val(in_present_address_landmark);
          $('#in_permanent_pincode').val(in_present_pincode);
          $('#in_permanent_city').val(in_present_city);
          $('#in_permanent_state').val(in_present_state);
          $('#in_permanent_country').val(in_present_country);
          } else{
          $('#in_permanent_address_line1').val("");
          $('#in_permanent_address_line2').val("");
          $('#in_permanent_address_landmark').val("");
          $('#in_permanent_pincode').val("");
          $('#in_permanent_city').val("");
          $('#in_permanent_state').val("");
          $('#in_permanent_country').val("");
            }
    });





      $('.in_first_name').keyup(function(){
        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
        var str = $(this).val();

         if(intRegex.test(str) || floatRegex.test(str)) {
           alert('Please Inter input character for your First Name ');
        }

        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(str);
        if(isSplChar)
        {
          var no_spl_char = str.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
      });

      $('.in_middle_name').keyup(function(){
        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
        var str = $(this).val();

         if(intRegex.test(str) || floatRegex.test(str)) {
           alert('Please Inter input character for your Middle Name Name ');
        }

        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(str);
        if(isSplChar)
        {
          var no_spl_char = str.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
      });

      $('#in_last_name').keyup(function(){
        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

        var str = $(this).val();
        if(intRegex.test(str) || floatRegex.test(str)) {
           alert('Please Inter input character for your Last  Name ');
        }

        //spacial character repalce
        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(str);
        if(isSplChar)
        {
          var no_spl_char = str.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
      });

      $('#in_father_name').keyup(function(){
        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

        var str = $(this).val();
        if(intRegex.test(str) || floatRegex.test(str)) {
           alert('Please Inter input character for your Last  Name ');
        }

        //spacial character repalce
        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(str);
        if(isSplChar)
        {
          var no_spl_char = str.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
      });



      

      $('#in_spouse_name').keyup(function(){
        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
        var str = $(this).val();

         if(intRegex.test(str) || floatRegex.test(str)) {
           alert('Please Inter input character for your Spouse Name Name ');
        }

        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(str);
        if(isSplChar)
        {
          var no_spl_char = str.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
      });


      $('#in_email').blur(function(e) {
 
      var sender_email = $(this).val();
      // Check if the Fields are empty or not.
      if ($.trim(sender_email).length == 0 || $("#in_email").val()=="" || $("#in_email").val()=="") {
      alert('email fields are mandatory,Try again');
      e.preventDefault();
      }
      if (validate_Email(sender_email)) {
      //alert('Nice!! your Email is valid, now you can continue..');
      }
      else {
      alert('Invalid Email Address');
      e.preventDefault();
      }
      });
     
      // Function that validates email address through a regular expression.
      function validate_Email(sender_email) {
      var expression = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
      if (expression.test(sender_email)) {
        return true;
        }
      else {
        return false;
        }
      }

      $("#in_mobile_no").blur(function(e){
        var mobile_number =  $("#in_mobile_no").val()

       
        if (mobile_valid(mobile_number)) {
        
           if(mobile_number.length == 10){
            //alert('Nice!! your Phone Number is valid, now you can continue..');
          }
          else{
            alert('Enter 10 digit mobile number!');
          }
        }
        else {
        //alert('Invalid Phone Number !');
        e.preventDefault();
        }
      });

      function mobile_valid(mobile_number){
        var expression = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
        if(expression.test(mobile_number)){
          return true;
        }else{
          return false;
        }
      }

      $('#in_present_address_line1').keyup(function(e)
      {
        var present_address =  $("#in_present_address_line1").val();
             
        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(present_address);
        if(isSplChar)
        {
          var no_spl_char = present_address.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }

        
      });

      $('#in_present_address_line2').keyup(function(e)
      {
        var present_address =  $("#in_present_address_line2").val();
             
        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(present_address);
        if(isSplChar)
        {
          var no_spl_char = present_address.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }

        
      });


      $('#in_present_address_landmark').keyup(function(e)
      {
        var present_address =  $("#in_present_address_landmark").val();
             
        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(present_address);
        if(isSplChar)
        {
          var no_spl_char = present_address.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }

        
      });




      $("#in_present_pincode").blur(function(e){
        var pincode_number =  $("#in_present_pincode").val()
         if ($.trim(pincode_number).length == 0 || $("#in_present_pincode").val()=="" || $("#in_present_pincode").val()==""){
          alert('Pincode Number fields are mandatory,Try again');
          e.preventDefault();
          }
          if (pincode_valid(pincode_number)) {
          
             if(pincode_number.length !=6){
              alert('Enter 6 digit Pincode number!');
            }
            else{
              //alert('Nice!! your Pincode Number is valid, now you can continue..');
            }
          }
        else {
        alert('Invalid Pincode Number !');
        e.preventDefault();
        }
      });

        function pincode_valid(pincode_number){
            var expression =  /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
            if(expression.test(pincode_number)){
              return true;
            }else{
              return false;
            }
        }


      $('#in_present_city').keyup(function(){
      var intRegex = /^\d+$/;
      var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
      

      var str = $(this).val();
      if(intRegex.test(str) || floatRegex.test(str)) {
         alert('Please Inter input character for your Present City Name ');
      }

      //spacial character repalce
      re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
      var isSplChar = re.test(str);
      if(isSplChar)
      {
        var no_spl_char = str.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
        $(this).val(no_spl_char);
      }
      });

      $('#in_permanent_address_line1').keyup(function(e)
      {
        var permanent_address =  $("#in_permanent_address_line1").val();
             
        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(permanent_address);
        if(isSplChar)
        {
          var no_spl_char = permanent_address.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
      });

      $('#in_permanent_address_line2').keyup(function(e)
      {
        var permanent_address =  $("#in_permanent_address_line2").val();
             
        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(permanent_address);
        if(isSplChar)
        {
          var no_spl_char = permanent_address.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
      });

      $('#in_permanent_address_line2').keyup(function(e)
      {
        var permanent_address =  $("#in_permanent_address_line2").val();
             
        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(permanent_address);
        if(isSplChar)
        {
          var no_spl_char = permanent_address.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
      });

      $('#in_permanent_address_landmark').keyup(function(e)
      {
        var permanent_address =  $("#in_permanent_address_landmark").val();
             
        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(permanent_address);
        if(isSplChar)
        {
          var no_spl_char = permanent_address.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
      });


      $("#in_permanent_pincode").blur(function(e){
        var pincode_number =  $("#in_permanent_pincode").val()
         if ($.trim(pincode_number).length == 0 || $("#in_permanent_pincode").val()=="" || $("#in_permanent_pincode").val()==""){
          alert('Parmanent Pincode Number fields are mandatory,Try again');
          e.preventDefault();
          }
          if (pincode_valid(pincode_number)) {
          
             if(pincode_number.length !=6){
              alert('Enter 6 digit Parmanent Pincode number!');
            }
            else{
              alert('Nice!! your Parmanent Pincode Number is valid, now you can continue..');
            }
          }
        else {
        alert('Invalid Parmanent Pincode Number !');
        e.preventDefault();
        }
      });

        function pincode_valid(pincode_number){
            var expression =  /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
            if(expression.test(pincode_number)){
              return true;
            }else{
              return false;
            }
        }



      $('#in_permanent_city').keyup(function(){
        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
        

        var str = $(this).val();
        if(intRegex.test(str) || floatRegex.test(str)) {
           alert('Please Inter input character for your Permanent City Name ');
        }

        //spacial character repalce
        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(str);
        if(isSplChar)
        {
          var no_spl_char = str.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
      });

      

     


      



      /*$('#save').on('click',function(){
      var doc_name=[];
      var doc_type=[];
      var doc_number=[];

      $('.doc_name').each(function(){
        doc_name.push($(this).text());
      });
      $('.doc_type').each(function(){
        doc_type.push($(this).text());
      });
      $('.doc_number').each(function(){
        doc_number.push($(this).text());
      });

      /*var base_url = 'http://localhost/pcc/public';
      $.ajax({
        url: base_url+'/applicationSave',
        method:"POST",
        data:{doc_name:doc_name,doc_type:doc_type,doc_number:doc_number},
        success:function(data){
          console.log(data);
        }
      });

      });*/
