<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/setup_matauang.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>

<p align="left"><u><b><font face="Arial" size="3" color="#000080">Mata Uang</font></b></u></p>
<?php
# Define Field
$field = array('kode','matauang','simbol','kodeiso');

# Create Table
$header = array();
foreach($field as $row) {
  $header[] = $_SESSION['lang'][$row];
}
$header[] = "Z";

# Get Data
$query = selectQuery($dbname,'setup_matauang',$field);
$data = fetchData($query);

#=========== Reform Content from Data =================
$content = array();

# Editable Row
$j=0;
if($data!=array()) {
  foreach($data as $i=>$row) {
    foreach($row as $key=>$data) {
      $content[$i][$key] = makeElement($key."_".$i,'txt',$data,array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)'));
    }
    $content[$i]['Z'] = "<img id='edit_".$i."' title='Edit' class=zImgBtn onclick=\"editMain('".$i."','kode','".$row['kode']."')\" src='images/001_45.png'/>";
    $content[$i]['Z'] .= "&nbsp;<img id='delete_".$i."' title='Hapus' class=zImgBtn onclick=\"deleteMain('".$i."','kode','".$row['kode']."')\" src='images/delete_32.png'/>";
    $content[$i]['Z'] .= "&nbsp;<img id='pass_".$i."' title='Lihat Detail' class=zImgBtn onclick=\"pass2detail('".$i."')\" src='images/nxbtn.png'/>";
    $j = $i+1;
  }
}

# New Row
foreach($field as $row) {
  $content[$j][$row] = makeElement($row."_".$j,'txt','',array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)'));
}
$content[$j]['Z'] = "<img id='add_".$j."' title='Tambah' class=zImgBtn onclick=\"addMain('".$j."')\" src='images/plus.png'/>";
$content[$j]['Z'] .= "&nbsp;<img id='delete_".$j."' />";
$content[$j]['Z'] .= "&nbsp;<img id='pass_".$j."' />";

#============= Generate Main Table =======================
$mainTable = makeTable('matauangMainTable','mainBody',$header,$content);
echo "<div id='mainTable' style='float:left;margin-right:100px;'>";
echo "<fieldset><legend><b>Header Mata Uang</b></legend>";
echo $mainTable;
echo "</fieldset></div>";
#============= Container for Detail Table ================
echo "<fieldset><legend><b>Detail Mata Uang</b></legend>";
echo "<div id='detailTable'>";
echo "</div></fieldset>";
?>
<!--FORM NAME = "Pinjaman">
<p align="left"><u><b><font face="Arial" size="5" color="#000080">Mata Uang</font></b></u></p>
<table id="Table" border="1" width="352">
  <tr>
    <td width="72" bgcolor="#C0C0C0"><font face="Fixedsys">Kode</font></td>
    <td width="72" bgcolor="#C0C0C0"><font face="Fixedsys">Mata Uang</font></td>
    <td width="106" bgcolor="#C0C0C0"><font face="Fixedsys">Symbol</font></td>
    <td width="106" bgcolor="#C0C0C0"><font face="Fixedsys">Kode Iso</font></td>
   </tr>
  <tr>
    <td width="72"><font face="Fixedsys">
    <input type=text size="9" name="kode"></font></td>
    <td width="72"><font face="Fixedsys">
    <input type=text size="27" name="matauang"></font></td>
    <td width="106"><font face="Fixedsys">
    <input type=text size="10" name="simbol"></font></td>
    <td width="106"><font face="Fixedsys">
    <input type=text size="10" name="kodeiso"></font></td>
  </tr>
</table>
<p>&nbsp;</p>

<table id="Table" border="1" width="352">
  <tr>
    <td width="72" bgcolor="#C0C0C0"><font face="Fixedsys">Tanggal</font></td>
    <td width="72" bgcolor="#C0C0C0"><font face="Fixedsys">Sampai Tanggal</font></td>
    <td width="106" bgcolor="#C0C0C0"><font face="Fixedsys">Kurs</font></td>
   </tr>
  <tr>
    <td width="72"><font face="Fixedsys">
    <input type=text size="9" name="tanggal"></font></td>
    <td width="72"><font face="Fixedsys">
    <input type=text size="10" name="sampaitanggal"></font></td>
    <td width="106"><font face="Fixedsys">
    <input type=text size="20" name="kurs"></font></td>
  </tr>
</table>
<p>&nbsp;</p>
<p><font face="Fixedsys"><input type="button" value="Simpan" name="ModifDtl">&nbsp;
<input type="button" value="   Batal   " name="DeleteDtl"></font></p>
<p><font face="Fixedsys">&nbsp;&nbsp; &nbsp;</font></p-->
<?php
CLOSE_BOX();
echo close_body();
?>