
/*function loadItems(element, path, selectInputClass) {
  var selectedVal = $(element).val();

  // select all
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  
  type: 'GET',
  data: {'id' : selectedVal}, 
  url: path + selectedVal,
  dataType: 'json',
  success: function (data) {
    console.log(data);
    alert('success');


    if (!datas || datas.length === 0) {
       return;
    }
   
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        value: datas[i].id,
        text: datas[i].name
    }));
    }

  },
  error: function (ex) {
     alert("hello");
  }
  });
}*/
function loadCountAjax(element,reportlevel1_data,reportlevel2_data,reportlevel2d_data,
    reportdistrict_data,path,selectInputClass) {
  
  var selectedVal = $(element).val();
 
 
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  type: 'POST',
  url: path +selectedVal,
  data:{
    'reportlevel1_data':reportlevel1_data,
    'reportlevel2_data':reportlevel2_data,
    'reportlevel2d_data':reportlevel2d_data,
    //'reportlevel3_data':reportlevel3_data,
    'reportdistrict_data':reportdistrict_data,
  },
  headers: {
        'X-CSRF-TOKEN': $('#token').val()
    },

  success: function (datas) {
    if (!datas || datas.length === 0) {
      //alert("sucess with 0 data");
       return;
    }
   // alert('succeess');   
    $(selectInputClass).append(
       datas.count,
      );
   
   
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}


function loadItems2d(element,reportlevel1_data,path,selectInputClass) {
  var selectedVal = $(element).val();
  //var service_category = $(service_category).val();
 // console.log(service_category);
  //console.log(service_category);
 
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  type: 'GET',
  url: path + selectedVal +'/'+reportlevel1_data ,
   //url: '{{ route('custom_name') }}',
   //data: selectedVal,

  success: function (datas) {
    if (!datas || datas.length === 0) {
      // alert("sucess with 0 data");
       return;
    }
   // alert('success with non zero data');
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        value: datas[i].id,
        text: datas[i].name
    }));
    }
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}

function loadPostingPlaceajax(element, path, selectInputClass) {
  var selectedVal = $(element).val();
  //var service_category = $(service_category).val();
 // console.log(service_category);
  //console.log(service_category);
 
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  type: 'GET',
  url: path + selectedVal ,
   //url: '{{ route('custom_name') }}',
   //data: selectedVal,

  success: function (datas) {
    if (!datas || datas.length === 0) {
      //alert("sucess with 0 data");
       return;
    }
   //alert('success url:'paths);
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        value: datas[i].code+':'+datas[i].name,

        text: datas[i].name
    }));
    }
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}

function loadreportafterlevel4ajax(element,reportlevel1_data,reportlevel2_data,reportlevel3_data,path, selectInputClass1,selectInputClass2,
  selectInputClass3,selectInputClass4,selectInputClass5,selectInputClass6,selectInputClass7) {
  
  var selectedVal = $(element).val();
 
 
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  type: 'POST',
  url: path + selectedVal,
  data:{
    'level1':reportlevel1_data,
    'level2':reportlevel2_data,
    'level3':reportlevel3_data,
    'level4':$(element).val(),
  },
  headers: {
        'X-CSRF-TOKEN': $('#token').val()
    },

  success: function (datas) {
    if (!datas || datas.length === 0) {
      //alert("sucess with 0 data");
       return;
    }
   // alert('succeess');   
    $(selectInputClass1).append(
       datas.applications_received,
      );
    $(selectInputClass2).append(
       datas.applications_pending_for_verification,
      );
    $(selectInputClass3).append(
       datas.applications_pending_for_approval,
      );
    $(selectInputClass4).append(
       datas.applications_approved,
      );
    $(selectInputClass5).append(
       datas.applications_verified,
      );
    $(selectInputClass6).append(
       datas.applications_rejected,
      );
    $(selectInputClass7).append(
       datas.applications_rejected_at_approval,
      );

   
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}

function loadreportafterlevel3ajax(element,reportlevel1_data,reportlevel2_data, path, selectInputClass1,selectInputClass2,
  selectInputClass3,selectInputClass4,selectInputClass5,selectInputClass6,selectInputClass7) {
  
  var selectedVal = $(element).val();
 
 
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  type: 'POST',
  url: path + selectedVal,
  data:{
    'level1':reportlevel1_data,
    'level2':reportlevel2_data,
    'level3':$(element).val(),
  },
  headers: {
        'X-CSRF-TOKEN': $('#token').val()
    },

  success: function (datas) {
    if (!datas || datas.length === 0) {
      //alert("sucess with 0 data");
       return;
    }
   // alert('succeess');   
    $(selectInputClass1).append(
       datas.applications_received,
      );
    $(selectInputClass2).append(
       datas.applications_pending_for_verification,
      );
    $(selectInputClass3).append(
       datas.applications_pending_for_approval,
      );
    $(selectInputClass4).append(
       datas.applications_approved,
      );
    $(selectInputClass5).append(
       datas.applications_verified,
      );
    $(selectInputClass6).append(
       datas.applications_rejected,
      );
    $(selectInputClass7).append(
       datas.applications_rejected_at_approval,
      );
   
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}
function loadreportafterlevel2ajax(element,reportlevel1_data, path, selectInputClass1,selectInputClass2,
  selectInputClass3,selectInputClass4,selectInputClass5,selectInputClass6,selectInputClass7) {
  
  var selectedVal = $(element).val();
 
 
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  type: 'POST',
  url: path + selectedVal,
  data:{
    'level1':reportlevel1_data,
    'level2':$(element).val(),
  },
  headers: {
        'X-CSRF-TOKEN': $('#token').val()
    },

  success: function (datas) {
    if (!datas || datas.length === 0) {
      //alert("sucess with 0 data");
       return;
    }
   // alert('succeess');   
    $(selectInputClass1).append(
       datas.applications_received,
      );
    $(selectInputClass2).append(
       datas.applications_pending_for_verification,
      );
    $(selectInputClass3).append(
       datas.applications_pending_for_approval,
      );
    $(selectInputClass4).append(
       datas.applications_approved,
      );
    $(selectInputClass5).append(
       datas.applications_verified,
      );
    $(selectInputClass6).append(
       datas.applications_rejected,
      );
     $(selectInputClass7).append(
       datas.applications_rejected_at_approval,
      );
   
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}
function loadItems2_2d(element,reportlevel1_data,reportlevel2_data,reportlevel2d_data,path, selectInputClass) {
  //var selectedVal = $(element).val();
  var selectedVal = $(element).children(":selected").val();
  //var service_category = $(service_category).val();
 // console.log(service_category);
  //console.log(service_category);
  
  if (selectedVal == -1) {
    return;
  }
$.ajax({
  type: 'POST',
  url: path + selectedVal,
  data:{
    'reportlevel1_data':reportlevel1_data,
    'reportlevel2_data':reportlevel2_data,
    'reportlevel2d_data':reportlevel2d_data,
    'reportlevel3_data':$(element).val(),
  },
  headers: {
        'X-CSRF-TOKEN': $('#token').val()
    },
  // $.ajax({
  // type: 'GET',
  // url: path + reportlevel1_data +'/'+ reportlevel2_data +'/'+reportlevel2d_data+'/'+ selectedVal,
   //url: '{{ route('custom_name') }}',
   //data: selectedVal,

  success: function (datas) {
    if (!datas || datas.length === 0) {
      //alert("sucess with 0 data");
       return;
    }
   // alert('succeess');
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        //value: datas[i].name,
        value: datas[i].id,
        text: datas[i].name,
        id: datas[i].id
    }));
    }
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });

}
function loadItems2_2(element,reportlevel1_data,reportlevel2_data, path, selectInputClass) {
  //var selectedVal = $(element).val();
  var selectedVal = $(element).children(":selected").val();
  //var service_category = $(service_category).val();
 // console.log(service_category);
  //console.log(service_category);
  
  if (selectedVal == -1) {
    return;
  }
$.ajax({
  type: 'POST',
  url: path + selectedVal,
  data:{
    'reportlevel1_data':reportlevel1_data,
    'reportlevel2_data':reportlevel2_data,
   
    'reportlevel3_data':$(element).val(),
  },
  headers: {
        'X-CSRF-TOKEN': $('#token').val()
    },
  // $.ajax({
  // type: 'GET',
  // url: path + reportlevel1_data +'/'+ reportlevel2_data +'/'+ selectedVal,
   //url: '{{ route('custom_name') }}',
   //data: selectedVal,

  success: function (datas) {
    if (!datas || datas.length === 0) {
      //alert("sucess with 0 data");
       return;
    }
   // alert('succeess');
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        //value: datas[i].name,
        value: datas[i].id,
        text: datas[i].name,
        id: datas[i].id
    }));
    }
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });

}


function loadItems1_1(element, path, selectInputClass) {
  var selectedVal = $(element).val();
  //var service_category = $(service_category).val();
 // console.log(service_category);
  //console.log(service_category);
 
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  type: 'GET',
  url: path + selectedVal ,
   //url: '{{ route('custom_name') }}',
   //data: selectedVal,

  success: function (datas) {
    if (!datas || datas.length === 0) {
      // alert("sucess with 0 data");
       return;
    }
   // alert('success with non zero data');
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        value: datas[i],
        text: datas[i]
    }));
    }
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}
function loadItems3(element, path, selectInputClass) {
  var selectedVal = $(element).val();
  //var service_category = $(service_category).val();
 // console.log(service_category);
  //console.log(service_category);
 
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  type: 'GET',
  url: path + selectedVal ,
   //url: '{{ route('custom_name') }}',
   //data: selectedVal,

  success: function (datas) {
    if (!datas || datas.length === 0) {
      //alert("sucess with 0 data");
       return;
    }
   //alert('success url:'paths);
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        value: datas[i].place,
        text: datas[i].place
    }));
    }
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}

function loadItems2(element,service_category,major_programme_head, path, selectInputClass) {
  //var selectedVal = $(element).val();
  var selectedVal = $(element).children(":selected").attr("id");
  //var service_category = $(service_category).val();
 // console.log(service_category);
  //console.log(service_category);
 
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  type: 'GET',
  url: path + selectedVal +'/'+ service_category + '/' + major_programme_head,
   //url: '{{ route('custom_name') }}',
   //data: selectedVal,

  success: function (datas) {
    if (!datas || datas.length === 0) {
      //alert("sucess with 0 data");
       return;
    }
   // alert('succeess');
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        //value: datas[i].name,
        value: datas[i].id,
        text: datas[i].name,
        id: datas[i].id
    }));
    }
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}

function loadItems1_lot(element,reportlevel2d_data ,path, selectInputClass) {
  var selectedVal = $(element).val();
  //var service_category = $(service_category).val();
 // console.log(service_category);
  //console.log(service_category);
 
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  // type: 'GET',
  // url: path + selectedVal+ reportlevel2d_data,
 
  type: 'POST',
  url: path + selectedVal,
  data:{
    'level_name':reportlevel2d_data,
    'id':selectedVal
   
  },
  headers: {
        'X-CSRF-TOKEN': $('#token').val()
    },
  success: function (datas) {
    if (!datas || datas.length === 0) {
      // alert("sucess with 0 data");
       return;
    }
   // alert('success with non zero data');
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        value: datas[i].id,
        text: datas[i].name
    }));
    }
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}

function loadItemsnew(element,reportlevel2d_data, path, selectInputClass) {
  var selectedVal = $(element).val();
  //var service_category = $(service_category).val();
 // console.log(service_category);
  //console.log(service_category);
 
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  type: 'GET',
  url: path + selectedVal + '/' + reportlevel2d_data,
   //url: '{{ route('custom_name') }}',
   //data: selectedVal,

  success: function (datas) {
    if (!datas || datas.length === 0) { 
      // alert("sucess with 0 data");
       return;
    }
   // alert('success with non zero data');
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        value: datas[i].id,
        text: datas[i].name
    }));
    }
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}



function loadItems1(element, path, selectInputClass) {
  var selectedVal = $(element).val();
  //var service_category = $(service_category).val();
 // console.log(service_category);
  //console.log(service_category);
 
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  type: 'GET',
  url: path + selectedVal ,
   //url: '{{ route('custom_name') }}',
   //data: selectedVal,

  success: function (datas) {
    if (!datas || datas.length === 0) {
      // alert("sucess with 0 data");
       return;
    }
   // alert('success with non zero data');
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        value: datas[i].id,
        text: datas[i].name
    }));
    }
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}




function loadItems(element,service_category, path, selectInputClass) {
  var selectedVal = $(element).val();
  //var service_category = $(service_category).val();
 // console.log(service_category);
  //console.log(service_category);
 
  if (selectedVal == -1) {
    return;
  }

  //alert(path + selectedVal +'/'+ service_category);

  $.ajax({
  type: 'GET',
  url: path + selectedVal +'/'+ service_category,
   //url: '{{ route('custom_name') }}',
   //data: selectedVal,

  success: function (datas) {
    if (!datas || datas.length === 0) {
      //alert("sucess with 0 data");
       return;
    }
   //alert('success url:'paths);
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        //value: datas[i].name,
        value: datas[i].id,
        text: datas[i].name,
        id: datas[i].id
    }));
    }
  },
  error: function (ex) {
     //alert('error url:'paths);
  }
  });
}


function loadLocalBody(element,service_category) {  
  $('.js-localbody').empty().append('<option value="">--  Select  --</option>');  
  loadItems(element,service_category, '../api/localbody/', '.js-localbody');
}
/*******SD*********/
function loadProgrammeHead(element,service_category) {
  $('.js-programme_head').empty().append('<option value="">Please select your Programme Head</option>');
  $('.js-designation_list').empty().append('<option value="">Please select your Designation</option>');
  //$('.js-cities').empty().append('<option value="-1">Please select your city</option>');
  //$id=$('#contractual_under_nhm').val();
  //loadItems(element, '../api/parameters/', '.js-parameters');
 
  //console.log("inside programmehead"+service_category);
  loadItems(element,service_category, '../api/programmehead/', '.js-programme_head');
}

//sss
function loadProgrammeHeadDesignation(element,service_category) {
  $('.js-programme_head_designation').empty().append('<option value="">Please select your Programme Head</option>');
  //$('.js-designation_list').empty().append('<option value="-1">Please select your Designation</option>');
  //$('.js-cities').empty().append('<option value="-1">Please select your city</option>');
  //$id=$('#contractual_under_nhm').val();
  //loadItems(element, '../api/parameters/', '.js-parameters');
 
  //console.log("inside programmehead"+service_category);
  loadItems(element,service_category_designation, '../api/programmeheadDesignation/', '.js-programme_head_designation');
}

//sss
function loadProgrammeHeadDesignationEdit(element,service_category) {
  $('.js-programme_head_designation_edit').empty().append('<option value="">Please select your Programme Head</option>');
  //$('.js-designation_list').empty().append('<option value="-1">Please select your Designation</option>');
  //$('.js-cities').empty().append('<option value="-1">Please select your city</option>');
  //$id=$('#contractual_under_nhm').val();
  //loadItems(element, '../api/parameters/', '.js-parameters');
 
  //console.log("inside programmehead"+service_category);
  loadItems(element,service_category_designation_edit, '../../api/programmeheadDesignationEdit/', '.js-programme_head_designation_edit');
}

/****OLD  designationlist****/
// function loadDesignationList(element,service_category,major_programme_head) {
//   $('.js-designation_list').empty().append('<option value="">Please select your Designation</option>');
//   //$('.js-cities').empty().append('<option value="-1">Please select your city</option>');
//   //$id=$('#contractual_under_nhm').val();
//   //loadItems(element, '../api/parameters/', '.js-parameters');
//   loadItems2(element,service_category,major_programme_head, '../public/api/programmehead/', '.js-designation_list');
// }
/*****************************/
function loadDesignationList(element,service_category,major_programme_head) {
  $('.js-designation_list').empty().append('<option value="">Please select your Designation</option>');
  //$('.js-cities').empty().append('<option value="-1">Please select your city</option>');
  //$id=$('#contractual_under_nhm').val();
  //loadItems(element, '../api/parameters/', '.js-parameters');

  loadItems2(element,service_category,major_programme_head, '../api/designationlist/', '.js-designation_list');
}

/************************Posting Level on change load posting place****************/
function loadPostingPlace(element) {
  $('.js-posting_place').empty().append('<option value="">Please select Posting Place</option>');
  loadPostingPlaceajax(element, '../api/postingplace/', '.js-posting_place');
}
/***********************************end********************************************/



function loadStates(element) {
  // $('.js-states').empty().append('<option value="-1">Please select your state</option>');
  // $('.js-cities').empty().append('<option value="-1">Please select your city</option>');
  loadItems(element, '../api/states/', '.js-states');
}

function loadCities(element) {
  // $('.js-cities').empty().append('<option value="-1">Please select your city</option>');
  loadItems(element, '../api/cities/', '.js-cities');
}

function registerEvents() {
  $('.js-country').change(function() {
    // $('.js-states').empty().append('<option value="-1">Please select your state</option>');
    // $('.js-cities').empty().append('<option value="-1">Please select your city</option>');
    loadStates(this);
  });

  $('.js-states').change(function() {

    // $('.js-cities').empty().append('<option value="-1">Please select your city</option>');
    loadCities(this);
  });
/********************SD***************/
  $('.js-urban').change(function() {
     //alert("Hello! I am an alert box!!");    
    district_code= $('#dist_code').val();
    loadLocalBody(this,district_code);
    console.log('on change service category:'+district_code);   
   
  });

  $('.js-service_category').change(function() {
     //alert("Hello! I am an alert box!!");
    loadMajorProgrammeHead(this);
     service_category= $('.js-service_category').val();
    console.log('on change service category:'+service_category);
    
    //callfields(this,service_category);
   /* $('.js-major_programme_head').change(function(){
      // alert("Hello! I am another alert box!!");
      console.log("inside service category:"+service_category);
       loadProgrammeHead(this,service_category);
    });*/
   
  });

   $('.js-service_category_designation').change(function() {
     //alert("Hello! I am an alert box!!");
    loadMajorProgrammeHeadDesignation(this);
     service_category_designation= $('.js-service_category_designation').val();
    console.log('on change service category designation:'+service_category_designation);
    
    //callfields(this,service_category);
   /* $('.js-major_programme_head').change(function(){
      // alert("Hello! I am another alert box!!");
      console.log("inside service category:"+service_category);
       loadProgrammeHead(this,service_category);
    });*/
   
  });
  
   $('.js-service_category_designation_edit').change(function() {
     //alert("Hello! I am an alert box!!");

     // $('.js-major_programme_head_designation').empty().append('<option value="-1">Please select Major Programme Head</option>');
 // $('.js-programme_head_designation').empty().append('<option value="-1">Please select your Programme Head</option>');
    loadMajorProgrammeHeadDesignationEdit(this);
     service_category_designation_edit= $('.js-service_category_designation_edit').val();
    
   // console.log('on change service category designation edit:'+service_category_designation_edit);
    
    //callfields(this,service_category);
   /* $('.js-major_programme_head').change(function(){
      // alert("Hello! I am another alert box!!");
      console.log("inside service category:"+service_category);
       loadProgrammeHead(this,service_category);
    });*/
   
  });

   $('.js-major_programme_head').change(function() {
   
    major_programme_head = $('.js-major_programme_head').val();
    loadProgrammeHead(this,service_category);
  });

    $('.js-major_programme_head_designation').change(function() {
   
    major_programme_head_designation = $('.js-major_programme_head_designation').val();
    loadProgrammeHeadDesignation(this,service_category_designation);
  });

    $('.js-major_programme_head_designation_edit').change(function() {
   
    major_programme_head_designation_edit = $('.js-major_programme_head_designation_edit').val();
    loadProgrammeHeadDesignationEdit(this,service_category_designation_edit);
  });
/********************************posting level on change posting place*******/
  $('.js-posting_level').change(function() {
   
  $('.js-posting_place').empty().append('<option value="">Please select Posting Place</option>');
  loadPostingPlace(this);
  });
/******************************end*****************************************/

  $('.js-programme_head').change(function() {
    // alert("Hello! I am an alert box!!");
    loadDesignationList(this,service_category,major_programme_head);
  });
/************Code 14-11-2019*/
  $('#maping_level').change(function() {
     if(document.getElementById("maping_level").value=="State HQ"){
        $('#divDistrict').hide();
        $('#divUrbanCode').hide();
        $('#divBodyCode').hide();   
     }else if(document.getElementById("maping_level").value=="District HQ"){
        $('#divDistrict').show();
        $('#divUrbanCode').show();
        $('#divBodyCode').show(); 
        $('#divUrbanCode').hide();
        $('#divBodyCode').hide();   
     }else if(document.getElementById("maping_level").value=="Block"){
        $('#divDistrict').show();
        $('#divUrbanCode').show();
        $('#divBodyCode').show();           
     }       
   
  });
/******change on 11-02-2020- creating drilldown in dashboard report*****/
$('.js-reportlevel1').change(function() {
    reportdistrict_data=0;
    reportlevel2_data=0;
    reportlevel2d_data=0;
    //alert("level1 change");
    // console.log("HI reportlevel2d_data:"+reportlevel2d_data);
    reportlevel1_data=$('.js-reportlevel1').val();
   // alert(reportlevel1_data);
    $('.js-reportlevel2').empty().append('<option value="">Please select Field</option>');
    //$('.js-reportlevel3').empty().append('<option value="">Please select Field</option>');
   // $('.js-reportlevel4').empty().append('<option value="">Please select Field</option>');   
   // $('.js-reportlevel5').empty().append('<option value="">Please select Field</option>');  
    

  });
$('.js-year-lot').change(function() {
 
    js_year_lot=$('.js-year-lot').val();
   
   
    

  });
$('.js-month-lot').change(function() {
 
    js_month_lot=$('.js-month-lot').val();
   
   
    

  });
/*************************************************************************/
$('.js-reportlevel2').change(function() {
    console.log("HI");
     reportlevel2_data= $('.js-reportlevel2').val();
     
     loadCount(this,reportlevel1_data,reportlevel2_data,reportlevel2d_data,reportdistrict_data);
    //$('.js-reportlevel4').empty().append('<option value="">Please select Field</option>');
    //loadreportafterlevel2(this,reportlevel1_data);
       //loadlevel4(this);
  });
/******change on 11-02-2020- creating drilldown in dashboard report*****/
$('.js-reportdistrict').change(function() {
    //console.log("HI");
     reportdistrict_data= $('.js-reportdistrict').val();
     //alert($('.js-reportdistrict').val());
     loadlevel2(this,reportlevel2d_data);
    //$('.js-reportlevel4').empty().append('<option value="">Please select Field</option>');
    //loadreportafterlevel2(this,reportlevel1_data);
       //loadlevel4(this);
  });


$('.js-reportlevel2d').change(function() {
     reportlevel2d_data= $('.js-reportlevel2d').val();
     console.log("level2d called"+reportlevel2d_data);
     if(reportlevel2d_data=="All" ){
      
       loadCount(this,reportlevel1_data,reportlevel2_data,reportlevel2d_data,reportdistrict_data);
     }
    // console.log("HI");
   // $('.js-reportlevel4').empty().append('<option value="">Please select Field</option>');
   // $('.js-reportlevel3').empty().append('<option value="">Please select Field</option>');
   // $('.js-reportlevel5').empty().append('<option value="">Please select Field</option>');
    $('.js-reportlevel2').empty().append('<option value="">Please select Field</option>');
    loadlevel2d(this,reportlevel1_data);
    //loadreportafterlevel2d(this,reportlevel1_data);
       //loadlevel4(this);
  });

// $('.js-reportlevel3').change(function() {
//     reportlevel3_data= $('.js-reportlevel3').val();
//     //console.log(reportlevel3_data);
//     $('.js-reportlevel4').empty().append('<option value="">Please select Field</option>');
//      //loadreportafterlevel3(this,reportlevel1_data,reportlevel2_data);
//     //loadlevel4(this);
//   });
/**********************************************************************/
$('.js-reportlevel4').change(function() {
   
    //console.log(reportlevel3_data);
    //$('.js-reportlevel4').empty().append('<option value="">Please select Field</option>');
    loadreportafterlevel4(this,reportlevel1_data,reportlevel2_data,reportlevel3_data);
    
  });
// $('.js-report-form').submit(function() {
    
//     loadreport(this);
//   });
/*************************health facility****************************/
$('.js-district_healthfacility_edit').change(function(){

 $('.js-location_healthfacility_edit').empty().append('<option value="">Please select Field</option>');
 loadlocationHealthFacilityEdit(this);
});

$('.js-district_healthfacility_create').change(function(){

 $('.js-location_healthfacility_create').empty().append('<option value="">Please select Field</option>');
 loadlocationHealthFacilityCreate(this);
});
/*************************health facility end****************************/
}

/************ENd Code 14-11-2019*/
function loadMajorProgrammeHead(element){
  $('.js-major_programme_head').empty().append('<option value="">Please select Major Programme Head</option>');
  $('.js-programme_head').empty().append('<option value="">Please select your Programme Head</option>');
  $('.js-designation_list').empty().append('<option value="">Please select your Designation</option>');
   loadItems1(element, '../api/majorprogrammehead/', '.js-major_programme_head');
   //callfields(service_category);
}
////ssss
function loadMajorProgrammeHeadDesignation(element){
  $('.js-major_programme_head_designation').empty().append('<option value="">Please select Major Programme Head</option>');
  $('.js-programme_head_designation').empty().append('<option value="">Please select your Programme Head</option>');
  //$('.js-designation_list').empty().append('<option value="-1">Please select your Designation</option>');
   loadItems1(element, '../api/majorprogrammeheadDesignation/', '.js-major_programme_head_designation');
   //callfields(service_category);
}

////ssss
function loadMajorProgrammeHeadDesignationEdit(element){
 $('.js-major_programme_head_designation_edit').empty().append('<option value="">Please select Major Programme Head</option>');
 $('.js-programme_head_designation_edit').empty().append('<option value="">Please select your Programme Head</option>');
  //$('.js-designation_list').empty().append('<option value="-1">Please select your Designation</option>');
   loadItems1(element, '../../api/majorprogrammeheadDesignationEdit/', '.js-major_programme_head_designation_edit');
   //callfields(service_category);
}

function callfields(element,service_category){
  //var x=$(element).val();
   // $('.js-major_programme_head').empty().append('<option value="-1">Please select Major Programme Head</option>');
   // $('.js-programme_head').empty().append('<option value="-1">Please select your Programme Head</option>');
   // $('.js-designation_list').empty().append('<option value="-1">Please select your Designation</option>');
    console.log('inside callfields:'+service_category);
    //console.log('inside callfields x:'+x);
   $('.js-major_programme_head').change(function() {
    // alert("Hello! I am an alert box!!");
     console.log('inside callfields again:'+service_category);
     // console.log('inside callfields x again:'+x);
    //loadProgrammeHead(('.js-major_programme_head'),service_category);
    loadProgrammeHead(this,service_category);

    //service_category=null;
  });
}

function loadlevel2(element,reportlevel2d_data){
   $('.js-reportlevel2').empty().append('<option value="">Please select Field</option>');
  //loadItems1_lot(element,reportlevel2d_data,'../public/api/loadlevel2lot/', '.js-reportlevel2');
   loadItemsnew(element,reportlevel2d_data,'../api/loadlevel2lot/', '.js-reportlevel2');
}

function loadCount(element,reportlevel1_data,reportlevel2_data,reportlevel2d_data,reportdistrict_data){
   $('.js-data_to_process').empty().append('0');
  
   loadCountAjax(element,reportlevel1_data,reportlevel2_data,reportlevel2d_data,
    reportdistrict_data,'../api/loadcountlot/', '.js-data_to_process');
}
/*******sd drilldown in dashboard************/
function loadlevel2d(element,reportlevel1_data){
   $('.js-reportlevel2').empty().append('<option value="">Please select Field</option>');
  loadItems2d(element,reportlevel1_data, '../api/loadlevel2dlot/', '.js-reportlevel2');
  
}
/*******************************************/
function loadlevel3(element){
   $('.js-reportlevel2').empty().append('<option value="">Please select Field</option>'); 
    loadItems1_1(element, '../api/loadlevel3/', '.js-reportlevel3');
}

/*******sd drilldown in dashboard************/
function loadlevel4(element){
  console.log("level4 called"+reportlevel2d_data);
    if(reportlevel2d_data!=0){
      loadItems2_2d(element,reportlevel1_data,reportlevel2_data,reportlevel2d_data, '../api/loadlevel4d/', '.js-reportlevel4');
    }else{
      loadItems2_2(element,reportlevel1_data,reportlevel2_data, '../api/loadlevel4/', '.js-reportlevel4');
    }
    
}
/*******************************************/
function loadreportafterlevel2(element,reportlevel1_data) {  
  $('.js-reportapplicationssubmittedcount').empty().append('0');
  $('.js-reportpendingverificationcount').empty().append('0');
  $('.js-reportpendingapprovalcount').empty().append('0');
  $('.js-employeecodegeneratedcount').empty().append('0');
  $('.js-reportverifiedcount').empty().append('0');
  $('.js-reportrejectedcount').empty().append('0');
  $('.js-reportrejectedapprovalcount').empty().append('0');    
  loadreportafterlevel2ajax(element,reportlevel1_data,'../loadreportafterlevel2/','.js-reportapplicationssubmittedcount','.js-reportpendingverificationcount'
    ,'.js-reportpendingapprovalcount','.js-employeecodegeneratedcount','.js-reportverifiedcount','.js-reportrejectedcount','.js-reportrejectedapprovalcount');
}

function loadreportafterlevel3(element,reportlevel1_data,reportlevel2_data) {  
  $('.js-reportapplicationssubmittedcount').empty().append('0');
  $('.js-reportpendingverificationcount').empty().append('0');
  $('.js-reportpendingapprovalcount').empty().append('0');
  $('.js-employeecodegeneratedcount').empty().append('0');
  $('.js-reportverifiedcount').empty().append('0');
  $('.js-reportrejectedcount').empty().append('0');
  $('.js-reportrejectedapprovalcount').empty().append('0');    
  loadreportafterlevel3ajax(element,reportlevel1_data,reportlevel2_data,'../loadreportafterlevel3/','.js-reportapplicationssubmittedcount','.js-reportpendingverificationcount'
    ,'.js-reportpendingapprovalcount','.js-employeecodegeneratedcount','.js-reportverifiedcount','.js-reportrejectedcount','.js-reportrejectedapprovalcount');
}

function loadreportafterlevel4(element,reportlevel1_data,reportlevel2_data,reportlevel3_data) {  
  $('.js-reportapplicationssubmittedcount').empty().append('0');
  $('.js-reportpendingverificationcount').empty().append('0');
  $('.js-reportpendingapprovalcount').empty().append('0');
  $('.js-employeecodegeneratedcount').empty().append('0');
  $('.js-reportverifiedcount').empty().append('0');
  $('.js-reportrejectedcount').empty().append('0');
  $('.js-reportrejectedapprovalcount').empty().append('0');    
  loadreportafterlevel4ajax(element,reportlevel1_data,reportlevel2_data,reportlevel3_data,'../loadreportafterlevel4/','.js-reportapplicationssubmittedcount','.js-reportpendingverificationcount'
    ,'.js-reportpendingapprovalcount','.js-employeecodegeneratedcount','.js-reportverifiedcount','.js-reportrejectedcount','.js-reportrejectedapprovalcount');
}

/************************************health facility**********************************/
function loadlocationHealthFacilityEdit(element) {  

  $('.js-location_healthfacility_edit').empty().append('<option value="">Please select Field</option>');
   
  loadItems1(element,'../../../api/loadlocationHealthFacilityEdit/','.js-location_healthfacility_edit');
}//../../api/majorprogrammeheadDesignationEdit/

function loadlocationHealthFacilityCreate(element) {  

  $('.js-location_healthfacility_create').empty().append('<option value="">Please select Field</option>');
   
  loadItems1(element,'../../api/loadlocationHealthFacilityCreate/','.js-location_healthfacility_create');
}
/************************************health facility end**********************************/
$('.employee_list').select2({
    placeholder: "Select Employee...",
    minimumInputLength: 2,
    ajax: {
        url: 'find',
        dataType: 'json',
        data: function (params) {
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    }
});

function init() {
  registerEvents();
}

init();







$(document).ready(function(){   // Add Datepicker here    
  // $('.datepick').datepicker({
  //   autoclose: true,
  //   format: 'yyyy-mm-dd'
  // });
});


