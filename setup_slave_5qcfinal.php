<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

////$arr="##nmQc##wrnaId##maxDt##method";
$method=$_POST['method'];
$nmQc=$_POST['nmQc'];
$wrnaId=$_POST['wrnaId'];
$maxDt=$_POST['maxDt'];
$nmQcOld=$_POST['nmQcOld'];
 
	switch($method)
	{
		case'insert':
                    $sIns="insert into ".$dbname.".qc_5final (name, color, max)
                           values ('".$nmQc."','".$wrnaId."','".$maxDt."')";
                    if(!mysql_query($sIns))
                    {
                            echo"Gagal".mysql_error($conn);
                    }
		break;
		case'loadData':
		$no=0;	 
		$str="select * from ".$dbname.".qc_5final order by name asc";
		$res=mysql_query($str);
		while($bar=mysql_fetch_assoc($res))
		{
		echo"<tr class=rowcontent>
		<td>".$bar['name']."</td>
		<td>".$bar['color']."</td>
		<td>".$bar['max']."</td>
		<td align=center>
			  <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['name']."','".$bar['color']."','".$bar['max']."');\">
			  <!--<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['tipe']."','".$bar['id']."');\">-->
		  </td>
		
		</tr>";	
		}     
		break;
		case'updateData':
                    $sUpd="update ".$dbname.".qc_5final set `name`='".$nmQc."',`color`='".$wrnaId."',`max`='".$maxDt."'
                              where name='".$nmQcOld."'";
			if(!mysql_query($sUpd))
			{
				echo"Gagal".mysql_error($conn);
			}
		
		break;
		case'delData':
		$sDel="delete from ".$dbname.".setup_franco where id_franco='".$idFranco."'";
		if(!mysql_query($sDel))
		{
			echo"Gagal".mysql_error($conn);
		}
		break;
		case'getData':
		$sDt="select distinct id from ".$dbname.".qc_5parameter  order by id desc";
                //exit("Error:".$sDt);
		$qDt=mysql_query($sDt) or die(mysql_error($conn));
		$rDet=mysql_fetch_assoc($qDt);
		$dt=1+intval($rDet['id']);
                echo $dt;
		break;
		default:
		break;
	}
?>