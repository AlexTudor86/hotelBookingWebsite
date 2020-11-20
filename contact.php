<?php

require('config.php');

$content = <<< CONT
		<div id="main">
			<h2>Contact Us</h2><br>
			<form method="post">
				<div class="form-control">
					<label>Introduceti numele complet :</label><br>
					<input type="text" name="nume" id="contact_nume">
				</div>
				<div class="form-control">
					<label>Introduceti adresa de email :</label><br>
					<input type="email" name="email" id="contact_email">
				</div>
				<div class="form-control">
					<label>Introduceti numarul de telefon :</label><br>
					<input type="text" name="tel" id="contact_tel">
				</div>
				<div class="form-control">
					<label>De unde ati auzit de noi ?</label><br>
					<select name="heared" id="contact_heared"><small></small>
						<option value="prieteni">Prienteni</option>
						<option value="google" selected>Google</option>
						<option value="alta">Alta sursa</option>
					</select>
				</div>
				<div class="form-control">
					<label>Adresati-ne o intrebare :</label><br>
					<textarea cols="50" rows="5" name="review" id="contact_review"></textarea>
				</div>
				<br>
				<div class="form-control">
					<input type="submit" value="Intreaba-ne">
				</div>
			</form>
		</div>
		<div id="footer">
			<div></div>
			<p>Author : Tudor Alexandru<span>
			Email : <a href="mailto:tudorik05@yahoo.com">tudorik05@yahoo.com</a></span></p>
		</div>
		<script>
		const contact_nume = document.getElementById('contact_nume');
		const contact_email = document.getElementById('contact_email');
		const contact_tel = document.getElementById('contact_tel');
		const contact_heared = document.getElementById('heared');
		const contact_review = document.getElementById('contact_review');
		</script>
		
CONT;

$content2 = <<<CONT
		<div id="main">
			<h2>Formularul a fost trimis cu succes !</h2><br>
			<h3>Va multumim pentru interes.<br> 
			Veti fi contactat in cel mai scurt timp de unul dintre consultantii nostri de vanzari.
			</h3>
		</div>
CONT;

$content2 .= FOOT;

session_start();

if($_SERVER['REQUEST_METHOD'] === 'GET') {
	if(isset($_GET['logout']) && $_GET['logout'] == 1) {
		session_destroy(); // distrugem sesiunea; atentie! datele sunt inca valabile in pagina curenta!
		setcookie(session_name(), '', time() - 86400); // (opt) stergem session cookie-ul (sess id propagat prin cookie)
		$_SESSION = array(); // (opt) golimm arrayul $_SESSION
	} 
	if(isset($_SESSION['username'])) {
		echo HEAD_AUTH;
		$content .= login($_SESSION['nume_complet']);
		$content .= show_pic($_SESSION['poza']);
		$content .= afficher('contact_nume', $_SESSION['nume_complet']);
		$content .= afficher('contact_email', $_SESSION['email']);
		$content .= afficher('contact_tel', $_SESSION['tel']);
	} else {
		echo HEAD;
	}
	$content .= "
	</body>
</html>";
	echo $content;
} elseif($_SERVER['REQUEST_METHOD'] === 'POST') {
	// validari Regex 
	$eroare = false;
	if(empty($_POST['nume']) || !preg_match($pattern_prenume, $_POST['nume'])) {
		$content .= error('contact_nume');
		$eroare = true;
	} else {
		// valid -> memorare in $_SESSION['contact'] 
		$_SESSION['contact']['contact_nume'] = $_POST['nume']; 
	}
	if(empty($_POST['email']) || !preg_match($pattern_email, $_POST['email'])) {
		$content .= error('contact_email');
		$eroare = true;
	} else {
		$_SESSION['contact']['contact_email'] = $_POST['email'];
	}
	if(empty($_POST['tel']) || !preg_match($pattern_tel, $_POST['tel'])) {
		$content .= error('contact_tel');
		$eroare = true;
	} else {
		$_SESSION['contact']['contact_tel'] = $_POST['tel'];
	}
	if(empty($_POST['heared']) || !in_array($_POST['heared'], array('prieteni', 'google', 'alta'))) {
		$content .= error('contact_heared', 'WTF you script kiddie ??!');
		$eroare = true;			
	}
	if(empty($_POST['review'])) {
		$content .= error('contact_review');
		$eroare = true;			
	}
	
	if($eroare) {
		if(!empty($_SESSION['contact'])) {
			afisare($_SESSION['contact']);
		}
	} else {
		// daca nu sunt erori, nu mai avem nevoie sa memoram datele introduse corect pentru a le precompleta
		unset($_SESSION['contact']);
		if(!file_exists(DB_CNT)) {
			file_put_contents(DB_CNT, 'Mesaje primite de la useri:'.PHP_EOL .PHP_EOL);
		} 
		$mesaj = PHP_EOL;
		$mesaj .= "Data : ".date('d F Y, H:i:s').PHP_EOL;
		$mesaj .= "Mesaj de la : $_POST[nume]".PHP_EOL;
		$mesaj .= "Email: $_POST[email]".PHP_EOL;
		$mesaj .= "Telefon: $_POST[tel]".PHP_EOL;
		$mesaj .= "Sursa: $_POST[heared]".PHP_EOL;		
		$mesaj .= PHP_EOL;
		$mesaj .= "$_POST[review]".PHP_EOL;
		$mesaj .= PHP_EOL;
		$mesaj .= "********************************************".PHP_EOL;
		file_put_contents(DB_CNT, $mesaj, FILE_APPEND);
		// daca datele au fost validate, afisam alt content
		$content = $content2;
	}
	// verificam aici daca userul este logat, pentru a completa $content (abia aici acoperim cazul de succes $content2)
	if(isset($_SESSION['username'])) {
		echo HEAD_AUTH;
		$content .= login($_SESSION['nume_complet']);
		$content .= show_pic($_SESSION['poza']);
	}
	else {
		echo HEAD;
	}
	$content .="
	</body>
</html>";
	echo $content;
}	
	
?>