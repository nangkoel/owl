<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();

$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
        $arr="##kdOrg##periode##afdId";
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','PABRIK','KANWIL','TRAKSI') order by namaorganisasi asc ";	
	$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji order by periode desc";
        $optOrg="<select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\" onchange='getSub()'><option value=''>".$_SESSION['lang']['pilihdata']."</option>";
}
else if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
        $arr="##kdOrg##periode##afdId";
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' or tipe in ('KEBUN','PABRIK','KANWIL') order by kodeorganisasi asc";
	$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji order by periode desc";
        $optOrg="<select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\" onchange='getSub()'><option value=''>".$_SESSION['lang']['pilihdata']."</option>";
}
else
{
        $arr="##kdOrg##periode";
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
	$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."' order by periode desc";
        $optOrg="<select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\"><option value=''>".$_SESSION['lang']['pilihdata']."</option>";
}
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
	$optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
}

$optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}


?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script>
function getSub()
{
    afd=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
    param='kdOrg='+afd+'&proses=getSubUnit';
    tujuan='sdm_slave_2laporanPremiPerHari.php';
    post_response_text(tujuan, param, respog);
    function respog()
    {
                  if(con.readyState==4)
                  {
                            if (con.status == 200) {
                                            busy_off();
                                            if (!isSaveResponse(con.responseText)) {
                                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                                            }
                                            else {
                                                    //alert(con.responseText);
                                                    document.getElementById('afdId').innerHTML=con.responseText;

                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
     }  	
}

function showpopup(karyawanid,tanggal,ev)
{
   param='karyawanid='+karyawanid+'&tanggal='+tanggal;
   tujuan='sdm_slave_2laporanPremiPerHari_showpopup.php'+"?"+param;  
   width='600';
   height='250';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('No Transaksi Premi '+karyawanid+' '+tanggal,content,width,height,ev); 
	
}
</script>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['laporanPremi']." ".$_SESSION['lang']['harian'];?></b></legend>
<table cellspacing="1" border="0" >
<tr>
	<td><label><?php echo $_SESSION['lang']['unit'];?></label></td>
	<td><?php echo $optOrg;?>
	</select></td>
</tr>
<?php if($_SESSION['empl']['tipelokasitugas']=='HOLDING'||$_SESSION['empl']['tipelokasitugas']=='KANWIL'){ ?>
<tr>
	<td><label><?php echo $_SESSION['lang']['subunit'];?></label></td>
	<td><select id="afdId" name="afdId" style="width:150px"><?php echo $optAfd;?>
	</select></td>
</tr>
<?php } ?>
<tr>
	<td><label><?php echo $_SESSION['lang']['periode'];?></label></td>
	<td><select id="periode" name="periode" style="width:150px">
		<!--<option value=""></option>--><?php echo $optPeriode;?>
	</select></td>
</tr>
 
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
	<button onclick="zPreview('sdm_slave_2laporanPremiPerHari','<?php echo $arr;?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
	<!--<button onclick="zPdf('sdm_slave_2laporanPremiPerHari','<?php echo $arr;?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>-->
	<button onclick="zExcel(event,'sdm_slave_2laporanPremiPerHari.php','<?php echo $arr;?>')" class="mybutton" name="preview" id="preview">Excel</button>

	<button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel'];?></button>
</td></tr>
</table>
</fieldset>
</div>


<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:330px;max-width:1100px'>

</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>