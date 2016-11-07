<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<FORM NAME = "ProsesPenggajian">
<p align="left"><b><font face="Arial" color="#000080" size="5">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </font>
<font face="Arial" color="#000080" size="4">
<u>PROSES PENGGAJIAN</u></font></b></p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="396" id="AutoNumber1" height="82">
  <tr>
    <td width="135" height="26">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial" style="font-size: 11pt">Kode Org</font></td>
    <td width="261" height="26">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial" style="font-size: 11pt"> 
    <input type=text size="9" name="PeriodeGaji"></font><span style="font-size: 11pt"><font face="Arial">
    <input type="button" value="Cek" name="Cek1"> </font></span></td>
  </tr>
  <tr>
    <td width="135" height="26">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial" style="font-size: 11pt">Periode Gaji &nbsp; </font></td>
    <td width="261" height="26">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial" style="font-size: 11pt"> 
    <input type=text size="9" name="PeriodeGaji"></font><span style="font-size: 11pt"><font face="Arial">
    <input type="button" value="Cek" name="Cek1"></font></span></td>
  </tr>
  <tr>
    <td width="135" height="25">
    <font face="Arial" style="font-size: 11pt">Tanggal</font></td>
    <td width="261" height="25">
    <font face="Times New Roman" style="font-size: 11pt"> 
    $!Dari</font><font face="Arial" style="font-size: 11pt">&nbsp; <b>&nbsp;s/d&nbsp;
    </b></font>
    <font face="Times New Roman" style="font-size: 11pt"> 
    $!Sampai </font></td>
  </tr>
  <tr>
    <td width="135" height="31">
    <font face="Arial" style="font-size: 11pt">Hari Kerja</font></td>
    <td width="261" height="31">
    $!HariKerja</td>
  </tr>
  </table>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<p style="margin-top: 0; margin-bottom: 0"><span style="font-size: 11pt">
<font face="Arial"><input type="button" value="Proses" name="ModifDtl">&nbsp; </font>
<font face="Arial">
<input type="button" value="   Batal   " name="DeleteDtl"></font></span></p>
<p style="margin-top: 0; margin-bottom: 0">
<font face="Arial" style="font-size: 11pt">&nbsp; 
&nbsp;</font></p>
<?php
CLOSE_BOX();
echo close_body();
?>