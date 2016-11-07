<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['rekomendasiPupuk']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script>
jdlExcel='<?php  echo $_SESSION['lang']['rekomendasiPupuk']?>';

tmblDone='<?php echo $_SESSION['lang']['done']?>';
tmblCancelDetail='<?php echo $_SESSION['lang']['cancel']?>';
</script>
<script type="application/javascript" src="js/kebun_rekomendasiPupuk.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />
<div id="action_list">
<?php
$lksi=substr($_SESSION['empl']['lokasitugas'],0,4);
$sKbn="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='AFDELING' and induk='".$lksi."'";
$qKbn=mysql_query($sKbn) or die(mysql_error());
while($rKbn=mysql_fetch_assoc($qKbn))
{
	$optKbn.="<option value=".$rKbn['kodeorganisasi'].">".$rKbn['namaorganisasi']."</option>";
}
for($x=0;$x<=24;$x++)
{
	$dt=mktime(0,0,0,date('m')+$x,15,date('Y'));
	$optPeriode.="<option value=".date("Y-m",$dt).">".date("Y-m",$dt)."</option>";
}
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['kebun'].":<select id=crKbn name=crKbn><option value=''></option>".$optKbn."</select>&nbsp;";
			echo $_SESSION['lang']['tahunpupuk'].":<select id=crPeriode nama=crPeriode style='width:150px;'><option value=''></option>".$optPeriode."</select>";
			echo"<button class=mybutton onclick=cariData()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>
<?php
CLOSE_BOX();
?>
<div id="list_ganti">
<script>loadData();</script>
</div>



<div id="headher" style="display:none">
<?php
OPEN_BOX();

$thn=intval(date("Y"));
for($i=1988;$i<=$thn;$i++)
{
	$optThn.="<option value=".$i." ".($i==$thn?'selected':'').">".$i."</option>";
}
$sKlmpkBrg="select kode from ".$dbname.".log_5klbarang where kelompok like '%PUPUK%'"; 
$qKlmpkBrg=mysql_query($sKlmpkBrg) or die(mysql_error());
$rKlmpkBrg=mysql_fetch_assoc($qKlmpkBrg);

$skdBrg="select kodebarang, namabarang, satuan from ".$dbname.".log_5masterbarang where kelompokbarang='".$rKlmpkBrg['kode']."'";//echo $skdBrg;
$qkdBrg=mysql_query($skdBrg) or die(mysql_error());
while($rkdBrg=mysql_fetch_assoc($qkdBrg))
{
	$optBrg.="<option value=".$rkdBrg['kodebarang'].">".$rkdBrg['namabarang']."</option>";
}
$sBibit="select jenisbibit  from ".$dbname.".setup_jenisbibit order by jenisbibit  asc" ;
$qBibit=mysql_query($sBibit) or die(mysql_error());
while($rBibit=mysql_fetch_assoc($qBibit))
{
	$optBibit.="<option value=".$rBibit['jenisbibit']." >".$rBibit['jenisbibit']."</option>";
}

?>
<fieldset>
<legend><?php echo $_SESSION['lang']['entryForm']?></legend>
<table cellspacing="1" border="0">
<tr>
<td><?php echo $_SESSION['lang']['tahunpupuk']?></td>
<td>:</td>
<td><select id="periode" nama="periode" style="width:150px;"><?php echo $optPeriode ?></select>
</td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['afdeling']?></td>
<td>:</td>
<td><select id="idKbn" name="idKbn" style="width:150px;" onchange="getBlok('0','0')"><option value=""></option><?php echo $optKbn ?></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['blok']?></td>
<td>:</td>
<td><select id="idBlok" name="idBlok" style="width:150px;" onchange="getThn()"></select><input type="hidden" id="oldBlok" name="oldBlok" /></td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['tahuntanam']?></td>
<td>:</td>
<td>
    <input type="text" id="thnTnm" name="thnTnm" class="myinputtextnumber" style="width:150px;" value="" />
    <!--<select id="thnTnm" name="thnTnm" style="width:150px;"></select></td>-->
</tr>
<tr>
<td><?php echo $_SESSION['lang']['jenisPupuk']?></td>
<td>:</td>
<td><select id="jnsPpk" name="jnsPpk" style="width:150px;" onchange="getSatuan()"><option value=""></option><?php echo $optBrg ?></select></td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['dosis']." ".$_SESSION['lang']['rotasi']?> 1</td>
<td>:</td>
<td>
<input type="text" id="dosis" name="dosis" class="myinputtextnumber" style="width:150px;" onkeypress="return angka_doang(event)" value="0" />&nbsp;<span id="satuan"></span></td>
</tr>
<tr>
<tr>
<td><?php echo $_SESSION['lang']['dosis']." ".$_SESSION['lang']['rotasi']?> 2</td>
<td>:</td>
<td>
<input type="text" id="dosis2" name="dosis2" class="myinputtextnumber" style="width:150px;" onkeypress="return angka_doang(event)" value="0" />&nbsp;<span id="satuan2"></span></td>
</tr>
<tr>
<tr>
<td><?php echo $_SESSION['lang']['dosis']." ".$_SESSION['lang']['rotasi']?> 3</td>
<td>:</td>
<td>
<input type="text" id="dosis3" name="dosis3" class="myinputtextnumber" style="width:150px;" onkeypress="return angka_doang(event)" value="0" />&nbsp;<span id="satuan3"></span></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['jenisbibit']?></td>
<td>:</td>
<td><select id="jnsBibit" name="jnsBibit" style="width:150px;" ><?php echo $optBibit;?></select></td>
</tr>

<tr>
<td colspan="3" id="tmblHeader">
<button class=mybutton id='dtl_pem' onclick='saveData()'><?php echo $_SESSION['lang']['save']?></button><button class=mybutton id='cancel_gti' onclick='cancelSave()'><?php echo $_SESSION['lang']['cancel']?></button>
</td>
</tr>
</table>
</fieldset>

<?php
CLOSE_BOX();
?>
</div>

<?php 
echo close_body();
?>