<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<FORM NAME = "Areal Statment">
<p align="center" style="margin-top: 0; margin-bottom: 0"><u><b><font face="Verdana" size="4" color="#000080">Areal Statmen</font></b></u></p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="86%" id="AutoNumber1">
  <tr>
    <td width="27%">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial" style="font-size: 11pt">No Transaksi</font></td>
    <td width="56%">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana"> 
    <input type=text size="15" name="nomororder"></font></td>
    <td width="10%">
    <p style="margin-top: 0; margin-bottom: 0" align="right">
    <font face="Verdana">Proses :</font></td>
    <td width="2%">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana"> <input type="checkbox" name="Posting" value="ON"></font></td>
  </tr>
  <tr>
    <td width="27%">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial" style="font-size: 11pt">Tanggal</font></td>
    <td width="56%">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana"> 
    <input type=text size="11" name="tanggalorder"></font></td>
    <td width="10%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="2%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
  </tr>
  <tr>
    <td width="27%">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial" style="font-size: 11pt">Kode Blok</font></td>
    <td width="56%">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana"> 
    <input type=text size="11" name="koderekanan">
<input type="button" value="Cek" name="Cek"></font></td>
    <td width="10%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="2%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
  </tr>
  <tr>
    <td width="10%">
    <font face="Arial" style="font-size: 11pt">Status</font></td>
    <td width="2%">
    <select size="1" name="syaratbayar">
    <option selected>Tambah</option>
    <option>Kurang</option>
    </select> </td>
  </tr>
  <tr>
    <td width="27%">
    <font face="Arial" style="font-size: 11pt">Jumlah&nbsp; Ha</font></td>
    <td width="56%">
    <font face="Verdana">
    <input type=text size="11" name="waktupenyerahan"> Ha</font></td>
    <td width="10%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="27%">
    <font face="Arial" style="font-size: 11pt">Jumlah&nbsp; Pokok</font></td>
    <td width="56%">
    <font face="Verdana">
    <input type=text size="11" name="waktupenyerahan1"> Pkk</font></td>
    <td width="10%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="27%">
    <font face="Verdana" style="font-size: 11pt">Keterangan</font></td>
    <td width="56%">
    <font face="Verdana">
    <input type=text size="54" name="penjual"></font></td>
    <td width="10%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="27%">
    &nbsp;</td>
    <td width="56%">
    &nbsp;</td>
    <td width="10%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
</table>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<p style="margin-top: 0; margin-bottom: 0"><font face="Verdana"><input type="button" value="Simpan" name="ModifDtl">&nbsp;
<input type="button" value="   Batal   " name="DeleteDtl"></font></p>
<p style="margin-top: 0; margin-bottom: 0"><font face="Verdana">&nbsp;&nbsp; &nbsp;</font></p>
<?php
CLOSE_BOX();
echo close_body();
?>