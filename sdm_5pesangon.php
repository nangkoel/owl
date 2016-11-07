<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/sdm_5pesangon.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php
#=======Form============
echo "<div id=form style='margin-bottom:30px;clear:both'>";
$els = array();
# Fields
$els[] = array(
  makeElement('rowNum','hidden').
  makeElement('masakerja','label',$_SESSION['lang']['masakerja']),
  makeElement('masakerja','textnum','',array('style'=>'width:100px','maxlength'=>'10'))
);
$els[] = array(
  makeElement('pesangon','label','Rp Pesangon'),
  makeElement('pesangon','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('penghargaan','label','Rp Penghargaan'),
  makeElement('penghargaan','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('pengganti','label','Rp Pengganti'),
  makeElement('pengganti','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('perusahaan','label','Rp Diberhentikan Perusahaan'),
  makeElement('perusahaan','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('kesalahanbiasa','label','Rp Kesalahan Biasa'),
  makeElement('kesalahanbiasa','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('kesalahanberat','label','Rp Kesalahan Berat / Mengundurkan diri'),
  makeElement('kesalahanberat','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('uangpisah','label','Rp Uang Pisah'),
  makeElement('uangpisah','textnum','',array('style'=>'width:100px'))
);

# Fields
$fieldStr = '##kodeorg##kodekelompok##keterangan##nokounter';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
	makeElement('addBtn','btn',$_SESSION['lang']['tambah'],array('onclick'=>'add()')).
	makeElement('editBtn','btn',$_SESSION['lang']['edit'],array('onclick'=>'edit()','style'=>'display:none')).
	makeElement('cancelBtn','btn',$_SESSION['lang']['cancel'],array('onclick'=>'cancel()'))
);

# Generate Field
echo genElTitle('Setup Pesangon',$els);
echo "</div>";
#=======End Form============

#=======Table===============
// Get Data
$cols = 'masakerja,pesangon,penghargaan,pengganti,perusahaan,kesalahanbiasa,kesalahanberat,uangpisah';
$query = selectQuery($dbname,'sdm_5pesangon',$cols);
$data = fetchData($query);

# Display Table
echo "<fieldset style='clear:both'>
	<legend><b>Table</b></legend>
	<table class='sortable'>";
echo "<tr class=rowheader>
		<td colspan=2>Aksi</td>
		<td>Masa Kerja</td>
		<td>Rp Pesangon</td>
		<td>Rp Penghargaan</td>
		<td>Rp Pengganti</td>
		<td>Rp Diberhentikan Perusahaan</td>
		<td>Rp Kesalahan Biasa</td>
		<td>Rp Kesalahan Berat / Mengundurkan diri</td>
		<td>Rp Uang Pisah</td>
	</tr><tbody id=tBody>";
foreach($data as $key=>$row) {
	echo "<tr class=rowcontent>";
	echo "<td><img src='images/".$_SESSION['theme']."/edit.png' onclick=editMode(".$key.") class=zImgBtn></td>";
	echo "<td><img src='images/".$_SESSION['theme']."/delete.png' onclick=deleteData(".$key.") class=zImgBtn></td>";
	foreach($row as $attr=>$val) {
		if($attr!='masakerja') {
			$tmpVal = number_format($val,2);
		} else {
			$tmpVal = $val;
		}
		echo "<td id='".$attr."_".$key."' value='".$val."'>".$tmpVal."</td>";
	}
	echo "</tr>";
}
echo "</tbody></table></fieldset>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>