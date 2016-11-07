<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdAfd=$_POST['kdAfd'];
$tahun=$_POST['tahun'];


$stream = "<table class=sortable cellspacing=1>";
    $stream.="<thead class=rowheader>
        <tr class=rowheader>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nourut']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['kodeblok']."</td>
                <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['statusblok']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['tahunproduksi']."</td>    
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['bjr']."</td>
        </tr>
        <tr>";
    $stream.="</thead>";
    
    $iBlok="select a.kodeorganisasi,b.statusblok from ".$dbname.".organisasi a left join ".$dbname.".setup_blok b  on a.kodeorganisasi=b.kodeorg"
         . " where a.kodeorganisasi like '".$kdAfd."%' and a.tipe='blok' and b.statusblok='TM' ";
    $nBlok=mysql_query($iBlok) or die (mysql_error($conn));
    while($dBlok=mysql_fetch_assoc($nBlok))
    {
        $blok[$dBlok['kodeorganisasi']]=$dBlok['kodeorganisasi'];
        $statusBlok[$dBlok['kodeorganisasi']]=$dBlok['statusblok'];
    }
    
    $iBjr="select * from ".$dbname.".kebun_5bjr where kodeorg like '".$kdAfd."%' and tahunproduksi='".$tahun."' ";
    $nBjr=mysql_query($iBjr) or die (mysql_error($conn));
    while($dBjr=mysql_fetch_assoc($nBjr))
    {
        $bjr[$dBjr['kodeorg']]=$dBjr['bjr'];
        $thnProd[$dBjr['kodeorg']]=$dBjr['tahunproduksi'];
    }
    
    foreach ($blok as $kdBlok) 
    {
        $no+=1;
        $stream.="<tr class=rowcontent id=row".$no.">";
        $stream.="<td>".$no."</td>";
        $stream.="<td hidden  id=kdBlok".$no.">".$kdBlok."</td>";
        $stream.="<td>".substr($kdBlok,6,6)."</td>";
        $stream.="<td>".$statusBlok[$kdBlok]."</td>";
        $stream.="<td>".$thnProd[$kdBlok]."</td>";
        $stream.="<td>".$bjr[$kdBlok]."</td>";
        $stream.="</tr>";  
    }
    
    
$stream.="BJR baru : <input type=text maxlength=5 id=bjr onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:75px;\">
";            
$stream.="<button class=mybutton onclick=saveAll(".$no.");>".$_SESSION['lang']['proses']."</button>";
$stream.="</table>";	
	   
   

   
   
  
   
   
   
   
   
  
   
  
   



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
		$nop_="laporan_daftar_panen".$pt."_".$per;
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