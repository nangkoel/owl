<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
	
    $tab=$_POST['tab'];
    if((isset($_POST['txtfind']))!='')
    {
        $awalan=$_POST['awalan'];
        $txtfind=$_POST['txtfind'];
	$str="select * from ".$dbname.".log_5masterbarang where kodebarang like '".$awalan."%' and (namabarang like '%".$txtfind."%' or kodebarang like '%".$txtfind."%') ";
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
                    if($tab=='2')
                        echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setBrg(2,'".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."')\" title='Click' >";
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

?>