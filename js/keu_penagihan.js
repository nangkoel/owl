function saveData(fileTarget,passParam) {
    var passP = passParam.split('##');
    var param = ""
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
	//alert(param);
  //alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
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

function displayFormInput(){
        clearData();
        param='proses=genNo';
	tujuan='keu_slave_penagihan';
        post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                        document.getElementById('formInput').style.display='block';
                        document.getElementById('listData').style.display='none';
                        document.getElementById('noinvoice').value=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getPage(){
    pg=document.getElementById('pages');
    pg=pg.options[pg.selectedIndex].value;
    paged=parseFloat(pg)-1;
    loadData(paged);
}
function cariData(pg){
    ntrs=document.getElementById('txtsearch').value;
    tglcr=document.getElementById('tgl_cari').value;
    param='proses=loadData'+'&page='+pg;
    if(ntrs!=''){
        param+='&noinvoice='+ntrs;
    }
    if(tglcr!=''){
        param+='&tanggalCr='+tglcr;
    }
    tujuan='keu_slave_penagihan.php';
    post_response_text(tujuan, param, respog);
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
                        isdt=con.responseText.split("####");
                        document.getElementById('formInput').style.display='none';
                        document.getElementById('listData').style.display='block';
                        document.getElementById('continerlist').innerHTML=isdt[0];
                        document.getElementById('footData').innerHTML=isdt[1];
                        
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }
     }
}
function loadData(page){
    ntrs=document.getElementById('txtsearch').value;
    tglcr=document.getElementById('tgl_cari').value;
    param='proses=loadData'+'&page='+page;
    if(ntrs!=''){
        param+='&noinvoice='+ntrs;
    }
    if(tglcr!=''){
        param+='&tanggalCr='+tglcr;
    }
    tujuan='keu_slave_penagihan.php';
    post_response_text(tujuan, param, respog);
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
                        isdt=con.responseText.split("####");
                        document.getElementById('formInput').style.display='none';
                        document.getElementById('listData').style.display='block';
                        document.getElementById('continerlist').innerHTML=isdt[0];
                        document.getElementById('footData').innerHTML=isdt[1];
                        clearData();
                        closeDialog();
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }
     }
}
function fillField(noinv){
    param='proses=getData'+'&noinvoice='+noinv;
    tujuan='keu_slave_penagihan.php';
    post_response_text(tujuan, param, respog);
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
                        document.getElementById('formInput').style.display='block';
                        document.getElementById('listData').style.display='none';
                        isis=con.responseText.split("###");
                        document.getElementById('noinvoice').value=isis[0];
                        document.getElementById('kodeorganisasi').value=isis[1];
                        document.getElementById('tanggal').value=isis[2];
                        document.getElementById('noorder').value=isis[3];
                        kdcst=document.getElementById('kodecustomer');
                        for(a=0;a<kdcst.length;a++){
                            if(kdcst.options[a].value==isis[4]){
                                    kdcst.options[a].selected=true;
                                }
                        }
                        document.getElementById('nilaiinvoice').value=isis[5];
                        document.getElementById('nilaippn').value=isis[6];
                        document.getElementById('jatuhtempo').value=isis[7];
                        document.getElementById('keterangan').value=isis[8];
                        byrke=document.getElementById('bayarke');
                        for(a=0;a<byrke.length;a++){
                            if(byrke.options[a].value==isis[9]){
                                    byrke.options[a].selected=true;
                                }
                        }
                        dbt=document.getElementById('debet');
                        for(a=0;a<dbt.length;a++){
                            if(dbt.options[a].value==isis[10]){
                                    dbt.options[a].selected=true;
                                }
                        }
                        kridit=document.getElementById('kredit');
                        for(a=0;a<kridit.length;a++){
                            if(kridit.options[a].value==isis[11]){
                                    kridit.options[a].selected=true;
                                }
                        }
                        document.getElementById('uangmuka').value=isis[12];
                        
                         
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }
     }
}
//jamhari
function searchNosibp(title,content,ev){
	width='400';
	height='520';
	showDialog1(title,content,width,height,ev);
        getFormNosibp();
	//alert('asdasd');
}
function getFormNosibp(){
        param='proses=getFormNosipb';
        tujuan='keu_slave_penagihan.php';
        post_response_text(tujuan+'?'+'', param, respog);
	
	function respog(){
              if(con.readyState==4){
                if (con.status == 200) {
                        busy_off();
                        if (!isSaveResponse(con.responseText)) {
                                alert('ERROR TRANSACTION,\n' + con.responseText);
                        }
                        else {
                                //alert(con.responseText);
                                document.getElementById('formPencariandata').innerHTML=con.responseText;
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }
	 }
} 
function findNosipb(){
	txt=trim(document.getElementById('nosipbcr').value);
	param='txtfind='+txt+'&proses=getnosibp';
        tujuan='keu_slave_penagihan.php';
        if(txt==''){
            alert("Nosipb is obligatory");
        } else {
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
                                    document.getElementById('container2').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }
	 }
}
function setData(nosibp,kdcust){
    document.getElementById('noorder').value=nosibp;
    kridit=document.getElementById('kodecustomer');
    for(a=0;a<kridit.length;a++){
        if(kridit.options[a].value==kdcust){
                kridit.options[a].selected=true;
            }
    }
    kridit.disabled=true;
    closeDialog();
}
function cancelData(){
//    $arr="##noinvoice##jatuhtempo##kodeorganisasi##nofakturpajak##tanggal##bayarke";
//    $arr.="##kodecustomer##uangmuka##noorder##nilaippn##keterangan##nilaiinvoice##debet##kredit";
document.getElementById('formInput').style.display='none';
document.getElementById('listData').style.display='block';
clearData();
}
function clearData(){
document.getElementById('jatuhtempo').value='';
document.getElementById('nofakturpajak').value='';
document.getElementById('tanggal').value='';
document.getElementById('bayarke').value='';
document.getElementById('kodecustomer').value='';
document.getElementById('uangmuka').value='';
document.getElementById('noorder').value='';
document.getElementById('nilaippn').value='';
document.getElementById('keterangan').value='';
document.getElementById('nilaiinvoice').value='';
document.getElementById('debet').value='';
document.getElementById('kredit').value='';
document.getElementById('txtsearch').value="";
document.getElementById('tgl_cari').value="";
}
function delData(notrans){
        param='noinvoice='+notrans+'&proses=delData';
        tujuan='keu_slave_penagihan.php';  
        if(confirm("Anda yakin menghapus no invoice ini?"+ notrans)){
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
                                    getPage();
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }
	 }
}
function postingData(notrans){
        param='noinvoice='+notrans+'&proses=postingData';
        tujuan='keu_slave_penagihan.php';  
        if(confirm("Anda yakin memposting no invoice ini?"+ notrans)){
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
                                    getPage();
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }
	 }
}
function detailPDF(numRow,ev) {
    // Prep Param
    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    var noakun = document.getElementById('noakun_'+numRow).getAttribute('value');
    var tipetransaksi = document.getElementById('tipetransaksi_'+numRow).getAttribute('value');
    var kodeorg = document.getElementById('kodeorg_'+numRow).getAttribute('value');
    param = "proses=pdf&notransaksi="+notransaksi+"&kodeorg="+kodeorg+
        "&tipetransaksi="+tipetransaksi+"&noakun="+noakun;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='keu_slave_kasbank_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}