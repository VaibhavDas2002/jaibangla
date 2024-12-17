
function loadDDLItems(element, path, selectInputClass) {
  var selectedVal = $(element).val();  
  // select all
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
  
  type: 'GET',  
  url: path + selectedVal,
  dataType: 'json',
  success: function (data) {
    console.log(data);
    if (!data || data.length === 0) {
       return;
    }else{

      $(selectInputClass).empty().append('<option value="-1">Please select Parent Level</option>');
    }
   
    for (var  i = 0; i < data.length; i++) {
      $(selectInputClass).append($('<option>', {
        value: data[i].id,
        text: data[i].name
    }),'</option>');
    }

  },
  error: function (ex) {
     alert("hello");
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
 
  if (selectedVal == -1) {
    return;
  }
  $.ajax({
  type: 'GET',
  url: path + selectedVal +'/'+ service_category,
  success: function (datas) {
    if (!datas || datas.length === 0) {
       return;
    }
    for (var  i = 0; i < datas.length; i++) {
      $(selectInputClass).append($('<option>', {
        value: datas[i].id,
        text: datas[i].name,
        id: datas[i].id
    }));
    }
  },
  error: function (ex) {
  }
  });
}

function loadwards(municipality, path, selectInputClass) {
  var selectedVal = municipality;
  if (selectedVal == -1) {
    return;
  }

  //alert(path + selectedVal +'/'+ service_category);

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
        //value: datas[i].name,
        value: datas[i].id,
        text: datas[i].name,
        id: datas[i].id
    }));
    }
  },
  error: function (ex) {
     alert('error url:');
  }
  });
}

function loadAssembly(element) {  
  
  $('.js-assembly').empty().append('<option value="">--  Select  --</option>');  
  loadItems1(element, '../api/assembly/', '.js-assembly');
}

function loadLocalBody(element,service_category) {  
  $('.js-localbody').empty().append('<option value="">--  Select  --</option>');  
  loadItems(element,service_category, '../api/localbody/', '.js-localbody');
}

function loadBlockSubdiv(element,service_category) { 
//alert("HI"); 
   ///rural_urban_var= $(element).val();
  $('.js-localbody').empty().append('<option value="">--  Select  --</option>');  
  loadItems(element,service_category, '../api/blocksubdiv/', '.js-localbody');
}



function loadGPWard(rural_urban,body_code) {  
  $('.js-gpward').empty().append('<option value="">--  Select  --</option>');   
  loadItems(rural_urban,body_code, '../api/gpward/', '.js-gpward');
}
/********sd change 17-03-2020 for verifier*********/
function loadGPWard_1(municipality) { 
  // alert('municipality',municipality); 
  $('.js-wards').empty().append('<option value="">--  Select  --</option>');   
  loadwards(municipality, '../api/loadwards/', '.js-wards');
}
/*******************end****************************/
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

  loadItems2(element,service_category,major_programme_head, '../designationlist/', '.js-designation_list');
}

/************************Posting Level on change load posting place****************/
function loadPostingPlace(element) {
  $('.js-posting_place').empty().append('<option value="">Please select Posting Place</option>');
  loadPostingPlaceajax(element, '../postingplace/', '.js-posting_place');
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

function loadNextLevel(element) {
  // $('.js-cities').empty().append('<option value="-1">Please select your city</option>');
  loadDDLItems(element, '../api/nextlevel/', '.js-nextlevel');
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

$('.js-district').change(function(event) { 

     //alert("Hello! I am an alert box!!");    
    district_code= $('.js-district').val();
    urban_code= $('.js-urban').val();

    loadAssembly(this);
    //loadLocalBody('.js-urban',district_code);
   // loadAssembly(urban_code,district_code);
    //console.log('on change service category:'+district_code);   
   
  });

  $('.js-urban').change(function() {
    
    //alert("Hello! I am an alert box!!");    
    district_code= $('.js-district').val();
    loadLocalBody(this,district_code);
    
   // console.log('on change service category:'+district_code);   
   
  });


  $('.js-block-subdiv').change(function() {
    
    //alert("Hello! I am an alert box!!");    
    district_code= $('.js-district').val();
    loadBlockSubdiv(this,district_code);
    
   // console.log('on change service category:'+district_code);   
   
  });
/******sd change 17-03-2020 for verifier*****/
  $('.js-municipality').change(function() {
    municipality=$('.js-municipality').val();  
    val = loadGPWard_1(municipality);
    // alert('municipality',municipality);
    console.log('on change municipality:'+municipality);  
    // console.log(js-wards); 
   
  });
  
/*****************end***********************/
  

  $('.js-localbody').change(function() {
    rural_urban=$('.js-urban').val();
    block_code= $('.js-localbody').val();    
    loadGPWard('.js-urban',block_code);

    console.log('on change service category:'+district_code);   
   
  });

  $('.js-scheme').change(function() {

    // $('.js-cities').empty().append('<option value="-1">Please select your city</option>');
     loadNextLevel(this);
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
 
    reportlevel2d_data=0;
    //alert("level1 change");
     console.log("HI reportlevel2d_data:"+reportlevel2d_data);
    reportlevel1_data=$('.js-reportlevel1').val();
    $('.js-reportlevel2').empty().append('<option value="">Please select Field</option>');
    $('.js-reportlevel3').empty().append('<option value="">Please select Field</option>');
    $('.js-reportlevel4').empty().append('<option value="">Please select Field</option>');   

    if(reportlevel1_data=="State" || reportlevel1_data=="District"){
       loadlevel2(this);
    $('.js-reportlevel4').empty().append('<option value="">Please select Field</option>');   
    }
   loadlevel3(this);

  });
/*************************************************************************/
$('.js-reportlevel2').change(function() {
    console.log("HI");
     reportlevel2_data= $('.js-reportlevel2').val();
     
    $('.js-reportlevel4').empty().append('<option value="">Please select Field</option>');
    loadreportafterlevel2(this,reportlevel1_data);
       //loadlevel4(this);
  });
/******change on 11-02-2020- creating drilldown in dashboard report*****/

$('.js-reportlevel2d').change(function() {
     reportlevel2d_data= $('.js-reportlevel2d').val();
     console.log("level2d called"+reportlevel2d_data);
     console.log("HI");
    $('.js-reportlevel4').empty().append('<option value="">Please select Field</option>');
    $('.js-reportlevel2').empty().append('<option value="">Please select Field</option>');
    loadlevel2d(this,reportlevel1_data);
    //loadreportafterlevel2d(this,reportlevel1_data);
       //loadlevel4(this);
  });

$('.js-reportlevel3').change(function() {
    reportlevel3_data= $('.js-reportlevel3').val();
    //console.log(reportlevel3_data);
    $('.js-reportlevel4').empty().append('<option value="">Please select Field</option>');
     loadreportafterlevel3(this,reportlevel1_data,reportlevel2_data);
    loadlevel4(this);
  });
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

function loadlevel2(element){
   $('.js-reportlevel2').empty().append('<option value="">Please select Field</option>');
  loadItems1(element, '../api/loadlevel2/', '.js-reportlevel2');
  
}
/*******sd drilldown in dashboard************/
function loadlevel2d(element,reportlevel1_data){
   $('.js-reportlevel2').empty().append('<option value="">Please select Field</option>');
  loadItems2d(element,reportlevel1_data, '../api/loadlevel2d/', '.js-reportlevel2');
  
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


function printfunction() {
  // var content=document.getElementById('divToPrint');
  // window.document.write('<html><head><style>.row{ margin-right: 0px!important; margin-left: 0px!important; margin-top: 1%!important;}.section1{border:1.5pxsolid#9187878c;margin:2%;padding:2%;}.color1{margin:0%!important;background-color: #5f9ea061;}.modal_field_name{ float:left;font-weight: 700;margin-right:1%;padding-top:1%;margin-top:1%;}.modal_field_value{margin-right:1%;padding-top:1%;margin-top:1%;}</style></head><body>'+content.innerHTML+'</body></html>');
  window.print();
}




$(document).ready(function(){   // Add Datepicker here    
  // $('.datepick').datepicker({
  //   autoclose: true,
  //   format: 'yyyy-mm-dd'
  // });
});



//Subhankar--for---bank--edit

$('.js_rural_urban').change(function() {

  loadBlockUlb(this);
});


function loadBlockUlb(element) {

var dist_code = document.getElementById('dist_code').value;
$('.js_block_ulb').empty().append('<option value="-1">--Select Block/Municipality</option>');

loadItems10(dist_code, element, '../api/ruralurban/', '.js_block_ulb');
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