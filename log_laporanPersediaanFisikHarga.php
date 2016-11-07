<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

    $pt=$_POST['pt'];
    $gudang=$_POST['gudang'];
    $periode=$_POST['periode'];

$str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
    where periode ='".$periode."' and kodeorg='".$gudang."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $tanggalmulai=$bar->tanggalmulai;
    $tanggalsampai=$bar->tanggalsampai;
}
    
if(isset($_POST['unitDt']))//ini dari tab laporan stok per unit (tab 3)
{
                $str="select 
                      a.kodeorg,
                      a.kodebarang,
                      sum(a.saldoakhirqty) as salakqty,
                      sum(a.nilaisaldoakhir) as salakrp,
                      sum(a.qtymasuk) as masukqty,
                      sum(a.qtykeluar) as keluarqty,
                      sum(qtymasukxharga) as masukrp,
                      sum(qtykeluarxharga) as keluarrp,                      
                      sum(a.saldoawalqty) as sawalqty,
                      sum(a.nilaisaldoawal) as sawalrp,
                        b.namabarang,b.satuan    
                        from ".$dbname.".log_5saldobulanan a
                        left join ".$dbname.".log_5masterbarang b
                        on a.kodebarang=b.kodebarang
                      where kodegudang like '".$_POST['unitDt']."%' 
                      and periode='".$periode."'
                      group by a.kodebarang order by a.kodebarang";
            #masuk
            $smasuk="select sum(jumlah) as masukqty,sum(jumlah*hargarata) as masukrp,kodebarang from ".$dbname.".log_transaksi_vw
                         where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and kodegudang like '".$_POST['unitDt']."%'
                         and tipetransaksi<5 and statussaldo in (1,2) group by kodebarang";
            $res=mysql_query($smasuk);
            while($bar=mysql_fetch_object($res)){
                $rmasukqty[$bar->kodebarang]=$bar->masukqty;
                $rmasukrp[$bar->kodebarang]=$bar->masukrp;
            }
            #keluar
            $skeluar="select sum(jumlah) as keluarqty,sum(jumlah*hargarata) as keluarrp,kodebarang from ".$dbname.".log_transaksi_vw
                         where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and kodegudang like '".$_POST['unitDt']."%'
                         and tipetransaksi>4 and statussaldo in (1,2) group by kodebarang";
            $res=mysql_query($skeluar);
            while($bar=mysql_fetch_object($res)){
                $rkeluarqty[$bar->kodebarang]=$bar->keluarqty;
                $rkeluarrp[$bar->kodebarang]=$bar->keluarrp;
            }
}
else if($gudang=='')
{
            $str="select 
                      a.kodeorg,
                      a.kodebarang,
                      sum(a.saldoakhirqty) as salakqty,
                      sum(a.nilaisaldoakhir) as salakrp,
                      sum(a.qtymasuk) as masukqty,
                      sum(a.qtykeluar) as keluarqty,
                      sum(qtymasukxharga) as masukrp,
                      sum(qtykeluarxharga) as keluarrp,                      
                      sum(a.saldoawalqty) as sawalqty,
                      sum(a.nilaisaldoawal) as sawalrp,
                        b.namabarang,b.satuan    
                        from ".$dbname.".log_5saldobulanan a
                        left join ".$dbname.".log_5masterbarang b
                        on a.kodebarang=b.kodebarang
                      where kodeorg='".$pt."' 
                      and periode='".$periode."'
                      group by a.kodebarang order by a.kodebarang";
            #masuk
            $smasuk="select sum(jumlah) as masukqty,sum(jumlah*hargarata) as masukrp,kodebarang from ".$dbname.".log_transaksi_vw
                         where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and kodept='".$pt."'
                         and tipetransaksi<5 and statussaldo in (1,2) group by kodebarang";
            $res=mysql_query($smasuk);
            while($bar=mysql_fetch_object($res)){
                $rmasukqty[$bar->kodebarang]=$bar->masukqty;
                $rmasukrp[$bar->kodebarang]=$bar->masukrp;
            }
            #keluar
            $skeluar="select sum(jumlah) as keluarqty,sum(jumlah*hargarata) as keluarrp,kodebarang from ".$dbname.".log_transaksi_vw
                         where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and kodept='".$pt."'
                         and tipetransaksi>4 and statussaldo in (1,2) group by kodebarang";
            $res=mysql_query($skeluar);
            while($bar=mysql_fetch_object($res)){
                $rkeluarqty[$bar->kodebarang]=$bar->keluarqty;
                $rkeluarrp[$bar->kodebarang]=$bar->keluarrp;
            }
}
else
{
            $str="select
                      a.kodeorg,
                      a.kodebarang,
                      a.saldoakhirqty as salakqty,
                      a.hargarata as harat,
                      a.nilaisaldoakhir as salakrp,
                      a.qtymasuk as masukqty,
                      a.qtykeluar as keluarqty,
                      a.qtymasukxharga as masukrp,
                      a.qtykeluarxharga as keluarrp,
                      a.saldoawalqty as sawalqty,
                      a.hargaratasaldoawal as sawalharat,
                      a.nilaisaldoawal as sawalrp,
                  b.namabarang,b.satuan 		 		      
                      from ".$dbname.".log_5saldobulanan a
                  left join ".$dbname.".log_5masterbarang b
                      on a.kodebarang=b.kodebarang
                      where kodeorg='".$pt."' 
                      and periode='".$periode."'
                      and kodegudang='".$gudang."'
                     order by a.kodebarang";
            #masuk
            $smasuk="select sum(jumlah) as masukqty,sum(jumlah*hargarata) as masukrp,kodebarang from ".$dbname.".log_transaksi_vw
                         where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and kodegudang='".$gudang."'
                         and tipetransaksi<5 and statussaldo in (1,2) group by kodebarang";
            $res=mysql_query($smasuk);
            while($bar=mysql_fetch_object($res)){
                $rmasukqty[$bar->kodebarang]=$bar->masukqty;
                $rmasukrp[$bar->kodebarang]=$bar->masukrp;
            }
            #keluar
            $skeluar="select sum(jumlah) as keluarqty,sum(jumlah*hargarata) as keluarrp,kodebarang from ".$dbname.".log_transaksi_vw
                         where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and kodegudang='".$gudang."'
                         and tipetransaksi>4 and statussaldo in (1,2) group by kodebarang";
            $res=mysql_query($skeluar);
            while($bar=mysql_fetch_object($res)){
                $rkeluarqty[$bar->kodebarang]=$bar->keluarqty;
                $rkeluarrp[$bar->kodebarang]=$bar->keluarrp;
            }
    }	

//=================================================
    $salakqty	=0;
    $harat	=0;
    $salakrp	=0;
    $masukqty	=0;
    $keluarqty	=0;
    $masukrp	=0;
    $keluarrp	=0;
    $sawalQTY	=0;
    $sawalharat	=0;
    $sawalrp	=0;
    $namabarang	=0;
	 

    //echo $str;
    $res=mysql_query($str);
    $no=0;
    if(mysql_num_rows($res)<1)
    {
            echo"<tr class=rowcontent><td colspan=17>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
    }
    else
    {
            while($bar=mysql_fetch_object($res))
            {
                    $no+=1;
                    $kodebarang=$bar->kodebarang;
                    $namabarang=$bar->namabarang; 
                    $salakqty	=$bar->sawalqty+$rmasukqty[$kodebarang]-$rkeluarqty[$kodebarang];
                    $salakrp	=$bar->sawalrp+$rmasukrp[$kodebarang]-$rkeluarrp[$kodebarang];
                    $masukqty	=$bar->masukqty;
                    $keluarqty	=$bar->keluarqty;
                    $masukrp	=$bar->masukrp;
                    $keluarrp	=$bar->keluarrp;
                    $sawalQTY	=$bar->sawalqty;
                    $sawalrp	=$bar->sawalrp;
                    // Mengunci saldo akhir per Des 2014
                    if (substr($periode,0,4)==2014 and substr($periode,5)==12){
                        $salakqty=$bar->salakqty;
                        $salakrp=$bar->salakrp;
                    }

                    @$sawalharat=$bar->sawalrp/$bar->sawalqty;
                    @$haratmasuk=$rmasukrp[$kodebarang]/$rmasukqty[$kodebarang];
                    @$haratkeluar=$rkeluarrp[$kodebarang]/$rkeluarqty[$kodebarang];
                    @$harat	=$salakrp/$salakqty;

        if(isset($_POST['unitDt']))//ini dari tab laporan stok per unit (tab 3)
        {
                echo"<tr class=rowcontent> ";                           
        }
        else {
                echo"<tr class=rowcontent  style='cursor:pointer;' title='Click' onclick=\"detailMutasiBarangHargaExcel(event,'".$pt."','".$periode."','".$gudang."','".$kodebarang."','".$namabarang."','".$bar->satuan."','log_laporanMutasiDetailPerBarangHarga_Excel.php');\"> ";
        }
        echo "                <td>".$no."</td>
                              <td nowrap>".$periode."</td>
                              <td>".$kodebarang."</td>
                              <td>".$namabarang."</td>
                              <td>".$bar->satuan."</td>
                               <td align=right class=firsttd>".number_format($sawalQTY,2,'.',',')."</td>
                               <td align=right>".number_format($sawalharat,2,'.',',')."</td>
                               <td align=right>".number_format($sawalrp,2,'.',',')."</td>
                               <td align=right class=firsttd>".number_format($rmasukqty[$kodebarang],2,'.',',')."</td>
                               <td align=right>".number_format($haratmasuk,2,'.',',')."</td>
                               <td align=right>".number_format($rmasukrp[$kodebarang],2,'.',',')."</td>
                               <td align=right class=firsttd>".number_format($rkeluarqty[$kodebarang],2,'.',',')."</td>
                               <td align=right>".number_format($haratkeluar,2,'.',',')."</td>
                               <td align=right>".number_format($rkeluarrp[$kodebarang],2,'.',',')."</td>
                               <td align=right class=firsttd>".number_format($salakqty,2,'.',',')."</td>
                               <td align=right>".number_format($harat,2,'.',',')."</td>
                               <td align=right>".number_format($salakrp,2,'.',',')."</td>			   
                            </tr>"; 		
            }		
    }
?>