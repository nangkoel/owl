<?php
require_once('master_validation.php');
require_once('config/connection.php');


$padid=$_POST['pid'];
$nama=$_POST['nama'];
$alamat=$_POST['alamat'];
$desa=$_POST['desa'];
$kecamatan=$_POST['kecamatan'];
$kabupaten=$_POST['kabupaten'];
$ktp=$_POST['ktp'];
$hp=$_POST['hp'];
$method=$_POST['method'];
$unitbawah=$_POST['unitbawah'];
if($method==''){
    $method=$_GET['method'];
    $unitbawah=$_GET['unitbawah'];    
}

switch($method)
{
case 'excel':
    
        $str1="select a.*,b.unit from ".$dbname.".pad_5masyarakat a
            left join ".$dbname.".pad_5desa b on a.desa=b.namadesa where b.unit like '".$unitbawah."%' order by a.desa,a.nama";
if($res1=mysql_query($str1))
{
$stream.="<table class=sortable cellspacing=1 border=1>
     <thead>
             <tr bgcolor='#dedede'>
                <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>              
                <td style='width:150px;'>".$_SESSION['lang']['nama']."</td>                    
                <td style='width:150px;'>".$_SESSION['lang']['alamat']."</td>                        
                <td style='width:150px;'>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['desa']."</td>
                <td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['kecamatan']."</td>                  
                <td>".$_SESSION['lang']['kabupaten']."</td>    
                <td>".$_SESSION['lang']['noktp']."</td>             
                <td>".$_SESSION['lang']['nohp']."</td>                       
      </thead>
      <tbody>";
while($bar1=mysql_fetch_object($res1))
{
                $stream.="<tr class=rowcontent>
                         <td>".$bar1->unit."</td>                    
                           <td>".$bar1->nama."</td>
                           <td>".$bar1->alamat."</td>
                           <td>".$bar1->desa."</td>                               
                           <td>".$bar1->kecamatan."</td>
                           <td>".$bar1->kabupaten."</td>  
                           <td>".$bar1->noktp."</td>  
                           <td>".$bar1->hp."</td>                                 
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
$nop_="Daftar_Masyarakat_".$unitbawah." ".$qwe;
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
        $str="update ".$dbname.".pad_5masyarakat 
           set nama='".$nama."',
            alamat='".$alamat."',
            desa='".$desa."',               
            kecamatan='".$kecamatan."',
            kabupaten='".$kabupaten."',
            noktp='".$ktp."',
             hp='".$hp."'
            where padid=".$padid;
        if(mysql_query($str))
        {}
        else
        {echo " Gagal,".addslashes(mysql_error($conn));exit();}
        break;
case 'insert':
        $str="insert into ".$dbname.".pad_5masyarakat (nama,alamat,desa,kecamatan,kabupaten,noktp,hp)
              values('".$nama."','".$alamat."','".$desa."','".$kecamatan."','".$kabupaten."','".$ktp."','".$hp."')";
        if(mysql_query($str))
        {}
        else
        {echo " Gagal,".addslashes(mysql_error($conn));exit();}	
        break;
case 'delete':
        $str="delete from ".$dbname.".pad_5masyarakat
        where padid='".$padid."'";
        if(mysql_query($str))
        {}
        else
        {echo " Gagal,".addslashes(mysql_error($conn));exit();}
        break;
default:
   break;					
}
        $str1="select a.*,b.unit from ".$dbname.".pad_5masyarakat a
            left join ".$dbname.".pad_5desa b on a.desa=b.namadesa where b.unit like '".$unitbawah."%' order by a.desa,a.nama";
if($res1=mysql_query($str1))
{
echo"<table class=sortable cellspacing=1 border=0>
     <thead>
             <tr class=rowheader>
                <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>              
                <td style='width:150px;'>".$_SESSION['lang']['nama']."</td>                    
                <td style='width:150px;'>".$_SESSION['lang']['alamat']."</td>                        
                <td style='width:150px;'>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['desa']."</td>
                <td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['kecamatan']."</td>                  
                <td>".$_SESSION['lang']['kabupaten']."</td>    
                <td>".$_SESSION['lang']['noktp']."</td>             
                <td>".$_SESSION['lang']['nohp']."</td>                       
               <td style='width:30px;'>*</td></tr>    
      </thead>
      <tbody>";
while($bar1=mysql_fetch_object($res1))
{
                echo"<tr class=rowcontent>
                         <td>".$bar1->unit."</td>                    
                           <td>".$bar1->nama."</td>
                           <td>".$bar1->alamat."</td>
                           <td>".$bar1->desa."</td>                               
                           <td>".$bar1->kecamatan."</td>
                           <td>".$bar1->kabupaten."</td>  
                           <td>".$bar1->noktp."</td>  
                           <td>".$bar1->hp."</td>                                 
                           <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->padid."','".$bar1->nama."','".$bar1->alamat."','".$bar1->desa."','".$bar1->kecamatan."','".$bar1->kabupaten."','".$bar1->noktp."','".$bar1->hp."');\">
                            </td></tr>";
}	 
echo"	 
         </tbody>
         <tfoot>
         </tfoot>
         </table>";
}
?>
