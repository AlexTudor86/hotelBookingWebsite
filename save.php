<?php

require('config.php');

session_start();

if(isset($_POST['save'])) {
	$f = 'to_be_saved_and_then_deleted.html';
	// daca s-a apasat butonul, cream un fisier in care scriem continut apeland functia rezervare cu ultimul argument setat true
	touch($f);
	$rate = $_SESSION['nr_rate']??0;
	file_put_contents($f, rezervare($_SESSION['camera'], $_SESSION['sosire'], $_SESSION['plecare'], $rate, true));
	// salvare in fisier
	if (!file_exists(DB_REZ)) {
		file_put_contents(DB_REZ, 'Nume,Tip camera,Data sosire,Data plecare,Nr nopti,Pret total'.PHP_EOL);
	};
	file_put_contents(DB_REZ, $_SESSION['nume_complet'].','.$_SESSION['camera'].','.$_SESSION['sosire'].','.$_SESSION['plecare'].','.$_SESSION['nopti'].','.$_SESSION['total'].PHP_EOL, FILE_APPEND);
	// trimitem header inainte de continut
	header('Content-type: text/html');
	header('Content-disposition: attachment, filename="detalii.html"');
	readfile($f);
	// dupa ce am trimis continut stergem fisierul temporar.
	unlink($f);
}