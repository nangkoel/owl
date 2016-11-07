<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<FORM NAME = "Pinjaman">
<p align="left"><u><b><font face="Arial" size="5" color="#000080">Konsolidasi </font></b></u></p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="67%" id="AutoNumber1" height="116">
  <tr>
    <td width="25%" height="1"><b><font face="Verdana" size="2">Ke Organisasi</font></b></td>
    <td width="56%" height="1"><select size="1" name="kodeorg"></select></td>
    <td width="1%" height="1"><font face="Fixedsys">&nbsp;</font></td>
    <td width="37%" height="1"></td>
  </tr>
  <tr>
    <td width="25%" height="22"><b><font face="Verdana" size="2">Dari Organisasi</font></b></td>
    <td width="56%" height="22"><select size="1" name="D1"></select></td>
    <td width="1%" height="22">&nbsp;</td>
    <td width="37%" height="22">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" height="22"><b><font face="Verdana" size="2">Ke Kode Perkiraan</font></b></td>
    <td width="56%" height="22"><font face="Fixedsys"> <input type=text size="11" name="tanggaldari"> $Nama Perkiraan</font></td>
    <td width="8%" height="22">&nbsp;</td>
    <td width="54%" height="22">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" height="22"><b><font face="Verdana" size="2">Dari Kode Perkiraan</font></b></td>
    <td width="56%" height="22"><font face="Fixedsys"> <input type=text size="11" name="tanggalsampai"> $Nama Perkiraan</font></td>
    <td width="1%" height="22">&nbsp;</td>
    <td width="37%" height="22">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" height="19">&nbsp;</td>
    <td width="56%" height="19">&nbsp;</td>
    <td width="1%" height="19">&nbsp;</td>
    <td width="37%" height="19">&nbsp;<p>&nbsp;</td>
  </tr>
</table>
<table id="Table" border="1" width="621">
  <tr>
    <td width="72" bgcolor="#C0C0C0"><font face="Fixedsys">KodeOrg</font></td>
    <td width="72" bgcolor="#C0C0C0"><font face="Fixedsys">Kode Perkiraan</font></td>
    <td width="375" bgcolor="#C0C0C0"><font face="Fixedsys">Nama Perkiraan</font></td>
    <td width="379" bgcolor="#C0C0C0"><font face="Fixedsys">Kode Org</font></td>
    <td width="72" bgcolor="#C0C0C0"><font face="Fixedsys">Kode Perkiraan</font></td>
    <td width="232" bgcolor="#C0C0C0"><font face="Fixedsys">Nama Perkiraan</font></td>
   </tr>
  <tr>
    <td width="72"><font face="Fixedsys">
    <input type=text size="9" name="kode"></font></td>
    <td width="72"><font face="Fixedsys">
    <input type=text size="10" name="kode"></font></td>
    <td width="375"><font face="Fixedsys">
    <input type=text size="30" name="kegiatan"></font></td>
    <td width="379"><font face="Fixedsys">
    <!--webbot bot="Validation" s-data-type="Number" s-number-separators=".," --><input type=text size="10" name="kelompok"></font></td>
    <td width="72"><font face="Fixedsys">
    <input type=text size="13" name="kode"></font></td>
    <td width="232"><p align="center"><font face="Fixedsys">
    <!--webbot bot="Validation" s-data-type="Number" s-number-separators=".," --><input type=text size="29" name="kelompok1"></font></td>
  </tr>
</table>
<p><font face="Fixedsys"><input type="button" value="Simpan" name="ModifDtl">&nbsp;
<input type="button" value="   Batal   " name="DeleteDtl"></font></p>
<p><font face="Fixedsys">&nbsp;&nbsp; &nbsp;</font></p>
<?php
CLOSE_BOX();
echo close_body();
?>