<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/approval.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['input'].' '.$_SESSION['lang']['persetujuan']);
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 order by namaorganisasi";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optOrg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
$str="select karyawanid,namakaryawan, lokasitugas from ".$dbname.".datakaryawan where tipekaryawan=0 
      and (tanggalkeluar='0000-00-00' or tanggalkeluar is null)order by namakaryawan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optkar.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."- ".$bar->lokasitugas."</option>";
}

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['kodeorg']."</td><td>
     <select id=kodeorg>".$optOrg."</select>    
     </td></tr>
	 <tr><td>Kode.App</td><td><input type=text id=app size=45 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
        <tr><td>Approval</td><td><select id=karyawanid>".$optkar."</select></td></tr> 
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanDep()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelDep()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['list']);

	$str1="select a.*,b.namakaryawan from ".$dbname.".setup_approval a
               left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               order by kodeunit";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td><td>APP</td><td>".$_SESSION['lang']['persetujuan']."</td><td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent><td align=center>".$bar1->kodeunit."</td><td>".$bar1->applikasi."</td><td>".$bar1->namakaryawan."</td>
                    <td>
                   <img src=images/skyblue/delete.png class=resicon  caption='Edit' onclick=\"dellField('".$bar1->kodeunit."','".$bar1->applikasi."','".$bar1->karyawanid."');\">     
                   </td></tr>";
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