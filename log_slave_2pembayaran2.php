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

$optSupp=makeOption($dbname, 'log_5supplier','supplierid,namasupplier');
$optNmOrg=makeOption($dbname, 'organisasi','kodeorganisasi,namaorganisasi');
//$arr2="##tgl_cari##tgl_cari2##jenisId2##kdUnit2##cariNopo##suppId2";
$_POST['tgl_cari']==''?$tgl_cari=tanggalsystem($_GET['tgl_cari']):$tgl_cari=tanggalsystem($_POST['tgl_cari']);
$_POST['tgl_cari2']==''?$tgl_cari2=tanggalsystem($_GET['tgl_cari2']):$tgl_cari2=tanggalsystem($_POST['tgl_cari2']);
$_POST['kdUnit2']==''?$kdUnit=$_GET['kdUnit2']:$kdUnit=$_POST['kdUnit2'];
$_POST['jenisId2']==''?$jenisId2=$_GET['jenisId2']:$jenisId2=$_POST['jenisId2'];
$_POST['cariNopo']==''?$cariNopo=$_GET['cariNopo']:$cariNopo=$_POST['cariNopo'];
$_POST['suppId2']==''?$suppId2=$_GET['suppId2']:$suppId2=$_POST['suppId2'];
$unitId=$_SESSION['lang']['all'];
$dktlmpk=$_SESSION['lang']['all'];
if($tgl_cari2!='')
{
    if($tgl_cari=='')
    {
        exit("Error: Date required");
    }

    $sSel="SELECT datediff('".$tgl_cari2."','".$tgl_cari."') as selisih";
    $qSel=mysql_query($sSel) or die(mysql_error($conn));
    $rSel=mysql_fetch_array($qSel);
    if($rSel['selisih']<0)
    {
        exit("Error: Date required");
    }
    $whre.=" and tanggal between '".$tgl_cari."' and  '".$tgl_cari2."'";
    $rhd=" and left(b.tanggal,4)='".substr($tgl_cari,0,4)."'";

}
else if($tgl_cari!='')
{
    $whre.=" and tanggal='".$tgl_cari."'";
}

if($kdUnit!='')
{
     $unitId=$optNmOrg[$kdUnit];
     $whre.=" and kodeorg='".$kdUnit."'";
}

if($jenisId2!='')
{
    $jenisId2=='0'?$dr="k":$dr="p";
    $whre.=" and tipeinvoice='".$dr."'";
}

if($suppId2!='')
{
    $whre.=" and kodesupplier='".$suppId2."'";
}
if($cariNopo!='')
{
    $whre.=" and nopo like '%".$cariNopo."%'";
}

if($proses=='preview'||$proses=='excel')
{
    if($jenisId2=='')
    {
        exit("Error: Transaction type required");
    }

    $sTagi="select distinct nopo,sum(nilaiinvoice+nilaippn) as jumlah,noinvoice,kodesupplier,tanggal,jatuhtempo from
           ".$dbname.".keu_tagihanht where tanggal!=''  ".$whre." group by nopo,noinvoice";
    //exit("Error:".$sTagi);
    $qTagi=mysql_query($sTagi) or die(mysql_error($conn));
    while($rTagi=  mysql_fetch_assoc($qTagi))
    {
        if($rTagi['kodesupplier']!='')
        {
        $dtNopo[$rTagi['noinvoice']]=$rTagi['nopo'];
        $dtNotrans[$rTagi['noinvoice']]=$rTagi['noinvoice'];
        $dtTagih[$rTagi['noinvoice']]=$rTagi['jumlah'];
        $dtSupp[$rTagi['noinvoice']]=$rTagi['kodesupplier'];
        $dtJth[$rTagi['noinvoice']]=$rTagi['jatuhtempo'];
        $dtTglEn[$rTagi['noinvoice']]=$rTagi['tanggal'];
        }
    }
  $sByr="select distinct sum(a.jumlah) as jumlah,b.tanggal,a.notransaksi,a.keterangan1
                  from ".$dbname.". keu_kasbankdt a
                  left join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi 
                  INNER JOIN ".$dbname.".keu_tagihanht c ON a.keterangan1 = c.noinvoice
                  where a.keterangan1!='' 
                  and a.tipetransaksi='K' and b.posting=1
                  group by a.keterangan1,a.tipetransaksi";
    
// $sByr="select distinct sum(a.jumlah) as jumlah,b.tanggal,a.notransaksi,a.keterangan1
//                  from ".$dbname.". keu_kasbankdt a
//                  left join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi where a.keterangan1!='' ".$rhd."
//                  and a.tipetransaksi='K' and posting=1
//                  group by a.keterangan1,a.tipetransaksi";
            //echo $sByr;
$qByr=mysql_query($sByr) or die(mysql_error($conn));
while($rByr=mysql_fetch_assoc($qByr)){
    $penambah[$rByr['keterangan1']]=$rByr['jumlah'];
    $ntrKasBank[$rByr['keterangan1']]=$rByr['notransaksi'];
    $tglKasBank[$rByr['keterangan1']]=$rByr['tanggal'];
}
$sByr="select distinct sum(a.jumlah) as jumlah,a.keterangan1  from ".$dbname.". keu_kasbankdt a
      left join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi 
      INNER JOIN ".$dbname.".keu_tagihanht c ON a.keterangan1 = c.noinvoice
      where a.keterangan1!='' 
      and a.tipetransaksi='M'  and b.posting=1 group by a.keterangan1,a.tipetransaksi";
//$sByr="select distinct sum(a.jumlah) as jumlah,a.keterangan1  from ".$dbname.". keu_kasbankdt a
//      left join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi where a.keterangan1!='' ".$rhd."
//      and a.tipetransaksi='M'  and posting=1 group by a.keterangan1,a.tipetransaksi";
//echo $sByr;
$qByr=mysql_query($sByr) or die(mysql_error($conn));
while($rByr2=mysql_fetch_assoc($qByr)){
    $pengurang[$rByr2['keterangan1']]=$rByr2['jumlah'];
}
$cekdt=count($dtNotrans);
if($cekdt==0)
{
    exit("Error: No data found");
}
$brdr=0;
$bgcoloraja='';
if($proses=='excel')
{
    //exit("error:".$arrPilMode[$pilMode]."__".$pilMode);
    $bgcoloraja="bgcolor=#DEDEDE align=center";
    $brdr=1;
    $tab.="
    <table>
    <tr><td colspan=15 align=left><b><font size=5>Riwayat Pembayaran</font></b></td></tr>
    <tr><td colspan=15 align=left>".$_SESSION['lang']['pt']." : ".$unitId."</td></tr>
    <tr><td colspan=15 align=left>".$_SESSION['lang']['periode']." : ".$periode."</td></tr>
    </table>";
}

        $tab.="<table cellspacing=1 border=".$brdr." class=sortable>
        <thead class=rowheader>
        <tr>
        <td ".$bgcoloraja." rowspan=2 align=center>No.</td>
        <td ".$bgcoloraja." rowspan=2 align=center>".$_SESSION['lang']['noinvoice']."</td>
        <td ".$bgcoloraja." rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>
        <td ".$bgcoloraja." rowspan=2 align=center>".$_SESSION['lang']['jatuhtempo']."</td>
        <td ".$bgcoloraja." colspan=4 align=center>".$_SESSION['lang']['tagihan']."</td>
        <td ".$bgcoloraja." colspan=3 align=center>".$_SESSION['lang']['dibayar']."</td>
        <td ".$bgcoloraja." rowspan=2 align=center>".$_SESSION['lang']['sisa']."</td></tr>";

        $tab.="<tr>";
        $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['kodesupplier']."</td>";
        $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['namasupplier']."</td>";
        $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['nopo']."/ ".$_SESSION['lang']['nospk']."</td>";
        $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['total']." ".$_SESSION['lang']['tagihan']."</td>";
        $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['notransaksi']."</td>";
        $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['tanggal']."</td>";
        $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['total']." ".$_SESSION['lang']['dibayar']."</td>";
        $tab.="</tr></thead><tbody>";
        foreach($dtNotrans as $hutang)
        {
            $aerta+=1;
           

            $dibayarsmp[$hutang]=$penambah[$hutang]-$pengurang[$hutang];
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$aerta."</td>";
            $tab.="<td>".$hutang."</td>";
            $tab.="<td>".$dtTglEn[$hutang]."</td>";
            $tab.="<td>".$dtJth[$hutang]."</td>";
            $tab.="<td>".$dtSupp[$hutang]."</td>";
            $tab.="<td>".$optSupp[$dtSupp[$hutang]]."</td>";
            $tab.="<td>".$dtNopo[$hutang]."</td>";
            $tab.="<td align=right>".number_format($dtTagih[$hutang],0)."</td>";
            $tab.="<td>".$ntrKasBank[$hutang]."</td>";
            $tab.="<td>".$tglKasBank[$hutang]."</td>";
            $tab.="<td align=right>".number_format($dibayarsmp[$hutang],0)."</td>";
            $sis[$hutang]=$dtTagih[$hutang]-$dibayarsmp[$hutang];
            $tab.="<td align=right>".number_format($sis[$hutang],0)."</td>";
            $tab.="</tr>";
        }
}
        $tab.="</tbody></table>";
switch($proses)
{
        case'getPt':
        //echo "warning:masuk";
        $optorg="<option value=''>".$_SESSION['lang']['all']."</option>";
        $sOrg="select distinct kodeorg  from ".$dbname.".log_po_vw where substr(tanggal,1,7)='".$periode."'";
        //exit("Error:".$sOrg);
        $qOrg=mysql_query($sOrg) or die(mysql_error());
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $optorg.="<option value=".$rOrg['kodeorg'].">".$optNmOrg[$rOrg['kodeorg']]."</option>";
        }
        echo $optorg;
        break;
        case'getJenis':
        $optSu=$optorg="<option value=''>".$_SESSION['lang']['all']."</option>";
         $jenisId=='0'?$whr=" and tipeinvoice='K'":$whr=" and tipeinvoice='P'";
         if($kdUnit!='')
         {
             $whr.=" and kodeorg='".$kdUnit."'";
         }

        $sData="select distinct nopo from ".$dbname.".keu_tagihanht 
               where substr(tanggal,1,7)='".$periode."' ".$whr."";
        $qOrg=mysql_query($sData) or die(mysql_error($conn));
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $optorg.="<option value=".$rOrg['nopo'].">".$rOrg['nopo']."</option>";
        }
        $sData="select distinct kodesupplier from ".$dbname.".keu_tagihanht
               where substr(tanggal,1,7)='".$periode."' ".$whr."";
        $qOrg=mysql_query($sData) or die(mysql_error($conn));
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $optSu.="<option value='".$rOrg['kodesupplier']."'>".$optSupp[$rOrg['kodesupplier']]."</option>";
        }

        echo $optorg."###".$optSu;
        break;
        case'preview':
        echo $tab;
        break;

    case'excel':

        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="riwayat_pembayaran_".$dte;
         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
         gzwrite($gztralala, $tab);
         gzclose($gztralala);
         echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls.gz';
            </script>";

        break;
        case'getTgl':
        if($periode!='')
        {
                $tgl=$periode;
                $tanggal=$tgl[0]."-".$tgl[1];
        }
        elseif($period!='')
        {
                $tgl=$period;
                $tanggal=$tgl[0]."-".$tgl[1];
        }
        if($kdUnit=='')
        {
            $kdUnit=$_SESSION['lang']['lokasitugas'];
        }
        $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where kodeorg='".substr($kdUnit,0,4)."' and periode='".$tanggal."' ";
        //echo"warning".$sTgl;
        $qTgl=mysql_query($sTgl) or die(mysql_error());
        $rTgl=mysql_fetch_assoc($qTgl);
        echo tanggalnormal($rTgl['tanggalmulai'])."###".tanggalnormal($rTgl['tanggalsampai']);
        break;

        case'pdf':
        $kdPt=$_GET['kdPt'];
        //$arr="##kdPt##kdSup##kdUnit##tglDr##tglSmp";
        $kdSup=$_GET['kdSup'];
        $kdUnit=$_GET['kdUnit'];
        $tglDari=tanggalsystem($_GET['tglDr']);
        $tanggalSampai=tanggalsystem($_GET['tanggalSampai']);	
        $lokBeli=$_GET['lokBeli'];
        //echo $tglDari."__".$tanggalSampai;exit();
        if(($tglDari=='')||($tanggalSampai==''))
        {
                echo"warning: Date required";
                exit();
        }
        else
        {
                if($kdPt!='')
                                {
                                        $where.=" and a.kodeorg='".$kdPt."'";
                                }
                                if($kdUnit!='')
                                {
                                        $where.=" and substring(b.nopp,16,4)='".$kdUnit."'";
                                }
                                if($kdSup!="")
                                {
                                        $where.=" and a.kodesupplier='".$kdSup."'";
                                }
                                if(($tglDr!='')||($tanggalSampai!=''))
                                {
                                        $where.=" and (a.tanggal between '".$tglDari."' and '".tanggalsystem($_GET['tanggalSampai'])."')";
                                }
                                if($lokBeli!='')
                                {
                                    $where.=" and lokalpusat='".$lokBeli."'";
                                }
        }

         class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                                global $kdPt;
                                global $kdSup;
                                global $kdUnit;
                                global $tglDari;
                                global $tanggalSampai;
                                global $where;
                                global $isi;

                                $isi=array();
                                if($kdPt=="")
                                {
                                        $pt='MHO';
                                }
                                else
                                {
                                        $pt=$kdPt;
                                }
                # Alamat & No Telp
       /*         $query = selectQuery($dbname,'organisasi','namaorganisasi,alamat,telepon',
                    "kodeorganisasi='".$kdPt."'");
                $orgData = fetchData($query);*/
                                $sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
                                $qAlamat=mysql_query($sAlmat) or die(mysql_error());
                                $rAlamat=mysql_fetch_assoc($qAlamat);

                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 11;
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
                $this->SetFont('Arial','B',11);
                $this->Cell($width,$height, $_SESSION['lang']['detPemb'],0,1,'C');	
                                $this->SetFont('Arial','',8);
                                $this->Cell($width,$height, "Periode : ".$_GET['tglDr']." s.d. ".$_GET['tanggalSampai'],0,1,'C');	
                                $this->Ln();$this->Ln();
                $this->SetFont('Arial','B',7);	
                $this->SetFillColor(220,220,220);


                                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                                $this->Cell(15/100*$width,$height,$_SESSION['lang']['supplier'],1,0,'C',1);		
                                $this->Cell(12/100*$width,$height,$_SESSION['lang']['nopo'],1,0,'C',1);		
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);			
                                $this->Cell(22/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);	
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['matauang'],1,0,'C',1);		
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['jumlah'],1,0,'C',1);
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);	
                                $this->Cell(7/100*$width,$height,$_SESSION['lang']['tanggal']." PP",1,0,'C',1);	
                                $this->Cell(7/100*$width,$height,$_SESSION['lang']['tanggal']." BAPB",1,1,'C',1);					

            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('L','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 9;
                $pdf->AddPage();
                $pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','',7);
                $sData="select a.kodesupplier from ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo where a.statuspo>1 ".$where." group by kodesupplier order by a.tanggal asc";
        $qData=mysql_query($sData) or die(mysql_error());
        while($rData=mysql_fetch_assoc($qData))
        {
                $isi[]=$rData;
        }
        $totalAll=array();
        foreach($isi as $test => $dt)
        {
                $no+=1;

                $i=0;$afdC=false;
                $sNm="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$dt['kodesupplier']."'";
                $qNm=mysql_query($sNm) or die(mysql_error());
                $rNm=mysql_fetch_assoc($qNm);
                if($afdC==false)
                {
                        $pdf->Cell(3/100*$width,$height,$no,'TLR',0,'C',1);
                        $pdf->Cell(15/100*$width,$height,$rNm['namasupplier'],'TLR',0,'C',1);	
                }

                $sList="select distinct a.tanggal,a.matauang,b.kodebarang,b.satuan,b.nopo,b.jumlahpesan,b.nopp,b.hargasatuan from ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo where a.kodesupplier='".$dt['kodesupplier']."' and b.nopo!='NULL' and a.tanggal between '".$tglDari."' and '".$tanggalSampai."'";
                $qList=mysql_query($sList) or die(mysql_error());
                $grandTot=array();

                while($rList=mysql_fetch_assoc($qList))
                {		
                        $limit++;
                        $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rList['kodebarang']."'";
                        $qBrg=mysql_query($sBrg) or die(mysql_error());
                        $rBrg=mysql_fetch_assoc($qBrg);
                        if($rList['matauang']!='IDR')
                        {
                                $sKurs="select kurs from ".$dbname.".setup_matauangrate where kode='".$rList['matauang']."' and daritanggal='".$rList['tanggal']."'";
                                $qKurs=mysql_query($sKurs) or die(mysql_error());
                                $rKurs=mysql_fetch_assoc($qKurs);
                                if($rKurs!='')
                                {
                                        $hrg=$rKurs['kurs']*$rList['hargasatuan'];
                                        $totHrg=$rList['jumlahpesan']*$hrg;
                                }
                                else
                                {
                                        if($rList['matauang']=='USD')
                                        {
                                                $hrg=$rList['hargasatuan']*8850;
                                                $totHrg=$rList['jumlahpesan']*$hrg;
                                                $rList['matauang']="IDR";
                                        }
                                        elseif($rList['matauang']=='EUR') 
                                        {
                                                $hrg=$rList['hargasatuan']*12643;
                                                $totHrg=$rList['jumlahpesan']*$hrg;
                                                $rList['matauang']="IDR";
                                        }
                                        elseif(($rList['matauang']=='')||($rList['matauang']=='NULL'))
                                        {
                                                $totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
                                        }
                                }
                        }
                        else
                        {
                                $totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
                        }
                        //$totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
                        $grandTot['total']+=$totHrg;
                        if($rList['nopp']!="")
                        {
                                $sTgl="select tanggal from ".$dbname.".log_prapoht where nopp='".$rList['nopp']."'";
                                $qTgl=mysql_query($sTgl) or die(mysql_error());
                                $rTgl=mysql_fetch_assoc($qTgl);

                                if(($rTgl['tanggal']!="")||($rTgl['tanggal']!="000-00-00"))
                                {
                                                $tglPP=tanggalnormal($rTgl['tanggal']);		
                                }
                                else
                                {
                                        $tglPP="";
                                }
                        }
                        else
                        {
                                $tglPP="";
                        }
                        if($rList['nopo']!="")
                        {
                                $sTgl2="select tanggal from ".$dbname.".log_transaksiht where nopo='".$rList['nopo']."' and tipetransaksi=1";
                                $qTgl2=mysql_query($sTgl2) or die(mysql_error());
                                $rTgl2=mysql_fetch_assoc($qTgl2);
                                if($rTgl2['tanggal']!="")
                                {
                                                $tglBapb=tanggalnormal($rTgl2['tanggal']);		
                                }
                                else
                                {
                                        $tglBapb="";
                                }
                        }
                        else
                        {
                                $tglBapb="";
                        }
                        if($afdC==true) {
                                $i=0;
                                $pdf->Cell(3/100*$width,$height,'','LR',$align[$i],1);
                                $pdf->Cell(15/100*$width,$height,'','LR',$align[$i],1);
                                //$pdf->Cell($length[$i]/100*$width,$height,'','LR',$align[$i],1);
                                $i++;
                        } else {
                                $afdC = true;
                        }	
                        $pdf->Cell(12/100*$width,$height,$rList['nopo'],1,0,'L',1);		
                        $pdf->Cell(6/100*$width,$height,tanggalnormal($rList['tanggal']),1,0,'C',1);			
                        $pdf->Cell(22/100*$width,$height,$rBrg['namabarang'],1,0,'L',1);	
                        $pdf->Cell(6/100*$width,$height,$rList['matauang'],1,0,'C',1);		
                        $pdf->Cell(6/100*$width,$height,$rList['jumlahpesan'],1,0,'R',1);
                        $pdf->Cell(6/100*$width,$height,$rList['satuan'],1,0,'C',1);
                        $pdf->Cell(10/100*$width,$height,number_format($totHrg,2),1,0,'R',1);	
                        $pdf->Cell(7/100*$width,$height,$tglPP,1,0,'C',1);	
                        $pdf->Cell(7/100*$width,$height,$tglBapb,1,1,'C',1);
                        //if($limit==46)				
//			{	
//				$limit=0;
//				$pdf->AddPage();
//			}

                }
                $totalAll['totalSemua']+=$grandTot['total'];
                $pdf->Cell(76/100*$width,$height,"Sub Total",1,0,'C',1);
                $pdf->Cell(10/100*$width,$height,number_format($grandTot['total'],2),1,0,'R',1);
                $pdf->Cell(14/100*$width,$height,'',1,1,'R',1);
        }
        $pdf->Cell(76/100*$width,$height,"Total",1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,number_format($totalAll['totalSemua'],2),1,0,'R',1);
        $pdf->Cell(14/100*$width,$height,'',1,1,'R',1);
        $pdf->Cell($width,$height,terbilang($totalAll['totalSemua'],2),1,1,'C',1);


        $pdf->Output();
        break;


        default:
        break;
}
?>