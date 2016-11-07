<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$param=$_POST;
$method=$_POST['method'];
$periodeAkutansi=$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];

switch($method)
{
	case 'insert':
		$i="insert into ".$dbname.".keu_5kursbulanan (periode,matauang,kurs,updateby,lastupdate)
		values ('".$param['periodeDt']."','".$param['mtUang']."','".$param['krsDt']."','".$_SESSION['standard']['userid']."','".date("Y-m-d H:i:s")."')";
		//exit("Error.$sDel2");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	case 'update':
		$i="update ".$dbname.".keu_5kursbulanan set kurs='".$param['krsDt']."',matauang='".$param['mtUang']."',updateby='".$_SESSION['standard']['userid']."',lastupdate='".date("Y-m-d H:i:s")."'
		 where periode='".$param['periodeDtold']."' and matauang='".$param['mtUangold']."' ";
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
				 <td align=center>No.</td>
                                 <td align=center>".$_SESSION['lang']['periode']."</td>
				 <td align=center>".$_SESSION['lang']['matauang']."</td>
				 <td align=center>".$_SESSION['lang']['kurs']."</td>
				 <td align=center>".$_SESSION['lang']['action']."</td>
			 </tr>
		</thead>
		<tbody>";
		if($_POST['periode']!=''){
                    $whr.=" and periode like '%".$_POST['periode']."%'";
                }
                if($_POST['mtUang']!=''){
                    $whr.=" and matauang='".$_POST['mtUang']."'";
                }
		$limit=15;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".keu_5kursbulanan  
                       where periode!='' ".$whr." order by periode desc ";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".keu_5kursbulanan 
                    where periode!='' ".$whr." order by periode desc limit ".$offset.",".$limit."";
		
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n)){
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td>".$d['periode']."</td>";
			echo "<td>".$d['matauang']."</td>";
			echo "<td align=right>".number_format($d['kurs'],2)."</td>";
                        if($d['periode']==$periodeAkutansi){
			echo "<td align=center>
			<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"edit('".$d['periode']."','".$d['matauang']."','".$d['kurs']."');\">
			<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['periode']."','".$d['matauang']."');\"></td>";
                        }else{
                            echo"<td colspan=2>&nbsp</td>";
                        }
			echo "</tr>";
		}
		echo"</tbody>";
                echo"<tfoot>
		<tr class=rowheader><td colspan=5 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=loadData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=loadData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr></tfoot></table>";
    break;

	case 'delete':
	//exit("Error:hahaha");
		$i="delete from ".$dbname.".keu_5kursbulanan where periode='".$param['periodeDt']."' and matauang='".$param['mtUang']."'";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

}
?>
