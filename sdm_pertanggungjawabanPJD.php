<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/sdm_pertanggungjawabanPJD.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['pertanggungjawabandinas']);

  $str="select * from ".$dbname.".sdm_pjdinasht 
        where
        karyawanid=".$_SESSION['standard']['userid']."
		and lunas=0 and statuspertanggungjawaban=0";
  $res=mysql_query($str);
  $optNo='';
  while($bar=mysql_fetch_object($res))
  {
  	$optNo.="<option value='".$bar->notransaksi."'>".$bar->notransaksi."</option>";
  }		
	
$str="select * from ".$dbname.".sdm_5jenisbiayapjdinas order by keterangan";
$res=mysql_query($str);
$optJns='';
while($bar=mysql_fetch_object($res))
{
	$optJns.="<option value='".$bar->id."'>".$bar->keterangan."</option>";
}		
$frm[0].="<fieldset>
     <legend>".$_SESSION['lang']['form']."</legend>
	 <table>
		<tr>
		   <td>".$_SESSION['lang']['notransaksi']."</td>
		   <td><select id=notransaksi>".$optNo."</select>
		    <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJDOri(event);\"> 
		   </td>		   
		</tr>	
	 </table>
	 <fieldset>
	    <legend>".$_SESSION['lang']['detail']."</legend>
		<table>
		 <tr>
		    <td>".$_SESSION['lang']['tanggal']."<input type=hidden id=method value=insert></td>
			<td><input type=text size=10 id=tanggal class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this)></td>
			<td>".$_SESSION['lang']['jenisbiaya']."</td>
		    <td><select id=jenisby>".$optJns."</select></td>
			<td>".$_SESSION['lang']['keterangan']."</td>
			<td><input type=text id=keterangan size=30 maxlength=100 class=myinputtext onkeypress=\"return tanpa_kutip(event);\">
		    </td>
			<td>".$_SESSION['lang']['jumlah']."</td>
			<td><input type=text id=jumlah size=12 maxlength=15 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=change_number(this)>
			</td>
			<td>
			  <button class=mybutton onclick=savePPJD()>".$_SESSION['lang']['save']."</button>
			</td>
		</table>
	 </fieldset>
	 <fieldset>
	    <legend>".$_SESSION['lang']['datatersimpan']."</legend>
		<table class=sortable cellspacing=1>
		<thead>
		 <tr>
		    <td>No.</td>
                    <td>".$_SESSION['lang']['tanggal']."</td>
		    <td>".$_SESSION['lang']['jenisbiaya']."</td>
			<td>".$_SESSION['lang']['keterangan']."</td>
			<td>".$_SESSION['lang']['jumlah']."</td>
		    <td></td>
		</tr>	
		 </thead>	
		 <tbody id=innercontainer>
		 </tbody>
		 <tfoot>
		 </tfoot>
		</table>
		<button class=mybutton onclick=selesai()>".$_SESSION['lang']['done']."</button>
	 </fieldset>
	 </fieldset>
	 "; 
//=======================================
$frm[1]="<fieldset>
         <legend>Description of the results of official travel</legend>
		 <textarea id=uraian cols=60 rows=15 onkeypress=\"return tanpa_kutip(event);\"></textarea><br>
		 <button class=mybutton onclick=simpanUraianPjDinas()>".$_SESSION['lang']['save']."</button>
         </fieldset>
		 ";		 
//=====================================
$frm[2]="<fieldset>
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
	  <td>".$_SESSION['lang']['uangmuka']."</td>
	  <td>".$_SESSION['lang']['digunakan']."</td>	  
	  <td>".$_SESSION['lang']['approval_status']."</td>	  
	  <td></td>
	  </tr>
	  </head>
	   <tbody id=containerlist>";
$limit=20;
$page=0;
//========================
//ambil jumlah baris dalam tahun ini
  if(isset($_POST['tex']))
  {
  	$notransaksi.=" and notransaksi like '%".$_POST['tex']."%' ";
  } 
$str="select count(*) as jlhbrs from ".$dbname.".sdm_pjdinasht 
        where
		karyawanid=".$_SESSION['standard']['userid']."
		".$notransaksi."
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
        where
        karyawanid=".$_SESSION['standard']['userid']."
		".$notransaksi."
		order by tanggalbuat desc  limit ".$offset.",20";	
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
	  if($bar->statuspertanggungjawaban==0)
	  {
	  	$add.="&nbsp <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editPPJD('".$bar->notransaksi."');\">
         ";
	  }
   if($bar->statuspertanggungjawaban==2)
     $stpersetujuan=$_SESSION['lang']['ditolak'];
   else if($bar->statuspertanggungjawaban==1)
    $stpersetujuan=$_SESSION['lang']['disetujui'];
   else 
    $stpersetujuan=$_SESSION['lang']['wait_approve'];	  
   
   $str1="select sum(jumlah) as jumlah from ".$dbname.".sdm_pjdinasdt
         where notransaksi='".$bar->notransaksi."'";
   $res1=mysql_query($str1);

   $usage=0;
   while($bar1=mysql_fetch_object($res1))
   {
   	 $usage=$bar1->jumlah;
   }	 	 
	  
	$frm[2].="<tr class=rowcontent>
	  <td>".$no."</td>
	  <td>".$bar->notransaksi."</td>
	  <td>".$namakaryawan."</td>
	  <td>".tanggalnormal($bar->tanggalbuat)."</td>
	  <td>".$bar->tujuan1."</td>
	  <td align=right>".number_format($bar->dibayar,2,'.',',')."</td>
	  <td align=right>".number_format($usage,2,'.',',')."</td>
	  <td>".$stpersetujuan."</td>
	  <td align=center>
	     <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']." (Cost)' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> 
		 <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']." (Task Result Description)' onclick=\"previewPJDUraian('".$bar->notransaksi."',event);\"> 
       ".$add."
	  </td>
	  </tr>";
  }
  $frm[2].="<tr><td colspan=11 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariPJD(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariPJD(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";	   
$frm[2].="</tbody>
	   <tfoot>
	   </tfoot>
	   </table>
	 </fieldset>"; 
	 
 
//==================================================	 	 
$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['hasilkerjajumlah'];
$hfrm[2]=$_SESSION['lang']['list'];
	 
drawTab('FRM',$hfrm,$frm,100,900);	  
CLOSE_BOX();
echo close_body();
?>