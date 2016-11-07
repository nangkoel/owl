<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

   
if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}

$_POST['thnKontrak']==''?$thnKontrak=$_GET['thnKontrak']:$thnKontrak=$_POST['thnKontrak'];
$_POST['kdKomoditi']==''?$kdKomoditi=$_GET['kdKomoditi']:$kdKomoditi=$_POST['kdKomoditi'];
$_POST['pt']==''?$pt=$_GET['pt']:$pt=$_POST['pt'];
if($pt=='')
{
    exit("Error: ".$_SESSION['lang']['pt']." can't empty");
}
if($kdKomoditi=='')
{
    exit("Error: ".$_SESSION['lang']['komoditi']." can't empty");
}
if($thnKontrak==''){
    exit("Error: ".$_SESSION['lang']['tahunkontrak']." can't empty");
}else if(strlen($thnKontrak)!=4){
    exit("Error: pls check  ".$_SESSION['lang']['tahunkontrak']."");
}
$nmCust=makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$brdr=0;
if($proses=='excel')
{
    $bgclr=" bgcolor=#DEDEDE";
    $brdr=1;
}
if($proses=='preview'||$proses=='excel')
{
$tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable><thead>";
$tab.="<tr ".$bgclr.">";
$tab.="<td >No.</td>";
$tab.="<td >".$_SESSION['lang']['NoKontrak']."</td>";
$tab.="<td >".$_SESSION['lang']['namacust']."</td>";
$tab.="<td >".$_SESSION['lang']['volume']."</td>";
/*if($proses!='excel'){
    $tab.="<td >".$_SESSION['lang']['action']."</td>";
}*/
$tab.="</tr>";
$tab.="</thead><tbody>";// and substr(nokontrak,4,3)
$sData="select nokontrak,koderekanan,kuantitaskontrak from ".$dbname.".pmn_kontrakjual where 
        tanggalkontrak like '".$thnKontrak."%' and kodebarang='".$kdKomoditi."' and  substr(nokontrak,5,3)='".$pt."'";
		//echo $sData;
$qData=mysql_query($sData) or die(mysql_error($conn));
while($rData=  mysql_fetch_assoc($qData)){
    $no+=1;
    $tab.="<tr class=rowcontent>";
    $tab.="<td>".$no."</td>";
    $tab.="<td>".$rData['nokontrak']."</td>";
    $tab.="<td>".$nmCust[$rData['koderekanan']]."</td>";
    $tab.="<td align=right>".number_format($rData['kuantitaskontrak'],0)."</td>";
   /* if($proses!='excel'){
        $tab.="<td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pmn_kontrakjual','".$rData['nokontrak']."','','pmn_kontakjual_pdf2',event)\"></td>";
    }*/
    $tab.="</tr>";
}

$tab.="</tbody></table>";
}
switch($proses)
{
	case'preview':
	echo $tab;
	break;
	
	case'excel':

			
                $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
                $tglSkrg=date("Ymd");
                $nop_="daftarkontrak";
                if(strlen($tab)>0)
                {
                if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                @unlink('tempExcel/'.$file);
                }
                }	
                closedir($handle);
                }
                $handle=fopen("tempExcel/".$nop_.".xls",'w');
                if(!fwrite($handle,$tab))
                {
                echo "<script language=javascript1.2>
                parent.window.alert('Can't convert to excel format');
                </script>";
                exit;
                }
                else
                {
                echo "<script language=javascript1.2>
                window.location='tempExcel/".$nop_.".xls';
                </script>";
                }
                closedir($handle);
                }
	break;
        case'getKodeorg':
        $optorg="<option value=''>".$_SESSION['lang']['all']."</option>";
	if($tipeIntex==1)
	{
		//$sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and induk in(select induk from ".$dbname.".organisasi where tipe='PABRIK') order by namaorganisasi asc";
            $sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and induk ='PMO' order by namaorganisasi asc";
	}
	elseif($tipeIntex==0)
	{
		$sOrg="SELECT namasupplier,`kodetimbangan` FROM ".$dbname.".log_5supplier WHERE substring(kodekelompok,1,1)='S' and kodetimbangan!='NULL' order by namasupplier asc";//echo "warning:".$sOrg;
	}
	elseif($tipeIntex==2)
	{
		//$sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and induk not in(select induk from ".$dbname.".organisasi where tipe='PABRIK') order by namaorganisasi asc";
            $sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and induk <>'PMO' order by namaorganisasi asc";
	}
	//echo "warning".$sOrg;exit();
        if($tipeIntex!=3)
        {
            $qOrg=mysql_query($sOrg) or die(mysql_error());
            while($rOrg=mysql_fetch_assoc($qOrg))
            {
                    if($tipeIntex!=0)
                    {
                            $optorg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
                    }
                    else
                    {
                            $optorg.="<option value=".$rOrg['kodetimbangan'].">".$rOrg['namasupplier']."</option>";
                    }
            }
        }
	echo $optorg;
        break;
	default:
	break;
}
?>