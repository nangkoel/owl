<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

//$arr="##tipeDt##maxData##nilData##method";
$method=$_POST['method'];
$tipeDt=$_POST['tipeDt'];
$maxData=$_POST['maxData'];
$nilData=$_POST['nilData'];
$idData=$_POST['idData2'];
$optTipe=makeOption($dbname, 'qc_5parameter', 'id,nama');
$optParam=makeOption($dbname, 'qc_5parameter', 'id,tipe');
$optSat=makeOption($dbname, 'qc_5parameter', 'id,satuan');
 
	switch($method)
	{
		case'insert':
                    $sIns="insert into ".$dbname.".qc_5nilai (id, max, nilai,updateby)
                          values ('".$tipeDt."','".$maxData."','".$nilData."','".$_SESSION['standard']['userid']."')";
                    if(!mysql_query($sIns))
                    {
                            echo"Gagal".mysql_error($conn);
                    }
		break;
		case'loadData':
		$no=0;	 
		$str="select * from ".$dbname.".qc_5nilai order by id asc";
		$res=mysql_query($str);
                $row=mysql_num_rows($res);
                if($row==0)
                {
                    echo"<tr class=rowcontent><td colspan=5>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
                else
                {
                    while($bar=mysql_fetch_assoc($res))
                    {
                    echo"<tr class=rowcontent>
                    <td>".$optParam[$bar['id']]."-".$optTipe[$bar['id']]." (".$optSat[$bar['id']].")</td>
                    <td align=right>".$bar['max']."</td>
                    <td align=right>".$bar['nilai']."</td>
                    <td align=center>
                              <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['id']."','".$bar['max']."','".$bar['nilai']."');\">
                      </td>
                    </tr>";
                    }
                }
		break;
		case'updateData':
	
			$sUpd="update ".$dbname.".qc_5nilai set `max`='".$maxData."',`nilai`='".$nilData."',updateby='".$_SESSION['standard']['userid']."'
                              where id='".$tipeDt."' and  max='".$idData."'";
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