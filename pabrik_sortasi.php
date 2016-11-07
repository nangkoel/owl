<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['sortasi']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script>
tmblPilih='<?php echo $_SESSION['lang']['proses']?>';
canForm='<?php echo $_SESSION['lang']['done']?>';
</script>
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript" src="js/pabrik_sortasi.js"></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<div id="action_list">
<?php
        $optFraksi="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sFraksi="select * from ".$dbname.".pabrik_5fraksi order by kode";
        $qFraksi=mysql_query($sFraksi) or die(mysql_error());
        while($rFraksi=mysql_fetch_assoc($qFraksi))
        {
                $optFraksi.="<option value=".$rFraksi['kode'].">".$rFraksi['keterangan']."</option>";
        }
echo"<table cellspacing=1 border=0>
     <tr valign=moiddle>
         <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
           <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
         <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
           <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
         <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
                        echo $_SESSION['lang']['noTiket'].":<input type=text id=noTiketcr name=noTiketcr class=myinputtext onkeypress=return(tanpa_kutip)  style=width:150px; />&nbsp;";
                        //echo $_SESSION['lang']['tanggal'].":<input type=\"text\" class=\"myinputtext\" id=\"tglCari\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false; \" size=\"10\" maxlength=\"4\" style=\"width:150px;\" />";
                        echo"<button class=mybutton onclick=cariTiket()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>

         </tr>
         </table> "; 
?>
</div>
<?php
CLOSE_BOX();
?>
<div id="listData">
<?php OPEN_BOX('','')?>
<fieldset>
<legend><?php echo $_SESSION['lang']['list']?></legend>
<div id="contain">
<script>loadData()</script>
</div>
</fieldset>

<?php CLOSE_BOX()?>
</div>



<div id="headher" style="display:none">
<?php
OPEN_BOX();

?>
<fieldset>
<legend><?php echo $_SESSION['lang']['entryForm']?></legend>
<div id="pilih">
<table cellspacing="1" border="0">
<tr>
<td><?php echo $_SESSION['lang']['pilihTanggal']?></td>
<td>:</td>
<td>
<input type="text" class="myinputtext" id="tgl" onmousemove="setCalendar(this.id)" onkeypress="return false; " size="10" maxlength="4" style="width:150px;" /></td>
</tr>
<tr>
<td colspan="3"><div id="tmblPilih"><button class="mybutton" id="dtlAbn" onclick="addData(0,0)"><?php echo $_SESSION['lang']['save']?></button></div></td>
</tr>
</table>
</div><br />
<br />

<div id="formInput" style="display:none">
<?php 
echo $_SESSION['lang']['noTiket'];
echo $_SESSION['lang']['cancel']
?>
</div>
<input type="hidden" id="proses" name="proses" value="insert"  />
<br />
<div id="showFormBwh" style="display:none;">
    <fieldset>
        <legend><?php echo $_SESSION['lang']['detail']?></legend>
        <div id="formDetail" style=" width:1100px;overflow:scroll;"></div>
    </fieldset>
    <br />
    <fieldset>
        <legend><?php echo $_SESSION['lang']['data'];?> : <span id="tanggalForm"></span></legend>
        <div id="isiDetail" style=" width:1100px;height:400px;overflow:scroll;">

        </div>
    </fieldset>
</div>

</fieldset>

<?php
CLOSE_BOX();
?>
</div>
<?php 
echo close_body();

?>