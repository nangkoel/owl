<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdOrg=$_POST['kdOrg'];
$per=$_POST['per'];
if($proses=='excel')
{
    $kdOrg=$_GET['kdOrg'];
    $per=$_GET['per'];
}

$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');





if ($proses == 'excel') 
{
    $stream = "<table class=sortable cellspacing=1 border=1>";
} else 
{
    $stream = "<table class=sortable cellspacing=1>";
}



//notransaksi	tanggal	keranimuat	nikmandor	nikasisten	nikmandor1


$stream.="<thead class=rowheader>
    <tr class=rowheader>
        <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nourut']."</td>
        <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['tanggal']."</td>
        <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['notransaksi']."</td>
        <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['mandor']."</td>
        <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nikmandor1']."</td>
        <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['keraniproduksi']."</td>
        <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['keranimuat']."</td>    
        <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['panen']."</td> 
    </tr>
    <tr>";
   $stream.="</thead>";
   
   
$iIsi="select tanggal,notransaksi,nikmandor,nikmandor1,nikasisten,keranimuat from ".$dbname.".kebun_aktifitas where "
      . " notransaksi in (select notransaksi from ".$dbname.".kebun_prestasi where kodeorg like '".$kdOrg."%') "
      . " and tanggal like '".$per."%' and tipetransaksi='PNN' ";
$nIsi=mysql_query($iIsi) or die (mysql_error($conn));
while($dIsi=  mysql_fetch_assoc($nIsi))
{
    $no+=1;
    $noK=0;
    $iKar="select nik from ".$dbname.".kebun_prestasi where notransaksi='".$dIsi['notransaksi']."' ";
    $nKar=mysql_query($iKar) or die (mysql_error($conn));
    $span=mysql_num_rows($nKar);
    $stream.="<tr class=rowcontent>";
            $stream.="<td rowspan=$span valign=top>".$no."</td>";
            $stream.="<td rowspan=$span valign=top>".tanggalnormal($dIsi['tanggal'])."</td>";
            $stream.="<td rowspan=$span valign=top>".$dIsi['notransaksi']."</td>";
            $stream.="<td rowspan=$span valign=top>".$nmKar[$dIsi['nikmandor']]."</td>";
            $stream.="<td rowspan=$span valign=top>".$nmKar[$dIsi['nikmandor1']]."</td>";
            $stream.="<td rowspan=$span valign=top>".$nmKar[$dIsi['nikasisten']]."</td>";
            $stream.="<td rowspan=$span valign=top>".$nmKar[$dIsi['keranimuat']]."</td>";
            
    while($dKar=mysql_fetch_assoc($nKar))
    {
        $noK+=1;
        if($noK==1)
        {
            $stream.="<td>".$nmKar[$dKar['nik']]."</td>";
            $stream.="</tr>";
        }
        else
        {
            $stream.="<tr class=rowcontent>";
                $stream.="<td>".$nmKar[$dKar['nik']]."</td>";
            $stream.="</tr>";
        }
    } 
    
}
  
   
   

   
   
  
   
  
   



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