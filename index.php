<?php 

require('config.php');
				
$content = <<<CONT
<div id="main">	
			<h2>Bine ati venit !</h2>
			<p>Pensiunea Vietii va asteapta intr-o zona exclusivista, ferita de ochii lumii,
				<img src="images/cab1.jpg" alt="poza cabana 1">
			unde va puteti relaxa intr-un loc retras si sa va bucurati de peisajele naturii. 
			Linistea acestui mic colt de rai va umple de energie, entuziasm si claritate. 
			Aici puteti contempla in tihna la nemurirea sufletului, mai ales noaptea cand cerul este plin 
			de stele.</p>
			<img src="images/partie.jpg" alt="partie">
			<p>Printre activitatile de interes se numara sporturile de iarna, precum schi sau snowboard, 
			pentru care natura ne rasfata cu o partie de vis, numai buna de profitat. 
			Traseele sunt frumoase si numeroase, variind in dificultate, avand astfel o gama larga de optiuni.
				<img src="images/jacuzzi.jpg" alt="jacuzzi"></p>
			<p>Nu in ultimul rand, pensiunea dispune si de un jacuzzi, ideal pentru o			
			relaxare insotita de o sticla de sampanie, dupa o lunga zi de schi.</p>
			<br><br>
			<form method='post' action=$_SERVER[PHP_SELF] id="home_form">
				<h3>Autentificare</h3>
				<div class="form-ctrl">
					<label>Username :</label>
					<input type="text" name="username" id="home_user">
					<small style="position:absolute;color:red;top:560px;left:450px;"></small>
				</div>
				<div class="form-ctrl">
					<label>Parola :</label>
					<input type="password" name="pass" id="home_pass">
					<small></small>					
				</div>
				<div class="form-ctrl">
					<label>Pastreaza-ma logat </label>
					<input type="checkbox" name="log">
				</div>
				<div class="form-ctrl">
					<input type="submit" value="Login">
				</div>
			</form>
			
		</div>
		<br><br><br><br>
		
		<div id="footer">
			<div></div>
			<p>Author : Tudor Alexandru<span>
			Email : <a href="mailto:tudorik05@yahoo.com">tudorik05@yahoo.com</a></span></p>
		</div>
		<script type="text/javascript">
		const username = document.getElementById('home_user');
		const password = document.getElementById('home_pass');
		const form = document.getElementById('home_form');
		const title = document.querySelector('h2');
		</script>
		
CONT;


session_start();
// pentru GET
if($_SERVER['REQUEST_METHOD'] === 'GET') {
	if(isset($_GET['logout']) && $_GET['logout'] == 1) {
		session_destroy(); // distrugem sesiunea; atentie! datele sunt inca valabile in pagina curenta!
		setcookie(session_name(), '', time() - 86400); // (opt) stergem session cookie-ul (sess id propagat prin cookie)
		$_SESSION = array(); // (opt) golim arrayul $_SESSION
	}
	if(isset($_SESSION['username'])) {
		$content .= hide('form');
		$content .= login($_SESSION['nume_complet']);
		$content .= show_pic($_SESSION['poza']);
		if($_SESSION['admin'] === 'yes') {
			$content .= sumar();
		}
		echo HEAD_AUTH;
	} else {
		echo HEAD;
	}
	$content .= "
	</body>
</html>";
	echo $content;
	
// pentru POST ($_SESSION nu ar trebui sa existe ! ne asiguram totusi 
} elseif($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['username'])) {
	// validare regex inainte sa citim fisierul cu baza de date
	if(empty($_POST['username']) || !preg_match($pattern_user, $_POST['username'])) {
		$content .= error('username', 'Username-ul nu este valid !');
	} else {
		//setam flag
		$exists = false;
		// daca fisierul cu BD nu exista => eroare
		if(!file_exists(DB_USR)) {
			$content .= error('username', 'Username inexistent !');
		} else {
			// citim fisierul in intregime, facem split dupa EOL si cautam in fiecare element din array username-ul
			$file = explode(PHP_EOL, file_get_contents(DB_USR));
			foreach($file as $entry) {
				if(substr($entry, 0, strpos($entry, ',')) === $_POST['username']) {
					// daca l-am gasit, flag = true
					$exists = true;
					$_SESSION['username'] = $_POST['username'];
					$_SESSION['nume_complet'] = explode(',', $entry)[1] . ' ' . explode(',', $entry)[2];
					preg_match($pattern_tel_fisier, $entry, $tel_array);
					$_SESSION['tel'] = implode('', $tel_array);
					$_SESSION['email'] = explode(',', $entry)[3];
					$pass = explode(',', $entry)[5];
					$_SESSION['admin'] = explode(',', $entry)[6];
					if(count(explode(',', $entry)) === 8) {
						$_SESSION['poza'] = substr($entry, strrpos($entry, ',')+1);
					} else {
						$_SESSION['poza'] = 'images/user.png';
					}
					break;
				}
			}
		}
		if(!$exists) {
			// daca userul nu exita in BD, afisam eroare
			$content .= error('username', 'Username inexistent !');
		} else {
			// daca este si parola nu face match, afisam eroare si distrugem $_SESSION['nume_complet'] pe care il setasem
			if($pass !== $_POST['pass']){
				$content .= afficher('username', $_POST['username']);
				$content .= error('password', 'Parola nu este corecta !');
				$_SESSION = array();
			} else {
			// ! daca parola face match, ascundem form-ul de login, ii afisam in bara statusul si poza
			// !!! raman setate variabilele din$_SESSION : username, nume_complet, email, telefon --> se folosesc la contact
				$content .= hide('form');
				$content .= login($_SESSION['nume_complet']);
				$content .= show_pic($_SESSION['poza']);
				if($_SESSION['admin'] === 'yes') {
					$content .= sumar();
				}
				// bifa remember me 
				if(isset($_POST['log'])) {
					$params = session_get_cookie_params();
					setcookie(session_name(), session_id(), time() + 86400, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
				}
			}
		}
	}
	$head = isset($_SESSION['username'])?HEAD_AUTH:HEAD;
	echo $head;
	$content .= "
	</body>
</html>";
	echo $content;
}


		
?>	