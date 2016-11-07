<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';
$frm[2]='';
OPEN_BOX();
?>
<?php
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='KEBUN'";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
//for($x=0;$x<=6;$x++)
//{
//	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
//	$optper.="<option value=".date("Y-m",$dt).">".date("m-Y",$dt)."</option>";
//}
$optper="<option value=''>".$_SESSION['lang']['all']."</option>";
$sTgl="select distinct substr(tanggal,1,7) as periode from ".$dbname.".pabrik_timbangan order by tanggal desc";
$qTgl=mysql_query($sTgl) or die(mysql_error());
while($rTgl=mysql_fetch_assoc($qTgl))
{
     $thn=explode("-", $rTgl['periode']);
   if($thn[1]=='12')
   {
   $optper.="<option value='".substr($rTgl['periode'],0,4)."'>".substr($rTgl['periode'],0,4)."</option>";
   }
   $optper.="<option value='".$rTgl['periode']."'>".substr($rTgl['periode'],5,2)."-".substr($rTgl['periode'],0,4)."</option>";
}
$intex=array('0'=>'External','1'=>'Internal','2'=>'Afiliasi');
$optTbs="<option value=''>".$_SESSION['lang']['all']."</option>";
foreach($intex as $dt => $rw)
{
	$optTbs.="<option value=".$dt.">".$rw."</option>";
}
$optISPO="<option value=''>".$_SESSION['lang']['all']."</option>";
$optISPO.="<option value='1'>ISPO</option>";
$optISPO.="<option value='0'>Non ISPO</option>";
$arr="##periode##tipeIntex##unit##ispo";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<script>
function getKode()
{
	tipeIntex=document.getElementById('tipeIntex').options[document.getElementById('tipeIntex').selectedIndex].value;
	param='tipeIntex='+tipeIntex+'&proses=getKdorg';
	tujuan="kebun_slave_3laporanProduksi.php";
	//alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                  	document.getElementById('unit').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan, param, respon);

}
function getAfd(id)
{
	kdOrg=document.getElementById('kdOrg_'+id).getAttribute('value');
	tglAfd=document.getElementById('tanggal_'+id).getAttribute('value');
	param="kodeOrg="+kdOrg+"&proses=getAfdeling"+"&brsKe="+id+"&tglAfd="+tglAfd;
	tujuan="kebun_slave_3laporanProduksi.php";
	//alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
				//	alert(con.responseText);
                  	document.getElementById('detail_'+id).innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan, param, respon);
}
function detailBlok(idAwal,id)
{
	kdBlok=document.getElementById('kdBlok_'+idAwal+'_'+id).innerHTML;
	nospb=document.getElementById('nospb_'+idAwal+'_'+id).innerHTML;
	tgl=document.getElementById('tanggal_'+idAwal).innerHTML;
	
	param='kdBlok='+kdBlok+'&proses=getPrestasi'+'&tgl='+tgl+'&brsKe='+idAwal+'&endKe='+id+'&nospb='+nospb;
	tujuan="kebun_slave_3laporanProduksi.php";

	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
				//	alert(con.responseText);
                  	document.getElementById('detailBlok_'+idAwal+'_'+id).innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan, param, respon);
}
function closeBlok(idAwal,id)
{
	document.getElementById('detailBlok_'+idAwal+'_'+id).innerHTML='';
}
function closeAfd(id)
{
	document.getElementById('detail_'+id).innerHTML='';
}
function batal()
{
	document.getElementById('periode').value='';
	document.getElementById('tipeIntex').value='';	
	document.getElementById('unit').value='';
	document.getElementById('printContainer').innerHTML='';
	
}
function batal2()
{
	document.getElementById('periodeId').value='';
	document.getElementById('unitId').value='';	
	
	document.getElementById('printContainer2').innerHTML='';
	
}

</script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php
$frm[0].="<div>
<fieldset style=\"float: left;\">
<legend><b>".$_SESSION['lang']['rProdKebun']."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periode\" name=\"periode\" style=\"width:150px\">".$optper."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['tbs']."</label></td><td><select id=\"tipeIntex\" name=\"tipeIntex\" onchange=\"getKode()\" style=\"width:150px\">".$optTbs."</select></td></tr>
<tr><td>".$_SESSION['lang']['unit']."/".$_SESSION['lang']['supplier']."</td><td><select id=\"unit\" style=\"width:150px\"><option value=''>".$_SESSION['lang']['all']."</option></select></td></tr>
<tr><td>".$_SESSION['lang']['statusISPO']."</td><td><select id=\"ispo\" name=\"ispo\" style=\"width:150px\">".$optISPO."</select></td></tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('kebun_slave_3laporanProduksi','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
<button onclick=\"zPdf('kebun_slave_3laporanProduksi','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['pdf']."</button>
<button onclick=\"zExcel(event,'kebun_slave_3laporanProduksi.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['excel']."</button>
<button onclick=batal() class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['cancel']."</button></td></tr>
</table>
</fieldset>
</div>";
//get unit
$optUniDt.="<option value=''>".$_SESSION['lang']['all']."</option>";
$sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' order by namaorganisasi asc";
$qUnit=mysql_query($sUnit) or die(mysql_error($conn));
while($rUnit=mysql_fetch_assoc($qUnit))
{
    $optUniDt.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['namaorganisasi']."</option>";
}
$arr2="##periodeId##unitId##ispo2";
$frm[0].="<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";
$frm[1].="<fieldset><legend>".$_SESSION['lang']['rProdKebundetail']."</legend>";
$frm[1].="<table cellspacing=1 border=0>";
$frm[1].="<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=periodeId style=width:150px>".$optper."</select></td></tr>";
$frm[1].="<tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id=unitId style=width:150px>".$optUniDt."</select></td></tr>";
$frm[1].="<tr><td><label>".$_SESSION['lang']['statusISPO']."</label></td><td><select id=\"ispo2\" name=\"ispo2\" style=\"width:150px\">".$optISPO."</select></td></tr>";
$frm[1].="<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=2>
<button onclick=\"zPreview('kebun_slave_3laporanProduksi2','".$arr2."','printContainer2')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
<button onclick=\"zPdf('kebun_slave_3laporanProduksi2','".$arr2."','printContainer2')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['pdf']."</button>
<button onclick=\"zExcel(event,'kebun_slave_3laporanProduksi2.php','".$arr2."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['excel']."</button>
<button onclick=batal2() class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['cancel']."</button></td></tr>";
$frm[1].="</table></fieldset>";
$frm[1].="<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer2' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";

#==============tab 3
$arr3="##periodetahun##unittahun##ispo3";
$frm[2].="<fieldset><legend>Production Trend</legend>";
$frm[2].="<table cellspacing=1 border=0>";
$frm[2].="<tr><td><labe>".$_SESSION['lang']['periode']."</label></td><td><select id=periodetahun style=width:150px>
                   <option value='".date('Y')."'>".date('Y')."</option>
                   <option value='".(date('Y')-1)."'>".(date('Y')-1)."</option>
                   <option value='".(date('Y')-2)."'>".(date('Y')-2)."</option>    
                   </select></td></tr>";
$frm[2].="<tr><td><labe>".$_SESSION['lang']['unit']."</label></td><td><select id=unittahun style=width:150px>".$optUniDt."</select></td></tr>";
$frm[2].="<tr><td><label>".$_SESSION['lang']['statusISPO']."</label></td><td><select id=\"ispo3\" name=\"ispo3\" style=\"width:150px\">".$optISPO."</select></td></tr>";
$frm[2].="<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=2>
<button onclick=\"zPreview('kebun_slave_3laporanProduksi3','".$arr3."','printContainer3')\" class=\"mybutton\" name=\"preview2\" id=\"preview2\">".$_SESSION['lang']['preview']."</button>
<button onclick=\"zExcel(event,'kebun_slave_3laporanProduksi3.php','".$arr3."')\" class=\"mybutton\" name=\"excel2\" id=\"excel2\">".$_SESSION['lang']['excel']."</button>
<button onclick=batal2() class=\"mybutton\" name=\"batal2\" id=\"batal2\">".$_SESSION['lang']['cancel']."</button></td></tr>";
$frm[2].="</table></fieldset>";
$frm[2].="<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer3' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";

//========================
$hfrm[0]=$_SESSION['lang']['rProdKebun'];
$hfrm[1]=$_SESSION['lang']['rProdKebundetail'];
$hfrm[2]='Production Trend';
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,900);
//===============================================	

CLOSE_BOX();
echo close_body();
?>