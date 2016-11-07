<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript1.2 src='js/tool_resethmkm.js'></script>
<?php
$arr="##listTransaksi##listReset##method";
include('master_mainMenu.php');
OPEN_BOX();
$opt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$pil=array("1"=>$_SESSION['lang']['kasbank'],"3"=>$_SESSION['lang']['kontrak'],"4"=>$_SESSION['lang']['tbm']."/".$_SESSION['lang']['tm']."/".$_SESSION['lang']['panen'],"5"=>$_SESSION['lang']['traksi']);
foreach($pil as $dtl=>$vw)
{
    $opt.="<option value='".$dtl."'>".$vw."</option>";
}
$optUnit2=$optUnit=$optPrd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriode="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error($conn));
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPrd.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
}
$sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)=4 order by namaorganisasi asc";
$qUnit=mysql_query($sUnit) or die(mysql_error($conn));
while($rUnit=mysql_fetch_assoc($qUnit))
{
    $optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['kodeorganisasi']." - ".$rUnit['namaorganisasi']."</option>";
    if(substr($rUnit['kodeorganisasi'],3,1)=='E')
    {
    $optUnit2.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['kodeorganisasi']." - ".$rUnit['namaorganisasi']."</option>";
    }
}
echo"<table><tr><td valign=top><fieldset style=width:350px;>
     <legend>Reset HM/KM</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['kodevhc']."</td>
	   <td><textarea id=listTransaksi name=listTransaksi></textarea></td>
	 </tr>
     
         <tr>
	   <td>".$_SESSION['lang']['vhc_kmhm_akhir']." ></td>
	   <td>
            <textarea id=listReset name=listReset></textarea>
           </td>
	 </tr>
	
	 </table>
	 <button class=mybutton id=tmblDt onclick=saveFranco('tool_slave_reset','".$arr."')>".$_SESSION['lang']['proses']."</button>
     </fieldset><input type=hidden id=method value=getData />";
echo"</td></tr></table>";

CLOSE_BOX();

echo"<div id=listData style=display:none>";
OPEN_BOX();
echo"<fieldset style=height:550px;width:700px;><legend>".$_SESSION['lang']['list']."</legend>
    <div id=container style=overflow:auto;height:450px;width:700px;>";
echo"</div></fieldset>";
CLOSE_BOX();
echo"</div>";
echo close_body();
?>