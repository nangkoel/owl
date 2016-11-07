<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

  $saya=$_SESSION['standard']['userid'];
  
    $method=$_POST['method'];
if($method!=''){ // alasan ditolak/disetujui
    $kodetraining=$_POST['kodetraining'];
    $karyawanid=$_POST['karyawanid'];
    $sayaadalah=$_POST['sayaadalah'];
    $alasannya=$_POST['alasannya'];
    if($method=='alasanditolak'){
        if($sayaadalah=='atasan'){
            $str="UPDATE ".$dbname.".`sdm_5training` SET `stpersetujuan1` = '2',
`catatan1` = '".$alasannya."' WHERE `kode` = '".$kodetraining."' AND `karyawanid` =".$karyawanid." ";
        }else{ // hrd
            $str="UPDATE ".$dbname.".`sdm_5training` SET `sthrd` = '2',
`catatanhrd` = '".$alasannya."' WHERE `kode` = '".$kodetraining."' AND `karyawanid` =".$karyawanid." ";
            
        }
    }else{ // alasansetuju
        if($sayaadalah=='atasan'){
            $str="UPDATE ".$dbname.".`sdm_5training` SET `stpersetujuan1` = '1',
`catatan1` = '".$alasannya."' WHERE `kode` = '".$kodetraining."' AND `karyawanid` =".$karyawanid." ";
        }else{ // hrd
            $str="UPDATE ".$dbname.".`sdm_5training` SET `sthrd` = '1',
`catatanhrd` = '".$alasannya."' WHERE `kode` = '".$kodetraining."' AND `karyawanid` =".$karyawanid." ";
            
        }
        
    }
    
//    echo "error:".$str;
    
        if(mysql_query($str))
    {
        
    }
    else
    {
        echo " Gagal, ".addslashes(mysql_error($conn));   
        exit;
    }

    
}  

if($method==''){
    $method=$_GET['method'];
    if(($method=='tolak')||($method=='setuju')){
    $kodetraining=$_GET['kodetraining'];
    $karyawanid=$_GET['karyawanid'];
    $sayaadalah=$_GET['sayaadalah'];
    
    if($method=='tolak'){
        $tulisanalasan=$_SESSION['lang']['alasanDtolak'];
        $scriptalasan='alasanditolak';
    }else{ // setuju
        $tulisanalasan=$_SESSION['lang']['alasanDterima'];        
        $scriptalasan='alasandisetujui';
    }
    
echo"<link rel=stylesheet type='text/css' href='style/generic.css'>
    <script language=javascript src='js/sdm_daftarPengajuanTraining.js'></script>
";

    echo"<table cellspacing=1 border=0 style='width:500px;'>
         <thead>
         <tr class=rowheader>
            <td>".$tulisanalasan."</td>
            <td><textarea rows=2 cols=22 id=alasannya onkeypress=\"return parent.tanpa_kutip();\"></textarea></td>
            <td><button class=mybutton onclick=".$scriptalasan."('".$kodetraining."','".$karyawanid."','".$sayaadalah."')>".$_SESSION['lang']['save']."</button></td>
         </tr></thead>
         <tbody>";
    echo"</tbody>
        <tfoot>
        </tfoot>
        </table>";
    
    
    exit;
        
    }
}


//ambil karyawan permanen yang belum keluar
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
      where tipekaryawan=0 order by namakaryawan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $nam[$bar->karyawanid]=$bar->namakaryawan;
}	  	

$limit=20;
$page=0;
//========================
//ambil jumlah baris dalam tahun ini
  if(isset($_POST['pilihkaryawan']))
  {
  	$pilihkaryawan=$_POST['pilihkaryawan'];
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
	echo"<tr class=rowcontent>
	  <td>".$no."</td>
	  <td>".$nam[$bar->karyawanid]."</td>
	  <td>".$bar->namatraining."</td>
	  <td align=center>".tanggalnormal($bar->tglmulai)."</td>
	  <td align=right>".number_format($bar->hargasatuan)."</td>
	  <td align=center>".tanggalnormal($bar->tglselesai)."</td>
	  <td align=center>
             <button class=mybutton onclick=\"lihatpdf(event,'sdm_slave_5rencanatraining.php','".$bar->kode."','".$bar->karyawanid."');\">".$_SESSION['lang']['pdf']."</button>";
             if((($bar->persetujuan1==$saya)and($bar->stpersetujuan1==0))or(($bar->persetujuanhrd==$saya)and($bar->sthrd==0)))
             echo"<button class=mybutton onclick=tolak('".$bar->kode."','".$bar->karyawanid."','".$sayaadalah."',event)>".$_SESSION['lang']['tolak']."</button>
             <button class=mybutton onclick=setuju('".$bar->kode."','".$bar->karyawanid."','".$sayaadalah."',event)>".$_SESSION['lang']['setuju']."</button>";
	  echo"</td>
	  </tr>";
  }
  echo"<tr><td colspan=11 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariPJD(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariPJD(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";	   

?>