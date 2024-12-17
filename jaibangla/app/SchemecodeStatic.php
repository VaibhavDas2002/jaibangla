<?php
namespace App;
use Config;
class SchemecodeStatic 
{
    public static function getpr1ListPurohit()
    {
      $purohitMonthlyArr=array();
      $purohitHousingArr=array();
      $purohitBothArr=array();
      $monthlySchemeCode=$housingSchemeCode=$bothSchemeCode='';
      
      $monthlySchemeMainTable=$housingSchemeMainTable='';
      $monthlySchemeDocTable=$housingSchemeDocTable='';
      $monthlySchemeDocArcTable=$housingSchemeDocArcTable='';
      $schemestatislist=Config::get('constants.schemecodeStatic');
      
      foreach( $schemestatislist as $key=>$arr){
        if($key=='purohitmonthly'){
          $monthlySchemeCode=$arr['scheme_code'];
          $monthlySchemeMainTable=$arr['maintable'];
          $monthlySchemeDocTable=$arr['doctable'];
          $monthlySchemeDocArcTable=$arr['docarctable'];
         }
         if($key=='purohithousing'){
          $housingSchemeCode=$arr['scheme_code'];
          $housingSchemeMainTable=$arr['maintable'];
          $housingSchemeDocTable=$arr['doctable'];
          $housingSchemeDocArcTable=$arr['docarctable'];
         }
         if($key=='purohitboth'){
          $bothSchemeCode=$arr['scheme_code'];
          

         }
      }
      $purohitMonthlyArr['slug']= 'purohitmonthly';
      $purohitMonthlyArr['scheme_code']= $monthlySchemeCode;
      $purohitMonthlyArr['maintable']= $monthlySchemeMainTable;
      $purohitMonthlyArr['doctable']= $monthlySchemeDocTable;
      $purohitMonthlyArr['docarchtable']= $monthlySchemeDocArcTable;

      $purohitHousingArr['slug']= 'purohithousing';
      $purohitHousingArr['scheme_code']= $housingSchemeCode;
      $purohitHousingArr['maintable']= $housingSchemeMainTable;
      $purohitHousingArr['doctable']= $housingSchemeDocTable;
      $purohitHousingArr['docarchtable']= $housingSchemeDocArcTable;

      $purohitBothArr['slug']= 'purohitboth';
      $purohitBothArr['scheme_code']=  $bothSchemeCode;
      $arr=array('monthly'=>$purohitMonthlyArr,'housing'=>$purohitHousingArr,'both'=>$purohitBothArr);
      return $arr;

    }
 
    
}
 