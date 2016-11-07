<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

//$arr="##id_supplier##tglKrm##jlhKoli##kpd##lokPenerimaan##srtJalan##biaya##ket##method";
$method=$_POST['method'];
$id_supplier=$_POST['id_supplier'];
$jlhKoli=$_POST['jlhKoli'];
$kpd=$_POST['kpd'];
$srtJalan=$_POST['srtJalan'];
$biaya=$_POST['biaya'];
$lokPenerimaan=$_POST['lokPenerimaan'];
$tglKrm=tanggalsystem($_POST['tglKrm']);
$ket=$_POST['ket'];
$karyId=$_POST['karyId'];
$idLokasi=$_POST['idLokasi'];
$optSupplier=makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optSatuanBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$optNmOr=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$nopo=$_POST['nopo'];
$idNomor=$_POST['idNomor'];
$kdPt=$_POST['kdPt'];
$statInputan=$_POST['statInputan'];
$kdbrgc=$_POST['kdbrgc'];
$jmlhc=$_POST['jmlhc'];
$nopoc=$_POST['nopoc'];
//detail pengiriman
$nosj=$_POST['notrans'];
$satbrg=$_POST['satbrg'];
$jmlhbrg=$_POST['jmlhbrg'];
$nopodata=$_POST['nopodata'];
$idFranco=$_POST['idFranco'];
$txtSrc=$_POST['txtSrc'];
$tglSrc=$_POST['tglSrc'];
$arrd=array("1"=>$_SESSION['lang']['expeditor'],"2"=>$_SESSION['lang']['internal']);
	switch($method)
	{
		
                case'getDataPt':
                $tab.="<fieldset style=width:850px;><legend>".$_SESSION['lang']['data']." <span id=kdPte>".$kdPt."</span></legend>";
                $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
                $tab.="<thead><tr class=rowheader>";
                $tab.="<td>No.</td>";
                $tab.="<td>".$_SESSION['lang']['notransaksi']."</td>";
                $tab.="<td>".$_SESSION['lang']['nopo']."</td>";
                $tab.="<td>".$_SESSION['lang']['kodebarang']."</td>";
                $tab.="<td>".$_SESSION['lang']['namabarang']."</td>";
                $tab.="<td>".$_SESSION['lang']['satuan']."</td>";
                $tab.="<td>".$_SESSION['lang']['jumlah']."</td>";
                $tab.="<td>".$_SESSION['lang']['jumlah']." ".$_SESSION['lang']['pengiriman']."</td>";
                $tab.="<td>Action</td>";
                $tab.="</tr><tbody id=dataBarangBapb>";
                $sDtPt="select distinct a.*,b.tanggal,b.tipetransaksi,sum(jumlah-jumlahlalu) as jmlhdata,nopo from ".$dbname.".log_lpbdt a  left join ".$dbname.".log_lpbht b
                        on a.notransaksi=b.notransaksi where 
                        b.kodept='".$kdPt."' and b.tipetransaksi=1 and statussaldo=0 
                        group by kodebarang,notransaksi having jmlhdata>0 order by b.notransaksi desc ";
                 //exit("Error:".$sDtPt);
                $tempNotrans='';
                $qDtPt=mysql_query($sDtPt) or die(mysql_error($conn));
                while($rDtPt=mysql_fetch_assoc($qDtPt))
                {
                    $no++;
                    if($rDtPt['notransaksi']!=$tempNotrans)
                    {
                        $dt='';
                        $jmlhRow='';
                        $sRow="select count(notransaksi) as jmlhrow from ".$dbname.".log_lpbdt where notransaksi='".$rDtPt['notransaksi']."'";
                        $qRow=mysql_query($sRow) or die(mysql_error($conn));
                        $rRow=mysql_fetch_assoc($qRow);
                        $dt=$no;
                        $jmlhRow=$rRow['jmlhrow'];
                        $tempNotrans=$rDtPt['notransaksi'];
                    }
                    if($rDtPt['jumlahlalu']!=0)
                    {
                        $rDtPt['jumlah']=$rDtPt['jumlah']-$rDtPt['jumlahlalu'];
                    }
                    $tab.="<tr class=rowcontent><td>".$no."</td>";
                    $tab.="<td id=notrans_c".$no." onclick=centangSma('".$dt."','".$jmlhRow."') style=cursor:pointer>".$rDtPt['notransaksi']."</td>";
                    $tab.="<td id=nopo_c".$no." onclick=centangSma('".$dt."','".$jmlhRow."') style=cursor:pointer>".$rDtPt['nopo']."</td>";
                    $tab.="<td id=kdBarang_c".$no." onclick=centangSma('".$dt."','".$jmlhRow."') style=cursor:pointer>".$rDtPt['kodebarang']."</td>";
                    $tab.="<td onclick=centangSma('".$dt."','".$jmlhRow."')  style=cursor:pointer>".$optNmBrg[$rDtPt['kodebarang']]."</td>";
                    $tab.="<td onclick=centangSma('".$dt."','".$jmlhRow."')  style=cursor:pointer>".$rDtPt['satuan']."</td>";
                    $tab.="<td align=right id=jmlhBarang_c".$no.">".$rDtPt['jumlah']."</td>";
                    $tab.="<td align=right id=jmlhBarang_lalu".$no.">".$rDtPt['jumlahlalu']."</td>";
                    $tab.="<td align=center><input type=checkbox id='dtBpab_".$no."' /></td></tr>";
                }
                $tab.="<tr><td colspan=7 align=center><button name=process id=process onclick=process()>". $_SESSION['lang']['proses']."</button>
		<button name=cancel id=cancel onclick=normalView()>". $_SESSION['lang']['cancel']."</button></td></tr>";
                $tab.="</tbody></table></fieldset>";
                echo $tab;
                break;
                case'createTable':
                   
                    if($statInputan==0)
                    {
                        $thnskrng=date("Y");
                        $notrans="/".date("Y")."/SP/MA/".$kdPt;
                        $sCek="select distinct nosj from ".$dbname.".log_pengiriman_ht where nosj like '%".$notrans."%' order by nosj desc";
                        // exit("Error:".$sCek);
                        $qCek=mysql_query($sCek) or die(mysql_error($conn));
                        $rCek=mysql_fetch_assoc($qCek);
                        $awal=substr($rCek['nosj'],0,3);
                        $awal=intval($awal);
                        $cekthn=substr($rCek['nosj'],7,4);
                        if($thnskrng!=$cekthn)
                        {
                            $awal=1;
                        }
                        else
                        {
                            $awal+=1;
                        }
                        $counter=addZero($awal,3);
                        $notrans=$counter."/".date("m")."/".date("Y")."/SP/MA/".$kdPt;
                        $tglharini=date("d-m-Y");
                        $ketrngan='';
                        $biayakirim=0;
                        $loktrima='';
                        $jmlhkoli=0;
                        $kpd='';
                        $supplierd='';
                    }
                    else
                    {
                        $sHeader="select distinct * from ".$dbname.".log_pengiriman_ht where nosj='".$nosj."'";
                        $qHeader=mysql_query($sHeader) or die(mysql_error($conn));
                        $rHeader=mysql_fetch_assoc($qHeader);
                        $ketrngan=$rHeader['keterangan'];
                        $biayakirim=$rHeader['biaya'];
                        $loktrima=$rHeader['lokasipenerima'];
                        $jmlhkoli=$rHeader['jumlahkoli'];
                        $notrans=$nosj;
                        $supplierd=$rHeader['expeditor'];
                        $Pengirim=$rHeader['pengirim'];
                        $tglharini=tanggalnormal($rHeader['tanggalkirim']);
                        $kpd=$rHeader['kepada'];
                        $franco=$rHeader['id_franco'];
                        $berat=$rHeader['berat'];
                        $moda_trams=$rHeader['moda_trans'];
                        $biyperkg=$rHeader['biayaperkg'];
                        $biyperpcking=$rHeader['biayapacking'];
                    }
                $piland=2;
                $dert=$supplierd;
                $dissupplier="disabled=disabled";
                $diskaryawan="";
                if(substr($supplierd,0,1)=='S'){
                    $piland=1;
                    $dert="";
                    $dissupplier="";
                    $diskaryawan="disabled=disabled";
                }
                    $optKary2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                    $sKary2="select  karyawanid,namakaryawan from ".$dbname.".datakaryawan 
                             where tipekaryawan in ('0','1','2') and sistemgaji='Bulanan' and 
                             tanggalkeluar='0000-00-00' and karyawanid!='".$_SESSION['standard']['userid']."' order by namakaryawan asc";
                    $qKary2=mysql_query($sKary2) or die(mysql_error());
                    while($rKary2=mysql_fetch_assoc($qKary2))
                    {
                        if($dert!=''){
                            $optKary2.="<option value='".$rKary2['karyawanid']."' ".($rKary2['karyawanid']==$dert?"selected":"").">".$rKary2['namakaryawan']."</option>";  
                        }else{
                            $optKary2.="<option value='".$rKary2['karyawanid']."'>".$rKary2['namakaryawan']."</option>";  
                        }
                    }
                $sql="select namasupplier,supplierid from ".$dbname.".log_5supplier order by namasupplier asc";
                $optSupplier="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $query=mysql_query($sql) or die(mysql_error());
                while($res=mysql_fetch_assoc($query))
                {
                    if($supplierd!='')
                    {
                        $optSupplier.="<option value='".$res['supplierid']."' ".($res['supplierid']==$supplierd?"selected":"").">".$res['namasupplier']."</option>";
                    }
                    else
                    {
                        $optSupplier.="<option value='".$res['supplierid']."'>".$res['namasupplier']."</option>";
                    }
                    
                }
                if($kdPt=='')
                {
                    $kdPt=substr($nosj,18,3);
                }
                $optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $sKary="select  kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$kdPt."' and tipe='KEBUN' 
                        order by namaorganisasi asc";
                $qKary=mysql_query($sKary) or die(mysql_error());
                while($rKary=mysql_fetch_assoc($qKary))
                {
                    if($kpd!='')
                    {
                        $optKary.="<option value='".$rKary['kodeorganisasi']."' ".($rKary['kodeorganisasi']==$loktrima?"selected":"").">".$rKary['namaorganisasi']."</option>";
                    }
                    else
                    {
                        $optKary.="<option value='".$rKary['kodeorganisasi']."'>".$rKary['namaorganisasi']."</option>";
                    }
                   
                }
                $optFranco="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $sFranco="select distinct id_franco,franco_name from ".$dbname.".setup_franco order by franco_name asc";
                $qFranco=mysql_query($sFranco) or die(mysql_error($conn));
                while($rFranco=mysql_fetch_assoc($qFranco))
                {
                    if($franco!='')
                    {
                        $optFranco.="<option value='".$rFranco['id_franco']."' ".($franco==$rFranco['id_franco']?"selected":"").">".$rFranco['franco_name']."</option>";
                    }
                    else
                    {
                        $optFranco.="<option value='".$rFranco['id_franco']."'>".$rFranco['franco_name']."</option>";
                    }
                }
               
                
                $optpildt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                
                foreach($arrd as $lstdt=>$isiArrd){
                      $optpildt.="<option value='".$lstdt."' ".($lstdt==$piland?"selected":"").">".$isiArrd."</option>";
                }
                $arragama=getEnum($dbname,'log_pengiriman_ht','moda_trans');
                foreach($arragama as $kei=>$fal)
                {
                        $optmoda.="<option value='".$kei."' ".($kei==$moda_trams?"selected":"").">".$fal."</option>";
                }  
                $tab.="<fieldset style=width:850px;float:left;>
                       <legend>INTERNAL DELIVERY</legend>
                 <table>
                 <tr>
                   <td>".$_SESSION['lang']['suratjalan']."</td>
                   <td><input type=text class=myinputtext id=srtJalan name=srtJalan disabled style=\"width:150px;\" value='".$notrans."'  /></td>
                 </tr> 
                 <tr>
                   <td>".$_SESSION['lang']['tgl_kirim']."</td>
                   <td>
                   <input type=text class=myinputtext id=tglKrm onmousemove=setCalendar(this.id) onkeypress='return false';  size=10 maxlength=10 style=\"width:150px;\" value='".$tglharini."' /></td>
                 </tr>
                  <tr>
                   <td>Number of Package</td>
                   <td><input type=text class=myinputtextnumber id=jlhKoli name=jlhKoli onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" value='".$jmlhkoli."' /> </td>
                 </tr>
                 <tr>
                   <td>".$_SESSION['lang']['status']."</td>
                   <td><select id=idPilihanExin  style='width:150px' onchange='ubahdata()'>".$optpildt."</select></td>
                 </tr> 
                 <tr>
                   <td>".$_SESSION['lang']['expeditor']."</td>
                   <td><select id=\"id_supplier\" name=\"id_supplier\" style=\"width:150px;\" ".$dissupplier." >".$optSupplier."</select>&nbsp;
                 <img src='images/search.png' class=dellicon title='".$_SESSION['lang']['findRkn']."' onclick=\"searchSupplier('".$_SESSION['lang']['findRkn']."','<fieldset><legend>".$_SESSION['lang']['findRkn']."</legend>".$_SESSION['lang']['namasupplier']."&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event);\"></td>
                 </tr> 
                 <tr>
                   <td>".$_SESSION['lang']['internal']."</td>
                   <td><select id=\"id_internal\" name=\"id_internal\" style=\"width:150px;\"  ".$diskaryawan."  >".$optKary2."</select>&nbsp;
                   <img src='images/search.png' class=dellicon title='".$_SESSION['lang']['find']."' onclick=\"searchInternal('".$_SESSION['lang']['find']."','<fieldset><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['namakaryawan']."</legend>".$_SESSION['lang']['namakaryawan']."&nbsp;<input type=text class=myinputtext id=nmKaryawan><button class=mybutton onclick=findKaryawan()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerKaryawan style=overflow=auto;height=380;width=485></div>',event);\" /></td>
                 </tr> 
                  <tr>
                   <td>".$_SESSION['lang']['lokasipenerimaan']."</td>
                   <td><select id=lokPenerimaan  style=width:150px>".$optKary."</select></td>
                 </tr> 
                  <tr>
                   <td>Franco</td>
                   <td><select id=franco_id style=width:150px>".$optFranco."</select></td>
                 </tr> 
                 <tr>
                   <td>".$_SESSION['lang']['modatransportasi']."</td>
                   <td><select id=moda_trans style=width:150px>".$optmoda."</select></td>
                 </tr> 
                 <tr>
                   <td>".$_SESSION['lang']['berat']."</td>
                   <td><input type=text class=myinputtextnumber onkeypress='return angka_doang(event)' id=beratKg style=\"width:150px;\" onblur='kaliaja()' value='".$berat."' /> Kg</td>
                 </tr> 
                 <tr>
                   <td>".$_SESSION['lang']['biaya']." per Kg</td>
                   <td><input type=text class=myinputtextnumber onkeypress='return angka_doang(event)' id=biayaPerkg style=\"width:150px;\" onblur='kaliaja()'  value='".$biyperkg."'   /></td>
                 </tr> 
                  <tr>
                   <td>".$_SESSION['lang']['biaya']." Packing</td>
                   <td><input type=text class=myinputtextnumber id=biayapckng name=biayapckng onkeypress=\"return angka_doang(event);\" style=\"width:150px;\"  value='".$biyperpcking."'  onblur='kaliaja()' /></td>
                 </tr> 
                 <tr>
                   <td>".$_SESSION['lang']['biaya']."</td>
                   <td><input type=text class=myinputtextnumber id=biaya name=biaya onkeypress=\"return angka_doang(event);\" style=\"width:150px;\"  value='".$biayakirim."'  /></td>
                 </tr> 
                
                 <tr>
                   <td>".$_SESSION['lang']['keterangan']."</td>
                   <td><input type=text class=myinputtext id=ket name=ket onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" value='".$ketrngan."'  /></td>
                 </tr> 
                 </table>";
                $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
                $tab.="<thead><tr class=rowheader>";
                $tab.="<td>".$_SESSION['lang']['notransaksi']."</td>";
                $tab.="<td>".$_SESSION['lang']['nopo']."</td>";
                $tab.="<td>".$_SESSION['lang']['kodebarang']."</td>";
                $tab.="<td>".$_SESSION['lang']['namabarang']."</td>";
                $tab.="<td>".$_SESSION['lang']['satuan']."</td>";
                $tab.="<td>".$_SESSION['lang']['jumlah']."</td>";
                $tab.="<td>".$_SESSION['lang']['jumlah']." ".$_SESSION['lang']['pengiriman']."</td>";
                $tab.="<td>Action</td></tr><tbody id=detailDtBarang>";
                if($statInputan==0)
                {
                    foreach($_POST['notransc'] as $rw=>$hsl)
                    {
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td id=notrans_".$rw.">".$hsl."</td>";
                    $tab.="<td id=nopodata_".$rw.">".$nopoc[$rw]."</td>";
                    $tab.="<td id=kdbarang_".$rw.">".$kdbrgc[$rw]."</td>";
                    $tab.="<td>".$optNmBrg[$kdbrgc[$rw]]."</td>";
                    $tab.="<td id=satuanbrg_".$rw.">".$optSatuanBrg[$kdbrgc[$rw]]."</td>";
                    $tab.="<td id=jmlh_total_".$rw.">".$jmlhc[$rw]."</td>";
                    $tab.="<td><input type=text class=myinputtextnumber id=jmlhBarang_".$rw." value='".$jmlhc[$rw]."' onblur=checkData(".$rw.") /></td>";
                    $tab.="<td align=center><img id='detail_delete_".$rw."' title='Hapus' class=zImgBtn onclick=\"deleteDetail('".$rw."')\" src='images/delete_32.png'/></td>";
                    $tab.="</tr>";
                    }
                    
                }
                else
                {
                    $rw=0;
                    $sData="select distinct * from ".$dbname.".log_pengiriman_dt where nosj='".$nosj."'";
                    $qData=mysql_query($sData) or die(mysql_error($sData));
                    while($rData=mysql_fetch_assoc($qData))
                    {
                        $sJmlhTotal="select distinct jumlah,jumlahlalu from ".$dbname.".log_lpbdt 
                                     where kodebarang='".$rData['kodebarang']."' and notransaksi='".$rData['notransaksi']."'";
                        $qJmlhTotal=mysql_query($sJmlhTotal) or die(mysql_error($conn));
                        $rJmlhTotal=mysql_fetch_assoc($qJmlhTotal);
                        $rJmlhTotal['total']=$rJmlhTotal['jumlah'];
                        if($rJmlhTotal['jumlahlalu']!=0)
                        {
                            $rJmlhTotal['total']=$rJmlhTotal['jumlahlalu'];
                        }
                        $tab.="<tr class=rowcontent id=detail_tr_".$rw."'d>";
                        $tab.="<td id=notrans_".$rw.">".$rData['notransaksi']."</td>";
                        $tab.="<td id=nopodata_".$rw.">".$rData['nopo']."</td>";
                        $tab.="<td id=kdbarang_".$rw.">".$rData['kodebarang']."</td>";
                        $tab.="<td>".$optNmBrg[$rData['kodebarang']]."</td>";
                        $tab.="<td id=satuanbrg_".$rw.">".$rData['satuan']."</td>";
                        $tab.="<td id=jmlh_total_".$rw.">".$rJmlhTotal['total']."</td>";
                        $tab.="<td><input type=text class=myinputtextnumber id=jmlhBarang_".$rw." value=".$rData['jumlahbrg']." onblur=checkData(".$rw.") /></td>";
                        $tab.="<td align=center><img id='detail_delete_".$rw."' title='Hapus' class=zImgBtn onclick=\"deleteDetail('".$rw."')\" src='images/delete_32.png'/></td>";
                        $tab.="</tr>";
                        $rw++;
                    }
                }
                $tab.="<tr><td align=center colspan=6><button class=mybutton onclick=saveFranco()>".$_SESSION['lang']['save']."</button>
                <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button></td></tr>";
                $tab.="</tbody></table>
                </fieldset>";
                echo $tab;
                break;
            
            
            
                case'insert':
		if($id_supplier==''||$tglKrm==''||$jlhKoli==''||$biaya==''||$_POST['beratKg']==''||$_POST['biayaPerkg']=='')
		{
			echo"warning: Field can not be empty";
			exit();
		}
                
                if($statInputan==0)
                {
                    foreach($_POST['kdbrg'] as $bras=>$kodebarang)
                    {
                        if($jmlhbrg[$bras]==''||$jmlhbrg[$bras]=='0')
                        {
                            exit("Error: goods volume must greater than 0");
                        }
                    }
                    $sIns="insert into ".$dbname.".log_pengiriman_ht ( jumlahkoli, expeditor, tanggalkirim, pengirim, lokasipengirim, nosj,keterangan, biaya,lokasipenerima,id_franco,moda_trans,berat,biayaperkg,biayapacking) 
                           values ('".$jlhKoli."','".$id_supplier."','".$tglKrm."','".$_SESSION['standard']['userid']."','".$_SESSION['empl']['lokasitugas']."'
                                   ,'".$srtJalan."','".$ket."','".$biaya."','".$lokPenerimaan."','".$idFranco."','".$_POST['moda_trans']."','".$_POST['beratKg']."','".$_POST['biayaPerkg']."','".$_POST['biayapckng']."')";
                    if(mysql_query($sIns))
                    {
                        foreach($_POST['kdbrg'] as $bras=>$kodebarang)
                        {
                            $sInsert="insert into ".$dbname.".log_pengiriman_dt (nosj, nopo, kodebarang, satuan, notransaksi, jumlahbrg) values 
                                      ('".$srtJalan."','".$nopodata[$bras]."','".$kodebarang."','".$satbrg[$bras]."'
                                      ,'".$nosj[$bras]."','".$jmlhbrg[$bras]."')";
                           if(mysql_query($sInsert))
                           {
                               $suPdate="update ".$dbname.".log_lpbdt set jumlahlalu='".$jmlhbrg[$bras]."' 
                                         where notransaksi='".$nosj[$bras]."' and kodebarang='".$kodebarang."'";
                               if(!mysql_query($suPdate))
                               {
                                    echo"Gagal: update log_lbpdt failed".mysql_error($conn);
                               }
                           }
                           else
                           {
                               echo"Gagal: update log_pengiriman_dt failed".mysql_error($conn);
                           }
                        }
                    }
                    else
                    {
                            echo"Gagal: update log_pengiriman_ht failed ".mysql_error($conn);
                    }
                }
                else
                {
                    foreach($_POST['kdbrg'] as $bras=>$kodebarang)
                    {
                        if($jmlhbrg[$bras]==''||$jmlhbrg[$bras]=='0')
                        {
                            exit("Error: Goods volume must greater than 0");
                        }
                    }//,moda_trans,berat,biayaperkg
                    $sIns="update ".$dbname.".log_pengiriman_ht set jumlahkoli='".$jlhKoli."', expeditor='".$id_supplier."', 
                           tanggalkirim='".$tglKrm."', pengirim='".$_SESSION['standard']['userid']."', 
                           lokasipengirim='".$_SESSION['empl']['lokasitugas']."',  
                           keterangan='".$ket."', biaya='".$biaya."',lokasipenerima='".$lokPenerimaan."',id_franco='".$idFranco."'
                           ,moda_trans='".$_POST['moda_trans']."',berat='".$_POST['beratKg']."',biayaperkg='".$_POST['biayaPerkg']."'
                           ,biayapacking='".$_POST['biayapckng']."'
                           where nosj='".$srtJalan."'";
                    if(mysql_query($sIns))
                    {
                        foreach($_POST['kdbrg'] as $bras=>$kodebarang)
                        {
                            $sInsert="update ".$dbname.".log_pengiriman_dt set  jumlahbrg='".$jmlhbrg[$bras]."'
                                      where  nosj='".$srtJalan."' and  notransaksi='".$nosj[$bras]."' and kodebarang='".$kodebarang."'";
                           if(mysql_query($sInsert))
                           {
                               $suPdate="update ".$dbname.".log_lpbdt set jumlahlalu=jumlahlalu-'".$jmlhbrg[$bras]."' 
                                         where notransaksi='".$nosj[$bras]."' and kodebarang='".$kodebarang."'";
                               if(!mysql_query($suPdate))
                               {
                                    echo"Gagal: update log_lbpdt failed ".mysql_error($conn);
                               }
                           }
                           else
                           {
                               echo"Gagal: update log_pengiriman_dt failed ".mysql_error($conn);
                           }
                        }
                    }
                    else
                    {
                            echo"Gagal: insert log_pengiriman_ht failed ".mysql_error($conn);
                    }
                }
			
		break;
		case'loadData':
		$no=0;	 
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                if($txtSrc!='')
                {
                    $where.=" and nosj like '%".$txtSrc."%'";
                }
                if($tglSrc!='')
                {
                    $where.=" and tanggal='".$tglSrc."'";
                }
                $sql2="select * from ".$dbname.".log_pengiriman_ht 
                      where lokasipengirim ='".$_SESSION['empl']['lokasitugas']."' ".$where." order by lastupdate desc";
                //echo $sql2;
                $query2=mysql_query($sql2) or die(mysql_error());
                $jlhbrs=mysql_num_rows($query2);
                if($jlhbrs!=0)
                {
		$str="select * from ".$dbname.".log_pengiriman_ht 
                      where lokasipengirim ='".$_SESSION['empl']['lokasitugas']."' ".$where." order by lastupdate desc limit ".$offset.",".$limit." ";
		$res=mysql_query($str);
                
		while($bar=mysql_fetch_assoc($res))
		{
                    if(substr($bar['expeditor'],0,1)=='S'){
                        $drtet=$optSupplier[$bar['expeditor']];
                        $pildt=1;
                    }else{
                        $drtet=$optKary[$bar['expeditor']];
                        $pildt=2;
                    }
                    $no+=1;	
                    echo"<tr class=rowcontent>
                    <td>".$no."</td>
                    <td>".$bar['nosj']."</td>
                    <td>".$arrd[$pildt]."</td>
                    <td>".$drtet."</td>
                    <td>".tanggalnormal($bar['tanggalkirim'])."</td>
                    <td align=right>".$bar['jumlahkoli']."</td>
                    <td>".$optKary[$bar['kepada']]."</td>
                    <td>".$optNmOr[$bar['lokasipenerima']]."</td>
                    <td>".$bar['moda_trans']."</td>
                    <td align=right>".number_format($bar['berat'],0)."</td>
                    <td align=right>".number_format($bar['biaya'],2)."</td>";
                    if($bar['penerima']=='0000000000')
                    {
                        
                    echo"
                    <td>
                        <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['nosj']."');\"> 
                        <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['nosj']."');\">
                        <img src=images/pdf.jpg class=resicon  title='Surat Jalan ".$_SESSION['lang']['pdf']."' onclick=\"suratJalan('".$bar['nosj']."',event);\">
                        <img src=images/pdf.jpg class=resicon  title='Untuk Ke Keuangan ".$_SESSION['lang']['pdf']."' onclick=\"suratJalan2('".$bar['nosj']."',event);\">";
                    
                        //<img src=images/pdf.jpg class=resicon  title='Daftar Barang ".$_SESSION['lang']['pdf']."' onclick=\"detailBarang('".$bar['nosj']."',event);\"> 
                      echo"</td>";
                }
                else
                {
                     echo"
		<td>
                    <img src=images/pdf.jpg class=resicon  title='Surat Jalan ".$_SESSION['lang']['pdf']."' onclick=\"suratJalan('".$bar['nosj']."',event);\"> 
                    <img src=images/pdf.jpg class=resicon  title='Untuk Ke Keuangan ".$_SESSION['lang']['pdf']."' onclick=\"suratJalan2('".$bar['nosj']."',event);\">";
                    //<img src=images/pdf.jpg class=resicon  title='Daftar Barang ".$_SESSION['lang']['pdf']."' onclick=\"detailBarang('".$bar['nosj']."',event);\"> 
		  echo"</td>";
                }
		echo"
		</tr>";	
                
		} 
                echo"
		<tr class=rowheader><td colspan=12 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
                }
                else
                {
                    echo "<tr class=rowcontent><td colspan=12>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
		break;
		
		case'delData':
                $sUpdate="select distinct penerima from ".$dbname.".log_pengiriman_ht where nosj='".$nosj."'";
                //exit("Error:".$sUpdate);
                $qUpdate=mysql_query($sUpdate) or die(mysql_error($conn));
                $rUpdate=mysql_fetch_assoc($qUpdate);
                if($rUpdate['penerima']!='0000000000')
                {
                    exit("Error: has been receipt before");
                }
                else
                {

                    $sData="select distinct notransaksi,jumlahbrg,kodebarang from ".$dbname.".log_pengiriman_dt where nosj='".$nosj."'";
                    //exit("Error:".$sData);
                    $qData=mysql_query($sData) or die(mysql_error($conn));
                    while($rData=mysql_fetch_assoc($qData))
                    {
                        $sUpdate="update ".$dbname.".log_lpbdt set jumlahlalu=jumlahlalu-".$rData['jumlahbrg']."
                                  where notransaksi='".$rData['notransaksi']."' and kodebarang='".$rData['kodebarang']."'";
                         if(!mysql_query($sUpdate))
                        {
                            echo"Gagal".mysql_error($conn);
                        }
                    }
                    
                    $sDel="delete from ".$dbname.".log_pengiriman_ht where nosj='".$nosj."'";
                    if(!mysql_query($sDel))
                    {
                        echo"Gagal".mysql_error($conn);
                    }
                   
                }
		break;
		case'getData':
		$sDt="select * from ".$dbname.".log_pengiriman_ht where nomor='".$idNomor."'";
		$qDt=mysql_query($sDt) or die(mysql_error($conn));
		$rDet=mysql_fetch_assoc($qDt);
		echo $rDet['nomor']."###".$rDet['expeditor']."###".tanggalnormal($rDet['tanggalkirim'])."###".$rDet['jumlahkoli']."###".$rDet['kepada']."###".$rDet['lokasipenerima']."###".$rDet['nosj']."###".$rDet['biaya']."###".$rDet['keterangan'];
		break;
                case'getLokasi':
                    if($idLokasi=='')
                    {
                    $sLokTgs="select distinct lokasitugas from ".$dbname.".datakaryawan where karyawanid='".$kpd."'";
                    $qLokTgs=mysql_query($sLokTgs) or die(mysql_error());
                    $rLokTgs=mysql_fetch_assoc($qLokTgs);
                    echo $rLokTgs['lokasitugas'];
                    }
                    else
                    {
                        echo $idLokasi;
                    }
                    unset($karyId,$idLokasi);
                break;
                case'cariData':
                           
                            $tab.="<table cellspacing=1 border=0 class=data>
                            <thead>
                            <tr class=rowheader><td>No</td>
                            <td>".$_SESSION['lang']['nopo']."</td>
                            <td>".$_SESSION['lang']['pt']."</td>
                            <td>".$_SESSION['lang']['tanggal']."</td>
                            <td>".$_SESSION['lang']['purchaser']."</td>
                            </tr>
                            </thead>
                            </tbody>";
                            if($nopo!='')
                            {
                                    $str="select * from ".$dbname.".log_poht where nopo like '%".$nopo."%'
                                    order by tanggal desc,nopo desc";
                                    $res=mysql_query($str);
                                    $no=0;
                                    while($bar=mysql_fetch_object($res))
                                    {
                                    //ambil userid purchaser
                                    $purchaser='';
                                    if($bar->karyawanid!='')
                                    {
                                    $str="select namauser from ".$dbname.".user where karyawanid=".$bar->karyawanid;
                                    $resv=mysql_query($str);
                                    while($barv=mysql_fetch_object($resv))
                                    {
                                    $purchaser=$barv->namauser;
                                    }
                                    }
                                    $no+=1;
                                    $tab.="
                                    <tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=goPickPo('".$bar->nopo."')><td>".$no."</td>
                                    <td>".$bar->nopo."</td>
                                    <td>".$bar->kodeorg."</td>
                                    <td>".tanggalnormal($bar->tanggal)."</td>
                                    <td>".$purchaser."</td>
                                    </tr>
                                    ";


                                    }	 	
                            }
                            $tab.="</tbody>
                            <tfoot>
                            </tfoot>
                            </table>";	
                            
                            $tab2.="<table class=sortable cellpadding=1 cellspacing=1 border=0>";
                            $tab2.="<thead><tr class=rowheader><td>".$_SESSION['lang']['nopo']."</td><td>Action</td></tr></thead><tbody>";
                            $sData="select distinct * from ".$dbname.".log_pengiriman_dt where nomor='".$idNomor."' order by nopo desc";
                            // echo $sData;
                            $qData=mysql_query($sData) or die(mysql_error());
                            while($rData=mysql_fetch_assoc($qData))
                            {
                                $tab2.="<tr class=rowcontent><td>".$rData['nopo']."</td>";
                                $tab2.="<td align=center><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataDetail('".$rData['nomor']."','".$rData['nopo']."');\"></td>";
                            }
                            
                            $tab2.="</tbody></table>";
                           
                               echo $tab."###".$tab2;
                            
                break;
               
                case'deleteDetail':
                $sUpdate="select distinct penerima from ".$dbname.".log_pengiriman_ht where nosj='".$srtJalan."'";
                //exit("Error:".$sUpdate);
                $qUpdate=mysql_query($sUpdate) or die(mysql_error($conn));
                $rUpdate=mysql_fetch_assoc($qUpdate);
                if($rUpdate['penerima']!='0000000000')
                {
                    exit("Error: has been receipt before");
                }
                $sData="select distinct notransaksi,jumlahbrg,kodebarang ".$dbname.".log_pengiriman_dt where nosj='".$srtJalan."'";
                $qData=mysql_query($sData) or die(mysql_error($conn));
                $rData=mysql_fetch_assoc($qData);
                
                $sUpdate="update ".$dbname.".log_lpbdt set jumlahlalu=jumlahlalu-".$rData['jumlahbrg']."
                where notransaksi='".$rData['notransaksi']."' and kodebarang='".$rData['kodebarang']."'";
                if(mysql_query($sUpdate))
                {
                $sDel="delete from ".$dbname.".log_pengiriman_dt where 
                           nosj='".$srtJalan."' and kodebarang='".$kdbrgc."' and notransaksi='".$nosj."'";
                     if(!mysql_query($sDel))
                     {
                            echo"Gagal".mysql_error($conn);
                     }
                }
                else
                {
                    echo"Gagal".mysql_error($conn);
                }
                    
                    
                break;
                case'getKaryNm':
                    echo"<fieldset><legend>".$_SESSION['lang']['result']."</legend>
                        <div style=\"overflow:auto;height:295px;width:455px;\">
                        <table cellpading=1 border=0 class=sortbale>
                        <thead>
                        <tr class=rowheader>
                        <td>No.</td>
                        <td>".$_SESSION['lang']['kodesupplier']."</td>
                        <td>".$_SESSION['lang']['namasupplier']."</td>
                        </tr><tbody>
                        ";
                 $sSupplier="select  karyawanid,namakaryawan from ".$dbname.".datakaryawan 
                             where tipekaryawan in ('0','1','2') and sistemgaji='Bulanan' and 
                             tanggalkeluar='0000-00-00' and karyawanid!='".$_SESSION['standard']['userid']."' 
                             and namakaryawan like '%".$_POST['nmKaryawan']."%' order by namakaryawan asc";
                 $qSupplier=mysql_query($sSupplier) or die(mysql_error($conn));
                 while($rSupplier=mysql_fetch_assoc($qSupplier))
                 {
                     $no+=1;
                     echo"<tr class=rowcontent onclick=setDatakary('".$rSupplier['karyawanid']."')>
                         <td>".$no."</td>
                         <td>".$rSupplier['karyawanid']."</td>
                         <td>".$rSupplier['namakaryawan']."</td>
                    </tr>";
                 }
                    echo"</tbody></table></div>";
                break;
		default:
		break;
	}
?>