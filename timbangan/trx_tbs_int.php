<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zLib.php');
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
<script language=javascript1.2 src='js/trx.js'></script>
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

$str="select kodefraksi,potongan from ".$dbname.".msfraksi";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    echo "<input type=hidden id=frk".$bar->kodefraksi." value=".$bar->potongan.">";
    if($bar->kodefraksi=='pottetap')
        $ptetap=$bar->potongan;
}

// ambil parameter sistem
$str="select pecahtiket from ".$dbname.".mssystem";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $pecahtiket=$bar->pecahtiket;
}

$stg="select UNITCODE,UNITNAME from ".$dbname.".msunit order by UNITCODE";
//echo $stg;
$reg=mysql_query($stg);
$opt_unit="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_unit.="<option value='".$bag[0]."'>".$bag[1]."</option>";
		}
$opt_divisi="<option></option>";
$trp_name="<option value='0'>Silakan Pilih...</option>";
$str="select distinct TICKETNO from ".$dbname.".mstrxtbs";
$res=mysql_query($str);
$row=mysql_num_rows($res);
//echo $row;
if($row<1){
	$no_1=1;
	$no=str_pad($no_1,6,"0",STR_PAD_LEFT);
}
else {
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
			$ticketno=$bar[0];
			$no_1=intval($ticketno)+1;
			$no=str_pad($no_1,6,"0",STR_PAD_LEFT);
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
$stg="select * from ".$dbname.".msvendortrp order by TRPNAME desc";
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
<strong><font size=3 color=#000000 font-family: Verdana, Arial, Helvetica, sans-serif>TIMBANG TBS INTERNAL</font>
</strong></td>
<td align='right' <!--bgcolor='#FFFFFF'--><button onclick=getReminderData()>P</button><input type=text name=WEIGH id=WEIGH size=7 style='background-color:#2AFFD4;height:60px;font-size:50px;text-align:right' maxlength=7 value='$content3' onkeypress=\"return false;\" disabled>
<font face='Times New Roman' size='7' color='black'>Kg</font></td></tr></table>
";

echo"</tbody>
     </table>
     </div>
	 </span>";
CLOSE_BOX();

if (date('m')<12){
    if (date('d')>=26 and date('d')<=31){
        $bulan=addZero(date('m')+1,2);
    } else {
        $bulan=date('m');
    }
} else {
    $bulan=date('m');
}
OPEN_BOX('');
echo"<!--<fieldset style='width:100%;text-align:right;'>-->
	 <fieldset>
     <legend>
	 <!--<img src=images/E1205web.jpg align=left height=50px width=50px valign=asmiddle>-->
	 <b>Input Data SPB</b>
	 </legend>
      <table><tr><td>   
      <table border=0 cellspacing=4>
            <tr>
                <td><b>NO. TIKET :</b></td>
                <td><input type=text style='background-color:#2AFFD4;height:22px;font-size:22px;text-align:center;' name=IDWB id=IDWB size=1 disabled='true' style='font-size:20px' value='".$IDWB."'><input type=text name=TICKETNO id=TICKETNO size=5 disabled='true' style='background-color:#2AFFD4;height:22px;font-size:22px;text-align:center;' value='".$no."'></td>
                <td><b>UNIT:</b></td>
                 <td colspan=3><select id=unitcode tabindex='1' onchange=loa(this.options[selectedIndex].value,'Unit'); style='height:18px;text-align:left;font-size:11px;' tabindex='1'>".$opt_unit."</select></td> 
           </tr>
            <tr>
                    <td><b>NO. SPB :</b></td>
                    <td><input type=text id=SPBNO tabindex='2' size=5 maxlength=7 onkeypress='return angka_doang(event);' style='text-align:right;'>
                        <select id=divcode tabindex='3' style='height:22px;'>".$opt_divisi."</select>
                        <input type=text style='text-align:center;' id=bulan size=1 disabled value='".$bulan."'/>
                        <input type='text' style='text-align:center;' id=tahun size=2 disabled value='".date('Y')."'/></td>
            <td align=right><b>BJR :</b></td>
            <td><input type=text tabindex='8'  id=BJR size=10 onkeypress='return angka_doang(event);' value=0 maxlength=4 style='text-align:right;'>KG</td>                
            </tr>
            <tr>
             <td><b>NO.KENDARAAN :</b></td>
            <td><input type=text id=VEHNOCODE tabindex='4' style='height:18px;text-align:left;font-size:12px;' size=8 maxlength=8 onkeypress=\"return tanpa_kutip(event);\"></td>
            <td><b>JUM.JJG:</b></td>
            <td><input type=text tabindex='5' id=JUMJJG size=5 onkeypress='return angka_doang(event);' maxlength=5 style='text-align:right;'></td>
            </tr>
            <tr>
            <td><b>TRANSPORTER :</b></td>
            <td><select id=TRPCODE tabindex='7' style='height:22px;font-size:12px;text-align:left;width:175px;'>".$opt_trp."</select></td>  
            <td><b>NAMA SUPIR :</b></td>
            <td><input type=text tabindex='6' id=DRIVER size=20 onkeypress='return charAndNum(event);' maxlength=35 style='text-align:right;'></td>                
            
        </tr>

            <input type=hidden id=BERATKIRIM size=10 onkeypress='return angka_doang(event);' maxlength=6 style='text-align:right;'>
            <input type=hidden  id=TAHUNTANAM size=18 onkeypress='return angka_doang(event);' maxlength=18 style='text-align:right;'>
            <input type=hidden  id=BRONDOLAN size=10 onkeypress='return angka_doang(event);' maxlength=4 value=0 style='text-align:right;'> 
        
  </table>
  </td><td>
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
  </td></tr>
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
		BERAT  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input type=text size=12 id=WEIGH1 onkeypress='return false;'  style='height:20px;font-size:18px;text-align:right;' disabled>&nbsp;<b>KG</B><br>
     </td></tr>
     <tr><td align=center>
     <input type=button id=button1 tabindex='10' class=tombol2 value=GetWeight onclick=ambil_tanggal();>
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
		BERAT  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input type=text size=12 id=WEIGH2 onkeypress='return false;'  style='height:20px;font-size:18px;text-align:right;' disabled>&nbsp;<b>KG</B><br>
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
	 <tr><td align=center  valign='bottom'>
	 <b><font size=4>NETTO :</font></b></td><td> <input type=text id=NETTO size=12 onkeypress='return false;' style='height:25px;font-size:20px;text-align:right;'  disabled>
     </td></tr>
	 <tr><td align=center valign='bottom'>
	 <b>POTONGAN(KG) :</b></td><td> <input type=text id=POTONGAN size=12 onkeypress='return angka_doang(event);' value=0 onfocus='bersihkanField(this);' style='height:25px;font-size:20px;text-align:right;'>
     </td></tr>
     <tr><td align=center colspan=2>
     <input type=button id=SIMPAN class=tombol2 value=SIMPAN onclick=saveTbsInt(".$pecahtiket.",0,event);>&nbsp;&nbsp;
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
echo "<center><b>LIST KENDARAAN TBS INTERNAL YG BELUM TIMBANG KELUAR</b></center>";
 echo "<center><table border=1 cellspacing=0 width=70%></center>";
 echo "<tr>
        <td align=center width=10%><b>No. Tiket</b></td>
        <td align=center width=10%><b>No. KEND</b></td>
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
$str4="select *,count(TICKETNO) as tiket from ".$dbname.".mstrxtbs where PRODUCTCODE='40000003' and ".$dbname.".mstrxtbs.IDWB='".$IDWB."' and UNITCODE not like '' group by TICKETNO having tiket=1";
//echo $str4;
$res4=mysql_query($str4);
while($bar4=mysql_fetch_object($res4)){
	$str5="select * from ".$dbname.".msvendortrp where ".$dbname.".msvendortrp.TRPCODE='".$bar4->TRPCODE."'";
	$res5=mysql_query($str5);
	$bar5=mysql_fetch_object($res5);
	$spbno=$bar4->SPBNO;
	$tx=explode("/",$spbno);
	//$retval=substr($spbno,0,3);
	$retval=$tx[0];
  echo"<tr class=content onmouseover=\"this.style.backgroundColor='#00FF00';\" onmouseout=\"this.style.backgroundColor='#FFFFFF';\" style='cursor:pointer;' title='Click untuk memilih'
  onclick=\"load('".$bar4->TICKETNO."','".$retval."','".$bar4->VEHNOCODE."','".$bar4->TRPCODE."','".$bar4->DRIVER."','".$bar4->JMLHJJG."','".$bar4->TAHUNTANAM."','".$bar4->BRONDOLAN."','".$bar4->BERATKIRIM."','".tanggalnormal($bar4->DATEIN)."','".$bar4->WEI1ST."','".$bar4->UNITCODE."','".$bar4->DIVCODE."','".$bar5->TRPNAME."');\">
  <td>".$bar4->TICKETNO2."</td>
  <td>".$bar4->VEHNOCODE."</td>
  <td>".tanggalnormal($bar4->DATEIN)."</td>
  <td>".$bar4->SPBNO."</td>
  <td>".$bar4->WEI1ST."</td>
  <td>".$bar4->USERID."</td>
  </tr>";

 }
echo"</tbody>
     </table>
     </div>
	 </span>";
CLOSE_BOX();
echo close_body();
?>
