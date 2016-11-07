<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
#================untuk filter nama
if(isset($_POST['tipekaryawan'])){
 #kamus tipe karyawan
$str="select id,tipe from ".$dbname.".sdm_5tipekaryawan";
$grr=mysql_query($str);
while($bar=mysql_fetch_object($grr)){
    $tkar[$bar->id]=$bar->tipe;
}

    
 $tip= $_POST['tipekaryawan'];
 $lok=$_POST['lokasitugas'];
//get karyawan

//get karyawan
if(substr($_SESSION['empl']['lokasitugas'],2,2)=='HO')
{
  $str=" select nik,karyawanid,namakaryawan,bagian,subbagian,lokasitugas,tipekaryawan from ".$dbname.".datakaryawan
       where tanggalkeluar='0000-00-00' and tipekaryawan=0  and lokasitugas='".$lok."' order by namakaryawan";	
}
else if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
       if($tip=='%')
           $tip='tipekaryawan in(1,2,3,6,4)';
       else if($tip=='0')
            exit(" Error: you don`t have permission");
       else {
           $tip="tipekaryawan='".$tip."'";
       }
       
       if($lok=='%')
            $lok= "left(lokasitugas,4) in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";  
       else
            $lok="lokasitugas='".$lok."'";
       
       $whr=$tip." and ".$lok;
     $str=" select nik,karyawanid,namakaryawan,bagian,subbagian,lokasitugas,tipekaryawan from ".$dbname.".datakaryawan
       where   ".$whr." and tanggalkeluar='0000-00-00'";
}
else
{
       if($tip=='%')
           $tip='tipekaryawan in(1,2,3,6,4)';
       else if($tip=='0')
            exit("Error: you don`t have permission");
       else {
           $tip="tipekaryawan='".$tip."'";
       }
       
 $str=" select nik,karyawanid,namakaryawan,bagian,subbagian,lokasitugas,tipekaryawan from ".$dbname.".datakaryawan
       where left(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
       and ".$tip."
       and tanggalkeluar='0000-00-00' order by namakaryawan";   
}
$optkar="<option value=''></option>";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        $optkar.="<option value='".$bar->karyawanid."'>".$bar->nik." | ".$bar->namakaryawan." | ".$tkar[$bar->tipekaryawan]." | ".$bar->lokasitugas." | ".$bar->subbagian."</option>";
}
 echo    $optkar;
    exit();
}

#end filter nama=========================
//limit/page
$limit=20;
$page=0;
//========================
//ambil jumlah baris dalam tahun ini
  if(isset($_POST['tex']))
  {
  	$notransaksi.=$_POST['tex'];
  }
//get karyawan
if(substr($_SESSION['empl']['lokasitugas'],2,2)=='HO')
{
$str="select count(*) as jlhbrs from ".$dbname.".sdm_suratperingatan a
    left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
    where b.namakaryawan like '%".$notransaksi."%' and kodeorg in (select kodeunit from ".$dbname.".bgt_regional_assignment
   where regional='".$_SESSION['empl']['regional']."') order by jlhbrs desc";
}
else if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
    $str="select count(*) as jlhbrs from ".$dbname.".sdm_suratperingatan a
    left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
    where b.namakaryawan like '%".$notransaksi."%'
   and b.lokasitugas in(select kodeunit from ".$dbname.".bgt_regional_assignment
   where regional='".$_SESSION['empl']['regional']."') and b.lokasitugas not like '%HO' order by jlhbrs desc";
}
else
{
$str="select count(*) as jlhbrs from ".$dbname.".sdm_suratperingatan a
    left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
    where b.namakaryawan like '%".$notransaksi."%'
    and left(a.nomor,4)='".$_SESSION['empl']['lokasitugas']."'
    and b.tipekaryawan in(1,2,3,6) order by jlhbrs desc";
}  
  

//echo $str;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$jlhbrs=$bar->jlhbrs;
}		
//==================
		 
  if(isset($_POST['page']))
     {
            $page=$_POST['page'];
        if($page<0)
              $page=0;
	 }
	 
  
  $offset=$page*$limit;
  

  if(substr($_SESSION['empl']['lokasitugas'],2,2)=='HO')
{
$str="select a.*,b.tipekaryawan from ".$dbname.".sdm_suratperingatan a
    left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
    where b.namakaryawan like '%".$notransaksi."%' and kodeorg in (select kodeunit from ".$dbname.".bgt_regional_assignment
   where regional='".$_SESSION['empl']['regional']."') limit ".$offset.",20";
}
else if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
    $str="select a.*,b.tipekaryawan from ".$dbname.".sdm_suratperingatan a
    left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
    where b.namakaryawan like '%".$notransaksi."%'
   and b.lokasitugas in(select kodeunit from ".$dbname.".bgt_regional_assignment
   where regional='".$_SESSION['empl']['regional']."') and b.lokasitugas not like '%HO'  limit ".$offset.",20";
}
else
{
$str="select a.*,b.tipekaryawan from ".$dbname.".sdm_suratperingatan a
    left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
    where b.namakaryawan like '%".$notransaksi."%'
    and left(a.nomor,4)='".$_SESSION['empl']['lokasitugas']."'
    and b.tipekaryawan in(1,2,3,6,4)  limit ".$offset.",20";
}  

	
  $res=mysql_query($str);
  $no=$page*$limit;
  while($bar=mysql_fetch_object($res))
  {
  	$no+=1;

	  $namakaryawan='';
	  $strx="select namakaryawan from ".$dbname.".datakaryawan where karyawanid=".$bar->karyawanid;

	  $resx=mysql_query($strx);
	  while($barx=mysql_fetch_object($resx))
	  {
	  	$namakaryawan=$barx->namakaryawan;
	  }
	//====================ambil username pembuat
	  $namapembuat='';
	  $stry="select namakaryawan from ".$dbname.".datakaryawan where karyawanid=".$bar->updateby;
	  $resy=mysql_query($stry);
	  while($bary=mysql_fetch_object($resy))
	  {
	  	$namapembuat=$bary->namakaryawan;
	  }   

	echo"<tr class=rowcontent>
	  <td>".$no."</td>
	  <td>".$bar->nomor."</td>
	  <td>".$namakaryawan."</td>
	  <td>".tanggalnormal($bar->tanggal)."</td>
	  <td>".tanggalnormal($bar->sampai)."</td>
	  <td>".$bar->jenissp."</td>
	  <td>".$namapembuat."</td>	
	  <td align=center>";
                      if($_SESSION['empl']['tipelokasitugas']=='KANWIL' && $bar->tipekaryawan=='0'){
	     echo"<img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewSP('".$bar->nomor."',event);\">";
	      } else{
	     echo"<img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewSP('".$bar->nomor."',event);\"> 
		 &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delSP('".$bar->nomor."','".$bar->karyawanid."');\">
		 &nbsp <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editSP('".$bar->nomor."','".$bar->karyawanid."');\">";                  
                            } 
                    echo"</td>
	  </tr>";
  }
  echo"<tr><td colspan=11 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariSP(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariSP(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";
?>