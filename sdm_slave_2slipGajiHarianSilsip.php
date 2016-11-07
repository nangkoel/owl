<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');
function dates_inbetween($date1, $date2){

    $day = 60*60*24;

    $date1 = strtotime($date1);
    $date2 = strtotime($date2);

    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between

    $dates_array = array();
    $dates_array[] = date('Y-m-d',$date1);

    for($x = 1; $x < $days_diff; $x++){
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }

    $dates_array[] = date('Y-m-d',$date2);
    if($date1==$date2){
        $dates_array = array();
        $dates_array[] = date('Y-m-d',$date1);        
    }
    return $dates_array;
}

$proses=$_GET['proses'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['period']==''?$period=$_GET['period']:$period=$_POST['period'];
$_POST['idKry']==''?$idKry=$_GET['idKry']:$idKry=$_POST['idKry'];
$_POST['kdBag']==''?$kdBag=$_GET['kdBag']:$kdBag=$_POST['kdBag'];
$_POST['tPkary']==''?$tPkary=$_GET['tPkary']:$tPkary=$_POST['tPkary'];
$rNmTipe=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
$dtTipe="";
$arrBln=array(1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desembe");
if($idKry=='')
{
    if($kdBag!='')
    {
        $tam="  and b.bagian='".$kdBag."'";
    }
    $where="a.sistemgaji='HARIAN' and a.periodegaji='".$periode."' and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'
          ".$tam." ";
    $where2="b.sistemgaji='HARIAN' and a.periodegaji='".$periode."' and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'
            ".$tam." ";
}
 else {
     if($period!='')
    {
        $periode=$period;
    }
     $where="a.sistemgaji='Harian' and a.periodegaji='".$periode."' and a.karyawanid='".$idKry."'";
    $where2="b.sistemgaji='Harian' and a.periodegaji='".$periode."' and a.karyawanid='".$idKry."'";
}

if($tPkary!='')
{
    $dtTipe=" and b.tipekaryawan='".$tPkary."'";
}

$sKemandoran="select karyawanid,jabatan,sum(potongan) as potongan from ".$dbname.".kebun_premikemandoran where periode='".$periode."' and kodeorg='".$_SESSION['empl']['lokasitugas']."' group by karyawanid,jabatan";
$qKemandoran=mysql_query($sKemandoran) or die(mysql_error($conn));
while ($rKemandoran=mysql_fetch_object($qKemandoran)){
    $arrPotMandor[$rKemandoran->karyawanid]['jabatan']=$rKemandoran->jabatan;
    $arrPotMandor[$rKemandoran->karyawanid]['potongan']=$rKemandoran->potongan;
    $arrPotMandor[$rKemandoran->karyawanid]['premi']=$rKemandoran->premiinput;
}

switch($proses)
{
        case'preview':
        if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
        $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
        $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
        $rOrg=mysql_fetch_assoc($qOrg);

        //periode gaji
        $bln=explode('-',$periode);
        $idBln=intval($bln[1]);	
          //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.npwp,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
               ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode where ".$where." ".$dtTipe."";
        //exit("Error".$sSlip);
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
                    $arrStatusPajak[$rSlip['karyawanid']]=$rSlip['statuspajak'];
                    $arrNpwp[$rSlip['karyawanid']]=$rSlip['npwp'];
                    $arrNik[$rSlip['karyawanid']]=$rSlip['nik'];
                    $arrNmKary[$rSlip['karyawanid']]=$rSlip['namakaryawan'];
                    $arrBag[$rSlip['karyawanid']]=$rSlip['bagian'];
                    $arrJbtn[$rSlip['karyawanid']]=$rSlip['namajabatan'];
                    $arrDept[$rSlip['karyawanid']]=$rSlip['nama'];
                    $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                    }
                }
                //array data komponen penambah dan pengurang
                $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='1' and id not in ('28','26') ";
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


                foreach($arrKary as $dtKary)
                {
                    if ($arrPotMandor[$dtKary]['potongan']!=''){
                            $dendaPnn[$dtKary]+=$arrPotMandor[$dtKary]['potongan'];
                    }

                    echo"<table cellspacing=1 border=0 width=500>
                    <tr><td> <h2><img src=".$path." width=60 height=35>&nbsp;".$_SESSION['org']['namaorganisasi']."</h2></td></tr>
                    <tr style='border-bottom:#000 solid 2px; border-top:#000 solid 2px;'><td valign=top>
                    <table border=0 width=110%>
                    <tr><td width=49% valign=top><table border=0>
                    <tr><td colspan=3>PAY SLYP/SLIP GAJI: ".$arrBln[$idBln]."-".$bln[0]."</td></tr>
                    <tr><td>NIP/TMK</td><td>:</td><td>".$arrNik[$dtKary]."/".tanggalnormal($arrTglMsk[$dtKary])."</td></tr>
                    <tr><td>NAMA</td><td>:</td><td>".$arrNmKary[$dtKary]."</td></tr>
                    </table></td><td width=51% valign=top>
                    <table border=0>
                    <tr><td colspan=3>&nbsp;</td></tr>
                    <tr><td>UNIT/BAGIAN</td><td>:</td><td>".$rOrg['namaorganisasi']."/".$arrBag[$dtKary]."</td></tr>
                    <tr><td>JABATAN</td><td>:</td><td>".$arrJbtn[$dtKary]."</td></tr>
                    </table></td></tr>
                    </table>
                    </td></tr>
                    <tr>
                    <td>
                    <table width=100%>
                    <thead>
                    <tr><td align=center>PENAMBAH</td><td align=center>PENGURANG</td>
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

                                    echo"</table>

                    </td>
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
     //   $this->lMargin=5;  
}

function Footer()
{
    $this->SetY(-15);
    $this->SetFont('Arial','I',5);
   // $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
}
}
$pdf=new PDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','',5);
//	$pdf->SetY(5);
//	$pdf->SetX(5);

//periode gaji
$bln=explode('-',$periode);
$idBln=intval($bln[1]);	
 //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
$sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.npwp,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
       ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
       left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
       left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode where ".$where."  ".$dtTipe."";	
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
            $arrStatusPajak[$rSlip['karyawanid']]=$rSlip['statuspajak'];
            $arrNpwp[$rSlip['karyawanid']]=$rSlip['npwp'];
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
  $sKomp2="select id,name,plus from ".$dbname.".sdm_ho_component where plus=0 ";
  $qKomp2=mysql_query($sKomp2) or die(mysql_error());
  while($rKomp2=mysql_fetch_assoc($qKomp2))
  {
      $arrIdKompPls[]=$rKomp2['id'];
      $arrNmKomPls[$rKomp2['id']][0]=$rKomp2['name'];
  }
  //komponen
    $arrMinusId=Array();
    $arrMinusName=Array();
     $str="select distinct(idkomponen) as id from ".$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b "
      . " on a.karyawanid=b.karyawanid where  ".$where2."  ".$dtTipe." and "
      . " idkomponen  in (select id from ".$dbname.".sdm_ho_component where plus='0' order by id)";
    
    $res=mysql_query($str,$conn);
    while($bar=mysql_fetch_object($res))
    {
        array_push($arrMinusId,$bar->id);
    }
    //samakan
    $arrPlusId=$arrMinusId;
    //Kosongkan
    for($r=0;$r<count($arrMinusId);$r++)
    {
         $arrPlusId[$r]='';
    }
    //sdm_gaji_vw
    $str="select distinct(idkomponen) as id from ".$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b "
      . " on a.karyawanid=b.karyawanid  where ".$where2."  ".$dtTipe." and "
      . " idkomponen  in (select  id from ".$dbname.".sdm_ho_component where plus='1'  and id not in ('26','28') order by id)";
    
    
    //echo $str;    
    $res=mysql_query($str,$conn);
    $n=-1;
    while($bar=mysql_fetch_object($res))
    {
        $n+=1;
        $arrPlusId[$n]=$bar->id;
        //$arrPlusName[$n]=$bar->name;
    }
    
    $whrnmh="plus='1'";
    $whrmin="plus='0'";
    $arrPlusName=makeOption($dbname,'sdm_ho_component','id,name',$whrnmh);
    $arrMinusName=makeOption($dbname,'sdm_ho_component','id,name',$whrmin);
    
    
    
   $arrValPlus=Array();
   $arrValMinus=Array();
   for($x=0;$x<count($arrPlusId);$x++)
   {
        $arrValPlus[$x]=0;
        $arrValMinus[$x]=0;
   }
   $str3="select jumlah,idkomponen,a.karyawanid,c.plus from ".$dbname.".sdm_gaji_vw a 
          left join ".$dbname.".sdm_ho_component c on a.idkomponen=c.id
         where a.sistemgaji='Harian' and a.periodegaji='".$periode."' group by a.karyawanid,idkomponen";
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

    $fontSize=7;
foreach($arrKary as $dtKary)
{
                if ($arrPotMandor[$dtKary]['potongan']!=''){
                        $dendaPnn[$dtKary]+=$arrPotMandor[$dtKary]['potongan'];
                }
                $awalX=$pdf->GetX();
			   $awalY=$pdf->GetY();
                //$pdf->Image('images/logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
                $pdf->SetX($pdf->getX()+10);
                $pdf->SetFont('Arial','B',$fontSize+1);	
                $pdf->Cell(75,6,$_SESSION['org']['namaorganisasi'],0,1,'L');
                $pdf->SetFont('Arial','',$fontSize);	
                $pdf->Cell(71,4,'PAY SLYP/SLIP GAJI : '.$arrBln[$idBln]."-".$bln[0],'T',0,'L');
                $pdf->SetFont('Arial','',$fontSize);
                        $pdf->Cell(25,4,'Printed on: '.date('d-m-Y: H:i:s'),"T",1,'R');
                $pdf->SetFont('Arial','',$fontSize);		
                $pdf->Cell(19,4,$_SESSION['lang']['nik']."/".$_SESSION['lang']['tmk'],0,0,'L');
                        $pdf->Cell(35,4,": ".$arrNik[$dtKary]."/".tanggalnormal($arrTglMsk[$dtKary]),0,0,'L');
                $pdf->Cell(13,4,$_SESSION['lang']['unit']."/".$_SESSION['lang']['bagian'],0,0,'L');	
                        $pdf->Cell(28,4,': '.$_SESSION['empl']['lokasitugas']." / ".$arrBag[$dtKary],0,1,'L');		
                $pdf->Cell(19,4,$_SESSION['lang']['namakaryawan'],0,0,'L');
                        $pdf->Cell(35,4,': '.$arrNmKary[$dtKary],0,0,'L');	
                $pdf->Cell(13,3,$_SESSION['lang']['jabatan'],0,0,'L');
                        $pdf->Cell(28,4,': '.$arrJbtn[$dtKary],0,1,'L');	
                $pdf->Cell(19,4,$_SESSION['lang']['statuspajak'],0,0,'L');
                        $pdf->Cell(35,4,': '.$arrStatusPajak[$dtKary],0,0,'L');	
                $pdf->Cell(13,3,$_SESSION['lang']['npwp'],0,0,'L');
                        $pdf->Cell(28,4,': '.$arrNpwp[$dtKary],0,1,'L');	
                        
                        
                $pdf->Cell(48,4,$_SESSION['lang']['penambah'],'TB',0,'C');
                $pdf->Cell(48,4,$_SESSION['lang']['pengurang'],'TB',1,'C');
               
                
                   // echo count($arrPlusId);
                
//                $counter=($dendaPnn[$dtKary]>0)?count($arrPlusId)+1:count($arrPlusId);
                for($mn=0;$mn<count($arrPlusId);$mn++)
                {
                        $pdf->Cell(25,4,$arrPlusName[$arrPlusId[$mn]],0,0,'L');
                        if($arrPlusName[$arrPlusId[$mn]]=='')
                        {
                          $pdf->Cell(5,4,"",0,0,'L');
                          $pdf->Cell(18,4,'','R',0,'R');
                        }
                        else
                        {
                            if($arrPlusName[$arrPlusId[$mn]]=='')
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
                        
                        
                        
                        $pdf->Cell(25,4,$arrMinusName[$arrMinusId[$mn]],0,0,'L');
                        if($arrMinusName[$arrMinusId[$mn]]=='')
                        {
                          $pdf->Cell(5,4,"",0,0,'L');
                          $pdf->Cell(18,4,'',0,1,'R');
                        }
                        else
                        {
                            if($arrMinusName[$arrMinusId[$mn]]=='')
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
                

                
                
                
                        $pdf->Cell(25,4,'Total.Pendapatan','TB',0,'L');
                        $pdf->Cell(5,4,":Rp.",'TB',0,'L');
                                $pdf->Cell(18,4,number_format(round($arrPlus[$dtKary])),'TB',0,'R');
                        $pdf->Cell(25,4,'Total.Pengurangan','TB',0,'L');
                        $pdf->Cell(5,4,":Rp.",'TB',0,'L');
                                $pdf->Cell(18,4,number_format(round(($arrMin[$dtKary]*-1))),'TB',1,'R');
//                if ($dendaPnn[$dtKary]>0){
//                            $pdf->Cell(25,4,'',0,0,'L');
//                            $pdf->Cell(5,4,"",0,0,'L');
//                            $pdf->Cell(18,4,'','R',0,'R');
//                            $pdf->Cell(25,4,'Po. Denda Panen',0,0,'L');
//                            $pdf->Cell(5,4,":Rp.",0,0,'L');
//                            $pdf->Cell(18,4,number_format(($dendaPnn[$dtKary]*-1),2,'.',','),0,1,'R');
//                            $arrMin[$dtKary]+=($dendaPnn[$dtKary]*-1);
//                }

                $pdf->SetFont('Arial','B',$fontSize);
                $pdf->Cell(23,4,'Gaji.Bersih',0,0,'L');
                $pdf->Cell(5,4,":Rp.",0,0,'L');
                        $pdf->Cell(18,4,number_format(round(($arrPlus[$dtKary]-($arrMin[$dtKary]*-1)))),0,0,'R');
                        if ($dendaPnn[$dtKary]>0){
                            $pdf->Cell(2,4,"",0,0,'L');
                            $pdf->SetFont('Arial','I',6);
                            $pdf->Cell(25,4,'* Denda Panen',0,0,'L');
                            $pdf->Cell(5,4,":Rp.",0,0,'L');
                            $pdf->Cell(18,4,number_format($dendaPnn[$dtKary],2,'.',','),0,1,'R');
                        } else {
                            $pdf->Cell(47,4,"",0,1,'L');
                        }
                        $terbilang=($arrPlus[$dtKary]-($arrMin[$dtKary]*-1));
                        $blng=terbilang(round($terbilang))." rupiah";
                $pdf->SetFont('Arial','',$fontSize);	
                $pdf->Cell(23,4,'Terbilang',0,0,'L');
                $pdf->Cell(5,4,":",0,0,'L');
                
				
                $pdf->MultiCell(70,4,$blng,0,'L');
                
                $akhirX=$pdf->getX();
                
                
                
                $pdf->setX($akhirX+60);
                $pdf->Cell(30,4,'Sebakis,...................... '.substr($period,0,4),0,1,'L');
                $pdf->Cell(23,4,'Dibayar Oleh',0,0,'L');
                $pdf->setX($akhirX+60);
                $pdf->Cell(23,4,'Diterima Oleh,',0,1,'L');
                
                 $pdf->Ln(20);	
                $pdf->setX($akhirX+60);
                 $pdf->Cell(23,4,$arrNmKary[$dtKary],'0',1,'L');
                 
                 
                $pdf->Cell(23,4,'Asst. Payroll','T',0,'L');
               $pdf->setX($akhirX+60);
                $pdf->Cell(23,4,'Karyawan','T',0,'L');
                
                
                
                
               
                
                
                
                
                
               // $pdf->SetFont('Arial','I',5);
               // $pdf->Cell(96,4,'Note: This is computer generated system, signature is not required','T',1,'L');	
              //  $pdf->SetFont('Arial','',6);	
                $pdf->Ln(40);	
                
				
                
               // $pdf->Rect($awalX-2,$awalY,100,100);
			   
			   
			   // $pdf->Rect($pdf->lMargin-10, $pdf->tMargin-10, $width+60, 300+$addHeight+$ketSumY);
                
                if($pdf->GetY()>225 and $pdf->col<1)
                        $pdf->AcceptPageBreak();
                if ($pdf->GetY()>225 and $pdf->col>0)
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
$pdf->Image('images/logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
$pdf->SetX($pdf->getX()+8);
$pdf->SetFont('Arial','B',$fontSize+1);	
$pdf->Cell(70,5,$_SESSION['org']['namaorganisasi'],0,1,'L');
$pdf->SetFont('Arial','',5);	
$pdf->Cell(60,3,'NOT FOUND','T',0,'L');
}
$pdf->Output();

break;



        case'excel':
        $sAbsen="select kodeabsen from ".$dbname.".sdm_5absensi order by kodeabsen";
        $qAbsen=mysql_query($sAbsen) or die(mysql_error());
        while($rKet=mysql_fetch_assoc($qAbsen))
        {
                $klmpkAbsn[]=$rKet;
        }
            $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
                   where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$periode."' 
                   and jenisgaji='H'";
            //exit("error:".$sTgl);
            $qTgl=mysql_query($sTgl) or die(mysql_error($conn));
            $rTgl=mysql_fetch_assoc($qTgl);

            $test = dates_inbetween($rTgl['tanggalmulai'], $rTgl['tanggalsampai']);
            ##tambahan absen permintaan dari pak ujang#
            $sAbsn="select absensi,tanggal,karyawanid from ".$dbname.".sdm_absensidt 
                            where tanggal like '".$periode."%' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'";
                         //exit("Error".$sAbsn);
                        $rAbsn=fetchData($sAbsn);
                        foreach ($rAbsn as $absnBrs =>$resAbsn)
                        {
                                if(!is_null($resAbsn['absensi']))
                                {
                                        $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][]=array(
                'absensi'=>$resAbsn['absensi']);
                                $resData[$resAbsn['karyawanid']][]=$resAbsn['karyawanid'];
                                }

                        }

                        $sKehadiran="select absensi,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                                     where tanggal like '".$periode."%' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'";
                        //exit("Error".$sKehadiran);
                        $rkehadiran=fetchData($sKehadiran);
                        foreach ($rkehadiran as $khdrnBrs =>$resKhdrn)
                        {	
                                if($resKhdrn['absensi']!='')
                                {
                                    $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][]=array(
                                    'absensi'=>$resKhdrn['absensi']);
                                    $resData[$resKhdrn['karyawanid']][]=$resKhdrn['karyawanid'];

                                }

                        }
                        $sPrestasi="select a.nik,b.tanggal from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                                    where b.notransaksi like '%PNN%' and b.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and b.tanggal like '".$periode."%'";
                        //exit("Error".$sPrestasi);
                        $rPrestasi=fetchData($sPrestasi);
                        foreach ($rPrestasi as $presBrs =>$resPres)
                        {
                            $hasilAbsn[$resPres['nik']][$resPres['tanggal']][]=array(
                            'absensi'=>'H');
                            $resData[$resPres['nik']][]=$resPres['nik'];
                        } 

        // ambil pengawas                        
        $dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
            where a.tanggal like '".$periode."%' and b.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and c.namakaryawan is not NULL
            union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
            where a.tanggal like '".$periode."%' and b.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and c.namakaryawan is not NULL";
        // exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres))
        {
            $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
            'absensi'=>'H');
            $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
        }
        // ambil administrasi                       
        $dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
            where a.tanggal like '".$periode."%' and b.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and c.namakaryawan is not NULL
            union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
            where a.tanggal like '".$periode."%' and b.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and c.namakaryawan is not NULL";
         //exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres))
        {
            $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
            'absensi'=>'H');
            $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
        }
        // ambil traksi                       
        $dzstr="SELECT a.tanggal,idkaryawan FROM ".$dbname.".vhc_runhk a
        left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
        where a.tanggal like '".$periode."%' and notransaksi like '%".$_SESSION['empl']['lokasitugas']."%'";
        //exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres))
        {
        $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][]=array(
        'absensi'=>'H');
        $resData[$dzbar->idkaryawan][]=$dzbar->idkaryawan;
        }      
        foreach($resData as $hslBrs => $hslAkhir)
        {	
            if($hslAkhir[0]!='')
            {
                foreach($test as $barisTgl =>$isiTgl)
                {
                        $brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']]+=1;
                }
            }	
        }
        #tambahan absen permintaan abis disini#

        $bln=explode('-',$perod);
        $idBln=intval($bln[1]);	

          //array data komponen penambah dan pengurang
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='1'  and id not in ('26','28') ";
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

                        $sPeriod="select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where jenisgaji='H' and periode='".$periode."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";	
                        $qPeriod=mysql_query($sPeriod) or die(mysql_error());
                        $rPeriod=mysql_fetch_assoc($qPeriod);
                        $mulai=tanggalnormal($rPeriod['tanggalmulai']);
                        $selesi=tanggalnormal($rPeriod['tanggalsampai']);

                        $stream.="
                        <table>
                        <tr><td colspan=15 align=center>List Data Gaji Harian, Unit : ".$_SESSION['empl']['lokasitugas']."</td></tr>
                        <tr><td colspan=15 align=center>Periode : ".$mulai." s.d. ".$selesi."</td></tr>
                        </table>
                        <table border=1>
                        <tr>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>No.</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['namakaryawan']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['nik']."/".$_SESSION['lang']['tmk']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['unit']."/".$_SESSION['lang']['bagian']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>No. Rekening</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['totLembur']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['tipekaryawan']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['statuspajak']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['npwp']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['jabatan']."</td>";
                        //absen di bayar
                        $shkdbyr="select distinct kodeabsen from ".$dbname.".sdm_5absensi where kelompok=1 order by kodeabsen";
                        $qhkdbyr=mysql_query($shkdbyr) or die(mysql_error($conn));
                        $rowabs=mysql_num_rows($qhkdbyr);
                        //absen tidak di bayar
                        $shkdbyr2="select distinct kodeabsen from ".$dbname.".sdm_5absensi where kelompok=0 order by kodeabsen";
                        $qhkdbyr2=mysql_query($shkdbyr2) or die(mysql_error($conn));
                        $rowabs2=mysql_num_rows($qhkdbyr2);

                        $stream.="<td bgcolor=#DEDEDE align=center  colspan='".($rowabs+1)."'>".$_SESSION['lang']['hkdibayar']."</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center colspan='".($rowabs2+1)."'>".$_SESSION['lang']['hktdkdibayar']."</td>";
                        $plsCol=count($arrIdKompPls);
                        $minCol=count($arrIdKompMin);
                        $stream.="<td bgcolor=#DEDEDE align=center colspan='".($plsCol+3)."'>".$_SESSION['lang']['penambah']."</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center colspan='".($minCol-1)."'>".$_SESSION['lang']['pengurang']."</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>GAJI BERSIH</td></tr><tr>";
                        while($rdbyr=mysql_fetch_assoc($qhkdbyr)){
                           $stream.="<td bgcolor=#DEDEDE align=center>".$rdbyr['kodeabsen']."</td>";
                            $dtAbsByr[]=$rdbyr['kodeabsen'];
                        }
                        $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."</td>";
                        while($rdbyr=mysql_fetch_assoc($qhkdbyr2)){
                            $stream.="<td bgcolor=#DEDEDE align=center>".$rdbyr['kodeabsen']."</td>";
                            $dtAbsTdkByr[]=$rdbyr['kodeabsen'];
                        }
                           $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."</td>";
                        foreach($arrIdKompPls as $lstKompPls)
                                {
                                    $brsPlus++;
                                    $stream.="<td bgcolor=#DEDEDE align=center>".$arrNmKomPls[$lstKompPls]."</td>";
                                    if($brsPlus==1)
                                    {
                                        $stream.="<td bgcolor=#DEDEDE align=center>".$arrNmKomMin[37]."</td>";
                                        $stream.="<td bgcolor=#DEDEDE align=center>".$arrNmKomMin[36]."</td>";
                                    }

                                }
                        $stream.="<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['totalPendapatan']."</td>";

                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td bgcolor=#DEDEDE align=center>".$arrNmKomMin[$lstKompMin]."</td>";
                                    }
                                }			

                      $stream.="<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['totalPotongan']."</td></tr>";

                         //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
         $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.npwp,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,
                b.norekeningbank from ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode where ".$where."  ".$dtTipe."";	
        // exit("Error:".$sSlip);
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
                    $arrNpwp[$rSlip['karyawanid']]=$rSlip['npwp'];
                    $arrDept[$rSlip['karyawanid']]=$rSlip['nama'];
                    $arrRek[$rSlip['karyawanid']]=$rSlip['norekeningbank'];
                    $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                    $arrTotal[$rSlip['idkomponen']]+=$rSlip['jumlah'];
                    }
                }
                $sTot="select tipelembur,jamaktual,karyawanid from ".$dbname.".sdm_lemburdt where substr(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' and tanggal between '".$rPeriod['tanggalmulai']."' and '".$rPeriod['tanggalsampai']."'";		
                $qTot=mysql_query($sTot) or die(mysql_error($conn));
                while($rTot=mysql_fetch_assoc($qTot))
                {
                        $sJum="select jamlembur as totalLembur from ".$dbname.".sdm_5lembur where tipelembur='".$rTot['tipelembur']."'
                        and jamaktual='".$rTot['jamaktual']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
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
                                <td>".$arrNik[$dtKary]."</td>
                                <td>".$arrDept[$dtKary]."</td>
                                <td>".$arrRek[$dtKary]."</td>
                                <td>".$jumTot[$dtKary]."</td>
                                <td>".$rNmTipe[$arrTipekary[$dtKary]]."</td> 
                                <td>".$arrStatPjk[$dtKary]."</td>
                                <td>".$arrNpwp[$dtKary]."</td>
                                <td>".$arrJbtn[$dtKary]."</td>";

                                foreach($dtAbsByr as $dtJmlhAbsDbyr){
                                    $stream.="<td align=right>".number_format($brt[$dtKary][$dtJmlhAbsDbyr])."</td>";
                                    $totAbsen[$dtKary]+=$brt[$dtKary][$dtJmlhAbsDbyr];
                                    $grTotDbyr[$dtJmlhAbsDbyr]+=$brt[$dtKary][$dtJmlhAbsDbyr];
                                }
                                $stream.="<td align=right>".number_format($totAbsen[$dtKary])."</td>";
                                foreach($dtAbsTdkByr as $dtTidakDbyr){
                                    $stream.="<td align=right>".number_format($brt[$dtKary][$dtTidakDbyr])."</td>";
                                    $totAbsenTdkDbyr[$dtKary]+=$brt[$dtKary][$dtTidakDbyr];
                                    $grTotTdkDbyr[$dtTidakDbyr]+=$brt[$dtKary][$dtTidakDbyr];
                                }
                                $stream.="<td align=right>".number_format($totAbsenTdkDbyr[$dtKary])."</td>";

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

                                        $stream.="<td>-".number_format($arrJmlh[$dtKary.$peng1],2)."</td>";
                                        $stream.="<td>-".number_format($arrJmlh[$dtKary.$peng2],2)."</td>";
                                    }

                                }

                                $totDpt=array_sum($arrPlus)-($arrJmlh[$dtKary.$peng1]+$arrJmlh[$dtKary.$peng2]);
                                $stream.="<td align=right>".number_format($totDpt,2)."</td>";


                                $arrMin=Array();
                                $q=0;
                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td align=right>".number_format($arrJmlh[$dtKary.$lstKompMin],2)."</td>";
                                         $arrMin[$q]=$arrJmlh[$dtKary.$lstKompMin];
                                         $q++;
                                    }
                                }
                                $gajiBersih=$totDpt-array_sum($arrMin);				

                                //$stream.="<td align=right>".number_format(array_sum($arrPlus),2)."</td>";
                                $stream.="<td align=right>".number_format(array_sum($arrMin),2)."</td>";
                                $stream.="<td align=right>".number_format($gajiBersih,0)."</td></tr>";	

                                }
                                $stream.="<tr><td colspan=".(10+$rowabs+$rowabs2+2)." align=right>".$_SESSION['lang']['total']."</td>";

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


                                $arrMin=Array();
                                $q=0;
                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td align=right>".number_format($arrTotal[$lstKompMin],2)."</td>";
                                         $arrMin[$q]=$arrTotal[$lstKompMin];
                                         $q++;
                                    }
                                }
                                $gajiBersih=$totDpt-array_sum($arrMin);				

                                //$stream.="<td align=right>".number_format(array_sum($arrPlus),2)."</td>";
                                $stream.="<td align=right>".number_format(array_sum($arrMin),2)."</td>";
                                $stream.="<td align=right>".number_format($gajiBersih,0)."</td>";	
                                $stream.="</tr>";
                }

                        //=================================================


                        $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];

                        $dte=date("YmdHms");
                        $nop_="GajiHarian".$_SESSION['empl']['lokasitugas'].$dte;
                         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                         gzwrite($gztralala, $stream);
                         gzclose($gztralala);
                         echo "<script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls.gz';
                            </script>";
        break;
        default:
        break;
}
?>