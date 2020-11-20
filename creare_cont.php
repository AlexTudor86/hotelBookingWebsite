<?php

require('config.php');
$content = <<<CONT
		<div id="main">
			<h2 style="margin-top:0; margin-bottom:10px">Cont Nou</h2>
			<form method="post" action=$_SERVER[PHP_SELF] enctype="multipart/form-data">
				<div class="form-control">
					<label>Introduceti numele :</label><br>
					<input type="text" name="nume" id="cont_nume">
					<small></small>
				</div>
				<div class="form-control">
					<label>Introduceti prenumele :</label><br>
					<input type="text" name="prenume" id="cont_prenume">
					<small></small>
				</div>
				<div class="form-control">
					<label>Introduceti adresa de email :</label><br>
					<input type="text" name="email" id="cont_email"><br>
					<small></small>
				</div>
				<div class="form-control">
					<label>Introduceti numarul de telefon :</label><br>
					<input type="text" name="tel" id="cont_tel"><br>
					<small></small>
				</div>
				<div class="form-control">
					<label>Alegeti un username :</label><br>
					<input type="text" name="username" id="cont_user">
					<small></small>
				</div>
				<div class="form-control">
					<label>Alegeti o parola :</label></br>
					<input type="password" name="parola" id="cont_pass">
					<small></small>
				</div>
				<div class="form-control">
					<label>Confirmati parola :</label></br>
					<input type="password" name="parola2" id="cont_pass2">
					<small></small>
				</div>			
				<div class="form-control">
					<label>Alegeti o poza (optional) :</label></br>
					<input type="hidden" name="MAX_FILE_SIZE" value="2097152">
					<input type="file" name="poza" id="cont_poza">
					<small></small>
				</div>
				<div class="form-control">
					<input type="submit" value="Inregistreaza Cont">
				</div>
			</form>
		</div>
		<div id="footer">
			<p>Author : Tudor Alexandru<span>
			Email : <a href="mailto:tudorik05@yahoo.com">tudorik05@yahoo.com</a></span></p>
		</div>
		<script type="text/javascript">
		const nume = document.getElementById('cont_nume');
		const prenume = document.getElementById('cont_prenume');
		const email = document.getElementById('cont_email');
		const username = document.getElementById('cont_user');
		const pass = document.getElementById('cont_pass');
		const pass2 = document.getElementById('cont_pass2');
		const tel = document.getElementById('cont_tel');
		const poza = document.getElementById('cont_poza');
		</script>
CONT;

$content_success = <<<CONT
		<div id="main">
			<h2>Contul a fost creat cu succes !</h2><br><br>
			<h3>Mergeti la <a href="index.php">pagina</a> de autentificare</h3>
		</div>
		<div id="footer">
			<div></div>
			<p>Author : Tudor Alexandru<span>
			Email : <a href="mailto:tudorik05@yahoo.com">tudorik05@yahoo.com</a></span></p>
		</div>
	</body>
</html>
CONT;

session_start();

// daca userul este logat si incearca accesarea acestei pagini, il redirectionam la home
if(isset($_SESSION['username'])) {
	header('Location: index.php');
}

if($_SERVER['REQUEST_METHOD'] === 'GET') {
	echo HEAD;
	$content .= "
	</body>
</html>";
	echo $content;
} elseif($_SERVER['REQUEST_METHOD'] === 'POST') {
	// flag pentru verificarea erorilor
	$error = false;
	// Validari regex, pentru fields valide le memoram in array-ul $_SESSION['cont']
	if(empty($_POST['nume']) || !preg_match($pattern_nume, $_POST['nume'])) {
		$content .= error('nume', 'Va rugam furnizati un nume valid !');		
		$error = true;
	} else {
		$_SESSION['cont']['nume'] = $_POST['nume'];
	}
	if(empty($_POST['prenume']) || !preg_match($pattern_prenume, $_POST['prenume'])) {
		$content .= error('prenume', 'Va rugam furnizati un prenume valid !');
		$error = true;
	} else {
		$_SESSION['cont']['prenume'] = $_POST['prenume'];
	}
	if(empty($_POST['email']) || !preg_match($pattern_email, $_POST['email'])) {
		$content .= error('email', 'Va rugam furnizati un email valid !');
		$error = true;
	} else {
		$_SESSION['cont']['email'] = $_POST['email'];
	}
	if(empty($_POST['tel']) || !preg_match($pattern_tel, $_POST['tel'])) {
		$content .= error('tel', 'Va rugam furnizati un nr de telefon valid !');
		$error = true;
	} else {
		$_SESSION['cont']['tel'] = $_POST['tel'];
	}
	if(empty($_POST['username']) || !preg_match($pattern_user, $_POST['username'])) {
		$content .= error('username', 'Va rugam furnizati un username valid !');
		$error = true;
	} else {
		$_SESSION['cont']['username'] = $_POST['username'];
	}
	if(empty($_POST['parola']) || !preg_match($pattern_parola, $_POST['parola']) || strlen($_POST['parola']) < 4) {
		$content .= error('pass', 'Va rugam furnizati o parola legitima si valida !');
		$error = true;
	}
	// verificare parole coincid
	if(empty($_POST['parola2']) || ($_POST['parola2'] != $_POST['parola'])) {
		$content .= error('pass2', 'Parolele trebuie sa fie identice!');
		$error = true;
	}
	
	// Validare poza - daca nu au fost erori la upload ; pentru MIME type; imagini trebuie sa aibe max 2MB (directiva upload_max_filesize din php.ini)
	
	// flag pentru salvare abia dupa validarea datelor !
	$ready_to_save = false;
	if(!empty($_FILES['poza'])) {
		if($_FILES['poza']['error'] > 0) {
			$content .= error('poza', 'Eroare la upload!');
	} else {
			// daca nu avem erori la upload
			$dir_upload = 'uploads/';
			$src = $_FILES['poza']['tmp_name'];
			$dst = $dir_upload.basename($_FILES['poza']['name']);
			// vrem numai fisiere care au MIME conform regex below
			$pattern_extensie = '/(jpeg|jpg|png)/i';
			$f_resource = finfo_open(FILEINFO_MIME_TYPE);
			$check_extensie = preg_match($pattern_extensie, finfo_file($f_resource, $src));
			// daca nu face match afisam eroare
			if(!$check_extensie) {
				$content .= error('poza', 'Numai format de tip jpeg sau png!');
			} else {
				// altfel ne pregatim sa salvam fisierul dupa validare (daca nu il redenumim sau nu il mutam nu se salveaza)
				$ready_to_save = true;
			}
		}
	}		
			
	
	// setam flag pt cont creat (sau necreat)
	$cont_creat = false;
	
	if($error) { 
		// campurile introduse corect raman setate
		if(isset($_SESSION['cont'])) {
			afisare($_SESSION['cont']);
		}
	} else {
		// daca validarea regex s-a incheiat cu succes, nu mai avem nevoie de arrayul $_SESSION['cont']
		unset($_SESSION['cont']);
		// daca fisierul nu exista, il cream si scriem in el capul de tabel
		if(!file_exists(DB_USR)) {
			file_put_contents(DB_USR, 'Username,Nume,Prenume,Email,Telefon,Parola,Admin,Poza'.PHP_EOL);
		} 
		// handler pt fisier
		$fh = fopen(DB_USR, 'r+');
		$exists = false; // flag control
		// citim fisierul linie cu linie pana atata timp cat cursorul nu a ajuns la sfarsitul fisierului
		while(!feof($fh)) {
			$linie = fgets($fh);
			// Vericam daca user-ul este deja in fisier; in caz afirmativ setam un flag
			if(substr($linie, 0, strpos($linie,',')) === $_POST['username']) {
				$exists = !$exists;
				break;
			}
		}
		fclose($fh);
		if($exists) {
			// Stilizam input-ul de username pentru ca flag este setat
			$content .= error('username', 'Username-ul exista deja !');
		} else {
			file_put_contents(DB_USR, $_POST['username'].','.ucfirst($_POST['nume']).','.ucwords($_POST['prenume']).','.$_POST['email'].','.
			$_POST['tel'].','.$_POST['parola'].','.'no', FILE_APPEND); // ***********ATENTIE************** pt admin editam manual fisierul si modificam "no" cu "yes *********
			// fisierul este salvat aici ! - dupa ce s-au facut verificari
			if($ready_to_save) {
				if(move_uploaded_file($src, $dst)){
					file_put_contents(DB_USR, ','.$dst.PHP_EOL, FILE_APPEND);
				} else {
					file_put_contents(DB_USR, PHP_EOL, FILE_APPEND);
				}
			} else {
				file_put_contents(DB_USR, PHP_EOL, FILE_APPEND);
			};
			$cont_creat = true;
		}
	}
	$content .= "</body></html>";
	echo HEAD;
	$c = $cont_creat?$content_success:$content;
	echo $c;
}

?>

		
		
		
		