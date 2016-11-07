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
<script language=javascript1.2 src='js/hr.js'></script>
<script language=javascript1.2 src='js/blocker.js'></script>
<script language=javascript1.2 src='js/trx.js'></script>
<script language=javascript1.2 src='js/generic.exe'></script>
<?php
include('master_mainMenu.php');
$stg="select VEHNOCODE from ".$dbname.".msvehicle where FLAG='T' order by VEHNOCODE";
$reg=mysql_query($stg);
$opt_vehicle="<option value='0'></option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_vehicle.="<option value='".$bag[0]."'>".$bag[0]."</option>";
		}

$str="select kodefraksi,potongan from ".$dbname.".msfraksi";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    echo "<input type=hidden id=frk".$bar->kodefraksi." value=".$bar->potongan.">";
    if($bar->kodefraksi=='pottetap')
        $ptetap=$bar->potongan;
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
$stg="select * from ".$dbname.".msvendortrp order by TRPNAME";
$reg=mysql_query($stg);
$opt_trp="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_trp.="<option value='".$bag[0]."'>".$bag[1]."</option>";
		}

//$filename = "D:\WB\\result.nangkoel";
//$handle = fopen($filename, "r");
//$contents = fread($handle, filesize($filename));
//$content2 = substr($contents,0,6);
//$content3 = intval($content2);
//fclose($handle);

$content3='0';
OPEN_BOX('');
echo"
<table border=1 width=100%><tr><td><img src='images/E1205web.gif' width=70 height=70 align=absmiddle>
<strong><font size=3 color=#000000 font-family: Verdana, Arial, Helvetica, sans-serif>TIMBANG TBS EKSTERNAL</font>
</strong></td>
<td align='right' <!--bgcolor='#FFFFFF'--><button onclick=getReminderData()>P</button><input type=text name=WEIGH id=WEIGH size=7 style='background-color:#2AFFD4;height:60px;font-size:50px;text-align:right' maxlength=7 value='$content3' onkeypress='return false;' disabled>
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
	 <b>Input Data SPB Eksternal</b>
	 </legend>
      <table><tr><td>   
      <table border=0>
            <tr><td>NO. TIKET 
            :</td>
            <td><input type=text style='background-color:#2AFFD4;height:22px;font-size:22px;text-align:center;' name=IDWB id=IDWB size=1 disabled='true' style='font-size:20px' value='$IDWB' '><input type=text name=TICKETNO id=TICKETNO size=5 disabled='true' style='background-color:#2AFFD4;height:22px;font-size:22px;text-align:center;' value='$no'></td></tr>
            <tr><td>NO. SPB 
            :</td>
            <td><input type=text id=SPBNO  size=30 maxlength=35 onkeypress='return charAndNumAndStrip(event);' style='text-align:right;'>
            
            <td align=right>JUM.JJG I</td><td>
            :<input type=text  id=JUMJJG size=5 onkeypress='return angka_doang(event);' maxlength=5 value=0 style='text-align:right;'></td>
            </tr>
            <tr><td>NO.KENDARAAN
            :</td><td><input type=text id=VEHNOCODE  style='height:18px;text-align:left;font-size:12px;' size=8 maxlength=8 onkeypress=\"return tanpa_kutip(event);\">
            </td>
            <td align=right>B J R </td><td>
            :<input type=text  id=BJR size=5 onkeypress='return false;' maxlength=3 value=0 style='text-align:right;'></td>
            </tr>
            <tr><td>PENGIRIM
            :</td><td align=right><select id=TRPCODE  style='height:22px;font-size:12px;text-align:left;width:200px;'>".$opt_trp."</select></td>
            <td align=right>NAMA SUPIR</td><td><input type=text  id=DRIVER size=20 onkeypress='return charAndNum(event);' maxlength=35 style='text-align:right;'>*</td>
            </tr>
            </table>
            
            <input type=hidden id=TAHUNTANAM size=5 onkeypress='return angka_doang(event);' maxlength=35  style='text-align:right;'>            
            <input type=hidden id=BRONDOLAN size=5 onkeypress='return angka_doang(event);' maxlength=4 value=0 style='text-align:right;'>
            <input type=hidden id=TAHUNTANAM2 size=5 onkeypress='return angka_doang(event);' maxlength=4  style='text-align:right;'>          
            <input type=hidden id=JUMJJG2 size=5 onkeypress='return angka_doang(event);' maxlength=4 value=0 style='text-align:right;'>
            <input type=hidden id=BERATKIRIM size=20 onkeypress='return angka_doang(event);' maxlength=35 style='text-align:right;'>     
           <input type=hidden id=BRONDOLAN2 size=5 onkeypress='return angka_doang(event);' maxlength=4 value=0 style='text-align:right;'>   
           <input type=hidden id=JUMJJG3 size=5 onkeypress='return angka_doang(event);' maxlength=4 value=0 style='text-align:right;'> 
           <input type=hidden id=BRONDOLAN3 size=5 onkeypress='return angka_doang(event);' maxlength=4 value=0 style='text-align:right;'> 
           <input type=hidden id=TAHUNTANAM3 size=5 onkeypress='return angka_doang(event);' maxlength=6  style='text-align:right;'>    
           </td>
           <td>
     <fieldset><legend>Sortasi</legend>
      <table border=1 cellspacing=0>
      <tr><td>Buah Busuk</td><td><input type=text id=buahbusuk size=5 value=0 onblur=periksa(this) style='text-align:right'>JJG</td>
      <td>Krng.Matang</td><td><input type=text id=buahkrgmatang size=5 value=0 onblur=periksa(this) style='text-align:right'>JJG</td></tr>
      <tr><td>Buah Sakit</td><td><input type=text id=buahsakit size=5 value=0 onblur=periksa(this) style='text-align:right'>JJG</td>
      <td>JJg.Kosong</td><td><input type=text id=janjangkosong size=5 value=0 onblur=periksa(this) style='text-align:right'>JJG</td></tr>
      <tr><td>Lewat Matang</td><td><input type=text id=lwtmatang size=5 value='0' onblur=periksa(this) style='text-align:right'>JJG</td>
      <td>Buah Mentah</td><td><input type=text id=mentah size=5 value='0' onblur=periksa(this) style='text-align:right'>JJG</td></tr>
      <tr><td>Tk.Panjang</td><td><input type=text id=tkpanjang size=5 value='0' onblur=periksa(this) style='text-align:right'>JJG</td>
      <td>BJR Kurang 3Kg</td><td><input type=text id=tigakilo size=5 value='0' onblur=periksa(this) style='text-align:right'>JJG</td></tr>
      </table>
      </fieldset>              
           </td>
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
		TANGGAL : <input type=text name=datein id=datein  size=20 onkeypress='return false;' style='height:20px;font-size:18px;text-align:right;'><br><br>
		BERAT  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input type=text size=12 id=WEIGH1  onkeypress='return false;' style='height:20px;font-size:18px;text-align:right;'  disabled>&nbsp;<b>KG</B><br>
     </td></tr>
     <tr><td align=center>
     <input type=button id=button1 class=tombol2 value=GetWeight onclick=ambil_tanggal();>
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
     <input type=button id=button2 class=tombol2 value=GetWeight onclick=ambil_tanggal2(); disabled>
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
	 <tr><td align=center height='35px' valign='bottom'> 
		<b><font size=4>NETTO</font> :</b></td><td> <input type=text id=NETTO size=12 onkeypress='return false;' style='height:20px;font-size:20px;text-align:right;'  disabled>
     </td></tr>
     	 <tr><td align=center height='35px' valign='bottom'>
		<b>POTONGAN (KG) :</b></td><td> <input type=text id=POTONGAN size=12 onkeypress='return angka_doang(event);' value=0 onfocus='bersihkanField(this);' style='height:20px;font-size:20px;text-align:right;' maxlength=7>
     </td></tr>
     <tr><td align=center height='62px' valign='bottom' colspan=2>
     <input type=button id=SIMPAN class=tombol2 value=SIMPAN onclick=saveTbsEks();>&nbsp;&nbsp;
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
echo "<center><b>LIST KENDARAAN TBS EKSTERNAL YG BELUM TIMBANG KELUAR</b></center>";
 echo "<center><table border=1 cellspacing=0 width=70%></center>";
      echo "<tr>
            <td align=center width=10%><b>No. TIKET</b></td>
            <td align=center width=10%><b>No. KEND</b></td>
	    <td align=center width=10%><b>PENGIRIM</b></td>
            <td align=center width=20%><b>TGL.MASUK</b></td>
            <td align=center width=20%><b>No. SPB</b></td>
            <td align=center width=10%><b>Timbang I</b></td>
            <td align=center width=20%><b>Ditimbang Oleh</b></td>
            </tr>";
$str6="select * from ".$dbname.".mssystem";
$res6=mysql_query($str6);
while ($bar6=mysql_fetch_object($res6)){
	$IDWB=$bar6->IDWB;
}
$str4="select *,count(TICKETNO) as tiket from ".$dbname.".mstrxtbs where PRODUCTCODE='40000003' and ".$dbname.".mstrxtbs.IDWB='".$IDWB."' and UNITCODE is null group by TICKETNO having tiket=1";
//echo $str4;
$res4=mysql_query($str4);
while($bar4=mysql_fetch_object($res4)){
	//$str5="select * from wbridge.msvendortrp,wbridge.msvehicle where wbridge.msvehicle.VEHNOCODE='".$bar4->VEHNOCODE."' and wbridge.msvendortrp.TRPCODE=wbridge.msvehicle.TRPCODE";
	$str5="select * from ".$dbname.".msvendortrp where TRPCODE='".$bar4->TRPCODE."'";
	//echo $str5;
	$res5=mysql_query($str5);
	$bar5=mysql_fetch_object($res5);
	$spbno=$bar4->SPBNO;
	$retval=substr($spbno,0,3);
  echo"<tr class=content onmouseover=\"this.style.backgroundColor='#00FF00';\" onmouseout=\"this.style.backgroundColor='#FFFFFF';\" style='cursor:pointer;' title='Click untuk memilih'
  onclick=\"loadeks('".$bar4->TICKETNO."','".$bar4->JENISSPB."','".$bar4->SPBNO."','".$bar4->VEHNOCODE."','".$bar4->TRPCODE."','".$bar4->DRIVER."','".$bar4->JMLHJJG."','".$bar4->JMLHJJG2."','".$bar4->JMLHJJG3."','".$bar4->TAHUNTANAM."','".$bar4->TAHUNTANAM2."','".$bar4->TAHUNTANAM3."','".$bar4->BRONDOLAN."','".$bar4->BRONDOLAN2."','".$bar4->BRONDOLAN3."','".$bar4->BERATKIRIM."','".tanggalnormal($bar4->DATEIN)."','".$bar4->WEI1ST."','".$bar5->BUYERNAME."');\">
  <td align=center>".$bar4->TICKETNO2."</td>
  <td align=center>".$bar4->VEHNOCODE."</td>
  <td align=center>".$bar5->TRPNAME."</td>
  <td align=center>".tanggalnormal($bar4->DATEIN)."</td>
  <td align=center>".$bar4->SPBNO."</td>
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
