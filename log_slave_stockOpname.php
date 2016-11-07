<?php
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');
$param=$_POST;
$nilaisal=$param['jumlah']*$param['harga'];
#periksa periode gudang
$str="select * from ".$dbname.".setup_periodeakuntansi where kodeorg='".$param['kodegudang']."' and tutupbuku=0";
$res=mysql_query($str);
if(mysql_num_rows($res)<1){
    exit('Periode gudang belum ada');
}else{
    while($bar=mysql_fetch_object($res)){
        $periode=$bar->periode;
    }  
    #periksa apakah transaksi sudah ada:
    $str="select * from ".$dbname.".log_5saldobulanan where periode='".$periode."' and kodebarang='".$param['kodebarang']."' and kodegudang='".$param['kodegudang']."'";
    $res=mysql_query($str);
    if(mysql_num_rows($res)>0){
        
        while($bar=mysql_fetch_object($res)){
            $currqty=$bar->saldoakhirqty;
            $curnil=$bar->hargarata;
            $curtot=$bar->nilaisaldoakhir;
        }
        #ada maka update saldobulanan
        $str="update ".$dbname.".log_5saldobulanan set saldoakhirqty=".$param['jumlah'].", hargarata=".$param['harga'].",
                  nilaisaldoakhir=".$nilaisal." where periode='".$periode."' and kodebarang='".$param['kodebarang']."' and kodegudang='".$param['kodegudang']."'";
        #update masterbarangdt
        $str2="update ".$dbname.".log_5masterbarangdt set saldoqty=".$param['jumlah']." where kodebarang='".$param['kodebarang']."' and kodegudang='".$param['kodegudang']."'";
        if(mysql_query($str2)){
            if(mysql_query($str)){
                #write log
                $str="insert into ".$dbname.".log_stopname_log(kodegudang,kodebarang,updateby,oldqty,oldharga,newqty,newharga)
                          values('".$param['kodegudang']."','".$param['kodebarang']."',".$_SESSION['standard']['userid'].",".$currqty.",".$curnil.",".$param['jumlah'].",".$param['harga'].")";
                $res=mysql_query($str);
            }else{
                echo " Error saldobulanan:".mysql_error($conn);
                #rollback masterbarangdt
                $str2="update ".$dbname.".log_5masterbarangdt set saldoqty=".$currqty." where kodebarang='".$param['kodebarang']."' and kodegudang='".$param['kodegudang']."'";
                mysql_query($str2);
                exit();
            }
        }else{
            echo " Error masterbarangdt:".mysql_error($conn);
            exit();
        }     
    }else{
          #ambil kode pt    
           $str="select induk from ".$dbname.".organisasi where kodeorganisasi='".substr($param['kodegudang'],0,4)."'";
           $res=mysql_query($str);
           while($bar=mysql_fetch_object($res)){
               $kodept=$bar->induk;
           }
        #tidak ada maka insert
        $str="insert into ".$dbname.".log_5saldobulanan(`kodeorg`, `kodebarang`, `saldoakhirqty`, `hargarata`, `lastuser`, `periode`, `nilaisaldoakhir`, `kodegudang`, `qtymasuk`, `qtykeluar`, `qtymasukxharga`, `qtykeluarxharga`, `saldoawalqty`, `hargaratasaldoawal`, `nilaisaldoawal`) 
                 values ('".$kodept."', '".$param['kodebarang']."', ".$param['jumlah'].", ".$param['harga'].", ".$_SESSION['standard']['userid'].", '".$periode."', ".$nilaisal.", '".$param['kodegudang']."', 0, 0, 0, 0, 0, 0, 0);";
       
       $str2= "insert into ".$dbname.".log_5masterbarangdt (`kodeorg`, `kodebarang`, `saldoqty`, `hargalastin`, `hargalastout`, `stockbataspesan`, `stockminimum`, `lastuser`, `kodegudang`) values
        ('".$kodept."', '".$param['kodebarang']."', ".$param['jumlah'].", 0, 0, 0, 0, ".$_SESSION['standard']['userid'].",  '".$param['kodegudang']."')";
       if(mysql_query($str2)){
           if(mysql_query($str)){
         #write log
                $str="insert into ".$dbname.".log_stopname_log(kodegudang,kodebarang,updateby,oldqty,oldharga,newqty,newharga)
                          values('".$param['kodegudang']."','".$param['kodebarang']."',".$_SESSION['standard']['userid'].",0,0,".$param['jumlah'].",".$param['harga'].")";
                $res=mysql_query($str);               
           }else{
                echo " Error insert saldobulanan:".mysql_error($conn);
               #rollback masterbarangdt 
                  $str2="delete from ".$dbname.".log_5masterbarangdt  where kodebarang='".$param['kodebarang']."' and kodegudang='".$param['kodegudang']."'";
                mysql_query($str2);
                exit();                   
           }
       }else{
            echo " Error insert masterbarangdt:".mysql_error($conn);
            exit(); 
       }
    }    
}
?>