// JavaScript Document

function loadData()
{
    per=document.getElementById('optper').value;
    nmkar=document.getElementById('nmKar').value;
    param='method=loadData&persch='+per+'&nmkar='+nmkar;
    tujuan='kebun_slave_5nilaipemanen';
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
    periode=document.getElementById('periode').value;
    nilai=document.getElementById('nilai').value;
    method=trim(document.getElementById('method').value);
    param='karyawanid='+karyawanid+'&periode='+periode+'&nilai='+nilai+'&method='+method;
   
    tujuan = 'kebun_slave_5nilaipemanen.php';
    post_response_text(tujuan, param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }else {
                    alert(con.responseText);
                    cancelIsi();
                    loadData();
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

function fillField(karyawanid,periode,nilai)
{
    document.getElementById('karyawanid').value=karyawanid;
    document.getElementById('karyawanid').disabled=true;
    document.getElementById('periode').value=periode;
    document.getElementById('periode').disabled=true;
    document.getElementById('nilai').value=nilai;
    document.getElementById('method').value="update";
}

function cancelIsi()
{
    document.getElementById('karyawanid').value='';
    document.getElementById('karyawanid').disabled=false;
    document.getElementById('periode').value='';
    document.getElementById('periode').disabled=false;
    document.getElementById('nilai').value='';
    document.getElementById('nmKar').value='';
    document.getElementById('method').value="insert";
}

function del(karyawanid,periode)
{
    bhs=document.getElementById('bhs').value;
    param='method=delete'+'&karyawanid='+karyawanid+'&periode='+periode;
    //alert(param);
    tujuan='kebun_slave_5nilaipemanen.php';
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

function validat(ev)
{
  key=getKey(ev);
  if(key==13){
        loadData();
  } else {
  return tanpa_kutip(ev);	
  }	
}

