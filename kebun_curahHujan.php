<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['curahHujan']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript" src="js/kebun_curahHujan.js"></script>

<div id="action_list">
<?php

##untuk jam dan menit option			
for($t=0;$t<24;)
{
	if(strlen($t)<2)
	{
		$t="0".$t;
	}
	$jm.="<option value=".$t." ".($t==00?'selected':'').">".$t."</option>";
	$t++;
}
for($y=0;$y<60;)
{
	if(strlen($y)<2)
	{
		$y="0".$y;
	}
	$mnt.="<option value=".$y." ".($y==00?'selected':'').">".$y."</option>";
	$y++;
}	




for($x=0;$x<=24;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPeriode.="<option value=".date("Y-m",$dt).">".date("Y-m",$dt)."</option>";
}
	$lokasi=$_SESSION['empl']['lokasitugas'];
	$sql="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe in ('AFDELING','KEBUN') and kodeorganisasi like '".$lokasi."%'";
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		$optOrg.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>"; 
	}
echo"<table cellspacing=1 border=0>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['kodeorg'].":<select id=unitOrg name=unitOrg><option value=''></option>".$optOrg."</select>&nbsp;";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariCurah()>".$_SESSION['lang']['find']."</button>";
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
<fieldset>
<legend><?php echo $_SESSION['lang']['list']?></legend>

<table cellspacing="1" border="0" class="sortable">
<thead>
<tr class="rowheader">
<td>No.</td>
<td><?php echo $_SESSION['lang']['kebun']?></td>
<td><?php echo $_SESSION['lang']['tanggal'];?></td> 
<td><?php echo $_SESSION['lang']['pagi'];?></td>
<td><?php echo $_SESSION['lang']['sore'];?></td>	 
<td><?php echo $_SESSION['lang']['note'];?></td>
<td>Action</td>
</tr>
</thead>
<tbody id="contain">
<?php
$periodeAkutansi=$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
	$limit=10;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	
	$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_curahhujan where `kodeorg` like '".$lokasi."%'  order by `tanggal` desc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}
	

	$str="select * from ".$dbname.".kebun_curahhujan where `kodeorg` like  '".$lokasi."%' order by tanggal desc  limit ".$offset.",".$limit."";
	if(mysql_query($str))
	{
            $res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
		$spr="select namaorganisasi from  ".$dbname.".organisasi where  kodeorganisasi='".$bar->kodeorg."'";
		$rep=mysql_query($spr) or die(mysql_error($conn));
		$bas=mysql_fetch_object($rep);
		$no+=1;
		$sGp="select DISTINCT sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".$bar->kodeorg."' and `periode`='".substr($bar->tanggal,0,7)."'";
                $qGp=mysql_query($sGp) or die(mysql_error());
                $rGp=mysql_fetch_assoc($qGp);
                
		//echo $minute_selesai; exit();
		echo"<tr class=rowcontent id='tr_".$no."'>
		<td>".$no."</td>
		<td id='nmorg_".$no."'>".$bas->namaorganisasi."</td>
		<td id='kpsits_".$no."'>".tanggalnormal($bar->tanggal)."</td>
		<td id='strt_".$no."'>".$bar->pagi."</td>
		<td id='end_".$no."'>".$bar->sore."</td>
		<td id='tglex_".$no."'>".$bar->catatan."</td><td>";
		 if((substr($bar->tanggal,0,7)==$periodeAkutansi)||($rGp['sudahproses']==0)){
				echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"printPDF('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."',event);\">";
		 }else{
				
			}
                echo"</td></tr>";
		}	 	
		echo"
		<tr><td colspan=7 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";   	
	}	
	else
	{
	echo " Gagal,".(mysql_error($conn));
	}	
	?>
<?php 

?>

</tbody>
</table>
</fieldset>

<?php CLOSE_BOX()?>
</div>



<div id="headher" style="display:none">
<?php
OPEN_BOX();

for($x=0;$x<=12;$x++)
{
	$dte=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPrd.="<option value=".date("Y-m",$dte).">".date("Y-m",$dte)."</option>";
}
?>
<fieldset>
<legend><?php echo $_SESSION['lang']['entryForm']?></legend>
<table cellspacing="1" border="0">
<tr>
<td><?php echo $_SESSION['lang']['kodeorg']?></td>
<td>:</td>
<td>
<select id="kodeOrg" name="kodeOrg" style="width:150px;" ><option value=""></option><?php echo $optOrg;?></select>
<!--<input type="text"  id="noSpb" name="noSpb" class="myinputtext" style="width:120px;" disabled="disabled" />--></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['tanggal']?></td>
<td>:</td>
<td>
<input type="text" class="myinputtext" id="tgl" onmousemove="setCalendar(this.id)" onkeypress="return false; " size="10" maxlength="10" style="width:150px;" />
</td>
</tr>

<?php
	echo"
		<tr>
			<td>".$_SESSION['lang']['jam']." ".$_SESSION['lang']['mulai']." ".$_SESSION['lang']['pagi']."</td>
			<td>:</td>
			<td><select id=jmp>".$jm."</select>:<select id=mmp>".$mnt."</select></td>
		</tr>
		<tr>
			<td>".$_SESSION['lang']['jam']." ".$_SESSION['lang']['selesai']." ".$_SESSION['lang']['pagi']."</td>
			<td>:</td>
			<td><select id=jsp>".$jm."</select>:<select id=msp>".$mnt."</select></td>
		</tr>
	
	";
?>



<tr>
<td><?php echo $_SESSION['lang']['pagi']?></td>
<td>:</td>
<td>
<input type="text" class="myinputtextnumber" id="pg"  onkeypress="return angka_doang(event)" size="10" maxlength="10" value="0" style="width:150px;" /> mm
</td>
</tr>

<?php
	echo"
		<tr>
			<td>".$_SESSION['lang']['jam']." ".$_SESSION['lang']['mulai']." ".$_SESSION['lang']['sore']."</td>
			<td>:</td>
			<td><select id=jms>".$jm."</select>:<select id=mms>".$mnt."</select></td>
		</tr>
		<tr>
			<td>".$_SESSION['lang']['jam']." ".$_SESSION['lang']['selesai']." ".$_SESSION['lang']['sore']."</td>
			<td>:</td>
			<td><select id=jss>".$jm."</select>:<select id=mss>".$mnt."</select></td>
		</tr>
	
	";
?>



<tr>
<td><?php echo $_SESSION['lang']['sore']?></td>
<td>:</td>
<td>
<input type="text" class="myinputtextnumber" id="sr"  onkeypress="return angka_doang(event)" size="10" maxlength="10" value="0" style="width:150px;" /> mm</td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['note']?></td>
<td>:</td>
<td><input type="text" class="myinputtext" id="cttn" name="cttn" onkeypress="return tanpa_kutip(event)" style="width:150px;" maxlength="45" /></td>
</tr>
<tr>



<td colspan="3" id="tmbLheader">
<button class="mybutton" id="dtlAbn" onclick="saveData()"><?php echo $_SESSION['lang']['save']?></button><button class="mybutton" id="cancelAbn" onclick="cancelSave()"><?php echo $_SESSION['lang']['cancel']?></button><input type="hidden" id="proses" name="proses" value="insert"  />
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
