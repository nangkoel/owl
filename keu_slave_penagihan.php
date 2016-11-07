<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
$param=$_POST;
$optnmcust=makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
switch($param['proses']){
    case'insert':
        if($param['noinvoice']==''){
            exit("error:Noinvoice tidak boleh kosong");
        }
        if($param['tanggal']==''){
            exit("error:Tanggal tidak boleh kosong");
        }
        if($param['nilaiinvoice']==''){
            exit("error:Nilai invoice tidak boleh kosong");
        }
        if($param['nilaippn']==''){
            $param['nilaippn']=0;
        }
        if($param['uangmuka']==''){
            $param['uangmuka']=0;
        }
        if($param['jatuhtempo']==''){
            $param['jatuhtempo']='0000-00-00 00:00:00';
        }
        $sdel="delete from ".$dbname.".keu_penagihanht where noinvoice='".$param['noinvoice']."'";
        if(mysql_query($sdel)){
                $sinser="insert into ".$dbname.".keu_penagihanht 
                         (noinvoice,kodeorg,tanggal,noorder,kodecustomer,nilaiinvoice,nilaippn,jatuhtempo,keterangan,bayarke,debet,kredit,nofakturpajak,uangmuka) values 
                         ('".$param['noinvoice']."','".$param['kodeorganisasi']."','".tanggalsystem($param['tanggal'])."','".$param['noorder']."','".$param['kodecustomer']."','".$param['nilaiinvoice']."'
                          ,'".$param['nilaippn']."','".tanggalsystem($param['jatuhtempo'])."','".$param['keterangan']."','".$param['bayarke']."','".$param['debet']."','".$param['kredit']."','".$param['nofakturpajak']."','".$param['uangmuka']."')";
                if(!mysql_query($sinser)){
                    exit("error: code 1125\n ".  mysql_error($conn)."___".$sinser);
                }
        }else{
                exit("error: code 1125\n ".  mysql_error($conn)."___".$sdel);
        }
    break;
    case'genNo':
           $dtg=date("Ymdhis");
        echo $dtg;
    break;
    case'loadData':
        if($param['noinvoice']!=''){            
            $where=" and noinvoice like '%".$param['noinvoice']."%'";
        }
        if($param['tanggalCr']!=''){            
            $tgrl=explode("-",$param['tanggalCr']);
            $ert=$tgrl[2]."-".$tgrl[1]."-".$tgrl[0];
            $where=" and left(tanggal,10) = '".$ert."'";
        }
        $sdel="";
        $limit=10;
        $page=0;
        if(isset($_POST['page']))
        {
            $page=$_POST['page'];
            if($page<0)
            $page=0;
        }
        $offset=$page*$limit;
        $sql="select count(*) jmlhrow from ".$dbname.".keu_penagihanht where kodeorg='".$_SESSION['empl']['lokasitugas']."' ".$where." order by tanggal desc";
        $query=mysql_query($sql) or die(mysql_error());
        while($jsl=mysql_fetch_object($query)){
            $jlhbrs= $jsl->jmlhrow;
        }

        $str="select * from ".$dbname.".keu_penagihanht where kodeorg='".$_SESSION['empl']['lokasitugas']."'  ".$where."  order by tanggal desc
              limit ".$offset.",".$limit." ";
        
        $qstr=mysql_query($str) or die(mysql_error($conn));
        while($rstr=  mysql_fetch_assoc($qstr)){
            $nor+=1;
           
           $tab.="<tr ".$bgdr." class=rowcontent>
                 <td id='noinvoice_".$nor."' align=center value='".$rstr['noinvoice']."'>".$rstr['noinvoice']."</td>
                 <td id='kodeorg_".$nor."' align=center value='".$rstr['kodeorg']."'>".$rstr['kodeorg']."</td>
                 <td id='tanggal_".$nor."' align=center value='".$rstr['tanggal']."'>".tanggalnormal(substr($rstr['tanggal'],0,10))."</td>
                 <td id='noakun_".$nor."' align=center value='".$rstr['noorder']."'>".$rstr['noorder']."</td>
                 <td align=center>".$rstr['jumlah']."</td>
                 <td align=center>".$rstr['keterangan']."</td>";
           if($rstr['posting']==0){
               $tab.="<td align=center><img src=images/application/application_edit.png class=resicon  title='Edit ".$rstr['noinvoice']."' onclick=\"fillField('".$rstr['noinvoice']."');\" ></td>";
               $tab.="<td align=center><img src=images/application/application_delete.png class=resicon  title='Hapus ".$rstr['noinvoice']."' onclick=\"delData('".$rstr['noinvoice']."');\" ></td>";
               $tab.="<td align=center><img src=images/pdf.jpg class=resicon  title='Detail ".$rstr['notransaksi']."' onclick=\"masterPDF('keu_penagihanht','".$rstr['noinvoice']."','','keu_slave_print_pengihan',event);\" ></td>";
               $tab.="<td align=center><img src=images/skyblue/posting.png class=resicon  title='Posting ".$rstr['noinvoice']."' onclick=\"postingData('".$rstr['noinvoice']."');\" ></td>";
           }else{
               $tab.="<td align=center colspan=2><img src=images/pdf.jpg class=resicon  title='Detail ".$rstr['noinvoice']."'  onclick=\"masterPDF('keu_penagihanht','".$rstr['noinvoice']."','','keu_slave_print_pengihan',event);\" ></td>";
               $tab.="<td align=center colspan=2><img src=images/skyblue/posted.png class=resicon  title='Posted ".$rstr['noinvoice']."' ></td>";
           }
           $tab.="</tr>"; 
        }
            $skeupenagih="select count(*) as rowd from ".$dbname.".keu_penagihanht where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            $qkeupenagih=mysql_query($skeupenagih) or die(mysql_error($conn));
            $rkeupenagih=mysql_num_rows($qkeupenagih);
            $totrows=ceil($rkeupenagih/$limit);
            if($totrows==0){
            $totrows=1;
            }
            for($er=1;$er<=$totrows;$er++){
            $isiRow.="<option value='".$er."'>".$er."</option>";
            }
            $footd.="</tr>
            <tr><td colspan=10 align=center>
            
            <button class=mybutton onclick=loadData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
            <select id=\"pages\" name=\"pages\" style=\"width:50px\" onchange=\"getPage()\">".$isiRow."</select>
            <button class=mybutton onclick=loadData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
            </td>
            </tr>";
     echo $tab."####".$footd;
    break;
    case'getData':
    $sdata="select distinct * from ".$dbname.".keu_penagihanht where noinvoice='".$param['noinvoice']."'";
    $qdata=mysql_query($sdata) or die(mysql_error($conn));
    $rdata=mysql_fetch_assoc($qdata);
    //noinvoice,kodeorg,tanggal,noorder,kodecustomer,nilaiinvoice,nilaippn,jatuhtempo,keterangan,bayarke,debet,kredit,nofakturpajak
    echo $rdata['noinvoice']."###".$rdata['kodeorg']."###".tanggalnormal(substr($rdata['tanggal'],0,10))."###".$rdata['noorder']."###".$rdata['kodecustomer']."###".$rdata['nilaiinvoice']."###".$rdata['nilaippn']."###".tanggalnormal(substr($rdata['jatuhtempo'],0,10))."###".$rdata['keterangan']."###".$rdata['bayarke']."###".$rdata['debet']."###".$rdata['kredit']."###".$rdata['nofakturpajak']."###".$rdata['uangmuka'];
    break;
    case'getFormNosipb':
        $optSupplierCr="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sSuplier="select distinct supplierid,namasupplier,substr(kodekelompok,1,1) as status from ".$dbname.".log_5supplier order by namasupplier asc";
        $qSupplier=mysql_query($sSuplier) or die(mysql_error($sSupplier));
        while($rSupplier=mysql_fetch_assoc($qSupplier))
        {
            $optSupplierCr.="<option value='".$rSupplier['supplierid']."'>".$rSupplier['namasupplier']." [".$rSupplier['status']."]</option>";
        }
        $form="<fieldset style=float: left;>
               <legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['nosipb']."</legend>
               ".$_SESSION['lang']['nosipb']."&nbsp;<input type=text class=myinputtext id=nosipbcr />&nbsp;&nbsp;&nbsp;<button class=mybutton onclick=findNosipb()>".$_SESSION['lang']['find']."</button></fieldset>
               <fieldset><legend>".$_SESSION['lang']['result']."</legend><div id=container2 style=overflow:auto;width:100%;height:430px;></fieldset></div>";
        echo $form;
    break;
    case'getnosibp':
        //txtfind
    $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
    $tab.="<thead>";
    $tab.="<tr><td>".$_SESSION['lang']['nosipb']."</td>";
    $tab.="<td>".$_SESSION['lang']['kodecustomer']."</td>";
    $tab.="<td>".$_SESSION['lang']['namacustomer']."</td></tr></thead><tbody>";
    $sdata="select distinct a.SIPBNO,b.koderekanan from ".$dbname.".pabrik_mssipb a
            left join ".$dbname.".pmn_kontrakjual b on a.CTRNO=b.nokontrak
            where SIPBNO like '%".$param['txtfind']."%'";
    $qdata=mysql_query($sdata) or die(mysql_error($conn));
    while($rdata=  mysql_fetch_assoc($qdata)){
        $brt="style=cursor:pointer; onclick=setData('".$rdata['SIPBNO']."','".$rdata['koderekanan']."')";
        $tab.="<tr ".$brt." class=rowcontent><td>".$rdata['SIPBNO']."</td>";
        $tab.="<td>".$rdata['koderekanan']."</td>";
        $tab.="<td>".$optnmcust[$rdata['koderekanan']]."</td></tr>";
    }
    $tab.="</tbody></table>";
    echo $tab;
    break;
    case'delData':
        $sdel="delete from ".$dbname.".keu_penagihanht where noinvoice='".$param['noinvoice']."'";
        if(!mysql_query($sdel)){
            exit("error: gak berhasil".mysql_error($conn)."___".$sdel);
        }
    break;
    case'postingData':
    $sdata="select distinct * from ".$dbname.".keu_penagihanht where noinvoice='".$param['noinvoice']."'";
    $qdata=mysql_query($sdata) or die(mysql_error($conn));
    $rdata=mysql_fetch_assoc($qdata);
    $roc=mysql_num_rows($qdata);
    #=== Cek if posted ===
    $error0 = "";
    if($rdata['posting']==1) {
        $error0 .= $_SESSION['lang']['errisposted'];
    }
    if($error0!='') {
        echo "Data Error :\n".$error0;
        exit;
    }
    #====cek periode
    $tgl = str_replace("-","",$rdata['tanggal']);
    if($_SESSION['org']['period']['start']>$tgl)
        exit('Error:Date beyond active period');
    #=== Cek if data not exist ===
    $error1 = "";
    if($roc==0) {
        $error1 .= $_SESSION['lang']['errheadernotexist']."\n";
    }
    if($error1!='') {
        echo "Data Error :\n".$error1;
        exit;
    }
    //ALTER TABLE `keu_penagihanht` CHANGE `posting` `posting` TINYINT( 1 ) NOT NULL DEFAULT '0';
    $yy=tanggalnormal(substr($rdata['tanggal'],0,10));
    $isyy=tanggalsystem($yy);
    $norjunal=$isyy."/".$_SESSION['empl']['lokasitugas']."/PNJ/";
    $snojr="select max(substr(nojurnal,19,7)) as nourut from ".$dbname.".keu_jurnalht where nojurnal like '".$norjunal."%'";
    
    $qnojr=mysql_query($snojr) or die(mysql_error($conn));
    $rnojr=mysql_fetch_assoc($qnojr);
    $nourut=addZero((intval($rnojr['nourut'])+1), '3');
    $jmlall=($rdata['nilaiinvoice']+$rdata['nilaippn'])-$rdata['uangmuka'];
    $nojurnal=$norjunal.$nourut;
    //exit("error:".$rnojr['nourut']."__".$nojurnal);
    $sinsert="insert into ".$dbname.".keu_jurnalht 
             (nojurnal, kodejurnal, tanggal, tanggalentry, posting, totaldebet, totalkredit, amountkoreksi, noreferensi, autojurnal, matauang, kurs, revisi)
             values 
             ('".$nojurnal."','PNJ','".$isyy."','".date('Y-m-d')."','1','".$jmlall."','-".$jmlall."','0','".$rdata['noinvoice']."','1','IDR','1','0')";
    if(mysql_query($sinsert)){
        $sins="insert into ".$dbname.".keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier)
               values ('".$nojurnal."','".$isyy."','1','".$rdata['debet']."','PIUTANG DARI ".$optnmcust[$rdata['kodecustomer']]."','".$jmlall."','IDR','1','".$_SESSION['empl']['lokasitugas']."',
                       '','".$rdata['noinvoice']."','".$rdata['noorder']."','0','',''),
                       ('".$nojurnal."','".$isyy."','2','".$rdata['kredit']."','PIUTANG DARI ".$optnmcust[$rdata['kodecustomer']]."','-".$jmlall."','IDR','1','".$_SESSION['empl']['lokasitugas']."',
                       '".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".$rdata['noorder']."','0','','')";
        if(!mysql_query($sins)){
            exit("error: gak berhasil".mysql_error($conn)."___".$sins);
        }else{
            $supd="update ".$dbname.".keu_penagihanht set posting=1 where noinvoice='".$rdata['noinvoice']."'";
            if(!mysql_query($supd)){
                 exit("error: gak berhasil".mysql_error($conn)."___".$supd);
            }
        }
    }else{
        exit("error: gak berhasil".mysql_error($conn)."___".$sinsert);
    }
                 

    break;
}

?>