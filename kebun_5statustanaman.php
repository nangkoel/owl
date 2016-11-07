<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src='js/zMaster.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
  
<p align="left"><u><b><font face="Arial" size="5" color="#000080"><?php echo $_SESSION['lang']['statustanam']?></font></b></u></p>
<?php
#======Select Prep======
$optOrg = getHolding($dbname,$_SESSION['org']['kodeorganisasi'],true);
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('kode','label',$_SESSION['lang']['kode']),
  makeElement('kode','text','',array('style'=>'width:100px','maxlength'=>'10',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
  makeElement('keterangan','text','',array('style'=>'width:250px','maxlength'=>'50',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:250px'),$optOrg)
);

# Fields
$fieldStr = '##kode##keterangan##kodeorg';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'kebun_5sttanam',"##kode##kodeorg")
);

# Generate Field
echo genElement($els);
echo "</div>";
#=======End Form============

#=======Table===============
# Display Table
echo "<div style='height:200px;overflow:auto'>";
echo masterTable($dbname,'kebun_5sttanam',"*",array(),array(),null,array(),null,'kode##kodeorg');
echo "</div>";
#=======End Table============
?>
<!--FORM NAME = "status tanaman">
<p align="center"><u><b><font face="Verdana" size="4" color="#000080">Status Tanaman</font></b></u></p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="87%" id="AutoNumber1" height="115">
  <tr>
    <td width="24%" height="1">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial">Kode</font></td>
    <td width="46%" height="1">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys"> 
    <input type=text size="6" name="koderekening">&nbsp; </font>
    </td>
    <td width="16%" height="1">
    <p style="margin-top: 0; margin-bottom: 0">
    </td>
  </tr>
  <tr>
    <td width="24%" height="22">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial">Keterangan</font></td>
    <td width="46%" height="22">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys"> 
    <input type=text size="41" name="tanggal"></font></td>
    <td width="16%" height="22">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
  </tr>
  <tr>
    <td width="24%" height="22">
    <p style="margin-top: 0; margin-bottom: 0">
    &nbsp;</td>
    <td width="46%" height="22">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
    <td width="16%" height="22">
    <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
  </tr>
  <tr>
    <td width="24%" height="22">&nbsp;</td>
    <td width="46%" height="22">&nbsp;</td>
    <td width="16%" height="22">&nbsp;</td>
  </tr>
  </table>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="Simpan" name="Simpan">
<input type="reset" value="Batal" name="Batal"></font></p>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber2"><tr><td width="16%" align="center">Kode</td><td width="16%" align="center">Keterangan</td></tr><tr><td width="16%">&nbsp;</td><td width="16%">&nbsp;</td>
</tr></table>
<p><font face="Fixedsys">&nbsp;&nbsp;&nbsp; &nbsp;</font></p-->
<?php
CLOSE_BOX();
echo close_body();
?>