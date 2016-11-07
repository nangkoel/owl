<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$proses=$_GET['proses'];

$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['period']==''?$period=$_GET['period']:$period=$_POST['period'];
$_POST['perod']==''?$perod=$_GET['perod']:$perod=$_POST['perod'];
$_POST['idKry']==''?$idKry=$_GET['idKry']:$idKry=$_POST['idKry'];
$arrBln=array(1=>"Jan",2=>"Feb",3=>"Mar",4=>"Apr",5=>"Mei",6=>"Jun",7=>"Jul",8=>"Agu",9=>"Sep",10=>"Okt",11=>"Nov",12=>"Des");
$_POST['idAfd']==''?$idAfd=$_GET['idAfd']:$idAfd=$_POST['idAfd'];
$_POST['tPkary2']==''?$tPkary=$_GET['tPkary2']:$tPkary=$_POST['tPkary2'];
$rNmTipe=makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$lksiTgs=substr($idAfd,0,4);
$kdBag2=$_POST['kdBag2'];
 if($_SESSION['empl']['tipelokasitugas']=='HOLDING'||$_SESSION['empl']['tipelokasitugas']=='KANWIL')
                {
                    if($idAfd!='')
                    {
                     $add="b.lokasitugas='".$idAfd."'";
                    }
                    else
                    {
                        exit("Error: Working unit required");
                    }
                    if($kdBag2!='')
                    {
                     $add.=" and b.bagian='".$kdBag2."'";
                    }
                }
                else
                {
                if(strlen($idAfd)<6)
                {
                    $add="b.lokasitugas='".$idAfd."' and (b.subbagian is null or b.subbagian='')";
                }
                else
                {
                    $add="b.subbagian='".$idAfd."'";
                }
                                if($kdBag2!='')
                                {
                                        $add.=" and b.bagian='".$kdBag2."'";
                                }
                }
                if($tPkary!='')
                {
                    $dtTipe=" and b.tipekaryawan='".$tPkary."'";
                }
switch($proses)
{
        case'preview':

                $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$idAfd."'";
                $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
                $rOrg=mysql_fetch_assoc($qOrg);
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }


        //periode gaji
        $bln=explode('-',$perod);
        $idBln=intval($bln[1]);	

        //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
               ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
               where b.sistemgaji='Bulanan' and a.periodegaji='".$perod."' and ".$add." ".$dtTipe." order by b.namakaryawan asc";
        //exit("Error:".$sSlip);
        $qSlip=mysql_query($sSlip) or die(mysql_error());
        $rCek=mysql_num_rows($qSlip);
        if($rCek>0)
        {
                while($rSlip=mysql_fetch_assoc($qSlip))
                {
                    if($rSlip['karyawanid']!='')
                    {
                    $arrKary[$rSlip['karyawanid']]=$rSlip['karyawanid'];
                    $arrKomp[$rSlip['karyawanid']]=$rSlip['idkomponen'];
                    $arrTglMsk[$rSlip['karyawanid']]=$rSlip['tanggalmasuk'];
                    $arrNik[$rSlip['karyawanid']]=$rSlip['nik'];
                    $arrNmKary[$rSlip['karyawanid']]=$rSlip['namakaryawan'];
                    $arrBag[$rSlip['karyawanid']]=$rSlip['bagian'];
                    $arrJbtn[$rSlip['karyawanid']]=$rSlip['namajabatan'];
                    $arrDept[$rSlip['karyawanid']]=$rSlip['nama'];
                    $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                    }
                }
          //array data komponen penambah dan pengurang
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='1'  and id not in ('26','28') ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $arrIdKompPls[]=$rKomp['id'];
              $arrNmKomPls[$rKomp['id']]=$rKomp['name'];
          }
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='0'  ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $arrIdKompMin[]=$rKomp['id'];
              $arrNmKomMin[$rKomp['id']]=$rKomp['name'];
          }
          $jmlhKary=count($arrKary);
        //  exit("Error".$sSlip);
                  foreach($arrKary as $dtKary)
                  {
                      echo"<table cellspacing=1 border=0 width=500>
                        <tr style='border-bottom:#000 solid 2px; border-top:#000 solid 2px;'><td valign=top>
                        <table border=0 width=110%>
                        <tr><td width=49% valign=top><table border=0><tr><td colspan=3>".$_SESSION['lang']['slipGaji'].": ".$arrBln[$idBln]."-".$bln[0]."</td></tr>
                        <tr><td>".$_SESSION['lang']['nik']."/".$_SESSION['lang']['tmk']."</td><td>:</td><td>".$arrNik[$dtKary]."/".tanggalnormal($arrTglMsk[$dtKary])."</td></tr>
                        <tr><td>".$_SESSION['lang']['nama']."</td><td>:</td><td>".$arrNmKary[$dtKary]."</td></tr>
                        </table></td><td width=51% valign=top><table border=0>
                        <tr><td colspan=3>&nbsp;</td></tr>
                        <tr><td>".$_SESSION['lang']['unit']."/".$_SESSION['lang']['bagian']."</td><td>:</td><td>".$rOrg['namaorganisasi']."/".$arrBag[$dtKary]."</td></tr>
                        <tr><td>".$_SESSION['lang']['jabatan']."</td><td>:</td><td>".$arrJbtn[$dtKary]."</td></tr>
                        </table></td></tr>
                        </table>
                        </td></tr>
                        <tr>
                        <td><table width=100%>
                        <thead>
                        <tr><td align=center>".$_SESSION['lang']['penambah']."</td><td align=center>".$_SESSION['lang']['pengurang']."</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                        <td valign=top>
                        <table width=100%>";
                      $arrPlus=Array();
                      $s=0;
                      foreach($arrIdKompPls as $idKompPls)
                      {
                          echo"<tr><td>".$arrNmKomPls[$idKompPls]."</td><td>:Rp.</td><td align=right> ".number_format($arrJmlh[$dtKary.$idKompPls],2)."</td></tr>";
                            $arrPlus[$s]=$arrJmlh[$dtKary.$idKompPls];
                            $s++;
                      }
                      echo"</table></td>
                        <td valign=top>
                        <table width=100%>";
                        $arrMin=Array();
                        $q=0;
                        foreach($arrIdKompMin as $idKompMin)
                          {
                              echo"<tr><td>".$arrNmKomMin[$idKompMin]."</td><td>:Rp.</td><td align=right> ".number_format($arrJmlh[$dtKary.$idKompMin],2)."</td></tr>";
                                $arrMin[$q]=$arrJmlh[$dtKary.$idKompMin];
                                $q++;
                          }
                          $gajiBersih=array_sum($arrPlus)-array_sum($arrMin);
                          echo"</table>
                        </td></tr>
                        <tr><td colspan=2><table width=100%>
                        <tr><td>Total Penambahan</td><td>:Rp.</td><td align=right> ".number_format(array_sum($arrPlus),2)."</td><td>Total Pengurangan</td><td>:Rp.</td><td align=right> ".number_format(array_sum($arrMin),2)."</td></tr>
                        <tr><td>Gaji Bersih</td><td>:Rp.</td><td align=right> ".number_format((array_sum($arrPlus)-array_sum($arrMin)),2)."</td><td>&nbsp;</td><td>&nbsp;</td><td align=right> &nbsp;</td></tr>
                        <tr><td>Terbilang</td><td>:</td><td colspan=4> ".terbilang($gajiBersih,2)." rupiah</td></tr></table></td></tr></tbody>
                        </table></td>
                        </tr>


                        <tr>
                        <td>&nbsp;</td>
                        </tr>
                        </table>
                        ";
                  }

        }
        else
        {
                echo"Not Found";
        }

        break;
        case'pdf':
        $perod=$_GET['perod'];
        $idAfd=$_GET['idAfd'];
        $idKry=$_GET['idKry'];
        $kdBag2=$_GET['kdBag2'];

        //+++++++++++++++++++++++++++++++++++++++++++++++++++++
//create Header

class PDF extends FPDF
{
var $col=0;
var $dbname;

function SetCol($col)
        {
            //Move position to a column
            $this->col=$col;
            $x=10+$col*100;
            $this->SetLeftMargin($x);
            $this->SetX($x);
        }

function AcceptPageBreak()
        { 
                        if($this->col<1)
                    {
                        //Go to next column
                        $this->SetCol($this->col+1);
                        $this->SetY(10);
                        return false;
                    }
                    else
                    {
                        //Go back to first column and issue page break
                                $this->SetCol(0);
                        return true;
                    }
        }

        function Header()
        {    
                $this->lMargin=5;  
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',5);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }
}
        $pdf=new PDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial','',5);
        //periode gaji
        $bln=explode('-',$perod);
        $idBln=intval($bln[1]);	

        //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
               ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
               where b.sistemgaji='Bulanan' and a.periodegaji='".$perod."' and ".$add."  ".$dtTipe." order by b.namakaryawan asc";
        $qSlip=mysql_query($sSlip) or die(mysql_error());
        $rCek=mysql_num_rows($qSlip);
        if($rCek>0)
        {
                while($rSlip=mysql_fetch_assoc($qSlip))
                {
                    if($rSlip['karyawanid']!='')
                    {
                    $arrKary[$rSlip['karyawanid']]=$rSlip['karyawanid'];
                    $arrKomp[$rSlip['karyawanid']]=$rSlip['idkomponen'];
                    $arrTglMsk[$rSlip['karyawanid']]=$rSlip['tanggalmasuk'];
                    $arrNik[$rSlip['karyawanid']]=$rSlip['nik'];
                    $arrNmKary[$rSlip['karyawanid']]=$rSlip['namakaryawan'];
                    $arrBag[$rSlip['karyawanid']]=$rSlip['bagian'];
                    $arrJbtn[$rSlip['karyawanid']]=$rSlip['namajabatan'];
                    $arrDept[$rSlip['karyawanid']]=$rSlip['nama'];
                    $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                    }
                }

          //array data komponen penambah dan pengurang
          $sKomp="select id,name,plus from ".$dbname.".sdm_ho_component where plus=1 ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $arrIdKompPls[]=$rKomp['id'];
              $arrNmKomPls[$rKomp['id']][1]=$rKomp['name'];
          }
          $sKomp2="select distinct id,name,plus from ".$dbname.".sdm_ho_component
                   where plus=0  order by id";
          $qKomp2=mysql_query($sKomp2) or die(mysql_error());
          while($rKomp2=mysql_fetch_assoc($qKomp2))
          {
              $arrIdKompPls[]=$rKomp2['id'];
              $arrNmKomPls[$rKomp2['id']][0]=$rKomp2['name'];
          }
          //komponen
            $arrMinusId=Array();
            $arrMinusName=Array();
            $str="select distinct id,name from ".$dbname.".sdm_ho_component  a
                   left join ".$dbname.".sdm_gaji_vw b on a.id=b.idkomponen
                   where plus=0 and jumlah!=0 and b.periodegaji='".$perod."' 
                   and b.kodeorg='".substr($idAfd,0,4)."'  order by id";
            // echo $str;exit();
            $res=mysql_query($str,$conn);
            while($bar=mysql_fetch_object($res))
            {
                array_push($arrMinusId,$bar->id);
                array_push($arrMinusName,$bar->name);
            }
            //samakan
            $arrPlusId=$arrMinusId;
            $arrPlusName=$arrMinusName;
            //Kosongkan
            for($r=0;$r<count($arrMinusId);$r++)
            {
                 $arrPlusId[$r]='';
                 $arrPlusName[$r]='';
            }
            //$str="select  id,name from ".$dbname.".sdm_ho_component where plus='1' and id not in ('26','28') order by id";
            $str="select distinct id,name from ".$dbname.".sdm_ho_component  a
                   left join ".$dbname.".sdm_gaji_vw b on a.id=b.idkomponen
                   where plus=1 and jumlah!=0 and b.periodegaji='".$perod."' 
                   and b.kodeorg='".substr($idAfd,0,4)."' and id not in ('26','28') order by id";
            $res=mysql_query($str,$conn);
            $n=-1;
            while($bar=mysql_fetch_object($res))
            {
                $n+=1;
                $arrPlusId[$n]=$bar->id;
                $arrPlusName[$n]=$bar->name;
            }
           $arrValPlus=Array();
           $arrValMinus=Array();
           for($x=0;$x<count($arrPlusId);$x++)
           {
                $arrValPlus[$x]=0;
                $arrValMinus[$x]=0;
           }
           $str3="select jumlah,idkomponen,a.karyawanid,c.plus from ".$dbname.".sdm_gaji_vw a 
                  left join ".$dbname.".sdm_ho_component c on a.idkomponen=c.id
                 where a.sistemgaji='Bulanan' and a.periodegaji='".$perod."' ";
           //exit("Error:".$str3);
           $res3=mysql_query($str3,$conn);
           while($bar3=mysql_fetch_assoc($res3))
           {
               if($bar3['plus']=='1')
               {
                    if($bar3['jumlah']!='')
                    {
                        $arrValPlus[$bar3['karyawanid']][$bar3['idkomponen']]=$bar3['jumlah'];
                    }
               }
               elseif($bar3['plus']=='0')
               {
                    if($bar3['jumlah']!='')
                    {
                        $arrValMinus[$bar3['karyawanid']][$bar3['idkomponen']]=$bar3['jumlah'];
                    }
               } 
            }	 

        foreach($arrKary as $dtKary)
        {
                        if($_SESSION['org']['kodeorganisasi']=='HIP'){  
                            $pdf->Image('images/hip_logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
                        } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  
                            $pdf->Image('images/sil_logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
                        } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  
                            $pdf->Image('images/sip_logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
                        }
                        $pdf->SetX($pdf->getX()+10);
                        $pdf->SetFont('Arial','B',8);	
                        $pdf->Cell(75,6,$_SESSION['org']['namaorganisasi'],0,1,'L');
                        $pdf->SetFont('Arial','',7);	
                        $pdf->Cell(71,4,$_SESSION['lang']['slipGaji'].': '.$arrBln[$idBln]."-".$bln[0],'T',0,'L');
                        $pdf->SetFont('Arial','',6);
                                $pdf->Cell(25,4,$_SESSION['lang']['printedOn'].': '.date('d-m-Y: H:i:s'),"T",1,'R');
                        $pdf->SetFont('Arial','',6);		
                        $pdf->Cell(16,4,$_SESSION['lang']['nik']."/".$_SESSION['lang']['tmk'],0,0,'L');
                                $pdf->Cell(35,4,": ".$arrNik[$dtKary]."/".tanggalnormal($arrTglMsk[$dtKary]),0,0,'L');
                        $pdf->Cell(18,4,$_SESSION['lang']['unit']."/".$_SESSION['lang']['bagian'],0,0,'L');	
                                $pdf->Cell(28,4,': '.$idAfd." / ".$arrBag[$dtKary],0,1,'L');		
                        $pdf->Cell(16,4,$_SESSION['lang']['namakaryawan'],0,0,'L');
                                $pdf->Cell(35,4,': '.$arrNmKary[$dtKary],0,0,'L');	
                        $pdf->Cell(18,4,$_SESSION['lang']['jabatan'],0,0,'L');
                                $pdf->Cell(28,4,':'.$arrJbtn[$dtKary],0,1,'L');	
                        $pdf->Cell(48,4,$_SESSION['lang']['penambah'],'TB',0,'C');
                        $pdf->Cell(48,4,$_SESSION['lang']['pengurang'],'TB',1,'C');


                     for($mn=0;$mn<count($arrPlusId);$mn++)
                        {
                                $pdf->Cell(25,4,$arrPlusName[$mn],0,0,'L');
                                if($arrPlusName[$mn]=='')
                                {
                                  $pdf->Cell(5,4,"",0,0,'L');
                                  $pdf->Cell(18,4,'','R',0,'R');
                                }
                                else
                                {
                                    if($arrPlusId[$mn]=='')
                                    {
                                        $pdf->Cell(5,4,"",0,0,'L');
                                        $pdf->Cell(18,4,'','R',0,'R');
                                    }
                                    else
                                    {
                                        $pdf->Cell(5,4,":Rp.",0,0,'L');
                                        $pdf->Cell(18,4,number_format($arrValPlus[$dtKary][$arrPlusId[$mn]],2,'.',','),'R',0,'R');
                                        $arrPlus[$dtKary]+=$arrValPlus[$dtKary][$arrPlusId[$mn]];
                                    }
                                }
                                $pdf->Cell(25,4,$arrMinusName[$mn],0,0,'L');
                                if($arrMinusName[$mn]=='')
                                {
                                  $pdf->Cell(5,4,"",0,0,'L');
                                  $pdf->Cell(18,4,'',0,1,'R');
                                }
                                else
                                {
                                    if($arrMinusId[$mn]=='')
                                    {
                                      $pdf->Cell(5,4,"",0,0,'L');
                                       $pdf->Cell(18,4,'',0,1,'R');
                                    }
                                    else
                                    {
                                      $pdf->Cell(5,4,":Rp.",0,0,'L');
                                      $pdf->Cell(18,4,number_format(($arrValMinus[$dtKary][$arrMinusId[$mn]]*-1),2,'.',','),0,1,'R');
                                      $arrMin[$dtKary]+=$arrValMinus[$dtKary][$arrMinusId[$mn]]*-1;
                                    }
                                }
                        }

                                $pdf->Cell(25,4,$_SESSION['lang']['totalPendapatan'],'TB',0,'L');
                                $pdf->Cell(5,4,":Rp.",'TB',0,'L');
                                        $pdf->Cell(18,4,number_format($arrPlus[$dtKary],2,'.',','),'TB',0,'R');
                                $pdf->Cell(25,4,$_SESSION['lang']['totalPotongan'],'TB',0,'L');
                                $pdf->Cell(5,4,":Rp.",'TB',0,'L');
                                        $pdf->Cell(18,4,number_format(($arrMin[$dtKary]*-1),2,'.',','),'TB',1,'R');

                        $pdf->SetFont('Arial','B',7);
                        $pdf->Cell(23,4,$_SESSION['lang']['gajiBersih'],0,0,'L');
                        $pdf->Cell(5,4,":Rp.",0,0,'L');
                                $pdf->Cell(18,4,number_format(($arrPlus[$dtKary]-($arrMin[$dtKary]*-1)),2,'.',','),0,0,'R');
                                $pdf->Cell(47,4,"",0,1,'L');
                                $terbilang=($arrPlus[$dtKary]-($arrMin[$dtKary]*-1));
                                $blng=terbilang($terbilang,2)." rupiah";
                        $pdf->SetFont('Arial','',7);	
                        $pdf->Cell(23,4,'Terbilang',0,0,'L');
                        $pdf->Cell(2,4,":",0,0,'L');
                                $pdf->MultiCell(65,4,$blng,0,'L');
//                        $pdf->SetFont('Arial','I',5);
                        $pdf->Cell(96,4,'','T',1,'L');
                        $pdf->Cell(50,4,$_SESSION['lang']['pembayar'].",",0,0,'L');
                        $pdf->Cell(50,4,$_SESSION['lang']['kodeorgpenerima'].",",0,1,'L');
                        $pdf->SetY($pdf->GetY()+10);
                        $pdf->SetX($pdf->getX()+50);
                        $pdf->Cell(50,4,"(".$arrNmKary[$dtKary].")",0,1,'L');
                        $pdf->SetFont('Arial','',6);	
                        $pdf->Ln();	
                        if($pdf->GetY()>140 and $pdf->col<1)
                                $pdf->AcceptPageBreak();
                        if ($pdf->GetY()>140 and $pdf->col>0)
                           {
                                //$pdf->lewat=true;
                                // $pdf->AcceptPageBreak();
                                //$pdf->SetY(277-$pdf->GetY());
                                $r=275-$pdf->GetY();
                                $pdf->Cell(80,$r,'',0,1,'L');

                                //$pdf->ln();
                           }
                        //else   
                        //$pdf->lewat=false; 	

                        $pdf->cell(-1,3,'',0,0,'L');	
                }
}
else
{
        if($_SESSION['org']['kodeorganisasi']=='HIP'){  
            $pdf->Image('images/hip_logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
        } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  
            $pdf->Image('images/sil_logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
        } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  
            $pdf->Image('images/sip_logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
        }
        $pdf->SetX($pdf->getX()+8);
        $pdf->SetFont('Arial','B',8);	
        $pdf->Cell(70,5,$_SESSION['org']['namaorganisasi'],0,1,'L');
        $pdf->SetFont('Arial','',5);	
        $pdf->Cell(60,3,'NOT FOUND','T',0,'L');
}
        $pdf->Output();

        break;

        case'excel':
        //periode gaji
        $bln=explode('-',$perod);
        $idBln=intval($bln[1]);	

          //array data komponen penambah dan pengurang
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='1' and id not in ('26','28')  ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $arrIdKompPls[]=$rKomp['id'];
              $arrNmKomPls[$rKomp['id']]=$rKomp['name'];
          }
          $totPlus=count($arrIdKompPls);
          $brsPlus=0;
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='0'  ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $arrIdKompMin[]=$rKomp['id'];
              $arrNmKomMin[$rKomp['id']]=$rKomp['name'];
          }

                        $sPeriod="select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where jenisgaji='H' and periode='".$perod."' and kodeorg='".substr($idAfd,0,4)."'";	
                        $qPeriod=mysql_query($sPeriod) or die(mysql_error());
                        $rPeriod=mysql_fetch_assoc($qPeriod);
                        $mulai=tanggalnormal($rPeriod['tanggalmulai']);
                        $selesi=tanggalnormal($rPeriod['tanggalsampai']);

                        $stream.="
                        <table>
                        <tr><td colspan=15 align=center>List Data Gaji Harian, Unit : ".$idAfd."</td></tr>
                        <tr><td colspan=15 align=center>Periode : ".$mulai." s.d. ".$selesi."</td></tr>
                        </table>
                        <table border=1>
                        <tr>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>No.</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['namakaryawan']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['nik']."/".$_SESSION['lang']['tmk']."</td>";
                         if($_SESSION['empl']['tipelokasitugas']=='HOLDING'||$_SESSION['empl']['tipelokasitugas']=='KANWIL')
                         {
                            $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['subbagian']."</td>";
                         }
                         $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>No. Rekening</td>";
                               $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['tipekaryawan']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['totLembur']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['unit']."/".$_SESSION['lang']['bagian']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['statuspajak']."</td>

                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['jabatan']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['penambah']."
                                <table cellspacing=0 border=0><tr>";
                        $brsPlus=0;
                        foreach($arrIdKompPls as $lstKompPls)
                        {
                             $brsPlus++;
                            $stream.="<td>".$arrNmKomPls[$lstKompPls]."</td>";
                            if($brsPlus==1)
                            {
                                $stream.="<td>".$arrNmKomMin[37]."</td>";
                                $stream.="<td>".$arrNmKomMin[36]."</td>";
                            }

                        }


                        $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['totalPendapatan']."</td>";
                        $stream.="</tr></table>	</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['pengurang']."<table cellspacing=0 border=0><tr>";
                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td>".$arrNmKomMin[$lstKompMin]."</td>";
                                    }
                                }

                                $stream.="</tr></table>	</td>";
                                $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['totalPotongan']."</td><td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['gajiBersih']."</td>";
                                $stream.="</tr><tr>";

        //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,b.subbagian,b.norekeningbank from
               ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
               where b.sistemgaji='Bulanan' and a.periodegaji='".$perod."' and ".$add." ".$dtTipe."
               order by b.namakaryawan asc";
        $qSlip=mysql_query($sSlip) or die(mysql_error());
        $rCek=mysql_num_rows($qSlip);
        if($rCek>0)
        {
                while($rSlip=mysql_fetch_assoc($qSlip))
                {
                    if($rSlip['karyawanid']!='')
                    {
                    $arrKary[$rSlip['karyawanid']]=$rSlip['karyawanid'];
                    $arrKomp[$rSlip['karyawanid']]=$rSlip['idkomponen'];
                    $arrTglMsk[$rSlip['karyawanid']]=$rSlip['tanggalmasuk'];
                    $arrNik[$rSlip['karyawanid']]=$rSlip['nik'];
                    $arrNmKary[$rSlip['karyawanid']]=$rSlip['namakaryawan'];
                    $arrBag[$rSlip['karyawanid']]=$rSlip['bagian'];
                    $arrJbtn[$rSlip['karyawanid']]=$rSlip['namajabatan'];
                    $arrTipekary[$rSlip['karyawanid']]=$rSlip['tipekaryawan'];
                    $arrStatPjk[$rSlip['karyawanid']]=$rSlip['statuspajak'];
                    $arrDept[$rSlip['karyawanid']]=$rSlip['nama'];
                    $arrSubbagian[$rSlip['karyawanid']]=$rSlip['subbagian'];
                    $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                    $arrRek[$rSlip['karyawanid']]=$rSlip['norekeningbank'];
                    $arrTotal[$rSlip['idkomponen']]+=$rSlip['jumlah'];
                    }
                }
                $sTot="select tipelembur,jamaktual,karyawanid from ".$dbname.".sdm_lemburdt where substr(kodeorg,1,4)='".substr($idAfd,0,4)."' and tanggal between '".$rPeriod['tanggalmulai']."' and '".$rPeriod['tanggalsampai']."'";		
                $qTot=mysql_query($sTot) or die(mysql_error($conn));
                while($rTot=mysql_fetch_assoc($qTot))
                {
                        $sJum="select jamlembur as totalLembur from ".$dbname.".sdm_5lembur where tipelembur='".$rTot['tipelembur']."'
                        and jamaktual='".$rTot['jamaktual']."' and kodeorg='".substr($idAfd,0,4)."'";
                        $qJum=mysql_query($sJum) or die(mysql_error());
                        $rJum=mysql_fetch_assoc($qJum);
                        $jumTot[$rTot['karyawanid']]+=$rJum['totalLembur'];
                }
                $peng1=37;
                $peng2=36;
                    foreach($arrKary as $dtKary)
                    {		
                        $no+=1;
                                $stream.="<tr class=rowcontent>
                                <td>".$no."</td>
                                <td>".$arrNmKary[$dtKary]."</td>
                                <td>".$arrNik[$dtKary]."</td>";
                                $cold=9;
                                if($_SESSION['empl']['tipelokasitugas']=='HOLDING'||$_SESSION['empl']['tipelokasitugas']=='KANWIL')
                                {
                                    $cold=10;
                                    $stream.="<td>".$arrSubbagian[$dtKary]."</td>";
                                }
                                $stream.="<td>".$arrRek[$dtKary]."</td>";
                                $stream.="<td>".$rNmTipe[$arrTipekary[$dtKary]]."</td>
                                <td>".$jumTot[$dtKary]."</td>
                                <td>".$arrDept[$dtKary]."</td>

                                <td>".$arrStatPjk[$dtKary]."</td>
                                <td>".$arrJbtn[$dtKary]."</td><td>";

                                $stream.="<table width=100% border=1><tr>";
                                $arrPlus=Array();
                                $s=0;
                                $brsPlus2=0;
                                 foreach($arrIdKompPls as $lstKompPls)
                                {

                                    $stream.="<td align=right>".number_format($arrJmlh[$dtKary.$lstKompPls],2)."</td>";
                                    $arrPlus[$s]=$arrJmlh[$dtKary.$lstKompPls];
                                    $s++;
                                    $brsPlus2++;
                                    if($brsPlus2==1)
                                    {
                                        $stream.="<td>-".$arrJmlh[$dtKary.$peng1]."</td>";
                                        $stream.="<td>-".$arrJmlh[$dtKary.$peng2]."</td>";
                                    }

                                }

                                $totDpt=array_sum($arrPlus)-($arrJmlh[$dtKary.$peng1]+$arrJmlh[$dtKary.$peng2]);
                                $stream.="<td align=right>".number_format($totDpt,2)."</td>";
                                $stream.="</tr></table>";			
                                $stream.="</td><td><table width=100% border=1><tr>";
                                $arrMin=Array();
                                $q=0;
                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td align=right>".number_format($arrJmlh[$dtKary.$lstKompMin])."</td>";
                                         $arrMin[$q]=$arrJmlh[$dtKary.$lstKompMin];
                                         $q++;
                                    }
                                }
                                $gajiBersih=$totDpt-array_sum($arrMin);				
                                $stream.="</tr></table></td>";
                                //$stream.="<td align=right>".number_format(array_sum($arrPlus),2)."</td>";
                                $stream.="<td align=right>".number_format(array_sum($arrMin),2)."</td>";
                                $stream.="<td align=right>".number_format($gajiBersih,0)."</td>";	
                                $stream.="</tr>";
                                }
                                $stream.="<tr><td colspan=".$cold." align=right>".$_SESSION['lang']['total']."</td><td>";
                                $stream.="<table border=1 width=100%>";
                                $s=0;
                                $brsPlus2=0;
                                $arrPlus=array();
                                foreach($arrIdKompPls as $lstKompPls)
                                {
                                    $stream.="<td align=right>".number_format($arrTotal[$lstKompPls],2)."</td>";
                                    $arrPlus[$s]=$arrTotal[$lstKompPls];
                                    $s++;
                                    $brsPlus2++;
                                    if($brsPlus2==1)
                                    {

                                        $stream.="<td>-".number_format($arrTotal[$peng1],2)."</td>";
                                        $stream.="<td>-".number_format($arrTotal[$peng2],2)."</td>";
                                    }
                                }
                                $totDpt=array_sum($arrPlus)-($arrTotal[$peng1]+$arrTotal[$peng2]);
                                $stream.="<td align=right>".number_format($totDpt,2)."</td>";
                                $stream.="</tr></table>";			
                                $stream.="</td><td><table width=100% border=1><tr>";
                                $arrMin=Array();
                                $q=0;
                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td align=right>".number_format($arrTotal[$lstKompMin])."</td>";
                                         $arrMin[$q]=$arrTotal[$lstKompMin];
                                         $q++;
                                    }
                                }
                                $gajiBersih=$totDpt-array_sum($arrMin);				
                                $stream.="</tr></table></td>";
                                //$stream.="<td align=right>".number_format(array_sum($arrPlus),2)."</td>";
                                $stream.="<td align=right>".number_format(array_sum($arrMin),2)."</td>";
                                $stream.="<td align=right>".number_format($gajiBersih,0)."</td>";	
                                $stream.="</tr>";
                }
                else
                {
                    $stream.="<tr><td colspan=20>&nbsp;</td></tr>";
                }

                        //echo "warning:".$strx;
                        //=================================================

                        //echo $stream;exit();
                        $stream.="</table>Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
                        $dte=date("YmdHms");
                        $nop_="GajiBulananAfdeling_".$_SESSION['empl']['lokasitugas'].$dte;
                         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                         gzwrite($gztralala, $stream);
                         gzclose($gztralala);
                         echo "<script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls.gz';
                            </script>";

        break;
         case'getPeriode':
            $optPeriode="<option value''>".$_SESSION['lang']['pilihdata']."</option>";
            $sPeriode="select periode from ".$dbname.".sdm_5periodegaji where kodeorg='".substr($idAfd,1,4)."' and jenisgaji='B'";
            $qPeriode=mysql_query($sPeriode) or die(mysql_error());
            while($rPeriode=mysql_fetch_assoc($qPeriode))
            {
                $optPeriode.="<option value=".$rPeriode['periode'].">".$rPeriode['periode']."</option>";
            }
            echo $optPeriode;
        break;
        default:
        break;
}
?>