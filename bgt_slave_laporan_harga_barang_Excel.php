<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tab=$_GET['tab'];
$tahunbudget0=$_GET['tahunbudget0'];
$regional0=$_GET['regional0'];
$kelompokbarang0=$_GET['kelompokbarang0'];
        
//kamus barang
$str="select kodebarang, namabarang, satuan from ".$dbname.".log_5masterbarang
    ";
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
   $namabarang[$bar->kodebarang]=$bar->namabarang;
   $satuanbarang[$bar->kodebarang]=$bar->satuan;
}
	
//check, one-two
if($tahunbudget0==''){
    echo "WARNING: silakan mengisi tahunbudget."; exit;
}
if($regional0==''){
    echo "WARNING: silakan mengisi regional."; exit;
}

//echo $tahun.$kebun;

    $str="select kode, kelompok from ".$dbname.".log_5klbarang
                    order by kode 
                    ";
            $artikelompok['']=$_SESSION['lang']['all'];
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $artikelompok[$bar->kode]=$bar->kelompok;
    }

    $hkef='';
//    $hkef.="<span id=printPanel style='display:none;'>
        $hkef.="<table><tr>
            <td colspan=2 align=left>".$_SESSION['lang']['budgetyear']."</td>
            <td colspan=4 align=left>: ".$tahunbudget0."</td>
        </tr>
        <tr>
            <td colspan=2 align=left>".$_SESSION['lang']['regional']."</td>
            <td colspan=4 align=left>: ".$regional0."</td>
        </tr>
        <tr>
            <td colspan=2 align=left>".$_SESSION['lang']['kelompokbarang']."</td>
            <td colspan=4 align=left>: ".$kelompokbarang0." ".$artikelompok[$kelompokbarang0]."</td>
        </tr>
        </table>";

    $hkef.="<table id=container00 class=sortable cellspacing=1 border=1 width=100%>
     <thead>
        <tr bgcolor=#DEDEDE>
            <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
            <td align=center>".$_SESSION['lang']['kodebarang']."</td>
            <td align=left>".$_SESSION['lang']['namabarang']."</td>
            <td align=left>".$_SESSION['lang']['satuan']."</td>
            <td align=center>".$_SESSION['lang']['hargabudget']."</td>
            <td align=center>".$_SESSION['lang']['hargatahunlalu']."</td>
       </tr>  
     </thead>
     <tbody>";
    if($tab=='1')
    $str="select * from ".$dbname.".bgt_masterbarang
        where closed = 1 and tahunbudget = '".$tahunbudget0."' and regional = '".$regional0."' and kodebarang like '".$kelompokbarang0."%'";
    if($tab=='2')
    $str="select a.* from ".$dbname.".bgt_masterbarang a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
        where a.closed = 1 and a.tahunbudget = '".$tahunbudget0."' and a.regional = '".$regional0."' and b.namabarang like '%".$kelompokbarang0."%'";
    $res=mysql_query($str);
    $no=0;
    while($bar= mysql_fetch_object($res))
    {
    $no+=1;
    $hkef.="<tr class=rowcontent>
            <td align=center>".$no."</td>
            <td align=center>".$bar->kodebarang."</td>
            <td align=left>".$namabarang[$bar->kodebarang]."</td>
            <td align=left>".$satuanbarang[$bar->kodebarang]."</td>
            <td align=right>".number_format($bar->hargasatuan,2)."</td>
            <td align=right>".number_format($bar->hargalalu,2)."</td>
       </tr>";
    }
    if($no==0){
    $hkef.="<tr>
            <td colspan= 6 align=center>Data tidak ada atau belum ditutup.</td>
       </tr>";
    }

    $hkef.="</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
$hkef.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
$qwe=date("YmdHms");

$nop_="bgt_hargabarang".$tahunbudget0." ".$regional0." ".$kelompokbarang0." ".$qwe;
if(strlen($hkef)>0)
{
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $hkef);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}    
?>