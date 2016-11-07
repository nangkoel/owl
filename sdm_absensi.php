<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['absensi']."</b>");
//print_r($_SESSION['temp']);
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript">
nmTmblDone='<?php echo $_SESSION['lang']['done']?>';
nmTmblCancel='<?php echo $_SESSION['lang']['cancel']?>';
nmTmblSave='<?php echo $_SESSION['lang']['save']?>';
nmTmblCancel='<?php echo $_SESSION['lang']['cancel']?>';

</script>
<script language="javascript" src="js/sdm_absensi.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />



<div id="action_list">
<?php
//for($x=0;$x<=24;$x++)
//{
//	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
//	$optPeriode.="<option value=".date("m-Y",$dt).">".date("m-Y",$dt)."</option>";
//}

//print_r($_SESSION['empl']);exit;
$optPeriodeCr="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sGp="select DISTINCT periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and `sudahproses`=0";
$qGp=mysql_query($sGp) or die(mysql_error());
while($rGp=mysql_fetch_assoc($qGp))
{
	$optPeriode.="<option value=".$rGp['periode'].">".substr(tanggalnormal($rGp['periode']),1,7)."</option>";
}
$optPeriodeCr.=$optPeriode;
	$idOrg=substr($_SESSION['empl']['lokasitugas'],0,4);
        $sql="select kodeorganisasi,namaorganisasi 
           from ".$dbname.".organisasi 
           where kodeorganisasi='".$idOrg."' or induk='".$_SESSION['empl']['lokasitugas']."' 
           ORDER BY `namaorganisasi` ASC";
//echo strlen($_SESSION['empl']['subbagian']);
       if(strlen($_SESSION['empl']['subbagian'])==6){
           $sql="select kodeorganisasi,namaorganisasi 
           from ".$dbname.".organisasi 
           where (kodeorganisasi='".$idOrg."' or induk='".$_SESSION['empl']['lokasitugas']."') 
           and kodeorganisasi like '".$_SESSION['empl']['subbagian']."%' 
           ORDER BY `namaorganisasi` ASC";
       }
//       echo $sql;
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		$optOrg.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>"; 
	}
        
if($_SESSION['language']=='ID'){
    $ket="Form absensi hari libur berfungsi untuk mencatat absensi <br>seluruh karyawan KHT yang masih aktif secara otomatis. <br>
                       Setiap hari libur dan hari minggu  harus dicatatkan melalui form ini. <br>Jika tidak dicatatkan maka akan menjadi potongan HK.";
}else{
    $ket="Form holiday attendance serves to record the attendance of all employees<br>who are still active KHT automatically. <br>
              Every holiday and day of week should be listed in this form. <br>If not listed then it will be a deduction HK.";
}        
echo"<table cellspacing=1 border=0>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend><table><tr>"; 
                    echo "<td>".$_SESSION['lang']['unit']."</td><td>:<select id=kdOrgCari style='width:120px;' ><option value=''></option>".$optOrg."</select></td>
                    <!--<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext onclick=\"cariOrg('".$_SESSION['lang']['find']."','<fieldset><legend>".$_SESSION['lang']['searchdata']."</legend>Find<input type=text class=myinputtext id=crOrg><button class=mybutton onclick=findOrg2()>Find</button></fieldset><div id=container></div>','event')\">-->&nbsp;";
                    echo "<td>".$_SESSION['lang']['tanggal']."</td><td>:<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>";
                    echo "<tr><td>".$_SESSION['lang']['nik']."/".$_SESSION['lang']['namakaryawan']."</td><td>:<input type=text class=myinputtext id=niknm_cari  style='width:120px;' /></td>";
                    echo "<td>".$_SESSION['lang']['periode']."</td><td>:<select id=periode_cari>".$optPeriodeCr."</select></td></tr></table>";
                    echo"<button class=mybutton onclick=cariAsbn()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td><td>
                    <fieldset><legend>Absensi Hari Libur(Holiday Absence)</legend>
                     <table>
                     <tr>
                      <td>
                           ".$_SESSION['lang']['harilibur']."<input type=text class=myinputtext id=tgllibur onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />
                          ".$_SESSION['lang']['jenis']."<select id=jlibur>
                                  <option value='L'>Hari Libur(Holiday)</option>
                                  <option value='MG'>".$_SESSION['lang']['hariminggu']."</option>
                                  <option value='C'>".$_SESSION['lang']['cuti']."(Bersama)</option></select>
                          <button class=mybutton onclick=saveHariLibur()>".$_SESSION['lang']['save']."</button> 
                     </td>
                     </tr>
                     <tr>
                     <td>
                            ".$ket."
                     </td>
                     </tr>
                     </table>
                     </fieldset></td>
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
<div id="contain">
<script>loadData();</script>
</div>
</fieldset>
<?php CLOSE_BOX()?>
</div>



<div id="headher" style="display:none">
<?php
OPEN_BOX();
for($x=0;$x<=3;$x++)
{
	$dte=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPrd.="<option value=".date("m-Y",$dte).">".date("m-Y",$dte)."</option>";
}
?>
<fieldset>
<legend><?php echo $_SESSION['lang']['header']?></legend>
<table cellspacing="1" border="0">
<tr>
<td><?php echo $_SESSION['lang']['kodeorg']?></td>
<td>:</td>
<td>
<select id="kdOrg" style="width:150px;" ><option value=""><?php echo $_SESSION['lang']['pilihdata']; ?></option><?php echo $optOrg ?></select>
</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['tanggal']?></td>
<td>:</td>
<td>
<input type="text" class="myinputtext" id="tglAbsen" name="tglAbsen" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" />
</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['periode']?></td>
<td>:</td>
<td>
<select id="periode" name="periode" style="width:150px;" ><?php echo $optPeriode;?></select>
</td>
</tr>
<tr>
<td colspan="3" id="tmbLheader">
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
<fieldset>
<legend><?php echo $_SESSION['lang']['detail']?></legend>
<div id="detailIsi">
</div>
<table>
<tr><td id="tombol">

</td></tr>
</table>
</fieldset>
</div><br />
<br />
<div style="overflow:auto;height:300px">
<fieldset>
<legend><?php echo $_SESSION['lang']['datatersimpan']?></legend>
    <table cellspacing="1" border="0">
    <thead>
        <tr class="rowheader">
        <td>No</td>
        <td><?php echo $_SESSION['lang']['nik'] ?></td>
        <td><?php echo $_SESSION['lang']['namakaryawan'] ?></td>
        <td><?php echo $_SESSION['lang']['shift'] ?></td>
        <td><?php echo $_SESSION['lang']['absensi'] ?></td>
        <td><?php echo $_SESSION['lang']['jamMsk'] ?></td>
        <td><?php echo $_SESSION['lang']['jamPlg'] ?></td>
        <td><?php echo $_SESSION['lang']['premi'] ?></td>
        <td><?php echo $_SESSION['lang']['penaltykehadiran'] ?></td>
        <td><?php echo $_SESSION['lang']['keterangan'] ?></td>
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
