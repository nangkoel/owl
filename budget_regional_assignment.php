<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/budget_regional_assignment.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['input'].' '.$_SESSION['lang']['regional'].' Assignment');

//ambil organisasi 
$optorg='';
$str="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi 
      where char_length(kodeorganisasi) = 4
      order by kodeorganisasi";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $optorg.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi." - ".$bar->namaorganisasi."</option>";	
}

//ambil regional 
$optreg='';
$str="select regional, nama from ".$dbname.".bgt_regional 
      order by regional";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $optreg.="<option value='".$bar->regional."'>".$bar->regional." - ".$bar->nama."</option>";	
}

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['kodeorganisasi']."</td><td><select onchange=\"resetcontainer();\" id=organisasi style='width:150px'><option value=''>".$optorg."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['regional']."</td><td><select onchange=\"resetcontainer();\" id=regional style='width:150px'><option value=''>".$optreg."</select></td></tr>
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanDep()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelDep()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['datatersimpan']);

	$str1="select * from ".$dbname.".bgt_regional_assignment order by kodeunit";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['kodeorganisasi']."</td><td>".$_SESSION['lang']['regional']."</td><td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent><td align=center>".$bar1->kodeunit."</td><td>".$bar1->regional."</td><td align=center><img src=images/application/application_delete.png class=resicon  caption='Edit' onclick=\"deleteDep('".$bar1->kodeunit."','".$bar1->regional."');\"></td></tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";

echo close_theme();
CLOSE_BOX();
echo close_body();
?>