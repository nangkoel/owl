<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/fpdf.php');
include_once('lib/zLib.php');
$nmBarang=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
?>

<?php
$method=$_POST['method'];
$kolom=$_POST['kolom'];

switch($method){
        case'get_form_approval':
        $sql="select * from ".$dbname.".log_prapoht where nopp='".$_POST['nopp']."'";
        $query=mysql_query($sql) or die(mysql_error());
        $rest=mysql_fetch_assoc($query);

        for($i=1;$i<6;$i++)
        {
     // echo "warning".$rest['persetujuan'.$a];
                if($_SESSION['standard']['userid']==$rest['persetujuan'.$i])
                {
                        if($rest['persetujuan5']!='')
                        {
                                echo"<br /><div id=approve>
                                <fieldset>
                                <legend><input type=text readonly=readonly name=rnopp id=rnopp value=".$_POST['nopp']."  /></legend>
                                <table cellspacing=1 border=0>
                                <tr>
                                <td colspan=3>
                                Submit to Purchasing Dept.</td></tr>
                                <tr>
                                <td>".$_SESSION['lang']['note']."</td>
                                <td>:</td>
                                <td><input type=text id=note name=note class=myinputtext onClick=\"return tanpa_kutip(event)\" /></td>
                                </tr>
                                <tr><td colspan=3 align=center>
                                <button class=mybutton onclick=close_pp() >".$_SESSION['lang']['ok']."</button></td></tr></table>
                                </fieldset>
                                </div>";
                        }
                        else
                                {	
                                        echo"<br />
                                        <div id=test style=display:block>
                                        <fieldset>
                                        <legend><input type=text readonly=readonly name=rnopp id=rnopp value=".$_POST['nopp']."  /></legend>
                                        <table cellspacing=1 border=0>
                                        <tr>
                                        <td colspan=3>
                                         Submit to the next approval :</td>
                                        </tr>
                                        <td>".$_SESSION['lang']['namakaryawan']."</td>
                                        <td>:</td>
                                        <td valign=top>";
                                        $kd=substr($_POST['nopp'],17,2);
                                        $unit=substr($_POST['nopp'],15,4);
                                        $optPur='';

                                        if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
                                            $str="select distinct a.karyawanid,b.namakaryawan,b.lokasitugas from ".$dbname.".setup_approval a 
                                                  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where 
                                                  a.karyawanid!='".$_SESSION['standard']['userid']."' and a.applikasi='PP2' and a.kodeunit like '%HO'  order by b.namakaryawan asc";
                                        } else {
                                            if ($_SESSION['empl']['regional']=='KALIMANTAN'){
                                                if ($_SESSION['empl']['kodegolongan']>='7'){
                                                    if ($_SESSION['empl']['kodegolongan']<'8'){
                                                    $str="select distinct a.karyawanid,b.namakaryawan,b.lokasitugas from ".$dbname.".setup_approval a 
                                                          left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where 
                                                          a.karyawanid!='".$_SESSION['standard']['userid']."' and a.applikasi='PP2' and (b.lokasitugas like '%HO' or b.lokasitugas like 'L%' or b.lokasitugas like 'P%') and b.kodegolongan>='7' order by b.namakaryawan asc";
                                                    } else {
                                                    $str="select distinct a.karyawanid,b.namakaryawan,b.lokasitugas from ".$dbname.".setup_approval a 
                                                          left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where 
                                                          a.karyawanid!='".$_SESSION['standard']['userid']."' and a.applikasi='PP2' and b.lokasitugas like '%HO' and b.kodegolongan>='7' order by b.namakaryawan asc";
                                                    }
                                                } else {
                                                    if ($_SESSION['empl']['bagian']=='IT'){
                                                    $str="select distinct a.karyawanid,b.namakaryawan,b.lokasitugas from ".$dbname.".setup_approval a 
                                                          left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where 
                                                          a.karyawanid!='".$_SESSION['standard']['userid']."' and a.applikasi='PP2' and b.lokasitugas like '%HO' and b.kodegolongan>='5' order by b.namakaryawan asc";
                                                    } else {
                                                        $str="select distinct a.karyawanid,b.namakaryawan,b.lokasitugas from ".$dbname.".setup_approval a 
                                                              left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where 
                                                              a.karyawanid!='".$_SESSION['standard']['userid']."' and a.applikasi='PP2' and a.kodeunit like '%HO' 
                                                              and b.lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by b.namakaryawan asc";
                                                    }
                                                }
                                            } else {
                                                if ($_SESSION['empl']['kodegolongan']>='8'){
                                                    $str="select distinct a.karyawanid,b.namakaryawan,b.lokasitugas from ".$dbname.".setup_approval a 
                                                          left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where 
                                                          a.karyawanid!='".$_SESSION['standard']['userid']."' and a.applikasi='PP2' and a.kodeunit like '%HO' and b.kodegolongan>='7'
                                                          and b.lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by b.namakaryawan asc";
                                                } else {
                                                    $str="select distinct a.karyawanid,b.namakaryawan,b.lokasitugas from ".$dbname.".setup_approval a 
                                                          left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where 
                                                          a.karyawanid!='".$_SESSION['standard']['userid']."' and a.applikasi='PP2' and a.kodeunit like '%HO' 
                                                          and b.lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by b.namakaryawan asc";
                                                }
                                            }
                                        }

                                        $qry=mysql_query($str) or die(mysql_error($conn));
                                        while($rkry=mysql_fetch_assoc($qry))
                                        {
                                            $optPur.="<option value='".$rkry['karyawanid']."'>".$rkry['namakaryawan']." [".$rkry['lokasitugas']."]</option>";
                                        }
                                        echo"

                                                <select id=user_id name=user_id  style=\"width:150px;\">
                                                        ".$optPur." 
                                                </select></td></tr>
                                                <tr>
                                                <tr>
                                                <td>".$_SESSION['lang']['note']."</td>
                                                <td>:</td>
                                                <td><input type=text id=comment_fr name=comment_fr class=myinputtext onClick='return tanpa_kutip(event)'  style=\"width:150px;\" /></td>
                                                </tr>
                                                <td colspan=3 align=center>
                                                <button class=mybutton onclick=forward_pp() title=\" Submit to the next level\" id=Ajukan >".$_SESSION['lang']['diajukan']."</button>

                                                <button class=mybutton onclick=cancel_pp() title=\" Close this form \">".$_SESSION['lang']['cancel']."</button>
                                                </td></tr></table><br /> 
                                                <input type=hidden name=method id=method  /> 
                                                <input type=hidden name=user_id id=user_id value=".$_SESSION['standard']['userid']." />
                                                <input type=hidden name=nopp id=nopp value=".$_POST['nopp']."  /> 
                                                </fieldset></div><br />
<br />";
                                                    if($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
                                                        if($rest['hasilpersetujuan1']!='0' or $_SESSION['empl']['kodegolongan']>='8'){
                                                            echo"<div id=approve style=display:block>
                                                            <fieldset>
                                                            <legend><input type=text readonly=readonly name=rnopp id=rnopp value=".$_POST['nopp']."  /></legend>
                                                            <table cellspacing=1 border=0>
                                                            <tr>
                                                            <td colspan=3>
                                                             Approve and submit directly to Purchasing Dept.</td></tr>
                                                            <tr>
                                                            <td>".$_SESSION['lang']['note']."</td>
                                                            <td>:</td>
                                                            <td><input type=text id=note name=note class=myinputtext onClick=\"return tanpa_kutip(event)\" style=\"width:150px;\" /></td>
                                                            </tr>
                                                            <tr><td colspan=3 align=center>
                                                            <button class=mybutton onclick=close_pp() title=\"You are agree to this PR and submit it to Purchasing Dept. \"  >".$_SESSION['lang']['kePurchaser']."</button><button class=mybutton onclick=cancel_pp() title=\"Close this form\">".$_SESSION['lang']['cancel']."</button></td></tr></table>
                                                            </fieldset>
                                                            </div>
                                                            ";
                                                        }
                                                    }else{
                                                                echo"<div id=approve style=display:block>
                                                            <fieldset>
                                                            <legend><input type=text readonly=readonly name=rnopp id=rnopp value=".$_POST['nopp']."  /></legend>
                                                            <table cellspacing=1 border=0>
                                                            <tr>
                                                            <td colspan=3>
                                                             Approve and submit directly to Purchasing Dept.</td></tr>
                                                            <tr>
                                                            <td>".$_SESSION['lang']['note']."</td>
                                                            <td>:</td>
                                                            <td><input type=text id=note name=note class=myinputtext onClick=\"return tanpa_kutip(event)\" style=\"width:150px;\" /></td>
                                                            </tr>
                                                            <tr><td colspan=3 align=center>
                                                            <button class=mybutton onclick=close_pp() title=\"You are agree to this PR and submit it to Purchasing Dept. \"  >".$_SESSION['lang']['kePurchaser']."</button><button class=mybutton onclick=cancel_pp() title=\"Close this form\">".$_SESSION['lang']['cancel']."</button></td></tr></table>
                                                            </fieldset>
                                                            </div>
                                                            ";
                                                    }
                                }
                }
        }

break;

        case 'get_form_rejected':
        echo"<div id=rejected_form>
        <fieldset>
        <legend><input type=text readonly=readonly name=rnopp id=rnopp value=".$_POST['nopp']."  /></legend>
        <table cellspacing=1 border=0>
        <tr>
        <td colspan=3>
         PR Rejection form </td></tr>
        <tr>
        <td>".$_SESSION['lang']['note']."</td>
        <td>:</td>
        <td><input type=text id=cmnt_tolak name=cmnt_tolak class=myinputtext onClick=\"return tanpa_kutip(event)\" /></td>
        </tr>
        <tr><td colspan=3 align=center>
        <button class=mybutton onclick=\"rejected_pp_proses(".$_POST['kolom'].")\" >".$_SESSION['lang']['ditolak']."</button>
        </td></tr></table>
        </fieldset>
        </div>";
        break;
        case 'get_form_rejected_some':
        $nopp=$_POST['nopp'];
        $sql="select * from ".$dbname.".log_prapodt where `nopp`='".$nopp."' and status<>3";
        $query=mysql_query($sql) or die(mysql_error());

        echo"
        <fieldset>
        <legend><input type=text id=rnopp name=rnopp value=".$nopp." readonly=readonly /></legend>
        <div style=overflow:auto;width=850px;height:350px;>
        <table cellspacing=1 border=0 class=sortable>
        <thead class=rowheader>
        <tr>
        <td>No.</td>
        <td>".$_SESSION['lang']['kodebarang']."</td>
        <td>".$_SESSION['lang']['namabarang']."</td>
        <td>".$_SESSION['lang']['satuan']."</td>
        <td>".$_SESSION['lang']['kodeanggaran']."</td>
        <td>".$_SESSION['lang']['jmlhDiminta']."</td>
        <td>".$_SESSION['lang']['tanggalSdt']."</td>
        <td>".$_SESSION['lang']['keterangan']."</td>
        <td>".$_SESSION['lang']['alasanDtolak']."</td>
        <td colspan=2>Action</td>
        </tr>
        </thead>

        <tbody id=reject_some class=rowcontent>

        ";
        while($res=mysql_fetch_assoc($query)){
        $no+=1;
        $sql2="select * from ".$dbname.".log_5masterbarang where `kodebarang`='".$res['kodebarang']."'";
        $query2=mysql_query($sql2) or die(mysql_error());
        $res2=mysql_fetch_assoc($query2);
        if($res['status']==3)
        {
                $dis="disabled=disabled";
                $stadData="checked";
        }
        else
        {
                $dis="";
                $stadData="";
        }
        echo"<tr>
        <td>".$no."</td>
        <td id=kd_brg_".$no.">".$res['kodebarang']."</td>
        <td>".$res2['namabarang']."</td>
        <td>".$res2['satuan']."</td>
        <td id=kd_angrn_".$no.">".$res['kd_anggran']."</td>
        <td id=jmlh_".$no.">".$res['jumlah']."</td>
        <td id=tgl_".$no.">".$res['tgl_sdt']."</td>
        <td id=ket_".$no.">".$res['keterangan']."</td>
        <td><input type=text id=alsnDtolak_".$no." name=alsnDtolak_".$no." class=myinputtext style=width:100px  ".$dis." value='".$res['alasanstatus']."' /></td>
        <td align=center><input type=checkbox onclick='checkAlasan(".$no.")' id='tolak_chk_".$no."' ".$stadData." ".$dis."  /></td>
        </tr>";
        }
        //<button class=mybutton onclick=\"rejected_some('".$nopp."','".$no."','".$kolom."')\" ".$dis." >".$_SESSION['lang']['ditolak']."</button>
        echo"</tbody><tfoot><tr><td colspan=10 align=center><button class=mybutton onclick=\"rejected_some_done('".$nopp."','".$kolom."','".$no."')\" >".$_SESSION['lang']['done']."</button></td></tr></tfoot></table></div></fieldset><input type=hidden id=user_id name=user_id value='".$_SESSION['standard']['userid']."'>";
        break;

        case 'rejected_some_done':
        $user_id=$_POST['user_id'];
        for($i=1;$i<6;$i++)
        {
                $sql="select * from ".$dbname.".log_prapoht where nopp='".$_POST['nopp']."' and persetujuan".$i."='".$user_id."' ";
                if($query2=mysql_query($sql2))
                {
                        while($res=mysql_fetch_assoc($query))
                        {
                                for($i=1;$i<6;$i++)
                                {	
                                        if($res['hasilpersetujuan'.$i]=='')
                                        {
                                                $sql2="update ".$dbname.".log_prapoht set hasilpersetujuan".$i."='1'";
                                        }
                                }
                        }

                        break;
                }
                else
                {
                        echo $sql2;exit();
                        echo " Gagal,".addslashes(mysql_error($conn));
                }
        }
        break;
        case 'cari_pp':
        if((isset($_POST['txtSearch']))||(isset($_POST['tglCari'])))
        {
                $txt_search=$_POST['txtSearch'];
                $txt_tgl=tanggalsystem($_POST['tglCari']);
                $txt_tgl_a=substr($txt_tgl,0,4);
                $txt_tgl_b=substr($txt_tgl,4,2);
                $txt_tgl_c=substr($txt_tgl,6,2);
                $txt_tgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;
        }

        if($_POST['txtSearch']!='')
        {
                $where="and nopp LIKE  '%".$txt_search."%'  ";
        }
        elseif($_POST['tglCari']!='')
        {
                $where="and tanggal LIKE '%".$txt_tgl."%' ";
        }
        elseif(($txt_tgl!='')&&($txt_search!=''))
        {
                $where="nopp LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%' ";
        }//


        if($_POST['pembuat']!='')
        {
            $where.=" and dibuat='".$_POST['pembuat']."'";
        }
        $str="SELECT * FROM ".$dbname.".log_prapoht where (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."')
                     ".$where." ORDER BY tanggal desc ";
        if($_POST['nmbrg']!='')
        {
            if(strlen($_POST['nmbrg'])>3)
            {

                $where.="and b.kodebarang in (select distinct kodebarang from ".$dbname.".log_5masterbarang where namabarang like '%".$_POST['nmbrg']."%')";
                $str="SELECT distinct a.* FROM ".$dbname.".log_prapoht a left join ".$dbname.".log_prapodt b on a.nopp=b.nopp
                      where (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."')
                     ".$where." ORDER BY tanggal desc ";

            }
            else
            {
                exit("Error:Harus 3 Karakter atau lebih");
            }
        }

                //$sql="SELECT count(*) as jmlhrow FROM ".$dbname.".log_prapoht where  ".$where." ORDER BY nopp DESC";
        //echo "warning :".$str;exit();
/*	$query=mysql_query($sql) or die(mysql_error());
                        while($jsl=mysql_fetch_object($query)){
                        $jlhbrs= $jsl->jmlhrow;
                        }
*/	 
          $res=mysql_query($str) or die(mysql_error($conn));
                $rCek=mysql_num_rows($res);
                if($rCek>0)
                {

                        while($bar=mysql_fetch_assoc($res))
                        {
                        $koderorg=substr($bar['nopp'],15,4);
                        $spr="select namaorganisasi from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
                        $rep=mysql_query($spr) or die(mysql_error($conn));
                        $bas=mysql_fetch_object($rep);
                        $no+=1;
                        echo"<tr class=rowcontent id='tr_".$no."'>
                                  <td>".$no."</td>
                                  <td id=td_".$no.">".$bar['nopp']."</td>
                                  <td>".tanggalnormal($bar['tanggal'])."</td>
                                  <td>".$bas->namaorganisasi."</td>
                                  <td align=center>
                                  <img src=images/pdf.jpg class=resicon width='30' height='30' title='Print' onclick=\"masterPDF('log_prapoht','".$bar['nopp']."','','log_slave_print_log_pp',event);\"> &nbsp
                                  <img src=images/zoom.png class=resicon height='30' title='Preview' onclick=\"previewDetail('".$bar['nopp']."',event);\">    
                                  </td>";      
                                        if($bar['close']==2)
                                        {
                                                $accept=0;
                                                for($i=1;$i<6;$i++)
                                                {
                                                        if($bar['hasilpersetujuan'.$i]=='3')
                                                        {
                                                                $accept=3;
                                                                break;
                                                        }
                                                        elseif($bar['hasilpersetujuan'.$i]=='1')
                                                        {
                                                                $accept=1;

                                                        }
                                                }
                                                if($accept==3) {
                                                        echo"<td colspan=3>".$_SESSION['lang']['ditolak']."</td>";
                                                } elseif($accept==1) {
                                                        echo"<td colspan=3>".$_SESSION['lang']['disetujui']."</td>";
                                                }
                                        }
                                        elseif($bar['close']<2)
                                        {
                                                for($a=1;$a<6;$a++)
                                                {
                                                        if($bar['persetujuan'.$a]!='')
                                                        {
                                                                if(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&(($bar['hasilpersetujuan'.$a]!='')
                                                                and $bar['hasilpersetujuan'.$a]!=0))
                                                                {
                                                                        echo"<td colspan=3>&nbsp;</td>";
                                                                }
                                                                elseif(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&($bar['hasilpersetujuan'.$a]=='' 
                                                                or $bar['hasilpersetujuan'.$a]==0))
                                                                {
                                                                echo"
                                                                <td><a href=# onclick=\"get_data_pp('".$bar['nopp']."','".$a."')\">".$_SESSION['lang']['approve']."</a></td>
                                                                <td><a href=# onclick=rejected_pp('".$bar['nopp']."','".$a."') >".$_SESSION['lang']['ditolak']."</a></td>
                                                                <td><a href=# onclick=\"rejected_some_proses('".$bar['nopp']."','".$a."')\" >
                                                                ".$_SESSION['lang']['ditolak_some']."</a></td>";
                                                                }
                                                        }
                                                }
                                        }
                                 for($i=1;$i<6;$i++)
                                 {
                                        //echo $bar['hasilpersetujuan'.$i];
                                        if(($bar['persetujuan'.$i]!='')&&($bar['persetujuan'.$i]!=0))
                                        {	
                                                $kr=$bar['persetujuan'.$i];
                                                $sql="select * from ".$dbname.".datakaryawan where karyawanid='".$kr."'";
                                                $query=mysql_query($sql) or die(mysql_error());
                                                $yrs=mysql_fetch_assoc($query);
                                                echo"<td><a href=# onclick=\"cek_status_pp('".$bar['hasilpersetujuan'.$i]."')\">".$yrs['namakaryawan']."</a></td>";
                                        }
                                        else
                                        {
                                                echo"<td>&nbsp;</td>";
                                        }
                                 }				 
                                 echo"</tr>";
                }	 	 	

          }
          else
          {
                echo"<tr class=rowcontent><td colspan=13 align=center>Not Found</td></tr>";  
          }
        break;
        case'tolakBeberapa':
        $tglskrng=date("Y-m-d");
        $adrt=0;
        foreach($_POST['kode_brg'] as $lstKdBrg=>$kdbrg)
        {
            $sUpadate="update ".$dbname.".log_prapodt set status=3, alasanstatus='".$_POST['alsan'][$lstKdBrg]."',
                       ditolakoleh='".$_SESSION['standard']['userid']."'
                       where kodebarang='".$kdbrg."' and nopp='".$_POST['nopp']."'";
            if(!mysql_query($sUpadate))
            {
                echo " Gagal,".addslashes(mysql_error($conn));
            }
            else
            {
                $adrt+=1;
            }
        }

            //exit("Error".$sUpdate);
            if($adrt!=0)
            {
            $sData="select distinct dibuat,persetujuan1,persetujuan2,persetujuan3,persetujuan4,persetujuan5
            from ".$dbname.".log_prapoht where nopp='".$_POST['nopp']."'";
            $qData=mysql_query($sData) or die(mysql_error($conn));
            $rData=mysql_fetch_assoc($qData);
            if($rData['persetujuan1']!='')
                $to=$rData['persetujuan1'];
               if($rData['persetujuan2']!='')
                $to.=",".$rData['persetujuan2'];
                if($rData['persetujuan3']!='')
                $to.=",".$rData['persetujuan3'];
                 if($rData['persetujuan4']!='')
                $to.=",".$rData['persetujuan4'];
                 if($rData['persetujuan5']!='')
                $to.=",".$rData['persetujuan5'];

                    #send an email to incharge person
                    $to=getUserEmail($to);
                    $namakaryawan=getNamaKaryawan($rData['dibuat']);
                    $nmpnlk=getNamaKaryawan($rData['persetujuan'.$_POST['kolom']]);
                     if($_SESSION['language']=='EN'){     
                    $subject="[Notification] Partially or all items on PR No:".$_POST['nopp']." submitted by ".$namakaryawan." rejected by ".$nmpnlk;
                    $body="<html>
                             <head>
                             <body>
                               <dd>Dear Sir/Madam,</dd><br>
                               <br>
                                Purchase Request No:".$_POST['nopp']." rejected by [".$nmpnlk."]  corresponding to below notes:
                               <br>
                               Item rejected : <ul>";
                    $sBrg="select kodebarang,alasanstatus from ".$dbname.".log_prapodt where nopp='".$_POST['nopp']."' and status='3'";
                    $qBrg=mysql_query($sBrg) or die(mysql_error($conn));
                    while($rBrg=mysql_fetch_assoc($qBrg))
                    {
                       $body.="<li>".$nmBarang[$rBrg['kodebarang']].", note : ".$rBrg['alasanstatus']."</li>";
                    }
                    $body.="</ul><br>
                               <br>
                               Regards,<br>
                               Owl-Plantation System.
                             </body>
                             </head>
                           </html>
                           ";
                     }else{
                    $subject="[Notifikasi] Sebagian atau Seluruhnya PP No :".$_POST['nopp']." dari ".$namakaryawan." ditolak oleh ".$nmpnlk;
                    $body="<html>
                             <head>
                             <body>
                               <dd>Dengan Hormat,</dd><br>
                               <br>
                               Permintaan pembelian no.".$_POST['nopp']." ditolak oleh [".$nmpnlk."] dengan alasan.
                               <br>
                               Item yang ditolak adalah : <ul>";
                    $sBrg="select kodebarang,alasanstatus from ".$dbname.".log_prapodt where nopp='".$_POST['nopp']."' and status='3'";
                    $qBrg=mysql_query($sBrg) or die(mysql_error($conn));
                    while($rBrg=mysql_fetch_assoc($qBrg))
                    {
                       $body.="<li>".$nmBarang[$rBrg['kodebarang']].", alasan : ".$rBrg['alasanstatus']."</li>";
                    }
                    $body.="</ul><br>
                               <br>
                               Regards,<br>
                               Owl-Plantation System.
                             </body>
                             </head>
                           </html>
                           ";                         
                     }
                     
                   $x=kirimEmail($to,$subject,$body);#this has return but disobeying;
                   echo $x;
                }  

        break;

        default:
        break;
        }
?>	