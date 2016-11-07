 <?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}


$optNmOrg=makeOption($dbname, 'organisasi','kodeorganisasi,namaorganisasi');
$statuspajak=makeOption($dbname, 'datakaryawan','karyawanid,statuspajak');
$npwp=makeOption($dbname, 'datakaryawan','karyawanid,npwp');
//$statuspajak[$dPph['karyawanid']]=$dPph['karyawanid'];

$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];//
$_POST['periodeGaji']==''?$periodeGaji=$_GET['periodeGaji']:$periodeGaji=$_POST['periodeGaji'];
$_POST['tpKary']==''?$tpKary=$_GET['tpKary']:$tpKary=$_POST['tpKary'];

//$prdlalu=explode("-",$periodeGaji);
//if($prdlalu[0]!=date("Y")){
//    $periodeGjLalu=($prdlalu[0]-1)."-12";
//}else{
//    $bln=strlen(($prdlalu[1]-1))>1?($prdlalu[1]-1):"0".($prdlalu[1]-1);
//    $periodeGjLalu=$prdlalu[0]."-".$bln;
//}
$arrPer=explode("-",$periodeGaji);
if (($arrPer[1]-1)==0) {
    $periodeGjLalu=($arrPer[0]-1)."-12";
} else {
    $periodeGjLalu=$arrPer[0]."-".($arrPer[1]-1);
    if (strlen($periodeGjLalu)==6)
        $periodeGjLalu=$arrPer[0]."-0".($arrPer[1]-1);
}

$arr="##kdOrg##periodeGaji";
if($proses=='preview'||$proses=='excel')
{


$brdr=0;
$bgcoloraja='';
if($proses=='excel'){
    $brdr=1;
    $bgcoloraja='#77ff77';
}
if($tpKary!=''){
    $wdt=" and karyawanid in (select distinct karyawanid from ".$dbname.".datakaryawan where tipekaryawan='".$tpKary."' "
            . " and lokasitugas='".$kdOrg."' and tanggalkeluar='0000-00-00')";
}
            $optNmTipeKar=  makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
      
            #ngambil komponen simpanan di bulan lalu di jadikan penambah di bulan ini
            $sGaji="select * from ".$dbname.".sdm_gaji where "
                 . "periodegaji='".$periodeGjLalu."' and kodeorg='".$kdOrg."' and idkomponen='61' ".$wdt."";
            
           // exit("Error:$sGaji");
            //echo $sGaji;
            $qGaji=  mysql_query($sGaji) or die(mysql_error($conn));
            while($rGaji=  mysql_fetch_assoc($qGaji)){
                $lstGaji[$rGaji['karyawanid']]=$rGaji['jumlah'];
                $dtKary[$rGaji['karyawanid']]=$rGaji['karyawanid'];
            }
            #ngambil penambah dan pengurang
            $sGaji2="select jumlah,karyawanid,idkomponen from ".$dbname.".sdm_gaji where "
                 . "periodegaji='".$periodeGaji."' and kodeorg='".$kdOrg."' "
                 . "and idkomponen in (select id from ".$dbname.".sdm_ho_component where plus=1) and idkomponen not in ('60','71','28')  ".$wdt."";
            //echo $sGaji2;
            $qGaji2=  mysql_query($sGaji2) or die(mysql_error($conn));
            while($rGaji2=  mysql_fetch_assoc($qGaji2)){                
                $lstGajiPembh[$rGaji2['karyawanid']]+=$rGaji2['jumlah'];
                $dtKary[$rGaji2['karyawanid']]=$rGaji2['karyawanid'];
            }
            $sGaji="select jumlah,karyawanid,idkomponen from ".$dbname.".sdm_gaji where "
                 . "periodegaji='".$periodeGaji."' and kodeorg='".$kdOrg."' "
                 . "and idkomponen in (select id from ".$dbname.".sdm_ho_component where plus=0) and idkomponen!='61'  ".$wdt."";
            $qGaji=  mysql_query($sGaji) or die(mysql_error($conn));
            while($rGaji=  mysql_fetch_assoc($qGaji)){                
                $lstGajiPeng[$rGaji['karyawanid']]+=$rGaji['jumlah'];
                $dtKary[$rGaji['karyawanid']]=$rGaji['karyawanid'];
            }
            
            
            
            
            ##======PPh21=================================================================
            ##ambil total komponen yang terkena bruto
           /*
            $iPph="select jumlah,karyawanid,idkomponen from ".$dbname.".sdm_gaji where "
             . "periodegaji='".$periodeGaji."' and kodeorg='".$kdOrg."' "
             . "and idkomponen in (select id from ".$dbname.".sdm_5komponenpph "
                . " where status='1' and regional='".$_SESSION['empl']['regional']."') ".$wdt."";
            
            $nPph=  mysql_query($iPph) or die(mysql_error($conn));
            while($dPph=  mysql_fetch_assoc($nPph)){                
                $lstPph[$dPph['karyawanid']]+=$dPph['jumlah'];
                $dtKary[$dPph['karyawanid']]=$dPph['karyawanid'];
            }
            
           
            
            
            $iGp="select jumlah,karyawanid,idkomponen from ".$dbname.".sdm_5gajipokok where "
             . "tahun='".substr($periodeGaji,0,4)."'  "
             . "and idkomponen='1' ".$wdt."";
            $nGp=  mysql_query($iGp) or die(mysql_error($conn));
            while($dGp=  mysql_fetch_assoc($nGp)){       
                $tjms[$dGp['karyawanid']]=$dGp['jumlah'];
            }
            
            
            //ambil biaya jabatan    
            $jabPersen=0;
            $jabMax=0;
            $str="select persen,max from ".$dbname.".sdm_ho_pph21jabatan";
            $res=mysql_query($str);        
            while($bar=mysql_fetch_object($res))
            {
                $jabPersen=$bar->persen/100;
                $jabMax=$bar->max*12;
            }    

            //Ambil PTKP:
            $ptkp=Array();
            $str="select id,value from ".$dbname.".sdm_ho_pph21_ptkp";
            $res=mysql_query($str);        
            while($bar=mysql_fetch_object($res))
            {
                $ptkp[$bar->id]=$bar->value;
            } 

            //ambil tarif pph21
            $pphtarif=Array();  
            $pphpercent=Array();  
            $str="select level,percent,upto from ".$dbname.".sdm_ho_pph21_kontribusi order by level";
            $res=mysql_query($str);    
            $urut=0;
            while($bar=mysql_fetch_object($res))
            {
                $pphtarif[$urut]    =$bar->upto;
                $pphpercent[$urut]  =$bar->percent/100;      
                $urut+=1;  
            } 

            $ijkk="select persen from ".$dbname.".sdm_ho_pph21jaminan where regional='".$_SESSION['empl']['regional']."' and tipe='jkk' ";
            $njkk=mysql_query($ijkk) or die (mysql_error($conn))."____".$ijkk;
            $djkk=mysql_fetch_assoc($njkk);
                     $jkk=$djkk['persen'];

            $ijht="select persen from ".$dbname.".sdm_ho_pph21jaminan where regional='".$_SESSION['empl']['regional']."' and tipe='jht' ";
            $njht=mysql_query($ijht) or die (mysql_error($conn))."____".$ijht;
            $djht=mysql_fetch_assoc($njht);
                     $jht=$djht['persen'];
                     
            if($_SESSION['empl']['regional']=='SULAWESI')
            {   
            
             foreach($lstPph as $xid =>$jlh){
                 
                 $penghasilanSetahun[$xid]=($jlh*12)+($jkk/100*$jlh*12);
                 
                 $biayaJab[$xid]=$penghasilanSetahun[$xid]*$jabPersen;
                 if($biayaJab[$xid]>$jabMax){#jika lebih dari max maka dibatasi sebesar max
                     $biayaJab[$xid]=$jabMax;
                 }
                 $penghasilanKurangJab[$xid]=$penghasilanSetahun[$xid]-$biayaJab[$xid]-($jht/100*$jlh*12);
                 
                 $pkp[$xid]=$penghasilanKurangJab[$xid]-$ptkp[str_replace("K","",$statuspajak[$xid])]; //$satatuspajak ambil dr array pertama di atas	
                 $zz=0;
                  $sisazz=0;
                  
             
                  
                  if($pkp[$xid]>0){         
                      if($pkp[$xid]<$pphtarif[0])
                      {
                          $zz+=$pphpercent[0]*$pkp[$xid];
                          $sisazz=0;
                      }
                      else if($pkp[$xid]>=$pphtarif[0])
                      {
                          $zz+=$pphpercent[0]*$pphtarif[0];
                          $sisazz=$pkp[$xid]-$pphtarif[0];
                          
                              if($sisazz<($pphtarif[1]-$pphtarif[0]))
                              {
                                  $zz+=$pphpercent[1]*$sisazz;
                                  $sisazz=0;        
                              }    
                              else if($sisazz>=($pphtarif[1]-$pphtarif[0]))
                              {
                                  $zz+=$pphpercent[1]*($pphtarif[1]-$pphtarif[0]);
                                  $sisazz=$pkp[$xid]-$pphtarif[1]; 
                                  
                                      if($sisazz<($pphtarif[2]-$pphtarif[1]))
                                      {
                                          $zz+=$pphpercent[2]*$sisazz;
                                          $sisazz=0;        
                                      }    
                                      else if($sisazz>=($pphtarif[2]-$pphtarif[1]))
                                      {
                                          $zz+=$pphpercent[2]*($pphtarif[2]-$pphtarif[1]);
                                          $sisazz=$pkp[$xid]-$pphtarif[2];
                                          
                                              if($sisazz>0){
                                             
                                                  $zz+=$pphpercent[3]*$sisazz;  
                                              }                          
                                      } 
                              }   

                      }
                  }
        
                      $pphSetahun[$xid]=$zz/12;
                     
                      if($npwp[$xid]==''){
                          $pphSetahun[$xid]=$pphSetahun[$xid]+($pphSetahun[$xid]*20/100);
                      }
                 }
            }
            else
            {
             foreach($lstPph as $xid =>$jlh){

             
                    
                 $brutoPenghasilan[$xid]=$jlh+($jkk/100*$tjms[$xid]);
                 if($tpKary=='3')
                 {   
                     $biayaJab[$xid]=$brutoPenghasilan[$xid]*$jabPersen;
                 }
                 else
                 {
                     $biayaJab[$xid]=0;
                 }

                 if($biayaJab[$xid]>$jabMax){
                     $biayaJab[$xid]=$jabMax;
                 }
                 $netto[$xid]=($brutoPenghasilan[$xid]-$biayaJab[$xid]-($jht/100*$tjms[$xid]))*12;

                 $pkp[$xid]=$netto[$xid]-$ptkp[str_replace("K","",$statuspajak[$xid])]; //$satatuspajak ambil dr array pertama di atas	

                 $pkp[$xid]=1000*(floor($pkp[$xid]/1000));

                 
                 $zz=0;
                  $sisazz=0;
                  if($pkp[$xid]>0){         
                  
                      if($pkp[$xid]<$pphtarif[0])
                      {
                          $zz+=$pphpercent[0]*$pkp[$xid];
                          $sisazz=0;
                      }
                      else if($pkp[$xid]>=$pphtarif[0])
                      {
                          $zz+=$pphpercent[0]*$pphtarif[0];
                          $sisazz=$pkp[$xid]-$pphtarif[0];
                         
                              if($sisazz<($pphtarif[1]-$pphtarif[0]))
                              {
                                  $zz+=$pphpercent[1]*$sisazz;
                                  $sisazz=0;        
                              }    
                              else if($sisazz>=($pphtarif[1]-$pphtarif[0]))
                              {
                                  $zz+=$pphpercent[1]*($pphtarif[1]-$pphtarif[0]);
                                  $sisazz=$pkp[$xid]-$pphtarif[1]; 
                                     
                                      if($sisazz<($pphtarif[2]-$pphtarif[1]))
                                      {
                                          $zz+=$pphpercent[2]*$sisazz;
                                          $sisazz=0;        
                                      }    
                                      else if($sisazz>=($pphtarif[2]-$pphtarif[1]))
                                      {
                                          $zz+=$pphpercent[2]*($pphtarif[2]-$pphtarif[1]);
                                          $sisazz=$pkp[$xid]-$pphtarif[2];
                                              if($sisazz>0){
                                                  $zz+=$pphpercent[3]*$sisazz;  
                                              }                          
                                      } 
                              }   

                      }
                  }
                      $pphSetahun[$xid]=$zz/12;
                 }    
            }*/         
                     
                     
          
            
            
            
         $rowdt=count($dtKary);   
        
        $tab.="<button class=mybutton onclick=postingDat(".$rowdt.")  id=revTmbl>Proses</button>&nbsp;<button class=mybutton onclick=zExcel(event,'sdm_slave_3pembulatangaji.php','".$arr."')>Excel</button>";
	$tab.="<table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr  ".$bgcoloraja." >
        <td align=center rowspan=2>No.</td>
        <td align=center rowspan=2>".$_SESSION['lang']['karyawanid']."</td>
        <td align=center rowspan=2>".$_SESSION['lang']['nik']."</td>
        <td align=center rowspan=2>".$_SESSION['lang']['namakaryawan']."</td>
        <td align=center rowspan=2>".$_SESSION['lang']['tipekaryawan']."</td>
        <td align=center colspan=6>".$_SESSION['lang']['gaji']."</td></tr>
        <tr ".$bgcoloraja." >
        <td  align=center>Penambah</td>
        <td  align=center>Komponen Penambah</td>
        
       
        <td align=center>Komponen Pengurang</td>
        
       

        <td align=center>Netto</td>
        <td align=center title='61'>Simpanan</td>
        
        <td align=center>Diterima</td>
        ";
        $tab.="</tr></thead><tbody>";
        
         // $tab.="penambah tetap, pengurang menjadi pengurang+pph+jms"
           //       . " <br> sumber jms dan pph dari bruto jika SULAWESI";
          
            foreach($dtKary as $lstKaryawan){
                $no+=1;
                $whr="karyawanid='".$lstKaryawan."'";
                $nik=  makeOption($dbname, 'datakaryawan', 'karyawanid,nik', $whr);
                $nmkar=  makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whr);
                $tPkar=  makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan', $whr);
                #menggenapkan menjadi seribu
//                $simp[$lstKaryawan]=(($lstGajiPembh[$lstKaryawan]+$lstGaji[$lstKaryawan])-$lstGajiPeng[$lstKaryawan])%1000;//hasil mod 
//                
//                
//                $dptGaji[$lstKaryawan]=(($lstGajiPembh[$lstKaryawan]+$lstGaji[$lstKaryawan])-($lstGajiPeng[$lstKaryawan]+$simp[$lstKaryawan]));
//                $tmbh2=explode(".",$dptGaji[$lstKaryawan]);
//                $simp2[$lstKaryawan]=$simp[$lstKaryawan].".".$tmbh2[1];
//                $simp2[$lstKaryawan]=intval($simp2[$lstKaryawan]);
//                $dptGaji2[$lstKaryawan]=round(($lstGajiPembh[$lstKaryawan]+$lstGaji[$lstKaryawan]))-round(($lstGajiPeng[$lstKaryawan]+$simp2[$lstKaryawan]));
//                if($dptGaji2[$lstKaryawan]<0){
//                    $dptGaji2[$lstKaryawan]=0;
//                }
                #netto
                #potongan pph di masukan ke netto
                $net[$lstKaryawan]=(round($lstGaji[$lstKaryawan])+round($lstGajiPembh[$lstKaryawan]))-($lstGajiPeng[$lstKaryawan]);
                //$net[$lstKaryawan]=(round($lstGaji[$lstKaryawan])+round($lstGajiPembh[$lstKaryawan]))-($lstGajiPeng[$lstKaryawan])-($pphSetahun[$lstKaryawan]);
                $sim[$lstKaryawan]=$net[$lstKaryawan]%1000;
                $bg="class=rowcontent";
                if($lstGajiPeng[$lstKaryawan]>$lstGajiPembh[$lstKaryawan]){
                    $bg="bgcolor=red";
                    #ambil belakangkoma
                    $simpUlang[$lstKaryawan]=$net[$lstKaryawan]-$sim[$lstKaryawan];
                    $ambilBlkn=explode(".",$simpUlang[$lstKaryawan]);
                    if($ambilBlkn[1]!=''){
                        $sim[$lstKaryawan]=$sim[$lstKaryawan].".".$ambilBlkn[1];
                    }
                    $simpUlang[$lstKaryawan]=$net[$lstKaryawan]-$sim[$lstKaryawan];
                }else{
                    #ambil belakangkoma
                    $simpUlang[$lstKaryawan]=$net[$lstKaryawan]-$sim[$lstKaryawan];
                    $ambilBlkn=explode(".",$simpUlang[$lstKaryawan]);
                    if($ambilBlkn[1]!=''){
                        $sim[$lstKaryawan]=$sim[$lstKaryawan].".".$ambilBlkn[1];
                    }
                    $simpUlang[$lstKaryawan]=$net[$lstKaryawan]-$sim[$lstKaryawan];
                }
                
              
                $tab.="<tr  ".$bg." id=rowDt_".$no.">";
                $tab.="<td>".$no."</td>";
                 $tab.="<td>".$lstKaryawan."</td>";
                if($proses=='excel'){
                    $tab.="<td>'".$nik[$lstKaryawan]."</td>";
                }else{
                    $tab.="<td><input type=hidden id=karyId_".$no." value=".$lstKaryawan." />".$nik[$lstKaryawan]."</td>";
                }
                
                
                $tab.="<td>".$nmkar[$lstKaryawan]."</td>";
                $tab.="<td>".$optNmTipeKar[$tPkar[$lstKaryawan]]."</td>";
                $tab.="<td align=right><input type=hidden id=penambah_".$no." value='".round($lstGaji[$lstKaryawan])."' />".round($lstGaji[$lstKaryawan])."</td>";
                $tab.="<td align=right>".$lstGajiPembh[$lstKaryawan]."</td>";
              
               // $tab.="<td align=right id=pph_".$no.">".$pphSetahun[$lstKaryawan]."</td>";//pph
                $tab.="<td align=right>".$lstGajiPeng[$lstKaryawan]."</td>";
                $tab.="<td align=right>".$net[$lstKaryawan]."</td>";
                $tab.="<td align=right><input type=hidden id=simpanan_".$no." value=".$sim[$lstKaryawan]." />".$sim[$lstKaryawan]."</td>";
                $tab.="<td align=right>".number_format($simpUlang[$lstKaryawan],0)."</td>";
                $tab.="</tr>";
                
            }
        $tab.="</tbody></table>";
}     
switch($proses)
{ 
	case'preview':
	echo $tab;
	break;
        case'getPeriode': 
            $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $sPeriodeAkut="select distinct periode from ".$dbname.".setup_periodeakuntansi 
                         where kodeorg='".$_POST['kdOrg']."' and tutupbuku=0";
            $qPeriodeCari=mysql_query($sPeriodeAkut) or die(mysql_error());
            while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))
            {
               $optPeriode.="<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode']."</option>";
            }
            echo $optPeriode;
        break;
        case'getPeriodeGaji': 
            $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $sPeriodeAkut="select distinct periode from ".$dbname.".sdm_5periodegaji 
                         where kodeorg='".$_POST['kdOrg']."' and sudahproses=0";
            $qPeriodeCari=mysql_query($sPeriodeAkut) or die(mysql_error());
            while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))
            {
               $optPeriode.="<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode']."</option>";
            }
            echo $optPeriode;
        break;
        case'updateData':
            /*$scek="select distinct tutupbuku  from ".$dbname.".setup_periodeakuntansi where periode='".$_POST['periodeGaji']."'";
            $qcek=  mysql_query($scek) or die(mysql_error($conn));
            $rcek= mysql_fetch_assoc($qcek);
            if($rcek['tutupbuku']==0){*/
                //$sdel="delete from ".$dbname.".sdm_gaji where karyawanid='".$_POST['karyId']."' and idkomponen in ('61','60','44') and periodegaji='".$_POST['periodeGaji']."'";
                $sdel="delete from ".$dbname.".sdm_gaji where karyawanid='".$_POST['karyId']."' and idkomponen in ('61','60') and periodegaji='".$_POST['periodeGaji']."'";
                //exit("error:".$sdel);
               if(!mysql_query($sdel)){
                   if($_POST['penambah']==''){
                       $_POST['penambah']=0;
                   }
                   if($_POST['simpanan']==''){
                       $_POST['simpanan']=0;
                   }
                        $supdate="insert into ".$dbname.".sdm_gaji (periodegaji,karyawanid,idkomponen,kodeorg,jumlah,pengali) values"
                               . "('".$_POST['periodeGaji']."','".$_POST['karyId']."','61','".$_POST['kdOrg']."','".$_POST['simpanan']."','1'),"
                               . "('".$_POST['periodeGaji']."','".$_POST['karyId']."','60','".$_POST['kdOrg']."','".$_POST['penambah']."','1')";
                       // exit("error:".$supdate);
                        if(!mysql_query($supdate)){
                         exit("error: db bermasalah ".mysql_error($conn)."___".$supdate);   
                        }
                }else{
                         $supdate="insert into ".$dbname.".sdm_gaji (periodegaji,karyawanid,idkomponen,kodeorg,jumlah,pengali) values"
                               . "('".$_POST['periodeGaji']."','".$_POST['karyId']."','61','".$_POST['kdOrg']."','".$_POST['simpanan']."','1'),"
                               . "('".$_POST['periodeGaji']."','".$_POST['karyId']."','60','".$_POST['kdOrg']."','".$_POST['penambah']."','1')";
                        //exit("error:".$supdate);
                        if(!mysql_query($supdate)){
                         exit("error: db bermasalah ".mysql_error($conn)."___".$supdate);   
                        } 
                }
            /*}else{
                exit("error: Periode ini sudah tutup buku");
            }*/
            
        break;
        case'excel':
        $thisDate=date("YmdHms");
                   //$nop_="Laporan_Pembelian";
                   $nop_="pembulatanGaji_".$thisDate;
                   $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                    gzwrite($gztralala, $tab);
                    gzclose($gztralala);
                    echo "<script language=javascript1.2>
                       window.location='tempExcel/".$nop_.".xls.gz';
                       </script>";
        break;
	default:
	break;
}

?>