//1625
function getData(){
    kodeorg=document.getElementById('kodeorg').value;
    hk=document.getElementById('hkpanen').value;
	//jml=document.getElementById('jmlHk').value;
    periode=document.getElementById('periode');
    periode=periode.options[periode.selectedIndex].value;
	tpdt=document.getElementById('tpDt');//
    tpdt=tpdt.options[tpdt.selectedIndex].value;
//	ttPtg=document.getElementById('totPtg').value;
    param='kodeorg='+kodeorg+'&periode='+periode+'&proses=preview'+'&tpDt='+tpdt+'&hk='+hk;
    tujuan='kebun_slave_premimandorbaru.php';
    post_response_text(tujuan, param, respog);
    function respog(){
      if(con.readyState==4)
      {
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
function saveAll(jmlRow){
    kodeorg=document.getElementById('kodeorg').value;
	//jml=document.getElementById('jmlHk').value;
    periode=document.getElementById('periode');
    periode=periode.options[periode.selectedIndex].value;
	tpdt=document.getElementById('tpDt');//
    tpdt=tpdt.options[tpdt.selectedIndex].value;
	//ttPtg=document.getElementById('totPtg').value;
    var strUrl2='';
    for(awal=1;awal<=jmlRow;awal++){
        try{
            if (tpdt=='keranimuat' || tpdt=='nikmandor1' || tpdt=='nikasisten') {prslbmptg='premiMandorSblmPtg1_';} else { prslbmptg='premiSblmPtg_';}
                if(strUrl2 != ''){					
                        strUrl2 +='&karyId[]='+trim(document.getElementById('mandorId_'+awal).value)
                                +'&premiRp[]='+document.getElementById('premiId_'+awal).value
                                +'&premiSblmPtg[]='+document.getElementById(prslbmptg+awal).value
                                +'&afdId[]='+document.getElementById('afdId_'+awal).value
                                +'&rataPremiPemanen[]='+document.getElementById('rataPremi_'+awal).value
                                +'&totPtg[]='+document.getElementById('totPtg_'+awal).value;
                }
                else{
                        strUrl2 +='&karyId[]='+trim(document.getElementById('mandorId_'+awal).value)
                                +'&premiRp[]='+document.getElementById('premiId_'+awal).value
                                +'&premiSblmPtg[]='+document.getElementById(prslbmptg+awal).value
                                +'&afdId[]='+document.getElementById('afdId_'+awal).value
                                +'&rataPremiPemanen[]='+document.getElementById('rataPremi_'+awal).value
                                +'&totPtg[]='+document.getElementById('totPtg_'+awal).value;
                }
            }
            catch(e){}

    }
    param='kodeorg='+kodeorg+'&periode='+periode+'&proses=saveAll'+'&tpDt='+tpdt;
    param+=strUrl2;
    tujuan='kebun_slave_premimandorbaru.php';
    post_response_text(tujuan, param, respog);
    function respog(){
      if(con.readyState==4)
      {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                            alert("Done");
                            document.getElementById('container').innerHTML="";
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }
}
function updatePremi(row){
    potongandt=0;
    premiSblmptg=document.getElementById('premiSblmPtg_'+row);
    premiMandor=document.getElementById('totPremiMandor_'+row);
    PremiForm=document.getElementById('premiId_'+row);
    ptgnId=document.getElementById('totPtg_'+row);
    if((ptgnId.value!="")||(ptgnId.value!=0)){
        prmiMandor=parseFloat((remove_comma_var(premiSblmptg.value)))-parseFloat((remove_comma_var(ptgnId.value)));
    }else{
        potongandt=parseFloat((remove_comma_var(premiSblmptg.value)))-parseFloat((remove_comma_var(PremiForm.value)));
    }
    PremiForm.value=remove_comma(PremiForm);
    PremiForm.value=prmiMandor;
    //premiMandor.innerHTML=remove_comma(premiMandor);
    change_number(PremiForm);
    premiMandor.innerHTML=PremiForm.value;
    ptgnId.value=remove_comma(ptgnId);
    if(potongandt!=0){
        ptgnId.value=potongandt;
    }
    change_number(ptgnId);
}
function bersihForm(row){
    document.getElementById('totPtg_'+row).value="";
}
function updatePremi2(row){
    potongandt=0;
    PremiDidapet=0;
    prmiBersih=0;
    prmbrsh=document.getElementById('premiSblmPtg_'+row);
    premiSblmptg=document.getElementById('premiSblmPtg1_'+row);//premi sebelum dengan potongan
    premiMandorSblmptg=document.getElementById('premiMandorSblmPtg_'+row);//premi mandor sebelum dengan potongan
    premiMandorSblmptg1=document.getElementById('premiMandorSblmPtg1_'+row);//premi mandor sebelum dengan potongan
    ptgnId=document.getElementById('totPtg_'+row);
    PremiForm=document.getElementById('premiId_'+row);//total premi yang di dapet
    PremiMandor=document.getElementById('totPremiMandor_'+row);//total premi yang di dapet
    jmlhKrm=document.getElementById('jmlKirim_'+row).innerHTML;
    jmlhHK=document.getElementById('jmlhHk_'+row).innerHTML;
    tipeDt=document.getElementById('tpDt').options[document.getElementById('tpDt').selectedIndex].innerHTML;
    if(tipeDt=="MANDOR" && parseFloat(jmlhHK)>23){
        jmlhHK=23;
    }
     if((ptgnId.value!="")||(ptgnId.value!=0)){
        prmiBersih=parseFloat((remove_comma_var(premiSblmptg.value)))-parseFloat((remove_comma_var(ptgnId.value)));
        //PremiDidapet=parseFloat((remove_comma_var(premiMandorSblmptg1.value)))-parseFloat((remove_comma_var(ptgnId.value)));
        PremiDidapet=(parseFloat(prmiBersih)/parseFloat(jmlhKrm))*parseFloat(jmlhHK);
        prmbrsh.value=remove_comma(prmbrsh);//
        prmbrsh.value=prmiBersih;
        change_number(prmbrsh);
        premiMandorSblmptg.value=remove_comma(premiMandorSblmptg);//
        premiMandorSblmptg.value=PremiDidapet;
        change_number(premiMandorSblmptg);
     }else{
         //alert("masuk");
          potongandt=parseFloat((remove_comma_var(premiSblmptg.value)))-parseFloat((remove_comma_var(prmbrsh.value)));
          //PremiDidapet=parseFloat((remove_comma_var(premiMandorSblmptg.value)));
          PremiDidapet=parseFloat((remove_comma_var(prmbrsh.value))/parseFloat(jmlhKrm))*parseFloat(jmlhHK);
          change_number(prmbrsh);
     }
    PremiForm.value=remove_comma(PremiForm);
    //PremiMandor.value=remove_comma(PremiMandor);
    PremiForm.value=PremiDidapet;
    change_number(PremiForm);
    //change_number(PremiDidapet);
    document.getElementById('totPremiMandor_'+row).innerHTML=premiMandorSblmptg.value;
    document.getElementById('premiBersih_'+row).innerHTML=prmbrsh.value;
    ptgnId.value=remove_comma(ptgnId);
    if(potongandt!=0){
        ptgnId.value=potongandt;
    }
    change_number(ptgnId);
}
function loadKemandoran(num){
	param='proses=loadData';
	param+='&page='+num;
	tujuan='kebun_slave_premimandorbaru.php';
    post_response_text(tujuan, param, respog);
    function respog(){
      if(con.readyState==4)
      {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
							dtR=con.responseText.split("####");
                            document.getElementById('containerlist').innerHTML=dtR[0];
							document.getElementById('footerDt').innerHTML=dtR[1];
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }
}
function dataKeExcel(ev,tujuan,dtKrm){
	par=dtKrm.split(",");
	judul='Data';		
	param='periode='+par[0]+'&jabatan='+par[1]+'&kodeorg='+par[2]+'&proses=dtExcel';
	//alert(param);	
	printFile(param,tujuan,judul,ev)	
}
function getExcel(ev,tujuan){
	kodeorg=document.getElementById('kodeorg').value;
    hk=document.getElementById('hkpanen').value;
	//jml=document.getElementById('jmlHk').value;
    periode=document.getElementById('periode');
    periode=periode.options[periode.selectedIndex].value;
	tpdt=document.getElementById('tpDt');//
    tpdt=tpdt.options[tpdt.selectedIndex].value;
//	ttPtg=document.getElementById('totPtg').value;
    param='kodeorg='+kodeorg+'&periode='+periode+'&proses=excel'+'&tpDt='+tpdt+'&hk='+hk;
	judul='List Data';		
	//alert(param);	
	printFile(param,tujuan,judul,ev)	
}
function printFile(param,tujuan,title,ev){
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}