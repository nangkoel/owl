<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
 
$kdbrg=$_POST['kdbrg'];
$idcom=$_POST['idcom'];
 $oldId=$_POST['oldId'];
 $oldBrgId=$_POST['oldBrgId'];
$method=$_POST['method'];


switch($method)
{
	case 'insert':
		$whrd="kodebarang='".$kdbrg."'";
	    $optCek=makeOption($dbname,'keu_5piutangbrgkary','kodebarang,id',$whrd);
	    if(!empty($optCek[$kdbrg])){
	    	$whrd="id='".$optCek[$kdbrg]."'";
			$optNmCom=makeOption($dbname,'sdm_ho_component','id,name',$whrd);
	    	exit("warning: Kode barang sudah terinput sebelumnya pada componen :".$optNmCom[$optCek[$kdbrg]]);
	    }
	    $whrd="id='".$idcom."'";
	    $optCek=makeOption($dbname,'sdm_ho_component','id,pinjamanid',$whrd);
	    if($optCek[$idcom]==0){
	    	$sUpdate="update ".$dbname.".sdm_ho_component set pinjamanid=1 where id='".$idcom."'";#cek jika pinjamanid belum = 1
	    	if(!mysql_query($sUpdate)){
	    		echo " Gagal,".addslashes(mysql_error($conn))."___".$sUpdate;
	    	}else{
	    		$i="insert into ".$dbname.".keu_5piutangbrgkary values ('".$idcom."','".$kdbrg."')";
					//exit("Error.$sDel2");
					if(mysql_query($i))
					echo"";
					else
					echo " Gagal,".addslashes(mysql_error($conn));		
	    	}
	    }else{
	    	$i="insert into ".$dbname.".keu_5piutangbrgkary values ('".$idcom."','".$kdbrg."')";
			//exit("Error.$sDel2");
			if(mysql_query($i))
			echo"";
			else
			echo " Gagal,".addslashes(mysql_error($conn));
	    }
	    
		
	break;
	
	case 'update':
	$i="update ".$dbname.".keu_5piutangbrgkary set kodebarang='".$kdbrg."',id='".$idcom."'
		 where id='".$oldId."' and kodebarang='".$oldBrgId."' ";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
		
case'loadData':
	echo"
	<div id=container>
		<table class=sortable cellspacing=1 border=0>
	     <thead>
			 <tr class=rowheader>
				 <td align=center>".$_SESSION['lang']['nourut']."</td>
				 <td align=center>".$_SESSION['lang']['nama']."</td>
				 <td align=center>".$_SESSION['lang']['kodebarang']."</td>
				 <td align=center>".$_SESSION['lang']['namabarang']."</td>
				 <td align=center>".$_SESSION['lang']['action']."</td>
			 </tr>
		</thead>
		<tbody>";
		 
		
		
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".keu_5piutangbrgkary  ";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".keu_5piutangbrgkary order by id  limit ".$offset.",".$limit."";
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
		    $whrd="id='".$d['id']."'";
			$optNmCom=makeOption($dbname,'sdm_ho_component','id,name',$whrd);
		 	$whrdnmbrg="kodebarang='".$d['kodebarang']."'";
			$optNmBrg=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',$whrdnmbrg);
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td align=left>".$optNmCom[$d['id']]."</td>";
			echo "<td align=right>".$d['kodebarang']."</td>";
			echo "<td align=left>".$optNmBrg[$d['kodebarang']]."</td>";
			 
			 
			echo "<td align=center>
			<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"edit('".$d['id']."','".$d['kodebarang']."');\">
			<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['id']."','".$d['kodebarang']."');\"></td>";
			echo "</tr>";
		}
		echo"
		<tr class=rowheader><td colspan=18 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=loadData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=loadData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		echo"</tbody></table>";
    break;

	case 'delete':
	//exit("Error:hahaha");
		$i="delete from ".$dbname.".keu_5piutangbrgkary where id='".$idcom."' and kodebarang='".$kdbrg."'";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
}
?>
