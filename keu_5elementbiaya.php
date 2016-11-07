<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?><FORM NAME = "Daftar Perkiraan">
<p align="center"><u><b><font face="Verdana" size="4" color="#000080">Element Biaya</font></b></u></p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="94%" id="AutoNumber1" height="111">
  <tr>
    <td width="15%" height="1">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial">Kode </font></td>
    <td width="63%" height="1">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys"> 
    <input type=text size="10" name="kodeorg">&nbsp; </font>
    </td>
    <td width="16%" height="1">
    <p style="margin-top: 0; margin-bottom: 0">
    </td>
  </tr>
  <tr>
    <td width="15%" height="22">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial">Keterangan </font></td>
    <td width="63%" height="22">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys"> 
    <input type=text size="51" name="keterangan"></font></td>
    <td width="16%" height="22">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
  </tr>
  <tr>
    <td width="15%" height="22">&nbsp;</td>
    <td width="63%" height="22">&nbsp;</td>
    <td width="16%" height="22">&nbsp;</td>
  </tr>
  </table>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="Simpan" name="Simpan">
<input type="reset" value="Batal" name="Batal"></font></p>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber2"><tr><td width="16%" align="center">Kode </td><td width="16%" align="center">Keterangan</td></tr><tr><td width="16%">&nbsp;</td><td width="16%">&nbsp;</td>
</tr></table>
<p><font face="Fixedsys">&nbsp;&nbsp;&nbsp; &nbsp;</font></p>

<?php
CLOSE_BOX();
echo close_body();
?>