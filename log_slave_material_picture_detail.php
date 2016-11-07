<?php
require_once('master_validation.php');
require_once('config/connection.php');

  $kodebarang =$_GET['kodebarang'];
  $str="select a.*,b.* from ".$dbname.".log_5masterbarang a
        left join ".$dbname.".log_5photobarang b 
		on a.kodebarang=b.kodebarang
       where a.kodebarang='".$kodebarang."'";
  $depan='';
  $samping='';
  $atas='';
  $spesifikasi='';
  
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {
   	  $namabarang=$bar->namabarang;
	  $satuan=$bar->satuan;
	  $depan	=$bar->depan;
	  $samping	=$bar->samping;
	  $atas		=$bar->atas;
	  $spesifikasi=$bar->spesifikasi; 	
  } 
  echo"<fieldset><legend>[".$kodebarang."]".$namabarang."(".$satuan.")</legend>";
       echo"<table>
	        <tr><td>Spec</td><td>".$spesifikasi."</td></tr>
			<tr><td>Pic1</td><td><img src='".$depan."' height=150px></td></tr>
			<tr><td>Pic2</td><td><img src='".$samping."' height=150px></td></tr>
			<tr><td>Pic3</td><td><img src='".$atas."' height=150px></td></tr>
	        </table>";
  echo"</fieldset>";
?>
