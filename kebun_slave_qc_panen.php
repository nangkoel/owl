<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');


$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];

$_POST['tanggalcek']==''?$tanggalcek=tanggalsystem($_GET['tanggalcek']):$tanggalcek=tanggalsystem($_POST['tanggalcek']);

//$_POST['tanggalcek']==''?$tanggalcek=$_GET['tanggalcek']:$tanggalcek=$_POST['tanggalcek'];
$_POST['kdBlok']==''?$kdBlok=$_GET['kdBlok']:$kdBlok=$_POST['kdBlok'];


//$tanggalcek=tanggalsystem($_POST['tanggalcek']);
$tanggalpanen=tanggalsystem($_POST['tanggalpanen']);
$kdDiv=$_POST['kdDiv'];
$kdAfd=$_POST['kdAfd'];
//$kdBlok=$_POST['kdBlok'];
$pusingan=$_POST['pusingan'];
$diperiksa=$_POST['diperiksa'];
$pendamping=$_POST['pendamping'];
$mengetahui=$_POST['mengetahui'];


$nopokok=$_POST['nopokok'];
$jjgpanen=$_POST['jjgpanen'];
$jjgtdkpanen=$_POST['jjgtdkpanen'];
$jjgtdkkumpul=$_POST['jjgtdkkumpul'];
$jjgmentah=$_POST['jjgmentah'];
$jjggantung=$_POST['jjggantung'];
$brdtdkdikutip=$_POST['brdtdkdikutip'];
$rumpukan=$_POST['rumpukan'];
$piringan=$_POST['piringan'];
$jalurpanen=$_POST['jalurpanen'];
$tukulan=$_POST['tukulan'];

$arrSt=array("0"=>"X","1"=>"V");
//$arrSt=array("0"=>$_SESSION['lang']['no'],"1"=>$_SESSION['lang']['yes']);
$perSch=$_POST['perSch'];
$kdDivSch=$_POST['kdDivSch'];

//exit("Error:$mengetahui");
$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$nmKeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$nmCust=makeOption($dbname,'pmn_4customer','kodecustomer,namacustomer');
$nmBarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$nmTranp=makeOption($dbname,'log_5supplier','supplierid,namasupplier');

?>

<?php
switch($method)
{		

        case'getAfd':
                $optAfd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $i="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi  where induk='".$kdDiv."' and tipe='AFDELING'";
                //exit("Error:$i");
                $n=mysql_query($i) or die (mysql_error($conn));
                while($d=mysql_fetch_assoc($n))
                {
                        $optAfd.="<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi']."</option>";
                }
        echo $optAfd;
        break;

        case'getBlok':
                $optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $i="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi  where induk='".$kdAfd."' and tipe='BLOK'";
                //exit("Error:$i");
                $n=mysql_query($i) or die (mysql_error($conn));
                while($d=mysql_fetch_assoc($n))
                {
                        $optBlok.="<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi']."</option>";
                }
        echo $optBlok;
        break;

        case'saveHeader':

                $i="INSERT INTO ".$dbname.".`kebun_qc_panenht` (`tanggalcek`, `kodeblok`, `pusingan`, `tanggalpanen`, `diperiksa`, `pendamping`, `mengetahui`, `updateby`)

                values ('".$tanggalcek."','".$kdBlok."','".$pusingan."','".$tanggalpanen."','".$diperiksa."','".$pendamping."',
                                '".$mengetahui."','".$_SESSION['standard']['userid']."')";


                if(mysql_query($i))
                {
                }
                else
                {
                        echo " Gagal,".addslashes(mysql_error($conn));
                }
        break;


        case'saveDetail':
        $i="INSERT INTO ".$dbname.".`kebun_qc_panendt` (`tanggalcek`, `kodeblok`, `nopokok`, 
                `jjgpanen`, `jjgtdkpanen`, `jjgtdkkumpul`, `jjgmentah`, `jjggantung`, `brdtdkdikutip`,
                `rumpukan`, `piringan`, `jalurpanen`, `tukulan`)

                values ('".$tanggalcek."','".$kdBlok."','".$nopokok."',
                '".$jjgpanen."','".$jjgtdkpanen."','".$jjgtdkkumpul."','".$jjgmentah."','".$jjggantung."','".$brdtdkdikutip."',
                '".$rumpukan."','".$piringan."','".$jalurpanen."','".$tukulan."')";


                if(mysql_query($i))
                {
                }
                else
                {
                        echo " Gagal,".addslashes(mysql_error($conn));
                }
        break;	



        case'getKar':
                #pendamping
                $optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $d="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and kodeunit like '%RO%')  and kodejabatan in (		
			select kodejabatan from ".$dbname.".sdm_5jabatan where namajabatan like '%pengawas%' or namajabatan like '%QC%')";
                $e=mysql_query($d) or die (mysql_error($conn));
                while($f=mysql_fetch_assoc($e))
                {
                        $optKar.="<option value='".$f['karyawanid']."'>".$f['nik']." - ".$f['namakaryawan']."</option>";
                }

                #mengetahui (manager/kadiv)
                $optKadiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $g="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and kodeunit like '%RO%')  and kodejabatan in (		
			select kodejabatan from ".$dbname.".sdm_5jabatan where  namajabatan like '%KEPALA%')";
                $h=mysql_query($g) or die (mysql_error($conn));
                while($i=mysql_fetch_assoc($h))
                {
                        $optKadiv.="<option value='".$i['karyawanid']."'>".$i['nik']." - ".$i['namakaryawan']."</option>";
                }
                echo $optKar."###".$optKadiv;
        break;	

        #####LOAD DETAIL DATA	
        case 'loadDetail';	
        //No.Pokok	JJG Tdk Dipanen	JJG Tdk Dikumpul	JJG Mentah	JJG Menggantung	Brd. Tdk Dikutip	Rumpukan	Piringan	Jalur Panen	Tukulan	Aksi
//`nopokok`, `jjgpanen`, `jjgtdkpanen`, `jjgtdkkumpul`, `jjgmentah`, `jjggantung`, `brdtdkdikutip`, `rumpukan`, `piringan`, `jalurpanen`, `tukulan`) 
                echo"<fieldset><legend>Data Tersimpan</legend>
                        <table class=sortable cellspacing=1 border=0>
                         <thead>
                                 <tr class=rowheader>
                                        <td align=center>".$_SESSION['lang']['nourut']." ".$_SESSION['lang']['pokok']."</td> 
                                        <td align=center>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['panen']."</td>
                                        <td align=center>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['no']." ".$_SESSION['lang']['panen']."</td> 
                                        <td align=center>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['no']." ".$_SESSION['lang']['dikumpul']."</td> 

                                        <td align=center>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['mentah']."</td> 
                                        <td align=center>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['menggantung']."</td> 
                                        <td align=center>".$_SESSION['lang']['brondolan']." ".$_SESSION['lang']['tdkdikutip']."</td> 
                                        <td align=center>".$_SESSION['lang']['rumpukan']."</td>
                                        <td align=center>".$_SESSION['lang']['piringan']."</td>
                                        <td align=center>".$_SESSION['lang']['jalur']." ".$_SESSION['lang']['panen']."</td>
                                        <td align=center>".$_SESSION['lang']['tukulan']."</td>					 
                                        <td align=center>".$_SESSION['lang']['action']."</td>

                                 </tr>
                        </thead>
                        <tbody></fieldset>";

                $no=0;
                $a="select * from ".$dbname.".kebun_qc_panendt where tanggalcek='".$tanggalcek."' and kodeblok='".$kdBlok."' ";
                //exit("Error:$a");
                $b=mysql_query($a) or die(mysql_error());
                while($c=mysql_fetch_assoc($b))
                {
                        $no+=1;
                        echo"<tr class=rowcontent>
                                        <td align=right>".$c['nopokok']."</td>
                                        <td align=right>".$c['jjgpanen']."</td>
                                        <td align=right>".$c['jjgtdkpanen']."</td>
                                        <td align=right>".$c['jjgtdkkumpul']."</td>
                                        <td align=right>".$c['jjgmentah']."</td>
                                        <td align=right>".$c['jjggantung']."</td>
                                        <td align=right>".$c['brdtdkdikutip']."</td>
                                        <td align=center>".$arrSt[$c['rumpukan']]."</td>
                                        <td align=center>".$arrSt[$c['piringan']]."</td>
                                        <td align=center>".$arrSt[$c['jalurpanen']]."</td>
                                        <td align=center>".$arrSt[$c['tukulan']]."</td>
                                        <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"DelDetail('".tanggalnormal($c['tanggalcek'])."','".$c['kodeblok']."','".$c['nopokok']."');\" ></td></tr>";
                        //$tab.="<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"DelDetail('".$bar1['notransaksi']."','".$bar1['karyawanid']."');\" ></td>";
                }
                echo"</table>";
        break;	

        case'printExcel':


        $i="select * from ".$dbname.".kebun_qc_panenht where kodeblok='".$kdBlok."' and tanggalcek='".$tanggalcek."'  ";
        $n=mysql_query($i) or die (mysql_error($conn));
        $d=mysql_fetch_assoc($n);

        //print_r($_SESSION['org']['namaorganisasi']);
        $stream=$_SESSION['org']['namaorganisasi'];

        $stream.="
                        <table>
                                <tr>
                                        <td colspan=11 align=center><b><u>PEMERIKSAAN PANEN</u></b></td>
                                </tr>
                                <tr>
                                        <td colspan=11  align=center>HARVESTING CHECKLIST</td>
                                </tr>
                                <tr>
                                        <td></td>
                                </tr>
                                <tr>
                                        <td colspan=2>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['cek']."</td>
                                        <td colspan=4>: ".tanggalnormal($d['tanggalcek'])."</td>
                                        <td colspan=2>".$_SESSION['lang']['blok']."</td>
                                        <td colspan=4>: ".$d['kodeblok']."</td>
                                </tr>
                                <tr>
                                        <td colspan=2>".$_SESSION['lang']['divisi']."</td>
                                        <td colspan=4>: ".substr($d['kodeblok'],0,4)."</td>
                                        <td colspan=2>".$_SESSION['lang']['pusingan']." ".$_SESSION['lang']['panen']."</td>
                                        <td colspan=4>: ".$d['pusingan']."</td>
                                </tr>
                                <tr>
                                        <td colspan=2>".$_SESSION['lang']['afdeling']."</td>
                                        <td colspan=4>: ".substr($d['kodeblok'],0,6)."</td>
                                        <td colspan=2>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['panen']."</td>
                                        <td colspan=4>: ".tanggalnormal($d['tanggalcek'])."</td>
                                </tr>
                        </table>
        ";

        $stream.="
                        <table class=sortable border=1 cellspacing=1>
                                 <thead>
                                         <tr>
                                                <td align=center valign=top rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['nourut']." ".$_SESSION['lang']['pokok']."</td> 
                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['panen']."</td>
                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['no']." ".$_SESSION['lang']['panen']."</td> 
                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['no']." ".$_SESSION['lang']['dikumpul']."</td> 

                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['mentah']."</td> 
                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['menggantung']."</td> 
                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['brondolan']." ".$_SESSION['lang']['tdkdikutip']."</td> 
                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['rumpukan']."</td>
                                                <td align=center valign=top  colspan=3 bgcolor=#CCCCCC>".$_SESSION['lang']['brondolan']." ".$_SESSION['lang']['tdkdikutip']."</td> 
                                        </tr>
                                        <tr>							
                                                <td align=center valign=top  bgcolor=#CCCCCC>".$_SESSION['lang']['piringan']."</td>
                                                <td align=center valign=top  bgcolor=#CCCCCC>".$_SESSION['lang']['jalur']." ".$_SESSION['lang']['panen']."</td>
                                                <td align=center valign=top  bgcolor=#CCCCCC>".$_SESSION['lang']['tukulan']."</td>					 
                                         </tr>";

        $w="select * from ".$dbname.".kebun_qc_panendt where kodeblok='".$kdBlok."' and tanggalcek='".$tanggalcek."' order by nopokok asc";
        $i=mysql_query($w) or die (mysql_error($conn));
        while($b=mysql_fetch_assoc($i))
        {

                $stream.="<tr class=rowcontent>
                                        <td align=right>".$b['nopokok']."</td>
                                        <td align=right>".$b['jjgpanen']."</td>
                                        <td align=right>".$b['jjgtdkpanen']."</td>
                                        <td align=right>".$b['jjgtdkkumpul']."</td>
                                        <td align=right>".$b['jjgmentah']."</td>
                                        <td align=right>".$b['jjggantung']."</td>
                                        <td align=right>".$b['brdtdkdikutip']."</td>
                                        <td align=center>".$arrSt[$b['rumpukan']]."</td>
                                        <td align=center>".$arrSt[$b['piringan']]."</td>
                                        <td align=center>".$arrSt[$b['jalurpanen']]."</td>
                                        <td align=center>".$arrSt[$b['tukulan']]."</td>";

                        $totJjgpanen+=$b['jjgpanen'];	
                        $totTdkPanen+=$b['jjgtdkpanen'];
                        $totTdkKumpul+=$b['jjgtdkkumpul'];

                        $totJjgMentah+=$b['jjgmentah'];
                        $totJjgGantung+=$b['jjggantung'];
                        $totBrondolan+=$b['brdtdkdikutip'];

                        $totRumpukan+=$b['rumpukan'];
                        $totPiringan+=$b['piringan'];
                        $totJalurPanen+=$b['jalurpanen'];
                        $totTukulan=$b['tukulan'];

        }

        $stream.="<tr>
                                <td align=right>".$_SESSION['lang']['total']."</td>
                                <td align=right>".$totJjgpanen."</td>
                                <td align=right>".$totTdkPanen."</td>
                                <td align=right>".$totTdkKumpul."</td>

                                <td align=right>".$totJjgMentah."</td>
                                <td align=right>".$totJjgGantung."</td>
                                <td align=right>".$totBrondolan."</td>

                                <td align=right>".$totRumpukan."</td>
                                <td align=right>".$totPiringan."</td>
                                <td align=right>".$totJalurPanen."</td>
                                <td align=right>".$totTukulan."</td>



        </tr></table>";


        $stream.="
                        <table>
                                <tr>
                                        <td colspan=3>Ratio Brondolan (B/TBS)</td>
                                        <td>: ".$totBrondolan/$totJjgpanen."</td>
                                </tr>
                                <tr>
                                </tr>
                                <tr>
                                </tr>
                                <tr>
                                </tr>

                                <tr>
                                        <td colspan=3>".$_SESSION['lang']['diperiksa']."</td>
                                        <td colspan=3>".$_SESSION['lang']['pendamping']."</td>
                                        <td colspan=3>".$_SESSION['lang']['mengetahui']."</td>
                                        <td></td>
                                </tr>
                                <tr>
                                </tr>
                                <tr>
                                </tr>
                                <tr>
                                </tr>


                                <tr>
                                        <td colspan=3><b><u>".$nmKar[$d['diperiksa']]."</b></u></td>
                                        <td colspan=3><b><u>".$nmKar[$d['pendamping']]."</b></u></td>
                                        <td colspan=3><b><u>".$nmKar[$d['mengetahui']]."</b></u></td>
                                        <td></td>
                                </tr>
                                <tr>
                                </tr>

                                <tr>
                                        <td colspan=6><b><u>Indicator :</b></u></td>
                                        <td colspan=5><b><u>Catatan lain :</b></u></td>
                                </tr>

                                <tr>
                                        <td colspan=3>Pruning/Rumpukan Pelepah</td>
                                        <td>V = ".$_SESSION['lang']['bagus']."</td>
                                        <td colspan=2>X= ".$_SESSION['lang']['buruk']."</td>
                                        <td colspan=5>1. Brondolan tertinggal di TPH : ...............</td>
                                </tr>
                                <tr>
                                        <td colspan=3>Kondisi Lahan</td>
                                        <td>V= bagus</td>
                                        <td colspan=2>X= jelek</td>
                                        <td colspan=5>2. Brondolan tertinggal di jalan : ..............</td>
                                </tr>
                                <tr>
                                        <td colspan=3>Tukulan Sawit (VOP) </td>
                                        <td>V= bagus</td>
                                        <td>X= jelek</td>
                                </tr>



                        </table>


        ";

        //exit("Error:$stream");



                //exit("Error:$tanggalcek");
                //$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
                //$tglSkrg=date("Ymd");
                $nop_="Laporan_QC_panen_".tanggalnormal($d['tanggalcek']);
                if(strlen($stream)>0)
                {
                        if ($handle = opendir('tempExcel')) {
                                while (false !== ($file = readdir($handle))) {
                                if ($file != "." && $file != "..") {
                                        @unlink('tempExcel/'.$file);
                                }
                                }	
                                closedir($handle);
                        }
                        $handle=fopen("tempExcel/".$nop_.".xls",'w');
                        if(!fwrite($handle,$stream))
                        {
                                echo "<script language=javascript1.2>
                                parent.window.alert('Can't convert to excel format');
                                </script>";
                                exit;
                        }
                        else
                        {
                                echo "<script language=javascript1.2>
                                window.location='tempExcel/".$nop_.".xls';
                                </script>";
                        }
                        closedir($handle);
                }

        break;


        case'loadData':

                if($kdDivSch!='')
                        $kdDivLoad="kodeblok like '%".$kdDivSch."%'";
                else
                        $kdDivLoad="kodeblok!='' ";

                if($perSch!='')
                        $perLoad="and tanggalcek like '%".$perSch."%'";
                else
                        $perLoad="";	

                echo"

                        <table class=sortable cellspacing=1 border=0>
                         <thead>
                                 <tr class=rowheader>
                                        <td align=center>".$_SESSION['lang']['nourut']."</td>
                                         <td align=center>".$_SESSION['lang']['tanggal']."</td>
                                         <td align=center>".$_SESSION['lang']['divisi']."</td>
                                         <td align=center>".$_SESSION['lang']['afdeling']."</td>
                                         <td align=center>".$_SESSION['lang']['blok']."</td>
                                         <td align=center>".$_SESSION['lang']['diperiksa']."</td>
                                         <td align=center>".$_SESSION['lang']['updateby']."</td>
                                         <td align=center>".$_SESSION['lang']['action']."</td>
                                 </tr>
                        </thead>
                        <tbody>";

                        $limit=10;
                        $page=0;
                        if(isset($_POST['page']))
                        {
                        $page=$_POST['page'];
                        if($page<0)
                        $page=0;
                        }
                        $offset=$page*$limit;
                        $maxdisplay=($page*$limit);

                        $ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_panenht where ".$kdDivLoad."  ".$perLoad."  ";// where kodeorg='".$kodeorg."' and periode='".$per."'
                        //exit("Error:$ql2");
                        //where kodeorg='".$kodeorg."' and periode='".$per."' order by lastupdate
                        $query2=mysql_query($ql2) or die(mysql_error());
                        while($jsl=mysql_fetch_object($query2)){
                        $jlhbrs= $jsl->jmlhrow;
                        }
                        $i="select * from ".$dbname.".kebun_qc_panenht where ".$kdDivLoad."  ".$perLoad."  limit ".$offset.",".$limit."";

                        //echo $i;
                        $n=mysql_query($i) or die(mysql_error());
                        $no=$maxdisplay;
                        while($d=mysql_fetch_assoc($n))
                        {
                                $arr="##".$d['kodeblok']."##".$d['tanggalcek']."";	
                                $no+=1;
                                echo "<tr class=rowcontent>";
                                echo "<td align=center>".$no."</td>";
                                echo "<td align=left>".tanggalnormal($d['tanggalcek'])."</td>";
                                echo "<td align=left>".substr($d['kodeblok'],0,4)."</td>";
                                echo "<td align=left>".substr($d['kodeblok'],0,6)."</td>";
                                echo "<td align=left>".$d['kodeblok']."</td>";
                                echo "<td align=left>".$nmKar[$d['diperiksa']]."</td>";
                                echo "<td align=left>".$nmKar[$d['updateby']]."</td>";
                                echo "<td align=center>
                                                <img src=images/application/application_delete.png class=resicon caption='Delete' onclick=\"del('".tanggalnormal($d['tanggalcek'])."','".$d['kodeblok']."');\">		
                                                <img onclick=datakeExcel(event,'".tanggalnormal($d['tanggalcek'])."','".$d['kodeblok']."') src=images/excel.jpg class=resicon title='MS.Excel'></td>";
                                echo "</tr>";/*<img src=images/application/application_edit.png class=resicon  caption='Edit' 
                                        onclick=\"edit('".tanggalnormal($d['tanggal'])."','".substr($d['blok'],0,4)."','".$d['blok']."');\">*/
                        }
                        echo"
                        <tr class=rowheader><td colspan=43 align=center>
                        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                        <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                        <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                        </td>
                        </tr>";
                        echo"</tbody></table>";
                break;


                case'delete':

                        $i="delete from ".$dbname.".kebun_qc_panenht where tanggalcek='".$tanggalcek."' and kodeblok='".$kdBlok."'";

                        if(mysql_query($i))
                        {
                                $n="delete from ".$dbname.".kebun_qc_panendt where tanggalcek='".$tanggalcek."' and kodeblok='".$kdBlok."'";
                                if(mysql_query($n))
                                {
                                }
                                else
                                echo " Gagal,".addslashes(mysql_error($conn));	
                        }
                        else
                        echo " Gagal,".addslashes(mysql_error($conn));
                break;


                case'deleteDetail':
                        $i="delete from ".$dbname.".kebun_qc_panendt where tanggalcek='".$tanggalcek."' and kodeblok='".$kdBlok."' and nopokok='".$nopokok."'";
                        //exit("Error:$i");
                        if(mysql_query($i))
                        echo"";
                        else
                        echo " Gagal,".addslashes(mysql_error($conn));
                break;

        default;
}
?>