<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<p align="left"><u><b><font face="Arial" size="5" color="#000080">Daftar 
Kegiatan</font></b></u></p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" height="80" width="608">
  <tr>
    <td width="174" height="22"><b><font face="Verdana" size="2">Kode Kegiatan</font></b></td>
    <td width="434" height="22"><font face="Fixedsys">
    <input type=text size="8" name="kodekegiatan"></font></td>
  </tr>
  <tr>
    <td width="174" height="19"><b><font face="Verdana" size="2">Keterangan 
    Kegiatan</font></b></td>
    <td width="434" height="19">
<input type="text" name="namakegiatan" size="58"></td>
  </tr>
  <tr>
    <td width="174" height="19"><b><font face="Verdana" size="2">Satuan Prestasi</font></b></td>
    <td width="434" height="19">
    <select size="1" name="satuanprestasi">
    <option selected>Ha</option>
    <option>Km</option>
    <option>Jam</option>
    <option>Rit</option>
    </select></td>
  </tr>
  <tr>
    <td width="174" height="19"><b><font face="Verdana" size="2">Untuk Status</font></b></td>
    <td width="434" height="19">
    <select size="1" name="untukstatus">
    <option selected>TM</option>
    <option>TBM</option>
    </select></td>
  </tr>
  <tr>
    <td width="174" height="18"></td>
    <td width="434" height="18">
    </td>
  </tr>
  </table>
  <p><font face="Arial Black" color="#000080">Norma Anggaran</font></p>
  <table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber3" height="57">
    <tr>
      <td width="9%" height="37" bgcolor="#808080"><b>
      <font face="Verdana" size="2">Type</font></b></td>
      <td width="14%" height="37" bgcolor="#808080"><b>
      <font face="Verdana" size="2">Kode</font></b></td>
      <td width="42%" height="37" bgcolor="#808080"><b>
      <font face="Verdana" size="2">Keterangan</font></b></td>
      <td width="17%" height="37" bgcolor="#808080" align="center"><b>
      <font face="Verdana" size="2">Kuantitas</font></b></td>
      <td width="35%" height="37" bgcolor="#808080" align="center"><b>
      <font face="Verdana" size="2">Satuan</font></b></td>
      <td width="35%" height="37" bgcolor="#808080" align="center"><b>
      <font face="Verdana" size="2">Per</font></b></td>
      <td width="33%" height="37" bgcolor="#808080" align="center"><b>
      <font face="Verdana" size="2">Kuantitas</font></b></td>
      <td width="25%" height="37" bgcolor="#808080" align="center"><b>
      <font face="Verdana" size="2">Satuan Prestasi</font></b></td>
    </tr>
    <tr>
      <td width="9%" height="19"><select size="1" name="type">
      <option selected>Material</option>
      <option>SDM</option>
      <option>Unit</option>
      <option>Hasil</option>
      </select></td>
      <td width="14%" height="19"><font face="Fixedsys">
    <input type=text size="8" name="kodebarang"></font><input type="reset" value="Cek" name="Cek"></td>
      <td width="42%" height="19">&nbsp;</td>
      <td width="17%" height="19">
      <p align="center"><font face="Fixedsys">
    <input type=text size="8" name="jumlah"></font></td>
      <td width="35%" height="19">
      <p align="center">$!Satuan</td>
      <td width="35%" height="19">&nbsp;/</td>
      <td width="33%" height="19"><font face="Fixedsys">
    <input type=text size="8" name="perjumlah"></font></td>
      <td width="25%" height="19">$!Satuan</td>
    </tr>
  </table>
  <p><input type="submit" value="Simpan" name="B1">&nbsp;
  <input type="reset" value="Batal" name="B2"></p>
</form>
</FORM>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
CLOSE_BOX();
echo close_body();
?>