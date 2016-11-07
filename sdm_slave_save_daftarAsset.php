<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/zLib.php');

$kodeorg		=$_POST['kodeorg'];
$tipe			=$_POST['tipe'];
$kodeasset		=$_POST['kodeasset'];
$kodebarang		=$_POST['kodebarang'];
$namaaset		=$_POST['namaaset'];
$tahunperolehan	=$_POST['tahunperolehan'];
$nilaiperolehan	=$_POST['nilaiperolehan'];
$jumlahbulan	=$_POST['jumlahbulan'];
$bulanawal		=$_POST['bulanawal'];
$keterangan		=$_POST['keterangan'];
$status			=$_POST['status'];
$method			=$_POST['method'];
$leasing			=$_POST['leasing'];
$penambah			=$_POST['penambah'];
$pengurang			=$_POST['pengurang'];
$refbayar			=$_POST['refbayar'];
$nodokpengadaan			=$_POST['nodokpengadaan'];
$persendecline			=$_POST['persendecline'];
$posisiasset			=$_POST['posisiasset'];
$optTpasset=makeOption($dbname, 'sdm_5tipeasset', 'kodetipe,metodepenyusutan');
$kamusleasing[0]='Not Leasing';
$kamusleasing[1]='Leasing';
if($penambah==''){
$penambah=0;
}
if($pengurang==''){
$pengurang=0;
}
if($jumlahbulan!=='' and $jumlahbulan!='' and $jumlahbulan>0)
   $bulanan=$nilaiperolehan/$jumlahbulan;
else
  $bulanan=0;  
$tex='';
if(isset($_POST['txtcari']))
{
	$tex=" and (kodeasset like '%".$_POST['txtcari']."%' or namasset like '%".$_POST['txtcari']."%')";
}
$dmn="char_length(kodeorganisasi)='4'";
$orgOption=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $dmn,'2');
$nmKary=makeOption($dbname,"datakaryawan","karyawanid,namakaryawan");
//==================
//limit/page
$limit=20;
$page=0;
  if(isset($_POST['page']))
     {
	 	$page=$_POST['page'];
	    if($page<0)
		  $page=0;
	 }
  $offset=$page*$limit;
//===========================

	$str="select a.*		  
		  from ".$dbname.".sdm_daftarasset a
		  where kodeorg in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
		  ".$tex;
	$res=mysql_query($str);	  
	$jlhbrs=mysql_num_rows($res);
	//===================================================
switch($method)
{
case 'update':	
    if(($jumlahbulan=='')||($jumlahbulan=='0')){
            exit("error: ".$_SESSION['lang']['jumlahbulanpenyusutan']." can't empty or zero");
    }
    #cek periode
    $qPer = selectQuery($dbname,'setup_periodeakuntansi','*', "periode='".$bulanawal."' and kodeorg='".$posisiasset."'");
    $Per=fetchData($qPer);
    if ($Per[0]['sudahproses']==1){
        echo "Periode akunting ".$bulanawal." dimana aset berada (".$posisiasset.") sudah tertutup.\n\r";
        exit("error");
    }
//    if(substr($bulanawal,0,4)<date('Y') or (substr($bulanawal,0,4)==date('Y') and substr($bulanawal,5,2)<date('m'))){
//        echo "Awal penyusutan tidak boleh mundur dari periode saat ini.\n\r";
//        exit("error");
//    }
//    if ($tahunperolehan<substr($bulanawal,0,4)) $tahunperolehan=substr($bulanawal,0,4);
    if($optTpasset[$tipe]=='double'){
        if(($persendecline=='')||($persendecline=='0')){
            exit("error: percentage can't empty or zero");
        }
    }
	$str="update ".$dbname.".sdm_daftarasset set 
	       tipeasset='".$tipe."',
		   kodebarang='".$kodebarang."',
		   namasset='".$namaaset."',
		   tahunperolehan=".$tahunperolehan.",
		   status=".$status.",
		   leasing=".$leasing.",
		   hargaperolehan=".$nilaiperolehan.",
		   jlhblnpenyusutan=".$jumlahbulan.",
		   awalpenyusutan='".$bulanawal."',
		   keterangan='".$keterangan."',
		   user=".$_SESSION['standard']['userid'].",
		   bulanan=".$bulanan.",
		   penambah=".$penambah.",
		   pengurang=".$pengurang.",
			refbayar='".$refbayar."',
			dokpengadaan='".$nodokpengadaan."',
			persendecline=".$persendecline.",
                        posisiasset='".$posisiasset."'
	       where kodeasset='".$kodeasset."'
		   and kodeorg='".$kodeorg."'";
		   
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));
		 exit(0);
	}
	break;
case 'insert':
    if(strlen($tipe)==4)
        $kodeasset=str_pad($kodeasset, 6, "0", STR_PAD_LEFT);
    else if(strlen($tipe)==3)
        $kodeasset=str_pad($kodeasset, 7, "0", STR_PAD_LEFT);
    else if(strlen($tipe)==2)
         $kodeasset=str_pad($kodeasset, 8, "0", STR_PAD_LEFT);
     else 
         $kodeasset=str_pad($kodeasset, 8, "0", STR_PAD_LEFT);    
   if(($jumlahbulan=='')||($jumlahbulan=='0')){
            exit("error: ".$_SESSION['lang']['jumlahbulanpenyusutan']." can't empty or zero");
   }
   if($optTpasset[$tipe]=='double'){   
        if(($persendecline=='')||($persendecline=='0')){
            exit("error: percentage can't empty or zero");
        }
    } else {
        if($persendecline=='') $persendecline=0;
    }
    #cek periode
    $qPer = selectQuery($dbname,'setup_periodeakuntansi','*', "periode='".$bulanawal."' and kodeorg='".$posisiasset."'");
    $Per=fetchData($qPer);
    if ($Per[0]['sudahproses']==1){
        echo "Periode akunting ".$bulanawal." dimana aset berada (".$posisiasset.") sudah tertutup.\n\r";
        exit("error");
    }
//    if(substr($bulanawal,0,4)<date('Y') or (substr($bulanawal,0,4)==date('Y') and substr($bulanawal,5,2)<date('m'))){
//        echo "Awal penyusutan tidak boleh mundur dari periode saat ini.\n\r";
//        exit("error");
//    }
//    if ($tahunperolehan<substr($bulanawal,0,4)) $tahunperolehan=substr($bulanawal,0,4);
    
    //$kodeasset=$_SESSION['org']['kodeorganisasi']."-".$tipe.$kodeasset;
	$str="insert into ".$dbname.".sdm_daftarasset (
	       tipeasset,kodeorg,kodebarang,
		   namasset,tahunperolehan,status,
		   hargaperolehan,jlhblnpenyusutan,
		   awalpenyusutan,keterangan,kodeasset,user,bulanan,leasing,penambah,pengurang,
		   refbayar,dokpengadaan,persendecline,posisiasset
		   )
	      values(
		    '".$tipe."',
			'".$kodeorg."',
			'".$kodebarang."',
			'".$namaaset."',
			".$tahunperolehan.",
			".$status.",
			".$nilaiperolehan.",
			".$jumlahbulan.",
			'".$bulanawal."',
			'".$keterangan."',
			'".$kodeasset."',
			".$_SESSION['standard']['userid'].",
			".$bulanan.",
			".$leasing.",
			".$penambah.",
			".$pengurang.",
			'".$refbayar."',
			'".$nodokpengadaan."',
			".$persendecline.",'".$posisiasset."'
			)";
        //exit("error:".$str);
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));
		 exit(0);
	}	
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_daftarasset 
	where kodeasset='".$kodeasset."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));
	 exit(0);
	}
	break;
default:
   break;					
}
         if($_SESSION['language']=='EN'){
             $ads="b.namatipe1 as namatipe";
         }
         else{
            $ads="b.namatipe as namatipe"; 
         }
         
	$str="select a.*,".$ads.", 
	      CASE a.status
		  when 0 then '".$_SESSION['lang']['pensiun']."'
		  when 1 then '".$_SESSION['lang']['aktif']."' 
		  when 2 then '".$_SESSION['lang']['rusak']."' 
		  when 3 then '".$_SESSION['lang']['hilang']."' 
		  else 'Unknown'
          END as stat		  
		  from ".$dbname.".sdm_daftarasset a
	      left join  ".$dbname.".sdm_5tipeasset b
	      on a.tipeasset=.b.kodetipe
		  where a.kodeorg in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') ".$tex." 
		  order by tahunperolehan desc,awalpenyusutan desc,namatipe asc
		   limit ".$offset.",".$limit;
	$res=mysql_query($str);

	$no=$offset;
	while($bar=mysql_fetch_object($res))
	{
	  $no+=1;
	  echo"<tr class=rowcontent>
	          <td>".$no."</td>
		      <td>".$orgOption[$bar->kodeorg]."</td>
                          <td>".$orgOption[$bar->posisiasset]."</td>
			  <td>".$bar->namatipe."</td>
			  <td nowrap>".$bar->kodeasset."</td>
			  <td>".$bar->namasset."</td>
			  <td align=right>".$bar->tahunperolehan."</td>
			  <td>".$bar->stat."</td>
			  <td align=right>".number_format($bar->hargaperolehan,2,'.',',')."</td>
			  <td align=right>".$bar->jlhblnpenyusutan."</td>
			  <td align=right>".$bar->persendecline."</td>
			  <td align=center>".substr($bar->awalpenyusutan,5,2)."-".substr($bar->awalpenyusutan,0,4)."</td>
			  <td>".$bar->keterangan."</td>
                          <td>".$nmKary[$bar->user]."</td>
                          <td>".$bar->update."</td>
			  <td>".$kamusleasing[$bar->leasing]."</td>
			  <td>
			   <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAsset('".$bar->kodeorg."','".$bar->tipeasset."','".$bar->kodeasset."','".$bar->namasset."','".$bar->kodebarang."','".$bar->tahunperolehan."','".$bar->stat."','".$bar->hargaperolehan."','".$bar->jlhblnpenyusutan."','".$bar->awalpenyusutan."','".$bar->keterangan."','".$bar->leasing."','".$bar->penambah."','".$bar->pengurang."','".$bar->refbayar."','".$bar->dokpengadaan."','".$bar->persendecline."','".$bar->posisiasset."');\">
		      &nbsp <!--<img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAsset('".$bar->kodeorg."','".$bar->kodeasset."');\">-->
			  </td>
		   </tr>
		   </tr>";		
	}
  echo"<tr><td colspan=12 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariAsset(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariAsset(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";	
?>
