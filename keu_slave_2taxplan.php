<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');

$_POST['pt']!=''?$pt=$_POST['pt']:$pt=$_GET['pt'];
$_POST['unit']!=''?$unit=$_POST['unit']:$unit=$_GET['unit'];

$tanggaldr=tanggalsystem($_POST['tanggaldari']);
$tanggaldari=substr($tanggaldr,0,4).'-'.substr($tanggaldr,4,2).'-'.substr($tanggaldr,6,2);
$tanggalsd=tanggalsystem($_POST['tanggalsampai']);
$tanggalsampai=substr($tanggalsd,0,4).'-'.substr($tanggalsd,4,2).'-'.substr($tanggalsd,6,2);
if($tanggaldr==''){
    $tanggaldari=tanggalsystem($_GET['tanggaldari']);
    $tanggalsampai=tanggalsystem($_GET['tanggalsampai']);
}
if($_GET['type']==''){
    if($tanggaldari==''||$tanggalsampai==''){
        echo "Warning: silakan mengisi tanggal"; exit;
    }
}

if($_SESSION['language']=='EN'){
    $zz='namakegiatan1 as namakegiatan';
}
else{
    $zz='namakegiatan';
}
// kamus kegiatan
$strh="SELECT kodekegiatan, ".$zz.", satuan FROM ".$dbname.".setup_kegiatan";
$resh=mysql_query($strh);
while($barh=mysql_fetch_object($resh))
{
    $kamusnama[$barh->kodekegiatan]=$barh->namakegiatan;
    $kamussatuan[$barh->kodekegiatan]=$barh->satuan;
}

if($unit!='')
{
    $where="and substr(nopp,16,4)='".$unit."'";
    $whr="and b.kodeorg='".$unit."'";
}
else
{
    $where="and substr(nopp,16,4) in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."'
            and length(kodeorganisasi)=4)";
    $whr="and b.kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."'
          and length(kodeorganisasi)=4 )";
}
// eksekyut PO
$str="SELECT tanggal, nopo, nopp, jumlahpesan, hargasatuan, satuan, namasupplier, namabarang
      FROM ".$dbname.".log_po_vw
      WHERE tanggal between '".$tanggaldari."' and '".$tanggalsampai."' and kodeorg = '".$pt."' 
      ".$where."  ";
//exit("error: ".$str);
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $qwe=$bar->tanggal.$bar->nopo.$bar->namabarang;
    $isidata[$qwe]['tang']=$bar->tanggal;
    $isidata[$qwe]['tipe']='PO';
    $isidata[$qwe]['nopo']=$bar->nopo;
    $isidata[$qwe]['supp']=$bar->namasupplier;
    $isidata[$qwe]['nopp']=$bar->nopp;
    $isidata[$qwe]['bara']=$bar->namabarang;
    $isidata[$qwe]['satu']=$bar->satuan;
    $isidata[$qwe]['juml']=$bar->jumlahpesan;
    $isidata[$qwe]['harg']=$bar->hargasatuan;
    $isidata[$qwe]['tota']=($bar->jumlahpesan)*($bar->hargasatuan);
}

// eksekyut SPK
$str="SELECT a.tanggal, a.notransaksi, a. kodekegiatan, sum(a.hasilkerjarealisasi) as jumlah, sum(a.jumlahrealisasi) as total, c.namasupplier 
    FROM ".$dbname.".log_baspk a
    LEFT JOIN ".$dbname.".log_spkht b on a.notransaksi=b.notransaksi
    LEFT JOIN ".$dbname.".log_5supplier c on b.koderekanan=c.supplierid
    WHERE a.tanggal between '".$tanggaldari."' and '".$tanggalsampai."' ".$whr." 
    GROUP BY a.tanggal, a.notransaksi";
//exit("error: ".$str);
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $qwe=$bar->tanggal.$bar->notransaksi;
    $isidata[$qwe]['tang']=$bar->tanggal;
    $isidata[$qwe]['tipe']='SPK';
    $isidata[$qwe]['nopo']=$bar->notransaksi;
    $isidata[$qwe]['supp']=$bar->namasupplier;
    $isidata[$qwe]['nopp']='';
    $isidata[$qwe]['bara']=$kamusnama[$bar->kodekegiatan];
    $isidata[$qwe]['satu']=$kamussatuan[$bar->kodekegiatan];
    $isidata[$qwe]['juml']=$bar->jumlah;
    @$harga=$bar->total/$bar->jumlah;
    $isidata[$qwe]['harg']=$harga;
    $isidata[$qwe]['tota']=$bar->total;
}


if(!empty($isidata)) foreach($isidata as $c=>$key) {
    $sort_tang[] = $key['tang'];
    $sort_nopo[] = $key['nopo'];
    $sort_bara[] = $key['bara'];
}

// sort
if(!empty($isidata))array_multisort($sort_tang, SORT_ASC, $sort_nopo, SORT_ASC,  $sort_bara, SORT_ASC, $isidata);

//echo "<pre>";
//print_r($isidata);
//echo "</pre>";


function tanggalsistem($qwe)
{
    $tahun=substr($qwe,0,4);
    $bulan=substr($qwe,4,2);
    $tanggal=substr($qwe,6,2);
    return($tanggal.'-'.$bulan.'-'.$tahun);
}

//$stream="Tax Planing : ".tanggalsistem($tanggaldr)." s.d.".tanggalsistem($tanggalsd)."";
if($_GET['type']=='excel')
{
    $stream.="<table cellspacing=1 border=1 width=100%>";
    $bg="bgcolor=#DEDEDE";
    $stream.="Tax Planning : ".tanggalsistem($tanggaldari)." s.d.".tanggalsistem($tanggalsampai)."";
}else{
    $stream.="<table class=sortable cellspacing=1 border=0 width=100%>";
    $stream.="Tax Planning : ".tanggalsistem($tanggaldr)." s.d.".tanggalsistem($tanggalsd)."";
}

$stream.="
    <thead>
    <tr>
        <td ".$bg." align=center>No.</td>
        <td ".$bg." align=center>".$_SESSION['lang']['tanggal']."</td>
        <td ".$bg." align=center>".$_SESSION['lang']['tipe']."</td>
        <td ".$bg." align=center>".$_SESSION['lang']['nopo']."/".$_SESSION['lang']['kontrak']."</td>  
        <td ".$bg." align=center>".$_SESSION['lang']['supplier']."</td>  
        <td ".$bg." align=center>".$_SESSION['lang']['nopp']."</td>  
        <td ".$bg." align=center>".$_SESSION['lang']['namabarang']."/".$_SESSION['lang']['pekerjaan']."</td>  
        <td ".$bg." align=center>".$_SESSION['lang']['satuan']."</td>  
        <td ".$bg." align=center>".$_SESSION['lang']['jumlahbarang']."/".$_SESSION['lang']['hasilkerjad']."</td>  
        <td ".$bg." align=center>".$_SESSION['lang']['hargasatuan']."</td>  
        <td ".$bg." align=center>".$_SESSION['lang']['total']."</td>  
    </tr>  
    </thead>
    <tbody>";

$res=mysql_query($str);
//$res1=mysql_query($str);

$no=0;
//if(mysql_num_rows($res)<1)
//{
//    $stream.="<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
//}
//else
//{
    if(!empty($isidata)){
      foreach($isidata as $baris)
      {        
        if($_GET['type']!='excel')$tampiltanggal=tanggalnormal($baris['tang']); else $tampiltanggal=$baris['tang'];
        $no+=1; $total=0;
        $stream.="<tr class=rowcontent>
            <td align=right>".$no."</td>
            <td>".$tampiltanggal."</td>
            <td>".$baris['tipe']."</td>
            <td>".$baris['nopo']."</td>
            <td>".$baris['supp']."</td>
            <td>".$baris['nopp']."</td>
            <td>".$baris['bara']."</td>
            <td>".$baris['satu']."</td>
            <td align=right>".number_format($baris['juml'],2)."</td>
            <td align=right>".number_format($baris['harg'],0)."</td>
            <td align=right>".number_format($baris['tota'],0)."</td>
            </tr>";
        $totalhasil+=$bar->hasil;
        $totalkomunikasi+=$bar->komunikasi;
        $totalwaktu+=$bar->waktu;        
       }
    }
    else{
        $stream.="<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
    }
    $stream.="</tbody>
    <tfoot>
    </tfoot>		 
    </table>";
//}

if($_GET['type']=='unit'){
    $opt_unit="<option value=''>".$_SESSION['lang']['all']."</option>";
    $s_unit="select * from ".$dbname.".organisasi where induk='".$pt."' order by kodeorganisasi asc";
    $q_unit=mysql_query($s_unit) or die(mysql_error($conn));
    while($r_unit=mysql_fetch_assoc($q_unit))
    {
        $opt_unit.="<option value='".$r_unit['kodeorganisasi']."'>".$r_unit['namaorganisasi']."</option>";  
        
    }
    echo $opt_unit;
}
else if($_GET['type']=='excel')
{
    $stream.="<br>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
    $nop_="taxplan_".$tanggaldari."sd".$tanggalsampai;
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
}
else    
{
   echo $stream;
}    
   
?>