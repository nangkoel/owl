/* tutupBuku
 * Fungsi untuk melakukan proses tutup buku bulanan
 */
function listBarang() {
    var listPost = document.getElementById('listPosting');
    var param = "kodegudang="+getValue('kodeorg')+"&periode="+getValue('periode')+"&kodebarang="+getValue('kodebarang')+"&metode=getList";

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
    post_response_text('log_slave_rekalharga.php', param, respon);
}

function prosesRekalkulasi(row)
{
    if(document.getElementById('brg'+row)){
        if (row==1){
            if (confirm('Yang akan di-rekalkulasi hanya data yang salah (merah).\n\rAnda yakin melakukan proses rekalkulasi?')){
                document.getElementById('btnproses').disabled=true;
                kdbrg=document.getElementById('brg'+row).value;
                prosesRekalkulasiBrg(kdbrg,1,row);
            }
        } else {
            document.getElementById('btnproses').disabled=true;
            kdbrg=document.getElementById('brg'+row).value;
            prosesRekalkulasiBrg(kdbrg,1,row);
        }
    } else {
        alert('Done');
        document.getElementById('btnList').focus();
    }
}

function prosesRekalkulasiBrg(brg,row,rowbrg)
{
    tipe        =document.getElementById('tipe'+brg+row).innerHTML;
    notransaksi =document.getElementById('notransaksi'+brg+row).innerHTML;
    harat       =document.getElementById('harat'+brg+row).innerHTML;
    nilai       =document.getElementById('nilai'+brg+row).innerHTML;
    harat2      =document.getElementById('harat2'+brg+row).innerHTML;
    nilai2      =document.getElementById('nilai2'+brg+row).innerHTML;
    nopo        =document.getElementById('nopo'+brg+row).value;
    nopp        =document.getElementById('nopp'+brg+row).value;
    notrx       =document.getElementById('notrx'+brg+row).value;

    param='kodegudang='+getValue('kodeorg')+'&periode='+getValue('periode')+
          '&notransaksi='+notransaksi+'&tipe='+tipe+'&kodebarang='+brg+'&harat='+harat+'&nilai='+nilai+'&harat2='+harat2+'&nilai2='+nilai2+'&nopo='+nopo+'&nopp='+nopp+'&notrx='+notrx;
    tujuan='log_slave_rekalharga.php';
    post_response_text(tujuan, param, respon);
    document.getElementById('row'+brg+row).style.backgroundColor='orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row'+brg+row).style.backgroundColor='red';
                } else {
                    document.getElementById('row'+brg+row).style.display='none';
                    try{
                        x=row+1;
                        if(document.getElementById('row'+brg+x))
                        {   
                             row=x;
                             prosesRekalkulasiBrg(brg,row,rowbrg);
                        }
                        else
                        {
                            if (document.getElementById('saldo'+brg+row)){
                                updateNextSaldo(brg,row,rowbrg);
                            } else {
                                xx=rowbrg+1;
                                prosesRekalkulasi(xx);
                            }
                        }
                    }
                    catch(e)
                    {
                        alert('Done');
//                        document.getElementById('btnList').focus();
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }     
}

function updateNextSaldo(brg,row,rowbrg)
{
    saldo       =document.getElementById('saldo'+brg+row).innerHTML;
    harat2      =document.getElementById('harat2'+brg+row).innerHTML;

    param='kodegudang='+getValue('kodeorg')+'&periode='+getValue('periode')+
          '&kodebarang='+brg+'&saldo='+saldo+'&harat2='+harat2+"&metode=updateSaldo";
    tujuan='log_slave_rekalharga.php';
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row'+brg+row).style.backgroundColor='red';
                } else {
                    document.getElementById('row'+brg+row).style.display='none';
                        xx=rowbrg+1;
                        prosesRekalkulasi(xx);
//                    try{
//                        xx=rowbrg+1;
//                        prosesRekalkulasi(xx);
//                    }
//                    catch(e)
//                    {
//                        alert('Done');
//                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }     
}
