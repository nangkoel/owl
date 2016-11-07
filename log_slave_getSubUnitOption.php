<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zFunction.php');
require_once('lib/zLib.php');
//====================================
$optBlokLm=makeOption($dbname,'setup_blok','kodeorg,bloklama');
$optStatBlok=makeOption($dbname,'setup_blok','kodeorg,statusblok');

if(isTransactionPeriod())//check if transaction period is normal
{
        $induk=$_POST['induk'];
		$untukunit=$_POST['untukunit'];
		
		//exit("Error:$induk");
       
        $blehh="<option value=''></option>";
        $str="select distinct kodeorganisasi,namaorganisasi,tipe from ".$dbname.".organisasi where induk='".$induk."' and tipe not like '%gudang%' order by kodeorganisasi";
        //exit("error:".$str);
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res)){
            
            if(strlen($induk)==6){
                    if($_POST['afdeling']!=''){
                        $blehh.="<option value='".$bar->kodeorganisasi."' ".($bar->kodeorganisasi==$_POST['afdeling']?"selected":"").">".$bar->kodeorganisasi."-".$optBlokLm[$bar->kodeorganisasi]."-".$bar->namaorganisasi." (".$optStatBlok[$bar->kodeorganisasi].")</option>";
                    }else{
                        $blehh.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi."-".$optBlokLm[$bar->kodeorganisasi]."-".$bar->namaorganisasi." (".$optStatBlok[$bar->kodeorganisasi].")</option>";
                    }
            }else{
                 if($_POST['afdeling']!=''){
                     $blehh.="<option value='".$bar->kodeorganisasi."' ".($bar->kodeorganisasi==$_POST['afdeling']?"selected":"").">".$bar->namaorganisasi."</option>";
                 }else{
                     $blehh.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
                 }
            }
            
        }

    #ambil proect
   if(substr($induk,0,2)=='AK' or substr($induk,0,2)=='PB')
   {
       $blehh='';
       $str="select kode,nama from ".$dbname.".project where kode='".$induk."'";    
      $res=mysql_query($str);
      while($bar=mysql_fetch_object($res))
       {
        $blehh.="<option value='".$bar->kode."'>Project:".$bar->kode."-".$bar->nama."</option>";
        }          
   }
   else{
       $str="select kode,nama from ".$dbname.".project where kodeorg='".$induk."' and posting=0";    
      $res=mysql_query($str);
      while($bar=mysql_fetch_object($res))
       {
        $blehh.="<option value='".$bar->kode."'>Project:".$bar->kode."-".$bar->nama."</option>";
        }          
   }          
    
	        //$blehh.=ambilSubUnit('',$induk);
   $kdunit=$_POST['induk'];
   $whr="";
   
  
   
   if(strlen($_POST['induk'])>4)
   {
	   if(substr($_POST['induk'],0,2)=='AK')
	   {
		 // $kdunit=$untukunit;
		  
			$kdunit='H0RO';
			$whr="and subbagian='H0ROTR'";
		  
		
	   }
	   else
	   {
			$kdunit=substr($_POST['induk'],0,4);
       		$whr="and subbagian='".$_POST['induk']."'";
		
	   }
   }
   
   if($kdunit=='')
   {
	   $kdunit=$_POST['untukunit'];
	//   untukunit
   }
   
   
   
   if($_POST['induk']=='')
   {
	    $kdunit=$untukunit;
   }
   
   
   $optKary.="<option value=''></option>";
   $skary="select distinct nik,namakaryawan,karyawanid from ".$dbname.".datakaryawan 
           where lokasitugas='".$kdunit."' ".$whr."  and tanggalkeluar='0000-00-00' 
           order by namakaryawan asc";
   //exit("error:".$skary);
   
   
   
   $qkary=mysql_query($skary) or die(mysql_error($conn));
   while($rkary=  mysql_fetch_assoc($qkary)){
       if($_POST['namapenerima']==$rkary['karyawanid']){            
           //exit("error:masul");
            $optKary.="<option value='".$rkary['karyawanid']."' selected=selected>".$rkary['nik']."-".$rkary['namakaryawan']."</option>";
       }else{
            $optKary.="<option value='".$rkary['karyawanid']."'>".$rkary['nik']."-".$rkary['namakaryawan']."</option>";
       }
   }
   $optKary.="<option value='masyarakat'>".$_SESSION['lang']['masyarakat']."</option>";
   $optKary.="<option value='traksi'>Traksi</option>";
   //exit("error:".$_POST['namapenerima']);
            echo $blehh."####".$optKary;
}
else
{
	echo " Error: Transaction Period missing";
}
?>