<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$tgl=tanggalsystem($_POST['tgl']);
$jmlh=$_POST['jmlh'];
$kdFraksi=$_POST['kdFraksi'];
$noTiket=$_POST['noTiket'];
//$tglCari=tanggalsystem($_POST['tglCari']);
$lokasi=$_SESSION['empl']['lokasitugas'];
$jmlhJJg=$_POST['jmlhJJg'];
$persenBrnd=$_POST['persenBrnd'];
$kgPtngan=$_POST['kgPtngan'];

if($_SESSION['language']=='EN'){
    $zz='keterangan1 as keterangan';
}else{
    $zz='keterangan';
}
        switch($proses)
        {
                case'getTiket':
                $thn=substr($tgl,0,4);
                $bln=substr($tgl,4,2);
                $hari=substr($tgl,6,2);
                $tanggal=$thn."-".$bln."-".$hari;
                $optNotiket="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $sTim="select notransaksi from ".$dbname.".pabrik_timbangan where substr(tanggal,1,10) = '".$tanggal."' and  kodebarang='40000003'";
        //echo "warning:".$sTim;exit();
                $qTim=mysql_query($sTim) or die(mysql_error());
                $row=mysql_num_rows($qTim);
                if($row>0)
                {
                        while($rTim=mysql_fetch_assoc($qTim))
                        {
                                if($noTiket=='0')
                                {
                                        $optNotiket.="<option value=".$rTim['notransaksi'].">".$rTim['notransaksi']."</option>";
                                }
                                else
                                {
                                        $optNotiket.="<option value=".$rTim['notransaksi']." ".($rTim['notransaksi']==$noTiket?'selected':'').">".$rTim['notransaksi']."</option>";
                                }
                        }
                        echo $optNotiket;
                }
                else
                {
                        echo"warning: Weighbridge data is empty";
                        exit();
                }
                break;
                case'getData':
                $sDt="select * from ".$dbname.".pabrik_sortasi where notiket='".$noTiket."' and kodefraksi='".$kdFraksi."'";
                $qDt=mysql_query($sDt) or die(mysql_error());
                $rDt=mysql_fetch_assoc($qDt);
                $sTgl="select tanggal from ".$dbname.".pabrik_timbangan where notransaksi='".$noTiket."'";
                $qTgl=mysql_query($sTgl) or die(mysql_error());
                $rTgl=mysql_fetch_assoc($qTgl);
                echo $rDt['notiket']."###".$rDt['kodefraksi']."###".$rDt['jumlah']."###".tanggalnormal($rTgl['tanggal']);
                break;

                case'LoadData':
                echo"
                    <table cellspacing=1 border=0 class=sortable>
                <thead>
                <tr class=rowheader>
                <td>No.</td>
                <td>".$_SESSION['lang']['noTiket']."</td>
                ";
                    
                $sFraksi="select kode,".$zz.",type from ".$dbname.".pabrik_5fraksi order by kode asc";
                $qFraksi=mysql_query($sFraksi) or die(mysql_error());
                while($rFraksi=mysql_fetch_assoc($qFraksi))
                {
                echo"<td>".$rFraksi['keterangan']." ".($rFraksi['type']!=''?"(".$rFraksi['type'].")":'')."</td> ";
                }
                                 
                echo"<td>".$_SESSION['lang']['sortasi']."(JJG)</td><td> ".$_SESSION['lang']['potongankg']."</td>
                <td>Action</td>
                </tr>
                </thead>
                <tbody>";

                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                $ql2="select notiket from ".$dbname.".pabrik_sortasi group by `notiket` order by `notiket` desc";//echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                $jsl=mysql_num_rows($query2);
                $jlhbrs= $jsl;


                $sNotiket="select notiket from ".$dbname.".pabrik_sortasi group by `notiket` order by `notiket` desc limit ".$offset.",".$limit." ";
                $qNotiket=mysql_query($sNotiket) or die(mysql_error());
                $a=0;
                while($rNotiket=mysql_fetch_assoc($qNotiket))
                {
                        $no+=1;
                        echo"<tr class=rowcontent><td>".$no."</td>";
                        echo"<td>".$rNotiket['notiket']."</td>";
                        $sFraksi="select kode from ".$dbname.".pabrik_5fraksi order by kode asc";
                        $qFraksi=mysql_query($sFraksi) or die(mysql_error());
                        $sJjg="select jjgsortasi,tanggal,persenBrondolan,kgpotsortasi from ".$dbname.".pabrik_timbangan where notransaksi='".$rNotiket['notiket']."'";
                        $qJjg=mysql_query($sJjg) or die(mysql_error());
                        $rJjg=mysql_fetch_assoc($qJjg);
                        while($rFraksi=mysql_fetch_assoc($qFraksi))
                        {
                                $sMax="select notiket,jumlah,kodefraksi from ".$dbname.".pabrik_sortasi where notiket='".$rNotiket['notiket']."' and kodefraksi='".$rFraksi['kode']."'";
                                $qMax=mysql_query($sMax) or die(mysql_error());
                                $rMax=mysql_fetch_assoc($qMax);
                                if($rFraksi['kode']==$rMax['kodefraksi'])
                                {
                                        echo"<td align=right id='".$rFraksi['kode']."##".$rMax['notiket']."' onclick=\"editDetHead('".$rNotiket['notiket']."','".tanggalnormal((substr($rJjg['tanggal'],0,10)))."')\" style=\"cursor:pointer\" >".number_format($rMax['jumlah'],2)."</td>";
                                }
                                else
                                {
                                        echo"<td align=right>".number_format($rMax['jumlah'],2)."</td>";
                                }
                        }
                        //while($a!=$rMax)

                        echo"<td align=right>".number_format($rJjg['jjgsortasi'],0)."</td>";
                        //echo"<td align=right>".number_format($rJjg['persenBrondolan'],0)."</td>";
                        echo"<td align=right>".number_format($rJjg['kgpotsortasi'],2)."</td>";
                        echo"<td>

<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$rNotiket['notiket']."');\"></td></tr>";
                }
                echo"
                <tr><td colspan=17 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";  	

                echo"</tbody></table>";
                break;
                case'insert':
                   // echo"warning";
                    if($noTiket=='')
                    {
                        echo"warning:No Tiket Tidak boleh Kosong";
                        exit();
                    }
                $kdFraksi=$_POST['kdFraksi'];
                $isiData=$_POST['isiData'];
//                    echo"<pre>";
//                    print_r();
//                    echo"</pre><br />";
                foreach ($kdFraksi as $rt =>$isi)
                {
                    if($isiData[$isi]=='')
                    {
                       $isiData[$isi]=0; 
                    }
                    $sCek="select notiket,kodefraksi from ".$dbname.".pabrik_sortasi where notiket='".$noTiket."' and kodefraksi='".$isi."'";
                    $qCek=mysql_query($sCek) or die(mysql_error());
                    $rCek=mysql_num_rows($qCek);
                    if($rCek<1)
                    {
                            $sIns="insert into ".$dbname.".pabrik_sortasi (notiket, kodefraksi, jumlah) values ('".$noTiket."','".$isi."','".$isiData[$isi]."')";
                            if(mysql_query($sIns))
                            {
                               $sCekDt="select jjgsortasi,persenBrondolan from ".$dbname.".pabrik_timbangan where notransaksi='".$noTiket."'";
                               $qCekDt=mysql_query($sCekDt) or die(mysql_error());
                               $rCekDt=mysql_fetch_assoc($qCekDt);
                               if($rCekDt['jjgsortasi']==0||$rCekDt['persenBrondolan']==0)
                               {
                               $sDt="update ".$dbname.".pabrik_timbangan set jjgsortasi='".$jmlhJJg."',persenBrondolan='".$persenBrnd."',kgpotsortasi='".$kgPtngan."' where notransaksi='".$noTiket."'";
                               if(mysql_query($sDt))
                                   echo"";
                               else
                                   echo "DB Error : ".$sDt."__".mysql_error($conn);
                               }
                            }
                            else
                            {
                                echo "DB Error : ".mysql_error($conn);
                            }
                    }
                    else
                    {
                        $sIns="update ".$dbname.".pabrik_sortasi set kodefraksi='".$isi."', jumlah='".$isiData[$isi]."' where notiket='".$noTiket."' and kodefraksi='".$isi."'";
                        if(mysql_query($sIns))
                        {
                         $sDt="update ".$dbname.".pabrik_timbangan set jjgsortasi='".$jmlhJJg."',persenBrondolan='".$persenBrnd."',kgpotsortasi='".$kgPtngan."' where notransaksi='".$noTiket."'";
                           if(mysql_query($sDt))
                               echo"";
                           else
                               echo "DB Error : ".$sDt."__".mysql_error($conn);
                        }
                        else
                        {
                            echo "DB Error : ".$sDt."__".mysql_error($conn);
                        }
                    }
                }
                break;

                case'update':
                    if($noTiket=='')
                    {
                        echo"warning:No Tiket Tidak boleh Kosong";
                        exit();
                    }
                $kdFraksi=$_POST['kdFraksi'];
                $isiData=$_POST['isiData'];
//                    echo"<pre>";
//                    print_r();
//                    echo"</pre><br />";
                foreach ($kdFraksi as $rt =>$isi)
                {

                    if($isiData[$isi]=='')
                    {
                       $isiData[$isi]=0; 
                    }
                    $sCek="select notiket,kodefraksi from ".$dbname.".pabrik_sortasi where notiket='".$noTiket."' and kodefraksi='".$isi."'";
                    $qCek=mysql_query($sCek) or die(mysql_error());
                    $rCek=mysql_num_rows($qCek);
                    if($rCek>0)
                    {
                        $sIns="update ".$dbname.".pabrik_sortasi set kodefraksi='".$isi."', jumlah='".$isiData[$isi]."' where notiket='".$noTiket."' and kodefraksi='".$isi."'";
                        if(mysql_query($sIns))
                        {

                           $sDt="update ".$dbname.".pabrik_timbangan set jjgsortasi='".$jmlhJJg."',persenBrondolan='".$persenBrnd."',kgpotsortasi='".$kgPtngan."' where notransaksi='".$noTiket."'";
                           if(mysql_query($sDt))
                               echo"";
                           else
                               echo "DB Error : ".$sDt."__".mysql_error($conn);

                        }
                        else
                        {
                            echo "DB Error : ".mysql_error($conn);
                        }
                    }
                    else
                    {
                        $sIns="insert into ".$dbname.".pabrik_sortasi (notiket, kodefraksi, jumlah) values ('".$noTiket."','".$isi."','".$isiData[$isi]."')";
                        if(mysql_query($sIns))
                        {
                            $sDt="update ".$dbname.".pabrik_timbangan set jjgsortasi='".$jmlhJJg."',persenBrondolan='".$persenBrnd."',kgpotsortasi='".$kgPtngan."' where notransaksi='".$noTiket."'";
                           if(mysql_query($sDt))
                               echo"";
                           else
                               echo "DB Error : ".$sDt."__".mysql_error($conn);
                        }
                           else
                           {    echo "DB Error : ".$sDt."__".mysql_error($conn);}

                    }

                }    
                // exit("Error".$sIns);
                break;
                case'delData':
                //$where=" notiket='".$noTiket."' and kodefraksi='".$kdFraksi."'";
                $where=" notiket='".$noTiket."'";
                $sDel="delete from ".$dbname.".pabrik_sortasi where  ".$where."";
                if(mysql_query($sDel))
                {
                   $sUpd="update ".$dbname.".pabrik_timbangan set jjgsortasi=0,persenBrondolan=0 where notransaksi='".$noTiket."'";
                   if(mysql_query($sUpd))
                       echo"";
                   else
                       echo "DB Error : ".$sUpd."__".mysql_error($conn);
                }
                else
                {
                    echo "DB Error : ".mysql_error($conn);
                }
                break;

                case'cariData':
                echo"<table cellspacing=1 border=0 class=sortable>
                <thead>
                <tr class=rowheader>
                <td>No.</td>
                <td>".$_SESSION['lang']['noTiket']."</td>
                ";
                $sFraksi="select kode,".$zz.",type from ".$dbname.".pabrik_5fraksi order by kode asc";
                $qFraksi=mysql_query($sFraksi) or die(mysql_error());
                while($rFraksi=mysql_fetch_assoc($qFraksi))
                {
                echo"<td>".$rFraksi['keterangan']." ".($rFraksi['type']!=''?"(".$rFraksi['type'].")":'')."</td> ";
                }
                echo"<td>".$_SESSION['lang']['sortasi']."(JJG)</td><td>% ".$_SESSION['lang']['brondolan']."</td>
                <td>Action</td>
                </tr>
                </thead>
                <tbody>";
                        if($noTiket!='')
                        {
                                $where="where notiket like '%".$noTiket."%'";
                        }

                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                $ql2="select notiket from ".$dbname.".pabrik_sortasi  ".$where." group by `notiket` order by `notiket` desc ";//echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                $jsl=mysql_num_rows($query2);
                $jlhbrs= $jsl;


                $sNotiket="select notiket from ".$dbname.".pabrik_sortasi  ".$where." group by `notiket` order by `notiket` desc  limit ".$offset.",".$limit." ";
                //echo $sNotiket;exit();
                $qNotiket=mysql_query($sNotiket) or die(mysql_error());
                $a=0;
                while($rNotiket=mysql_fetch_assoc($qNotiket))
                {
                        $no+=1;
                        echo"<tr class=rowcontent><td>".$no."</td>";
                        echo"<td>".$rNotiket['notiket']."</td>";
                        $sFraksi="select kode from ".$dbname.".pabrik_5fraksi order by kode asc";
                        $qFraksi=mysql_query($sFraksi) or die(mysql_error());

                        $sJjg="select jjgsortasi,tanggal,persenBrondolan from ".$dbname.".pabrik_timbangan where notransaksi='".$rNotiket['notiket']."'";
                        $qJjg=mysql_query($sJjg) or die(mysql_error());
                        $rJjg=mysql_fetch_assoc($qJjg);
                        while($rFraksi=mysql_fetch_assoc($qFraksi))
                        {
                                $sMax="select notiket,jumlah,kodefraksi from ".$dbname.".pabrik_sortasi where notiket='".$rNotiket['notiket']."' and kodefraksi='".$rFraksi['kode']."'";
                                $qMax=mysql_query($sMax) or die(mysql_error());
                                $rMax=mysql_fetch_assoc($qMax);
                                if($rFraksi['kode']==$rMax['kodefraksi'])
                                {
                                        echo"<td align=right id='".$rFraksi['kode']."##".$rMax['notiket']."' onclick=\"editDetHead('".$rNotiket['notiket']."','".tanggalnormal((substr($rJjg['tanggal'],0,10)))."')\" style=\"cursor:pointer\" >".number_format($rMax['jumlah'],2)."</td>";
                                }
                                else
                                {
                                        echo"<td align=right>".number_format($rMax['jumlah'],2)."</td>";
                                }
                        }
                        //while($a!=$rMax)
                        echo"<td align=right>".number_format($rJjg['jjgsortasi'],2)."</td>";
                        echo"<td align=right>".number_format($rJjg['persenBrondolan'],2)."</td>";
                        echo"<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$rNotiket['notiket']."');\"></td></tr>";
                }
                echo"
                <tr><td colspan=17 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";  	

                echo"</tbody></table>";
                break;
                case'getJenjang':
                $sGet="select jumlahtandan1 from ".$dbname.".pabrik_timbangan where notransaksi='".$noTiket."'";
                $qGet=mysql_query($sGet) or die(mysql_error());
                $rGet=mysql_fetch_assoc($qGet);
                echo $rGet['jumlahtandan1'];
                break;
                case'createTable':
                //=========================
                #ambil potongan sortasi
                    $str="select * from ".$dbname.".pabrik_5pot_fraksi where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by kodefraksi";
                    $res=mysql_query($str);
                    $resx=mysql_query($str);
                    echo"<table class=sortable border=0 cellspacing=1>
                         <thead><tr class=rowheader><td>Kode Fraksi</td><td>Netto</td><td>BJR</td>";                                           
                    while($barf=mysql_fetch_object($res))
                    {
                        echo"<td width=50px align=center>".$barf->kodefraksi."</td>";
                    }
                    echo"</tr></thead>
                         <tbody><tr class=rowcontent><td>CODE * 100(%)</td><td id=nettox></td><td id=bjrx></td>";
                    while($barf=mysql_fetch_object($resx))
                    {
                        echo"<td align=center id=pot".$barf->kodefraksi.">".$barf->potongan."</td>";
                    }   

                    echo"</tr></tbody>
                         <tfoot></tfoot></table>";
                #pembatasan 12.5 persen ada pada javascript    
                //============================    
                $thn=substr($tgl,0,4);
                $bln=substr($tgl,4,2);
                $hari=substr($tgl,6,2);
                $tanggal=$thn."-".$bln."-".$hari;
                $optNotiket="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $sTim="select notransaksi from ".$dbname.".pabrik_timbangan where substr(tanggal,1,10) = '".$tanggal."' and  kodebarang='40000003'";
        //echo "warning:".$sTim;exit();
                $qTim=mysql_query($sTim) or die(mysql_error());
                $row=mysql_num_rows($qTim);
                if($row>0)
                {
                        while($rTim=mysql_fetch_assoc($qTim))
                        {
                                if($noTiket=='0')
                                {
                                        $optNotiket.="<option value=".$rTim['notransaksi'].">".$rTim['notransaksi']."</option>";
                                }
                                else
                                {
                                        $optNotiket.="<option value=".$rTim['notransaksi']." ".($rTim['notransaksi']==$noTiket?'selected':'').">".$rTim['notransaksi']."</option>";
                                }
                        }
                        //echo $optNotiket;
                }
                $table.="<table id='ppDetailTable'>";
                //echo"warning:".$table;
                # Header
                $table .= "<thead>";
                $table .= "<tr>";
                $table .= "<td>".$_SESSION['lang']['noTiket']."</td><td>".$_SESSION['lang']['sortasi']."(JJG)</td>";
                $qHead="select distinct kode,".$zz." from ".$dbname.".pabrik_5fraksi  order by kode asc";
                $zd=mysql_query($qHead);
                $rHead=fetchData($qHead);
               // $brs=count($rHead);
                foreach($rHead as $row =>$isi)
                {
                    $table .= "<td>".$isi['keterangan']."</td>";
                    // $brs+=1;
                }
                $table .= "<td>".$_SESSION['lang']['potongankg']."</td><td>Action</td></tr>";

                $table .= "</thead><tbody>";
                $table.="<tr class=rowcontent><td><select style='width:80px;' id=noTkt name=noTkt onchange=getNetto(this.options[this.selectedIndex].value)>".$optNotiket."</select></td>";          
                $table.="<td><input type=text class=myinputtextnumber style='width:65px;' id=jmlhJJg  onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"4\" value=0  onblur=hitungBJR(this.value,".mysql_num_rows($zd).")></td>";
                foreach($rHead as $row2 =>$isi2)
                {
                    $a++;
                    $arr.="##".$isi2['kode'];
                $table .="<td align=right id=fraksi_".$a." value=".$isi2['kode'].">
                    <input type=text class=myinputtextnumber style='width:65px;' id=inputan_".$a." name=frak".$isi2['kode']." onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"4\" value=0 onblur=hitungPotongan(this.value,'".$isi2['kode']."',".mysql_num_rows($zd).")></td>";
                }
                
                $table.="<td><input type=text class=myinputtextnumber style='width:65px;' id=kgPtngan disabled  onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"4\" value=0  /></td>";
                $table .="<td><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail('".$a."')\" src='images/save.png'/></td>";
                $table.="</tr></tbody></table><input type=hidden id=jmlhBaris value=".$a." />";
                echo $table;



                break;
                case'EditData':
                $thn=substr($tgl,0,4);
                $bln=substr($tgl,4,2);
                $hari=substr($tgl,6,2);
                $tanggal=$thn."-".$bln."-".$hari;   
                $optNotiket="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $sTim="select notransaksi from ".$dbname.".pabrik_timbangan where substr(tanggal,1,10) = '".$tanggal."' and  kodebarang='40000003'";
               // echo "warning:".$sTim."__".$tgl;exit();
                $qTim=mysql_query($sTim) or die(mysql_error());
                $row=mysql_num_rows($qTim);
                if($row>0)
                {
                        while($rTim=mysql_fetch_assoc($qTim))
                        {
                                if($noTiket=='')
                                {
                                        $optNotiket.="<option value=".$rTim['notransaksi'].">".$rTim['notransaksi']."</option>";
                                }
                                else
                                {
                                        $optNotiket.="<option value=".$rTim['notransaksi']." ".($rTim['notransaksi']==$noTiket?'selected':'').">".$rTim['notransaksi']."</option>";
                                }
                        }
                        //echo $optNotiket;
                }
                $sJjg="select jjgsortasi,tanggal,persenBrondolan,kgpotsortasi,beratbersih from ".$dbname.".pabrik_timbangan where notransaksi='".$noTiket."'";
                $qJjg=mysql_query($sJjg) or die(mysql_error());
                $rJjg=mysql_fetch_assoc($qJjg);
                                //=========================
                #ambil potongan sortasi
                    $str="select * from ".$dbname.".pabrik_5pot_fraksi order by kodefraksi";
                    $res=mysql_query($str);
                    $resx=mysql_query($str);
                    echo"<table class=sortable border=0 cellspacing=1>
                         <thead><tr class=rowheader><td>Kode Fraksi</td><td>Netto</td><td>BJR</td>";                                           
                    while($barf=mysql_fetch_object($res))
                    {
                        echo"<td width=50px align=center>".$barf->kodefraksi."</td>";
                    }
                    echo"</tr></thead>
                         <tbody><tr class=rowcontent><td>Standar Potongan*100(%)</td><td id=nettox>".$rJjg['beratbersih']."</td><td id=bjrx>".number_format(($rJjg['beratbersih']/$rJjg['jjgsortasi']),2,".","")."</td>";
                    while($barf=mysql_fetch_object($resx))
                    {
                        echo"<td align=center id=pot".$barf->kodefraksi.">".$barf->potongan."</td>";
                    }   

                    echo"</tr></tbody>
                         <tfoot></tfoot></table>";
                #pembatasan 12.5 persen ada pada javascript    
                //============================ 

                $table.="<table id='ppDetailTable'>";
                //echo"warning:".$table;
                # Header
                $table .= "<thead>";
                $table .= "<tr>";               
                $table .= "<td>".$_SESSION['lang']['noTiket']."</td><td>".$_SESSION['lang']['sortasi']."(JJG)</td>";
                $qHead="select distinct kode,".$zz." from ".$dbname.".pabrik_5fraksi order by kode asc";
                $zd=mysql_query($qHead);
                $rHead=fetchData($qHead);

                foreach($rHead as $row =>$isi)
                {
                    $table .= "<td>".$isi['keterangan']."</td>";
                   $brs+=1;
                }
                $table .= "<td>KG Potongan</td><td>Action</td></tr>";

                $table .= "</thead><tbody>";
                $table.="<tr class=rowcontent><td><select style='width:80px;' id=noTkt name=noTkt disabled>".$optNotiket."</select></td>";          
                $table.="<td><input type=text class=myinputtextnumber style='width:65px;' id=jmlhJJg  onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"4\" value='".$rJjg['jjgsortasi']."'  onblur=hitungBJR(this.value,".mysql_num_rows($zd).")></td>";
                $qData="select * from ".$dbname.".pabrik_sortasi where notiket='".$noTiket."' order by kodefraksi asc";
                $rData=fetchData($qData);
                foreach($rData as $brs =>$dt)
                {
                   $listData[$dt['kodefraksi']]=$dt['jumlah'];
                }
                foreach($rHead as $row2 =>$isi2)
                {
                    $a++;
                    if($listData[$isi2['kode']]=='')
                    {
                        $listData[$isi2['kode']]=0;
                    }
                   $table .="<td align=right id=fraksi_".$a." value=".$isi2['kode'].">
                    <input type=text class=myinputtextnumber style='width:65px;' id=inputan_".$a." onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"4\" value=".$listData[$isi2['kode']]." onblur=hitungPotongan(this.value,'".$isi2['kode']."',".mysql_num_rows($zd).")></td>";
                }
                //$table.="<td><input type=text class=myinputtextnumber style='width:65px;' id=persenBrnd  onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"4\" value='".$rJjg['persenBrondolan']."'  onblur=hitungPotongan(this.value,'BRD',".mysql_num_rows($zd).")></td>";
                $table.="<td><input type=text class=myinputtextnumber style='width:65px;' id=kgPtngan disabled onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"4\" value='".$rJjg['kgpotsortasi']."'  /></td>";
                $table .="<td><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail('".$a."')\" src='images/save.png'/></td>";
                $table.="</tr></tbody></table><input type=hidden id=jmlhBaris value=".$a." />";
                echo $table;
                break;
                case'loadDataDetail':
                echo"<div style=overflow:auto;>
                    <table cellspacing=1 border=0 class=sortable>
                <thead>
                <tr class=rowheader>
                <td>No.</td>
                <td>".$_SESSION['lang']['noTiket']."</td>
                ";
                    $thn=substr($tgl,0,4);
                    $bln=substr($tgl,4,2);
                    $dt=substr($tgl,6,2);
                    $tanggal=$thn."-".$bln."-".$dt;
                $qHead="select distinct kode,".$zz." from ".$dbname.".pabrik_5fraksi order by kode asc";
                $rHead=fetchData($qHead);
                $brs=count($rHead);
                foreach($rHead as $row =>$isi)
                {
                    echo "<td>".$isi['keterangan']."</td>";

                }
                echo"<td>".$_SESSION['lang']['sortasi']."(JJG)</td><td>% ".$_SESSION['lang']['brondolan']."</td><td>%".$_SESSION['lang']['potongankg']."</td><td>Action</td></tr></thead><tbody>";
                $qData="select * from ".$dbname.".pabrik_sortasi a left join ".$dbname.".pabrik_timbangan b on a.notiket=b.notransaksi 
                    where substr(b.tanggal,1,10) = '".$tanggal."'   ";
                $rData=fetchData($qData);
                foreach($rData as $brs =>$dt)
                {
                   $listData[$dt['notiket']][$dt['kodefraksi']]=$dt['jumlah'];
                }

                $sNotiket="select notiket from ".$dbname.".pabrik_sortasi a left join ".$dbname.".pabrik_timbangan b on a.notiket=b.notransaksi 
                    where substr(b.tanggal,1,10)= '".$tanggal."' group by `notiket` order by `notiket`  ";
                //echo $sNotiket;
                $qNotiket=mysql_query($sNotiket) or die(mysql_error());

                while($rNotiket=mysql_fetch_assoc($qNotiket))
                {
                        $no+=1;
                        $sJjg="select jjgsortasi,tanggal,persenBrondolan,kgpotsortasi from ".$dbname.".pabrik_timbangan where notransaksi='".$rNotiket['notiket']."'";
                        $qJjg=mysql_query($sJjg) or die(mysql_error());
                        $rJjg=mysql_fetch_assoc($qJjg);
                        echo"<tr class=rowcontent onclick=\"editDet('".$rNotiket['notiket']."','".tanggalnormal((substr($rJjg['tanggal'],0,10)))."');\" style=\"cursor:pointer\"><td>".$no."</td>";
                        echo"<td>".$rNotiket['notiket']."</td>";
                        $sKdFrak="select kodefraksi from ".$dbname.".pabrik_sortasi where notiket='".$rNotiket['notiket']."'";
                        $rKdFrak=fetchData($sKdFrak);
                        foreach($rHead as $row2 =>$isi2)
                        {         
                            if($listData[$rNotiket['notiket']][$isi2['kode']]=='')
                            {
                                $listData[$rNotiket['notiket']][$isi2['kode']]=0;
                            }
                            echo "<td  align=right>".number_format($listData[$rNotiket['notiket']][$isi2['kode']],2)."</td>";
                        }

                        echo"<td align=right>".number_format($rJjg['jjgsortasi'],2)."</td>";
                        echo"<td align=right>".number_format($rJjg['persenBrondolan'],2)."</td><td align=right>".number_format($rJjg['kgpotsortasi'],2)."</td><td>
                        <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDet('".$rNotiket['notiket']."');\"></td></tr>";
                }


                echo"</tbody></table></div>";
                break;
                case'getNetto':
                    $str="select beratbersih from ".$dbname.".pabrik_timbangan where notransaksi='".$_POST['noticket']."'";
                    $res=mysql_query($str);
                    $netto=0;
                    while($bar=mysql_fetch_object($res))
                    {
                        $netto=$bar->beratbersih;
                    }
                    echo $netto;  
                break;    
                default:
                break;
        }

?>