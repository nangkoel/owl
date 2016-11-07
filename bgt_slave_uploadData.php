<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');
require_once('lib/zLib.php');
$pemisah=$_POST['pemisah'];
$jenisdata=$_POST['jenisdata'];
$path='tempExcel';
$optSatKeg=makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');
$optSatBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');

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
    global $optSatKeg;
    global $optSatBrg;
    
    
      $jlhbaris=count($x)-1;
      #baris pertama adalah header;
      foreach($x[0] as $val){
          $header[]=trim($val);
      }
      switch ($jenisdata) {
          case 'SDM':  
              #ambil kegiatan
              $str="select kodekegiatan from ".$dbname.".setup_kegiatan order by kodekegiatan asc";
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $noakun[]=$bar->kodekegiatan;
              }
              #ambil  kolom periode
              foreach ($header as $ki=> $val){
                if($val=='tahunbudget'){
                    $index1=$ki;//tahunbudget
                }
                if($val=='kodeblok'){
                    $index2=$ki;//kodelbok
                }
                if($val=='tipebudget'){
                    $index3=$ki;//tipebudget
                }
                if($val=='kodebudget'){
                    $index4=$ki;//kodebudget
                }
                if($val=='kodekegiatan'){
                    $index5=$ki;//kodekegiatan
                }
                if($val=='volumepekerjaansetahun'){
                    $index6=$ki;//volumepekerjaansetahun
                }
                if($val=='rupiahsetahun'){
                    $index7=$ki;//volumepekerjaansetahun
                }
                if($val=='rotasi'){
                    $index8=$ki;//rotasi
                }
                if($val=='jumlahhk'){
                    $index9=$ki;//jumlahhk
                }
                if($val=='satuan'){
                    $index10=$ki;//satuan
                }
              }
               #periksa kelengkapan data
              if(count($x[0])!=11){
                  exit("Error: Form not valid");
              }
              #ambil kode blok budget
              $str="select kodeblok from ".$dbname.".bgt_blok 
                    where tahunbudget='".$x[1][$index1]."' and kodeblok like '".substr($x[1][$index2],0,4)."%' and closed=1
                    order by kodeblok asc";
              //exit("error:".$str);
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $kdblok[]=$bar->kodeblok;
              }
              #cek budget sudah di tutup atau belum
              $str="select distinct * from ".$dbname.".bgt_budget where 
                    tahunbudget='".$x[1][$index1]."' and kodeorg like '".substr($x[1][$index2],0,4)."%' and tutup=1";
              $res=mysql_query($str) or die(mysql_error($conn));
              $barcek=mysql_num_rows($res);
              if($barcek>0){
                  exit("error: budget data for this ".$x[1][$index1]." year has been closed ");
              }
             
              if(count($kdblok)==0){
                  exit("error: setup block budget has not been processed or closed ");
              }
          $thnBerjln=date("Y");
          foreach($x as $key =>$arr){
              if($key==0){
                  continue;
              }else{
                  foreach($arr as $ids =>$rinc){
                      if($header[$ids]=='tahunbudget' AND strlen($rinc)!=4){
                          exit("Error: some data on budget year not valid (line ".$key.") ".$rinc);
                      }
                      if($header[$ids]=='tahunbudget' AND $rinc<$thnBerjln){
                          exit("Error: some data on budget year the format not valid (line ".$key.")");
                      }
                      if($header[$ids]=='kodeblok' AND strlen($rinc)!=10){
                          exit("Error: some data on code block not valid (line ".$key.")");
                      }
                      if($header[$ids]=='kodekegiatan' AND strlen($rinc)!=9){
                            exit("Error: some data on activity code not valid (line ".$key.")");
                      }
                      if($header[$ids]=='tipebudget' AND $rinc!='ESTATE'){
                          exit("Error: some data on budget type not valid (line ".$key.")");
                      }
                      if($header[$ids]=='rupiahsetahun' AND (intval($rinc)=='0')){
                          exit("Error: some data on rupiah a year not valid (line ".$key.")");
                      }
                      if($header[$ids]=='kodekegiatan' ){
                        #periksa noakun yang disubmit
                        $akunbermasalah[$rinc]=$rinc;
                        foreach($noakun as $bb=>$cc){
                                if($cc==$rinc)
                                    unset($akunbermasalah[$rinc]);
                        }
                      }
                      if($header[$ids]=='kodeblok' ){
                        #periksa kodeblok yang disubmit
                        $blokbermasalah[$rinc]=$rinc;
                        foreach($kdblok as $bb=>$cc){
                                if($cc==$rinc)
                                    unset($blokbermasalah[$rinc]);
                        }
                      }
                     }
              }
          }
          if(count($akunbermasalah)>0){
              echo "The following activity code were not defined:<br>";
              print_r($akunbermasalah);
              exit();
          }
          if(count($blokbermasalah)>0){
              echo "The following block code were not defined:<br>";
              print_r($blokbermasalah);
              exit();
          }
              $jmlhRow=count($x);
              #generate SQL:
              for($aerto=1;$aerto<$jmlhRow;$aerto++){
                   #delete first
                  $str="delete from ".$dbname.".bgt_budget where 
                        kodeorg='".$x[$aerto][$index2]."' and tahunbudget='".$x[$aerto][$index1]."' and kegiatan='".$x[$aerto][$index5]."'
                        and kodebudget='".trim($x[$aerto][$index4])."'";
                  if(mysql_query($str)){
                  $detData="insert into ".$dbname.".bgt_budget(`tahunbudget`,`kodeorg`,`tipebudget`,
                          `kodebudget`,`kegiatan`,`noakun`,`volume`,`satuanv`,`rupiah`,`rotasi`,`jumlah`,`satuanj`,`updateby`,`keterangan`) values ";
                    $rupiah[$aerto][$index7]=str_replace(",","", trim($x[$aerto][$index7]));
                    $detData.="('".trim($x[$aerto][$index1])."','".trim($x[$aerto][$index2])."','".trim($x[$aerto][$index3])."','".trim($x[$aerto][$index4])."','".trim($x[$aerto][$index5])."',
                                '".substr(trim($x[$aerto][$index5]),0,7)."','".trim($x[$aerto][$index6])."','".$optSatKeg[trim($x[$aerto][$index5])]."','".$rupiah[$aerto][$index7]."',
                                '".trim($x[$aerto][$index8])."','".trim($x[$aerto][$index9])."','HK','".$_SESSION['standard']['userid']."','Data di upload oleh ".$_SESSION['standard']['username']."')";
                     if(!mysql_query($detData)){
                         exit("error:\n".$detData."__".  mysql_error());
                     }else{
                         echo "";
                     }
                  }else{
                         exit("error:\n".$str."__".  mysql_error());
                  }
             }
             
              break;
#===========================================================end  sdm ======================================================             
           case 'MATANDTOOL':  
//              echo"<pre>";
//              print_r($x); 
//              echo"</pre>";
//              exit("error");
              #ambil kegiatan
              $str="select kodekegiatan from ".$dbname.".setup_kegiatan order by kodekegiatan asc";
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $noakun[]=$bar->kodekegiatan;
              }
              #ambil  kolom periode
              foreach ($header as $ki=> $val){
                if($val=='tahunbudget'){
                    $index1=$ki;//tahunbudget
                }
                if($val=='kodeblok'){
                    $index2=$ki;//kodelbok
                }
                if($val=='tipebudget'){
                    $index3=$ki;//tipebudget
                }
                if($val=='kodebudget'){
                    $index4=$ki;//kodebudget
                }
                if($val=='kodekegiatan'){
                    $index5=$ki;//kodekegiatan
                }
                if($val=='volumepekerjaansetahun'){
                    $index6=$ki;//volumepekerjaansetahun
                }
                if($val=='rupiahsetahun'){
                    $index7=$ki;//volumepekerjaansetahun
                }
                if($val=='rotasi'){
                    $index8=$ki;//rotasi
                }
                if($val=='kodebarang'){
                    $index9=$ki;//kodebarang
                }
                if($val=='jumlahbrg'){
                    $index11=$ki;//jumlahbrg
                }
                if($val=='satuanbrg'){
                    $index12=$ki;//jumlahbrg
                }
              }
              #periksa kelengkapan data
              if(count($x[0])!=13){
                  exit("Error: Form not valid");
              }
              
              #ambil kode blok budget
              $str="select kodeblok from ".$dbname.".bgt_blok 
                    where tahunbudget='".$x[1][$index1]."' and kodeblok like '".substr($x[1][$index2],0,4)."%' and closed=1
                    order by kodeblok asc";
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $kdblok[]=$bar->kodeblok;
              }
              #ambil kodebarang
              $str="select kodebarang from ".$dbname.".log_5masterbarang 
                    where inactive=0 order by kodebarang asc";
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $kdbarang[]=$bar->kodebarang;
              }
              #ambil kodebudget yang material
              $str="select kodebudget from ".$dbname.".bgt_kode 
                    where left(kodebudget,1)='M' order by kodebudget asc";
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $kdbudget[]=$bar->kodebudget;
              }
              if(count($kdblok)==0){
                  exit("error: setup block budget has not been processed or closed ");
              }
              #cek budget sudah di tutup atau belum
              $str="select distinct * from ".$dbname.".bgt_budget where 
                    tahunbudget='".$x[1][$index1]."' and kodeorg like '".substr($x[1][$index2],0,4)."%' and tutup=1";
              $res=mysql_query($str) or die(mysql_error($conn));
              $barcek=mysql_num_rows($res);
              if($barcek>0){
                  exit("error: budget data for this ".$x[1][$index1]." year has been closed ");
              }
          $thnBerjln=date("Y");
          foreach($x as $key =>$arr){
              if($key==0){
                  continue;
              }else{
                  foreach($arr as $ids =>$rinc){
                      if($header[$ids]=='tahunbudget' AND strlen($rinc)!=4){
                          exit("Error: some data on budget year not valid (line ".$key.") ".$rinc);
                      }
                      if($header[$ids]=='tahunbudget' AND $rinc<$thnBerjln){
                          exit("Error: some data on budget year the format not valid (line ".$key.")");
                      }
                      if($header[$ids]=='kodeblok' AND strlen($rinc)!=10){
                          exit("Error: some data on code block not valid (line ".$key.")");
                      }
                      if($header[$ids]=='kodekegiatan' AND strlen($rinc)!=9){
                            exit("Error: some data on activity code not valid (line ".$key.")");
                      }
                      if($header[$ids]=='tipebudget' AND $rinc!='ESTATE'){
                          exit("Error: some data on budget type not valid (line ".$key.")");
                      }
                      if($header[$ids]=='rupiahsetahun' AND (intval($rinc)=='0')){
                          exit("Error: some data on rupiah a year not valid (line ".$key.")");
                      }
                      if($header[$ids]=='kodekegiatan' ){
                        #periksa noakun yang disubmit
                        $akunbermasalah[$rinc]=$rinc;
                        foreach($noakun as $bb=>$cc){
                                if($cc==$rinc)
                                    unset($akunbermasalah[$rinc]);
                        }
                      }
                      if($header[$ids]=='kodeblok' ){
                        #periksa kodeblok yang disubmit
                        $blokbermasalah[$rinc]=$rinc;
                        foreach($kdblok as $bb=>$cc){
                                if($cc==$rinc)
                                    unset($blokbermasalah[$rinc]);
                        }
                      }
                      if($header[$ids]=='kodebarang' ){
                        #periksa kodebarang yang disubmit
                        $kdbrgbermasalah[$rinc]=$rinc;
                        foreach($kdbarang as $bb=>$cc){
                                if($cc==$rinc)
                                    unset($kdbrgbermasalah[$rinc]);
                        }
                      }
                      if($header[$ids]=='kodebudget'){
                        #periksa kodebudget yang disubmit
                        $kdbgtbermasalah[$rinc]=$rinc;
                        foreach($kdbudget as $bb=>$cc){
                                if($cc==$rinc)
                                    unset($kdbgtbermasalah[$rinc]);
                        }
                      }
                     }
              }
          }
          if(count($akunbermasalah)>0){
              echo "The following activity code were not defined:<br>";
              print_r($akunbermasalah);
              exit();
          }
          if(count($blokbermasalah)>0){
              echo "The following block code were not defined:<br>";
              print_r($blokbermasalah);
              exit();
          }
          if(count($kdbrgbermasalah)>0){
              echo "The following material code were not defined:<br>";
              print_r($kdbrgbermasalah);
              exit();
          }
          if(count($kdbgtbermasalah)>0){
              echo "The following budget code were not defined:<br>";
              print_r($kdbgtbermasalah);
              exit();
          }
              $jmlhRow=count($x);
              #generate SQL:
              for($aerto=1;$aerto<$jmlhRow;$aerto++){
                   #delete first
                  $str="delete from ".$dbname.".bgt_budget where 
                        kodeorg='".trim($x[$aerto][$index2])."' and tahunbudget='".trim($x[$aerto][$index1])."' and kegiatan='".trim($x[$aerto][$index5])."'
                        and kodebarang='".trim($x[$aerto][$index9])."' and kodebudget='".trim($x[$aerto][$index4])."'";
                  if(mysql_query($str)){
                  $detData="insert into ".$dbname.".bgt_budget(`tahunbudget`,`kodeorg`,`tipebudget`,
                          `kodebudget`,`kegiatan`,`noakun`,`volume`,`satuanv`,`rupiah`,`kodebarang`,`rotasi`,`jumlah`,`satuanj`,`updateby`,`keterangan`) values ";
                    $rupiah[$aerto][$index7]=str_replace(",","", trim($x[$aerto][$index7]));
                    $detData.="('".trim($x[$aerto][$index1])."','".trim($x[$aerto][$index2])."','".trim($x[$aerto][$index3])."','".trim($x[$aerto][$index4])."','".trim($x[$aerto][$index5])."',
                                '".substr(trim($x[$aerto][$index5]),0,7)."','".trim($x[$aerto][$index6])."','".$optSatKeg[trim($x[$aerto][$index5])]."','".$rupiah[$aerto][$index7]."',
                                '".trim($x[$aerto][$index9])."','".trim($x[$aerto][$index8])."','".trim($x[$aerto][$index11])."','".$optSatBrg[trim($x[$aerto][$index9])]."',
                                '".$_SESSION['standard']['userid']."','Data di upload oleh ".$_SESSION['standard']['username']."')";
                     if(!mysql_query($detData)){
                         exit("error:\n".$detData."__".  mysql_error());
                     }else{
                         echo "";
                     }
                  }else{
                         exit("error:\n".$str."__".  mysql_error());
                  }
             }
             
              break;
 #===========================================================end  material and tool ======================================================             
 #==============================BEGIN VHC
             case 'VHC':  
//                 echo"<pre>";
//                 print_r($x);
//                 echo"</pre>";
//                 exit("error:");
              #ambil kegiatan
              $str="select kodekegiatan from ".$dbname.".setup_kegiatan order by kodekegiatan asc";
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $noakun[]=$bar->kodekegiatan;
              }
              #ambil  kolom periode
              foreach ($header as $ki=> $val){
                if($val=='tahunbudget'){
                    $index1=$ki;//tahunbudget
                }
                if($val=='kodeblok'){
                    $index2=$ki;//kodelbok
                }
                if($val=='tipebudget'){
                    $index3=$ki;//tipebudget
                }
                if($val=='kodebudget'){
                    $index4=$ki;//kodebudget
                }
                if($val=='kodekegiatan'){
                    $index5=$ki;//kodekegiatan
                }
                if($val=='volumepekerjaansetahun'){
                    $index6=$ki;//volumepekerjaansetahun
                }
                if($val=='rupiahsetahun'){
                    $index7=$ki;//volumepekerjaansetahun
                }
                
                if($val=='kodevhc'){
                    $index9=$ki;//kodevhc
                }
                if($val=='jumlahhmkmpertahun'){
                    $index11=$ki;//jumlahbrg
                }
                if($val=='satuan'){
                    $index12=$ki;//jumlahbrg
                }
              }
               #periksa kelengkapan data
              if(count($x[0])!=12){
                  exit("Error: Form not valid");
              }
              
              #ambil kode blok budget
              $str="select kodeblok from ".$dbname.".bgt_blok 
                    where tahunbudget='".$x[1][$index1]."' and kodeblok like '".substr($x[1][$index2],0,4)."%' and closed=1
                    order by kodeblok asc";
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $kdblok[]=$bar->kodeblok;
              }
              
              #ambil kodevhc yang alokasinya untuk divisi 
              $str="select kodevhc from ".$dbname.".bgt_vhc_jam 
                    where tahunbudget='".$x[1][$index1]."' and unitalokasi like '".substr($x[1][$index2],0,4)."%' order by kodevhc asc";
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $kdvhc[]=$bar->kodevhc;
              }
              #cek unit alokasi yang masih nol
              $str="select kodevhc from ".$dbname.".bgt_vhc_jam 
                    where tahunbudget='".$x[1][$index1]."' and unitalokasi like '".substr($x[1][$index2],0,4)."%' 
                    and jumlahjam=0 order by kodevhc asc";
              //exit("error:".$str);
              $res=mysql_query($str);
              while($bar2=mysql_fetch_object($res)){
                  $kdvhcnol[]=$bar2->kodevhc;
              }
              #cek unit alokasi yang tidak nol
              $str="select kodevhc,jumlahjam from ".$dbname.".bgt_vhc_jam 
                    where tahunbudget='".$x[1][$index1]."' and unitalokasi like '".substr($x[1][$index2],0,4)."%' 
                    and jumlahjam!=0 order by kodevhc asc";
              
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $jmlVhc[$bar->kodevhc]=$bar->jumlahjam;
              }
              if(count($kdblok)==0){
                  exit("error: setup block budget has not been processed or closed ");
              }
              #cek budget sudah di tutup atau belum
              $str="select distinct * from ".$dbname.".bgt_budget where 
                    tahunbudget='".$x[1][$index1]."' and kodeorg like '".substr($x[1][$index2],0,4)."%' and tutup=1";
              $res=mysql_query($str) or die(mysql_error($conn));
              $barcek=mysql_num_rows($res);
              if($barcek>0){
                  exit("error: budget data for this ".$x[1][$index1]." year has been closed ");
              }
          $thnBerjln=date("Y");
          foreach($x as $key =>$arr){
              if($key==0){
                  continue;
              }else{
                  foreach($arr as $ids =>$rinc){
                      if($header[$ids]=='tahunbudget' AND strlen($rinc)!=4){
                          exit("Error: some data on budget year  (".$rinc.") not valid (line ".$key.") ".$rinc);
                      }
                      if($header[$ids]=='tahunbudget' AND $rinc<$thnBerjln){
                          exit("Error: some data on budget year (".$rinc.") the format not valid (line ".$key.")");
                      }
                      if($header[$ids]=='kodeblok' AND strlen($rinc)!=10){
                          exit("Error: some data on code block not  (".$rinc.") valid (line ".$key.")");
                      }
                      if($header[$ids]=='kodekegiatan' AND strlen($rinc)!=9){
                            exit("Error: some data on activity code (".$rinc.") not valid (line ".$key.")");
                      }
                      if($header[$ids]=='tipebudget' AND $rinc!='ESTATE'){
                          exit("Error: some data on budget type (".$rinc.") not valid (line ".$key.")");//
                      }
                      if($header[$ids]=='kodebudget' AND $rinc!='VHC'){
                          exit("Error: some data on budget code (".$rinc.") not valid (line ".$key.")");
                      }
                      if($header[$ids]=='kodevhc' AND $rinc==''){
                          exit("Error: some data on kode vhc not valid (line ".$key.")");
                      }
                      if($header[$ids]=='rupiahsetahun' AND (intval($rinc)=='0')){
                          exit("Error: some data on rupiah a year not valid (line ".$key.")");
                      }
                      if($header[$ids]=='kodekegiatan' ){
                        #periksa noakun yang disubmit
                        $akunbermasalah[$rinc]=$rinc;
                        foreach($noakun as $bb=>$cc){
                                if($cc==$rinc)
                                    unset($akunbermasalah[$rinc]);
                        }
                      }
                      if($header[$ids]=='kodeblok' ){
                        #periksa kodeblok yang disubmit
                        $blokbermasalah[$rinc]=$rinc;
                        foreach($kdblok as $bb=>$cc){
                                if($cc==$rinc)
                                    unset($blokbermasalah[$rinc]);
                        }
                      }
         
                      if($header[$ids]=='kodevhc'){
                        #periksa kodevhc yang disubmit
                        $kdvhcbermasalah[$rinc]=$rinc;
                        foreach($kdvhc as $bb=>$cc){
                                if($cc==$rinc)
                                    unset($kdvhcbermasalah[$rinc]);
                        }
                        if(count($kdvhcnol)!=0){
                            #periksa kodevhc yang disubmit
                            $kdvhcnolbermasalah[$rinc]=$rinc;
                            foreach($kdvhcnol as $bb=>$cc){
                                    if($cc==$rinc)
                                        unset($kdvhcnolbermasalah[$rinc]);
                            }
                        }
                      }
                       
                     }
              }
          }
          if(count($akunbermasalah)>0){
              echo "The following activity code were not defined:<br>";
              print_r($akunbermasalah);
              exit();
          }
      
          if(count($kdvhcbermasalah)>0){
              echo "The following vhc code were not alocate to your site:<br>";
              print_r($kdvhcbermasalah);
              exit();
          }
          if(count($kdvhcnolbermasalah)>0){
              echo "The following vhc code were alocate but 0 KM/HM or is not alocate to your site:<br>";
              print_r($kdvhcnolbermasalah);
              exit();
          }
           if(count($blokbermasalah)>0){
              echo "The following block code were not defined:<br>";
              print_r($blokbermasalah);
              exit();
          }
              $jmlhRow=count($x);
              #generate SQL:
              for($aerto=1;$aerto<$jmlhRow;$aerto++){
                  $jmlhBgtJam[$x[$aerto][$index9]]+=trim($x[$aerto][$index11]);
                  if($jmlVhc[trim($x[$aerto][$index9])]>$jmlhBgtJam[$x[$aerto][$index9]]){// cek vhc
                       #delete first
                      $str="delete from ".$dbname.".bgt_budget where 
                            kodeorg='".trim($x[$aerto][$index2])."' and tahunbudget='".trim($x[$aerto][$index1])."' and kegiatan='".trim($x[$aerto][$index5])."'
                            and kodevhc='".trim($x[$aerto][$index9])."' and kodebudget='VHC'";
                      if(mysql_query($str)){
                      $detData="insert into ".$dbname.".bgt_budget(`tahunbudget`,`kodeorg`,`tipebudget`,
                              `kodebudget`,`kegiatan`,`noakun`,`volume`,`satuanv`,`rupiah`,`kodevhc`,`jumlah`,`satuanj`,`updateby`,`keterangan`) values ";
                        $rupiah[$aerto][$index7]=str_replace(",","", trim($x[$aerto][$index7]));
                        $detData.="('".trim($x[$aerto][$index1])."','".trim($x[$aerto][$index2])."','".trim($x[$aerto][$index3])."','".trim($x[$aerto][$index4])."','".trim($x[$aerto][$index5])."',
                                    '".substr(trim($x[$aerto][$index5]),0,7)."','".trim($x[$aerto][$index6])."','".$optSatKeg[trim($x[$aerto][$index5])]."','".$rupiah[$aerto][$index7]."',
                                    '".trim($x[$aerto][$index9])."','".trim($x[$aerto][$index11])."','".trim($x[$aerto][$index12])."',
                                    '".$_SESSION['standard']['userid']."','Data di upload oleh ".$_SESSION['standard']['username']."')";
                         if(!mysql_query($detData)){
                             exit("error:\n".$detData."__".  mysql_error());
                         }else{
                             echo "";
                         }
                      }else{
                             exit("error:\n".$str."__".  mysql_error());
                      }
                  }else{
                      exit("error: alocation for this vhc code (".$x[$aerto][$index9].") already over in line ".$aerto);
                  }
             }
             
              break;                      
           #====================================================END VHC  ================================================ 
           #==============================BEGIN VHC
             case 'KONTRAK':  
//                 echo"<pre>";
//                 print_r($x);
//                 echo"</pre>";
//                 exit("error:");
              #ambil kegiatan
              $str="select kodekegiatan from ".$dbname.".setup_kegiatan order by kodekegiatan asc";
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $noakun[]=$bar->kodekegiatan;
              }
              #ambil  kolom periode
              foreach ($header as $ki=> $val){
                if($val=='tahunbudget'){
                    $index1=$ki;//tahunbudget
                }
                if($val=='kodeblok'){
                    $index2=$ki;//kodelbok
                }
                if($val=='tipebudget'){
                    $index3=$ki;//tipebudget
                }
                if($val=='kodebudget'){
                    $index4=$ki;//kodebudget
                }
                if($val=='kodekegiatan'){
                    $index5=$ki;//kodekegiatan
                }
                if($val=='volumepekerjaansetahun'){
                    $index6=$ki;//volumepekerjaansetahun
                }
                if($val=='rupiahsetahun'){
                    $index7=$ki;//volumepekerjaansetahun
                }
              }
               #periksa kelengkapan data
              if(count($x[0])!=8){
                  exit("Error: Form not valid");
              }
              
              #ambil kode blok budget
              $str="select kodeblok from ".$dbname.".bgt_blok 
                    where tahunbudget='".$x[1][$index1]."' and kodeblok like '".substr($x[1][$index2],0,4)."%' and closed=1
                    order by kodeblok asc";
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res)){
                  $kdblok[]=$bar->kodeblok;
              }
              
              if(count($kdblok)==0){
                  exit("error: setup block budget has not been processed or closed ");
              }
              #cek budget sudah di tutup atau belum
              $str="select distinct * from ".$dbname.".bgt_budget where 
                    tahunbudget='".$x[1][$index1]."' and kodeorg like '".substr($x[1][$index2],0,4)."%' and tutup=1";
              $res=mysql_query($str) or die(mysql_error($conn));
              $barcek=mysql_num_rows($res);
              if($barcek>0){
                  exit("error: budget data for this ".$x[1][$index1]." year has been closed ");
              }
          $thnBerjln=date("Y");
          foreach($x as $key =>$arr){
              if($key==0){
                  continue;
              }else{
                  foreach($arr as $ids =>$rinc){
                      if($header[$ids]=='tahunbudget' AND strlen($rinc)!=4){
                          exit("Error: some data on budget year  (".$rinc.") not valid (line ".$key.") ".$rinc);
                      }
                      if($header[$ids]=='tahunbudget' AND $rinc<$thnBerjln){
                          exit("Error: some data on budget year (".$rinc.") the format not valid (line ".$key.")");
                      }
                      if($header[$ids]=='kodeblok' AND strlen($rinc)!=10){
                          exit("Error: some data on code block not  (".$rinc.") valid (line ".$key.")");
                      }
                      if($header[$ids]=='kodekegiatan' AND strlen($rinc)!=9){
                            exit("Error: some data on activity code (".$rinc.") not valid (line ".$key.")");
                      }
                      if($header[$ids]=='tipebudget' AND $rinc!='ESTATE'){
                          exit("Error: some data on budget type (".$rinc.") not valid (line ".$key.")");//
                      }
                      if($header[$ids]=='kodebudget' AND $rinc!='KONTRAK'){
                          exit("Error: some data on budget code (".$rinc.") not valid (line ".$key.")");
                      }
                      if($header[$ids]=='rupiahsetahun' AND (intval($rinc)=='0')){
                          exit("Error: some data on rupiah a year not valid (line ".$key.")");
                      }
                     
                      if($header[$ids]=='kodekegiatan' ){
                        #periksa noakun yang disubmit
                        $akunbermasalah[$rinc]=$rinc;
                        foreach($noakun as $bb=>$cc){
                                if($cc==$rinc)
                                    unset($akunbermasalah[$rinc]);
                        }
                      }
                      if($header[$ids]=='kodeblok' ){
                        #periksa kodeblok yang disubmit
                        $blokbermasalah[$rinc]=$rinc;
                        foreach($kdblok as $bb=>$cc){
                                if($cc==$rinc)
                                    unset($blokbermasalah[$rinc]);
                        }
                      }
                       
                     }
              }
          }
          if(count($akunbermasalah)>0){
              echo "The following activity code were not defined:<br>";
              print_r($akunbermasalah);
              exit();
          }
      
           if(count($blokbermasalah)>0){
              echo "The following block code were not defined:<br>";
              print_r($blokbermasalah);
              exit();
          }
              $jmlhRow=count($x);
              #generate SQL:
              for($aerto=1;$aerto<$jmlhRow;$aerto++){
                       #delete first
                      $str="delete from ".$dbname.".bgt_budget where 
                            kodeorg='".trim($x[$aerto][$index2])."' and tahunbudget='".trim($x[$aerto][$index1])."' and kegiatan='".trim($x[$aerto][$index5])."'
                            and kodevhc='".trim($x[$aerto][$index9])."'";
                      if(mysql_query($str)){
                      $detData="insert into ".$dbname.".bgt_budget(`tahunbudget`,`kodeorg`,`tipebudget`,
                              `kodebudget`,`kegiatan`,`noakun`,`volume`,`satuanv`,`rupiah`,`updateby`,`keterangan`) values ";
                        $rupiah[$aerto][$index7]=str_replace(",","", trim($x[$aerto][$index7]));
                        $detData.="('".trim($x[$aerto][$index1])."','".trim($x[$aerto][$index2])."','".trim($x[$aerto][$index3])."','".trim($x[$aerto][$index4])."','".trim($x[$aerto][$index5])."',
                                    '".substr(trim($x[$aerto][$index5]),0,7)."','".trim($x[$aerto][$index6])."','".$optSatKeg[trim($x[$aerto][$index5])]."','".$rupiah[$aerto][$index7]."',
                                    '".$_SESSION['standard']['userid']."','Data di upload oleh ".$_SESSION['standard']['username']."')";
                         if(!mysql_query($detData)){
                             exit("error:\n".$detData."__".  mysql_error());
                         }else{
                             echo "";
                         }
                      }else{
                             exit("error:\n".$str."__".  mysql_error());
                      }
                  
             }
             
              break;                      
           #====================================================END VHC  ================================================ 

            default:
            break;
      }
   
}
?>
