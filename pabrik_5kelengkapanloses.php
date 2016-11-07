<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
require_once('lib/zLib.php');
?>

<script language=javascript1.2 src='js/pabrik_5kelengkapanloses.js'></script>

<?php
$optOrg="<option value=''>Pilih data</option>";
$x="select * from ".$dbname.".organisasi where length(kodeorganisasi)=4 and kodeorganisasi like '%M' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$y=  mysql_query($x) or die (mysql_error($conn));
while($z=mysql_fetch_assoc($y))
{
    $optOrg.="<option value='".$z['kodeorganisasi']."'>".$z['namaorganisasi']."</option>";
}

$optProduk="<option value=''>Pilih data</option>";
$optProduk.="<option value='CPO'>CPO</option>";
$optProduk.="<option value='KERNEL'>KERNEL</option>";

$nama=  makeOption($dbname,'datakaryawan','karyawanid,namakaryawan')

?>

<?php
OPEN_BOX();
echo"
    <fieldset style='float:left;'>
        <legend>Kelengkapan Loses</legend>
            <table border=0 cellspacing=1 cellpadding=0>
                
               
                  <input id=id disabled type=hidden onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=45>
               
                <tr>
                    <td>Kodeorg</td>
                    <td>:</td>
                     <td><input id=kodeorg disabled value='".$_SESSION['empl']['lokasitugas']."' type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=45></td>
                </tr>
                <tr>
                    <td>Produk</td>
                    <td>:</td>
                    <td><select id=produk>$optProduk</td>
                </tr>
                <tr>
                    <td>Nama Item</td>
                    <td>:</td>
                    <td><input id=namaitem type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=45></td>
                <tr>
                    <td>Standard</td>
                    <td>:</td>
                    <td><input id=standard type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=4></td>
                <tr>
                    <td>Satuan</td>
                    <td>:</td>
                    <td><input id=satuan type=text onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\" maxlength=3></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td> <button class=mybutton onclick=simpan()>Simpan</button></td>
                   
                </tr>
            </table>
       </fieldset>
<input type=hidden id=method value='insert'>";
CLOSE_BOX();

OPEN_BOX();
echo"
    <fieldset style='float:left;'>
        <legend>List Data</legend>
         <table border=0 cellspacing=1 cellpadding=0 class=sortable>
            <thead>
                <tr class=rowheader>
					<td>".$_SESSION['lang']['nourut']."</td>
				
                    
                    <td>".$_SESSION['lang']['kodeorg']."</td>
					<td>".$_SESSION['lang']['produk']."</td>
					<td>".$_SESSION['lang']['namabarang']."</td>
                   
				   <td>".$_SESSION['lang']['standard']."</td>
				   <td>".$_SESSION['lang']['satuan']."</td>
				   <td>".$_SESSION['lang']['updateby']."</td>
				   <td>".$_SESSION['lang']['action']."</td>
                   
                </tr>
            </thead>";
            $r="select * from ".$dbname.".pabrik_5kelengkapanloses where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            $s=  mysql_query($r) or die (mysql_error($conn));// <td>".$t['id']."</td>
            while($t=mysql_fetch_assoc($s))
            { 
			$no+=1;
                echo"<tr class=rowcontent>
					<td>".$no."</td>
                   
                    <td>".$t['kodeorg']."</td>
                    <td>".$t['produk']."</td>
                    <td>".$t['namaitem']."</td>
                    <td>".$t['standard']."</td>
                    <td>".$t['satuan']."</td>
                    <td>".$nama[$t['updateby']]."</td>
                    <td><img src=images/application/application_edit.png class=resicon title='Edit' caption='Edit' onclick=\"edit('".$t['id']."','".$t['kodeorg']."','".$t['produk']."','".$t['namaitem']."','".$t['standard']."','".$t['satuan']."');\">
			<img src=images/application/application_delete.png class=resicon title='Delete' caption='Delete' onclick=\"del('".$t['id']."');\"></td>
                    </td>
                </tr>";
             }   
         echo"</table>
    </fieldset>";


CLOSE_BOX();
?>