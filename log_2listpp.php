<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
//OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['riwayatPP']." v2").'</b>'); //1 O
OPEN_BOX('','<b>LIST PP DAN PO</b>'); //1 O
?>
<!--<script type="text/javascript" src="js/log_2keluarmasukbrg.js" /></script>
-->
<script language="javascript" src="js/zMaster.js"></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<!--<script language="javascript">
    function validat3(arr,ev){
        key=getKey(ev);
        if(key==13){
            param='log_slave_2listpp',+arr+,'contain';
            exit('tes');
              return zPreview(param);
        } else {
        return tanpa_kutip(ev);	
        }	
    }
</script>-->

<div id="action_list">
<?php
$arrPil=array("1"=>"Proses Persetujuan PP","2"=>"Proses Purchsing","3"=>"Sudah PO","4"=>"Blm PO");
foreach($arrPil as $id =>$isi)
{
	$optPil.="<option value=".$id.">".$isi."</option>";
}
$optLokal="<option value=''>".$_SESSION['lang']['all']."</option>";
$arrPo=array("0"=>"Head Office","1"=>"Local");
foreach($arrPo as $brsLokal =>$isiLokal)
{
    $optLokal.="<option value=".$brsLokal.">".$isiLokal."</option>";
}
$optLokal2="<option value=''>".$_SESSION['lang']['all']."</option>";
$arrPo=array("0"=>$_SESSION['lang']['belumterima'],"1"=>$_SESSION['lang']['sudahditerima']);
foreach($arrPo as $brsLokal =>$isiLokal)
{
    $optLokal2.="<option value=".$brsLokal.">".$isiLokal."</option>";
}
$optper="<option value=''>".$_SESSION['lang']['all']."</option>";
$sTgl="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_prapoht order by tanggal desc";
$qTgl=mysql_query($sTgl) or die(mysql_error());
while($rTgl=mysql_fetch_assoc($qTgl))
{
   $optper.="<option value='".$rTgl['periode']."'>".substr($rTgl['periode'],5,2)."-".substr($rTgl['periode'],0,4)."</option>";
}
$optSupplier="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="select namasupplier,supplierid from ".$dbname.".log_5supplier  where kodekelompok='S001' and namasupplier!='' order by namasupplier asc";
$query=mysql_query($sql) or die(mysql_error());
while($res=mysql_fetch_assoc($query))
{
   $optSupplier.="<option value='".$res['supplierid']."'>".$res['namasupplier']."</option>";
}
$arr="##txtNopp##tgl_cari##periode##lokBeli##txtNmBrg##supplier_id##stat_id";
	 echo"<table>
     <tr valign=moiddle>
		 <td><fieldset><legend>".$_SESSION['lang']['pilihdata']."</legend>"; 
	            echo "<table cellpadding=1 cellspacing=1 border=0>
                          <tr><td>".$_SESSION['lang']['nopp']."</td><td>:</td><td><input type='text' id='txtNopp' name='txtNopp' onkeypress='return tanpa_kutip(event)' style='width:150px' class=myinputtext /></td>";
		    echo "<td>".$_SESSION['lang']['tanggal']." </td><td>:</td><td><input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;   maxlength=10 style=width:150px /></td>";
		    echo "</td><td>".$_SESSION['lang']['periode']."</td><td>:</td><td><select id=periode name=periode style='width:150px;'>".$optper."</select></td></tr>";
                    echo "<tr><td>".$_SESSION['lang']['lokasiBeli']."</td><td>:</td><td><select id=lokBeli name=lokBeli style='width:150px;'>".$optLokal."</select></td>";
                    echo "<td>".$_SESSION['lang']['namabarang']."</td><td>:</td><td><input type='text' id='txtNmBrg' name='txtNmBrg' onkeypress='return tanpa_kutip(event)' style='width:150px' class=myinputtext /></td>";
                    echo "<td>".$_SESSION['lang']['namasupplier']."</td><td>:</td><td><select id=\"supplier_id\" name=\"supplier_id\"  style=\"width:150px;\" >
			".$optSupplier."</select><img src=\"images/search.png\" class=\"resicon\" title='".$_SESSION['lang']['findRkn']."' onclick=\"searchSupplier('".$_SESSION['lang']['findRkn']."','<fieldset><legend>".$_SESSION['lang']['find']."</legend>".$_SESSION['lang']['find']."&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event);\"></td></tr>";
                    echo"<tr><td>".$_SESSION['lang']['status']."</td><td>:</td><td><select id=\"stat_id\" name=\"supplier_id\"  style=\"width:150px;\" >".$optLokal2."</td><td colspan=3></td></tr></table>";
                    echo"<button onclick=\"zPreview('log_slave_2listpp','".$arr."','contain')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
                         <button onclick=\"zExcel(event,'log_slave_2listpp.php','".$arr."','contain')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>

    <fieldset>
    <legend><?php echo $_SESSION['lang']['list']?></legend>
        <div style="overflow:auto; height:400px; width:1000px;"   id="contain">
		
    </div>
    </fieldset>

<?php
CLOSE_BOX();
?>
<?php
echo close_body();
?>