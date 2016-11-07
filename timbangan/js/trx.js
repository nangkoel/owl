function ambil_tanggal() {
                var myDate = new Date();
                var tanggal,bulan,tahun,jam,menitdetik;
                var output;
                tanggal= myDate.getDate().toString();
                bulan  = (myDate.getMonth()+1).toString();
                tahun  = myDate.getFullYear().toString();
                jam     = myDate.getHours().toString();
                menit  = myDate.getMinutes().toString();
                detik  = myDate.getSeconds().toString();
                if(tanggal.length<2)
                   tanggal="0"+tanggal;
                if(bulan.length<2)
                   bulan="0"+bulan;
                if(jam.length<2)
                   jam="0"+jam;
                if(menit.length<2)
                   menit="0"+menit;
                if(detik.length<2)
                   detik="0"+detik;
                output=tanggal+"-"+bulan+"-"+tahun+" "+jam+":"+menit+":"+detik;
                document.getElementById('datein').value=output;
                _WEIGH=document.getElementById('WEIGH').value;
        document.getElementById('WEIGH1').value = _WEIGH;
}
function ambil_tanggal2() {
                var myDate = new Date();
                var tanggal,bulan,tahun,jam,menitdetik;
                var output;
                tanggal= myDate.getDate().toString();
                bulan  = (myDate.getMonth()+1).toString();
                tahun  = myDate.getFullYear().toString();
                jam     = myDate.getHours().toString();
                menit  = myDate.getMinutes().toString();
                detik  = myDate.getSeconds().toString();
                if(tanggal.length<2)
                   tanggal='0'+tanggal;
                if(bulan.length<2)
                   bulan='0'+bulan;
                if(jam.length<2)
                   jam='0'+jam;
                if(menit.length<2)
                   menit='0'+menit;
                if(detik.length<2)
                   detik='0'+detik;
                output=tanggal+'-'+bulan+'-'+tahun+" "+jam+':'+menit+':'+detik;
                document.getElementById('dateout').value=output;
        _WEIGH=document.getElementById('WEIGH').value;
        document.getElementById('WEIGH2').value = _WEIGH;
        weigh1=parseInt(document.getElementById('WEIGH1').value);
        weigh2=parseInt(document.getElementById('WEIGH2').value);
        weigh3=weigh1-weigh2;
        document.getElementById('NETTO').value = weigh3;
        //hitung BJR
        JJG1=parseInt(document.getElementById('JUMJJG').value);
        JJG2=0;
        JJG3=0;
        try{
        JJG2=parseInt(document.getElementById('JUMJJG3').value);
        JJG3=parseInt(document.getElementById('JUMJJG2').value);
        }
        catch(err)
        {}
        bjr=(weigh3/(JJG1+JJG2+JJG3)).toFixed(2);
        document.getElementById('BJR').value=bjr;
        
        //ambil default potongan dari JS
         frkbuahbusuk          =document.getElementById('frkbuahbusuk').value;
         frkbuahkrgmatang =document.getElementById('frkbuahkrgmatang').value;
         frkbuahsakit            =document.getElementById('frkbuahsakit').value;
         frkjanjangkosong   =document.getElementById('frkjanjangkosong').value; 
         frklwtmatang          =document.getElementById('frklwtmatang').value; 
         frkmentah               =document.getElementById('frkmentah').value; 
         frktkpanjang          =document.getElementById('frktkpanjang').value;
		 frktigakilo          =document.getElementById('frktigakilo').value; 		 
       //formula
       if(frkbuahbusuk=='')
           frkbuahbusuk=0;
       if(frkbuahkrgmatang=='')
           frkbuahkrgmatang=0;
       if(frkbuahsakit=='')
           frkbuahsakit=0;
       if(frkjanjangkosong=='')
           frkjanjangkosong=0;
       if(frklwtmatang=='')
           frklwtmatang=0;   
        if(frkmentah=='')
           frkmentah=0;   
        if(frktkpanjang=='')
           frktkpanjang=0; 
        if(frktigakilo=='')
           frktigakilo=0; 		   
       //ambil inputan potongan
         buahbusuk          =document.getElementById('buahbusuk').value;
         buahkrgmatang =document.getElementById('buahkrgmatang').value;
         buahsakit            =document.getElementById('buahsakit').value;
         janjangkosong   =document.getElementById('janjangkosong').value; 
         lwtmatang          =document.getElementById('lwtmatang').value; 
         mentah               =document.getElementById('mentah').value; 
         tkpanjang           =document.getElementById('tkpanjang').value;
		 tigakilo           =document.getElementById('tigakilo').value;		 
        
            vbuahbusuk            =bjr*frkbuahbusuk*buahbusuk;
            vbuahkrgmatang   =bjr*frkbuahkrgmatang*buahkrgmatang;
            vbuahsakit              =bjr*frkbuahsakit*buahsakit;
            vjanjangkosong     =bjr*frkjanjangkosong*janjangkosong;
            vlwtmatang             =bjr*frklwtmatang*lwtmatang;   
            vmentah                 =bjr*frkmentah*mentah;
            vtkpanjang             =bjr*frktkpanjang*tkpanjang;
			vtigakilo             =bjr*frktigakilo*tigakilo;
        potx=   vbuahbusuk+vbuahkrgmatang+vbuahsakit+vjanjangkosong+vlwtmatang+vmentah+vtkpanjang+vtigakilo;

        document.getElementById('POTONGAN').value=potx.toFixed(0);
        
}
function loa(key,field){
    clearTimeout(mainReminder);
        function response_divisiku()
        {
                 if(con.readyState==4)
                 {
                        if(con.status==200)
                        {
                           //alert(con.responseText);
                           document.getElementById('divcode').innerHTML=con.responseText;
                           //unlock();
                           startReminder();
                        }
                        else
                        {
                          //unlock();
                          error_catch(con.status);
                        }
                 }
        }

                //alert(document.getElementById('kelompok').innerHTML);
                param='key='+key+'&field='+field;
                if (field == 'Unit') {
                        //alert (param);
                        hubungkan_post('load_divisi.php', param, response_divisiku);
                }		
}
/*
function response_divisi()
{
     if(con.readyState==4)
     {
        if(con.status==200)
        {
                   //alert(con.responseText);
                   document.getElementById('divcode').innerHTML=con.responseText;
                   //unlock();
                }
        else
        {
                  //unlock();
          error_catch(con.status);
        }
     }
}
*/	
function saveTbsInt(pecah,pecahkan,event){
        window.clearInterval(mainReminder);
        var _spb1 = document.getElementById('SPBNO').value;
        var _spb2 = document.getElementById('unitcode').value;
        var _spb3 = document.getElementById('divcode').value;
        var _spb4 = document.getElementById('bulan').value;
        var _spb5 = document.getElementById('tahun').value;
        //_SPBNO = _spb1 + '/' + _spb2 + '/' + _spb3 + '/' + _spb4 + '/' + _spb5;
        _SPBNO = _spb1 + '/' + _spb3 + '/' + _spb4 + '/' + _spb5;
    var IDWB = document.getElementById('IDWB').value;
        var TICKETNO = document.getElementById('TICKETNO').value;
        _TICKETNO2 = IDWB+TICKETNO;
        _IDWB = document.getElementById('IDWB').value;
        if (document.getElementById('button2').disabled){ // TIMBANG MASUK
                OUTIN = 1;
                PRODUCTCODE = 40000003;
                SLOC = "MGDG";
                MILLCODE = "H01M";
                _TICKETNO = document.getElementById('TICKETNO').value;
                _UNITCODE = document.getElementById('unitcode').options[document.getElementById('unitcode').selectedIndex].value;
                _DIVCODE = document.getElementById('divcode').options[document.getElementById('divcode').selectedIndex].value;
                _VEHNOCODE = document.getElementById('VEHNOCODE').value;//options[document.getElementById('VEHNOCODE').selectedIndex].value;
                _TRPCODE = document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
                _DRIVER = document.getElementById('DRIVER').value;
                _JJG = document.getElementById('JUMJJG').value;
                _THNTNM = document.getElementById('TAHUNTANAM').value;
                _BRONDOLAN = document.getElementById('BRONDOLAN').value;
                _BERATKIRIM = document.getElementById('BERATKIRIM').value;
                _DATEIN = document.getElementById('datein').value;
                _WEIGH1 = document.getElementById('WEIGH1').value;
                _WEIGH2 = document.getElementById('WEIGH2').value;
                _NETTO = document.getElementById('NETTO').value;
                _IDWB = document.getElementById('IDWB').value;
                _POTONGAN = document.getElementById('POTONGAN').value;
                if (_spb1=='') {
                        alert('No.SPB/DO harus diisi');
                        document.getElementById('SPBNO').focus();
                }
                else
                if (document.getElementById('unitcode').options[document.getElementById('unitcode').selectedIndex].value == 0) {
                        alert('Field Unit dari No. SPB belum terisi');
                        document.getElementById('unitcode').options[document.getElementById('unitcode').focus()];
                }
                else if (_VEHNOCODE==0){//(document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value == 0) {
                        alert('Field No.Kendaraan Belum Dipilih');
                        document.getElementById('VEHNOCODE').focus();
                }
                else if (document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value == 0) {
                        alert('Field Transporter Belum Dipilih');
                        document.getElementById('TRPCODE').focus();
                }
                else if (_DRIVER.length < 1) {
                        alert('Nama Supir masih kosong belum diisi');
                        document.getElementById('DRIVER').focus();
                }
                else if (_JJG=='0' || _JJG=='') {
                        alert('Jumlah Janjang masih kosong belum diisi');
                        document.getElementById('JUMJJG').focus();
                }
                /*else if (_THNTNM.length < 1) {
                        alert('Tahun Tanam masih kosong belum diisi');
                        document.getElementById('TAHUNTANAM').focus();
                }*/
                else if (_DATEIN.length < 1) {
                        alert('Field Tanggal Masih Kosong');
                        document.getElementById('datein').focus();
                }
                else
                if (document.getElementById('WEIGH1').length < 1) {
                        alert('Berat ke-1 Belum Terisi!!! ');
                        document.getElementById('WEIGH1').focus();
                }
                else if (_WEIGH1 <= 0) {
                        alert('Berat ke-1 Tidak Boleh Lebih Kecil Dari Nol Atau Sama Dengan Nol');
                        document.getElementById('WEIGH1').focus();
                }
                else {
                        param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&SPBNO=' + _SPBNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&WEIGH1=' + _WEIGH1;
                        param += '&SLOC=' + SLOC + '&VEHNOCODE=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&UNITCODE=' + _UNITCODE + '&DIVISI=' + _DIVCODE + '&TAHUNTANAM=' + _THNTNM;
                        param += '&JJG=' + _JJG + '&BRONDOLAN=' + _BRONDOLAN + '&DRIVER=' + _DRIVER + '&BERATKIRIM=' + _BERATKIRIM + '&TICKETNO2=' + _TICKETNO2;
                        param += '&IDWB=' + _IDWB + '&MILLCODE=' + MILLCODE+'&POTONGAN='+_POTONGAN;
                        if (confirm('Anda Yakin Ingin Menyimpan SPB NO :' + ' ' + _SPBNO + ' ' + 'Dari UNIT :' + ' ' + _UNITCODE + ', DIVISI :' + _DIVCODE + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', SUPIR :' + ' ' + _DRIVER + ' ' + ', JML.JJG :' + ' ' + _JJG + ' ' + ', TONNASE :' + ' ' + _WEIGH1 + 'Kg')) {
                             post_response_text('simpan_trxtbs_int.php',param,respon);
                        }
                }
        }
        else { // TIMBANG KELUAR
                        OUTIN = 0;
                        PRODUCTCODE = 40000003;
                        SLOC = "MGDG";
                        MILLCODE = "H01M";
                        _TICKETNO = document.getElementById('TICKETNO').value;
                        _UNITCODE = document.getElementById('unitcode').options[document.getElementById('unitcode').selectedIndex].value;
                        _DIVCODE = document.getElementById('divcode').options[document.getElementById('divcode').selectedIndex].value;
                        _VEHNOCODE = document.getElementById('VEHNOCODE').value;//.options[document.getElementById('VEHNOCODE').selectedIndex].value;
                        _TRPCODE = document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
                        _DRIVER = document.getElementById('DRIVER').value;
                        _JJG = document.getElementById('JUMJJG').value;
                        _THNTNM = document.getElementById('TAHUNTANAM').value;
                        _BRONDOLAN = document.getElementById('BRONDOLAN').value;
                        _BERATKIRIM = document.getElementById('BERATKIRIM').value;
                        _DATEIN = document.getElementById('datein').value;
                        _DATEOUT = document.getElementById('dateout').value;
                        _WEIGH1 = document.getElementById('WEIGH1').value;
                        //_WEIGH2 = document.getElementById('WEIGH2').value;
                        _WEIGH2 = document.getElementById('WEIGH').value
                        //_NETTO = document.getElementById('NETTO').value;
        //koresi===================
        x=parseInt(_WEIGH1);
        y=parseInt(_WEIGH2);
        _NETTO = x-y;
        //============================
        ambil_tanggal2();
                buahbusuk          =document.getElementById('buahbusuk').value;
                buahkrgmatang =document.getElementById('buahkrgmatang').value;
                buahsakit            =document.getElementById('buahsakit').value;
                janjangkosong   =document.getElementById('janjangkosong').value; 
                lwtmatang          =document.getElementById('lwtmatang').value; 
                mentah               =document.getElementById('mentah').value; 
                tkpanjang           =document.getElementById('tkpanjang').value;
				tigakilo           =document.getElementById('tigakilo').value;
       
                        _IDWB = document.getElementById('IDWB').value;
                       _POTONGAN = document.getElementById('POTONGAN').value;
                        if (_DATEOUT.length < 1) {
                                alert('Kolom tanggal ke-2 belum terisi!!!');
                                document.getElementById('dateout').focus();
                        }
                        else
                                if (document.getElementById('WEIGH2').length < 1) {
                                        alert('Berat ke-2 Belum Terisi!!! ');
                                        document.getElementById('WEIGH2').focus();
                                }
                        else
                                if (_WEIGH2 <= 0) {
                                        alert('Berat ke-2 Tidak Boleh Lebih Kecil Atau Sama Dengan Nol');
                                        document.getElementById('WEIGH2').focus();
                                }
                        else
                                if (_NETTO <= 0) {
                                        alert('Netto Tidak Boleh Lebih Kecil Atau Sama Dengan Nol');
                                        document.getElementById('NETTO').focus();
                                }
                        else if(parseInt(_POTONGAN)>parseInt(_NETTO))
                            {
                                alert('Potongan terlalu besar');
                            }     
                        else {
                                param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&SPBNO=' + _SPBNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&WEIGH1=' + _WEIGH1;
                                param += '&SLOC=' + SLOC + '&VEHNOCODE=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&UNITCODE=' + _UNITCODE + '&DIVISI=' + _DIVCODE + '&TAHUNTANAM=' + _THNTNM;
                                param += '&JJG=' + _JJG + '&BRONDOLAN=' + _BRONDOLAN + '&DRIVER=' + _DRIVER + '&BERATKIRIM=' + _BERATKIRIM + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                                param += '&NETTO=' + _NETTO + '&DATEOUT=' + _DATEOUT + '&WEIGH2=' + _WEIGH2 + '&MILLCODE=' + MILLCODE+'&POTONGAN='+_POTONGAN;
                                param +='&buahbusuk='+buahbusuk+'&buahkrgmatang='+buahkrgmatang+'&buahsakit='+buahsakit+'&janjangkosong='+janjangkosong+'&lwtmatang='+lwtmatang;
                                param +='&mentah='+mentah+'&tkpanjang='+tkpanjang+'&tigakilo='+tigakilo;
                                param+='&pecahtiket='+pecahkan;
                                pesan='Anda Yakin Ingin Menyimpan SPB NO :' + ' ' + _SPBNO + ' ' + ' Dengan NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', NETTO :' + ' ' + _NETTO + 'Kg?';
                                valid=true;
                                if (pecahkan==1){
                                    _SPBNO2 = document.getElementById('SPBNO2').value;
                                    if (_SPBNO2=='') {
                                        alert('No.SPB Kedua harus diisi');
                                        document.getElementById('SPBNO2').focus();
                                        valid=false;
                                    } else {
                                        _SPBNO2 = _SPBNO2 + '/' + _spb3 + '/' + _spb4 + '/' + _spb5;
                                        pesan='Anda Yakin Ingin Memecah SPB NO: ' + ' ' + _SPBNO + ' dengan SPB NO: ' + _SPBNO2 + '?';
                                    }
                                }
                                 if (valid==true && confirm(pesan)) {
                                     if (pecah==1){
                                        if (confirm('Ingin memecah tiket ini?')){
                                            pecahTiket('PECAH TIKET','<div id=formPecahTiket></div>',event);
                                        } else {
                                            post_response_text('simpan_trxtbs_int.php',param,respon);
                                        }
                                     } else {
                                         if (pecahkan==1){
                                             _TICKETNOPECAH = document.getElementById('TICKETNOPECAH').value;
                                             pct=document.getElementById('pct').value;
                                             jjg1=document.getElementById('jjg1st').value;
                                             param+='&TICKETNOPECAH=' + _TICKETNOPECAH+'&SPBNO2='+_SPBNO2+'&jjg1st='+jjg1;
                                         }
                                        post_response_text('simpan_trxtbs_int.php',param,respon);
                                     }
                                 }
                         }
        }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="trx_tbs_int.php";
                }
                else {
                      tx=con.responseText;

                                        if(tx=='0' || tx=='000'){
                                                //o_DATEOUT = document.getElementById('dateout');
                                                o_weigh2 = document.getElementById('button2');
                                                if (o_weigh2.disabled)
                                                        OUTIN = 1;
                                                else
                                                        OUTIN = 0;
                                        if (OUTIN == 1) {
                                                alert('Timbang 1 Berhasil di simpan');
                                                window.location.reload();
                                        }
                                        else {
                                                alert('Timbang 2 Berhasil Disimpan');
                                                var IDWB = document.getElementById('IDWB').value;
                                                var TICKETNO = document.getElementById('TICKETNO').value;
                                                _TICKETNO2 = IDWB+TICKETNO;
                                                if(window.open('fpdf/kartu_timbang_tbs_form.php?TICKETNO='+_TICKETNO2+'&IDWB='+IDWB,'location=0','resizable=0','scrollbars=0','navigation bar=0','width=100','height=100')){
                                                    if (pecahkan==1){
                                                        var TICKETNOPECAH = document.getElementById('TICKETNOPECAH').value;
                                                        _TICKETNOPECAH = IDWB+TICKETNOPECAH;
                                                        if(window.open('fpdf/kartu_timbang_tbs_form.php?TICKETNO='+_TICKETNOPECAH+'&IDWB='+IDWB,'location=1','resizable=0','scrollbars=0','navigation bar=0','width=100','height=100')){
                                                            window.location.reload();
                                                        }
                                                    } else {
                                                        window.location.reload();
                                                    }
                                                }
                                        }
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

function load(_TICKETNO,_SPBNO,_VEHNOCODE,_TRPCODE,_DRIVER,_JMLHJJG,_TAHUNTANAM,_BRONDOLAN,_BERATKIRIM,_DATEIN,_WEI1ST,_UNITCODE,_DIVCODE,_TRPNAME)
{
       o_TICKETNO=document.getElementById('TICKETNO');
        o_SPBNO=document.getElementById('SPBNO');
        o_VEHNOCODE=document.getElementById('VEHNOCODE');

        o_TRPCODE=document.getElementById('TRPCODE');
        o_DRIVER=document.getElementById('DRIVER');
        o_JMLHJJG=document.getElementById('JUMJJG');
        o_TAHUNTANAM=document.getElementById('TAHUNTANAM');
        o_BRONDOLAN=document.getElementById('BRONDOLAN');
        o_BERATKIRIM=document.getElementById('BERATKIRIM');
        o_DATEIN=document.getElementById('datein');
        o_WEI1ST=document.getElementById('WEIGH1');
        //_opt=new Option(_nama_wilayah,_WILCODE);
        var temp;
        var temp2;
        var temp3;
        var temp4;
        /*
        for (x=0;x<=(document.getElementById('VEHNOCODE').length-1);x++)
        {
                if(document.getElementById('VEHNOCODE').options[x].value==_VEHNOCODE)
                  temp=x;
        }
    */ 
        for (a=0;a<=(document.getElementById('unitcode').length-1);a++)
        {
                if(document.getElementById('unitcode').options[a].value==_UNITCODE)
                    document.getElementById('unitcode').options[a].selected=true;
        }
        for (b=0;b<=(document.getElementById('divcode').length-1);b++)
        {
                if(document.getElementById('divcode').options[b].value==_DIVCODE)
                    document.getElementById('divcode').options[b].selected=true;
        }
		document.getElementById('divcode').options[0].value=_DIVCODE;
		document.getElementById('divcode').options[0].text=_DIVCODE;
		document.getElementById('divcode').options[0].selected=true;
        for (c=0;c<=(document.getElementById('TRPCODE').length-1);c++)
        {
                if(document.getElementById('TRPCODE').options[c].value==_TRPCODE)
                    document.getElementById('TRPCODE').options[c].selected=true;
        }        
        if(isNaN(temp))
                temp2=0;
        //document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].text=_VEHNOCODE;
        //document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value=_VEHNOCODE;
        document.getElementById('VEHNOCODE').value=_VEHNOCODE;
        document.getElementById('VEHNOCODE').disabled=true;


                o_TICKETNO.value=_TICKETNO;
                o_SPBNO.value=_SPBNO;
                o_DRIVER.value=_DRIVER;
                o_JMLHJJG.value=_JMLHJJG;
                o_TAHUNTANAM.value=_TAHUNTANAM;
                o_BRONDOLAN.value=_BRONDOLAN;
                o_BERATKIRIM.value=_BERATKIRIM;
                o_DATEIN.value=_DATEIN;
                o_WEI1ST.value=_WEI1ST;
                document.getElementById('SPBNO').disabled = true;
                document.getElementById('unitcode').disabled = true;
                document.getElementById('divcode').disabled = true;
                document.getElementById('VEHNOCODE').disabled = true;
                document.getElementById('TRPCODE').disabled = true;
                document.getElementById('DRIVER').disabled = true;
                //document.getElementById('JUMJJG').disabled = true;
                //document.getElementById('TAHUNTANAM').disabled = true;
                //document.getElementById('BRONDOLAN').disabled = true;
                document.getElementById('BERATKIRIM').disabled = true;
                document.getElementById('datein').disabled = true;
                document.getElementById('WEIGH1').disabled = true;
                document.getElementById('button1').disabled = true;
                document.getElementById('dateout').disabled = true;
                document.getElementById('WEIGH2').disabled = true;
                document.getElementById('button2').disabled = false;

}
//================================================================TbsEks
function saveTbsEks(){
        window.clearInterval(mainReminder);
        o_DATEOUT = document.getElementById('dateout');
        o_weigh2 = document.getElementById('button2');
        var IDWB = document.getElementById('IDWB').value;
        var TICKETNO = document.getElementById('TICKETNO').value;
        _TICKETNO2 = IDWB+TICKETNO;
        o_IDWB = document.getElementById('IDWB');
        _IDWB = o_IDWB.value;
        if (o_weigh2.disabled){
                _CEKBOX = 1
                OUTIN = 1;
                PRODUCTCODE = 40000003;
                SLOC = "MGDG";
                MILLCODE = "H01M";
                _TICKETNO = document.getElementById('TICKETNO').value;
                _SPBNO = document.getElementById('SPBNO').value;
                _DRIVER = document.getElementById('DRIVER').value;
                _JJG = document.getElementById('JUMJJG').value;
                _JJG2 = document.getElementById('JUMJJG2').value;
                _JJG3 = document.getElementById('JUMJJG3').value;
                _THNTNM=document.getElementById('TAHUNTANAM').value;
                _THNTNM2=document.getElementById('TAHUNTANAM2').value;
                _THNTNM3=document.getElementById('TAHUNTANAM3').value;
                _BRONDOLAN = document.getElementById('BRONDOLAN').value;
                _BRONDOLAN2 = document.getElementById('BRONDOLAN2').value;
                _BRONDOLAN3 = document.getElementById('BRONDOLAN3').value;
                _BERATKIRIM = document.getElementById('BERATKIRIM').value;
                _DATEIN = document.getElementById('datein').value;
                _DATEOUT = document.getElementById('dateout').value;
                _WEIGH1 = document.getElementById('WEIGH1').value;
                _WEIGH2 = document.getElementById('WEIGH2').value;
                _NETTO = document.getElementById('NETTO').value;
                _IDWB = document.getElementById('IDWB').value;
                _POTONGAN = document.getElementById('POTONGAN').value;
                _TRPCODE = document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
                _TRPNAME = document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].text;

                _VEHNOCODE = document.getElementById('VEHNOCODE').value;

                if(_VEHNOCODE=='')
                {
                        alert('No. Kendaraan belum dipilih');
                }
                else if (document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value==0) {
                                alert('Pengirim belum dipilih');
                                document.getElementById('TRPCODE').focus();
                        }
                else if (document.getElementById('datein').value.length < 1){
                        alert('Field Tanggal Masih Kosong');
                        document.getElementById('datein').focus();
                }
                else if (_WEIGH1 <= 0) {
                        alert('Berat ke-1 Tidak Boleh Lebih Kecil dari Nol Atau Sama Dengan Nol ');
                        document.getElementById('WEIGH1').focus();
                }
                else if (document.getElementById('WEIGH1').length < 1) {
                        alert('Berat ke-1 Belum Terisi!!! ');
                        document.getElementById('WEIGH1').focus();
                }
                else if (_DRIVER.length < 1) {
                        alert('Nama Supir masih kosong belum diisi');
                        document.getElementById('DRIVER').focus();
                }					
                else {
                        param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&SPBNO=' + _SPBNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT +' &WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2;
                        param += '&SLOC=' + SLOC + '&VEHNOCODE=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&TAHUNTANAM=' + _THNTNM + '&TAHUNTANAM2=' + _THNTNM2 + '&TAHUNTANAM3=' + _THNTNM3 + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                        param += '&JJG=' + _JJG + '&JJG2=' + _JJG2 + '&JJG3=' + _JJG3 + '&BRONDOLAN=' + _BRONDOLAN + '&BRONDOLAN2=' + _BRONDOLAN2 + '&BRONDOLAN3=' + _BRONDOLAN3 + '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO;
                        param += '&BERATKIRIM=' + _BERATKIRIM + '&CEKBOX=' + _CEKBOX + '&MILLCODE=' + MILLCODE+'&POTONGAN='+_POTONGAN;
                        if (confirm('Anda Yakin Ingin Menyimpan SPB Eksternal NO :' + ' ' + _SPBNO + ' ' + 'Pengirim :' + ' ' + _TRPNAME + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', SUPIR :' + ' ' + _DRIVER + ' ' + ', JML.JJG I :' + ' ' + _JJG + ' ' + ', JML.JJG II :' + ' ' + _JJG2 + ', JML.JJG III :'+ ' ' + _JJG3 + ', TONNASE :' + ' ' + _WEIGH1 + 'Kg')) {
                                post_response_text('simpan_trxtbs_ext.php',param,respon);
                        }
                }
        }
        else {
                _CEKBOX = 1
                OUTIN = 0;
                PRODUCTCODE = 40000003;
                SLOC = "MGDG";
                MILLCODE = "H01M";
                _TICKETNO = document.getElementById('TICKETNO').value;
                _SPBNO = document.getElementById('SPBNO').value;
                _DRIVER = document.getElementById('DRIVER').value;
                _JJG = document.getElementById('JUMJJG').value;
                _JJG2 = document.getElementById('JUMJJG2').value;
                _JJG3 = document.getElementById('JUMJJG3').value;
                _THNTNM=document.getElementById('TAHUNTANAM').value;_THNTNM2=document.getElementById('TAHUNTANAM2').value;_THNTNM3=document.getElementById('TAHUNTANAM3').value;
                _BRONDOLAN = document.getElementById('BRONDOLAN').value;_BRONDOLAN2 = document.getElementById('BRONDOLAN2').value;_BRONDOLAN3 = document.getElementById('BRONDOLAN3').value;
                _BERATKIRIM = document.getElementById('BERATKIRIM').value;
                _DATEIN = document.getElementById('datein').value;
                _DATEOUT = document.getElementById('dateout').value;
                _WEIGH1 = document.getElementById('WEIGH1').value;
                //_WEIGH2 = document.getElementById('WEIGH2').value;
                _WEIGH2 = document.getElementById('WEIGH').value;		
                //_NETTO = document.getElementById('NETTO').value;

        //koresi===================
        x=parseInt(_WEIGH1);
        y=parseInt(_WEIGH2);
        _NETTO = x-y;
        //============================
        ambil_tanggal2();
                buahbusuk          =document.getElementById('buahbusuk').value;
                buahkrgmatang =document.getElementById('buahkrgmatang').value;
                buahsakit            =document.getElementById('buahsakit').value;
                janjangkosong   =document.getElementById('janjangkosong').value; 
                lwtmatang          =document.getElementById('lwtmatang').value; 
                mentah               =document.getElementById('mentah').value; 
                tkpanjang           =document.getElementById('tkpanjang').value;
				tigakilo           =document.getElementById('tigakilo').value;				
                
                _IDWB = document.getElementById('IDWB').value;				
                _POTONGAN = document.getElementById('POTONGAN').value;
                _TRPCODE = document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
                _TRPNAME = document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].text;
                _VEHNOCODE = document.getElementById('VEHNOCODE').value;//options[document.getElementById('VEHNOCODE').selectedIndex].value;
                if (_DATEOUT <= 0) {
                        alert('Kolom tanggal belum terisi');
                        document.getElementById('dateout').focus();
                }
                else
                if (_WEIGH2 <= 0) {
                        alert('Berat ke-2 Tidak Boleh Minus atau Sama Dengan Nol');
                        document.getElementById('WEIGH2').focus();
                }
                else
                if (document.getElementById('WEIGH2').length < 1) {
                        alert('Berat ke-2 Belum Terisi!!! ');
                        document.getElementById('WEIGH2').focus();
                }
                else
                if (_NETTO <= 0) {
                        alert('Netto Tidak Boleh Minus atau Sama Dengan Nol');
                        document.getElementById('NETTO').focus();
                }
                else if (_JJG=='0' || _JJG=='') {
                        alert('Jumlah Janjang masih kosong belum diisi');
                        document.getElementById('JUMJJG').focus();
                }				
                else
                if (document.getElementById('NETTO').length < 1) {
                        alert('Netto Belum Terisi');
                        document.getElementById('NETTO').focus();
                }
                else if(parseInt(_POTONGAN)>parseInt(_NETTO))
                {
                    alert('Potongan terlalu besar');
                }
                else {
                        param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&SPBNO=' + _SPBNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT + '&WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2 + '&MILLCODE=' + MILLCODE;
                        param += '&SLOC=' + SLOC + '&VEHNOCODE=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&TAHUNTANAM=' + _THNTNM + '&TAHUNTANAM2=' + _THNTNM2 + '&TAHUNTANAM3=' + _THNTNM3 + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                        param += '&JJG=' + _JJG + '&JJG2=' + _JJG2 + '&JJG3=' + _JJG3 + '&BRONDOLAN=' + _BRONDOLAN +'&BRONDOLAN2=' + _BRONDOLAN2 + '&BRONDOLAN3=' + _BRONDOLAN3 + '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO;
                        param += '&BERATKIRIM=' + _BERATKIRIM + '&CEKBOX=' + _CEKBOX+'&POTONGAN='+_POTONGAN;
                        param +='&buahbusuk='+buahbusuk+'&buahkrgmatang='+buahkrgmatang+'&buahsakit='+buahsakit+'&janjangkosong='+janjangkosong+'&lwtmatang='+lwtmatang;
                        param +='&mentah='+mentah+'&tkpanjang='+tkpanjang+'&tigakilo='+tigakilo;                       
                        //alert(param);
                        if (confirm('Anda Yakin Ingin Menyimpan SPB Eksternal NO :' + ' ' + _SPBNO + ' ' + ' Dengan NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', NETTO :' + ' ' + _NETTO + 'Kg')) {
                                post_response_text('simpan_trxtbs_ext.php',param,respon);
                                }
                        }
        }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="trx_tbs_ext.php";
                }
                else {
                      tx=con.responseText;
                      //alert(tx);
                                        if(tx=='0'){
                                                //o_DATEOUT = document.getElementById('dateout');
                                                o_weigh2 = document.getElementById('button2');
                                                if (o_weigh2.disabled)
                                                        OUTIN = 1;
                                                else
                                                        OUTIN = 0;
                                        if (OUTIN == 1) {
                                                alert('Berhasil di simpan');
                                                window.location.reload();
                                        }
                                        else {
                                                alert('Berhasil Disimpan');
                                                var IDWB = document.getElementById('IDWB').value;
                                                var TICKETNO = document.getElementById('TICKETNO').value;
                                                _TICKETNO2 = IDWB+TICKETNO;
                                                if(window.open('fpdf/kartu_timbang_tbs_form.php?TICKETNO='+_TICKETNO2+'&IDWB='+IDWB,'location=0','resizable=0','scrollbars=0','navigation bar=0','width=100','height=100'))
                                                window.location.reload();
                                        }
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

function loadeks(_TICKETNO,_JENISSPB,_SPBNO,_VEHNOCODE,_TRPCODE,_DRIVER,_JMLHJJG,_JMLHJJG2,_JMLHJJG3,_TAHUNTANAM,_TAHUNTANAM2,_TAHUNTANAM3,_BRONDOLAN,_BRONDOLAN2,_BRONDOLAN3,_BERATKIRIM,_DATEIN,_WEI1ST,_TRPNAME)
{

        o_TICKETNO=document.getElementById('TICKETNO');
        o_SPBNO=document.getElementById('SPBNO');
        o_VEHNOCODE=document.getElementById('VEHNOCODE');
        o_TRPCODE=document.getElementById('TRPCODE');
        o_DRIVER=document.getElementById('DRIVER');
        o_JMLHJJG=document.getElementById('JUMJJG');
        o_JMLHJJG2=document.getElementById('JUMJJG2');
        o_JMLHJJG3=document.getElementById('JUMJJG3');
        o_TAHUNTANAM=document.getElementById('TAHUNTANAM');
        o_TAHUNTANAM2=document.getElementById('TAHUNTANAM2');
        o_TAHUNTANAM3=document.getElementById('TAHUNTANAM3');
        o_BRONDOLAN=document.getElementById('BRONDOLAN');
        o_BRONDOLAN2=document.getElementById('BRONDOLAN2');
        o_BRONDOLAN3=document.getElementById('BRONDOLAN3');
        o_BERATKIRIM=document.getElementById('BERATKIRIM');
        o_DATEIN=document.getElementById('datein');
        o_WEI1ST=document.getElementById('WEIGH1');
                o_TICKETNO.value=_TICKETNO;
                o_SPBNO.value=_SPBNO;
                o_DRIVER.value=_DRIVER;
                o_JMLHJJG.value=_JMLHJJG;
                o_JMLHJJG2.value=_JMLHJJG2;
                o_JMLHJJG3.value=_JMLHJJG3;
                o_TAHUNTANAM.value=_TAHUNTANAM;
                o_TAHUNTANAM2.value=_TAHUNTANAM2;
                o_TAHUNTANAM3.value=_TAHUNTANAM3;
                o_BRONDOLAN.value=_BRONDOLAN;
                o_BRONDOLAN2.value=_BRONDOLAN2;
                o_BRONDOLAN3.value=_BRONDOLAN3;
                o_BERATKIRIM.value=_BERATKIRIM;
                o_DATEIN.value=_DATEIN;
                o_WEI1ST.value=_WEI1ST;
                document.getElementById('VEHNOCODE').value=_VEHNOCODE;
                document.getElementById('VEHNOCODE').disabled=true;
                //document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value=_VEHNOCODE;
                        for (c=0;c<=(document.getElementById('TRPCODE').length-1);c++)
                        {
                                        if(document.getElementById('TRPCODE').options[c].value==_TRPCODE)
                                                document.getElementById('TRPCODE').options[c].selected=true;
                        }  
                document.getElementById('TRPCODE').disabled = true;
                //document.getElementById('SPBNO').disabled = true;
                //document.getElementById('VEHNOCODE').disabled = true;
                //document.getElementById('DRIVER').disabled = true;
                //document.getElementById('JUMJJG').disabled = true;
                //document.getElementById('JUMJJG2').disabled = true;
                //document.getElementById('JUMJJG3').disabled = true;
                //document.getElementById('TAHUNTANAM').disabled = true;
                //document.getElementById('TAHUNTANAM2').disabled = true;
                //document.getElementById('TAHUNTANAM3').disabled = true;
                //document.getElementById('BRONDOLAN').disabled = true;
                //document.getElementById('BRONDOLAN2').disabled = true;
                //document.getElementById('BRONDOLAN3').disabled = true;
                //document.getElementById('BERATKIRIM').disabled = true;
                document.getElementById('datein').disabled = true;
                document.getElementById('WEIGH1').disabled = true;
                document.getElementById('button1').disabled = true;
                document.getElementById('dateout').disabled = true;
                document.getElementById('WEIGH2').disabled = true;
                document.getElementById('button2').disabled = false;
}
//===================================================Lain-lain

function saveLain(){
                window.clearInterval(mainReminder);
        o_DATEOUT = document.getElementById('dateout');
        o_weigh2 = document.getElementById('WEIGH2');
        var IDWB = document.getElementById('IDWB').value;
        var TICKETNO = document.getElementById('TICKETNO').value;
        _TICKETNO2 = IDWB+TICKETNO;
        o_IDWB = document.getElementById('IDWB');
        _IDWB = o_IDWB.value;
        if (o_DATEOUT.disabled && o_weigh2.disabled){
                OUTIN = 1;
                SLOC = "RGDG";
                MILLCODE = "H01M";
                _TICKETNO = document.getElementById('TICKETNO').value;
                _SPBNO = document.getElementById('SPBNO').value;
                _VEHNOCODE = document.getElementById('VEHNOCODE').value;
                _DRIVER = document.getElementById('DRIVER').value;
                _PENERIMA = document.getElementById('PENERIMA').value;
                _DATEIN = document.getElementById('datein').value;
                _DATEOUT = document.getElementById('dateout').value;
                _WEIGH1 = document.getElementById('WEIGH1').value;
                _WEIGH2 = document.getElementById('WEIGH2').value;
                _NETTO = document.getElementById('NETTO').value;
                _TRPCODE=document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
                //_VEHNOCODE = document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value;
                _VEHNOCODE = document.getElementById('VEHNOCODE').value;
                PRODUCTCODE = document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].value;
                PRODUCTNAME = document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].text;
                /*if (_SPBNO.length == 0) {
                        alert('Field No. Surat Pengantar masih kosong');
                        document.getElementById('SPBNO').focus();
                }
                else*/
                        if (_VEHNOCODE==''){//(document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value==0) {
                                alert('No. Kendaraan belum dipilih');
                                document.getElementById('VEHNOCODE').focus();//options[document.getElementById('VEHNOCODE').focus()];
                }
                else
                        if (document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value==0) {
                                alert('Pengirim belum dipilih');
                                document.getElementById('TRPCODE').focus();//options[document.getElementById('TRPCODE').focus()];
                        }
                else
                        if (document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].value == 0) {
                                alert('Product belum dipilih');
                                document.getElementById('PRODUCT').focus();//options[document.getElementById('PRODUCT').focus()];
                        }
                /*else
                        if (_DRIVER.length < 1) {
                                alert('Nama Supir belum diisi');
                                document.getElementById('DRIVER').focus();
                        }*/
                else
                        if (_PENERIMA.length < 1) {
                                alert('Penerima belum diisi');
                                document.getElementById('PENERIMA').focus();
                        }
                else
                        if (_DATEIN.length < 1) {
                                alert('Field Tanggal belum diinput');
                                document.getElementById('datein').focus();
                        }
                else
                        if (_WEIGH1 <= 0) {
                                alert('Berat ke-1 Tidak Boleh Lebih Kecil dari Nol Atau Sama Dengan Nol');
                                document.getElementById('WEIGH1').focus();
                        }
                else
                        if (document.getElementById('WEIGH1').length < 1) {
                                alert('Berat ke-1 Belum Terisi!!! ');
                                document.getElementById('WEIGH1').focus();
                        }
                else {
                                param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&SPBNO=' + _SPBNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT + ' &WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2;
                                param += '&SLOC=' + SLOC + '&VEHNO=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&MILLCODE=' + MILLCODE;
                                param += '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO + '&PENERIMA=' + _PENERIMA + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + IDWB;
                                if (confirm('Anda Yakin Ingin Menyimpan Data Timbang :' + ' ' + PRODUCTNAME + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', SUPIR :' + ' ' + _DRIVER + ' ' + ', TONNASE :' + ' ' + _WEIGH1 + 'Kg')) {
                                        post_response_text('simpan_trxlain.php',param,respon);
                        }
                }
        }
        else {
                OUTIN = 0;
                SLOC = "RGDG";
                MILLCODE = "LRGM";
                _TICKETNO = document.getElementById('TICKETNO').value;
                _SPBNO = document.getElementById('SPBNO').value;
                _DRIVER = document.getElementById('DRIVER').value;
                _PENERIMA = document.getElementById('PENERIMA').value;
                _DATEIN = document.getElementById('datein').value;
                _DATEOUT = document.getElementById('dateout').value;
                _WEIGH1 = document.getElementById('WEIGH1').value;
                _WEIGH2 = document.getElementById('WEIGH2').value;
                _NETTO = document.getElementById('NETTO').value;
                _TRPCODE=document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
                _DRIVER = o_DRIVER.value;
                _VEHNOCODE = document.getElementById('VEHNOCODE').value;//options[document.getElementById('VEHNOCODE').selectedIndex].value;
                PRODUCTCODE = document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].value;
                PRODUCTNAME = document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].text;
                if (_DATEOUT.length < 1) {
                        alert('Kolom tanggal belum terisi');
                        document.getElementById('dateout').focus();
                }
                else
                        if (document.getElementById('WEIGH2').length < 1) {
                                alert('Berat ke-2 Belum Terisi!!! ');
                                document.getElementById('WEIGH2').focus();
                        }
                else
                        if (document.getElementById('WEIGH2').length < 1) {
                                alert('Berat ke-2 Belum Terisi!!! ');
                                document.getElementById('WEIGH2').focus();
                        }
                else
                        if (_WEIGH2 <= 0) {
                                alert('Berat ke-2 Tidak Boleh Lebih Kecil dari Nol Atau Sama Dengan Nol');
                                document.getElementById('WEIGH2').focus();
                        }
                else
                        if (_NETTO <= 0) {
                                alert('Netto Tidak Boleh Lebih Kecil dari Nol Atau Sama Dengan Nol');
                                document.getElementById('NETTO').focus();
                        }
                else {
                                param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&SPBNO=' + _SPBNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT + '&WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2;
                                param += '&SLOC=' + SLOC + '&VEHNO=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&MILLCODE=' + MILLCODE;
                                param += '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO + '&PENERIMA=' + _PENERIMA + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + IDWB;
                                //alert(param);
                                if (confirm('Anda Yakin Ingin Menyimpan Data Timbang :' + ' ' + PRODUCTNAME + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', TONNASE :' + ' ' + _WEIGH1 + 'Kg')) {
                                        post_response_text('simpan_trxlain.php',param,respon);
                                }
                        }
        }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="trx_lain.php";
                }
                else {
                      tx=con.responseText;
                      //alert(tx);
                                        if(tx=='0'){
                                                o_DATEOUT = document.getElementById('dateout');
                                                o_weigh2 = document.getElementById('WEIGH2');
                                                if (o_DATEOUT.disabled && o_weigh2.disabled)
                                                        OUTIN = 1;
                                                else
                                                        OUTIN = 0;
                                        if (OUTIN == 1) {
                                                alert('Berhasil di simpan');
                                                window.location.reload();
                                        }
                                        else {
                                                alert('Berhasil Disimpan');
                                                var IDWB = document.getElementById('IDWB').value;
                                                var TICKETNO = document.getElementById('TICKETNO').value;
                                                _TICKETNO2 = IDWB+TICKETNO;
                                                if(window.open('fpdf/kartu_timbang_lain_form.php?TICKETNO='+_TICKETNO2+'&IDWB='+IDWB,'location=0','resizable=0','scrollbars=0','navigation bar=0','width=100','height=100'))
                                                window.location.reload();
                                        }
                        }
            }Lain
          }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function loadlain(_TICKETNO,_VEHNOCODE,_TRPCODE,_DRIVER,_DATEIN,_WEI1ST,_PENERIMA,_TRPNAME,_PRODUCTCODE,_PRODUCTNAME,_SPNO)
{
        o_TICKETNO=document.getElementById('TICKETNO');
        o_SPNO=document.getElementById('SPBNO');
        o_VEHNOCODE=document.getElementById('VEHNOCODE');
        o_TRPCODE=document.getElementById('TRPCODE');
        o_DRIVER=document.getElementById('DRIVER');
        o_DATEIN=document.getElementById('datein');
        o_WEI1ST=document.getElementById('WEIGH1');
        o_PENERIMA=document.getElementById('PENERIMA');
        o_PRODUCT=document.getElementById('PRODUCT');
        o_TRPNAME=document.getElementById('TRPCODE');

                o_TICKETNO.value=_TICKETNO;
                o_SPNO.value=_SPNO;
                o_DRIVER.value=_DRIVER;
                o_DATEIN.value=_DATEIN;
                o_WEI1ST.value=_WEI1ST;
                o_PENERIMA.value=_PENERIMA;
                o_PRODUCT.value=_PRODUCTCODE;
                o_PRODUCT.text=_PRODUCTNAME;
                //document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value=_VEHNOCODE;
                //document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].text=_VEHNOCODE;
                document.getElementById('VEHNOCODE').value=_VEHNOCODE;
                document.getElementById('VEHNOCODE').disabled=true;
				for (c=0;c<=(document.getElementById('TRPCODE').length-1);c++)
				{
						if(document.getElementById('TRPCODE').options[c].value==_TRPCODE)
							document.getElementById('TRPCODE').options[c].selected=true;
				} 
                document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].value=_PRODUCTCODE;
                document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].text=_PRODUCTNAME;
                //document.getElementById('TRPCODE').disabled = true;
                //document.getElementById('SPBNO').disabled = true;
                //document.getElementById('VEHNOCODE').disabled = true;
                //document.getElementById('PRODUCT').disabled = true;
                //document.getElementById('PENERIMA').disabled = true;
                //document.getElementById('DRIVER').disabled = true;
                document.getElementById('datein').disabled = true;
                document.getElementById('WEIGH1').disabled = true;
                document.getElementById('button1').disabled = true;
                document.getElementById('dateout').disabled = false;
                document.getElementById('WEIGH2').disabled = false;
                document.getElementById('button2').disabled = false;
}
//=====================================================================CPO
function ambil_tanggal3() {
                var myDate = new Date();
                var tanggal,bulan,tahun,jam,menitdetik;
                var output;
                tanggal= myDate.getDate().toString();
                bulan  = (myDate.getMonth()+1).toString();
                tahun  = myDate.getFullYear().toString();
                jam     = myDate.getHours().toString();
                menit  = myDate.getMinutes().toString();
                detik  = myDate.getSeconds().toString();
                if(tanggal.length<2)
                   tanggal='0'+tanggal;
                if(bulan.length<2)
                   bulan='0'+bulan;
                if(jam.length<2)
                   jam='0'+jam;
                if(menit.length<2)
                   menit='0'+menit;
                if(detik.length<2)
                   detik='0'+detik;
                output=tanggal+'-'+bulan+'-'+tahun+" "+jam+':'+menit+':'+detik;
                document.getElementById('dateout').value=output;
         _WEIGH=document.getElementById('WEIGH').value;
        document.getElementById('WEIGH2').value = _WEIGH;
            weigh1=parseInt(document.getElementById('WEIGH1').value);
        weigh2=parseInt(document.getElementById('WEIGH2').value);
        weigh3=weigh2-weigh1;
                document.getElementById('NETTO').value = weigh3;
}
function rubah(key,field)
{
        clearTimeout(mainReminder);
        function response_product()
        {
                if(con.readyState==4)
                 {
                        if(con.status==200)
                        {
                           //alert(con.responseText);
                           //document.getElementById('SIPBNO').innerHTML=con.responseText;
                           ss=con.responseText.split(",");
                           document.getElementById('SIPBNO').innerHTML=ss[0];
                                if (ss[2] == "40000007") {
                                        document.getElementById('product1').checked = true;
                                        document.getElementById('product2').checked = false;
                                        document.getElementById('product3').checked = false;
                                        document.getElementById('PRODUCT').value = 'CPO';
                                }
                                else if(ss[2] == "40000006")
                                {
                                        document.getElementById('product2').checked = true
                                        document.getElementById('product1').checked = false;
                                        document.getElementById('product3').checked = false;
                                        document.getElementById('PRODUCT').value = 'KER';
                                }
                                else {
                                        document.getElementById('product3').checked = true
                                        document.getElementById('product1').checked = false;
                                        document.getElementById('product2').checked = false;
                                        document.getElementById('PRODUCT').value = 'CK';
                                }
                           unlock();
                           muat(document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].text,'SIPBNO');
                        }
                        else
                        {
                          unlock();
                          error_catch(con.status);
                        }
                 }
        }

        param='key='+key+'&field='+field;
        //alert(param);
        if ((field=='product1')||(field=='product2')||(field=='product3')){
                hubungkan_post('load_product.php',param,response_product);
                if (document.getElementById('product1').checked)
                        //_product=document.getElementById('product1').value='CPO';
                        _product=document.getElementById('product1').value;
                else if(document.getElementById('product2').checked)
                        //_product=document.getElementById('product2').value='KER';
                        _product=document.getElementById('product2').value;
                else
                        _product=document.getElementById('product3').value;
        }


}
function muat(key,field){
        function response_saldo(){
                if(con.readyState==4)
                 {
                        if(con.status==200)
                        {
                           //alert(con.responseText);
                           document.getElementById('SALDO').value=con.responseText;
                           unlock();
                          startReminder();
                        }
                        else
                        {
                          document.getElementById('SALDO').value=0;
                          unlock();
                          error_catch(con.status);
                        }
                 }
        }
        param='key='+key+'&field='+field;
        if (field=='SIPBNO'){
                hubungkan_post('load_saldocpo.php',param,response_saldo);
        }
}


function saveCpo(){
                window.clearInterval(mainReminder);
        var _spb1 = document.getElementById('SPNO').value;
        var _spb2 = document.getElementById('MILLCODE').value;
        var _spb3 = document.getElementById('PRODUCT').value;
        var _spb4 = document.getElementById('bulan').value;
        var _spb5 = document.getElementById('tahun').value;
        var _IDWB = document.getElementById('IDWB').value;
        var TICKETNO = document.getElementById('TICKETNO').value;
        _TICKETNO2 = _IDWB+TICKETNO;
        _SPNO = _spb1+'/'+_spb2+'/'+_spb3+'/'+_spb4+'/'+_spb5;
        o_DATEOUT = document.getElementById('dateout');
        o_weigh2 = document.getElementById('button2');
        MILLCODE = "H01M";
        if (o_weigh2.disabled)
        {
                if ((document.getElementById('product1').checked)||(document.getElementById('product2').checked)||(document.getElementById('product3').checked)) {
                        if (document.getElementById('product1').checked) {
                                PRODUCTCODE = 40000007;
                                OUTIN = 1;
                                TIPE = 1;
                                o_TICKETNO = document.getElementById('TICKETNO');
                                o_DRIVER = document.getElementById('DRIVER');
                                o_NODO = document.getElementById('NODO');
                                o_NOSEGEL = document.getElementById('NOSEGEL');
                                o_DATEIN = document.getElementById('datein');
                                o_DATEOUT = document.getElementById('dateout');
                                o_weigh1 = document.getElementById('WEIGH1');
                                o_weigh2 = document.getElementById('WEIGH2');
                                o_NETTO = document.getElementById('NETTO');
                                _TICKETNO = o_TICKETNO.value;
                                _SIPBNO = document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].text;
                                _CTRNO = document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].value;
                                //_VEHNOCODE = document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value;
                                _VEHNOCODE = document.getElementById('VEHNOCODE').value;
                                _TRPCODE=document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
                                _DRIVER = o_DRIVER.value;
                                _NODO = o_NODO.value;
                                _NOSEGEL = o_NOSEGEL.value;
                                _DATEIN = o_DATEIN.value;
                                _DATEOUT = o_DATEOUT.value;
                                _WEIGH1 = o_weigh1.value;
                                _WEIGH2 = o_weigh2.value;
                                _NETTO = o_NETTO.value;
                        if (_VEHNOCODE==''){//(document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value==0) {
                                alert('No. Kendaraan belum dipilih');
                                document.getElementById('VEHNOCODE').focus();//options[document.getElementById('VEHNOCODE').focus()];
                        }
                        else
                        if (document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value==0) {
                                alert('Transporter belum dipilih');
                                document.getElementById('TRPCODE').focus();//options[document.getElementById('TRPCODE').focus()];
                        }
                        else
                        if (_SIPBNO.length <= 0) {
                                alert('No. SIPB belum dipilih');
                                o_SIPBNO.focus();
                        }
                        /*else
                        if (_DRIVER.length <= 0) {
                                alert('Nama Supir belum diisi');
                                o_DRIVER.focus();
                        }*/
                        else
                        if (_DATEIN.length <= 0) {
                                alert('Field Tanggal masih Kosong');
                                o_DATEIN.focus();
                        }
                        else
                        if (_WEIGH1 <= 0) {
                                alert('Berat ke-1 Tidak Boleh Lebih Kecil Dari Nol Atau Sama Dengan Nol');
                                o_weigh1.focus();
                        }
                        else
                        if (document.getElementById('WEIGH1').length < 1) {
                                alert('Berat ke-1 Belum Terisi!!! ');
                                document.getElementById('WEIGH1').focus();
                        }
                        else {
                                                param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&CTRNO=' + _CTRNO + '&SIPBNO=' + _SIPBNO + '&SPNO=' + _SPNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT + ' &WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2;
                                                param += '&VEHNO=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&NOSEGEL=' + _NOSEGEL + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                                                param += '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO + '&NODO=' + _NODO + '&TIPE=' + TIPE + '&MILLCODE=' + MILLCODE;
                                                //alert(param);
                                                if (confirm('Anda Yakin Ingin Menyimpan SIPB NO :' + ' ' + _SIPBNO + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', SUPIR :' + ' ' + _DRIVER + ' ' + ', TONNASE :' + ' ' + _WEIGH1 + 'Kg')) {
                                                                post_response_text('simpan_trx_cpo_pk.php',param,respon);
                                                }
                                        }
                        }
                        if (document.getElementById('product2').checked) {
                                //PRODUCTCODE = 40000000;
                                PRODUCTCODE = 40000006;
                                OUTIN = 1;
                                TIPE = 1;
                                //SLOC = "MGDG";
                                o_TICKETNO = document.getElementById('TICKETNO');
                                //o_VEHNO = document.getElementById('VEHNO');
                                //o_TRPCODE = document.getElementById('TRPCODE');
                                o_DRIVER = document.getElementById('DRIVER');
                                o_NODO = document.getElementById('NODO');
                                o_NOSEGEL = document.getElementById('NOSEGEL');
                                o_DATEIN = document.getElementById('datein');
                                o_DATEOUT = document.getElementById('dateout');
                                o_weigh1 = document.getElementById('WEIGH1');
                                o_weigh2 = document.getElementById('WEIGH2');
                                o_NETTO = document.getElementById('NETTO');
                                //o_cekbox = document.getElementById('cek');
                                _TICKETNO = o_TICKETNO.value;
                                _SIPBNO = document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].text;
                                _CTRNO = document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].value;
                                //_TRPCODE = o_TRPCODE.value;
                                //_VEHNOCODE = document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value;
                                _VEHNOCODE = document.getElementById('VEHNOCODE').value;
                                _TRPCODE=document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
                                _DRIVER = o_DRIVER.value;
                                _NODO = o_NODO.value;
                                _NOSEGEL = o_NOSEGEL.value;
                                _DATEIN = o_DATEIN.value;
                                _DATEOUT = o_DATEOUT.value;
                                _WEIGH1 = o_weigh1.value;
                                _WEIGH2 = o_weigh2.value;
                                _NETTO = o_NETTO.value;
                                //_CEKBOX = o_cekbox.value;
                        if (_VEHNOCODE==''){//(document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value==0) {
                        alert('No. Kendaraan belum dipilih');
                        document.getElementById('VEHNOCODE').focus();//options[document.getElementById('VEHNOCODE').focus()];
                    }
                        else
                        if (document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value==0) {
                                alert('Transporter belum dipilih');
                                document.getElementById('TRPCODE').focus();//options[document.getElementById('TRPCODE').focus()];
                        }
                        else
                        if (_SIPBNO.length <= 0) {
                                alert('No. SIPB belum dipilih');
                                o_SIPBNO.focus();
                        }
                        /*else
                        if (_DRIVER.length <= 0) {
                                alert('Nama Supir belum diisi');
                                o_DRIVER.focus();
                        }*/
                        else
                        if (_DATEIN.length <= 0) {
                                alert('Field Tanggal masih Kosong');
                                o_DATEIN.focus();
                        }
                        else
                        if (_WEIGH1 <= 0) {
                                alert('Berat ke-1 Tidak Boleh Lebih Kecil Dari Nol Atau Sama Dengan Nol');
                                o_weigh1.focus();
                        }
                        else
                        if (document.getElementById('WEIGH1').length < 1) {
                                alert('Berat ke-1 Belum Terisi!!! ');
                                document.getElementById('WEIGH1').focus();
                        }
                        else {
                                param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&CTRNO=' + _CTRNO + '&SIPBNO=' + _SIPBNO + '&SPNO=' + _SPNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT + ' &WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2;
                                param += '&VEHNO=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&NOSEGEL=' + _NOSEGEL + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                                param += '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO + '&NODO=' + _NODO + '&TIPE=' + TIPE + '&MILLCODE=' + MILLCODE;
                                //alert(param);
                                if (confirm('Anda Yakin Ingin Menyimpan SIPB NO :' + ' ' + _SIPBNO + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', SUPIR :' + ' ' + _DRIVER + ' ' + ', TONNASE :' + ' ' + _WEIGH1 + 'Kg')) {
                                                post_response_text('simpan_trx_cpo_pk.php',param,respon);
                                        }
                        }
                        }
                        if (document.getElementById('product3').checked) {
                                //PRODUCTCODE = 40000003;
                                PRODUCTCODE = 40000005;
                                OUTIN = 1;
                                TIPE = 5;
                                //SLOC = "MGDG";
                                o_TICKETNO = document.getElementById('TICKETNO');
                                //o_TRPCODE = document.getElementById('TRPCODE');
                                o_DRIVER = document.getElementById('DRIVER');
                                o_NODO = document.getElementById('NODO');
                                o_NOSEGEL = document.getElementById('NOSEGEL');
                                o_DATEIN = document.getElementById('datein');
                                o_DATEOUT = document.getElementById('dateout');
                                o_weigh1 = document.getElementById('WEIGH1');
                                o_weigh2 = document.getElementById('WEIGH2');
                                o_NETTO = document.getElementById('NETTO');
                                //o_cekbox = document.getElementById('cek');
                                _TICKETNO = o_TICKETNO.value;
                                //_VEHNOCODE = document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value;
                                _VEHNOCODE = document.getElementById('VEHNOCODE').value;
                                _SIPBNO = document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].text;
                                _CTRNO = document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].value;
                                //_VEHNO = o_VEHNO.value;
                                //_TRPCODE = o_TRPCODE.value;
                                _TRPCODE=document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
                                _DRIVER = o_DRIVER.value;
                                _NODO = o_NODO.value;
                                _NOSEGEL = o_NOSEGEL.value;
                                _DATEIN = o_DATEIN.value;
                                _DATEOUT = o_DATEOUT.value;
                                _WEIGH1 = o_weigh1.value;
                                _WEIGH2 = o_weigh2.value;
                                _NETTO = o_NETTO.value;
                                //_CEKBOX = o_cekbox.value;
                        if (_VEHNOCODE==''){//(document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value==0) {
                        alert('No. Kendaraan belum dipilih');
                        document.getElementById('VEHNOCODE').value;//options[document.getElementById('VEHNOCODE').focus()];
                        }
                        else
                        if (document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value==0) {
                                alert('Transporter belum dipilih');
                                document.getElementById('TRPCODE').focus();//options[document.getElementById('TRPCODE').focus()];
                        }
                        else
                        if (_SIPBNO.length <= 0) {
                                alert('No. SIPB belum dipilih');
                                o_SIPBNO.focus();
                        }
                        /*else
                        if (_DRIVER.length <= 0) {
                                alert('Nama Supir belum diisi');
                                o_DRIVER.focus();
                        }*/
                        else
                        if (_DATEIN.length <= 0) {
                                alert('Field Tanggal masih Kosong');
                                o_DATEIN.focus();
                        }
                        else
                        if (_WEIGH1 <= 0) {
                                alert('Berat ke-1 Tidak Boleh Lebih Kecil Dari Nol Atau Sama Dengan Nol');
                                o_weigh1.focus();
                        }
                        else
                        if (document.getElementById('WEIGH1').length < 1) {
                                alert('Berat ke-1 Belum Terisi!!! ');
                                document.getElementById('WEIGH1').focus();
                        }
                        else {
                                param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&CTRNO=' + _CTRNO + '&SIPBNO=' + _SIPBNO + '&SPNO=' + _SPNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT + ' &WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2;
                                param += '&VEHNO=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&NOSEGEL=' + _NOSEGEL + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                                param += '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO + '&NODO=' + _NODO + '&TIPE=' + TIPE + '&MILLCODE=' + MILLCODE;
                                //alert(param);
                                if (confirm('Anda Yakin Ingin Menyimpan SIPB NO :' + ' ' + _SIPBNO + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', SUPIR :' + ' ' + _DRIVER + ' ' + ', TONNASE :' + ' ' + _WEIGH1 + 'Kg')) {
                                        post_response_text('simpan_trx_cpo_pk.php',param,respon);
                                }
                        }
                }
        }
                else{
                        alert('Product Belum dipilih');
                }
        }
        else {
                if (document.getElementById('product1').checked) {
                        OUTIN = 0;
                        //_CEKBOX = 1;
                        PRODUCTCODE = 40000007;
                        TIPE = 1;
                        //SLOC = "MGDG";
                        o_TICKETNO = document.getElementById('TICKETNO');
                        //o_SPBNO = document.getElementById('SPBNO');
                        //o_TRPCODE = document.getElementById('TRPCODE');
                        o_DRIVER = document.getElementById('DRIVER');
                        o_NODO = document.getElementById('NODO');
                        o_NOSEGEL = document.getElementById('NOSEGEL');
                        o_DATEIN = document.getElementById('datein');
                        o_DATEOUT = document.getElementById('dateout');
                        o_weigh1 = document.getElementById('WEIGH1');
                        o_weigh2 = document.getElementById('WEIGH2');
                        o_NETTO = document.getElementById('NETTO');
                        _TICKETNO = o_TICKETNO.value;
                        //_SPBNO = o_SPBNO.value;
                        //_TRPCODE = o_TRPCODE.value;
                        //_VEHNOCODE = document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value;
                        _VEHNOCODE = document.getElementById('VEHNOCODE').value;

                        _TRPCODE=document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
                        _DRIVER = o_DRIVER.value;
                        _NODO = o_NODO.value;
                        _NOSEGEL = o_NOSEGEL.value;
                        _SIPBNO = document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].text;
                        _CTRNO = document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].value;
                        _SLOC = document.getElementById('SLOC').options[document.getElementById('SLOC').selectedIndex].value;
                        _DATEIN = o_DATEIN.value;
                        _DATEOUT = o_DATEOUT.value;
                        _WEIGH1 = o_weigh1.value;
                        _WEIGH2 = o_weigh2.value;
                        _NETTO = o_NETTO.value;
                        if (_DATEOUT <= 0) {
                                alert('Kolom tanggal belum terisi');
                                o_DATEOUT.focus();
                        }
                        else
                        if (_WEIGH2.length <= 0) {
                                        alert('Berat ke-2 Tidak Boleh Lebih Kecil Atau Sama Dengan Nol');
                                        o_weigh2.focus();
                                }
                        else
                        if (_WEIGH2 <= 0) {
                                alert('Berat ke-2 Tidak Boleh Lebih Kecil dari Nol Atau Sama Dengan Nol');
                                document.getElementById('WEIGH2').focus();
                        }
                        else
                        if (_NETTO <= 0) {
                                alert('Netto Tidak Boleh Lebih Kecil Atau Sama Dengan Nol');
                                o_NETTO.focus();
                        }
                        else
                        if (document.getElementById('SLOC').options[document.getElementById('SLOC').selectedIndex].value == 0) {
                                alert('Field Lokasi Tangki Belum Dipilih');
                                //_SLOC.focus();
                                document.getElementById('SLOC').options[document.getElementById('SLOC').focus()];
                        }
                        else {
                                param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&CTRNO=' + _CTRNO + '&SIPBNO=' + _SIPBNO + '&SPNO=' + _SPNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT + ' &WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2 + '&SLOC=' + _SLOC;
                                param += '&VEHNO=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&NOSEGEL=' + _NOSEGEL + '&NODO=' + _NODO + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                                param += '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO + '&TIPE=' + TIPE + '&MILLCODE=' + MILLCODE;
                                //alert(param);
                                if (confirm('Anda Yakin Ingin Menyimpan SIPB NO :' + ' ' + _SIPBNO + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', SUPIR :' + ' ' + ', SLOC :' + _SLOC + ' ' + ', SUPIR :' + _DRIVER + ' ' + ', NETTO :' + ' ' + _NETTO + 'Kg')) {
                                        post_response_text('simpan_trx_cpo_pk.php',param,respon);
                                }
                        }
                }
                if (document.getElementById('product2').checked) {
                        OUTIN = 0;
                        PRODUCTCODE = 40000006;
                        TIPE = 1;
                        o_TICKETNO = document.getElementById('TICKETNO');
                        o_DRIVER = document.getElementById('DRIVER');
                        o_NODO = document.getElementById('NODO');
                        o_NOSEGEL = document.getElementById('NOSEGEL');
                        o_DATEIN = document.getElementById('datein');
                        o_DATEOUT = document.getElementById('dateout');
                        o_weigh1 = document.getElementById('WEIGH1');
                        o_weigh2 = document.getElementById('WEIGH2');
                        o_NETTO = document.getElementById('NETTO');
                        _TICKETNO = o_TICKETNO.value;
                        //_VEHNOCODE = document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value;
                        _VEHNOCODE = document.getElementById('VEHNOCODE').value;

                        _TRPCODE=document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
                        _DRIVER = o_DRIVER.value;
                        _NODO = o_NODO.value;
                        _NOSEGEL = o_NOSEGEL.value;
                        _SIPBNO = document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].text;
                        _CTRNO = document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].value;
                        _SLOC = document.getElementById('SLOC').options[document.getElementById('SLOC').selectedIndex].value;
                        _DATEIN = o_DATEIN.value;
                        _DATEOUT = o_DATEOUT.value;
                        _WEIGH1 = o_weigh1.value;
                        _WEIGH2 = o_weigh2.value;
                        _NETTO = o_NETTO.value;
                        if (_DATEOUT.length <= 0) {
                                alert('Kolom tanggal belum terisi');
                                o_DATEOUT.focus();
                        }
                        else
                                if (_WEIGH2.length <= 0) {
                                        alert('Berat ke-2 masih kosong');
                                        o_weigh2.focus();
                                }
                        else
                        if (_WEIGH2 <= 0) {
                                alert('Berat ke-2 Tidak Boleh Lebih Kecil dari Nol Atau Sama Dengan Nol');
                                document.getElementById('WEIGH2').focus();
                        }
                        else
                        if (_NETTO <= 0) {
                                alert('Field NETTO Tidak Boleh Minus');
                                o_NETTO.focus();
                        }
                        else
            if (document.getElementById('SLOC').options[document.getElementById('SLOC').selectedIndex].value==0) {
                  alert('Field Lokasi Tangki Belum Dipilih');
                  document.getElementById('SLOC').options[document.getElementById('SLOC').focus()];
             }
                        else {
                                param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&CTRNO=' + _CTRNO + '&SIPBNO=' + _SIPBNO + '&SPNO=' + _SPNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT + ' &WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2 + '&SLOC=' + _SLOC;
                                param += '&VEHNO=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&NOSEGEL=' + _NOSEGEL + '&NODO=' + _NODO + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                                param += '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO + '&TIPE=' + TIPE + '&MILLCODE=' + MILLCODE;
                                //alert(param);
                                if (confirm('Anda Yakin Ingin Menyimpan SIPB NO :' + ' ' + _SIPBNO + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', SUPIR :' + ' ' + _DRIVER + ' ' + ', NETTO :' + ' ' + _NETTO + 'Kg')) {
                                        post_response_text('simpan_trx_cpo_pk.php',param,respon);
                                }
                        }
                }
                if (document.getElementById('product3').checked) {
                        OUTIN = 0;
                        PRODUCTCODE = 40000005;
                        TIPE = 5;
                        o_TICKETNO = document.getElementById('TICKETNO');
                        o_DRIVER = document.getElementById('DRIVER');
                         o_NODO = document.getElementById('NODO');
                        o_NOSEGEL = document.getElementById('NOSEGEL');
                        o_DATEIN = document.getElementById('datein');
                        o_DATEOUT = document.getElementById('dateout');
                        o_weigh1 = document.getElementById('WEIGH1');
                        o_weigh2 = document.getElementById('WEIGH2');
                        o_NETTO = document.getElementById('NETTO');
                        _TICKETNO = o_TICKETNO.value;
                        //_VEHNOCODE = document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value;
                        _VEHNOCODE = document.getElementById('VEHNOCODE').value;

                        _TRPCODE=document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
                        _DRIVER = o_DRIVER.value;
                        _NODO = o_NODO.value;
                        _NOSEGEL = o_NOSEGEL.value;
                        _SIPBNO = document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].text;
                        _CTRNO = document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].value;
                        _SLOC = document.getElementById('SLOC').options[document.getElementById('SLOC').selectedIndex].value;
                        _DATEIN = o_DATEIN.value;
                        _DATEOUT = o_DATEOUT.value;
                        _WEIGH1 = o_weigh1.value;
                        _WEIGH2 = o_weigh2.value;
                        _NETTO = o_NETTO.value;
                        if (_DATEOUT.length <= 0) {
                                alert('Kolom tanggal belum terisi');
                                o_DATEOUT.focus();
                        }
                        else
                        if (_WEIGH2.length <= 0) {
                                        alert('Berat ke-2 masih kosong');
                                        o_weigh2.focus();
                                }
                        else
                        if (_WEIGH2 <= 0) {
                                alert('Berat ke-2 Tidak Boleh Lebih Kecil dari Nol Atau Sama Dengan Nol');
                                document.getElementById('WEIGH2').focus();
                        }
                        else
                        if (_NETTO <= 0) {
                                alert('Field NETTO Tidak Boleh Minus');
                                o_NETTO.focus();
                        }
                        else
                        if (document.getElementById('SLOC').options[document.getElementById('SLOC').selectedIndex].value == 0) {
                                        alert('Field Lokasi Tangki Belum Dipilih');
                                        document.getElementById('SLOC').options[document.getElementById('SLOC').focus()];
                        }
                        else {
                                param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&CTRNO=' + _CTRNO + '&SIPBNO=' + _SIPBNO + '&SPNO=' + _SPNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT + ' &WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2 + '&SLOC=' + _SLOC;
                                param += '&VEHNO=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&NOSEGEL=' + _NOSEGEL + '&NODO=' + _NODO + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                                param += '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO + '&TIPE=' + TIPE + '&MILLCODE=' + MILLCODE;
                                //alert(param);
                                if (confirm('Anda Yakin Ingin Menyimpan SIPB NO :' + ' ' + _SIPBNO + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', SUPIR :' + ' ' + _DRIVER + ' ' + ', NETTO :' + ' ' + _NETTO + 'Kg')) {
                                                post_response_text('simpan_trx_cpo_pk.php',param,respon);
                                }
                        }
                }
        }
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="trx_cpo_pk.php";
                }
                else {
                      tx=con.responseText;
                      //alert(tx);
                                        if(tx == 0){
                                                //alert('ok');
                                                //o_DATEOUT = document.getElementById('dateout');
                                                o_weigh2 = document.getElementById('button2');
                                                if (o_weigh2.disabled){
                                                //if (document.getElementById('dateout').disabled && document.getElementById('WEIGH2').disabled){
                                                        OUTIN = 1;
                                                        //alert(OUTIN);
                                                }
                                                else{
                                                        OUTIN = 0;
                                                        //alert(OUTIN);
                                                }
                                        if (OUTIN == 1) {
                                                alert('Berhasil di simpan');
                                                window.location.reload();
                                        }
                                        else {
                                                alert('Berhasil Disimpan');
                                                var IDWB = document.getElementById('IDWB').value;
                                                var TICKETNO = document.getElementById('TICKETNO').value;
                                                _TICKETNO2 = IDWB+TICKETNO;
                                                if(window.open('fpdf/kartu_timbang_cpo_pk_form.php?TICKETNO='+_TICKETNO2+'&IDWB='+IDWB,'location=0','resizable=0','scrollbars=0','navigation bar=0','width=100','height=100'))
                                                window.location.reload();
                                        }
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

function loadcpo(_TICKETNO,_VEHNOCODE,_TRPCODE,_SIPBNO,_NODOTRP,_NOSEGEL,_DRIVER,_SLOC,_DATEIN,_WEI1ST,_TRPNAME,_PRODUCTCODE,_SPNO,_CTRNO)
{
        o_TICKETNO=document.getElementById('TICKETNO');
        o_NODOTRP=document.getElementById('NODO');
        o_SPNO=document.getElementById('SPNO');
        o_NOSEGEL=document.getElementById('NOSEGEL');
        o_DRIVER=document.getElementById('DRIVER');
        o_DATEIN=document.getElementById('datein');
        o_WEI1ST=document.getElementById('WEIGH1');
                //o_status.options[1].selected=true;
                o_TICKETNO.value=_TICKETNO;
                //o_VEHNO.value=_VEHNOCODE;
                //_VEHNOCODE=o_status.options[o_status.selectedIndex].text;
                o_NODOTRP.value=_NODOTRP;
                o_SPNO.value=_SPNO;
                o_NOSEGEL.value=_NOSEGEL;
                o_DRIVER.value=_DRIVER;
                o_DATEIN.value=_DATEIN;
                o_WEI1ST.value=_WEI1ST;
                //document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].text=_VEHNOCODE;
                //document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value=_VEHNOCODE;
                document.getElementById('VEHNOCODE').value=_VEHNOCODE;
                document.getElementById('VEHNOCODE').disabled=true;
				//alert(_TRPCODE);
				for (c=0;c<=(document.getElementById('TRPCODE').length-1);c++)
				{
						if(document.getElementById('TRPCODE').options[c].value==_TRPCODE)
							document.getElementById('TRPCODE').options[c].selected=true;
				} 
                document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].value=_CTRNO;
                document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].text=_SIPBNO;
                //document.getElementById('VEHNOCODE').disabled = true;
                //document.getElementById('TRPCODE').disabled = true;
        document.getElementById('SIPBNO').disabled = true;
                //document.getElementById('NODO').disabled = true;
                //document.getElementById('NOSEGEL').disabled = true;
                //document.getElementById('DRIVER').disabled = true;
                document.getElementById('datein').disabled = true;
                document.getElementById('WEIGH1').disabled = true;
                document.getElementById('SALDO').disabled = true;
                document.getElementById('SALDO').value = '0';
                document.getElementById('button1').disabled = true;
                document.getElementById('dateout').disabled = true;
                document.getElementById('WEIGH2').disabled = true;
                document.getElementById('button2').disabled = false;
        //document.getElementById('SLOC').options[document.getElementById('SLOC').disabled=true;
        document.getElementById('SLOC').disabled = false;
                if (_PRODUCTCODE=="40000007"){
                                        document.getElementById('product1').checked = true;
                                        document.getElementById('product1').disabled = true;
                                        document.getElementById('product2').checked = false;
                                        document.getElementById('product2').disabled = true;
                                        document.getElementById('product3').checked = false;
                                        document.getElementById('product3').disabled = true;
                                        document.getElementById('PRODUCT').value = 'CPO';
        }
        if (_PRODUCTCODE=="40000006")
                        {
                                document.getElementById('product2').checked = true;
                                document.getElementById('product2').disabled = true;
                                document.getElementById('product1').checked = false;
                                document.getElementById('product1').disabled = true;
                                document.getElementById('product3').checked = false;
                                document.getElementById('product3').disabled = true;
                                document.getElementById('PRODUCT').value = 'KER';
                        }
        if(_PRODUCTCODE=="40000005"){
                                document.getElementById('product3').checked = true;
                                document.getElementById('product3').disabled = true;
                                document.getElementById('product1').checked = false;
                                document.getElementById('product1').disabled = true;
                                document.getElementById('product2').checked = false;
                                document.getElementById('product2').disabled = true;
                                document.getElementById('PRODUCT').value = 'CK';
                }
}
//================================================Composting
function saveComposting()
{
                window.clearInterval(mainReminder);
        o_DATEOUT = document.getElementById('dateout');
        o_weigh2 = document.getElementById('WEIGH2');
        var IDWB = document.getElementById('IDWB').value;
        var TICKETNO = document.getElementById('TICKETNO').value;
        _TICKETNO2 = IDWB+TICKETNO;
        o_IDWB = document.getElementById('IDWB');
        _IDWB = o_IDWB.value;
        MILLCODE = "H01M";
        if (o_DATEOUT.disabled && o_weigh2.disabled)
        {
                //PRODUCTCODE = 40000004;
                OUTIN = 1;
                SLOC = "MGDG";
                o_TICKETNO = document.getElementById('TICKETNO');
                o_TRPCODE = document.getElementById('TRPCODE');
                o_DRIVER = document.getElementById('DRIVER');
                o_DATEIN = document.getElementById('datein');
                o_DATEOUT = document.getElementById('dateout');
                o_weigh1 = document.getElementById('WEIGH1');
                o_weigh2 = document.getElementById('WEIGH2');
                o_NETTO = document.getElementById('NETTO');
                o_IDWB = document.getElementById('IDWB');
                _TICKETNO = o_TICKETNO.value;
                _VEHNOCODE = document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value;
                PRODUCTCODE = document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].value;
                PRODUCTNAME = document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].text;
                PENERIMA = document.getElementById('PENERIMA').options[document.getElementById('PENERIMA').selectedIndex].value;
                _TRPCODE = o_TRPCODE.value;
                _DRIVER = o_DRIVER.value;
                _DATEIN = o_DATEIN.value;
                _DATEOUT = o_DATEOUT.value;
                _WEIGH1 = o_weigh1.value;
                _WEIGH2 = o_weigh2.value;
                _NETTO = o_NETTO.value;
                if (document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value==0) {
                                alert('No. Kendaraan belum dipilih');
                                document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').focus()];
                }
                else
                if (document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].value==0) {
                                alert('Product belum Dipilih');
                                document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').focus()];
                }
                else
                if (document.getElementById('PENERIMA').options[document.getElementById('PENERIMA').selectedIndex].value==0){
                                alert('Penerima belum Dipilih');
                                document.getElementById('PENERIMA').options[document.getElementById('PENERIMA').focus()];
                }
                else
                if (_DRIVER.length < 1) {
                                alert('Nama Supir belum diisi');
                                o_DRIVER.focus();
                }
                else
                if (_DATEIN.length < 1){
                                alert('Field Tanggal belum terisi');
                                o_DATEIN.focus();
                }
                else
                if (_WEIGH1 <= 0) {
                        alert('Berat ke-1 Tidak Boleh Lebih Kecil Dari Nol Atau Sama Dengan Nol');
                        o_weigh1.focus();
                }
                else
                if (document.getElementById('WEIGH1').length < 1) {
                        alert('Berat ke-1 Belum Terisi!!! ');
                        document.getElementById('WEIGH1').focus();
                }
                else {
                                                param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT + ' &WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2;
                                                param += '&SLOC=' + SLOC + '&VEHNO=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                                                param += '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO +'&PENERIMA=' + PENERIMA + '&MILLCODE=' + MILLCODE;
                                                //alert(param);
                                                if (confirm('Anda Yakin Ingin Menyimpan Data Timbang :' + ' ' + PRODUCTNAME + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', SUPIR :' + ' ' + _DRIVER + ' ' + ', TONNASE :' + ' ' + _WEIGH1 + 'Kg')) {
                                                        post_response_text('simpan_trx_composting.php', param, respon);
                                                }
                                }
        }
        else {
                OUTIN = 0;
                SLOC = "MGDG";
                o_TICKETNO = document.getElementById('TICKETNO');
                o_TRPCODE = document.getElementById('TRPCODE');
                o_DRIVER = document.getElementById('DRIVER');
                o_DATEIN = document.getElementById('datein');
                o_DATEOUT = document.getElementById('dateout');
                o_weigh1 = document.getElementById('WEIGH1');
                o_weigh2 = document.getElementById('WEIGH2');
                o_NETTO = document.getElementById('NETTO');
                _TICKETNO = o_TICKETNO.value;
                _VEHNOCODE = document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value;
                PRODUCTCODE = document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].value;
                PRODUCTNAME = document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].text;
                PENERIMA = document.getElementById('PENERIMA').options[document.getElementById('PENERIMA').selectedIndex].text;
                _TRPCODE = o_TRPCODE.value;
                _DRIVER = o_DRIVER.value;
                _DATEIN = o_DATEIN.value;
                _DATEOUT = o_DATEOUT.value;
                _WEIGH1 = o_weigh1.value;
                _WEIGH2 = o_weigh2.value;
                _NETTO = o_NETTO.value;
                if (_DATEOUT.length < 1) {
                        alert('Kolom tanggal belum terisi');
                        o_DATEOUT.focus();
                }
                else
                if (_WEIGH2.length <= 0) {
                                alert('Berat ke-2 masih kosong');
                                o_weigh2.focus();
                }
                else
                if (_WEIGH2 <= 0) {
                                alert('Berat ke-2 Tidak Boleh Lebih Kecil dari Nol Atau Sama Dengan Nol');
                                document.getElementById('WEIGH2').focus();
                        }
                else
                if (_NETTO <= 0) {
                        alert('Netto Tidak Boleh Lebih Kecil dari Nol Atau Sama Dengan Nol');
                        o_NETTO.focus();
                }
                else {
                                        param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT + ' &WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2;
                                        param += '&VEHNO=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&SLOC=' + SLOC + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                                        param += '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO + '&PENERIMA=' + PENERIMA + '&MILLCODE=' + MILLCODE;
                                        //alert(param);
                                        if (confirm('Anda Yakin Ingin Menyimpan Data Timbang :' + ' ' + PRODUCTNAME + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', TONNASE :' + ' ' + _WEIGH1 + 'Kg')) {
                                                post_response_text('simpan_trx_composting.php', param, respon);
                                        }
                                }
        }
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="trx_composting.php";
                }
                else {
                      tx=con.responseText;
                      //alert(tx);
                                        if(tx==0){
                                                o_DATEOUT = document.getElementById('dateout');
                                                o_weigh2 = document.getElementById('WEIGH2');
                                                if (o_DATEOUT.disabled && o_weigh2.disabled)
                                                        OUTIN = 1;
                                                else
                                                        OUTIN = 0;
                                        if (OUTIN == 1) {
                                                alert('Berhasil di simpan');
                                                window.location.reload();
                                        }
                                        else {
                                                alert('Berhasil Disimpan');
                                                var IDWB = document.getElementById('IDWB').value;
                                                var TICKETNO = document.getElementById('TICKETNO').value;
                                                _TICKETNO2 = IDWB+TICKETNO;
                                                if(window.open('fpdf/kartu_timbang_composting_form.php?TICKETNO='+_TICKETNO2+'&IDWB='+IDWB,'location=0','resizable=0','scrollbars=0','navigation bar=0','width=100','height=100'))
                                                window.location.reload();
                                        }
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
function loadComposting(_TICKETNO,_VEHNOCODE,_TRPCODE,_DRIVER,_DATEIN,_WEI1ST,_PENERIMA,_TRPNAME,_PRODUCTCODE,_PRODUCTNAME)
{
        o_TICKETNO=document.getElementById('TICKETNO');
        o_VEHNOCODE=document.getElementById('VEHNOCODE');
        o_TRPCODE=document.getElementById('TRPCODE');
        o_DRIVER=document.getElementById('DRIVER');
        o_DATEIN=document.getElementById('datein');
        o_WEI1ST=document.getElementById('WEIGH1');
        o_PENERIMA=document.getElementById('PENERIMA');
        o_PRODUCT=document.getElementById('PRODUCT');
        o_TRPNAME=document.getElementById('TRPCODE');
                o_TICKETNO.value=_TICKETNO;
                o_DRIVER.value=_DRIVER;
                o_DATEIN.value=_DATEIN;
                o_WEI1ST.value=_WEI1ST;
                o_PENERIMA.value=_PENERIMA;
                o_PRODUCT.value=_PRODUCTCODE;
                o_PRODUCT.text=_PRODUCTNAME;
                document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].text=_VEHNOCODE;
                document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value=_VEHNOCODE;
                document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value=_TRPCODE;
                document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].text=_TRPNAME;
                document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].value=_PRODUCTCODE;
                document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].text=_PRODUCTNAME;
                //document.getElementById('TRPCODE').disabled = true;
                //document.getElementById('VEHNOCODE').disabled = true;
                //document.getElementById('PRODUCT').disabled = true;
                //document.getElementById('PENERIMA').disabled = true;
                //document.getElementById('DRIVER').disabled = true;
                document.getElementById('datein').disabled = true;
                document.getElementById('WEIGH1').disabled = true;
                document.getElementById('button1').disabled = true;
                document.getElementById('dateout').disabled = false;
                document.getElementById('WEIGH2').disabled = false;
                document.getElementById('button2').disabled = false;
}
//==========================================Pengiriman Barang Selain CPO/PK
function saveX()
{
   window.clearInterval(mainReminder);
        o_DATEOUT = document.getElementById('dateout');
        o_weigh2 = document.getElementById('WEIGH2');
        var IDWB = document.getElementById('IDWB').value;
        var TICKETNO = document.getElementById('TICKETNO').value;
        o_IDWB = document.getElementById('IDWB');
        _IDWB = o_IDWB.value;
        _TICKETNO2 = IDWB+TICKETNO;
        MILLCODE = "H01M";
        if (o_DATEOUT.disabled && o_weigh2.disabled)
        {
                //PRODUCTCODE = 40000004;
                OUTIN = 1;
                SLOC = "MGDG";
                o_TICKETNO = document.getElementById('TICKETNO');
                o_SPBNO = document.getElementById('SPBNO');
                o_TRPCODE = document.getElementById('TRPCODE');
                o_DRIVER = document.getElementById('DRIVER');
                o_PENGIRIM = document.getElementById('PENGIRIM');
                o_PENERIMA = document.getElementById('PENERIMA');
                o_DATEIN = document.getElementById('datein');
                o_weigh1 = document.getElementById('WEIGH1');
                o_weigh2 = document.getElementById('WEIGH2');
                o_NETTO = document.getElementById('NETTO');
                _TICKETNO = o_TICKETNO.value;
                _PENGIRIM = o_PENGIRIM.value;
                //_VEHNOCODE = document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value;
                _VEHNOCODE=document.getElementById('VEHNOCODE').value;
                PRODUCTCODE = document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].value;
                PRODUCTNAME = document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].text;
                _PENERIMA = o_PENERIMA.value;
                _SPBNO = o_SPBNO.value;
                _TRPCODE = o_TRPCODE.value;
                _DRIVER = o_DRIVER.value;
                _DATEIN = o_DATEIN.value;
                _WEIGH1 = o_weigh1.value;
                _WEIGH2 = o_weigh2.value;
                _NETTO = o_NETTO.value;
                /*if (_SPBNO.length == 0) {
                        alert('Field No. Surat Pengantar masih kosong');
                        o_SPBNO.focus();
                }
                else*/
                if (_VEHNOCODE==''){//(document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value==0) {
                                alert('No. Kendaraan belum dipilih');
                                document.getElementById('VEHNOCODE').focus();//options[document.getElementById('VEHNOCODE').focus()];
                }
                else
                if (document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].value==0) {
                                alert('Product belum Dipilih');
                                document.getElementById('PRODUCT').focus();//options[document.getElementById('PRODUCT').focus()];
                }
                else
                if (document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value==0){
                                alert('Pengangkut belum Dipilih');
                                document.getElementById('TRPCODE').focus();//options[document.getElementById('TRPCODE').focus()];
                }
                else
                if (_PENGIRIM.length < 1) {
                                alert('Pengirim belum diisi');
                                o_PENGIRIM.focus();
                }
                else
                if (_PENERIMA.length < 1) {
                                alert('Penerima belum diisi');
                                o_PENERIMA.focus();
                }
                else
                if (_DRIVER.length < 1) {
                                alert('Nama Supir belum diisi');
                                o_DRIVER.focus();
                }
                else
                if (_DATEIN.length < 1){
                                alert('Field Tanggal belum terisi');
                                o_DATEIN.focus();
                }
                else
                if (_WEIGH1 <= 0) {
                        alert('Berat ke-1 Tidak Boleh Lebih Kecil Dari Nol Atau Sama Dengan Nol');
                        o_weigh1.focus();
                }
                else
                if (document.getElementById('WEIGH1').length < 1) {
                        alert('Berat ke-1 Belum Terisi!!! ');
                        document.getElementById('WEIGH1').focus();
                }
                else {
                                                param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN +  ' &WEIGH1=' + _WEIGH1;
                                                param += '&VEHNO=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                                                param += '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO +'&PENERIMA=' + _PENERIMA+'&PENGIRIM=' + _PENGIRIM+'&SPBNO=' + _SPBNO +'&MILLCODE=' + MILLCODE;
                                             //alert(param);
                                                if (confirm('Anda Yakin Ingin Menyimpan Data Timbang :' + ' ' + PRODUCTNAME + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', SUPIR :' + ' ' + _DRIVER + ' ' + ', TONNASE :' + ' ' + _WEIGH1 + 'Kg')) {
                                                        post_response_text('simpan_trx_pengiriman_barang.php', param, respon);
                                                }
                                }
        }
        else {
                OUTIN = 0;
                SLOC = "MGDG";
                o_IDWB = document.getElementById('IDWB');
                o_TICKETNO = document.getElementById('TICKETNO');
                o_SPBNO = document.getElementById('SPBNO');
                o_TRPCODE = document.getElementById('TRPCODE');
                o_DRIVER = document.getElementById('DRIVER');
                o_PENGIRIM = document.getElementById('PENGIRIM');
                o_PENERIMA = document.getElementById('PENERIMA');
                o_DATEIN = document.getElementById('datein');
                o_DATEOUT = document.getElementById('dateout');
                o_weigh1 = document.getElementById('WEIGH1');
                o_weigh2 = document.getElementById('WEIGH2');
                o_NETTO = document.getElementById('NETTO');
                _TICKETNO = o_TICKETNO.value;
                _PENGIRIM = o_PENGIRIM.value;
                //_VEHNOCODE = document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value;
                _VEHNOCODE=document.getElementById('VEHNOCODE').value;
                PRODUCTCODE = document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].value;
                PRODUCTNAME = document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].text;
                _PENERIMA = o_PENERIMA.value;
                _SPBNO = o_SPBNO.value;
                _TRPCODE = o_TRPCODE.value;
                _DRIVER = o_DRIVER.value;
                _DATEIN = o_DATEIN.value;
                _DATEOUT = o_DATEOUT.value;
                _WEIGH1 = o_weigh1.value;
                _WEIGH2 = o_weigh2.value;
                _NETTO = o_NETTO.value;
                if (_DATEOUT.length < 1) {
                        alert('Kolom tanggal belum terisi');
                        o_DATEOUT.focus();
                }
                else
                if (_WEIGH2.length <= 0) {
                                alert('Berat ke-2 masih kosong');
                                o_weigh2.focus();
                }
                else
                if (_WEIGH2 <= 0) {
                                alert('Berat ke-2 Tidak Boleh Lebih Kecil dari Nol Atau Sama Dengan Nol');
                                document.getElementById('WEIGH2').focus();
                }
                else
                if (_NETTO <= 0) {
                        alert('Netto Tidak Boleh Lebih Kecil dari Nol Atau Sama Dengan Nol');
                        o_NETTO.focus();
                }
                else {
                                        param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&DATEOUT=' + _DATEOUT + ' &WEIGH1=' + _WEIGH1 + '&WEIGH2=' + _WEIGH2;
                                        param += '&VEHNO=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&SLOC=' + SLOC + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
                                        param += '&DRIVER=' + _DRIVER + '&NETTO=' + _NETTO + '&PENERIMA=' + _PENERIMA+ '&PENGIRIM=' + _PENGIRIM+'&SPBNO=' + _SPBNO +'&MILLCODE=' + MILLCODE;
                                        //alert(param);
                                        if (confirm('Anda Yakin Ingin Menyimpan Data Timbang :' + ' ' + PRODUCTNAME + ' ' + ', NO.KENDARAAN :' + ' ' + _VEHNOCODE + ' ' + ', TONNASE :' + ' ' + _WEIGH1 + 'Kg')) {
                                                post_response_text('simpan_trx_pengiriman_barang.php', param, respon);
                                        }
                                }
        }
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="trx_pengiriman_barang.php";
                }
                else {
                      tx=con.responseText;
                      //alert(tx);
                                        if(tx==0){
                                                o_DATEOUT = document.getElementById('dateout');
                                                o_weigh2 = document.getElementById('WEIGH2');
                                                if (o_DATEOUT.disabled && o_weigh2.disabled)
                                                        OUTIN = 1;
                                                else
                                                        OUTIN = 0;
                                        if (OUTIN == 1) {
                                                alert('Berhasil di simpan');
                                                window.location.reload();
                                        }
                                        else {
                                                alert('Berhasil Disimpan');
                                                var IDWB = document.getElementById('IDWB').value;
                                                var TICKETNO = document.getElementById('TICKETNO').value;
                                                _TICKETNO2 = IDWB+TICKETNO;
                                                if(window.open('fpdf/kartu_timbang_pengiriman_barang_form.php?TICKETNO='+_TICKETNO2+'&IDWB='+IDWB,'location=0','resizable=0','scrollbars=0','navigation bar=0','width=100','height=100'))
                                                window.location.reload();
                                        }
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
function loadX(_TICKETNO,_VEHNOCODE,_TRPCODE,_DRIVER,_DATEIN,_WEI1ST,_PENERIMA,_TRPNAME,_PRODUCTCODE,_PRODUCTNAME,_SPNO,_PENGIRIM)
{
                o_TICKETNO=document.getElementById('TICKETNO');
                o_SPNO=document.getElementById('SPBNO');
                o_VEHNOCODE=document.getElementById('VEHNOCODE');
                o_TRPCODE=document.getElementById('TRPCODE');
                o_DRIVER=document.getElementById('DRIVER');
                o_DATEIN=document.getElementById('datein');
                o_WEI1ST=document.getElementById('WEIGH1');
                o_PENERIMA=document.getElementById('PENERIMA');
                o_PENGIRIM=document.getElementById('PENGIRIM');
                o_PRODUCT=document.getElementById('PRODUCT');
                o_TRPNAME=document.getElementById('TRPCODE');
                o_TICKETNO.value=_TICKETNO;
                o_SPNO.value=_SPNO;
                o_DRIVER.value=_DRIVER;
                o_DATEIN.value=_DATEIN;
                o_WEI1ST.value=_WEI1ST;
                o_PENERIMA.value=_PENERIMA;
                o_PENGIRIM.value=_PENGIRIM;
                o_PRODUCT.value=_PRODUCTCODE;
                o_PRODUCT.text=_PRODUCTNAME;
                //document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].text=_VEHNOCODE;
                //document.getElementById('VEHNOCODE').options[document.getElementById('VEHNOCODE').selectedIndex].value=_VEHNOCODE;
                document.getElementById('VEHNOCODE').value=_VEHNOCODE;
                document.getElementById('VEHNOCODE').disabled=true;
                document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value=_TRPCODE;
                document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].text=_TRPNAME;
                document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].value=_PRODUCTCODE;
                document.getElementById('PRODUCT').options[document.getElementById('PRODUCT').selectedIndex].text=_PRODUCTNAME;
        //document.getElementById('TRPCODE').disabled = true;
        //document.getElementById('PENGIRIM').disabled = true;
                //document.getElementById('SPBNO').disabled = true;
                //document.getElementById('VEHNOCODE').disabled = true;
                //document.getElementById('PRODUCT').disabled = true;
                //document.getElementById('PENERIMA').disabled = true;
                //document.getElementById('DRIVER').disabled = true;
                document.getElementById('datein').disabled = true;
                document.getElementById('WEIGH1').disabled = true;
                document.getElementById('button1').disabled = true;
                document.getElementById('dateout').disabled = false;
                document.getElementById('WEIGH2').disabled = false;
                document.getElementById('button2').disabled = false;
}
function simpanSystem()
{
        _simpan = 1;
        o_COMPCODE=document.getElementById('COMPCODE');
        o_COMPNAME=document.getElementById('COMPNAME');
        o_MILLCODE=document.getElementById('MILLCODE');
        o_MILLNAME=document.getElementById('MILLNAME');
        o_MNGRNAME=document.getElementById('MNGRNAME');
        o_KTUNAME=document.getElementById('KTUNAME');
        o_KRANINAME=document.getElementById('KRANINAME');
        o_TIMEVEH=document.getElementById('TIMEVEH');
        _COMPCODE=o_COMPCODE.value;_MNGRNAME=o_MNGRNAME.value;
        _COMPNAME=o_COMPNAME.value;_KTUNAME=o_KTUNAME.value;
        _MILLCODE=o_MILLCODE.value;_KRANINAME=o_KRANINAME.value;
        _MILLNAME=o_MILLNAME.value;_TIMEVEH=o_TIMEVEH.value;
                if (_COMPCODE.length <= 0) {
                                alert('Kode Perusahaan harus 4 digit');
                                o_COMPCODE.focus();
                }
                else
                if (_COMPNAME.length <= 0) {
                        alert('Nama Perusahaan masih kosong belum diisi');
                        o_COMPNAME.focus();
                }
                else
                if (_MILLCODE.length <= 0) {
                        alert('Kode Mill masih kosong belum diisi');
                        o_MILLCODE.focus();
                }
                else
                if (_MILLNAME.length <= 0){
                        alert('Nama Mill masih kosong belum diisi');
                        o_MILLNAME.focus();
                }
                else
                if (_MNGRNAME.length <= 0){
                        alert('Nama Manager masih kosong belum diisi');
                        o_MNGRNAME.focus();
                }
                else
                if (_KTUNAME.length <= 0){
                        alert('Nama Kasie/KTU masih kosong belum diisi');
                        o_KTUNAME.focus();
                }
                else
                if (_KRANINAME.length <= 0){
                        alert('Nama Krani masih kosong belum diisi');
                        o_KRANINAME.focus();
                }
                else
                if (_TIMEVEH.length <= 0){
                        alert('Waktu Timbang antar kendaraan masih kosong belum diisi');
                        o_TIMEVEH.focus();
                }
                else {
                        param = 'MILLCODE=' + _MILLCODE + '&MILLNAME=' + _MILLNAME + '&COMPCODE=' + _COMPCODE + '&COMPNAME=' + _COMPNAME + '&MNGRNAME=' + _MNGRNAME + '&KTUNAME=' + _KTUNAME;
                        param += '&KRANINAME=' + _KRANINAME + '&TIMEVEH=' + _TIMEVEH + '&SIMPAN=' + _simpan;
                        //alert(param);
                        hubungkan_post('simpan_system.php', param, response_system);
                }
}

function response_system()
{
  if(con.readyState==4)
     {
        if(con.status==200)
        {
           tx=con.responseText;
                   if(tx=='0')
                    {
                                alert('Berhasil di simpan');
                                window.location.reload();
                        }
                        else
                        {
                         alert(tx);
                        }
                   unlock();
                }
        else
        {
                  unlock();
          error_catch(con.status);
        }
     }
}
function ubahSystem()
{
        document.getElementById('COMPCODE').disabled = false;
        document.getElementById('COMPNAME').disabled = false;
        document.getElementById('MILLCODE').disabled = false;
        document.getElementById('MILLNAME').disabled = false;
        document.getElementById('MNGRNAME').disabled = false;
        document.getElementById('KTUNAME').disabled = false;
        document.getElementById('KRANINAME').disabled = false;
        document.getElementById('TIMEVEH').disabled = false;
        document.getElementById('MNGRNAME').disabled = false;
        document.getElementById('simpan').disabled = false;
        document.getElementById('rubah').disabled = true;
}
function hapusTiket()
{
            function response_dela(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    alert('Berhasil di hapus');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
                _TICKETNO=document.getElementById('TICKETNO').value;
                o_TICKETNO=document.getElementById('TICKETNO');
                if (_TICKETNO.length == 7){
                        param = 'TICKETNO=' + _TICKETNO;
                        //alert(param);
                        if (confirm('Anda Yakin Ingin Menghapus No. Tiket :' + _TICKETNO)){
                        hubungkan_post('delete_no_tiket.php',param,response_dela);
                        }
                        else{
                                window.location.reload();
                        }
                }
                else
                {
                        alert('No.Tiket yang dimasukkan harus 7 digit');
                        window.location.reload();
                        o_TICKETNO.focus();
                }
}
function response_delete()
{
  //alert(con.responseText);
    if(con.readyState==4)
    {
       if(con.status==200)
       {
          tx=con.responseText;
           if((tx==0)||(tx==00) || (tx==''))
            {
                                alert('Berhasil dihapus');
                                window.location.reload();
                        }
                else
                        {
                         alert(tx);
                        }
                   unlock();
                }
        else
        {
                  unlock();
          error_catch(con.status);
        }
     }
}

//==================================================================saveIP
function saveIP(){
                 id = document.getElementById('idx').value;
                _ip=document.getElementById('ip').value;
                _name=document.getElementById('name').value;
                _port=document.getElementById('port').value;
                _wilayahx=document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].text;
                _wilayah=document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].value;
                if(_ip.length<6)
                {
                        alert('IP Salah');
                        document.getElementById('ip').focus();
                }
                else {
                                param = 'IP=' + _ip + '&NAME=' + _name + '&PORT=' + _port + '&WILAYAH=' + _wilayah + '&id=' + id;
                                //alert(param);
                                post_response_text('simpan_ipserver.php',param,respon);
                }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="setting_server.php";
                }
                else {
                    //document.getElementById('result').innerHTML = con.responseText;
                    alert('Berhasil di simpan');
                    window.location="setting_server.php";
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function editIP(wilayah,ip,name,port){
    document.getElementById('idx').value = 'edit';
    document.getElementById('ip').value = ip;
    document.getElementById('name').value = name;
    document.getElementById('port').value = port;
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('wilayah').disabled = true;
    document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].text=wilayah;
        document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].value=wilayah;
}
function clearIP(){
    window.location="setting_server.php";
}

//==================== auto load
function post_param(tujuan,param,functiontoexecute)
{
        con.open("POST", tujuan, true);
        con.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        con.setRequestHeader("Content-length", param.length);
        con.setRequestHeader("Connection", "close");

        con.onreadystatechange = eval(functiontoexecute);
        con.send(param);
}
var mainReminder;
var lastMess;
var idle=1;//is idle
function startReminder(){

        //default looping 
        //1000 adalah 1 detik(a secon)
        interval=5000;
        mainReminder= window.setInterval("getReminderData()",interval);
}
function getReminderData()
{   
         reminderSlave='wb_getIndicatorValue.php';

                if (idle == 0) {
                //if prev query has not response than wait
                }
                else {
                        //post request
                        post_param(reminderSlave, 'x='+Math.random(), respot);
                        idle = 0;//waiting for response;
                }

            function respot() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                                idle=1;//set idle=true
                if (!isSaveResponse(con.responseText)) {
                                        alert(con.responseText);
                } else {
                    try {
                                                if(con.responseText=='')
                                                {}
                                                else
                                                  document.getElementById('WEIGH').value = con.responseText;
                                        }
                                        catch(ER)
                                        {
                                                clearTimeout(mainReminder);
                                        }
                }
            } else {
                error_catch(con.status);
            }
        }
    }
}
startReminder();

function loadPlat(trp)
{

        param='trp='+trp;
        if (trp == '') {
        }
        else {
                post_response_text('wb_getVhc.php', param, respon);
        }	
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('VEHNOCODE').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function bersihkanField(obj)
{
    if(obj.value=='0')
        obj.value='';
}
function ambilTara(kodekend)
{
    tujuan='wb_ambilTara.php';
        param='kodekend='+kodekend;
    post_response_text(tujuan, param, responx);	
        function responx(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('tara').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function periksa(obj){
    if(obj.value==''){
        obj.value=0;
    }
}

function pecahTiket(title,content,ev){
    width='850';
    height='475';
    showDialog3(title,content,width,height,ev);
    getFormPecahTiket();
}

function getFormPecahTiket()
{
        SLOC = "MGDG";
        MILLCODE = "H01M";
        _TICKETNO = document.getElementById('TICKETNO').value;
        _UNITCODE = document.getElementById('unitcode').options[document.getElementById('unitcode').selectedIndex].value;
        _DIVCODE = document.getElementById('divcode').options[document.getElementById('divcode').selectedIndex].value;
        _VEHNOCODE = document.getElementById('VEHNOCODE').value;//.options[document.getElementById('VEHNOCODE').selectedIndex].value;
        _TRPCODE = document.getElementById('TRPCODE').options[document.getElementById('TRPCODE').selectedIndex].value;
        _DRIVER = document.getElementById('DRIVER').value;
        _JJG = document.getElementById('JUMJJG').value;
        _THNTNM = document.getElementById('TAHUNTANAM').value;
        _BRONDOLAN = document.getElementById('BRONDOLAN').value;
        _BERATKIRIM = document.getElementById('BERATKIRIM').value;
        _DATEIN = document.getElementById('datein').value;
        _DATEOUT = document.getElementById('dateout').value;
        _WEIGH1 = document.getElementById('WEIGH1').value;
        _WEIGH2 = document.getElementById('WEIGH').value
        _NETTO = parseInt(_WEIGH1)-parseInt(_WEIGH2);
        _IDWB = document.getElementById('IDWB').value;
        _POTONGAN = document.getElementById('POTONGAN').value;
        param = 'TICKETNO=' + _TICKETNO + '&OUTIN=' + OUTIN + '&SPBNO=' + _SPBNO + '&PRODUCTCODE=' + PRODUCTCODE + '&DATEIN=' + _DATEIN + '&WEIGH1=' + _WEIGH1;
        param += '&SLOC=' + SLOC + '&VEHNOCODE=' + _VEHNOCODE + '&TRPCODE=' + _TRPCODE + '&UNITCODE=' + _UNITCODE + '&DIVISI=' + _DIVCODE + '&TAHUNTANAM=' + _THNTNM;
        param += '&JJG=' + _JJG + '&BRONDOLAN=' + _BRONDOLAN + '&DRIVER=' + _DRIVER + '&BERATKIRIM=' + _BERATKIRIM + '&TICKETNO2=' + _TICKETNO2 + '&IDWB=' + _IDWB;
        param += '&NETTO=' + _NETTO + '&DATEOUT=' + _DATEOUT + '&WEIGH2=' + _WEIGH2 + '&MILLCODE=' + MILLCODE+'&POTONGAN='+_POTONGAN;
        param +='&buahbusuk='+buahbusuk+'&buahkrgmatang='+buahkrgmatang+'&buahsakit='+buahsakit+'&janjangkosong='+janjangkosong+'&lwtmatang='+lwtmatang;
        param +='&mentah='+mentah+'&tkpanjang='+tkpanjang;
        tujuan='simpan_trxtbs_int.php';
        post_response_text(tujuan+'?'+'proses=getFormPecahTiket', param, respog);
	
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
							document.getElementById('formPecahTiket').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }
	 }
} 

function hitungpecah()
{
    _WEIGH1 = document.getElementById('WEIGH1').value;
    _WEIGH2 = document.getElementById('WEIGH2').value;
    _POTONGAN = document.getElementById('POTONGAN').value;
    _JJG = document.getElementById('JUMJJG').value;
    jjg1=document.getElementById('jjg1st').value;
    weigh11=document.getElementById('weigh11');
    weigh21=document.getElementById('weigh21');
    potongan1=document.getElementById('POTONGAN1');
    jjg2=document.getElementById('jjg2');
    weigh12=document.getElementById('weigh12');
    weigh22=document.getElementById('weigh22');
    potongan2=document.getElementById('POTONGAN2');
    persen=document.getElementById('pct');
    if (jjg1!=''){
        if (jjg1!=0){
            if (_JJG-jjg1==0) 
                dasar=_JJG 
            else 
                dasar=_JJG-jjg1;
            pct=(dasar)/_JJG*100;
            persen.value=pct;
            change_number(persen);
            document.getElementById('jjg1').value=jjg1;
            hitung1=parseFloat(_WEIGH1)-(parseFloat(_WEIGH1)*parseFloat(pct)/100);
            weigh11.value=hitung1;
            change_number(weigh11);
            hitung2=parseFloat(_WEIGH2)-(parseFloat(_WEIGH2)*parseFloat(pct)/100);
            weigh21.value=hitung2;
            change_number(weigh21);
            hitungpotongan1=parseFloat(_POTONGAN)-(parseFloat(_POTONGAN)*parseFloat(pct)/100);
            potongan1.value=hitungpotongan1;
            change_number(potongan1);
            hitungjjg2=parseFloat(_JJG)*parseFloat(pct)/100;
            jjg2.value=hitungjjg2;
            hitung21=parseFloat(_WEIGH1)*parseFloat(pct)/100;
            weigh12.value=hitung21;
            change_number(weigh12);
            remove_comma(weigh12);
            hitung22=parseFloat(_WEIGH2)*parseFloat(pct)/100;
            weigh22.value=hitung22;
            change_number(weigh22);
            hitungpotongan2=parseFloat(_POTONGAN)*parseFloat(pct)/100;
            potongan2.value=hitungpotongan2;
            change_number(potongan2);
        } else {
            jjg1.value=_JJG;
            weigh11.value=_WEIGH1;
            weigh21.value=_WEIGH2;
            potongan1.value=_POTONGAN;
            jjg2.value=0;
            weigh12.value=0;
            weigh22.value=0;
            potongan2.value=0;
        }
    } else {
        jjg1.value=_JJG;
        weigh11.value=_WEIGH1;
        weigh21.value=_WEIGH2;
        potongan1.value=_POTONGAN;
        jjg2.value=0;
        weigh12.value=0;
        weigh22.value=0;
        potongan2.value=0;
    }
}