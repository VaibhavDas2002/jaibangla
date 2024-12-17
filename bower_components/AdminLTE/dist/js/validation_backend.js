$('#firstname').keyup(function(){
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

  $('#lastname').keyup(function(){
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

  $('#middlename').keyup(function(){
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


  $('#address').keyup(function(e)
      {
        var present_address =  $("#address").val();
             
        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(present_address);
        if(isSplChar)
        {
          var no_spl_char = present_address.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
  });

    $("#zip").blur(function(e){
        var pincode_number =  $("#zip").val()
      
         if ($.trim(pincode_number).length == 0 || $("#zip").val()==""){
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


     $("#age").blur(function(e){
        var age =  $("#age").val()
      
         if ($.trim(age).length == 0 || $("#age").val()==""){
          alert('Age Digit fields are mandatory,Try again');
          e.preventDefault();
          }
        else {
        alert('Invalid Age Number !');
        e.preventDefault();
        }
      });

      function age_valid(age){
            var expression =  /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
            if(expression.test(age)){
              return true;
            }else{
              return false;
            }
        } 

   // $('#username').keyup(function(){
   //      var intRegex = /^\d+$/;
   //      var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
   //      var str = $(this).val();

   //       if(intRegex.test(str) || floatRegex.test(str)) {
   //         alert('Please Inter input character for your User Name ');
   //      }

   //      re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
   //      var isSplChar = re.test(str);
   //      if(isSplChar)
   //      {
   //        var no_spl_char = str.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
   //        $(this).val(no_spl_char);
   //      }
   //  }); 


    $('#email').blur(function(e) {
 
      var sender_email = $(this).val();
      // Check if the Fields are empty or not.
      if ($.trim(sender_email).length == 0 || $("#email").val()=="" || $("#email").val()=="") {
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


      $('#name').keyup(function(){
        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
        var str = $(this).val();

         if(intRegex.test(str) || floatRegex.test(str)) {
           alert('Please Inter input character for your Name ');
        }

        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(str);
        if(isSplChar)
        {
          var no_spl_char = str.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
      });  

      $('#dept_name').keyup(function(){
        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
        var str = $(this).val();

         if(intRegex.test(str) || floatRegex.test(str)) {
           alert('Please Inter input character for Deptertment Name ');
        }

        re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
        var isSplChar = re.test(str);
        if(isSplChar)
        {
          var no_spl_char = str.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
          $(this).val(no_spl_char);
        }
      });


       $("#state_code").blur(function(e){
        var statecode_number =  $("#state_code").val()
      
         if ($.trim(statecode_number).length == 0 || $("#state_code").val()==""){
          alert('State code Number fields are mandatory,Try again');
          e.preventDefault();
          }
          if (statecode_valid(statecode_number)) {
              alert('Please enter a number!');
            
          }
        else {
        alert('Invalid State code Number !');
        e.preventDefault();
        }
     
    });

    function statecode_valid(statecode_number){
          var expression =  /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
          if(expression.test(statecode_number)){
            return true;
          }else{
            return false;
          }
    }

  $('#statename').keyup(function(){
          var intRegex = /^\d+$/;
          var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
          var str = $(this).val();

           if(intRegex.test(str) || floatRegex.test(str)) {
             alert('Please Inter input character for your State Name ');
          }

          re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
          var isSplChar = re.test(str);
          if(isSplChar)
          {
            var no_spl_char = str.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
            $(this).val(no_spl_char);
          }
  });


  $('#sms_reason').keyup(function(){
          var intRegex = /^\d+$/;
          var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
          var str = $(this).val();

           if(intRegex.test(str) || floatRegex.test(str)) {
             alert('Please Inter input character for your Sms Message ');
          }

          re = /[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi;
          var isSplChar = re.test(str);
          if(isSplChar)
          {
            var no_spl_char = str.replace(/[`~!@#$%^&*()_|+\=?;:'".<>\{\}]/gi, '');
            $(this).val(no_spl_char);
          }
  });


      

