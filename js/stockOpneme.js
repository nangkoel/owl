/**
 * @author repindra.ginting
 */

function searchBarang(title,content,ev)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        //alert('asdasd');
}

function findBarang()
{
        txt=trim(document.getElementById('namabrg').value);
        if(txt=='')
        {
                alert('Text is obligatory');
        }
        else
        {
                param='txtfind='+txt;
                tujuan='log_slave_get_barang.php';
                post_response_text(tujuan, param, respog);
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
                                                else {
                                                        //alert(con.responseText);
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

function setKodeBarang(kelompok,kode,nama,satuan)
{
         document.getElementById('namadisabled').value=nama;
         document.getElementById('sat').innerHTML=satuan;
         document.getElementById('kodebarang').innerHTML=kode;
         closeDialog();
}

function saveAdjustment(){
    kodebarang=document.getElementById('kodebarang').innerHTML;
    kodegudang=document.getElementById('kodegudang').options[document.getElementById('kodegudang').selectedIndex].value;
    jumlah=document.getElementById('jumlah').value;
    harga=document.getElementById('harga').value;
    if(harga=='' || harga=='0'){
        harga=1;
    }
    if(jumlah==''){
        jumlah=0;
    }
    if(!kodegudang || kodebarang==''){
        alert('Data incomplete');
    }else{
        param='kodebarang='+kodebarang+'&kodegudang='+kodegudang+'&harga='+harga+'&jumlah='+jumlah;
        tujuan='log_slave_stockOpname.php';
        if(confirm('Update material balance..?')){
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
                                                else {
                                                            alert('Done');
                                                            document.getElementById('namadisabled').value='';
                                                            document.getElementById('sat').innerHTML='';
                                                            document.getElementById('kodebarang').innerHTML='';
                                                            document.getElementById('jumlah').value=0;
                                                            document.getElementById('harga').value=0;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}
