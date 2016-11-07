<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdOrg=$_POST['kdOrg'];
$akunKas=$_POST['akunKas'];
$akunBank=$_POST['akunBank'];
$tgl1=tanggalsystem($_POST['tgl1']);
$tgl2=tanggalsystem($_POST['tgl2']);


if($proses=='excel')
{
    $kdOrg=$_GET['kdOrg'];
    $akunKas=$_GET['akunKas'];
    $akunBank=$_GET['akunBank'];
    $tgl1=tanggalsystem($_GET['tgl1']);
    $tgl2=tanggalsystem($_GET['tgl2']);
    $border="border=1";
}

$per=substr($tgl1,0,6);

$bln=substr($per,4,2);

$tglAwal=$per.'01';

$pt=makeOption($dbname,'organisasi','kodeorganisasi,induk');
$nmPt=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');


$nmBln=numToMonth($bln, 'I', 'long');
$tglAtas=substr($tgl1,6,2);
$thnAtas=substr($tgl1,0,4);



$stream=" ".$nmPt[$pt[$kdOrg]]."<br>POSISI KAS DAN BANK<br>KANTOR NUNUKAN<br>per ".$tglAtas." ".$nmBln." ".$thnAtas."  ";


$stream.= "<table class=sortable cellspacing=1 ".$border.">";
    $stream.="<thead class=rowheader>
        <tr class=rowheader>
            <td bgcolor=#CCCCCC align=center>No Transaksi</td>
            <td bgcolor=#CCCCCC align=center>Referensi</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['keterangan']."</td>
            <td bgcolor=#CCCCCC align=center>KAS</td>
            <td bgcolor=#CCCCCC align=center>BANK</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['total']."</td> 
        </tr></thead>";
    
    
    
    #bentuk sawal
    $iSawal="select awal".$bln." as sawalbulan,noakun from ".$dbname.".keu_saldobulanan where noakun in ('".$akunKas."','".$akunBank."') "
            . "and  kodeorg='".$kdOrg."' and periode='".$per."' ";
    
    $nSawal=mysql_query($iSawal) or die (mysql_error($conn));
    while($dSawal=mysql_fetch_assoc($nSawal))
    {
        $sawalBulan[$dSawal['noakun']]=$dSawal['sawalbulan'];
    }
    
    
    #bentuk sawal jika pemilihan tanggal tidak di awal bulan
    $iTotMas="select sum(jumlah) as jumlah,noakun,tipetransaksi from ".$dbname.".keu_kasbankht where noakun in ('".$akunKas."','".$akunBank."')"
            . " and tanggalposting >=  '".$tglAwal."' and tanggalposting < '".$tgl1."' and posting=1 "
            . " and kodeorg='".$kdOrg."' and tipetransaksi='M' group by noakun";
    $nTotMas=mysql_query($iTotMas) or die (mysql_error($conn));
    while($dTotMas=mysql_fetch_assoc($nTotMas))
    {
        $jumTotMas[$dTotMas['noakun']]=$dTotMas['jumlah'];   
    }
    
    $iTotKel="select sum(jumlah) as jumlah,noakun,tipetransaksi from ".$dbname.".keu_kasbankht where noakun in ('".$akunKas."','".$akunBank."')"
            . " and tanggalposting >=  '".$tglAwal."' and tanggalposting < '".$tgl1."' and posting=1 "
            . " and kodeorg='".$kdOrg."' and tipetransaksi='K' group by noakun";
    $nTotKel=mysql_query($iTotKel) or die (mysql_error($conn));
    while($dTotKel=mysql_fetch_assoc($nTotKel))
    {
        $jumTotKel[$dTotKel['noakun']]=$dTotKel['jumlah'];   
    }    

  

    
    
    #masuk
    $iMas="select * from ".$dbname.".keu_kasbankht where noakun in ('".$akunKas."','".$akunBank."') and tanggalposting between"
            . " '".$tgl1."' and '".$tgl2."' and posting=1 and kodeorg='".$kdOrg."' and tipetransaksi='M' order by notransaksi asc";
    $nMas=mysql_query($iMas) or die (mysql_error($conn));
    while($dMas=mysql_fetch_assoc($nMas))
    {
        $notranMas[$dMas['notransaksi']]=$dMas['notransaksi'];
        $tglMas[$dMas['notransaksi']]=$dMas['tanggalposting'];
        $refMas[$dMas['notransaksi']]=$dMas['nobayar'];
        $ketMas[$dMas['notransaksi']]=$dMas['keterangan'];
        $jumMas[$dMas['notransaksi']][$dMas['noakun']]=$dMas['jumlah'];   
    }
    
    
   #keluar
    $iKel="select * from ".$dbname.".keu_kasbankht where noakun in ('".$akunKas."','".$akunBank."') and tanggalposting between"
            . " '".$tgl1."' and '".$tgl2."' and posting=1 and kodeorg='".$kdOrg."' and tipetransaksi='K' order by notransaksi asc";
    $nKel=mysql_query($iKel) or die (mysql_error($conn));
    while($dKel=mysql_fetch_assoc($nKel))
    {
        $notranKel[$dKel['notransaksi']]=$dKel['notransaksi'];
        $tglKel[$dKel['notransaksi']]=$dKel['tanggalposting'];
        $refKel[$dKel['notransaksi']]=$dKel['nobayar'];
        $ketKel[$dKel['notransaksi']]=$dKel['keterangan'];
        $jumKel[$dKel['notransaksi']][$dKel['noakun']]=$dKel['jumlah'];   
    }  

   
    
   $noAkun[$akunKas]=$akunKas;
   $noAkun[$akunBank]=$akunBank;
    
   
  
   
   $stream.="<tr class=rowcontent>
                
                <td></td>
                <td>Saldo Awal</td>";
                foreach($noAkun as $noakun)
                {
                    if($tglAwal==$tgl1)
                    {
                        $sawal[$noakun]=$sawalBulan[$noakun];
                    }
                    else
                    { 
                        $sawal[$noakun]=$sawalBulan[$noakun]+$jumTotMas[$noakun]-$jumTotKel[$noakun]; 
                    }
                    $stream.="<td align=right>".number_format($sawal[$noakun],2)."</td>";
                    $totalSawal+=$sawal[$noakun];
                }
                
              $stream.="<td align=right>".number_format($totalSawal,2)."</td>
            </tr>";  
    $stream.="<tr class=rowcontent>
              <td></td>
              <td></td>
              <td>Penerimaan :</td>
              <td></td>
              <td></td>
              <td></td>
          </tr>";
   
    foreach($notranMas as $noMas)
    {
        
        $no+=1;
        $stream.="<tr class=rowcontent>";
//        $stream.="<td>".tanggalnormal($tglMas[$noMas])."</td>";
        $stream.="<td>".$noMas."</td>";
        $stream.="<td>".$refMas[$noMas]."</td>";
        $stream.="<td>".$ketMas[$noMas]."</td>";
        foreach($noAkun as $noakun)
        {
            $stream.="<td align=right>".number_format($jumMas[$noMas][$noakun],2)."</td>";
            $totalMasPerTran[$noMas]+=$jumMas[$noMas][$noakun];
            $totalMasAkun[$noakun]+=$jumMas[$noMas][$noakun];
        }
        $stream.="<td align=right>".number_format($totalMasPerTran[$noMas],2)."</td>";
        $stream.="</tr>";  
        
        $totalMas+=$totalMasPerTran[$noMas];
    }
    
   $stream.="<tr class=rowcontent>
                <td></td>
                <td></td>
                <td></td>
                <td>Total Penerimaan</td>";
                foreach($noAkun as $noakun)
                {
                    $stream.="<td align=right>".number_format($totalMasAkun[$noakun],2)."</td>";
                }
                 $stream.="
                
                <td align=right>".number_format($totalMas,2)."</td>
            </tr>";  
    $stream.="<tr class=rowcontent>
              <td></td>
              <td></td>
              <td></td>
              <td>Pengeluaran :</td>
              <td></td>
              <td></td>
              <td></td>
          </tr>";  
    
    
    
    
    
    #keluar
    foreach($notranKel as $noKel)
    {
        $no+=1;
        $stream.="<tr class=rowcontent>";
//        $stream.="<td>".tanggalnormal($tglKel[$noKel])."</td>";
        $stream.="<td>".$noKel."</td>";
        $stream.="<td>".$refKel[$noKel]."</td>";
        $stream.="<td>".$ketKel[$noKel]."</td>";
        foreach($noAkun as $noakun)
        {
            $stream.="<td align=right>".number_format($jumKel[$noKel][$noakun],2)."</td>";
            $totalKelPerTran[$noKel]+=$jumKel[$noKel][$noakun];
            $totalKelAkun[$noakun]+=$jumKel[$noKel][$noakun];
        }
        
        $stream.="<td align=right>".number_format($totalKelPerTran[$noKel],2)."</td>";
        $stream.="</tr>"; 
        $totalKel+=$totalKelPerTran[$noKel];
    }
   
   $stream.="<tr class=rowcontent>
                <td></td>
                <td></td>
                <td></td>
                <td>Total Pengeluaran</td>";
                foreach($noAkun as $noakun)
                {
                    $stream.="<td align=right>".number_format($totalKelAkun[$noakun],2)."</td>";
                }
                 $stream.="
                
                <td align=right>".number_format($totalKel,2)."</td>
            </tr>"; 
    
   $stream.="<tr class=rowcontent>
                <td></td>
                <td></td>
                <td></td>
                <td>Saldo Akhir</td>";
                foreach($noAkun as $noakun)
                {
                    $akunSalak=$sawal[$noakun]+$totalMasAkun[$noakun]-$totalKelAkun[$noakun];
                    $stream.="<td align=right>".number_format($akunSalak)."</td>";
                }
                $totalSalak=$totalSawal+$totalMas-$totalKel;
                $stream.="
                
                <td align=right>".number_format($totalSalak,2)."</td>
            </tr>";
 $stream.="<tbody></table><br>";
 
 
 $stream.= "<table class=sortable cellspacing=1 border=0>";
    $stream.="<tr class=rowcontent>
                <td></td>
                <td></td>
                <td align=center>Diketahui</td>
                <td align=center>Diperiksa</td>
                <td></td>
                <td></td>
                <td align=center>Dibuat Oleh</td>
            </tr>";
    $stream.="<tr class=rowcontent>
                <td></td>
                <td></td>
                <td align=center></td>
                <td align=center></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>";  
        $stream.="<tr class=rowcontent>
                <td></td>
                <td></td>
                <td align=center></td>
                <td align=center></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>";
       $stream.="<tr class=rowcontent>
                <td></td>
                <td></td>
                <td align=center></td>
                <td align=center></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>"; 
        $stream.="<tr class=rowcontent>
                <td></td>
                <td></td>
                <td align=center>General Manager</td>
                <td align=center>Finance Manager</td>
                <td></td>
                <td></td>
                <td td align=center>Kasir</td>
            </tr>";
  
$stream.="<tbody></table>";
switch($proses)
{
######PREVIEW
	case 'preview':
		echo $stream;
    break;

######EXCEL	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="laporan_kasharian_".$kdOrg."_".$tgl1."_".$tgl2;
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
		break;	
}
?>