<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/klsupplier.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();
//default value for the code is supplier
//then ghet max number for supplier
$tipe='SUPPLIER';
$str1="select max(kode) as kode from ".$dbname.".log_5klsupplier where tipe='".$tipe."'";
$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
        $kode=$bar1->kode;
}
$kode=substr($kode,1,5);
$newkode=$kode+1;
        switch($newkode)
        {
                case $newkode<10:
                   $newkode='00'.$newkode;
                   break;
                case $newkode<100:
                   $newkode='0'.$newkode;
                   break;
                default:
           $newkode=$newkode;   
                break;     
        }
$newkode='S'.$newkode;

?>
<u><b><font face="Verdana" size="4" color="#000080"><?echo $_SESSION['lang']['suppliergroup'];?></font></b></u>
<fieldset>
        <legend>
                <?echo $_SESSION['lang']['input'].' '.$_SESSION['lang']['suppliergroup'];?>
        </legend>
<table>
    <tr><td><?echo $_SESSION['lang']['Type'];?></td><td><select id=tipe onchange="getCodeNumber(this.options[this.selectedIndex].value)"><option value=SUPPLIER>Supplier</option><option value=KONTRAKTOR>Contractor</option></select></td></tr>	
        <tr><td><?echo $_SESSION['lang']['kode'];?></td><td><input type=text disabled value='<?echo $newkode;?>' class=myinputtext id=kodespl onkeypress="return tanpa_kutip(event);" maxlength=10 size=10></td></tr>
        <tr><td><?echo $_SESSION['lang']['namakelompok'];?></td><td><input type=text class=myinputtext id=kelompok onkeypress="return tanpa_kutip(event);" maxlength=40 size=40></td></tr>
<?php
if($_SESSION['language']=='EN'){
    $zz='namaakun1 as namaakun';
}
else{
    $zz='namaakun';
}
$str="select noakun,".$zz." from ".$dbname.".keu_5akun where detail=1 and (noakun like '211%' or noakun like '213%')";
$res=mysql_query($str);
$opt="";
while($bar=mysql_fetch_object($res))
{
        $opt.="<option value='".$bar->noakun."'>".$bar->namaakun."</option>";
}	
echo" <tr><td>".$_SESSION['lang']['noakun']."</td><td><select id=akun>".$opt."</select></td></tr>";
?>
<input type=hidden value='insert' id=method>
</table>
<button class=mybutton onclick=saveKelSup()><?echo $_SESSION['lang']['save'];?></button>
<button class=mybutton onclick=cancelKelSup()><?echo $_SESSION['lang']['cancel'];?></button>
</fieldset>
<?php
CLOSE_BOX();
OPEN_BOX();
?>
<fieldset>
        <legend><?echo $_SESSION['lang']['list'].' '.$_SESSION['lang']['suppliergroup'];?></legend>
        <div style='width:100%;overflow:scroll;height:300px;'>
        <table class=sortable cellspacing=1 border=0>
                <thead>
                        <tr>
                                <td><?echo $_SESSION['lang']['no'];?>.</td>
                                <td><?echo $_SESSION['lang']['kode'];?></td>
                                <td><?echo $_SESSION['lang']['namakelompok'];?></td>
                                <td><?echo $_SESSION['lang']['Type'];?></td>
                                <td><?echo $_SESSION['lang']['noakun'];?></td>
                                <td></td>
                        </tr>
                </thead>
                <tbody id=container>
<?php
$str=" select * from ".$dbname.".log_5klsupplier where tipe='".$tipe."' order by kelompok";
$res=mysql_query($str);

        while($bar=mysql_fetch_object($res))
        {
                $no+=1;
                echo"<tr class=rowcontent>
                      <td>".$no."</td>
                      <td>".$bar->kode."</td>
                          <td>".$bar->kelompok."</td>
                          <td>".$bar->tipe."</td>
                          <td>".$bar->noakun."</td>
                          <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delKlSupplier('".$bar->kode."');\"></td>
                          <td><img src=images/application/application_edit.png class=resicon  title='Update' onclick=\"editKlSupplier('".$bar->kode."','".$bar->kelompok."','".$bar->tipe."','".$bar->noakun."');\"></td>
                         </tr>";
        }	 	   	

?>			
                </tbody>
                <tfoot>'</tfoot>
        </table>
        </div>
</fieldset>	
<?php
CLOSE_BOX();
echo close_body();
?>