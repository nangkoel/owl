<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];
$_POST['kdUnit']==''?$kodeOrg=$_GET['kdUnit']:$kodeOrg=$_POST['kdUnit'];
$_POST['thnBudget']==''?$thnBudget=$_GET['thnBudget']:$thnBudget=$_POST['thnBudget'];
$_POST['modPil']==''?$modPil=$_GET['modPil']:$modPil=$_POST['modPil'];
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$where=" kodeunit='".$kodeOrg."' and tahunbudget='".$thnBudget."'";
$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sep","10"=>"Okt","11"=>"Nov","12"=>"Des");
 $abr=1;
if($kodeOrg==''||$thnBudget=='')
{
    exit("Error:Field Tidak Boleh Kosong");
}
if($modPil=='0')
{
    $spanLt=3;
    $sThnTnm="select distinct thntnm from ".$dbname.".bgt_produksi_afdeling where  ".$where." order by thntnm asc";
    $sKodeOrg="select * from ".$dbname.".bgt_produksi_afdeling where  ".$where."  order by tahunbudget asc";
    //echo $sKodeOrg;
    $qKodeOrg=mysql_query($sKodeOrg) or die(mysql_error($conn));
    while($rKode=mysql_fetch_assoc($qKodeOrg))
    {
        $a+=1; 

        $dtJjg[$rKode['tahunbudget']][$rKode['afdeling']][$rKode['thntnm']]=$rKode['jlhjjg'];
        $dtJmlhKg[$rKode['tahunbudget']][$rKode['afdeling']][$rKode['thntnm']]=$rKode['jlhkg'];
        $dtJmlhLuas[$rKode['tahunbudget']][$rKode['afdeling']][$rKode['thntnm']]=$rKode['luas'];
    }
}
if($modPil=='1')
{
    $spanLt=4;
    $sThnTnm="select distinct kodeblok as thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw where  ".$where." order by thntnm asc";
    $sKodeOrg="select tahunbudget,kodeblok,substr(kodeblok,1,6) as afdeling,bjr,kgsetahun,pokokproduksi,luas,thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw where  ".$where."  order by tahunbudget asc";
    //echo $sKodeOrg;
    $qKodeOrg=mysql_query($sKodeOrg) or die(mysql_error($conn));
    while($rKode=mysql_fetch_assoc($qKodeOrg))
    {
    $a+=1; 
        $dtJjg[$rKode['tahunbudget']][$rKode['afdeling']][$rKode['kodeblok']]=$rKode['bjr'];
        $dtJmlhKg[$rKode['tahunbudget']][$rKode['afdeling']][$rKode['kodeblok']]=$rKode['kgsetahun'];
        $dtJmlhLuas[$rKode['tahunbudget']][$rKode['afdeling']][$rKode['kodeblok']]=$rKode['luas'];
        $dtJmlhPkk[$rKode['tahunbudget']][$rKode['afdeling']][$rKode['kodeblok']]=$rKode['pokokproduksi'];
        $dtJmlhThnTnm[$rKode['tahunbudget']][$rKode['kodeblok']]=$rKode['thntnm'];
    }
}
if($modPil=='2')
{  
    
    $sThnTnm="select distinct tahunbudget, kodeunit, sum(kgsetahun) as kgsetahun, sum(kg01) as kg01, sum(kg02) as kg02, sum(kg03) as kg03, sum(kg04) as kg04, sum(kg05) as kg05, 
        sum(kg06) as kg06, sum(kg07) as kg07, sum(kg08) as kg08, sum(kg09) as kg09, sum(kg10) as kg10, sum(kg11) as kg11, sum(kg12) as kg12 from ".$dbname.".bgt_produksi_kbn_kg_vw where  kodeunit='".$kodeOrg."' and tahunbudget='".$thnBudget."' order by thntnm asc";
    
   // echo $sThnTnm;
    $qKodeOrg=mysql_query($sThnTnm) or die(mysql_error($conn));
    while($rKode=mysql_fetch_assoc($qKodeOrg))
    {
        $a+=1; 
        $dtJmlhKgStaun[$rKode['tahunbudget']][$rKode['kodeunit']]=$rKode['kgsetahun'];
        for($abc=1;$abc<13;$abc++)
        {
            if(strlen($abc)<2)
            {
                $abcd="0".$abc;
            }
            else
            {
                $abcd=$abc;
            }
            $dtJmlhKg[$rKode['tahunbudget']][$rKode['kodeunit']][$abcd]=$rKode['kg'.$abcd];
        }

    }
    
}
if($modPil=='3')
{  
    
    $sThnTnm="select   tahunbudget, thntnm,left(kodeblok,6) as afdeling, sum(kg01) as kg01, sum(kg02) as kg02, sum(kg03) as kg03, sum(kg04) as kg04, sum(kg05) as kg05,
              sum(kg06) as kg06, sum(kg07) as kg07, sum(kg08) as kg08, sum(kg09) as kg09, sum(kg10) as kg10, sum(kg11) as kg11, sum(kg12) as kg12 from ".$dbname.".bgt_produksi_kbn_kg_vw 
              where  kodeunit='".$kodeOrg."' and tahunbudget='".$thnBudget."' group by left(kodeblok,6),thntnm order by left(kodeblok,6),thntnm asc";
 //echo $sThnTnm;
 
    $qKodeOrg=mysql_query($sThnTnm) or die(mysql_error($conn));
    while($rKode=mysql_fetch_assoc($qKodeOrg))
    {
        
        for($abcre=1;$abcre<13;$abcre++)
        {
            if($abcre<10)
            {
                $abcdty="0".$abcre;
            }
            else
            {
                $abcdty=$abcre;
            }
            
            @$dtJmlhKgdr[$rKode['thntnm']][$rKode['afdeling']][$abcre]=$rKode['kg'.$abcdty];
            @$totKgThn[$rKode['thntnm']][$rKode['afdeling']]+=$rKode['kg'.$abcdty];
        }

    }

    $sThnTnm2="select  tahunbudget, thntnm, left(kodeblok,6) as afdeling ,sum(jjg01) as kg01, sum(jjg02) as kg02, sum(jjg03) as kg03, sum(jjg04) as kg04, sum(jjg05) as kg05,
               sum(jjg06) as kg06, sum(jjg07) as kg07, sum(jjg08) as kg08, sum(jjg09) as kg09, sum(jjg10) as kg10, sum(jjg11) as kg11, sum(jjg12) as kg12 from ".$dbname.".bgt_produksi_kbn_vw where  kodeunit='".$kodeOrg."' and tahunbudget='".$thnBudget."' 
               group by left(kodeblok,6),thntnm order by thntnm asc";
    $qKodeOrg2=mysql_query($sThnTnm2) or die(mysql_error($conn));
    while($rKode2=mysql_fetch_assoc($qKodeOrg2))
    {
      
        for($abcr=1;$abcr<13;$abcr++)
        {
            if($abcr<10)
            {
                $abcde="0".$abcr;
            }
            else
            {
                $abcde=$abcr;
            }
            $dtJmlhKg25[$rKode2['thntnm']][$rKode2['afdeling']][$abcr]=$rKode2['kg'.$abcde];
            $totJjgThn[$rKode2['thntnm']][$rKode2['afdeling']]+=$rKode2['kg'.$abcde];
        }

    }
    $atat=0;
    $sKodeOrg="select * from ".$dbname.".bgt_produksi_afdeling where  ".$where."  order by thntnm asc";
   // echo $sKodeOrg;
    $qKodeOrg=mysql_query($sKodeOrg) or die(mysql_error($conn));
    while($rKode=mysql_fetch_assoc($qKodeOrg))
    {
        $atat+=1;
        //$dtJmlhKg[$rKode['tahunbudget']][$rKode['thntnm']][$atat]=$rKode['afdeling'];
        $dtJmlhLuas[$rKode['thntnm']][$rKode['afdeling']]=$rKode['luas'];
       // $listThnTnm[]=$rKode['thntnm'];
    }


}
 if($modPil!=4)
 {
//get tahun tanam
$brd=0;
//exit("Error".$sThnTnm);
$qThnTnm=mysql_query($sThnTnm) or die(mysql_error());
while($rThnTnm=  mysql_fetch_assoc($qThnTnm))
{
    $a+=1; 
     $dtThnBudget[]=$rThnTnm['thntnm']; 
}
//get kode afdeling
$sThnTnm2="select distinct afdeling from ".$dbname.".bgt_produksi_afdeling where  ".$where." group by afdeling order by afdeling asc";
$qThnTnm2=mysql_query($sThnTnm2) or die(mysql_error());
while($rThnTnm2=  mysql_fetch_assoc($qThnTnm2))
{
     $bc+=1;
     $dtKdunit[$bc]=$rThnTnm2['afdeling']; 
}
$varCheck= mysql_num_rows($qKodeOrg);

if($varCheck==0)
{
    exit("Error: Kebun ".$kodeOrg.", Belum Melakukan Proses Budget Di tahun ".$thnBudget."");
}
//exit("Error".$varCheck);
$totalUnit=count($dtKdunit);
$totaThntnm=count($dtThnBudget);
$cols=$totalUnit*$spanLt;
if($proses=='excel')
{
    $brd=1;
    $bg="bgcolor=#DEDEDE";
}
 }
        if($modPil<'2')
        {
            $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable><thead>";
            $tab.="<tr class=rowheader>";
            $tab.="<td  rowspan='3' align=center ".$bg.">".$_SESSION['lang']['thntanam']."</td>";
            $tab.="<td colspan='".$cols."' align=center ".$bg.">PERINCIAN  PER AFDELING</td>";
            $tab.="<td colspan='".$spanLt."' rowspan=2 align=center ".$bg.">".$_SESSION['lang']['total']."</td>";
            $tab.="</tr>";
            $tab.="<tr>";
            foreach($dtKdunit as $brsKdUnit)
            {
               $tab.="<td  colspan='".$spanLt."' align=center ".$bg.">".$brsKdUnit."</td>";
            }

            $tab.="</tr><tr>";
            if($modPil=='0')
            {
                for($dra=1;$dra<=($totalUnit+1);$dra++)
                {
                $tab.="<td  align=center ".$bg.">".$_SESSION['lang']['luas']."</td><td align=center ".$bg.">JJG</td><td align=center ".$bg.">TON</td>";
                }
            }
            else
            {
                for($dra=1;$dra<=($totalUnit+1);$dra++)
                {
                $tab.="<td  align=center ".$bg.">".$_SESSION['lang']['luas']."</td><td align=center ".$bg.">".$_SESSION['lang']['bjr']."</td><td align=center ".$bg.">".$_SESSION['lang']['pkkproduktif']."</td><td align=center ".$bg.">TON</td>";
                }
            }
            $tab.="</tr>";
            $tab.="</thead><tbody>";
            $total=1;
            foreach($dtThnBudget as $brsThnBudget)
            {
              
                $tab.="<tr class=rowcontent>";
                $modPil!='0'?$tab.="<td>".$brsThnBudget." [".$dtJmlhThnTnm[$thnBudget][$brsThnBudget]."]</td>":$tab.="<td>".$brsThnBudget."</td>";
                 for($abr=1;$abr<=($totalUnit+1);$abr++)
                 {
                     $total+=1; 
                     if($abr!=($totalUnit+1))
                     {
                    

                        @$kgTon[$thnBudget][$dtKdunit[$abr]][$brsThnBudget]=$dtJmlhKg[$thnBudget][$dtKdunit[$abr]][$brsThnBudget]/1000;
                        
                        $tab.="<td  align=right>".number_format($dtJmlhLuas[$thnBudget][$dtKdunit[$abr]][$brsThnBudget],2)."</td>";
                         $tab.="<td align=right>".number_format($dtJjg[$thnBudget][$dtKdunit[$abr]][$brsThnBudget],2)."</td>";
                        if($modPil!='0')
                        {  
                            $tab.="<td align=right>".number_format($dtJmlhPkk[$thnBudget][$dtKdunit[$abr]][$brsThnBudget],2)."</td>";
                        }
              
                        $tab.="<td align=right>".number_format($kgTon[$thnBudget][$dtKdunit[$abr]][$brsThnBudget],2)."</td>";
                        $totKg[$brsThnBudget]+=$dtJmlhKg[$thnBudget][$dtKdunit[$abr]][$brsThnBudget];
                        $totJjg[$brsThnBudget]+=$dtJjg[$thnBudget][$dtKdunit[$abr]][$brsThnBudget];
                        $totLuas[$brsThnBudget]+=$dtJmlhLuas[$thnBudget][$dtKdunit[$abr]][$brsThnBudget];
                        
                        $totPoko[$brsThnBudget]+=$dtJmlhPkk[$thnBudget][$dtKdunit[$abr]][$brsThnBudget];
                        $totKgAfd[$dtKdunit[$abr]]+=$dtJmlhKg[$thnBudget][$dtKdunit[$abr]][$brsThnBudget];
                        $totJjgAfd[$dtKdunit[$abr]]+=$dtJjg[$thnBudget][$dtKdunit[$abr]][$brsThnBudget];
                        $totLuasAfd[$dtKdunit[$abr]]+=$dtJmlhLuas[$thnBudget][$dtKdunit[$abr]][$brsThnBudget];
                        $totPokoAfd[$dtKdunit[$abr]]+=$dtJmlhPkk[$thnBudget][$dtKdunit[$abr]][$brsThnBudget];
                       
                     }
                     else
                     {
                         @$sbTot[$brsThnBudget]=$totKg[$brsThnBudget]/1000;
                        $tab.="<td  align=right>".number_format($totLuas[$brsThnBudget],2)."</td>";
                        
                         
                         $tab.="<td align=right>".number_format($totJjg[$brsThnBudget],2)."</td>";
                        if($modPil!='0')
                        { 
                         $tab.="<td align=right>".number_format($totPoko[$brsThnBudget],2)."</td>";
                        }
                       
                        $tab.="<td align=right>".number_format($sbTot[$brsThnBudget],2)."</td>";
                        $grndTotLuas+=$totLuas[$brsThnBudget];
                        $grndTotJjg+=$totJjg[$brsThnBudget];
                        $grndTotKg+=$totKg[$brsThnBudget];
                        $grndTotPokok+=$totPoko[$brsThnBudget];
                     }
                 }
                $tab.="</tr>";
               //$gakSm=substr($brsThnBudget,0,6);
            }  
            $tab.="<tr class=rowcontent><td align='right'>".$_SESSION['lang']['total']."</td>";
            foreach($dtKdunit as $brsKdUnit)
            {
                 @$tonTotAfd[$brsKdUnit]=$totKgAfd[$brsKdUnit]/1000;
                    $tab.="<td  align=right>".number_format($totLuasAfd[$brsKdUnit],2)."</td>";
                    if($modPil!='0')
                    { 
                    //$tab.="<td  align=right>".number_format($totJjgAfd[$brsKdUnit]/$total,2)."</td>";
                    $tab.="<td align=right>&nbsp;</td>";
                    $tab.="<td  align=right>".number_format($totPokoAfd[$brsKdUnit],2)."</td>";
                    }
                    else
                    {
                        $tab.="<td  align=right>".number_format($totJjgAfd[$brsKdUnit],2)."</td>";
                    }
                    $tab.="<td  align=right>".number_format($tonTotAfd[$brsKdUnit],2)."</td>";
            }
            @$GrnTot=$grndTotKg/1000;
            $tab.="<td  align=right>".number_format($grndTotLuas,2)."</td>";
             if($modPil!='0')
             { 
            //$tab.="<td align=right>".number_format($grndTotJjg/$total,2)."__".$total."</td>";
            $tab.="<td align=right>&nbsp;</td>";
            $tab.="<td  align=right>".number_format($grndTotPokok,2)."</td>";
             }
             else
             {
                 $tab.="<td align=right>".number_format($grndTotJjg,2)."</td>";
             }
            $tab.="<td align=right>".number_format(@$GrnTot,2)."</td>";
            $tab.="</tr>";
            $tab.="</tbody></table>";
        }
        if($modPil=='3')
        {

           
           
            $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable><thead>";
            $tab.="<tr class=rowheader>";
            $tab.="<td rowspan=2 ".$bg.">".$_SESSION['lang']['thntanam']."</td>";
            $tab.="<td rowspan=2 ".$bg.">".$_SESSION['lang']['afdeling']."</td>";
            foreach($arrBln as $listBln)
            {
                $tab.="<td colspan=2 align=center>".$listBln."</td>";
            }
            $tab.="<td colspan=3 align=center ".$bg.">".$_SESSION['lang']['total']."</td></tr>";
            $tab.="<tr>";
            foreach($arrBln as $dtBln)
            {
                $tab.="<td align=center ".$bg.">JJG</td><td align=center ".$bg.">TON</td>";
            }
            $tab.="<td  align=center ".$bg.">".$_SESSION['lang']['luas']."</td><td align=center ".$bg.">JJG</td><td align=center ".$bg.">TON</td>";
            $tab.="</tr></thead><tbody>";
            $drat=0;


            foreach($dtJmlhKgdr as $dtThnTnm=>$thn)
            {
                
                foreach($thn as $key=>$dtAfd)
                {
                    $tab.="<tr class=rowcontent><td>".$dtThnTnm."</td>";
                    $tab.="<td>".$key."</td>";
                    foreach($dtAfd as $bln =>$val)
                    {
                        $tab.="<td align=right>".number_format($dtJmlhKg25[$dtThnTnm][$key][$bln],2)."</td>";
                        $tab.="<td align=right>".number_format($val/1000,2)."</td>";
                        $totjjgDbwh[$bln]+=$dtJmlhKg25[$dtThnTnm][$key][$bln];
                        $totkgDbwh[$bln]+=$val;
                    }
                    $tab.="<td align=right>".number_format($dtJmlhLuas[$dtThnTnm][$key] ,2)."</td>";
                    $tab.="<td align=right>".number_format($totJjgThn[$dtThnTnm][$key],2)."</td>";
                    $tab.="<td align=right>".number_format($totKgThn[$dtThnTnm][$key]/1000,2)."</td></tr>";
                    $totLdbwh+=$dtJmlhLuas[$dtThnTnm][$key];
                    $totsmajjgdbwh+=$totJjgThn[$dtThnTnm][$key];
                    $totsmaKgdbwh+=$totKgThn[$dtThnTnm][$key];
                }

            }
            $tab.="<tr class=rowcontent><td colspan=2>".$_SESSION['lang']['total']."</td>";
            for($arde=1;$arde<13;$arde++)
            {
                $tab.="<td align=right>".number_format($totjjgDbwh[$arde],2)."</td>";
                $tab.="<td align=right>".number_format($totkgDbwh[$arde],2)."</td>";
            }
            $tab.="<td align=right>".number_format($totLdbwh,2)."</td>";
            $tab.="<td align=right>".number_format($totsmajjgdbwh,2)."</td>";
            $tab.="<td align=right>".number_format($totsmaKgdbwh,2)."</td></tr>";
            $tab.="</tbody></table>";
            
        }
        if($modPil=='4')
        {
            //exit("Error:masuk");
            $_POST['kdUnit']==''?$_POST['kdUnit']=$_GET['kdUnit']:$_POST['kdUnit']=$_POST['kdUnit'];
            $_POST['thnBudget']==''?$_POST['thnBudget']=$_GET['thnBudget']:$_POST['thnBudget']=$_POST['thnBudget'];
            $arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sep","10"=>"Okt","11"=>"Nov","12"=>"Des");
            $totRowDlm=count($arrBln);
            $tab="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>";
            $tab.="<thead><tr class=rowheader><td width=15 align=center>No</td>";
            $tab.="<td align=center ".$bg." width=90>".$_SESSION['lang']['kodeblok']."</td>";
            $tab.="<td align=center ".$bg." width=90>".$_SESSION['lang']['budgetyear']."</td>";
            $tab.="<td align=center ".$bg." width=75>".$_SESSION['lang']['thntnm']."</td>";
            $tab.="<td align=center ".$bg." width=100>".$_SESSION['lang']['pkkproduktif']."</td>";
            $tab.="<td align=center ".$bg."  width=50>".$_SESSION['lang']['bjr']."</td>";
            $tab.="<td align=center ".$bg." width=150>".$_SESSION['lang']['jenjangpokoktahun']."</td>";
            $tab.="<td align=center  width=50>".$_SESSION['lang']['jjgThn']."</td>";
            foreach($arrBln as $brs7=>$dtBln7)
            {
                $tab.="<td  align=center>".$dtBln7."(kg)</td>";
            }
             $tab.="<td align=center  width=50>".$_SESSION['lang']['total']." (KG)</td></tr></thead><tbody>";
             $sList="select * from ".$dbname.".bgt_produksi_kbn_kg_vw where
                    kodeunit like '".$_POST['kdUnit']."%' and tahunbudget='".$_POST['thnBudget']."'
                    order by kodeblok asc ";
                // echo $sList;
                $qList=mysql_query($sList) or die(mysql_error());
                while($rList=mysql_fetch_assoc($qList))
                {
					$pokok="select jjgperpkk,tutup from ".$dbname.".bgt_produksi_kebun WHERE kodeblok='".$rList['kodeblok']."' and tahunbudget='".$rList['tahunbudget']."'";
					$qOpt=mysql_query($pokok) or die(mysql_error());
					$rOpt=mysql_fetch_assoc($qOpt);


                        $a1=$rOpt['jjgperpkk'];
                        $a3=$rList['pokokproduksi'];
                        $totala=$a1*$a3;
                        
                    $no+=1;
                    $tab.="<tr class=rowcontent >";
                    $tab.="<td align=center ".$rtp.">".$no."</td>";
                    $tab.="<td align=left ".$rtp.">".$rList['kodeblok']."</td>";
	            $tab.="<td align=right ".$rtp.">".$rList['tahunbudget']."</td>";
                    $tab.="<td align=right ".$rtp.">".$rList['thntnm']."</td>";
                    $tab.="<td align=right ".$rtp.">".$rList['pokokproduksi']."</td>";
                    $tab.="<td align=right ".$rtp.">".$rList['bjr']."</td>";
                    $tab.="<td align=right ".$rtp.">".$rOpt['jjgperpkk']."</td>";

                    $tab.="<td align=right ".$rtp.">".number_format($totala,0)."</td>";


                    for($a=1;$a<=$totRowDlm;$a++)
                    {
                        if(strlen($a)=='1')
                        {
                            $b="0".$a;
                        }
                        else
                        {
                            $b=$a;
                        }
                        if($rList['kg'.$b]=='')
                        {
                            $rList['kg'.$b]=0;
                        }
                        $tab.="<td align='right' ".$rtp.">".number_format($rList['kg'.$b],0)."</td>";
                        $rTotal[$rList['kodeblok']]+=$rList['kg'.$b];
                    }
			 $tab.="<td align=right ".$rtp.">".number_format($rTotal[$rList['kodeblok']],0)."</td>";
		  
                    $tab.="</tr>";




				//total sebaran perbulan (harus dalam while)
				$a=array("1"=>"kg01","2"=>"kg02","3"=>"kg03","4"=>"kg04","5"=>"kg05","6"=>"kg06","7"=>"kg07","8"=>"kg08","9"=>"kg09","10"=>"kg10","11"=>"kg11","12"=>"kg12");
				for($i=1;$i<=12;$i++)
				{
					if(strlen($i)=='1')
                        {
                            $b="0".$i;
                        }
                        else
                        {
                            $b=$i;
                        }
					$totseb1="select kg".$b." from ".$dbname.".bgt_produksi_kbn_kg_vw where kodeblok='".$rList['kodeblok']."' and tahunbudget='".$rList['tahunbudget']."'";
					$totseb2=mysql_query($totseb1) or die (mysql_error());
					$totseb3=mysql_fetch_array($totseb2) or die (mysql_error());
					$hasil['kg'.$b]+=$totseb3['kg'.$b];
				}
				//untuk total


				$totSemua+=$totala;
				$totbjr+=$rList['bjr'];
				$totpkkprod+=$rList['pokokproduksi'];
				$totjpt+=$rOpt['jjgperpkk'];



			}//tutup while
                        $tab.="<thead><tr class=rowheader><td align=center colspan=4>".$_SESSION['lang']['total']."</td>";
				$tab.="<td align=right>".number_format($totpkkprod)."</td>";
				$tab.="<td align=right>".number_format($totbjr)."</td>";
				$tab.="<td align=right>".number_format($totjpt)."</td>";
				$tab.="<td align=right>".number_format($totSemua)."</td>";
				for($i=1;$i<=12;$i++)
				{
					/*echo "<pre>";
					print_r($hasil);
					echo "</pre>";*/
					$tab.="<td align=right>".number_format($hasil[$a[$i]],2)."</td>";
				}
                $tab.="<td></td>";
				$tab.="</tr></thead>";
	
                        $tab.="</tbody></table>";
        }
//        else
//        {
//            if($proses!='pdf')
//            {
//                $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable><thead>";
//                $tab.="<tr class=rowheader>";
//                $tab.="<td>".$_SESSION['lang']['budgetyear']."</td>";
//                $tab.="<td>".$_SESSION['lang']['unit']."</td>";
//                $tab.="<td>".$_SESSION['lang']['totalkg']."</td>";
//                foreach($arrBln as $listBulan)
//                {
//                    $tab.="<td>".$listBulan."</td>";
//                }
//                $tab.="</tr><thead><tbody>";
//                $tab.="<tr class=rowcontent>";
//                $tab.="<td>".$thnBudget."</td>";
//                $tab.="<td>".$optNm[$kodeOrg]."</td>";
//                $tab.="<td align=right>".number_format($dtJmlhKgStaun[$thnBudget][$kodeOrg],0)."</td>";
//                for($abc=1;$abc<13;$abc++)
//                {
//                    if(strlen($abc)<2)
//                    {
//                        $abcd="0".$abc;
//                    }
//                    else
//                    {
//                        $abcd=$abc;
//                    }
//
//                    $tab.="<td>".number_format((@$dtJmlhKg[$thnBudget][$kodeOrg][$abcd]/1000),0)."</td>";
//                }
//                $tab.="</tr>";
//                $tab.="</tbody></table>";
//            }
//        }
         $tab.="".$_SESSION['lang']['prodInfo']."";
            
	switch($proses)
        {
            case'preview':

           //PERINCIAN  PER AFDELING
            
            echo $tab;
            break;
            case'excel':
           
            $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHis");
            $nop_="laporanKebunProduksi_".$dte;
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
            case'pdf':
            if($modPil=='4')
            {
                exit("Error:Fitur Belum Tersedia");
            }
            if($kodeOrg==''||$thnBudget=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
//            echo"<pre>";
//            print_r($dtKdunit);
//            echo"</pre>";
//            exit();
           class PDF extends FPDF {
            function Header() {
            global $dtThnBudget;
            global $dtKdunit;
            global $dtJmlhKg;
            global $dtJjg;
            global $dtJmlhLuas;
            global $totKg;
            global $totJjg;
            global $totLuas;
            global $dbname;
            global $optNm;
            global $kodeOrg;
            global $totalUnit;
            global $modPil;
            global $spanLt;
            global $dtJmlhThnTnm;
            global $totaThntnm;
            global $arrBln;
            
        
  
         		$sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
				$qAlamat=mysql_query($sAlmat) or die(mysql_error());
				$rAlamat=mysql_fetch_assoc($qAlamat);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 10;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$rAlamat['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$rAlamat['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$rAlamat['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();	
                $this->Ln();
		$this->Ln();
               
                $this->SetFont('Arial','B',12);
                $this->Cell($width,$height,strtoupper($_SESSION['lang']['rProdKebun']),0,1,'C');
                $this->Ln();	
                //$this->Cell(275,5,strtoupper($_SESSION['lang']['rprodksiPabrik']),0,1,'C');
                $this->Cell($width,$height,$_SESSION['lang']['unit'].' : '.$optNm[$kodeOrg],0,1,'C');
                $this->SetFont('Arial','',8);
                $this->Cell(650,$height,$_SESSION['lang']['tanggal'],0,0,'R');
                $this->Cell(10,$height,':','',0,0,'R');
                $this->Cell(70,$height,date('d-m-Y H:i'),0,1,'R');
                $this->Cell(650,$height,$_SESSION['lang']['page'],0,0,'R');
                $this->Cell(10,$height,':','',0,0,'R');
                $this->Cell(70,$height,$this->PageNo(),0,1,'R');
                 $this->Cell(650,$height,'User',0,0,'R');
                $this->Cell(10,$height,':','',0,0,'R');
                $this->Cell(70,$height,$_SESSION['standard']['username'],0,1,'R');

                $this->Ln();
                $this->Ln();
                $height = 30;
                $this->SetFillColor(220,220,220);
                $this->SetFont('Arial','B',6);
                if($modPil!='2')
                {   
                        $this->Cell(15,$height,'No.',1,0,'C',1);
                        $this->Cell(55,$height,$_SESSION['lang']['thntanam'],1,1,'C',1);
                        $modPil=='0'?$this->SetFont('Arial','B',5):$this->SetFont('Arial','B',4);
                        $cols=($totalUnit*$spanLt)*30;
                        $col2=$spanLt*30;
                        $ypertama=$this->GetY();
                        $this->SetY($ypertama-30);
                        $xPertama=$this->GetX();
                        $this->SetX($xPertama+65);
                        $this->Cell($cols,10,"PERINCIAN  PER AFDELING",1,1,'C',1);

                        $yTinggi=$this->GetY();

                        $this->SetY($yTinggi);
                        $wth=$this->GetX();
                        $this->setX($wth+65);
                        $art=10;
                        //$wth=103;
                         foreach($dtKdunit as $brsKdUnit)
                         {
                            $a+=1;

                                 if($a==1)
                                 {
                                    $this->Cell($col2,10,$brsKdUnit,1,0,'C',1);
                                    $yTinggi=$this->GetY();
                                    $this->SetY($yTinggi+$art);
                                    $this->setX($wth+65);
                                     if($modPil=='0')
                                     {
                                        $this->Cell(30,10,$_SESSION['lang']['luas'],1,0,'C',1);
                                        $this->Cell(30,10,"JJG",1,0,'C',1);
                                        $this->Cell(30,10,"TON",1,0,'C',1);
                                     }
                                     else
                                     {

                                        $this->Cell(30,10,$_SESSION['lang']['luas'],1,0,'C',1);
                                        $this->Cell(30,10,$_SESSION['lang']['bjr'],1,0,'C',1);
                                        $this->Cell(30,10,$_SESSION['lang']['pkkproduktif'],1,0,'C',1);
                                        $this->Cell(30,10,"TON",1,0,'C',1);
                                     }

                                 }
                                 else
                                 {
                                    $yTinggi=$this->GetY();
                                    $xwith=$this->GetX();
                                    $this->SetY($yTinggi-10);
                                    $this->setX($xwith);
                                    $this->Cell($col2,10,$brsKdUnit,1,0,'C',1);
                                    $yTinggi=$this->GetY();
                                    //$xwith=$this->GetX($wth);
                                    $this->SetY($yTinggi+10);
                                    $this->setX($xwith);
                                    if($modPil=='0')
                                     {
                                    $this->Cell(30,10,$_SESSION['lang']['luas'],1,0,'C',1);
                                    $this->Cell(30,10,"JJG",1,0,'C',1);
                                    $this->Cell(30,10,"TON",1,0,'C',1);
                                     }
                                     else
                                     {
                                         $this->Cell(30,10,$_SESSION['lang']['luas'],1,0,'C',1);
                                        $this->Cell(30,10,$_SESSION['lang']['bjr'],1,0,'C',1);
                                        $this->Cell(30,10,$_SESSION['lang']['pkkproduktif'],1,0,'L',1);
                                        $this->Cell(30,10,"TON",1,0,'C',1);
                                     }
                                 }

                         }
                        $yTinggi=$this->GetY();
                        $xwith=$this->GetX();
                        $this->SetY($yTinggi-20);
                        $this->setX($xwith);
                        $this->Cell($col2,20,$_SESSION['lang']['total'],1,0,'C',1);
                        $yTinggi=$this->GetY();
                        $this->SetY($yTinggi+20);
                        $this->setX($xwith);
                        if($modPil=='0')
                        {
                        $this->Cell(30,10,$_SESSION['lang']['luas'],1,0,'C',1);
                        $this->Cell(30,10,"JJG",1,0,'C',1);
                        $this->Cell(30,10,"TON",1,1,'C',1);
                        }
                        else
                        {
                            $this->Cell(30,10,$_SESSION['lang']['luas'],1,0,'C',1);
                            $this->Cell(30,10,$_SESSION['lang']['bjr'],1,0,'C',1);
                            $this->Cell(30,10,$_SESSION['lang']['pkkproduktif'],1,0,'C',1);
                            $this->Cell(30,10,"TON",1,0,'C',1);
                        }
                }
                else
                {
                    $height=15;
                    $ard=1;
                        $this->Cell(50,$height,$_SESSION['lang']['budgetyear'],1,0,'C',1);
                        $this->Cell(55,$height,$_SESSION['lang']['unit'],1,0,'C',1);
                        $this->Cell(50,$height,$_SESSION['lang']['totalkg'],1,0,'C',1);
                        foreach($arrBln as $daftBln)
                        {
                            if($ard<12)
                            {
                                $this->Cell(50,$height,$daftBln,1,0,'C',1);
                            }
                            else
                            {
                                $this->Cell(50,$height,$daftBln,1,1,'C',1);
                            }
                            $ard++;
                        }
                }
            }
            
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
            }
            //================================

            $pdf=new PDF('L','pt','A4');
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 10;
            $pdf->AddPage();
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',4.5);
            if($modPil!='2')
            {
                $ang=1;
                $totalThn=count($dtThnBudget);
                $totalThn=$totalThn+1;

                foreach($dtThnBudget as $brsThnBudget)
                {
                    if($ang==1)
                    {
                        $bmilY=$pdf->GetY();
                        $bmilX=$pdf->GetX();
                        $pdf->SetY($bmilY+10);
                        $pdf->Cell(15,$height,$ang,1,0,'C');
                        $modPil=='0'?$pdf->Cell(50,$height,$brsThnBudget,1,0,'L'):$pdf->Cell(50,$height,$brsThnBudget."[".$dtJmlhThnTnm[$thnBudget][$brsThnBudget]."]",1,0,'L');

                    }
                    else if($ang<$totalThn)
                    {
                        $bmilY=$pdf->GetY();
                        $bmilX=$pdf->GetX();
                        $pdf->SetY($bmilY+10);
                        $pdf->Cell(15,$height,$ang,1,0,'C');
                       $modPil=='0'?$pdf->Cell(50,$height,$brsThnBudget,1,0,'L'):$pdf->Cell(50,$height,$brsThnBudget."[".$dtJmlhThnTnm[$thnBudget][$brsThnBudget]."]",1,0,'L');
                    }

                    $ang+=1;
                    for($pdfAngk=1;$pdfAngk<=($totalUnit+1);$pdfAngk++)
                    {
                         if($pdfAngk!=($totalUnit+1))
                         {
                            if($dtJmlhLuas[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget]==''||$dtJjg[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget]==''||$dtJmlhKg[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget]=='')
                            {
                                $dtJmlhKg[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget]=0;
                                $dtJjg[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget]=0;
                                $dtJmlhLuas[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget]=0;
                            }
                            @$toNdtJmlhKg[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget]=$dtJmlhKg[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget]/1000;
                            $pdf->Cell(30,10,number_format($dtJmlhLuas[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget],2),1,0,'R');
                            $pdf->Cell(30,10,number_format($dtJjg[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget],2),1,0,'R');
                            if($modPil!='0')
                            {
                                $pdf->Cell(30,10,number_format($dtJmlhPkk[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget],2),1,0,'R');
                            }
                            $pdf->Cell(30,10,number_format($toNdtJmlhKg[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget],2),1,0,'R');
                            $totKg[$brsThnBudget]+=$dtJmlhKg[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget];
                            $totJjg[$brsThnBudget]+=$dtJjg[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget];
                            $totLuas[$brsThnBudget]+=$dtJmlhLuas[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget];
                            $totPoko[$brsThnBudget]+=$dtJmlhPkk[$thnBudget][$dtKdunit[$abr]][$brsThnBudget];

                            $totKgAfd[$dtKdunit[$pdfAngk]]+=$dtJmlhKg[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget];
                            $totJjgAfd[$dtKdunit[$pdfAngk]]+=$dtJjg[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget];
                            $totLuasAfd[$dtKdunit[$pdfAngk]]+=$dtJmlhLuas[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget];
                            $totPokoAfd[$dtKdunit[$abr]]+=$dtJmlhPkk[$thnBudget][$dtKdunit[$abr]][$brsThnBudget];
                         }
                         else
                         {
                                $grndTotLuas+=$totLuas[$brsThnBudget];
                                $grndTotJjg+=$totJjg[$brsThnBudget];
                                $grndTotKg+=$totKg[$brsThnBudget];
                                $grndTotPokok+=$totPoko[$brsThnBudget];
                                @$toNtotKg[$brsThnBudget]=$totKg[$brsThnBudget]/1000;
                                $pdf->Cell(30,10,number_format($totLuas[$brsThnBudget],2),1,0,'R');
                                $pdf->Cell(30,10,number_format($totJjg[$brsThnBudget],2),1,0,'R');
                                if($modPil!='0')
                                {
                                    $pdf->Cell(30,10,number_format($totPoko[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget],2),1,0,'R');
                                }
                                $pdf->Cell(30,10,number_format($toNtotKg[$brsThnBudget],2),1,0,'R');

                         }
                    }


                }
                $dr=$pdf->GetY();
                $pdf->SetY($dr+10);
                $pdf->Cell(65,10,$_SESSION['lang']['total']."",1,0,'R');
                foreach($dtKdunit as $brsKdUnit)
                {
                    @$toNtotKgAfd[$brsKdUnit]=$totKgAfd[$brsKdUnit]/1000;
                    $pdf->Cell(30,10,number_format($totLuasAfd[$brsKdUnit],2),1,0,'R');
                    $modPil!='0'?$pdf->Cell(30,10,'',1,0,'R'):$pdf->Cell(30,10,number_format($totJjgAfd[$brsKdUnit],2),1,0,'R');
                    if($modPil!='0')
                    {
                        $pdf->Cell(30,10,number_format($totPokoAfd[$thnBudget][$dtKdunit[$pdfAngk]][$brsThnBudget],2),1,0,'R');
                    }
                    $pdf->Cell(30,10,number_format($toNtotKgAfd[$brsKdUnit],2),1,0,'R');
                }
               @$toNgrndTotKg=$grndTotKg/1000;
               $itung=count($dtKdunit);
               $luasancell=(30*3)*$itung;
                $pdf->Cell(30,10,number_format($grndTotLuas,2),1,0,'R');
                $modPil!='0'?$pdf->Cell(30,10,'',1,0,'R'):$pdf->Cell(30,10,number_format($grndTotJjg,2),1,0,'R');
                if($modPil!='0')
                {
                    $pdf->Cell(30,10,number_format($grndTotPokok,2),1,0,'R'); 
                }
                $pdf->Cell(30,10,number_format($toNgrndTotKg,2),1,1,'R');
             
            }
            else
            {
                $ard=1;
                $pdf->Cell(50,$height,$thnBudget,1,0,'C',1);
                $pdf->Cell(55,$height,$optNm[$kodeOrg],1,0,'C',1);
                $pdf->Cell(50,$height,number_format($dtJmlhKgStaun[$thnBudget][$kodeOrg],0),1,0,'R',1);
                for($tarik=1;$tarik<13;$tarik++)
                {
                    if(strlen($tarik)<2)
                    {
                        $dor="0".$tarik;
                    }
                    else
                    {
                        $dor=$tarik;
                    }
                    if($ard<12)
                    {
                        
                        $pdf->Cell(50,$height,(@$dtJmlhKg[$thnBudget][$kodeOrg][$dor]/1000),1,0,'R',1);
                    }
                    else
                    {
                        $pdf->Cell(50,$height,(@$dtJmlhKg[$thnBudget][$kodeOrg][$dor]/1000),1,1,'R',1);
                    }
                    $ard++;
                }
            }
            $pdf->Cell($luasancell,10,$_SESSION['lang']['prodInfo'],0,0,'L');
            $pdf->Output();	
                
                
            break;
                
            default:
            break;
        }
	
?>
