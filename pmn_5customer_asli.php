<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?><FORM NAME = "Supplier">
<p align="left"><b><font face="Arial" size="5" color="#000080">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<u>Daftar Rekanan</u></font></b></p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" height="80" width="713">
  <tr>
    <td width="138" height="22">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Kode Rekanan</font></b></td>
    <td width="575" height="22">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys">
    <input type=text size="8" name="koderekanan"></font></td>
  </tr>
  <tr>
    <td width="138" height="19">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Nama Rekanan</font></b></td>
    <td width="575" height="19">
<p style="margin-top: 0; margin-bottom: 0">
<input type="text" name="namarekanan" size="42"></td>
  </tr>
  <tr>
    <td width="138" height="19">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Alamat</font></b></td>
    <td width="575" height="19">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Fixedsys">
    <input type=text size="80" name="alamat"></font></td>
  </tr>
  <tr>
    <td width="138" height="18">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Kota</font></b></td>
    <td width="575" height="18">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Fixedsys">
    <input type=text size="19" name="kota"></font></td>
  </tr>
  <tr>
    <td width="138" height="19">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Telepon</font></b></td>
    <td width="575" height="19">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Fixedsys">
    <input type=text size="24" name="telepon"></font></td>
  </tr>
  <tr>
    <td width="138" height="17">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Hubungan</font></b></td>
    <td width="575" height="17">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Fixedsys">
    <input type=text size="24" name="contakperson"></font></td>
  </tr>
  <tr>
    <td width="138" height="19">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">
    Plafon</font></b></td>
    <td width="575" height="17">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Fixedsys">
    <input type=text size="24" name="plafon"></font></td>
  </tr>
  <tr>
    <td width="138" height="17">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">NPWP</font></b></td>
    <td width="575" height="17">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Fixedsys">
    <input type=text size="24" name="npwp"></font></td>
  </tr>
  <tr>
    <td width="138" height="17">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">No Seri Pajak</font></b></td>
    <td width="575" height="17">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Fixedsys">
    <input type=text size="24" name="noseripajak"></font></td>
  </tr>
  <tr>
    <td width="138" height="17">
    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Kategori </font>
    </b></td>
    <td width="575" height="17">
    <p style="margin-top: 0; margin-bottom: 0">
    <select size="1" name="typerekanan">
    <option selected>A</option>
    <option>B</option>
    <option>C</option>
    </select></td>
  </tr>
  <tr>
    <td width="138" height="17">
    </td>
    <td width="575" height="17">
    </td>
  </tr>
  <tr>
    <td width="138" height="17">
    </td>
    <td width="575" height="17">
    </td>
  </tr>
  </table>
  <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" value="Simpan" name="B1">&nbsp;
  <input type="reset" value="Batal" name="B2"></p>
</form>
<?php
CLOSE_BOX();
echo close_body();
?>