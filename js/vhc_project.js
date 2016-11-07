/**
 * @author repindra.ginting
 */
 
 
 
 
////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////// JS IND ////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////



////////excel material

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
    width='600';
    height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2(title,content,width,height,ev); 	
}
function excelMaterial(ev,kode)
{
        param='method=excelMaterial'+'&kode='+kode;
        //alert(param);
        tujuan='vhc_slave_project.php';
        judul='Material '+kode;		
        printFile(param,tujuan,judul,ev)	
}



//excel timeframe
function timeFrame(ev,kode)
{
        param='method=timeFrame'+'&kode='+kode;
        //alert(param);
        tujuan='vhc_slave_project.php';
        judul='Time Frame '+kode;		
        printFile(param,tujuan,judul,ev)	
}


/////////////////////



/////posting


function postIni(kode)
{
	param='method=postIni'+'&kode='+kode;
	//alert(param);
	tujuan='vhc_slave_project.php';
	if(confirm("Anda yakin ingin menutup project ini??"))
	{
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
					else 
					{
						// document.getElementById('contain').innerHTML=con.responseText;
                                                alert('Posting berhasil.\nHarap segera membuat BP3 Penutupan Project untuk dilaporkan ke Bag.Akunting untuk penambahan aset.');
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


/////////

 
////////////////////
//PERSETUJUAN
//////////////////// 

function selesaiFormApv()
{
	closeDialog();
}



/*function agree()
{
	width='300';
	height='10';
	//nopp=document.getElementById('nopp_'+id).value;
	content="<div id=containerd align=center></div>";
	ev='event';
	title="Persetujuan Atau Penolakan Form";
	showDialog1(title,content,width,height,ev);
	//get_data_pp();	
}
*/


function apv(kode,title,ev)
{
	
	//style=\"height:250px;width:400px;overflow:scroll;\"
	title='Project : '+kode;
	width='290';
	height='10';
	content= "<div id=formApv ></div>";
	showDialog1(title,content,width,height,ev);	
	getFormApv(kode);
}


function getFormApv(kode)
{
	param='method=getFormApv'+'&kode='+kode;
	//alert(param);
	tujuan = 'vhc_slave_project.php';
	post_response_text(tujuan, param, respog);		
	function respog(){
			if (con.readyState == 4) {
					if (con.status == 200) {
							busy_off();
							if (!isSaveResponse(con.responseText)) {
									alert('ERROR TRANSACTION,\n' + con.responseText);
							}
							else {
								//alert(con.responseText);
									document.getElementById('formApv').innerHTML=con.responseText;
							}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
			}
	} 
		
}


function saveFormApv(kode)
{
	
	//alert('MASUK');
	
 	//a=document.getElementById('total_'+id);
	

	

	persetujuan1=document.getElementById('persetujuan1').value;
	persetujuan2=document.getElementById('persetujuan2').value;
	persetujuan3=document.getElementById('persetujuan3').value;
	persetujuan4=document.getElementById('persetujuan4').value;
	persetujuan5=document.getElementById('persetujuan5').value;
	persetujuan6=document.getElementById('persetujuan6').value;
	persetujuan7=document.getElementById('persetujuan7').value; 
	
/*	 p1=parseFloat(document.getElementById('persetujuan1'));
	 p2=parseFloat(persetujuan2);
	 p3=parseFloat(persetujuan3);
	 p4=parseFloat(persetujuan4);
	 p5=parseFloat(persetujuan5);
	 p6=parseFloat(persetujuan6);
	 p7=parseFloat(persetujuan7);
	 
	 alert(p1);return;*/
	
	
	if(persetujuan1=='' || persetujuan2=='')
	{
		alert('Approval 1 or 2 was empty');return;
	}
	else
	{
	}
	
	method=document.getElementById('method').value;

	//param='kodeproject='+kodeproject+'&kodekegiatan='+kodekegiatan+'&kodeBarangForm='+kodeBarangForm+'&jumlahBarangForm='+jumlahBarangForm+'&method='+saveFormBarang;
	param='method=saveFormApv'+'&persetujuan1='+persetujuan1+'&persetujuan2='+persetujuan2+'&persetujuan3='+persetujuan3+'&persetujuan4='+persetujuan4;
	param+='&persetujuan5='+persetujuan5+'&persetujuan6='+persetujuan6+'&persetujuan7='+persetujuan7+'&kode='+kode;
	
	//alert(param);
	tujuan = 'vhc_slave_project.php';
	
	//alert(tujuan);
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
							
							alert('Aproval sent');
							closeDialog();
							//alert(con.responseText
							//cancelFormBarang(kegiatan,kodeproject);
							
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
	
}


////////////////////
//TUTUP PERSETUJUAN
//////////////////// 
 
 
 
////////////////////
//BUKA MATERIAL
////////////////////

function saveFormBarang(kegiatan,kodeproject)
{

	//alert('MASUK');
	kodeproject=document.getElementById('kodeproject').value;
	kodekegiatan=document.getElementById('kodekegiatan').value;
	kodeBarangForm=document.getElementById('kodeBarangForm').value;
	jumlahBarangForm=document.getElementById('jumlahBarangForm').value;
	method=document.getElementById('method').value;

	//param='kodeproject='+kodeproject+'&kodekegiatan='+kodekegiatan+'&kodeBarangForm='+kodeBarangForm+'&jumlahBarangForm='+jumlahBarangForm+'&method='+saveFormBarang;
	param='method=saveFormBarang'+'&kodeproject='+kodeproject+'&kodekegiatan='+kodekegiatan+'&kodeBarangForm='+kodeBarangForm+'&jumlahBarangForm='+jumlahBarangForm;
	
	tujuan = 'vhc_slave_project.php';
	
	//alert(tujuan);
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
							//alert(con.responseText
							cancelFormBarang(kegiatan,kodeproject);
							
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
	
}
 

 
function tambahBarang(kegiatan,kodeproject,title,ev)
{
                  content= "<div id=formBarang style=\"height:450px;width:800px;overflow:scroll;\"></div>";
				 
				   //content+="<div id=formCariBarang></div>";
                
                 title='Project : '+kodeproject;
			
                   width='800';
                   height='450';
                   showDialog1(title,content,width,height,ev);	
				   getListBarang(kegiatan,kodeproject);
}






function moveDataBarang(kodebarang,namabarang,satuanbarang)
{
	document.getElementById('kodeBarangForm').value=kodebarang;
	document.getElementById('namaBarangForm').value=namabarang;
	document.getElementById('satuanBarangForm').value=satuanbarang;
	
	//document.getElementById('').innerHTML=con.responseText;
	document.getElementById('listCariBarang').style.display='none';
	
}



function cariListBarang(kegiatan,kodeproject)
{
	//alert('MASUK');
	namaBarangCari=document.getElementById('namaBarangCari').value;
	//alert(kegiatan);
	param='method=getListBarang'+'&namaBarangCari='+namaBarangCari+'&kegiatan='+kegiatan+'&kodeproject='+kodeproject;
	//alert(param);
	tujuan = 'vhc_slave_project.php';
	post_response_text(tujuan, param, respog);		
	function respog(){
			if (con.readyState == 4) {
					if (con.status == 200) {
							busy_off();
							if (!isSaveResponse(con.responseText)) {
									alert('ERROR TRANSACTION,\n' + con.responseText);
							}
							else {
								//alert(con.responseText);
									document.getElementById('formBarang').innerHTML=con.responseText;
							}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
			}
	} 
		
}



function delMaterial(kodeproject,kegiatan,kodebarang)
{
	param='method=deleteMaterial'+'&kodeproject='+kodeproject+'&kegiatan='+kegiatan+'&kodebarang='+kodebarang;
	//alert(param);
	tujuan='vhc_slave_project.php';
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
					else 
					{
						cancelFormBarang(kegiatan,kodeproject);
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}


function cancelFormBarang(kegiatan,kodeproject)
{
	
	//document.getElementById('kodekegiatan').value=kodek
	//kodeproject
	
	
	document.getElementById('kodeBarangForm').value='';
	document.getElementById('namaBarangForm').value='';
	document.getElementById('jumlahBarangForm').value='';
	getListBarang(kegiatan,kodeproject);
	//document.getElementById('listCariBarang').style.display='none';
}


function getListBarang(kegiatan,kodeproject)
{
	param='method=getListBarang'+'&kegiatan='+kegiatan+'&kodeproject='+kodeproject;
	//alert(param);
	tujuan = 'vhc_slave_project.php';
	post_response_text(tujuan, param, respog);		
	function respog(){
			if (con.readyState == 4) {
					if (con.status == 200) {
							busy_off();
							if (!isSaveResponse(con.responseText)) {
									alert('ERROR TRANSACTION,\n' + con.responseText);
							}
							else {
								//alert(con.responseText);
									document.getElementById('formBarang').innerHTML=con.responseText;
							}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
			}
	} 
		
}


////////////////////
//TUTUP MATERIAL
////////////////////



////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////TUTUP JS IND////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////










 
 
function simpan()
{
	notran=document.getElementById('notran').value;
    unit=document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
    aset=document.getElementById('aset').options[document.getElementById('aset').selectedIndex].value;
    jenis=document.getElementById('jenis').options[document.getElementById('jenis').selectedIndex].value;
    nama=trim(document.getElementById('nama').value);
	kelompok=document.getElementById('kelompok').options[document.getElementById('kelompok').selectedIndex].value;
	 nilai=trim(document.getElementById('nilai').value);
    tanggalmulai=trim(document.getElementById('tanggalmulai').value);
    tanggalselesai=trim(document.getElementById('tanggalselesai').value);
    method=document.getElementById('method').value;	
    kode=document.getElementById('kode').value;	
    
	if(notran=='')            { alert('Please fill No. transaction'); exit(); }
    if(unit=='')            { alert('Please fill UNIT'); exit(); }
    if(aset=='')            { alert('Please fill ASET'); exit(); }
    if(nama=='')            { alert('Please fill NAMA'); exit(); }
    if(tanggalmulai=='')    { alert('Please fill TANGGAL MULAI'); exit(); }
    if(tanggalselesai=='')  { alert('Please fill TANGGAL SELESAI'); exit(); }
    
    param='unit='+unit+'&aset='+aset+'&jenis='+jenis+'&notran='+notran;
    param+='&nama='+nama+'&tanggalmulai='+tanggalmulai+'&tanggalselesai='+tanggalselesai+'&kode='+kode;
	param+='&kelompok='+kelompok+'&nilai='+nilai;
    param+='&method='+method;
    if(confirm('Save/Simpan?'))
    {
        tujuan = 'vhc_slave_project.php';
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
                    alert('Done.');
                    //document.getElementById('container').innerHTML=con.responseText;
                    loadData();
                    batal();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }	
}

function batal()
{
	
    var d = new Date();
    var curr_date = d.getDate();
    var curr_month = d.getMonth() + 1; //Months are zero based
    var curr_year = d.getFullYear();
	if(curr_date.length=1)
	{
		curr_date='0'+curr_date;
	}
	if(curr_month.length=1)
	{
		curr_month='0'+curr_month;
	}
    d1=curr_date + "-" + curr_month + "-" + curr_year;
    document.getElementById('unit').value='';
    document.getElementById('aset').value='';
    document.getElementById('jenis').value='AK';
    document.getElementById('nama').value='';
    document.getElementById('tanggalmulai').value=d1;
    document.getElementById('tanggalselesai').value=d1;
    document.getElementById('method').value='insert';
    document.getElementById('kode').value='';
    document.getElementById('kelompok').value='HOUSING';
	document.getElementById('kelompok').disabled=false;
    document.getElementById('unit').disabled=false;
    document.getElementById('aset').disabled=false;
    document.getElementById('jenis').disabled=false;
	 document.getElementById('nilai').value='';
	document.getElementById('notran').value='';
	document.getElementById('nilai').disabled=false;
	document.getElementById('notran').disabled=false;
		
	document.getElementById('nama').disabled=false;
	document.getElementById('tanggalmulai').disabled=false;
	document.getElementById('tanggalselesai').disabled=false; 
	
}

function fillField(unit,aset,jenis,nama,tanggalmulai,tanggalselesai,method,kode,kelompok,nilai,notran)
{
    document.getElementById('unit').value=unit;
    document.getElementById('aset').value=aset;
    document.getElementById('jenis').value=jenis;
    document.getElementById('nama').value=nama;
    document.getElementById('tanggalmulai').value=tanggalmulai;
    document.getElementById('tanggalselesai').value=tanggalselesai;
    document.getElementById('method').value=method;
    document.getElementById('kode').value=kode;
	document.getElementById('kelompok').value=kelompok;
	document.getElementById('nilai').value=nilai;
	document.getElementById('notran').value=notran;
    
    document.getElementById('unit').disabled=true;
    document.getElementById('aset').disabled=true;
    document.getElementById('jenis').disabled=true;
    

}
function detailForm(unit,aset,jenis,nama,tanggalmulai,tanggalselesai,method,kode,kelompok,nilai,notran)
{
    document.getElementById('unit').value=unit;
    document.getElementById('aset').value=aset;
    document.getElementById('jenis').value=jenis;
    document.getElementById('nama').value=nama;
    document.getElementById('tanggalmulai').value=tanggalmulai;
    document.getElementById('tanggalselesai').value=tanggalselesai;
    document.getElementById('method').value='insertDetail';
    document.getElementById('kode').value=kode;
    document.getElementById('kdProj').value=kode;
    document.getElementById('unit').disabled=true;
    document.getElementById('aset').disabled=true;
    document.getElementById('jenis').disabled=true;
    document.getElementById('tanggalselesai').disabled=true;
    document.getElementById('tanggalmulai').disabled=true;
    document.getElementById('nama').disabled=true;
	document.getElementById('kelompok').value=kelompok;
	document.getElementById('nilai').value=nilai;	
	document.getElementById('kelompok').disabled=true;
	document.getElementById('nilai').disabled=true;
	document.getElementById('notran').value=notran;
	document.getElementById('notran').disabled=true;
	
	document.getElementById('saveH').disabled=true;
	document.getElementById('cancelH').disabled=true;

	
    param='method='+method+'&kode='+kode;
    tujuan='vhc_slave_project.php';
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
                   document.getElementById('detailInput').style.display='block';
                   document.getElementById('dataDisimpan').style.display='none';
                   document.getElementById('printDat').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}
function doneSlsi()
{
    //waktu=date('d-m-Y');
    document.getElementById('unit').value='';
    document.getElementById('aset').value='';
    document.getElementById('jenis').value='';
    document.getElementById('nama').value='';
    document.getElementById('method').value='insert';
    document.getElementById('kode').value='';
    document.getElementById('kdProj').value='';
    document.getElementById('unit').disabled=false;
    document.getElementById('aset').disabled=false;
    document.getElementById('jenis').disabled=false;
    document.getElementById('tanggalselesai').disabled=false;
    document.getElementById('tanggalmulai').disabled=false;
    document.getElementById('nama').disabled=false;
    document.getElementById('detailInput').style.display='none';
    document.getElementById('dataDisimpan').style.display='block';
    document.getElementById('printDat').innerHTML='';
	
	document.getElementById('saveH').disabled=false;
	document.getElementById('cancelH').disabled=false;
	
	document.getElementById('notran').disabled=false;
	document.getElementById('notran').value='';
	document.getElementById('nilai').value='';
	document.getElementById('nilai').disabled=false;
	
	
    //document.getElementById('tanggalmulai').value=waktu;
    //document.getElementById('tanggalselesai').value=waktu;
}
function editDet(tanggalmulai,tanggalselesai,method,kode,knci,nmkeg)
{
    document.getElementById('kdProj').value=kode;
    document.getElementById('namaKeg').value=nmkeg;
    document.getElementById('tanggalMulai').value=tanggalmulai;
    document.getElementById('tanggalSampai').value=tanggalselesai;
    document.getElementById('kegId').value=knci;
    document.getElementById('method').value=method;
}
function hapus(kode)
{
    document.getElementById('method').value='hapus';
    param='kode='+kode+'&method=delete';
    if(confirm('Delete/Hapus '+kode+'?'))
    {
        tujuan='vhc_slave_project.php';
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
                    alert('Done.');
                    //document.getElementById('container').innerHTML=con.responseText;
                    loadData();
                    batal();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }		
}
function loadData()
{
    param='method=loadData';
    tujuan='vhc_slave_project.php';
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
function addDetail()
{
    kd=document.getElementById('kdProj').value;
    nmKeg=document.getElementById('namaKeg').value;
    tglMul=document.getElementById('tanggalMulai').value;
    tglSmp=document.getElementById('tanggalSampai').value;
    knci=document.getElementById('kegId').value;
    met=document.getElementById('method').value;
    param='&kode='+kd+'&nmKeg='+nmKeg+'&tglMul='+tglMul+'&tglSmp='+tglSmp;
    param+='&index='+knci+'&method='+met;
    tujuan='vhc_slave_project.php';
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
                   // document.getElementById('container').innerHTML=con.responseText;
                   loadDetail();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function loadDetail()
{
    kd=document.getElementById('kdProj').value;
    param='method=detail'+'&kode='+kd;
    tujuan='vhc_slave_project.php';
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
                    document.getElementById('printDat').innerHTML=con.responseText;
                    document.getElementById('method').value='insertDetail';
                    document.getElementById('namaKeg').value='';
                    document.getElementById('tanggalMulai').value=date('d-m-Y');
                    document.getElementById('tanggalSampai').value=date('d-m-Y');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function hapusData(kode)
{
    param='index='+kode+'&method=hpsDetail';
    if(confirm('Delete/Hapus Detail ?'))
    {
        tujuan='vhc_slave_project.php';
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
                   loadDetail();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}