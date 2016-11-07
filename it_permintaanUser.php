<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['permintaanlayanan']."</b>");
?>
<script languange=javascript1.2 src='js/zSearch.js'></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<script languange=javascript1.2 src='js/formReport.js'></script>
<script languange=javascript1.2 src='js/zGrid.js'></script>
<script type="text/javascript" src="js/it_permintaanUser.js"></script>
<?php
$opt_jenis_layanan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$s_jenis_layanan="select kodekegiatan,keterangan from ".$dbname.".it_standard order by kodekegiatan asc";
$q_jenis_layanan=mysql_query($s_jenis_layanan) or die(mysql_error($conn));
while($r_jenis_layanan=mysql_fetch_assoc($q_jenis_layanan))
{
    $opt_jenis_layanan.="<option value='".$r_jenis_layanan['kodekegiatan']."'>".$r_jenis_layanan['keterangan']."</option>";
}

$opt_karyawan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$s_karyawan="select karyawanid,namakaryawan from ".$dbname.".datakaryawan
             where tipekaryawan='0' and karyawanid not like '".$_SESSION['standard']['userid']."' order by namakaryawan asc";
$q_karyawan=mysql_query($s_karyawan) or die(mysql_error($conn));
while($r_karyawan=mysql_fetch_assoc($q_karyawan))
{
    $opt_karyawan.="<option value='".$r_karyawan['karyawanid']."'>".$r_karyawan['namakaryawan']."</option>";
}
?>
<div id="add">
<fieldset style='float:left;'>
<legend><?php echo $_SESSION['lang']['form']." ".$_SESSION['lang']['permintaanlayanan']?></legend>
<table cellspacing="1" border="0" style="width:100px;">
    <tr>
        <td><?php echo $_SESSION['lang']['jenislayanan']?></td><td>:</td>
        <td><select id='jenislayanan' style="width:150px;"><?php echo $opt_jenis_layanan?></select></td>
    </tr>
    <tr>
        <td><?php echo $_SESSION['lang']['deskripsi']."/".$_SESSION['lang']['keluhan']?></td><td>:</td>
        <td><textarea rows="5" cols="50" id='deskripsi' onkeypress="return tanpa_kutip();" /></textarea></td>
    </tr>
    <tr>
        <td><?php echo $_SESSION['lang']['atasan']?></td><td>:</td>
        <td><select id='atasan' style="width:150px;"><?php echo $opt_karyawan?></select></td>
    </tr>
    <tr>
        <td><?php echo $_SESSION['lang']['managerit']?></td><td>:</td>
        <td><select id='managerit' style="width:150px;"><?php echo $opt_karyawan?></select></td>
    </tr>
    <td colspan="3" id="tombol" align="center">
        <button class=mybutton id=saveForm onclick=saveForm()><?php echo $_SESSION['lang']['save']?></button>
    </td>
    </tr>
</table>
</fieldset>
</div>
<?php CLOSE_BOX()?>
<?php OPEN_BOX()?>
<fieldset style='float:left;'>
    <legend><?php echo $_SESSION['lang']['list']?></legend>
     <table cellspacing="1" border="0" class="sortable">
        <thead>
            <tr class="rowheader">
            <td align="center">No.</td>
            <td align="center"><?php echo $_SESSION['lang']['tanggal']?></td>
            <td align="center"><?php echo $_SESSION['lang']['namakegiatan']?></td>
            <td align="center"><?php echo $_SESSION['lang']['namakaryawan']?></td>
            <td align="center"><?php echo $_SESSION['lang']['atasan']?></td>
            <td align="center"><?php echo $_SESSION['lang']['status']." ".$_SESSION['lang']['atasan']?></td>
            <td align="center"><?php echo $_SESSION['lang']['tanggal']." ".$_SESSION['lang']['atasan']?></td>
            <td align="center"><?php echo $_SESSION['lang']['status']." ".substr($_SESSION['lang']['managerit'],0,7)?></td>
            <td align="center"><?php echo $_SESSION['lang']['pelaksana']?></td>
            <td align="center"><?php echo $_SESSION['lang']['waktupelaksanaan']?></td>
            <td align="center"><?php echo $_SESSION['lang']['waktu']." ".$_SESSION['lang']['selesai']?></td>
            <td align="center"><?php echo $_SESSION['lang']['kepuasanuser']?></td>
            <td align="center"><?php echo $_SESSION['lang']['nilai']." ".$_SESSION['lang']['komunikasi']?></td>
            <td align="center" colspan="2"><?php echo $_SESSION['lang']['saran']?></td>
            <td align="center"><?php echo $_SESSION['lang']['saran']." ".$_SESSION['lang']['pelaksana']?></td>
            <td align="center"><?php echo $_SESSION['lang']['lihat']?></td>
            </tr>
        </thead>
        <tbody id="contain">
<?php

$limit=25;
$page=0;
if(isset($_POST['page']))
{
    $page=$_POST['page'];
    if($page<0)
    $page=0;
}
$offset=$page*$limit;

$sCount="select count(*) as jmlhrow from ".$dbname.".it_request order by notransaksi asc";
$qCount=mysql_query($sCount) or die(mysql_error());
while($rCount=mysql_fetch_object($qCount)){
    $jmlbrs= $rCount->jmlhrow;
}			

echo"<script>loaddata()</script>";
echo"<tr class=rowheader><td colspan=15 align=center>
    ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jmlbrs."<br />
    <button class=mybutton onclick=pages(".($page-1).");>".$_SESSION['lang']['pref']."</button>
    <button class=mybutton onclick=pages(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
    </td>
    </tr>";
?>
</tbody></table></fieldset>
<?php CLOSE_BOX()?>