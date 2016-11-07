<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/sdm_5periodegajiunit.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['periodepenggajian']);
$optPrd="<option value=''></option>";
for($x=0;$x<=12;$x++)
{
	$dte=mktime(0,0,0,(date('m')+2)-$x,15,date('Y'));
	$optPrd.="<option value=".date("Y-m",$dte).">".date("m-Y",$dte)."</option>";
}
if ($_SESSION['empl']['bagian']=='IT'){
    $str="select * from ".$dbname.".bgt_regional_assignment where kodeunit not like '%HO' order by kodeunit";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $optOrg.="<option value='".$bar->kodeunit."'>".$bar->kodeunit."</option>";
    }	
} else {
    $optOrg="<option value='".substr($_SESSION['empl']['lokasitugas'],0,4)."'>".substr($_SESSION['empl']['lokasitugas'],0,4)."</option>";
}
for($x=0;$x<=12;$x++)
{
	$dte=mktime(0,0,0,(date('m')+2)-$x,15,date('Y'));
	$optPrd.="<option value=".date("Y-m",$dte).">".date("m-Y",$dte)."</option>";
}
$metodepenggajian='';
$metodepenggajian.="<option value='H'>".$_SESSION['lang']['harian']."</option>
                    
					";

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['kodeorg']."</td><td><select id=kodeorg>".$optOrg."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['metodepanggajian']."</td><td><select id=metodepenggajian>".$metodepenggajian."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['periode']."</td><td><select id=periode name=periode>".$optPrd."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['tanggalmulai']."</td><td><input type=text id=tanggalmulai size=10 onkeypress=\"return false;\" class=myinputtext maxlength=10  onmouseover=setCalendar(this)></td></tr>
     <tr><td>".$_SESSION['lang']['tanggalsampai']."</td><td><input type=text id=tanggalsampai size=10 onkeypress=\"return false;\" class=myinputtext maxlength=10 onmouseover=setCalendar(this)></td></tr>
         <tr><td>".$_SESSION['lang']['tanggal']." cut off</td><td><input type=text id=tanggalctf size=10 onkeypress=\"return false;\" class=myinputtext maxlength=10  onmouseover=setCalendar(this)></td></tr>
	 <tr><td>".$_SESSION['lang']['tutup']."</td><td><input type=checkbox id=tutup>".$_SESSION['lang']['yes']."/".$_SESSION['lang']['no']."</td></tr>
	 </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['availvhc']);
echo "<div>";
    if ($_SESSION['empl']['bagian']=='IT'){
	$str1="select *,
	     case jenisgaji when 'H' then '".$_SESSION['lang']['harian']."'
		 when 'B' then '".$_SESSION['lang']['bulanan']."'
		 end as ketgroup, 
		 case sudahproses when '1' then '".$_SESSION['lang']['yes']."'
		 when '0' then '".$_SESSION['lang']['no']."'
		 end as sts
	     from ".$dbname.".sdm_5periodegaji order by kodeorg, periode desc";
    } else {
	$str1="select *,
	     case jenisgaji when 'H' then '".$_SESSION['lang']['harian']."'
		 when 'B' then '".$_SESSION['lang']['bulanan']."'
		 end as ketgroup, 
		 case sudahproses when '1' then '".$_SESSION['lang']['yes']."'
		 when '0' then '".$_SESSION['lang']['no']."'
		 end as sts
	     from ".$dbname.".sdm_5periodegaji 
		 where kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
		 order by periode desc";
    }
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:750px;'>
	     <thead>
		 <tr class=rowheader>
		    <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>
			<td>".$_SESSION['lang']['metodepanggajian']."</td>
			<td>".$_SESSION['lang']['periode']."</td>
			<td>".$_SESSION['lang']['tanggalmulai']."</td>
			<td>".$_SESSION['lang']['tanggalsampai']."</td>
                        <td>".$_SESSION['lang']['tanggal']." cut off</td>
			<td>".$_SESSION['lang']['tutup']."</td>
			<td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>"; 
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
		           <td align=center>".$bar1->kodeorg."</td>
				   <td>".$bar1->ketgroup."</td>
				   <td align=center>".substr(tanggalnormal($bar1->periode),1,7)."</td>
				   <td align=center>".tanggalnormal($bar1->tanggalmulai)."</td>
				   <td align=center>".tanggalnormal($bar1->tanggalsampai)."</td>
                                   <td align=center>".tanggalnormal($bar1->tglcutoff)."</td>
				   <td align=center>".$bar1->sts."</td>
				   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->jenisgaji."','".$bar1->periode."','".tanggalnormal($bar1->tanggalmulai)."','".tanggalnormal($bar1->tanggalsampai)."','".$bar1->sudahproses."','".tanggalnormal($bar1->tglcutoff)."');\"></td></tr>";
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