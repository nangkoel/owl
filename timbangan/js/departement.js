/**
 * @author repindra.ginting
 */
function simpanDep()
{
        kode=document.getElementById('kode').value;
        nama=document.getElementById('nama').value;
        potongan=document.getElementById('potongan').value;
        satuan=document.getElementById('satuan').value;
        met=document.getElementById('method').value;
        if(trim(kode)=='')
        {
                alert('Code is empty');
                document.getElementById('kode').focus();
        }
        else
        {
                kode=trim(kode);
                nama=trim(nama);
                param='kode='+kode+'&nama='+nama+'&method='+met+'&potongan='+potongan+'&satuan='+satuan;
                tujuan='slave_fraksi.php';
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

function fillField(kode,nama,potongan,satuan)
{
        document.getElementById('kode').value=kode;
      document.getElementById('kode').disabled=true;
        document.getElementById('nama').value=nama;
        document.getElementById('satuan').value=satuan;
        document.getElementById('potongan').value=potongan;
        document.getElementById('method').value='update';
}

function cancelDep()
{
    document.getElementById('kode').disabled=false;
        document.getElementById('kode').value='';
        document.getElementById('nama').value='';
        document.getElementById('potongan').value='';
        document.getElementById('satuan').value='';
        document.getElementById('method').value='insert';		
}
