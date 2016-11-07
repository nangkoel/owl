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
$_POST['perod']==''?$perod=$_GET['perod']:$perod=$_POST['perod'];
$_POST['idKry']==''?$idKry=$_GET['idKry']:$idKry=$_POST['idKry'];
$_POST['tPkary2']==''?$tPkary=$_GET['tPkary2']:$tPkary=$_POST['tPkary2'];
$arrBln=array(1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember");
$_POST['idAfd']==''?$idAfd=$_GET['idAfd']:$idAfd=$_POST['idAfd'];
$rNmTipe=makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$lksiTgs=substr($idAfd,0,4);
$pt=makeOption($dbname, 'organisasi', 'kodeorganisasi,induk',"kodeorganisasi='".$lksiTgs."'");
$kdBag2=$_POST['kdBag2'];
 if($_SESSION['empl']['tipelokasitugas']=='HOLDING'||$_SESSION['empl']['tipelokasitugas']=='KANWIL')
                {
                    if($idAfd!='')
                    {
                     $add="b.lokasitugas='".$idAfd."' ";
                    }
                    else
                    {
                        exit("Error: Work unit required");
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
                    $add="b.lokasitugas='".$idAfd."' and (b.subbagian is null or b.subbagian='') ";
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
                
$sKemandoran="select karyawanid,jabatan,sum(potongan) as potongan from ".$dbname.".kebun_premikemandoran where periode='".$perod."' and kodeorg='".$lksiTgs."' group by karyawanid,jabatan";
$qKemandoran=mysql_query($sKemandoran) or die(mysql_error($conn));
while ($rKemandoran=mysql_fetch_object($qKemandoran)){
    $arrPotMandor[$rKemandoran->karyawanid]['jabatan']=$rKemandoran->jabatan;
    $arrPotMandor[$rKemandoran->karyawanid]['potongan']=$rKemandoran->potongan;
    $arrPotMandor[$rKemandoran->karyawanid]['premi']=$rKemandoran->premiinput;
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
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.npwp,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,e.kodeorg templok from 
               ".$dbname.".sdm_gaji_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
               left join ".$dbname.".setup_temp_lokasitugas e on e.karyawanid=b.karyawanid
               where b.sistemgaji='Harian' and a.periodegaji='".$perod."' and ".$add." ".$dtTipe."";
        //exit("Error".$sSlip);
        $qSlip=mysql_query($sSlip) or die(mysql_error());
        $rCek=mysql_num_rows($qSlip);
        if($rCek>0)
        {
                $trig=1;
                while($rSlip=mysql_fetch_assoc($qSlip))
                {
                    if($trig==1){
                        $trig=2;
                        #ambil karyawan yang pindah lokasi tugas
                        $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                            . "where a.kodeorg='".$idAfd."'";
                        $qcek=mysql_query($scek) or die(mysql_error($conn));
                        while($rcek=mysql_fetch_assoc($qcek)){
                            $sTemp="select distinct a.*,b.tipekaryawan,b.statuspajak,b.npwp,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
                            ".$dbname.".sdm_gaji_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
                            left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
                            left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
                            where a.karyawanid='".$rcek["karyawanid"]."'";
                            $qTemp=mysql_query($sTemp) or die(mysql_error());
                            while($rTemp=mysql_fetch_assoc($qTemp)){
                                if($rTemp['karyawanid']!='') {
                                $arrKary[$rTemp['karyawanid']]=$rTemp['karyawanid'];
                                $arrKomp[$rTemp['karyawanid']]=$rTemp['idkomponen'];
                                $arrTglMsk[$rTemp['karyawanid']]=$rTemp['tanggalmasuk'];
                                $arrNik[$rTemp['karyawanid']]=$rTemp['nik'];
                                $arrNmKary[$rTemp['karyawanid']]=$rTemp['namakaryawan'];
                                $arrBag[$rTemp['karyawanid']]=$rTemp['bagian'];
                                $arrJbtn[$rTemp['karyawanid']]=$rTemp['namajabatan'];
                                $arrDept[$rTemp['karyawanid']]=$rTemp['nama'];
                                $arrJmlh[$rTemp['karyawanid'].$rTemp['idkomponen']]=$rTemp['jumlah'];
                                }
                            }
                        } 
                    } // end cek
                    if($rSlip['karyawanid']!='') {
                        if ($rSlip['templok']=='' || $rSlip['templok']==$idAfd){
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
                }
          //array data komponen penambah dan pengurang
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='1'  and id not in ('26','28')  ";
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
                        if ($arrPotMandor[$dtKary]['potongan']!=''){
                                $dendaPnn[$dtKary]+=$arrPotMandor[$dtKary]['potongan'];
                        }

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
                echo" Not Found";
        }

        break;
        case'pdf':
        $perod=$_GET['perod'];
        $idAfd=$_GET['idAfd'];
        $idKry=$_GET['idKry'];
        $kdBag2=$_GET['kdBag2'];

//exit("Error:$perod._.$idAfd.__.$idKry.__.$kdBag2");


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
//                $this->lMargin=5;  
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',5);
//            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }
}
        $pdf=new PDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial','',5);
        //periode gaji
        $bln=explode('-',$perod);
        $idBln=intval($bln[1]);	

        //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.npwp,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
               ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
               where b.sistemgaji='Harian' and a.periodegaji='".$perod."' and ".$add."  ".$dtTipe." order by b.namakaryawan asc";
			   
			   //exit("Error:$sSlip");
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
      . " on a.karyawanid=b.karyawanid where  ".$add."  ".$dtTipe." and "
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
      . " on a.karyawanid=b.karyawanid  where ".$add."  ".$dtTipe." and "
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
                 where a.sistemgaji='Harian' and a.periodegaji='".$perod."' group by a.karyawanid,idkomponen";
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
                if ($arrPotMandor[$dtKary]['potongan']!=''){
                        $dendaPnn[$dtKary]+=$arrPotMandor[$dtKary]['potongan'];
                }
            
                   if (trim($arrNik[$dtKary])!=''){ 
                        if($pt[$lksiTgs]=='HIP'){$path='images/hip_logo.jpg';} else if($pt[$lksiTgs]=='SIL'){$path='images/sil_logo.jpg';} else if($pt[$lksiTgs]=='SIP'){$path='images/sip_logo.jpg';}
                        $pdf->Image($path,$pdf->GetX(),$pdf->GetY(),10);
                        $pdf->SetX($pdf->getX()+10);
                        $pdf->SetFont('Arial','B',8);	
                        $pdf->Cell(75,6,$_SESSION['org']['namaorganisasi'],0,1,'L');
                        $pdf->SetFont('Arial','',7);	
                        $pdf->Cell(71,4,$_SESSION['lang']['slipGaji'].': '.$arrBln[$idBln]."-".$bln[0],'T',0,'L');
                        $pdf->SetFont('Arial','',6);
                                $pdf->Cell(25,4,'Printed on: '.date('d-m-Y: H:i:s'),"T",1,'R');
                        $pdf->SetFont('Arial','',6);		
                        $pdf->Cell(15,4,$_SESSION['lang']['nik']."/".$_SESSION['lang']['tmk'],0,0,'L');
                                $pdf->Cell(35,4,": ".$arrNik[$dtKary]."/".tanggalnormal($arrTglMsk[$dtKary]),0,0,'L');
                        $pdf->Cell(18,4,$_SESSION['lang']['unit']."/".$_SESSION['lang']['bagian'],0,0,'L');	
                                $pdf->Cell(28,4,': '.$idAfd." / ".$arrBag[$dtKary],0,1,'L');		
                        $pdf->Cell(15,4,$_SESSION['lang']['namakaryawan'].":",0,0,'L');
                                $pdf->Cell(35,4,': '.$arrNmKary[$dtKary],0,0,'L');	
                        $pdf->Cell(18,3,$_SESSION['lang']['jabatan'],0,0,'L');
                                $pdf->Cell(28,4,':'.$arrJbtn[$dtKary],0,1,'L');	
                        $pdf->Cell(15,4,$_SESSION['lang']['statuspajak'].":",0,0,'L');
                                $pdf->Cell(35,4,': '.$arrStatusPajak[$dtKary],0,0,'L');	
                        $pdf->Cell(18,3,$_SESSION['lang']['npwp'],0,0,'L');
                                $pdf->Cell(28,4,':'.$arrNpwp[$dtKary],0,1,'L');	
                        $pdf->Cell(48,4,$_SESSION['lang']['penambah'],'TB',0,'C');
                        $pdf->Cell(48,4,$_SESSION['lang']['pengurang'],'TB',1,'C');


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

//                if ($dendaPnn[$dtKary]>0){
//                            $pdf->Cell(25,4,'',0,0,'L');
//                            $pdf->Cell(5,4,"",0,0,'L');
//                            $pdf->Cell(18,4,'','R',0,'R');
//                            $pdf->Cell(25,4,'Po. Denda Panen',0,0,'L');
//                            $pdf->Cell(5,4,":Rp.",0,0,'L');
//                            $pdf->Cell(18,4,number_format(($dendaPnn[$dtKary]*-1),2,'.',','),0,1,'R');
//                            $arrMin[$dtKary]+=($dendaPnn[$dtKary]*-1);
//                }
                
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
                                $blng=terbilang($terbilang,2)." rupiah";
                        $pdf->SetFont('Arial','',7);	
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
                $pdf->Ln(20);
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
}
else
{
        $pdf->Image('images/logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
        $pdf->SetX($pdf->getX()+8);
        $pdf->SetFont('Arial','B',8);	
        $pdf->Cell(70,5,$_SESSION['org']['namaorganisasi'],0,1,'L');
        $pdf->SetFont('Arial','',5);	
        $pdf->Cell(60,3,'NOT FOUND','T',0,'L');
}
        $pdf->Output();

        break;

        case'excel':

//

//

//            $mulai=tanggalnormal($rPeriod2['tglcutoff']);
//            $selesi=tanggalnormal($rPeriod['tglcutoff']);

//            //exit("error".$tglLemburawal."___".$rPeriod2['tglcutoff']);

//            $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
//                   where kodeorg='".substr($idAfd,0,4)."' and periode='".$perod."' 
//                   and jenisgaji='H'";
//            //exit("error:".$sTgl);
//            $qTgl=mysql_query($sTgl) or die(mysql_error($conn));
//            $rTgl=mysql_fetch_assoc($qTgl);
//
//            $test = dates_inbetween($tglLemburawal, $rPeriod2['tglcutoff']);
                $sAbsen="select kodeabsen from ".$dbname.".sdm_5absensi order by kodeabsen";
                $qAbsen=mysql_query($sAbsen) or die(mysql_error());
                while($rKet=mysql_fetch_assoc($qAbsen)){
                   $klmpkAbsn[]=$rKet;
                }
            if($tPkary==3){
                    $prd=explode("-",$perod);
                    if($prd[1]-1==0){
                        $periodeGjLalu=($prd[0]-1)."-12";
                    }else{
                        $bln=strlen(($prd[1]-1))>1?($prd[1]-1):"0".($prd[1]-1);
                        $periodeGjLalu=$prd[0]."-".$bln;
                    }
                    $sPeriod="select distinct tglcutoff from ".$dbname.".sdm_5periodegaji "
                           . "where jenisgaji='H' and periode='".$perod."' and kodeorg='".substr($idAfd,0,4)."'";	
                    $qPeriod=mysql_query($sPeriod) or die(mysql_error());//periode yang sekarang
                    $rPeriod=mysql_fetch_assoc($qPeriod);
                    $sPeriod2="select distinct tglcutoff from ".$dbname.".sdm_5periodegaji "
                            . "where jenisgaji='H' and periode='".$periodeGjLalu."' and kodeorg='".substr($idAfd,0,4)."'";	
                    $qPeriod2=mysql_query($sPeriod2) or die(mysql_error());//periode bulan lalu
                    $rPeriod2=mysql_fetch_assoc($qPeriod2);
                    $tglLemburawal=nambahHari($rPeriod2['tglcutoff'],1,1);//ditambahkan satu hari dari hari cut off untuk perhitungan lembur dan premi
                    $mulai=tanggalnormal($tglLemburawal);
                    $selesi=tanggalnormal($rPeriod['tglcutoff']);
                    $rTgl['tanggalmulai']=$tglLemburawal;
                    $rTgl['tanggalsampai']=$rPeriod['tglcutoff'];
                    $test = dates_inbetween($rTgl['tanggalmulai'], $rTgl['tanggalsampai']);
                    //exit("error:".$rTgl['tanggalmulai']."___".$rTgl['tanggalsampai']);
            }else{
                $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
                       where kodeorg='".substr($idAfd,0,4)."' and periode='".$perod."' 
                       and jenisgaji='H'";
                //exit("error:".$sTgl);
                $qTgl=mysql_query($sTgl) or die(mysql_error($conn));
                $rTgl=mysql_fetch_assoc($qTgl);
                $test = dates_inbetween($rTgl['tanggalmulai'], $rTgl['tanggalsampai']);
                $mulai=tanggalnormal($rTgl['tanggalmulai']);
                $selesi=tanggalnormal($rTgl['tanggalsampai']);
            }
            
            ##tambahan absen permintaan dari pak ujang#
            if($tPkary==3){
                $sAbsn="select absensi,tanggal,karyawanid from ".$dbname.".sdm_absensidt 
                                where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and substr(kodeorg,1,4) = '".substr($idAfd,0,4)."'";
            }else{
                $sAbsn="select absensi,tanggal,karyawanid from ".$dbname.".sdm_absensidt 
                                where tanggal like '".$perod."%' and substr(kodeorg,1,4) = '".substr($idAfd,0,4)."'";
            }
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
                if($tPkary==3){
                    $sKehadiran="select absensi,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                                 where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and substr(kodeorg,1,4) = '".substr($idAfd,0,4)."'";
                }else{
                    $sKehadiran="select absensi,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                                 where tanggal like '".$perod."%' and substr(kodeorg,1,4) = '".substr($idAfd,0,4)."'";
                }
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
        
                if($tPkary==3){
                    $sPrestasi="select a.nik,b.tanggal from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                                    where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and b.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."'";
                }else{
                        $sPrestasi="select a.nik,b.tanggal from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                                    where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and b.tanggal like '".$perod."%'";
                }
                        //exit("Error".$sPrestasi);
                        $rPrestasi=fetchData($sPrestasi);
                        foreach ($rPrestasi as $presBrs =>$resPres)
                        {
                            $hasilAbsn[$resPres['nik']][$resPres['tanggal']][]=array(
                            'absensi'=>'H');
                            $resData[$resPres['nik']][]=$resPres['nik'];
                        } 

        // ambil pengawas                        
               if($tPkary==3){
                $dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
                    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
                    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
                    where a.tanggal like '".$perod."%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL
                    union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
                    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
                    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
                    where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL";
               }else{
                   $dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
                        left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
                        left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
                        where a.tanggal like '".$perod."%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL
                        union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
                        left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
                        left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
                        where a.tanggal like '".$perod."%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL";
               }
        // exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres))
        {
            $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
            'absensi'=>'H');
            $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
        }
        // ambil administrasi      
        if($tPkary==3){
        $dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
            where a.tanggal like '".$perod."%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL
            union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
            where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL";
        }else{
      $dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
            where a.tanggal like '".$perod."%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL
            union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
            where a.tanggal like '".$perod."%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL";
        }
         //exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres))
        {
            $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
            'absensi'=>'H');
            $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
        }
        // ambil traksi                
        if($tPkary==3){
        $dzstr="SELECT a.tanggal,idkaryawan FROM ".$dbname.".vhc_runhk a
                left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and notransaksi like '%".substr($idAfd,0,4)."%'";
        }else{
             $dzstr="SELECT a.tanggal,idkaryawan FROM ".$dbname.".vhc_runhk a
                    left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                    where a.tanggal like '".$perod."%' and notransaksi like '%".substr($idAfd,0,4)."%'";
        }
        //exit("Error".$dzstr);
        $dzres=mysql_query($dzstr) or die(mysql_error());
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
//        echo"<pre>";
//        print_r($brt);
//        echo"</pre>";
//        exit("error");
        //periode gaji
        $bln=explode('-',$perod);
        $idBln=intval($bln[1]);	

          //array data komponen penambah dan pengurang
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='1'  and id not in ('26','28') ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $sfilter="select distinct idkomponen from ".$dbname.".sdm_gaji_vw "
                     . "where periodegaji='".$perod."' and kodeorg='".substr($idAfd,0,4)."' and idkomponen='".$rKomp['id']."'";
               //exit("error".$sfilter);
              $qfilter=  mysql_query($sfilter) or die(mysql_error($conn));
              $rfilter=  mysql_fetch_assoc($qfilter);
              if($rfilter['idkomponen']!=''){
                $arrIdKompPls[]=$rKomp['id'];
                $arrNmKomPls[$rKomp['id']]=$rKomp['name'];
              }
          }
          $totPlus=count($arrIdKompPls);
          $brsPlus=0;
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='0' and id<>'61'  ";
          //$sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='0'  ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $sfilter="select distinct idkomponen from ".$dbname.".sdm_gaji_vw "
                     . "where periodegaji='".$perod."' and kodeorg='".substr($idAfd,0,4)."' and idkomponen='".$rKomp['id']."'";
               //exit("error".$sfilter);
              $qfilter=  mysql_query($sfilter) or die(mysql_error($conn));
              $rfilter=  mysql_fetch_assoc($qfilter);
              if($rfilter['idkomponen']!=''){
                $arrIdKompMin[]=$rKomp['id'];
                $arrNmKomMin[$rKomp['id']]=$rKomp['name'];
              }
          }
                        
                        $mulai=tanggalnormal($tglLemburawal);
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
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['npwp']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['jabatan']."</td>";
                                //absen di bayar
                        $shkdbyr="select distinct kodeabsen from ".$dbname.".sdm_5absensi where kelompok=1 and kodeabsen!='MG'  order by kodeabsen";
                        $qhkdbyr=mysql_query($shkdbyr) or die(mysql_error($conn));
                        $rowabs=mysql_num_rows($qhkdbyr);
                        //absen tidak di bayar
                        $shkdbyr2="select distinct kodeabsen from ".$dbname.".sdm_5absensi where kelompok=0 or kodeabsen='MG' order by kodeabsen";
                        $qhkdbyr2=mysql_query($shkdbyr2) or die(mysql_error($conn));
                        $rowabs2=mysql_num_rows($qhkdbyr2);

                        $stream.="<td bgcolor=#DEDEDE align=center  colspan='".($rowabs+1)."'>".$_SESSION['lang']['hkdibayar']."</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center colspan='".($rowabs2+1)."'>".$_SESSION['lang']['hktdkdibayar']."</td>";
                        $plsCol=count($arrIdKompPls);
                        $minCol=count($arrIdKompMin);
                        $stream.="<td bgcolor=#DEDEDE align=center colspan='".($plsCol+2)."'>".$_SESSION['lang']['penambah']."</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center colspan='".($minCol)."'>".$_SESSION['lang']['pengurang']."</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>GAJI NETTO</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>GAJI DITERIMA</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>SIMPANAN</td>";
                        $stream.="</tr><tr>";
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
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.npwp,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,b.subbagian,
               b.norekeningbank,e.kodeorg templok from
               ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
               left join ".$dbname.".setup_temp_lokasitugas e on e.karyawanid=b.karyawanid
               where b.sistemgaji='Harian' and a.periodegaji='".$perod."'  and ".$add." ".$dtTipe."";
        $qSlip=mysql_query($sSlip) or die(mysql_error());
        $rCek=mysql_num_rows($qSlip);
        if($rCek>0)
        {
                $trig=1;
                while($rSlip=mysql_fetch_assoc($qSlip))
                {
                    if($trig==1){
                        $trig=2;
                        #ambil karyawan yang pindah lokasi tugas
                        $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                            . "where a.kodeorg='".$idAfd."'";
                        $qcek=mysql_query($scek) or die(mysql_error($conn));
                        while($rcek=mysql_fetch_assoc($qcek)){
                            $sTemp="select distinct a.*,b.tipekaryawan,b.statuspajak,b.npwp,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
                            ".$dbname.".sdm_gaji_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
                            left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
                            left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
                            where a.karyawanid='".$rcek["karyawanid"]."' and a.periodegaji='".$perod."'";
                            $qTemp=mysql_query($sTemp) or die(mysql_error());
                            while($rTemp=mysql_fetch_assoc($qTemp)){
                                if($rTemp['karyawanid']!='') {
                                $arrKary[$rTemp['karyawanid']]=$rTemp['karyawanid'];
                                $arrKomp[$rTemp['karyawanid']]=$rTemp['idkomponen'];
                                $arrTglMsk[$rTemp['karyawanid']]=$rTemp['tanggalmasuk'];
                                $arrNik[$rTemp['karyawanid']]=$rTemp['nik'];
                                $arrNmKary[$rTemp['karyawanid']]=$rTemp['namakaryawan'];
                                $arrBag[$rTemp['karyawanid']]=$rTemp['bagian'];
                                $arrJbtn[$rTemp['karyawanid']]=$rTemp['namajabatan'];
                                $arrTipekary[$rTemp['karyawanid']]=$rTemp['tipekaryawan'];
                                $arrStatPjk[$rTemp['karyawanid']]=$rTemp['statuspajak'];
                                $arrNpwp[$rTemp['karyawanid']]=$rTemp['npwp'];
                                $arrDept[$rTemp['karyawanid']]=$rTemp['nama'];
                                $arrSubbagian[$rTemp['karyawanid']]=$rTemp['subbagian'];
                                $arrRek[$rTemp['karyawanid']]=$rTemp['norekeningbank'];
                                $arrJmlh[$rTemp['karyawanid'].$rTemp['idkomponen']]=$rTemp['jumlah'];
                                $arrTotal[$rTemp['idkomponen']]+=$rTemp['jumlah'];
                                }
                            }
                        } 
                    } // end cek
                    if($rSlip['karyawanid']!='') {
                        if ($rSlip['templok']=='' || $rSlip['templok']==$idAfd){
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
                            $arrSubbagian[$rSlip['karyawanid']]=$rSlip['subbagian'];
                            $arrRek[$rSlip['karyawanid']]=$rSlip['norekeningbank'];
                            $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                            $arrTotal[$rSlip['idkomponen']]+=$rSlip['jumlah'];
                        }
                    }
                }
                $sTot="select tipelembur,jamaktual,karyawanid from ".$dbname.".sdm_lemburdt where substr(kodeorg,1,4)='".substr($idAfd,0,4)."' "
                     . "and tanggal between '".$tglLemburawal."' and '".$rPeriod['tglcutoff']."'";		
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
                        if ($arrPotMandor[$dtKary]['potongan']!=''){
                                $dendaPnn[$dtKary]+=$arrPotMandor[$dtKary]['potongan'];
                        }
                        
                        $no+=1;
                                $stream.="<tr class=rowcontent>
                                <td>".$no."</td>
                                <td>".$arrNmKary[$dtKary]."</td>";
                                $stream.="<td>'".$arrNik[$dtKary]."</td>";
                                $ocldt=9;
                                if($_SESSION['empl']['tipelokasitugas']=='HOLDING'||$_SESSION['empl']['tipelokasitugas']=='KANWIL')
                                {
                                    $ocldt=10;
                                    $stream.="<td>".$arrSubbagian[$dtKary]."</td>";
                                }
                                $stream.="
                                <td>".$arrRek[$dtKary]."</td>
                                <td>".$rNmTipe[$arrTipekary[$dtKary]]."</td>
                                <td>".$jumTot[$dtKary]."</td>
                                <td>".$arrDept[$dtKary]."</td> 
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

                                    $stream.="<td align=right>".number_format(intval($arrJmlh[$dtKary.$lstKompPls]),0)."</td>";
                                    $arrPlus[$s]=$arrJmlh[$dtKary.$lstKompPls];
                                    $s++;
                                    $brsPlus2++;
                                    if($brsPlus2==1){
                                        $stream.="<td>-".number_format(intval($arrJmlh[$dtKary.$peng1]),0)."</td>";
                                        //$stream.="<td>-".number_format($arrJmlh[$dtKary.$peng2],2)."</td>";
                                    }

                                }

                                $totDpt=array_sum($arrPlus)-($arrJmlh[$dtKary.$peng1]+$arrJmlh[$dtKary.$peng2]);
                                $stream.="<td align=right>".number_format(intval($totDpt),0)."</td>";


                                $arrMin=Array();
                                $q=0;
                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td align=right>".number_format(intval($arrJmlh[$dtKary.$lstKompMin]))."</td>";
                                         $arrMin[$q]=$arrJmlh[$dtKary.$lstKompMin];
                                         $q++;
                                    }
                                }
                                $drkompak="61";
                                $gajiBersih=$totDpt-array_sum($arrMin);	
                                $hslmod=$gajiBersih%1000;
                                $jmlhdtrima=$gajiBersih-$arrJmlh[$dtKary.$drkompak];
                                //$stream.="<td align=right>".number_format(array_sum($arrPlus),2)."</td>";
                                $stream.="<td align=right>".number_format(array_sum($arrMin),0)."</td>";
                                $stream.="<td align=right>".number_format($gajiBersih,0)."</td>";	
                                $stream.="<td align=right>".number_format($jmlhdtrima,0)."</td>";
                                $stream.="<td align=right>".number_format($arrJmlh[$dtKary.$drkompak],0)."</td></tr>";
                                $totdtrima+=round($jmlhdtrima,0);
                                $totSimpanan+=round($arrJmlh[$dtKary.$drkompak],0);
                      }
                                $stream.="<tr><td colspan=".($ocldt+$rowabs+$rowabs2+3)." align=right>".$_SESSION['lang']['total']."</td>";

                                $s=0;
                                $brsPlus2=0;
                                $arrPlus=array();
                                foreach($arrIdKompPls as $lstKompPls)
                                {
                                    $stream.="<td align=right>".number_format(intval($arrTotal[$lstKompPls]),0)."</td>";
                                    $arrPlus[$s]=$arrTotal[$lstKompPls];
                                    $s++;
                                    $brsPlus2++;
                                    if($brsPlus2==1)
                                    {
                                        $stream.="<td>-".number_format(intval($arrTotal[$peng1]),0)."</td>";
                                        //$stream.="<td>-".number_format(intval($arrTotal[$peng2]),0)."</td>";
                                    }
                                }
                                $totDpt=array_sum($arrPlus)-($arrTotal[$peng1]+$arrTotal[$peng2]);
                                $stream.="<td align=right>".number_format(intval($totDpt),0)."</td>";

                                $arrMin=Array();
                                $q=0;
                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td align=right>".number_format(intval($arrTotal[$lstKompMin]))."</td>";
                                         $arrMin[$q]=$arrTotal[$lstKompMin];
                                         $q++;
                                    }
                                }
                                
                                $gajiBersih=$totDpt-array_sum($arrMin);				
                                
                                //$stream.="<td align=right>".number_format(array_sum($arrPlus),2)."</td>";
                                $stream.="<td align=right>".number_format(intval(array_sum($arrMin)),0)."</td>";
                                $stream.="<td align=right>".number_format(intval($gajiBersih),0)."</td>";	
                                $stream.="<td align=right>".number_format(intval($totdtrima),0)."</td>";	
                                $stream.="<td align=right>".number_format(intval($totSimpanan),0)."</td>";	
                                	
                                $stream.="</tr>";
                }
                else
                {
                    $stream.="<tr><td colspan=20>&nbsp;</td></tr>";
                }

                        //echo "warning:".$strx;
                        //=================================================


                        $stream.="</table>Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
                        //echo $stream;exit();
                        $dte=date("YmdHms");
                        $nop_="GajiHarianAfdeling_".$_SESSION['empl']['lokasitugas'].$dte;
                         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                         gzwrite($gztralala, $stream);
                         gzclose($gztralala);
                         echo "<script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls.gz';
                            </script>";

        break;
         case'getPeriode':
            $optPeriode="<option value''>".$_SESSION['lang']['pilihdata']."</option>";
            $sPeriode="select periode from ".$dbname.".sdm_5periodegaji where kodeorg='".substr($idAfd,1,4)."' and jenisgaji='H'";
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