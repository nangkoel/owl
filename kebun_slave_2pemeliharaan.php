<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$noGudang=$_POST['noGudang'];
$proses=$_GET['proses'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];
$kdOrg=$_POST['kdOrg'];
$kdAfd=$_POST['kdAfd'];	
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
$kegiatan=$_POST['kegiatan'];
$ispo=$_POST['ispo'];
if(($proses=='excel')or($proses=='pdf')){
	$kdOrg=$_GET['kdOrg'];
	$kdAfd=$_GET['kdAfd'];
	$tgl1_=$_GET['tgl1'];
	$tgl2_=$_GET['tgl2'];     
        $kegiatan=$_GET['kegiatan'];
        $ispo=$_GET['ispo'];
}

if($kdAfd=='')
    $kdAfd=$kdOrg;

$tgl1_=tanggalsystem($tgl1_); $tgl1=substr($tgl1_,0,4).'-'.substr($tgl1_,4,2).'-'.substr($tgl1_,6,2);
$tgl2_=tanggalsystem($tgl2_); $tgl2=substr($tgl2_,0,4).'-'.substr($tgl2_,4,2).'-'.substr($tgl2_,6,2);


//$presJjg=makeOption($dbname,'kebun_prestasi_vw','notransaksi,jjg',"tanggal between '".$tgl1_."' and '".$tgl2_."' and unit like '".$kdAfd."%'");
$blokLama=makeOption($dbname,'setup_blok','kodeorg,bloklama');
$presJjg=makeOption($dbname,'kebun_prestasi','notransaksi,jjg',"kodeorg like '".$kdAfd."%'");

if($_SESSION['language']=='EN'){
    $zz='namakegiatan1 as namakegiatan';
}else{
    $zz='namakegiatan';
}    
    $str="select kodekegiatan, ".$zz.", satuan
        from ".$dbname.".setup_kegiatan
        ";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $kamusKeg[$bar->kodekegiatan]=$bar->namakegiatan;
    }

if(($proses=='preview')or($proses=='excel')or($proses=='pdf')){
    if($kdOrg==''){
            echo"Error: Estate code and afdeling code required."; exit;
    }

    if(($tgl1_=='')or($tgl2_=='')){
            echo"Error: Date required."; exit;
    }

    if($tgl1>$tgl2){
            echo"Error: First date must lower than the second."; exit;
    }
	
}
if ($ispo!=''){
    $blk=" and ispo=".$ispo;
}
if ($proses=='excel' or $proses=='preview')
{
//ambil material
    $str="select a.notransaksi,a.kwantitas,a.kodebarang, b.namabarang,b.satuan from ".$dbname.".kebun_pakai_material_vw a 
          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang 
          left join ".$dbname.".setup_blok c on a.kodeorg=c.kodeorg    
          where a.kodeorg like '".$kdAfd."%'".$blk." and tanggal between '".$tgl1_."' and '".$tgl2_."' and a.kodekegiatan like '%".$kegiatan."%'";
//    echo $str;
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $barang[$bar->notransaksi]['kodebarang'][]=$bar->kodebarang;
        $barang[$bar->notransaksi]['namabarang'][]=$bar->namabarang;
        $barang[$bar->notransaksi]['satuan'][]=$bar->satuan;
        $barang[$bar->notransaksi]['jumlah'][]=$bar->kwantitas;
    }
    $border=0;
    if($proses=='excel')$border=1;
	
	
    $str="select a.* from ".$dbname.".kebun_perawatan_dan_spk_vw a 
          left join ".$dbname.".setup_blok b on a.kodeorg=b.kodeorg where a.kodeorg like '".$kdAfd."%'".$blk." 
          and tanggal between '".$tgl1_."' and '".$tgl2_."' and kodekegiatan like '%".$kegiatan."%'";
    //echo $str;
        $res=mysql_query($str);
	$stream.="<table cellspacing='1' border='".$border."' class='sortable'>
	<thead>
	<tr class=rowheader>
        <td>".$_SESSION['lang']['nomor']."</td>
        <td>".$_SESSION['lang']['notransaksi']."</td>    
	<td>".$_SESSION['lang']['sumber']."</td>
	<td>".$_SESSION['lang']['tanggal']."</td>
	<td>".$_SESSION['lang']['lokasi']."</td>
	<td>".$_SESSION['lang']['bloklama']."</td>
	<td>".$_SESSION['lang']['kodekegiatan']."</td>            
	<td>".$_SESSION['lang']['kegiatan']."</td>
	<td>".$_SESSION['lang']['jjg']."</td>
	<td>".$_SESSION['lang']['hasilkerjarealisasi']."</td>
	<td>".$_SESSION['lang']['satuan']."</td>
        <td>".$_SESSION['lang']['jumlahhk']."</td>
	<td>".$_SESSION['lang']['upahkerja']."</td>
	<td>".$_SESSION['lang']['insentif']."</td>
        <td>".$_SESSION['lang']['kodebarang']."</td> 
        <td>".$_SESSION['lang']['namabarang']."</td>
        <td>".$_SESSION['lang']['jumlah']."</td>  
        <td>".$_SESSION['lang']['satuan']."</td>     
        </tr></thead>
	<tbody>";
        $no=0;
        $oldnotrans='';
        while($bar=mysql_fetch_object($res))
        {
            $no+=1;
            $notran=$bar->notransaksi;
            if($notran!=$oldnotrans and $no!=1)
            {
                if(is_array($barang[$oldnotrans]['kodebarang'])){
                foreach($barang[$oldnotrans]['kodebarang'] as $key =>$val){
                $stream.="<tr class=rowcontent>
                <td></td>
                <td>".$oldnotrans."</td>    
                <td>BKM</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
				<td></td>
                <td></td>         
                <td align=right></td>                 
                <td></td>
                <td align=right></td>
                <td align=right></td>
                <td align=right></td>
                <td>".$barang[$oldnotrans]['kodebarang'][$key]."</td> 
                <td>".$barang[$oldnotrans]['namabarang'][$key]."</td>
                <td>".$barang[$oldnotrans]['jumlah'][$key]."</td>  
                <td>".$barang[$oldnotrans]['satuan'][$key]."</td>  
                </tr>";  
                }
                }
            }
            if($proses=='excel')$tampiltanggal=$bar->tanggal; else $tampiltanggal=tanggalnormal($bar->tanggal);
            $stream.="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$bar->notransaksi."</td>    
            <td>".$bar->sumber."</td>
            <td nowrap>".$tampiltanggal."</td>
            <td>".$bar->kodeorg."</td>
            <td>".$blokLama[$bar->kodeorg]."</td>
            <td>".$bar->kodekegiatan."</td>
            <td>". $kamusKeg[$bar->kodekegiatan]."</td>   
			<td>".$presJjg[$bar->notransaksi]."</td>      
            <td align=right>".number_format($bar->hasilkerja,2)."</td>                 
            <td>".$bar->satuan."</td>
            <td align=right>".number_format($bar->jumlahhk)."</td>
            <td align=right>".number_format($bar->upah)."</td>
            <td align=right>".number_format($bar->premi)."</td>
            <td>-</td> 
            <td>-</td>
            <td>-</td>  
            <td>-</td>                  
            </tr>";
            
            $oldnotrans=$notran;
            $thk+=$bar->jumlahhk;
            $tupah+=$bar->upah;
            $tpremi+=$bar->premi;
			$thasilker+=$bar->hasilkerja;
			$tjjg+=$presJjg[$bar->notransaksi];
			
			$satuan=$bar->satuan;
        }
                if(is_array($barang[$oldnotrans]['kodebarang'])){
                foreach($barang[$oldnotrans]['kodebarang'] as $key =>$val){
                $stream.="<tr class=rowcontent>
                <td></td>
                <td>".$oldnotrans."</td>    
                <td>BKM</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>         
                <td align=right></td>                 
                <td></td>
                <td align=right></td>
                <td align=right></td>
                <td align=right></td>
				<td align=right></td>
                <td>".$barang[$oldnotrans]['kodebarang'][$key]."</td> 
                <td>".$barang[$oldnotrans]['namabarang'][$key]."</td>
                <td>".$barang[$oldnotrans]['jumlah'][$key]."</td>  
                <td>".$barang[$oldnotrans]['satuan'][$key]."</td>  
                </tr>";  
                }
                }
   
       $stream.="
	<tr class=rowcontent>
	<td colspan=8>Total</td>
	<td align=right>".number_format($tjjg,2)."</td>
	<td align=right>".number_format($thasilker,2)."</td>
	<td>".$satuan."</td>
	<td align=right>".number_format($thk)."</td>
	<td align=right>".number_format($tupah)."</td>
	<td align=right>".number_format($tpremi)."</td>
        <td>-</td> 
        <td>-</td>
        <td>-</td>  
        <td>-</td>  
        </tbody></table>";
 
}  
switch($proses)
{
     case'goCariGudang':
         //exit("Error:MASUK");
                    echo"
                            <table cellspacing=1 border=0 class=data>
                            <thead>
                                    <tr class=rowheader>
                                            <td>No</td>
                                            <td>".$_SESSION['lang']['kodekegiatan']."</td>
                                            <td>".$_SESSION['lang']['namakegiatan']."</td>
                                            <td>".$_SESSION['lang']['satuan']."</td>
                                            <td>".$_SESSION['lang']['kelompok']."</td>
                                    </tr>
                    </thead>
                    </tbody>";

                    $i="select * from ".$dbname.".setup_kegiatan where namakegiatan like '%".$noGudang."%' order by kodekegiatan asc";
                    //exit("Error:MASUK".$i);
                    $n=mysql_query($i) or die (mysql_error($conn));
                    while ($d=mysql_fetch_assoc($n))
                    {
                            $no+=1;
                         echo"
                            <tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=goPickGudang('".$d['kodekegiatan']."')>
                                    <td>".$no."</td>
                                    <td>".$d['kodekegiatan']."</td>
                                    <td>".$d['namakegiatan']."</td>
                                    <td>".$d['satuan']."</td>
                                    <td>".$d['kelompok']."</td>
                            </tr>
                    ";
                    }

            break;
            
            
      case 'getAfdAll':
          $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
                where kodeorganisasi like '".$kdAfd."%' and length(kodeorganisasi)=6 and tipe in ('AFDELING','BIBITAN') order by namaorganisasi
                ";
          $op="<option value=''>".$_SESSION['lang']['all']."</option>";
          $res=mysql_query($str);
          while($bar=mysql_fetch_object($res)) 
          {
              $op.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
          }
          echo $op;
          exit();
      break; 
       case'preview':
            echo $stream;    
	break;
        case 'excel':
            $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHms");
            $nop_="Laporan_perawatan".$kdAfd.$tgl1_."-".$tgl2_."_".date('YmdHis');
             $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
             gzwrite($gztralala, $stream);
             gzclose($gztralala);
             echo "<script language=javascript1.2>
                window.location='tempExcel/".$nop_.".xls.gz';
                </script>";            
        break;    
}
?>