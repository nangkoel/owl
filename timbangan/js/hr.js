//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//title/jabatan
nForm = '';   //to store original tab caption
cForm = '';   //to store input original Employee input form when the form changed
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function loaX(key,field){
		param='key='+key+'&field='+field;
		if (field == 'Company') {
			//alert (param);
			hubungkan_post('load_unit.php', param, response_unit);
		}
}
function response_unit()
{
     if(con.readyState==4)
     {
        if(con.status==200)
        {
		   //alert(con.responseText);
		   document.getElementById('unit').innerHTML=con.responseText;
		   unlock();
		}
        else
        {
		  unlock();
          error_catch(con.status);
        }
     }
}
function loaW(key,field){
		//alert(document.getElementById('kelompok').innerHTML);
		param='key='+key+'&field='+field;
		if (field == 'wilayah') {
			//alert (param);
			hubungkan_post('load_company.php', param, response_company);
		}
}
function response_company()
{
     if(con.readyState==4)
     {
        if(con.status==200)
        {
		   //alert(con.responseText);
		   document.getElementById('company').innerHTML=con.responseText;
		   unlock();
		}
        else
        {
		  unlock();
          error_catch(con.status);
        }
     }
	 //bersihkan2();
}
function cari(){
	window.find();
}
//======================Cari2
function cari2()
{
	_cari=document.getElementById('cari').value;
	_lokasi=document.getElementById('nlokasi').value;
	_order=document.getElementById('norder').value;
	window.location='?lokasi='+_lokasi+'&order='+_order+'&cari='+_cari+'&field='+field;
	//alert('?lokasi='+_lokasi+'&order='+_order+'&cari='+_cari+'&field='+field);
}
//======================
function isProper() {
   //if (!string) return false;
   if (document.getElementById('name').value=='') return false;
   var iChars = "*|,\":<>[]{}`\';()@&$#%";
   //var ichars =  /^[0-9a-zA-Z]+$/;
   //var ichars =  /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
   for (var i = 0; i < document.getElementById('name').value.length; i++) {
      if (iChars.indexOf(document.getElementById('name').value.charAt(i)) != -1)
         return false;
   }
   return true;
}
function isProper() {
    if (document.getElementById('name').value.search(/^\w+( \w+)?$/) != -1)
        return true;
    else
        return false;
}
//=====master wilayah
function simpanWilayah(){
    id = document.getElementById('idx').value;
    code = document.getElementById('newTitle').value;
    name = document.getElementById('name').value;
    o_name = document.getElementById('name');
   if (document.getElementById('newTitle').value.length == '0') {
			alert('Kolom Kode masih kosong');
			document.getElementById('newTitle').focus();
		}
	else if(name.length == '0'){
		    alert('Kolom Uraian masih kosong');
			o_name.focus();
	}
    /*else if(isProper(document.getElementById('name').value) == false){
    	alert("Anda Memasukkan Karakter Yang Tidak Diijinkan!!!.");
    	o_name.value='';
    	o_name.focus();
    }*/
	else {
			param = 'id='+id+'&code='+code+'&name='+name;
			//alert(param);
			if (confirm('Anda Yakin Ingin Menyimpan Data wilayah :' + ' ' + name)) {
					post_response_text('simpan_master_wilayah.php', param, respon);
				}
		    else{
		    	window.location="master_wilayah.php";
		    }
		}
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="master_wilayah.php";
                }
                else {
                    //document.getElementById('result').innerHTML = con.responseText;
                    alert('Berhasil di simpan');
                    window.location="master_wilayah.php";
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function changeWilayah(code,name){
    document.getElementById('idx').value = 'edit';
    document.getElementById('newTitle').value = code;
    document.getElementById('name').value = name;
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('newTitle').disabled = true;
}
function batalWilayah(){
    window.location="master_wilayah.php";
}

//===============================master company
function saveCompany(){
    id = document.getElementById('idx').value;
    code = document.getElementById('newTitle').value;
    name = document.getElementById('name').value;
    o_name = document.getElementById('name');
    o_wilayah = document.getElementById('wilayah');
	wilcode = document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].value;
	wilname = document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].text;
    alamat = document.getElementById('alamat').value;
    kota = document.getElementById('kota').value;
    if (document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].value==0) {
			alert('Wilayah belum dipilih!!!');
			document.getElementById('wilayah').options[document.getElementById('wilayah').focus()];
		}
    else if(document.getElementById('newTitle').value.length == '0'){
    	alert('Kolom Kode masih kosong');
		document.getElementById('newTitle').focus();
    }
    else if(name.length == '0'){
		    alert('Kolom Nama Perusahaan masih kosong');
			o_name.focus();
	}
    /*else if(isProper(document.getElementById('name').value) == false){
    	alert("Anda Memasukkan Karakter Yang Tidak Diijinkan!!!.");
    	o_name.value='';
    	o_name.focus();
    }*/
	else {
			param = 'id='+id+'&code='+code+'&name='+name+'&wilcode='+wilcode+'&alamat='+alamat+'&kota='+kota;
			//alert(param);
			if (confirm('Anda Yakin Ingin Menyimpan Data Perusahaan :' + ' ' + name)) {
					post_response_text('simpan_master_company.php', param, respon);
				}
		}
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="master_company.php";
                }
                else {
                    //document.getElementById('result').innerHTML = con.responseText;
                    //clearFormDept();
                     alert('Berhasil di simpan');
                    window.location="master_company.php";
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearCompany(){
    window.location="master_company.php";
}
function changeCompany(code,name,wilcode,wilname,alamat,kota){
    document.getElementById('idx').value = 'edit';
    document.getElementById('newTitle').value = code;
    document.getElementById('name').value = name;
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('newTitle').disabled = true;
    document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].text=wilname;
	document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].value=wilcode;
	document.getElementById('wilayah').disabled=true;
    document.getElementById('alamat').value = alamat;
    document.getElementById('kota').value = kota;
}

//===============================master unit
function saveUnit(){
    id = document.getElementById('idx').value;
    code = document.getElementById('newTitle').value;
    name = document.getElementById('name').value;
    o_name = document.getElementById('name');
    o_wilayah = document.getElementById('wilayah');
	wilcode = document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].value;
	wilname = document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].text;
	compcode = document.getElementById('company').options[document.getElementById('company').selectedIndex].value;
	compname = document.getElementById('company').options[document.getElementById('company').selectedIndex].text;
    if (document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].value==0) {
			alert('Wilayah belum dipilih!!!');
			document.getElementById('wilayah').options[document.getElementById('wilayah').focus()];
		}
	else if(document.getElementById('company').options[document.getElementById('company').selectedIndex].value==0) {
			alert('Perusahaan belum dipilih!!!');
			document.getElementById('company').options[document.getElementById('company').focus()];
		}
    else if(document.getElementById('newTitle').value.length == '0'){
    	alert('Kolom Kode masih kosong');
		document.getElementById('newTitle').focus();
    }
    else if(name.length == '0'){
		    alert('Kolom Nama Unit/Estate masih kosong');
			o_name.focus();
	}
    /*else if(isProper(document.getElementById('name').value) == false){
    	alert("Anda Memasukkan Karakter Yang Tidak Diijinkan!!!.");
    	o_name.value='';
    	o_name.focus();
    }*/
	else {
			param = 'id='+id+'&code='+code+'&name='+name+'&wilcode='+wilcode+'&compcode='+compcode;
			//alert(param);
			if (confirm('Anda Yakin Ingin Menyimpan Data Unit/Estate :' + ' ' + name)) {
					post_response_text('simpan_master_unit.php', param, respon);
				}
		}
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="master_unit.php";
                }
                else {
                    //document.getElementById('result').innerHTML = con.responseText;
                    //clearFormDept();
                     alert('Berhasil di simpan');
                    window.location="master_unit.php";
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearUnit(){
    window.location="master_unit.php";
}
function changeUnit(compcode,compname,wilcode,wilname,code,name){
    document.getElementById('idx').value = 'edit';
    document.getElementById('newTitle').value = code;
    document.getElementById('name').value = name;
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('newTitle').disabled = true;
    document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].text=wilname;
	document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].value=wilcode;
	document.getElementById('company').options[document.getElementById('company').selectedIndex].text=compname;
	document.getElementById('company').options[document.getElementById('company').selectedIndex].value=compcode;
	document.getElementById('wilayah').disabled=true;
	document.getElementById('company').disabled=true;
}

//=================master afdeling
function saveAfdeling(){
    id = document.getElementById('idx').value;
    code = document.getElementById('newTitle').value;
    name = document.getElementById('name').value;
    o_name = document.getElementById('name');
    o_company = document.getElementById('company');
	unitcode = document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
	unitname = document.getElementById('unit').options[document.getElementById('unit').selectedIndex].text;
	compcode = document.getElementById('company').options[document.getElementById('company').selectedIndex].value;
	compname = document.getElementById('company').options[document.getElementById('company').selectedIndex].text;
    if (document.getElementById('company').options[document.getElementById('company').selectedIndex].value==0) {
			alert('Perusahaan belum dipilih!!!');
			document.getElementById('company').options[document.getElementById('company').focus()];
		}
	else if(document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value==0) {
			alert('Unit/Estate belum dipilih!!!');
			document.getElementById('unit').options[document.getElementById('unit').focus()];
		}
    else if(document.getElementById('newTitle').value.length == '0'){
    	alert('Kolom Kode Afdeling masih kosong');
		document.getElementById('newTitle').focus();
    }
    else if(name.length == '0'){
		    alert('Kolom Nama Afdeling masih kosong');
			o_name.focus();
	}
    /*else if(isProper(document.getElementById('name').value) == false){
    	alert("Anda Memasukkan Karakter Yang Tidak Diijinkan!!!.");
    	o_name.value='';
    	o_name.focus();
    }*/
	else {
			param = 'id='+id+'&code='+code+'&name='+name+'&compcode='+compcode+'&unitcode='+unitcode;
			if (confirm('Anda Yakin Ingin Menyimpan Data Afdeling :' + ' ' + name)) {
					post_response_text('simpan_master_afdeling.php', param, respon);
				}
		}
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="master_divisi.php";
                }
                else {
                     alert('Berhasil di simpan');
                    window.location="master_divisi.php";
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearAfdeling(){
    window.location="master_divisi.php";
}
function changeAfdeling(compcode,compname,unitcode,unitname,code,name){
    document.getElementById('idx').value = 'edit';
    document.getElementById('newTitle').value = code;
    document.getElementById('name').value = name;
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('newTitle').disabled = true;
    document.getElementById('unit').options[document.getElementById('unit').selectedIndex].text=unitname;
	document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value=unitcode;
	document.getElementById('company').options[document.getElementById('company').selectedIndex].text=compname;
	document.getElementById('company').options[document.getElementById('company').selectedIndex].value=compcode;
	document.getElementById('unit').disabled=true;
	document.getElementById('company').disabled=true;
}

//=================master vendor
function saveVendor(){
    id = document.getElementById('idx').value;
    code = document.getElementById('newTitle').value;
    name = document.getElementById('name').value;
    o_name = document.getElementById('name');
    alamat = document.getElementById('alamat').value;
    kota = document.getElementById('kota').value;
    if(document.getElementById('newTitle').value.length == '0'){
    	alert('Kolom Kode masih kosong');
		document.getElementById('newTitle').focus();
    }
    else if(name.length == '0'){
		    alert('Kolom Nama Vendor masih kosong');
			o_name.focus();
	}/*
    else if(isProper(document.getElementById('name').value) == false){
    	alert("Anda Memasukkan Karakter Yang Tidak Diijinkan!!!.");
    	o_name.value='';
    	o_name.focus();
    }*/
	else {
			param = 'id='+id+'&code='+code+'&name='+name+'&alamat='+alamat+'&kota='+kota;
			//alert(param);
			if (confirm('Anda Yakin Ingin Menyimpan Data Vendor :' + ' ' + name)) {
					post_response_text('simpan_master_vendor.php', param, respon);
				}
		}
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="master_vendor.php";
                }
                else {
                    //document.getElementById('result').innerHTML = con.responseText;
                    //clearFormDept();
                     alert('Berhasil di simpan');
                    window.location="master_vendor.php";
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearVendor(){
    window.location="master_vendor.php";
}
function changeVendor(code,name,alamat,kota){
    document.getElementById('idx').value = 'edit';
    document.getElementById('newTitle').value = code;
    document.getElementById('name').value = name;
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('newTitle').disabled = true;
    document.getElementById('alamat').value = alamat;
    document.getElementById('kota').value = kota;
}

//=================master customer
function saveCustomer(){
    id = document.getElementById('idx').value;
    code = document.getElementById('newTitle').value;
    name = document.getElementById('name').value;
    o_name = document.getElementById('name');
    alamat = document.getElementById('alamat').value;
    kota = document.getElementById('kota').value;
    if(document.getElementById('newTitle').value.length == '0'){
    	alert('Kolom Kode Customer masih kosong');
		document.getElementById('newTitle').focus();
    }
    else if(name.length == '0'){
		    alert('Kolom Nama Customer masih kosong');
			o_name.focus();
	}/*
    else if(isProper(document.getElementById('name').value) == false){
    	alert("Anda Memasukkan Karakter Yang Tidak Diijinkan!!!.");
    	o_name.value='';
    	o_name.focus();
    }*/
	else {
			param = 'id='+id+'&code='+code+'&name='+name+'&alamat='+alamat+'&kota='+kota;
			if (confirm('Anda Yakin Ingin Menyimpan Data Customer :' + ' ' + name)) {
					post_response_text('simpan_master_customer.php', param, respon);
				}
		}
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="master_customer.php";
                }
                else {
                     alert('Berhasil di simpan');
                     window.location="master_customer.php";
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearCustomer(){
    window.location="master_customer.php";
}
function changeCustomer(code,name,alamat,kota){
    document.getElementById('idx').value = 'edit';
    document.getElementById('newTitle').value = code;
    document.getElementById('name').value = name;
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('newTitle').disabled = true;
    document.getElementById('alamat').value = alamat;
    document.getElementById('kota').value = kota;
}

//=====master tipe kendaraan
function simpanTipe(){
    id = document.getElementById('idx').value;
    code = document.getElementById('newTitle').value;
    name = document.getElementById('name').value;
    o_name = document.getElementById('name');
   if (document.getElementById('newTitle').value.length == '0') {
			alert('Kolom Kode masih kosong');
			document.getElementById('newTitle').focus();
		}
	else if(name.length == '0'){
		    alert('Kolom Nama Tipe Kendaraan masih kosong!!!');
			o_name.focus();
	}/*
    else if(isProper(document.getElementById('name').value) == false){
    	alert("Anda Memasukkan Karakter Yang Tidak Diijinkan!!!.");
    	o_name.value='';
    	o_name.focus();
    }*/
	else {
			param = 'id='+id+'&code='+code+'&name='+name;
			//alert(param);
			if (confirm('Anda Yakin Ingin Menyimpan Tipe Kendaraan :' + ' ' + name)) {
					post_response_text('simpan_master_tipe_kendaraan.php', param, respon);
				}
		    else{
		    	window.location="master_tipe_kendaraan.php";
		    }
		}
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="master_tipe_kendaraan.php";
                }
                else {
                    alert('Berhasil di simpan');
                    window.location="master_tipe_kendaraan.php";
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function changeTipe(code,name){
    document.getElementById('idx').value = 'edit';
    document.getElementById('newTitle').value = code;
    document.getElementById('name').value = name;
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('newTitle').disabled = true;
}
function batalTipe(){
    window.location="master_tipe_kendaraan.php";
}

//=================master kendaraan
function saveKendaraan(){
    id = document.getElementById('idx').value;
    code = document.getElementById('newTitle').value;
    name = document.getElementById('name').value;name2 = document.getElementById('name2').value;
    o_name = document.getElementById('name');o_name2 = document.getElementById('name2');
    o_company = document.getElementById('company');
	typecode = document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].value;
	typename = document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].text;
	trpcode = document.getElementById('company').options[document.getElementById('company').selectedIndex].value;
	trpname = document.getElementById('company').options[document.getElementById('company').selectedIndex].text;
    driver = document.getElementById('supir').value;
    nosim = document.getElementById('nosim').value;
    if (document.getElementById('company').options[document.getElementById('company').selectedIndex].value==0) {
			alert('Vendor belum dipilih!!!');
			document.getElementById('company').options[document.getElementById('company').focus()];
		}
	else if(document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].value==0) {
			alert('Tipe Kendaraan belum dipilih!!!');
			document.getElementById('tipe').options[document.getElementById('tipe').focus()];
		}
    else if(document.getElementById('newTitle').value.length == '0'){
    	alert('Kolom No.Kendaraan masih kosong!!!');
		document.getElementById('newTitle').focus();
    }
    else if(document.getElementById('newTitle').value.length < '5'){
    	alert('Kolom No.Kendaraan minimal 6 Digit!!!');
		document.getElementById('newTitle').focus();
    }
    else if(document.getElementById('newTitle').value == '0'){
    	alert('Kolom No.Kendaraan Tidak Boleh Diisi Nol(0)!!!');
		document.getElementById('newTitle').focus();
    }
    else if(name.length == '0'){
		    alert('Kolom Tarra Minimum masih kosong');
			o_name.focus();
	}
	else if(name == '0'){
		    alert('Kolom Tarra Tidak Boleh Bernilai 0!!!');
			o_name.focus();
	}
	else if(name2.length == '0'){
		    alert('Kolom Tarra Maksimum masih kosong');
			o_name2.focus();
	}
	else if(name2 == '0'){
		    alert('Kolom Tarra Tidak Boleh Bernilai 0!!!');
			o_name2.focus();
	}
	else if(name2<name){
		    alert('Tarra Maksimum Tidak Boleh Lebih Kecil Dari Tarra Minimum!!!');
			o_name2.focus();
	}
	else if(name2==name){
		    alert('Tarra Maksimum Tidak Boleh Sama Dengan Tarra Minimum!!!');
			o_name2.focus();
	}
	else {
			param = 'id='+id+'&code='+code+'&name='+name+'&trpcode='+trpcode+'&typecode='+typecode;
			param += '&vehtarmin='+name+'&vehtarmax='+name2+'&driver='+driver+'&nosim='+nosim;
			if (confirm('Anda Yakin Ingin Menyimpan Data Kendaraan :' + ' ' + code)) {
					post_response_text('simpan_master_kendaraan.php', param, respon);
				}
		}
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="master_kendaraan.php";
                }
                else {
                     alert('Berhasil di simpan');
                    window.location="master_kendaraan.php";
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearKendaraan(){
    window.location="master_kendaraan.php";
}
function changeKendaraan(code,trpcode,trpname,vehtypecode,vehtypename,vehtarmin,vehtarmax,driver,nosim){
    document.getElementById('idx').value = 'edit';
    document.getElementById('newTitle').value = code;
    document.getElementById('name').value = vehtarmin;
    document.getElementById('name2').value = vehtarmax;
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('newTitle').disabled = true;
    document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].text=vehtypename;
	document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].value=vehtypecode;
	document.getElementById('company').options[document.getElementById('company').selectedIndex].text=trpname;
	document.getElementById('company').options[document.getElementById('company').selectedIndex].value=trpcode;
	document.getElementById('tipe').disabled=true;
	document.getElementById('company').disabled=true;
	document.getElementById('supir').value = driver;
	document.getElementById('nosim').value = nosim;
}

//=================master product
function saveProduct(){
    id = document.getElementById('idx').value;
    code = document.getElementById('newTitle').value;
    name = document.getElementById('name').value;
    o_name = document.getElementById('name');
    if(document.getElementById('newTitle').value.length == '0'){
    	alert('Kolom Kode Product masih kosong!!!');
		document.getElementById('newTitle').focus();
    }
    else if(name.length == '0'){
		    alert('Kolom Nama Product masih kosong');
			o_name.focus();
	}
	else {
			param = 'id='+id+'&code='+code+'&name='+name;
			if (confirm('Anda Yakin Ingin Menyimpan Data Product :' + ' ' + name)) {
					post_response_text('simpan_master_product.php', param, respon);
				}
		}
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="master_product.php";
                }
                else {
                     alert('Berhasil di simpan');
                    window.location="master_product.php";
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearProduct(){
    window.location="master_product.php";
}
function changeProduct(code,name){
    document.getElementById('idx').value = 'edit';
    document.getElementById('newTitle').value = code;
    document.getElementById('name').value = name;
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('newTitle').disabled = true;
}
//=================master kontrak
function saveKontrak(){
    id = document.getElementById('idx').value;
    code = document.getElementById('newTitle').value;
    tglkontrak = document.getElementById('tglkontrak').value;
    o_buyer = document.getElementById('buyer');
    buyercode = document.getElementById('buyer').options[document.getElementById('buyer').selectedIndex].value;
	buyername = document.getElementById('buyer').options[document.getElementById('buyer').selectedIndex].text;
    qty = document.getElementById('qty').value;
    ket = document.getElementById('ket').value;
    if(document.getElementById('status1').checked)
		_CTRSTATUS=document.getElementById('status1').value;
	else
		_CTRSTATUS=document.getElementById('status2').value;

	if(document.getElementById('newTitle').value.length == '0'){
    	alert('Kolom No.Kontrak masih kosong!!!');
		document.getElementById('newTitle').focus();
    }
    else if(document.getElementById('tglkontrak').value.length == '0'){
    	alert('Kolom Tanggal Kontrak masih kosong!!!');
		document.getElementById('tglkontrak').focus();
    }
    else if (document.getElementById('buyer').options[document.getElementById('buyer').selectedIndex].value==0) {
			alert('Pembeli belum dipilih!!!');
			document.getElementById('buyer').options[document.getElementById('buyer').focus()];
		}
    else if(document.getElementById('qty').value.length == '0'){
    	alert('Kolom Qty Kontrak belum diisi!!!');
		document.getElementById('qty').focus();
    }
    else if(document.getElementById('qty').value == '0'){
    	alert('Kolom Qty Kontrak Tidak Boleh Diisi Nol(0)!!!');
		document.getElementById('qty').focus();
    }

	else {
			param = 'id='+id+'&code='+code+'&tglkontrak='+tglkontrak+'&buyercode='+buyercode;
			param += '&qty='+qty+'&ket='+ket+'&status='+_CTRSTATUS;
			//alert(param);
			if (confirm('Anda Yakin Ingin Menyimpan Data Kontrak :' + ' ' + code)) {
					post_response_text('simpan_master_kontrak.php', param, respon);
				}
		}
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="master_kontrak.php";
                }
                else {
                     alert('Berhasil di simpan');
                    window.location="master_kontrak.php";
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearKontrak(){
    window.location="master_kontrak.php";
}
function changeKontrak(code,ctrdate,buyercode,buyername,qty,ket,status){
    document.getElementById('idx').value = 'edit';
    document.getElementById('newTitle').value = code;
    document.getElementById('tglkontrak').value = ctrdate;
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('newTitle').disabled = true;
    document.getElementById('buyer').options[document.getElementById('buyer').selectedIndex].text=buyername;
	document.getElementById('buyer').options[document.getElementById('buyer').selectedIndex].value=buyercode;
	document.getElementById('buyer').disabled=true;
	//document.getElementById('qty').disabled=true;
	document.getElementById('qty').value = qty;
	document.getElementById('ket').value = ket;
	if(status=='Aktif')
	{
		document.getElementById('status1').checked = true;
		document.getElementById('status2').checked = false;
	}
	else
	{
		document.getElementById('status2').checked = true;
        document.getElementById('status1').checked = false;
	}
}
//=================master sipb
function saveSIPB(){
    id = document.getElementById('idx').value;
    code = document.getElementById('newTitle').value;
    tglsipb = document.getElementById('tglsipb').value;
    ctrno = document.getElementById('noctr').options[document.getElementById('noctr').selectedIndex].value;
	ctrno = document.getElementById('noctr').options[document.getElementById('noctr').selectedIndex].text;
	product = document.getElementById('product').options[document.getElementById('product').selectedIndex].value;
	trp = document.getElementById('trp').options[document.getElementById('trp').selectedIndex].value;
    qty = document.getElementById('qty').value;
    ket = document.getElementById('ket').value;
    if(document.getElementById('status1').checked)
		_SIPBSTATUS=document.getElementById('status1').value;
	else
		_SIPBSTATUS=document.getElementById('status2').value;

	if(document.getElementById('newTitle').value.length == '0'){
    	alert('Kolom No.SIPB masih kosong!!!');
		document.getElementById('newTitle').focus();
    }
    else if(document.getElementById('tglsipb').value.length == '0'){
    	alert('Kolom Tanggal SIPB masih kosong!!!');
		document.getElementById('tglsipb').focus();
    }
    else if (document.getElementById('noctr').options[document.getElementById('noctr').selectedIndex].value==0) {
			alert('Kontrak belum dipilih!!!');
			document.getElementById('noctr').options[document.getElementById('noctr').focus()];
		}
	else if (document.getElementById('product').options[document.getElementById('product').selectedIndex].value==0) {
			alert('Product belum dipilih!!!');
			document.getElementById('product').options[document.getElementById('product').focus()];
		}
	else if (document.getElementById('trp').options[document.getElementById('trp').selectedIndex].value==0) {
			alert('Transporter belum dipilih!!!');
			document.getElementById('trp').options[document.getElementById('trp').focus()];
		}
    else if(document.getElementById('qty').value.length == '0'){
    	alert('Kolom Qty SIPB belum diisi!!!');
		document.getElementById('qty').focus();
    }
    else if(document.getElementById('qty').value == '0'){
    	alert('Kolom Qty SIPB Tidak Boleh Diisi Nol(0)!!!');
		document.getElementById('qty').focus();
    }

	else {
			param = 'id='+id+'&code='+code+'&tglsipb='+tglsipb+'&ctrno='+ctrno+'&product='+product;
			param += '&trp='+trp+'&qty='+qty+'&ket='+ket+'&status='+_SIPBSTATUS;
			if (confirm('Anda Yakin Ingin Menyimpan Data SIPB :' + ' ' + code)) {
					post_response_text('simpan_master_sipb.php', param, respon);
				}
		}
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    window.location="master_sipb.php";
                }
                else {
                     alert('Berhasil di simpan');
                    window.location="master_sipb.php";
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearSIPB(){
    window.location="master_sipb.php";
}
function changeSIPB(_CTRNO,_SIPBNO2,_SIPBDATE,_PRODUCTCODE,_SIPBQTY,_DESCRIPTION,_SIPBSTATUS,_USERID,_TRPCODE,_BUYERNAME,_PRODUCTNAME,_TRPNAME){
    document.getElementById('idx').value = 'edit';
    document.getElementById('newTitle').value = _SIPBNO2;
    document.getElementById('newTitle').disabled = true;
    document.getElementById('tglsipb').value = _SIPBDATE;
    document.getElementById('noctr').options[document.getElementById('noctr').selectedIndex].text=_CTRNO;
	document.getElementById('noctr').options[document.getElementById('noctr').selectedIndex].value=_CTRNO;
	document.getElementById('noctr').disabled=true;
	document.getElementById('product').options[document.getElementById('product').selectedIndex].text=_PRODUCTNAME;
	document.getElementById('product').options[document.getElementById('product').selectedIndex].value=_PRODUCTCODE;
	document.getElementById('product').disabled=true;
	document.getElementById('trp').options[document.getElementById('trp').selectedIndex].text=_TRPNAME;
	document.getElementById('trp').options[document.getElementById('trp').selectedIndex].value=_TRPCODE;
    document.getElementById('note').innerHTML = 'Edit ';
	//document.getElementById('qty').disabled=true;
	document.getElementById('qty').value = _SIPBQTY;
	document.getElementById('BUYERCODE').value = _BUYERNAME;
	document.getElementById('ket').value = _DESCRIPTION;
	if(_SIPBSTATUS=='Aktif')
	{
		document.getElementById('status1').checked = true;
		document.getElementById('status2').checked = false;
	}
	else
	{
		document.getElementById('status2').checked = true;
        document.getElementById('status1').checked = false;
	}
}
function loa(key,field,_BUYERNAME){
		//alert(document.getElementById('kelompok').innerHTML);
		param='key='+key+'&field='+field+'&buyername='+_BUYERNAME;
		if (field == 'CTRNO') {
			//alert (param);
			hubungkan_post('load_contract.php', param, response_contract);
		}
}
function response_contract()
{
     if(con.readyState==4)
     {
        if(con.status==200)
        {
		   //alert(con.responseText);
		   //document.getElementById('BUYERCODE').innerHtml=con.responseText;
		   document.getElementById('BUYERCODE').value=con.responseText;
		   //unlock();
		}
        else
        {
		  //unlock();
          error_catch(con.status);
        }
     }
}







