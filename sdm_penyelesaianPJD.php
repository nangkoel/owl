<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/sdm_penyelesaianPJD.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['penyelesaianpjd']);
	
		
//=====================================
echo"<fieldset>
	   <legend>".$_SESSION['lang']['list']."</legend>
	  ".$_SESSION['lang']['cari_transaksi']."
	  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>
	  <button class=mybutton onclick=cariPJD(0)>".$_SESSION['lang']['find']."</button>
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
	  <td>".$_SESSION['lang']['saldo']."</td>
                    
	  <td>".$_SESSION['lang']['action']."</td>
	  </tr>
	  </head>
	   <tbody id=containerlist>";//  <td>".$_SESSION['lang']['biaya']." ".$_SESSION['lang']['ticket']."(PP)</td>
$limit=20;
$page=0;
//========================
//ambil jumlah baris dalam tahun ini
  if(isset($_POST['tex']))
  {
  	$notransaksi.=" and notransaksi like '%".$_POST['tex']."%' ";
  } 
  if ($_SESSION['empl']['bagian']=='IT'){
      $whrLokTgs="";
  } else {
    if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
        $whrLokTgs=" and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
    } else {
        $whrLokTgs=" and kodeorg not like '%HO' and kodeorg in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
    }
  }
$str="select count(*) as jlhbrs from ".$dbname.".sdm_pjdinasht 
        where statuspertanggungjawaban=1 and lunas=0
		".$notransaksi.$whrLokTgs."
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
        where  statuspertanggungjawaban=1 and lunas=0
		".$notransaksi.$whrLokTgs."
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
	  
	//==================ambil jumlah pertanggungjawaban
	$strv="select sum(jumlahhrd) as vali from ".$dbname.".sdm_pjdinasdt
	       where notransaksi='".$bar->notransaksi."'";
	$vali=0;
	$resv=mysql_query($strv);
	while($barv=mysql_fetch_object($resv))
	{
		$vali=$barv->vali;
	}	   
	 //sisa adalah dp diterima kurang penggunaan
	 $vali=$bar->dibayar-$vali;	  
	//===============================================  
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
	  
	echo"<tr class=rowcontent>
	  <td>".$no."</td>
	  <td>".$bar->notransaksi."</td>
	  <td>".$namakaryawan."</td>
	  <td>".tanggalnormal($bar->tanggalbuat)."</td>
	  <td>".$bar->tujuan1."</td>
	  <td align=right>".number_format($bar->dibayar,2,'.',',')."</td>
	  <td align=right>".number_format($usage,2,'.',',')."</td>
	  <td>".$stpersetujuan."</td>
	  <td align=right>".number_format($vali,2,'.',',')."</td>
                     
	  <td align=center> 
                        
                         <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\">    
	  </td>
	  </tr>";
  }/*  <td align=right><input type=text class=myinputtextnumber size=14 onkeypress=\"return angka_doang(event);\" value=".($bar->notransaksi!=0?$bar->byticket:0)." id=t".$bar->notransaksi." title='Tidak termasuk tiket yang dibayar oleh  karyawan dari uang muka'></td>
  <button class=mybutton onclick=savePenyelesaianPJD('".$bar->notransaksi."','".$vali."')>".$_SESSION['lang']['save']."</button>*/
  echo"<tr><td colspan=11 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariPJD(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariPJD(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";	   
  echo "</tbody>
	   <tfoot>
	   </tfoot>
	   </table>
	 </fieldset>";   
CLOSE_BOX();
echo close_body();
?>