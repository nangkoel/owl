<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$param=$_GET;
$bgcoloraja="bgcolor=#DEDEDE align=center";
$brdr=1;
$arrTipe=array("PNN"=>"Panen","TM"=>"Perawatan TM","TMINVK"=>"Pemakaian_Brg_TM","TBM"=>"Perawatan_TBM","TBMINVK"=>"Pemakaian_Brg_TBM","BBT"=>"Bibitan","BBTINVK"=>"Pemakaian_Brg_Bibitan","1"=>"Penerimaan Gudang","3"=>"Penerimaan Mutasi","7"=>"Pengeluaran Mutasi","5"=>"Pengeluaran Barang","B"=>"Bank","K"=>"Kas Bank","VP"=>"Voucher Payable");

switch($param['tpId']){
	case'PNN':
	 $sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,sum(jumlah) as totaldebet from ".$dbname.".keu_jurnaldt where 
				  noreferensi in (select distinct notransaksi from ".$dbname.".kebun_aktifitas where tipetransaksi='".$param['tpId']."' and tanggal like '".$param['periodeDt']."%' and jurnal=1) and tanggal like '".$param['periodeDt']."%' and nojurnal not like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') and noakun='2130100'
				  group by substr(nojurnal,10,4),noreferensi";
		/* $sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,sum(jumlah*-1) as totaldebet from ".$dbname.".keu_jurnaldt where noreferensi in (select distinct notransaksi from ".$dbname.".kebun_aktifitas where tipetransaksi='".$param['tpId']."' and tanggal like '".$param['periodeDt']."%' and jurnal=1) and tanggal like '".$param['periodeDt']."%' and nojurnal not like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') and jumlah<0 group by substr(nojurnal,10,4),noreferensi"; */
		$sTrans="SELECT unit,notransaksi,jurnal,sum(upahkerja+upahpremi) as jumlah
                 FROM ".$dbname.".`kebun_prestasi_vw` where tanggal like '".$param['periodeDt']."%' and jurnal=1 
				 and unit in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')
				 group by unit,notransaksi";
	break;
	case'BBT':
	$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,sum(jumlah) as totaldebet from ".$dbname.".keu_jurnaldt where 
		          noreferensi in (select distinct notransaksi from ".$dbname.".kebun_aktifitas where tipetransaksi='".$param['tpId']."' and tanggal like '".$param['periodeDt']."%' and jurnal=1) and tanggal like '".$param['periodeDt']."%' and nojurnal not like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') and noakun='2130100'
		          group by substr(nojurnal,10,4),noreferensi";
	/* $sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,totaldebet from ".$dbname.".keu_jurnalht where noreferensi in (select distinct    notransaksi from ".$dbname.".kebun_aktifitas where tipetransaksi='".$param['tpId']."' and tanggal like '".$param['periodeDt']."%' and jurnal=1) and tanggal like '".$param['periodeDt']."%' and nojurnal not like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')"; */
		$sTrans="SELECT unit,notransaksi,jurnal,sum(umr+insentif) as jumlah
                 FROM ".$dbname.".`kebun_kehadiran_vw` where notransaksi like '%BBT%' and jurnal=1 and tanggal like '".$param['periodeDt']."%' 
				 and unit in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')
				 group by unit,notransaksi";
				 
	break;
	case'BBTINVK':
	$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,sum(jumlah) as totaldebet from ".$dbname.".keu_jurnaldt where noreferensi in (select distinct    notransaksi from ".$dbname.".kebun_aktifitas where tipetransaksi='BBT' and tanggal like '".$param['periodeDt']."%' and jurnal=1) and tanggal like '".$param['periodeDt']."%' and nojurnal like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')  and jumlah>0 group by substr(nojurnal,10,4),noreferensi";
		$sTrans="SELECT left(a.kodeorg,4) as unit,a.notransaksi as notransaksi,jurnal,sum(hargasatuan*kwantitas) as jumlah
                 FROM ".$dbname.".`kebun_pakaimaterial` a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi
				 where b.tipetransaksi='BBT' and jurnal=1 and tanggal like '".$param['periodeDt']."%' 
				 and left(a.kodeorg,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')
				 group by left(a.kodeorg,4),a.notransaksi";
				 
	break;
	case'TBM':
	 $sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,sum(jumlah) as totaldebet from ".$dbname.".keu_jurnaldt where 
		          noreferensi in (select distinct notransaksi from ".$dbname.".kebun_aktifitas where tipetransaksi='".$param['tpId']."' and tanggal like '".$param['periodeDt']."%' and jurnal=1) and tanggal like '".$param['periodeDt']."%' and nojurnal not like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') and noakun='2130100'
		          group by substr(nojurnal,10,4),noreferensi";
	/* $sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,totaldebet from ".$dbname.".keu_jurnalht where noreferensi in (select distinct    notransaksi from ".$dbname.".kebun_aktifitas where tipetransaksi='".$param['tpId']."' and tanggal like '".$param['periodeDt']."%' and jurnal=1) and tanggal like '".$param['periodeDt']."%' and nojurnal not like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')"; */
		$sTrans="SELECT unit,notransaksi,jurnal,sum(umr+insentif) as jumlah
                 FROM ".$dbname.".`kebun_kehadiran_vw` where notransaksi like '%TBM%' and jurnal=1 and tanggal like '".$param['periodeDt']."%' 
				 and unit in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')
				 group by unit,notransaksi";
	break;
	case'TBMINVK':
	$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,sum(jumlah) as totaldebet from ".$dbname.".keu_jurnaldt where noreferensi in (select distinct    notransaksi from ".$dbname.".kebun_aktifitas where tipetransaksi='TBM' and tanggal like '".$param['periodeDt']."%' and jurnal=1) and tanggal like '".$param['periodeDt']."%' and nojurnal like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')  and jumlah>0 group by substr(nojurnal,10,4),noreferensi";
		$sTrans="SELECT left(a.kodeorg,4) as unit,a.notransaksi as notransaksi,jurnal,sum(hargasatuan*kwantitas) as jumlah
                 FROM ".$dbname.".`kebun_pakaimaterial` a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi
				 where b.tipetransaksi='TBM' and jurnal=1 and tanggal like '".$param['periodeDt']."%' 
				 and left(a.kodeorg,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')
				 group by left(a.kodeorg,4),a.notransaksi";
	break;
	case'TM':
		$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,sum(jumlah) as totaldebet from ".$dbname.".keu_jurnaldt where 
		          noreferensi in (select distinct notransaksi from ".$dbname.".kebun_aktifitas where tipetransaksi='".$param['tpId']."' and tanggal like '".$param['periodeDt']."%' and jurnal=1) and tanggal like '".$param['periodeDt']."%' and nojurnal not like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') and noakun='2130100'
		          group by substr(nojurnal,10,4),noreferensi";
		$sTrans="SELECT unit,notransaksi,jurnal,sum(umr+insentif) as jumlah
                 FROM ".$dbname.".`kebun_kehadiran_vw` where  notransaksi like '%TM%' and  jurnal=1 and tanggal like '".$param['periodeDt']."%' 
				 and unit in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')
				 group by unit,notransaksi";
	break;
	case'TMINVK':
	$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,sum(jumlah) as totaldebet from ".$dbname.".keu_jurnaldt where noreferensi in (select distinct    notransaksi from ".$dbname.".kebun_aktifitas where tipetransaksi='TM' and tanggal like '".$param['periodeDt']."%' and jurnal=1) and tanggal like '".$param['periodeDt']."%' and nojurnal like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') and jumlah>0 
	group by substr(nojurnal,10,4),noreferensi";
		$sTrans="SELECT left(a.kodeorg,4) as unit,a.notransaksi as notransaksi,jurnal,sum(hargasatuan*kwantitas) as jumlah
                 FROM ".$dbname.".`kebun_pakaimaterial` a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi
				 where b.tipetransaksi='TM' and jurnal=1 and tanggal like '".$param['periodeDt']."%' 
				 and left(a.kodeorg,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')
				 group by left(a.kodeorg,4),a.notransaksi";
	break;
	case'K':
	$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,totaldebet from ".$dbname.".keu_jurnalht where noreferensi in (select distinct    a.notransaksi from ".$dbname.".keu_kasbankdt a left join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi where 
		left(kode,1)='".$param['tpId']."' and tanggalposting like '".$param['periodeDt']."%' and posting=1) and tanggal like '".$param['periodeDt']."%' and nojurnal not like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')";
	$sTrans="SELECT a.kodeorg as unit,a.notransaksi,a.posting as jurnal,(a.jumlah*b.kurs) as jumlah
            FROM ".$dbname.".keu_kasbankht a left join ".$dbname.".`keu_kasbankdt` b on a.notransaksi=b.notransaksi where a.posting=1 and 
			tanggalposting like '".$param['periodeDt']."%' and a.kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') and left(kode,1)='".$param['tpId']."'  
			group by a.kodeorg,notransaksi";
		/*$sTrans="SELECT a.kodeorg as unit,a.notransaksi,b.posting as jurnal,sum(a.jumlah*a.kurs) as jumlah
                 FROM ".$dbname.".`keu_kasbankdt` a left join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi where b.posting=1 and 
				 tanggalposting like '".$param['periodeDt']."%' and a.kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') and left(kode,1)='".$param['tpId']."' and a.jumlah>0
				 group by a.kodeorg,notransaksi";*/
	break;
	case'B':
		$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,totaldebet from ".$dbname.".keu_jurnalht where noreferensi in (select distinct    a.notransaksi from ".$dbname.".keu_kasbankdt a left join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi where 
		left(kode,1)='".$param['tpId']."' and tanggalposting like '".$param['periodeDt']."%' and posting=1) and tanggal like '".$param['periodeDt']."%' and nojurnal not like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')";
		$sTrans="SELECT a.kodeorg as unit,a.notransaksi,b.posting as jurnal,sum(a.jumlah*a.kurs) as jumlah
                 FROM ".$dbname.".`keu_kasbankdt` a left join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi where b.posting=1 and 
				 tanggalposting like '".$param['periodeDt']."%' and a.kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') and left(kode,1)='".$param['tpId']."'
				 group by a.kodeorg,notransaksi";
	break;
	case'VP':
		$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,totaldebet from ".$dbname.".keu_jurnalht where noreferensi in (select distinct    novp from ".$dbname.".keu_vpht where kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') and tanggal like '".$param['periodeDt']."%' and posting=1 and updateby!='0000000000') and tanggal like '".$param['periodeDt']."%' and nojurnal not like '%INVK%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')";
		$sTrans="SELECT b.kodeorg as unit,a.novp as notransaksi,b.posting as jurnal,((sum(a.jumlah)*a.kurs)*-1) as jumlah
                 FROM ".$dbname.".`keu_vpdt` a left join ".$dbname.".keu_vpht b on a.novp=b.novp where b.posting=1 and 
				 tanggal like '".$param['periodeDt']."%' and b.kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')
				 and a.jumlah<0 and updateby!='0000000000'
				 group by b.kodeorg,a.novp";
	break;
	case'1':
	$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,totaldebet from ".$dbname.".keu_jurnalht where 
		          noreferensi in (select distinct notransaksi from ".$dbname.".log_transaksiht where tipetransaksi='".$param['tpId']."' and statusjurnal=1 and tanggal like '".$param['periodeDt']."%') and tanggal like '".$param['periodeDt']."%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') order by noreferensi,totaldebet asc";
		$sTrans="SELECT kodegudang as unit,notransaksi,sum(hartot) as jumlah,statusjurnal as jurnal
                 FROM ".$dbname.".`log_transaksi_vw` where statusjurnal=1 
				 and left(kodegudang,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')
                 and tanggal like '".$param['periodeDt']."%'  and tipetransaksi=".$param['tpId']." group by kodegudang,notransaksi,kodebarang order by notransaksi,sum(hartot)  asc";
	break;
	case'3':
	$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,totaldebet from ".$dbname.".keu_jurnalht where 
		          noreferensi in (select distinct notransaksi from ".$dbname.".log_transaksiht where tipetransaksi='".$param['tpId']."' and statusjurnal=1 and tanggal like '".$param['periodeDt']."%') and tanggal like '".$param['periodeDt']."%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') order by  noreferensi,totaldebet  asc";
		$sTrans="SELECT kodegudang as unit,notransaksi,sum(hartot) as jumlah,statusjurnal as jurnal
                 FROM ".$dbname.".`log_transaksi_vw` where statusjurnal=1 
				 and left(kodegudang,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')
                 and tanggal like '".$param['periodeDt']."%'  and tipetransaksi=".$param['tpId']." group by kodegudang,notransaksi,kodebarang order by notransaksi,sum(hartot) asc";
    break;
	case'5':
	$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,sum(jumlah*-1) as totaldebet from ".$dbname.".keu_jurnaldt_vw where 
		          noreferensi in (select distinct notransaksi from ".$dbname.".log_transaksi_vw where tipetransaksi='".$param['tpId']."' and statusjurnal=1 and tanggal like '".$param['periodeDt']."%' and notransaksireferensi is null) and tanggal like '".$param['periodeDt']."%' and kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') and left(noakun,3)='115' and jumlah<0 group by kodeorg,noreferensi,kodebarang  order by noreferensi,sum(jumlah*-1) asc";
		$sTrans="SELECT left(kodegudang,4) as unit,notransaksi,sum(hartot) as jumlah,statusjurnal as jurnal
                 FROM ".$dbname.".`log_transaksi_vw` where statusjurnal=1 
				 and substr(notransaksi,16,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')
                 and tanggal like '".$param['periodeDt']."%'  and notransaksireferensi is null and tipetransaksi=".$param['tpId']." group by substr(notransaksi,16,4),notransaksi,kodebarang order by substr(notransaksi,16,4),notransaksi,sum(hartot) asc";
	break;
	case'7':
		$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,totaldebet from ".$dbname.".keu_jurnalht where 
		          noreferensi in (select distinct notransaksi from ".$dbname.".log_transaksiht where tipetransaksi='".$param['tpId']."' and statusjurnal=1 and tanggal like '".$param['periodeDt']."%') and tanggal like '".$param['periodeDt']."%' and substr(nojurnal,10,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."') order by noreferensi,totaldebet asc";
		$sTrans="SELECT kodegudang as unit,notransaksi,sum(hartot) as jumlah,statusjurnal as jurnal
                 FROM ".$dbname.".`log_transaksi_vw` where statusjurnal=1 
				 and left(kodegudang,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."')
                 and tanggal like '".$param['periodeDt']."%'  and tipetransaksi=".$param['tpId']." group by kodegudang,notransaksi,kodebarang order by notransaksi,sum(hartot) asc";
	break;

}
//exit("error:".$sJurnal);
if((strlen($param['tpId'])>1)||($param['tpId']=='B')||($param['tpId']=='K')||($param['tpId']=='VP')){
	$qJurnal=mysql_query($sJurnal) or die(mysql_error($conn));
	while($rJurnal=mysql_fetch_assoc($qJurnal)){	
		$lstNoreferensi[$rJurnal['noreferensi']]=$rJurnal['noreferensi'];
		$lstUnit[$rJurnal['unit']]=$rJurnal['unit'];
		$dtNotrans[$rJurnal['unit'].$rJurnal['noreferensi']]=$rJurnal['noreferensi'];
		$dtJurnal[$rJurnal['noreferensi']]=$rJurnal['nojurnal'];
		if($rJurnal['totaldebet']<0){
			$rJurnal['totaldebet']=$rJurnal['totaldebet']*-1;
		}
		$dtJumlah[$rJurnal['noreferensi']]+=$rJurnal['totaldebet'];
	}

	$qTrans=mysql_query($sTrans) or die(mysql_error($conn));
	while($rTrans=mysql_fetch_assoc($qTrans)){
		$lstNoreferensi[$rTrans['notransaksi']]=$rTrans['notransaksi'];
		$lstUnit[$rTrans['unit']]=$rTrans['unit'];
		$dtNotrans[$rTrans['unit'].$rTrans['notransaksi']]=$rTrans['notransaksi'];
		$dtTrans[$rTrans['unit'].$rTrans['notransaksi']]=$rTrans['notransaksi'];
		$dtRupiah[$rTrans['notransaksi']]+=$rTrans['jumlah'];
		$dtStat[$rTrans['notransaksi']]+=$rTrans['jurnal'];
	}
}else{
	//exit("error:masuk sini pengadaan");
	$qJurnal=mysql_query($sJurnal) or die(mysql_error($conn));
	while($rJurnal=mysql_fetch_assoc($qJurnal)){	
		if($rJurnal['noreferensi']!=$tempNo){
			$tempNo=$rJurnal['noreferensi'];
			$totRow[$rJurnal['noreferensi']]=1;
			$rowDt=1;
		}else{
			$totRow[$rJurnal['noreferensi']]+=1;
			$rowDt+=1;
		}
		$lstNoreferensi[$rJurnal['noreferensi']]=$rJurnal['noreferensi'];
		$lstUnit[$rJurnal['unit']]=$rJurnal['unit'];
		$dtNotrans[$rJurnal['unit'].$rJurnal['noreferensi']]=$rJurnal['noreferensi'];
		$dtJurnal[$rJurnal['noreferensi'].$rowDt]=$rJurnal['nojurnal'];
		if($rJurnal['totaldebet']<0){
			$rJurnal['totaldebet']=$rJurnal['totaldebet']*-1;
		}
		$dtJumlah[$rJurnal['noreferensi'].$rowDt]=$rJurnal['totaldebet'];
	}

	$qTrans=mysql_query($sTrans) or die(mysql_error($conn));
	while($rTrans=mysql_fetch_assoc($qTrans)){
		if($rTrans['notransaksi']!=$tempNo){
			$tempNo=$rTrans['notransaksi'];
			$totRow2[$rTrans['notransaksi']]=1;
			$rowDt2=1;
		}else{
			$totRow2[$rTrans['notransaksi']]+=1;
			$rowDt2+=1;
		}
		$rTrans['unit']=substr($rTrans['unit'],0,4);
		$lstNoreferensi[$rTrans['notransaksi']]=$rTrans['notransaksi'];
		$lstUnit[$rTrans['unit']]=$rTrans['unit'];
		$dtNotrans[$rTrans['unit'].$rTrans['notransaksi']]=$rTrans['notransaksi'];
		$dtTrans[$rTrans['unit'].$rTrans['notransaksi']]=$rTrans['notransaksi'];
		$dtRupiah[$rTrans['notransaksi'].$rowDt2]=$rTrans['jumlah'];
		$dtStat[$rTrans['notransaksi'].$rowDt2]=$rTrans['jurnal'];
	}
}
 

switch($param['proses']){
        case'excel':
			if((strlen($param['tpId'])>1)||($param['tpId']=='B')||($param['tpId']=='K')){
					foreach($lstUnit as $dtUnit){
						$whrt="kodeorganisasi='".$dtUnit."'";
					    $optOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whrt);
						$tab.="<table cellpadding=1 cellspacing=1 border=1 class=sortable>
							   <thead>";
						$tab.="<tr ".$bgcoloraja.">";
						$tab.="<td colspan=6>".$dtUnit."-".$optOrg[$dtUnit]."</td></tr>";
						$tab.="<tr ".$bgcoloraja."><td>".$_SESSION['lang']['nojurnal']."</td>";
						$tab.="<td>".$_SESSION['lang']['noreferensi']."</td>";
						$tab.="<td>".$_SESSION['lang']['jumlah']."</td>";
						$tab.="<td>".$_SESSION['lang']['notransaksi']."</td>";
						$tab.="<td>".$_SESSION['lang']['status']."</td>
							   <td>".$_SESSION['lang']['jumlah']."</td>
							   </tr></thead><tbody id=dataIsi>";
								foreach($lstNoreferensi as $notransaksi){
									$notrans=$dtNotrans[$dtUnit.$notransaksi];
									if($notrans!=''){
										$tab.="<tr>";
										$tab.="<td>".$dtJurnal[$notrans]."</td>";
										$tab.="<td>".$notrans."</td>";
										$tab.="<td align=right>".number_format($dtJumlah[$notrans],2)."</td>";
										$tab.="<td>".$notrans."</td>";
										$tab.="<td>".$dtStat[$notrans]."</td>";
										$tab.="<td align=right>".number_format($dtRupiah[$notrans],2)."</td>";
										$tab.="</tr>";
										if($dtJurnal[$notrans]!=''){
											$itungJurnal[$dtUnit]+=1;
											$rpJurnal[$dtUnit]+=$dtJumlah[$notrans];
										}
										if($dtTrans[$dtUnit.$notransaksi]!=''){
											$itungTransaksi[$dtUnit]+=1;
											$rpTransaksi[$dtUnit]+=$dtRupiah[$notrans];
										}
										
									}
									
								}
						$tab.="<tr>";
						$tab.="<td>Jumlah Data Jurnal</td><td align=right>".number_format($itungJurnal[$dtUnit],0)."</td><td align=right>".number_format($rpJurnal[$dtUnit],2)."</td>";
						$tab.="<td>Jumlah Data Transaksi</td><td align=right>".number_format($itungTransaksi[$dtUnit],0)."</td><td align=right>".number_format($rpTransaksi[$dtUnit],2)."</td></tr>";
						$tab.="</tbody></table>";
					}
			}else{
			foreach($lstUnit as $dtUnit){
					$whrt="kodeorganisasi='".$dtUnit."'";
					$optOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whrt);
						$tab.="<table cellpadding=1 cellspacing=1 border=1 class=sortable>
							   <thead>";
						$tab.="<tr ".$bgcoloraja.">";
						$tab.="<td colspan=6>".$dtUnit."-".$optOrg[$dtUnit]."</td></tr>";
						$tab.="<tr ".$bgcoloraja."><td>".$_SESSION['lang']['nojurnal']."</td>";
						$tab.="<td>".$_SESSION['lang']['noreferensi']."</td>";
						$tab.="<td>".$_SESSION['lang']['jumlah']."</td>";
						$tab.="<td>".$_SESSION['lang']['notransaksi']."</td>";
						$tab.="<td>".$_SESSION['lang']['status']."</td>
							   <td>".$_SESSION['lang']['jumlah']."</td>
							   </tr></thead><tbody id=dataIsi>";
								foreach($lstNoreferensi as $notransaksi){
									$notrans=$dtNotrans[$dtUnit.$notransaksi];
									$totRowDt[$notrans]=$totRow[$notrans];
									if($totRow[$notrans]>$totRow2[$notrans]){
										$totRowDt[$notrans]=$totRow[$notrans];
									}elseif($totRow[$notrans]<$totRow2[$notrans]){
										$totRowDt[$notrans]=$totRow2[$notrans];
									}
									if($notrans!=''){
									for($as=1;$as<=$totRowDt[$notrans];$as++){
										$tab.="<tr>";
										$tab.="<td>".$dtJurnal[$notrans.$as]."</td>";
										$tab.="<td>".$notrans."</td>";
										$tab.="<td align=right>".number_format($dtJumlah[$notrans.$as],2)."</td>";
										$tab.="<td>".$notrans."</td>";
										$tab.="<td>".$dtStat[$notrans.$as]."</td>";
										$tab.="<td align=right>".number_format($dtRupiah[$notrans.$as],2)."</td>";
										$tab.="</tr>";
										if($dtJurnal[$notrans.$as]!=''){
											$itungJurnal[$dtUnit]+=1;
											$rpJurnal[$dtUnit]+=$dtJumlah[$notrans.$as];
										}
										if($dtTrans[$dtUnit.$notrans]!=''){
											$itungTransaksi[$dtUnit]+=1;
											$rpTransaksi[$dtUnit]+=$dtRupiah[$notrans.$as];
										}
										
									}
									}
									
								}
						$tab.="<tr>";
						$tab.="<td>Jumlah Data Jurnal</td><td align=right>".number_format($itungJurnal[$dtUnit],0)."</td><td align=right>".number_format($rpJurnal[$dtUnit],2)."</td>";
						$tab.="<td>Jumlah Data Transaksi</td><td align=right>".number_format($itungTransaksi[$dtUnit],0)."</td><td align=right>".number_format($rpTransaksi[$dtUnit],2)."</td></tr>";
						$tab.="</tbody></table>";
					}
				
			}
            
            $wktu=date("Hms");
		    $nop_="jurnal_dt_".$arrTipe[$param['tpId']]."__".$param['periodeDt']."__".$wktu;
			if(strlen($tab)>0)
			{
				 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
				 gzwrite($gztralala, $tab);
				 gzclose($gztralala);
				 echo "<script language=javascript1.2>
					window.location='tempExcel/".$nop_.".xls.gz';
					</script>";
			} 
        break;       
}
?>