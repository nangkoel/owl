<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/sdm_pertanggungjawabanPJD.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['pertanggungjawabandinas']);

$frm[2]="<fieldset>
	   <legend>".$_SESSION['lang']['list']."</legend>
	  <fieldset><legend></legend>
	  ".$_SESSION['lang']['cari_transaksi']."
	  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>
	  <button class=mybutton onclick=cariPJDUraian(0)>".$_SESSION['lang']['find']."</button>
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
		persetujuan=".$_SESSION['standard']['userid']."
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
        persetujuan=".$_SESSION['standard']['userid']."
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
	 /*
	  if($bar->statuspertanggungjawaban==0)
	  {
	  	$add.="&nbsp <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editPPJD('".$bar->notransaksi."');\">
         ";
	  }
	 */ 
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
       <button class=mybutton onclick=cariPJDUraian(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariPJDUraian(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";	   
$frm[2].="</tbody>
	   <tfoot>
	   </tfoot>
	   </table>
	 </fieldset>"; 
	 
 
//==================================================	 	 
echo $frm[2];
	 
//drawTab('FRM',$hfrm,$frm,100,900);	  
CLOSE_BOX();
echo close_body();
?>