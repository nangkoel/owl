// JavaScript Document
function getData()
{
	param = "proses=showData";
 	tujuan="show_data.php";
	pesanerror='';
	pesanjaringan='';
		function respon(){
				if (con.readyState == 4) 
			{			
						if (con.status == 200) {
							if (!isSaveResponse(con.responseText)) {
							   pesanerror=con.responseText;
							 ar=pesanerror.split("###");	
							 //alert(ar);				   
							 document.getElementById('content').innerHTML=ar[0];	
							 document.getElementById('isiData').innerHTML=ar[1];
							 jam_jaman();//coba lagi.. jika error
							}
							else {
								//berhasil 
									pesanerror=con.responseText;
									ar=pesanerror.split("###");

									 document.getElementById('content').innerHTML=ar[0];	
									 document.getElementById('isiData').innerHTML=ar[1];
									if(ar[1]=='null')
									{
											jam_jaman();
									}
									else
									{
											uploadData();
									}
							}
						} else {
							pesanjaringan=error_catch(con.status);
							document.getElementById('content').innerHTML=pesanjaringan;	
							jam_jaman();
							}
			}
    }
    
post_response_text(tujuan, param, respon);
}
var waktu;
function jam_jaman()
{
	clearTimeout(waktu);
	waktu=setInterval("getData()",180000);
        try{
            document.getElementById('pesanku').innerHTML='Reloading...';  
        }
        catch(e)
        {}
}
function uploadData()
{
	idTimbangan=document.getElementById('notiket').innerHTML;
	tglData=document.getElementById('tglData').innerHTML;
	custData=document.getElementById('custData').innerHTML;
	kbn=document.getElementById('kbn').innerHTML;
	pabrik=document.getElementById('pabrik').innerHTML;
	kdBrg=document.getElementById('kdBrg').innerHTML;
	spbno=document.getElementById('spbno').innerHTML;
	sibno=document.getElementById('sibno').innerHTML;
	thnTnm=document.getElementById('thnTnm').innerHTML;
	thnTnm2=document.getElementById('thnTnm2').innerHTML;
	thnTnm3=document.getElementById('thnTnm3').innerHTML;	
	jmlhjjg=document.getElementById('jmlhjjg').innerHTML;	
	jmlhjjg2=document.getElementById('jmlhjjg2').innerHTML;	
	jmlhjjg3=document.getElementById('jmlhjjg3').innerHTML;	
	brndln=document.getElementById('brndln').innerHTML;	
	nodo=document.getElementById('nodo').innerHTML;	
	kdVhc=document.getElementById('kdVhc').innerHTML;	
	spir=document.getElementById('spir').innerHTML;	
	jmMasuk=document.getElementById('jmMasuk').innerHTML;	
	jmKeluar=document.getElementById('jmKeluar').innerHTML;	
	brtBrsih=document.getElementById('brtBrsih').innerHTML;	
	brtMsk=document.getElementById('brtMsk').innerHTML;	
	brtOut=document.getElementById('brtOut').innerHTML;	
	usrNm=document.getElementById('usrNm').innerHTML;	
	kntrkNo=document.getElementById('kntrkNo').innerHTML;
        potsortasi=document.getElementById('potsortasi').innerHTML;
        try{
            document.getElementById('pesanku').innerHTML='Update remote data...';  
        }
        catch(err)
        {}
	sloc=document.getElementById('sloc').innerHTML;
	penerima=document.getElementById('penerima').innerHTML;
    buahbusuk=document.getElementById('buahbusuk').innerHTML;		
	buahkrgmatang=document.getElementById('buahkrgmatang').innerHTML;
	buahsakit=document.getElementById('buahsakit').innerHTML;	
	janjangkosong=document.getElementById('janjangkosong').innerHTML;	
	lwtmatang=document.getElementById('lwtmatang').innerHTML;
	mentah=document.getElementById('mentah').innerHTML;	
	tkpanjang=document.getElementById('tkpanjang').innerHTML;
	tigakilo=document.getElementById('tigakilo').innerHTML;
	
	param='proses=uploadData';
	param+='&idTimbangan='+idTimbangan+'&tglData='+tglData+'&custData='+custData+'&kbn='+kbn+'&pabrik='+pabrik;
	param+='&kdBrg='+kdBrg+'&spbno='+spbno+'&sibno='+sibno+'&thnTnm='+thnTnm+'&thnTnm2='+thnTnm2+'&thnTnm3='+thnTnm3;
	param+='&jmlhjjg='+jmlhjjg+'&jmlhjjg2='+jmlhjjg2+'&jmlhjjg3='+jmlhjjg3+'&brndln='+brndln+'&kdVhc='+kdVhc+'&spir='+spir;
	param+='&jmMasuk='+jmMasuk+'&jmKeluar='+jmKeluar+'&brtBrsih='+brtBrsih+'&brtMsk='+brtMsk+'&brtOut='+brtOut+'&usrNm='+usrNm;
	param+='&kntrkNo='+kntrkNo+'&potsortasi='+potsortasi+'&nodo='+nodo;
	param+='&buahbusuk='+buahbusuk+'&buahkrgmatang='+buahkrgmatang+'&buahsakit='+buahsakit+'&janjangkosong='+janjangkosong+'&lwtmatang='+lwtmatang;
	param+='&mentah='+mentah+'&tkpanjang='+tkpanjang+'&tigakilo='+tigakilo+'&sloc='+sloc+'&penerima='+penerima;
	
	tujuan = 'show_data.php';
	pesanerror='';
	pesanjaringan='';
	 function respon1(){
            if (con.readyState == 4) 
              {

                if (con.status == 200) {
                    busy_off();
                   pesanerror=con.responseText;
                } else {
                    busy_off();
                    pesanjaringan=error_catch(con.status);
                    document.getElementById('content').innerHTML=pesanjaringan;
                }
                if(pesanerror!='2')
                {
                     uploadData();
                }
                else
                {
                        getData();
                }
            }
    }
    post_response_text(tujuan, param, respon1);    
}