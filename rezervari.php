<?php

require('config.php');

$content = <<< CONT
		<div id="main">
			<h2>Rezervari<br><small class="hidden" style="color:red"></small></h2><br>
			<br>
			<form method="post" action=$_SERVER[PHP_SELF]>
				<div class="rezervare">
					<label for="single">Camera Single</label>
					<input type="radio" name="camera" value="single" id="single">
					<img src="images/single.jpg" alt="Camera Single">
					<p>100 Euro</p>
				</div>
				<div class="rezervare">
					<label for="double">Camera Double</label>
					<input type="radio" name="camera" value="double" id="double">
					<img src="images/double.jpg" alt="Camera Double">
					<p>165 Euro</p>
				</div>
				<div class="rezervare">
					<label for="apartament">Apartament</label>
					<input type="radio" name="camera" value="apartament" id="apartament">
					<img src="images/apartament.jpg" alt="Apartament">
					<p>250 Euro</p>
				</div>
				<div class="rezervare">
					<label for="sosire">Data Sosire</label>
					<input type="date" name="sosire" id="sosire"><br><br>
					<label for="plecare">Data Plecare</label>
					<input type="date" name="plecare" id="plecare"><br><br>
					<br><br><br>
					<label>Plateste in rate :</label><br><br>
					<label for="da">Da</label>
					<input type="radio" name="rate" id="da" value="da">
					<label for="nu">Nu</label>
					<input type="radio" name="rate" id="nu" value="nu" checked><br><br>
					<div id="opt" class="hidden">
						<input type="hidden" name="pas" value="initial" id="pas">
						<label>Total rate</label>
						<select name="nr_rate">
							<option value="2">2</option>
							<option value="3" selected>3</option>
							<option value="4">4</option>
						</select>
					</div>
				</div>
				<br><br>
				<div class="rez">
					<br>
					<input type="submit" value="Rezerva acum" id="submit">
				</div>
			</form>				
		</div>
		
		<div id="footer">
			<div></div>
			<p>Author : Tudor Alexandru<span>
			Email : <a href="mailto:tudorik05@yahoo.com">tudorik05@yahoo.com</a></span></p>
		</div>
		<script>
		let da = document.getElementById('da');
		let nu = document.getElementById('nu');
		let nr_rate = document.getElementById('opt');
		let error = document.querySelector('h2').querySelector('small');
		let submit  = document.getElementById('submit');
		let pas = document.getElementById('pas');
		let sosire = document.getElementById('sosire');
		let plecare = document.getElementById('plecare');
		let single = document.getElementById('single');
		let double = document.getElementById('double');
		let apartament = document.getElementById('apartament');
		da.addEventListener('change', () => {
			if (da.checked == true) {
				submit.value = 'Continua catre pasul 2';
			} 
		});
		nu.addEventListener('change', () => {
			if (nu.checked == true) {
				submit.value = 'Rezerva acum';
			} 
		});
		</script>
CONT;

$content_neauth = <<< CONT
		<div id="main">
			<h2>Pagina este accesibila doar pentru userii autentificati !</h2><br><br>
			<h3>Mergeti la <a href="index.php">pagina</a> de autentificare sau
			<a href="creare_cont.php">creati un cont</a></h3>
		</div>
CONT;

session_start();

// daca userul nu este logat ii afisam o pagina care contine link catre home (indiferent de GET/POST)
if(!isset($_SESSION['username'])) {
	echo HEAD;
	echo $content_neauth;
	echo FOOT;
} else {
	if($_SERVER['REQUEST_METHOD'] === 'GET') {
		//var_dump($_SESSION); ATENTIE - raman setate datele de la ultima rezervare, dar e ok, se suprascriu in cazul unei noi rezervari din partea aceluiasi user
		if(isset($_GET['logout']) && $_GET['logout'] == 1) {
			session_destroy(); // distrugem sesiunea; atentie! datele sunt inca valabile in pagina curenta!
			setcookie(session_name(), '', time() - 86400); // (opt) stergem session cookie-ul (sess id propagat prin cookie)
			$_SESSION = array(); // (opt) golimm arrayul $_SESSION
			echo HEAD;
			echo $content_neauth;
			echo FOOT;
			// cerere de tip GET, logout nu e setat => user autentificat
		} else {
			echo HEAD_AUTH;
			$content .= login($_SESSION['nume_complet']);
			$content .= show_pic($_SESSION['poza']);
			$content .="
	</body>
</html>";
			echo $content;
		}
	} elseif($_SERVER['REQUEST_METHOD'] === 'POST') {
		// verificare validare date
		if(empty($_POST['camera']) || !in_array($_POST['camera'], array_keys($camere)) || empty($_POST['sosire']) || empty($_POST['plecare']) || empty($_POST['rate'])
			|| verificare_data($_POST['sosire'], $_POST['plecare']) || empty($_POST['rate']) || !in_array($_POST['rate'], array('da', 'nu'))
			||  empty($_POST['pas']) || !in_array($_POST['pas'], array('initial', 'intermediar'))) {
			echo HEAD_AUTH;
			$content .= login($_SESSION['nume_complet']);
			$content .= show_pic($_SESSION['poza']);
			$content .= unhide('error','Va rugam sa furnizati toate datele necesare !');
			if (verificare_data($_POST['sosire'], $_POST['plecare'])) {
				$content .= unhide('error','Data de plecare nu poate fi inainte de date de sosire !');
			}
			$content .= "
	</body>
</html>";
			echo $content;
			//date valide
		} else {
			$_SESSION['camera'] = $_POST['camera'];
			$_SESSION['sosire'] = $_POST['sosire'];
			$_SESSION['plecare'] = $_POST['plecare'];
			$_SESSION['rate'] = $_POST['rate'];
			echo HEAD_AUTH;
			if($_POST['pas'] === 'initial') {	
				if($_POST['rate'] === 'nu') {
					// daca a ramas setat nr de rate de la o rezervare anterioara, il desetam
					if(isset($_SESSION['nr_rate'])) {
						unset($_SESSION['nr_rate']);
					}
					$content = rezervare($_SESSION['camera'], $_SESSION['sosire'], $_SESSION['plecare']);
					$content .= login($_SESSION['nume_complet']);
					$content .= show_pic($_SESSION['poza']);
					$content .="
	</body>
</html>";
					echo $content;
				} elseif($_POST['rate'] === 'da') {
					$content .= login($_SESSION['nume_complet']);
					$content .= show_pic($_SESSION['poza']);
					$content .= unhide('nr_rate');
					$content .= modificare_pas();
					$content .= afficher('sosire', $_SESSION['sosire']);
					$content .= afficher('plecare', $_SESSION['plecare']);
					$content .= check_n_disable($_SESSION['camera'],$_SESSION['rate']);
					$content .="
	</body>
</html>";			
					echo $content;
				}
			} elseif($_POST['pas'] === 'intermediar') {
				$_SESSION['nr_rate'] = $_POST['nr_rate'];
				$content = rezervare($_SESSION['camera'], $_SESSION['sosire'], $_SESSION['plecare'], $_SESSION['nr_rate']);
				$content .= login($_SESSION['nume_complet']);
				$content .= show_pic($_SESSION['poza']);
				$content .="
	</body>
</html>";
				echo $content;
			}
		}
	}
}

/* JS - rate

		da.addEventListener('change', () => {
			if (da.checked == true) {
				nr_rate.classList.remove('rate');
			} 
		});
		nu.addEventListener('change', () => {
			if (nu.checked == true) {
				nr_rate.classList.add('rate');
			} 
		});				
		</script>
		
*/

?>


