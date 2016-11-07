<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$regional=$_POST['regional'];
$kdkegiatan=$_POST['kdkegiatan'];
$rp=$_POST['rp'];
$insen=$_POST['insen'];
$konversi=$_POST['konversi'];
$method=$_POST['method'];


$nmkonv=array('0'=>$_SESSION['lang']['no'],'1'=>$_SESSION['lang']['yes']);
$nmid=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$nmen=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan1');
?>

<?php
switch($method)
{
	case 'insert':
		$i="insert into ".$dbname.".kebun_5psatuan (regional,kodekegiatan,rupiah,insentif,updateby,konversi)
		values ('".$regional."','".$kdkegiatan."','".$rp."','".$insen."','".$_SESSION['standard']['userid']."','".$konversi."')";
		//exit("Error.$sDel2");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	case 'update':
	//exit("Error:ASD");
		
		$i="update ".$dbname.".kebun_5psatuan set rupiah='".$rp."',insentif='".$insen."',updateby='".$_SESSION['standard']['userid']."',konversi='".$konversi."'
		 where regional='".$regional."' and kodekegiatan='".$kdkegiatan."' ";
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
				 <td align=center>".$_SESSION['lang']['regional']."</td>
				 <td align=center>".$_SESSION['lang']['kodekegiatan']."</td>
				 <td align=center>".$_SESSION['lang']['namakegiatan']."</td>
				 <td align=center>".$_SESSION['lang']['biaya']."</td>
				 <td align=center>".$_SESSION['lang']['insentif']."</td>
				 <td align=center>".$_SESSION['lang']['konversi']."</td>
				 <td align=center>".$_SESSION['lang']['action']."</td>
			 </tr>
		</thead>
		<tbody>";
		if($_POST['kdkegiatan']!=''){
                    $whr.=" and (kodekegiatan like '%".$_POST['kdkegiatan']."%' or kodekegiatan in (select distinct kodekegiatan from ".$dbname.".setup_kegiatan where namakegiatan like '%".$_POST['kdkegiatan']."%'))";
                }
                if($_POST['rp']!=''){
                    $whr.=" and rupiah<='".$_POST['rp']."'";
                }
                if($_POST['insen']!=''){
                    $whr.=" and insentif<='".$_POST['insen']."'";
                }
                if($_POST['konversi']!=''){
                    $whr.=" and konversi='".$_POST['konversi']."'";
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
		
		


		
		$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_5psatuan  
                       where regional='".$_SESSION['empl']['regional']."' ".$whr." order by kodekegiatan asc ";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".kebun_5psatuan 
                    where regional='".$_SESSION['empl']['regional']."' ".$whr." 
                     order by kodekegiatan asc  limit ".$offset.",".$limit."";
		
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
	
		/*$i="select * from ".$dbname.".kebun_5psatuan where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		//echo $i;
		$n=mysql_query($i) or die(mysql_error());
		while($d=mysql_fetch_assoc($n))
		{*/
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td align=left>".$d['regional']."</td>";
			echo "<td align=right>".$d['kodekegiatan']."</td>";
			if($_SESSION['language']=='ID')
				echo "<td align=left>".$nmid[$d['kodekegiatan']]."</td>";
			else
				echo "<td align=left>".$nmen[$d['kodekegiatan']]."</td>";
			echo "<td align=right>".number_format($d['rupiah'],2)."</td>";
			echo "<td align=right>".number_format($d['insentif'],2)."</td>";
			echo "<td align=right>".$nmkonv[$d['konversi']]."</td>";
			echo "<td align=center>
			<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"edit('".$d['regional']."','".$d['kodekegiatan']."','".$d['rupiah']."','".$d['insentif']."','".$d['konversi']."');\">
			<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['regional']."','".$d['kodekegiatan']."');\"></td>";
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
		$i="delete from ".$dbname.".kebun_5psatuan where kodeorg='".$kdorg."' and kodekegiatan='".$kdkegiatan."'";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
        case'upGradeData':
            
            if($_POST['noakun']!=''){
                $whr=" and kodekegiatan like '".$_POST['noakun']."%'";
            }
            if($_POST['prsnrpCr']!=''){
                $dert="'".($rData['rupiah']*$_POST['prsnrpCr']/100)."'";
            }
            if($_POST['prsninsenCr']!=''){
                $derte="";
            }
            $sData="select * from ".$dbname.".kebun_5psatuan where regional='".$_SESSION['empl']['regional']."' ".$whr."";
            //exit("error:masuk___".$sData);
            $qData=mysql_query($sData) or die(mysql_error($conn));
            if(mysql_num_rows($qData)==0){
                exit("error:Data Kosong");
            }
            while($rData=  mysql_fetch_assoc($qData)){
                @$rupiah=$rData['rupiah']+($rData['rupiah']*$_POST['prsnrpCr']/100);
                @$insentif=$rData['insentif']+($rData['insentif']*$_POST['prsninsenCr']/100);
                $supdate="update ".$dbname."kebun_5psatuan set rupiah='".$rupiah."',insentif='".$insentif."',updateby='".$_SESSION['standard']['userid']."'
                          where 
                          regional='".$_SESSION['empl']['regional']."' ".$whr." ";
                //exit("error:".$supdate);
                if(!mysql_query($supdate)){
                    exit("error: db gagal ".mysql_error($conn)."__".$supdate);
                }
            }
        break;

}
?>
