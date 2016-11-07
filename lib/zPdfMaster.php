<?php
include_once('lib/fpdf.php');
include_once('lib/zLib.php');

class zPdfMaster extends FPDF {
    public $_align;
    public $_length;
    public $_colArr;
    public $_title;
        public $_subTitle;
    public $_noThead;
        public $_kopOnly;
        public $_kodeOrg;
        public $_orgName;
        public $_orgInfo;
        public $_logoOrg;

    function zPdfMaster($ori,$unit,$format) {
        parent::FPDF($ori,$unit,$format);
        $this->_noThead = false;
                $this->_kopOnly = false;
                $this->_subTitle = null;
                $this->_kodeOrg = null;
                $this->_logoOrg = null;
    }

    function Header() {
        global $conn;
        global $dbname;
                global $bulan;
                global $tahun;

                if(!empty($this->_kodeOrg)) {
                        # Alamat & No Telp
                        $query = selectQuery($dbname,'organisasi','namaorganisasi,alamat,telepon',
                                "kodeorganisasi='".$this->_kodeOrg."'");
                        $orgData = fetchData($query);
                        $this->_orgName = $namaOrg = $orgData[0]['namaorganisasi'];
                        $this->_logoOrg=$this->_kodeOrg; 
                } else {
                        # Alamat & No Telp
                        $query = selectQuery($dbname,'organisasi','alamat,telepon',
                                "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                        $orgData = fetchData($query);
                        $this->_orgName = $namaOrg = $_SESSION['org']['namaorganisasi'];
                        $this->_logoOrg=$_SESSION['org']['kodeorganisasi'];
                }
                $this->_orgInfo = $orgData[0];

        $sPeriode="select distinct periode from ".$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
        $qPeriode=mysql_query($sPeriode) or die(mysql_error());
        $rPeriode=mysql_fetch_assoc($qPeriode);

        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 12;
                if(!empty($this->_logoOrg)) {
                        $path='images/'.strtolower($this->_logoOrg).'_logo.jpg';
                } else {
                        $path='images/logo.jpg';
                }
        $this->Image($path,$this->lMargin,$this->tMargin,70);	
        $this->SetFont('Arial','B',9);
        $this->SetFillColor(255,255,255);	
        $this->SetX(100);   
        $this->Cell($width-100,$height,$namaOrg,0,1,'L');	 
        $this->SetX(100); 		
        $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
        $this->SetX(100); 			
        $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
        $this->Line($this->lMargin,$this->tMargin+($height*4),
            $this->lMargin+$width,$this->tMargin+($height*4));
        $this->Ln($height*2);

                if($this->_kopOnly==false) {
                        $this->SetFont('Arial','',8);
                        $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodeorg'],'',0,'L');
                        $this->Cell(5,$height,':','',0,'L');
                        $this->Cell(45/100*$width,$height,$_SESSION['empl']['lokasitugas'],'',0,'L');
//                        $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
//                        $this->Cell(5,$height,':','',0,'L');
//                        $this->Cell(15/100*$width,$height,
//                                //numToMonth($bulan,'I','long')." ".
//                                //$tahun,0,0,'L');
//                         $rPeriode['periode'],0,0,'L');
                        $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                        $this->Cell(5,$height,':','',0,'L');
                        $this->Cell(15/100*$width,$height,date('d-m-Y H:i:s'),'',0,'L');		
                        $this->Ln();

                        $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['user'],'',0,'L');
                        $this->Cell(5,$height,':','',0,'L');
                        $this->Cell(45/100*$width,$height,$_SESSION['standard']['username'],'',1,'L');

                        $this->Ln();
                        $this->SetFont('Arial','U',13);
                        $this->Cell($width,$height,strtoupper($this->_title),0,1,'C');
                        if(!is_null($this->_subTitle)) {
                                $this->SetFont('Arial','',9);
                                $this->Cell($width,$height,strtoupper($this->_subTitle),0,1,'C');
                        }

                        if($this->_noThead==false) {
                                $this->Ln();
                                $this->SetFont('Arial','B',9);	
                                $this->SetFillColor(220,220,220);
                                foreach($this->_colArr as $key=>$head) {
                                        if(isset($_SESSION['lang'][$head])) {
                                                $this->Cell($this->_length[$key]/100*$width,$height,$_SESSION['lang'][$head],1,0,'C',1);
                                        } else {
                                                $this->Cell($this->_length[$key]/100*$width,$height,ucfirst($head),1,0,'C',1);
                                        }
                                }
                                $this->Ln();
                        }
                }
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
    }
    function setAttr1($cTitle,$cAlign,$cLength,$cColArr) {
        $this->_align = $cAlign;
        $this->_length = $cLength;
        $this->_colArr = $cColArr;
        $this->_title = $cTitle;
    }
}
?>