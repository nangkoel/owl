<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdOrg=$_POST['kodeorg'];
$periode=$_POST['periode'];
if(($proses=='excel')or($proses=='pdf')){
        $kdOrg=$_GET['kodeorg'];
        $periode=$_GET['periode'];
}

if(($proses=='preview')or($proses=='excel')or($proses=='pdf')){
    if($periode==''){
            echo"Error: Period required."; exit;
    }

}
#ambil tahun tanam
$tahuntanam=Array();
$str="select kodeorg,tahuntanam from ".$dbname.".setup_blok where kodeorg like '".$kdOrg."%'";
$res=mysql_query($str);

while($bar=mysql_fetch_object($res))
{
    $tahuntanam[$bar->kodeorg]=$bar->tahuntanam;
}
#ambil namakegiatan
$namakegiatan=Array();
if($_SESSION['language']=='EN'){
    $zz='namaakun1 as namaakun';
}else{
    $zz='namaakun as namaakun';
}
$str="select noakun,".$zz." from ".$dbname.".keu_5akun where length(noakun)=7";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namakegiatan[$bar->noakun]=$bar->namaakun;
}

#ambil satuan kegiatan:
$satuan=Array();
$str="select kodekegiatan,satuan from ".$dbname.".setup_kegiatan order by kodekegiatan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $satuan[$bar->kodekegiatan]=$bar->satuan;
}

#generate SQL
#mendapatkan rupiah realisasi per kelompok akun per blok
$str="select sum(debet) as biaya,noakun,kodeblok from ".$dbname.".keu_jurnaldt_vw where left(tanggal,7)='".$periode.
        "' and kodeorg='".$kdOrg."' and noakun in (select distinct noakun from ".$dbname.".keu_5akun where left(noakun,3) "
        . "in ('611','621')) group by noakun,kodeblok";
//exit ("error:".$str);
$res=mysql_query($str) or die(mysql_error());
while ($bar=mysql_fetch_object($res))
{
    $rprealisasi[$bar->noakun.$bar->kodeblok]=$bar->biaya;
    $nomorakun[$bar->noakun]=$bar->noakun;
    $blok[$bar->kodeblok]=$bar->kodeblok;
    
}

#mencari rupiah dan fisik budget perbulan
$str1="select sum(rp".substr($periode,5,2).") as bgtblnan,sum(fis".substr($periode,5,2).") as bgtfisik,noakun,kodeorg from ".$dbname.
        ".bgt_budget_detail where kodeorg like '".$kdOrg."%' and tahunbudget=".substr($periode,0,4).
        " and noakun in (select distinct noakun from ".$dbname.".keu_5akun where left(noakun,3)
        in ('611','621')) group by noakun,kodeorg";
//echo $str1;
$res1=mysql_query($str1) or die(mysql_error());
while($bar1=mysql_fetch_object($res1))
{
    $rpblnan[$bar1->noakun.$bar1->kodeorg]=$bar1->bgtblnan;
    $rpproduksi[$bar1->noakun.$bar1->kodeorg]=$bar1->bgtfisik;
    $nomorakun[$bar1->noakun]=$bar1->noakun;
    $blok[$bar1->kodeorg]=$bar1->kodeorg;
}

$str2="select a.kodeorg,left(kodekegiatan,7) as noakun,sum(hasilkerjakg) as kg from ".$dbname.
        ".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi where left(tanggal,7)="
        .$periode." and a.kodeorg like '".$kdOrg."%' and left(kodekegiatan,3) in ('611','621') group by left(kodekegiatan,7),a.kodeorg";
$res2=mysql_query($str2) or die(mysql_error());
while($bar2=mysql_fetch_object($res2))
{
    $nomorakun[$bar2->noakun]=$bar2->noakun;
    $hasilkerja[$bar2->kg]=$bar2->kg;
}

#mencari luas realisasi
$str3="select luasareaproduktif,kodeorg from ".$dbname.
        ".setup_blok where kodeorg like '".$kdOrg."%'";
$res3=mysql_query($str3) or die(mysql_error());
while($bar3=mysql_fetch_object($res3))
{
    $blok[$bar3->kodeorg]=$bar3->kodeorg;
    $luasrealisasi[$bar3->luasareaproduktif]=$bar3->luasareaproduktif;
}

#mencari luas budget
$str4="select hamutasi from ".$dbname.
        ".bgt_blok where kodeblok like '".$kdOrg."%' and tahunbudget=".substr($periode,0,4);
$res4=mysql_query($str4) or die(mysql_error());
while($bar4=mysql_fetch_object($res4))
{
    $luasbudget[$bar4->haproduktif]=$bar4->haproduktif;
}


#+++++++++++++++++++++++process data
$stream="UNIT:".$kdOrg."<br>
                PERIODE:".$periode."";
                if($proses=='excel')$stream.="<table cellspacing='1' border='1' class='sortable'>";
                else $stream.="<table cellspacing='1' border='0' class='sortable'>";
                $stream.="<thead class=rowheader>
                <tr>
                <td rowspan=3>No</td>
                <td rowspan=3>".$_SESSION['lang']['noakun']."</td>
                <td rowspan=3>".$_SESSION['lang']['kegiatan']."</td>    
                <td rowspan=3>".$_SESSION['lang']['blok']."</td>
                <td rowspan=3>".$_SESSION['lang']['tahuntanam']."</td> 
                <td rowspan=2 colspan=2 align=center>".$_SESSION['lang']['luas']."</td>
                <td colspan=6 align=center>".$_SESSION['lang']['produksi']."</td>
                <td colspan=2 align=center>".$_SESSION['lang']['biaya']."</td>
                </tr>
                <tr>
                <td colspan=3 align=center>".$_SESSION['lang']['realisasi']."</td>
                <td colspan=3 align=center>".$_SESSION['lang']['budget']."</td>
                <td>".$_SESSION['lang']['realisasi']."</td>
                <td>".$_SESSION['lang']['budget']."</td>
                </tr>
                </tr>
                <tr>
                <td>".$_SESSION['lang']['realisasi']."</td>
                <td>".$_SESSION['lang']['budget']."</td>
                <td>Hasil Kerja</td>
                <td>".$_SESSION['lang']['satuan']."</td>
                <td>".$_SESSION['lang']['panen']."(KG)</td>
                <td>Hasil Kerja</td>
                <td>".$_SESSION['lang']['satuan']."</td>
                <td>".$_SESSION['lang']['panen']."(KG)</td>
                <td colspan=2 align=center>".$_SESSION['lang']['jumlah']."(Rp)</td>
                </tr>
                </thead>
                <tbody>";
$no=0;
$ttl=0;
$arrOutput='';
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    $stream.="<tr class=rowcontent>
                <td>".$no."</td>
                <td>".$bar->noakun."</td>    
                <td>".$namakegiatan[$bar->noakun]."</td>  
                <td>".$bar->kodeblok."</td>
                <td>".$tahuntanam[$bar->kodeblok]."</td>
                <td align=right>".number_format($pres[$bar->kodeblok][$kegiatan])."</td>
                 <td>".$satuan[$kegiatan]."</td>                     
                <td align=right>".number_format($kg[$bar->kodeblok][$kegiatan])."</td>    
                <td align=right>".number_format($bar->biaya)."</td>
              </tr>";
    $ttl+=$bar->biaya;
    $tths+=$pres[$bar->kodeblok][$kegiatan];
    $ttkg+=$kg[$bar->kodeblok][$kegiatan];
}
$stream.="<tr class=rowcontent>
                <td colspan=5>Total</td>
                <td align=right>".number_format($tths)."</td>
                <td></td>    
                <td align=right>".number_format($ttkg)."</td>    
                <td align=right>".number_format($ttl)."</td>
              </tr>";
$stream.="</tbody><tfoot></tfoot></table>";
 #+++++++++++++++++++++++++++++++++++++++++++++++++            
switch($proses)
{
      case 'preview':
                     
		echo $stream;
            break;
       case 'excel':
$qwe=date("YmdHms");
           $nop_="Laporan Biaya Langsung Kebun ".$kdOrg."_".$kegiatan."_".$qwe;
            if(strlen($stream)>0)
            {
                 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                 gzwrite($gztralala, $stream);
                 gzclose($gztralala);
                 echo "<script language=javascript1.2>
                    window.location='tempExcel/".$nop_.".xls.gz';
                    </script>";
            } 
	    break;
	case'pdf':
//            echo "Format Belum tersedia";

            class PDF extends FPDF
                    {
                        function Header() {
                            global $conn;
                            global $dbname;
                            global $align;
                            global $length;
                            global $colArr;
                            global $title;
                                            global $kdOrg;
                                            global $kdAfd;
                                            global $tgl1;
                                            global $tgl2;
                                            global $where;
                                            global $nmOrg;
                                            global $lok;

                                            $cols=247.5;
                            $query = selectQuery($dbname,'organisasi','alamat,telepon',
                                "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                            $orgData = fetchData($query);

                            $width = $this->w - $this->lMargin - $this->rMargin;
                            $height = 20;
                            if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                            $this->Image($path,$this->lMargin,$this->tMargin,70);	
                            $this->SetFont('Arial','B',9);
                            $this->SetFillColor(255,255,255);	
                            $this->SetX(100);   
                            $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                            $this->SetX(100); 		
                            $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                            $this->SetX(100); 			
                            $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                            $this->Line($this->lMargin,$this->tMargin+($height*3),
                            $this->lMargin+$width,$this->tMargin+($height*3));

                            $this->SetFont('Arial','B',10);

                                            $this->Cell($width,$height,"Laporan Biaya Langsung Kebun (Budget vs Realisasi) ".$kdOrg." ".$kegiatan,'',0,'C');
                                            $this->Ln();
                                            $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." :". tanggalnormal($tgl1)." s.d. ". tanggalnormal($tgl2),'',0,'C');
                                            $this->Ln();
                            $this->SetFont('Arial','B',10);
                            $this->SetFillColor(220,220,220);
                                            $this->Cell(8/100*$width,$height,$_SESSION['lang']['nomor'],1,0,'C',1);		
                                            $this->Cell(12/100*$width,$height,$_SESSION['lang']['noakun'],1,0,'C',1);		
                                            $this->Cell(30/100*$width,$height,$_SESSION['lang']['kegiatan'],1,0,'C',1);		
                                            $this->Cell(15/100*$width,$height,$_SESSION['lang']['blok'],1,0,'C',1);		
                                            $this->Cell(15/100*$width,$height,$_SESSION['lang']['tahuntanam'],1,0,'C',1);		
                                            $this->Cell(15/100*$width,$height,$_SESSION['lang']['jumlah'],1,1,'C',1);		
                       }

                        function Footer()
                        {
                            $this->SetY(-15);
                            $this->SetFont('Arial','I',8);
                            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
                        }
                    }
                    $pdf=new PDF('P','pt','Legal');
                    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                    $height = 13;
                            $pdf->AddPage();
                            $pdf->SetFillColor(255,255,255);
                            $pdf->SetFont('Arial','',10);

$res=mysql_query($str);
$no=0;
$ttl=0;
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    $pdf->Cell(8/100*$width,$height,$no,1,0,'C',1);		
    $pdf->Cell(12/100*$width,$height,$bar->noakun,1,0,'C',1);		
    $pdf->Cell(30/100*$width,$height,$namakegiatan[$bar->noakun],1,0,'L',1);		
    $pdf->Cell(15/100*$width,$height,$bar->kodeblok,1,0,'L',1);		
    $pdf->Cell(15/100*$width,$height,$tahuntanam[$bar->kodeblok],1,0,'C',1);		
    $pdf->Cell(15/100*$width,$height,number_format($bar->biaya),1,1,'R',1);		
    $ttl+=$bar->biaya;
}
    $pdf->Cell(80/100*$width,$height,'Total',1,0,'C',1);		
    $pdf->Cell(15/100*$width,$height,number_format($ttl),1,1,'R',1);		

                    $pdf->Output();
            
	break;
	default:
	break;
}

?>