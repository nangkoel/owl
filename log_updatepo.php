<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['updatepo']."</b>");

$arr="##nopo";

?>
<script>
dert="<?php echo $optLokal2;?>";
dert2="<?php echo $optLokal;?>";
</script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/log_updatepo.js'></script>

<link rel=stylesheet type='text/css' href='style/zTable.css'>
<?php
echo"<div>
<fieldset style=\"float: left;\">
<legend><b>".$_SESSION['lang']['updatepo']."</b></legend>
<table cellspacing=\"1\" border=\"0\" >

<tr><td><label>".$_SESSION['lang']['nopo']."</label></td><td>
<input type=text id=nopo class=myinputtext  style=width:150px; /></td></tr>

<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('log_slave_updatepo','".$arr."','printContainer')\" class=\"mybutton\">Proses</button>
        </td></tr>

</table>
</fieldset>
</div>";


echo"<fieldset style='clear:both;width:1050px;display:block;'><legend><b>".$_SESSION['lang']['list']."</b></legend>
<div id='printContainer' style='overflow:auto;width:1050px'>

</div></fieldset>";
 
CLOSE_BOX();
echo close_body();
?>