<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$notransaksi_head=$_POST['notrans'];
$notransaksi=$_POST['noOptrans'];
$proses=$_POST['proses'];
$lokasi=$_SESSION['empl']['lokasitugas'];
$jnsPekerjaan=$_POST['jnsPekerjaan'];
$lokKerja=$_POST['locationKerja'];
$muatan=$_POST['muatan'];
$brtMuatan=$_POST['brtmuatan'];
$jmlhRit=$_POST['jmlhRit'];
$ket=$_POST['ket'];
$posisi=$_POST['posisi'];
$kdKry=$_POST['kdKry'];
$oldjnsPekerjaan=$_POST['oldjnsPekerjaan'];
$uphOprt=$_POST['uphOprt'];
$prmiOprt=$_POST['prmiOprt'];
$prmiLuarJam=$_POST['prmiLuarJam'];
$pnltyOprt=$_POST['pnltyOprt'];
$tglTrans=tanggalsystem($_POST['tglTrans']);
$thnKntrk=$_POST['thnKntrk'];
//$lksiTgs=substr($_SESSION['empl']['lokasitugas'],0,4);
$noKntrak=$_POST['noKntrak'];
$biaya=$_POST['biaya'];
$Blok=$_POST['Blok'];
$oldBlok=$_POST['oldBlok'];
$old_lokKerja=$_POST['old_lokKerja'];

$kmhmAwal=$_POST['kmhmAwal'];
$kmhmAkhir=$_POST['kmhmAkhir'];
$satuan=$_POST['satuan'];
if($notransaksi_head!='')
{
        $sKode="select kodeorg from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";
        $qKode=mysql_query($sKode) or die(mysql_error());
        $rKode=mysql_fetch_assoc($qKode);
}
$optKdVhc=makeOption($dbname, 'vhc_runht', 'notransaksi,kodevhc');
$optBlokLama=makeOption($dbname, 'setup_blok', 'kodeorg,bloklama');
$optKegiatan=makeOption($dbname, 'vhc_kegiatan', 'kodekegiatan,namakegiatan');
$tpKar=makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan');

switch($proses)
{
        case 'load_data_kerjaan':
        //echo "warning:masuk";	

        $sql="select * from ".$dbname.".vhc_rundt where substring(notransaksi,1,4)='".$rKode['kodeorg']."' and notransaksi='".$notransaksi_head."' order by notransaksi desc";// echo $sql;
        $query=mysql_query($sql) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
                $no+=1;
                echo"
                <tr class=rowcontent>
                <td>".$no."</td>
                <td style=display:none>".$res['notransaksi']."</td>
                <td>".$res['jenispekerjaan']."-".$optKegiatan[$res['jenispekerjaan']]."</td>
                <td>".$res['alokasibiaya']."-".$optBlokLama[$res['alokasibiaya']]."</td>
                <td>".number_format($res['jumlahrit'],2)."</td>
                <td>".number_format($res['beratmuatan'],2)."</td>
                <td>".number_format($res['kmhmawal'],2)."</td>
                <td>".number_format($res['kmhmakhir'],2)."</td>
                 <td>".$res['satuan']."</td>
                <td style=display:none;>".number_format($res['biaya'],2)."</td>
                <td><img src=images/application/application_edit.png class=resicon  title='Edit' 
                onclick=\"fillFieldKrj('".$res['jenispekerjaan']."','".$res['alokasibiaya']."','". $res['beratmuatan']."','". $res['jumlahrit']."','". $res['keterangan']."','". $res['biaya']."','". $res['kmhmawal']."','". $res['kmhmakhir']."','". $res['satuan']."');\">
                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataKrj('". $res['notransaksi']."','". $res['jenispekerjaan']."');\" >	
                </td>
                </tr>
                ";
        }
        break;

        case'insert_pekerjaan':
        if($notransaksi_head=='')
        {
                echo"warning: please confirm heade first";
                exit();
        }
        if($jnsPekerjaan=='')
        {
            echo"warning: Activity required";
            exit();

        }
        if($lokKerja=='')
        {
            echo"warning: Cost allocation (block) required";
            exit();

        }
        if($kmhmAwal>=$kmhmAkhir)
        {
                echo"warning:".$_SESSION['lang']['vhc_kmhm_awal']." must lower then ".$_SESSION['lang']['vhc_kmhm_akhir']."";
                exit();
        }
        $jumlah=$kmhmAkhir-$kmhmAwal;
        $sCekHt="select notransaksi from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";

        $qCekHt=mysql_query($sCekHt) or die(mysql_error());
        $rCekHt=mysql_num_rows($qCekHt);
        if($rCekHt<1)
        {
                echo"warning: Header required";
                exit();
        }

        if($Blok!='' && substr($Blok,3,1)!='M')
        {
            if(strlen($Blok)<10)
            {
                exit("Error: Block required");
            }
                $lokKerja=$Blok;
				
				
        }
		

        if($biaya=='')
            $biaya=0;
        $sins="insert into ".$dbname.".vhc_rundt (`notransaksi`,`jenispekerjaan`,`alokasibiaya`,`beratmuatan`,`jumlahrit`,`keterangan`,`biaya`,`kmhmawal`,
                `kmhmakhir`,`jumlah`,`satuan`) 
                values ('".$notransaksi_head."','".$jnsPekerjaan."','".$lokKerja."','".$brtMuatan."','".$jmlhRit."','".$ket."'
                ,'".$biaya."','".$kmhmAwal."','".$kmhmAkhir."','".$jumlah."','".$satuan."')";

        if(mysql_query($sins))
        {
            $sKm="select distinct kmhmakhir from ".$dbname.".vhc_kmhmakhir_vw where kodevhc='".$optKdVhc[$notransaksi_head]."'";
            $qKm=mysql_query($sKm) or die(mysql_error($conn));
            $rKm=mysql_fetch_assoc($qKm);
            echo $rKm['kmhmakhir'];
        }
        else
        {	echo "DB Error : ".mysql_error($conn);	 }
                break;

        case'update_kerja':
        if(($brtMuatan=='')||($jmlhRit==''))
        {
                echo"warning:Please Complete The Form";
                exit();
        }
        if($Blok!='')
        {
                $lokKerja=$Blok;
                if($lokKerja!=$oldBlok)
                {
                        $where.=" and alokasibiaya='".$oldBlok."'";
                }
                else
                {
                        $where.=" and alokasibiaya='".$lokKerja."'";
                }
        }
        else
        {
                if($old_lokKerja!=$lokKerja)
                {
                        $where.=" and alokasibiaya='".$old_lokKerja."'";
                }
                else
                {
                        $where.=" and alokasibiaya='".$lokKerja."'";
                }
        }
        if($oldjnsPekerjaan!='')
        {
                if($jnsPekerjaan!=$oldjnsPekerjaan)
                {
                        $where.="  and jenispekerjaan='".$oldjnsPekerjaan."'";
                }
                else
                {
                        $where.="  and jenispekerjaan='".$jnsPekerjaan."'";
                }
        }
        if($kmhmAwal>=$kmhmAkhir)
        {
                echo"warning:".$_SESSION['lang']['vhc_kmhm_awal']." must lower then ".$_SESSION['lang']['vhc_kmhm_akhir']."";
                exit();
        }
         $jumlah=$kmhmAkhir-$kmhmAwal;
        $sup="update ".$dbname.".vhc_rundt set jenispekerjaan='".$jnsPekerjaan."',alokasibiaya='".$lokKerja."',beratmuatan='".$brtMuatan."'
        ,jumlahrit='".$jmlhRit."',keterangan='".$ket."',biaya='".$biaya."',kmhmawal='".$kmhmAwal."',kmhmakhir='".$kmhmAkhir."',jumlah='".$jumlah."'
        ,satuan='".$satuan."' where notransaksi='".$notransaksi_head."' ".$where."";
        //exit("Error:".$sup);
        if(mysql_query($sup))
        {

            $sKm="select distinct kmhmakhir from ".$dbname.".vhc_kmhmakhir_vw where kodevhc='".$optKdVhc[$notransaksi_head]."'";
            //exit("Error:".$sKm);
            $qKm=mysql_query($sKm) or die(mysql_error($conn));
            $rKm=mysql_fetch_assoc($qKm);
            echo intval($rKm['kmhmakhir']);
        }
        else
        {echo "DB Error : ".mysql_error($conn);	 }
        break;

        case'deleteKrj':
        $delKrj="delete from ".$dbname.".vhc_rundt where notransaksi='".$notransaksi_head."' and jenispekerjaan='".$jnsPekerjaan."'";
        if(mysql_query($delKrj))
        echo"";
        else
        echo "DB Error : ".mysql_error($conn);	 

        break;
        case'insert_operator':
            if($prmiLuarJam==''){
                $prmiLuarJam=0;
            }
            if($pnltyOprt==''){
                $pnltyOprt=0;
            }
            if($prmiOprt==''){
                $prmiOprt=0;
            }
        $sCekHt="select notransaksi from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";
//	echo"warning:".$sCekHt;
        $qCekHt=mysql_query($sCekHt) or die(mysql_error());
        $rCekHt=mysql_num_rows($qCekHt);
        if($rCekHt<1)
        {
                echo"warning: Header required";
                exit();
        }
        $skry="select a.`alokasi`,b.tipe from ".$dbname.".datakaryawan a inner join ".$dbname.".sdm_5tipekaryawan b on 
        a.tipekaryawan=b.id where karyawanid='".$kdKry."'"; 
        //echo "warning:".$skry;
        $qkry=mysql_query($skry) or die(mysql_error());
        $rkry=mysql_fetch_assoc($qkry);
        $sPeriode="select periode from ".$dbname.".sdm_5periodegaji where kodeorg='".substr($rKode['kodeorg'],0,4)."' and periode='".substr($tglTrans,0,4)."-".substr($tglTrans,4,2)."'";# tanggalmulai<".$tglTrans." and tanggalsampai>=".$tglTrans;
        //echo $sPeriode;
        //exit("Error:");
        $qPeriode=mysql_query($sPeriode) or die(mysql_error($conn));
        $rPeriode=mysql_fetch_assoc($qPeriode);
        
                $sKd="select lokasitugas,subbagian from ".$dbname.".datakaryawan where karyawanid='".$kdKry."'";
                $qKd=mysql_query($sKd) or die(mysql_error());
                $rKd=mysql_fetch_assoc($qKd);
                $lokasiTugas=$rKd['lokasitugas'];
                if(!is_null($rKd['subbagian'])||$rKd['subbagian']!=0||$rKd['subbagian']!=''){
                   $lokasiTugas=$rKd['subbagian'];
                }
        if($uphOprt==''){
            $sUmr="select sum(jumlah) as jumlah from ".$dbname.".sdm_5gajipokok 
            where karyawanid='".$kdKry."' and tahun=".substr($tglTrans,0,4)."  and idkomponen in (1,31)";
            $qUmr=mysql_query($sUmr) or die(mysql_error());
            $rUmr=mysql_fetch_assoc($qUmr);
            $uphOprt=$rUmr['jumlah']/25;
        }


        if($posisi==1)
        {
                $sCek="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='1'";
                //echo "warning:".$sCek;
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_assoc($qCek);

                        if($rCek['jmlh']!=4)
                        {
                          
                            
                            $iCek="select count(*) as jum from ".$dbname.".vhc_runhk where tanggal='".$tglTrans."' and idkaryawan='".$kdKry."' ";
                           
                            $nCek=mysql_query($iCek) or die (mysql_error($conn));
                            $dCek=mysql_fetch_assoc($nCek);
                                if($dCek['jum']>0)
                                {
                                    $uphOprt=0;
                                }
                            
                            
                            #cek hari minggu
                                $qwe=date('D', strtotime($tglTrans));
                                if($qwe=='Sun' && $rkry['tipe']=='KHT')
                                {
                                   $uphOprt=0;
                                }  
                                
                                $sqlIns="insert into ".$dbname.".vhc_runhk 
                                (`notransaksi`,`idkaryawan`,`posisi`,`tanggal`,`statuskaryawan`,`upah`,`premi`,`penalty`,`premicuci`,`premiluarjam`) 
                                values 
                                ('".$notransaksi_head."','".$kdKry."','".$posisi."','".$tglTrans."','".$rkry['tipe']."','".$uphOprt."','".$prmiOprt."','".$pnltyOprt."',".$_POST['premicuci'].",".$prmiLuarJam.")";
                                //echo"warning:".$sqlIns;
                                if(mysql_query($sqlIns))
                                {									

                                        $sCek="select karyawanid from ".$dbname.".sdm_absensidt where kodeorg='".$lokasiTugas."' and tanggal='".$tglTrans."' and karyawanid='".$kdKry."'";
                                        $qCek=mysql_query($sCek) or die(mysql_error($conn));
                                        $rCek=mysql_num_rows($qCek);
                                        if($rCek!=1)
                                        {
                                            if($lokasiTugas==''){
                                                $sKd="select lokasitugas,subbagian from ".$dbname.".datakaryawan where karyawanid='".$kdKry."'";
                                                $qKd=mysql_query($sKd) or die(mysql_error());
                                                $rKd=mysql_fetch_assoc($qKd);
                                                $lokasiTugas=$rKd['lokasitugas'];
                                                if($rKd['subbagian']!=''){
                                                   $lokasiTugas=$rKd['subbagian'];
                                                }
                                            }
                                        $sUpdAbns="insert into ".$dbname.".sdm_absensidt (`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`) values ('".$lokasiTugas."','".$tglTrans."','".$kdKry."','H','07:00:00','15:00:00')";
                                        //echo"warning".$sUpdAbns;
                                                if(!mysql_query($sUpdAbns))
                                                {
                                                echo "DB Error : ".mysql_error($conn);
                                                }
                                        }
                                        
                                       
                                }
                                else
                                {
                                        echo "DB Error : ".mysql_error($conn);	
                                }
                        }
                        else
                        {
                                echo"warning: Can`t complete transaction, Operator maximum limit exeed";
                                exit();
                        }
        }
        elseif($posisi==0)
        {
            //exit("Error:MASUK");
                $sCekSop="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='0'";
                //echo "warning:".$sCekSop;
                $qCekSop=mysql_query($sCekSop) or die(mysql_error());
                $rCekSop=mysql_fetch_assoc($qCekSop);
                if($rCekSop['jmlh']==1)
                {
                        echo"warning: Operator can only one";
                        break;
                        exit();
                }
                elseif($rCekSop['jmlh']==0)
                {
                            
                            $iCek="select count(*) as jum from ".$dbname.".vhc_runhk where tanggal='".$tglTrans."' and idkaryawan='".$kdKry."' ";
                            //exit("Error:$iCek");
                            $nCek=mysql_query($iCek) or die (mysql_error($conn));
                            $dCek=mysql_fetch_assoc($nCek);
                                        if($dCek['jum']>0)
                                        {
                                            $uphOprt=0;
                                        }
                            
                    
                                #cek hari minggu
                                $qwe=date('D', strtotime($tglTrans));
                                if($qwe=='Sun' && $rkry['tipe']=='KHT')
                                {
                                   $uphOprt=0;
                                }  

                                $sqlIns="insert into ".$dbname.".vhc_runhk (`notransaksi`,`idkaryawan`,`posisi`,`tanggal`,`statuskaryawan`,`upah`,`premi`,`penalty`,`premicuci`,`premiluarjam`) 
                                         values ('".$notransaksi_head."','".$kdKry."','".$posisi."','".$tglTrans."','".$rkry['tipe']."','".$uphOprt."','".$prmiOprt."','".$pnltyOprt."',".$_POST['premicuci'].",".$prmiLuarJam.")";
                                        //echo"warning:".$sqlIns;
                                if(mysql_query($sqlIns))
                                {
                                      
                                        //echo"warning:Masuk aja A";
                                                $sCek="select karyawanid from ".$dbname.".sdm_absensidt where kodeorg='".$lokasiTugas."' and tanggal='".$tglTrans."' and karyawanid='".$kdKry."'";
                                                $qCek=mysql_query($sCek) or die(mysql_error($conn));
                                                $rCek=mysql_num_rows($qCek);
                                                if($rCek<1)
                                                {
                                                    if($lokasiTugas==''){
                                                        $sKd="select lokasitugas,subbagian from ".$dbname.".datakaryawan where karyawanid='".$kdKry."'";
                                                        $qKd=mysql_query($sKd) or die(mysql_error());
                                                        $rKd=mysql_fetch_assoc($qKd);
                                                        $lokasiTugas=$rKd['lokasitugas'];
                                                        if($rKd['subbagian']!=''){
                                                           $lokasiTugas=$rKd['subbagian'];
                                                        }
                                                    }
                                                    if($tpKar[$kdKry]!='4')
                                                    {
                                                        $sUpdAbns="insert into ".$dbname.".sdm_absensidt (`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`) values ('".$lokasiTugas."','".$tglTrans."','".$kdKry."','H','07:00:00','15:00:00')";
                                                        //echo"warning".$sUpdAbns;
                                                        if(!mysql_query($sUpdAbns)){
                                                        echo "DB Error : ".mysql_error($conn);
                                                        }
													}
													
                                                }
                                                                                
                                }
                                else
                                {
                                        echo "DB Error : ".mysql_error($conn);
                                }
                        }
        }
        break;
        case 'update_operator':
        if($posisi==1)
        {
                $sCek="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='1'";
                //echo "warning:".$sCek;
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_assoc($qCek);
        }
        elseif($posisi==0)
        {
                $sCekSop="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='0'";
                //echo "warning:".$sCekSop;
                $qCekSop=mysql_query($sCekSop) or die(mysql_error());
                $rCekSop=mysql_fetch_assoc($qCekSop);
        }
        if($rCek['jmlh']>4)
        {
                echo"warning: Can`t complete transaction, Operator maximum limit exeed";
                exit();
        }
        if($rCekSop['jmlh']>1)
        {
                echo"warning: Can`t complete transaction, Operator maximum limit exeed";
                exit();
        }
        $skry="select a.`alokasi`,b.tipe from ".$dbname.".datakaryawan a inner join ".$dbname.".sdm_5tipekaryawan b on 
        a.tipekaryawan=b.id where karyawanid='".$kdKry."'"; 
        //echo "warning:".$skry;
        $qkry=mysql_query($skry) or die(mysql_error());
        $rkry=mysql_fetch_assoc($qkry);


        $sup_op="update ".$dbname.".vhc_runhk set posisi='".$posisi."',tanggal='".$tglTrans."',statuskaryawan='".$rkry['tipe']."',
                 upah='".$uphOprt."',premi='".$prmiOprt."',penalty='".$pnltyOprt."',premicuci=".$_POST['premicuci'].",premiluarjam=".$prmiLuarJam." 
                 where notransaksi='".$notransaksi_head."' and idkaryawan='".$kdKry."'";
        if(mysql_query($sup_op))
        echo"";
        else
                echo "DB Error : ".mysql_error($conn);
        break;
        case'getUmr':
            if($_POST['tahun']!='')
                    $tahun=$_POST['tahun'];
            else {
                    $tahun=date('Y');
            }          
        $sUmr="select sum(jumlah) as jumlah from ".$dbname.".sdm_5gajipokok 
            where karyawanid='".$kdKry."' and tahun=".$tahun."  and idkomponen in (1,31)";
        $qUmr=mysql_query($sUmr) or die(mysql_error());
        $rUmr=mysql_fetch_assoc($qUmr);
        $umr=$rUmr['jumlah']/25;
        if($umr==0){
            exit("error: Don't have basic salary !!");
        }else{
            echo intval($umr);
        }
        break;

        case'load_data_opt':
        $arrPos=array("Sopir","Kondektur");
            $arrDt=array("0"=>"Tidak","1"=>"Ya");
        $sql="select * from ".$dbname.".vhc_runhk where substring(notransaksi,1,4)='".$rKode['kodeorg']."' and notransaksi='".$notransaksi_head."' order by notransaksi desc"; //echo "warning:".$sql;
        $query=mysql_query($sql) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
                $skry="select `namakaryawan`,nik from ".$dbname.".datakaryawan where karyawanid='".$res['idkaryawan']."'";
                $qkry=mysql_query($skry) or die(mysql_error());
                $rkry=mysql_fetch_assoc($qkry);
                $no+=1;
                echo"
                <tr class=rowcontent>
                <td>".$no."</td>
                <td style=display:none;>".$res['notransaksi']."</td>
                <td>".$rkry['nik']."</td>
                <td>".$rkry['namakaryawan']."</td>
                <td>".$arrPos[$res['posisi']]."</td>
                <td style=display:none>".number_format($res['upah'],2)."</td>
                <td>".number_format($res['premi'],2)."</td>
                 <td>".number_format($res['premiluarjam'],2)."</td>      
                <td>".number_format($res['penalty'],2)."</td>
                <td>".$arrDt[$res['premicuci']]."</td>
                <td align=center>
                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['notransaksi']."','". $res['idkaryawan']."');\" >	
                </td>
                </tr>
                ";
        }
        break;
        case'getKntrk':
        $optKntrk="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sSpk="select notransaksi from ".$dbname.".log_spkht where kodeorg='".$lokasi."' and posting<>'0' and tanggal like '%".$thnKntrk."%'";
        //echo "warning:".$sSpk;
        $qSpk=mysql_query($sSpk) or die(mysql_error());
        $rSpk=mysql_num_rows($qSpk);
        if($rSpk>0)
        {
                while($rSpk=mysql_fetch_assoc($qSpk))
                {
                        $optKntrk.="<option value=".$rSpk['notransaksi']." ".($rSpk['notransaksi']==$noKntrak?'selected':'').">".$rSpk['notransaksi']."</option>";
                }

        }
        else
        {
                $optKntrk="<option value=''></option>";
                //echo $optKntrk;
        }
        echo $optKntrk;
        break;

        case'delete_opt':
            $sTanggal="select distinct tanggal from ".$dbname.".vhc_runht where notransaksi='".$notransaksi."'";
            $qTanggal=mysql_query($sTanggal) or die(mysql_error($conn));
            $rTanggal=mysql_fetch_assoc($qTanggal);
            $delAbsen="delete from ".$dbname.".sdm_absensidt where karyawanid='".$kdKry."' and tanggal='".$rTanggal['tanggal']."'";
            if(mysql_query($delAbsen))
            {
                $sdel="delete from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi."' and idkaryawan='".$kdKry."'";
                //echo "warning:".$sdel;
                if(mysql_query($sdel))
                echo"";
                else
                echo "DB Error : ".mysql_error($conn);
            }
            else
            {
                 echo "DB Error : ".$delAbsen."___".mysql_error($conn);
            }
        break;
		
		
		
        case'getBlok':
        $optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sBlok="select kodeorganisasi,namaorganisasi,bloklama from ".$dbname.".organisasi a
                left join ".$dbname.".setup_blok b on a.kodeorganisasi=b.kodeorg
                where induk like '%".$lokKerja."%' and tipe='BLOK' and luasareaproduktif>0 order by kodeorganisasi";
        $qBlok=mysql_query($sBlok) or die(mysql_error());
        while($rBlok=mysql_fetch_assoc($qBlok))
        {
                if($Blok!="")
                {
                        $optBlok.="<option value=".$rBlok['kodeorganisasi']." ".($rBlok['kodeorganisasi']==$Blok?"selected":"").">".$rBlok['kodeorganisasi']."-".$rBlok['bloklama']."</option>";
                }
                else
                {
                        $optBlok.="<option value=".$rBlok['kodeorganisasi'].">".$rBlok['kodeorganisasi']."-".$rBlok['bloklama']."</option>";
                }
        }
            #khusus Project:
              $str="select kode,nama from  ".$dbname.".project where kodeorg='".$lokKerja."' and posting=0";
              $res=mysql_query($str);
              while($bar=mysql_fetch_object($res))
              {
                  $optBlok.="<option value=".$bar->kode.">Project-".$bar->kode."-".$bar->nama."</option>";
              }
			  
		if(substr($lokKerja,-1)=='M')
		{
			
       		 $sBlok="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='STATION' and kodeorganisasi like '%".$lokKerja."%'";
       		 $qBlok=mysql_query($sBlok) or die(mysql_error());
			  while($rBlok=mysql_fetch_assoc($qBlok))
			  {
				  $optBlok.="<option value=".$rBlok['kodeorganisasi'].">".$rBlok['kodeorganisasi']."-".$rBlok['namaorganisasi']."</option>";
			  }
		}
			  
			  
			  
        echo $optBlok;
		
		
		
		
		
        break;
        case'getSatuan':
                 $whr="kodekegiatan='".$_POST['kdKegiatan']."' and regional='".$_SESSION['empl']['regional']."'";
                 $optKeg=makeOption($dbname,'vhc_kegiatan','kodekegiatan,satuan',$whr);
                 echo $optKeg[$_POST['kdKegiatan']];
        break;
       case'getPremi':
        //0=(basis*hargasatuan)+(kelebihan basis*hargalebihbasis),minggu=prestasi*hargaminggu
        //1=(sum(hargasatuan)- (sum(basis)/jumlahbaris))*2+(sum(basis)/jumlahbaris)),minggu=sum(hargaminggu)
        //2= jika MO01 maka UMP+hargalebihbasis per hari
        //3=(sum(hargasatuan)- (sum(basis)/jumlahbaris))*2+(sum(basis)/jumlahbaris)),minggu=prestasi*hargaminggu
        //4=tidak mengenal hari sum(hargasatuan)
        
            $totPremi=0;
            $grnTot=0;
            $sdr="select a.*,b.tanggal,c.basis,c.hargasatuan,c.hargaslebihbasis,c.hargaminggu,c.auto,c.satuan,b.kodevhc 
                  from ".$dbname.".vhc_rundt a  left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
                  left join ".$dbname.".vhc_kegiatan c on a.jenispekerjaan=c.kodekegiatan
                  where a.notransaksi='".$_POST['notransaksi']."'";
            
           //exit("error:".$sdr);
           $qdr=mysql_query($sdr) or die(mysql_error($conn));
           while($rDr=  mysql_fetch_assoc($qdr)){
		   $dhr="regional='".$_SESSION['empl']['regional']."' and tanggal='".$rDr['tanggal']."'";
           $optHariLbr=makeOption($dbname, 'sdm_5harilibur', 'regional,tanggal',$dhr);
		   $qwe=date('D', strtotime($rDr['tanggal']));
               switch ($rDr['auto']){
                   case'0':
                       
                       $lbhbasis=0;
                       if(($qwe=='Sun')||($optHariLbr[$_SESSION['empl']['regional']]!='')){
                            $totPremi=$rDr['beratmuatan']*floatval(str_replace(',','',number_format($rDr['hargaminggu'],2)));
                       }else{
                           
                           //exit("Error:a");
                           //berat muat=9
                           //basis=10
                          //echo $rDr['hargasatuan']._.$rDr['beratmuatan'].__.$rDr['beratmuatan']*$rDr['hargasatuan'];exit();
                           
                         // echo $rDr['beratmuatan']._.$rDr['basis'];exit();
                            if($rDr['beratmuatan']>$rDr['basis']){
                               // exit("Error:a");
                                 $lbhbasis=$rDr['beratmuatan']-$rDr['basis'];
                                $totPremi=($rDr['basis']*floatval(str_replace(',','',number_format($rDr['hargasatuan'],2))))+($lbhbasis*$rDr['hargaslebihbasis']);
                         }else{
                             
				//$totPremi=$rDr['beratmuatan']*floatval(str_replace(',','',number_format($rDr['hargasatuan'],2)));//$rDr['hargasatuan'];
                               $totPremi= $rDr['beratmuatan']*$rDr['hargasatuan'];
                        // echo $totPremi;exit(); 
                         }
                           // echo $totPremi;exit(); 
                       }
                   break;
                   case'1'://perbaikan number format
                       $qwe=date('D', strtotime($rDr['tanggal']));
                       if(($qwe=='Sun')||($optHariLbr[$_SESSION['empl']['regional']]!='')){
                            $totPremi=$rDr['beratmuatan']*floatval(str_replace(',','',number_format($rDr['hargaminggu'],2)));//$rDr['hargaminggu'];
                       }else{
                            $totPremi=$rDr['beratmuatan']*floatval(str_replace(',','',number_format($rDr['hargasatuan'],2)));
                       }
                   break;
                   case'2':
					   
                       if(($qwe=='Sun')||($optHariLbr[$_SESSION['empl']['regional']]!='')){
                           $totPremi=$rDr['beratmuatan']*$rDr['hargaminggu'];
                       }else{
                          $totPremi=$rDr['hargaslebihbasis'];
                       }
                        
                   break;
                   case'3':
                        if($rDr['satuan']=='JJG'){
                            if(($rDr['alokasibiaya']=='')||(strlen($rDr['alokasibiaya'])!='10')){
                               exit("error: detail alokasi biaya harus diisi");
                           }
                           $sBjr="select distinct sum(a.totalkg)/sum(a.jjg) as bjr, tanggal from ".$dbname.".kebun_spbdt a left join 
                                  ".$dbname.".kebun_spbht b on a.nospb=b.nospb where tanggal='".tanggalsystem($_POST['tanggal'])."' 
                                  and blok like '".substr($rDr['alokasibiaya'],0,6)."%' order by tanggal desc limit 1";
                           $qBjr=mysql_query($sBjr) or die(mysql_error($conn));
                           $rBjr=mysql_fetch_assoc($qBjr);
                           //echo $rBjr['bjr'].__.$rBjr['tanggal'];
                           //exit("Error:a");
                           
                           if(($rBjr['bjr']=='')||intval($rBjr['bjr'])==0){
							   $sBjr="select distinct sum(a.totalkg)/sum(a.jjg) as bjr, tanggal from ".$dbname.".kebun_spbdt a left join 
											  ".$dbname.".kebun_spbht b on a.nospb=b.nospb where left(tanggal,7)='".substr($rDr['tanggal'],0,7)."' 
											  and blok like '".substr($rDr['alokasibiaya'],0,6)."%' group by left(blok,6)";
							   $qBjr=mysql_query($sBjr) or die(mysql_error($conn));
							   $rBjr=mysql_fetch_assoc($qBjr);
							   if(($rBjr['bjr']=='')||intval($rBjr['bjr'])==0){
									$sTblBjr="select bjr from ".$dbname.".kebun_5bjr where kodeorg='".$rDr['alokasibiaya']."' and tahunproduksi='".substr($_POST['tanggal'],-4,4)."'";
									$qTblBjr=mysql_query($sTblBjr) or die(mysql_error($conn));
									$rTblBjr=mysql_fetch_assoc($qTblBjr);
									$rBjr['bjr']=$rTblBjr['bjr'];
								}
						   }
                           
                            $rDr['beratmuatan']=$rDr['beratmuatan']*$rBjr['bjr'];                           
                        }
                        
                        #jika berat muatnya sudah ada
                        if(($qwe=='Sun')||($optHariLbr[$_SESSION['empl']['regional']]!='')){
                            $totPremi=$rDr['beratmuatan']*floatval(str_replace(',','',number_format($rDr['hargaminggu'],2)));   
                        }
                        else{
                           $totPremi=$rDr['beratmuatan']*floatval(str_replace(',','',number_format($rDr['hargasatuan'],2)));
                        }
                        
                   break;
                   case'4':
                       $summing="select sum(hargasatuan) as hrgsat from ".$dbname.".vhc_kegiatan
                                 where auto='".$rDr['auto']."' and regional='".$_SESSION['empl']['regional']."'";
                       $qumingg=mysql_query($summing) or die(mysql_error());
                       $rumingg=mysql_fetch_assoc($qumingg);
                       $totPremi=$rumingg['hrgsat'];
                   break;
                   case'6':
                       $lbhbasis=0;
                       if($rDr['satuan']=='TBS'){
                           if(($rDr['alokasibiaya']=='')||(strlen($rDr['alokasibiaya'])!='10')){
                               exit("error: detail alokasi biaya harus diisi");
                           }
                           $sBjr="select distinct sum(a.totalkg)/sum(a.jjg) as bjr, tanggal from ".$dbname.".kebun_spbdt a left join 
                                  ".$dbname.".kebun_spbht b on a.nospb=b.nospb where tanggal='".tanggalsystem($_POST['tanggal'])."' 
                                  and blok like '".substr($rDr['alokasibiaya'],0,6)."%' order by tanggal desc limit 1";
                           $qBjr=mysql_query($sBjr) or die(mysql_error($conn));
                           $rBjr=mysql_fetch_assoc($qBjr);
						   if(($rBjr['bjr']=='')||intval($rBjr['bjr'])==0){
							   $sBjr="select distinct sum(a.totalkg)/sum(a.jjg) as bjr, tanggal from ".$dbname.".kebun_spbdt a left join 
											  ".$dbname.".kebun_spbht b on a.nospb=b.nospb where left(tanggal,7)='".substr($rDr['tanggal'],0,7)."' 
											  and blok like '".substr($rDr['alokasibiaya'],0,6)."%' group by left(blok,6)";
							   $qBjr=mysql_query($sBjr) or die(mysql_error($conn));
							   $rBjr=mysql_fetch_assoc($qBjr);
							   if(($rBjr['bjr']=='')||intval($rBjr['bjr'])==0){
									$sTblBjr="select bjr from ".$dbname.".kebun_5bjr where kodeorg='".$rDr['alokasibiaya']."' and tahunproduksi='".substr($_POST['tanggal'],-4,4)."'";
									$qTblBjr=mysql_query($sTblBjr) or die(mysql_error($conn));
									$rTblBjr=mysql_fetch_assoc($qTblBjr);
									$rBjr['bjr']=$rTblBjr['bjr'];
								}
						   }
                           $rDr['beratmuatan']=$rDr['beratmuatan']*$rBjr['bjr'];
                       }
					   if($_SESSION['empl']['regional']=='SULAWESI'){
							if(substr($rDr['jenispekerjaan'],0,2)=='MJ'){
								$cek=0;
								#perhitungan premi progresif, konsepnya. total semua prestasi di cek sudah lebih basis atau belum jika sudah maka pengalinya adalah harga lebih basisnya berlaku di sulawesi,berlaku utk MJ saja
								$totBrtMuat[$rDr['tanggal'].$rDr['jenispekerjaan']]+=$rDr['beratmuatan'];
									if($totBrtMuat[$rDr['tanggal'].$rDr['jenispekerjaan']]>$rDr['basis']){
										$cek=$totBrtMuat[$rDr['tanggal'].$rDr['jenispekerjaan']]-$rDr['beratmuatan'];
										if($cek==0){
											$totPremi=(($rDr['beratmuatan']-$rDr['basis'])*floatval((str_replace(',','',(number_format($rDr['hargaslebihbasis'],2))))))+($rDr['basis']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2))))); 
										}else{
											$totPremi=($rDr['beratmuatan']*floatval((str_replace(',','',(number_format($rDr['hargaslebihbasis'],2)))))); 
										}
									}else{
										$totPremi=($rDr['beratmuatan']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2))))); 
									}
							}else{
								if($rDr['beratmuatan']>$rDr['basis']){
									   $lbhbasis=$rDr['beratmuatan']-$rDr['basis'];
								   }
								   //echo $lbhbasis."__";.
									if($rDr['beratmuatan']>$rDr['basis']){
										$totPremi=(($rDr['beratmuatan']-$rDr['basis'])*floatval(str_replace(',','',number_format($rDr['hargaslebihbasis'],2))))+(floatval(str_replace(',','',number_format($rDr['hargasatuan'],2)))*$rDr['basis']); 
									}
									else{
										 $totPremi=($rDr['beratmuatan']*floatval(str_replace(',','',number_format($rDr['hargasatuan'],2)))); 
									}
							}
						}else{
								   if($rDr['beratmuatan']>$rDr['basis']){
									   $lbhbasis=$rDr['beratmuatan']-$rDr['basis'];
								   }
								   //echo $lbhbasis."__";.
									if($rDr['beratmuatan']>$rDr['basis']){
										$totPremi=(($rDr['beratmuatan']-$rDr['basis'])*floatval(str_replace(',','',number_format($rDr['hargaslebihbasis'],2))))+(floatval(str_replace(',','',number_format($rDr['hargasatuan'],2)))*$rDr['basis']); 
									}
									else{
										 $totPremi=($rDr['beratmuatan']*floatval(str_replace(',','',number_format($rDr['hargasatuan'],2)))); 
									}
						}
                   break;
				   
                   case'7':
                       //$_POST['tanggal'] premi minggu atau jam setelah luar jam kerja atau di panggil lagi di nolkan
                       $totPremi=$rDr['hargasatuan'];
						
                   break;	   
                   case'8':
                       //$totPremi=$rDr['hargasatuan']+(($rDr['beratmuatan']-$rDr['basis'])*$rDr['hargaslebihbasis'])+$rDr['hargaminggu'];
                       $totPremi=$rDr['hargaslebihbasis']*$rDr['beratmuatan'];
                   break;
               }
              
               $grnTot=$grnTot+round($totPremi,2);
           //echo $grnTot;exit(); 
              
            }
            if($_SESSION['empl']['regional']=='SULAWESI')
            {
                $grnTot=$grnTot;
            }
            else
            { 
                
               $iCek="select count(*) as ada,basis,kodevhc 
                  from ".$dbname.".vhc_rundt a  left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
                  left join ".$dbname.".vhc_kegiatan c on a.jenispekerjaan=c.kodekegiatan
                  where a.notransaksi='".$_POST['notransaksi']."' and c.auto=3";
              
               $nCek=mysql_query($iCek) or die (mysql_error($conn));
               $dCek=mysql_fetch_assoc($nCek);
                    $ada=$dCek['ada'];
                    $lbhBsA3=$dCek['basis'];
                    $kdVhc=$dCek['kodevhc'];
                    
               if($ada>0)
               {
                   
                    if(($qwe=='Sun')||($optHariLbr[$_SESSION['empl']['regional']]!='')){//jika hari minggu gk ada target
                            $grnTot2=$grnTot;
                    }
                    else
                    {
                        if($grnTot>$lbhBsA3){//jika lebih basis
                              //exit("Error:MASUK");
                             $grnTot2=(($grnTot-$lbhBsA3)*2)+$lbhBsA3;	
                        }
                        else
                        {
                              $grnTot2=$grnTot;
                        }
                    }	   /*$at=3;
			   $whrt="auto=3 and regional='KALIMANTAN'";
			   $optBasis=makeOption($dbname,"vhc_kegiatan","auto,basis",$whrt);
                           if(($qwe=='Sun')||($optHariLbr[$_SESSION['empl']['regional']]!='')){//jika hari minggu gk ada target
                                   $grnTot2=$grnTot;
                           }else{
                                if($grnTot>$optBasis[$at]){//jika lebih basis
                                      //exit("Error:MASUK");
                                     $grnTot2=(($grnTot-$optBasis[$at])*2)+$optBasis[$at];	

                                }else{
                                     $grnTot2=$grnTot;
                                }
                           }*/
               }
               else
               {
                   $grnTot2=$grnTot;
               }
               
               
               $iKend="select kodevhc from ".$dbname.".vhc_runht where notransaksi='".$_POST['notransaksi']."' ";
               $nKend=mysql_query($iKend) or die (mysql_error($conn));
               $dKend=  mysql_fetch_assoc($nKend);
               
               
               
               //$rDr['kodevhc'
        
               
              // exit("Error:$kdVhc");
                           
                if($posisi==0)
                {
                    $grnTot=$grnTot2;
                } 
                else{
                    if($dKend['kodevhc']=='BS01L' || $dKend['kodevhc']=='SB01L' || $dKend['kodevhc']=='SB02L' || $dKend['kodevhc']=='SB03L')
                    {//jika mobilnya bus sekolah atau speedboat maka patok 15 rb
                        $grnTot=15000; //exit("Error:MASUK");
                    }
                    else
                    {
                        $grnTot=0.15*$grnTot2; 
                    }   
                } 
           }
           
           
           echo $grnTot;
       break;
       case'getOperator':
           $optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $skary="select a.karyawanid,b.nik,b.lokasitugas,a.nama from ".$dbname.".vhc_5operator a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where a.aktif='1' and b.lokasitugas='".$kdOrg."' ";//echo $skary;
            $qkary=mysql_query($skary) or die(mysql_error());
            while($rkary=mysql_fetch_assoc($qkary))
            {
                    $optKary.="<option value=".$rkary['karyawanid'].">".$rkary['nik']." - ".$rkary['nama']."&nbsp;[".$rkary['lokasitugas']."]</option>";
            }
            echo $optKary;
       break;
       case'getMesin':
        $optJns=makeOption($dbname, 'vhc_5jenisvhc', 'jenisvhc,namajenisvhc');
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead>";
        $tab.="<tr>
               <td>".$_SESSION['lang']['kodevhc']."</td>
               <td>".$_SESSION['lang']['jenisvch']."</td>
               <td>".$_SESSION['lang']['detail']."</td>";
        
        $tab.="</tr></thead><tbody>";
        
        if($_POST['txtcari']!=''){
            $isi=" and kodevhc like '%".$_POST['txtcari']."%'";
        }
        $sVhc="select distinct kodevhc,jenisvhc,detailvhc from ".$dbname.".vhc_5master 
               where kodetraksi like '%".$_POST['kdTraksi']."%' and jenisvhc='".$_POST['jnsVhc']."' ".$isi."";
        
        $qVHc=mysql_query($sVhc) or die(mysql_error($conn));
        while($rDt=  mysql_fetch_assoc($qVHc)){
                $clid="onclick=setBlok('".$rDt['kodevhc']."','TRAKSI','".$_POST['pil']."') style=cursor:pointer;";
                $tab.="<tr ".$clid." class=rowcontent>
                       <td>".$rDt['kodevhc']."</td>
                       <td>".$optJns[$rDt['jenisvhc']]."</td>
                       <td>".$rDt['detailvhc']."</td>";
                $tab.="</tr>";
             }
       $tab.="</tbody></table>";
       echo $tab;
    break;
    case'getKegiatan':
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead>";
        $tab.="<tr>
               <td>".$_SESSION['lang']['kodekegiatan']."</td>
               <td>".$_SESSION['lang']['namakegiatan']."</td>
               <td>".$_SESSION['lang']['satuan']."</td>";
        $tab.="</tr></thead><tbody>";
        
        
        if($_POST['txtcari']!=''){
            $isi=" and (kodekegiatan like '%".$_POST['txtcari']."%' or namakegiatan like '%".$_POST['txtcari']."%')";
        }
        $sVhc="select distinct kodekegiatan,namakegiatan,satuan from ".$dbname.".vhc_kegiatan 
               where regional='".$_SESSION['empl']['regional']."' ".$isi."";
        
        $qVHc=mysql_query($sVhc) or die(mysql_error($conn));
        while($rDt=  mysql_fetch_assoc($qVHc)){
                $clid="onclick=setBlok('".$rDt['kodekegiatan']."','".$rDt['satuan']."','".$_POST['pil']."') style=cursor:pointer;";
                $tab.="<tr ".$clid." class=rowcontent>
                       <td>".$rDt['kodekegiatan']."</td>
                       <td>".$rDt['namakegiatan']."</td>
                       <td>".$rDt['satuan']."</td>";
                $tab.="</tr>";
             }
       $tab.="</tbody></table>";
       echo $tab;
    break;
    case'getBlok2':
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead>";
        $tab.="<tr>
               <td>".$_SESSION['lang']['kodeorganisasi']."</td>
               <td>".$_SESSION['lang']['bloklama']."</td>
               <td>".$_SESSION['lang']['namaorganisasi']."</td>";
        $tab.="</tr></thead><tbody>";
        $optTipe=makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');
        if($optTipe[$_POST['lokKerja']]=='KEBUN'){
            if($_POST['txtcari']!=''){
              $isi=" and (kodeorg like '%".$_POST['txtcari']."%' or bloklama like '%".$_POST['txtcari']."%') ";
            }
            $sVhc="select distinct kodeorg as kodeorganisasi,bloklama,namaorganisasi from ".$dbname.".setup_blok a 
                   left join ".$dbname.".organisasi b on a.kodeorg=b.kodeorganisasi
                   where kodeorg like '".$_POST['lokKerja']."%' ".$isi." ";
       }else{
        
            if($_POST['txtcari']!=''){
                $isi=" and (kodeorganisasi like '%".$_POST['txtcari']."%' or namaorganisasi like '%".$_POST['txtcari']."%') ";
            }
            $sVhc="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
                   where (kodeorganisasi like '".$_POST['lokKerja']."%' ".$isi." and char_length(kodeorganisasi)<>'4')";
       }
        $qVHc=mysql_query($sVhc) or die(mysql_error($conn));
        while($rDt=  mysql_fetch_assoc($qVHc)){
                $clid="onclick=setBlok('".$rDt['kodeorganisasi']."','','".$_POST['pil']."') style=cursor:pointer;";
                $tab.="<tr ".$clid." class=rowcontent>
                       <td>".$rDt['kodeorganisasi']."</td>
                       <td>".$rDt['bloklama']."</td>
                       <td>".$rDt['namaorganisasi']."</td>";
                $tab.="</tr>";
             }
       $tab.="</tbody></table>";
       echo $tab;
    break;
    case'getNmKaryawan':
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead>";
        $tab.="<tr>
               <td>".$_SESSION['lang']['nik']."</td>
               <td>".$_SESSION['lang']['namakaryawan']."</td>
               <td>".$_SESSION['lang']['lokasitugas']."</td>";
        $tab.="</tr></thead><tbody>";
        
        
        if($_POST['txtcari']!=''){
            $isi=" and (nik like '%".$_POST['txtcari']."%' or namakaryawan like '%".$_POST['txtcari']."%') ";
        }
        $sVhc="select distinct nik,namakaryawan,lokasitugas,b.karyawanid from ".$dbname.".vhc_5operator b left join ".$dbname.".datakaryawan a
               on b.karyawanid=a.karyawanid where aktif=1 ".$isi." 
               and lokasitugas in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
        //echo $sVhc;
        $qVHc=mysql_query($sVhc) or die(mysql_error($conn));
        while($rDt=  mysql_fetch_assoc($qVHc)){
                $clid="onclick=setBlok('".$rDt['karyawanid']."','','".$_POST['pil']."') style=cursor:pointer;";
                $tab.="<tr ".$clid." class=rowcontent>
                       <td>".$rDt['nik']."</td>
                       <td>".$rDt['namakaryawan']."</td>
                       <td>".$rDt['lokasitugas']."</td>";
                $tab.="</tr>";
             }
       $tab.="</tbody></table>";
       echo $tab;
    break;
    
}
?>
 