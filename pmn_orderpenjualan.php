<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<FORM NAME = "Kontrak Penjualan">
<p align="center" style="margin-top: 0; margin-bottom: 0"><u><b>
<font face="Verdana" size="4" color="#000080">Order 
Penjualan</font></b></u></p>
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
    <font face="Arial" style="font-size: 11pt">Kode Rekanan</font></td>
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
    <td width="27%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="56%">
    <p style="margin-top: 0; margin-bottom: 0">$NamaRekanan</td>
    <td width="10%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="2%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
  </tr>
  <tr>
    <td width="27%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="56%">
    <p style="margin-top: 0; margin-bottom: 0">$AlamatRekanan</td>
    <td width="10%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="2%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
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
  <tr>
    <td width="27%">
    <font face="Arial" style="font-size: 11pt">Syarat Penyerahan</font></td>
    <td width="56%">
    <font face="Verdana">
    <input type=text size="54" name="syaratpenyerahan"></font></td>
    <td width="10%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="27%">
    <font face="Arial" style="font-size: 11pt">Waktu Penyerahan</font></td>
    <td width="56%">
    <font face="Verdana">
    <input type=text size="54" name="waktupenyerahan"></font></td>
    <td width="10%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="27%">
    <font face="Arial" style="font-size: 11pt">Syarat Pembayaran</font></td>
    <td width="56%">
    <select size="1" name="syaratbayar">
    <option selected>Tunai</option>
    <option>Kredit</option>
    </select></td>
    <td width="10%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="27%">
    <font face="Verdana" style="font-size: 11pt">Persetujuan Penjual Oleh</font></td>
    <td width="56%">
    <font face="Verdana">
    <input type=text size="37" name="penjual"></font></td>
    <td width="10%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="27%">
    <font face="Verdana" style="font-size: 11pt">Persetujuan Pembeli Oleh</font></td>
    <td width="56%">
    <font face="Verdana">
    <input type=text size="37" name="pembeli"></font></td>
    <td width="10%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
</table>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<table id="Table" border="0" width="849" style="border-collapse: collapse" bordercolor="#000000" cellpadding="0" cellspacing="0">
  <tr>
    <td width="36" bordercolor="#000000" bgcolor="#999999">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana">&nbsp;#</font></b></td>
    <td width="160" bordercolor="#000000" bgcolor="#999999">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana">Kode Barang</font></b></td>
    <td width="288" bordercolor="#000000" bgcolor="#999999">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana">Nama Barang</font></b></td>
    <td width="64" bordercolor="#000000" bgcolor="#999999">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana">Satuan</font></b></td>
    <td width="106" bordercolor="#000000" bgcolor="#999999">
    <p align="right" style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana">Jumlah Pesan</font></b></td>
    <td width="93" bordercolor="#000000" bgcolor="#999999">
    <p align="right" style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana">Harga Satuan</font></b></td>
    <td width="102" bordercolor="#000000" bgcolor="#999999">
    <p align="right" style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana">Jumlah</font></b></td>
   </tr>
  <tr>
    <td width="36" bordercolor="#000000">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana">
    <input type=text size="4" name="nourut"></font></td>
    <td width="160" bordercolor="#000000">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana">
    <input type=text size="12" name="kodebarang">
<input type="button" value="Cek" name="Cek1"></font></td>
    <td width="288" bordercolor="#000000">
<p style="margin-top: 0; margin-bottom: 0">
$!NamaBarang</td>
    <td width="64" bordercolor="#000000">
    <p style="margin-top: 0; margin-bottom: 0">$!Satuan</td>
    <td width="106" bordercolor="#000000">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana">
    <!--webbot bot="Validation" s-data-type="Number" s-number-separators=".," --><input type=text size="14" name="jumlahpesan"></font></td>
    <td width="93" bordercolor="#000000">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana">
    <!--webbot bot="Validation" s-data-type="Number" s-number-separators=".," --><input type=text size="11" name="hargasatuan"></font></td>
    <td width="102" bordercolor="#000000">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana">
    <!--webbot bot="Validation" s-data-type="Number" s-number-separators=".," --><input type=text size="13" name="Jumlah_Rate2"></font></td>
  </tr>
  <tr>
    <td width="600" colspan="5" bordercolor="#FFFFFF">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="93">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="102">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
  </tr>
  <tr>
    <td width="600" colspan="5" bordercolor="#FFFFFF">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="93" height="22">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Verdana" style="font-size: 11pt">Sub Total</font></td>
    <td width="102">
    <p style="margin-top: 0; margin-bottom: 0">$SubTotal</td>
  </tr>
  <tr>
    <td width="600" colspan="5" bordercolor="#FFFFFF">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="93" height="22">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Verdana" style="font-size: 11pt">Uang Muka</font></td>
    <td width="102">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana">
    <input type=text size="13" name="uangmuka"></font></td>
  </tr>
  <tr>
    <td width="600" colspan="5" bordercolor="#FFFFFF">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="93" height="22">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Verdana" style="font-size: 11pt">PPn</font></td>
    <td width="102">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana">
    <input type=text size="13" name="ppn"></font></td>
  </tr>
  <tr>
    <td width="600" colspan="5" bordercolor="#FFFFFF">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="93" height="28">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Verdana" style="font-size: 11pt">Total</font></td>
    <td width="102">
    <p style="margin-top: 0; margin-bottom: 0">$Total</td>
  </tr>
</table>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<p style="margin-top: 0; margin-bottom: 0"><font face="Verdana"><input type="button" value="Simpan" name="ModifDtl">&nbsp;
<input type="button" value="   Batal   " name="DeleteDtl"></font></p>
<p style="margin-top: 0; margin-bottom: 0"><font face="Verdana">&nbsp;&nbsp; &nbsp;</font></p>
<?php
CLOSE_BOX();
echo close_body();
?>