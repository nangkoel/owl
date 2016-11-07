<?php
    require_once('master_validation.php');
    require_once('config/connection.php');

    $notransaksi=$_POST['notransaksi'];
    $status=$_POST['status'];
    $gudang=$_POST['gudang'];
    $user=$_SESSION['standard']['userid'];
    
    #periksa apakah semua detail sudah status saldo 1
    $str="select kodebarang from ".$dbname.".log_transaksidt where notransaksi='".$notransaksi."'  and statussaldo=0"; 
    $res=mysql_query($str);
    if(mysql_num_rows($res)>0){#jika belum maka jangan ubah flag
        echo "Error : There is still unsucceed transaction, please re-process";
    }
    else{  #jika sudah, maka ubah flag      
    $str="update ".$dbname.".log_transaksiht set post=".$status.", postedby=".$user.",statusjurnal=1
             where notransaksi='".$notransaksi."'  and kodegudang='".$gudang."'";   
    if(mysql_query($str))
    {
        if(mysql_affected_rows($conn)<1)
            {
                    echo "Error : post status update nothing";
            }	
   }
    else
            {
                echo " Gagal,".(mysql_error($conn));
            }    
    }        
?>
