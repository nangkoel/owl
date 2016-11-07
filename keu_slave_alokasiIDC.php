<?php
// file creator: dhyaz sep 20, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

switch ($_POST[aksi]){
    case 'ambilnokas':
//        $str="select nojurnal as notransaksi,'".$_SESSION['empl']['lokasitugas']."' as kodeorg,totaldebet as jumlah from ".$dbname.".keu_jurnalht where tanggal=".tanggalsystem($_POST['tanggal'])." and nojurnal like '%/".$_SESSION['empl']['lokasitugas']."/M%'";
        $str="select nojurnal as notransaksi,'".$_SESSION['empl']['lokasitugas']."' as kodeorg,sum(jumlah) as jumlah from ".$dbname.".keu_jurnaldt_vw where tanggal=".tanggalsystem($_POST['tanggal'])." and nojurnal like '%/".$_SESSION['empl']['lokasitugas']."/M%' and jumlah > 0 group by nojurnal";
        $res=mysql_query($str);
        $opt=($_SESSION['language']=='EN')?"<option value=''>Choose..</option>":"<option value=''>Pilih....</option>";
        while($bar= mysql_fetch_object($res))
        {
            $opt.="<option value='".$bar->notransaksi."#".$bar->jumlah."#".$bar->kodeorg."'>".$bar->kodeorg.": ".$bar->notransaksi." jumlah ".number_format($bar->jumlah)."</option>";
        }
        echo $opt;
        break;
    case 'ambilAlokasi':
        $ambilInduk="select induk from ".$dbname.".organisasi where kodeorganisasi='".$_POST['kodeorg']."'";
        $res=mysql_query($ambilInduk);
        $induk='';
        while($bar=mysql_fetch_object($res))
        {
            $induk=$bar->induk;
        }
        $str="select distinct left(a.kodeorg,4) as kebun from ".$dbname.".setup_blok a
                  left join ".$dbname.".organisasi b on a.kodeorg=b.kodeorganisasi
                  where a.statusblok in('TB','TBM','LC','TBM1','TBM2','TBM3','TM')
                  and left(b.induk,4) in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$induk."')"; 
          $res=mysql_query($str);
        $opt="<option value=''>Choose....</option>";
        while($bar= mysql_fetch_object($res))
        {
            $opt.="<option value='".$bar->kebun."'>".$bar->kebun."</option>";
        }
        echo $opt;      
        break;
    case 'ambilBlok':
	
	
        #periksa tutup buku
        $tg=substr($_POST['tanggal'],6,4)."-".substr($_POST['tanggal'],3,2);
        $str="select * from ".$dbname.".setup_periodeakuntansi where kodeorg='".$_POST['kodeorg']."' and periode='".$tg."' and tutupbuku=0";
        $res=mysql_query($str);
        if(mysql_num_rows($res)<1)
        {
            exit(" Error: Transaction period is closed");
        }
        #periksa apakah  sudah pernah dialokasi
#        $nojurnal=  tanggalsystem($_POST['tanggal'])."/".$_POST['kodeorg']."/IDC/001";
#        $str="select * from ".$dbname.".keu_jurnalht where nojurnal='".$nojurnal."'";
#        $res=mysql_query($str);
#        if(mysql_num_rows($res)>0)
#        {
#               exit(" Error: IDC has been allocated before (".$_POST['kodeorg'].")");
#        }        
          #ambil noakun
        
        if($_SESSION['language']=='EN'){
            $optAkun="<option value=''>Choose..</option>";
            $str="select noakun,namaakun1 as namaakun from ".$dbname.".keu_5akun where detail=1 order by noakun";
        }else{
            $optAkun="<option value=''>Pilih..</option>";
            $str="select noakun,namaakun from ".$dbname.".keu_5akun where detail=1 order by noakun";
        }
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $optAkun.="<option value='".$bar->noakun."'>".$bar->noakun ."-".$bar->namaakun."</option>";
        }
       
		$whrstat=($_POST['statblok']!='')?" and statusblok='".$_POST['statblok']."'":"";
		### ambil luas perdiv
		$aLuas="select sum(luasareaproduktif) as luasdivisi from ".$dbname.".setup_blok where kodeorg like '".$_POST['kodeorg']."%'".$whrstat;
                //echo $aLuas;
        $bLuas=mysql_query($aLuas);
		$cLuas=mysql_fetch_assoc($bLuas);
			
			
			//exit("Error:$luatDiv");
			
		
		 #ambil blok TBM,TB,LC
        $str="select luasareaproduktif,kodeorg,bloklama,statusblok from ".$dbname.".setup_blok where kodeorg like '".$_POST['kodeorg']."%'".$whrstat;
        //echo $str;
        $res=mysql_query($str);
        $jumblok=mysql_num_rows($res);
        if($jumblok<1)
        {
            exit(" Error: There is no block to allocate");
        }
        else
        {
            echo"<fieldset style='width:400px'>".$_SESSION['lang']['idcnote']."</fieldset>
                <table>
                       <tr><td>".$_SESSION['lang']['debet']."</td><td><select id=debet>".$optAkun."</select>Rp.".number_format($_POST['jumlah'])."</td></tr>
                        <tr><td>".$_SESSION['lang']['kredit']."</td><td><select id=kredit>".$optAkun."</select>Rp.".number_format($_POST['jumlah'])."</td></tr>
                         </table>   
                        ";
            echo"<button onclick=saveDistribusi('".$_POST['kodeorg']."')>".$_SESSION['lang']['save']."</button>";
            echo"<fieldset><legend>".$_SESSION['lang']['distribusi']."</legend>";//<td>".$_SESSION['lang']['luas']."</td>
            echo"<table class=sortable border=0 cellspacing=1>
                       <thead>
                           <tr class=rowheader><td>No</td>
						   <td>".$_SESSION['lang']['blok']."</td>
						   <td>".$_SESSION['lang']['bloklama']."</td>
						   <td>".$_SESSION['lang']['statusblok']."</td>
						   
						   <td>".$_SESSION['lang']['jumlah']." (Rp.)</td></tr>
                       </thead><tbody>";
            $no=0;
            $tot=0;
            while($bar=mysql_fetch_object($res))
            {  $no+=1;
			/*3. alokasi IDC || biaya perblok=luas blok / sum (jumlah luas total blok ) * Rp total pembebanan,,luasareaproduktif
			contoh : H01E02K010 ---- 11.09 HA / 2,530.28 * 5 jt*/
				$proporsi=($bar->luasareaproduktif/$cLuas['luasdivisi'])*$_POST['jumlah'];//<td>".$bar->luasareaproduktif."</td>
				
                echo"<tr class=rowcontent>
						<td class=firsttd>".$no."</td>
						<td>".$bar->kodeorg."</td>
						<td>".$bar->bloklama."</td>
						<td>".$bar->statusblok."</td>
						
						<td align=right>".number_format($proporsi)."</td>
					</tr>";
              $tot+=$proporsi;//<td align=right>".number_format($_POST['jumlah']/$jumblok)."</td>
                
            }
            echo"<tr><td colspan=4>".$_SESSION['lang']['total']."</td><td align=right>".number_format($tot)."</td></tr>";
            echo"</tbody><tfoot></tfoot></fieldset>";
        }
        break;
		
		
		
		
		
		
		
		
		
    case 'simpanIDC':
                $whrstat=($_POST['statblok']!='')?" and statusblok='".$_POST['statblok']."'":"";
		#ambil luas perdiv
		$aLuas="select sum(luasareaproduktif) as luasdivisi from ".$dbname.".setup_blok where kodeorg like '".$_POST['kodeorg']."%'".$whrstat;
        $bLuas=mysql_query($aLuas);
		$cLuas=mysql_fetch_assoc($bLuas);
			
	
		
        
		#ambil blok TBM,TB,LC
        $str="select kodeorg,statusblok,luasareaproduktif from ".$dbname.".setup_blok where kodeorg like '".$_POST['kodeorg']."%'".$whrstat;
        $res=mysql_query($str);
        $jumblok=mysql_num_rows($res);
        if($jumblok<1)
        {
            exit(" Error: Tidak ada blok yang dapat dialokasi");
        }
        else
        {
		#persiapkan no jurnal
		 $exist="select nojurnal from ".$dbname.".keu_jurnalht where nojurnal 
		         like '%".tanggalsystem($_POST['tanggal'])."/".$_POST['kodeorg']."/IDC/%'";
		#echo $exist;		 
		 $res1=mysql_query($exist);
         while($bar1=mysql_fetch_object($res1)){
           $noterakhir=$bar1->nojurnal;
		 }   		
         if($noterakhir==''){
          $nolanjut='001';
		 }      		
          else{
		    $xx=explode("/",$noterakhir);
			$nolanjut=intval($xx[3])+1;
			$nolanjut=str_pad($nolanjut, 3, "0", STR_PAD_LEFT);
		  }		 
        # Prep Header
        $nojurnal=  tanggalsystem($_POST['tanggal'])."/".$_POST['kodeorg']."/IDC/".$nolanjut; 
         #exit("Error".$nojurnal);		
        $dataRes['header'] = array(
            'nojurnal'=>$nojurnal,
            'kodejurnal'=>'IDC',
            'tanggal'=>  tanggalsystem($_POST['tanggal']),
            'tanggalentry'=>date('Ymd'),
            'posting'=>'1',
            'totaldebet'=>$_POST['jumlah'],
            'totalkredit'=>$_POST['jumlah'],
            'amountkoreksi'=>'0',
            'noreferensi'=>$_POST['nokas'],
            'autojurnal'=>'1',
            'matauang'=>'IDR',
            'kurs'=>'1',
            'revisi'=>'0'
        );
        
        # Data Detail
        $noUrut = 1;
                        # kredit
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>tanggalsystem($_POST['tanggal']),
                            'nourut'=>$noUrut,
                            'noakun'=>$_POST['kredit'],
                            'keterangan'=>'Alokasi IDC:'.$_POST['tanggal'],
                            'jumlah'=>-1*$_POST['jumlah'],
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>$_POST['kodeorg'],
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>'',
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$_POST['nokas'],
                            'noaruskas'=>'',
                            'kodevhc'=>'',
                            'nodok'=>'',
                            'kodeblok'=>'',
                           'revisi'=>'0'            
                        );
                        $noUrut++;  
                while($bar=mysql_fetch_object($res)){// 'jumlah'=>$_POST['jumlah']/$jumblok,
					
					
					$proporsi=($bar->luasareaproduktif/$cLuas['luasdivisi'])*$_POST['jumlah'];
					
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>tanggalsystem($_POST['tanggal']),
                            'nourut'=>$noUrut,
                            'noakun'=>$_POST['debet'],
                            'keterangan'=>'Alokasi IDC:'.$_POST['tanggal'],
                            'jumlah'=>$proporsi,
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>$_POST['kodeorg'],
                            'kodekegiatan'=>$_POST['debet'].'01',
                            'kodeasset'=>'',
                            'kodebarang'=>'',
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$_POST['nokas'],
                            'noaruskas'=>'',
                            'kodevhc'=>'',
                            'nodok'=>'',
                            'kodeblok'=>$bar->kodeorg,
                           'revisi'=>'0'            
                        );
                        $noUrut++;                      
                }  
              #insert jurnal
                #=== Insert Data ===
                $errorDB = "";
                # Header
                $queryH = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($queryH)) {
                    $errorDB .= "Header :".mysql_error()."\n".$queryH;
                }
                # Detail
                if($errorDB=='') {
                    foreach($dataRes['detail'] as $key=>$dataDet) {
                        $queryD = insertQuery($dbname,'keu_jurnaldt',$dataDet);
                        if(!mysql_query($queryD)) {
                            $errorDB .= "Detail ".$key." :".mysql_error()."\n";
                        }
                    }
                }
                if($errorDB!='')
                {
                    #rollback
                       $where = "nojurnal='".$nojurnal."'";
                       $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
                        if(!mysql_query($queryRB)) {
                        $errorDB .= "Rollback 1 Error :".mysql_error()."\n".$queryRB;
                         }
                } 
        } #end while     
        break;
    case 'hapusJurnal':
        #periksa tutup buku
        $tg=substr($_POST['tanggal'],0,7);
        $str="select * from ".$dbname.".setup_periodeakuntansi where kodeorg='".$_POST['kodeorg']."' and periode='".$tg."' and tutupbuku=0";
		$res=mysql_query($str);
        if(mysql_num_rows($res)<1)
        {
            exit(" Error: Periode tersebut unit telah tutup buku");
        }
        else
        {
                   $str="delete from ".$dbname.".keu_jurnalht where nojurnal='".$_POST['nojurnal']."'";
                   if(mysql_query($str))
                   {}
                   else
                   {
                       exit(" Error: ".mysql_error($conn));
                   }   
        }
        break;
}