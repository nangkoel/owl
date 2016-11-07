<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['debetkreditnote']."</b>");
?>
<script language=javascript src='js/zMaster.js'></script> 
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script languange=javascript1.2 src='js/zSearch.js'></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<script languange=javascript1.2 src='js/keu_2debitNote.js'></script>
<?php
$opt_kepada=$opt_unit=$opt_pt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$s_pt="select * from ".$dbname.".organisasi where tipe='PT' order by kodeorganisasi asc";
$q_pt=mysql_query($s_pt) or die(mysql_error($conn));
while($r_pt=mysql_fetch_assoc($q_pt))
{
    $opt_pt.="<option value='".$r_pt['kodeorganisasi']."'>".$r_pt['namaorganisasi']."</option>";
}

$array = "##pt##unit##kepada##tanggal##sd##tipe";
?>
<div>
<fieldset style='float:left;'>
<legend><?php echo $_SESSION['lang']['form']?></legend>
<table cellspacing="1" border="0">
    <tr>
        <td><?php echo $_SESSION['lang']['namapt']?></td><td>:</td>
        <td colspan="4"><select id='pt' style="width:150px;" onchange="load_unit_kpd()"><?php echo $opt_pt?></select></td>
    </tr>
    <tr>
        <td><?php echo $_SESSION['lang']['unitkerja']?></td><td>:</td>
        <td colspan="4"><select id='unit' style="width:150px;" onchange="load_kpd()"><?php echo $opt_unit?></select></td>
    </tr>
    <tr>
        <td><?php echo $_SESSION['lang']['kepada']?></td><td>:</td>
        <td colspan="4"><select id='kepada' style="width:150px;" ><?php echo $opt_kepada?></select></td>
    </tr>
    <tr>
        <td><?php echo $_SESSION['lang']['tanggal']?></td><td>:</td>
        <td><input type='text' class='myinputtext' id='tanggal' name='tanggal' onmousemove='setCalendar(this.id);' 
             onkeypress='return false;'  maxlength=10 style='width:100px;' /></td>
        <td><?php echo $_SESSION['lang']['sd']?></td><td>:</td>
        <td><input type='text' class='myinputtext' id='sd' name='sd' onmousemove='setCalendar(this.id);' 
             onkeypress='return false;'  maxlength=10 style='width:100px;'/></td>
    </tr>
    <tr>
        <td><?php echo $_SESSION['lang']['tipe']?></td><td>:</td>
        <td colspan="4">
            <select id='tipe' style="width:150px;">
                <option value=''><?php echo $_SESSION['lang']['pilihdata']; ?></option>
                <option value="Debet Note"><?php echo$_SESSION['lang']['debet'];?> Note</option>
                <option value="Kredit Note"><?php echo$_SESSION['lang']['kredit'];?> Note</option>
            </select></td>
    </tr>
    <td colspan="6" id="tombol" align="center">
        <?php 
        echo "<button onclick=\"zPreview('keu_slave_2debitNote','".$array."','reportcontainer')\" 
         class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
        <button onclick=\"zPdf('keu_slave_2debitNote','".$array."','reportcontainer')\" class=\"mybutton\" 
         name=\"pdf\" id=\"pdf\">". $_SESSION['lang']['pdf']."</button>
        <button onclick=\"zExcel(event,'keu_slave_2debitNote.php','".$array."','reportcontainer')\" 
         class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>"; 
        ?>
    </td>
    </tr>
</table>
</fieldset>
</div>
<?php CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<fieldset><legend>".$_SESSION['lang']['debetkreditnote']."</legend>
                 <div id='reportcontainer' style='width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> 
                 </fieldset>"; 
CLOSE_BOX();
?>