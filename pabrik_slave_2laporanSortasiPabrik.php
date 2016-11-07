<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

if(isset($_POST['proses']))
{
        $proses=$_POST['proses'];
}
else
{
        $proses=$_GET['proses'];
}
$kdPbrk=$_POST['kdPbrk']==''?$_GET['kdPbrk']:$_POST['kdPbrk'];
$statBuah=$_POST['statBuah']==''?$_GET['statBuah']:$_POST['statBuah'];
if(isset($_POST['tglAkhir'])){
    $tglAkhir=tanggalsystem($_POST['tglAkhir']);
}else{
    $tglAkhir=tanggalsystem($_GET['tglAkhir']);
}
if(isset($_POST['tglAwal'])){
    $tglAwal=tanggalsystem($_POST['tglAwal']);
}else{
    $tglAwal=tanggalsystem($_GET['tglAwal']);
}

if($proses=='excel')
	$border="border=1";
else
	$border="border=0";

$_POST['suppId']==''?$suppId=$_GET['suppId']:$suppId=$_POST['suppId'];
$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];
$_POST['kdAfd']==''?$kdAfd=$_GET['kdAfd']:$kdAfd=$_POST['kdAfd'];
$intextId=$_POST['intextId']==''?$_GET['intextId']:$_POST['intextId'];
$BuahStat=$_POST['BuahStat']==''?$_GET['BuahStat']:$_POST['BuahStat'];
$sFr="select * from ".$dbname.".pabrik_5fraksi order by kode asc";
        $qFr=mysql_query($sFr) or die(mysql_error());
        $rNm=mysql_num_rows($qFr);

        while($rFraksi=mysql_fetch_assoc($qFr))
        {
            if($_SESSION['language']=='EN'){
                $zz=$rFraksi['keterangan1'];
            }else{
                $zz=$rFraksi['keterangan'];
            }
          $kodeFraksi[]=$rFraksi['kode'];
          $nmKeterangan[$rFraksi['kode']]=$zz;
        }
        // kondisi mendapatkan data


            if($suppId!='')
            {
                 $str="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$suppId."'";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                    $namaspl=$_SESSION['lang']['namasupplier'].":".$bar->namasupplier;
                }
            }
            else if($kdOrg!='')
            {
                 $str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$kdOrg."'";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                    $namaspl=$_SESSION['lang']['unit'].":".$bar->namaorganisasi;
                }
            }
            else
            {
                $namaspl=$_SESSION['lang']['dari'].":".$_SESSION['lang']['all'];
            }

        
        $thn=substr($tglAwal,0,4);
        $bln=substr($tglAwal,4,2);
        $dte=substr($tglAwal,6,2);
        $tglAwal1=$thn."-".$bln."-".$dte;
        $thn2=substr($tglAkhir,0,4);
        $bln2=substr($tglAkhir,4,2);
        $dte2=substr($tglAkhir,6,2);
        $tglAkhir1=$thn2."-".$bln2."-".$dte2;

        $stream.="<div style=overflow:auto; height:650px;>";
        $stream.="Mill FFB Grading Report ".$kdPbrk."  ".$namaspl." period :".$tglAwal."-".$tglAkhir."";

        $colspand=count($kodeFraksi);
        $stream.="<table cellpadding=1 cellspacing=1 ".$border." class=sortable width=100%>";
        $stream.="<thead><tr class=rowheader>";
        $stream.="<td rowspan=3>No.</td>";
        $stream.="<td rowspan=3>".$_SESSION['lang']['nospb']."</td>";
        $stream.="<td rowspan=3>".$_SESSION['lang']['noTiket']."</td>";
        $stream.="<td rowspan=3>".$_SESSION['lang']['tanggal']."</td>";
        $stream.="<td rowspan=3>".str_replace(" ","<br>",$_SESSION['lang']['nopol'])."</td>";
        $stream.="<td align=center  colspan=3 valign=middle>".$_SESSION['lang']['hslTimbangan']."</td>";
        $stream.="<td rowspan=3>".str_replace(" ","<br>",$_SESSION['lang']['jmlhTandan'])."</td>";
        $stream.="<td align=center rowspan=3 valign=middle>".$_SESSION['lang']['bjr']."</td>";
        $stream.="<td align=center rowspan=3 valign=middle>".$_SESSION['lang']['sortasi']."(JJG)</td>";
        $stream.="<td align=center rowspan=3 valign=middle>".$_SESSION['lang']['bjr']." ".$_SESSION['lang']['sortasi']."</td>";
        $stream.="<td align=center  valign=middle colspan=".($colspand).">Sortasi(JJg)</td>";
        $stream.="<td align=center rowspan=3 valign=middle>".$_SESSION['lang']['potongan']."(Kg)</td></tr>";
        $stream.="<tr>
             <td align=center rowspan=2  valign=middle>".$_SESSION['lang']['beratMasuk']."</td>
             <td align=center rowspan=2  valign=middle>".$_SESSION['lang']['beratkosong']."</td>
             <td align=center rowspan=2  valign=middle>".$_SESSION['lang']['beratBersih']."</td>";
        
       foreach ($kodeFraksi as $barisFraksi => $rFr)
        {

                     $stream.="<td align=center rowspan=2 >".$rFr."</td>";
        }
        $stream.="</tr>";
        $stream.="</thead><tr></tr><tbody>";
        if(($kdPbrk!='')&&($statBuah!='5'))
            {
                    if($statBuah==0)
                    {
                        if($suppId!='')
                        {
                            $add=" and kodecustomer='".$suppId."'";
                        }
                    }
                    else if($statBuah>0)
                    {
                        if($kdOrg!='')
                        {
                            $add=" and kodeorg='".$kdOrg."'";
                        }
                        if($kdAfd!='') {
                            $add=" and MID(nospb,9,6)='".$kdAfd."'";
                        }
                    }
                    $where=" substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and millcode='".$kdPbrk."' and intex='".$statBuah."'  ".$add."";
            }
            else if(($kdPbrk!='')&&($statBuah=='5'))
            {
                    $where=" substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and millcode='".$kdPbrk."'";
            }
            else if(($kdPbrk=='')&&($statBuah!='5'))
            {
                    if($statBuah=='0')
                    {
                        if($suppId!='')
                        {
                            $add=" and kodecustomer='".$suppId."'";
                        }
                    }
                    else if($statBuah>1)
                    {
                        if($kdOrg!='')
                        {
                            $add=" and kodeorg='".$kdOrg."'";
                        }
                        if($kdAfd!='') {
                            $add=" and MID(nospb,9,6)='".$kdAfd."'";
                        }
                    }
                    $where=" substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and intex='".$statBuah."'   ".$add."";
            }
            else if(($kdPbrk=='')&&($statBuah=='5'))
            {
                    $where= "substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."'";
            }
            $sMax="select notiket,kodefraksi,jumlah from ".$dbname.".pabrik_sortasi_vw where jumlah!=0 and ".$where." order by kodefraksi asc";
            //exit("error".$sMax);
            $qMax=fetchData($sMax);
            foreach($qMax as $brsMax => $rMax)
            {
                $jmlhFraksi[$rMax['notiket']][$rMax['kodefraksi']]=$rMax['jumlah'];
            }
//            echo "<pre>";
//                print_r( $jmlhFraksi);
//            echo "</pre>";
        $sql="select notransaksi,tanggal,nokendaraan,beratmasuk,beratkeluar,beratbersih,nospb,`jumlahtandan1` , `jumlahtandan2` , `jumlahtandan3`,a.jjgsortasi,a.persenBrondolan,a.kgpotsortasi
            from ".$dbname.".pabrik_timbangan a left join ".$dbname.".pabrik_sortasi b on a.notransaksi=b.notiket where ".$where." and b.jumlah!=0 and kodebarang='40000003' group by notransaksi,notiket  order by `tanggal` asc ";
        //echo "warning".$sql;exit();
        //echo $sql;
        $query=mysql_query($sql) or die(mysql_error());
        $row=mysql_num_rows($query);
        if($row>0)
        {
                while($res=mysql_fetch_assoc($query))
                {
                        $jmlhTndn=$res['jumlahtandan1']+$res['jumlahtandan2']+$res['jumlahtandan3'];
                        if(($jmlhTndn!=0)||($res['jjgsortasi']!=0))
                        {
                            @$jBrt=$res['beratbersih']/$res['jjgsortasi'];
                            @$jBrt2=$res['beratbersih']/$jmlhTndn;
                        }

                        else
                        {
                            $jBrt=0;
                            $jBrt2=0;
                        }
                            $subTotal['beratmasuk']+=$res['beratmasuk'];
                            $subTotal['beratkeluar']+=$res['beratkeluar'];
                            $subTotal['beratbersih']+=$res['beratbersih'];
                            $subTotal['jjgSortasitot']+=$res['jjgsortasi'];
                            $subTotal['prsnBrondolan']+=$res['persenBrondolan'];
                            $subTotal['jmlhTndn']+=$jmlhTndn;
                            //$subTotal['jBrt']+=$jBrt;
                            $subTotal['kgpotsortasi']+=$res['kgpotsortasi'];
                        $no+=1;

                            $stream.="<tr class=rowcontent>
                                    <td>".$no."</td>
                                    <td>".$res['nospb']."</td>
                                    <td>".$res['notransaksi']."</td>
                                    <td>".tanggalnormal($res['tanggal'])."</td>				 
                                    <td>".$res['nokendaraan']."</td>			 		
                                    <td align=right>".number_format($res['beratmasuk'],2)."</td>
                                    <td align=right>".number_format($res['beratkeluar'],2)."</td>
                                    <td align=right>".number_format($res['beratbersih'],2)."</td>
                                    <td align=right>".number_format($jmlhTndn,0)."</td>
                                    <td align=right>".number_format($jBrt,2)."</td>
                                    <td align=right>".number_format($res['jjgsortasi'],0)."</td>
                                    <td align=right>".number_format($jBrt2,2)."</td>";
                                        
                                    foreach($kodeFraksi as $brsKdFraksi =>$listFraksi)
                                    {
                                            $stream.="<td width=60 align=right>".number_format($jmlhFraksi[$res['notransaksi']][$listFraksi],2)."</td>";
                                       
                                       //  $jmlhFraksi[$rMax['notiket']][$rMax['kodefraksi']]
                                        $subTotal[$listFraksi]+=$jmlhFraksi[$res['notransaksi']][$listFraksi];
                                        $j++;

                                    }
                                    $stream.="<td align=right>".number_format($res['kgpotsortasi'],2)."</td>";
                            $stream.="	
                            </tr>
                            ";


                }
                 $stream.="<tr class=rowcontent><td colspan=5>".$_SESSION['lang']['total']."</td>
                    <td align=right>".number_format($subTotal['beratmasuk'],2)."</td>
                    <td align=right>".number_format($subTotal['beratkeluar'],2)."</td>
                    <td align=right>".number_format($subTotal['beratbersih'],2)."</td>
                    <td align=right>".number_format($subTotal['jmlhTndn'],2)."</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>".number_format($subTotal['jjgSortasitot'],2)."</td>
                    <td align=right>&nbsp;</td>
                        ";

                $sFraksi="select kode from ".$dbname.".pabrik_5fraksi order by kode asc";
                $qFraksi=mysql_query($sFraksi) or die(mysql_error());
                while($rFraksi=mysql_fetch_assoc($qFraksi))
                {    
                         $stream.="<td align=right>".number_format($subTotal[$rFraksi['kode']],2)."</td>";	
                      $subTotal[$rFraksi['kode']]=0;
                }

                $stream.="<td align=right>".number_format($subTotal['kgpotsortasi'],2)."</td>";  
                $stream.="</tr>";
                $subTotal['beratmasuk']=0;
                $subTotal['beratkeluar']=0;
                $subTotal['beratbersih']=0;
                $subTotal['jmlhTndn']=0;
                //$subTotal['jBrt']=0;
                $subTotal['jjgSortasitot']=0;
                $subTotal['prsnBrondolan']=0;
                $subTotal['kgpotsortasi']=0;
        }
        else
        {
                $stream.="<tr class=rowcontent><td colspan=23 align=center>Not Found</td></tr>";
        }
        $stream.="</tbody></table><div>";
             
            
switch($proses)
{
        case'preview':
		
		if(($tglAkhir=='')||($tglAwal==''))
        {
                echo"warning:Date required";
                exit();
        }
		
              echo $stream;       
        break;
        case'excel':
		
		if(($tglAkhir=='')||($tglAwal==''))
        {
                echo"warning:Date required";
                exit();
        }

                        //echo "warning:".$strx;
                        //=================================================
                $stream.="Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
                        $tglSkrg=date("Ymd");
                        $nop_="rekapSortasiBuah_".$tglSkrg;
                        if(strlen($stream)>0)
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
                        if(!fwrite($handle,$stream))
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
        case'getDetail':
        echo"<link rel=stylesheet type=text/css href=style/generic.css>";
        $nokontrak=$_GET['nokontrak'];
        $sHed="select  a.tanggalkontrak,a.koderekanan,a.kodebarang from ".$dbname.".pmn_kontrakjual a where a.nokontrak='".$nokontrak."'";
        $qHead=mysql_query($sHed) or die(mysql_error());
        $rHead=mysql_fetch_assoc($qHead);
        $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rHead['kodebarang']."'";
        $qBrg=mysql_query($sBrg) or die(mysql_error());
        $rBrg=mysql_fetch_assoc($qBrg);

        $sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rHead['koderekanan']."'";
        $qCust=mysql_query($sCust) or die(mysql_error());
        $rCust=mysql_fetch_assoc($qCust);
        echo"<fieldset><legend>".$_SESSION['lang']['detailPengiriman']."</legend>
        <table cellspacing=1 border=0 class=myinputtext>
        <tr>
                <td>".$_SESSION['lang']['NoKontrak']."</td><td>:</td><td>".$nokontrak."</td>
        </tr>
        <tr>
                <td>".$_SESSION['lang']['tglKontrak']."</td><td>:</td><td>".tanggalnormal($rHead['tanggalkontrak'])."</td>
        </tr>
        <tr>
                <td>".$_SESSION['lang']['komoditi']."</td><td>:</td><td>".$rBrg['namabarang']."</td>
        </tr>
        <tr>
                <td>".$_SESSION['lang']['Pembeli']."</td><td>:</td><td>".$rCust['namacustomer']."</td>
        </tr>
        </table><br />
        <table cellspacing=1 border=0 class=sortable><thead>
        <tr class=data>
        <td>".$_SESSION['lang']['notransaksi']."</td>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td>".$_SESSION['lang']['nodo']."</td>
        <td>".$_SESSION['lang']['nosipb']."</td>
        <td>".$_SESSION['lang']['beratBersih']."</td>
        <td>".$_SESSION['lang']['kodenopol']."</td>
        <td>".$_SESSION['lang']['sopir']."</td>
        </tr></thead><tbody>
        ";
/*	$sDet="select a.tanggalkontrak,a.pembeli,a.komoditi,b.* from ".$dbname.".pmn_kontrakjual a inner join ".$dbname.".pabrik_timbangan on a.nokontrak=b.nokontrak where a.nokontrak='".$nokontrak."'";
*/	

        $sDet="select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir from ".$dbname.".pabrik_timbangan where nokontrak='".$nokontrak."'";
        $qDet=mysql_query($sDet) or die(mysql_error());
        $rCek=mysql_num_rows($qDet);
        if($rCek>0)
        {
                while($rDet=mysql_fetch_assoc($qDet))
                {
                        echo"<tr class=rowcontent>
                        <td>".$rDet['notransaksi']."</td>
                        <td>".tanggalnormal($rDet['tanggal'])."</td>
                        <td>".$rDet['nodo']."</td>
                        <td>".$rDet['nosipb']."</td>
                        <td align=right>".number_format($rDet['beratbersih'],2)."</td>
                        <td>".$rDet['nokendaraan']."</td>
                        <td>".ucfirst($rDet['supir'])."</td>
                        </tr>";
                }
        }
        else
        {
                echo"<tr><td colspan=7>Not Found</td></tr>";
        }
        echo"</tbody></table></fieldset>";

        break;
        case'getkbn':
               // $optkdOrg2="<option value=''></option value=''>".$_SESSION['lang']['all']."</option>";
            if($kdPbrk=='')
            {
                exit("Error: Mill code required");
            }

                if($BuahStat==0)
                {
                        $optkdOrg2.="<option value=''>".$_SESSION['lang']['all']."</option>";
                        $sOrg="SELECT namasupplier,supplierid,kodetimbangan FROM ".$dbname.".log_5supplier WHERE substring(kodekelompok,1,1)='S' and kodetimbangan is not null";//echo "warning:".$sOrg;exit();
                        $qOrg=mysql_query($sOrg) or die(mysql_error());
                        while($rOrg=mysql_fetch_assoc($qOrg))
                        {
                                $optkdOrg2.="<option value=".$rOrg['kodetimbangan']."".($rOrg['kodetimbangan']==$idCust?'selected':'').">".$rOrg['namasupplier']."</option>";
                        }
                        //echo"warning:test";
                        echo $optkdOrg2."###".$BuahStat;exit();
                }
                elseif($BuahStat==5)
                {
                    $optkdOrg2.="<option value=''>".$_SESSION['lang']['all']."</option>";
                    echo $optkdOrg2."###".$BuahStat;exit();
                }
                elseif($BuahStat==1)
                {
                    $sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and kodeorganisasi in(select distinct kodeorg from ".$dbname.".pabrik_timbangan where intex='".$BuahStat."' and millcode='".$kdPbrk."')";//echo "warning:".$sOrg;
                        //$sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and induk in(select induk from ".$dbname.".organisasi where tipe='PABRIK')";//echo "warning:".$sOrg;
                }
                elseif($BuahStat==2)
                {
                    $sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and kodeorganisasi in(select distinct kodeorg from ".$dbname.".pabrik_timbangan where intex='".$BuahStat."'  and millcode='".$kdPbrk."')";//echo "warning:".$sOrg;
                        //$sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and induk not in(select induk from ".$dbname.".organisasi where tipe='PABRIK')"; //echo "warning:".$sOrg;
                }
                $optkdOrg="<option value=''>".$_SESSION['lang']['all']."</option>";
                $qOrg=mysql_query($sOrg) or die(mysql_error());
                while($rOrg=mysql_fetch_assoc($qOrg))
                {
                        $optkdOrg.="<option value=".$rOrg['kodeorganisasi']."".($rOrg['kodeorganisasi']==$kdKbn?'selected':'').">".$rOrg['namaorganisasi']."</option>";
                }

                echo $optkdOrg."###".$BuahStat;
                break;

        break;
        case'getafd':
                $sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='AFDELING' and induk='".$_SESSION['empl']['lokasitugas']."'";//echo "warning:".$sOrg;
                $optkdOrg="<option value=''>".$_SESSION['lang']['all']."</option>";
                $qOrg=mysql_query($sOrg) or die(mysql_error());
                while($rOrg=mysql_fetch_assoc($qOrg))
                {
                        $optkdOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
                }

                echo $optkdOrg;
                break;

        break;
}

?>