<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$noakun=$_POST['noakun'];

$dibuat=$_POST['dibuat'];
$diperiksa=$_POST['diperiksa'];


$tgl=tanggalsystem($_POST['tgl']);

//echo $bulan.$kdorg;
if($proses=='excel')
{
        $kdorg=$_GET['kdorg'];
        $noakun=$_GET['noakun'];
        $tgl=tanggalsystem($_GET['tgl']);
        $dibuat=$_GET['dibuat'];
        $diperiksa=$_GET['diperiksa'];


}

/*$arr=array('1180400'=>'Operasional Karyawan','1180300'=>'Perjalanan Dinas','1180100'=>'Pembelian Barang');*/

$arr=makeOption($dbname,'keu_5akun','noakun,namaakun');

$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$nikKar=makeOption($dbname,'datakaryawan','karyawanid,nik');

$pt=makeOption($dbname,'organisasi','kodeorganisasi,induk');
$nmPt=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');

if($proses=='excel')
{
        $border="border=1";
        $bgcol="bgcolor=#CCCCCC ";
}
else
{
        $border="border=0";
}

if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

    if(($kdorg==''))
        {
                echo"Error: Organisasi tidak boleh kosong"; 
                exit;
    }

}	
                        //$stream="Laporan <br />Tanggal : ".tanggalnormal($tgl1)." s/d ".tanggalnormal($tgl2)." ";

$thn=substr($tgl,0,4);//echo $thn;
$thnKm=$thn-1;
$tglAkhir="".$thnKm."1231";

$stream="<table>
                        <tr>
                                <td colspan=5><b>".$nmPt[$pt[$kdorg]]."</b></td>
                        </tr>
                        <tr>
                                <td></td>
                        </tr>
                        <tr>
                                <td><b>AGING SCHEDULE</b></td>
                        </tr>
                        <tr>
                                <td><b>UANG MUKA ".strtoupper($arr[$noakun])." </b></td>
                        </tr>
                        <tr>
                                <td><b>PER ".tanggalnormal($tgl)." </b></td>
                        </tr>
                        <tr>
                                <td></td>
                        </tr>
</table>";

$stream.="<table cellspacing='1' ".$border."  class='sortable'>
<thead>
          <tr class=rowheader>
               <td rowspan=2 ".$bgcol." align=center>Tanggal</td>
                <td rowspan=2 ".$bgcol." align=center><b>Nik Karyawan</b></td>
                <td rowspan=2 ".$bgcol." align=center><b>Nama</b></td>
                <td rowspan=2 ".$bgcol." align=center><b>".$_SESSION['lang']['kodebarang']."</b></td>
                                        <td rowspan=2 ".$bgcol." align=center><b>".$_SESSION['lang']['keterangan']."</b></td>
                <td rowspan=2 ".$bgcol." align=center><b>Saldo 31/12/".$thnKm."</b></td>
                <td colspan=5 ".$bgcol." align=center><b>Umur Piutang Thn Berjalan</b></td>

          </tr>
          <tr>
                <td align=center ".$bgcol."><b>0-30 Hari</b></td>
                <td align=center ".$bgcol."><b>31-60 Hari</b></td>
                <td align=center ".$bgcol."><b>61-90 Hari</b></td>
                <td align=center ".$bgcol."><b>90-120 Hari</b></td>
                <td align=center ".$bgcol."><b>120+ Hari</b></td>
          </tr>
</thead>
<tbody>";



//echo $noakun;
//$thnSkr=date('Y');		   

//$nik=array();

$data=array();		   
$i="select sum(jumlah) as jumlah,tanggal,nik,kodebarang,keterangan FROM ".$dbname.".keu_jurnaldt_vw
 where noakun='".$noakun."' and tanggal<='".$tgl."' and kodeorg like'".$kdorg."%' group by nik,tanggal,kodebarang,keterangan";
$n=mysql_query($i);	

while($d=mysql_fetch_assoc($n))
{
        $diff =(strtotime($tgl)-strtotime($d['tanggal']));
        $outstd =floor(($diff)/(60*60*24));
        $kel=0;
        if(($outstd>=0)and($outstd<=30))$kel=1;
        if(($outstd>=31)and($outstd<=60))$kel=2;
        if(($outstd>=61)and($outstd<=90))$kel=3;
        if(($outstd>=91)and($outstd<=120))$kel=4;
        if($outstd>120)$kel=5;
        
       $data['jumlah'][][$kel]= $d['jumlah'];
       $data['kodebarang'][]= $d['kodebarang'];
       $data['keterangan'][]= $d['keterangan'];
       $data['tanggal'][]= $d['tanggal'];
       $data['nik'][]= $d['nik'];
}
$a="select sum(jumlah) as jumlah,nik,kodebarang,tanggal,keterangan FROM ".$dbname.".keu_jurnaldt_vw where tanggal<='".$tglAkhir."' "
        . "and noakun='".$noakun."' and kodeorg='".$kdorg."' group by nik,tanggal,kodebarang,keterangan";
$b=mysql_query($a) or die (mysql_error($conn));
while($c=mysql_fetch_object($b))
{
        $dataJur['jumlah'][$c->nik][$c->tanggal][$c->kodebarang][$c->keterangan]=$c->jumlah;
}

        $nmBrg=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
if(count($data['jumlah'])>0){        
foreach($data['jumlah'] as $idx=>$row) {

        //$whereBrg="'kodebarang='".$row[$barang]."'";

        $stream.="<tr class=rowcontent>
                        <td align=left>".tanggalnormal($data['tanggal'][$idx])."</td>
                        <td align=left>".$nikKar[$data['nik'][$idx]]."</td>
                        <td align=left>".$nmKar[$data['nik'][$idx]]."</td>
                        <td align=left>".$nmBrg[$data['kodebarang'][$idx]]."</td>
                        <td align=left>". $data['keterangan'][$idx]."</td>
                        <td align=right>".number_format($dataJur['jumlah'][$data['nik'][$idx]][$data['tanggal'][$idx]][$data['kodebarang'][$idx]][$data['keterangan'][$idx]],2)."</td>
                        <td align=right>".number_format($row[1],2)."</td>
                        <td align=right>".number_format($row[2],2)."</td>
                        <td align=right>".number_format($row[3],2)."</td>
                        <td align=right>".number_format($row[4],2)."</td>
                        <td align=right>".number_format($row[5],2)."</td>
                </tr>";	
                $totalSaldo+=$dataJur['jumlah'][$data['nik'][$idx]][$data['tanggal'][$idx]][$data['kodebarang'][$idx]][$data['keterangan'][$idx]];
                $total[1]+=$row[1];
                $total[2]+=$row[2];
                $total[3]+=$row[3];
                $total[4]+=$row[4];
                $total[5]+=$row[5];	
}

        $stream.="
                        <tr class=rowcontent>
                                <td align=right colspan=2></td>
                                <td align=left>Jumlah</td>
                                <td align=left colspan=2></td>
                                <td align=right><B>".number_format($totalSaldo,2)."</b></td>
                                <td align=right><B>".number_format($total[1],2)."</b></td>
                                <td align=right><B>".number_format($total[2],2)."</b></td>
                                <td align=right><B>".number_format($total[3],2)."</b></td>
                                <td align=right><B>".number_format($total[4],2)."</b></td>
                                <td align=right><B>".number_format($total[5],2)."</b></td>
                        </tr>
                        <tr class=rowcontent>
                                <td align=right colspan=2></td>
                                <td align=left>Jumlah per Neraca Percobaan</td>
                                <td align=left colspan=2></td>
                                <td align=right><B>".number_format($totalSaldo,2)."</b></td>
                                <td align=right rowspan=3></td>
                                <td align=right rowspan=3></td>
                                <td align=right rowspan=3></td>
                                <td align=right rowspan=3></td>
                                <td align=right rowspan=3></td>
                        </tr>
                        <tr class=rowcontent>
                                <td align=right rowspan=2 colspan=2></td>
                                <td rowspan=2 align=left valign=top>Selisih</td>
                                <td align=left colspan=2></td>
                                <td align=right><font color=red><B>".number_format($totalSaldo-$totalSaldo,2)."</b></font></td>
                        </tr></table>
                        ";		


$stream.="<table>
                        <tr>
                                <td>Dibuat Oleh :</td>
                                <td></td>
                                <td>Diperiksa Oleh :</td>
                                <td colspan=3></td>
                                <td colspan=2>Diketahui Oleh :</td>
                        </tr>";

                        for($i=1;$i<=5;$i++);
                        {
                                $stream.="<tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan=3></td>
                                        <td colspan=2></td>
                                        </tr>";
                        }

                        $stream.="<tr>
                                <td>".$nmKar[$dibuat]."</td>
                                <td></td>
                                <td>".$nmKar[$diperiksa]."</td>
                                <td colspan=3></td>
                                <td colspan=2>Accounting Manager</td>
                        </tr>

</table>";
}
 else {
  $stream="No Data Found";      
}

#######################################################################
############PANGGGGGGGGGGGGGGGGGGILLLLLLLLLLLLLLLLLLLLLLLLLL###########   
#######################################################################

switch($proses)
{
######HTML
        case 'preview':
                echo $stream;
    break;

######EXCEL	
        case 'excel':
                //$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
                $tglSkrg=date("Ymd");
                $nop_="Laporan_Aging_Pinjaman".$tglSkrg;
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





        default:
        break;
}

?>