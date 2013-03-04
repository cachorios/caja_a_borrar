<?php

$fp = fsockopen ("192.168.1.5", 5331, $errno, $errstr, 30);
if (!$fp) {
	echo "$errstr ($errno)";
} else {
	$e = chr(27);
	//fputs ($fp, chr(149) );
	echo "Inicio";
	fputs ($fp, chr(hexdec ('H1B')) . "a"  . chr(1) );
	fputs ($fp, chr(hexdec ('H1B')) . "c0" . chr(2) );
	fputs ($fp, chr(hexdec ('H1B')) . "U"  . chr(0) );
	fputs ($fp, chr(hexdec ('H1B')) . "!"  . chr(2) ); //tamaño
	fputs ($fp, chr(hexdec ('H1B')) . "M"  . chr(2) );
	
	fputs ($fp, "PRUEBA de IMPRESION"  . chr(hexdec ('HA')) );
	
	fputs ($fp, chr(hexdec ('H1B')) . "!"  . chr(1) ); //Tamaño normal
	fputs ($fp, chr(hexdec ('H1B')) . "M"  . chr(1) );
	
	
	fputs ($fp, chr(hexdec ('H1B')) . "c0" . chr(3) ); //Enviar al journal
	fputs ($fp, chr(hexdec ('H1B')) . "z" . chr(1) ); 
	fputs ($fp, chr(hexdec ('H1B')) . "!" . chr(1) ); 
	
	fputs ($fp, "TIKET: 121545 "  . chr(hexdec ('HA')) );
	fputs ($fp, "****************************************"  . chr(hexdec ('HA')) );
	
	fputs ($fp, chr(hexdec ('H1B')) . "a" . chr(0) ); 
	
	
	/*
	for($i=1; $i<10; $i++){
		$string = 'string text here '.$i."\n<br>";
		echo $string;
		fputs ($fp, $string );
	}		*/
fclose ($fp);
}

?>
