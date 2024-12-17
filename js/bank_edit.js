$('.js_rural_urban').change(function() {

        loadBlockUlb(this);
  });


function loadBlockUlb(element) {
  
  var dist_code = document.getElementById('dist_code').value;
  $('.js_block_ulb').empty().append('<option value="-1">--Select Block/Municipality</option>');
  
  loadItems10(dist_code, element, '../public/api/ruralurban/', '.js_block_ulb');
}  



function loadItems10(dist_code, element, path, selectInputClass) {
  var selectedVal = $(element).val();
  //var service_category = $(service_category).val();
 // console.log(service_category);
  //console.log(service_category);
 
  if (selectedVal == -1) {
    return;
  }
  
  $.ajax({
  type: 'GET',
  url: path + selectedVal +'/'+dist_code,

  success: function (datas) {
    if (!datas || datas.length === 0) {
      //alert("sucess with 0 data");
       return;
    }
   //alert('success url:'paths);
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        value: datas[i].code,

        text: datas[i].name
    }));
    }
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}