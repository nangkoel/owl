// JavaScript Document
function get_kd(notrans)
{
        //alert("test");
        if(notrans=='')
        {
                jns_id=document.getElementById('jns_vhc').value;
                traksi_id=document.getElementById('kodetraksi').value;
                strAll='jns_id='+jns_id+'&traksi_id='+traksi_id+'&proses=getKodeVhc';
        }
        else
        {
                /*jnsid=jns;
                kd_vhc=kdvhc;*/
                strAll='no_trans='+notrans;
                strAll+='&proses=getKodeVhc';

        }
    //alert(param);
        param=strAll;
        //alert(param);
        tujuan='vhc_slave_save_pekerjaan.php';
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
                                                  //	alert(con.responseText);
                                                        document.getElementById('kde_vhc').innerHTML=con.responseText;
                                                        load_data_pekerjaan();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
         post_response_text(tujuan, param, respog);	
}
function fillField(noTrans,Thn)
{
        unlock_header_form();
        notrn=noTrans;
        param='no_trans='+notrn+'&proses=getData';
        tujuan='vhc_slave_save_pekerjaan.php';
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
                                                 //	alert(con.responseText);
                                                 clear_operator();
                                                 bersih_form_pekerjaan();
                                                ar=con.responseText.split("####");
                                                document.getElementById('no_trans').value=ar[0];
                                                document.getElementById('no_trans_pekerjaan').value=ar[0];
                                                document.getElementById('no_trans_opt').value=ar[0];
                                                document.getElementById('jns_vhc').value=ar[1];
                                                document.getElementById('kodetraksi').value=ar[7];
                                                //document.getElementById('kde_vhc').value=KdVhc;
                                                document.getElementById('tgl_pekerjaan').value=ar[2];
                                                document.getElementById('tgl_pekerjaan').disabled=true;
                                                //document.getElementById('kmhm_awal').value=ar[3];
                                                //document.getElementById('kmhm_akhir').value=ar[4];
                                                //document.getElementById('stn').value=ar[5];
                                                document.getElementById('jns_bbm').value=ar[3];
                                                document.getElementById('jmlh_bbm').value=ar[4];
                                                document.getElementById('KbnId').disabled=true;
                                                document.getElementById('KbnId').value=ar[5];
                                                //document.getElementById('thnKntrk').value=ar[9];
                                                document.getElementById('kode_karyawan').innerHTML=ar[6];


                                                if(ar[6]=='')
                                                {
                                                        ar[6]="<option value''></options>";
                                                }
                                                //document.getElementById('noKntrk').innerHTML=ar[10];
                                                document.getElementById('proses').value='update_head';
                                                get_kd(noTrans);
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
}
function createNew()
{
        get_notransaksi();
        //load_data_pekerjaan();
        //document.getElementById('create_new').style.display='none';
        document.getElementById('done_entry').disabled=true;
        document.getElementById('save_kepala').disabled=false;
        document.getElementById('cancel_kepala').disabled=false;
        document.getElementById('proses').value='insert_header';
        //document.getElementById('premiStat').disabled=false;
        document.getElementById('jns_vhc').disabled=false;
        document.getElementById('kodetraksi').disabled=false;
        document.getElementById('kde_vhc').disabled=false;
        document.getElementById('tgl_pekerjaan').disabled=false;
        document.getElementById('kmhm_awal').disabled=false;
        document.getElementById('kmhm_akhir').disabled=false;	
        document.getElementById('stn').disabled=false;	
        document.getElementById('jns_bbm').disabled=false;	
        document.getElementById('jmlh_bbm').disabled=false;	
        //document.getElementById('noKntrk').disabled=false;	
        //document.getElementById('thnKntrk').disabled=false;	
        //document.getElementById('noKntrk').innerHTML='';
        //document.getElementById('thnKntrk').value='';
}
function get_notransaksi()
{
        kdOrg=document.getElementById('KbnId').options[document.getElementById('KbnId').selectedIndex].value;
        param='proses=get_no_transaksi'+'&kdOrg='+kdOrg;
        tujuan='vhc_slave_save_pekerjaan.php';
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
                                                        ac=con.responseText.split("####");
                                                        document.getElementById('no_trans').value=ac[0];
                                                        ar=document.getElementById('no_trans').value;
                                                        document.getElementById('no_trans_pekerjaan').value=ar;
                                                        document.getElementById('no_trans_opt').value=ar;
                                                        document.getElementById('kode_karyawan').innerHTML=ac[1];
                                                        load_data();

                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}
function save_header()
{
        //jns_vhc,kde_vhc,tgl_pekerjaan,kmhm_awal,kmhm_akhir,stn,jns_bbm,jmlh_bbm

        jenis_vhc=document.getElementById('jns_vhc').options[document.getElementById('jns_vhc').selectedIndex].value;
        if(document.getElementById('kde_vhc').options[document.getElementById('kde_vhc').selectedIndex].value!='')
        {
                kdVhc=document.getElementById('kde_vhc').options[document.getElementById('kde_vhc').selectedIndex].value;
        }
        else
        {
                kdVhc='';
        }
        kodeOrg=document.getElementById('KbnId').options[document.getElementById('KbnId').selectedIndex].value;
        tgl_kerja=document.getElementById('tgl_pekerjaan').value;

        jns_bbm=document.getElementById('jns_bbm').options[document.getElementById('jns_bbm').selectedIndex].value;
        jmlh=document.getElementById('jmlh_bbm').value;
        pro=document.getElementById('proses');
        no_trans=document.getElementById('no_trans').value;
        

        param='jns_id='+jenis_vhc+'&kode_vhc='+kdVhc+'&tglKerja='+tgl_kerja+'&kodeOrg='+kodeOrg;
        param+='&jnsBbm='+jns_bbm+'&jumlah='+jmlh+'&proses='+pro.value+'&no_trans='+no_trans;
        //alert(param);
        tujuan='vhc_slave_save_pekerjaan.php';
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
                                                        
                                                        if(con.responseText!=''){
                                                                isidt=con.responseText.split("#####");
                                                        }
                                                        if(isidt[0]==''){
                                                            document.getElementById('kmhm_awal').disabled=false;
                                                            document.getElementById('kmhm_awal').value=0;
                                                        }else{
                                                            document.getElementById('kmhm_awal').disabled=true;
                                                            document.getElementById('kmhm_awal').value=isidt[0];
                                                        }
                                                        document.getElementById('no_trans').value=isidt[1];
                                                        ar=document.getElementById('no_trans').value;
                                                        document.getElementById('no_trans_pekerjaan').value=ar;
                                                        document.getElementById('no_trans_opt').value=ar;
                                                        lock_header_form();
                                                        

//                                                        if(pro.value=='insert_header'){
//                                                            document.getElementById('no_trans').value=isidt[1];
//                                                            ar=document.getElementById('no_trans').value;
//                                                            document.getElementById('no_trans_pekerjaan').value=ar;
//                                                            document.getElementById('no_trans_opt').value=ar;
//                                                            lock_header_form();
//                                                        }
//                                                        else if(pro.value=='update_head'){
//                                                                lock_header_form();//clear_form();
//                                                        }
                                                        load_data();


                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }

}


function lock_header_form()
{
        //jns_vhc,kde_vhc,tgl_pekerjaan,kmhm_awal,kmhm_akhir,stn,jns_bbm,jmlh_bbm
        document.getElementById('jns_vhc').disabled=true;
        document.getElementById('kodetraksi').disabled=true;
        document.getElementById('kde_vhc').disabled=true;
        document.getElementById('tgl_pekerjaan').disabled=true;

        document.getElementById('jns_bbm').disabled=true;
        document.getElementById('jmlh_bbm').disabled=true;
        document.getElementById('save_kepala').disabled=true;
        document.getElementById('cancel_kepala').disabled=true;
        document.getElementById('done_entry').disabled=false;
        //document.getElementById('thnKntrk').disabled=true;
        //document.getElementById('noKntrk').disabled=true;
        //document.getElementById('premiStat').disabled=true;
        document.getElementById('KbnId').disabled=true;
}
function unlock_header_form()
{
        document.getElementById('jns_vhc').disabled=false;
        document.getElementById('kodetraksi').disabled=false;
        document.getElementById('kde_vhc').disabled=false;
        document.getElementById('tgl_pekerjaan').disabled=false;
//	document.getElementById('kmhm_awal').disabled=false;
//	document.getElementById('kmhm_akhir').disabled=false;
//	document.getElementById('stn').disabled=false;
        document.getElementById('jns_bbm').disabled=false;
        document.getElementById('jmlh_bbm').disabled=false;
        document.getElementById('save_kepala').disabled=false;
        document.getElementById('cancel_kepala').disabled=false;
        document.getElementById('done_entry').disabled=true;
        document.getElementById('KbnId').disabled=false;
        //document.getElementById('create_new').style.display='none';
        //document.getElementById('thnKntrk').disabled=false;
        //document.getElementById('noKntrk').disabled=false;
        //document.getElementById('premiStat').disabled=false;
}
function clear_form()
{
        document.getElementById('no_trans').value='';
        document.getElementById('jns_vhc').value='';
        document.getElementById('kodetraksi').value='';
        document.getElementById('kde_vhc').innerHTML="<option value=''>"+dataKdvhc+"</option>";
        document.getElementById('tgl_pekerjaan').value='';

        document.getElementById('jns_bbm').value='';
        document.getElementById('jmlh_bbm').value='';
        document.getElementById('save_kepala').value='';
        document.getElementById('cancel_kepala').value='';
        document.getElementById('KbnId').value='';
        document.getElementById('KbnId').disabled=false;
}
function doneEntry()
{
        if(confirm("Are you sure..?"))
        {
                cancel_kepala_form();
                bersih_form_pekerjaan();
                clear_operator();
        }
        else
        {
                return;
        }
}
function cancel_kepala_form()
{
        clear_form();
        document.getElementById('save_kepala').disabled=true;
        document.getElementById('cancel_kepala').disabled=true;
        document.getElementById('done_entry').disabled=true;
        //document.getElementById('create_new').style.display='block';
        document.getElementById('no_trans_pekerjaan').value='';
        document.getElementById('no_trans_opt').value='';
}
function load_data()
{
        //alert("test");
        param='proses=load_data_header';
        tujuan='vhc_slave_save_pekerjaan.php';
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
                                                        document.getElementById('tgl_cari').value='';
                                                        document.getElementById('txtCari').value='';
                                                        document.getElementById('contain').innerHTML=con.responseText;
                                                        // getUmr();
                                                        //load_data();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }

}
function cariDataTransaksi()
{
        txtTgl=document.getElementById('tgl_cari').value;
        txtCari=document.getElementById('txtCari').value;
        statData=document.getElementById('statusInputan').options[document.getElementById('statusInputan').selectedIndex].value;
        param="txtTgl="+txtTgl+"&txtCari="+txtCari+'&statData='+statData;
        param+="&proses=cariTransaksi";
        //alert(param);
        tujuan='vhc_slave_save_pekerjaan.php';
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
                                                        document.getElementById('contain').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }

}
function cariData(num)
{
                txtTgl=document.getElementById('tgl_cari').value;
                txtCari=document.getElementById('txtCari').value;
                statData=document.getElementById('statusInputan').options[document.getElementById('statusInputan').selectedIndex].value;
                param="txtTgl="+txtTgl+"&txtCari="+txtCari+'&statData='+statData;
                param+="&proses=cariTransaksi";
                param+='&page='+num;
                //alert(param);
                tujuan = 'vhc_slave_save_pekerjaan.php';

                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('contain').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function load_data_operator()
{
        //alert(document.getElementById('no_trans_opt').value);
        if(document.getElementById('no_trans_opt').value!='')
        {
                no_tans=document.getElementById('no_trans_opt').value;
                param='proses=load_data_opt';
                param+='&notrans='+no_tans;
                //alert(param);
                tujuan='vhc_detailPekerjaan.php';	
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
                                                document.getElementById('containOperator').innerHTML=con.responseText;
                                                //load_data_pekerjaan();+
                                                noTrans=document.getElementById('no_trans_opt').value;
                                              
                                        //	getKntrk(thn,nokntrak);


                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                  }	
                }  	
                post_response_text(tujuan, param, respog);
        }
}
function load_data_pekerjaan()
{
        //alert(document.getElementById('no_trans_pekerjaan').value);
        if(document.getElementById('no_trans_pekerjaan').value!='')
        {
                no_trans=document.getElementById('no_trans_pekerjaan').value;
                param='notrans='+no_trans;
                param+='&proses=load_data_kerjaan';
                //alert(param);
                tujuan='vhc_detailPekerjaan.php';

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
                                                document.getElementById('containPekerja').innerHTML=con.responseText;
                                                load_data_operator();
                                        }	
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                  }	
                }  
                post_response_text(tujuan, param, respog);	
        }

}

function getKntrk(thn,nokntrak)
{
        if((thn=='')&&(nokntrak==''))
        {
                //alert("masuk");
                thnKntrk=document.getElementById('thnKntrk').options[document.getElementById('thnKntrk').selectedIndex].value;
                param='thnKntrk='+thnKntrk+'&proses=getKntrk';
        }
        else
        {
                thnKntrk=thn;
                noKntrak=nokntrak;
                param='thnKntrk='+thnKntrk+'&proses=getKntrk'+'&noKntrak='+noKntrak;
        }
        tujuan='vhc_detailPekerjaan.php';
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

                                                        document.getElementById('noKntrk').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 
}


function searchLok(title,content,ev)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
}
function findLok()
{
        txt=trim(document.getElementById('txtinputan').value);
        if(txt=='')
        {
                alert('Text is obligatory');
        }
        else if(txt.length<3)
        {
                alert('Too short');
        }
        else
        {
                param='txtinputan='+txt+'&proses=cari_lokasi';
                tujuan='vhc_slave_save_pekerjaan.php';
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
function throwThisRow(kd_org,nm_org)
{
     document.getElementById('lokasi_kerja_nm').value=nm_org;
         document.getElementById('lokasi_kerja').value=kd_org;
         closeDialog();
}
function fillFieldKrj(jnsKrj,lokKrj,brtMuat,jmlhRit,ktr,bya,kmawl,kmakhr,stn){
        document.getElementById('jns_kerja').value=jnsKrj;
        document.getElementById('old_jnskerja').value=jnsKrj;
        document.getElementById('brt_muatan').value=brtMuat;
        document.getElementById('jmlh_rit').value=jmlhRit;
        document.getElementById('biaya').value=bya;
        document.getElementById('ket').value=ktr;
        document.getElementById('kmhm_awal').value=kmawl;
        document.getElementById('kmhm_akhir').value=kmakhr;
        document.getElementById('stn').value=stn;
        document.getElementById('proses_pekerjaan').value='update_kerja';
        document.getElementById('old_jnskerja').value=jnsKrj;

        //document.getElementById('jns_kerja').disabled=true;
        //document.getElementById('lokasi_kerja').disabled=true;
        //document.getElementById('blok').disabled=true;
        if(lokKrj.length>4)
        {
                kd=lokKrj.substr(0,4);
                //alert(kd);
                document.getElementById('lokasi_kerja').value=kd;
                document.getElementById('old_lokkerja').value=kd;
                getBlok(kd,lokKrj,jnsKrj);
                //document.getElementById('blok').value=lokKrj;
        }
        else
        {
                document.getElementById('old_lokkerja').value=lokKrj;
                document.getElementById('lokasi_kerja').value=lokKrj;
                document.getElementById('blok').innerHTML="<option value=''>"+dataKdvhc+"</option>";
        }
       
}
function save_pekerjaan(){
        //no_trans_pekerjaan,jns_kerja,lokasi_kerja,muatan,brt_muatan,jmlh_rit,ket
        dcek=document.getElementById('save_kepala');
        if(dcek.disabled!=true)
        {
            alert("Please confirm header first");
            return;
        }
        notrans=document.getElementById('no_trans_pekerjaan').value;
        if(notrans=='')
        {
                alert("Please clik New")
                return;
        }
        jns_pekerjan=document.getElementById('jns_kerja').options[document.getElementById('jns_kerja').selectedIndex].value;
        if(document.getElementById('old_jnskerja').value=='')
        {
                document.getElementById('old_jnskerja').value=jns_pekerjan;
        }
        kmhm_aw=document.getElementById('kmhm_awal').value;
        kmhm_ak=document.getElementById('kmhm_akhir').value;
        satuan=document.getElementById('stn').options[document.getElementById('stn').selectedIndex].value;
        oldkerja=document.getElementById('old_jnskerja').value;
        locationKerj=document.getElementById('lokasi_kerja').options[document.getElementById('lokasi_kerja').selectedIndex].value;
        brtmuatan=document.getElementById('brt_muatan').value;
        jmlh_rit=document.getElementById('jmlh_rit').value;
        keterangan=document.getElementById('ket').value;
        pro=document.getElementById('proses_pekerjaan');
        bya=document.getElementById('biaya').value;
        Blok=document.getElementById('blok').options[document.getElementById('blok').selectedIndex].value;
        param='notrans='+notrans+'&jnsPekerjaan='+jns_pekerjan+'&locationKerja='+locationKerj+'&biaya='+bya;
        param+='&brtmuatan='+brtmuatan+'&jmlhRit='+jmlh_rit+'&ket='+keterangan+'&proses='+pro.value+'&oldjnsPekerjaan='+oldkerja;
        param+='&kmhmAwal='+kmhm_aw+'&kmhmAkhir='+kmhm_ak+'&satuan='+satuan;
        if(document.getElementById('old_lokkerja').value!='')
        {
                old_lokKerja=document.getElementById('old_lokkerja').value;
                param+='&old_lokKerja='+old_lokKerja;
        }
        if(document.getElementById('old_blok').value!='')
        {
                oldBlok=document.getElementById('old_blok').value;
                param+='&oldBlok='+oldBlok;
        }

        if(Blok!='')
        {
                param+='&Blok='+Blok;
        }
        //alert(param);
        tujuan='vhc_detailPekerjaan.php';
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
                                                        //document.getElementById('container').innerHTML=con.responseText;
                                                        bersih_form_pekerjaan();
                                                        isidt=0;
                                                        if(con.responseText!='')
                                                        {
                                                            isidt=parseFloat(con.responseText);
                                                        }
                                                        document.getElementById('kmhm_awal').disabled=true;
                                                        document.getElementById('kmhm_awal').value=isidt;


                                                        load_data_pekerjaan();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	

}
function delHead(noTran)
{
        notrans=noTran;
        param='no_trans='+notrans+'&proses=deleteHead';
        tujuan='vhc_slave_save_pekerjaan.php';
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
                                                        //document.getElementById('contain').value=con.responseText;
                                                        load_data();


                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }
         if(confirm("Header dan detail wil be deleted, are you sure?"))
         {
                post_response_text(tujuan, param, respog);
         }
         else
         {
                 return;
         }
}

function bersih_form_pekerjaan()
{       
        document.getElementById('proses_pekerjaan').value='insert_pekerjaan';
        document.getElementById('jns_kerja').value='';
        document.getElementById('jns_kerja').disabled=false;
        document.getElementById('lokasi_kerja').value='';
        document.getElementById('lokasi_kerja').disabled=false;
        document.getElementById('brt_muatan').value=0;
        document.getElementById('jmlh_rit').value=0;
        document.getElementById('ket').value='';
        document.getElementById('biaya').value=0;
        document.getElementById('blok').innerHTML="<option value=''>"+dataKdvhc+"</options>";
        //document.getElementById('kmhm_awal').value=0;
        document.getElementById('kmhm_akhir').value=0;
        document.getElementById('stn').value=0;
        document.getElementById('satuanKrj').innerHTML="";
}
function delDataKrj(noTrans,jnsKerja)
{
        no_trans=document.getElementById('no_trans_pekerjaan').value=noTrans;
        jns_kerja=document.getElementById('jns_kerja').value=jnsKerja;
        param='notrans='+no_trans+'&jnsPekerjaan='+jns_kerja+'&proses=deleteKrj';
        tujuan='vhc_detailPekerjaan.php';
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
                                        load_data_pekerjaan();
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
          }	
        } 	
        if(confirm("Delete, are you sure?"))
        {
                post_response_text(tujuan, param, respog);
        }
        else
        {
                return;
        }


}
stat_opt=0;
function delData(noTrans,Kdkry)
{
        no_trans=document.getElementById('no_trans_opt').value=noTrans;
        kdKry=document.getElementById('kode_karyawan').value=Kdkry;
        pros=document.getElementById('prosesOpt');
        //pros.value=;
        param='noOptrans='+no_trans+'&kdKry='+kdKry+'&proses=delete_opt';
        tujuan='vhc_detailPekerjaan.php';

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
                                        //document.getElementById('containPekerja').innerHTML=con.responseText;
                                        load_data_operator();
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
          }	
        } 	
        if(confirm("Delete, are you sure?"))
        {
                post_response_text(tujuan, param, respog);
        }
        else
        {
                return;
        }
}
function clear_operator(){
        document.getElementById('kode_karyawan').value='';
        document.getElementById('uphOprt').value='';
        document.getElementById('prmiOprt').value='';
		document.getElementById('prmiLuarJam').value='';
        document.getElementById('pnltyOprt').value='';
        document.getElementById('premiCuci').checked=false;
        document.getElementById('prosesOpt').value='insert_operator';
}
function save_operator()
{
        notrans=document.getElementById('no_trans_opt').value;
        kdKry=document.getElementById('kode_karyawan').options[document.getElementById('kode_karyawan').selectedIndex].value;
        posisi=document.getElementById('posisi').options[document.getElementById('posisi').selectedIndex].value;
        uphoprt=document.getElementById('uphOprt').value;
        prmiOprt=document.getElementById('prmiOprt').value;
		prmiLuarJam=document.getElementById('prmiLuarJam').value;
        pnltyOprt=document.getElementById('pnltyOprt').value;
        tglTrans=document.getElementById('tgl_pekerjaan').value;
        if(document.getElementById('premiCuci').checked==true){
            premcuc=1;
        }else{
            premcuc=0;
        }
        
        pros=document.getElementById('prosesOpt');
        param='notrans='+notrans+'&kdKry='+kdKry+'&posisi='+posisi;
        param+='&proses='+pros.value+'&pnltyOprt='+pnltyOprt+'&prmiOprt='+prmiOprt+'&prmiLuarJam='+prmiLuarJam;
        param+='&uphOprt='+uphoprt+'&tglTrans='+tglTrans+'&premicuci='+premcuc;
        tujuan='vhc_detailPekerjaan.php';
        //alert(param);
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
                                        //document.getElementById('containPekerja').innerHTML=con.responseText;
                                        load_data_operator();
                                        clear_operator();
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
          }	
        } 

}
function cariBast(num)
{
                param='proses=load_data_header';
                param+='&page='+num;
                tujuan = 'vhc_slave_save_pekerjaan.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('contain').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function cariBastKrj(num)
{
                param='proses=load_data_kerjaan';
                param+='&page='+num;
                tujuan = 'vhc_detailPekerjaan.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('containPekerja').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function cariBastOpt(num)
{
                param='proses=load_data_opt';
                param+='&page='+num;
                tujuan = 'vhc_detailPekerjaan.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('containOperator').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function getUmr(){
        notrans=document.getElementById('no_trans_opt').value;
        kdkry=document.getElementById('kode_karyawan').options[document.getElementById('kode_karyawan').selectedIndex].value;
        tanggal=document.getElementById('tgl_pekerjaan').value;
        tahun=tanggal.substr(6, 4);
        param='proses=getUmr'+'&kdKry='+kdkry+'&tahun='+tahun;
        param+='&notransaksi='+notrans;
        tujuan='vhc_detailPekerjaan.php';
        post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {

                                                document.getElementById('uphOprt').value=trim(con.responseText);
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function getBlok(kdkbn,kdblok,jnskrj)
{
        if((kdkbn=='')&&(kdblok==''))
        {
                locationKerja=document.getElementById('lokasi_kerja').options[document.getElementById('lokasi_kerja').selectedIndex].value;
                param='locationKerja='+locationKerja+'&proses=getBlok';
        }
        else
        {
                locationKerja=kdkbn;
                Blok=kdblok;
                param='locationKerja='+locationKerja+'&Blok='+Blok+'&proses=getBlok';
        }
        tujuan='vhc_detailPekerjaan.php';
        post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {

                                                document.getElementById('blok').innerHTML=con.responseText;
                                                document.getElementById('old_blok').value=kdblok;
                                                if(jnskrj!=''){
                                                     getSatuanKrj(jnskrj);
                                                }
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	

}
function getSatuanKrj(sat){
        if(sat=='0'){
            kdkeg=document.getElementById('jns_kerja');
            kdkeg=kdkeg.options[kdkeg.selectedIndex].value;
        }else{
            kdkeg=sat;
        }
        param='kdKegiatan='+kdkeg+'&proses=getSatuan';
        tujuan='vhc_detailPekerjaan.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {

                                    document.getElementById('satuanKrj').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
        }
}
function getPremi(){
        posisi=document.getElementById('posisi').value;
        notrans=document.getElementById('no_trans_opt').value;
        tgl=document.getElementById('tgl_pekerjaan').value;
        param='proses=getPremi'+'&tanggal='+tgl;
        param+='&notransaksi='+notrans;
        param+='&posisi='+posisi;
        tujuan='vhc_detailPekerjaan.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {

                                    document.getElementById('prmiOprt').value=trim(con.responseText);
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
        }
}
function getKary(title,pil,ev){
        notrn=document.getElementById('no_trans').value;
        kdTraksi=document.getElementById('kodetraksi');
        kdTraksi=kdTraksi.options[kdTraksi.selectedIndex].value;
        jnsVhc=document.getElementById('jns_vhc');
        jnsVhc=jnsVhc.options[jnsVhc.selectedIndex].value;
        lokKerja=document.getElementById('lokasi_kerja');
        lokKerja=lokKerja.options[lokKerja.selectedIndex].value;
        if((kdTraksi=='')||(notrn=='')){
            return;
        }
        content= "<div style='width:100%;'>";
        content+="<fieldset>"+title+"<input type=hidden id=kdTraksi value="+kdTraksi+" /><input type=hidden id=jnsVhc value="+jnsVhc+" /><input type=hidden id=lokKerja value="+lokKerja+" /><input type=text id=txtnamabarang class=myinputtext size=25 maxlength=35><button class=mybutton onclick=goCariKary("+pil+")>Go</button> </fieldset>";
        content+="<div id=containercari style='overflow:scroll;height:300px;width:520px'></div></div>";    
       //display window
       width='550';
       height='350';
       showDialog1(title,content,width,height,ev);		
}
function goCariKary(pil){
        kdtraksi=document.getElementById('kdTraksi').value;
        jnsvhc=document.getElementById('jnsVhc').value;
        lokkrj=document.getElementById('lokKerja').value;
        nmkary=document.getElementById('txtnamabarang').value;
        param='kdTraksi='+kdtraksi+'&jnsVhc='+jnsvhc+'&txtcari='+nmkary+'&pil='+pil;
        param+='&lokKerja='+lokkrj;
        if(pil==1){
            param+='&proses=getMesin';
        }
        if(pil==2){
            param+='&proses=getKegiatan';
        }
        if(pil==3){
            param+='&proses=getBlok2';
        }
        if(pil==4){
            param+='&proses=getNmKaryawan';
        }
        
        
    tujuan = 'vhc_detailPekerjaan.php';
    post_response_text(tujuan, param, respog);				
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    document.getElementById('containercari').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}
function setBlok(karyid,kdisi,pil){
    if(kdisi=='TRAKSI'){
        kar=document.getElementById('kde_vhc');
      for(x=0;x<kar.length;x++){
        if(kar.options[x].value==karyid){
                kar.options[x].selected=true;
        }
      }
    }
    if(pil==2){
        document.getElementById('satuanKrj').innerHTML=kdisi;
         kar=document.getElementById('jns_kerja');
      for(x=0;x<kar.length;x++){
        if(kar.options[x].value==karyid){
                kar.options[x].selected=true;
        }
      }
    }
    if(pil==3){
      kar=document.getElementById('blok');
      for(x=0;x<kar.length;x++){
        if(kar.options[x].value==karyid){
                kar.options[x].selected=true;
        }
      }
    }
    if(pil==4){
      kar=document.getElementById('kode_karyawan');
      for(x=0;x<kar.length;x++){
        if(kar.options[x].value==karyid){
                kar.options[x].selected=true;
        }
      }
      getUmr();
    }
    
    
      closeDialog();
}