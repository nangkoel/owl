<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
    include_once('lib/zLib.php');
	
    $tab=$_POST['tab'];
    $proses=$_POST['proses'];
    if((isset($_POST['txtfind']))!='')
    {
        $txtfind=$_POST['txtfind'];
	$str="select * from ".$dbname.".log_5masterbarang where (namabarang like '%".$txtfind."%' or kodebarang like '%".$txtfind."%') ";
	if($res=mysql_query($str))
	{
            echo"
            <fieldset>
            <legend>Result</legend>
            <div style=\"overflow:auto; height:300px;\" >
            <table class=data cellspacing=1 cellpadding=2  border=0>
            <thead>
            <tr class=rowheader>
                <td class=firsttd>
                    No.
                </td>
                <td>Kode Barang</td>
                <td>Nama Barang</td>
                <td>Satuan</td>
            </tr>
            </thead>
            <tbody>";
            $no=0;	 
            while($bar=mysql_fetch_object($res))
            {
                $no+=1;
		if($bar->inactive==1)
        	{
		    echo"<tr class=rowcontent style='cursor:pointer;'  title='Inactive' >";
                    $bar->namabarang=$bar->namabarang. " [Inactive]";
		}
		else
		{
                    if($tab=='1')
                        echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setBrg(1,'".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."')\" title='Click' >";
                }   
		echo" <td class=firsttd>".$no."</td>
                    <td>".$bar->kodebarang."</td>
                    <td>".$bar->namabarang."</td>
                    <td>".$bar->satuan."</td>
                    </tr>";
		}	 
		echo "</tbody>
                    <tfoot>
                    </tfoot>
            </table></div></fieldset>";
        }	
        else
	{
            echo " Gagal,".addslashes(mysql_error($conn));
	}	
    }
    
    
    
    if((isset($_POST['spkfind']))!='')
    {
	$str="select * from ".$dbname.".setup_kegiatan";
	if($res=mysql_query($str))
	{
            while($bar=mysql_fetch_object($res))
            {
                $kamuskeg[$bar->kodekegiatan]=$bar->namakegiatan;
            }
        }	

        $spkfind=$_POST['spkfind'];
        $no=0;
        $str="select * from ".$dbname.".log_baspk where notransaksi = '".$spkfind."' group by kodekegiatan";
	if($res=mysql_query($str))
	{
            while($bar=mysql_fetch_object($res))
            {
                $no+=1;
                $optws.="<option value='".$bar->kodekegiatan."'>".$kamuskeg[$bar->kodekegiatan]."</option>";
            }
        }	
        else
	{
            echo " Gagal,".addslashes(mysql_error($conn));
	}
        
        $no=0;
        $str="select * from ".$dbname.".log_baspk where notransaksi = '".$spkfind."' group by kodeblok";
	if($res=mysql_query($str))
	{
            while($bar=mysql_fetch_object($res))
            {
                $no+=1;
                $optws2.="<option value='".$bar->kodeblok."'>".$bar->kodeblok."</option>";
            }
        }	
        else
	{
            echo " Gagal,".addslashes(mysql_error($conn));
	}
        
        if($no==0)echo "error: BA SPK not found.";
        if($tab==9)echo $optws;
        if($tab==8)echo $optws2;
    }
    if($proses=='loaddata'){
        $limit=10;
        $page=0;
        if(isset($_POST['page']))
        {
            $page=$_POST['page'];
            if($page<0)
            $page=0;
        }
        $kamusbarang=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
        $kamussatuan=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
        $kamuskegiatan=makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');

        $ql2="select count(*) as jmlhrow from ".$dbname.".log_baspk_material order by `notransaksi` desc";
        $query2=mysql_query($ql2) or die(mysql_error());
        while($jsl=mysql_fetch_object($query2)){
            $jlhbrs= $jsl->jmlhrow;
        }
        
        $offset=$page*$limit;
        if($jlhbrs<($offset))$page-=1;
        $offset=$page*$limit;
        $no=$offset;
        
        $slvhc="select * from ".$dbname.".log_baspk_material order by `notransaksi` desc,`kodekegiatan`,`blok`,`tanggal`,`kodebarang` desc limit ".$offset.",".$limit." ";
        $qlvhc=mysql_query($slvhc) or die(mysql_error());
        $user_online=$_SESSION['standard']['userid'];
        while($rlvhc=mysql_fetch_assoc($qlvhc))
        {
            $no+=1;

            echo"<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$rlvhc['notransaksi']."</td>
            <td>".$kamuskegiatan[$rlvhc['kodekegiatan']]."</td>
            <td>".$rlvhc['blok']."</td>
            <td>".$rlvhc['tanggal']."</td>
            <td>".$kamusbarang[$rlvhc['kodebarang']]."</td>
            <td align=right>".$rlvhc['jumlah']."</td>
            <td>".$kamussatuan[$rlvhc['kodebarang']]."</td>
            <td>
            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['notransaksi']."','".$rlvhc['kodekegiatan']."','".$rlvhc['blok']."','".$rlvhc['tanggal']."','".$rlvhc['kodebarang']."');\" ></td>";
        }
        echo"
        </tr><tr class=rowheader><td colspan=9 align=center>
        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
        <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
        <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
        </td>
        </tr>";
    }
    
    if($proses=='deletedata'){
        $nospk=$_POST['nospk'];
        $kegiatan=$_POST['kegiatan'];
        $blok=$_POST['blok'];
        $tanggal=$_POST['tanggal'];
        $kodebarang=$_POST['kodebarang'];
        $where="notransaksi ='".$nospk."' and kodekegiatan = '".$kegiatan."' and blok = '".$blok."' and tanggal = '".$tanggal."'";
//        $sket="select distinct posting from ".$dbname.".log_baspk where ".$where."";
//        $qKet=mysql_query($sket) or die(mysql_error($conn));
//        $rKet=mysql_fetch_assoc($qKet); 
//        if($rKet['posting']==0)
//        {
            $sDel="delete from ".$dbname.".log_baspk_material where ".$where." and kodebarang = '".$kodebarang."'";
            if(mysql_query($sDel))
            echo"";
            else
            echo "DB Error : ".mysql_error($conn);                        
//        }
//        else
//        {
//            exit("Error:BA SPK sudah diposting");
//        }        
    }
    
    if($tab==7){
        $nospk=$_POST['nospk'];
        $kegiatan=$_POST['kegiatan'];
        $blok=$_POST['blok'];

        $no=0;
        $str="select * from ".$dbname.".log_baspk where notransaksi = '".$nospk."' and kodeblok = '".$blok."' and kodekegiatan = '".$kegiatan."' limit 1";
	if($res=mysql_query($str))
	{
            while($bar=mysql_fetch_object($res))
            {
                $no+=1;
                $optws4.=$bar->tanggal;
            }
        }	
        else
	{
            echo " Gagal,".addslashes(mysql_error($conn));
	}
        echo $optws4;
    }
    
    
    
    if($proses=='insert'){
        $nospk=$_POST['nospk'];
        $kegiatan=$_POST['kegiatan'];
        $blok=$_POST['blok'];
        $tanggal=$_POST['tanggal'];
        $kodebarang=$_POST['kodebarang'];
        $jumlah=$_POST['jumlah'];

        $rrr='';
        if($nospk=='')$rrr.=" No SPK, ";
        if($jumlah=='')$rrr.=" Jumlah, ";
        if($kodebarang=='')$rrr.=" Nama/Kode Barang, barang yang valid akan memunculkan satuan";
        if($rrr!=''){
            echo "error: Silakan mengisi ".$rrr.".";
            exit;
        }

        $str="select * from ".$dbname.".log_baspk_material
        where notransaksi='".$nospk."' and kodekegiatan = '".$kegiatan."' and blok = '".$blok."' 
            and tanggal = '".$tanggal."' and kodebarang = '".$kodebarang."' 
        limit 1";
        $sudahada=0;
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $sudahada=$bar->kodekegiatan;
        }
        if($sudahada!=0){
            echo 'error: data exist.';
            exit;
        }
        $str="INSERT INTO ".$dbname.".log_baspk_material (`notransaksi` ,`blok` ,`kodekegiatan` ,`tanggal` ,`kodebarang` ,`jumlah`)
        VALUES ('".$nospk."', '".$blok."', '".$kegiatan."', '".$tanggal."', '".$kodebarang."', '".$jumlah."')        
        ";
        if($res=mysql_query($str))
        {
        }	
        else
        {
            echo " Gagal,".addslashes(mysql_error($conn));
        }
            
    }
?>