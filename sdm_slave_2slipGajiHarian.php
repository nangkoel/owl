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

// Cek Proses Pembulatan Gaji sudah dilakukan atau belum
if ($periode!=''){
    $str="select count(*) as hitung from ".$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
            WHERE b.tipekaryawan=".$tPkary." AND idkomponen=61 AND periodegaji='".$periode."' AND kodeorg='".$_SESSION['empl']['lokasitugas']."'";
} else {
    $str="select count(*) as hitung from ".$dbname.".sdm_gaji
            WHERE karyawanid='".$idKry."' AND idkomponen=61 AND periodegaji='".$period."'";
}
//echo $str;
$rCek=fetchData($str);
if ($rCek[0]['hitung']==0){
    exit("error: Proses Pembulatan Gaji belum dilakukan.");
}

$rNmTipe=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
$dtTipe="";
$arrBln=array(1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desembe");

if($periode!=''&&$kdBag!='')
{
    $where="a.sistemgaji='HARIAN' and a.periodegaji='".$periode."' and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'
            and b.bagian='".$kdBag."'";
}
elseif($periode!='')
{
    $where="a.sistemgaji='Harian' and a.periodegaji='".$periode."'  
            and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'";

}
else
{
    if($period!='')
    {
        $periode=$period;
    }
    $where="a.sistemgaji='Harian' and a.periodegaji='".$periode."' and a.karyawanid='".$idKry."'";
}
if($tPkary!='')
{
    $dtTipe=" and b.tipekaryawan='".$tPkary."'";
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
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
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
        $bln=explode('-',$periode);
        $idBln=intval($bln[1]);	
class PDF extends FPDF
{
var $col=0;
var $dbname;

        function Header()
        {    
                //$this->lMargin=5;  
        }

        /*function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }*/
}
        $pdf=new PDF('P','mm','letter');
        $pdf->AddPage();
        $pdf->SetFont('Arial','',5);
        //periode gaji
        $bln=explode('-',$periode);
        $idBln=intval($bln[1]);	

        //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,b.lokasitugas,b.statuspajak from 
               ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode where ".$where." ".$dtTipe."";
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
                    $arrLok[$rSlip['karyawanid']]=$rSlip['lokasitugas'];
                    $arrJbtn[$rSlip['karyawanid']]=$rSlip['namajabatan'];
                    $arrDept[$rSlip['karyawanid']]=$rSlip['nama'];
                    $arrStataPajak[$rSlip['karyawanid']]=$rSlip['statuspajak'];
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
            //$str="select id,name from ".$dbname.".sdm_ho_component where plus='0' order by id";
            $str="select distinct id,name from ".$dbname.".sdm_ho_component  a
                   left join ".$dbname.".sdm_gaji_vw b on a.id=b.idkomponen
                   where plus=0 and jumlah!=0 and b.periodegaji='".$periode."' 
                   and b.kodeorg='".$_SESSION['empl']['lokasitugas']."' order by id";
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
            //$str="select  id,name from ".$dbname.".sdm_ho_component where plus='1'  and id not in ('26','28') order by id";
            $str="select distinct id,name from ".$dbname.".sdm_ho_component  a
                   left join ".$dbname.".sdm_gaji_vw b on a.id=b.idkomponen
                   where plus=1 and jumlah!=0 and b.periodegaji='".$periode."' 
                   and b.kodeorg='".$_SESSION['empl']['lokasitugas']."' and id not in ('26','28') order by id";
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
        $prd=explode("-",$periode);
        if($prd[0]!=(date("Y"))){
            $prdlalu=($prd[0]-1)."-12";
        }else{
            $bln=strlen(($prd[1]-1))>1?($prd[1]-1):"0".($prd[1]-1);
            $prdlalu=$prd[0]."-".$bln;
        }
        #ambil cut off bulan lalu
        $sDt="select distinct tglcutoff from ".$dbname.".sdm_5periodegaji where periode='".$prdlalu."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
        $qDt=  mysql_query($sDt) or die(mysql_error($conn));
        $rDtLalu=  mysql_fetch_assoc($qDt);
        $tglCutblnlalu=$rDtLalu['tglcutoff'];
        $tglcutblnIni=nambahHari(tanggalnormal($tglCutblnlalu),1,1);//ditambahkan satu hari dari hari cut off untuk perhitungan lembur dan premi
        $tglKmrnini=explode("-",$tglcutblnIni); 
        
        #ambil cut off bulan ini
        $sDt="select distinct tglcutoff from ".$dbname.".sdm_5periodegaji where periode='".$periode."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
        //exit("error:".$sDt);
        $qDt=  mysql_query($sDt) or die(mysql_error($conn));
        $rDt=  mysql_fetch_assoc($qDt);
        $tglCutblnini=$rDt['tglcutoff'];
        $tglSmpini=explode("-",$tglCutblnini);
        foreach($arrKary as $dtKary){
        $no+=1;
                        $pdf->SetX($pdf->getX());
                        $pdf->SetFont('Arial','',10);	
                        $pdf->Cell(75,4,$_SESSION['org']['namaorganisasi'],0,1,'L');
                        $pdf->Cell(75,4,"SLIP TANDA TERIMA UPAH / GAJI BULAN ".strtoupper($arrBln[$idBln])." ".$tglSmpini[0],0,1,'L');
                        $pdf->Cell(75,4,"PERHITUNGAN PERIODE   LEMBUR / SATUAN   ".$tglKmrnini[2]."  ".strtoupper($arrBln[intval($tglKmrnini[1])])."  ".$tglKmrnini[0]." - ".$tglSmpini[2]."  ".strtoupper($arrBln[intval($tglSmpini[1])])."  ".$tglSmpini[0],0,1,'L');
                        $pdf->Cell(150,4,"================================================================================================",0,1,'L');
                        //$pdf->Cell(150,4,$_SESSION['lang']['slipGaji'].': '.$arrBln[$idBln]."-".$bln[0],'T',0,'L');	
                        $pdf->Cell(16,4,"NO.  ".$no,0,0,'L');
                        $pdf->Cell(16,4,"N I K",0,0,'L');
                        $pdf->Cell(25,4,$arrNik[$dtKary],0,0,'L');
                        $pdf->Cell(26,4," ",0,0,'L');
                        $pdf->Cell(25,4,$_SESSION['lang']['nama'],0,0,'L');
                        $pdf->Cell(60,4,strtoupper($arrNmKary[$dtKary]),0,0,'L');
						
						$pdf->Cell(25,4,$_SESSION['lang']['statuspajak'],0,0,'L');
						$pdf->Cell(16,4,$arrStataPajak[$dtKary],0,1,'L');
						
						
                        $pdf->Cell(16,4,'',0,0,'L');
                        $pdf->Cell(16,4,"U N I T",0,0,'L');
                        $whr="kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
                        $optNmOrg=  makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi',$whr);
                        $pdf->Cell(25,4,$_SESSION['empl']['lokasitugas']." - ".$optNmOrg[$_SESSION['empl']['lokasitugas']],0,0,'L');
                        $pdf->Cell(26,4,'',0,0,'L');
                        $pdf->Cell(25,4,$_SESSION['lang']['bagian'],0,0,'L');
                        $pdf->Cell(35,4,strtoupper($arrDept[$dtKary]),0,1,'L');
                        $pdf->Cell(150,4,"================================================================================================",'0',1,'L');
                       // $pdf->Cell(16,4,"STATUS ".$arrStataPajak[$dtKary],0,0,'L');
						$pdf->Cell(16,4,'',0,0,'L');
                         for($mn=0;$mn<count($arrPlusId);$mn++){	
                             if($mn!=0){
                                 $pdf->Cell(16,4," ",0,0,'L');
                             }
                             if($arrValPlus[$dtKary][$arrPlusId[$mn]]!=0){
                             $pdf->Cell(55,4,$arrPlusName[$mn],0,0,'L');
                             $pdf->Cell(5,4,":Rp.",0,0,'L');
                             $pdf->Cell(20,4,number_format($arrValPlus[$dtKary][$arrPlusId[$mn]],0,'.',','),'0',0,'R');
                             $arrPlus[$dtKary]+=$arrValPlus[$dtKary][$arrPlusId[$mn]];
                             }else{
                                if($arrPlusName[$mn]!=''){
                                 $pdf->Cell(55,4,$arrPlusName[$mn],0,0,'L');
                                 $pdf->Cell(5,4,":Rp",0,0,'L');
                                 $pdf->Cell(20,4,"0",'0',0,'R');
                                }else{
                                 $pdf->Cell(55,4,"",0,0,'L');
                                 $pdf->Cell(5,4,"",0,0,'L');
                                 $pdf->Cell(20,4,"",'0',0,'R');
                                }
                             }
                             
                             $pdf->Cell(15,4,'','0',0,'R');
                             if($arrValMinus[$dtKary][$arrMinusId[$mn]]!=0){
                                $pdf->Cell(55,4,$arrMinusName[$mn],0,0,'L');
                                $pdf->Cell(5,4,":Rp.",0,0,'L');
                                $pdf->Cell(20,4,number_format(($arrValMinus[$dtKary][$arrMinusId[$mn]]),0,'.',','),0,1,'R');
                                $arrMin[$dtKary]+=$arrValMinus[$dtKary][$arrMinusId[$mn]];
                             }else{
                                if($arrMinusName[$mn]!=''){
                                    $pdf->Cell(55,4,$arrMinusName[$mn],0,0,'L');
                                    $pdf->Cell(5,4,":Rp.",0,0,'L');
                                    $pdf->Cell(20,4,"0",0,1,'R');
                                }else{
                                    $pdf->Cell(55,4,"",0,0,'L');
                                    $pdf->Cell(5,4,"",0,0,'L');
                                    $pdf->Cell(20,4,"",0,1,'R');

                                }
                             }
                         }
                  $pdf->Cell(16,4," ",0,0,'L');
                  $pdf->Cell(55,4,"Total Brutto",0,0,'L');
                  $pdf->Cell(5,4,":Rp",0,0,'L');
                  $pdf->Cell(20,4,number_format($arrPlus[$dtKary],0,'.',','),'0',0,'R');
                  $pdf->Cell(15,4,'','0',0,'R');
                  $pdf->Cell(55,4,"Total Potongan",0,0,'L');
                  $pdf->Cell(5,4,":Rp",0,0,'L');
                  $pdf->Cell(20,4,number_format($arrMin[$dtKary],0,'.',','),0,1,'R');
                  $pdf->Cell(16,4,"",0,0,'L');
                  $pdf->Cell(55,4,"Jumlah Diterima",0,0,'L');
                  $pdf->Cell(5,4,":Rp",0,0,'L');
                  $pdf->Cell(20,4,number_format(($arrPlus[$dtKary]-($arrMin[$dtKary]))),'0',1,'R');
				  
				  $angkatot=$arrPlus[$dtKary]-$arrMin[$dtKary];
				  $numfor=number_format($angkatot);
				  
				  $pdf->Cell(16,4,"",0,0,'L'); 
				  $pdf->Cell(55,4,"Terbilang",0,0,'L');
				  $pdf->SetFont('Arial','IB',10);
				  
				//  $pdf->MultiCell(130,4,terbilang($angkatot,1).' RUPIAH',0,'L',0);
				   $pdf->cell(130,4,"# ".terbilang($angkatot,1)." RUPIAH  #",0,1,'L');
                //  $pdf->Cell(20,4,terbilang($angkatot,1).' RUPIAH','0',1,'L');
				  $pdf->SetFont('Arial','',10);
				  
				  
				  
                  /*$pdf->Cell(15,4,'','0',0,'R');
                  $pdf->Cell(55,4,"",0,0,'L');
                  $pdf->Cell(5,4,"",0,0,'L');
                  $pdf->Cell(20,4,"",0,1,'R');
                  $pdf->Ln(6);*/
                  $pdf->Cell(16,4," ",0,0,'L');
                  $pdf->Cell(55,4,"Pembayar,",0,0,'C');
                  $pdf->Cell(5,4,"",0,0,'L');
                  $pdf->Cell(20,4,"",'0',0,'R');
                  $pdf->Cell(15,4,'','0',0,'R');
                  $pdf->Cell(55,4,"Penerima,",0,0,'C');
                  $pdf->Cell(5,4,"",0,0,'L');
                  $pdf->Cell(20,4,"",0,1,'R');
                  $pdf->Ln(6);
                  $pdf->Cell(16,4," ",0,0,'L');
                  $pdf->Cell(57,4,"(                                                         )",0,0,'C');
                  $pdf->Cell(3,4,"",0,0,'L');
                  $pdf->Cell(20,4,"",'0',0,'R');
                  $pdf->Cell(15,4,'','0',0,'R');
                   $pdf->Cell(57,4,  strtoupper($arrNmKary[$dtKary]),0,0,'C');
                  $pdf->Cell(3,4,"",0,0,'L');
                  $pdf->Cell(20,4,"",0,1,'R');
                    
                  $akhirY=$pdf->GetY();
                  $pdf->SetY($akhirY+5);
                  
                   $barisMod=$no%2;
                   if($barisMod=='1')
                       $akhirY='132.00125';
                   else
                       $akhirY='259.00125';
                   
                   
                  
                   
                  // echo $akhirY."<br>";
                  
                  $pdf->SetY($akhirY+5);




                    //$akhirY=$pdf->GetY();
                  //$pdf->SetY($akhirY+18);
				  //$pdf->SetY($akhirY+25.69875);
                 // $selisihtinggi=$pdf->h-$akhirY;//error:165.39875___114.00125__279.4__
                  //exit("error:".$selisihtinggi."___".$akhirY."__".$pdf->h."__".$totTinggHal);
                    
   }
}
else
{
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
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['jabatan']."</td>";
                        //absen di bayar
                        $shkdbyr="select distinct kodeabsen from ".$dbname.".sdm_5absensi where kelompok=1 and kodeabsen!='MG' order by kodeabsen";
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
         $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,
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
                                         $stream.="<td align=right>".number_format($arrJmlh[$dtKary.$lstKompMin])."</td>";
                                         $arrMin[$q]=$arrJmlh[$dtKary.$lstKompMin];
                                         $q++;
                                    }
                                }
                                $gajiBersih=$totDpt-array_sum($arrMin);				

                                //$stream.="<td align=right>".number_format(array_sum($arrPlus),2)."</td>";
                                $stream.="<td align=right>".number_format(array_sum($arrMin),2)."</td>";
                                $stream.="<td align=right>".number_format($gajiBersih,0)."</td></tr>";	

                                }
                                $stream.="<tr><td colspan=".(9+$rowabs+$rowabs2+2)." align=right>".$_SESSION['lang']['total']."</td>";

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
                                         $stream.="<td align=right>".number_format($arrTotal[$lstKompMin])."</td>";
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