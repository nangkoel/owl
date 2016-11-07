<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['ulatapi']."</b>");
//print_r($_SESSION['temp']);
$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript">
nmTmblDone='<?php echo $_SESSION['lang']['done']?>';
nmTmblCancel='<?php echo $_SESSION['lang']['cancel']?>';
nmTmblSave='<?php echo $_SESSION['lang']['save']?>';
nmTmblCancel='<?php echo $_SESSION['lang']['cancel']?>';
kdBlok='<?php echo $_SESSION['lang']['kodeblok']?>';
pilBlok="<?php echo $optKary; ?>";
</script>
<script language="javascript" src="js/kebun_qcUlatApi.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />
<div id="action_list">
<?php
$optOrg.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
       where induk='".$_SESSION['org']['kodeorganisasi']."' and tipe='KEBUN' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=  mysql_fetch_assoc($qOrg)){
    $optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}
echo"<table cellspacing=1 border=0>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
                    echo $_SESSION['lang']['kebun'].":<select id=kdOrgCari style='width:120px;' >".$optOrg."</select>
                    <!--<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext onclick=\"cariOrg('".$_SESSION['lang']['find']."','<fieldset><legend>".$_SESSION['lang']['searchdata']."</legend>Find<input type=text class=myinputtext id=crOrg><button class=mybutton onclick=findOrg2()>Find</button></fieldset><div id=container></div>','event')\">-->&nbsp;";
                    echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
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
    
$arrJenis=array("sebelum"=>"sebelum","pengendalian"=>"pengendalian","sesudah"=>"sesudah");
foreach($arrJenis as $lsJenis){
    $optJns.="<option value='".$lsJenis."'>".$lsJenis."</option>";
}




		#pengawas anggaota QC
		$optMandor="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		/*$j="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and kodeunit like '%RO%')  and kodejabatan in (		
			select kodejabatan from ".$dbname.".sdm_5jabatan where namajabatan like '%pengawas%' or namajabatan like '%QC%')";*/
		$j="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and kodeunit like '%RO%')  and bagian='QC'";	
		
		$k=mysql_query($j) or die (mysql_error($conn));
		while($l=mysql_fetch_assoc($k))
		{
			$optMandor.="<option value='".$l['karyawanid']."'>".$l['nik']." - ".$l['namakaryawan']."</option>";
		}
		
		#asisten
		$optAstn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		/*$d="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where 
			lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and kodeunit like '%RO%') 
			and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where  namajabatan like '%QC%' or namajabatan like '%KA. AFD%')";*/
			
		$d="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where 
			lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and kodeunit not like '%HO%') 
			and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where  namajabatan like '%PENGAWAS%' or 
			namajabatan like '%KA. AFDELING%' or namajabatan like '%recorder%' or namajabatan like '%KASUB AFDELING%')";	
			
		//exit("Error:$d");
		$e=mysql_query($d) or die (mysql_error($conn));
		while($f=mysql_fetch_assoc($e))
		{
			$optAstn.="<option value='".$f['karyawanid']."'>".$f['nik']." - ".$f['namakaryawan']."</option>";
		}
		
		#mengetahui (manager/kadiv)
		$optKadiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		/*$g="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and kodeunit like '%RO%')  and kodejabatan in (		
			select kodejabatan from ".$dbname.".sdm_5jabatan where  namajabatan like '%KEPALA%')";*/
		$g="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and kodeunit not like '%HO%')  and bagian='QC'";	
		//exit("Error:$i");
		$h=mysql_query($g) or die (mysql_error($conn));
		while($i=mysql_fetch_assoc($h))
		{
			$optKadiv.="<option value='".$i['karyawanid']."'>".$i['nik']." - ".$i['namakaryawan']."</option>";
		}



?>
<fieldset style="float:left"">
<legend><?php echo $_SESSION['lang']['header']?></legend>
<table cellspacing="1" border="0">
<tr>
<td><?php echo $_SESSION['lang']['kebun']?></td>
<td>:</td>
<td>
<select id="divisiId" style="width:150px;" ><?php echo $optOrg;?></select>
</td>
<td><?php echo $_SESSION['lang']['pengawas']?></td>
<td>:</td>
<td>
<select id="pengawasId" style="width:150px;"><?php echo $optMandor;?></select>
</td>

</tr>

 <tr>
<td><?php echo $_SESSION['lang']['kodeblok']?></td>
<td>:</td>
<td>
    <input type="text" class="myinputtext" id="kodeBlok" style="width:150px;" readonly onclick="getBlok(kdBlok,event)" />
    <br/><span id="nmOrg"></span>
</td>
<td><?php echo $_SESSION['lang']['pendamping']?></td>
<td>:</td>
<td>
<select id="pendampingId" style="width:150px;"><?php echo $optAstn;?></select>
</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['tglsensus']?></td>
<td>:</td>
<td>
<input type="text" class="myinputtext" id="tglSensus" name="tglSensus" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" />
</td>
<td><?php echo $_SESSION['lang']['mengetahui']?></td>
<td>:</td>
<td>
<select id="mengetahuiId" style="width:150px;"><?php echo $optKadiv;?></select>
</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['tglPengendalian']?></td>
<td>:</td>
<td>
<input type="text" class="myinputtext" id="tglPengendalian" name="tglPengendalian" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" />
</td>
<td><?php echo $_SESSION['lang']['catatan']?></td>
<td>:</td>
<td rowspan="2">
    <textarea id="catatan" onkeypress="return tanpa_kutip(event)"></textarea>
</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['jenis']?></td>
<td>:</td>
<td>
<select id="jenisId" style="width:150px;"><?php echo $optJns;?></select>
</td>
</tr>
<tr>
<td colspan="3" id="tmbLheader">
    
</td>
</tr>
</table>
</fieldset><input type="hidden" id="proses" value="insert" />
<?php
CLOSE_BOX();
?>
</div>
<div id="detailEntry" style="display:none">
<?php 
OPEN_BOX();
?>
<div id="addRow_table">
<fieldset style="clear:both;float:left;">
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
<div style="overflow:auto;height:300px;clear:both;">
<fieldset style="float:left;">
<legend><?php echo $_SESSION['lang']['datatersimpan']?></legend>
    <table cellspacing="1" border="0">
    <thead>
        <tr class="rowheader">
            <td>No.</td>
            <?php
            $table .= "<td>".$_SESSION['lang']['pokokdiamati']."</td>";
            $table .= "<td>".$_SESSION['lang']['luaspengamatan']."</td>";
            $table .= "<td>Darna Trima</td>"; 
            $table .= "<td>Setothosea Asigna</td>";
            $table .= "<td>Setora Nitens</td>";
			$table .= "<td>Ulat Kantong</td>";
            $table .= "<td>Keterangan</td>";
            $table .= "<td>Action</td>";
            echo $table;
            ?>
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
