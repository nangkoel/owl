<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src='js/sdm_pjdinas.js'></script>
<?php
OPEN_BOX('',$_SESSION['lang']['perjalanandinas']);
//ambil karyawan permanen
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
      where tipekaryawan=0 and karyawanid <>".$_SESSION['standard']['userid']. " and tanggalkeluar='0000-00-00' and kodegolongan in ('6b','7a','7b','8') and lokasitugas like '%HO' order by namakaryawan";
} else {
    if ($_SESSION['empl']['tipelokasitugas']=='KEBUN'){
        $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
          where tipekaryawan=0 and karyawanid <>".$_SESSION['standard']['userid']. " and tanggalkeluar='0000-00-00' and kodegolongan>='5' and kodegolongan <='7' and lokasitugas in 
          (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') and lokasitugas not like '%HO' order by namakaryawan";
    } else {
        $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
          where tipekaryawan=0 and karyawanid <>".$_SESSION['standard']['userid']. " and tanggalkeluar='0000-00-00' and kodegolongan >= '6b' and lokasitugas in 
          (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by namakaryawan";
    }
}
$res=mysql_query($str);
$optKar="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optKar.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
}	  	


#atasan dari atasan
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
          where tipekaryawan=0 and kodegolongan>='7B' and tanggalkeluar='0000-00-00' and karyawanid <>".$_SESSION['standard']['userid']. " ".$whr." and lokasitugas like '%HO' order by namakaryawan";
} else {
    if ($_SESSION['empl']['kodegolongan']<'7B'){
        $whr=" and lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') ";
    }
    $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
          where tipekaryawan=0 and kodegolongan>='7A' and tanggalkeluar='0000-00-00' and karyawanid <>".$_SESSION['standard']['userid']. " ".$whr." order by namakaryawan";
}
$res=mysql_query($str);
$optKar2="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optKar2.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
}	 


#HRD
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    if ($_SESSION['empl']['kodejabatan']==21 or $_SESSION['empl']['kodejabatan']==33){ // jika user adalah manager HRD
        $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
          where tipekaryawan=0 and bagian='HRD' and kodejabatan in (21,33) and tanggalkeluar='0000-00-00' and lokasitugas like '%HO' order by namakaryawan";
    } else {
        $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
          where tipekaryawan=0 and bagian='HRD' and kodejabatan in (21,33) and tanggalkeluar='0000-00-00' and karyawanid <>".$_SESSION['standard']['userid']. " and lokasitugas like '%HO' order by namakaryawan";
    }
} else {
    if ($_SESSION['empl']['kodejabatan']==21 or $_SESSION['empl']['kodejabatan']==33){ // jika user adalah manager HRD
        $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
          where tipekaryawan=0 and bagian='HRD' and kodejabatan in (21,33) and tanggalkeluar='0000-00-00' and lokasitugas in 
          (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by namakaryawan";
    } else {
        $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
          where tipekaryawan=0 and bagian='HRD' and kodejabatan in (21,33) and tanggalkeluar='0000-00-00' and karyawanid <>".$_SESSION['standard']['userid']. " and lokasitugas in 
          (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by namakaryawan";
    }
}
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optKarHrd.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
}


$str="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
      where length(kodeorganisasi)=4 order by namaorganisasi";
$res=mysql_query($str);
echo mysql_error($conn);
$optOrg="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optOrg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}	  

$lokasitugas=$_SESSION['empl']['lokasitugas'];
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
      where karyawanid=".$_SESSION['standard']['userid'];	  
$namakaryawan='';
$karyawanid='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namakaryawan	=$bar->namakaryawan;
	$karyawanid		=$bar->karyawanid;
}	  

$frm[0].="
     <fieldset>
	  <legend>".$_SESSION['lang']['form']."</legend>
     <table>
	 <tr>
	   <input type=hidden value='insert' id=method>
	   <input type=hidden value='' id=notransaksi>
	    <td>".$_SESSION['lang']['nama']."</td>
		<td><select id='karyawanid'><option value='".$karyawanid."'>".$namakaryawan."</option></select></td>
	 </tr>
	 <tr>
	    <td>".$_SESSION['lang']['kodeorg']."</td>
		<td><select id='kodeorg'><option  value='".$lokasitugas."'>".$lokasitugas."</option></select></td>
	 </tr>	 
	 <tr>
	    <td>".$_SESSION['lang']['tanggaldinas']."</td>
		<td><input type=text id=tanggalperjalanan class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this) size=10>
		    ".$_SESSION['lang']['tanggalkembali']." 
			<input type=text id=tanggalkembali class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this) size=10>
		</td>
	 </tr>	
	 <tr>
	    <td>".$_SESSION['lang']['transportasi']."/".$_SESSION['lang']['akomodasi']."</td>
		<td>
		     <input type=checkbox id=pesawat> ".$_SESSION['lang']['pesawatudara']."
			 <input type=checkbox id=darat> ".$_SESSION['lang']['transportasidarat']."
			 <input type=checkbox id=laut> ".$_SESSION['lang']['transportasiair']."
			 <input type=checkbox id=mess> ".$_SESSION['lang']['mess']."
			 <input type=checkbox id=hotel> ".$_SESSION['lang']['hotel']."
			 <input type=checkbox id=mobilsewa>Mobil Sewa
        </td>
	 </tr>	
	 
	 <tr>
	   <td>
	      ".$_SESSION['lang']['uangmuka']."
	   </td>
	   <td>
	     <input type=text class=myinputtextnumber onblur=change_number(this) id=uangmuka onkeypress=\"return angka_doang(event);\" size=15 maxlength=15>
	   </td>
	 </tr> 	 
	 
	  <tr>
	   <td>
	      ".$_SESSION['lang']['keterangan']."
	   </td>
	   <td>
	     <textarea  id=ket onkeypress=\"return tanpa_kutip(event);\"></textarea>
	   </td>
	 </tr> 
	 
	 	 
	 </table>
	 <table>
	   <tr>
	     <td>
		     ".$_SESSION['lang']['tujuan']."1
		 </td>
	     <td>
		   <select id='tujuan1' style='width:150px'>".$optOrg."</select>
		   ".$_SESSION['lang']['tugas']."
		   <input type=text id=tugas1 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=254>
		 </td>
		</tr>
		<tr> 
	     <td>
		    ".$_SESSION['lang']['tujuan']."2
		 </td>
	     <td>
		    <select id='tujuan2' style='width:150px'>".$optOrg."</select>
			".$_SESSION['lang']['tugas']."
			<input type=text id=tugas2 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=254>
		 </td>		 		 		 
	   </tr>
		</tr>
		<tr>	   
	   <tr>
	     <td>
		     ".$_SESSION['lang']['tujuan']."3
		 </td>
	     <td>
		   <select id='tujuan3' style='width:150px'>".$optOrg."</select>
		   ".$_SESSION['lang']['tugas']."
		   <input type=text id=tugas3 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=254>
		 </td>
		</tr>
		<tr>		 
	     <td>
		    ".$_SESSION['lang']['tujuan']."4
		 </td>
	     <td>
		    <input type=text style='width:148px' id=tujuanlain class=myinputtext onkeypress=\"return tanpa_kutip(event)\" maxlength=45>
		    ".$_SESSION['lang']['tugas']."
			<input type=text id=tugaslain class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=254>
		 </td>		 		 		 
	   </tr>
	 </table>
	   <fieldset>
	      <legend>
		    ".$_SESSION['lang']['approve']."
		  </legend>
		  <table>
		   <tr>
		     <td>".$_SESSION['lang']['atasan']."</td>
			 <td>
			    <select id=persetujuan style='width:150px'>".$optKar."</select>
			 </td>
		   </tr>
		   <tr>
		     <td>".$_SESSION['lang']['atasan']." ".$_SESSION['lang']['dari']." ".$_SESSION['lang']['atasan']."</td>
			 <td>
			    <select id=persetujuan2 style='width:150px'>".$optKar2."</select>
			 </td>
		   </tr>
		   <tr>	 
		     <td>".$_SESSION['lang']['hrd']."</td>
			 <td>
			    <select id=hrd style='width:150px'>".$optKarHrd."</select>
			 </td>			 
		   </tr>
		  </table>
	   </fieldset>	 
	 <center>
	   <button class=mybutton onclick=simpanPJD()>".$_SESSION['lang']['save']."</button>
	   <button class=mybutton onclick=clearForm()>".$_SESSION['lang']['new']."</button>
	 </center>
	 </fieldset>";

$frm[1]="<fieldset>
	   <legend>".$_SESSION['lang']['list']."</legend>
	  <fieldset><legend></legend>
	  ".$_SESSION['lang']['cari_transaksi']."
	  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>
	  <button class=mybutton onclick=cariPJD(0)>".$_SESSION['lang']['find']."</button>
	  </fieldset>
	  <table class=sortable cellspacing=1 border=0>
      <thead>
	  <tr class=rowheader>
	  <td>No.</td>
	  <td>".$_SESSION['lang']['notransaksi']."</td>
	  <td>".$_SESSION['lang']['karyawan']."</td>
	  <td>".$_SESSION['lang']['tanggalsurat']."</td>
	  <td>".$_SESSION['lang']['tujuan']."</td>
	  <td>".$_SESSION['lang']['approval_status']."</td>
	  <td>".$_SESSION['lang']['approval_status']." 2</td>
	  <td>".$_SESSION['lang']['hrd']."</td>
	  <td>".$_SESSION['lang']['action']."</td>
	  </tr>
	  </head>
	   <tbody id=containerlist>";
$limit=20;
$page=0;
//========================
//ambil jumlah baris dalam tahun ini
  if(isset($_POST['tex']))
  {
  	$notransaksi.=$_POST['tex'];
  }
$str="select count(*) as jlhbrs from ".$dbname.".sdm_pjdinasht 
        where notransaksi like '%".$notransaksi."%'
		and karyawanid=".$_SESSION['standard']['userid']."
		order by jlhbrs desc";
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

  $str="select * from ".$dbname.".sdm_pjdinasht 
        where notransaksi like '%".$notransaksi."%'
        and karyawanid=".$_SESSION['standard']['userid']."
		order by tanggalbuat desc limit ".$offset.",20";	
//		order by notransaksi desc limit ".$offset.",20";	 
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
	  $add='';
	  if($bar->statuspersetujuan==0 && $bar->statushrd==0)
	  {
	  	$add.="&nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delPJD('".$bar->notransaksi."','".$bar->karyawanid."');\">
		 &nbsp <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editPJD('".$bar->notransaksi."','".$bar->karyawanid."');\">
         ";
	  }
   if($bar->statuspersetujuan==2)
     $stpersetujuan=$_SESSION['lang']['ditolak'];
   else if($bar->statuspersetujuan==1)
    $stpersetujuan=$_SESSION['lang']['disetujui'];
   else {
    $stpersetujuan=$_SESSION['lang']['wait_approve'];	
	$stpersetujuan.="<br> &nbsp ".$_SESSION['lang']['ganti'].":<select  style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'persetujuan','".$bar->notransaksi."')>".$optKar."</select>";
   }
   
   
     if($bar->statuspersetujuan2==2)
     $stpersetujuan2=$_SESSION['lang']['ditolak'];
   else if($bar->statuspersetujuan2==1)
    $stpersetujuan2=$_SESSION['lang']['disetujui'];
   else {
    $stpersetujuan2=$_SESSION['lang']['wait_approve'];	
	$stpersetujuan2.="<br> &nbsp ".$_SESSION['lang']['ganti'].":<select  style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'persetujuan2','".$bar->notransaksi."')>".$optKar2."</select>";
   }
   
   


   if($bar->statushrd==2)
     $sthrd=$_SESSION['lang']['ditolak'];
  else if($bar->statushrd==1)
     $sthrd=$_SESSION['lang']['disetujui'];
  else{
     $sthrd=$_SESSION['lang']['wait_approve'];
	 $sthrd.="<br> &nbsp ".$_SESSION['lang']['ganti'].":<select   style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'hrd','".$bar->notransaksi."')>".$optKarHrd."</select>";
  }
  
#### cek bila persetuju pertama telah menolak   
if($bar->statuspersetujuan==2)
{
	$stpersetujuan2='';
	$sthrd='';
}
########################	  
  
	$frm[1].="<tr class=rowcontent>
	  <td>".$no."</td>
	  <td>".$bar->notransaksi."</td>
	  <td>".$namakaryawan."</td>
	  <td>".tanggalnormal($bar->tanggalbuat)."</td>
	  <td>".$bar->tujuan1."</td>
	  <td>".$stpersetujuan."</td>
	  <td>".$stpersetujuan2."</td>
	  <td>".$sthrd."</td>	
	  <td align=center>
	     <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> 
       ".$add."
	  </td>
	  </tr>";
  }
  $frm[1].="<tr><td colspan=11 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariPJD(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariPJD(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";	   
$frm[1].="</tbody>
	   <tfoot>
	   </tfoot>
	   </table>
	 </fieldset>";

$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['list'];
 	 
drawTab('FRM',$hfrm,$frm,100,900);
CLOSE_BOX();
echo close_body('');
?>