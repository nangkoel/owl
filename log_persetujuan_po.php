<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX(); //1 O

?>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/log_persetujuan_po.js"></script>
<div id="action_list">
<?php
echo"<table>
     <tr valign=moiddle>
         <td align=center style='width:100px;cursor:pointer;' onclick=refresh_data()>
           <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
         <td><fieldset><legend>".$_SESSION['lang']['carinopo']."</legend>"; 
                        echo $_SESSION['lang']['nopo'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>&nbsp;";
                        echo $_SESSION['lang']['tgl_po'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
                        echo"<button class=mybutton onclick=cariNopo()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
         </table> "; 
?>
</div>
<?php
CLOSE_BOX(); //1 C //2 O
?>
<div id=list_pp_verication>
<?php OPEN_BOX();?>
<fieldset>
<legend><?php echo $_SESSION['lang']['list_po'];?></legend>
<div style="overflow:scroll; height:420px;">
         <table class="sortable" cellspacing="1" border="0">
         <thead>
         <tr class=rowheader>
         <td>No.</td>
         <td><?php echo $_SESSION['lang']['nopo']?></td>
         <td><?php echo $_SESSION['lang']['tgl_po'];?></td> 
         <td><?php echo $_SESSION['lang']['namaorganisasi'];?></td>
          <td>Detail PO</td>
           <td colspan="2" align="center">Verification</td>
          <?php		
                                for($i=1;$i<4;$i++)
                                 {
                                        echo"<td align=center>".$_SESSION['lang']['persetujuan'].$i."</td>";
                                 }
           ?>

         </tr>
         </thead>
         <tbody id="contain">
        <script>refresh_data()</script>
         <?php 

         ?>
          </tbody>
         <tfoot>
         </tfoot>
         </table></div>
</fieldset
><?php
CLOSE_BOX();
?>
</div>
<input type="hidden" name="method" id="method"  /> 
<input type="hidden" id="no_po" name="no_po" />
<input type="hidden" name="user_login" id="user_login" value="<?php echo $_SESSION['standard']['userid']?>" />
<?php
echo close_body();
?>