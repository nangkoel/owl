<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
if(isTransactionPeriod())//check if transaction period is normal
{
  $gudang=$_POST['gudang'];
  $notransaksi=$_POST['notransaksi'];
  $tipetransaksi=$_POST['tipe'];
 //================================
 echo " <div  style='width:690px;height:380px;overflow:scroll;'> 
        <table class=sortable cellspacing=1 border=0 width=100%>
        <thead>";
  $num=0;		
 switch($tipetransaksi)
 {
case 1:
$str="select a.kodebarang,a.satuan,a.jumlah,a.hargasatuan,b.tanggal,b.kodept,b.tipetransaksi,c.namasupplier,b.nopo,b.idsupplier,a.hargasatuan
from ".$dbname.".log_transaksidt a left join  ".$dbname.".log_transaksiht b 
on a.notransaksi=b.notransaksi
        left join ".$dbname.".log_5supplier c
        on b.idsupplier=c.supplierid    
        where a.notransaksi='".$notransaksi."' and b.kodegudang='".$gudang."' and statussaldo=0";       
    echo"<tr class=rowheader>
                <td>No</td>
                    <td>".$_SESSION['lang']['tipe']."</td>			   
                    <td>".$_SESSION['lang']['tanggal']."</td>
                    <td>".$_SESSION['lang']['kodebarang']."</td>
                    <td>".$_SESSION['lang']['namabarang']."</td>
                    <td>".$_SESSION['lang']['satuan']."</td>
                    <td>".$_SESSION['lang']['kuantitas']."</td>
                    <td>".$_SESSION['lang']['hargasatuan']."</td>
                    <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
                    <td>".$_SESSION['lang']['supplier']."</td>
                    <td>".$_SESSION['lang']['nopo']."</td>
                    <td>".$_SESSION['lang']['hargasatuan']."</td>    
                    </tr>
                    </thead>
                    <tbody>";
            $res=mysql_query($str);
            $num=mysql_num_rows($res);
            $no=0;
            while($bar=mysql_fetch_object($res))
            {
                $no+=1;
                //=======ambil namabarang
                $strc="select namabarang from ".$dbname.".log_5masterbarang where
                        kodebarang='".$bar->kodebarang."'";   
                $resc=mysql_query($strc);
                $namabarang="";
                while($barc=mysql_fetch_object($resc))
                {
                        $namabarang=$barc->namabarang;
                }	    
                echo"<tr class=rowcontent id=row".$no.">
                    <td>".$no."</td>
                    <td id=tipe".$no." title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
                    <td id=tanggal".$no." >".tanggalnormal($bar->tanggal)."</td>
                    <td id=kodebarang".$no." >".$bar->kodebarang."</td>
                    <td>".$namabarang."</td>
                    <td id=satuan".$no." >".$bar->satuan."</td>
                    <td  id=jumlah".$no." align=right>".$bar->jumlah."</td>
                    <td  id=harga".$no." align=right>".$bar->hargasatuan."</td>
                    <td id=kodept".$no." >".$bar->kodept."</td>
                    <td id=supplier".$no.">".$bar->idsupplier."</td>
                    <td id=nopo".$no.">".$bar->nopo."</td>
                    <td id=hargasatuan".$no.">".$bar->hargasatuan."</td>    
                    </tr>";										
            }
            break;
case 2:
$str="select a.kodebarang,a.satuan,a.jumlah,b.tanggal,b.kodept,b.tipetransaksi,a.kodeblok,a.kodekegiatan,b.untukunit
                from ".$dbname.".log_transaksidt a left join  ".$dbname.".log_transaksiht b 
                    on a.notransaksi=b.notransaksi
                    where a.notransaksi='".$notransaksi."' and b.kodegudang='".$gudang."' and statussaldo=0";  
    echo"<tr class=rowheader>
                <td>No</td>
                    <td>".$_SESSION['lang']['tipe']."</td>			   
                    <td>".$_SESSION['lang']['tanggal']."</td>
                    <td>".$_SESSION['lang']['kodebarang']."</td>
                    <td>".$_SESSION['lang']['namabarang']."</td>
                    <td>".$_SESSION['lang']['satuan']."</td>
                    <td>".$_SESSION['lang']['kuantitas']."</td>
                    <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
                    <td>".$_SESSION['lang']['untukunit']."</td>                                
                    <td>".$_SESSION['lang']['kodekegiatan']."</td>
                    <td>".$_SESSION['lang']['kodevhc']."</td>                          
                    <td>".$_SESSION['lang']['kodeblok']."</td>                     
                    </tr>
                    </thead>
                    <tbody>";
            $res=mysql_query($str);
            $num=mysql_num_rows($res);
            $no=0;
            while($bar=mysql_fetch_object($res))
            {
                $no+=1;
                //=======ambil namabarang
                $strc="select namabarang from ".$dbname.".log_5masterbarang where
                        kodebarang='".$bar->kodebarang."'";   
                $resc=mysql_query($strc);
                $namabarang="";
                while($barc=mysql_fetch_object($resc))
                {
                        $namabarang=$barc->namabarang;
                }	    
                echo"<tr class=rowcontent id=row".$no.">
                    <td>".$no."</td>
                    <td id=tipe".$no." title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
                    <td id=tanggal".$no." >".tanggalnormal($bar->tanggal)."</td>
                    <td id=kodebarang".$no." >".$bar->kodebarang."</td>
                    <td>".$namabarang."</td>
                    <td id=satuan".$no." >".$bar->satuan."</td>
                    <td  id=jumlah".$no." align=right>".$bar->jumlah."</td>
                    <td id=kodept".$no." >".$bar->kodept."</td>
                    <td id=untukunit".$no.">".$bar->untukunit."</td>                                
                    <td id=kodekegiatan".$no.">".$bar->kodekegiatan."</td>      
                    <td id=kodemesin".$no.">".$bar->kodemesin."</td>                         
                    <td id=kodeblok".$no.">".$bar->kodeblok."</td>
                    </tr>";										
            }
            break;			
    case 3:
        $str="select a.kodebarang,a.satuan,a.jumlah,b.gudangx,a.kodeblok,a.hargasatuan,
                        b.tanggal,b.kodept,b.tipetransaksi
                        from ".$dbname.".log_transaksidt a left join  ".$dbname.".log_transaksiht b 
                            on a.notransaksi=b.notransaksi
                            where a.notransaksi='".$notransaksi."' and b.kodegudang='".$gudang."' and statussaldo=0";       

                echo"<tr class=rowheader>
                        <td>No</td>
                            <td>".$_SESSION['lang']['tipe']."</td>			   
                            <td>".$_SESSION['lang']['tanggal']."</td>
                            <td>".$_SESSION['lang']['kodebarang']."</td>
                            <td>".$_SESSION['lang']['namabarang']."</td>
                            <td>".$_SESSION['lang']['satuan']."</td>
                            <td>".$_SESSION['lang']['kuantitas']."</td>
                            <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
                            <td>".$_SESSION['lang']['sumber']."</td>
                            <td>".$_SESSION['lang']['kodeblok']."</td>
                            <td>".$_SESSION['lang']['hargasatuan']."</td>    
                            </tr>
                            </thead>
                            <tbody>";
                    $res=mysql_query($str);
                    $num=mysql_num_rows($res);
                    $no=0;
                    while($bar=mysql_fetch_object($res))
                    {
                        $no+=1;
                        //=======ambil namabarang
                        $strc="select namabarang from ".$dbname.".log_5masterbarang where
                                kodebarang='".$bar->kodebarang."'";   
                        $resc=mysql_query($strc);
                        $namabarang="";
                        while($barc=mysql_fetch_object($resc))
                        {
                                $namabarang=$barc->namabarang;
                        }	    
                        echo"<tr class=rowcontent id=row".$no.">
                            <td>".$no."</td>
                            <td id=tipe".$no." title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
                            <td id=tanggal".$no." >".tanggalnormal($bar->tanggal)."</td>
                            <td id=kodebarang".$no." >".$bar->kodebarang."</td>
                            <td>".$namabarang."</td>
                            <td id=satuan".$no." >".$bar->satuan."</td>
                            <td  id=jumlah".$no." align=right>".$bar->jumlah."</td>
                            <td id=kodept".$no." >".$bar->kodept."</td>
                            <td id=gudangx".$no." >".$bar->gudangx."</td>
                            <td id=kodeblok".$no.">".$bar->kodeblok."</td>
                            <td id=hargasatuan".$no.">".$bar->hargasatuan."</td>    
                            </tr>";										
                    }		 	
                break;
        case 5:
                    $str="select a.kodebarang,a.satuan,a.jumlah,b.untukpt,a.kodeblok,b.untukunit,a.kodekegiatan,a.kodemesin,
                        b.tanggal,b.kodept,b.tipetransaksi,b.namapenerima,b.idsupplier
                        from ".$dbname.".log_transaksidt a left join  ".$dbname.".log_transaksiht b 
                            on a.notransaksi=b.notransaksi
                            where a.notransaksi='".$notransaksi."' and b.kodegudang='".$gudang."' and statussaldo=0";  
                echo"<tr class=rowheader>
                        <td>No</td>
                            <td>".$_SESSION['lang']['tipe']."</td>			   
                            <td>".$_SESSION['lang']['tanggal']."</td>
                            <td>".$_SESSION['lang']['kodebarang']."</td>
                            <td>".$_SESSION['lang']['namabarang']."</td>
                            <td>".$_SESSION['lang']['satuan']."</td>
                            <td>".$_SESSION['lang']['kuantitas']."</td>
                            <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
                            <td>".$_SESSION['lang']['pt']."</td>    
                            <td>".$_SESSION['lang']['untukunit']."</td>                                
                            <td>".$_SESSION['lang']['kodeblok']."</td>
                            <td>".$_SESSION['lang']['kodekegiatan']."</td> 
                            <td>".$_SESSION['lang']['kodevhc']."</td>     
                            </tr>
                            </thead>
                            <tbody>";
                    $res=mysql_query($str);
                    $num=mysql_num_rows($res);
                    $no=0;
                    while($bar=mysql_fetch_object($res))
                    {
                        $no+=1;
                        //=======ambil namabarang
                        $strc="select namabarang from ".$dbname.".log_5masterbarang where
                                kodebarang='".$bar->kodebarang."'";   
                        $resc=mysql_query($strc);
                        $namabarang="";
                        while($barc=mysql_fetch_object($resc))
                        {
                                $namabarang=$barc->namabarang;
                        }	    
                        echo"<tr class=rowcontent id=row".$no.">
                            <td>".$no."</td>
                            <td id=tipe".$no." title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
                            <td id=tanggal".$no." >".tanggalnormal($bar->tanggal)."</td>
                            <td id=kodebarang".$no." >".$bar->kodebarang."</td>
                            <td>".$namabarang."</td>
                            <td id=satuan".$no." >".$bar->satuan."</td>
                            <td  id=jumlah".$no." align=right>".$bar->jumlah."</td>
                            <td id=kodept".$no." >".$bar->kodept."</td>
                            <td id=untukpt".$no." >".$bar->untukpt."</td>
                            <td id=untukunit".$no.">".$bar->untukunit."</td>                                
                            <td id=kodeblok".$no.">".$bar->kodeblok."</td>
                            <td id=kodekegiatan".$no.">".$bar->kodekegiatan."</td>                                
                            <td id=kodemesin".$no.">".$bar->kodemesin."</td>    
                            <td hidden id=namapenerima".$no.">".$bar->namapenerima."</td>    
                            <td hidden id=supplier".$no.">".$bar->idsupplier."</td>    
                            </tr>";										
                    }
                break;
    case 6:
    $str="select a.kodebarang,a.satuan,a.jumlah,a.hargasatuan,b.tanggal,b.kodept,b.tipetransaksi,c.namasupplier,b.nopo,b.idsupplier,a.hargasatuan
        from ".$dbname.".log_transaksidt a left join  ".$dbname.".log_transaksiht b 
        on a.notransaksi=b.notransaksi
        left join ".$dbname.".log_5supplier c
        on b.idsupplier=c.supplierid    
        where a.notransaksi='".$notransaksi."' and b.kodegudang='".$gudang."' and statussaldo=0";       
    echo"<tr class=rowheader>
                <td>No</td>
                    <td>".$_SESSION['lang']['tipe']."</td>			   
                    <td>".$_SESSION['lang']['tanggal']."</td>
                    <td>".$_SESSION['lang']['kodebarang']."</td>
                    <td>".$_SESSION['lang']['namabarang']."</td>
                    <td>".$_SESSION['lang']['satuan']."</td>
                    <td>".$_SESSION['lang']['kuantitas']."</td>
                    <td>".$_SESSION['lang']['hargasatuan']."</td>
                    <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
                    <td>".$_SESSION['lang']['supplier']."</td>
                    <td>".$_SESSION['lang']['nopo']."</td>
                    <td>".$_SESSION['lang']['hargasatuan']."</td>    
                    </tr>
                    </thead>
                    <tbody>";
            $res=mysql_query($str);
            $num=mysql_num_rows($res);
            $no=0;
            while($bar=mysql_fetch_object($res))
            {
                $no+=1;
                //=======ambil namabarang
                $strc="select namabarang from ".$dbname.".log_5masterbarang where
                        kodebarang='".$bar->kodebarang."'";   
                $resc=mysql_query($strc);
                $namabarang="";
                while($barc=mysql_fetch_object($resc))
                {
                        $namabarang=$barc->namabarang;
                }	    
                echo"<tr class=rowcontent id=row".$no.">
                    <td>".$no."</td>
                    <td id=tipe".$no." title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
                    <td id=tanggal".$no." >".tanggalnormal($bar->tanggal)."</td>
                    <td id=kodebarang".$no." >".$bar->kodebarang."</td>
                    <td>".$namabarang."</td>
                    <td id=satuan".$no." >".$bar->satuan."</td>
                    <td  id=jumlah".$no." align=right>".$bar->jumlah."</td>
                    <td  id=harga".$no." align=right>".$bar->hargasatuan."</td>
                    <td id=kodept".$no." >".$bar->kodept."</td>
                    <td id=supplier".$no.">".$bar->idsupplier."</td>
                    <td id=nopo".$no.">".$bar->nopo."</td>
                    <td id=hargasatuan".$no.">".$bar->hargasatuan."</td>                         
                    </tr>";										
            }                            
       break;
        case 7:
                    $str="select a.kodebarang,a.satuan,a.jumlah,b.gudangx,a.kodeblok,
                        b.tanggal,b.kodept,b.tipetransaksi
                        from ".$dbname.".log_transaksidt a left join  ".$dbname.".log_transaksiht b 
                            on a.notransaksi=b.notransaksi
                            where a.notransaksi='".$notransaksi."' and b.kodegudang='".$gudang."' and statussaldo=0";       

                echo"<tr class=rowheader>
                        <td>No</td>
                            <td>".$_SESSION['lang']['tipe']."</td>			   
                            <td>".$_SESSION['lang']['tanggal']."</td>
                            <td>".$_SESSION['lang']['kodebarang']."</td>
                            <td>".$_SESSION['lang']['namabarang']."</td>
                            <td>".$_SESSION['lang']['satuan']."</td>
                            <td>".$_SESSION['lang']['kuantitas']."</td>
                            <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
                            <td>".$_SESSION['lang']['tujuan']."</td>
                            <td>".$_SESSION['lang']['kodeblok']."</td>
                            </tr>
                            </thead>
                            <tbody>";
                    $res=mysql_query($str);
                    $num=mysql_num_rows($res);
                    $no=0;
                    while($bar=mysql_fetch_object($res))
                    {
                        $no+=1;
                        //=======ambil namabarang
                        $strc="select namabarang from ".$dbname.".log_5masterbarang where
                                kodebarang='".$bar->kodebarang."'";   
                        $resc=mysql_query($strc);
                        $namabarang="";
                        while($barc=mysql_fetch_object($resc))
                        {
                                $namabarang=$barc->namabarang;
                        }	    
                        echo"<tr class=rowcontent id=row".$no.">
                            <td>".$no."</td>
                            <td id=tipe".$no." title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
                            <td id=tanggal".$no." >".tanggalnormal($bar->tanggal)."</td>
                            <td id=kodebarang".$no." >".$bar->kodebarang."</td>
                            <td>".$namabarang."</td>
                            <td id=satuan".$no." >".$bar->satuan."</td>
                            <td  id=jumlah".$no." align=right>".$bar->jumlah."</td>
                            <td id=kodept".$no." >".$bar->kodept."</td>
                            <td id=gudangx".$no." >".$bar->gudangx."</td>
                            <td id=kodeblok".$no.">".$bar->kodeblok."</td>
                            </tr>";										
                    }			
            break;
        default:
    echo" Error: Unknown transaction type"; 				

  }
   echo"</tbody><tfoot></tfoot></table>
   <center>
     <button onclick=\"prosesPosting(".$no.",'".$tipetransaksi."','".$notransaksi."'); this.disabled=true;\" class=mybutton>".$_SESSION['lang']['posting']."</button>
	 <button onclick=closeDialog() class=mybutton>".$_SESSION['lang']['cancel']."</button>
   </center>
   </div>";
}
else
{
	echo " Error: Transaction Period missing";
}
?>