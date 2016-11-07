<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>

<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1257">
<meta name=ProgId content=FrontPage.Editor.Document>
<meta name=Generator content="Microsoft FrontPage 5.0">
<meta name=Originator content="Microsoft Word 11">
<link rel=Edit-Time-Data href="PengangkutanHslProduksi_files/editdata.mso">
</head>
<body bgcolor=white lang=EN-US link=blue vlink=blue style='tab-interval:.5in'>
<div class=Section1 style="width: 748; height: 383">
<form>
<p align=center style='text-align:left'><span class=SpellE><b><u><span
style='font-family:Tahoma;color:navy'><font size="4">Pengangkutan</font></span></u></b></span><b><u><span
style='font-family:Tahoma;color:navy'><font size="4"> <span class=SpellE>Hasil</span> </font>
<span class=SpellE><font size="4">Produksi</font></span></span></u></b><o:p></o:p></p>

<table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0 width=741
 style='width:555.75pt;border-collapse:collapse;mso-padding-alt:0in 0in 0in 0in'
 id=AutoNumber1 height=163>
 <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes;height:16.5pt'>
  <td width=160 style='width:120.0pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><b><span style='font-family:Tahoma'><font size="2">No </font> <span class=SpellE><font size="2">Kontrak</font></span></span></b></p>
  </td>
  <td width=581 style='width:435.75pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span style='font-family:Tahoma'><INPUT TYPE="text" SIZE="11" NAME="notransaksi"></span></p>
  </td>
 </tr>
 <tr style='mso-yfti-irow:1;height:16.5pt'>
  <td width=160 style='width:120.0pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span class=SpellE><b><span
  style='font-family:Tahoma'><font size="2">Tanggal</font></span></b></span></p>
  </td>
  <td width=581 style='width:435.75pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span style='font-family:Tahoma'><INPUT TYPE="text" SIZE="11" NAME="tanggal"></span></p>
  </td>
 </tr>
 <tr style='mso-yfti-irow:2;height:16.5pt'>
  <td width=160 style='width:120.0pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span class=SpellE><b><span
  style='font-family:Tahoma'><font size="2">Komoditi</font></span></b></span></p>
  </td>
  <td width=581 style='width:435.75pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><font face="Tahoma"><SELECT NAME="shift">
<OPTION SELECTED VALUE="MS">MS
<OPTION VALUE="IS">IS
<OPTION VALUE="CPO">CPO
</SELECT></font><b><font face="Tahoma" size="2">&nbsp; </font>
  </b></p>
  </td>
 </tr>
 <tr style='mso-yfti-irow:3;height:16.5pt'>
  <td width=160 style='width:120.0pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span class=SpellE><b><span
  style='font-family:Tahoma'><font size="2">Rekanan</font></span></b></span></p>
  </td>
  <td width=581 style='width:435.75pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><font face="Tahoma"><SELECT NAME="shift">
</SELECT><font size="2">&nbsp; </font></font></p>
  </td>
 </tr>
 <font face="Tahoma">
 <tr style='mso-yfti-irow:4;height:16.5pt'>
  <td width=160 style='width:120.0pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span class=SpellE><b><span
  style='font-family:Tahoma'><font size="2">Keterangan</font></span></b></span></p>
  </td>
  <td width=581 style='width:435.75pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><font face="Tahoma"><INPUT TYPE="text" SIZE="84" NAME="Tanggal_Entry1"><font size="2">&nbsp; </font></font></p>
  </td>
 </tr>
 <font face="Tahoma">
 <tr style='mso-yfti-irow:5;height:16.5pt'>
  <td width=160 style='width:120.0pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'>&nbsp;</p>
  </td>
  <td width=581 style='width:435.75pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span style='font-family:Tahoma'><font size="2">&nbsp;</font></span></p>
  </td>
 </tr>
 <tr style='mso-yfti-irow:6;height:16.5pt'>
  <td width=160 style='width:120.0pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span class=SpellE><b><span
  style='font-family:Tahoma'><font size="2">Jumlah</font></span></b></span><b><span
  style='font-family:Tahoma'><font size="2"> </font> <span class=SpellE><font size="2">Kontrak</font></span></span></b></p>
  </td>
  <td width=581 style='width:435.75pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><font face="Tahoma"><INPUT TYPE="text" SIZE="9" NAME="Tanggal_Entry"><font size="2">&nbsp;&nbsp;&nbsp;<span
  class=SpellE><b>Pengiriman</b></span> <b>:</b> </font></font><span style='font-family:Verdana'><font face="Tahoma"> <INPUT TYPE="text" SIZE="9" NAME="Tanggal_Entry16"><font size="2">&nbsp;
  <b>&nbsp;<span class=SpellE>Sisa</span> :</b> </font> <INPUT TYPE="text" NAME="Tanggal_Entry17" size="20"></font></span></p>
  </td>
 </tr>
 <tr style='mso-yfti-irow:7;mso-yfti-lastrow:yes;height:16.5pt'>
  <td width=160 style='width:120.0pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'>&nbsp;</p>
  </td>
  <td width=581 style='width:435.75pt;padding:0in 0in 0in 0in;height:16.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span style='font-family:Tahoma'><font size="2">&nbsp;&nbsp;</font></span></p>
  </td>
 </tr>
</table>

<p style='margin:0in;margin-bottom:.0001pt'>&nbsp;</p>

<p style='margin:0in;margin-bottom:.0001pt'><u><font size="2" color="#000080"><b><span style="font-family: Tahoma">Rincian Barang</span></b></font></u></p>

<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0 width=659
 style='width:494.6pt;border-collapse:collapse;border:none;mso-border-alt:outset #999999 .75pt;
 mso-padding-alt:0in 0in 0in 0in' id=Table>
 <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes;height:34.5pt'>
  <td width=131 style='width:98.6pt;border:inset #999999 1.0pt;mso-border-alt:
  inset #999999 .75pt;background:#00BBBB;padding:0in 0in 0in 0in;height:34.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><b><span style='font-family:Tahoma'><font size="2">No
  DO </font> </span></b></p>
  </td>
  <td width=96 style='width:1.0in;border:inset #999999 1.0pt;border-left:none;
  mso-border-left-alt:inset #999999 .75pt;mso-border-alt:inset #999999 .75pt;
  background:#00BBBB;padding:0in 0in 0in 0in;height:34.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span class=SpellE><b><span
  style='font-family:Tahoma'><font size="2">Tgl</font></span></b></span><b><span style='font-family:
  Tahoma'><font size="2">. DO</font></span></b></p>
  </td>
  <td width=96 style='width:1.0in;border:inset #999999 1.0pt;border-left:none;
  mso-border-left-alt:inset #999999 .75pt;mso-border-alt:inset #999999 .75pt;
  background:#00BBBB;padding:0in 0in 0in 0in;height:34.5pt'>
  <p align=center style='margin:0in;margin-bottom:.0001pt;text-align:center'><span
  class=SpellE><b><span style='font-family:Tahoma'><font size="2">Kode</font></span></b></span><b><span
  style='font-family:Tahoma'><font size="2"> </font> <span class=SpellE><font size="2">Expedisi</font></span></span></b></p>
  </td>
  <td width=96 style='width:1.0in;border:inset #999999 1.0pt;border-left:none;
  mso-border-left-alt:inset #999999 .75pt;mso-border-alt:inset #999999 .75pt;
  background:#00BBBB;padding:0in 0in 0in 0in;height:34.5pt'>
  <p align=center style='margin:0in;margin-bottom:.0001pt;text-align:center'><span
  class=SpellE><b><span style='font-family:Tahoma'><font size="2">Jumlah</font></span></b></span><b><span
  style='font-family:Tahoma'><font size="2"> DO</font></span></b></p>
  </td>
  <td width=108 style='width:81.0pt;border:inset #999999 1.0pt;border-left:
  none;mso-border-left-alt:inset #999999 .75pt;mso-border-alt:inset #999999 .75pt;
  background:#00BBBB;padding:0in 0in 0in 0in;height:34.5pt'>
  <p align=center style='margin:0in;margin-bottom:.0001pt;text-align:center'><span
  class=SpellE><b><span style='font-family:Tahoma'><font size="2">Jumlah</font></span></b></span><b><span
  style='font-family:Tahoma'><font size="2"> </font> <span class=SpellE><font size="2">Pengiriman</font></span></span></b></p>
  </td>
  <td width=132 style='width:99.0pt;border:inset #999999 1.0pt;border-left:
  none;mso-border-left-alt:inset #999999 .75pt;mso-border-alt:inset #999999 .75pt;
  background:#00BBBB;padding:0in 0in 0in 0in;height:34.5pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span class=SpellE><b><span
  style='font-family:Tahoma'><font size="2">Sisa</font></span></b></span><b><span style='font-family:
  Tahoma'><font size="2"> DO</font></span></b></p>
  </td>
 </tr>
 <tr style='mso-yfti-irow:1;mso-yfti-lastrow:yes;height:14.25pt'>
  <td width=131 style='width:98.6pt;border:inset #999999 1.0pt;border-top:none;
  mso-border-top-alt:inset #999999 .75pt;mso-border-alt:inset #999999 .75pt;
  padding:0in 0in 0in 0in;height:14.25pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span style='font-family:Tahoma'><INPUT TYPE="text" SIZE="9" NAME="stasiun"></span></p>
  </td>
  <font face="Tahoma">
  <td width=96 style='width:1.0in;border-top:none;border-left:none;border-bottom:
  inset #999999 1.0pt;border-right:inset #999999 1.0pt;mso-border-top-alt:inset #999999 .75pt;
  mso-border-left-alt:inset #999999 .75pt;mso-border-alt:inset #999999 .75pt;
  padding:0in 0in 0in 0in;height:14.25pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span style='font-family:Tahoma'><INPUT TYPE="text" SIZE="9" NAME="kodeunit"></span></p>
  </td>
  <td width=96 style='width:1.0in;border-top:none;border-left:none;border-bottom:
  inset #999999 1.0pt;border-right:inset #999999 1.0pt;mso-border-top-alt:inset #999999 .75pt;
  mso-border-left-alt:inset #999999 .75pt;mso-border-alt:inset #999999 .75pt;
  padding:0in 0in 0in 0in;height:14.25pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span style='font-family:Tahoma'><INPUT TYPE="text" SIZE="11" NAME="jammulai"></span></p>
  </td>
  <td width=96 style='width:1.0in;border-top:none;border-left:none;border-bottom:
  inset #999999 1.0pt;border-right:inset #999999 1.0pt;mso-border-top-alt:inset #999999 .75pt;
  mso-border-left-alt:inset #999999 .75pt;mso-border-alt:inset #999999 .75pt;
  padding:0in 0in 0in 0in;height:14.25pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span style='font-family:Tahoma'><INPUT TYPE="text" SIZE="11" NAME="jamselesai"></span></p>
  </td>
  <td width=108 style='width:81.0pt;border-top:none;border-left:none;
  border-bottom:inset #999999 1.0pt;border-right:inset #999999 1.0pt;
  mso-border-top-alt:inset #999999 .75pt;mso-border-left-alt:inset #999999 .75pt;
  mso-border-alt:inset #999999 .75pt;padding:0in 0in 0in 0in;height:14.25pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span style='font-family:Tahoma'><INPUT TYPE="text" NAME="stagnasi" size="20"></span></p>
  </td>
  <td width=132 style='width:99.0pt;border-top:none;border-left:none;
  border-bottom:inset #999999 1.0pt;border-right:inset #999999 1.0pt;
  mso-border-top-alt:inset #999999 .75pt;mso-border-left-alt:inset #999999 .75pt;
  mso-border-alt:inset #999999 .75pt;padding:0in 0in 0in 0in;height:14.25pt'>
  <p style='margin:0in;margin-bottom:.0001pt'><span style='font-family:Tahoma'><INPUT TYPE="text" SIZE="22" NAME="keterangan"></span></p>
  </td>
 </tr>
</table>

<p style='margin:0in;margin-bottom:.0001pt'><font face="Tahoma" size="2">&nbsp;</font><o:p></o:p></p>

<p>

<font face="Tahoma">

<input type=button value=Simpan name=ModifDtl><font size="2"> </font>

<input type=button value="   Batal   " name=DeleteDtl><font size="2">

&nbsp;</font></font></p>

</form>
</div>
</body>
</html>
<?php
CLOSE_BOX();
echo close_body();
?>