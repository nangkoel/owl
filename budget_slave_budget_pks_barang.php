<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
    #regional
    $sregional="select distinct regional from ".$dbname.".bgt_regional_assignment where kodeunit='".$_SESSION['empl']['lokasitugas']."'";
    $qregional=mysql_query($sregional) or die(mysql_error($conn));
    $regional=mysql_fetch_assoc($qregional);
    
    $tab=$_POST['tab'];
    if((isset($_POST['txtfind']))!='')
    {
        $awalan=$_POST['awalan'];
        $txtfind=$_POST['txtfind'];
        if($tab=='1'){
            $str="select b.kodebarang,a.namabarang,a.satuan from ".$dbname.".log_5masterbarang a "
                    . "left join ".$dbname.".bgt_masterbarang b on a.kodebarang=b.kodebarang"
                    . " where tahunbudget='".$_POST['thnbgt']."' and regional='".$regional['regional']."' and hargasatuan!=0 and "
                    . " (b.kodebarang like '".$txtfind."%' "
                    . "or namabarang like '%".$txtfind."%') ";
        }else{
            $str="select * from ".$dbname.".log_5masterbarang where kodebarang like '".$awalan."%' and (namabarang like '%".$txtfind."%' or kodebarang like '%".$txtfind."%') ";
        }
        //echo $str;
        //exit("error:".$str);
	if($res=mysql_query($str))
	{
            echo"
            <fieldset>
            <legend>".$_SESSION['lang']['result']."</legend>
            <div style=\"overflow:auto; height:300px;\" >
            <table class=data cellspacing=1 cellpadding=2  border=0>
            <thead>
            <tr class=rowheader>
                <td class=firsttd>
                    ".substr($_SESSION['lang']['nomor'],0,2)."
                </td>
                <td>".$_SESSION['lang']['kodebarang']."</td>
                <td>".$_SESSION['lang']['namabarang']."</td>
                <td>".$_SESSION['lang']['satuan']."</td>
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