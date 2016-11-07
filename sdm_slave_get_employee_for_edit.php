<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');



//exit("Error:AS");
$karyawanid=$_POST['karyawanid'];

$str="select * from ".$dbname.".datakaryawan where karyawanid=".$karyawanid ." limit 1";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{

	//return XML format
	//The receiver(js) will error when content is blank
	// so check it first and if it blank replace with *
	echo"<?xml version='1.0' ?>
	     <karyawan>
			 <karyawanid>".($bar->karyawanid!=''?$bar->karyawanid:"*")."</karyawanid>
			 <nik>".($bar->nik!=""?$bar->nik:"*")."</nik>
			 <namakaryawan>".($bar->namakaryawan!=""?$bar->namakaryawan:"*")."</namakaryawan>
			 <tempatlahir>".($bar->tempatlahir!=""?$bar->tempatlahir:"*")."</tempatlahir>
			 <tanggallahir>".tanggalnormal($bar->tanggallahir)."</tanggallahir>
		     <warganegara>".($bar->warganegara!=""?$bar->warganegara:"*")."</warganegara>
		     <jeniskelamin>".($bar->jeniskelamin!=""?$bar->jeniskelamin:"*")."</jeniskelamin>
			 <statusperkawinan>".($bar->statusperkawinan!=""?$bar->statusperkawinan:"*")."</statusperkawinan>
			 <tanggalmenikah>".tanggalnormal($bar->tanggalmenikah)."</tanggalmenikah>
			 <agama>".($bar->agama!=""?$bar->agama:"*")."</agama>
			 <golongandarah>".($bar->golongandarah!=""?$bar->golongandarah:"*")."</golongandarah>
			 <levelpendidikan>".($bar->levelpendidikan!=""?$bar->levelpendidikan:"*")."</levelpendidikan>
			 <alamataktif>".($bar->alamataktif!=""?$bar->alamataktif:"*")."</alamataktif>
			 <provinsi>".($bar->provinsi!=""?$bar->provinsi:"*")."</provinsi>
			 <kota>".($bar->kota!=""?$bar->kota:"*")."</kota>
			 <kodepos>".($bar->kodepos!=""?$bar->kodepos:"*")."</kodepos>
			 <noteleponrumah>".($bar->noteleponrumah!=""?$bar->noteleponrumah:"*")."</noteleponrumah>
			 <nohp>".($bar->nohp!=""?$bar->nohp:"*")."</nohp>
			 <norekeningbank>".($bar->norekeningbank!=""?$bar->norekeningbank:"*")."</norekeningbank>
			 <namabank>".($bar->namabank!=""?$bar->namabank:"*")."</namabank>
			 <sistemgaji>".($bar->sistemgaji!=""?$bar->sistemgaji:"*")."</sistemgaji>
			 <nopaspor>".($bar->nopaspor!=""?$bar->nopaspor:"*")."</nopaspor>
			 <noktp>".($bar->noktp!=""?$bar->noktp:"*")."</noktp>
			 <notelepondarurat>".($bar->notelepondarurat!=""?$bar->notelepondarurat:"*")."</notelepondarurat>
		     <tanggalmasuk>".tanggalnormal($bar->tanggalmasuk)."</tanggalmasuk>
		     <tanggalkeluar>".tanggalnormal($bar->tanggalkeluar)."</tanggalkeluar>
			 <tipekaryawan>".($bar->tipekaryawan!=""?$bar->tipekaryawan:"*")."</tipekaryawan>
			 <jumlahanak>".($bar->jumlahanak!=""?$bar->jumlahanak:"*")."</jumlahanak>	
			 <jumlahtanggungan>".($bar->jumlahtanggungan!=""?$bar->jumlahtanggungan:"*")."</jumlahtanggungan>			 
		     <statuspajak>".($bar->statuspajak!=""?$bar->statuspajak:"*")."</statuspajak>
			 <npwp>".($bar->npwp!=""?$bar->npwp:"*")."</npwp>
			 <lokasipenerimaan>".($bar->lokasipenerimaan!=""?$bar->lokasipenerimaan:"*")."</lokasipenerimaan>
			 <kodeorganisasi>".($bar->kodeorganisasi!=""?$bar->kodeorganisasi:"*")."</kodeorganisasi>
		     <bagian>".($bar->bagian!=""?$bar->bagian:"*")."</bagian>
			 <kodejabatan>".($bar->kodejabatan!=""?$bar->kodejabatan:"*")."</kodejabatan>
			 <kodegolongan>".($bar->kodegolongan!=""?$bar->kodegolongan:"*")."</kodegolongan>
			 <lokasitugas>".($bar->lokasitugas!=""?$bar->lokasitugas:"*")."</lokasitugas>
			  <photo>".($bar->photo!=""?$bar->photo:"*")."</photo>
			 <email>".($bar->email!=""?$bar->email:"*")."</email> 
			 <alokasi>".($bar->alokasi!=""?$bar->alokasi:"*")."</alokasi>
			 <subbagian>".($bar->subbagian!=""?$bar->subbagian:"*")."</subbagian>
			 <jms>".($bar->jms!=""?$bar->jms:"*")."</jms>
			 <catu>".($bar->kodecatu!=""?$bar->kodecatu:"0")."</catu>    
			 <dptPremi>".($bar->statpremi)."</dptPremi>
			 
			  <kecamatan>".($bar->kecamatan!=""?$bar->kecamatan:"*")."</kecamatan>
			  <desa>".($bar->desa!=""?$bar->desa:"*")."</desa>
			  <pangkat>".($bar->pangkat!=""?$bar->pangkat:"*")."</pangkat>
                          <tanggalpengangkatan>".($bar->tanggalpengangkatan!=""?$bar->tanggalpengangkatan:"*")."</tanggalpengangkatan>

		 </karyawan>";	
}
?>