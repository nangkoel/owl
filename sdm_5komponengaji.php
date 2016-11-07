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
  
<p align="left"><u><b><font face="Arial" size="5" color="#000080">Komponen Gaji</font></b></u></p>
<?php
#======Select Prep======
$optBin = array('Pengurang','Penambah');
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('idkomponen','label',$_SESSION['lang']['idkomponen']),
  makeElement('idkomponen','textnum','',array('style'=>'width:150px','maxlength'=>'11'))
);
$els[] = array(
  makeElement('namakomponen','label',$_SESSION['lang']['namakomponen']),
  makeElement('namakomponen','text','',array('style'=>'width:150px','maxlength'=>'45'))
);
$els[] = array(
  makeElement('tipe','label',$_SESSION['lang']['tipe']),
  makeElement('tipe','select','',array('style'=>'width:150px'),$optBin)
);
$els[] = array(
  makeElement('sumber','label',$_SESSION['lang']['sumber']),
  makeElement('sumber','text','',array('style'=>'width:150px','maxlength'=>'40'))
);

# Fields
$fieldStr = '##idkomponen##namakomponen##tipe##sumber';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'sdm_5komponengaji',"##idkomponen")
);

# Generate Field
echo genElement($els);
echo "</div>";
#=======End Form============

#=======Table===============
# Display Table
echo "<div style='height:200px;overflow:auto'>";
echo masterTable($dbname,'sdm_5komponengaji',"*",array(),array(),null,array(),null,'idkomponen');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>