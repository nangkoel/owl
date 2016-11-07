<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<FORM NAME = "Browser Based - Ledger_Jurnal">
<p align="center" style="margin-top: 0; margin-bottom: 0"><u><b><font face="Verdana" size="5" color="#000080">Permintaan 
Pembelian</font></b></u></p>
<p align="center" style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="83%" id="AutoNumber1">
  <tr>
    <td width="17%">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Verdana" style="font-size: 11pt">No PP</font></td>
    <td width="52%">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Verdana" style="font-size: 11pt"> 
    <input type=text size="19" name="nopp"></font></td>
    <td width="19%">
    <font face="Verdana">
<span style="font-size: 11pt">
    <input type="button" value="Cetak" name="Cetak" style="float: right"></span></font></td>
    <td width="2%">
    <p style="margin-top: 0; margin-bottom: 0">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="17%">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Verdana" style="font-size: 11pt">Tanggal</font></td>
    <td width="52%">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Verdana" style="font-size: 11pt"> 
    <input type=text size="11" name="tanggalpp"></font></td>
    <td width="19%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="2%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
  </tr>
  <tr>
    <td width="17%">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Verdana" style="font-size: 11pt">Dari Bagian</font></td>
    <td width="52%" valign="middle">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana"> 
    <span style="font-size: 11pt"> 
    <input type=text size="6" name="darikodeeu"></span><font style="font-size: 11pt">
    </font><span style="font-size: 11pt">
    <input type="button" value="Cek" name="Cek1"></span><font style="font-size: 11pt">&nbsp;
    </font></font><font style="font-size: 11pt"><font face="Times New Roman"> 
    $!NamaUnitUsaha</font></font></td>
    <td width="19%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="2%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
  </tr>
  <tr>
    <td width="17%">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Verdana">
<span style="font-size: 11pt"><input type="button" value="Detail" name="Detail"></span></font></td>
    <td width="52%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="19%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="2%">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
  </tr>
  <tr>
    <td width="17%">
    &nbsp;</td>
    <td width="52%">
    &nbsp;</td>
    <td width="19%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="17%">
    &nbsp;</td>
    <td width="52%">
    &nbsp;</td>
    <td width="19%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="17%">
    <font face="Verdana" style="font-size: 11pt">Kode Barang</font></td>
    <td width="52%">
    <font face="Verdana">
    <span style="font-size: 11pt">
    <input type=text size="12" name="kodebarang1"><input type="button" value="Cek" name="Cek2"></span></font>$!NamaBarang</td>
    <td width="19%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="17%">
    <font face="Verdana" style="font-size: 11pt">Satuan</font></td>
    <td width="52%">
    $!Satuan</td>
    <td width="19%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="17%">
    <font face="Verdana" style="font-size: 11pt">Jumlah Diminta</font></td>
    <td width="52%">
    <font face="Verdana" style="font-size: 11pt">
    <!--webbot bot="Validation" s-data-type="Number" s-number-separators=".," --><input type=text size="14" name="jumlahpesan1"></font></td>
    <td width="19%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="17%">
    <font face="Verdana" style="font-size: 11pt">Tanggal SDT</font></td>
    <td width="52%">
    <font face="Verdana" style="font-size: 11pt">
    <!--webbot bot="Validation" s-data-type="Number" s-number-separators=".," --><input type=text size="11" name="tanggalbutuh1"></font></td>
    <td width="19%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  <tr>
    <td width="17%">
    <font face="Verdana" style="font-size: 11pt">Kode Anggaran</font></td>
    <td width="52%">
    <font face="Verdana" style="font-size: 11pt">
    <!--webbot bot="Validation" s-data-type="Number" s-number-separators=".," --><input type=text size="11" name="kodeanggaran1"></font></td>
    <td width="19%">
    &nbsp;</td>
    <td width="2%">
    &nbsp;</td>
  </tr>
  </table>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<p style="margin-top: 0; margin-bottom: 0"><font face="Verdana">
<span style="font-size: 11pt"><input type="button" value="Tambah" name="Tambah"> </span></font></p>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<table id="Table" border="0" width="1045" style="border-collapse: collapse" bordercolor="#000000" cellpadding="0" cellspacing="0" height="46">
  <tr>
    <td width="43" bgcolor="#008080" height="26" style="border-style: solid; border-width: 1">
    <p style="margin-top: 0; margin-bottom: 0"><b>
    <font face="Verdana" style="font-size: 11pt">&nbsp;#</font></b></td>
    <td width="137" bgcolor="#008080" height="26" style="border-style: solid; border-width: 1">
    <p style="margin-top: 0; margin-bottom: 0"><b>
    <font face="Verdana" style="font-size: 11pt">Kode Barang</font></b></td>
    <td width="224" bgcolor="#008080" height="26" style="border-style: solid; border-width: 1">
    <p style="margin-top: 0; margin-bottom: 0"><b>
    <font face="Verdana" style="font-size: 11pt">Nama Barang</font></b></td>
    <td width="201" bgcolor="#008080" height="26" style="border-style: solid; border-width: 1">
    <p style="margin-top: 0; margin-bottom: 0"><b>
    <font face="Verdana" style="font-size: 11pt">Satuan</font></b></td>
    <td width="140" bgcolor="#008080" height="26" style="border-style: solid; border-width: 1">
    <p align="right" style="margin-top: 0; margin-bottom: 0"><b>
    <font face="Verdana" style="font-size: 11pt">Jumlah Diminta</font></b></td>
    <td width="122" bgcolor="#008080" height="26" style="border-style: solid; border-width: 1">
    <p align="right" style="margin-top: 0; margin-bottom: 0"><b>
    <font face="Verdana" style="font-size: 11pt">Tanggal SDT</font></b></td>
    <td width="198" bgcolor="#008080" height="26" style="border-style: solid; border-width: 1">
    <p align="left" style="margin-top: 0; margin-bottom: 0"><b>
    <font face="Verdana" style="font-size: 11pt">&nbsp;Kode Anggaran</font></b></td>
    <td width="41" bgcolor="#008080" height="26" style="border-style: solid; border-width: 1">
    <p align="center"><b><font face="Verdana" style="font-size: 11pt">Hapus</font></b></td>
   </tr>
  <tr>
    <td width="43" height="20" style="border-style: solid; border-width: 1">
    <p style="margin-top: 0; margin-bottom: 0">
    $!No</td>
    <td width="137" height="20" style="border-style: solid; border-width: 1">
    <p style="margin-top: 0; margin-bottom: 0"><font color="#0000FF"><i><u>$!KodeBarang</u></i></font></td>
    <td width="224" height="20" style="border-style: solid; border-width: 1">
<p style="margin-top: 0; margin-bottom: 0">
$!NamaBarang</td>
    <td width="201" height="20" style="border-style: solid; border-width: 1">
    <p style="margin-top: 0; margin-bottom: 0">
    $!Satuan</td>
    <td width="140" height="20" style="border-style: solid; border-width: 1">
    <p style="margin-top: 0; margin-bottom: 0">
    $!JumlahDiminta</td>
    <td width="122" height="20" style="border-style: solid; border-width: 1">
    <p style="margin-top: 0; margin-bottom: 0">
    $!TanggalSDT</td>
    <td width="198" height="20" style="border-style: solid; border-width: 1">
    <p style="margin-top: 0; margin-bottom: 0">
    &nbsp;$!KodeAnggaran</td>
    <td width="41" height="20" style="border-style: solid; border-width: 1">
    <p align="center"><font face="Fixedsys">
<input type="checkbox" name="cek" value="ON"></font></td>
  </tr>
</table>
<p style="margin-top: 0; margin-bottom: 0">
&nbsp;</p>
<p style="margin-top: 0; margin-bottom: 0"><font face="Verdana">
<span style="font-size: 11pt">
<input type="button" value="Proses" name="Proses">
<input type="submit" value="Simpan" name="Simpan">
<input type="reset" value="Batal" name="Batal"></span></font></p>
<?php
CLOSE_BOX();
echo close_body();
?>