<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$unit=$_POST['unit'];
$tk=$_POST['tk'];
$tahun=$_POST['tahun'];
if($proses=='excel')
{
    $tk=$_GET['tk'];
    $unit=$_GET['unit'];
    
}


if ($proses == 'excel') 
    {
        $stream = "<table class=sortable cellspacing=1 border=1>";
    } else 
    {
        $stream = "<table class=sortable cellspacing=1>";
    }

    $stream.="<thead class=rowheader>
        <tr class=rowheader>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nourut']."</td>
            <td bgcolor=#CCCCCC  align=center>".$_SESSION['lang']['karyawanid']."</td>    
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['namakaryawan']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nik']."</td>    
            
        </tr>
        <tr>";
       $stream.="</thead>";

       $iKar="select karyawanid,namakaryawan,nik "
               . " from ".$dbname.".datakaryawan where tipekaryawan='".$tk."' and "
               . " subbagian='".$unit."'  and (tanggalkeluar>='2014-06-30' or tanggalkeluar='0000-00-00') ";
       $nKar=mysql_query($iKar) or die (mysql_error($conn));
       while($dKar=  mysql_fetch_assoc($nKar))
       {
            $kar[$dKar['karyawanid']]=$dKar['karyawanid'];
            $nama[$dKar['karyawanid']]=$dKar['namakaryawan'];
            $nik[$dKar['karyawanid']]=$dKar['nik'];
           
       }

     
       //print_r($tglAngkat);
       
       foreach($kar as $karId)
       {
            
            $no+=1;
            $stream.="<tr class=rowcontent id=row".$no.">";
                $stream.="<td>".$no."</td>";
                $stream.="<td id=karyawanid".$no.">".$karId."</td>";
                $stream.="<td>".$nama[$karId]."</td>";
                $stream.="<td>".$nik[$karId]."</td>";
               
            $stream.="</tr>";    
       }


      
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