<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zMaster.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
  
<p align="left"><u><b><font face="Arial" size="5" color="#000080">Jenis Bibit</font></b></u></p>
<?php
#======Select Prep======
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('jenisbibit','label',$_SESSION['lang']['jenisbibit']),
  makeElement('jenisbibit','text','',array('style'=>'width:100px','maxlength'=>'30',
    'onkeypress'=>'return tanpa_kutip(event)'))
);

# Fields
$fieldStr = '##jenisbibit';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'setup_jenisbibit',"##jenisbibit")
);

# Generate Field
echo genElement($els);
echo "</div>";
#=======End Form============

#=======Table===============
# Display Table
echo "<div style='height:200px;overflow:auto'>";
echo masterTable($dbname,'setup_jenisbibit',"*",array(),array(),null,array(),null,'jenisbibit');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>