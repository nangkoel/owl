<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/log_3potongan.js'></script>
<?php
 
include('master_mainMenu.php');
OPEN_BOX();

$frm[0]='';
$optUnit="<option value=''></option>";
$sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
        where tipe='KEBUN' and kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
        order by namaorganisasi asc";
$qUnit=mysql_query($sUnit) or die(mysql_error($conn));
while($rUnit=  mysql_fetch_assoc($qUnit)){
    $optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['kodeorganisasi']."-".$rUnit['namaorganisasi']."</option>";
}
$optPoto.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPot="select id,name from ".$dbname.".sdm_ho_component where name like '%kop%' or name like '%KOP%' or name like '%toko%' or name like '%bbm%' or name like '%bpjs%' and name not like '%sil%' order by name asc";
$qPot=mysql_query($sPot) or die(mysql_error($conn));
while($rPot=mysql_fetch_assoc($qPot)){
	$optPoto.="<option value='".$rPot['id']."'>".$rPot['name']."</option>";
}
$optPrd="<option value=''></option>";
for($x=0;$x<=12;$x++)
{
	$dte=mktime(0,0,0,(date('m')+2)-$x,15,date('Y'));
	$optPrd.="<option value=".date("Y-m",$dte).">".date("m-Y",$dte)."</option>";
}
$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg)){
	$optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}
$frm[0].="<fieldset><legend>Form</legend>
                     <div id=uForm>
                                         <span id=sample></span><br><br>
                                         (File type support only CSV).
                                        <form id=frm name=frm enctype=multipart/form-data method=post action='log_slave_3uploadpotongan.php' target=frame>	
										<table border=1 cellpaddin=1 cellspacing=1 width=550px>
										<tr><td>Jenis Potongan</td><td><select name=jnsPotongan>".$optPoto."</select></td></tr>
										<tr><td>Periode Gaji</td><td><select name=periodeGaji>".$optPrd."</select></td></tr>
										<tr><td>Unit</td><td><select name=unitId>".$optOrg."</select></td></tr>
										<tr><td colspan=2>
                                        <input type=hidden name=MAX_FILE_SIZE value=1024000>
                                        File:<input name=filex type=file id=filex size=25 class=mybutton>
                                        Field separated by<select name=pemisah>
                                        <option value=','>, (comma)</option>
                                        <option value=';'>; (semicolon)</option>
                                        <option value=':'>: (two dots)</option>
                                        <option value='/'>/ (devider)</option>
                                        </select></td></tr>
										<tr><td colspan=2>
                                        <input type=button class=mybutton  value=".$_SESSION['lang']['save']." title='Submit this File' onclick=submitFile()></tr></table>
                                    </form>
 
                                    <iframe frameborder=1 width=800px height=200px name=frame>
                                    </iframe>
                     </div>
                    </fieldset>";

#============================================================================================
$hfrm[0]='Upload';
//$hfrm[0]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,900);
CLOSE_BOX();
echo close_body();
?>