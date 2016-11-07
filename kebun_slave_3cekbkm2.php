<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');


$proses=$_GET['proses'];
$kdorg2=$_POST['kdorg2'];
$per2=$_POST['per2'];

if($proses=='excel')
{
	$kdorg2=$_GET['kdorg2'];
	$per2=$_GET['per2'];
	$border="border=1";
        
}


$stream.="Data yang tampil adalah data yang salah";
$stream.="<table cellspacing='1' ".$border." class='sortable'>
			<thead class=rowheader>
				<tr class=rowheader>
                                    <td align=center>No</td>
                                    <td align=center>Notransaksi</td>
				</tr>
			</thead>
			<tbody>";
		
		#pres
                    
                $str="select distinct tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
                      periode='".$per2."' and kodeorg like '".$kdorg2."%'";
                $res=fetchData($str);
               
                        $iPres="select notransaksi,tanggal  FROM ".$dbname.".kebun_aktifitas where kodeorg='".$kdorg2."' "
                        . " and tanggal between '".$res[0]['tanggalmulai']."' and '".$res[0]['tanggalsampai']."' and jurnal=0 "
                        . " and notransaksi not in (select notransaksi fROM ".$dbname.".kebun_prestasi)";
                   
		//exit("Error:$iPres");
                $nPres=mysql_query($iPres) or die (mysql_error($conn));
		while($dPres=mysql_fetch_assoc($nPres))
		{
                    $no+=1;
                    $stream.= "
                    <tr class=rowcontent id=rowx".$no.">
                            <td  ".$bg." align=center>".$no."</td>
                            <td  ".$bg." align=left id=notx".$no.">".$dPres['notransaksi']."</td>
                    </tr>";
		}
                
                
                
                $ix=" select a.notransaksi,a.tanggal  FROM ".$dbname.".kebun_aktifitas a left join ".$dbname.".kebun_prestasi b "
                        . " on a.notransaksi=b.notransaksi  where a.kodeorg='".$kdorg2."' "
                        . " and a.tanggal between '".$res[0]['tanggalmulai']."' and '".$res[0]['tanggalsampai']."' and a.jurnal=0 and a.tipetransaksi!='PNN'"
                        . " and a.notransaksi not in (select notransaksi from ".$dbname.".kebun_kehadiran) ";
                
                $nx=mysql_query($ix) or die (mysql_error($conn));
		while($dx=mysql_fetch_assoc($nx))
		{
                    $no+=1;
                    $stream.= "
                    <tr class=rowcontent id=rowx".$no.">
                            <td  ".$bg." align=center>".$no."</td>
                            <td  ".$bg." align=left id=notx".$no.">".$dx['notransaksi']."</td>
                    </tr>";
		}
                
                
                
                
		
		$stream.="</table>";
	
$stream.="<button class=mybutton onclick=saveAllx(".$no.");>".$_SESSION['lang']['proses']."</button>";	
		
switch($proses)
{
	case'preview':
	//exit("Error:MASUK");
		echo $stream;
	break;
	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="laporan_cek_prestasi_kehadiran".$tglSkrg;
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
	
	default;
}


?>