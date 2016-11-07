<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');
                                    
                                  
///$arr="##thnBudget##kdGol##ungSaku##ungMkn##htel##method";

$method=$_POST['method'];
$thnBudget=$_POST['thnBudget'];
$kdGol=$_POST['kdGol'];
$ungSaku=$_POST['ungSaku'];
$ungMkn=$_POST['ungMkn'];
$htel=$_POST['htel'];
$optGol=makeOption($dbname, "sdm_5golongan", "kodegolongan,namagolongan");

$where=" tahunbudget='".$thnBudget."' and golongan='".$kdGol."' ";

	switch($method)
	{
		case'insert':
		if(($thnBudget=='')||($kdGol==''))
		{
			echo"warning:Field tidak boleh kosong";
			exit();
		}
		$sCek="select tahunbudget from ".$dbname.".sdm_5sakupjd where ".$where."";
		$qCek=mysql_query($sCek) or die(mysql_error($conn));
		$rCek=mysql_num_rows($qCek);
		if($rCek>0)
		{
			echo"warning:Data sudah ada";
			exit();
		}
		else
		{
                    $ungSaku==''?$ungSaku=0:$ungSaku=$ungSaku;
                    $ungMkn==''?$ungMkn=0:$ungMkn=$ungMkn;
                    $htel==''?$htel=0:$htel=$htel;

		
                    $sIns="insert into ".$dbname.".sdm_5sakupjd (tahunbudget, golongan, uangsaku, uangmakan, hotel) values 
                        ('".$thnBudget."','".$kdGol."','".$ungSaku."','".$ungMkn."','".$htel."')";
                    if(!mysql_query($sIns))
                    {
                            echo"Gagal".mysql_error($conn);
                    }

		}
		break;
		case'loadData':
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
		$no=0;	 
                if($thnBudget!='')
                {
                    $addKond.=" and tahunbudget='".$thnBudget."'";
                }
                if($kdGol!='')
                {
                    $addKond.=" and golongan='".$kdGol."'";
                }
                
                $sql2="SELECT count(*) as jmlhrow FROM ".$dbname.".sdm_5sakupjd where tahunbudget!='' ".$addKond." order by tahunbudget desc ";	 

                //echo "warning:".$strx;exit();
                $query2=mysql_query($sql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }
                if($jlhbrs!=0)
                {
		$str="select * from ".$dbname.".sdm_5sakupjd where tahunbudget!='' ".$addKond." order by tahunbudget desc limit ".$offset.",".$limit."";
		$res=mysql_query($str);
		while($bar=mysql_fetch_assoc($res))
		{
		$no+=1;	
		echo"<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$bar['tahunbudget']."</td>
		<td>".$optGol[$bar['golongan']]."</td>
		<td align=right>".number_format($bar['uangsaku'],2)."</td>
                <td align=right>".number_format($bar['uangmakan'],2)."</td>
		<td align=right>".number_format($bar['hotel'],2)."</td>                
		<td>
			  <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['tahunbudget']."','".$bar['golongan']."');\"> 
			  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['tahunbudget']."','".$bar['golongan']."');\">
		  </td>
		</tr>";	
		}     
                 echo"
                <tr><td colspan=8 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariPage(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariPage(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
                }
                else
                {
                    echo"<tr class=rowcontent><td colspan=8>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
		break;
		case'update':
		if(($thnBudget=='')||($kdGol==''))
		{
			echo"warning:Field tidak boleh kosong";
			exit();
		}
		else
		{
                     $ungSaku==''?$ungSaku=0:$ungSaku=$ungSaku;
                    $ungMkn==''?$ungMkn=0:$ungMkn=$ungMkn;
                    $htel==''?$htel=0:$htel=$htel;
                    
			$sUpd="update ".$dbname.".sdm_5sakupjd set `uangsaku`='".$ungSaku."',`uangmakan`='".$ungMkn."',`hotel`='".$htel."' where ".$where."";
			if(!mysql_query($sUpd))
			{
				echo"Gagal".mysql_error($conn);
			}
		}
		break;
		case'delData':
		$sDel="delete from ".$dbname.".sdm_5sakupjd  where ".$where."";
		if(!mysql_query($sDel))
		{
			echo"Gagal".mysql_error($conn);
		}
		break;
		case'getData':
		$sDt="select * from ".$dbname.".sdm_5sakupjd where ".$where."";
		$qDt=mysql_query($sDt) or die(mysql_error($conn));
		$rDet=mysql_fetch_assoc($qDt);
                //tahunbudget, golongan, tujuan, ticket, taxiboat, airporttax, visa, bylain
		echo $rDet['tahunbudget']."###".$rDet['golongan']."###".$rDet['uangsaku']."###".$rDet['uangmakan']."###".$rDet['hotel'];
		break;
		default:
		break;
	}
?>