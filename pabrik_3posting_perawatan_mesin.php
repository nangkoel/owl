<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['postingPerawatanMesin']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script type="application/javascript" src="js/pabrik_3posting_perawatan_mesin.js"></script>

<input type="hidden" id="proses" name="proses" value="insert"  />
<div id="action_list">
<?php
	 $arrPil=array($_SESSION['lang']['belumposting'],$_SESSION['lang']['posting']);
	 foreach($arrPil as $id => $ky)
	 {
		 $optPost.="<option value=".$id.">".$ky."</option>";
	 }

echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>

	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['notransaksi'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>&nbsp;";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />&nbsp;";
			echo $_SESSION['lang']['posting'].":<select id=statusPosting name=statusPosting><option value=''>".$_SESSION['lang']['all']."".$optPost."</option></select>&nbsp;";
			echo"<button class=mybutton onclick=cariTransaksi()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 

?>
</div>
<?php
CLOSE_BOX();
?>
<div id="list_ganti">
<?php OPEN_BOX()?>
<fieldset>
<legend><?php echo $_SESSION['lang']['list']?></legend>
<table cellspacing="1" border="0" class="sortable">
<thead>
<tr class="rowheader">
<td>No.</td>
<td><?php echo $_SESSION['lang']['notransaksi']?></td>
<td><?php echo $_SESSION['lang']['tanggal']?></td>
<td><?php echo $_SESSION['lang']['shift']?></td>
<td><?php echo $_SESSION['lang']['statasiun']?></td>
<td><?php echo $_SESSION['lang']['mesin']?></td>
<td><?php echo $_SESSION['lang']['jammulai']?></td>
<td><?php echo $_SESSION['lang']['jamselesai']?></td>
<td>Action</td>
</tr>
</thead>
<tbody id="contain">
<script>loadNData()</script>
<?php
		/*$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".pabrik_rawatmesinht   order by `notransaksi` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}*/
		
		//$slvhc="select * from ".$dbname.".pabrik_rawatmesinht where statPost='0'  order by `notransaksi` desc limit ".$offset.",".$limit."";
	/*	$slvhc="select * from ".$dbname.".pabrik_rawatmesinht where statPost='0'  order by `notransaksi` desc";
		$qlvhc=mysql_query($slvhc) or die(mysql_error());
		$user_online=$_SESSION['standard']['userid'];
		while($rlvhc=mysql_fetch_assoc($qlvhc))
		{
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$rlvhc['notransaksi']."</td>
		<td>".tanggalnormal($rlvhc['tanggal'])."</td>
		<td>".$rlvhc['shift']."</td>
		<td>".$rlvhc['statasiun']."</td>
		<td>".$rlvhc['mesin']."</td>
		<td>".tanggalnormald($rlvhc['jammulai'])."</td>
		<td>".tanggalnormald($rlvhc['jamselesai'])."</td>";
		if($rlvhc['updateby']!=$userOnline)
		{
		echo"<td><img src=images/skyblue/posting.png class=resicon  title='Edit' onclick=\"postThis('".$rlvhc['notransaksi']."');\">
		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\"></td>";
		 } else {
			 echo"
		<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event);\"></td>";}
		 }*/
		
?>
</tbody>
</table>
</fieldset>
<?php CLOSE_BOX()?>
</div>


<?php 
echo close_body();
?>