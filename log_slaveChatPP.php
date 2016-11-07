<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
?>
    <script language=JavaScript1.2 src=js/log_slaveChatPP.js></script>
	<script language=JavaScript1.2 src=js/log_2riwayatPP_addn.js></script>
	<link rel=stylesheet type=text/css href=style/generic.css>

<?php

   $nopp		=$_GET['nopp'];
   $kodebarang	=$_GET['kodebarang'];
 //com from its page
 if(isset($_POST['kodebarang'])){
   $nopp		=$_POST['nopp'];
   $kodebarang	=$_POST['kodebarang'];
   $pesan       =$_POST['pesan'];
   $karyawanid  =$_POST['empl']['userid'];
  
   $str= "insert into ".$dbname.".log_pp_chat (`nopp`,`karyawanid`,
          `pesan`,`kodebarang`)
		  values('".$nopp."',".$karyawanid.",'".$pesan."','".$kodebarang."')";
   if($res=mysql_query($str))
   {
   	
   }
   else
   {
   	 echo " Error: ".addslashes(mysql_error($conn));
   }		  
  
 }
 
 echo "<div id=container style='height:280px; overflow:scroll'>
       <table class=sortable cellspacing=1 border=0 width=100%>
        <tr>
		  <td>From</td>
		  <td>Time</td>
		  <td>Messages</td>
		</tr>
	   ";
   $str="select a.*,b.namauser from ".$dbname.".log_pp_chat a left join ".$dbname.".user b
         on a.karyawanid=b.karyawanid
         where a.kodebarang='".$kodebarang."' and a.nopp='".$nopp."' order by tanggal";	 
   $res=mysql_query($str);

   $no=0;
   while($bar=mysql_fetch_object($res))
   {
   	 $no+=1;
	 if($no%2==0)
	 {
	 	$ct="style='background-color:#FFFFFF'";
	 }
	 else
	 {
	 	$ct="style='background-color:#E8F2FE'";
	 }
	 echo"<tr>
	        <td ".$ct.">".$bar->namauser."</td>
			<td ".$ct.">".$bar->tanggal."</td>
			<td ".$ct.">".$bar->pesan."</td>
	      </tr>";
   }
  echo"</table></div>";
  echo"Messages:<br>";
  echo"<input type=hidden id=kodebarang value='".$kodebarang."'>
       <input type=hidden id=nopp value='".$nopp."'>
       <textarea id=pesan cols=60 rows=2 onkeypress=\"return tanpa_kutip(event);\"></textarea>
       <br><button class=mybutton onclick=\"savePPChat('".$nopp."','".$kodebarang."');\">".$_SESSION['lang']['chat']."</button>
	   "; 	
 echo"<div id='progress' style='display:none;border:orange solid 1px;width:150px;position:fixed;right:20px;top:65px;color:#ff0000;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>
Please wait.....! <br>
<img src='images/progress.gif'>
</div>";	
?>
