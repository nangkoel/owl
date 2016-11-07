<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['kdBrg']==''?$kdBrg=$_GET['kdBrg']:$kdBrg=$_POST['kdBrg'];
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNmSup=makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$optdt=  makeOption($dbname, 'log_5supplier', 'kodetimbangan,namasupplier');
$brd=0;
$bgclr="";
if($proses=='excel'){
    $bgclr=" bgcolor=#DEDEDE align=center";
    $brd=1;
}
if($periode!=''){
    $where.=" and SIPBDATE like '".$periode."%'" ;
}
 $tab.="<table class=sortable cellspacing=1 border=".$brd."><thead>
        <tr class=rowheader ".$bgclr.">
        <td>".$_SESSION['lang']['NoKontrak']."</td>
        <td>".$_SESSION['lang']['nosipb']."</td>
        <td>".$_SESSION['lang']['tglKontrak']."</td>
        <td>".$_SESSION['lang']['transporter']."</td>
        <td>".$_SESSION['lang']['kodebarang']."</td>
        <td>".$_SESSION['lang']['namabarang']."</td>
        </tr></thead><tbody>
        ";
        if($kdBrg!='')
        {
                $where=" and kodebarang='".$kdBrg."'";
        }
        $sql="select * from ".$dbname.".pabrik_mssipb "
             ."where SIPBDATE!='' ".$where." order by SIPBDATE asc";
        //exit("Error".$sql);
        $query=mysql_query($sql) or die(mysql_error());
        while($res=mysql_fetch_assoc($query)){
                $sTimb="select  distinct kodecustomer from ".$dbname.".pabrik_timbangan where nokontrak='".$res['CTRNO']."'";
                $qTimb=mysql_query($sTimb) or die(mysql_error());
                $rTimb=mysql_fetch_assoc($qTimb);
                
                $sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodetimbangan='".$rTimb['kodecustomer']."'";
                $qCust=mysql_query($sCust) or die(mysql_error());
                $rCust=mysql_fetch_assoc($qCust);
                if($rowd=  mysql_num_rows($qCust)==0){
                    $rCust['namacustomer']=$optdt[$rTimb['kodecustomer']];
                }
                
                $tab.="<tr class=rowcontent>
                <td>".$res['CTRNO']."</td>
                <td>".$res['SIPBNO']."</td>
                <td>".tanggalnormal($res['SIPBDATE'])."</td>
                <td>".$rCust['namacustomer']."</td>
                <td>".$res['PRODUCTCODE']."</td>
                <td>".$optNmBrg[$res['PRODUCTCODE']]."</td>
                </tr>";             
        }
      
        $tab.="</tbody></table>";
switch($proses)
{
        case'preview':
        echo $tab;
        break;
        
        case'excel':
        
            $tab.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

                        $nop_="daftarSibp";
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
        
}

?>