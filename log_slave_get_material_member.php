<?php
require_once('master_validation.php');
require_once('config/connection.php');

	$kelompok	=$_POST['mayor'];
    $kodebarang =$_POST['kodebarang'];
	$namabarang =$_POST['namabarang'];
	$satuan     =$_POST['satuan'];
	$minstok    =$_POST['minstok'];
	$konversi   =$_POST['konversi'];
	$nokartu    =$_POST['nokartu'];
	$method	    =$_POST['method'];
	$strx='select 1=1';
	switch($method){
		case 'delete':
			$strx="delete from ".$dbname.".log_5masterbarang where kodebarang='".$kodebarang."' and kelompokbarang='".$kelompok."'";
		break;
		case 'update':
			$strx="update ".$dbname.".log_5masterbarang set 
			       namabarang='".trim($namabarang)."',
			       satuan='".$satuan."',minstok=".$minstok.",
				   nokartubin='".$nokartu."',
                                   konversi='".$konversi."'
				   where kelompokbarang='".$kelompok."' 
				   and kodebarang='".$kodebarang."'";
		break;	
		case 'insert':
			$strx="insert into ".$dbname.".log_5masterbarang(
			       kelompokbarang,kodebarang,namabarang,satuan,minstok,
				   nokartubin,konversi)
			values('".$kelompok."','".$kodebarang."','"
			         .trim($namabarang)."','".$satuan."',".$minstok.",
					 '".$nokartubin."',".$konversi.")";	   
		break;
		default:
        break;	
	}
  if(mysql_query($strx))
  {}	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	

// kamus spek        
$str="select kodebarang, spesifikasi from ".$dbname.".log_5photobarang
    where kodebarang like '".$kelompok."%'"; #tidak sama dengan laba/rugi berjalan 

//=================================================
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $spek[$bar->kodebarang]=$bar->spesifikasi;
}         
	
//if search text is passing then search the item on given group
$txtfind=trim($_POST['txtcari']);	
	if(isset($_POST['txtcari']) && $txtfind!='' && $kelompok!='All')
		$str="select * from ".$dbname.".log_5masterbarang where namabarang like '%".$txtfind."%' and kelompokbarang='".$kelompok."' order by namabarang";
	else if(isset($_POST['txtcari']) && $txtfind!=='' && $kelompok=='All')
		$str="select * from ".$dbname.".log_5masterbarang where namabarang like '%".$txtfind."%' order by namabarang";
	else
	    $str="select * from ".$dbname.".log_5masterbarang where kelompokbarang='".$kelompok."' order by namabarang";
	
	$res=mysql_query($str);
	$no=0;
	while($bar=mysql_fetch_object($res))
	{
	  $stru="select * from ".$dbname.".log_5photobarang where kodebarang='".$bar->kodebarang."'";
	  if(mysql_num_rows(mysql_query($stru))>0)
	  {
	  	$adx="<img src=images/zoom.png class=resicon height=16px title='View detail'  onclick=viewDetailbarang('".$bar->kodebarang."',event)> <img src=images/tool.png class=resicon height=16px title='Edit Detail'  onclick=editDetailbarang('".$bar->kodebarang."',event)>";
	  }
	  else
	  {
	  	$adx="<img src=images/tool.png class=resicon height=16px title='Edit Detail' onclick=editDetailbarang('".$bar->kodebarang."',event)>";
	  }  
	  $no+=1;
          $isikon="NO";
          if($bar->konversi==1){
              $isikon="YES";
          }
		echo"<tr class=rowcontent>
		  <td>".$no."</td>
		  <td>".$bar->kelompokbarang."</td>
		  <td>".$bar->kodebarang."</td>
		  <td>".$bar->namabarang."</td>
		  <td>".$bar->satuan."</td>
		  <td>".$spek[$bar->kodebarang]."</td>
		  <td align=right>".$bar->minstok."</td>
		  <td>".$bar->nokartubin."</td>
		  <td>".$isikon."</td>
		  <td align=center><input type=checkbox id='br".$bar->kodebarang."' value='".$bar->kodebarang."' ".($bar->inactive==0?"":" checked")." onclick=setInactive(this.value);></td>
		  <td align=center>".$adx."</td>
		  <td>
		      <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kelompokbarang."','".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."','".$bar->minstok."','".$bar->nokartubin."','".$bar->konversi."');\"> 
			  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delBarang('".$bar->kodebarang."','".$bar->kelompokbarang."');\">
		  </td>
		  </tr>";
	}

?>
