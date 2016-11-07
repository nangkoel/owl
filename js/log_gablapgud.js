function getGudangDt()
{
    unt=document.getElementById('unitDt').options[document.getElementById('unitDt').selectedIndex].value;
    param='unitDt='+unt+'&proses=getAll';
    tujuan='log_slave_2getGabLapGdg.php';
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

function getPeriodeDt()
{
    gudang=document.getElementById('gudang').options[document.getElementById('gudang').selectedIndex].value;
    param='gudang='+gudang+'&proses=getAll';
    tujuan='log_slave_2getGabLapGdg.php';
    post_response_text(tujuan, param, respog);

            function respog(){
                    if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                    }
                                    else {
                                            document.getElementById('periode').innerHTML=con.responseText;
                                    }
                            }
                            else {
                                    busy_off();
                                    error_catch(con.status);
                            }
                    }
            }
}

function getBrg()
{
    klmpkBrg=document.getElementById('klmpkBrg').options[document.getElementById('klmpkBrg').selectedIndex].value;
    param='klmpkBrg='+klmpkBrg+'&proses=getAll';
    tujuan='log_slave_2getGabLapGdg.php';
    post_response_text(tujuan, param, respog);

            function respog(){
                    if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                    }
                                    else {
                                            document.getElementById('kdBrg').innerHTML=con.responseText;
                                    }
                            }
                            else {
                                    busy_off();
                                    error_catch(con.status);
                            }
                    }
            }
}

function getLaporanFisik2() {
    divisi = document.getElementById('unitDt');
    divisi = divisi.options[divisi.selectedIndex].value;
    //gudang = document.getElementById('gudang');
    //gudang = gudang.options[gudang.selectedIndex].value;
    periode= document.getElementById('periode');
    periode= periode.options[periode.selectedIndex].value;
    klmpkBrg = document.getElementById('klmpkBrg');
    klmpkBrg = klmpkBrg.options[klmpkBrg.selectedIndex].value;
    kdBrg = document.getElementById('kdBrg');
    kdBrg = kdBrg.options[kdBrg.selectedIndex].value;

    param = 'divisi='+divisi;
    param+= '&periode='+periode+'&klmpkBrg='+klmpkBrg;
    param+= '&kdBrg='+kdBrg+'&proses=preview';

    tujuan = 'log_slave_2getGabLapGdg.php';
    post_response_text(tujuan,param,respog)

    function respog() {
        if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                    }
                                    else {
                                            //showById('printPanel2');
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
function fisikKeExcel(ev,tujuan){
// tahunan
        divisi = document.getElementById('unitDt');
    divisi = divisi.options[divisi.selectedIndex].value;
    //gudang = document.getElementById('gudang');
    //gudang = gudang.options[gudang.selectedIndex].value;
    periode= document.getElementById('periode');
    periode= periode.options[periode.selectedIndex].value;
    klmpkBrg = document.getElementById('klmpkBrg'); 
    klmpkBrg = klmpkBrg.options[klmpkBrg.selectedIndex].value;
    kdBrg = document.getElementById('kdBrg');
    kdBrg = kdBrg.options[kdBrg.selectedIndex].value;
    judul='Report Ms.Excel';    
    param = 'divisi='+divisi;
    param+= '&periode='+periode+'&klmpkBrg='+klmpkBrg;
    param+= '&kdBrg='+kdBrg+'&proses=excel';
   // param+='&proses=excel';
    printFile(param,tujuan,judul,ev);   
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);  
}