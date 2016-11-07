<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?php

$noakundari=$_GET['noakundari'];
$noakunsampai=$_GET['noakunsampai'];
$periode=$_GET['periode'];
$periode1=$_GET['periode1'];
$pt=$_GET['pt'];
$unit=$_GET['unit'];
$periodesaldo=str_replace("-", "", $periode);
if(isset($_POST['proses'])){
    $proses=$_POST['proses'];}
else{
    $proses=$_GET['proses'];}

if($proses != 'excel'){
echo"<fieldset><legend>Print Excel</legend>
     <img onclick=\"parent.detailNeracaKeExcel(event,'keu_slave_getNeracaDetail.php?proses=excel&noakundari=".$noakundari."&noakunsampai=".$noakunsampai."&periode=".$periode."&periode1=".$periode1."&pt=".$pt."&unit=".$unit."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset>";
}
if($proses=='excel')
{
$bg=" bgcolor=#DEDEDE";
$brdr=1;

}
else
{ 
    $bg="";
    $brdr=0;
}
$qwe="<table class=sortable border=".$brdr." cellspacing=1>
      <thead>
        <tr class=rowcontent>
          <td align=center>No</td>
          <td align=center>No.Akun</td>
          <td align=center>Saldo Awal</td>
          <td align=center>Debet</td>
          <td align=center>Kredit</td>
        </tr>
      </thead>
      <tbody>";

if($unit=='')
    $where=" kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')";
else 
    $where=" kodeorg='".$unit."'";
	
$s_detail="select noakun,sum(awal".substr($periodesaldo,4,2).") as awal, sum(debet".substr($periodesaldo,4,2).") as debet, 
           sum(kredit".substr($periodesaldo,4,2).") as kredit
           from ".$dbname.".keu_saldobulanan where noakun between '".$noakundari."' 
           and '".$noakunsampai."' and periode='".$periodesaldo."' and ".$where."
           group by noakun";
//exit("error".$s_detail);
$q_detail=mysql_query($s_detail) or die(mysql_error($conn));
while($r_detail=mysql_fetch_assoc($q_detail))
{
    $akun[$r_detail['noakun']]=$r_detail['noakun'];
    $awal[$r_detail['noakun']]=$r_detail['awal'];
    $debet[$r_detail['noakun']]=$r_detail['debet'];
    $kredit[$r_detail['noakun']]=$r_detail['kredit'];
}
$no=0; 
if(!empty($akun))
    foreach($akun as $lst_akun) {
        $no+=1;
        $qwe.="<tr class=rowcontent>
                   <td align=center>".$no."</td>
                   <td align=center>".$lst_akun."</td>               
                   <td align=right>".number_format($awal[$lst_akun],0)."</td>    
                   <td align=right>".number_format($debet[$lst_akun],0)."</td>    
                   <td align=right>".number_format($kredit[$lst_akun],0)."</td>";
        $qwe.="</tr>";
        $tawal+=$awal[$lst_akun];
        $tdebet+=$debet[$lst_akun];
        $tkredit+=$kredit[$lst_akun];
}

$qwe.="<tr class=rowcontent>
           <td colspan=2 align=center><b>TOTAL</b></td>
           <td align=right>".number_format($tawal)."</td>
           <td align=right>".number_format($tdebet)."</td>
           <td align=right>".number_format($tkredit)."</td>
      </tr>";  
$qwe.="</tbody><tfoot></tfoot></table>";


switch($proses)
{
    case'preview':
        echo $qwe;
    break;
    case'excel':
    $qwe.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
    $dte=date("Hms");
    $nop_="DetailNeraca_".$dte;
    if(strlen($qwe)>0)
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
            if(!fwrite($handle,$qwe))
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
default:
break;
}
       
?>