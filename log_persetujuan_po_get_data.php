<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/fpdf.php');
include_once('lib/zMysql.php');

?>

<?php
        $method=$_POST['method'];
        $nopo=$_POST['nopo'];
        $user_id=$_SESSION['standard']['userid'];
        switch($method)
        {
        case 'get_form_approval'	:
        $sql="select * from ".$dbname.".log_poht where nopo='".$nopo."'";
        $query=mysql_query($sql) or die(mysql_error());
        $rest=mysql_fetch_assoc($query);

        for($i=1;$i<4;$i++)
        {
           //echo "warning : ".$sql."_____________".$i."_".$rest['persetujuan'.$i];
                if($user_id==$rest['persetujuan'.$i])
                {
                        if($rest['persetujuan3']!='')
                        {
                                echo"<br /><div id=approve>
                                <fieldset>
                                <legend><input type=text readonly=readonly name=rnopo id=rnopo value=".$nopo."  /></legend>
                                <table cellspacing=1 border=0>
                                <tr>
                                <td colspan=3>
                                Submit to Purchasing dept for Release</td></tr>

                                <tr><td colspan=3 align=center>
                                <button class=mybutton onclick=close_po() >".$_SESSION['lang']['yes']."</button><button class=mybutton onclick=cancel_po() >".$_SESSION['lang']['no']."</button></td></tr></table><input type=hidden name=kolom id=kolom />
                                </fieldset>
                                </div>";
                        }
                        else
                                {	
                                        echo"<br />
                                        <div id=test>
                                        <fieldset>
                                        <legend><input type=text readonly=readonly name=rnopo id=rnopo value=".$nopo."  /></legend>
                                        <table cellspacing=1 border=0>
                                        <tr>
                                        <td colspan=3>
                                        Submit for the next verification :</td>
                                        </tr>
                                        <td>".$_SESSION['lang']['namakaryawan']."</td>
                                        <td>:</td>
                                        <td valign=top>";

                                        $optPur='';
                                        $klq="select karyawanid,namakaryawan,bagian,lokasitugas from ".$dbname.".`datakaryawan` where tipekaryawan='0' and karyawanid!='".$user_id."' and lokasitugas!='' order by namakaryawan asc"; 
                                        //echo $klq;
                                        $qry=mysql_query($klq) or die(mysql_error());
                                        while($rst=mysql_fetch_object($qry))
                                        {
                                                $sBag="select nama from ".$dbname.".sdm_5departemen where kode='".$rst->bagian."'";
                                                $qBag=mysql_query($sBag) or die(mysql_error());
                                                $rBag=mysql_fetch_assoc($qBag);
                                                $optPur.="<option value='".$rst->karyawanid."'>".$rst->namakaryawan." [".$rst->lokasitugas."] [".$rBag['nama']."]</option>";
                                        }

                                        echo"
                                                <select id=id_user name=id_user>
                                                        $optPur; 
                                                </select></td></tr>
                                                <tr>

                                                <td colspan=3 align=center>
                                                <button class=mybutton onclick=forward_po() title=\"Submit for the next verification\" >".$_SESSION['lang']['diajukan']."</button>
                                                <button class=mybutton onclick=close_form_po() title=\"Submit to Purchasing dept for Release\"  >".$_SESSION['lang']['kePurchaser']."</button>
                                                <button class=mybutton onclick=cancel_po() title=\"Menutup Form Ini\">".$_SESSION['lang']['close']."</button>
                                                </td></tr></table><br /> 

                                                </fieldset></div>
                                                <div id=approve style=display:none>
                                                <fieldset>
                                                <legend><input type=text readonly=readonly name=rnopo id=rnopo value=".$nopo."  /></legend>
                                                <table cellspacing=1 border=0>
                                                <tr>
                                                <td colspan=3>
                                                Submit to Purchasing dept for Release</td></tr>

                                                <tr><td colspan=3 align=center>
                                                <button class=mybutton onclick=close_po() >".$_SESSION['lang']['yes']."</button>
                                                <button class=mybutton onclick=cancel_po() >".$_SESSION['lang']['no']."</button></td></tr></table>
                                                </fieldset>
                                                </div>
                                                <input type=hidden name=method id=method  /> 
                                                <input type=hidden name=user_id id=user_id value=".$user_id." />
                                                <input type=hidden name=nopo id=nopo value=".$nopo."  />
                                                <input type=hidden name=kolom id=kolom />
                                                ";
                                }
                }
        }

                break;
                case 'get_form_rejected':
                echo"<div id=rejected_form>
                <fieldset>
                <legend><input type=text readonly=readonly name=rnopo id=rnopo value=".$nopo."  /></legend>
                <table cellspacing=1 border=0>
                <tr>
                <td colspan=3>
                Are you sure rejecting this PO</td></tr>
                <tr><td colspan=3 align=center>
                <button class=mybutton onclick=rejected_po_proses() >".$_SESSION['lang']['yes']."</button>
                <button class=mybutton onclick=cancel_po() >".$_SESSION['lang']['no']."</button>
                </td></tr></table>
                </fieldset>
                </div>
                <input type=hidden name=method id=method  /> 
                <input type=hidden name=user_id id=user_id value=".$user_id." />
                <input type=hidden name=nopo id=nopo value=".$nopo."  />
                <input type=hidden name=kolom id=kolom />
                ";
                break;
                case 'cari_po' :
                        if((isset($_POST['txtSearch']))||(isset($_POST['tglCari'])))
                        {
                        $txt_search=$_POST['txtSearch'];
                        //$txt_tgl=str_replace('-','',$_POST['tglCari']);
                        $txt_tgl=tanggalsystem($_POST['tglCari']);
                        //$txt_tgl=$txt_tgl);
                        $txt_tgl_a=substr($txt_tgl,0,4);
                        $txt_tgl_b=substr($txt_tgl,4,2);
                        $txt_tgl_c=substr($txt_tgl,6,2);
                        $txt_tgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;
                        //echo $txt_tgl_a;
                        }
                        else
                        {
                        $txt_search='';
                        $txt_tgl='';			
                        }//			

                        if($txt_search!='')
                        {
                        $where=" nopo LIKE  '%".$txt_search."%' and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."')";
                        }
                        elseif($txt_tgl!='')
                        {
                        $where.="  tanggal LIKE '".$txt_tgl."' and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."')";
                        }
                        elseif(($txt_tgl!='')&&($txt_search!=''))
                        {
                        $where.="  nopo LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%' and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."')";
                        }//

                        if($txt_search==''&&$txt_tgl=='')
                        {

                        $strx="select * from ".$dbname.".log_poht where (persetujuan1='".$_SESSION['standard']['userid']."' 
                        or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."') order by nopo desc";

                        }//
                        else
                        {

                        $strx="select * from ".$dbname.".log_poht where ".$where;

                        }//
                        //echo "warning :".$strx;exit();
                        if($res=mysql_query($strx))
                        {
                                $numrows=mysql_num_rows($res);
                                if($numrows<1)
                                {
                                        echo"<tr class=rowcontent><td colspan=13>Not Found</td></tr>";
                                }
                                else
                                {
                                        while($bar=mysql_fetch_assoc($res))
                                        {
                                                $kodeorg=$bar['kodeorg'];
                                                $spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
                                                $rep=mysql_query($spr) or die(mysql_error($conn));
                                                $bas=mysql_fetch_object($rep);
                                                $no+=1;
                                                echo"<tr class=rowcontent id='tr_".$no."'>
                                                <td>".$no."</td>
                                                <td id=td_".$no.">".$bar['nopo']."</td>
                                                <td>".tanggalnormal($bar['tanggal'])."</td>
                                                <td>".$bas->namaorganisasi."</td>
                                                <td align=center>
                                                <img src=images/pdf.jpg class=resicon width='30' height='30' title='Print' 
                                                onclick=\"masterPDF('log_poht','".$bar['nopo']."','','log_slave_print_detail_po',event);\"></td>";                            
                                                                for ($a=1;$a<4;$a++)
                                                                 {	
                                                                        if($bar['persetujuan'.$a]!='')
                                                                        {
                                                                                        if(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&($bar['hasilpersetujuan'.$a]!=''))
                                                                                         {
                                                                                                  echo"
                                                                                                <td><button class=mybutton disabled onclick=\"get_data_po('".$bar['nopo']."')\">".$_SESSION['lang']['approve']."</button></td>
                                                                                                <td><button class=mybutton disabled onclick=rejected_po('".$bar['nopo']."') >".$_SESSION['lang']['ditolak']."</button></td>
                                                                                                ";                           

                                                                                         }
                                                                                         else if(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&($bar['hasilpersetujuan'.$a]==''))
                                                                                         {

                                                                                                echo"
                                                                                                <td><button class=mybutton onclick=\"get_data_po('".$bar['nopo']."')\">".$_SESSION['lang']['approve']."</button></td>
                                                                                                <td><button class=mybutton onclick=rejected_po('".$bar['nopo']."') >".$_SESSION['lang']['ditolak']."</button></td>
                                                                                                </td>";


                                                                                         }


                                                                        }
                                                                 }
                                                for($i=1;$i<4;$i++)
                                                {
                                                //echo $bar['hasilpersetujuan'.$i];
                                                if($bar['persetujuan'.$i]!='')
                                                {	
                                                $kr=$bar['persetujuan'.$i];
                                                $sql="select * from ".$dbname.".datakaryawan where karyawanid='".$kr."'";
                                                $query=mysql_query($sql) or die(mysql_error());
                                                $yrs=mysql_fetch_assoc($query);
                                                        if($bar['hasilpersetujuan'.$i]=='')
                                                        {
                                                                $b="No Decision yet ";
                                                        }
                                                        elseif($bar['hasilpersetujuan'.$i]=='1')
                                                        {	
                                                                $b=$_SESSION['lang']['approve'];
                                                        }
                                                        elseif($bar['hasilpersetujuan'.$i]=='3')
                                                        {
                                                                $b=$_SESSION['lang']['ditolak'];
                                                        }	
                                                        echo"<td align=center>".$yrs['namakaryawan']."<br />(".$b.")</td>";

                                                }
                                                else
                                                {
                                                echo"<td>&nbsp;</td>";
                                                }
                                                }
                                                echo"</tr><input type=hidden id=nopo_".$no." name=nopo_".$no." value='".$bar['nopo']."' />";
                                        }//while
                                 }//else
                        }//	
                        else
                        {
                        echo "Gagal,".(mysql_error($conn));
                        }	
                break;

                default:
                break;
        }
?>