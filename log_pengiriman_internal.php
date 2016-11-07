<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/log_pengiriman_internal.js'></script>
<?php
$arrData="##id_supplier##tglKrm##jlhKoli##kpd##lokPenerimaan##srtJalan##biaya##ket##method##nomor_id";

include('master_mainMenu.php');
//action cari
OPEN_BOX();
?>
<div id="action_list">
<?php

echo"<input type=hidden id=statusInputan value=0 /><table>
     <tr valign=moiddle>
	<!--<td align=center style='width:100px;cursor:pointer;' onclick=newData()><img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>-->
	 <td align=center style='width:100px;cursor:pointer;' onclick=normalView()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
                    echo $_SESSION['lang']['searchdata'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>";
                    echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
                    echo"<button class=mybutton onclick=loadData()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>";
echo"<td><fieldset><legend>".$_SESSION['lang']['list']."</legend>";
$sDtPt="select distinct kodept from ".$dbname.".log_lpbht where gudangx='".$_SESSION['empl']['lokasitugas']."'order by kodept asc";
$qDtPt=mysql_query($sDtPt) or die(mysql_error($conn));
while($rDtPt=mysql_fetch_assoc($qDtPt))
{
    echo "[ <a href=# onclick=newData('".$rDtPt['kodept']."')>".$rDtPt['kodept']."</a> ]";
}
echo"</fieldset></td>";
echo"</tr>
	 </table>"; 
?>
</div>
<?php
CLOSE_BOX();
?>
<div id="dataListMnc">
    <?php
OPEN_BOX();
echo"<fieldset style=width:100%;><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
           <td>".$_SESSION['lang']['suratjalan']."</td>
           <td>".$_SESSION['lang']['status']."</td>
	   <td>".$_SESSION['lang']['expeditor']."</td>
	   <td>".$_SESSION['lang']['tgl_kirim']."</td>
	   <td>Package Amount</td>
	   <td>".$_SESSION['lang']['kepada']."</td>
	   <td>".$_SESSION['lang']['lokasipenerimaan']."</td>
           <td>".$_SESSION['lang']['modatransportasi']."</td>
           <td>".$_SESSION['lang']['berat']."</td>
           <td>".$_SESSION['lang']['biaya']."</td>
	   <td>Action</td>
	  </tr>
	 </thead>
	 <tbody id=container>";
	 echo"<script>loadData()</script>";
    
echo"</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";
CLOSE_BOX();
?>
</div>
    

<!--form list data babp-->
<div id="vwListPenerimaan" style="display: none">
<?php
    OPEN_BOX();
?>
<div id="listPenerimaan">

    

</div>
    <?php
CLOSE_BOX();
?>
</div>


<!--form inputan-->

<div id="formInputanDt" style="display:none">
<?php
OPEN_BOX();?>
    <div id="formInputan">
    </div>
    <?php
CLOSE_BOX();
?>
</div>

<?php
echo close_body();
?>