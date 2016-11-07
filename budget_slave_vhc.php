<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
//exit("Error:masuk".$proses);
$kdTraksi=$_POST['kdTraksi'];
$kdVhc=$_POST['kdVhc'];
$thnBudget=$_POST['thnBudget'];
$kodeOrg=$_POST['kdOrg'];
$kdVhc=$_POST['kdVhc'];
$jmlhPerson=$_POST['jmlhPerson'];
$kdGol=$_POST['kdGol'];
$hkEfektif=$_POST['hkEfektif'];
$tipeBudget=$_POST['tipeBudget'];
$totBiaya=$_POST['totBiaya'];
$nmBrg=$_POST['nmBrg'];
$klmpkBrg=$_POST['klmpkBrg'];
$idData=$_POST['idData'];
$kdBudget=$_POST['kdBudget'];
$kdBrg=$_POST['kdBrg'];
$jmlhBrg=$_POST['jmlhBrg'];
$satuanBrg=$_POST['satuanBrg'];
$totHarga=$_POST['totHarga'];
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmAkun=makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$kdBudgetB=$_POST['kdBudgetB'];
$noAkun=$_POST['noAkun'];
$totBiayaB=$_POST['totBiayaB'];
$kdBudgetS=$_POST['kdBudgetS'];
$kdWorkshop=$_POST['kdWorkshop'];
$jmlhJam=$_POST['jmlhJam'];
$totHargaJam=$_POST['totHargaJam'];
$where2=" kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tipebudget='TRK' and tahunbudget='".$thnBudget."'";
////param+='&kdBudget='+kdBudget+'&kdBrg='+kdBrg+'&jmlhBrg='+jmlhBrg+'&proses=getHarga';
switch($proses)
{
        case'getVhc':
            $optVhc="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $sVhc="select distinct kodevhc from ".$dbname.".vhc_5master where kodetraksi='".$kdTraksi."'";
           // exit ("Error".$sVhc);
            $qVhc=mysql_query($sVhc) or die(mysql_error($conn));
            while($rVhc=mysql_fetch_assoc($qVhc))
            {
                if($kdVhc!='')
                {
                        $optVhc.="<option value='".$rVhc['kodevhc']."' ".($kdVhc==$rVhc['kodevhc']?'selected':'').">".$rVhc['kodevhc']."</option>";
                }
                else
                {

                        $optVhc.="<option value='".$rVhc['kodevhc']."'>".$rVhc['kodevhc']."</option>";
                }
            }
            echo $optVhc;
        break;
	case'cekSave':
            if($thnBudget==''||$kodeOrg==''||$kdVhc=='')
            {
                exit("Error: Budget year, Org code, Vhc code are obligatory");
            }
            if(strlen($thnBudget)<4)
            {
                exit("Error:Budget year required");
            }
            
             $sCek="select distinct tutup from ".$dbname.".bgt_budget where tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."'";
             //exit("Error".$sCek); and kodevhc='".$kdVhc."'
             $qCek=mysql_query($sCek) or die(mysql_error($conn));
             $rCek=mysql_fetch_assoc($qCek);
             if($rCek['tutup']!=0)
             {
                 exit("Error:  Budget year ".$thnBudget." has been closed,Can not add data");
             }
            $sHk="select distinct * from ".$dbname.".bgt_hk where tahunbudget='".$thnBudget."'";
            //exit("Error".$sHk);
            $qHk=mysql_query($sHk) or die(mysql_error($conn));
            $rHk=mysql_fetch_assoc($qHk);
            $hkEfektip=intval($rHk['harisetahun'])-intval($rHk['hrminggu'])-intval($rHk['hrlibur'])+intval($rHk['hrliburminggu']);
            
            $optWs="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $sWs="select distinct kodews from ".$dbname.".bgt_ws_jam where tahunbudget='".$thnBudget."' and kodetraksi='".$kodeOrg."'";
            $qWs=mysql_query($sWs) or die(mysql_error($conn));
            while($rWs=mysql_fetch_assoc($qWs))
            {
                $optWs.="<option value='".$rWs['kodews']."'>".$optNm[$rWs['kodews']]."</option>";
            }
            echo $hkEfektip."###".$optWs;
	break;
       
        case'getUpah':
            if($kdGol=='')
            {
               exit("Error: Budget code required");
            }
          $sUpah="select jumlah from ".$dbname.".bgt_upah where tahunbudget='".$thnBudget."' and kodeorg='".substr($kodeOrg,0,4)."' and golongan='".$kdGol."' and closed=1";
          //exit("Error".$sUpah);
          $qUpah=mysql_query($sUpah) or die(mysql_error($conn));
          $row=mysql_num_rows($qUpah);
          if($row!=0)
          {
          $rUpah=mysql_fetch_assoc($qUpah);
          if($rUpah['jumlah']=='0')
          {
              exit("Error:Daily salary not exist");
          }
          else
          {
              $totalUpah=(floatval($rUpah['jumlah'])*floatval($jmlhPerson))*floatval($hkEfektif);
              echo $totalUpah;
          }
          }
          else
          {
              exit("Error: Data not closed, please re-check");
          }
            
        break;
        case'saveSdm':
            if($kdGol==''||$jmlhPerson==''||$totBiaya==0)
            {
                exit("Error: Field can not be empty or 0");
            }
          $sCek="select * from ".$dbname.".bgt_budget where tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and kodebudget='".$kdGol."' and kodevhc='".$kdVhc."'";
          $qCek=mysql_query($sCek) or die(mysql_error($conn));
          $rCek=mysql_num_rows($qCek);
          if($rCek<1)
          {
              $vol=floatval($jmlhPerson)*floatval($hkEfektif);
              $sIns="insert into ".$dbname.".bgt_budget (tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,rupiah,jumlah, satuanj,updateby,volume, satuanv) values
                  ('".$thnBudget."','".$kodeOrg."','".$tipeBudget."','".$kdGol."','".$kdVhc."','".$totBiaya."','".$jmlhPerson."','orang','".$_SESSION['standard']['userid']."','".$vol."','HK')";
              if(mysql_query($sIns))
              echo"";
              else
             echo "DB Error : ".$sIns."\n".mysql_error($conn);
          }
          else
          {
              exit("Error:Data already exist");
          }
        break;
        case'saveMat':
             if($kdBudget==''||$kdBrg==''||$totHarga==0||$jmlhBrg=='')
            {
                exit("Error: Field can not be empty or 0");
            }
          $sCek="select * from ".$dbname.".bgt_budget where tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and kodebudget='".$kdBudget."' and kodebarang='".$kdBrg."' and kodevhc='".$kdVhc."'";
          $qCek=mysql_query($sCek) or die(mysql_error($conn));
          $rCek=mysql_num_rows($qCek);
          if($rCek<1)
          {
                $sRegion="select distinct regional from ".$dbname.".bgt_regional_assignment where kodeunit='".substr($kodeOrg,0,4)."'";
                //  exit("Error".$sRegion);
                $qRegion=mysql_query($sRegion) or die(mysql_error($conn));
                $rRegion=mysql_fetch_assoc($qRegion);
             
              $sIns="insert into ".$dbname.".bgt_budget (tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,rupiah, kodebarang, regional, updateby,jumlah,satuanj) values
                  ('".$thnBudget."','".$kodeOrg."','".$tipeBudget."','".$kdBudget."','".$kdVhc."','".$totHarga."','".$kdBrg."','".$rRegion['regional']."','".$_SESSION['standard']['userid']."','".$jmlhBrg."','".$satuanBrg."')";
              if(mysql_query($sIns))
              echo"";
              else
              echo "DB Error : ".$sIns."\n".mysql_error($conn);
          }
          else
          {
              exit("Error: Data already exist");
          }
        break;
        case'saveLain':
            if($kdBudgetB==''||$totBiayaB==0||$noAkun=='')
            {
                exit("Error: Field can not be empty or 0");
            }
          $sCek="select * from ".$dbname.".bgt_budget where tahunbudget='".$thnBudget."' 
                and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' 
                and kodebudget='".$kdBudgetB."' and noakun='".$noAkun."' and kodevhc='".$kdVhc."'";
         // exit("Error".$sCek);
          $qCek=mysql_query($sCek) or die(mysql_error($conn));
          $rCek=mysql_num_rows($qCek);
          if($rCek<1)
          {
             
              $sIns="insert into ".$dbname.".bgt_budget (tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc, noakun,rupiah,updateby) values
                  ('".$thnBudget."','".$kodeOrg."','".$tipeBudget."','".$kdBudgetB."','".$kdVhc."','".$noAkun."','".$totBiayaB."','".$_SESSION['standard']['userid']."')";
              if(mysql_query($sIns))
              echo"";
              else
             echo "DB Error : ".$sIns."\n".mysql_error($conn);
          }
          else
          {
              exit("Error: Data already exist");
          }
        break;
        case'saveService':
            if($kdBudgetS==''||$totHargaJam==0||$jmlhJam=='')
            {
                exit("Error:Field tidak boleh kosong atau nol \n Jika total harga nol, mohon input alokasi jam bengkel");
            }
          $sCek="select * from ".$dbname.".bgt_budget where tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and kodebudget='".$kdBudgetS."' and kodevhc='".$kdVhc."'";
          $qCek=mysql_query($sCek) or die(mysql_error($conn));
          $rCek=mysql_num_rows($qCek);
          if($rCek<1)
          {
             
              $sIns="insert into ".$dbname.".bgt_budget (tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc, rupiah,jumlah,satuanj,updateby) values
                  ('".$thnBudget."','".$kodeOrg."','".$tipeBudget."','".$kdBudgetS."','".$kdVhc."','".$totHargaJam."','".$jmlhJam."','JAM','".$_SESSION['standard']['userid']."')";
              if(mysql_query($sIns))
              echo"";
              else
             echo "DB Error : ".$sIns."\n".mysql_error($conn);
          }
          else
          {
              exit("Error: data already exist");
          }
        break;
	case'loadDataSdm':
        $sLoad="select kunci,tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,rupiah,jumlah, satuanj,volume, satuanv from ".$dbname.".bgt_budget where 
            tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and kodevhc='".$kdVhc."' and kodebudget like '%SDM%'";
           // echo $sLoad; and
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td align='center'>".$res['kunci']."</td>";
            $tab.="<td align='center'>".$res['tahunbudget']."</td>";
            $tab.="<td align='center'>".$res['kodeorg']."</td>";
            $tab.="<td align='center'>".$res['tipebudget']."</td>";
            $tab.="<td align='center'>".$res['kodebudget']."</td>";
            $tab.="<td align='center'>".$res['kodevhc']."</td>";
            $tab.="<td align='right'>".$res['volume']."</td>";
            $tab.="<td align='center'>".$res['satuanv']."</td>";
            $tab.="<td  align='right'>".$res['jumlah']."</td>";
            $tab.="<td  align='center'>".$res['satuanj']."</td>";
            $tab.="<td align='right'>".number_format($res['rupiah'],2)."</td>";
            $tab.="<td align=center style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",1)\" src='images/application/application_delete.png'/></td>";
            $tab.="</tr>";
        }
        echo $tab;
	break;
        case'getBarang':
               $tab="<fieldset><legend>".$_SESSION['lang']['result']."</legend>
                        <div style=\"overflow:auto;height:295px;width:455px;\">
                        <table cellpading=1 border=0 class=sortbale>
                        <thead>
                        <tr class=rowheader>
                        <td>No.</td>
                        <td>".$_SESSION['lang']['kodebarang']."</td>
                        <td>".$_SESSION['lang']['namabarang']."</td>
                        <td>".$_SESSION['lang']['satuan']."</td>
                        </tr><tbody>
                        ";
            if($nmBrg=='')
            {
                $nmBrg=$kdBarang;
            }
            $sLoad="select kodebarang,namabarang,satuan from ".$dbname.".log_5masterbarang where  kelompokbarang='".substr($klmpkBrg,2,3)."' and (kodebarang like '%".$nmBrg."%'
            or namabarang like '%".$nmBrg."%')";
        //   echo $sLoad;
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent onclick=\"setData('".$res['kodebarang']."','".$res['namabarang']."','".$res['satuan']."')\">";
            $tab.="<td>".$no."</td>";
            $tab.="<td>".$res['kodebarang']."</td>";
            $tab.="<td>".$res['namabarang']."</td>";
            $tab.="<td>".$res['satuan']."</td>";
            $tab.="</tr>";
        }
        echo $tab;
            
        break;
        case'getHarga':
            if(($jmlhBrg=='')||($jmlhBrg=='0'))
            {
                exit("Material volume is empty");
            }
            $sRegion="select distinct regional from ".$dbname.".bgt_regional_assignment where kodeunit='".substr($kodeOrg,0,4)."' ";
           //  exit("Error".$sRegion);
            $qRegion=mysql_query($sRegion) or die(mysql_error($conn));
            $rRegion=mysql_fetch_assoc($qRegion);
            $sHrg="select distinct hargasatuan from ".$dbname.".bgt_masterbarang where regional='".$rRegion['regional']."' and kodebarang='".$kdBrg."' and tahunbudget='".$thnBudget."' and closed=1";
             //exit("Error".$sHrg);
            $qHrg=mysql_query($sHrg) or die(mysql_error($conn));
            $row=mysql_num_rows($qHrg);
            if($row!=0)
            {
                $rHrg=mysql_fetch_assoc($qHrg);
                if(($rHrg['hargasatuan']!='')||($rHrg['hargasatuan']!='0'))
                {
                    $hasil=floatval($rHrg['hargasatuan'])*floatval($jmlhBrg);
                    echo $hasil;
                }
                else
                {
                    exit("Error: Please contact purchase dept");
                }
            }
            else
            {
             exit("Error:Please contact purchase dept");   
            }
        break;
        case'getBiayaService':
            if(($kdBudgetS=='')||($kdWorkshop=='')||($jmlhJam=='')||($jmlhJam=='0'))
            {
                exit("Working hour is empty");
            }
         
            $sHrg="select distinct rpperjam from ".$dbname.".bgt_biaya_ws_per_jam where tahunbudget='".$thnBudget."' and kodews='".$kdWorkshop."'";
           //exit("Error".$sHrg);
            $qHrg=mysql_query($sHrg) or die(mysql_error($conn));
            $rHrg=mysql_fetch_assoc($qHrg);
            if(($rHrg['rppertahun']!='')||($rHrg['rppertahun']!='0'))
            {
                $hasil=floatval($rHrg['rpperjam'])*floatval($jmlhJam);
                echo $hasil;
            }
            else
            {
                exit("Error: You are not assigned on a regional, please contact IT");
            }
        break;
        case'delData':
            $sDel="delete from ".$dbname.".bgt_budget where kunci='".$idData."'";
            if(mysql_query($sDel))
                echo"";
            else
                 echo "DB Error : ".$sDel."\n".mysql_error($conn);
        break;
        case'loadDataMat':
          $sLoad="select kunci,tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,rupiah,jumlah, satuanj,kodebarang, satuanv from ".$dbname.".bgt_budget where 
            tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and substring(kodebudget,1,1)='M' and kodevhc='".$kdVhc."'";
           // echo $sLoad; and
           // exit("Error".$sLoad);
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td align='center'>".$res['kunci']."</td>";
            $tab.="<td align='center'>".$res['tahunbudget']."</td>";
            $tab.="<td align='center'>".$res['kodeorg']."</td>";
            $tab.="<td align='center'>".$res['tipebudget']."</td>";
            $tab.="<td align='center'>".$res['kodebudget']."</td>";
            $tab.="<td align='center'>".$res['kodevhc']."</td>";
            $tab.="<td align='right'>".$res['kodebarang']."</td>";
            $tab.="<td align='center'>".$optNmBrg[$res['kodebarang']]."</td>";
            $tab.="<td  align='right'>".$res['jumlah']."</td>";
            $tab.="<td  align='center'>".$res['satuanj']."</td>";
            $tab.="<td align='right'>".number_format($res['rupiah'],2)."</td>";
            $tab.="<td align=center  style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",2)\" src='images/application/application_delete.png'/></td>";
            $tab.="</tr>";
        }

            echo $tab;
        break;
        case'loadDtLain':
          $sLoad="select kunci,tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,rupiah,noakun, satuanj from ".$dbname.".bgt_budget where 
            tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and kodebudget like '%TRANSIT%' and kodevhc='".$kdVhc."'";
           // echo $sLoad; and
           // exit("Error".$sLoad);
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td align='center'>".$res['kunci']."</td>";
            $tab.="<td align='center'>".$res['tahunbudget']."</td>";
            $tab.="<td align='center'>".$res['kodeorg']."</td>";
            $tab.="<td align='center'>".$res['tipebudget']."</td>";
            $tab.="<td align='center'>".$res['kodebudget']."</td>";
            $tab.="<td align='center'>".$res['kodevhc']."</td>";
            $tab.="<td align='right'>".$res['noakun']."</td>";
            $tab.="<td align='left'>".$optNmAkun[$res['noakun']]."</td>";
            $tab.="<td align='right'>".number_format($res['rupiah'],2)."</td>";
            $tab.="<td align=center  style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",4)\" src='images/application/application_delete.png'/></td>";
            $tab.="</tr>";
        }
        
            echo $tab;
        break;
       case'loadDtService':
          $sLoad="select kunci,tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,rupiah,jumlah, satuanj from ".$dbname.".bgt_budget where 
            tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and kodebudget like '%SERVICE%' and kodevhc='".$kdVhc."'";
           // echo $sLoad; and
           // exit("Error".$sLoad);
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td align='center'>".$res['kunci']."</td>";
            $tab.="<td align='center'>".$res['tahunbudget']."</td>";
            $tab.="<td align='center'>".$res['kodeorg']."</td>";
            $tab.="<td align='center'>".$res['tipebudget']."</td>";
            $tab.="<td align='center'>".$res['kodebudget']."</td>";
            $tab.="<td align='center'>".$res['kodevhc']."</td>";
            $tab.="<td align='center'>".$res['jumlah']."</td>";
            $tab.="<td align='center'>".$res['satuanj']."</td>";
            $tab.="<td align='right'>".number_format($res['rupiah'],2)."</td>";
            $tab.="<td align=center  style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",3)\" src='images/application/application_delete.png'/></td>";
            $tab.="</tr>";
        }
        
            echo $tab;
        break;
        case'setKdBrg':
            echo substr($klmpkBrg,2,3);
        break;
        case'DataHeader':
           // exit("Error:masuk");
            $sJm="select * from ".$dbname.".bgt_biaya_ken_per_jam order by tahunbudget desc";
            $qJm=mysql_query($sJm) or die(mysql_error($conn));
            while($rJm=mysql_fetch_assoc($qJm))
            {
                $rJmthn[$rJm['tahunbudget']][$rJm['kodetraksi']][$rJm['kodevhc']]=$rJm['rpsetahun'];
                $rJmhm[$rJm['tahunbudget']][$rJm['kodetraksi']][$rJm['kodevhc']]=$rJm['rpperjam'];
            }
               $tab="<table cellspacing=1 cellpadding=1 class=sortable border=0><thead>";
                $tab.="<tr class=rowheader>";
                $tab.="<td>No.</td>";
                $tab.="<td>".$_SESSION['lang']['budgetyear']."</td>";
                $tab.="<td>".$_SESSION['lang']['tipe']."</td>";
                $tab.="<td>".$_SESSION['lang']['kodeorg']."</td>";
                $tab.="<td>".$_SESSION['lang']['kodevhc']."</td>";
                $tab.="<td>".$_SESSION['lang']['rpperthn']."</td>";
                $tab.="<td>".$_SESSION['lang']['rpperjam']."</td>";
                $tab.="<td>Action</td>";
                $tab.="</tr></thead>";
                $tab.="<tbody>";
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                if($thnBudget!='')
                {
                    $addKon.=" and tahunbudget='".$thnBudget."'";
                }
                if($kdVhc!='')
                {
                    $addKon.=" and kodevhc='".$kdVhc."'";
                }
                
                $sql2="select * from ".$dbname.".bgt_budget where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tipebudget='TRK' ".$addKon." group by tahunbudget,kodeorg,tipebudget, kodevhc order by tahunbudget desc  ";
                $query2=mysql_query($sql2) or die(mysql_error());
                $jlhbrs=mysql_num_rows($query2);
                $sData="select kunci,tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,tutup from ".$dbname.".bgt_budget where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tipebudget='TRK' ".$addKon."  group by tahunbudget,kodeorg,tipebudget, kodevhc order by tahunbudget desc  limit ".$offset.",".$limit."";
               // exit("Error".$sData);
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=mysql_fetch_assoc($qData))
                {
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rData['tahunbudget']."</td>";
                    $tab.="<td>".$rData['tipebudget']."</td>";
                    $tab.="<td>".$rData['kodeorg']."</td>";
                    $tab.="<td>".$rData['kodevhc']."</td>";
                    $tab.="<td align=right>".number_format($rJmthn[$rData['tahunbudget']][$rData['kodeorg']][$rData['kodevhc']],2)."</td>";
                    $tab.="<td align=right>".number_format($rJmhm[$rData['tahunbudget']][$rData['kodeorg']][$rData['kodevhc']],2)."</td>";
                    if($rData['tutup']==0)
                    {
                    $tab.="<td  align=center style='cursor:pointer;'><img id='detail_edit' title='Simpan' class=zImgBtn onclick=\"filFieldHead('".$rData['tahunbudget']."','".$rData['kodeorg']."','".$rData['kodevhc']."')\" src='images/application/application_edit.png'/></td>";
                    }
                    else
                    {
                        $tab.="<td>".$_SESSION['lang']['tutup']."</td>";
                    }
                    $tab.="</tr>";
                }
                  $tab.="
		<tr class=rowheader><td colspan=10 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
                $tab.="</tbody></table>";
                echo $tab;
        break;
        case'closeBudget':
                if($thnBudget=='')
                {
                    exit("Error: Budget year required");
                }
                $sQl="select distinct tutup from ".$dbname.".bgt_budget where ".$where2." and tutup=1";
               // exit("error".$sQl);
                $qQl=mysql_query($sQl) or die(mysql_error($conn));
                $row=mysql_num_rows($qQl);
                if($row!=1)
                {
                    $sUpdate="update ".$dbname.".bgt_budget set tutup=1 where ".$where2."";
                    //exit("error".$sUpdate);
                    if(mysql_query($sUpdate))
                        echo"";
                    else
                         echo " Gagal,_".$sUpdate."__".(mysql_error($conn));
                }
                else
                {
                    exit("Error: Budget has been closed");
                }
            break;
            case'getThnBudget':
            $optThnTtp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $sThn="select distinct tahunbudget from ".$dbname.".bgt_budget where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tipebudget='TRK' and tutup=0 order by tahunbudget desc";
            //echo $sThn;
            $qThn=mysql_query($sThn) or die(mysql_error($conn));
            while($rThn=mysql_fetch_assoc($qThn))
            {
                $optThnTtp.="<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget']."</option>";
            }
            echo $optThnTtp;
            break;
	default:
	break;
}
?>