<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');
echo open_body();
?>
<script language=javascript1.2 src=js/sdm_payrollHO.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
include('master_mainMenu.php');
    $ptkp=Array();
    $str="select id,value from ".$dbname.".sdm_ho_pph21_ptkp";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
        $ptkp[$bar->id]=$bar->value;
    } 
OPEN_BOX('','<b>PPh21 FORMULA</b>');
        echo"<div id=EList>";
        echo OPEN_THEME('Formula Form:')."<br>";
$hfrm[0]='PTKP/Tahun Form';
$hfrm[1]='Kontribusi/Tahun';
$hfrm[2]='Biaya Jabatan';

$frm[0]="<br>PTKP S &nbsp &nbsp:Rp.<input type=text size=12 maxlength=12 id=ptkps class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='". $ptkp['T']."'> ( Single )<br>
         PTKP K/0 :Rp.<input type=text size=12 maxlength=12 id=ptkp0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='". $ptkp['0']."'> ( Tanpa Anak )<br>
                 PTKP K/1 :Rp.<input type=text size=12 maxlength=12 id=ptkp1 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='". $ptkp['1']."'> ( Anak Satu )<br>
                 PTKP K/2 :Rp.<input type=text size=12 maxlength=12 id=ptkp2 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='". $ptkp['2']."'> ( Anak Dua )<br>
                 PTKP K/3 :Rp.<input type=text size=12 maxlength=12 id=ptkp3 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='". $ptkp['3']."'> ( Anak Tiga )<br>
        <br>
                <button class=mybutton onclick=savePTKP()>".$_SESSION['lang']['save']."</button>
                ";

$frm[1]="<fieldset><legend>[Info]</legend> 
                                        '<': Sampai dengan,<br>
                    '>': Lebih dari,<br>
                                        Jika hanya 4 level, level ke-5 di beri nilai nol.
                 </fieldset><br>
         Level 1:<select id=sign0><option value='<'><</option></select> Rp.<input type=text size=12 maxlength=14 id=range0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='50,000,000'> Tarif<input type=text size=5 maxlength=4 id=percent0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" value=5>%<br>
         Level 2:<select id=sign1><option value='<'><</option></select> Rp.<input type=text size=12 maxlength=14 id=range1 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='250,000,000'> Tarif<input type=text size=5 maxlength=4 id=percent1 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" value=15>%<br>
                 Level 3:<select id=sign2><option value='<'><</option></select> Rp.<input type=text size=12 maxlength=14 id=range2 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='500,000,000'> Tarif<input type=text size=5 maxlength=4 id=percent2 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" value=25>%<br>
                 Level 4:<select id=sign3><option value='>'>></option><option value='<'><</option></select> Rp.<input type=text size=12 maxlength=14 id=range3 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='500,000,000'> Tarif<input type=text size=5 maxlength=4 id=percent3 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" value=30>%<br>
                 Level 5:<select id=sign4><option value='>'>></option></select> Rp.<input type=text size=12 maxlength=12 id=range4 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='0'> Tarif<input type=text size=5 maxlength=4 id=percent4 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" value=0>%<br>
        <br>
                <button class=mybutton onclick=saveKontribusi()>".$_SESSION['lang']['save']."</button>
                ";
$frm[2]="<fieldset>
           <legend>PPh21 Biaya Jabatan</legend>
                   <fieldset><legend>Info</legend>
                   Biaya jabatan dalam hal ini tidak mengacu kepada komponen gaji. Walaupun di dalam komponen gaji ada Biaya jabatan, akan dianggap sebagai komponen biasa oleh program
                   kecuali diatur pada Komponen PPh21
                   </fieldset>
                   Porsi <input type=text id=persen  onblur=change_number(this) value=5 size=5 maxlength=4 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">% <br>
                   Max.<input type=text id=max  onblur=change_number(this) size=15 maxlength=15 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">(Rp.)/bulan
           <br><button class=mybutton onclick=savePph21ByJabatan()>".$_SESSION['lang']['save']."</button>
                 </fieldset>
        ";		
drawTab('FRM',$hfrm,$frm,150,600);  	  			 
        echo"</div>";
        echo CLOSE_THEME();		
        CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>