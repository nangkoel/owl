<?php //@Copy nangkoelframework
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$str1="select karyawanid,namakaryawan,tanggalmasuk,lokasitugas,kodegolongan from ".$dbname.".datakaryawan
	       where  tanggalmasuk<>'0000-00-00'  and tanggalmasuk<".date('Ymd')."
                          and tanggalmasuk like '%".date('m-d')."'
                          and (tanggalkeluar='0000-00-00' or tanggalkeluar>".date('Ymd').") and tipekaryawan in(0,1,2,3)";
$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
            //=================================
            //default
            $x=readTextFile('config/jumlahcuti.lst');
            if(intval($x)>0)
                $hakcuti=$x;
            else
                $hakcuti=12;  //default
            #jika bukan orang HO maka dapat 
           if(substr($bar1->kodegolongan,0,1)>5){
               $hakcuti=24;
           }
            
            //=================================
            $tgl=substr(str_replace("-","",$bar1->tanggalmasuk),4,4);		
            $dari=mktime(0,0,0,substr($tgl,0,2),substr($tgl,2,2),date('Y'));
            $dari=date('Ymd',$dari);
            $sampai=mktime(0,0,0,substr($tgl,0,2),substr($tgl,2,2),date('Y')+1);		
            $sampai=date('Ymd',$sampai);
            #jika periode masuk masih belum 1tahun maka 0
             $d=substr(str_replace("-","",$bar1->tanggalmasuk),0,4);
        #ambil sisa cuti YBS
             $str="select sisa from ".$dbname.".sdm_cutiht where karyawanid=".$bar1->karyawanid." 
                       and periodecuti>".(date('Y')-2)." order by periodecuti desc limit 1";
             $resx=mysql_query($str);
             $sisalalu=0;
             while($barx=mysql_fetch_object($resx))
             {
                 $sisalalu=$barx->sisa;
             }
        #periksa apakah sudah ada pada periode yang sama
              $str="select * from ".$dbname.".sdm_cutiht where karyawanid=".$bar1->karyawanid." 
                       and periodecuti=".date('Y')." order by periodecuti desc limit 1";
             $resy=mysql_query($str);
             if(mysql_num_rows($resy)>0)
             {
                 #berarti  saldo saat ini adalah sisalalu
                 #$saldo=$sisalalu;
                 #tidak ada perubahan
             }
             else
             {   
                 $saldo=$hakcuti;
                #==========================periksa apakah sudah ada pengambilan cuti sebelum ada header (sebelum cuti baru muncul)
                                $strx="select sum(jumlahcuti) as diambil from ".$dbname.".sdm_cutidt
                                    where karyawanid=".$bar1->karyawanid."
                                     and  daritanggal >=".$dari." and daritanggal<=".$sampai;
                                $diambil=0;#sudah diambil diambil tahun ini
                                $resx=mysql_query($strx);
                                while($barx=mysql_fetch_object($resx))
                                {
                                        $diambil=$barx->diambil;
                                        if($diambil=='')
                                            $diambil=0;
                                }
                    $saldo=$saldo-$diambil;            
                 #================================================================
                 #maka insert periode baru
                 $str="insert into ".$dbname.".sdm_cutiht(kodeorg, karyawanid, periodecuti, keterangan, dari, sampai, hakcuti, diambil, sisa)
                           values('".$bar1->lokasitugas."',".$bar1->karyawanid.",".date('Y').",'',".$dari.",".$sampai.",".$hakcuti.",0,".$saldo.")";
                 mysql_query($str);
             } 
}             
?>