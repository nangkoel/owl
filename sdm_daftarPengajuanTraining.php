<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src='js/sdm_daftarPengajuanTraining.js'></script>
<?php
OPEN_BOX('',$_SESSION['lang']['rencanatraining']);

//ambil karyawan permanen yang belum keluar
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
      where tipekaryawan=0 order by namakaryawan";
$optKar="<option value=''></option>";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $optKar.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
    $nam[$bar->karyawanid]=$bar->namakaryawan;
}	  	


$frm[0]="<fieldset>
	   <legend>".$_SESSION['lang']['list']."</legend>
	  <fieldset><legend></legend>
	  ".$_SESSION['lang']['find']." : 
	  <select id=pilihkaryawan onchange=loadList()>".$optKar."</select>
	  </fieldset>
	  <table class=sortable cellspacing=1 border=0>
      <thead>
	  <tr class=rowheader>
	  <td>No.</td>
	  <td>".$_SESSION['lang']['namakaryawan']."</td>
	  <td>".$_SESSION['lang']['namatraining']."</td>
	  <td>".$_SESSION['lang']['tanggalmulai']."</td>
	  <td>".$_SESSION['lang']['hargaperpeserta']."</td>
	  <td>".$_SESSION['lang']['tanggalsampai']."</td>
	  <td>".$_SESSION['lang']['action']."</td>
	  </tr>
	  </head>
	   <tbody id=containerlist>";
$limit=20;
$page=0;
//========================
//ambil jumlah baris dalam tahun ini
  if(isset($_POST['pilihkaryawan']))
  {
  	$pilihkaryawan.=$_POST['karyawanid'];
  }
$str="select count(*) as jlhbrs from ".$dbname.".sdm_5training 
        where karyawanid like '%".$pilihkaryawan."%'
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

  $saya=$_SESSION['standard']['userid'];

  $str="select * from ".$dbname.".sdm_5training 
        where karyawanid like '%".$pilihkaryawan."%'
		order by tglmulai desc,tglselesai desc,updatetime desc  limit ".$offset.",20";	
  $res=mysql_query($str);
  $no=$page*$limit;
  while($bar=mysql_fetch_object($res))
  {
      if($bar->persetujuan1==$saya)$sayaadalah='atasan';
      if($bar->persetujuanhrd==$saya)$sayaadalah='hrd';
  	$no+=1;
	$frm[1].="<tr class=rowcontent>
	  <td>".$no."</td>
	  <td>".$nam[$bar->karyawanid]."</td>
	  <td>".$bar->namatraining."</td>
	  <td align=center>".tanggalnormal($bar->tglmulai)."</td>
	  <td align=right>".number_format($bar->hargasatuan)."</td>
	  <td align=center>".tanggalnormal($bar->tglselesai)."</td>
	  <td align=center>
             <button class=mybutton onclick=\"lihatpdf(event,'sdm_slave_5rencanatraining.php','".$bar->kode."','".$bar->karyawanid."');\">".$_SESSION['lang']['pdf']."</button>";
             if((($bar->persetujuan1==$saya)and($bar->stpersetujuan1==0))or(($bar->persetujuanhrd==$saya)and($bar->sthrd==0)))
             $frm[1].="<button class=mybutton onclick=tolak('".$bar->kode."','".$bar->karyawanid."','".$sayaadalah."',event)>".$_SESSION['lang']['tolak']."</button>
             <button class=mybutton onclick=setuju('".$bar->kode."','".$bar->karyawanid."','".$sayaadalah."',event)>".$_SESSION['lang']['setuju']."</button>";
	  $frm[1].="</td>
	  </tr>"; // dz note: pdf tembak langsung ke file Pengajuan Training
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

$hfrm[0]=$_SESSION['lang']['list'];
 	 
drawTab('FRM',$hfrm,$frm,100,900);
CLOSE_BOX();
echo close_body('');
?>