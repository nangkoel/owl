<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$param=$_POST;
echo "<table border=0 cellspacing=1><tr class=rowheader>
           <td>".$_SESSION['lang']['nourut']."</td>
           <td>".$_SESSION['lang']['periode']."</td>
           <td>".$_SESSION['lang']['nama']."</td>
           <td>".$_SESSION['lang']['subbagian']."</td>    
            <td>".$_SESSION['lang']['jenis']."</td>   
            <td>".$_SESSION['lang']['premi']."(Rp.)</td>   
           </tr>";

$str="select a.*,b.namakaryawan,b.subbagian from ".$dbname.".kebun_premikemandoran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where"
        . " periode='".$param['periode']."' and a.kodeorg='".$param['kodeorg']."' and a.jabatan='".$param['jenis']."'";

$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $no++;
 echo "<tr class=rowcontent>
           <td>".$no."</td>
           <td>".$bar->periode."</td>
           <td>".$bar->namakaryawan."</td>
           <td>".$bar->subbagian."</td>  
           <td>".$bar->jabatan."</td>   
           <td>".number_format($bar->premiinput)."</td> 
           </tr>";   
}
echo"</table>";
?>