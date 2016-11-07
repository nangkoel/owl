<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//	$pt=$_POST['pt'];
        $unit=$_GET['unit'];
        $periode=$_GET['periode'];

if($periode==''){
        echo "Warning: silakan mengisi periode"; exit;
}
$str="select induk from ".$dbname.".organisasi
      where kodeorganisasi ='".$unit."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        $induk=$bar->induk;
        $hasil.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}
$str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
      where kodeorg ='".$unit."' and periode='".$periode."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        $tanggalmulai=$bar->tanggalmulai;
        $tanggalsampai=$bar->tanggalsampai;
}

/*
$str="select distinct kodebarang, namabarang from ".$dbname.".log_5masterbarang";
$res=mysql_query($str);
$optper="";
while($bar=mysql_fetch_object($res))
{
        $barang[$bar->kodebarang]=$bar->namabarang;
}	

*/
if($_SESSION['language']=='EN'){
    $zz=' b.namaakun1';
}
else{
    $zz='b.namaakun';
}
        $str="select a.nojurnal as nojurnal, a.tanggal as tanggal, a.keterangan as keterangan, a.noakun as noakun, ".$zz." as namaakun, a.debet as debet, a.kredit as kredit, a.kodeblok as kodeorg, a.kodevhc as kodevhc  
                  from ".$dbname.".keu_jurnaldt_vw a
                  left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
                  where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and a.noreferensi in ('ALK_KERJA_AB') and a.kodeorg = '".$unit."' 
                  order by a.tanggal";

//echo"str :".$str;
//=================================================
        $res=mysql_query($str);
        $no=0;
        if(mysql_num_rows($res)<1)
        {
                echo"<tr class=rowcontent><td colspan=10>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
        }
        else
        {
                $stream.=$_SESSION['lang']['penerimaanalokasitraksi'].": ".$unit." : ".$periode."<br>
                <table border=1>
                                    <tr>
                          <td bgcolor=#DEDEDE align=center>No.</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nojurnal']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noakun']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namaakun']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['debet']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kredit']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodeblok']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodevhc']."</td>
                                        </tr>";
        while($bar=mysql_fetch_object($res))
        {
                $no+=1; $total=0;
                $stream.="<tr>
                                  <td align=right>".$no."</td>
                                  <td>".$bar->nojurnal."</td>
                                  <td align=right>".$bar->tanggal."</td>
                                  <td nowrap>".$bar->keterangan."</td>
                                  <td align=right>".$bar->noakun."</td>
                                  <td nowrap>".$bar->namaakun."</td>
                                  <td align=right>".number_format($bar->debet)."</td>
                                  <td align=right>".number_format($bar->kredit)."</td>
                                  <td>".$bar->kodeorg."</td>
                                  <td>".$bar->kodevhc."</td>
                        </tr>"; 	
        }

        $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
  }

$nop_="PenerimaanAlokasiTraksi_".$unit."_".$periode;
if(strlen($stream)>0)
{
if ($handle = opendir('tempExcel')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            @unlink('tempExcel/'.$file);
        }
    }	
   closedir($handle);
}
 $handle=fopen("tempExcel/".$nop_.".xls",'w');
 if(!fwrite($handle,$stream))
 {
  echo "<script language=javascript1.2>
        parent.window.alert('Can't convert to excel format');
        </script>";
   exit;
 }
 else
 {
  echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls';
        </script>";
 }
closedir($handle);
}
?>