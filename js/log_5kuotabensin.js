// JavaScript Document

function loadData()
{
    param='method=loadData';
    tujuan='log_slave_5kuotabensin';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function simpan() {
    karyawanid=document.getElementById('karyawanid').value;
    jumlahkuota=document.getElementById('jumlahkuota').value;
    method=trim(document.getElementById('method').value);
    param='karyawanid='+karyawanid+'&jumlahkuota='+jumlahkuota+'&method='+method;
   
    tujuan = 'log_slave_5kuotabensin.php';
    post_response_text(tujuan, param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }else {
                    alert(con.responseText);
                    loadData();
                    cancelIsi();
                }
                
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(fileTarget+'.php', param, respon);
}

function batal()
{
    cancelIsi();
}

function fillField(karyawanid,jumlahkuota)
{
    document.getElementById('karyawanid').value=karyawanid;
    document.getElementById('karyawanid').disabled=true;
    document.getElementById('jumlahkuota').value=jumlahkuota;
    document.getElementById('method').value="update";
}

function cancelIsi()
{
    document.getElementById('karyawanid').value='';
    document.getElementById('karyawanid').disabled=false;
    document.getElementById('jumlahkuota').value='';
    document.getElementById('method').value="insert";
}

function del(karyawanid,jumlahkuota)
{
    bhs=document.getElementById('bhs').value;
    param='method=delete'+'&karyawanid='+karyawanid+'&jumlahkuota='+jumlahkuota;
    //alert(param);
    tujuan='log_slave_5kuotabensin.php';
    if(bhs=='ID'){
        if(confirm('Anda yakin akan menghapus karyawan '+karyawanid+' dari daftar?'))
        {
             post_response_text(tujuan, param, respog);
        }        
    }else{
        if(confirm('Are you sure to delete the employee '+karyawanid+' from list?'))
        {
             post_response_text(tujuan, param, respog);
        }
    }
    
    function respog()
    {
        if(con.readyState==4)
        {
              if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else 
                    {
                        alert(con.responseText);                       
                        loadData();
                    }
              }
              else {
                    busy_off();
                    error_catch(con.status);
              }
        }	
    }
}
