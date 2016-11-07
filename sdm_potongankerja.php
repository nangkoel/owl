<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['potongan']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript">
function add_new_data(){
                document.getElementById('headher').style.display="block";
                document.getElementById('listData').style.display="none";
                document.getElementById('detailEntry').style.display="none";
                unlockForm();	
                document.getElementById('contentDetail').innerHTML='';
                statFrm=0;
}
nmTmblDone='<?php echo $_SESSION['lang']['done']?>';
nmTmblCancel='<?php echo $_SESSION['lang']['cancel']?>';
</script>
<script language="javascript" src="js/sdm_potongankerja.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />
<div id="action_list">
<?php
        $optTipePot=$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        
	$idOrg=substr($_SESSION['empl']['lokasitugas'],0,4);
        if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
            $sql="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where 
                  kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') 
                  ORDER BY `namaorganisasi` ASC";
            $sGet="select distinct periode from ".$dbname.".sdm_5periodegaji 
                   where kodeorg in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') 
                   and sudahproses=0 and jenisgaji='H' order by periode desc";
        }else{
            $sql="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' ORDER BY `namaorganisasi` ASC";
            $sGet="select distinct periode from ".$dbname.".sdm_5periodegaji 
                   where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by periode desc";
        }
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query)){
		$optOrg.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>"; 
	}
        #list potongan
        $sTipePot="select distinct id,name from ".$dbname.".sdm_ho_component where pinjamanid=1 order by name asc";
        $qTipePot=mysql_query($sTipePot) or die(mysql_error($conn));
        while($rTipePot=  mysql_fetch_assoc($qTipePot)){
            $lstKet=explode(" ",$rTipePot['name']);
            $nma="";
            foreach ($lstKet as $key => $value) {
                if($key==0){
                    continue;
                    $nma="";
                }else{
                    $nma.=$value." ";
                }
            }
            $optTipePot.="<option value='".$rTipePot['id']."'>".$nma."</option>";
        }
        $optPeriode.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>"; 
        $qGet=mysql_query($sGet) or die(mysql_error($conn));
        while($rGet=  mysql_fetch_assoc($qGet)){
            $optPeriode.="<option value=".$rGet['periode'].">".$rGet['periode']."</option>"; 
        }
        
        
        
echo"<table cellspacing=1 border=0>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['unit'].":<select id=kdOrgCr>".$optOrg."</select>&nbsp;";
			echo $_SESSION['lang']['periode'].":<input type=text class=myinputtext id=tgl_cari onkeypress='return tanpa_kutip(event)'  size=10 maxlength=10 />";
                        echo $_SESSION['lang']['potongan'].":<select id=tpPotCr>".$optTipePot."</select>&nbsp;";
			echo"<button class=mybutton onclick=loadData(0)>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
	 </tr>
	 </table> "; 
?>
</div>
<?php
CLOSE_BOX();
?>
<div id="listData">
<?php OPEN_BOX()?>
<fieldset style="float:left;">
<legend><?php echo $_SESSION['lang']['list']?></legend>
<!--display data-->
<div id="contain">
<script>loadData(0);</script>
</div>
</fieldset>
<?php CLOSE_BOX()?>
</div>

<div id="headher" style="display:none">
<?php
OPEN_BOX();
//$optTipePot
?>
<fieldset style="float:left">
<legend><?php echo $_SESSION['lang']['header']?></legend>
<table cellspacing="1" border="0">
<tr><td><?php echo $_SESSION['lang']['kodeorg']?></td>
<td>:</td><td>
<select id="kdOrg" name="kdOrg" style="width:150px;"  ><?php echo $optOrg;?></select></td>
</tr>
<tr><td><?php echo $_SESSION['lang']['periode']?></td>
<td>:</td>
<td><select id="tglAbsen" style="width:150px"><?php echo $optPeriode?></select></td>
</tr>
<tr><td><?php echo $_SESSION['lang']['potongan']?></td>
<td>:</td>
<td><select id="tpPotongan" name="tpPotongan" style="width:150px;" ><?php echo $optTipePot;?></select>
</td>
</tr>
<tr>
<td colspan="3">
    <div id="tombolHeader">
        <button class=mybutton id=dtlAbn onclick=add_detail()><?php echo $_SESSION['lang']['save'] ?></button>
        <button class=mybutton id=cancelAbn onclick=cancelAbsn()><?php echo $_SESSION['lang']['cancel']?></button>
    </div>
</td>
</tr>
</table>
</fieldset>

<?php
CLOSE_BOX();
?>
</div>
<div id="detailEntry" style="display:none">
<?php 
OPEN_BOX();
?>
<div id="addRow_table">
<fieldset  style="float:left">
<legend><?php echo $_SESSION['lang']['detail']?></legend>
<div id="detailIsi">
</div>
<table cellspacing="1" border="0">
<tr><td id="tombol">

</td></tr>
</table>
</fieldset  style="float:left;">
</div><br />
<br />
<div style="overflow:auto; height:300px; clear:both;">
<fieldset  style="float:left;">
<legend><?php echo $_SESSION['lang']['datatersimpan']?></legend>
<table cellspacing='1' border='0' class='sortable'>
<thead>
 <tr class="rowheader">
    <td>No</td>
    <td><?php echo $_SESSION['lang']['nik'] ?></td>
    <td><?php echo $_SESSION['lang']['namakaryawan'] ?></td>
    <td><?php echo $_SESSION['lang']['potongan'] ?></td>
    <td><?php echo $_SESSION['lang']['keterangan'] ?></td>
    <td><?php echo $_SESSION['lang']['updateby'] ?></td>
    <td>Action</td>
</tr>
</thead>
<tbody id="contentDetail">

</tbody>
</table>
</fieldset>
</div>
<?php
CLOSE_BOX();
?>
</div>
<?php 
echo close_body();
?>

