<?php

// Fisier configurare site

// !! Pentru drepturi de admin, dupa crearea unui cont, se modifica manual 'db_usr.txt', inlocuind 'no' cu 'yes'
// Admin are acces la pagina cu toate rezervarile (sumar)


// fisier in care scriem user pass etc
const DB_USR = 'db_usr.txt';
const DB_CNT = 'db_contact.txt';
const DB_REZ = 'rezervari.txt';
$camere = ['single' => 100, 'double' => 165, 'apartament' => 250];

//header pt useri neauth
const HEAD = <<< HEAD
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width initial-scale=1 maximum-scale=1">
		<title>Pensiunea Vietii</title>
		<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@500&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Lato&family=Roboto+Slab:wght@500&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Niconne&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Vollkorn:ital@0;1&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="main.css">
	</head>
	
	<body>
		<div id="header">
			<div id="logo">
				<img src="images/cab-logo.jpg" alt="Cabana de munte">
			</div>
			<div id="menu">
				<ul>
					<li><a href="index.php">Home</a></li>
					<li><a href="creare_cont.php">Creare Cont</a></li>
					<li><a href="contact.php">Contact</a></li>					
				</ul>
				<h1>Pensiunea Vietii</h1>
			</div>
		</div>
HEAD;

// header pt user auth ---> se putea face o functie care sa appenduiasca un script JS : createElement + appendChild (ex: functia sumar())
const HEAD_AUTH = <<< HEAD
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width initial-scale=1 maximum-scale=1">
		<title>Pensiunea Vietii</title>
		<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@500&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Lato&family=Roboto+Slab:wght@500&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Niconne&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Vollkorn:ital@0;1&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="main.css">
	</head>
	
	<body>
		<div id="header">
			<div id="logo">
				<img src="images/cab-logo.jpg" alt="Cabana de munte">
			</div>
			<div id="menu">
				<ul>
					<li><a href="index.php">Home</a></li>
					<li><a href="rezervari.php">Rezervari</a></li>
					<li><a href="contact.php">Contact</a></li>					
				</ul>
				<h1>Pensiunea Vietii</h1>
			</div>
		</div>
HEAD;

// footer - modificam cu functia login (javascript) pt user auth
const FOOT = <<<FOOT
		<div id="footer">
			<div></div>
			<p>Author : Tudor Alexandru<span>
			Email : <a href="mailto:tudorik05@yahoo.com">tudorik05@yahoo.com</a></span></p>
		</div>

	</body>
</html>
FOOT;

/* FUNCTII JAVASCRIPT care vor fi appenduite inainte de </body></html> , dupa necesitate */

// activam clasa eroare (highlight cu rosu) precum si mesaj
function error($input, $mesaj='') {
	$script = <<<SCR
	<script>
	$input.classList.add('eroare');
	$input.parentElement.querySelector('small').innerText = '$mesaj';
	</script>
SCR;
	return $script;
}

// salutam user-ul in footer, precum si includem link de logout (testam la inceput pe 
// fiecare pagina daca $_GET['logout'] exista si daca este egal cu 1 
// in functie de asta vom sterge sau nu (datele) sesiunea si afisam in consecinta
function login($nume) {
	$script = <<<SCR
	<script type="text/javascript">
	const foot = document.getElementById('footer');
	const p = foot.querySelector('p');
	p.innerHTML = 'Salut, $nume !<span><a href=$_SERVER[PHP_SELF]?logout=1>Logout</a></span>';	
	</script>
SCR;
	return $script;
}

// functie ajutatoare pentru functia afficher
function afisare($arr) {
	global $content;
	foreach($arr as $k => $v) {
		$content .= afficher($k, $v);
	}
}

// datele introduse corect sunt memorate si precompletate (cu date din $_SESSION (sau $_POST))
function afficher($input, $value) {
	$script = <<<SCR
	<script>
	$input.value = '$value'
	</script>
SCR;
	return $script;
}

// ascundem forumular de login (pt user auth)
function hide($element) {
	$script = <<<SCR
	<script>
	$element.classList.add('hidden')
	</script>
SCR;
	return $script;
}

function unhide($element, $mesaj='') {
	$script = <<<SCR
	<script>
	$element.classList.remove('hidden');
SCR;
	if(!empty($mesaj)) {
		$script .= "$element.innerText = '$mesaj';";
	}
	$script .= "</script>";
	return $script;
}

function show_pic($img) {
	$script = <<<SCR
	<script>
	const divImg = document.getElementById('footer').querySelector('div');
	divImg.classList.add('showpic');
	divImg.style.backgroundImage = "url($img)";
	</script>
SCR;
	return $script;
}

// initial -> intermediar
function modificare_pas() {
	$script = <<<SCR
	<script>
	pas.value = 'intermediar';
	submit.value = 'Continua catre pasul 3';
	</script>
SCR;
	return $script;
}


function verificare_data($data_sosire, $data_plecare) {
	$d1 = strtotime($data_sosire);
	$d2 = strtotime($data_plecare);
	if($d2 - $d1 < 0) {
		return true;
	}
	return false;
}

// in pasul 2 de la rezervari, dam disable la optiunile nebifate
function check_n_disable(...$input) {
	$script = "<script>";
	foreach($input as $radio) {
		$script .= "$radio.checked = true;";
	}
	$script .= <<<SCR
	const radioButtons = document.querySelectorAll('input[type="radio"]');
	radioButtons.forEach(radio => {
		if(radio.checked == false) {
		radio.disabled = true;
		};
	});
SCR;
	$script.= "</script>";
	return $script;
}

// functia asta este supraincarcata -> trebuia sparta in 2 fctii in loc de ultimul param, oh well, it works :)
function rezervare($tip, $data_sosire, $data_plecare, $nr_rate=0, $download=false) {
	global $camere;
	$d1 = new DateTime($data_sosire);
	$d2 = new DateTime($data_plecare);
	$interval = $d1 -> diff($d2);
	$nopti = $interval -> days;
	$pret = $camere[$tip];
	$total = $nopti * $pret;
	$content = <<<CONT
		<div id="main">
			<h2>Detalii rezervare</h2><br><br>
			<h3>Tipul de camera : $tip</h3>
			<h3>Data sosire: $data_sosire</h3>
			<h3>Data plecare: $data_plecare</h3>
			<h3>Numar de nopti: $nopti</h3>
CONT;
	if($nr_rate) {
		$pret_rata = round(($pret / $nr_rate) * 1.15, 2);		// 15% comision pt plata in rate
		$total = $pret_rata * $nr_rate * $nopti;
		$content .= "<h3>Numar rate: $nr_rate</h3>";
		$content .= "<h3>Pret rata: $pret_rata euro</h3>";
	}
	$content .= <<<CONT
			<h3>Total de plata: $total euro</h3>
			<br><br>
			<form method='post' action='save.php'>
				<div class='form-control'>
					<input type='submit' name='save' value='Rezerva si salveaza informatiile'>
				</div>
			</form>
		</div>
CONT;
	$content .= <<<FOOT
	<div id="footer">
			<p>Author : Tudor Alexandru<span>
			Email : <a href="mailto:tudorik05@yahoo.com">tudorik05@yahoo.com</a></span></p>
		</div>;
FOOT;
	$content_download = <<<CONT
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Detalii Rezervare</title>
		<style>
			table {
				border-collapse: collapse;
			}
			
			table, th, td {
				border: 1px solid black;
			}
			
			th, td {
				padding: 15px;
				width: 120px;
				height: 40px;
			}
			
			th { 
				text-align: center;
				vertical-align: center;
				color: white;
				background-color: green;
			}
			
			td {
				text-align: left;
				vertical-align: bottom;
			}
			
			tr:nth-child(odd) {
				background-color: lightgrey;
			}
			
			tr:hover {
				background-color: aliceblue;
			}
			
		
		</style>
	</head>
	<body>
		<h1>Detalii Rezervare</h1>
		<table>
			<thead>
CONT;
	if($nr_rate) {
		$content_download .= <<<CONT
				<tr><th>Tip camera</th><th>Pret unitar (euro)</th><th>Sosire</th><th>Plecare</th><th>Nr nopti</th><th>Nr rate</th><th>Pret rata</th><th>Total (euro)</th>
			</thead>
			<tbody>
				<tr><td>$tip</td><td>$pret</td><td>$data_sosire</td><td>$data_plecare</td><td>$nopti</td><td>$nr_rate</td><td>$pret_rata</td><td>$total</td>
			</tbody>
		</table>
	</body>
</html>
CONT;
	} else {
		$content_download .= <<<CONT
				<tr><th>Tip camera</th><th>Pret unitar (euro)</th><th>Sosire</th><th>Plecare</th><th>Nr nopti</th><th>Total (euro)</th>
			</thead>
			<tbody>
				<tr><td>$tip</td><td>$pret</td><td>$data_sosire</td><td>$data_plecare</td><td>$nopti</td><td>$total</td>
			</tbody>
		</table>
	</body>
</html>
CONT;
	}
	$_SESSION['nopti'] = $nopti;
	$_SESSION['total'] = $total;
	return ($download?$content_download:$content);
}

// functie pentru afisarea paginii 'sumar' (pt rezervari) in header
function sumar() {
	$script = <<<SCR
	<script>
	const sumar = document.createElement('li');
	const sumarAn = document.createElement('a');
	sumarAn.href = 'sumar.php';
	const sumarAnText = document.createTextNode('Sumar');
	sumarAn.appendChild(sumarAnText);
	sumar.appendChild(sumarAn);
	const ulHead = document.getElementById('menu').querySelector('ul');
	ulHead.appendChild(sumar);	
	</script>
SCR;
	return $script;
}

function display_rezervari() {
	$tabel = "<table id='tabel_rezervari'><thead><tr><th>Tip camera</th><th>Nume</th><th>Sosire</th><th>Plecare</th><th>Nopti</th><th>Pret euro</th></tr></thead><tbody>";
	$continut = explode(PHP_EOL, file_get_contents(DB_REZ, 'r'));
	$total_single = 0;
	$total_double = 0;
	$total_apartament = 0;
	for($i=1; $i < count($continut) - 1; $i++) {
		$nume = explode(',', $continut[$i])[0];
		$camera = explode(',', $continut[$i])[1];
		$sosire = explode(',', $continut[$i])[2];
		$plecare = explode(',', $continut[$i])[3];
		$nopti = explode(',', $continut[$i])[4];
		$total = explode(',', $continut[$i])[5];
		switch($camera) {
			case 'single': $total_single += $total; break;
			case 'double': $total_double += $total; break;
			case 'apartament': $total_apartament += $total; break;
		};
		$tabel .= "<tr><td>$camera</td><td>$nume</td><td>$sosire</td><td>$plecare</td><td>$nopti</td><td>$total</td></tr>";
	};
	$grand_total = $total_single + $total_double + $total_apartament;
	$tabel .= "</tbody></table>";
	$tabel2 = "<table id='tabel_rezervari'><thead><tr><th>Tip camera</th><th>Total</th></tr></thead>";
	$tabel2 .= "<tbody><tr><td>Single</td><td>$total_single</td></tr>";
	$tabel2 .= "<tr><td>Double</td><td>$total_double</td></tr>";
	$tabel2 .= "<tr><td>Apartament</td><td>$total_apartament</td></tr>";
	$tabel2 .= "<tr><td colspan=2 style='padding:0;font-weight:bold;text-align:center;word-spacing:3px;letter-spacing:1px'>TOTAL $grand_total EURO</td></tr></tbody></table>";
	return $tabel.$tabel2;
}


//REGEX
$pattern_nume = '/^[A-Za-z][a-z]+$/';
$pattern_prenume = '/^[A-Za-z][a-z]+([ ,\-\'][A-Za-z][a-z]+)*$/';
$pattern_email = '/(?i)^[a-z0-9_\-\.]+@[a-z\-]+\.[a-z]{2,6}+$/';
$pattern_tel = '/^4?07\d{8}$/';
$pattern_tel_fisier = '/4?07\d{8}/'; // pt cautare fara ancorare
$pattern_user = '/^[a-z][a-z\d_]+$/i';
$pattern_parola = '/.*[^\da-z].*/i';

// Logica

// pentru index.php verificam tipul de cerere 
// -> GET : - daca $_GET[logout] == 1 : distrugem sesiunea
//          - altfel afisam in functie de $_SESSION[username] ori pagina de autentifcare ori pagina de welcome
// -> POST : - numai daca userul este neautentificat
//           - verificam existenta lui in BD precum si match pe parola
//           - if so, setam $_SESSION[username] si $_SESSION[nume_complet]
//
// pentru celelate pagini, incepem lantul cu verificarea $_SESSION[username] mai intai 
// creare_cont.php -- daca userul este logat ( isset($_SESSION[username])) il redirectionam la home 
// rezervari.php -- daca userul NU este logat, ii afisam link catre home (alt content)





// TO DO

// pt user deja logat
// * buton de logout --------------------------------------------- YES
// * display:none pt formul de auth (js) ------------------------- YES
// head auth / non  ----> corelare intre pagini (auth / not) GLUE -- I think so ! - YES
// *** user neautentificat nu vede Rezervari // user auth nu vede creare-cont ----- YES
// pt rezervari => verificare $_SESSION['username'] -------------- YES
// pt bifa => setcookie(session_name, session_id(), +86400)  ----- YES
// poza ---------------------------------------------------------- YES
// * determinare pozitie ----------------------------------------- YES
// * sistemul de fisiere ----------------------------------------- YES
// * adaugare buton de upload ------------------------------------ YES
// pt rezervare in pasi => form hidden fields -------------------- YES
// consemnare rezervari + pagina admin --------------------------- YES

?>