<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

if(isset($_POST['proses'])!='')
{
	$proses=$_POST['proses'];
}
elseif(isset($_GET['proses'])!='')
{
	$proses=$_GET['proses'];
}

$idRemote=$_POST['idRemote'];
//$arr="##dbnm##prt##pswrd##ipAdd##period##kdBrg##usrName";
$dbnm=$_POST['dbnm'];
$prt=$_POST['prt'];
$pswrd=$_POST['pswrd'];
$ipAdd=$_POST['ipAdd'];
$usrName=$_POST['usrName'];
$lksiServer=$_POST['lksiServer'];
$nmTable=$_POST['nmTable'];
$idCustomer =$_POST['idCustomer'];
$kdTimbangan=$_POST['kdTimbangan'];
$nmCust=$_POST['nmCust'];
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
if($nmTable=='mssipb')
{
    if($proses!='uploadData2')
    {
    $proses="getDataSipb";
    }
}
$kntrk=$_POST['kntrk'];
$nosipb=$_POST['nosipb'];
$tglsibp=$_POST['tglsibp'];
$kdbrg=$_POST['kdbrg'];
$trpcode=$_POST['trpcode'];
$trpname=$_POST['trpname'];
$ketdt=$_POST['ketdt'];

switch($proses)
{
	case'preview':
	//echo"warning:masuk";
/*	$dbserver='192.168.1.204';
	//$dbserver='localhost';
	$dbport  ='3306';
	$dbname  ='owl';
	$uname   ='root';
	$passwd  ='dbdev';
	//$passwd  ='root';*/
	//echo"warning:".$ipAdd;
	//exit();127.0.0.1
///	$ipAdd='192.168.1.204';
	if($lksiServer=='')
	{
		echo"warning:Lokasi Harus Di Isi";
		exit();
	}
	$arr="##dbnm##prt##pswrd##ipAdd##usrName##lksiServer##nmTable";
	@$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
	//$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd);
	$sColom="SHOW COLUMNS FROM ".$dbnm.".".$nmTable."";
	$qColom=mysql_query($sColom,$corn) or die(mysql_error());
	$i = 0;
	$tColom=array();
	$tmpCol ="";
	while($rColom=mysql_fetch_assoc($qColom))
	{
		
		//print_r($rColom);

		$tColom[$i]=$rColom['Field'];
		$i++;
	}
	$a=0;
	foreach($tColom as $dt =>$isi)
	{
		if($tmpCol=="") {
			$tmpCol.=$isi;
		} else {
			$tmpCol.=",".$isi;
		}
		/*	print"<pre>";
				print_r($isi);
				print"</pre>";*/
		/*	foreach($isi as $td => $isi2)
			{

				if($tmpCol=="") {
					$tmpCol.=$td[0]['field'];
				} else {
					$tmpCol.=",".$td[0]['field'];
				}
			}
			$a++;*/
	}
	$sCob="select ".$tmpCol." from ".$dbnm.".".$nmTable." where uploadStat is NULL or uploadStat=''";
//	echo $sCob;exit();
	
	$res=mysql_query($sCob,$corn) or die(mysql_error());
	$row=mysql_num_rows($res);
	if($row>0)
	{
	//echo"warning:".$sCob;
	echo"<button class=mybutton onclick=uploadData('".$row."','".$arr."') id=btnUpload>".$_SESSION['lang']['startUpload']."</button>
	<div style='overflow:auto;height:350px;max-width:1220px'>
	 <table class=sortable cellspacing=1 border=0>
	<thead>
	<tr class=rowheader>
	<td>No.</td>
	<td>".$_SESSION['lang']['kodecustomer']." / ".$_SESSION['lang']['kodesupplier']."</td>
	<td>".$_SESSION['lang']['nmcust']." / ".$_SESSION['lang']['namasupplier']."</td>
	</tr>
	</thead><tbody id=ListData>";

		while($hsl=mysql_fetch_array($res))
		{
                    $sCek="select * from ".$dbname.".log_5supplier where kodetimbangan='".$hsl[0]."'";
                    $res=mysql_query($sCek,$con) or die(mysql_error());
                    $row=  mysql_num_rows($res);
                    if ($row!=0){
			$no+=1;
			echo"<tr class=rowcontent id=row_".$no." >
			<td >".$no."</td>
			<td id=kdTimbangan_".$no.">".$hsl[0]."</td>
			<td id=nmCust_".$no.">".$hsl[1]."</td>
			</tr>
			";
                    }
			
		}
	}
	else
	{
		echo" <table class=sortable cellspacing=1 border=0>
	<thead>
	<tr class=rowheader>
	<td>No.</td>
	<td>".$_SESSION['lang']['kodecustomer']." / ".$_SESSION['lang']['kodesupplier']."</td>
	<td>".$_SESSION['lang']['nmcust']." / ".$_SESSION['lang']['namasupplier']."</td>
	</tr>
	</thead><tbody><tr class=rowcontent align=center><td colspan=3>Not Found</td></tr>";
	}
	echo"</tbody></table></div>";
	break;
        
	case'uploadData':
	//echo"warning:masuk";
	//@$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
	if(substr($kdTimbangan,0,1)=='5')
	{
		$sCek="select kodetimbangan from ".$dbname.".log_5supplier where kodetimbangan='".$kdTimbangan."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			$sNo="select supplierid,kodekelompok from ".$dbname.".log_5supplier where kodekelompok like 'S003%' order by `supplierid` desc limit 1";
			//echo $sNo;
			$qNo=mysql_query($sNo) or die(mysql_error());
			$rNo=mysql_fetch_assoc($qNo);
			$no=substr($rNo['supplierid'],4,6);
			
			$supplierId=intval($no);
			$supplierId+=1;
			$supplierId=$rNo['kodekelompok'].$supplierId;
		//	echo $supplierId;
//			exit();
			
			$sIns="INSERT INTO ".$dbname.".`log_5supplier` (`supplierid`, `namasupplier`,`kodekelompok`,`kodetimbangan`) VALUES ('".$supplierId."','".$nmCust."','".$rNo['kodekelompok']."','".$kdTimbangan."')";
			//echo $sIns;
			if(mysql_query($sIns))
			{
                                $corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
                                $sUp="update ".$dbnm.".msvendortrp set uploadStat='1' where TRPCODE='".$kdTimbangan."'";
                                if(mysql_query($sUp,$corn)){
                                    $stat=1;
                                    echo $stat;
                                    exit();
                                }
			}
			else
			{
				echo "DB Error : ".mysql_error($conn);
				$stat=0;
				echo $stat;
				exit();
			}
		}
		else
		{
                       
			$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
			$sUp="update ".$dbnm.".msvendortrp set uploadStat='1' where TRPPCODE='".$kdTimbangan."'";
			if(mysql_query($sUp,$corn)){
                        $stat=1;
                        echo $stat;
                        exit();
                        }
                  
		}
	}
	elseif(substr($kdTimbangan,0,1)=='1')
	{
		
		$sCek="select kodetimbangan from ".$dbname.".pmn_4customer where kodetimbangan='".$kdTimbangan."'";
	//	echo"customer__".$sCek;
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			$sNo="select kodecustomer,klcustomer from ".$dbname.".pmn_4customer  order by `kodecustomer` desc limit 1";
			//echo $sNo;
			$qNo=mysql_query($sNo) or die(mysql_error());
			$rNo=mysql_fetch_assoc($qNo);
			$no=substr($rNo['kodecustomer'],1,4);
			
			$kdCust=intval($no);
                        //echo $no.":".$kdCust;
			$kdCust+=1;
			$kdCust='C'.addZero($kdCust,4);
			
			$sIns="INSERT INTO ".$dbname.".`pmn_4customer` (`kodecustomer`, `namacustomer`,`klcustomer`,`kodetimbangan`) VALUES ('".$kdCust."','".$nmCust."','".$rNo['klcustomer']."','".$kdTimbangan."')";
		//	echo $sIns;exit();
			if(mysql_query($sIns))
			{
                               @$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
                              $supd="update ".$dbnm.".".$nmTable." set uploadStat=1 where BUYERCODE='".$kdTimbangan."'";
                              if(mysql_query($supd,$corn)){
                                $stat=1;
                                echo $stat;
                                exit();
                              }
			}
			else
			{
				echo "DB Error : ".mysql_error($conn);
				$stat=0;
				echo $stat;
			}
		}
		else
		{
                              @$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
                              $supd="update ".$dbnm.".".$nmTable." set uploadStat=1 where BUYERCOE='".$kdTimbangan."'";
                              if(mysql_query($supd,$corn))
                              {
                                $stat=1;
                                echo $stat;
                                exit();
                              }
                    /*
                        $corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
			$sUp="update ".$dbnm.".msvendorbuyer set uploadStat='1' where BUYERCODE='".$kdTimbangan."'";
			mysql_query($sUp,$corn);
			$stat=1;
			echo $stat;
                  */
                }
		
		
	}
	break;
	

	case'getDataLokasi':
	//echo"warning:Masuk";
	$sql="select * from ".$dbname.".setup_remotetimbangan where id='".$idRemote."'";
	//echo"warning:".$sql;
	$query=mysql_query($sql) or die(mysql_error());
	$res=mysql_fetch_assoc($query);
	echo $res['ip']."###".$res['port']."###".$res['dbname']."###".$res['username']."###".$res['password'];
	break;
	
	case'getTable':
	$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
	//$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd);
	$sCob="SHOW TABLES FROM ".$dbnm." LIKE '%msvendor%' ";
	//echo $sCob;exit();
	$res=mysql_query($sCob,$corn) or die(mysql_error());
	while($row=mysql_fetch_row($res))
	{
		$optTable.="<option value=".$row[0].">".$row[0]."</option>";
	}
	echo $optTable;
	break;
        case'getDataSipb':
        $arr="##dbnm##prt##pswrd##ipAdd##usrName##lksiServer##nmTable";
            @$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
        $sGetDt="select * from ".$dbnm.".".$nmTable." where uploadStat=0";
        //echo $sGetDt;
        //exit("error:".$sGetDt);
        $qGetDt=mysql_query($sGetDt,$corn) or die(mysql_error($corn));
        $row=mysql_num_rows($qGetDt);
        $tab.="<button class=mybutton onclick=uploadData2('".$row."','".$arr."') id=btnUpload>".$_SESSION['lang']['startUpload']."</button>";
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr>";
        $tab.="<td>No.</td>";
        $tab.="<td>No. Kontrak</td>";
        $tab.="<td>No. SIPB</td>";
        $tab.="<td>Tanggal</td>";
        $tab.="<td>Kodebarang</td>";
        $tab.="<td>Kode Transporter</td>";
        $tab.="<td>Nama Transporter</td>";
        $tab.="<td>Keterangan</td>";
        $tab.="</tr></thead><tbody>";
           while($rGetDt=mysql_fetch_assoc($qGetDt))
            {
                $sNm="select TRPNAME from ".$dbnm.".msvendortrp where TRPCODE='".$rGetDt['TRPCODE']."'";
                $qNm=mysql_query($sNm,$corn) or die(mysql_error($corn));
                $rNm=mysql_Fetch_assoc($qNm);
                $no+=1;
                $tab.="<tr class=rowcontent id=row_".$no."><td>".$no."</td>";
                $tab.="<td id=kontrak_".$no.">".$rGetDt['CTRNO']."</td>";
                $tab.="<td id=sipb_".$no.">".$rGetDt['SIPBNO']."</td>";
                $tab.="<td id=tgl_sipb_".$no.">".$rGetDt['SIPBDATE']."</td>";
                $tab.="<td id=kdbrg_".$no.">".$rGetDt['PRODUCTCODE']."</td>";
                $tab.="<td id=trpcod_".$no.">".$rGetDt['TRPCODE']."</td>";
                $tab.="<td id=trp_nm_".$no.">".$rNm['TRPNAME']."</td>";
                $tab.="<td id=ket_".$no.">".$rGetDt['DESCRIPTION']."</td></tr>";
            }
            $tab.="</tbody></table>";
            echo $tab;
        break;
        case'uploadData2':
        $sCek="select * from ".$dbname.".pabrik_mssipb where CTRNO='".$kntrk."' and SIPBNO='".$nosipb."'";
        $qCek=mysql_query($sCek) or die(mysql_error());
        $rCek=mysql_num_rows($qCek);
        if($rCek<1)
        {
//$kdbrg=$_POST['kdbrg'];
//$trpcode=$_POST['trpcode'];
//$trpname=$_POST['trpname'];
//$ketdt=$_POST['ketdt'];
                $sMsk="insert into ".$dbname.".pabrik_mssipb (CTRNO,SIPBNO,SIPBDATE,PRODUCTCODE,TRPCODE,DESCRIPTION)";
                $sMsk.="values ('".$kntrk."','".$nosipb."','".$tglsibp."','".$kdbrg."','".$trpcode."','".$ketdt."')";
                if(mysql_query($sMsk))
                {
                    $sDck="select kodetimbangan from ".$dbname.".log_5supplier where kodetimbangan='".$trpcode."'";
                    $qDck=mysql_query($sDck) or die(mysql_error($conn));
                    $rDck=mysql_num_rows($qDck);
                    if($rDck<1)
                    {
                      $sNo="select supplierid,kodekelompok from ".$dbname.".log_5supplier where kodekelompok like 'S%' order by `supplierid` desc limit 1";
                      $qNo=mysql_query($sNo) or die(mysql_error());
                      $rNo=mysql_fetch_assoc($qNo);
                      $no=substr($rNo['supplierid'],4,6);
                      $rNo['kodekelompok']="S003";
                        $supplierId=intval($no);
                        $supplierId+=1;
                        $supplierId=$rNo['kodekelompok'].$supplierId;
       
                        $sIns="INSERT INTO ".$dbname.".`log_5supplier` (`supplierid`, `namasupplier`,`kodekelompok`,`kodetimbangan`) 
                               VALUES ('".$supplierId."','".$trpname."','".$rNo['kodekelompok']."','".$trpcode."')";
                        if(mysql_query($sIns))
                        {
                              @$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
                              $supd="update ".$dbnm.".".$nmTable." set uploadStat=1 where CTRNO='".$kntrk."' and SIPBNO='".$nosipb."'";
                              if(mysql_query($supd,$corn))
                              {
                                $stat=1;
                                echo $stat;
                                exit();
                              }
                        }
                        else
                        {
                                echo "DB Error : ".mysql_error($conn);
                                $stat=0;
                                echo $stat;
                                exit();
                        }  
                    }
                    else
                    {
                        @$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
                              $supd="update ".$dbnm.".".$nmTable." set uploadStat=1 where CTRNO='".$kntrk."' and SIPBNO='".$nosipb."'";
                              if(mysql_query($supd,$corn))
                              {
                                $stat=1;
                                echo $stat;
                                exit();
                              }
                    }
                }
                else
                {
                        echo "DB Error : ".mysql_error($conn);
                        $stat=0;
                        echo $stat;
                        exit();
                }  
                
        }
        else
        {
                $stat=1;
                echo $stat;
                exit();
        }
        break;
	default:
	break;
}

?>