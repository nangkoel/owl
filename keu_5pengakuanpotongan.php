<?php //@Copy nangkoelframework
require_once('config/connection.php');
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/keu_5pengakuanHutang.js'></script>
<?php
include('master_mainMenu.php');
#ambil komponen gaji

$optKomponen="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sAkun="select  id,name from ".$dbname.".sdm_ho_component where plus=0 order by name";
$qAkun=mysql_query($sAkun) or die(mysql_error($conn));
while($rAkun=mysql_fetch_assoc($qAkun))
{
    $optKomponen.="<option value='".$rAkun['id']."'>".$rAkun['name']."</option>";
}
if($_SESSION['language']=='EN'){
    OPEN_BOX('','Salary deduction - Journal mapping');
        $zz="namaakun1 as namaakun";
}
else{
    OPEN_BOX('','Mapping Potongan Karyawan');
        $zz="namaakun";
}

$optAkun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sAkun="select  noakun,".$zz." from ".$dbname.".keu_5akun where length(noakun)=7 order by noakun asc";
$qAkun=mysql_query($sAkun) or die(mysql_error($conn));
while($rAkun=mysql_fetch_assoc($qAkun))
{
    $optAkun.="<option value='".$rAkun['noakun']."'>".$rAkun['noakun']." - ".$rAkun['namaakun']."</option>";
}

echo"<fieldset style='width:500px;'><table>
          <tr><td>Component ".$_SESSION['lang']['potongan']."</td><td><select id=potongan style=width:150px>".$optKomponen."</select></td></tr>
          <tr><td>".$_SESSION['lang']['debet']."</td><td><select id=debet style=width:150px>".$optAkun."</select></td></tr>
          <tr><td>".$_SESSION['lang']['kredit']."</td><td><select id=kredit style=width:150px>".$optAkun."</select></td></tr>        
         </table>
         <input type=hidden id=method value='insert'>
         <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
         <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
         </fieldset>";
echo "<div>
        ".$_SESSION['lang']['keteranganjrnlpotongan']." 
        <table class=sortable cellspacing=1 border=0>
             <thead>
                 <tr class=rowheader>
                    <td>Component ID</td>                 
                    <td>Component Name</td>
                    <td>".$_SESSION['lang']['debet']."</td>
                    <td>".$_SESSION['lang']['kredit']."</td>                     
                    <td style='width:30px;'>*</td></tr>
                 </thead>
                 <tbody id=container>"; 
                echo"<script>loadData()</script>";
                echo" </tbody>
                 <tfoot>
                 </tfoot>
                 </table>";
echo "</div>";
CLOSE_BOX();
echo close_body();
?>