<?php
require_once('master_validation.php');
require_once('config/connection.php');

$unit=$_POST['unit'];
$unitbawah=$_POST['unitbawah'];
$desa=$_POST['desa'];
$kecamatan=$_POST['kecamatan'];
$kabupaten=$_POST['kabupaten'];
$method=$_POST['method'];
if($method==''){
    $method=$_GET['method'];
    $unitbawah=$_GET['unitbawah'];    
}

switch($method)
{
case 'excel':

$stream="";    
$str1="select * from ".$dbname.".pad_5desa where unit like '".$unitbawah."%' order by namadesa";
if($res1=mysql_query($str1))
{
    $stream.="<table class=sortable cellspacing=1 border=1 style='width:500px;'>
    <thead><tr bgcolor='#dedede'>
        <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>
        <td style='width:150px;'>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['desa']."</td>
        <td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['kecamatan']."</td>
        <td>".$_SESSION['lang']['kabupaten']."</td>    
    </thead>
    <tbody>";
    while($bar1=mysql_fetch_object($res1))
    {
        $stream.="<tr class=rowcontent>
        <td align=center>".$bar1-> unit."</td>
        <td>".$bar1-> namadesa."</td>
        <td>".$bar1->kecamatan."</td>
        <td>".$bar1->kabupaten."</td>    
        </tr>";
    }	 
    $stream.="	 
    </tbody>
    <tfoot>
    </tfoot>
    </table><br>";
}    
$stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
$qwe=date("YmdHms");
$nop_="Daftar_Desa_".$unitbawah." ".$qwe;
if(strlen($stream)>0)
{
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $stream);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}    
exit;

break;    
case 'update':	
        $str="update ".$dbname.".pad_5desa set unit='".$unit."',
                           kecamatan='".$kecamatan."',
                           kabupaten='".$kabupaten."'
               where namadesa='".$desa."'";
        if(mysql_query($str))
        {}
        else
        {echo " Gagal,".addslashes(mysql_error($conn));exit();}
        break;
case 'insert':
        $str="insert into ".$dbname.".pad_5desa (namadesa,unit,kecamatan,kabupaten)
              values('".$desa."','".$unit."','".$kecamatan."','".$kabupaten."')";
        if(mysql_query($str))
        {}
        else
        {echo " Gagal,".addslashes(mysql_error($conn));exit();}	
        break;
case 'delete':
        $str="delete from ".$dbname.".pad_5desa
        where namadesa='".$desa."'";
        if(mysql_query($str))
        {}
        else
        {echo " Gagal,".addslashes(mysql_error($conn));exit();}
        break;
default:
   break;					
}
 $str1="select * from ".$dbname.".pad_5desa where unit like '".$unitbawah."%' order by namadesa";
if($res1=mysql_query($str1))
{
echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
     <thead><tr class=rowheader>
                <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>
                <td style='width:150px;'>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['desa']."</td>
                <td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['kecamatan']."</td>
                <td>".$_SESSION['lang']['kabupaten']."</td>    
                <td style='width:30px;'>*</td></tr>    
      </thead>
      <tbody>";
while($bar1=mysql_fetch_object($res1))
{
                echo"<tr class=rowcontent>
                          <td align=center>".$bar1-> unit."</td>
                           <td>".$bar1-> namadesa."</td>
                           <td>".$bar1->kecamatan."</td>
                           <td>".$bar1->kabupaten."</td>    
                           <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->unit."','".$bar1->namadesa."','".$bar1->kecamatan."','".$bar1->kabupaten."');\">
                            </td></tr>";
}	 
echo"	 
         </tbody>
         <tfoot>
         </tfoot>
         </table>";
}
?>
