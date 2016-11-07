/**
 * @author repindra.ginting
 */

/*

//Example for xml response processing
function simpanKaryawan()
{
		//param='kodejabatan='+kodejabatan+'&namajabatan='+namajabatan+'&method='+met;
		tujuan='sdm_slave_save_datakaryawan.php';
		param='';
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
							//alert(con.responseText);
							xml=con.responseText.toString();
							xmlobject = (new DOMParser()).parseFromString(xml, "text/xml");
							alert(xmlobject.getElementsByTagName('ch1')[0].firstChild.nodeValue);
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}
*/

function simpanKaryawan()
{
//get input text and textarea value	
	nik					=trim(document.getElementById('nik').value);
	namakaryawan		=trim(document.getElementById('namakaryawan').value); 
	tempatlahir			=trim(document.getElementById('tempatlahir').value);
	tanggallahir		=trim(document.getElementById('tanggallahir').value);
	noktp				=trim(document.getElementById('noktp').value);
	nopassport			=trim(document.getElementById('nopassport').value);
	npwp				=trim(document.getElementById('npwp').value);
	kodepos				=trim(document.getElementById('kodepos').value);
	alamataktif			=trim(document.getElementById('alamataktif').value);
	kota				=trim(document.getElementById('kota').value);
	noteleponrumah		=trim(document.getElementById('noteleponrumah').value);
	nohp				=trim(document.getElementById('nohp').value);
	norekeningbank		=trim(document.getElementById('norekeningbank').value);
	namabank			=trim(document.getElementById('namabank').value);
	tanggalmasuk		=trim(document.getElementById('tanggalmasuk').value);
	tanggalkeluar		=trim(document.getElementById('tanggalkeluar').value);
	tanggalmenikah		=trim(document.getElementById('tanggalmenikah').value);
	jumlahanak			=trim(document.getElementById('jumlahanak').value);
	jumlahtanggungan	=trim(document.getElementById('jumlahtanggungan').value);
	tanggalmenikah		=trim(document.getElementById('tanggalmenikah').value);
	notelepondarurat	=trim(document.getElementById('notelepondarurat').value);
	email				=trim(document.getElementById('email').value);
	method				=trim(document.getElementById('method').value);
	karyawanid			=trim(document.getElementById('karyawanid').value);
//get options value	
	jeniskelamin		=document.getElementById('jeniskelamin');
	jeniskelamin	=trim(jeniskelamin.options[jeniskelamin.selectedIndex].value);
	agama				=document.getElementById('agama');
	agama			=trim(agama.options[agama.selectedIndex].value);	
	bagian				=document.getElementById('bagian');
	bagian			=trim(bagian.options[bagian.selectedIndex].value);
	kodejabatan			=document.getElementById('kodejabatan');
	kodejabatan		=trim(kodejabatan.options[kodejabatan.selectedIndex].value);
	kodegolongan		=document.getElementById('kodegolongan');
	kodegolongan	=trim(kodegolongan.options[kodegolongan.selectedIndex].value);
	lokasitugas			=document.getElementById('lokasitugas');
	lokasitugas		=trim(lokasitugas.options[lokasitugas.selectedIndex].value);
	kodeorganisasi		=document.getElementById('kodeorganisasi');
	kodeorganisasi	=trim(kodeorganisasi.options[kodeorganisasi.selectedIndex].value);
	tipekaryawan		=document.getElementById('tipekaryawan');
	tipekaryawan	=trim(tipekaryawan.options[tipekaryawan.selectedIndex].value);
	warganegara			=document.getElementById('warganegara');
	warganegara		=trim(warganegara.options[warganegara.selectedIndex].value);
	lokasipenerimaan	=document.getElementById('lokasipenerimaan');
	lokasipenerimaan=trim(lokasipenerimaan.options[lokasipenerimaan.selectedIndex].value);
	statuspajak			=document.getElementById('statuspajak');
	statuspajak		=trim(statuspajak.options[statuspajak.selectedIndex].value);
	provinsi			=document.getElementById('provinsi');
	provinsi		=trim(provinsi.options[provinsi.selectedIndex].value);
	sistemgaji			=document.getElementById('sistemgaji');
	sistemgaji		=trim(sistemgaji.options[sistemgaji.selectedIndex].value);
	golongandarah		=document.getElementById('golongandarah');
	golongandarah	=trim(golongandarah.options[golongandarah.selectedIndex].value);
	//convert + sign on golongan darah when posting to prevent missing this character
	 while(golongandarah.indexOf("+")>-1)
	   {
	   	golongandarah=golongandarah.replace("+","%2B");
	   }
	statusperkawinan	=document.getElementById('statusperkawinan');
	statusperkawinan=trim(statusperkawinan.options[statusperkawinan.selectedIndex].value);
	levelpendidikan		=document.getElementById('levelpendidikan');
	levelpendidikan	=trim(levelpendidikan.options[levelpendidikan.selectedIndex].value);

 	if(noktp=='' || alamataktif=='' || kota=='' || tempatlahir =='' || tanggallahir.length!=10 || tanggalmasuk.length!=10)
	{
		alert('ID.Num/KTP, Address/Alamat, City/Kota,\nPlace Of Birth/Tempat lahir, Birth.Date/Tgl.lahir,\nJoin.date/Tgl.Masuk \n are Obligatory');
	}
	else
	{
	  param='nik='+nik+'&namakaryawan='+namakaryawan+'&tempatlahir='+tempatlahir;
	  param+='&tanggallahir='+tanggallahir+'&noktp='+noktp;	
      param+='&nopassport='+nopassport+'&npwp='+npwp+'&kodepos='+kodepos;
	  param+='&alamataktif='+alamataktif+'&kota='+kota+'&noteleponrumah='+noteleponrumah
	  param+='&nohp='+nohp+'&norekeningbank='+norekeningbank+'&namabank='+namabank+'&tanggalmasuk='+tanggalmasuk;
	  param+='&tanggalkeluar='+tanggalkeluar+'&jumlahanak='+jumlahanak;
	  param+='&jumlahtanggungan='+jumlahtanggungan+'&tanggalmenikah='+tanggalmenikah;
	  param+='&notelepondarurat='+notelepondarurat+'&email='+email;
	  param+='&jeniskelamin='+jeniskelamin+'&agama='+agama;
	  param+='&bagian='+bagian+'&kodejabatan='+kodejabatan;
	  param+='&kodegolongan='+kodegolongan+'&lokasitugas='+lokasitugas;
	  param+='&kodeorganisasi='+kodeorganisasi+'&tipekaryawan='+tipekaryawan;
	  param+='&warganegara='+warganegara+'&lokasipenerimaan='+lokasipenerimaan;
	  param+='&statuspajak='+statuspajak+'&provinsi='+provinsi;
	  param+='&sistemgaji='+sistemgaji+'&golongandarah='+golongandarah;
	  param+='&statusperkawinan='+statusperkawinan+'&levelpendidikan='+levelpendidikan;	
	  param+='&method='+method+'&karyawanid='+karyawanid;
	  
   tujuan='sdm_slave_save_datakaryawan.php';
	if(confirm('Save '+namakaryawan+' ?'))
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
							alert('Done');
							//alert(con.responseText);
							controlThisForm(con.responseText);
							enableOtherButton();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}

function controlThisForm(tex)
{
	xml=tex.toString();
	xmlobject = (new DOMParser()).parseFromString(xml, "text/xml");
    getId=xmlobject.getElementsByTagName('karyawanid')[0].firstChild.nodeValue;
	getNama=xmlobject.getElementsByTagName('namakaryawan')[0].firstChild.nodeValue;
	if (trim(getId) != '') {
		//Change first Tab Caption
		document.getElementById('tabFRM0').innerHTML = getNama;
		//change to update method
		document.getElementById('method').value = 'update';
		//give value to the hidden element karyawanid
		document.getElementById('karyawanid').value = getId;
	}
	else
	{
		alert('Last transaction has nothing affected');
	}
}
function cancelDataKaryawan()
{
	document.getElementById('nik').value='';
	document.getElementById('namakaryawan').value='';
	document.getElementById('tempatlahir').value='';
	document.getElementById('tanggallahir').value='';
	document.getElementById('noktp').value='';
	document.getElementById('nopassport').value='';
	document.getElementById('npwp').value='';
	document.getElementById('alamataktif').value='';
	document.getElementById('kota').value='';
	document.getElementById('provinsi').value='';
	document.getElementById('kodepos').value='';
	document.getElementById('noteleponrumah').value='';
	document.getElementById('nohp').value='';
	document.getElementById('norekeningbank').value='';
	document.getElementById('namabank').value='';
	document.getElementById('sistemgaji').value='';
	document.getElementById('tanggalmasuk').value='';
	document.getElementById('tanggalkeluar').value='';
	document.getElementById('statusperkawinan').value='';
	document.getElementById('tanggalmenikah').value='';
	document.getElementById('jumlahanak').value='';
	document.getElementById('jumlahtanggungan').value='';
	document.getElementById('tanggalmenikah').value='';
	document.getElementById('notelepondarurat').value='';
	document.getElementById('karyawanid').value='';
	document.getElementById('method').value='insert';
	document.getElementById('tabFRM0').innerHTML='New';
}

function enableOtherButton()
{
	//after success saving then activate sumbit button on each tab 
	document.getElementById('btncv').disabled=false;
	document.getElementById('btnpendidikan').disabled=false;
	document.getElementById('btntraining').disabled=false;
	document.getElementById('btnphoto').disabled=false;
	document.getElementById('btnalamat').disabled=false;
	document.getElementById('btnkeluarga').disabled=false;
}
//========================tab pengalaman
function simpanPengalaman()
{
	 namaperusahaan =trim(document.getElementById('namaperusahaan').value);
	 bidangusaha	=(document.getElementById('bidangusaha').value);
	 
	 blnmasuk		=document.getElementById('blnmasuk');
       blnmasuk		=blnmasuk.options[blnmasuk.selectedIndex].value;
	 thnmasuk		=document.getElementById('thnmasuk');
	   thnmasuk		=thnmasuk.options[thnmasuk.selectedIndex].value;
	 blnkeluar		=document.getElementById('blnkeluar');
	   blnkeluar	=blnkeluar.options[blnkeluar.selectedIndex].value;
	 thnkeluar		=document.getElementById('thnkeluar');
       thnkeluar	=thnkeluar.options[thnkeluar.selectedIndex].value;

	 jabatan		=trim(document.getElementById('pengalamanjabatan').value);
	 bagian			=document.getElementById('pengalamanbagian').value;
	 alamat	 		=document.getElementById('pengalamanalamat').value;
	 karyawanid		=document.getElementById('karyawanid').value;
   
   if (blnmasuk == '' || thnmasuk == '' || blnkeluar == '' || thnkeluar == '') {
   	alert('Incorrect period');
   }
   else if(namaperusahaan=='' || bidangusaha=='' || jabatan=='')
   {
   	alert('Data Incomplete');
   }
   else {
   	param = 'namaperusahaan=' + namaperusahaan + '&bidangusaha=' + bidangusaha;
   	param += '&blnmasuk=' + blnmasuk + '&thnmasuk=' + thnmasuk;
   	param += '&blnkeluar=' + blnkeluar + '&thnkeluar=' + thnkeluar;
   	param += '&jabatan=' + jabatan + '&bagian=' + bagian;
   	param += '&alamat=' + alamat + '&karyawanid=' + karyawanid;
   	
   	tujuan = 'sdm_slave_save_riwayat_pekerjaan.php';
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

function delPengalaman(karyawanid,nomor)
{
   	param ='nomor=' + nomor + '&karyawanid=' + karyawanid+'&del=true';
   	tujuan = 'sdm_slave_save_riwayat_pekerjaan.php';
   	if (confirm('Deleting..?')) {
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
//=================tab pendidikan
function simpanPendidikan()
{
		levelpendidikan		=document.getElementById('levelpendidikan');
		levelpendidikan=trim(levelpendidikan.options[levelpendidikan.selectedIndex].value);
		tahunlulus			=document.getElementById('tahunlulus');
		tahunlulus=trim(tahunlulus.options[tahunlulus.selectedIndex].value);
        spesialisasi		=trim(document.getElementById('spesialisasi').value);
        gelar				=trim(document.getElementById('gelar').value);
        namasekolah			=trim(document.getElementById('namasekolah').value);
        nilai				=document.getElementById('nilai').value;
        pendidikankota		=trim(document.getElementById('pendidikankota').value);
        pendidikanketerangan=trim(document.getElementById('pendidikanketerangan').value);
	    karyawanid		=document.getElementById('karyawanid').value;
		
 if(tahunlulus=='' || namasekolah=='')
 {
 	alert('Data incomplete');
 }
 else
 {
 	param='levelpendidikan='+levelpendidikan+'&tahunlulus='+tahunlulus;
	param+='&spesialisasi='+spesialisasi+'&gelar='+gelar;
	param+='&namasekolah='+namasekolah+'&nilai='+nilai;
	param+='&pendidikankota='+pendidikankota+'&keterangan='+pendidikanketerangan;
	param+='&karyawanid='+karyawanid;
		tujuan = 'sdm_slave_save_riwayat_pendidikan.php';
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
                          document.getElementById('containerpendidikan').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	 
}

function delPendidikan(karyawanid,kode)
{
   	param ='kode=' + kode + '&karyawanid=' + karyawanid+'&del=true';
   	tujuan = 'sdm_slave_save_riwayat_pendidikan.php';
   	if (confirm('Deleting..?')) {
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
                          document.getElementById('containerpendidikan').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}
//=================training
function simpanTraining()
{
	    jenistraining		=trim(document.getElementById('jenistraining').value);
		judultraining		=trim(document.getElementById('judultraining').value);
		penyelenggara		=trim(document.getElementById('penyelenggara').value);		
		trainingblnmulai	=document.getElementById('trainingblnmulai');
			trainingblnmulai=trim(trainingblnmulai.options[trainingblnmulai.selectedIndex].value);
		trainingthnmulai	=document.getElementById('trainingthnmulai');
			trainingthnmulai=trim(trainingthnmulai.options[trainingthnmulai.selectedIndex].value);
		trainingblnselesai	=document.getElementById('trainingblnselesai');
			trainingblnselesai=trim(trainingblnselesai.options[trainingblnselesai.selectedIndex].value);
		trainingthnselesai	=document.getElementById('trainingthnselesai');
			trainingthnselesai=trim(trainingthnselesai.options[trainingthnselesai.selectedIndex].value);
		sertifikat			=document.getElementById('sertifikat');
		    sertifikat		=trim(sertifikat.options[sertifikat.selectedIndex].value);
	    karyawanid			=document.getElementById('karyawanid').value;
 if(jenistraining=='' || judultraining=='' || penyelenggara=='')
 {
 	alert('Data incomplete');
 }
 else
 {
 	param='jenistraining='+jenistraining+'&judultraining='+judultraining;
	param+='&penyelenggara='+penyelenggara+'&trainingblnmulai='+trainingblnmulai;
	param+='&trainingthnmulai='+trainingthnmulai+'&trainingblnselesai='+trainingblnselesai;
	param+='&trainingthnselesai='+trainingthnselesai+'&sertifikat='+sertifikat;
	param+='&karyawanid='+karyawanid;
		tujuan = 'sdm_slave_save_riwayat_training.php';
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
                          document.getElementById('containertraining').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
}

function delTraining(karyawanid,nomor)
{
   	param ='nomor=' + nomor + '&karyawanid=' + karyawanid+'&del=true';
   	tujuan = 'sdm_slave_save_riwayat_training.php';
   	if (confirm('Deleting..?')) {
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
                          document.getElementById('containertraining').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}

//==================keluarga
function simpanKeluarga()
{
	keluarganama		=trim(document.getElementById('keluarganama').value);		
	keluargatmplahir	=document.getElementById('keluargatmplahir').value;
	keluargatgllahir	=document.getElementById('keluargatgllahir').value;
	keluargapekerjaan	=document.getElementById('keluargapekerjaan').value;
	keluargatelp		=document.getElementById('keluargatelp').value;
	keluargaemail		=document.getElementById('keluargaemail').value;
 	karyawanid			=document.getElementById('karyawanid').value;
 	method				=document.getElementById('keluargamethod').value;
	nomor				=document.getElementById('keluarganomor').value;
	
    keluargajk			=document.getElementById('keluargajk');
	    keluargajk=keluargajk.options[keluargajk.selectedIndex].value;
	hubungankeluarga	=document.getElementById('hubungankeluarga');
		hubungankeluarga=hubungankeluarga.options[hubungankeluarga.selectedIndex].value;
	keluargastatus		=document.getElementById('keluargastatus');
		keluargastatus	=keluargastatus.options[keluargastatus.selectedIndex].value;
	keluargapendidikan	=document.getElementById('keluargapendidikan');
		keluargapendidikan=keluargapendidikan.options[keluargapendidikan.selectedIndex].value;
	keluargatanggungan	=document.getElementById('keluargatanggungan');
		keluargatanggungan=keluargatanggungan.options[keluargatanggungan.selectedIndex].value;


 if(keluarganama=='')
 {
 	alert('Data incomplete');
 }
 else
 {
 	param='keluarganama='+keluarganama+'&keluargajk='+keluargajk;
	param+='&keluargatmplahir='+keluargatmplahir+'&keluargatgllahir='+keluargatgllahir;
	param+='&keluargapekerjaan='+keluargapekerjaan+'&keluargatelp='+keluargatelp;
	param+='&keluargaemail='+keluargaemail+'&karyawanid='+karyawanid;
	param+='&hubungankeluarga='+hubungankeluarga+'&keluargastatus='+keluargastatus;
	param+='&keluargapendidikan='+keluargapendidikan+'&keluargatanggungan='+keluargatanggungan;
	param+='&method='+method+'&nomor='+nomor;
	//alert(param);
		tujuan = 'sdm_slave_save_keluarga.php';
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
                          document.getElementById('containerkeluarga').innerHTML=con.responseText;
						  clearKeluarga();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}

function delKeluarga(karyawanid,nomor)
{
   	param ='nomor=' + nomor + '&karyawanid=' + karyawanid+'&del=true';
   	tujuan = 'sdm_slave_save_keluarga.php';
   	if (confirm('Deleting..?')) {
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
                          document.getElementById('containerkeluarga').innerHTML=con.responseText;
						  clearKeluarga();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}

function clearKeluarga()
{
	document.getElementById('keluarganama').value='';		
	document.getElementById('keluargatmplahir').value='';
	document.getElementById('keluargatgllahir').value='';
	document.getElementById('keluargapekerjaan').value='';
	document.getElementById('keluargatelp').value='';
	document.getElementById('keluargaemail').value='';
	document.getElementById('keluargamethod').value='insert';
 	
}

function fillField(nama,jeniskelamin,tempatlahir,tanggallahir,hubungankeluarga,status,levelpendidikan,pekerjaan,telp,email,tanggungan,nomor)
{
    document.getElementById('keluargamethod').value='update';
	document.getElementById('keluarganomor').value=nomor;
  	document.getElementById('keluarganama').value=nama;		
	document.getElementById('keluargatmplahir').value=tempatlahir;
	document.getElementById('keluargatgllahir').value=tanggallahir;
	document.getElementById('keluargapekerjaan').value=pekerjaan;
	document.getElementById('keluargatelp').value=telp;
	document.getElementById('keluargaemail').value=email;	

    jk=document.getElementById('keluargajk');
	for(x=0;x<jk.length;x++)
	{
		if(jk.options[x].value==jeniskelamin)
		{
			jk.options[x].selected=true;
		}
	}
    hk=document.getElementById('hubungankeluarga');
	for(x=0;x<hk.length;x++)
	{
		if(hk.options[x].value==hubungankeluarga)
		{
			hk.options[x].selected=true;
		}
	}	
	st=document.getElementById('keluargastatus');
	for(x=0;x<st.length;x++)
	{
		if(st.options[x].value==status)
		{
			st.options[x].selected=true;
		}
	}		
    lp=document.getElementById('keluargapendidikan');
	for(x=0;x<lp.length;x++)
	{
		if(lp.options[x].value==levelpendidikan)
		{
			lp.options[x].selected=true;
		}
	}		
    tgx=document.getElementById('keluargatanggungan');
	for(x=0;x<tgx.length;x++)
	{
		if(tgx.options[x].value==tanggungan)
		{
			tgx.options[x].selected=true;
		}
	}	
}
//=================tab photo
function cancelPhoto()
{
	winForm.document.getElementById('frmUpload').reset();
}
function simpanPhoto()
{
	winForm.document.getElementById('karyawanid').value=document.getElementById('karyawanid').value;
	winForm.document.getElementById('frmUpload').submit();
}

//==============tab alamat
function simpanAlamat()
{
	karyawanid			=document.getElementById('karyawanid').value;
	alamatalamat		=trim(document.getElementById('alamatalamat').value);
	alamatkota			=document.getElementById('alamatkota').value;
	alamatkodepos		=document.getElementById('alamatkodepos').value;
	alamattelepon		=document.getElementById('alamattelepon').value;
	alamatemplasement	=document.getElementById('alamatemplasement').value;
	alamatstatus		=document.getElementById('alamatstatus');
	alamatstatus=alamatstatus.options[alamatstatus.selectedIndex].value;
	alamatprovinsi		=document.getElementById('alamatprovinsi');
	alamatprovinsi=alamatprovinsi.options[alamatprovinsi.selectedIndex].value;
	if(alamatalamat=='')
	{
		alert('Data incomplete');
	}	
	else
	{
	   	param ='alamatalamat=' + alamatalamat + '&karyawanid=' + karyawanid;
	   	param +='&alamatkota=' + alamatkota + '&alamatkodepos=' + alamatkodepos;
		param +='&alamattelepon=' + alamattelepon + '&alamatemplasement=' + alamatemplasement;
		param +='&alamatstatus=' + alamatstatus + '&alamatprovinsi=' + alamatprovinsi;				
		tujuan = 'sdm_slave_save_alamat_karyawan.php';
	   	if (confirm('Saving..?')) {
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
                          document.getElementById('containeralamat').innerHTML=con.responseText;
						  clearAlamat();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }		
}

function clearAlamat()
{
	document.getElementById('alamatalamat').value='';		
	document.getElementById('alamatkota').value='';
	document.getElementById('alamatkodepos').value='';
	document.getElementById('alamattelepon').value='';
	document.getElementById('alamatemplasement').value='';
 	
}

function delAlamat(karyawanid,nomor)
{
   	param ='nomor=' + nomor + '&karyawanid=' + karyawanid+'&del=true';
   	tujuan = 'sdm_slave_save_alamat_karyawan.php';
   	if (confirm('Deleting..?')) {
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
                          document.getElementById('containeralamat').innerHTML=con.responseText;
						  clearAlamat();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}
//=====================list click
function displayList()
{
	document.getElementById('frminput').style.display='none';
	document.getElementById('searchplace').style.display='';
	loadEmployeeList();
}
function displayFormInput()
{
	document.getElementById('frminput').style.display='';
	document.getElementById('searchplace').style.display='none';	
}

function loadEmployeeList()
{
   	param='';
	tujuan = 'sdm_slave_load_employee_list.php';
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
                          document.getElementById('searchplaceresult').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }		
}
function prefDatakaryawan(btn,curval)
{
    cariKaryawan(curval); 
	if(curval==0)
	{
	}
	else
 	  btn.value=parseInt(curval)-1;
   document.getElementById('nextbtn').value=parseInt(btn.value)+2;	  
	
}
function nextDatakaryawan(btn,curval)
{
      cariKaryawan(curval);
 	  btn.value=parseInt(curval)+1;
	  document.getElementById('prefbtn').value=parseInt(btn.value)-2;	
}

function cariKaryawan(page)
{
	txtsearch =trim(document.getElementById('txtsearch').value);	
	schorg	  =document.getElementById('schorg');
	schtipe	  =document.getElementById('schtipe');
	schorg=schorg.options[schorg.selectedIndex].value;
	schtipe=schtipe.options[schtipe.selectedIndex].value;

		param='txtsearch='+txtsearch;
		param+='&orgsearch='+schorg;
		param+='&tipesearch='+schtipe;
		param+='&page='+page;

	tujuan = 'sdm_slave_load_employee_list.php';
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
                          document.getElementById('searchplaceresult').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}
