<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');



?>	

<?php		

$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];
$per=$_POST['per'];
$kom=$_POST['kom'];
$kar=$_POST['kar'];
$jum=$_POST['jum'];
$org=$_POST['org'];
//$=$_POST[''];


$txtBarang=$_POST['txtBarang'];
$perSch=$_POST['perSch'];
$komSch=$_POST['komSch'];

$nmKom=makeOption($dbname,'sdm_ho_component','id,name');
$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optLok=makeOption($dbname,'datakaryawan','karyawanid,lokasitugas',"karyawanid='".$kar."'");

?>

<?php
switch($method)
{		
	case'saveHeader':
		$i="INSERT INTO ".$dbname.".`sdm_pendapatanlainht` (`kodeorg`,`periodegaji`, `idkomponen`, `updateby`)	
		values ('".$org."','".$per."','".$kom."','".$_SESSION['standard']['userid']."')";
		if(mysql_query($i))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	

	

	case'saveDetail':

		$i="INSERT INTO ".$dbname.".`sdm_pendapatanlaindt` (`kodeorg`, `periodegaji`, `karyawanid`, `idkomponen`, `jumlah`, `pengali`, `updateby`)
		values ('".$optLok[$kar]."','".$per."','".$kar."','".$kom."','".$jum."','1','".$_SESSION['standard']['userid']."')";
	//exit("Error:$i");	
		if(mysql_query($i))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;	
	
	
	

	#####LOAD DETAIL DATA	
	case 'loadDetail';
			echo"<table class=sortable cellspacing=1 border=0>
			 <thead>
				 <tr class=rowheader>
					<td>".$_SESSION['lang']['nourut']."</td>
					<td align=center>".$_SESSION['lang']['nik']."</td>
					<td align=center style=\"width:225px;\">".$_SESSION['lang']['namakaryawan']."</td>
					
					<td align=center style=\"width:125px;\">".$_SESSION['lang']['lokasitugas']."</td>
					<td align=center style=\"width:25px;\">".$_SESSION['lang']['jumlah']."</td>
					<td>*</td>
				 </tr>
			</thead>
			<tbody></fieldset>";
		$no=0;
                
                //print_r($_SESSION['empl']);
                //if($org)
                if($_SESSION['empl']['regional']=='SULAWESI')
                {
                   if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
                   {
                        $orgSort="and kodeorg in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
                   }
                   else 
                   {
                        $orgSort="and kodeorg='".$org."' ";
                   } 
                }
                else
                {
                   $orgSort="and kodeorg='".$org."' ";
                }
                
                
		$a="select * from ".$dbname.".sdm_pendapatanlaindt where idkomponen='".$kom."' and periodegaji='".$per."' ".$orgSort." ";
               //  echo $a;
//exit("Error:$a");
		$b=mysql_query($a) or die(mysql_error($conn));
		while($c=mysql_fetch_assoc($b))
		{
			$optLokD=makeOption($dbname,'datakaryawan','karyawanid,lokasitugas',"karyawanid='".$c['karyawanid']."'");
			$nik=makeOption($dbname,'datakaryawan','karyawanid,nik',"karyawanid='".$c['karyawanid']."'");
			$no+=1;
			echo"<tr class=rowcontent>
					<td>".$no."</td>
					<td>".$nik[$c['karyawanid']]."</td>
					<td>".$nmKar[$c['karyawanid']]."</td>
					<td>".$nmOrg[$optLokD[$c['karyawanid']]]."</td>
					<td>".number_format($c['jumlah'])."</td>
					<td>
						<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"DelDetail('".$c['periodegaji']."','".$c['karyawanid']."','".$c['idkomponen']."');\" >					
					</td>
				</tr>";
				$tot+=$c['jumlah'];
		}		
		echo"
			<tr class=rowcontent>
				<td colspan=4><b>".$_SESSION['lang']['total']."</b></td>
				<td><b>".number_format($tot)."</b></td>
				<td></td>
			</tr>
			<tr>
				<td colspan=14 align=center>
					<button class=mybutton id=cancelDetail onclick=cancel()>".$_SESSION['lang']['selesai']."</button>
				</td>
			 </tr>";//<button class=mybutton id=editAll onclick=editAll()>".$_SESSION['lang']['edit']."</button>
		
		echo"</table>";
	break;	
	

	case'loadData':
       	

            if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
                $orgSort=" kodeorg in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
            } else {
                $orgSort=" kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            }

            
            
		if($perSch!='')
			$perSch="and periodegaji='".$perSch."'";
		else
			$perSch="";
                
			
                
		echo"
			<table class=sortable cellspacing=1 border=0>
			 <thead>
				 <tr class=rowheader>
					 <td align=center>".$_SESSION['lang']['nourut']."</td>
                                         <td align=center>".$_SESSION['lang']['kodeorg']."</td>
					 <td align=center>".$_SESSION['lang']['periodegaji']."</td>
					 <td align=center>".$_SESSION['lang']['jenis']."</td>
					  <td align=center>".$_SESSION['lang']['dibuat']."</td>
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
			$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_pendapatanlainht where ".$orgSort." ".$perSch." ";
			$query2=mysql_query($ql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
			$i="select * from ".$dbname.".sdm_pendapatanlainht where ".$orgSort." ".$perSch."  limit ".$offset.",".$limit."";
			
			//echo $i;
			$n=mysql_query($i) or die(mysql_error());
			$no=$maxdisplay;
			while($d=mysql_fetch_assoc($n))
			{
				$no+=1;
				echo "<tr class=rowcontent>";
				echo "<td align=center>".$no."</td>";
                                echo "<td align=left>".$d['kodeorg']."</td>";
				echo "<td align=left>".$d['periodegaji']."</td>";
				echo "<td align=left>".$nmKom[$d['idkomponen']]."</td>";
				echo "<td align=left>".$nmKar[$d['updateby']]."</td>";
				echo"<td align=center>
						<img src=images/application/application_edit.png  title='update' class=resicon  caption='Edit' onclick=\"edit('".$d['periodegaji']."','".$d['idkomponen']."','".$d['kodeorg']."');\">
						<img src=images/application/application_delete.png  title='delete' class=resicon caption='Delete' onclick=\"delHead('".$d['periodegaji']."','".$d['idkomponen']."','".$d['kodeorg']."');\">
						<img onclick=excel(event,'".$d['periodegaji']."','".$d['idkomponen']."','".$d['kodeorg']."') src=images/excel.jpg class=resicon title='MS.Excel'>
					</td>";
				echo "</tr>";
			}
			echo"
			<tr class=rowheader><td colspan=5 align=center>
			".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
			<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
			<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
			</td>
			</tr>";
			echo"</tbody></table>";
		break;
		
		
		case'delHead':
                    
                    
                        if($_SESSION['empl']['regional']=='SULAWESI')
                        {
                           if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
                           {
                                $orgSort="and kodeorg in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
                           }
                           else 
                           {
                                $orgSort="and kodeorg='".$org."' ";
                           } 
                        }
                        else
                        {
                           $orgSort="and kodeorg='".$org."' ";
                        }
                    
		
			$i="delete from ".$dbname.".sdm_pendapatanlainht where idkomponen='".$kom."' and periodegaji='".$per."' and kodeorg='".$org."'";
			if(mysql_query($i))
			{
                            if($_SESSION['empl']['regional']=='SULAWESI')
                            {
                                $x="delete from ".$dbname.".sdm_pendapatanlaindt where idkomponen='".$kom."' and periodegaji='".$per."' ".$orgSort;
                            }
                            else
                            {
                                $x="delete from ".$dbname.".sdm_pendapatanlaindt where idkomponen='".$kom."' and periodegaji='".$per."' ".$orgSort;
                            }
                            if(mysql_query($x))
                            {
                            }
                            else
                            echo " Gagal,".addslashes(mysql_error($conn));
			}
			else
			echo " Gagal,".addslashes(mysql_error($conn));
		
			
		break;
		

		
		
		case'deleteDetail':
			$i="delete from ".$dbname.".sdm_pendapatanlaindt where karyawanid='".$kar."' and kodeorg='".$optLok[$kar]."' and idkomponen='".$kom."' and periodegaji='".$per."'";
			if(mysql_query($i))
			echo"";
			else
			echo " Gagal,".addslashes(mysql_error($conn));
		break;
	
	
	
	
	default;
}
?>