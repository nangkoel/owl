<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');
$param=$_POST;
if(isset($_GET['proses'])!=''){
    if(substr($_GET['proses'],0,5)=='excel'){
        $param=$_GET;
        $tab.= $_SESSION['lang']['biayapengobatan'];
        $brd=1;
        $bgcolor="bgcolor=#DEDEDE";
    }else{
        $param['proses']=$_GET['proses'];
    }
}
$optNmBy=makeOption($dbname, 'sdm_5jenisbiayapengobatan', 'kode,nama');
$optTpkary=makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$optRegional=makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
$optNmKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');

    if($param['ptId2']!=''){
        $whr.=" and b.kodeorganisasi='".$param['ptId2']."'";
    }
    if($param['unitId2']!=''){
        $whr="";
        $whr.=" and b.lokasitugas='".$param['ptId2']."'";
    }
    if($param['smstr']=='I'){
        $whr.=" and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
    }else{
        $whr.=" and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
    }
    $arrbln=array();
    $arrSmstrSatu=array("01"=>$_SESSION['lang']['jan'],"02"=>$_SESSION['lang']['feb'],"03"=>$_SESSION['lang']['mar'],"04"=>$_SESSION['lang']['apr'],"05"=>$_SESSION['lang']['mei'],"06"=>$_SESSION['lang']['jun']);
    $arrSmstrDua=array("07"=>$_SESSION['lang']['jul'],"08"=>$_SESSION['lang']['agt'],"09"=>$_SESSION['lang']['sep'],"10"=>$_SESSION['lang']['okt'],"11"=>$_SESSION['lang']['nov'],"12"=>$_SESSION['lang']['dec']);
    $param['smstr']=='I'?$arrbln=$arrSmstrSatu:$arrbln=$arrSmstrDua;
    if(($param['proses']=='preview')||($param['proses']=='excel')){
        #staff    
        $sstaff="select distinct sum(jlhbayar) as jmlhdbyr,periode,a.kodeorg,kodebiaya from 
                 ".$dbname.".sdm_pengobatanht  a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=0
                  group by kodebiaya,periode,a.kodeorg order by b.tipekaryawan";
        $qstaff=mysql_query($sstaff) or die(mysql_error($conn));
        while($rstaff=  mysql_fetch_assoc($qstaff)){
            $dtby[$optRegional[$rstaff['kodeorg']].$rstaff['kodebiaya'].$rstaff['periode']]+=$rstaff['jmlhdbyr'];
            //$dtby[$rstaff['kodebiaya'].$rstaff['periode']]=$rstaff['jmlhdbyr'];
            $kdBy[$rstaff['kodebiaya']]=$rstaff['kodebiaya'];
            $dtReg[$optRegional[$rstaff['kodeorg']]]=$optRegional[$rstaff['kodeorg']];
        }
        #non staff
        $snonstaff="select distinct sum(jlhbayar) as jmlhdbyr,periode,a.kodeorg,kodebiaya,b.tipekaryawan from 
                 ".$dbname.".sdm_pengobatanht a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where jlhbayar!=0 ".$whr." and b.tipekaryawan!=0
                  group by kodebiaya,periode,a.kodeorg,b.tipekaryawan order by b.tipekaryawan";
        //echo $snonstaff;
        $qnonstaff=mysql_query($snonstaff) or die(mysql_error($conn));
        while($rnonstaff=  mysql_fetch_assoc($qnonstaff)){
                $dtby[$optRegional[$rnonstaff['kodeorg']].$rnonstaff['kodebiaya'].$rnonstaff['periode']]+=$rnonstaff['jmlhdbyr'];
                $kdBy[$rnonstaff['kodebiaya']]=$rnonstaff['kodebiaya'];
                $tpKary[$rnonstaff['tipekaryawan']]=$rnonstaff['tipekaryawan'];
        }
        
        if($param['proses']!='excel'){
                $brd=0;
                $bgcolor="";
            }else{
                $tab.= $_SESSION['lang']['biayapengobatan'];
                $brd=1;
                $bgcolor="bgcolor=#DEDEDE";
            }
            $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable><thead><tr>";
            $tab.="<td  rowspan=2>".$_SESSION['lang']['regional']."</td>";
            foreach($kdBy as $lstBy){
                $tab.="<td colspan=7 align=center colspan=7>".$optNmBy[$lstBy]."</td>";
            }
            $tab.="<td rowspan=2>".$_SESSION['lang']['grnd_total']."</td></tr>";
            foreach($kdBy as $lstBy){
                foreach($arrbln as $lstBln=>$dftrbln){
                    $tab.="<td  align=center>".$dftrbln."</td>";
                }
                $tab.="<td  align=center>".$_SESSION['lang']['total']."</td>";
            }
            $tab.="</thead><tbody>";
            foreach($dtReg as $lstRegional){
                $tab.="<tr class=rowcontent>";
                $tab.="<td style='cursor:pointer;' onclick=getDetRegional('".$lstRegional."','')>".$lstRegional."</td>";
                foreach($kdBy as $lstBy){
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        if($dtby[$lstRegional.$lstBy.$prd]!=0){
                            $linkDet="title='detail data ".$lstRegional.",".$lstBy.",".$prd."' style='cursor:pointer;' onclick=getDetRegional('".$lstRegional."','".$lstBy."')";
                        }
                        $tab.="<td  align=right ".$linkDet.">".number_format($dtby[$lstRegional.$lstBy.$prd],0)."</td>";
                        $subTot[$lstRegional.$lstBy]+=$dtby[$lstRegional.$lstBy.$prd];
                    }
                $tab.="<td  align=right>".number_format($subTot[$lstRegional.$lstBy],0)."</td>";
                }
                $tab.="</tr>";
            }
    }
switch($param['proses'])
{
    case'preview':
        echo $tab;
    break;
    case'level1':
        echo $tab;
    break;
    case'excel':
        if($param['ptId2']==''){
            $param['ptId2']=$_SESSION['lang']['all'];
        }
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $nop_="RekapByPengobatan_".$param['ptId2'];
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
    case'getDetailRegional':
      $whr="";
      $ert=1;  
      if($param['ptId2']!=''){
          $whertd.="and induk='".$param['ptId2']."'";
      }
      $isir="kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment 
             where regional='".$param['regional']."')";
      if($param['unitId2']!=''){
          $isir="";
          $isir="kodeorganisasi='".$param['unitId2']."'";
      }
      $whr=" and a.kodeorg in (";
      $sreg="select distinct kodeorganisasi from ".$dbname.".organisasi where ".$isir." ".$whertd."
              order by namaorganisasi asc";
      //exit("error:".$sreg);
      $qreg=mysql_query($sreg) or die(mysql_error($conn));
      while($rreg=  mysql_fetch_assoc($qreg)){
          if($ert==1){
              $whr.="'".$rreg['kodeorganisasi']."'";
          }else{
              $whr.=",'".$rreg['kodeorganisasi']."'";
          }
          $ert++;
      }
      $whr.=")";
        if($param['smstr']=='I'){
            $whr.=" and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        }else{
            $whr.=" and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }
        if($param['byPeng']!=''){
            $whr.="and kodebiaya='".$param['byPeng']."'";
        }
        #staff    
        $sstaff="select distinct sum(jlhbayar) as jmlhdbyr,periode,a.kodeorg,kodebiaya from 
                 ".$dbname.".sdm_pengobatanht  a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=0
                  group by kodebiaya,periode,a.kodeorg order by b.tipekaryawan";
        //exit("error:".$sstaff);
        $qstaff=mysql_query($sstaff) or die(mysql_error($conn));
        while($rstaff=  mysql_fetch_assoc($qstaff)){
            $dtby[$rstaff['kodebiaya'].$rstaff['periode']]=$rstaff['jmlhdbyr'];
            $kdBy[$rstaff['kodebiaya']]=$rstaff['kodebiaya'];
        }
        #non staff
        $snonstaff="select distinct sum(jlhbayar) as jmlhdbyr,periode,a.kodeorg,kodebiaya,b.tipekaryawan from 
                 ".$dbname.".sdm_pengobatanht a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where jlhbayar!=0 ".$whr." and b.tipekaryawan!=0
                  group by kodebiaya,periode,a.kodeorg,b.tipekaryawan order by b.tipekaryawan";
        //echo $snonstaff;
        $qnonstaff=mysql_query($snonstaff) or die(mysql_error($conn));
        while($rnonstaff=  mysql_fetch_assoc($qnonstaff)){
                $dtnonby[$rnonstaff['tipekaryawan']][$rnonstaff['kodebiaya'].$rnonstaff['periode']]=$rnonstaff['jmlhdbyr'];
                $kdBy[$rnonstaff['kodebiaya']]=$rnonstaff['kodebiaya'];
                $tpKary[$rnonstaff['tipekaryawan']]=$rnonstaff['tipekaryawan'];
        }
        
        if($param['proses']!='excel'){
                $brd=0;
                $bgcolor="";
            }else{
                $tab.= $_SESSION['lang']['biayapengobatan'];
                $brd=1;
                $bgcolor="bgcolor=#DEDEDE";
            }
            $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable><thead><tr>";
            $tab.="<td rowspan=2>No.</td>";
            $tab.="<td rowspan=2>".$_SESSION['lang']['tipekaryawan']."</td>";
            foreach($kdBy as $lstBy){
                $tab.="<td colspan=7 align=center>".$optNmBy[$lstBy]."</td>";
            }
            $tab.="<td rowspan=2>".$_SESSION['lang']['grnd_total']."</td></tr>";
            $tab.="<tr>";
            foreach($kdBy as $lstBy){
                foreach($arrbln as $lstBln=>$dftrbln){
                    $tab.="<td  align=center>".$dftrbln."</td>";
                }
                $tab.="<td  align=center>".$_SESSION['lang']['total']."</td>";
            }
            $tab.="</tr></thead><tbody>";
            $tab.="<tr class=rowcontent>";
            $tab.="<td>1</td>";
            $tab.="<td style='cursor:pointer;' onclick=detailStaff('0','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."','".$param['regional']."','".$param['byPeng']."')>Staff</td>";
            foreach($kdBy as $lstBy){
                foreach($arrbln as $lstBln=>$dftrbln){

                    $prd=$param['thn']."-".$lstBln;
                    $det="style='cursor:pointer;' onclick=detailDt2('0','".$lstBy."','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."','".$param['regional']."')";    
                    $tab.="<td align=right ".$det.">".number_format($dtby[$lstBy.$prd],0)."</td>";
                    $totPerby[$lstBy]+=$dtby[$lstBy.$prd];
                    $grndtotstaff+=$dtby[$lstBy.$prd];
                    $totPerbln[$lstBy.$prd]+=$dtby[$lstBy.$prd];
                    $totBiaya[$lstBy]+=$dtby[$lstBy.$prd];
                }
                 $tab.="<td align=right>".number_format($totPerby[$lstBy],0)."</td>";
            }
            $tab.="<td align=right>".number_format($grndtotstaff,0)."</td>";
            $tab.="</tr>";
            $now=1;
            foreach($tpKary as $lstKary){
                $now+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$now."</td>";
                    $tab.="<td>".$optTpkary[$lstKary]."</td>";
               foreach($kdBy as $lstBy){
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        $det="style='cursor:pointer;' onclick=detailDt2('".$lstKary."','".$lstBy."','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."')";    
                        $tab.="<td align=right ".$det.">".number_format($dtnonby[$lstKary][$lstBy.$prd],0)."</td>";
                        $grndtotnonstaff[$lstKary]+=$dtnonby[$lstKary][$lstBy.$prd];
                        $totPerbln[$lstBy.$prd]+=$dtnonby[$lstKary][$lstBy.$prd];
                        $totPerby2[$lstKary.$lstBy]+=$dtnonby[$lstKary][$lstBy.$prd];
                        $totBiaya[$lstBy]+=$dtnonby[$lstKary][$lstBy.$prd];
                    }
                    $tab.="<td align=right>".number_format($totPerby2[$lstKary.$lstBy],0)."</td>";
                }
                $tab.="<td align=right>".number_format($grndtotnonstaff[$lstKary],0)."</td>";
                $tab.="</tr>";
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td colspan=2>".$_SESSION['lang']['grnd_total']."</td>";
           foreach($kdBy as $lstBy){
                foreach($arrbln as $lstBln=>$dftrbln){
                    $prd=$param['thn']."-".$lstBln;
                    $tab.="<td align=right>".number_format($totPerbln[$lstBy.$prd],0)."</td>";
                    $grndtotsmua+=$totPerbln[$lstBy.$prd];
                }
                $tab.="<td align=right>".number_format($totBiaya[$lstBy],0)."</td>";
            }
            $tab.="<td align=right>".number_format($grndtotsmua,0)."</td>";
            $tab.="</tr>";
            $tab.="</tbody></table>";
        $tab.="<button class=mybutton onclick=zExcelDt(event,'sdm_slave_2biayapengobatan1.php','".$param['regional']."','".$param['byPeng']."','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."')>".$_SESSION['lang']['excel']."</button>
               <button class=mybutton onclick=kembali(0)>".$_SESSION['lang']['back']."</button>";
        echo $tab;
    break;
   
    case'excelgetDetail2':
        $whr="";
      $ert=1;  
      if($param['ptId2']!=''){
          $whertd.="and induk='".$param['ptId2']."'";
      }
      $isir="kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment 
             where regional='".$param['regional']."')";
      if($param['unitId2']!=''){
          $isir="";
          $isir="kodeorganisasi='".$param['unitId2']."'";
      }
      $whr=" and a.kodeorg in (";
      $sreg="select distinct kodeorganisasi from ".$dbname.".organisasi where ".$isir." ".$whertd."
              order by namaorganisasi asc";
      //exit("error:".$sreg);
      $qreg=mysql_query($sreg) or die(mysql_error($conn));
      while($rreg=  mysql_fetch_assoc($qreg)){
          if($ert==1){
              $whr.="'".$rreg['kodeorganisasi']."'";
          }else{
              $whr.=",'".$rreg['kodeorganisasi']."'";
          }
          $ert++;
      }
      $whr.=")";
        if($param['smstr']=='I'){
            $whr.=" and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        }else{
            $whr.=" and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }
        if($param['byPeng']!=''){
            $whr.="and kodebiaya='".$param['byPeng']."'";
        }
        #staff    
        $sstaff="select distinct sum(jlhbayar) as jmlhdbyr,periode,a.kodeorg,kodebiaya from 
                 ".$dbname.".sdm_pengobatanht  a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=0
                  group by kodebiaya,periode,a.kodeorg order by b.tipekaryawan";
        //exit("error:".$sstaff);
        $qstaff=mysql_query($sstaff) or die(mysql_error($conn));
        while($rstaff=  mysql_fetch_assoc($qstaff)){
            $dtby[$rstaff['kodebiaya'].$rstaff['periode']]=$rstaff['jmlhdbyr'];
            $kdBy[$rstaff['kodebiaya']]=$rstaff['kodebiaya'];
        }
        #non staff
        $snonstaff="select distinct sum(jlhbayar) as jmlhdbyr,periode,a.kodeorg,kodebiaya,b.tipekaryawan from 
                 ".$dbname.".sdm_pengobatanht a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where jlhbayar!=0 ".$whr." and b.tipekaryawan!=0
                  group by kodebiaya,periode,a.kodeorg,b.tipekaryawan order by b.tipekaryawan";
        //echo $snonstaff;
        $qnonstaff=mysql_query($snonstaff) or die(mysql_error($conn));
        while($rnonstaff=  mysql_fetch_assoc($qnonstaff)){
                $dtnonby[$rnonstaff['tipekaryawan']][$rnonstaff['kodebiaya'].$rnonstaff['periode']]=$rnonstaff['jmlhdbyr'];
                $kdBy[$rnonstaff['kodebiaya']]=$rnonstaff['kodebiaya'];
                $tpKary[$rnonstaff['tipekaryawan']]=$rnonstaff['tipekaryawan'];
        }
        
       
            $tab.="<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead><tr ".$bgcolor.">";
            $tab.="<td rowspan=2>No.</td>";
            $tab.="<td rowspan=2>".$_SESSION['lang']['tipekaryawan']."</td>";
            foreach($kdBy as $lstBy){
                $tab.="<td colspan=7 align=center>".$optNmBy[$lstBy]."</td>";
            }
            $tab.="<td rowspan=2>".$_SESSION['lang']['grnd_total']."</td></tr>";
            $tab.="<tr  ".$bgcolor.">";
            foreach($kdBy as $lstBy){
                foreach($arrbln as $lstBln=>$dftrbln){
                    $tab.="<td  align=center>".$dftrbln."</td>";
                }
                $tab.="<td  align=center>".$_SESSION['lang']['total']."</td>";
            }
            $tab.="</tr></thead><tbody>";
            $tab.="<tr class=rowcontent>";
            $tab.="<td>1</td>";
            $tab.="<td style='cursor:pointer;' onclick=detailStaff('0','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."','".$param['regional']."','".$param['byPeng']."')>Staff</td>";
            foreach($kdBy as $lstBy){
                foreach($arrbln as $lstBln=>$dftrbln){

                    $prd=$param['thn']."-".$lstBln;
                    $det="style='cursor:pointer;' onclick=detailDt2('0','".$lstBy."','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."','".$param['regional']."')";    
                    $tab.="<td align=right ".$det.">".number_format($dtby[$lstBy.$prd],0)."</td>";
                    $totPerby[$lstBy]+=$dtby[$lstBy.$prd];
                    $grndtotstaff+=$dtby[$lstBy.$prd];
                    $totPerbln[$lstBy.$prd]+=$dtby[$lstBy.$prd];
                    $totBiaya[$lstBy]+=$dtby[$lstBy.$prd];
                }
                 $tab.="<td align=right>".number_format($totPerby[$lstBy],0)."</td>";
            }
            $tab.="<td align=right>".number_format($grndtotstaff,0)."</td>";
            $tab.="</tr>";
            $now=1;
            foreach($tpKary as $lstKary){
                $now+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$now."</td>";
                    $tab.="<td>".$optTpkary[$lstKary]."</td>";
               foreach($kdBy as $lstBy){
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        $det="style='cursor:pointer;' onclick=detailDt2('".$lstKary."','".$lstBy."','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."')";    
                        $tab.="<td align=right ".$det.">".number_format($dtnonby[$lstKary][$lstBy.$prd],0)."</td>";
                        $grndtotnonstaff[$lstKary]+=$dtnonby[$lstKary][$lstBy.$prd];
                        $totPerbln[$lstBy.$prd]+=$dtnonby[$lstKary][$lstBy.$prd];
                        $totPerby2[$lstKary.$lstBy]+=$dtnonby[$lstKary][$lstBy.$prd];
                        $totBiaya[$lstBy]+=$dtnonby[$lstKary][$lstBy.$prd];
                    }
                    $tab.="<td align=right>".number_format($totPerby2[$lstKary.$lstBy],0)."</td>";
                }
                $tab.="<td align=right>".number_format($grndtotnonstaff[$lstKary],0)."</td>";
                $tab.="</tr>";
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td colspan=2>".$_SESSION['lang']['grnd_total']."</td>";
           foreach($kdBy as $lstBy){
                foreach($arrbln as $lstBln=>$dftrbln){
                    $prd=$param['thn']."-".$lstBln;
                    $tab.="<td align=right>".number_format($totPerbln[$lstBy.$prd],0)."</td>";
                    $grndtotsmua+=$totPerbln[$lstBy.$prd];
                }
                $tab.="<td align=right>".number_format($totBiaya[$lstBy],0)."</td>";
            }
            $tab.="<td align=right>".number_format($grndtotsmua,0)."</td>";
            $tab.="</tr>";
            $tab.="</tbody></table>";
         
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $nop_="detailPengobatan";
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
    case'getDetail3':
 $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>
        <tr class=rowheader>
        <td width=50></td>
        <td>No</td>
        <td width=100>".$_SESSION['lang']['notransaksi']."</td>
        <td width=50>".$_SESSION['lang']['periode']."</td>
        <td width=30>".$_SESSION['lang']['tanggal']."</td>
        <td width=200>".$_SESSION['lang']['lokasitugas']."</td>
        <td width=200>".$_SESSION['lang']['namakaryawan']."</td>
        <td width=200>".$_SESSION['lang']['jabatan']."</td>
        <td>".$_SESSION['lang']['pasien']."</td>
        <td width=150>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['pasien']."</td>
        <td width=150>".$_SESSION['lang']['rumahsakit']."</td>
        <td width=50>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
        <td width=90>".$_SESSION['lang']['nilaiklaim']."</td>
        <td>".$_SESSION['lang']['dibayar']."</td>
        <td width=90>".$_SESSION['lang']['perusahaan']."</td>
        <td width=90>".$_SESSION['lang']['karyawan']."</td>
        <td width=90>Jamsostek</td>      
        <td>".$_SESSION['lang']['diagnosa']."</td>
        <td>".$_SESSION['lang']['keterangan']."</td>
    </tr>
    </thead><tbody>"; 
        $str="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, c.lokasitugas as loktug,c.kodejabatan, nama 
              from ".$dbname.".sdm_pengobatanht a left join ".$dbname.".sdm_5rs b on a.rs=b.id 
              left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid 
              left join ".$dbname.".sdm_5diagnosa d on a.diagnosa=d.id 
              left join ".$dbname.".sdm_karyawankeluarga f
              on a.ygsakit=f.nomor
              where a.periode like '".$param['periode']."%' and a.kodebiaya='".$param['byPeng']."'
              and a.kodeorg = '".$param['unitId2']."' and c.tipekaryawan='".$param['tipeKary']."'
              order by a.jlhbayar desc,a.updatetime desc, a.tanggal desc";
    //echo $str;
    $res=mysql_query($str) or mysql_error($conn);
    $no=0;
    while($bar=mysql_fetch_object($res))
    {
        $no+=1;

        $pasien='';
        //get hubungan keluarga
        $stru="select hubungankeluarga from ".$dbname.".sdm_karyawankeluarga 
              where nomor=".$bar->ygsakit;
        $resu=mysql_query($stru);
        while($baru=mysql_fetch_object($resu))
        {
            $pasien=$baru->hubungankeluarga;
        }
        if($pasien=='')$pasien='AsIs';	

        $tab.="<tr class=rowcontent>
            <td>&nbsp <img src=images/zoom.png title='view' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)></td>
            <td>".$no."</td>
            <td>".$bar->notransaksi."</td>
            <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
            <td>".tanggalnormal($bar->tanggal)."</td>
            <td>".$bar->loktug."</td>
            <td>".$bar->namakaryawan."</td>
            <td>".$optJabatan[$bar->kodejabatan]."</td>
            <td>".$pasien."</td>
            <td>".$bar->nama."</td>
            <td>".$bar->namars."[".$bar->kota."]"."</td>
            <td>".$bar->kodebiaya."</td>
            <td align=right>".number_format($bar->totalklaim,0,'.',',')."</td>
            <td align=right>".number_format($bar->jlhbayar,0,'.',',')."</td>
            <td align=right>".number_format($bar->bebanperusahaan,0,'.',',')."</td>
            <td align=right>".number_format($bar->bebankaryawan,0,'.',',')."</td>
            <td align=right>".number_format($bar->bebanjamsostek,0,'.',',')."</td>     
            <td>".$bar->ketdiag."</td>
            <td>".$bar->keterangan."</td>
        </tr>";	  	
        $totKlaim+=$bar->totalklaim;
        $totJlhByr+=$bar->jlhbayar;
        $totBbnprshn+=$bar->bebanperusahaan;
        $totBbnKary+=$bar->bebankaryawan;
        $totBbnJam+=$bar->bebanjamsostek;
    }    
    $tab.="<tr class=rowcontent><td colspan=12>".$_SESSION['lang']['total']."</td>";
    $tab.="<td align=right>".number_format($totKlaim,0)."</td>";
    $tab.="<td align=right>".number_format($totJlhByr,0)."</td>";
    $tab.="<td align=right>".number_format($totBbnprshn,0)."</td>";
    $tab.="<td align=right>".number_format($totBbnKary,0)."</td>";
    $tab.="<td align=right>".number_format($totBbnJam,0)."</td>";
    $tab.="<td colspan=2>&nbsp</td></tr>";
    $tab.="</tbody></table>";
        $tab.="<button class=mybutton onclick=zExcelDt2(event,'sdm_slave_2biayapengobatan.php','".$param['unitId2']."','".$param['tipeKary']."','".$param['periode']."','".$param['byPeng']."')>".$_SESSION['lang']['excel']."</button>
               <button class=mybutton onclick=kembali(2)>".$_SESSION['lang']['back']."</button>";
        echo $tab;
    break;
     case'excelgetDetail3':
 $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable><thead>
        <tr class=rowheader>
        
        <td  ".$bgcolor.">No</td>
        <td width=100  ".$bgcolor.">".$_SESSION['lang']['notransaksi']."</td>
        <td width=50  ".$bgcolor.">".$_SESSION['lang']['periode']."</td>
        <td width=30  ".$bgcolor.">".$_SESSION['lang']['tanggal']."</td>
        <td width=200 ".$bgcolor.">".$_SESSION['lang']['lokasitugas']."</td>
        <td width=200 ".$bgcolor.">".$_SESSION['lang']['namakaryawan']."</td>
        <td width=200 ".$bgcolor.">".$_SESSION['lang']['jabatan']."</td>
        <td ".$bgcolor.">".$_SESSION['lang']['pasien']."</td>
        <td width=150 ".$bgcolor.">".$_SESSION['lang']['nama']." ".$_SESSION['lang']['pasien']."</td>
        <td width=150 ".$bgcolor.">".$_SESSION['lang']['rumahsakit']."</td>
        <td width=50 ".$bgcolor.">".$_SESSION['lang']['jenisbiayapengobatan']."</td>
        <td width=90 ".$bgcolor.">".$_SESSION['lang']['nilaiklaim']."</td>
        <td ".$bgcolor.">".$_SESSION['lang']['dibayar']."</td>
        <td width=90 ".$bgcolor.">".$_SESSION['lang']['perusahaan']."</td>
        <td width=90 ".$bgcolor.">".$_SESSION['lang']['karyawan']."</td>
        <td width=90 ".$bgcolor.">Jamsostek</td>      
        <td ".$bgcolor.">".$_SESSION['lang']['diagnosa']."</td>
        <td ".$bgcolor.">".$_SESSION['lang']['keterangan']."</td>
    </tr>
    </thead><tbody>"; 
        $str="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, c.lokasitugas as loktug,c.kodejabatan, nama 
              from ".$dbname.".sdm_pengobatanht a left join ".$dbname.".sdm_5rs b on a.rs=b.id 
              left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid 
              left join ".$dbname.".sdm_5diagnosa d on a.diagnosa=d.id 
              left join ".$dbname.".sdm_karyawankeluarga f
              on a.ygsakit=f.nomor
              where a.periode like '".$param['periode']."%' and a.kodebiaya='".$param['byPeng']."'
              and a.kodeorg = '".$param['unitId2']."' and c.tipekaryawan='".$param['tipeKary']."'
              order by a.jlhbayar desc,a.updatetime desc, a.tanggal desc";
     //echo $str;
    $res=mysql_query($str) or mysql_error($conn);
    $no=0;
    while($bar=mysql_fetch_object($res))
    {
        $no+=1;

        $pasien='';
        //get hubungan keluarga
        $stru="select hubungankeluarga from ".$dbname.".sdm_karyawankeluarga 
              where nomor=".$bar->ygsakit;
        $resu=mysql_query($stru);
        while($baru=mysql_fetch_object($resu))
        {
            $pasien=$baru->hubungankeluarga;
        }
        if($pasien=='')$pasien='AsIs';	

        $tab.="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$bar->notransaksi."</td>
            <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
            <td>".tanggalnormal($bar->tanggal)."</td>
            <td>".$bar->loktug."</td>
            <td>".$bar->namakaryawan."</td>
            <td>".$optJabatan[$bar->kodejabatan]."</td>
            <td>".$pasien."</td>
            <td>".$bar->nama."</td>
            <td>".$bar->namars."[".$bar->kota."]"."</td>
            <td>".$bar->kodebiaya."</td>
            <td align=right>".number_format($bar->totalklaim,0,'.',',')."</td>
            <td align=right>".number_format($bar->jlhbayar,0,'.',',')."</td>
            <td align=right>".number_format($bar->bebanperusahaan,0,'.',',')."</td>
            <td align=right>".number_format($bar->bebankaryawan,0,'.',',')."</td>
            <td align=right>".number_format($bar->bebanjamsostek,0,'.',',')."</td>     
            <td>".$bar->ketdiag."</td>
            <td>".$bar->keterangan."</td>
        </tr>";	  	
        $totKlaim+=$bar->totalklaim;
        $totJlhByr+=$bar->jlhbayar;
        $totBbnprshn+=$bar->bebanperusahaan;
        $totBbnKary+=$bar->bebankaryawan;
        $totBbnJam+=$bar->bebanjamsostek;
    }    
    $tab.="<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['total']."</td>";
    $tab.="<td align=right>".number_format($totKlaim,0)."</td>";
    $tab.="<td align=right>".number_format($totJlhByr,0)."</td>";
    $tab.="<td align=right>".number_format($totBbnprshn,0)."</td>";
    $tab.="<td align=right>".number_format($totBbnKary,0)."</td>";
    $tab.="<td align=right>".number_format($totBbnJam,0)."</td>";
    $tab.="<td colspan=2>&nbsp</td></tr>";
    $tab.="</tbody></table>";
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $nop_="detailPengobatan2_".$param['ptId2'];
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
    case'detailStaff':
      $whr="";
      $ert=1;  
      if($param['ptId2']!=''){
          $whertd.="and induk='".$param['ptId2']."'";
      }
      $isir="kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment 
             where regional='".$param['regional']."')";
      if($param['unitId2']!=''){
          $isir="";
          $isir="kodeorganisasi='".$param['unitId2']."'";
      }
      $whr=" and a.kodeorg in (";
      $sreg="select distinct kodeorganisasi from ".$dbname.".organisasi where ".$isir." ".$whertd."
              order by namaorganisasi asc";
      //exit("error:".$sreg);
      $qreg=mysql_query($sreg) or die(mysql_error($conn));
      while($rreg=  mysql_fetch_assoc($qreg)){
          if($ert==1){
              $whr.="'".$rreg['kodeorganisasi']."'";
          }else{
              $whr.=",'".$rreg['kodeorganisasi']."'";
          }
          $ert++;
      }
      $whr.=")";
        if($param['smstr']=='I'){
            $whr.=" and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        }else{
            $whr.=" and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }
        if($param['byPeng']!=''){
            $whr.=" and kodebiaya='".$param['byPeng']."'";
        }
        #staff    
        $sstaff="select distinct sum(jlhbayar) as jmlhdbyr,periode,b.bagian,kodebiaya from 
                 ".$dbname.".sdm_pengobatanht  a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=0
                  group by kodebiaya,periode,b.bagian order by b.tipekaryawan";
        //exit("error:".$sstaff);
        $qstaff=mysql_query($sstaff) or die(mysql_error($conn));
        while($rstaff=  mysql_fetch_assoc($qstaff)){
            $dtby[$rstaff['bagian'].$rstaff['kodebiaya'].$rstaff['periode']]+=$rstaff['jmlhdbyr'];
            //$dtby[$rstaff['kodebiaya'].$rstaff['periode']]=$rstaff['jmlhdbyr'];
            $kdBy[$rstaff['kodebiaya']]=$rstaff['kodebiaya'];
            $dtBagian[$rstaff['bagian']]=$rstaff['bagian'];
        }
       
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
        $tab.="<tr>";
        $tab.="<td rowspan=2>".$_SESSION['lang']['bagian']."</td>";
         foreach($kdBy as $lstBy){
                $tab.="<td colspan=7 align=center colspan=7>".$optNmBy[$lstBy]."</td>";
            }
            $tab.="<td rowspan=2>".$_SESSION['lang']['grnd_total']."</td></tr>";
            foreach($kdBy as $lstBy){
                foreach($arrbln as $lstBln=>$dftrbln){
                    $tab.="<td  align=center>".$dftrbln."</td>";
                }
                $tab.="<td  align=center>".$_SESSION['lang']['total']."</td>";
            }
            $tab.="</thead><tbody>";
            foreach($dtBagian as $lstRegional){
                $tab.="<tr class=rowcontent>";
                $tab.="<td style='cursor:pointer;' onclick=getDetDept('".$lstRegional."','".$param['regional']."','".$param['ptId2']."','".$param['unitId2']."','".$param['thn']."','".$param['smstr']."','".$param['byPeng']."')>".$lstRegional."</td>";
                foreach($kdBy as $lstBy){
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        if($dtby[$lstRegional.$lstBy.$prd]!=0){
                            $linkDet="title='detail data ".$lstRegional.",".$lstBy.",".$prd."' style='cursor:pointer;' onclick=getDetailDept('".$lstRegional."','".$lstBy."','".$param['regional']."','".$param['ptId2']."','".$param['unitId2']."','".$param['thn']."','".$param['smstr']."')";
                        }
                        $tab.="<td  align=right ".$linkDet.">".number_format($dtby[$lstRegional.$lstBy.$prd],0)."</td>";
                        $subTot[$lstRegional.$lstBy]+=$dtby[$lstRegional.$lstBy.$prd];
                        $grTot[$lstRegional]+=$dtby[$lstRegional.$lstBy.$prd];
                        $grTotPrd[$lstBy.$prd]+=$dtby[$lstRegional.$lstBy.$prd];
                    }
                $tab.="<td  align=right>".number_format($subTot[$lstRegional.$lstBy],0)."</td>";
                }
                $tab.="<td  align=right>".number_format($grTot[$lstRegional],0)."</td>";
                $tab.="</tr>";
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$_SESSION['lang']['total']."</td>";
            foreach($kdBy as $lstBy){
                    
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        $tab.="<td  align=right>".number_format($grTotPrd[$lstBy.$prd],0)."</td>";
                        $totDtAl+=$grTotPrd[$lstBy.$prd];
                        $totDtper[$lstBy]+=$grTotPrd[$lstBy.$prd];
                    }
                    $tab.="<td  align=right>".number_format($totDtper[$lstBy],0)."</td>";
                    
            }
            $tab.="<td  align=right>".number_format($totDtAl,0)."</td>";
            $tab.="</tr>";
        
        $tab.="</tbody></table>";
        $tab.="<button class=mybutton onclick=zExcelStaff(event,'sdm_slave_2biayapengobatan1.php','0','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."','".$param['regional']."','".$param['byPeng']."')>".$_SESSION['lang']['excel']."</button>
               <button class=mybutton onclick=kembali(1)>".$_SESSION['lang']['back']."</button>";
            echo $tab;
    break;
    case'excelStaffDet':
      $whr="";
      $ert=1;  
      if($param['ptId2']!=''){
          $whertd.="and induk='".$param['ptId2']."'";
      }
      $isir="kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment 
             where regional='".$param['regional']."')";
      if($param['unitId2']!=''){
          $isir="";
          $isir="kodeorganisasi='".$param['unitId2']."'";
      }
      $whr=" and a.kodeorg in (";
      $sreg="select distinct kodeorganisasi from ".$dbname.".organisasi where ".$isir." ".$whertd."
              order by namaorganisasi asc";
      //exit("error:".$sreg);
      $qreg=mysql_query($sreg) or die(mysql_error($conn));
      while($rreg=  mysql_fetch_assoc($qreg)){
          if($ert==1){
              $whr.="'".$rreg['kodeorganisasi']."'";
          }else{
              $whr.=",'".$rreg['kodeorganisasi']."'";
          }
          $ert++;
      }
      $whr.=")";
        if($param['smstr']=='I'){
            $whr.=" and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        }else{
            $whr.=" and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }
        if($param['byPeng']!=''){
            $whr.=" and kodebiaya='".$param['byPeng']."'";
        }
        #staff    
        $sstaff="select distinct sum(jlhbayar) as jmlhdbyr,periode,b.bagian,kodebiaya from 
                 ".$dbname.".sdm_pengobatanht  a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=0
                  group by kodebiaya,periode,b.bagian order by b.tipekaryawan";
        //exit("error:".$sstaff);
        $qstaff=mysql_query($sstaff) or die(mysql_error($conn));
        while($rstaff=  mysql_fetch_assoc($qstaff)){
            $dtby[$rstaff['bagian'].$rstaff['kodebiaya'].$rstaff['periode']]+=$rstaff['jmlhdbyr'];
            //$dtby[$rstaff['kodebiaya'].$rstaff['periode']]=$rstaff['jmlhdbyr'];
            $kdBy[$rstaff['kodebiaya']]=$rstaff['kodebiaya'];
            $dtBagian[$rstaff['bagian']]=$rstaff['bagian'];
        }
       
        $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable><thead>";
        $tab.="<tr ".$bgcolor.">";
        $tab.="<td rowspan=2>".$_SESSION['lang']['bagian']."</td>";
         foreach($kdBy as $lstBy){
                $tab.="<td colspan=7 align=center colspan=7>".$optNmBy[$lstBy]."</td>";
            }
            $tab.="<td rowspan=2>".$_SESSION['lang']['grnd_total']."</td></tr><tr ".$bgcolor.">";
            foreach($kdBy as $lstBy){
                foreach($arrbln as $lstBln=>$dftrbln){
                    $tab.="<td  align=center>".$dftrbln."</td>";
                }
                $tab.="<td  align=center>".$_SESSION['lang']['total']."</td>";
            }
            $tab.="</thead><tbody>";
            foreach($dtBagian as $lstRegional){
                $tab.="<tr class=rowcontent>";
                $tab.="<td style='cursor:pointer;' onclick=getDetDept('".$lstRegional."','".$param['regional']."','".$param['ptId2']."','".$param['unitId2']."','".$param['thn']."','".$param['smstr']."','".$param['byPeng']."')>".$lstRegional."</td>";
                foreach($kdBy as $lstBy){
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        if($dtby[$lstRegional.$lstBy.$prd]!=0){
                            $linkDet="title='detail data ".$lstRegional.",".$lstBy.",".$prd."' style='cursor:pointer;' onclick=getDetailDept('".$lstRegional."','".$lstBy."')";
                        }
                        $tab.="<td  align=right ".$linkDet.">".number_format($dtby[$lstRegional.$lstBy.$prd],0)."</td>";
                        $subTot[$lstRegional.$lstBy]+=$dtby[$lstRegional.$lstBy.$prd];
                        $grTot[$lstRegional]+=$dtby[$lstRegional.$lstBy.$prd];
                        $grTotPrd[$lstBy.$prd]+=$dtby[$lstRegional.$lstBy.$prd];
                    }
                $tab.="<td  align=right>".number_format($subTot[$lstRegional.$lstBy],0)."</td>";
                }
                $tab.="<td  align=right>".number_format($grTot[$lstRegional],0)."</td>";
                $tab.="</tr>";
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$_SESSION['lang']['total']."</td>";
            foreach($kdBy as $lstBy){
                    
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        $tab.="<td  align=right>".number_format($grTotPrd[$lstBy.$prd],0)."</td>";
                        $totDtAl+=$grTotPrd[$lstBy.$prd];
                        $totDtper[$lstBy]+=$grTotPrd[$lstBy.$prd];
                    }
                    $tab.="<td  align=right>".number_format($totDtper[$lstBy],0)."</td>";
                    
            }
            $tab.="<td  align=right>".number_format($totDtAl,0)."</td>";
            $tab.="</tr>";
        
        $tab.="</tbody></table>";
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $nop_="detailPengobatan2Staff_".$param['ptId2'];
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
    case'getDetDept':
      $whr="";
      $ert=1;  
      if($param['ptId2']!=''){
          $whertd.="and induk='".$param['ptId2']."'";
      }
      $isir="kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment 
             where regional='".$param['regional']."')";
      if($param['unitId2']!=''){
          $isir="";
          $isir="kodeorganisasi='".$param['unitId2']."'";
      }
      $whr=" and a.kodeorg in (";
      $sreg="select distinct kodeorganisasi from ".$dbname.".organisasi where ".$isir." ".$whertd."
              order by namaorganisasi asc";
      //exit("error:".$sreg);
      $qreg=mysql_query($sreg) or die(mysql_error($conn));
      while($rreg=  mysql_fetch_assoc($qreg)){
          if($ert==1){
              $whr.="'".$rreg['kodeorganisasi']."'";
          }else{
              $whr.=",'".$rreg['kodeorganisasi']."'";
          }
          $ert++;
      }
      $whr.=")";
        if($param['smstr']=='I'){
            $whr.=" and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        }else{
            $whr.=" and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }
        if($param['byPeng']!=''){
            $whr.=" and kodebiaya='".$param['byPeng']."'";
        }
        #staff    
        $sstaff="select distinct sum(jlhbayar) as jmlhdbyr,periode,b.bagian,kodebiaya,a.karyawanid,b.lokasitugas from 
                 ".$dbname.".sdm_pengobatanht  a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=0 and b.bagian='".$param['bagian']."'
                  group by a.karyawanid,periode  order by b.namakaryawan asc";
        //echo $sstaff;
        //exit("error:".$sstaff);
        $qstaff=mysql_query($sstaff) or die(mysql_error($conn));
        while($rstaff=  mysql_fetch_assoc($qstaff)){
            $dtby[$rstaff['karyawanid'].$rstaff['kodebiaya'].$rstaff['periode']]+=$rstaff['jmlhdbyr'];
            $dtTgs[$rstaff['karyawanid']]=$rstaff['lokasitugas'];
            $kdBy[$rstaff['kodebiaya']]=$rstaff['kodebiaya'];
            $dtBagian[$rstaff['karyawanid']]=$rstaff['karyawanid'];
        }
       
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead><tr ".$bgcolor."><td rowspan=2>".$_SESSION['lang']['namakaryawan']."</td>";
        foreach($kdBy as $lstBy){
                $tab.="<td colspan=7 align=center colspan=7>".$optNmBy[$lstBy]."</td>";
        }
        $tab.="<td rowspan=2>".$_SESSION['lang']['grnd_total']."</td></tr><tr ".$bgcolor.">";
        foreach($kdBy as $lstBy){
            foreach($arrbln as $lstBln=>$dftrbln){
                $tab.="<td  align=center>".$dftrbln."</td>";
            }
            $tab.="<td  align=center>".$_SESSION['lang']['total']."</td>";
        }
        $tab.="</thead><tbody>";
        foreach($dtBagian as $lstRegional){
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$optNmKary[$lstRegional]."</td>";
                foreach($kdBy as $lstBy){
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        if($dtby[$lstRegional.$lstBy.$prd]!=0){
                            $linkDet="title='detail data ".$optNmKary[$lstRegional].",".$lstBy.",".$prd."' style='cursor:pointer;' onclick=getKaryDept('".$lstRegional."','".$lstBy."','".$dtTgs[$lstRegional]."','".$prd."')";
                        }
                        $tab.="<td  align=right ".$linkDet.">".number_format($dtby[$lstRegional.$lstBy.$prd],0)."</td>";
                        $subTot[$lstRegional.$lstBy]+=$dtby[$lstRegional.$lstBy.$prd];
                        $grTot[$lstRegional]+=$dtby[$lstRegional.$lstBy.$prd];
                        $grTotPrd[$lstBy.$prd]+=$dtby[$lstRegional.$lstBy.$prd];
                    }
                $tab.="<td  align=right>".number_format($subTot[$lstRegional.$lstBy],0)."</td>";
                }
                $tab.="<td  align=right>".number_format($grTot[$lstRegional],0)."</td>";
                $tab.="</tr>";
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$_SESSION['lang']['total']."</td>";
            foreach($kdBy as $lstBy){
                    
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        $tab.="<td  align=right>".number_format($grTotPrd[$lstBy.$prd],0)."</td>";
                        $totDtAl+=$grTotPrd[$lstBy.$prd];
                        $totDtper[$lstBy]+=$grTotPrd[$lstBy.$prd];
                    }
                    $tab.="<td  align=right>".number_format($totDtper[$lstBy],0)."</td>";
                    
            }
            $tab.="<td  align=right>".number_format($totDtAl,0)."</td>";
            $tab.="</tr>";
        
        $tab.="</tbody></table>";
        $tab.="<button class=mybutton onclick=zExcelStaff2(event,'sdm_slave_2biayapengobatan1.php','".$param['bagian']."','','".$param['regional']."','".$param['ptId2']."','".$param['unitId2']."','".$param['thn']."','".$param['smstr']."')>".$_SESSION['lang']['excel']."</button>
               <button class=mybutton onclick=kembali(2)>".$_SESSION['lang']['back']."</button>";
        echo $tab;
    break;
    case'getDetail3':
 $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>
        <tr class=rowheader>
        <td width=50></td>
        <td>No</td>
        <td width=100>".$_SESSION['lang']['notransaksi']."</td>
        <td width=50>".$_SESSION['lang']['periode']."</td>
        <td width=30>".$_SESSION['lang']['tanggal']."</td>
        <td width=200>".$_SESSION['lang']['lokasitugas']."</td>
        <td width=200>".$_SESSION['lang']['namakaryawan']."</td>
        <td width=200>".$_SESSION['lang']['jabatan']."</td>
        <td>".$_SESSION['lang']['pasien']."</td>
        <td width=150>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['pasien']."</td>
        <td width=150>".$_SESSION['lang']['rumahsakit']."</td>
        <td width=50>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
        <td width=90>".$_SESSION['lang']['nilaiklaim']."</td>
        <td>".$_SESSION['lang']['dibayar']."</td>
        <td width=90>".$_SESSION['lang']['perusahaan']."</td>
        <td width=90>".$_SESSION['lang']['karyawan']."</td>
        <td width=90>Jamsostek</td>      
        <td>".$_SESSION['lang']['diagnosa']."</td>
        <td>".$_SESSION['lang']['keterangan']."</td>
    </tr>
    </thead><tbody>"; 
        $str="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, c.lokasitugas as loktug,c.kodejabatan, nama 
              from ".$dbname.".sdm_pengobatanht a left join ".$dbname.".sdm_5rs b on a.rs=b.id 
              left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid 
              left join ".$dbname.".sdm_5diagnosa d on a.diagnosa=d.id 
              left join ".$dbname.".sdm_karyawankeluarga f
              on a.ygsakit=f.nomor
              where a.periode like '".$param['periode']."%' and a.kodebiaya='".$param['byPeng']."'
              and a.kodeorg = '".$param['unitId2']."' and c.tipekaryawan='".$param['tipeKary']."'
              order by a.jlhbayar desc,a.updatetime desc, a.tanggal desc";
    //echo $str;
    $res=mysql_query($str) or mysql_error($conn);
    $no=0;
    while($bar=mysql_fetch_object($res))
    {
        $no+=1;

        $pasien='';
        //get hubungan keluarga
        $stru="select hubungankeluarga from ".$dbname.".sdm_karyawankeluarga 
              where nomor=".$bar->ygsakit;
        $resu=mysql_query($stru);
        while($baru=mysql_fetch_object($resu))
        {
            $pasien=$baru->hubungankeluarga;
        }
        if($pasien=='')$pasien='AsIs';	

        $tab.="<tr class=rowcontent>
            <td>&nbsp <img src=images/zoom.png title='view' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)></td>
            <td>".$no."</td>
            <td>".$bar->notransaksi."</td>
            <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
            <td>".tanggalnormal($bar->tanggal)."</td>
            <td>".$bar->loktug."</td>
            <td>".$bar->namakaryawan."</td>
            <td>".$optJabatan[$bar->kodejabatan]."</td>
            <td>".$pasien."</td>
            <td>".$bar->nama."</td>
            <td>".$bar->namars."[".$bar->kota."]"."</td>
            <td>".$bar->kodebiaya."</td>
            <td align=right>".number_format($bar->totalklaim,0,'.',',')."</td>
            <td align=right>".number_format($bar->jlhbayar,0,'.',',')."</td>
            <td align=right>".number_format($bar->bebanperusahaan,0,'.',',')."</td>
            <td align=right>".number_format($bar->bebankaryawan,0,'.',',')."</td>
            <td align=right>".number_format($bar->bebanjamsostek,0,'.',',')."</td>     
            <td>".$bar->ketdiag."</td>
            <td>".$bar->keterangan."</td>
        </tr>";	  	
        $totKlaim+=$bar->totalklaim;
        $totJlhByr+=$bar->jlhbayar;
        $totBbnprshn+=$bar->bebanperusahaan;
        $totBbnKary+=$bar->bebankaryawan;
        $totBbnJam+=$bar->bebanjamsostek;
    }    
    $tab.="<tr class=rowcontent><td colspan=12>".$_SESSION['lang']['total']."</td>";
    $tab.="<td align=right>".number_format($totKlaim,0)."</td>";
    $tab.="<td align=right>".number_format($totJlhByr,0)."</td>";
    $tab.="<td align=right>".number_format($totBbnprshn,0)."</td>";
    $tab.="<td align=right>".number_format($totBbnKary,0)."</td>";
    $tab.="<td align=right>".number_format($totBbnJam,0)."</td>";
    $tab.="<td colspan=2>&nbsp</td></tr>";
    $tab.="</tbody></table>";
        $tab.="<button class=mybutton onclick=zExcelDt2(event,'sdm_slave_2biayapengobatan.php','".$param['unitId2']."','".$param['tipeKary']."','".$param['periode']."','".$param['byPeng']."')>".$_SESSION['lang']['excel']."</button>
               <button class=mybutton onclick=kembali(2)>".$_SESSION['lang']['back']."</button>";
        echo $tab;
    break;
     case'detailKarywan':
      $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>
        <tr class=rowheader>
        
        <td  ".$bgcolor.">No</td>
        <td width=100  ".$bgcolor.">".$_SESSION['lang']['notransaksi']."</td>
        <td width=50  ".$bgcolor.">".$_SESSION['lang']['periode']."</td>
        <td width=30  ".$bgcolor.">".$_SESSION['lang']['tanggal']."</td>
        <td width=200 ".$bgcolor.">".$_SESSION['lang']['lokasitugas']."</td>
        <td width=200 ".$bgcolor.">".$_SESSION['lang']['namakaryawan']."</td>
        <td width=200 ".$bgcolor.">".$_SESSION['lang']['jabatan']."</td>
        <td ".$bgcolor.">".$_SESSION['lang']['pasien']."</td>
        <td width=150 ".$bgcolor.">".$_SESSION['lang']['nama']." ".$_SESSION['lang']['pasien']."</td>
        <td width=150 ".$bgcolor.">".$_SESSION['lang']['rumahsakit']."</td>
        <td width=50 ".$bgcolor.">".$_SESSION['lang']['jenisbiayapengobatan']."</td>
        <td width=90 ".$bgcolor.">".$_SESSION['lang']['nilaiklaim']."</td>
        <td ".$bgcolor.">".$_SESSION['lang']['dibayar']."</td>
        <td width=90 ".$bgcolor.">".$_SESSION['lang']['perusahaan']."</td>
        <td width=90 ".$bgcolor.">".$_SESSION['lang']['karyawan']."</td>
        <td width=90 ".$bgcolor.">Jamsostek</td>      
        <td ".$bgcolor.">".$_SESSION['lang']['diagnosa']."</td>
        <td ".$bgcolor.">".$_SESSION['lang']['keterangan']."</td>
    </tr>
    </thead><tbody>"; 
        $str="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, c.lokasitugas as loktug,c.kodejabatan, nama 
              from ".$dbname.".sdm_pengobatanht a left join ".$dbname.".sdm_5rs b on a.rs=b.id 
              left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid 
              left join ".$dbname.".sdm_5diagnosa d on a.diagnosa=d.id 
              left join ".$dbname.".sdm_karyawankeluarga f
              on a.ygsakit=f.nomor
              where a.periode like '".$param['periode']."%' and a.kodebiaya='".$param['byPeng']."'
              and a.karyawanid='".$param['karyId']."'
              order by a.jlhbayar desc,a.updatetime desc, a.tanggal desc";
   //echo $str;
    $res=mysql_query($str) or mysql_error($conn);
    $no=0;
    while($bar=mysql_fetch_object($res))
    {
        $no+=1;

        $pasien='';
        //get hubungan keluarga
        $stru="select hubungankeluarga from ".$dbname.".sdm_karyawankeluarga 
              where nomor=".$bar->ygsakit;
        $resu=mysql_query($stru);
        while($baru=mysql_fetch_object($resu))
        {
            $pasien=$baru->hubungankeluarga;
        }
        if($pasien=='')$pasien='AsIs';	

        $tab.="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$bar->notransaksi."</td>
            <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
            <td>".tanggalnormal($bar->tanggal)."</td>
            <td>".$bar->loktug."</td>
            <td>".$bar->namakaryawan."</td>
            <td>".$optJabatan[$bar->kodejabatan]."</td>
            <td>".$pasien."</td>
            <td>".$bar->nama."</td>
            <td>".$bar->namars."[".$bar->kota."]"."</td>
            <td>".$bar->kodebiaya."</td>
            <td align=right>".number_format($bar->totalklaim,0,'.',',')."</td>
            <td align=right>".number_format($bar->jlhbayar,0,'.',',')."</td>
            <td align=right>".number_format($bar->bebanperusahaan,0,'.',',')."</td>
            <td align=right>".number_format($bar->bebankaryawan,0,'.',',')."</td>
            <td align=right>".number_format($bar->bebanjamsostek,0,'.',',')."</td>     
            <td>".$bar->ketdiag."</td>
            <td>".$bar->keterangan."</td>
        </tr>";	  	
        $totKlaim+=$bar->totalklaim;
        $totJlhByr+=$bar->jlhbayar;
        $totBbnprshn+=$bar->bebanperusahaan;
        $totBbnKary+=$bar->bebankaryawan;
        $totBbnJam+=$bar->bebanjamsostek;
    }    
    $tab.="<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['total']."</td>";
    $tab.="<td align=right>".number_format($totKlaim,0)."</td>";
    $tab.="<td align=right>".number_format($totJlhByr,0)."</td>";
    $tab.="<td align=right>".number_format($totBbnprshn,0)."</td>";
    $tab.="<td align=right>".number_format($totBbnKary,0)."</td>";
    $tab.="<td align=right>".number_format($totBbnJam,0)."</td>";
    $tab.="<td colspan=2>&nbsp</td></tr>";
    $tab.="</tbody></table>";
      $tab.="<button class=mybutton onclick=zExcelDt2(event,'sdm_slave_2biayapengobatan.php','".$param['unitId2']."','".$param['tipeKary']."','".$param['periode']."','".$param['byPeng']."')>".$_SESSION['lang']['excel']."</button>
               <button class=mybutton onclick=kembali(3)>".$_SESSION['lang']['back']."</button>";
       echo $tab;
    break;
     case'excelStaffDet2':
        $whr="";
      $ert=1;  
      if($param['ptId2']!=''){
          $whertd.="and induk='".$param['ptId2']."'";
      }
      $isir="kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment 
             where regional='".$param['regional']."')";
      if($param['unitId2']!=''){
          $isir="";
          $isir="kodeorganisasi='".$param['unitId2']."'";
      }
      $whr=" and a.kodeorg in (";
      $sreg="select distinct kodeorganisasi from ".$dbname.".organisasi where ".$isir." ".$whertd."
              order by namaorganisasi asc";
      //exit("error:".$sreg);
      $qreg=mysql_query($sreg) or die(mysql_error($conn));
      while($rreg=  mysql_fetch_assoc($qreg)){
          if($ert==1){
              $whr.="'".$rreg['kodeorganisasi']."'";
          }else{
              $whr.=",'".$rreg['kodeorganisasi']."'";
          }
          $ert++;
      }
      $whr.=")";
        if($param['smstr']=='I'){
            $whr.=" and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        }else{
            $whr.=" and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }
        if($param['byPeng']!=''){
            $whr.=" and kodebiaya='".$param['byPeng']."'";
        }
        #staff    
        $sstaff="select distinct sum(jlhbayar) as jmlhdbyr,periode,b.bagian,kodebiaya,a.karyawanid,b.lokasitugas from 
                 ".$dbname.".sdm_pengobatanht  a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=0 and b.bagian='".$param['bagian']."'
                  group by a.karyawanid,periode  order by b.namakaryawan asc";
        //echo $sstaff;
        //exit("error:".$sstaff);
        $qstaff=mysql_query($sstaff) or die(mysql_error($conn));
        while($rstaff=  mysql_fetch_assoc($qstaff)){
            $dtby[$rstaff['karyawanid'].$rstaff['kodebiaya'].$rstaff['periode']]+=$rstaff['jmlhdbyr'];
            $dtTgs[$rstaff['karyawanid']]=$rstaff['lokasitugas'];
            $kdBy[$rstaff['kodebiaya']]=$rstaff['kodebiaya'];
            $dtBagian[$rstaff['karyawanid']]=$rstaff['karyawanid'];
        }
       
        $tab.="<table cellpadding=1 cellspacing=1 border=1 class=sortable>";
        $tab.="<thead><td rowspan=2 ".$bgcolor.">".$_SESSION['lang']['namakaryawan']."</td>";
        foreach($kdBy as $lstBy){
                $tab.="<td colspan=7 align=center colspan=7>".$optNmBy[$lstBy]."</td>";
        }
        $tab.="<td rowspan=2>".$_SESSION['lang']['grnd_total']."</td></tr><tr ".$bgcolor.">";
        foreach($kdBy as $lstBy){
            foreach($arrbln as $lstBln=>$dftrbln){
                $tab.="<td  align=center>".$dftrbln."</td>";
            }
            $tab.="<td  align=center>".$_SESSION['lang']['total']."</td>";
        }
        $tab.="</thead><tbody>";
        foreach($dtBagian as $lstRegional){
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$optNmKary[$lstRegional]."</td>";
                foreach($kdBy as $lstBy){
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        if($dtby[$lstRegional.$lstBy.$prd]!=0){
                            $linkDet="title='detail data ".$optNmKary[$lstRegional].",".$lstBy.",".$prd."' style='cursor:pointer;' onclick=getKaryDept('".$lstRegional."','".$lstBy."','".$dtTgs[$lstRegional]."','".$prd."')";
                        }
                        $tab.="<td  align=right ".$linkDet.">".number_format($dtby[$lstRegional.$lstBy.$prd],0)."</td>";
                        $subTot[$lstRegional.$lstBy]+=$dtby[$lstRegional.$lstBy.$prd];
                        $grTot[$lstRegional]+=$dtby[$lstRegional.$lstBy.$prd];
                        $grTotPrd[$lstBy.$prd]+=$dtby[$lstRegional.$lstBy.$prd];
                    }
                $tab.="<td  align=right>".number_format($subTot[$lstRegional.$lstBy],0)."</td>";
                }
                $tab.="<td  align=right>".number_format($grTot[$lstRegional],0)."</td>";
                $tab.="</tr>";
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$_SESSION['lang']['total']."</td>";
            foreach($kdBy as $lstBy){
                    
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        $tab.="<td  align=right>".number_format($grTotPrd[$lstBy.$prd],0)."</td>";
                        $totDtAl+=$grTotPrd[$lstBy.$prd];
                        $totDtper[$lstBy]+=$grTotPrd[$lstBy.$prd];
                    }
                    $tab.="<td  align=right>".number_format($totDtper[$lstBy],0)."</td>";
                    
            }
            $tab.="<td  align=right>".number_format($totDtAl,0)."</td>";
            $tab.="</tr>";
        
        $tab.="</tbody></table>";
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $nop_="detailPengobatan2Staff2_".$param['ptId2'];
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
        case'getDetailDeptBy':
        $whr="";
      $ert=1;  
      if($param['ptId2']!=''){
          $whertd.="and induk='".$param['ptId2']."'";
      }
      $isir="kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment 
             where regional='".$param['regional']."')";
      if($param['unitId2']!=''){
          $isir="";
          $isir="kodeorganisasi='".$param['unitId2']."'";
      }
      $whr=" and a.kodeorg in (";
      $sreg="select distinct kodeorganisasi from ".$dbname.".organisasi where ".$isir." ".$whertd."
              order by namaorganisasi asc";
      //exit("error:".$sreg);
      $qreg=mysql_query($sreg) or die(mysql_error($conn));
      while($rreg=  mysql_fetch_assoc($qreg)){
          if($ert==1){
              $whr.="'".$rreg['kodeorganisasi']."'";
          }else{
              $whr.=",'".$rreg['kodeorganisasi']."'";
          }
          $ert++;
      }
      $whr.=")";
        if($param['smstr']=='I'){
            $whr.=" and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        }else{
            $whr.=" and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }
        #staff    
        $sstaff="select distinct sum(jlhbayar) as jmlhdbyr,periode,b.bagian,kodebiaya,a.karyawanid,b.lokasitugas from 
                 ".$dbname.".sdm_pengobatanht  a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=0 and b.bagian='".$param['bagian']."' and kodebiaya='".$param['byPeng']."'
                  group by a.karyawanid,periode  order by b.namakaryawan asc";
        //echo $sstaff;
        //exit("error:".$sstaff);
        $qstaff=mysql_query($sstaff) or die(mysql_error($conn));
        while($rstaff=  mysql_fetch_assoc($qstaff)){
            $dtby[$rstaff['karyawanid'].$rstaff['kodebiaya'].$rstaff['periode']]+=$rstaff['jmlhdbyr'];
            $dtTgs[$rstaff['karyawanid']]=$rstaff['lokasitugas'];
            $kdBy[$rstaff['kodebiaya']]=$rstaff['kodebiaya'];
            $dtBagian[$rstaff['karyawanid']]=$rstaff['karyawanid'];
        }
       
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead><td rowspan=2>".$_SESSION['lang']['namakaryawan']."</td>";
        foreach($kdBy as $lstBy){
                $tab.="<td colspan=7 align=center colspan=7>".$optNmBy[$lstBy]."</td>";
        }
        $tab.="<td rowspan=2>".$_SESSION['lang']['grnd_total']."</td></tr><tr ".$bgcolor.">";
        foreach($kdBy as $lstBy){
            foreach($arrbln as $lstBln=>$dftrbln){
                $tab.="<td  align=center>".$dftrbln."</td>";
            }
            $tab.="<td  align=center>".$_SESSION['lang']['total']."</td>";
        }
        $tab.="</thead><tbody>";
        foreach($dtBagian as $lstRegional){
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$optNmKary[$lstRegional]."</td>";
                foreach($kdBy as $lstBy){
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        if($dtby[$lstRegional.$lstBy.$prd]!=0){
                            $linkDet="title='detail data ".$optNmKary[$lstRegional].",".$lstBy.",".$prd."' style='cursor:pointer;' onclick=getKaryDept('".$lstRegional."','".$lstBy."','".$dtTgs[$lstRegional]."','".$prd."')";
                        }
                        $tab.="<td  align=right ".$linkDet.">".number_format($dtby[$lstRegional.$lstBy.$prd],0)."</td>";
                        $subTot[$lstRegional.$lstBy]+=$dtby[$lstRegional.$lstBy.$prd];
                        $grTot[$lstRegional]+=$dtby[$lstRegional.$lstBy.$prd];
                        $grTotPrd[$lstBy.$prd]+=$dtby[$lstRegional.$lstBy.$prd];
                    }
                $tab.="<td  align=right>".number_format($subTot[$lstRegional.$lstBy],0)."</td>";
                }
                $tab.="<td  align=right>".number_format($grTot[$lstRegional],0)."</td>";
                $tab.="</tr>";
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$_SESSION['lang']['total']."</td>";
            foreach($kdBy as $lstBy){
                    
                    foreach($arrbln as $lstBln=>$dftrbln){
                        $prd=$param['thn']."-".$lstBln;
                        $tab.="<td  align=right>".number_format($grTotPrd[$lstBy.$prd],0)."</td>";
                        $totDtAl+=$grTotPrd[$lstBy.$prd];
                        $totDtper[$lstBy]+=$grTotPrd[$lstBy.$prd];
                    }
                    $tab.="<td  align=right>".number_format($totDtper[$lstBy],0)."</td>";
                    
            }
            $tab.="<td  align=right>".number_format($totDtAl,0)."</td>";
            $tab.="</tr>";
        
        $tab.="</tbody></table>";
        $tab.="<button class=mybutton onclick=zExcelStaff2(event,'sdm_slave_2biayapengobatan1.php','".$param['bagian']."','".$param['byPeng']."','".$param['regional']."','".$param['ptId2']."','".$param['unitId2']."','".$param['thn']."','".$param['smstr']."')>".$_SESSION['lang']['excel']."</button>
               <button class=mybutton onclick=kembali(2)>".$_SESSION['lang']['back']."</button>";
        echo $tab;
        break;
}
?>