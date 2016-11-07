<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
?>

<?php

$param = $_POST;
$proses = $_POST['proses'];
 
switch($proses) {
    # Daftar Header
    case 'loadData':
        $bloklama = makeOption($dbname,'setup_blok','kodeorg,bloklama',"kodeorg like '".$_SESSION['empl']['lokasitugas']."%'");
	$where = "afdeling in (select distinct kodeorganisasi from ".$dbname.".organisasi where tipe='AFDELING' and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%')";
        
	$tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%><thead><tr align=center>";
//        $tab.="<td>".$_SESSION['lang']['mandor']."</td>";
        $tab.="<td>".$_SESSION['lang']['afdeling']."</td>";
        $tab.="<td>".$_SESSION['lang']['tanggal']."</td>";
        $tab.="<td>".$_SESSION['lang']['blok']."</td>";
        $tab.="<td>".$_SESSION['lang']['bloklama']."</td>";
        $tab.="<td>".$_SESSION['lang']['section']."</td>";
        $tab.="<td>".$_SESSION['lang']['hasisa']."</td>";
        $tab.="<td>".$_SESSION['lang']['haesok']."</td>";
        $tab.="<td>".$_SESSION['lang']['jmlhpokok']."</td>";
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
            $tab.="<td>".$rdata['afdeling']."</td>";
            $tab.="<td>".tanggalnormal($rdata['tanggal'])."</td>";
            $tab.="<td>".$rdata['blok']."</td>";
            $tab.="<td>".$bloklama[$rdata['blok']]."</td>";
            $tab.="<td>".$rdata['seksi']."</td>";
            $tab.="<td align=right>".$rdata['hasisa']."</td>";
            $tab.="<td align=right>".$rdata['haesok']."</td>";
            $tab.="<td align=right>".$rdata['jmlhpokok']."</td>";
            $tab.="<td><img title=\"Edit\" onclick=\"showEdit('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."','".$rdata['blok']."')\" class=\"zImgBtn\" src=\"images/skyblue/edit.png\"></td>";
            $tab.="<td><img title=\"Delete\" onclick=\"deleteData('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."','".$rdata['blok']."')\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\"></td>";
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
	$where = "afdeling in (select distinct kodeorganisasi from ".$dbname.".organisasi where tipe='AFDELING')";
        if($param['sNoTrans']!=''){
            $tgl=explode("-",$param['sNoTrans']);
            $param['tanggal']=$tgl[2]."-".$tgl[1]."-".$tgl[0];
            $where.=" and tanggal like '%".$param['tanggal']."%'";
        }
	$tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%><thead><tr align=center>";
        $tab.="<td>".$_SESSION['lang']['afdeling']."</td>";
        $tab.="<td>".$_SESSION['lang']['tanggal']."</td>";
        $tab.="<td>".$_SESSION['lang']['blok']."</td>";
        $tab.="<td>".$_SESSION['lang']['section']."</td>";
        $tab.="<td>".$_SESSION['lang']['hasisa']."</td>";
        $tab.="<td>".$_SESSION['lang']['haesok']."</td>";
        $tab.="<td>".$_SESSION['lang']['jmlhpokok']."</td>";
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
            $tab.="<td>".$rdata['afdeling']."</td>";
            $tab.="<td>".tanggalnormal($rdata['tanggal'])."</td>";
            $tab.="<td>".$rdata['blok']."</td>";
            $tab.="<td>".$rdata['seksi']."</td>";
            $tab.="<td align=right>".$rdata['hasisa']."</td>";
            $tab.="<td align=right>".$rdata['haesok']."</td>";
            $tab.="<td align=right>".$rdata['jmlhpokok']."</td>";
            $tab.="<td><img title=\"Edit\" onclick=\"showEdit('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."','".$rdata['karyawanid']."')\" class=\"zImgBtn\" src=\"images/skyblue/edit.png\"></td>";
            $tab.="<td><img title=\"Delete\" onclick=\"deleteData('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."','".$rdata['karyawanid']."')\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\"></td>";
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
       #var ek//$arr="##tanggal##afdeling##blok##seksi##proses##hasisa##haesok##jmlhpokok##persenbuahmatang##jjgmasak##jjgoutput##hkdigunakan##bjr";
       $param['hasisa']==''?$param['hasisa']=0:$param['hasisa']=$param['hasisa'];
       $param['haesok']==''?$param['haesok']=0:$param['haesok']=$param['haesok'];
       $param['jmlhpokok']==''?$param['jmlhpokok']=0:$param['jmlhpokok']=$param['jmlhpokok'];
       $param['persenbuahmatang']==''?$param['persenbuahmatang']=0:$param['persenbuahmatang']=$param['persenbuahmatang'];
       $param['jjgmasak']==''?$param['jjgmasak']=0:$param['jjgmasak']=$param['jjgmasak'];
       $param['jjgoutput']==''?$param['jjgoutput']=0:$param['jjgoutput']=$param['jjgoutput'];
       $param['hkdigunakan']==''?$param['hkdigunakan']=0:$param['hkdigunakan']=$param['hkdigunakan'];
       $param['bjr']==''?$param['bjr']=0:$param['bjr']=$param['bjr'];
       
       #end var
       
       $tgl=explode("-",$param['tanggal']);
       $param['tanggal']=$tgl[2]."-".$tgl[1]."-".$tgl[0];
       
       $scek2="select distinct * from ".$dbname.".kebun_taksasi where tanggal='".$param['tanggal']."' and afdeling='".$param['afdeling']."' and blok='".$param['blok']."'";
       $qcek2=mysql_query($scek2) or die(mysql_error($conn));
       $rcek2=mysql_num_rows($qcek2);
       if($rcek2!=0){
//            exit("error: Data sudah pernah diinput.");
           
            $sins="update ".$dbname.".kebun_taksasi  set `seksi`='".$param['seksi']."',
            `hasisa`='".$param['hasisa']."', `haesok`='".$param['haesok']."', `jmlhpokok`='".$param['jmlhpokok']."', 
            `persenbuahmatang`='".$param['persenbuahmatang']."',`jjgmasak`='".$param['jjgmasak']."', `jjgoutput`='".$param['jjgoutput']."', 
            `hkdigunakan`='".$param['hkdigunakan']."', `bjr`='".$param['bjr']."'   
             where tanggal='".$param['tanggal']."' and afdeling='".$param['afdeling']."' and blok='".$param['blok']."'";
            if(!mysql_query($sins)){
            exit("error:".mysql_error($conn)."__".$sins);
            }
       }else{
            $scek="select distinct * from ".$dbname.".kebun_taksasi 
              where tanggal='".$param['tanggal']."' and afdeling='".$param['afdeling']."' and blok='".$param['blok']."'";
            //exit("error:".$scek);
            $qcek=mysql_query($scek) or die(mysql_error($conn));
            $rcek=mysql_num_rows($qcek);
            if($rcek!=0){
            exit("error:Data Sudah Ada");
            }
            $sins="insert into ".$dbname.".kebun_taksasi  
            (`afdeling`,`tanggal`, `blok`, `seksi`, `hasisa`, `haesok`, `jmlhpokok`, `persenbuahmatang`, `jjgmasak`, `jjgoutput`, `hkdigunakan`, `bjr`)
            values ('".$param['afdeling']."','".$param['tanggal']."','".$param['blok']."','".$param['seksi']."','".$param['hasisa']."','".$param['haesok']."','".$param['jmlhpokok']."','".$param['persenbuahmatang']."','".$param['jjgmasak']."','".$param['jjgoutput']."','".$param['hkdigunakan']."','".$param['bjr']."')";
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
          afdeling='".$param['afdeling']."' and blok ='".$param['blok']."'";
   //exit("error:".$str);
   $qstr=mysql_query($str) or die(mysql_error($conn));
   $rts=mysql_fetch_assoc($qstr);
   
   echo $rts['afdeling']."###".tanggalnormal($rts['tanggal'])."###".$rts['blok']."###".$rts['seksi']."###".$rts['hasisa']."###".$rts['haesok']."###".$rts['jmlhpokok']."###".$rts['persenbuahmatang']."###"
   .$rts['jjgmasak']."###".$rts['jjgoutput']."###".$rts['hkdigunakan']."###".$rts['bjr']."###".$rts['karyawanid']."###".substr($rts['afdeling'],0,4)."###".$rts['blok'];
   break;
   
   
   
   
    case 'delete': 
    $tgl=explode("-",$param['tanggal']);
    $param['tanggal']=$tgl[2]."-".$tgl[1]."-".$tgl[0];
	$where = "tanggal='".$param['tanggal']."' and afdeling='".$param['afdeling']."'  and blok='".$param['blok']."'";
	$query = "delete from `".$dbname."`.`kebun_taksasi` where ".$where;
        //exit("error:".$query);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
    break;
    case'getAfd':
	$bloklama=makeOption($dbname,'setup_blok','kodeorg,bloklama');
        $optafd="";
		$sorg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='AFDELING' and induk='".$param['kebun']."'";
		//echo $sorg;
		//exit("error:".$sorg);
		$qorg=mysql_query($sorg) or die(mysql_error($conn));
		while($rorg=mysql_fetch_assoc($qorg)){
			if(!empty($param['afdeling'])){
				$optafd.="<option value='".$rorg['kodeorganisasi']."' ".($param['afdeling']==$rorg['kodeorganisasi']?"selected":"").">".$rorg['namaorganisasi']."</option>";
			}
			else{
				$optafd.="<option value='".$rorg['kodeorganisasi']."'>".$rorg['namaorganisasi']."</option>";
			}
		}
		
		
		
		$sorg2="select distinct kodeorganisasi,namaalias from ".$dbname.".organisasi 
				where tipe='BLOK' and kodeorganisasi like '".$param['kebun']."%' 
				and kodeorganisasi in (select distinct kodeorg from ".$dbname.".setup_blok where left(kodeorg,4)='".$param['kebun']."' and luasareaproduktif!=0)";
		$qorg2=mysql_query($sorg2) or die(mysql_error($conn));
		$optafd2 = '';$i=0;
		while($rorg2=mysql_fetch_assoc($qorg2)){
			if(!empty($param['blok'])){
				$optafd2.="<option value='".$rorg2['kodeorganisasi']."' ".($param['blok']==$rorg2['kodeorganisasi']?"selected":"").">".$rorg2['namaalias']." [".$bloklama[$rorg2['kodeorganisasi']]."]</option>";
			}
			else{//
				$optafd2.="<option value='".$rorg2['kodeorganisasi']."'>".$rorg2['namaalias']." [".$bloklama[$rorg2['kodeorganisasi']]."]</option>";
			}
			if($i==0) {$firstBlok = $rorg2['kodeorganisasi'];}
			$i++;
		}
		
		if(!empty($param['blok'])){
			$qBlok = selectQuery($dbname,'setup_blok','jumlahpokok,luasareaproduktif',"kodeorg='".$param['blok']."'");
		} else {
			$qBlok = selectQuery($dbname,'setup_blok','jumlahpokok,luasareaproduktif',"kodeorg='".$firstBlok."'");
		}
		$resBlok = fetchData($qBlok);
		if(empty($resBlok)) {
			$sph = '0.00';
		} else {
			$sph = number_format($resBlok[0]['jumlahpokok']/$resBlok[0]['luasareaproduktif'],2);
		}
		
		echo $optafd."####".$optafd2."####".$sph;
    break;
	case 'getSph':
		$qBlok = selectQuery($dbname,'setup_blok','jumlahpokok,luasareaproduktif',"kodeorg='".$param['blok']."'");
		$resBlok = fetchData($qBlok);
		if(empty($resBlok)) {
			$sph = '0.00';
		} else {
			$sph = number_format($resBlok[0]['jumlahpokok']/$resBlok[0]['luasareaproduktif'],2);
		}
		echo $sph;
		break;
}
?>