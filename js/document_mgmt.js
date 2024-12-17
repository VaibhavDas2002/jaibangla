
$('#scheme_type').on('change',function(){
    //alert("Hello");
    var scheme_type_Id = $(this).val();
    $('#doc_mand').val({});
    $('#doc_mand').trigger('change');
    $('#doc_opt').val({});
    $('#doc_opt').trigger('change');;
    if(scheme_type_Id){
      $.ajax({
        url:'ajaxschemeChnageRequest/{id}',
        type:"get",
        data:'scheme_type_Id='+ scheme_type_Id,
        dataType: 'json',
        contentType: "application/json; charset=utf-8",
        success:function(data){
        // console.log(data);
         $('#schemes_name').empty();
        $('#schemes_name').append("<option value=''> --Select -- </option>");
        $.each(data,function(index,schemeObj){
          $('#schemes_name').append('<option value="'+schemeObj.id+'">'+schemeObj.scheme_name+'</option>')
        });
        }
      });
    }
  });


$('#schemes_name').on('change',function(){
    var schemes_name = $(this).val();
    
    if(schemes_name){
      $.ajax({
        url:'ajaxschemenameRequest/{id}',
        type:"get",
        data:'schemes_name='+ schemes_name,
        dataType: 'json',
        contentType: "application/json; charset=utf-8",
        success:function(data){
          console.log(data[0].doc_list_man);
          console.log(data[0].doc_list_opt);
        
          var man_docs=JSON.parse(data[0].doc_list_man);
          $('#doc_mand').val(man_docs);
          $('#doc_mand').trigger('change');
          var opt_docs=JSON.parse(data[0].doc_list_opt);
          $('#doc_opt').val(opt_docs);
          $('#doc_opt').trigger('change');
          var man_docs_group=JSON.parse(data[0].doc_list_man_group);
          $('#doc_group').val(man_docs_group);
          $('#doc_group').trigger('change');
        }
      });
    }
  });

