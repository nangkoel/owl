// JavaScript Document


function getKar(){
    kdUnit=document.getElementById('kdUnit').options[document.getElementById('kdUnit').selectedIndex].value;
    tpKary=document.getElementById('tpKary').options[document.getElementById('tpKary').selectedIndex].value;
    golongan=document.getElementById('golongan').options[document.getElementById('golongan').selectedIndex].value;
    idKomponen=document.getElementById('idKomponen').options[document.getElementById('idKomponen').selectedIndex].value;
    pilDt=document.getElementById('pilInp').options[document.getElementById('pilInp').selectedIndex].value;
	
        param='method=getKar'+'&kdUnit='+kdUnit+'&tpKary='+tpKary+'&golongan='+golongan+'&idKomponen='+idKomponen;
        param+='&pilDt='+pilDt;
        tujuan='sdm_slave_5gajipokok';
        post_response_text(tujuan+'.php', param, respon);
        function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                                          document.getElementById('karyawanId').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}



function saveFranco(fileTarget,passParam) {

    var passP = passParam.split('##');
    var param = "";

    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                        loadData();
                        //cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php', param, respon);

}
function loadData()
{
		
		 kdUnitCr=document.getElementById('kdUnitCr').options[document.getElementById('kdUnitCr').selectedIndex].value;
        opt=document.getElementById('opttahun').options[document.getElementById('opttahun').selectedIndex].value;
        nmkar=document.getElementById('nmKar').value;
        tpKar=document.getElementById('tpKaryCr').options[document.getElementById('tpKaryCr').selectedIndex].value;
        idkomp=document.getElementById('idKomponenCr').options[document.getElementById('idKomponenCr').selectedIndex].value;

        param='method=loadData'+'&optThn='+opt;
        if(nmkar!=''){
            param+='&namaKary='+nmkar;
        }
        if(tpKar!=''){
            param+='&tpKaryCr='+tpKar;
        }
        if(idkomp!=''){
            param+='&idKomponenCr='+idkomp;
        }
		 if(kdUnitCr!=''){
            param+='&kdUnitCr='+kdUnitCr;
        }
		
		

        tujuan='sdm_slave_5gajipokok';
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
function cariBast(num)
{
        opt=document.getElementById('opttahun').options[document.getElementById('opttahun').selectedIndex].value;
        nmkar=document.getElementById('nmKar').value;
        tpKar=document.getElementById('tpKaryCr').options[document.getElementById('tpKaryCr').selectedIndex].value;
        idkomp=document.getElementById('idKomponenCr').options[document.getElementById('idKomponenCr').selectedIndex].value;
        param='method=loadData'+'&optThn='+opt;
        if(nmkar!=''){
            param+='&namaKary='+nmkar;
        }
        if(tpKar!=''){
            param+='&tpKaryCr='+tpKar;
        }
        if(idkomp!=''){
            param+='&idKomponenCr='+idkomp;
        }
        param+='&page='+num;
        tujuan='sdm_slave_5gajipokok.php';

        post_response_text(tujuan, param, respog);			
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
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

function fillField(thn,karywnid,tpkary,kompongj,jmlh,kdUnit,namakar,golongan)
{
	document.getElementById('karyawanId').innerHTML="<option value='"+ karywnid +"'>"+ namakar +"</option>"
	document.getElementById('golongan').innerHTML="<option value='"+ golongan +"'>"+ golongan +"</option>"
	//alert(tpkary);
//##tipeKary##brt##brt2##brt3##brt4##method";
document.getElementById('tpKary').value=tpkary;
document.getElementById('idKomponen').value=kompongj;
document.getElementById('thn').value=thn;
document.getElementById('karyawanId').value=karywnid;
document.getElementById('karyawanId').disabled=true;
document.getElementById('kdUnit').disabled=true;
document.getElementById('jmlhDt').value=jmlh;
document.getElementById('thn').value=thn;
document.getElementById('thn').disabled=true;
document.getElementById('golongan').disabled=true;
document.getElementById('idKomponen').disabled=true;
document.getElementById('tpKary').disabled=true;
document.getElementById('method').value='updateData';
document.getElementById('pilInp').disabled=true;
document.getElementById('pilInp').value='0';
document.getElementById('kdUnit').value=kdUnit;
}
/*function cancelIsi()
{
//"##thn##pilInp##karyawanId##idKomponen##jmlhDt##method"##tpKary;
document.getElementById('tpKary').value='';
document.getElementById('pilInp').value='';
document.getElementById('karyawanId').value='';
document.getElementById('golongan').value='';
document.getElementById('jmlhDt').value='';
document.getElementById('idKomponen').value='';
document.getElementById('thn').disabled=false;
document.getElementById('idKomponen').disabled=false;
document.getElementById('tpKary').disabled=false;
document.getElementById('pilInp').disabled=false;
document.getElementById('method').value='insert';
}
*/
function cancelIsi(){
    document.getElementById('method').value='insert';
    document.getElementById('thn').disabled=false;
    document.getElementById('idKomponen').disabled=false;
    document.getElementById('tpKary').disabled=false;
    document.getElementById('pilInp').disabled=false;
}


function displatList(){
    document.getElementById('nmKar').value='';
    document.getElementById('tpKaryCr').value='';
    document.getElementById('idKomponenCr').value='';
    document.getElementById('idKomponen').value='';
	document.getElementById('kdUnitCr').value='';
    loadData();
}
function delData(thndt,karywnid,kompongj){

    param='method=delData'+'&optThn='+thndt;
    param+='&karyawanId='+karywnid+'&idKomponen='+kompongj;
    tujuan='sdm_slave_5gajipokok.php';
    if(confirm("Delete, are you sure ?")){
        post_response_text(tujuan, param, respog);			
    }
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                alert("Done");
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
//dari originalnya
function loadGaji(tahun)
{

  param='optThn='+tahun;		
  param+='&method=loadData';
  post_response_text('sdm_slave_5gajipokok.php', param, respon);
    function respon(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            } else { 
                                    //eval(con.responseText);
                                    document.getElementById('container').innerHTML=con.responseText;
                                    cancelIsi();
                            }
                    } else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }                
}
function copyTahun()
{
    tahun1=document.getElementById('tahun1');
    tahun2=document.getElementById('tahun2');
    tahun1=tahun1.options[tahun1.selectedIndex].value;
    tahun2=tahun2.options[tahun2.selectedIndex].value;
	kdUnit2=document.getElementById('kdUnit2').value;
    param='tahun1='+tahun1+'&tahun2='+tahun2+'&kdUnit2='+kdUnit2;
    if(tahun2<=tahun1)
        {
            alert('Tahun Tujuan harus lebih besar dari tahun Sumber');
        }
    else
        {
            if(confirm('Data pada Tahun Tujuan ('+tahun2+') akan ditimpa ?'))
                {
                    if(confirm('Apakah Anda yakin..?'))
                        {
                            post_response_text('sdm_slave_copyGP.php?', param, respon); 
                        }
                }         
        }

    function respon(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            } else {
                                 alert('Done');   
                            }
                    } else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }   
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2(title,content,width,height,ev); 	
}
function dataKeExcel(ev)
{
    thn=document.getElementById('opttahun').options[document.getElementById('opttahun').selectedIndex].value;
    param='method=dataDetail'+'&thn='+thn;
   // alert(param);
    tujuan='sdm_slave_5gajipokok_excel.php';
    judul='List Data';	
    printFile(param,tujuan,judul,ev)	
}
