/* tutupBuku
 * Fungsi untuk melakukan proses tutup buku bulanan
 */
function tutupBuku() {
    var param = "kodeorg="+getValue('kodeorg')+"&periode="+getValue('periode');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert('Proses Tutup Buku berhasil');
                    logout();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Close this period for '+getValue('kodeorg')+
        '\n are you sure?')) {
        post_response_text('keu_slave_3tutupbulan.php?proses=tutupBuku', param, respon);
    }
}

function listAkun() {
    var listPost = document.getElementById('listPosting');
    var param = "kodeorg="+getValue('kodeorg')+"&periode="+getValue('periode')+"&metode=getList";

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    isi=con.responseText.split("####");
                    listPost.innerHTML = isi[0];
                    if (isi[1]=='salah'){
                        document.getElementById('btnproses').focus();
                    }else{
                        document.getElementById('periode').focus();
                        document.getElementById('btnproses').disabled=true;
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('keu_slave_rekalakun.php', param, respon);
}

function prosesRekalkulasi(row)
{
    document.getElementById('btnproses').disabled=true;
    noakun      =document.getElementById('noakun'+row).innerHTML;
    debet       =document.getElementById('debet'+row).innerHTML;
    debet2      =document.getElementById('debetprev'+row).innerHTML;
    kredit      =document.getElementById('kredit'+row).innerHTML;
    kredit2     =document.getElementById('kreditprev'+row).innerHTML;
    sakhir      =document.getElementById('akhir'+row).innerHTML;
    sakhir2     =document.getElementById('akhirprev'+row).innerHTML;

    param='kodeorg='+getValue('kodeorg')+'&periode='+getValue('periode')+
          '&noakun='+noakun+'&debet='+debet+'&debet2='+debet2+'&kredit='+kredit+'&kredit2='+kredit2+'&sakhir='+sakhir+'&sakhir2='+sakhir2;
    tujuan='keu_slave_rekalakun.php';
    if(row==1){
        if (confirm('Yang akan di-rekalkulasi hanya data yang salah (merah).\n\rAnda yakin melakukan proses rekalkulasi?')){
            post_response_text(tujuan, param, respon);
        }
    }else{
        post_response_text(tujuan, param, respon);
    }
    document.getElementById('row'+row).style.backgroundColor='orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row'+row).style.backgroundColor='red';
                } else {
                    document.getElementById('row'+row).style.display='none';
                    try{
                        x=row+1;
                        if(document.getElementById('row'+x))
                         {   
                             row=x;
                             prosesRekalkulasi(row);
                         }
                         else
                         {
                            alert('Done');
                            document.getElementById('periode').focus();
                         }
                    }
                    catch(e)
                    {
                        alert('Done');
                        document.getElementById('btnList').focus();
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }     
}
