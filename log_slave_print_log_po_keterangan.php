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
		
		$str1="select namaorganisasi,alamat,wilayahkota,telepon from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
        $res1=mysql_query($str1);
		$bar1=mysql_fetch_object($res1);
			$namapt=$bar1->namaorganisasi;
            $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
            $telp=$bar1->telepon;	
		

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
        $this->Cell(60,5,"Tel: ".$telp,0,1,'L');	
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
       /* $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        $this->SetFont('Arial','',6); 	
        //$this->SetY(27);
        $this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s')." ".$nopo,0,1,'L');*/
    }

}

$pdf=new PDF('P','mm','A4');
$pdf->AddPage();
$height=4.5;
// kepada yth
$pdf->SetFont('Arial','',8);	

$iPo="select * from   ".$dbname.".log_poht  where nopo='".$_GET['column']."'";

$nPo=mysql_query($iPo) or die (mysql_error($conn));
$dPo=mysql_fetch_assoc($nPo);

	//$pdf->MultiCell(134,$height, $_SESSION['lang']['keterangan'].":"."\n".$dPo['uraian'],0,0,'J',0);

$pdf->MultiCell(200,$height,$_SESSION['lang']['keterangan'].":   "."\n".$dPo['uraian'],0,'J');


$pdf->Output();
?>
