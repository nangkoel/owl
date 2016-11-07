<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');

$val=trim($_GET['string']);
$pt=substr($_SESSION['empl']['lokasitugas'],0,4);
$str="select * from ".$dbname.".sdm_ho_component
      where name like '%Angs%'";
$res=mysql_query($str,$conn);
$arr=Array();
$opt='';
while($bar=mysql_fetch_object($res))
{
        $arr[$bar->id]=$bar->name;
}
$valstat=$val;
switch ($val){
        case 'lunas':
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                      and `end`< '".date('Y-m')."'
                                          order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                      and `end`< '".date('Y-m')."'
                                          order by namakaryawan";		
                        }				
        break;
        case 'blmlunas':
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                      and `end`> '".date('Y-m')."'
                                          order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                      and `end`> '".date('Y-m')."'
                                          order by namakaryawan";		
                        }
        break;
        case 'active':
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                      and `active`=1
                                          order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                      and `active`=1
                                          order by namakaryawan";		
                        }
        break;
        case 'notactive':
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                      and `active`=0
                                          order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                      and `active`=0
                                          order by namakaryawan";		
                        }
        break;
        case '':
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                      order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                      order by namakaryawan";		
                        }
                break;	
        default:
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                          and (`start`<='".$val."' AND `end`>='".$val."')
                                          order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid 
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                          and (`start`<='".$val."' AND `end`>='".$val."')
                                          order by namakaryawan";		
                        }			  					  					  			  
}	

//=================================================
class PDF extends FPDF {
    function Header() {
           global $pt;
           global $valstat;
        $this->SetFont('Arial','B',8); 
                $this->Cell(20,5,'','',1,'L');
        $this->SetFont('Arial','B',12);
                $this->Cell(190,5,strtoupper($_SESSION['lang']['laporanangsuran']),0,1,'C');
        $this->SetFont('Arial','',8);

                        $this->Cell(35,5,'','',0,'L');
                        $this->Cell(2,5,'','',0,'L');
                        $this->Cell(100,5,'','',0,'L');		
                        $this->Cell(15,5,$_SESSION['lang']['tanggal'],'',0,'L');
                        $this->Cell(2,5,':','',0,'L');
                        $this->Cell(35,5,date('d-m-Y H:i'),0,1,'L');
//		$this->Cell(140,5,' ','',0,'R');
                $this->Cell(35,5,$_SESSION['lang']['unit'],'',0,'L');
                $this->Cell(2,5,'',':',0,'L');
                $this->Cell(100,5,$pt,'',0,'L');		
                $this->Cell(15,5,$_SESSION['lang']['page'],'',0,'L');
                $this->Cell(2,5,':','',0,'L');
                $this->Cell(35,5,$this->PageNo(),'',1,'L');

//			$this->Cell(140,5,' ','',0,'R');
                        $this->Cell(35,5,$_SESSION['lang']['status'],'',0,'L');
                        $this->Cell(2,5,':','',0,'L');
                        $this->Cell(100,5,$valstat,'',0,'L');		
                        $this->Cell(15,5,'User','',0,'L');
                        $this->Cell(2,5,':','',0,'L');
                        $this->Cell(35,5,$_SESSION['standard']['username'],'',1,'L');
//	        $this->Ln();

            $this->SetFont('Arial','',6);
                $this->Cell(5,5,'No.',1,0,'C');		
                $this->Cell(15,5,$_SESSION['lang']['nokaryawan'],1,0,'C');	
                $this->Cell(45,5,$_SESSION['lang']['namakaryawan'],1,0,'C');
                $this->Cell(30,5,$_SESSION['lang']['jennisangsuran'],1,0,'C');						
                $this->Cell(15,5,$_SESSION['lang']['jumlah'],1,0,'C');
                $this->Cell(15,5,$_SESSION['lang']['bulanawal'],1,0,'C');		
                $this->Cell(15,5,$_SESSION['lang']['tglcutisampai'],1,0,'C');
                $this->Cell(15,5,$_SESSION['lang']['jumlahbulan'],1,0,'C');
                $this->Cell(15,5,$_SESSION['lang']['angsuranperbulan'],1,0,'C');
                $this->Cell(20,5,$_SESSION['lang']['status'],1,1,'C');
    }
}
//================================



        $pdf=new PDF('P','mm','A4');
        $pdf->AddPage();
        $no=0;
                $res=mysql_query($str,$conn);		
                $no=0;
                while($bar=mysql_fetch_object($res))
                {			  
                   $no+=1;
                $pdf->Cell(5,5,$no,0,0,'C');		
                $pdf->Cell(15,5,$bar->karyawanid,0,0,'L');	
                $pdf->Cell(45,5,$bar->namakaryawan,0,0,'L');
                $pdf->Cell(30,5,$arr[$bar->jenis],0,0,'L');						
                $pdf->Cell(15,5,number_format($bar->total,2,'.',','),0,0,'R');
                $pdf->Cell(15,5,$bar->start,0,0,'L');		
                $pdf->Cell(15,5,$bar->end,0,0,'L');
                $pdf->Cell(15,5,$bar->jlhbln,0,0,'R');
                $pdf->Cell(15,5,number_format($bar->bulanan,2,'.',','),0,0,'R');
                $pdf->Cell(20,5,($bar->active==1?"Active":"Not Active"),0,1,'C');			  			
                }
        $pdf->Output();	

?>