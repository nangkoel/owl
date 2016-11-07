<?php
        require_once('master_validation.php');
        require_once('config/connection.php');
        include('lib/nangkoelib.php');
        include_once('lib/zLib.php');


        $supplier_id=$_POST['supplier_id'];
        $proses=$_POST['proses'];
        $nopo=$_POST['nopo'];
        $tgl_po=tanggalsystem($_POST['tglpo']);
        $sub_total=$_POST['subtot'];
        $disc=$_POST['diskon'];
        $nilai_dis=$_POST['nildiskon'];
        $nppn=$_POST['ppn'];
        $tanggl_kirim=tanggalsystemd($_POST['tgl_krm']);
        $lokasi_krm=$_POST['lok_kirim'];
        $cr_pembayaran=$_POST['cara_pembayarn'];
        $nilai_po=$_POST['grand_total'];
        $purchaser=$_POST['purchser_id'];
        $lokasi_kirim=$_POST['lokasi_krm'];
        $persetujuan=$_POST['id_user'];
        $comment=$_POST['cm_hasil'];
        $jmlh_realisasi=$_POST['jmlh_realisasi'];
        $jmlh_diminta	=$_POST['jmlh_diminta'];
        $jnopp=$_POST['jnopp'];
        $jkdbrg=$_POST['jkdbrg'];
        $ketUraian=$_POST['ketUraian'];
        $nmSupplier=$_POST['nmSupplier'];
        switch($proses)
        {
                case 'cek_supplier':
                        $sql="select * from ".$dbname.".log_5supplier where supplierid='".$supplier_id."'";
                        $query=mysql_query($sql) or die(mysql_error());
                        $res=mysql_fetch_assoc($query);
                        echo $res['rekening'].",";
                        echo $res['npwp'];
                break;

                case 'insert':
                if(($supplier_id=='')||($nopo=='')||($disc=='')||($tanggl_kirim=='')||($cr_pembayaran=='')||($lokasi_kirim==''))
                {
                        echo"warning: Please complete the form";
                        exit();
                }
                foreach($_POST['kdbrg'] as $row =>$cntn)
                {
                        $kdbrg=$cntn;
                        $nopp=$_POST['nopp'][$row];
                        $jmlh_pesan=$_POST['rjmlh_psn'][$row];
                        $hrg_satuan=$_POST['rhrg_sat'][$row];
                         $hrg_sblmdiskon=str_replace(',','',$hrg_satuan);
                        $mat_uang=$_POST['rmat_uang'][$row];
                        $satuan=$_POST['rsatuan_unit'][$row];
                        $diskon=($hrg_sblmdiskon*$disc)/100;
                        $hrg_diskon=$hrg_sblmdiskon-$diskon;
                        $sqjmlh="select selisih,jlpesan,realisasi,purchaser from ".$dbname.".log_sudahpo_vsrealisasi_vw where nopp='".$nopp."' and kodebarang='".$kdbrg."'";
                        //echo "warning:".$sqjmlh;exit();
                        $qujmlh=mysql_query($sqjmlh) or die(mysql_error());
                        $resjmlh=mysql_fetch_assoc($qujmlh);
                        $jmlh_pesan=$resjmlh['jlpesan']+$jmlh_pesan;
                        if($purchaser!=$resjmlh['purchaser'])
                        {
                            $purchaser=$resjmlh['purchaser'];
                        }
                        if(($jmlh_pesan=='')||($hrg_satuan==''))
                        {
                                echo "warning: Please complete the form";
                                exit();
                        }
                        elseif($resjmlh['realisasi']<$jmlh_pesan)
                                {
                                        echo "warning : \nTotal requested (".$jmlh_pesan.") to material code ".$kdbrg.".(".$jmlh_pesan.") =
                                        \nVolum of previous request (".$resjmlh['jlpesan'].")\nVolum on current request (".$_POST['rjmlh_psn'][$row].")
                                        \nLarger than approved (".$resjmlh['realisasi'].").";
                                        exit();
                                }
                }
                        //$kode_org=substr($nopo,20,4);
                        $sKd="select kodeorg from ".$dbname.".log_prapoht where nopp='".$nopp."'";
                        $qKd=mysql_query($sKd) or die(mysql_error());
                        $rKdorg=mysql_fetch_assoc($qKd);    
                        $sql="select nopo from ".$dbname.".log_poht where nopo='".$nopo."'";
                        $query=mysql_query($sql) or die(mysql_error());
                        $res=mysql_fetch_row($query);
                        if($res<1)
                        {
                        $strx="insert into ".$dbname.".log_poht (`nopo`,`tanggal`,`kodesupplier`,`subtotal`,`diskonpersen`,`nilaidiskon`,`ppn`,`nilaipo`,`tanggalkirim`,`lokasipengiriman`,`syaratbayar`,`uraian`,`purchaser`,`kodeorg`,`lokalpusat`,`tgledit`,`statusbayar`) 
                            values ('".$nopo."','".$tgl_po."','".$supplier_id."','".$sub_total."','".$disc."','".$nilai_dis."','".$nppn."','".$nilai_po."','".$tanggl_kirim."','".$lokasi_kirim."','".$cr_pembayaran."','".$ketUraian."','".$_SESSION['standard']['userid']."','".$rKdorg['kodeorg']."','1','".$tgl_po."','".$_POST['crByr']."')";
             /*           $strx="insert into ".$dbname.".log_poht (`nopo`,`tanggal`,`kodesupplier`,`subtotal`,`diskonpersen`,`nilaidiskon`,`ppn`,`nilaipo`,`tanggalkirim`,`lokasipengiriman`,`syaratbayar`,`purchaser`,`kodeorg`,`lokalpusat`) values ('".$nopo."','".$tgl_po."','".$supplier_id."','".$sub_total."','".$disc."','".$nilai_dis."','".$nppn."','".$nilai_po."','".$tanggl_kirim."','".$lokasi_kirim."','".$cr_pembayaran."','".$purchaser."','".$kode_org."','1')";*/
                        }
                       //echo "warning:".$strx; exit();
                        if(!mysql_query($strx))
                        {
                        //echo $sqp;
                            echo "Gagal,".(mysql_error($conn));exit();
                        }

                        foreach($_POST['kdbrg'] as $row =>$isi)
                        {
                                //echo "warning:masuk";exit();
                                $kdbrg=$isi;
                                $nopp=$_POST['nopp'][$row];
                                $jmlh_pesan=$_POST['rjmlh_psn'][$row];
                                $hrg_satuan=$_POST['rhrg_sat'][$row];
                $hrg_sblmdiskon=str_replace(',','',$hrg_satuan);
                                $mat_uang=$_POST['rmat_uang'][$row];
                                $satuan=$_POST['rsatuan_unit'][$row];
                                $diskon=($hrg_sblmdiskon*$disc)/100;
                                $hrg_diskon=$hrg_sblmdiskon-$diskon;
                                $sqjmlh="select selisih,jlpesan,realisasi from ".$dbname.".log_sudahpo_vsrealisasi_vw where nopp='".$nopp."' and kodebarang='".$kdbrg."'";
                                //echo "warning:".$sqjmlh;exit();
                $qujmlh=mysql_query($sqjmlh) or die(mysql_error());
                                $resjmlh=mysql_fetch_assoc($qujmlh);
                                //$jmlh_pesan=$resjmlh['jlpesan']+$jmlh_pesan;
                                //echo "warning:".$jmlh_pesan."__________".$rjmlh['realisasi'];exit();
                                       $sql="insert into ".$dbname.".log_podt (`nopo`,`kodebarang`,`jumlahpesan`,`hargasatuan`,`nopp`,`matauang`,`hargasbldiskon`,`satuan`)
                                        values ('".$nopo."','".$kdbrg."','".$jmlh_pesan."','".$hrg_diskon."','".$nopp."','".$mat_uang."','".$hrg_sblmdiskon."','".$satuan."')";
                                        //echo "warning:".$sql;exit();
                                        if(!mysql_query($sql))
                                        {
                                        echo $sql."-----";
                                        echo "Gagal,".(mysql_error($conn));exit();
                                        }
                                        $snopp="select nopo from ".$dbname.".log_prapoht where nopp='".$nopp."'";
                                        $qnopp=mysql_query($snopp) or die(mysql_error());
                                        $rnopp=mysql_fetch_row($qnopp);
                                        if($rnopp<1)
                                        {
                                                $supp="update ".$dbname.".log_prapoht set `nopo`='".$nopo."' where nopp='".$nopp."'";
                                                if(mysql_query($supp))
                                                {
                                                        $sdpp="update ".$dbname.".log_prapodt set `create_po`='1' where `nopp`='".$nopp."' and `kodebarang`='".$kdbrg."'";
                                                        if(mysql_query($sdpp))
                                                        echo"";
                                                        else
                                                        echo "Gagal,".(mysql_error($conn));exit();	
                                                //echo"warning".$sdpp;exit();
                                                }
                                                else
                                                {
                                                        echo "Gagal,".(mysql_error($conn));exit();
                                                }						
                                        }
                                    $sdpp="update ".$dbname.".log_prapodt set create_po='1' where nopp='".$nopp."' and kodebarang='".$kdbrg."'";
                                    $qdpp=mysql_query($sdpp) or die(mysql_error());

                        }


                break;
                case 'update_data' :

                echo" <table cellspacing='1' border='0' class='sortable'>
        <thead>
            <tr class=rowheader>
                <td>".$_SESSION['lang']['nopo']."</td>
                <td>".$_SESSION['lang']['namasupplier']."</td>
                                <td>".$_SESSION['lang']['tgl_po']."</td>
                <td>".$_SESSION['lang']['tgl_kirim']."</td>
                <td>".$_SESSION['lang']['almt_kirim']."</td>
                                <td>".$_SESSION['lang']['purchaser']."</td> 
                <td>".$_SESSION['lang']['syaratPem']."</td>
                                 <td>".$_SESSION['lang']['status']."</td>
                <td>action</td>
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

                if($_SESSION['empl']['kodejabatan']=='5')
                {
                        $sql2="select count(*) as jmlhrow from ".$dbname.".log_poht where lokalpusat='1' order by tanggal desc";
                         $sql="select * from ".$dbname.".log_poht where lokalpusat='1' order by tanggal desc limit ".$offset.",".$limit.""; 
                }
                else
                {
                    $sql2="select count(*) as jmlhrow from ".$dbname.".log_poht where purchaser='".$_SESSION['standard']['userid']."' and lokalpusat='1' order by tanggal desc";
                     $sql="select * from ".$dbname.".log_poht where purchaser='".$_SESSION['standard']['userid']."' and lokalpusat='1' order by tanggal desc limit ".$offset.",".$limit.""; 
                }
                        $query2=mysql_query($sql2) or die(mysql_error());
                        while($jsl=mysql_fetch_object($query2)){
                        $jlhbrs= $jsl->jmlhrow;
                        }


                $query=mysql_query($sql) or die(mysql_error());
                while ($res = mysql_fetch_object($query)) {
                                        $no+=1;
                    $sql2="select * from ".$dbname.".log_5supplier where supplierid='".$res->kodesupplier."'";
                    $query2=mysql_query($sql2) or die(mysql_error());
                    $res2=mysql_fetch_object($query2);

                                        $skry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$res->purchaser."'";// echo $skry;
                                        $qkry=mysql_query($skry) or die(mysql_error());
                                        $rkry=mysql_fetch_assoc($qkry);

                                        if($res->stat_release!=1)
                                        {
                                                $stat=0;
                                        }
                                        else
                                        {
                                                $stat=1;
                                        }

                                         if(($res->stat_release==0)||is_null($res->stat_release))
                                         {
                                                $stat_po=$_SESSION['lang']['un_release_po'];
                                                $edit_data="<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res->nopo."','".tanggalnormal($res->tanggal)."','".$res->kodesupplier."','".$res->subtotal."','".$res->diskonpersen."','".$res->ppn."','".$res->nilaipo."','".$res2->rekening."','".$res2->npwp."','".$res->nilaidiskon."','".$stat."','".tanggalnormal($res->tanggalkirim)."');\" >";
                                                $delete_data="<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPo('".$res->nopo."','".$stat."');\" >";
                                                $release_po_button="<img src=images/application/application_key.png class=resicon onclick=release_po('".$no."') title='Choose Signature' />";
                                         }           
                                         elseif($res->stat_release==1)
                                         {
                                                $stat_po=$_SESSION['lang']['release_po'];
                                                $edit_data="";
                                                $delete_data="";
                                                $release_po_button="";
                                         }     

                      echo"
                        <tr class=rowcontent id=tr_".$no.">
                            <td id=td_nopo_".$no.">".$res->nopo."</td>
                            <td id=td_ns_".$no.">".$res2->namasupplier."</td>
                                                        <td id=td_tgl_".$no.">".tanggalnormal($res->tanggal)."</td>
                            <td id=td_tgl_krm_".$no.">".tanggalnormal($res->tanggalkirim)."</td>
                                                        <td>".$res->lokasipengiriman."</td>
                                                        <td>".$rkry['namakaryawan']."</td>
                            <td>".$res->syaratbayar."</td>
                                                         <td>".$stat_po."</td>
                                                ";	
                                                        if($res->purchaser==$_SESSION['standard']['userid']||$_SESSION['empl']['kodejabatan']=='5')
                                                        {

                                                        echo"<td>".$edit_data."";
                                                        echo"".$delete_data."".$release_po_button."<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_poht','".$res->nopo."','','log_slave_print_log_po_lokal',event);\">
                                                        </td>";
                                                        }
                                                        else
                                                        {
                                                        echo"
                                                        <td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_poht','".$res->nopo."','','log_slave_print_log_po_lokal',event);\">
                                                        </td>";
                                                        }
                        echo"</tr>";
                }
                                        echo"
                                 <tr><td colspan=8 align=center>
                                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                                </td>
                                </tr></tbody></table>"; 
                break;
                case 'edit_po':
                if(($supplier_id=='')||($nopo=='')||($disc==''))
                {
                        echo"warning: Please complete the form";
                        exit();
                }
                $scek="select statuspo from ".$dbname.".log_poht where nopo='".$nopo."'";
                $qcek=mysql_query($scek) or die(mysql_error($conn));
                $rcek=mysql_fetch_assoc($qcek);
                if($rcek['statuspo']==1)
                {
                        echo"warning :  PO : ".$nopo." being under verification process";
                        exit();
                }
                else    $tglSkrng=date("Y-m-d");
                {
                        $kode_org=substr($nopo,20,4);
                        $strx="update ".$dbname.".log_poht set `kodesupplier`='".$supplier_id."',`subtotal`='".$sub_total."',
                        `diskonpersen`='".$disc."',`nilaidiskon`='".$nilai_dis."',`ppn`='".$nppn."',`nilaipo`='".$nilai_po."',
                        `tanggalkirim`='".$tanggl_kirim."',`lokasipengiriman`='".$lokasi_kirim."',`syaratbayar`='".$cr_pembayaran."',
                        `uraian`='".$ketUraian."',`tgledit`='".$tglSkrng."',`statusbayar`='".$_POST['crByr']."'  where nopo='".$nopo."'";
                        //	echo "warning:".$strx; exit();
                        if(!mysql_query($strx))
                        {
                                //echo $sqp; 
                                echo "Gagal,".(mysql_error($conn));exit();
                        }
                        foreach($_POST['kdbrg'] as $row =>$isi)
                        {

                                $kdbrg=$isi;
                                $nopp=$_POST['nopp'][$row];
                                $jmlh_pesan=$_POST['rjmlh_psn'][$row];
                                $hrg_satuan=$_POST['rhrg_sat'][$row];
                                $hrg_sblmdiskon=str_replace(',','',$hrg_satuan);
                                $diskon=($hrg_sblmdiskon*$disc)/100;
                                $hrg_diskon=$hrg_sblmdiskon-$diskon;
                                $mat_uang=$_POST['rmat_uang'][$row];
                                $satuan=$_POST['rsatuan_unit'][$row];
                                $sqjmlh="select selisih,jlpesan,realisasi from ".$dbname.".log_sudahpo_vsrealisasi_vw where nopp='".$nopp."' and kodebarang='".$kdbrg."'";
                                //echo "warning :".$sqjmlh; exit();
                                $qujmlh=mysql_query($sqjmlh) or die(mysql_error());
                                $resjmlh=mysql_fetch_assoc($qujmlh);
                                ///$jmlh_pesan=$resjmlh['jlpesan']+$jmlh_pesan;

                                if($resjmlh['realisasi']>=$jmlh_pesan)
                                {
                                        //echo "warning:masuk";exit();
                                        $sql="update ".$dbname.".log_podt set `jumlahpesan`='".$jmlh_pesan."',`hargasatuan`='".$hrg_diskon."',`matauang`='".$mat_uang."',`hargasbldiskon`='".$hrg_sblmdiskon."',`satuan`='".$satuan."'
                                        where nopo='".$nopo."' and kodebarang='".$kdbrg."' and nopp='".$nopp."'";
                                        //echo "warning:".$sql; exit();
                                        if(!mysql_query($sql))
                                        {
                                                //echo $sqp; 
                                                echo "Gagal,".(mysql_error($conn));exit();
                                        }
                                        else
                                        {
                                            $sCek="select distinct create_po from ".$dbname.".log_prapodt where nopp='".$_POST['nopp'][$row]."' and kodebarang='".$isi."'";
                                            $qCek=mysql_query($sCek) or die(mysql_error());
                                            $rCek=mysql_fetch_assoc($qCek);
                                            if($rCek['create_po']==''||$rCek['create_po']=='0')
                                            {
                                                $sUpdate="update ".$dbname.".log_prapodt set create_po=1 where nopp='".$_POST['nopp'][$row]."' and kodebarang='".$isi."'";
                                                if(!mysql_query($sUpdate))
                                                {
                                                echo "Gagal,".(mysql_error($conn));exit();
                                                }
                                            }
                                        }
                                }
                                else
                                {
                                        echo "warning : Order volume (".$jmlh_pesan.") must lower or equal as approved (".$resjmlh['realisasi'].")";
                                        exit();
                                }	
                        }
                }
                break;
                case 'delete_all':
                $scek="select statuspo from ".$dbname.".log_poht where nopo='".$nopo."'";
                $qcek=mysql_query($scek) or die(mysql_error($conn));
                $rcek=mysql_fetch_assoc($qcek);
                if($rcek['statuspo']>2)
                {
                        echo"warning : PO : ".$nopo." being on verification process";
                        exit();
                }
                $sCekGdng="select distinct nopo from ".$dbname.".log_transaksi_vw where nopo='".$nopo."'";
                //exit("Error:".$sCekGdng);
                $qCekGdng=mysql_query($sCekGdng) or die(mysql_error($conn));
                $rCekGdng=mysql_num_rows($qCekGdng);
                if($rCekGdng>0)
                {
                    exit("Error: PO :  ".$nopo." has arrived at warehouse, can not delete");
                }

                $sql="delete from ".$dbname.".log_podt where nopo='".$nopo."'"; //echo "warning:".$sql;exit();
                if(!mysql_query($sql))
                {
                        echo "Gagal,".(mysql_error($conn));exit();
                }
                $sql2="delete from ".$dbname.".log_poht where nopo='".$nopo."'";
                if(!mysql_query($sql2))
                {
                        echo "Gagal,".(mysql_error($conn));exit();
                }

                break;

                case 'insert_release_po' :
//echo "warning:masuk '===='";exit();
                $sql="select * from ".$dbname.".log_poht where nopo='".$nopo."' and lokalpusat='1'";
                $query=mysql_query($sql) or die(mysql_error());
                $rest=mysql_fetch_assoc($query);

                                        echo"<br />
                                        <div id=test style=display:block>
                                        <fieldset>
                                        <legend><input type=text readonly=readonly name=rnopo id=rnopo value=".$nopo."  /></legend>
                                        <table cellspacing=1 border=0>
                                        <tr>
                                        <td colspan=3>
                                        ".$_SESSION['lang']['penandatangan']." :</td>
                                        </tr>
                                        <td>".$_SESSION['lang']['namakaryawan']."</td>
                                        <td>:</td>
                                        <td valign=top>";
                                        $optPur='';
                                        $se=substr($nopo,15,4);
                                        $klq="select karyawanid,namakaryawan from ".$dbname.".`datakaryawan` where tipekaryawan='0' and karyawanid!='".$user_id."' order by namakaryawan asc"; 
                                        //echo $klq;
                                        $qry=mysql_query($klq) or die(mysql_error());
                                        while($rst=mysql_fetch_object($qry))
                                        {
                                        $optPur.="<option value='".$rst->karyawanid."'>".$rst->namakaryawan."</option>";
                                        }
                                        echo"
                                                <select id=persetujuan_id name=persetujuan_id>
                                                        $optPur;
                                                </select></td></tr>
                                                <tr>
                                                <td colspan=3 align=center>
                                                <button class=mybutton onclick=proses_release_po() title=\"Choose signature\" >".$_SESSION['lang']['tandatangan']."</button>
                                                <button class=mybutton onclick=cancel_po() title=\"close\">".$_SESSION['lang']['cancel']."</button>
                                                </td></tr></table><br />
                                                <input type=hidden name=proses id=proses  />
                                                </fieldset></div>";
               
                break;
         case 'get_form_approval' :
        $sql="select * from ".$dbname.".log_poht where nopo='".$nopo."' and lokalpusat='0'";
        $query=mysql_query($sql) or die(mysql_error());
        $rest=mysql_fetch_assoc($query);
                                        echo"<br />
                                        <div id=test style=display:block>
                                        <fieldset>
                                        <legend><input type=text readonly=readonly name=snopo id=snopo value=".$nopo."  /></legend>
                                        <table cellspacing=1 border=0>
                                        <tr>
                                        <td colspan=3>
                                        Submit to the next verification process :</td>
                                        </tr>
                                        <td>".$_SESSION['lang']['namakaryawan']."</td>
                                        <td>:</td>
                                        <td valign=top>";

                                        $optPur='';
                                        $klq="select * from ".$dbname.".`datakaryawan` where tipekaryawan='0' and karyawanid!='".$user_id."'"; 
                                        //echo $klq;
                                        $qry=mysql_query($klq) or die(mysql_error());
                                        while($rst=mysql_fetch_object($qry))
                                        {
                                        $optPur.="<option value='".$rst->karyawanid."'>".$rst->namakaryawan."</option>";
                                        }

                                        echo"
                                                <select id=persetujuan_id name=persetujuan_id>
                                                        $optPur;
                                                </select></td></tr>
                                                <tr>
                                                <td colspan=3 align=center>
                                                <button class=mybutton onclick=forward_po() title=\"Submission to the next verificator\" >".$_SESSION['lang']['diajukan']."</button>
                                                <button class=mybutton onclick=cancel_po() title=\"Close>".$_SESSION['lang']['cancel']."</button>
                                                </td></tr></table><br />
                                                <input type=hidden name=proses id=proses  />
                                                </fieldset></div>

                                                <div id=close_po style=\"display:none;\">	
                                                <fieldset><legend><input type=text id=snopo name=snopo disabled value='".$nopo."' /></legend>
                                                <p align=center>Processing this PO, Are you sure</p><br />
                                                <button class=mybutton onclick=proses_release_po() title=\"Process\" >".$_SESSION['lang']['approve']."</button>
                                                <button class=mybutton onclick=cancel_po() title=\"Close\">".$_SESSION['lang']['cancel']."</button>
                                                </fieldset></div>
                                                ";

                break;
                case 'proses_release_po':
                $tgl_klo=date("Y-m-d");
                $sql="update ".$dbname.".log_poht set statuspo='2',hasilpersetujuan1='1',tanggal='".$tgl_klo."',persetujuan1='".$persetujuan."',tglp1='".$tgl_klo."' where nopo='".$nopo."'";		
                if(mysql_query($sql))
                echo "";
                else
                echo "Gagal,".(mysql_error($conn));
                break;
                case 'cari_nopo':
                echo"<div style=\"overflow:auto; height:450px;\"> <table cellspacing='1' border='0' class='sortable'>
        <thead>
            <tr class=rowheader>
                <td>".$_SESSION['lang']['nopo']."</td>
                <td>".$_SESSION['lang']['namasupplier']."</td>
                                <td>".$_SESSION['lang']['tgl_po']."</td>
                <td>".$_SESSION['lang']['tgl_kirim']."</td>
                <td>".$_SESSION['lang']['almt_kirim']."</td>
                                <td>".$_SESSION['lang']['purchaser']."</td> 
                <td>".$_SESSION['lang']['syaratPem']."</td>
                                 <td>".$_SESSION['lang']['status']."</td>
                <td>action</td>
            </tr>
         </thead>
         <tbody>";
                //$sql2="select count(*) as jmlhrow from ".$dbname.".log_poht order by nopo desc ";

                if(isset($_POST['txtSearch']))
                                {
                                        $txt_search=$_POST['txtSearch'];
                                        $txt_tgl=tanggalsystem($_POST['tglCari']);
                                        $txt_tgl_t=substr($txt_tgl,0,4);
                                        $txt_tgl_b=substr($txt_tgl,4,2);
                                        $txt_tgl_tg=substr($txt_tgl,6,2);
                                        $txt_tgl=$txt_tgl_t."-".$txt_tgl_b."-".$txt_tgl_tg;
                                        //echo "warning:".$txt_tgl;
                                }
                                else
                                {
                                        $txt_search='';
                                        $txt_tgl='';			
                                }
                        if($txt_search!='')
                        {
                                $where=" and nopo LIKE  '%".$txt_search."%'";
                        }
                        elseif($txt_tgl!='')
                        {
                                $where=" and tanggal LIKE '%".$txt_tgl."%'";
                        }
                        elseif(($txt_tgl!='')&&($txt_search!=''))
                        {
                                $where=" and nopo LIKE '%".$txt_search."%' or tanggal LIKE '%".$txt_tgl."%' ";
                        }
//			elseif(($txt_search=='')&&($txt_tgl==''))
//			{
//				$where=" ;
//			}
                        if($_SESSION['empl']['kodejabatan']!='5')
                        {
                            $where.=" and purchaser='".$_SESSION['standard']['userid']."'";
                        }


                        $strx="select * from ".$dbname.".log_poht where lokalpusat='1' ".$where." order by nopo desc";
                        //echo $strx;
                        //echo"warning:".$strx;exit();
                        if(mysql_query($strx))
                        {
                        $query=mysql_query($strx);
                        $numrows=mysql_num_rows($query);
                        if($numrows<1)
                        {
                                echo"<tr class=rowcontent><td colspan=10>Not Found</td></tr>";
                        }
                        else
                        {
                                //echo $sql2;

                          while ($res = mysql_fetch_object($query)) {
                    $sql2="select * from ".$dbname.".log_5supplier where supplierid='".$res->kodesupplier."'";
                    $query2=mysql_query($sql2) or die(mysql_error());
                    $res2=mysql_fetch_object($query2);

                                        $skry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$res->purchaser."'";// echo $skry;
                                        $qkry=mysql_query($skry) or die(mysql_error());
                                        $rkry=mysql_fetch_assoc($qkry);

                                        if($res->stat_release!=1)
                                        {
                                                $stat=0;
                                        }
                                        else
                                        {
                                                $stat=1;
                                        }
                                        if($res->stat_release==0)
                                         {
                                                $stat_po=$_SESSION['lang']['un_release_po'];
                                                $edit_data="<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res->nopo."','".tanggalnormal($res->tanggal)."','".$res->kodesupplier."','".$res->subtotal."','".$res->diskonpersen."','".$res->ppn."','".$res->nilaipo."','".$res2->rekening."','".$res2->npwp."','".$res->nilaidiskon."','".$stat."','".tanggalnormal($res->tanggalkirim)."');\" >";//,'".$res->syaratbayar."',,'".$res->purchaser."','".$res->lokasipengiriman."',,,,
                                                $delete_data="<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPo('".$res->nopo."','".$stat."');\" >";
                                                $release_po_button="<img src=images/application/application_key.png class=resicon onclick=release_po('".$no."') title='Release This PO' />";
                                         }           
                                         elseif($res->stat_release==1)
                                         {
                                                $stat_po=$_SESSION['lang']['release_po'];
                                                $edit_data="";
                                                $delete_data="";
                                                $release_po_button="";
                                         }     

                      echo"
                        <tr class=rowcontent id=tr_".$no.">
                            <td id=td_nopo_".$no.">".$res->nopo."</td>
                            <td id=td_ns_".$no.">".$res2->namasupplier."</td>
                                                        <td id=td_tgl_".$no.">".tanggalnormal($res->tanggal)."</td>
                            <td id=td_tgl_krm_".$no.">".tanggalnormal($res->tanggalkirim)."</td>
                                                        <td>".$res->lokasipengiriman."</td>
                                                        <td>".$rkry['namakaryawan']."</td>
                            <td>".$res->syaratbayar."</td>
                                                         <td>".$stat_po."</td>
                                                ";	
                                                        if(($res->purchaser==$_SESSION['standard']['userid'])||($_SESSION['empl']['kodejabatan']=='5'))
                                                        {

                                                        echo"<td>".$edit_data."";
                                                        echo"".$delete_data."".$release_po_button."<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_poht','".$res->nopo."','','log_slave_print_log_po_lokal',event);\">
                                                        </td>";
                                                        }
                                                        else
                                                        {
                                                        echo"
                                                        <td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_poht','".$res->nopo."','','log_slave_print_log_po',event);\">
                                                        </td>";
                                                        }
                        echo"</tr>";

                }
                                echo"</tbody></table></div>
                                 <input type=hidden id=nopp_".$no." name=nopp_".$no." value='".$bar['nopp']."' />"; 
                        }
                }
                break;
                case'getNotifikasi':
                $Sorg="select kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
                $qOrg=mysql_query($Sorg) or die(mysql_error());
                while($rOrg=mysql_fetch_assoc($qOrg))
                {
                if($_SESSION['empl']['kodejabatan']=='5')
                {
                $sList="select count(*) as jmlhJob from  ".$dbname.".log_sudahpo_vsrealisasi_vw  where (kodept='".$rOrg['kodeorganisasi']."' and lokalpusat='1' and status!='3') and (selisih>0 or selisih is null)";
                }
                else
                {
                   $sList="select count(*) as jmlhJob from  ".$dbname.".log_sudahpo_vsrealisasi_vw  where (kodept='".$rOrg['kodeorganisasi']."' and purchaser='".$_SESSION['standard']['userid']."' and lokalpusat='1' and status!='3') and (selisih>0 or selisih is null)"; 
                }
                //echo $sList;
                $qList=mysql_query($sList) or die(mysql_error());
                $rList=mysql_fetch_assoc($qList);
                if($rList['jmlhJob']=='')
                {
                $rList['jmlhJob']=0;
                }
                echo"[".$rOrg['kodeorganisasi']." : <a href='#' onclick=\"cek_pp_pt('".$rOrg['kodeorganisasi']."')\">".$rList['jmlhJob']."</a> ]";
                }
                break;
                case 'cek_pembuat_po':
                //echo "warning:Please See Your Username";
                        $user_id=$_SESSION['standard']['userid'];
                        $skry="select purchaser from ".$dbname.".log_poht where nopo='".$nopo."'";
                        $qkry=mysql_query($skry) or die(mysql_error());
                        $rkry=mysql_fetch_assoc($qkry);
                        if($rkry['purchaser']!=$user_id)
                        {
                                echo "warning:Please See Your Username";
                                exit();
                        }
                break;
                case'getSupplierNm':
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
                 $sSupplier="select namasupplier,supplierid from ".$dbname.".log_5supplier where kodekelompok='S001' and namasupplier like '%".$nmSupplier."%'";
                 $qSupplier=mysql_query($sSupplier) or die(mysql_error($conn));
                 while($rSupplier=mysql_fetch_assoc($qSupplier))
                 {
                     $no+=1;
                     echo"<tr class=rowcontent onclick=setData('".$rSupplier['supplierid']."')>
                         <td>".$no."</td>
                         <td>".$rSupplier['supplierid']."</td>
                         <td>".$rSupplier['namasupplier']."</td>
                    </tr>";
                 }
                    echo"</tbody></table></div>";
                break;
                default:
                break;
        }
         ?>