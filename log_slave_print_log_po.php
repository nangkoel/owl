<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');
include_once('lib/zLib.php');


$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];

//=============

//create Header
class PDF extends FPDF
{
    var $col=0;
    function SetCol($col)
    {
        //Move position to a column

        $this->col=$col;
        $x=4+$col*100;
        $this->SetLeftMargin($x);
        $this->SetX($this->GetX());
    }

    function AcceptPageBreak()
    { 
        if($this->col<1 and !$this->UseFooter) {
            //Go to next column
            $this->SetCol($this->col+1);
            $this->SetY(0);
            return false;
        } else {
            //Go back to first column and issue page break
            $this->SetCol(0);
            return true;
        }
    }
    
    function Header()
    {
        global $conn;
        global $dbname;
        global $userid;
        global $posted;
        global $tanggal;
        global $norek_sup;
        global $npwp_sup;
        global $nm_kary;
        global $nm_pt;
        global $nmSupplier;
        global $almtSupplier;
        global $tlpSupplier;
        global $faxSupplier;
        global $nopo;
        global $tglPo;
        global $kdBank;
        global $an;
        global $optNmkry;
        global $kota;
        global $cp;
        $optNmkry=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');

        $str="select kodeorg,kodesupplier,purchaser,nopo,tanggal from ".$dbname.".log_poht  where nopo='".$_GET['column']."'";
        //echo $str;exit();
        $res=mysql_query($str);
        $bar=mysql_fetch_object($res);
		
		$str1="select namaorganisasi,alamat,wilayahkota,telepon,fax from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
        $res1=mysql_query($str1);
		$bar1=mysql_fetch_object($res1);
			$namapt=$bar1->namaorganisasi;
            $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
            $telp=$bar1->telepon;	
            $fax=$bar1->fax;	
		

        /*//ambil nama pt
        if($bar->kodeorg=='')
        {
            $bar->kodeorg=$_SESSION['org']['kodeorganisasi']; 
        }
        $str1="select namaorganisasi,alamat,wilayahkota,telepon from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
        $res1=mysql_query($str1);
        while($bar1=mysql_fetch_object($res1))
        {
           
            if((substr($bar1->namaorganisasi,0,8)=='KOPERASI')||(substr($bar1->namaorganisasi,0,8)=='koperasi'))
            {
                $scek="select distinct regional from ".$dbname.".bgt_regional_assignment 
                       where kodeunit in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$bar->kodeorg."')";
                //exit("Error:".$scek);
                $qcek=mysql_query($scek) or die(mysql_error($conn));
                $rcek=mysql_fetch_assoc($qcek);
                if($rcek['regional']=='KALTIM')
                {
                    $bar->kodeorg='DPA';
                }
                if($rcek['regional']=='SUMSEL'||$rcek['regional']=='LAMPUNG')
                {
                    $bar->kodeorg='PMO';
                }
                $str1="select namaorganisasi,alamat,wilayahkota,telepon from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
                $res1=mysql_query($str1);
                $bar1=mysql_fetch_object($res1);
                
            }
            $namapt=$bar1->namaorganisasi;
            $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
            $telp=$bar1->telepon;				 
        }*/
		
		
		

		
		
		
		
		 
        $sNpwp="select npwp,alamatnpwp from ".$dbname.".setup_org_npwp where kodeorg='".$bar->kodeorg."'";
        $qNpwp=mysql_query($sNpwp) or die(mysql_error());
        $rNpwp=mysql_fetch_assoc($qNpwp);

        $sql="select * from ".$dbname.".log_5supplier where supplierid='".$bar->kodesupplier."'"; //echo $sql;
        $query=mysql_query($sql) or die(mysql_error());
        $res=mysql_fetch_object($query);

        $sql2="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar->purchaser."'";
        $query2=mysql_query($sql2) or die(mysql_error());
        $res2=mysql_fetch_object($query2);

        $sql3="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
        $query3=mysql_query($sql3) or die(mysql_error());
        $res3=mysql_fetch_object($query3); 

        $norek_sup=$res->rekening;
        $kdBank=$res->bank;
        $npwp_sup=$res->npwp;
        $an=$res->an;   
        $nm_kary=$res2->namakaryawan;
        $nm_pt=$res3->namaorganisasi;
        //data PO
        $nopo=$bar->nopo;
        $tglPo=tanggalnormal($bar->tanggal);
        //data supplier
        $nmSupplier=$res->namasupplier;
        $almtSupplier=$res->alamat;
        $tlpSupplier=$res->telepon;
        $faxSupplier=$res->fax;
        $kota=$res->kota;
        $cp=$res->kontakperson;

        $this->SetMargins(15,10,0);
		
	
		
        if($bar->kodeorg=='HIP')
		{  
			$path='images/hip_logo.jpg'; 
		} else if($bar->kodeorg=='SIL')
		{  
			$path='images/sil_logo.jpg'; 
		} 
		else if($bar->kodeorg=='SIP')
		{	
			$path='images/sip_logo.jpg'; 
		}
        $this->Image($path,15,5,40);	
        $this->SetFont('Arial','B',9);
        $this->SetFillColor(255,255,255);	
        $this->SetX(55);   
        $this->Cell(60,5,$namapt,0,1,'L');	 
        $this->SetX(55); 		
        $this->Cell(60,5,$alamatpt,0,1,'L');	
        $this->SetX(55); 			
        $this->Cell(50,5,"Tel: ".$telp,0,0,'L');	
        $this->Cell(50,5,"Fax: ".$fax,0,1,'L');	
        $this->SetFont('Arial','B',7);
        $this->SetX(55); 			
        $this->Cell(60,5,"NPWP: ".$rNpwp['npwp'],0,1,'L');	
        $this->SetX(55); 			
        $this->Cell(60,5,$_SESSION['lang']['alamat']." NPWP: ".$rNpwp['alamatnpwp'],0,1,'L');	
        $this->SetFont('Arial','B',9);
        $this->Line(15,35,205,35);	
        $this->SetX(140);
		$this->SetFont('Arial','B',12);
        $this->Cell(30,10,"No. PO: ".$nopo,0,1,'L');
		$this->SetFont('Arial','B',9);
            
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        $this->SetFont('Arial','',6); 	
        //$this->SetY(27);
        $this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s')." ".$nopo,0,1,'L');
    }

}

$pdf=new PDF('P','mm','A4');
$pdf->SetAutoPageBreak(true,10);
$pdf->AddPage();
$last=false;
// kepada yth
$pdf->SetFont('Arial','B',8);	
if($_SESSION['language']=='EN'){
    $pdf->Cell(30,4,"TO :",0,0,'L');     
}else{                
    $pdf->Cell(30,4,"KEPADA YTH :",0,0,'L'); 
}

$pdf->Ln();
$arte="";
 
$pdf->Cell(35,4,$_SESSION['lang']['nm_perusahaan'],0,0,'L'); 
$pdf->Cell(2,4,":",0,0,'L');
$pdf->Cell(40,4,$nmSupplier.$arte,0,1,'L'); 	
if($cp!='')
{
    $pdf->Cell(35,4,$_SESSION['lang']['cperson'],0,0,'L'); 
    $pdf->Cell(2,4,":",0,0,'L');
    $pdf->Cell(40,4,$cp,0,1,'L'); 
}
$pdf->Cell(35,4,$_SESSION['lang']['alamat'],0,0,'L'); 
//$pdf->Cell(40,4,": ".$almtSupplier,0,1,'L');
$pdf->Cell(2,4,":",0,0,'L');
$pdf->MultiCell(150,4,$almtSupplier,0,'L',0);
$pdf->Cell(35,4,$_SESSION['lang']['telp'],0,0,'L'); 
$pdf->Cell(2,4,":",0,0,'L');
$pdf->Cell(40,4,$tlpSupplier,0,1,'L'); 
$pdf->Cell(35,4,$_SESSION['lang']['fax'],0,0,'L'); 
$pdf->Cell(2,4,":",0,0,'L');
$pdf->Cell(40,4,$faxSupplier,0,1,'L'); 
$pdf->Cell(35,4,$_SESSION['lang']['namabank'],0,0,'L'); 
$pdf->Cell(2,4,":",0,0,'L');
$pdf->Cell(40,4,$kdBank,0,1,'L'); 
$pdf->Cell(35,4,$_SESSION['lang']['norekeningbank'],0,0,'L'); 
$pdf->Cell(2,4,":",0,0,'L');
$pdf->Cell(40,4,$an." ".$norek_sup,0,1,'L'); 
$pdf->Cell(35,4,$_SESSION['lang']['npwp'],0,0,'L'); 
$pdf->Cell(2,4,":",0,0,'L');
$pdf->Cell(40,4,$npwp_sup,0,1,'L'); 
$pdf->Cell(35,4,$_SESSION['lang']['kota'],0,0,'L'); 
$pdf->Cell(2,4,":",0,0,'L');
$pdf->Cell(40,4,$kota,0,1,'L'); 

//title
$pdf->SetFont('Arial','U',12);
$ar=round($pdf->GetY());
$pdf->SetY($ar+5);
$pdf->Cell(190,5,strtoupper("Purchase Order"),0,1,'C');		
$pdf->SetY($ar+12);

//no po + tanggal po
$pdf->SetFont('Arial','',8);		
$pdf->Cell(10,4,"",0,0,'L'); 
$pdf->Cell(20,4," ",0,0,'L'); 
$pdf->SetX(163);
$pdf->Cell(20,4,$_SESSION['lang']['tanggal'],0,0,'L'); 
$pdf->Cell(20,4,": ".$tglPo,0,0,'L'); 
$pdf->SetY($ar+17);

//title
$height=4.5;
$pdf->SetFont('Arial','B',8);	
$pdf->SetFillColor(220,220,220);
$pdf->Cell(8,$height,'No',1,0,'L',1);
//$pdf->Cell(12,5,$_SESSION['lang']['kodeabs'],1,0,'C',1);	
$pdf->Cell(60,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);
$pdf->Cell(36,$height,$_SESSION['lang']['nopp'],1,0,'C',1);	
//$pdf->Cell(12,5,$_SESSION['lang']['untukunit'],1,0,'C',1);		
$pdf->Cell(14,$height,$_SESSION['lang']['jumlah'],1,0,'C',1);	
$pdf->Cell(14,$height,$_SESSION['lang']['satuan'],1,0,'C',1);	
$pdf->Cell(29,$height,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
$pdf->Cell(26,$height,'Total',1,1,'C',1);

$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',8);
		
$str="select a.*,b.kodesupplier,b.subtotal,b.diskonpersen,b.tanggal,b.nilaidiskon,b.ppn,b.nilaipo,b.tanggalkirim,b.lokasipengiriman,b.uraian,b.matauang from ".$dbname.".log_podt a inner join ".$dbname.".log_poht b on a.nopo=b.nopo  where a.nopo='".$_GET['column']."'";
//echo $str;exit();
$re=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($re))
{
    $no+=1;

    $kodebarang=$bar->kodebarang;
    $jumlah=floatval($bar->jumlahpesan);
    $harga_sat=$bar->hargasbldiskon;
    $total=$jumlah*$harga_sat;
    $unit=substr($bar->nopp,15,4);
    $namabarang='';
    $nopp=$bar->nopp;
	//$nopp=substr($bar->nopp,0,3);
    $strv="select b.spesifikasi from  ".$dbname.".log_5photobarang b  where b.kodebarang='".$bar->kodebarang."'"; //echo $strv;exit();	
    $resv=mysql_query($strv);
    $barv=mysql_fetch_object($resv);

    if($barv->spesifikasi!='')
    {
        $spek=$barv->spesifikasi."\n";
    }
    else
    {
        $spek="";
    }

    $sSat="select satuan,namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar->kodebarang."'";
    $qSat=mysql_query($sSat) or die(mysql_error());
    $rSat=mysql_fetch_assoc($qSat);
    $satuan=$rSat['satuan'];
    $namabarang=$rSat['namabarang'];

    $i++;

	/*$flag=false;
	if(strlen($no)==2)
	{
		if(substr($no,1,1)==1)
		{$akhirY=$pdf->GetY();
                echo $akhirY.__.$no._;
			$flag=true;
		}
	}*/

    //diganti 30/06/2014
    //akhir y untuk new page dinaikan kembali agar penandatangan ikut
    //kalau penanda tangan di bawah maka barangnya harus kebawah juga minimal 1 barang
    //ambil akhir y aja

   if($no!=1)
    {
        $pdf->SetY($akhirY);
    }
    $akhirY=$pdf->GetY();
    if($akhirY>=190){
        $pdf->AddPage();
       //$akhirY=$pdf->GetY();
        $akhirY=0;
    }    
    //no
    $pdf->Cell(8,$height,$no,0,0,'L',0);
    $pdf->SetX($pdf->GetX());
    $posisiY=round($pdf->GetY());
    
   // $pdf->Cell(12,5,substr($bar->kodebarang,0,3),0,0,'C',0);
    //nama barang
    $pdf->MultiCell(60,$height," ".$namabarang."\n".$spek.$bar->catatan,0,'L',0);
//    $pdf->SetX($pdf->GetX()+8);
//    $pdf->MultiCell(400,$height," ".$bar->catatan,0,'L',0);
    $akhirY=$pdf->GetY();

    //naik lagi kursornya
    $pdf->SetY($posisiY);
    $pdf->SetX($pdf->GetX()+70);

    //no pp + pt + jumlah + satuan + harga + total
    $pdf->Cell(36,$height,$nopp,0,0,'L',0);
   // $pdf->Cell(12,5,$unit,0,0,'C',0);
    $pdf->Cell(14,$height,number_format($jumlah,2,'.',','),0,0,'C',0);
    $pdf->Cell(14,$height,$bar->satuan,0,0,'C',0);
    $pdf->Cell(29,$height,$bar->matauang." ".number_format($harga_sat,2,'.',','),0,0,'R',0);	
    $desimal=2;

    $pdf->Cell(26,$height,number_format($total,2,'.',','),0,1,'R',0);
////    if($i==15)
//    {
//        $i=0;
//        $akhirY=$akhirY-20;
//        $akhirY=$pdf->GetY()-$akhirY;
//        $akhirY=$akhirY+35;
//        //$pdf->SetY($posisiY+25);
////        $pdf->AddPage();
//    }
}
$akhirSubtot=$pdf->GetY();
$pdf->SetY($akhirY);
$slopoht="select * from ".$dbname.".log_poht where nopo='".$_GET['column']."'";
$qlopoht=mysql_query($slopoht) or die(mysql_error());
$rlopoht=mysql_fetch_object($qlopoht);
$sb_tot=$rlopoht->subtotal;
$nil_diskon=$rlopoht->nilaidiskon;
$nppn=$rlopoht->ppn;
$stat_release=$rlopoht->stat_release ;
$user_release=$rlopoht->useridreleasae;
$gr_total=$rlopoht->nilaipo;
                	
if($akhirSubtot>=240){
    $pdf->AddPage();
    $akhirY=$pdf->GetY();
}
$akhirlistBarang=$pdf->GetY();



//echo $akhirKet;
//$akhirKet=$pdf->;

//echo $akhirY;
$pdf->SetY($akhirlistBarang);
$pdf->SetX($pdf->GetX()+134);
$pdf->Cell(29,$height,$_SESSION['lang']['subtotal'],'T',0,'L',1);	
$pdf->Cell(26,$height,number_format($rlopoht->subtotal,2,'.',','),'T',1,'R',1);


$pdf->SetY($pdf->GetY());
$pdf->SetX($pdf->GetX()+134);
$pdf->Cell(29,$height,'Discount'." (".$rlopoht->diskonpersen."% )",0,0,'L',1);	
$pdf->Cell(26,$height,number_format($rlopoht->nilaidiskon,$desimal,'.',','),0,1,'R',1);
$pdf->SetY($pdf->GetY());
$pdf->SetX($pdf->GetX()+134);
$pdf->Cell(29,$height,'PPh/PPn (10 %)',0,0,'L',1);	
$pdf->Cell(26,$height,number_format($rlopoht->ppn,$desimal,'.',','),0,1,'R',1);

$pdf->SetY($pdf->GetY());
$pdf->SetX($pdf->GetX()+134);
$pdf->Cell(29,$height,'Ongkos Kirim','',0,'L',1);	
$pdf->Cell(26,$height,number_format($rlopoht->ongkosangkutan,$desimal,'.',','),'',1,'R',1);


$persenPpnOngkir=$rlopoht->ongkirimppn/$rlopoht->ongkosangkutan*100;
$pdf->SetY($pdf->GetY());
$pdf->SetX($pdf->GetX()+134);
$pdf->Cell(35,$height,'Ppn Ongkos Kirim ('.round($persenPpnOngkir,2).'%)','',0,'L',1);	
$pdf->Cell(20,$height,number_format($rlopoht->ongkirimppn,$desimal,'.',','),'',1,'R',1);


$pdf->SetY($pdf->GetY());
$pdf->SetX($pdf->GetX()+134);
$pdf->Cell(29,$height,'Misc','',0,'L',1);	
$pdf->Cell(26,$height,number_format($rlopoht->misc,$desimal,'.',','),'',1,'R',1);

if ($rlopoht->miscppn>0){
$pdf->SetY($pdf->GetY());
$pdf->SetX($pdf->GetX()+134);
$pdf->Cell(29,$height,'Misc Ppn (10 %)','',0,'L',1);	
$pdf->Cell(26,$height,number_format($rlopoht->miscppn,$desimal,'.',','),'',1,'R',1);
}

$pdf->SetFont('Arial','B',8);
$pdf->SetY($pdf->GetY());
$pdf->SetX($pdf->GetX()+134);

$pdf->Cell(29,$height,$_SESSION['lang']['grnd_total'],0,0,'L',1);	
$pdf->Cell(26,$height,$rlopoht->matauang." ".number_format($gr_total,$desimal,'.',','),0,1,'R',1);	
$akhirlistGrantot=$pdf->GetY();
$pdf->SetY($akhirlistGrantot);



$pdf->SetY($akhirlistBarang);
$pdf->MultiCell(134,$height, $_SESSION['lang']['keterangan'].":"."\n".$rlopoht->uraian,'T',1,'J',0);
//$akhirKet=$pdf->SetY($akhirY);

$akhirKet=$pdf->GetY();



//echo $akhirlistGrantot.__.$akhirKet;

//echo $akhirKet;



if($akhirKet<=180)
{
	$akhirKet=$akhirKet+40;
}//echo $akhirKet;
//akhirKet
//if(strlen($rlopoht->uraian)>616)
if($akhirKet<=210)
{
	
    $tmbhBrs=5;                // jul 12, 2013 diganti 80 -> 70
    //$tmbhBrs2=5;              // jul 12, 2013 diganti 105 -> 95
    $tmbhBrs3=40;               // jul 12, 2013 diganti 75 -> 65
    //$tmbhBrs5=125;              // jul 12, 2013 diganti 135 -> 125
}
else
{
    $tmbhBrs=35;                // jul 12, 2013 diganti 45 -> 35
    $tmbhBrs2=55;               // jul 12, 2013 diganti 65 -> 55
    $tmbhBrs3=45;               // jul 12, 2013 diganti 55 -> 45
    $tmbhBrs5=85;               // jul 12, 2013 diganti 95 -> 85
}
# kalo terlalu ke bawah, pindah halaman aja    


//echo $akhirKet;
//echo $akhirKet+$tmbhBrs3;
            
if(($akhirKet)>=240){   // tadinya if($akhirY)>=175)
    $akhirY=0;                  // jul 12, 2013 diganti lagi 250 -> 300
    //exit("Error:MASYK");
	$pdf->AddPage();
	$akhirKet=20;
	$tmbhBrs=10;
}
# $dz



$akhirYKursAwal=$pdf->GetY();
$akhirYKurs=$pdf->GetY();


$selisihY=$akhirlistGrantot-$akhirYKurs;

//echo $selisihY;

//echo $selisihY._.$akhirlistGrantot._.$akhirYKurs;
if($selisihY>10 && $selisihY<80)
{
	$akhirYKurs=$akhirlistGrantot;
}


$page=$pdf->PageNo();
//echo $a;

//10-70
//80++
//echo $selisihY;

//echo $akhirlistGrantot.__.$akhirKet;

if($page>1)
{
	//echo masuk;
	if($selisihY<=31.5)
	{
		$akhirYKurs=$akhirlistGrantot;
	}
	else
	{
		$akhirYKurs=$akhirYKursAwal;
	}
}



$pdf->SetY($akhirYKurs+10);
//$pdf->SetY($akhirKet+$tmbhBrs);
$pdf->SetFont('Arial','',8);
$pdf->Cell(30,4,$_SESSION['lang']['kurs'],0,0,'L'); 
$pdf->Cell(60,4,":  ".$rlopoht->kurs,0,0,'L');

$pdf->Cell(50,4,'AGREED BY SUPPLIER',0,0,'C'); 
$pdf->Cell(50,4,$nm_pt,0,1,'C'); 

$pdf->Cell(30,4,$_SESSION['lang']['syaratPem'],0,0,'L'); 
$pdf->Cell(40,4,":  ".$rlopoht->statusbayar." : ".$rlopoht->syaratbayar,0,1,'L'); 

$pdf->Cell(30,4,$_SESSION['lang']['tgl_kirim'],0,0,'L'); 
$pdf->Cell(40,4,": ".tanggalnormald($rlopoht->tanggalkirim),0,1,'L'); 		

if((is_null($rlopoht->idFranco))||($rlopoht->idFranco=='')||($rlopoht->idFranco==0))
{
    $pdf->Cell(30,4,$_SESSION['lang']['almt_kirim'],0,0,'L'); 
    $pdf->Cell(40,4,": ".$rlopoht->lokasipengiriman,0,1,'L'); 		
}
else
{
    $sFr="select * from ".$dbname.".setup_franco where id_franco='".$rlopoht->idFranco."'";
    $qFr=mysql_query($sFr) or die(mysql_error());
    $rFr=mysql_fetch_assoc($qFr);
    $pdf->Cell(30,4,$_SESSION['lang']['almt_kirim'],0,0,'L'); 
    $pdf->Cell(40,4,": ".$rFr['alamat'],0,1,'L'); 		
    $pdf->Cell(30,4,"Kontak Person",0,0,'L'); 
    $pdf->Cell(40,4,": ".$rFr['contact'],0,1,'L'); 	
    $pdf->Cell(30,4,"Telp / Handphone No.",0,0,'L'); 
    $pdf->Cell(40,4,": ".$rFr['handphone'],0,1,'L'); 	
}


$sPo="select persetujuan1,updateby,purchaser from ".$dbname.".log_poht where nopo='".$nopo."'";
$qPo=mysql_query($sPo) or die(mysql_error($conn));
$rPo=mysql_fetch_assoc($qPo);
$pdf->SetFont('Arial','',8);

$akhirYTtd=$pdf->GetY();
$pdf->setY($akhirYTtd);
$pdf->Ln(10);
$pdf->Cell(95,4,strtoupper($_SESSION['lang']['purchaser']).": ".strtoupper($optNmkry[$rPo['purchaser']]),0,0,'L',0);

$pdf->Cell(5,4,'','',0,'C',0);
$pdf->Cell(35,4,'','T',0,'C',0);
$pdf->Cell(5,4,'','',0,'C',0);

$pdf->Cell(5,4,'','',0,'C',0);
$pdf->Cell(40,4,strtoupper($optNmkry[$rPo['persetujuan1']]),'T',0,'C',0);
$pdf->Cell(5,4,'','',1,'C',0);
$pdf->SetFont('Arial','I',7);
$pdf->Cell(10,3,$_SESSION['lang']['fyiGudang'],0,0,'L',0);

//Terms and conditions
$last=true;
$height=2;
$colwidth=104;
$pdf->SetAutoPageBreak(true,0);
$pdf->UseHeader=false;
$pdf->UseFooter=false;
$pdf->AddPage();
$pdf->SetY(0);
$pdf->SetLeftMargin(0);
$pdf->SetFont('Arial','B',7);
$pdf->Cell($colwidth-20,$height+4,'Terms and Conditions of Purchase Order',0,1,'C',0);
$pdf->SetFont('Arial','',6);
$pdf->MultiCell($colwidth,$height,'Unless otherwise specified hereinafter, the aforementioned Purchase Order is subject to the following terms and conditions :',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'1.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Fixed Price',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'The prices stated in the Purchase Order are fixed and no escalation until the order is completed. The price of the goods is already including all kind of taxes which may be imposed by the resident country of Seller.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'2.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Delivery Term',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Delivery term shall be governed and construed in accordance with provisions of "Incoterms" (latest editions) and any amendments there to and also a per agreement between Seller and Purchaser as stated in the Purchase Order.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'3.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Delivery Time',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'a.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Confirmed delivery schedules for goods delivery have to be provided by the Seller to the Buyer before the payment.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'b.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Delivery time shall be informed by the Seller at the latest one (1) working day to the Buyer before the goods being delivered to the specified place by the buyer.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'c.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'In the event delivery cannot be carried out in accordance with the Purchase Order:',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'i.',0,0,'L',0);
$pdf->MultiCell($colwidth-9,$height,'The Seller should notify in writing to the Buyer at least 10 working days before the delivery date as specified in Purchase Order if the specified delivery date cannot be met.',0,'J',0);
$pdf->Ln(2);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'ii.',0,0,'L',0);
$pdf->MultiCell($colwidth-9,$height,'If there are any changes related the Purchase Order, the Seller must obtain approval from the Buyer before delivery.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'iii.',0,0,'L',0);
$pdf->MultiCell($colwidth-9,$height,'In the event that any delivery should be delayed, and such delays are not caused by reason originating from the Buyer or Force Majeure, the Seller shall pay a fine to the Buyer 0,2% of the Purchase Order value, for each day of late delivery, up to a maximum of 10% of the Purchase Order value. If the liquidated damages stated above exceed 10% of the Purchase Order value, the Buyer may cancel the Purchase Order. In such event the Seller is obliged to return all payment received from the Buyer or the corresponding portion, and should the Buyer re-purchase the order from other sources, any increased cost that will occur from the new transaction shall also be charged to the Seller.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'iv.',0,0,'L',0);
$pdf->MultiCell($colwidth-9,$height,'If the goods failed to meet the requirements the latest Incoeterm agreed and rescheduled delivery date  pursuant to clause 3 b above, the Buyer have right to terminate Purchase Order. The Seller andthe Buyer (“hereinafter referred to as the “the Parties”) hereby expressly waive the provisions of requires all court decisions required for termination of Purchase Order under the rules of each Seller andBuyer.',0,'J',0);
$pdf->Ln(4);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'4.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Delivery Order',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'a.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'The Seller has to provide Delivery Order when delivered the goods to Buyer\'s premises. For chemical products need to attach Copy of Material Safety Data Sheet (MSDS).',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'b.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Delivery Order has to stated clearly detailed description of material, quantity of material, Purchase Order number, and number of delivery (for partial delivery).',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'c.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Partial deliveries are not allowed without prior written consent from the Buyer.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'d.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Goods covered by the Purchase Order shall be packed by the Seller in accordance with generally accepted industry standard. The Seller shall use commercially means to ensure each package is in point of sale condition acceptable to the Buyer upon arrival at the specified place by the buyer.',0,'J',0);
$pdf->Ln(4);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'5.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Purchase Order',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'The Purchase Order number (example: 001/01/2014/PO/M/H0RO/HIP) has to be stated for all related correspondence, delivery documents, delivery marks to enable the Buyer arrange on time payment.',0,'J',0);
$pdf->Ln(4);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'6.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Guarantee',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'a.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'The Seller guarantees that the goods furnished hereunder shall conform to the specification stated in the Purchase Order, be genuine, made by the original authorized manufacturer, not violate any intellectual property rights and or/not come from illegal source or unlawful ownership, and/or not in the conflict with third party. In case, the goods do not meet such conditions, then the Seller shall change the goods whereas can be an amount of money that match with the value of the goods.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'b.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'In additional, the Seller shall be responsible and hold the Buyer harmless from any losses and damages arising out of any claims or lawsuit by any third party for intellectual right infringement and/or ownership of the goods.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'c.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'The Seller guarantees that the goods are delivered to the Buyer hereunder shall conform to the specification stated in the Purchase Order, be brand-new, be in good quality and condition, not expired, and free of all defects. If there is any defect(s), non-conformity, or shortage, the Buyer shall notify the Seller for a period of at most 7 (seven) working days from the date of receipt of the goods by the Buyer.  The Seller shall, within the next 7 (seven) working days from the date of notify from Buyer, inspect the rejected good(s). Should the Seller fail to inspect the rejected good(s) within the above time limit, the Seller is considered to have accepted the Buyer\'s claim as final result. The Seller shall, by the latest 30 (thirty) days as of notification or the agreed period by the Buyer, refurnish or replace the rejected good(s) with new ones or make up the shortage, failing which shall entitle the Buyer to demand for liquidated damages. All extra cost incurred thereof due to such defect(s), non-conformity, or shortage will be on the Seller\'s account.',0,'J',0);
$pdf->Ln(8);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'7.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Property and risk in the goods',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Property and risk in the goods shall remain with supplier until they are delivered at the point specified in the Purchase Order and a Delivery Order is signed by the Buyer.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'After the goods received by the Buyer, shall not affect the right of the Buyer to reject the goods and claim guarantee pursuant to the provision of the Purchase Order and the terms and condition hereunder.',0,'J',0);
$pdf->Ln(4);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'8.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'ForceMajeur',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'a.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Force Majeure means in case of there is  any cause beyond the Parties reasonable control, including but not limited to, fire, explosion, flood, or acts of God’s or the public enemy , regulations, or laws of any government, war or civil commotion , terrorist activity, strike, lock-out, or labor disturbances, or failure of public utilities or common carriers.',0,'J',0);
$pdf->Ln(2);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'b.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'In the occurrence of the force majeure, then party who suffering shall notify another party with prior written notice in 7 (seven) calendar days as of the occurrence the force majeure. Party who is default these stipulations shall have no right to invoke force majeure.',0,'J',0);
$pdf->Ln(2);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'c.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Neither party shall be liable for any failure to fulfill any term of the Purchase Order which iscaused by force majeure.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'d.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'In the occurrence of the force majeure, the  Seller and the Buyer will discuss to sustainability of the Purchase Order.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'9.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Invoice Conditions',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'a.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Invoice has to stated Purchase Order number, name of Buyer as per Purchase Order and shall described goods detail, delivery terms, currency and unit price same as the one stated in Purchase Order .',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'b.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Payment will be made by Buyer in accordance with Terms and Conditions as stated in Purchase Order.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'10.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Entire Agreement',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'The Terms and Conditions attached to the Purchase Order together with any amendments, constitutes the entire agreement between the Seller and the Buyer relating to Purchase Order superseding all prior agreements, oral or written. This Agreement shall be binding upon and shall inure to the benefit of Seller and Buyer, and their respective successors and permitted assignees.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'11.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Representations and Warranties',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'The Parties represent and warrant that they have full power and authority to enter into this transaction and to comply with all obligations hereunder, and all governmental, corporate and other approvals and action appropriate or necessary to authorize the execution, delivery and performance of the Purchase Order by the Parties have been obtained and/or taken.',0,'J',0);
$pdf->Ln(4);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'12.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Special Conditions',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Where special conditions are stated in the Purchase Order, those conditions shall apply equally with the general terms and conditions shown herein except there is any inconsistency between the general and special conditions, the special conditions shall apply.',0,'J',0);
$pdf->Ln(4);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'13.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Language',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'The Purchase Order, the terms and conditions made in bilingual form (English language and Indonesian language). In the event of inconsistency between the two versions, the Indonesian version shall control in all respects.',0,'J',0);
$pdf->Ln(4);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'14.',0,0,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->MultiCell($colwidth-3,$height,'This Terms and Conditions shall be effective when the Purchase Order is signed and/or agreed in its entirety by the Seller and shall continue to be effective in relation to the said goods, and shall be governed by laws of the Republic of Indonesia.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'15.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Dispute Settlement',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'In the event a dispute arises between the Buyer and the Seller in relation to any matter arising out of or in connection with the Purchase Order, the  Parties agree to settled through the Central Jakarta District Court.',0,'J',0);
$pdf->Ln(1);
$pdf->MultiCell($colwidth,$height,'The Terms and Conditions herein which is incorporated and constituted as an integral part of this Purchase Order.',0,'J',0);


//if($pdf->GetY()>350 and $pdf->col<1 and $last==true)
//        $pdf->AcceptPageBreak();
//if ($pdf->GetY()>350 and $pdf->col>0)
//   {
//        $r=350-$pdf->GetY();
//        $pdf->Cell(80,$r,'',0,1,'L');
//   }


$pdf->SetFont('Arial','B',7);
//$pdf->SetX($pdf->GetX()+80);
$pdf->Cell(105,$height+4,'Ketentuan dan Persyaratan dari Pemesanan Pembelian ("Purchase Order")',0,1,'C',0);
$pdf->SetFont('Arial','',6);
$pdf->MultiCell($colwidth,$height,'Kecuali dinyatakan lain untuk selanjutnya, Purchase Order tersebut tunduk pada ketentuan dan persyaratan sebagai  berikut :',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'1.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Harga Tetap',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Harga yang tercantum dalam Purchase Order adalah tetap dan tidak ada kenaikan sampai dengan pemesanan telah diselesaikan. Harga barang sudah termasuk semua jenis pajak yang dapat dikenakan oleh negara dimana Penjual berkedudukan.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'2.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Ketentuan Pengiriman',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Ketentuan Pengiriman harus diatur dan ditafsirkan sesuai dengan ketentuan-ketentuan " Incoterms "   ( edisi terbaru ) dan setiap perubahan-perubahannya dan juga sesuai dengan kesepakatan antara Penjual dan Pembeli yang tertera didalam Purchase Order.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'3.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Waktu Pengiriman',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'a.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Jadwal konfirmasi pengiriman barang harus diberikan oleh Penjual kepada Pembeli sebelum pembayaran.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'b.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Waktu pengiriman harus diberitahukan oleh Penjual kepada Pembeli paling lambat 1 ( satu) hari kerja sebelum barang dikirimkan ke tempat yang ditentukan oleh Pembeli.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'c.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Dalam hal Pengiriman bila tidak dapat dilaksanakan sesuai dengan Purchase Order:',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'i.',0,0,'L',0);
$pdf->MultiCell($colwidth-9,$height,'Penjual harus memberitahukan secara tertulis kepada Pembeli paling lambat  10 hari kerja sebelum tanggal pengiriman sebagaimana ditentukan dalam Purchase Order, jika tanggal pengiriman yang ditentukan tidak dapat dipenuhi.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'ii.',0,0,'L',0);
$pdf->MultiCell($colwidth-9,$height,'Jika terdapat perubahan apapun terkait dengan Purchase Order, Penjual harus mendapatkan persetujuan dari Pembeli terlebih dahulu sebelum dilakukan pengiriman.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'iii.',0,0,'L',0);
$pdf->MultiCell($colwidth-9,$height,'Dalam hal pengiriman apapun harus ditunda, dan penundaan tersebut tidak disebabkan oleh alasan yang berasal dari Pembeli atau Kejadian Kahar, Penjual harus membayar denda kepada Pembeli 0,2 % dari nilai Purchase Order, untuk setiap hari keterlambatan, sampai maksimal 10 % dari nilai Purchase Order. Jika kerugian tersebut di atas melebihi 10 % dari nilai Purchase Order, Pembeli dapat membatalkan Purchase Order. Dalam hal demikian Penjual wajib mengembalikan semua pembayaran yang diterima dari Pembeli atau bagian yang sesuai, dan jika Pembeli membeli dari sumber lain, setiap peningkatan biaya yang akan terjadi dari transaksi yang baru juga harus dibebankan kepada Penjual.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'iv.',0,0,'L',0);
$pdf->MultiCell($colwidth-9,$height,'Jika barang tidak memenuhi persyaratan Incoterm yang terakhir yang disepakati dan penjadwalan ulang tanggal pengiriman berdasarkan  ayat 3 b di atas, Pembeli memiliki hak untuk mengakhiri Purchase Order. Penjual dan Pembeli (keduanya selanjutnya disebut “Para Pihak”) dengan ini secara tegas mengesampingkan ketentuan yang mensyaratkan semua keputusan pengadilan yang diperlukan untuk pengakhiran Purchase Order berdasarkan peraturan yang berlaku bagi masing-masing Pembeli dan Penjual.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'4.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Instruksi Pengiriman ("Delivery Order")',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'a.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Penjual harus memberikan Perintah Pengiriman ("Delivery Order") saat mengirim barang ke tempat Pembeli. Untuk produk kimia perlu melampirkan Copy Material Safety Data Sheet (MSDS).',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'b.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Delivery Order harus menyatakan dengan jelas rincian keterangan bahan, kuantitas bahan, jumlah Purchase Order, dan jumlah pengiriman (untuk pengiriman sebagian).',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'c.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Pengiriman sebagian tidak diperbolehkan tanpa persetujuan tertulis terlebih dahulu dari Pembeli.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'d.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Barang yang tercantum dalam Purchase Order harus dikemas oleh Penjual sesuai dengan standar industri yang berlaku umum. Penjual harus memastikan bahwa setiap kemasan dalam kondisi penjualan yang dapat diterima oleh Pembeli pada saat Barang sampai di tempat yang ditentukan Pembeli.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'5.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Purchase Order',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Nomor  Purchase Order ( contoh: 001/01/2014/PO/M/H0RO/HIP ) harus dicantumkan untuk semua korespondensi terkait, dokumen-dokumen pengiriman, tanda pengiriman  untuk memungkinkan Pembeli  menyiapkan pembayaran tepat waktu.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'6.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Jaminan',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'a.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Penjual menjamin bahwa barang-barang yang disediakan ini harus sesuai dengan spesifikasi yang tercantum dalam Purchase Order, harus asli, dibuat oleh produsen asli yang sah, tidak melanggar hak kekayaan intelektual dan/atau tidak berasal dari sumber ilegal atau melanggar hukum kepemilikan , dan/atau tidak dalam sengketa dengan pihak ketiga . Jika, ada barang yang tidak memenuhi kondisi tersebut, maka Penjual harus bisa memberikan penggantian dimana hal ini dapat berupa sejumlah uang yang setara dengan nilai barang tersebut.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'b.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Selain itu, Penjual harus bertanggung jawab dan menjamin Pembeli dari setiap kehilangan dan kerugian yang timbul dari setiap klaim atau tuntutan hukum oleh pihak ketiga atas pelanggaran hak kekayaan intelektual dan/atau atas kepemilikan barang.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'c.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Penjual menjamin bahwa barang yang diserahkan kepada Pembeli harus memenuhi spesifikasi yang tercantum dalam Purchase Order, harus baru , berada dalam kualitas dan kondisi yang baik, belum kadaluarsa, dan bebas dari segala cacat. Jika ada cacat, ketidaksesuaian , atau kekurangan, Pembeli harus memberitahukan kepada Penjual dalam jangka waktu paling lama 7 (tujuh) hari kerja sejak tanggal diterimanya Barang oleh Pembeli. Penjual dalam waktu 7 ( tujuh ) hari kerja sejak tanggal penerimaan pemberitahuan dari Pembeli, harus memeriksa barang yang ditolak tersebut. Jika Penjual gagal untuk memeriksa barang yang ditolak dalam batas waktu di atas, Penjual dianggap telah menerima klaim Pembeli sebagai hasil akhir. Penjual dalam kurun waktu paling lambat 30 ( tiga puluh ) hari sejak pemberitahuan atau periode yang disepakati oleh Pembeli, menyediakan kembali atau mengganti barang yang ditolak dengan yang baru atau memperbaiki kekurangannya, gagal untuk memenuhi hal tersebut akan memberi Pembeli hak untuk menuntut penggantian kerugian. Semua biaya tambahan yang timbul daripadanya karena cacat tersebut, ketidaksesuaian, atau kekurangan akan ditanggung oleh Penjual.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'7.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Kepemilikan dan Risiko atas Barang',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Kepemilikan dan risiko atas barang akan tetap berada pada Penjual sampai barang dikirim pada tempat yang ditentukan dalam Purchase Order dan Delivery Order telah ditandatangani oleh Pembeli.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Setelah barang diterima oleh Pembeli, tidak akan mempengaruhi hak dari Pembeli untuk menolak barang dan mengajukan tuntutan jaminan berdasarkan ketentuan Purchase Order dan ketentuan dan persyaratan ini.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'8.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Keadaan Kahar',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'a.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Keadaan Kahar yaitu apabila terjadi hal-hal yang disebabkan oleh suatu keadaan diluar kendali Para Pihak, termasuk namun tidak terbatas pada, kebakaran, ledakan, banjir,atau bencana alam atau kerusuhan, peraturan, atau hukum dari pemerintah, perang atau huru-hara, kegiatan teroris, pemogokan, penutupan perusahaan, atau kerusuhan tenaga kerja, atau kegagalan fasilitas umum atau angkutan umum.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'b.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Apabila terjadi Keadaan Kahar, maka pihak yang terkena keadaan kahar harus memberitahukan segera pada  pihak lainnya dengan pemberitahuan tertulis selambat-lambatnya dalam waktu 7 (tujuh) hari kalender sejak terjadinya Keadaan Kahar tersebut. Suatu pihak yang lalai atas ketentuan tersebut maka tidak berhak untuk memohon Keadaan Kahar.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'c.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Tidak ada pihak yang bertanggungjawab atas setiap kegagalan untuk memenuhi ketentuan Purchase Order yang disebabkan oleh Keadaan Kahar.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'d.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Dalam hal terjadi Keadaan Kahar, Penjual dan Pembeli akan mendiskusikan mengenai keberlansungan Purchase Order.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'9.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Ketentuan Tagihan ("Invoice")',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'a.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'a.	Invoice harus mencantumkan nomor Purchase Order,nama Pembeli sesuai Purchase Order dan harus menguraikan rincian barang, persyaratan pengiriman , mata uang dan harga satuan yang sama seperti yang tercantum dalam Purchase Order.',0,'J',0);
$pdf->Cell(3,$height,'',0,0,'L',0); $pdf->Cell(3,$height,'b.',0,0,'L',0);
$pdf->MultiCell($colwidth-6,$height,'Pembayaran akan dilakukan oleh Pembeli sesuai dengan Syarat dan Ketentuan sebagaimana tercantum dalam Purchase Order.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'10.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Keseluruhan Perjanjian',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Ketentuan dan Persyaratan yang terlampirpada Purchase Order bersama-sama dengan setiap perubahannyamerupakan seluruh Perjanjian antara Penjual dan Pembeli berkaitan dengan Purchase Order, menggantikan semua perjanjian sebelumnya, lisan atau tertulis. Perjanjian ini mengikat dan berlaku untuk manfaat Penjual dan Pembeli,  dan masing-masing penerus mereka.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'11.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Pernyataan dan Jaminan',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Para Pihak menyatakan dan menjamin bahwa mereka memiliki kekuasaan dan wewenang penuh untuk mengadakan transaksi ini dan untuk memenuhi semua kewajiban ini, dan semua persetujuan pemerintah, perusahaan dan persetujuan lainnya dan tindakan yang sesuai atau diperlukan untuk mengizinkan pelaksanaan, pengiriman dan kinerja Purchase Order telah diperoleh dan didapat oleh Para Pihak.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'12.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Persyaratan Khusus',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Dimana persyaratan-persyaratan khusus yang dinyatakan dalam Purchase Order, persyaratan-persyaratan tersebut berlaku sama dengan ketentuan dan persyaratan umum yang ditunjukkan di sini , kecuali  terjadi ketidaksesuaian antara persyaratan umum dan persyaratan khusus, maka persyaratan khusus yang berlaku.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'13.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Bahasa',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Purchase Order, Ketentuan dan Persyaratan ini dibuat dalam dua bahasa (bahasa Inggris dan bahasa Indonesia). Dalam hal ketidaksesuaian antara kedua versi, versi Bahasa Indonesia akan mengatur dalam segala hal. Jika terdapat perbedaan pengertian atau penafsiran, versi bahasa Indonesia yang akan berlaku.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'14.',0,0,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->MultiCell($colwidth-3,$height,'Syarat dan Ketentuan akan efektif ketika Purchase Order telah ditandatangani dan/atau disepakati secara keseluruhan oleh Penjual dan akan terus menjadi efektif dalam kaitannya dengan barang tersebut, dan akan tunduk pada hukum Republik Indonesia.',0,'J',0);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(3,$height,'15.',0,0,'L',0); $pdf->Cell($colwidth-3,$height,'Penyelesaian Sengketa',0,1,'L',0);
$pdf->SetFont('Arial','',6);
$pdf->Cell(3,$height,'',0,0,'L',0);
$pdf->MultiCell($colwidth-3,$height,'Dalam hal timbul sengketa antara Pembeli dan Penjual dalam kaitannya dengan setiap masalah yang timbul dari atau sehubungan dengan Purchase Order, Para Pihak setuju untuk menyelesaikan melalui Pengadilan Negeri Jakarta Pusat.',0,'J',0);
$pdf->Ln(1);
$pdf->MultiCell($colwidth,$height,'Ketentuan dan Persyaratan ini merupakan satu kesatuan dan merupakan bagian yang tidak terpisahkan dari Purchase Order.',0,'J',0);



$pdf->Output();
?>
