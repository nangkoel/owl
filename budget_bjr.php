<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/anggaran_bjr.js'></script>


<?php
include('master_mainMenu.php');

//$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='KEBUN' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' ORDER BY kodeorganisasi";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
                        {
                       	 $optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
                        }	


$optthnttp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optorgclose="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";			

?>


<?php
OPEN_BOX('',"<b>".$_SESSION['lang']['bjr']."</b>");

echo"<br /><br /><fieldset style='float:left;'>
                <legend>".$_SESSION['lang']['entryForm']."</legend> 
                        <table border=0 cellpadding=1 cellspacing=1>
                                <tr><td width=100>".$_SESSION['lang']['budgetyear']."<td width=10>:</td></td><td><input type=text id=tahunbudget size=10 onkeypress=\"return angka(event,'0123456789');validatefn(event);\" class=myinputtext maxlength=4 style=\"width:200px;\"></td></tr>
                                <tr><td>".$_SESSION['lang']['kodeorganisasi']." <td>:</td></td><td><select id=kodeorg style=\"width:200px;\" >".$optOrg."</select></td></tr>
                                <tr><td>".$_SESSION['lang']['thntnm']."<td>:</td></td><td><input type=text id=thntanam size=10 onkeypress=\"return angka(event,'0123456789');validatefn(event);\" class=myinputtext maxlength=4 style=\"width:200px;\"></td></tr>
                                <tr><td>".$_SESSION['lang']['bjr']."<td>:</td></td><td><input type=text id=bjr size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:200px;\"> </td></tr>

                                <tr><td></td><td></td><br />
                                        <td><br /><button class=mybutton onclick=simpanbjr()>Simpan</button>
                                        <button class=mybutton onclick=cancelbjr()>Hapus</button></td></tr>
                        </table></fieldset>
                                        <input type=hidden id=method value='insert'>
                                        <input type=hidden id=oldtahunbudget value='insert'>
                                        <input type=hidden id=oldkodeorg value='insert'>
                                        <input type=hidden id=oldthntanam value='insert'>";


echo"<fieldset  style='float:left'><legend>".$_SESSION['lang']['tutup']."</legend>
    <div id=closetab><table>
                <tr><br /><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td><select id=thnttp style='widht:150px'>".$optthnttp."</select></td></tr>
                <tr><td>".$_SESSION['lang']['kodeorganisasi']." </td><td>:</td><td><select id=lkstgs style='widht:150px'>".$optorgclose."</select></td></tr>";
echo"<tr><td></td><td></td><td><br /><button class=\"mybutton\"  id=\"saveData\" onclick='closebjr()'>".$_SESSION['lang']['tutup']."</button></td></tr></table>";
echo"</div></fieldset>";
CLOSE_BOX();

OPEN_BOX();
//ISI UNTUK DAFTAR 
echo "<fieldset>";
echo "<legend><b>".$_SESSION['lang']['datatersimpan']."</b></legend>";
echo "<div id=container>";

        echo"<table class=sortable cellspacing=1 border=0>
             <thead>
                 <tr class=rowheader>
                         <td align=center style='width:5px;'>No</td>
                         <td align=center style='width:100px;'>".$_SESSION['lang']['budgetyear']."</td>
                         <td align=center style='width:125px;'>".$_SESSION['lang']['kodeorganisasi']." </td>
                         <td align=center style='width:100px;'>".$_SESSION['lang']['thntnm']."</td>
                         <td align=center style='width:50px;'>".$_SESSION['lang']['bjr']."</td>
                         <td align=center style='width:20px;'>Edit</td></tr>
                 </thead>
                 <tbody id='containerData'><script>loadData()</script>";


        echo"	 
                 </tbody>
                 <tfoot>
                 </tfoot>
                 </table>";
echo "</div>";
echo close_theme();
echo "</fieldset>";
CLOSE_BOX();
echo close_body();					
?>