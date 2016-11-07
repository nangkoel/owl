<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$param = $_POST;
$proses = $_POST['proses'];

//        $sorg="select distinct kodetimbangan,namacustomer from ".$dbname.".pmn_4customer where kodetimbangan like '1%' order by namacustomer";
        $sorg="select distinct kodetimbangan,namasupplier from ".$dbname.".log_5supplier where kodetimbangan like '1%' order by namasupplier";
        $qorg=mysql_query($sorg) or die(mysql_error($conn));
        while($rorg=mysql_fetch_assoc($qorg)){
            $kamuscust[$rorg['kodetimbangan']]=$rorg['namasupplier'];
        }        
 
switch($proses) {
    # Daftar Header
    case 'loadData':
//	$where = "afdeling in (select distinct kodetimbangan from ".$dbname.".pmn_4customer where kodetimbangan like '1%' order by namacustomer)";
	$where = "afdeling in (select distinct kodetimbangan from ".$dbname.".log_5supplier where kodetimbangan like '1%' order by namasupplier)";
        
	$tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%><thead><tr align=center>";
//        $tab.="<td>".$_SESSION['lang']['mandor']."</td>";
        $tab.="<td>".$_SESSION['lang']['nmcust']."</td>";
        $tab.="<td>".$_SESSION['lang']['tanggal']."</td>";
        $tab.="<td>".$_SESSION['lang']['kg']."</td>";
//        $tab.="<td>".$_SESSION['lang']['bjr']."</td>";
        $tab.="<td colspan=2>".$_SESSION['lang']['action']."</td>";
        $tab.="</tr></thead><tbody>";
        $limit=10;
        $page=0;
        if(isset($_POST['page']))
        {
            $page=$_POST['page'];
            if($page<0)
            $page=0;
        }
        if($_POST['page2']!=''){
         $page=$_POST['page2']-1;   
        }
        $offset=$page*$limit;
        
//        $sdata="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan 
//            where lokasitugas like '".$_SESSION['empl']['lokasitugas']."%' and tipekaryawan!='4' order by namakaryawan asc";
//        //echo $sdata;
//        $qdata=mysql_query($sdata) or die(mysql_error($conn));
//        while($rdata=  mysql_fetch_assoc($qdata)){
//            $kamuskaryawan[$rdata['karyawanid']]=$rdata['namakaryawan'];            
//        }
        
        $sdata="select distinct * from ".$dbname.".kebun_taksasi where ".$where." order by tanggal desc limit ".$offset.",".$limit." ";
        //echo $sdata;
        $qdata=mysql_query($sdata) or die(mysql_error($conn));
        while($rdata=  mysql_fetch_assoc($qdata)){
            $tab.="<tr class=rowcontent align=center>";
//            $tab.="<td>".$kamuskaryawan[$rdata['karyawanid']]."</td>";
            $tab.="<td>".$kamuscust[$rdata['afdeling']]."</td>";
            $tab.="<td>".tanggalnormal($rdata['tanggal'])."</td>";
            $tab.="<td align=right>".$rdata['kg']."</td>";
//            $tab.="<td align=right>".$rdata['bjr']."</td>";
            $tab.="<td><img title=\"Edit\" onclick=\"showEdit('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."')\" class=\"zImgBtn\" src=\"images/skyblue/edit.png\"></td>";
            $tab.="<td><img title=\"Delete\" onclick=\"deleteData('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."')\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\"></td>";
            $tab.="</tr>";
        }
        $tab.="</tbody><tfoot>";
        $tab.="<tr>";
        $tab.="<td colspan=10 align=center>";
        $tab.="<img src=\"images/skyblue/first.png\" onclick='loadData(0)' style='cursor:pointer'>";
        $tab.="<img src=\"images/skyblue/prev.png\" onclick='loadData(".($page-1).")'  style='cursor:pointer'>";
        
        $spage="select distinct * from ".$dbname.".kebun_taksasi where ".$where."";
        //echo $spage;
        $qpage=mysql_query($spage) or die(mysql_error($conn));
        $rpage=mysql_num_rows($qpage);
        $tab.="<select id='pages' style='width:50px' onchange='loadData(1.1)'>";
        @$totalPage=ceil($rpage/10);
        for($starAwal=1;$starAwal<=$totalPage;$starAwal++)
        {
            $_POST['page']=='1.1'?$_POST['page']=$_POST['page2']:$_POST['page']=$_POST['page'];
            $tab.="<option value='".$starAwal."' ".($starAwal==$_POST['page']?'selected':'').">".$starAwal."</option>";
        }
        $tab.="</select>";
        $tab.="<img src=\"images/skyblue/next.png\" onclick='loadData(".($page+1).")'  style='cursor:pointer'>";
        $tab.="<img src=\"images/skyblue/last.png\" onclick='loadData(".intval($totalPage).")'  style='cursor:pointer'>";
        $tab.="</td></tr></tfoot></table>";
	 
	echo $tab;
	break;
        case 'cariData':
//	$where = "afdeling in (select distinct kodeorganisasi from ".$dbname.".organisasi where tipe='AFDELING')";
        if($param['sNoTrans']!=''){
            $tgl=explode("-",$param['sNoTrans']);
            $param['tanggal']=$tgl[2]."-".$tgl[1]."-".$tgl[0];
            $where.=" tanggal like '%".$param['tanggal']."%'";
        }
	$tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%><thead><tr align=center>";
        $tab.="<td>".$_SESSION['lang']['nmcust']."</td>";
        $tab.="<td>".$_SESSION['lang']['tanggal']."</td>";
        $tab.="<td>".$_SESSION['lang']['kg']."</td>";
        $tab.="<td colspan=2>".$_SESSION['lang']['action']."</td>";
        $tab.="</tr></thead><tbody>";
        $limit=10;
        $page=0;
        if(isset($_POST['page']))
        {
            $page=$_POST['page'];
            if($page<0)
            $page=0;
        }
        if($_POST['page2']!=''){
         $page=$_POST['page2']-1;   
        }
        $offset=$page*$limit;
        $sdata="select distinct * from ".$dbname.".kebun_taksasi where ".$where." order by tanggal desc limit ".$offset.",".$limit." ";
        //echo $sdata;
        $qdata=mysql_query($sdata) or die(mysql_error($conn));
        while($rdata=  mysql_fetch_assoc($qdata)){
            $tab.="<tr class=rowcontent align=center>";
//            $tab.="<td>".$kamuskaryawan[$rdata['karyawanid']]."</td>";
            $tab.="<td>".$kamuscust[$rdata['afdeling']]."</td>";
            $tab.="<td>".tanggalnormal($rdata['tanggal'])."</td>";
            $tab.="<td align=right>".$rdata['kg']."</td>";
//            $tab.="<td align=right>".$rdata['bjr']."</td>";
            $tab.="<td><img title=\"Edit\" onclick=\"showEdit('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."')\" class=\"zImgBtn\" src=\"images/skyblue/edit.png\"></td>";
            $tab.="<td><img title=\"Delete\" onclick=\"deleteData('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."')\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\"></td>";
            $tab.="</tr>";
        }
        $tab.="</tbody><tfoot>";
        $tab.="<tr>";
        $tab.="<td colspan=10 align=center>";
        $tab.="<img src=\"images/skyblue/first.png\" onclick='cariData(0)' style='cursor:pointer'>";
        $tab.="<img src=\"images/skyblue/prev.png\" onclick='cariData(".($page-1).")'  style='cursor:pointer'>";
        
        $spage="select distinct * from ".$dbname.".kebun_taksasi where ".$where."";
        //echo $spage;
        $qpage=mysql_query($spage) or die(mysql_error($conn));
        $rpage=mysql_num_rows($qpage);
        $tab.="<select id='pages' style='width:50px' onchange='cariData(1.1)'>";
        @$totalPage=ceil($rpage/10);
        for($starAwal=1;$starAwal<=$totalPage;$starAwal++)
        {
            $_POST['page']=='1.1'?$_POST['page']=$_POST['page2']:$_POST['page']=$_POST['page'];
            $tab.="<option value='".$starAwal."' ".($starAwal==$_POST['page']?'selected':'').">".$starAwal."</option>";
        }
        $tab.="</select>";
        $tab.="<img src=\"images/skyblue/next.png\" onclick='cariData(".($page+1).")'  style='cursor:pointer'>";
        $tab.="<img src=\"images/skyblue/last.png\" onclick='cariData(".intval($totalPage).")'  style='cursor:pointer'>";
        $tab.="</td></tr></tfoot></table>";
	# Content
	$cols = "notransaksi,tanggal,kodeorg,kodetangki,kuantitas,suhu";
	echo $tab;
	break;
   case'insert':
       #var ek//$arr="##tanggal##customer##proses##jjgmasak##bjr";
       $param['kg']==''?$param['kg']=0:$param['kg']=$param['kg'];
//       $param['bjr']==''?$param['bjr']=0:$param['bjr']=$param['bjr'];
       
       #end var
       
       $tgl=explode("-",$param['tanggal']);
       $param['tanggal']=$tgl[2]."-".$tgl[1]."-".$tgl[0];
       
       $scek2="select distinct * from ".$dbname.".kebun_taksasi where tanggal='".$param['tanggal']."' and afdeling='".$param['customer']."'";
       $qcek2=mysql_query($scek2) or die(mysql_error($conn));
       $rcek2=mysql_num_rows($qcek2);
       if($rcek2!=0){
//            exit("error: Data sudah pernah diinput.");
           
            $sins="update ".$dbname.".kebun_taksasi  set `kg`='".$param['kg']."'
             where tanggal='".$param['tanggal']."' and afdeling='".$param['customer']."'";
            if(!mysql_query($sins)){
            exit("error:".mysql_error($conn)."__".$sins);
            }
       }else{
            $scek="select distinct * from ".$dbname.".kebun_taksasi 
              where tanggal='".$param['tanggal']."' and afdeling='".$param['customer']."'";
            //exit("error:".$scek);
            $qcek=mysql_query($scek) or die(mysql_error($conn));
            $rcek=mysql_num_rows($qcek);
            if($rcek!=0){
            exit("error:Data Sudah Ada");
            }
            $sins="insert into ".$dbname.".kebun_taksasi  
            (`afdeling`,`tanggal`, `kg`)
            values ('".$param['customer']."','".$param['tanggal']."','".$param['kg']."')";
            if(!mysql_query($sins)){
            exit("error:".mysql_error($conn)."__".$sins);
            }
       }

   break;
   case'getData':
    $tgl=explode("-",$param['tanggal']);
    $param['tanggal']=$tgl[2]."-".$tgl[1]."-".$tgl[0];
    $str="select distinct * from ".$dbname.".kebun_taksasi 
          where tanggal='".$param['tanggal']."' and 
          afdeling='".$param['afdeling']."'";
   //exit("error:".$str);
   $qstr=mysql_query($str) or die(mysql_error($conn));
   $rts=mysql_fetch_assoc($qstr);
   
   echo $rts['afdeling']."###".tanggalnormal($rts['tanggal'])."###".$rts['kg'];
   break;
    case 'delete': 
    $tgl=explode("-",$param['tanggal']);
    $param['tanggal']=$tgl[2]."-".$tgl[1]."-".$tgl[0];
	$where = "tanggal='".$param['tanggal']."' and afdeling='".$param['afdeling']."'";
	$query = "delete from `".$dbname."`.`kebun_taksasi` where ".$where;
        //exit("error:".$query);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
    break;
//    case'getAfd':
//        $optafd="";
//    $sorg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='AFDELING' and induk='".$param['kebun']."'";
//    //echo $sorg;
//    //exit("error:".$sorg);
//    $qorg=mysql_query($sorg) or die(mysql_error($conn));
//    while($rorg=mysql_fetch_assoc($qorg)){
//        if($param['afdeling']!=''){
//            $optafd.="<option value='".$rorg['kodeorganisasi']."' ".($param['afdeling']==$rorg['kodeorganisasi']?"selected":"").">".$rorg['namaorganisasi']."</option>";
//        }
//        else{
//            $optafd.="<option value='".$rorg['kodeorganisasi']."'>".$rorg['namaorganisasi']."</option>";
//        }
//    }
//    $sorg2="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
//            where tipe='BLOK' and kodeorganisasi like '".$param['kebun']."%' 
//            and kodeorganisasi in (select distinct kodeorg from ".$dbname.".setup_blok where left(kodeorg,4)='".$param['kebun']."' and luasareaproduktif!=0)";
//    $qorg2=mysql_query($sorg2) or die(mysql_error($conn));
//    while($rorg2=mysql_fetch_assoc($qorg2)){
//        if($param['blok']!=''){
//            $optafd2.="<option value='".$rorg2['kodeorganisasi']."' ".($param['blok']==$rorg2['kodeorganisasi']?"selected":"").">".$rorg2['namaorganisasi']."</option>";
//        }
//        else{
//            $optafd2.="<option value='".$rorg2['kodeorganisasi']."'>".$rorg2['namaorganisasi']."</option>";
//        }
//    }
////    $sorg2="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan 
////            where lokasitugas='".$param['kebun']."' and tipekaryawan!='4' order by namakaryawan asc";
////    
////    $qorg2=mysql_query($sorg2) or die(mysql_error($conn));
////    while($rorg2=mysql_fetch_assoc($qorg2)){
////        if($param['mandor']!=''){
////            $optafd2.="<option value='".$rorg2['karyawanid']."' ".($param['mandor']==$rorg2['karyawanid']?"selected":"").">".$rorg2['namakaryawan']."</option>";
////        }
////        else{
////            $optafd2.="<option value='".$rorg2['karyawanid']."'>".$rorg2['namakaryawan']."</option>";
////        }
////    }
//    echo $optafd."####".$optafd2;
//    break;
    
}
?>