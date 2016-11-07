<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src='js/zMaster.js'></script>
<script language=javascript src='js/keu_5kelompokjurnal_reset.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php
#======Select Prep======
# Get Data
$where = "`tipe`='HOLDING' or `tipe`='PT'";
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$where,'0');

$optPt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOPt="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where ".$where."";

$qOpt=mysql_query($sOPt) or die(mysql_error($conn));
while($rOpt=  mysql_fetch_assoc($qOpt))
{
    $optPt.="<option value=".$rOpt['kodeorganisasi'].">".$rOpt['namaorganisasi']."</option>";
}

#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:150px'),$optOrg)
);
$els[] = array(
  makeElement('kodekelompok','label',$_SESSION['lang']['kodekelompok']),
  makeElement('kodekelompok','text','',array('style'=>'width:100px','maxlength'=>'5',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
  makeElement('keterangan','text','',array('style'=>'width:250px','maxlength'=>'45',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('nokounter','label',$_SESSION['lang']['nokounter']),
  makeElement('nokounter','text','0',array('style'=>'width:70px','maxlength'=>'11',
    'onkeypress'=>'return angka_doang(event)'))
);

# Fields
$fieldStr = '##kodeorg##kodekelompok##keterangan##nokounter';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'keu_5kelompokjurnal',"##kodeorg##kodekelompok")
);

# Generate Field
echo genElTitle($_SESSION['lang']['kodekelompok']." ".$_SESSION['lang']['jurnal'],$els);
echo "</div><br /><br /><br /><br /><br /><br /><br /><br />";
#=======End Form============
echo"
<fieldset style=width=30px;float:left;>
<table cellpading=1 border=0>
<tr><td><select id=kodePt name=kodePt style='width:150px;'>".$optPt."</select><button class=mybutton onclick=\"resetJurnal()\">".$_SESSION['lang']['save']."</button>
</table>
</fieldset><br /><br /><br /><br />";

#=======Table===============
# Display Table
echo "<div style='clear:both;float:left'>";
echo masterTable($dbname,'keu_5kelompokjurnal');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>