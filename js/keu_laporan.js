/**
 * @author repindra.ginting
 */
function switchHidden(id)
{
    status=document.getElementById(id).style.display;
    if(status=='none'){
        document.getElementById(id).style.display='block';
    }else{
        document.getElementById(id).style.display='none';
    }
}

function getLaporanKeuangan()
{
    pt	=document.getElementById('pt');
    unit    =document.getElementById('gudang');
    periode =document.getElementById('periode');
    pt	=pt.options[pt.selectedIndex].value;
    unit	=unit.options[unit.selectedIndex].value;
    periode	=periode.options[periode.selectedIndex].value;

    param='pt='+pt+'&unit='+unit+'&periode='+periode;
    tujuan='keu_slave_2laporankeuangan.php';
    post_response_text(tujuan, param, respog);
    
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    showById('printPanel');
                    document.getElementById('container').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
} 
 
 
function getLaporanJurnal()
{
            pt=document.getElementById('pt');
            gudang  =document.getElementById('gudang');
            periode =document.getElementById('periode');
            periodeV=periode.options[periode.selectedIndex].value;
            periode1 =document.getElementById('periode1');
            periode1=periode1.options[periode1.selectedIndex].value;            
            ptV         =pt.options[pt.selectedIndex].value;
            gudangV	=gudang.options[gudang.selectedIndex].value;
            revisi =document.getElementById('revisi');
            revisi=revisi.options[revisi.selectedIndex].value;  
            
        param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV+'&periode1='+periode1+'&revisi='+revisi;
        tujuan='keu_laporanJurnal.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}
function getLaporanJurnalPiutangKaryawan()
{
        tanggalmulai=document.getElementById('tanggalmulai');
        tanggalsampai=document.getElementById('tanggalsampai');
        noakun  =document.getElementById('noakun');
                    kodeorg  =document.getElementById('kodeorg');
        tanggalmulaiV         =tanggalmulai.value;
        tanggalsampaiV         =tanggalsampai.value;
        noakunV	=noakun.options[noakun.selectedIndex].value;
                    kodeorgV=kodeorg.options[kodeorg.selectedIndex].value;
        param='tanggalmulai='+tanggalmulaiV+'&tanggalsampai='+tanggalsampaiV+'&noakun='+noakunV+'&kodeorg='+kodeorgV;
        tujuan='keu_laporanJurnalPiutangKaryawan.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}
function getUsiaHutang()
{
        pt=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
                ptV		=pt.options[pt.selectedIndex].value;
                gudangV	=gudang.options[gudang.selectedIndex].value;
                periodeV	=periode.options[periode.selectedIndex].value;

        param='pt='+ptV+'&gudang='+gudangV+'&periodeV='+periodeV;
        tujuan='keu_laporanUsiaHutang.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}
function getLaporanBukuBesar()
{
        pt		=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
        periode1 =document.getElementById('periode1');
                ptV	=pt.options[pt.selectedIndex].value;
                gudangV	=gudang.options[gudang.selectedIndex].value;
                periodeV	=periode.options[periode.selectedIndex].value;
                periodeV1	=periode1.options[periode1.selectedIndex].value;
            revisi =document.getElementById('revisi');
            revisi=revisi.options[revisi.selectedIndex].value;  
        param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV+'&periode1='+periodeV1+'&revisi='+revisi;
        tujuan='keu_slave_2bukubesar.php';
        
        if(ptV==''){
            alert('Please fill PT...');
        }else      
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}
function getLaporanNeracaCoba()
{
        pt		=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
                ptV		=pt.options[pt.selectedIndex].value;
                gudangV	=gudang.options[gudang.selectedIndex].value;
                periodeV	=periode.options[periode.selectedIndex].value;

        param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
        tujuan='keu_laporanNeracaCoba.php';
        post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}
function getMesinLaporan()
{
        pt		=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
                ptV		=pt.options[pt.selectedIndex].value;
                gudangV	=gudang.options[gudang.selectedIndex].value;
                periodeV	=periode.options[periode.selectedIndex].value;

        param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
        tujuan='keu_slave_2mesinlaporan.php';
        post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}
function getLaporanNeraca()
{
        pt	=document.getElementById('pt');
        unit    =document.getElementById('gudang');
        periode =document.getElementById('periode');
        periode1 =document.getElementById('periode1');
        pt	=pt.options[pt.selectedIndex].value;
        unit	=unit.options[unit.selectedIndex].value;
        periode	=periode.options[periode.selectedIndex].value;
        periode1	=periode1.options[periode1.selectedIndex].value;       
            revisi =document.getElementById('revisi');
            revisi=revisi.options[revisi.selectedIndex].value;       
        param='pt='+pt+'&unit='+unit+'&periode='+periode+'&periode1='+periode1+'&revisi='+revisi;
        tujuan='keu_slave_2neraca.php';
        post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}

function getLaporanNeracaPeriodik()
{
        pt	=document.getElementById('pt');
        unit    =document.getElementById('gudang');
        periode =document.getElementById('periode');
        pt	=pt.options[pt.selectedIndex].value;
        unit	=unit.options[unit.selectedIndex].value;
        periode	=periode.options[periode.selectedIndex].value;

        param='pt='+pt+'&unit='+unit+'&periode='+periode;
        tujuan='keu_slave_2neracaPeriodik.php';
        post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}


function getLaporanRugiLaba()
{
        pt	=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
        pt	=pt.options[pt.selectedIndex].value;
        gudang	=gudang.options[gudang.selectedIndex].value;
        periode	=periode.options[periode.selectedIndex].value;

        param='pt='+pt+'&gudang='+gudang+'&periode='+periode;
        tujuan='keu_slave_2rugilaba.php';
        post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}

function getLaporanRugiLabaPeriodik()
{
        pt	=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
        pt	=pt.options[pt.selectedIndex].value;
        gudang	=gudang.options[gudang.selectedIndex].value;
        periode	=periode.options[periode.selectedIndex].value;

        param='pt='+pt+'&gudang='+gudang+'&periode='+periode;
        tujuan='keu_slave_2rugilabaperiodik.php';
        post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}

function getLaporanArusKas()
{
        pt		=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
                ptV		=pt.options[pt.selectedIndex].value;
                gudangV	=gudang.options[gudang.selectedIndex].value;
                periodeV	=periode.options[periode.selectedIndex].value;

        param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
        tujuan='keu_slave_2aruskas.php';
        post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}
function getLaporanArusKasLangsung()
{
        pt		=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
                ptV		=pt.options[pt.selectedIndex].value;
                gudangV	=gudang.options[gudang.selectedIndex].value;
                periodeV	=periode.options[periode.selectedIndex].value;

        param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
        tujuan='keu_slave_2aruskasLangsung.php';
        post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}
function fisikKeExcel(ev,tujuan)
{
        pt	=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
        revisi='';
        try{
        periode1 =document.getElementById('periode1').options[document.getElementById('periode1').selectedIndex].value;
        revisi =document.getElementById('revisi');
        revisi=revisi.options[revisi.selectedIndex].value;  
        }
        catch(err){
          periode1='';  
        }	
                pt	=pt.options[pt.selectedIndex].value;
                gudang	=gudang.options[gudang.selectedIndex].value;
                periode	=periode.options[periode.selectedIndex].value;


        judul='Report Ms.Excel';	
        param='pt='+pt+'&gudang='+gudang+'&periode='+periode+'&periode1='+periode1+'&revisi='+revisi;
        printFile(param,tujuan,judul,ev)	
}

function piutangKaryawanKeExcel(ev,tujuan)
{
        tanggalmulai	=document.getElementById('tanggalmulai');
        tanggalsampai	=document.getElementById('tanggalsampai');
        noakun  =document.getElementById('noakun');
                    kodeorg  =document.getElementById('kodeorg');
        namakaryawan =document.getElementById('namakaryawan');
        tanggalmulaiV	=tanggalmulai.value;
        tanggalsampaiV	=tanggalsampai.value;
        noakunV	=noakun.options[noakun.selectedIndex].value;
                    kodeorgV	=kodeorg.options[kodeorg.selectedIndex].value;

        judul='Report Ms.Excel';	
        param='tanggalmulai='+tanggalmulaiV+'&tanggalsampai='+tanggalsampaiV+'&noakun='+noakunV+'&kodeorg='+kodeorgV; 
        printFile(param,tujuan,judul,ev)	
}

function piutangKaryawanKeExcel2(ev,tujuan)
{
        tanggalmulai	=document.getElementById('tanggalmulai');
        tanggalsampai	=document.getElementById('tanggalsampai');
        noakun  =document.getElementById('noakun');
                    kodeorg  =document.getElementById('kodeorg');
        namakaryawan =document.getElementById('namakaryawan');
        tanggalmulaiV	=tanggalmulai.value;
        tanggalsampaiV	=tanggalsampai.value;
        noakunV	=noakun.options[noakun.selectedIndex].value;
                    kodeorgV	=kodeorg.options[kodeorg.selectedIndex].value;

        judul='Report Ms.Excel Detail';	
        param='tanggalmulai='+tanggalmulaiV+'&tanggalsampai='+tanggalsampaiV+'&noakun='+noakunV+'&kodeorg='+kodeorgV; 
        printFile(param,tujuan,judul,ev)	
}

function piutangKaryawanKePDF(ev,tujuan)
{
        tanggalmulai	=document.getElementById('tanggalmulai');
        tanggalsampai	=document.getElementById('tanggalsampai');
        noakun  =document.getElementById('noakun');
        namakaryawan =document.getElementById('namakaryawan');
                tanggalmulaiV	=tanggalmulai.value;
                tanggalsampaiV	=tanggalsampai.value;
                noakunV	=noakun.options[noakun.selectedIndex].value;
                namakaryawanV	=namakaryawan.options[namakaryawan.selectedIndex].value;
        judul='Report PDF';	
        param='tanggalmulai='+tanggalmulaiV+'&tanggalsampai='+tanggalsampaiV+'&noakun='+noakunV+'&namakaryawan='+namakaryawanV;
        printFile(param,tujuan,judul,ev)	
}

function detailMutasiBarang(ev,pt,periode,gudang,kodebarang,namabarang,satuan)
{
        tujuan='log_laporanMutasiDetailPerBarang_pdf.php';
        judul='Report PDF';	
        param='pt='+pt+'&gudang='+gudang+'&periode='+periode+'&namabarang='+namabarang+'&satuan='+satuan+'&kodebarang='+kodebarang;
        printFile(param,tujuan,judul,ev);	
}
function detailMutasiBarangHarga(ev,pt,periode,gudang,kodebarang,namabarang,satuan)
{
        tujuan='log_laporanMutasiDetailPerBarangHarga_pdf.php';
        judul='Report PDF';	
        param='pt='+pt+'&gudang='+gudang+'&periode='+periode+'&namabarang='+namabarang+'&satuan='+satuan+'&kodebarang='+kodebarang;
        printFile(param,tujuan,judul,ev);	
}

function fisikKePDF(ev,tujuan)
{
        pt		=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
        revisi='';
        try{
        periode1 =document.getElementById('periode1').options[document.getElementById('periode1').selectedIndex].value;
                revisi =document.getElementById('revisi');
        revisi=revisi.options[revisi.selectedIndex].value;  
        }
        catch(err){
          periode1='';  
        }
                pt		=pt.options[pt.selectedIndex].value;
                gudang	=gudang.options[gudang.selectedIndex].value;
                periode	=periode.options[periode.selectedIndex].value;
        judul='Report PDF';	
        param='pt='+pt+'&gudang='+gudang+'&periode='+periode+'&periode1='+periode1+'&revisi='+revisi;
        printFile(param,tujuan,judul,ev)	
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='900';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function getLaporanFisikHarga()
{
        pt		=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
                pt		=pt.options[pt.selectedIndex].value;
                gudang	=gudang.options[gudang.selectedIndex].value;
                periode	=periode.options[periode.selectedIndex].value;
        document.getElementById('orglegend').innerHTML= pt+"-"+gudang;		
        param='pt='+pt+'&gudang='+gudang+'&periode='+periode;
        tujuan='log_laporanPersediaanFisikHarga.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}

function ambilAnak(pt)
{
        param='pt='+pt;
        tujuan='keu_slave_getUnit.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('gudang').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	

}

function ambilAnakBB(pt) // list untuk buku besar, lihat tipe lokasi tugas
{
        param='pt='+pt+'&tipe=bb';
        tujuan='keu_slave_getUnit.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('gudang').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	

}

function ambilAnakPA(pt) // list untuk periode akuntansi, lihat tipe lokasi tugas
{
    param='pt='+pt+'&tipe=pa';
    tujuan='keu_slave_getUnit.php';
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('kodeunit').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	

}



function ambilAkun2(akun)
{
        param='pam=1&akun='+akun;
        tujuan='keu_slave_getAkun2.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('akunsampai').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	

}

function cekTanggal1(tanggal1)
{
        tanggal2 =document.getElementById('tgl2').value;
        param='pam=2&tanggal1='+tanggal1+'&tanggal2='+tanggal2;
//	param='pam=2&tanggal1='+tanggal1;
        tujuan='keu_slave_getAkun2.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert(con.responseText);
                                                document.getElementById('tgl1').value="";
                                        }
                                        else {
//						document.getElementById('akunsampai').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}

function cekTanggal2(tanggal2)
{
        tanggal1 =document.getElementById('tgl1').value;
        param='pam=3&tanggal1='+tanggal1+'&tanggal2='+tanggal2;
        tujuan='keu_slave_getAkun2.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert(con.responseText);
                                                document.getElementById('tgl2').value="";
                                        }
                                        else {
//						document.getElementById('akunsampai').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}

function getLaporanBukuBesarv1()
{
        pt =document.getElementById('pt');
        gudang =document.getElementById('gudang');
        tanggal1 =document.getElementById('tgl1');
        tanggal2 =document.getElementById('tgl2');
        akundari =document.getElementById('akundari');
        akunsampai  =document.getElementById('akunsampai');
                ptV =pt.options[pt.selectedIndex].value;
                gudangV	=gudang.options[gudang.selectedIndex].value;
                tanggal1V	=tanggal1.value;
                tanggal2V	=tanggal2.value;
                akundariV	=akundari.options[akundari.selectedIndex].value;
                akunsampaiV	=akunsampai.options[akunsampai.selectedIndex].value;

        param='pt='+ptV+'&gudang='+gudangV+'&tanggal1='+tanggal1V+'&tanggal2='+tanggal2V+'&akundari='+akundariV+'&akunsampai='+akunsampaiV;
//alert(param);
tujuan='keu_slave_2bukubesarv1.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}

function getLaporanCatatanNeraca()
{
        pt =document.getElementById('pt');
        periode =document.getElementById('periode');
        akundari =document.getElementById('akundari');
        akunsampai  =document.getElementById('akunsampai');
                ptV =pt.options[pt.selectedIndex].value;
                periodeV	=periode.options[periode.selectedIndex].value;
                akundariV	=akundari.options[akundari.selectedIndex].value;
                akunsampaiV	=akunsampai.options[akunsampai.selectedIndex].value;

        param='pt='+ptV+'&periode='+periodeV+'&akundari='+akundariV+'&akunsampai='+akunsampaiV;
//alert(param);
tujuan='keu_slave_2catatanNeraca.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
                                                document.getElementById('container').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}

function showDetail(nourut,keterangan,ev)
{
        judul='Detail '+keterangan;	
        param='nourut='+nourut;
        printFile(param,'keu_slave_2neraca_detail.php',judul,ev)	
}

function lihatDetail(noakun,periode,periode1,lmperiode,pt,gudang,revisi,ev)
{
   param='noakun='+noakun+'&periode='+periode+'&periode1='+periode1;
   param+='&lmperiode='+lmperiode+'&pt='+pt+'&gudang='+gudang+'&revisi='+revisi;
   tujuan='keu_slave_getBBDetail.php'+"?"+param;  
   width='800';
   height='400';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Jurnal '+noakun,content,width,height,ev); 
}


function detailKeExcel(ev,tujuan)
{
    width='700';
   height='400';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Jurnal',content,width,height,ev); 
}

function jurnalv1KeExcel(ev,tujuan)
{
        pt =document.getElementById('pt');
        gudang =document.getElementById('gudang');
        tanggal1 =document.getElementById('tgl1');
        tanggal2 =document.getElementById('tgl2');
        akundari =document.getElementById('akundari');
        akunsampai  =document.getElementById('akunsampai');
                ptV =pt.options[pt.selectedIndex].value;
                gudangV	=gudang.options[gudang.selectedIndex].value;
                tanggal1V	=tanggal1.value;
                tanggal2V	=tanggal2.value;
                akundariV	=akundari.options[akundari.selectedIndex].value;
                akunsampaiV	=akunsampai.options[akunsampai.selectedIndex].value;

        param='pt='+ptV+'&gudang='+gudangV+'&tanggal1='+tanggal1V+'&tanggal2='+tanggal2V+'&akundari='+akundariV+'&akunsampai='+akunsampaiV;
//alert(param);                

        judul='Report Ms.Excel';	
        printFile(param,tujuan,judul,ev)	
}

function catatanNeracaKeExcel(ev,tujuan)
{
        pt =document.getElementById('pt');
        periode =document.getElementById('periode');
        akundari =document.getElementById('akundari');
        akunsampai  =document.getElementById('akunsampai');
                ptV =pt.options[pt.selectedIndex].value;
                periodeV	=periode.options[periode.selectedIndex].value;
                akundariV	=akundari.options[akundari.selectedIndex].value;
                akunsampaiV	=akunsampai.options[akunsampai.selectedIndex].value;

        param='pt='+ptV+'&periode='+periodeV+'&akundari='+akundariV+'&akunsampai='+akunsampaiV;
//alert(param);                

        judul='Report Ms.Excel';	
        printFile(param,tujuan,judul,ev)	
}

function catatanNeracaKePDF(ev,tujuan)
{
        pt =document.getElementById('pt');
        periode =document.getElementById('periode');
        akundari =document.getElementById('akundari');
        akunsampai  =document.getElementById('akunsampai');
                ptV =pt.options[pt.selectedIndex].value;
                periodeV	=periode.options[periode.selectedIndex].value;
                akundariV	=akundari.options[akundari.selectedIndex].value;
                akunsampaiV	=akunsampai.options[akunsampai.selectedIndex].value;

        param='pt='+ptV+'&periode='+periodeV+'&akundari='+akundariV+'&akunsampai='+akunsampaiV;
//alert(param);                

        judul='Report PDF';	
        printFile(param,tujuan,judul,ev)	
}

function jurnalv1KePDF(ev,tujuan)
{
        pt =document.getElementById('pt');
        gudang =document.getElementById('gudang');
        tanggal1 =document.getElementById('tgl1');
        tanggal2 =document.getElementById('tgl2');
        akundari =document.getElementById('akundari');
        akunsampai  =document.getElementById('akunsampai');
                ptV =pt.options[pt.selectedIndex].value;
                gudangV	=gudang.options[gudang.selectedIndex].value;
                tanggal1V	=tanggal1.value;
                tanggal2V	=tanggal2.value;
                akundariV	=akundari.options[akundari.selectedIndex].value;
                akunsampaiV	=akunsampai.options[akunsampai.selectedIndex].value;

        param='pt='+ptV+'&gudang='+gudangV+'&tanggal1='+tanggal1V+'&tanggal2='+tanggal2V+'&akundari='+akundariV+'&akunsampai='+akunsampaiV;
//alert(param);                

        judul='Report PDF';	
        printFile(param,tujuan,judul,ev)	
}

function ambilJurnal()
{
    unit =document.getElementById('unit');
    unitV =unit.options[unit.selectedIndex].value;
    periode =document.getElementById('periode');
    periodeV =periode.options[periode.selectedIndex].value;
    param='pam=1&unit='+unitV+'&periode='+periodeV;
    tujuan='keu_slave_getJurnal.php';
    if((unitV!='')&&(periodeV!=''))
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('jurnaldari').innerHTML=con.responseText;
                    document.getElementById('jurnalsampai').innerHTML=con.responseText;
    hideById('printPanel');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	

}

function getLaporanPeriksaJurnal()
{
    unit =document.getElementById('unit');
    periode =document.getElementById('periode');
    jurnaldari =document.getElementById('jurnaldari');
    jurnalsampai =document.getElementById('jurnalsampai');
    unitV =unit.options[unit.selectedIndex].value;
    periodeV =periode.options[periode.selectedIndex].value;
    jurnaldariV	=jurnaldari.options[jurnaldari.selectedIndex].value;
    jurnalsampaiV =jurnalsampai.options[jurnalsampai.selectedIndex].value;

    param='unit='+unitV+'&periode='+periodeV+'&jurnaldari='+jurnaldariV+'&jurnalsampai='+jurnalsampaiV;
//alert(param);
    tujuan='keu_slave_2periksaJurnal.php';
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    showById('printPanel');
                    document.getElementById('container').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function periksajurnalKeExcel(ev,tujuan)
{
    unit =document.getElementById('unit');
    periode =document.getElementById('periode');
    jurnaldari =document.getElementById('jurnaldari');
    jurnalsampai =document.getElementById('jurnalsampai');
    unitV =unit.options[unit.selectedIndex].value;
    periodeV =periode.options[periode.selectedIndex].value;
    jurnaldariV	=jurnaldari.options[jurnaldari.selectedIndex].value;
    jurnalsampaiV =jurnalsampai.options[jurnalsampai.selectedIndex].value;

    param='unit='+unitV+'&periode='+periodeV+'&jurnaldari='+jurnaldariV+'&jurnalsampai='+jurnalsampaiV;
//alert(param);                

    judul='Report Ms.Excel';	
    printFile(param,tujuan,judul,ev)	
}

function periksajurnalKePDF(ev,tujuan)
{
    unit =document.getElementById('unit');
    periode =document.getElementById('periode');
    jurnaldari =document.getElementById('jurnaldari');
    jurnalsampai =document.getElementById('jurnalsampai');
    unitV =unit.options[unit.selectedIndex].value;
    periodeV =periode.options[periode.selectedIndex].value;
    jurnaldariV	=jurnaldari.options[jurnaldari.selectedIndex].value;
    jurnalsampaiV =jurnalsampai.options[jurnalsampai.selectedIndex].value;

    param='unit='+unitV+'&periode='+periodeV+'&jurnaldari='+jurnaldariV+'&jurnalsampai='+jurnalsampaiV;
//alert(param);                

    judul='Report Ms.Excel';	
    printFile(param,tujuan,judul,ev)	
}

function lihatDetailHutang(kodesupplier,noakun,mulai,sampai,kodeorg,nama,ev){
   param='noakun='+noakun+'&mulai='+mulai+'&sampai='+sampai+'&kodesupplier='+kodesupplier+'&kodeorg='+kodeorg;
   tujuan='keu_slave_laporan_HutangPiutang.php'+"?"+param;  
   width='1000';
   height='400';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Jurnal '+noakun+ ' for '+nama,content,width,height,ev);     
}

function getPeriodeAkuntansi()
{
    kodept=document.getElementById('kodept');
    kodeptV=kodept.options[kodept.selectedIndex].value;
    kodeunit=document.getElementById('kodeunit');
    kodeunitV=kodeunit.options[kodeunit.selectedIndex].value;

    param='kodept='+kodeptV+'&kodeunit='+kodeunitV;
    tujuan='keu_slave_2periodeAkuntansi.php';
    
    if(kodeptV==''){
        alert('Please choose...');
    }
    else
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
//						showById('printPanel');
                    document.getElementById('container').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}
function getPeriodeRev(){
    revId=document.getElementById('revisi');
    revId=revId.options[revId.selectedIndex].value;
    
        pete=document.getElementById('pt');
        pete=pete.options[pete.selectedIndex].value;
        unit=document.getElementById('gudang');
        unit=unit.options[unit.selectedIndex].value;
        param='proses=getPeriodeRev'+'&revisi='+revId;
        param+='&pt='+pete+'&divisi='+unit;
        tujuan='keu_slave_2globalfungsi.php';
        
        function respog(){
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
    //						showById('printPanel');
                        document.getElementById('periode').innerHTML=con.responseText;
                        document.getElementById('periode1').innerHTML=con.responseText;
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }	
        post_response_text(tujuan, param, respog);	
    
    
}