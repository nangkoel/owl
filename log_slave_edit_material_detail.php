<?php
require_once('master_validation.php');
require_once('config/connection.php');

  $kodebarang =$_GET['kodebarang'];
  $str="select * from ".$dbname.".log_5photobarang where kodebarang='".$jodebarang."'";
  $depan='';
  $samping='';
  $atas='';
  $spesifikasi='';
  
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {
   	  $depan	=$bar->depan;
	  $samping	=$bar->samping;
	  $atas		=$bar->atas;
	  $spesifikasi=$bar->spesifikasi; 	
  } 
  
  echo"<fieldset  style='width:430px;'><legend>Update Detail</legend>
       <form id='".$kodebarang."' name=photobarang method=post enctype='multipart/form-data'>
	   	 
       <table cellapacing=1 border=0>
	   <tr> 
	   		<td>Spec</td>
	   		<td><textarea name=spec id=spec cols=20 rows=3 onkeypress=\"return parent.tanpa_kutip(event)\">".$spesifikasi."</textarea></td>
	   </tr>		
	   <tr>
	   		<td>Tampak depan</td>
	   		<td>
			   <input type=hidden name=MAX_FILE_SIZE value=100000>
			   <input type=file name=file[] size35>
			</td>
	   </tr>				
	   		<td>Tampak Samping</td>
	   		<td>
			   <input type=file name=file[] size35>
			</td>
	   </tr>	
	   		<td>Tampak Atas</td>
	   		<td>
			   <input type=file name=file[] size35>
			   <input type=hidden name=kodebarangx id=kodebarangx value='".$kodebarang."'>
			</td>						
	   </tr>
       </table>
	   
	   </form>
	   <center>
	   1 File(s) Max 100 Kb.<br>
	   <button onclick=parent.simpanPhoto()>Save</button>
	   </center>
	   </fieldset>";
?>
