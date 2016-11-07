<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');
require_once('lib/zLib.php');
$pemisah=$_POST['pemisah'];
$jnsPotongan=$_POST['jnsPotongan'];
$periodeGaji=$_POST['periodeGaji'];
$unitId=$_POST['unitId'];
//$sCek="select * from ".$dbname.".sdm_gaji where periodegaji='".$periodeGaji."' and kodeorg='".$unitId."'";
//$qCek=mysql_query($sCek) or die(mysql_error($conn));
//$rCek=mysql_num_rows($qCek);
//if($rCek!=0){
//	echo "<script> alert('Gagal, periode gaji sudah terproses');</script>";
//    exit(0);
//}
$jenisdata=array("periodegaji"=>$periodeGaji,"jnsPotongan"=>$jnsPotongan,"unitId"=>$unitId);
$path='tempExcel';

  if(is_dir($path))
  {
  	writeFile($path,$pemisah);
	//chmod($path, 0777);
  }
  else
  {
  	if(mkdir($path))
	{
                    writeFile($path,$pemisah);
	 // chmod($path, 0777);
	}
	else
	{ 
                        echo "<script> alert('Gagal, Can`t create folder for uploaded file');</script>";
                        exit(0);
	}
  } 
function writeFile($path,$pemisah)
{ 
          global $jenisdata;
               $dir=$path;
               $ext=split('[.]', basename( $_FILES['filex']['name']));
                  $ext=$ext[count($ext)-1];
                  $ext=strtolower($ext);
                  if($ext=='csv')
                  {
                  $path = $dir."/".date('ymd').".".$ext;
                  @unlink($path);
                  try{
                         if(move_uploaded_file($_FILES['filex']['tmp_name'], $path))
                         {
                                $x=readCSV($path,$pemisah);
                                simpanData($x,$jenisdata);
                                
                         }
                   }
                   catch(Exception $e)
                   {
                         echo "<script>alert(\"Error Writing File".addslashes($e->getMessage())."\");</script>";
                   }
                  }
                  else
                  {
                         echo "<script>alert('Filetype not support');</script>";		 	
                  }
}
function simpanData($x,$jenisdata){
    global $dbname;
    global $conn;
    global $pemisah;
    
    
     
   $jlhbaris=count($x)-1;
      #baris pertama adalah header;
      foreach($x[0] as $val){
          $header[]=trim($val);
      }
      $sCek="select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where periode='".$jenisdata['periodegaji']."' and kodeorg='".$jenisdata['unitId']."'";
      $qCek=mysql_query($sCek) or die(mysql_error($conn));
	  $rCek=mysql_fetch_assoc($qCek);
              #ambil nik
              $str="select nik,karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$jenisdata['unitId']."' and 
					(tanggalkeluar>='".$rCek['tanggalmulai']."' or tanggalkeluar='0000-00-00') 
                     and (tanggalmasuk<='".$rCek['tanggalsampai']."' or tanggalmasuk='0000-00-00' or tanggalmasuk is null)			  
				     order by nik asc";
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $nikdt[]=$bar->nik;
				  $optKaryId[$bar->nik]=$bar->karyawanid;
              }
              #ambil  kolom periode
              foreach ($header as $ki=> $val){
                if($val=='nik'){
                    $index1=$ki;//tahunbudget
                }
                if($val=='jumlahpotongan'){
                    $index2=$ki;//kodelbok
                }
                if($val=='keterangan'){
                    $index3=$ki;//tipebudget
                }
              }
               #periksa kelengkapan data
              if(count($x[0])!=3){
                  exit("Error: Form not valid");
              }
                
          $thnBerjln=date("Y");
          foreach($x as $key =>$arr){
              if($key==0){
                  continue;
              }else{
                  foreach($arr as $ids =>$rinc){
                      if($header[$ids]=='nik' AND strlen($rinc)!=7){
                          exit("Error: some data on nik not valid (line ".$key.") ".$rinc);
                      }
                      if($header[$ids]=='jumlahpotongan' AND $rinc==0){
                          exit("Error: some data on jumlahpotongan format not valid (line ".$key.")");
                      }
                      
                      if($header[$ids]=='nik' ){
                        #periksa noakun yang disubmit
                        $nikbermasalah[$rinc]=$rinc;
                        foreach($nikdt as $bb=>$cc){
                                if($cc==$rinc)
                                    unset($nikbermasalah[$rinc]);
                        }
                      }
                  }
              }
          }
          if(count($nikbermasalah)>0){
              echo "The following nik were not defined:<br>";
              print_r($nikbermasalah);
              exit();
          }
           
                $jmlhRow=count($x);
			    $scekHeader="select * from ".$dbname.".sdm_potonganht 
			               where periodegaji='".$jenisdata['periodegaji']."' 
						   and kodeorg='".$jenisdata['unitId']."' and tipepotongan='".$jenisdata['jnsPotongan']."'";
				$qcekHeader=mysql_query($scekHeader) or die(mysql_error($conn));
				$rcekHeader=mysql_num_rows($qcekHeader);
				if($rcekHeader!=1){
					$sInsert="insert into ".$dbname.".sdm_potonganht values ('".$jenisdata['unitId']."','".$jenisdata['periodegaji']."','".$jenisdata['jnsPotongan']."','".date("Y-m-d H:i:s")."',".$_SESSION['standard']['userid'].")";
					if(!mysql_query($sInsert)){
						exit("error: ".mysql_error($conn)."____".$sInsert);
					}
				}
					  #generate SQL:
					  for($aerto=1;$aerto<$jmlhRow;$aerto++){
						   #delete first
						  $str="delete from ".$dbname.".sdm_potongandt where 
								kodeorg='".$jenisdata['unitId']."' and periodegaji='".$jenisdata['periodegaji']."' and nik='".$optKaryId[$x[$aerto][$index1]]."'
								and tipepotongan='".$jenisdata['jnsPotongan']."'";
						  if(mysql_query($str)){
						  $detData="insert into ".$dbname.".sdm_potongandt(`kodeorg`,`tipepotongan`,`periodegaji`,
								  `nik`,`jumlahpotongan`,`keterangan`,`updateby`) values ";
							$detData.="('".$jenisdata['unitId']."','".$jenisdata['jnsPotongan']."','".$jenisdata['periodegaji']."','".$optKaryId[$x[$aerto][$index1]]."','".trim($x[$aerto][$index2])."',
										'".trim($x[$aerto][$index3])."','".$_SESSION['standard']['userid']."')";
							 if(!mysql_query($detData)){
								 exit("error:\n".$detData."__".  mysql_error());
							 }else{
								 echo "";
							 }
						  }else{
								 exit("error:\n".$str."__".  mysql_error());
						  }
					 }
}
?>

