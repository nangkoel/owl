<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<style>
	.input{
		background-color:#2AFFD4;
	}
	.tombol2{
	background-color:#A5A5A5;
	font-size:x-large;
	}
</style>
<script language=javascript1.2 src=js/hr.js></script>
<script language=javascript1.2 src=js/blocker.js></script>
<script language=javascript1.2 src=js/trx.js></script>
<script language=javascript1.2 src=js/generic.exe></script>
<?php
include('master_mainMenu.php');
$stg="select VEHNOCODE from ".$dbname.".msvehicle where FLAG='T' order by VEHNOCODE";
$reg=mysql_query($stg);
$opt_vehicle="<option value='0'></option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_vehicle.="<option value='".$bag[0]."'>".$bag[0]."</option>";
		}

$stg="select * from ".$dbname.".msunit,".$dbname.".msdivisi
where ".$dbname.".msdivisi.DIVCODE and ".$dbname.".msunit.UNITCODE=".$dbname.".msdivisi.UNITCODE
group by ".$dbname.".msdivisi.UNITCODE";
$reg=mysql_query($stg);
$opt_unit="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_unit.="<option value='".$bag[0]."'>".$bag[0]."</option>";
		}
$opt_divisi="<option></option>";
$trp_name="<option value='0'>Silakan Pilih...</option>";
$str="select distinct TICKETNO from ".$dbname.".mstrxtbs";
$res=mysql_query($str);
$row=mysql_num_rows($res);
//echo $row;
if($row<1){
	//$no=1;
	//echo str_pad($no,5,"0",STR_PAD_LEFT);
	$no_1=1;
	$no=str_pad($no_1,6,"0",STR_PAD_LEFT);
}
else {
	//$str2="select max(TICKETNO)+1 from wbridge.mstrxtbs";
	$str2="select * from ".$dbname.".mssystem";
	$res2=mysql_query($str2);
	while ($bar2=mysql_fetch_object($res2)){
		$IDWB=$bar2->IDWB;
	}
	$str2="select TICKETNO from ".$dbname.".mstrxtbs where IDWB='".$IDWB."' order by TICKETNO desc limit 1";
	//echo $str2;
	$res2=mysql_query($str2);
	if (mysql_num_rows($res2)>0){
	while ($bar=mysql_fetch_array($res2))
		{
			//$no=$bar[0];
			$ticketno=$bar[0];
			$no_1=intval($ticketno)+1;
			$no=str_pad($no_1,6,"0",STR_PAD_LEFT);
			//echo str_pad($no,5,"0",STR_PAD_LEFT);
		}
	}
	else {
		$no3=1;
		$no=str_pad($no3,6,"0",STR_PAD_LEFT);
	}
}
$str2="select * from ".$dbname.".mssystem";
$res2=mysql_query($str2);
while ($bar2=mysql_fetch_object($res2)){
	$IDWB=$bar2->IDWB;
}
$stg="select * from ".$dbname.".msvendortrp order by TRPCODE";
$reg=mysql_query($stg);
$opt_trp="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_trp.="<option value='".$bag[0]."'>".$bag[1]."</option>";
		}
$stg="select * from ".$dbname.".msproduct where PRODUCTCODE like '10%' order by PRODUCTCODE";
$reg=mysql_query($stg);
$opt_product="<option value='0'>Silakan Pilih....</option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_product.="<option value='".$bag[0]."'>".$bag[1]."</option>";
		}
$stg1="select UNITCODE,UNITNAME from ".$dbname.".msunit order by UNITCODE";
$reg1=mysql_query($stg1);
$opt_unit="<option value='0'>Silakan Pilih....</option>";
	while($bag1=mysql_fetch_array($reg1))
		{
			$opt_unit.="<option value='".$bag1[0]."'>".$bag1[0]."</option>";
		}
/*$str="select distinct TICKETNO from wbridge.mstrxtbs";
$res=mysql_query($str);
$row=mysql_num_rows($res);
//echo $row;
if($row<1){
	$no=1;
}
else {
	$str2="select max(TICKETNO)+1 from wbridge.mstrxtbs";
	$res2=mysql_query($str2);
	while ($bar=mysql_fetch_array($res2))
		{
			$no=$bar[0];
		}
}*/

//$filename = "D:\WB\WB.txt";
//$handle = fopen($filename, "r");
//$contents = fread($handle, filesize($filename));
//$content2 = substr($contents,0,6);
//$content3 = intval($content2);
//fclose($handle);

$content3='0';
OPEN_BOX('');
echo"
<table border=1 width=100%><tr><td><img src='images/E1205web.gif' width=70 height=70 align=absmiddle>
<strong><font size=3 color=#000000 font-family: Verdana, Arial, Helvetica, sans-serif>TIMBANG COMPOSTING</font>
</strong></td>
<td align='right' <!--bgcolor='#FFFFFF'--><button onclick=getReminderData()>P</button><input type=text name=WEIGH id=WEIGH size=7 style='background-color:#2AFFD4;height:60px;font-size:50px;text-align:right' maxlength=7 value='$content3' onkeypress='return false;'>
<font face='Times New Roman' size='7' color='black'>Kg</font></td></tr></table>
";

echo"</tbody>
     </table>
     </div>
	 </span>";
CLOSE_BOX();

OPEN_BOX('');
echo"<!--<fieldset style='width:100%;text-align:right;'>-->
	 <fieldset>
     <legend>
	 <!--<img src=images/E1205web.jpg align=left height=50px width=50px valign=asmiddle>-->
	 <b>Input Data Timbang Composting</b>
	 </legend>
     <table border=0>
					<tr><td><b>NO. TIKET &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b></td>
					<td><input type=text style='background-color:#2AFFD4;height:22px;font-size:22px;text-align:center;' name=IDWB id=IDWB size=1 disabled='true' style='font-size:20px' value='$IDWB' '><input type=text name=TICKETNO id=TICKETNO size=5 disabled='true' style='background-color:#2AFFD4;height:22px;font-size:22px;text-align:center;' value='$no'></td></tr>
					<tr><td><b>NO.KENDARAAN
					:</b></td>
					<td><select id=VEHNOCODE tabindex='1' style='height:18px;text-align:left;font-size:12px;'>".$opt_vehicle."</select>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<td><b>PENGANGKUT &nbsp;&nbsp;:</b></td>
					<td><select id=TRPCODE tabindex='3' style='height:22px;font-size:12px;text-align:left;'>".$opt_trp."</select></td>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;
					<b>SUPIR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					:</b>&nbsp;&nbsp<td><input type=text tabindex='5' id=DRIVER size=25 onkeypress='return charAndNum(event);' maxlength=30  style='text-align:right;'></td></tr>
					<tr><td><b>PRODUCT &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					:</b></td>
					<td><select id=PRODUCT tabindex='2' style='height:22px;font-size:12px;text-align:left;'>".$opt_product."</select>
					</td>
					<td><b>PENERIMA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b></td>
					<td><select id=PENERIMA tabindex='4' style='height:22px;font-size:12px;text-align:left;'>".$opt_unit."</select></td>
					
					</tr>

			</table>
     </fieldset>";
echo"</tbody>
     </table>
     </div>
	 </span>";
CLOSE_BOX();
OPEN_BOX('');
echo"
	 <table width=100% border=0 align=center>
	 <tr><td align='left'>
	 <fieldset>
     <legend>
	 <b>Timbang 1</b>
	 </legend>
	 <table border=0 width=100%>
	 <tr><td>
		TANGGAL : <input type=text name=datein id=datein size=20 onkeypress='return false;' style='height:20px;font-size:18px;text-align:right;'><br><br>
		BERAT  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input type=text size=12 id=WEIGH1  onkeypress='return false;' style='height:20px;font-size:18px;text-align:right;'>&nbsp;<b>KG</B><br>
     </td></tr>
     <tr><td align=center>
     <input type=button id=button1 tabindex='6' class=tombol2 value=GetWeight onclick=ambil_tanggal();>
     </td></tr>
     </table>
     </fieldset>
     </td>
     <td align='left'>
	 <fieldset>
     <legend>
	 <b>Timbang 2 :</b>
	 </legend>
		<table border=0 width=100%>
	 <tr><td>
		TANGGAL : <input type=text name=dateout id=dateout size=20 onkeypress='return false;' style='height:20px;font-size:18px;text-align:right;' disabled><br><br>
		BERAT  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input type=text size=12 id=WEIGH2  onkeypress='return false;' style='height:20px;font-size:18px;text-align:right;' disabled>&nbsp;<b>KG</B><br>
     </td></tr>
     <tr><td align=center>
     <input type=button id=button2 class=tombol2 value=GetWeight onclick=ambil_tanggal3(); disabled>
     </td>
     </tr>
     </table>
     </fieldset>
     </td>
     <td align='left'>
	 <fieldset>
     <legend>
	 <b>SIMPAN :</b>
	 </legend>
		<table border=0 width=100%>
	 <tr><td align=center height='44px' valign='bottom'>
		<b>NETTO :</b> <input type=text id=NETTO size=12 onkeypress='return false;' style='height:32px;font-size:20px;text-align:right;'>
     </td></tr>
     <tr><td align=center height='62px' valign='bottom'>
     <input type=button id=SIMPAN class=tombol2 value=SIMPAN onclick=saveComposting();>&nbsp;&nbsp;
     <input type=button id=button3 class=tombol2 value=BATAL onclick=window.location.reload();>
     </td>
     </tr>
     </table>
     </fieldset>
     </td>
     </tr></table>
";

echo"</tbody>
     </table>
     </div>
	 </span>";
CLOSE_BOX();
OPEN_BOX('');
echo "<center><b>LIST KENDARAAN TIMBANG COMPOSTING YG BELUM TIMBANG KELUAR</b></center>";
 echo "<center><table border=1 cellspacing=0 width=70%></center>";
      echo "<tr>
		    <td align=center width=10%><b>No. TIKET</b></td>
            <td align=center width=10%><b>No. KEND</b></td>
            <td align=center width=20%><b>TGL.MASUK</b></td>
            <td align=center width=20%><b>SUPIR</b></td>
            <td align=center width=10%><b>Timbang I</b></td>
            <td align=center width=20%><b>Ditimbang Oleh</b></td>
            </tr>";
$str6="select * from ".$dbname.".mssystem";
$res6=mysql_query($str6);
while ($bar6=mysql_fetch_object($res6)){
	$IDWB=$bar6->IDWB;
}
$str4="select *,count(TICKETNO) as tiket from ".$dbname.".mstrxtbs where PRODUCTCODE like '10%' and ".$dbname.".mstrxtbs.IDWB='".$IDWB."' group by TICKETNO having tiket=1";
//echo $str4;
$res4=mysql_query($str4);
while($bar4=mysql_fetch_object($res4)){
	$str5="select * from ".$dbname.".msvendortrp where ".$dbname.".msvendortrp.TRPCODE='".$bar4->TRPCODE."'";
	$res5=mysql_query($str5);
	$bar5=mysql_fetch_object($res5);
	$str6="select * from ".$dbname.".msproduct where PRODUCTCODE='".$bar4->PRODUCTCODE."'";
	$res6=mysql_query($str6);
	$bar6=mysql_fetch_object($res6);
	$spbno=$bar4->SPBNO;
	$retval=substr($spbno,0,3);
   echo"<tr class=content onmouseover=\"this.style.backgroundColor='#00FF00';\" onmouseout=\"this.style.backgroundColor='#FFFFFF';\" style='cursor:pointer;' title='Click untuk memilih'
  onclick=\"loadComposting('".$bar4->TICKETNO."','".$bar4->VEHNOCODE."','".$bar4->TRPCODE."','".$bar4->DRIVER."','".tanggalnormal($bar4->DATEIN)."','".$bar4->WEI1ST."','".$bar4->PENERIMA."','".$bar5->TRPNAME."','".$bar4->PRODUCTCODE."','".$bar6->PRODUCTNAME."');\">	
  <td align=center>".$bar4->TICKETNO2."</td>
  <td align=center>".$bar4->VEHNOCODE."</td>
  <td align=center>".tanggalnormal($bar4->DATEIN)."</td>
  <td align=center>".$bar4->DRIVER."</td>
  <td align=center>".$bar4->WEI1ST."</td>
  <td align=center>".$bar4->USERID."</td>
  </tr>";

 }
echo"</tbody>
     </table>
     </div>
	 </span>";
CLOSE_BOX();
echo close_body();
?>
