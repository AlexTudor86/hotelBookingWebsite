<?php 

require('config.php');

$content = <<< CONT
		<div id="main">
			<h2>Tabel cu rezervari</h2><br><br>
CONT;
$content .= display_rezervari();
$content .= '</div>';
$content .= <<<FOOT
		<div id="footer">
			<div></div>
			<p>Author : Tudor Alexandru<span>
			Email : <a href="mailto:tudorik05@yahoo.com">tudorik05@yahoo.com</a></span></p>
		</div>
FOOT;

session_start();

if(!isset($_SESSION['username']) || ($_SESSION['admin'] !== 'yes')) {
	header('Location: index.php');
} else {
	if(isset($_GET['logout']) && $_GET['logout'] == 1) {
		session_destroy(); 
		setcookie(session_name(), '', time() - 86400);
		$_SESSION = array();
		header('Location: index.php');
	} else {
		$content .= login($_SESSION['nume_complet']);
		$content .= show_pic($_SESSION['poza']);
		$content .= "
		</body>
	</html>";
		echo HEAD_AUTH;
		echo $content;
	}
}