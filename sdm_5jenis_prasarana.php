<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/sdm_5jenis_prasarana.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['jnsPrasarana']);
$optKlmpk="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sKlmpk="select distinct * from ".$dbname.".sdm_5kl_prasarana order by kode asc";
$qKlmpk=mysql_query($sKlmpk) or die(mysql_error());
while($rKlmpk=mysql_fetch_assoc($qKlmpk))
{
    $orgNmKlmpk[$rKlmpk['kode']]=$rKlmpk['nama'];
    $optKlmpk.="<option value='".$rKlmpk['kode']."'>".$rKlmpk['nama']."</option>";
}
echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['kodekelompok']."</td><td><select id=idKlmpk>".$optKlmpk."</select></td></tr>
         <tr><td>".$_SESSION['lang']['jenis']."</td><td><input type=text id=kodejabatan size=3 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['namajenisvhc']."</td><td><input type=text id=namajabatan size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
             <tr><td>".$_SESSION['lang']['satuan']."</td><td><input type=text id=satuan size=20 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanJabatan()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJabatan()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['list']." ".$_SESSION['lang']['jnsPrasarana']);
echo "<div id=container>";
	$str1="select * from ".$dbname.".sdm_5jenis_prasarana order by nama";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader><td>".$_SESSION['lang']['namakelompok']."</td><td>".$_SESSION['lang']['jenis']."</td><td>".$_SESSION['lang']['namajenisvhc']."</td><td>".$_SESSION['lang']['satuan']."</td><td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
                    <td align=center>".$orgNmKlmpk[$bar1->kelompok]."</td>
                    <td>".$bar1->jenis."</td>
                    <td>".$bar1->nama."</td>
                    <td>".$bar1->satuan."</td>
                        
                    <td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kelompok."','".$bar1->jenis."','".$bar1->satuan."','".$bar1->nama."');\"></td></tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo "</div>";
echo close_theme();
CLOSE_BOX();
echo close_body();
?>