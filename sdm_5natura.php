<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/sdm_5natura.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['setupnatura']);



$str="select * from ".$dbname.".sdm_5catuporsi order by kode";
$res=mysql_query($str);
$st='';
while($bar=mysql_fetch_object($res))
{
	$st.="<option value='".$bar->kode."'>[".$bar->kode."]-".$bar->keterangan."</option>";
}

echo"<fieldset style='width:500px;'><table>
                <tr><td>".$_SESSION['lang']['kodeorg']."</td><td><select id=kodeorg><option value='".substr($_SESSION['empl']['lokasitugas'],0,4)."'>".substr($_SESSION['empl']['lokasitugas'],0,4)."</option></select></td></tr>
                <tr><td>".$_SESSION['lang']['tahun']."</td><td><input type=text id=tahun size=4 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=4 value=".date('Y')."></td></tr>
                <tr><td>".$_SESSION['lang']['kodekelompok']."</td><td><select id=kode>".$st."</option></select></td></tr>
                 <tr><td>".$_SESSION['lang']['jumlah']."</td><td><input type=text id=jumlah size=4 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=4 value=0>Ltr</td></tr>
	 <tr><td>".$_SESSION['lang']['keterangan']."</td><td><input type=text id=keterangan size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext maxlength=45></td></tr>
	 </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['availvhc']);
echo "<div>";
	$str1="select *
	     from ".$dbname.".sdm_5catu 
		   where kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
		  order by tahun desc,kelompok"; 
	$res1=mysql_query($str1);

	echo"<table class=sortable cellspacing=1 border=0 style='width:700px;'>
	     <thead>
		 <tr class=rowheader>
		    <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>
			<td>".$_SESSION['lang']['tahun']."</td>
			<td>".$_SESSION['lang']['kodekelompok']."</td>
			<td>".$_SESSION['lang']['keterangan']."</td>
			<td>".$_SESSION['lang']['jumlah']."  (Ltr)</td>
			<td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>"; 
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
		        <td align=center>".$bar1->kodeorg."</td>
                                        <td align=center>".$bar1->tahun."</td>
                                        <td align=center>".$bar1->kelompok."</td>    
                                         <td>".$bar1->keterangan."</td>    
                                        <td align=right>".$bar1->jumlah."</td>
                                        <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->tahun."','".$bar1->kelompok."','".$bar1->keterangan."','".$bar1->jumlah."');\"></td></tr>";
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