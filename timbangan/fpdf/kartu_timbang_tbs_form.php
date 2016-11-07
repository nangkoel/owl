<?php
session_start();
require('fpdf.php');

class PDF extends FPDF
{
        function Header()
        {
            require_once('connection.php');
            $TICKETNO=$_GET['TICKETNO'];
            $IDWB=$_GET['IDWB'];
            $USERNAME=$_SESSION['standard']['username'];
           $str="select * from ".$dbname.".mstrxtbs where TICKETNO2='".$TICKETNO."' and IDWB ='".$IDWB."' and OUTIN='0'";
           $res=mysql_query($str);
           while ($bar=mysql_fetch_object($res)){;
                        //$trpname=$bar[0];
                        $VEHNO=$bar->VEHNOCODE;$THNTNM=$bar->TAHUNTANAM;
                        $SPBNO=$bar->SPBNO;$TICKETNO2=$bar->TICKETNO2;
                        $TRPCODE=$bar->TRPCODE;
                        $DRIVER=$bar->DRIVER;
                        $UNIT=$bar->UNITCODE;
                        $DIV=$bar->DIVCODE;
                        $TAHUNTANAM=$bar->TAHUNTANAM;
                        $JJG=$bar->JMLHJJG;
                        $BRONDOLAN=$bar->BRONDOLAN;
                        $BERATKIRIM=$bar->BERATKIRIM;
                        $DATEIN=$bar->DATEIN;$DATEOUT=$bar->DATEOUT;
                        $WEIGH1=$bar->WEI1ST;$WEIGH2=$bar->WEI2ND;$NETTO=$bar->NETTO;
                        $tglin=substr($DATEIN,8,2)."-".substr($DATEIN,5,2)."-".substr($DATEIN,0,4)." ".substr($DATEIN,11,2).":".substr($DATEIN,14,2).":".substr($DATEIN,17,2);
                        $tglout=substr($DATEOUT,8,2)."-".substr($DATEOUT,5,2)."-".substr($DATEOUT,0,4)." ".substr($DATEOUT,11,2).":".substr($DATEOUT,14,2).":".substr($DATEOUT,17,2);
                        $POTONGAN=$bar->KGPOTSORTASI; 
                        $produk=$bar->PRODUCTCODE;
                        $buahbusuk=$bar->buahbusuk;
                        $buahkrgmatang=$bar->buahkrgmatang;
                        $buahsakit=$bar->buahsakit;
                        $janjangkosong=$bar->janjangkosong;
                        $lwtmatang=$bar->lwtmatang;
                        $mentah=$bar->mentah;
                        $tkpanjang  =$bar->tkpanjang;
						$tigakilo  =$bar->tigakilo;

                }
                //ambil standar fraksi
              $str="select kodefraksi,potongan from ".$dbname.".msfraksi";
              $resk=mysql_query($str);
              while($bar=mysql_fetch_object($resk)){
                  $frag[$bar->kodefraksi]=$bar->potongan;
              }
                
                //Ambil jenis produk:
                $stru="select productname from ".$dbname.".msproduct where PRODUCTCODE='".$produk."'";
                $resu=mysql_query($stru);
                $produk='';
                while($baru=mysql_fetch_object($resu))
                {
                 $produk=$baru->productname;
                }
                $str2="select TRPNAME from ".$dbname.".msvendortrp where TRPCODE='".$TRPCODE."'";
                $res2=mysql_query($str2);
                while ($bar1=mysql_fetch_object($res2)){
                        $TRPNAME=$bar1->TRPNAME;
                }
				if($TRPNAME=='')
				{
					$str2="select BUYERNAME from ".$dbname.".msvendorbuyer where BUYERCODE='".$TRPCODE."'";
					$res2=mysql_query($str2);
					while ($bar1=mysql_fetch_object($res2)){
							$TRPNAME=$bar1->BUYERNAME;
					}
				}
                $str2="select UNITNAME from ".$dbname.".msunit where UNITCODE='".$UNIT."'";
                $res2=mysql_query($str2);
                while ($bar1=mysql_fetch_object($res2)){
                        $UNIT=$UNIT." (".$bar1->UNITNAME.")";
                }
                if($UNIT=='') $UNIT=$TRPNAME;
                $str2="select DIVNAME from ".$dbname.".msdivisi where DIVCODE='".$DIV."'";
                $res2=mysql_query($str2);
                while ($bar1=mysql_fetch_object($res2)){
                        $DIV=$DIV." (".$bar1->DIVNAME.")";
                }
         //ambil nama manager
		 $str="select MNGRNAME from ".$dbname.".mssystem";
		 $resh=mysql_query($str);
		 while($barh=mysql_fetch_object($resh)){
		  $mgr=$barh->MNGRNAME;
 		 }
           $this->Ln(0);
           $this->SetFont('arial','',12);
           $this->Cell(30,5,'PT.HARDAYA INTI PLANTATIONS',0,1,'L');
	   $this->Cell(30,5,'PMKS BUOL SULTENG',0,l,'L');
           $this->Ln(10);
           $this->Cell(30,5,'No. Tiket/Seri',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(22,5,$TICKETNO2,0,0,'L');
           
           $this->Cell(20,5,'Tgl.Cetak:',0,0,'L');           
           $this->Cell(15,5,date("d/m/Y"),0,1,'L');

           $this->Cell(30,5,'No.Kend.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$VEHNO,0,1,'L');

           $this->Cell(30,5,'Kebun/Supplier.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$UNIT,0,1,'L');
 
           $this->Cell(30,5,'Afdeling.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$DIV,0,1,'L');           
       
           $this->Cell(30,5,'No.SP.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$SPBNO,0,1,'L');  
           
            $this->Cell(30,5,'Komoditi.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$produk,0,1,'L');          

            $this->Cell(30,5,'Pengangkut.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$TRPNAME,0,1,'L');                

            $this->Cell(30,5,'Supir.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$DRIVER,0,1,'L');      
           
            $this->Cell(30,5,'Jlh.Janjang.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(25,5,$JJG,0,0,'');
           $this->Cell(20,5," BJR: ".@number_format(($NETTO/$JJG),2)." Kg",0,1,'R');

            $this->Cell(30,5,'Timbang I.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(20,5,number_format($WEIGH1,0,',','.'),0,0,'R'); 
            $this->Cell(6,5,'Kg','',0,'L');          
           $this->Cell(35,5,$tglin,0,1,'L');           

            $this->Cell(30,5,'Timbang II.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(20,5,number_format($WEIGH2,0,',','.'),'B',0,'R'); 
            $this->Cell(6,5,'Kg','B',0,'L');          
           $this->Cell(35,5,$tglout,'',1,'L');    
           
            $this->Cell(30,5,'Berat Bersih.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(20,5,number_format($NETTO,0,',','.'),0,0,'R'); 
            $this->Cell(6,5,'Kg','',1,'L');          

            $this->Cell(30,5,'Potongan.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');
           if (abs($POTONGAN)!=0){
               $this->Cell(20,5,"-".number_format($POTONGAN,0,',','.'),'B',0,'R'); 
           } else {
               $this->Cell(20,5,number_format($POTONGAN,0,',','.'),'B',0,'R'); 
           }
            $this->Cell(6,5,'Kg','B',1,'L');     
            
           $this->Cell(30,5,'Normal.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(20,5,number_format($NETTO-$POTONGAN,0,',','.'),0,0,'R'); 
           $this->Cell(6,5,'Kg','',1,'L');   
           $this->Line(10,95,100,95);
           //$this->Ln(20);

           $this->Cell(30,5,'SORTASI:','',1,'L');
           $this->Cell(25,5,'Busuk',0,0,'L');
           $this->Cell(12,5,$buahbusuk." JJG",0,0,'R');
           $this->Cell(2,5,"=",0,0,'L');           
           $this->Cell(20,5,@number_format(($buahbusuk*($NETTO/$JJG)*$frag['buahbusuk']),0)." Kg.",0,0,'R');
 
           $this->Cell(35,5,'Kurang Matang',0,0,'L');
           $this->Cell(12,5,$buahkrgmatang." JJG",0,0,'R');
           $this->Cell(2,5,"=",0,0,'L');           
           $this->Cell(20,5,number_format(($buahkrgmatang*($NETTO/$JJG)*$frag['buahkrgmatang']),0)." Kg.",0,1,'R');           
           
           $this->Cell(25,5,'Buah Sakit',0,0,'L');
           $this->Cell(12,5,$buahsakit." JJG",0,0,'R');
           $this->Cell(2,5,"=",0,0,'L');           
           $this->Cell(20,5,@number_format(($buahsakit*($NETTO/$JJG)*$frag['buahsakit']),0)." Kg.",0,0,'R');           
           
           $this->Cell(35,5,'Jjg.Kosong',0,0,'L');
           $this->Cell(12,5,$janjangkosong." JJG",0,0,'R');
           $this->Cell(2,5,"=",0,0,'L');           
           $this->Cell(20,5,number_format(($janjangkosong*($NETTO/$JJG)*$frag['janjangkosong']),0)." Kg.",0,1,'R');
           
           $this->Cell(25,5,'Lw. Matang',0,0,'L');
           $this->Cell(12,5,$lwtmatang." JJG",0,0,'R');
           $this->Cell(2,5,"=",0,0,'L');           
           $this->Cell(20,5,number_format(($lwtmatang*($NETTO/$JJG)*$frag['lwtmatang']),0)." Kg.",0,0,'R');
           
           $this->Cell(35,5,'Mentah',0,0,'L');
           $this->Cell(12,5,$mentah." JJG",0,0,'R');
           $this->Cell(2,5,"=",0,0,'L');           
           $this->Cell(20,5,number_format(($mentah*($NETTO/$JJG)*$frag['mentah']),0)." Kg.",0,1,'R');
           
           $this->Cell(25,5,'Tk. Panjang',0,0,'L');
           $this->Cell(12,5,$tkpanjang." JJG",0,0,'R');
           $this->Cell(2,5,"=",0,0,'L');           
           $this->Cell(20,5,number_format(($tkpanjang*($NETTO/$JJG)*$frag['tkpanjang']),0)." Kg.",0,0,'R');

           $this->Cell(35,5,'BJR < 3KG',0,0,'L');
           $this->Cell(12,5,$tigakilo." JJG",0,0,'R');
           $this->Cell(2,5,"=",0,0,'L');           
           $this->Cell(20,5,number_format(($tigakilo*($NETTO/$JJG)*$frag['tigakilo']),0)." Kg.",0,1,'R');
           
           //$this->Line(10,145,100,145);
           $this->Ln(15);
           
           
          $this->Cell(60,5,strtoupper($USERNAME),'B',0,'C');
          $this->Cell(60,5,strtoupper($DRIVER),'B',1,'C');          
          $this->Cell(60,5,'OPERATOR WB',0,0,'C');
          $this->Cell(60,5,'DRIVER',0,1,'C');
        }
}
$pdf=new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',8);
$pdf->Output();
?>
