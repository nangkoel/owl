<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

   $karyawanid  =$_SESSION['standard']['userid'];
   $nopp		=$_POST['nopp'];
   $kodebarang	=$_POST['kodebarang'];   
   
 if(isset($_POST['pesan'])){

   $pesan       =$_POST['pesan'];
  
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

  echo "<table class=sortable cellspacing=1 border=0 width=100%>
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
  echo"</table>"; 
 ?>
