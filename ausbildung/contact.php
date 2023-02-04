<?php
define ('MAILTO', "info@mmichel.de"); // Empfänger hier eintragen
define ('MAILFROM', "Kontaktformular@example.org"); // ggfls. Absender hier eintragen
define ('SUBJECT', "Nachricht vom Kontaktformular"); // ohne Sonderzeichen
define ('CHARSET', "ISO-8859-15"); // Zeichenkodierung ggfls. anpassen
$Pflichtfelder = array('Nachricht'); // ggfls. weitere Pflichtfelder angeben
define ('MailAnzeige', true); // Nachricht nach Versand angezeigen
define ('FormularLink', true); // nach Versand Link auf neues Formular ausgeben
define ('FormularAnzeige', false); // keine Formularausgabe nach Versand


define ('LF', chr(13).chr(10)); // Zeilenumbruch
$AddHeader = (MAILFROM) ? 'From: '.MAILFROM.LF : ""; if(function_exists('quoted_printable_encode')) { // ab PHP 5.3.0 für RFC-Konformität
  $AddHeader .= 'MIME-Version: 1.0';
  $AddHeader .= LF.'Content-Type: text/plain; charset='.CHARSET;
  $AddHeader .= LF.'Content-Transfer-Encoding: quoted-printable';
}
else $AddHeader .= 'Content-Type: text/plain; charset='.CHARSET;

if($Formular_abgeschickt = !empty($_POST)) {
  $Formular_leer = true;
  if(ini_get('magic_quotes_runtime')) ini_set('magic_quotes_runtime',0);
  $_POST = array_map('Formular_Daten', $_POST);
}
function Formular_Daten($val) {
  global $Formular_leer;
  if(is_array($val)) return array_map('Formular_Daten', $val);
  if(ini_get('magic_quotes_gpc')) $val = stripslashes($val);
  if($val = trim($val)) $Formular_leer = false;
  return $val;
}

function Formular_Pflichtfelder() {
  global $Pflichtfelder;
  $Fehler = '';
  foreach ($Pflichtfelder as $Feld) {
    $key = str_replace(' ','_',$Feld);
    if(!(isset($_POST[$key]) && trim($_POST[$key])!=='')) {
      if($Fehler) $Fehler .= '<br />';
      $Fehler .= 'Pflichtfeld "' . $Feld . '" nicht ausgefüllt.';
    }
  }
  return $Fehler;
}

function Formular_neu($log='.htPOSTdata.txt') {
  if(file_exists($log) && is_readable($log)
   && file_get_contents($log) == print_r($_POST,true))
  return false;
  if($handle=@fopen($log, 'w')) {
    fwrite($handle, print_r($_POST,true)); fclose($handle);
  }
  return true;
}

function Formular_Check() {
  global $Formular_leer;
  if($Formular_leer) $Fehler = 'Keine Daten eingetragen.';
  elseif(!$Fehler = Formular_Pflichtfelder()) {
    if(!Formular_neu()) $Fehler = 'Nachricht war bereits verschickt.';
  }
  return $Fehler;
}

function Formular_Eingabe($Feldname, $def='') {
  if(isset($_POST[$Feldname]) && $_POST[$Feldname]!=='')
    echo htmlspecialchars($_POST[$Feldname],ENT_COMPAT,CHARSET);
  else echo $def;
}

function Formular_Nachricht($HTML=false) {
  $msg=''; $vor=''; $nach=': ';
  foreach ($_POST as $key => $val) {
    if($key != 'abschicken' && trim($val)) { // if(true) um alle Felder auszugeben
      if($HTML) {
        $msg .= '<strong>'.$vor.$key.$nach.'</strong>'.htmlspecialchars($val).'<br />';
      }
      else {
        if(function_exists('quoted_printable_encode')) {
          $val = quoted_printable_encode($val);
        }
        $msg .= $vor.$key.$nach.$val.LF.LF;
      }
    }
  }
  return $msg;
}

$Meldung = ""; $id = "";
if($Formular_abgeschickt) {
  if($Formular_Fehler = Formular_Check()) {
    $Meldung = $Formular_Fehler; $id = 'Fehler';
  }
  elseif(@mail(MAILTO, SUBJECT, Formular_Nachricht(), $AddHeader)) {
    $Meldung = 'Nachricht verschickt.'; $id = 'OK';
  }
  else {
    $Meldung = 'Server-Fehler !'; $id = 'Fehler';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Homöopathieausbildung in Hannover" />
	<meta name="keywords" content="Homöopathie, Hannover, Ausbildung, Kurs, Organon, Anamnese, Synthesis, Potenzen, Miasmen, Miasmenlehre, Hahnemann, Ortega, Gienov"/>
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
	<link rel="stylesheet" href="./css/lightbox.min.css">
    <title>Ausbildung für klassische Homöopathie in Hannover</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" />
    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="./css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="style.css" rel="stylesheet">
	
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="./js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="./js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body data-spy="scroll" data-target="#navbar">
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div id="navbar" class="navbar-collapse collapse navbar-right">
		<ul class="nav nav-pills">
		  <li role="presentation" class="active"><a href="#home">HOME</a></li>
		  <li role="presentation"><a href="#ausbildung">AUSBILDUNG</a></li>
		  <li role="presentation"><a href="#lehrplan">INHALT</a></li>
		  <li role="presentation"><a href="#konzept">KONZEPT</a></li>
		  <li role="presentation"><a href="#orga">ORGANISATION</a></li>
		  <li role="presentation"><a href="#contact_form">KONTAKT</a></li>
		</ul>

        </div><!--/.navbar-collapse -->
      </div>
    </nav>
	<div class="jumbotron nomargin" id="home">
	</div>
    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div id="green" class="jumbotron">
      <div class="container">
        <h1>Ausbildung für klassische Homöopathie in Hannover 2017-2019</h1>
        <p>Unsere zweijährige Ausbildung in Klassischer Homöopathie nach der Lehre Samuel Hahnemanns hat sich in vielen Jahren bewährt und ist von uns stets weiterentwickelt worden. Wir sind seit zwanzig Jahren ein eingespieltes, engagiertes Dozententeam, und unser Ziel ist es, Sie zur Praxisreife zu führen.</p>
      </div>
    </div>
	<div class="whitebackground">
	<div class="container" id="ausbildung">
	<h1>Die Ausbildung</h1>
	<p><strong>Im Februar 2017</strong> bieten wir zum 11. Mal eine <strong>zweijährige Ausbildung in 
	klassischer Homöopathie</strong> an, wobei wir uns auf ein bewährtes Konzept
	mit einem eingespielten Team von erfahrenen Therapeuten stützen können.
	<br/><br/>
	Unser Angebot richtet sich an Sie, wenn Sie in einem Heilberuf tätig sind,
	sich dazu ausbilden lassen oder wenn Sie einfach nur ein grundsätzliches 
	Interesse an der Homöopathie für sich und ihre Familie haben.
	<br/><br/>
	Wir wollen Ihnen ein <strong>sicheres Verständnis</strong> und <strong>Freude</strong> für diese ganzheitliche
	Heilmethode vermitteln und Sie anhand praktischer Übungen und Fallbei-
	spiele befähigen und <strong>ermutigen</strong>, die Homöopathie selbst anzuwenden.
	<br/><br/>
	Es ist uns wichtig, Wissen mit Intuition zu verbinden und einen ganzheitlichen
	Zugang  zum Patienten und zu den Arzneimitteln zu gewinnen. Durch  
	<u>Arzneimittelverreibungen</u>  und  <u>freiwillige Arzneimittelprüfungen</u> erfahren
	wir selbst etwas an Geist, Seele und Körper von der individuellen Heilkraft des
	jeweiligen Arzneimittels.
	<br/><br/>
	Indem wir Zusammenhänge herstellen zwischen der “Signatur “ der verwendeten Substanz - d.h. Ihrem Aufbau und ihren Eigenschaften – und eigenen Assoziationen entstehen persönlich erfahrbare, <strong>lebendige Arzneimittelbilder</strong>.
	<br/><br/>
	Bei uns erhalten Sie eine <strong>fundierte praktisch theoretische Ausbildung</strong>:
	Wir verbinden die alten Grundlagen, wie das „Organon“ , mit der neuesten
	„Miasmenlehre “ und erläutern dies praxisnah an Fällen. Wir üben den Umgang mit dem verbreitetsten Repertorium, dem „SYNTHESIS“.
	<br/><br/>
	In <strong>Live-Anamnesen</strong> zeigen wir Dozenten unsere Methode der Fallaufnahme,
	und in zahlreichen <strong>Differenzialdiagnosen</strong> werden die Kenntnisse der besprochenen Arzneimittel vertieft.
	</p>
	</div>
   	</div>
	<center>
	<img src="./images/pflanze_5.jpg" Alt="Heilmittel" class="roundborderImage"/>
	</center>
	<div class="beigebackground">
	<div class="container" id="lehrplan">
		<h1>Inhalte und Ablauf</h1>
		<p>Im Folgenden finden Sie einen Überblick über den Ablauf und die Inhalte unseres Ausbildungsplans.</p>
		<h2>1: Theorie</h2>
		<p><strong>Grundlagen gemäß Hahnemanns "Organon", "Chronische Krankheiten", u.a.</strong></p>
		<ul class="unorderedlist">
		<li>Das Wesen der Krankheit <span class="first">Lehrjahr 1</span></li>
		<li>Die Arzneikräfte <span class="first">Lehrjahr 1</span></li>
		<li>Die Arzneikräfte <span class="second">Lehrjahr 1</span></li>
		<li>Das Simile-Gesetz <span class="first">Lehrjahr 1</span></li>
		<li>Die Anamnese (Grundsätze und Methoden) <span >Lehrjahr 1 + 2</span></li>
		<li>Das Repertorisieren mit dem <u>„SYNTHESIS“</u> <span class="first">Lehrjahr 1</span></li>
		<li>Die Erstverschreibung <span class="first">Lehrjahr 1</span></li>
		<li>Die Verlaufsbeurteilung und die Folgeverschreibung <span class="second">Lehrjahr 2</span></li>
		<li>Die Arzneimittelherstellung - C- und Q-Potenzen <span >Lehrjahr 1 + 2</span></li>
		<li>Die Nosoden <span class="second">Lehrjahr 2</span></li>
		<li>Die Arzneimittelprüfung <span class="first">Lehrjahr 1</span></li>
		<li>Die <u>Miasmenlehre</u> nach Hahnemann, Ortega und Gienow <span class="second">Lehrjahr 2</span></li>
		<li>Impfung und Vakzinose <span class="second">Lehrjahr 2</span></li>
		<li>Homöopathische Krebsbehandlung <span class="second">Lehrjahr 2</span></li>
		</ul>
		<h2>2: Arzneimittelbilder</h2>
		<p>Um Ihnen ein umfängliches Verständnis über Heil- und Arzneimittel zu vermitteln, gehören zu unserer Ausbildung eine ausführliche Besprechung von ca. 50 Arzneimitteln und sowie eine weiteer, kürzere Vorstellung von ca. 80 weiteren Arzneimitteln meist im Rahmen von Differentialdiagnosen.</p>
		<h2>3: Praxis</h2>
		<ul class="unorderedlist">
		<li>Spezielle Wochenenden und Abende mit den Schwerpunkten (Live-)Anamnese, Repertorisation und Fallbearbeitung, Anamnese-Übungen</li>
		<li>2 Kollektivverreibungen eines Arzneimittels über ein Wochenende, Arzneimittelpotenzierung</li>
		<li>Freiwillige Gruppenprüfung eines Arzneimittels</li>
		<li>10 Differentialdiagnosen zu einem speziellen Indikationsaspekt mit zuvor behandelten Arzneimitteln und weiteren kleineren Mitteln</li>
		<li>Regelmäßige Fallbeispiele sowie Hinweise zur Dosierung und Patientenführung</li>
		<li>Kleine "Hausaufgaben" zur Vertiefung</li>
		</ul>
	</div>
	</div>
	<center>
	<img src="./images/pflanze_1.jpg" Alt="Heilmittel" class="roundborderImage"/>
	</center>
	<div class="brightgreenbackground">
	<div class="container" id="konzept">
		<h1>Pädagogisches Konzept</h1>
		<p>Die Organisation der Unterrichtsinhalte folgt einem eigenen pädagogischen Konzept:<br/>
		Die Abfolge der Arzneimittelbilder ist so gewählt, dass auf einige “große“
		Mittel eine <strong>Differenzialdiagnose</strong> ( z.B. “ Grippaler Infekt“ ) folgt, in der auf die  vorher besprochenen Mittel unter einer speziellen, praktischen Fragestellung zurückgegriffen wird und “ kleine“ , spezifische Mittel ergänzt wer-
		den. Dadurch wird Ihre Kenntnis der “ großen“ Mittel vertieft, und Sie können
		frühzeitig praktische Kenntnisse in einem überschaubaren Rahmen gewinnen
		und gegebenenfalls bereits anwenden.</p>
		<h2>1. Ausbildungsjahr:</h2>
		<p>Das 1. Ausbildungsjahr legt den Schwerpunkt auf die theoretischen Grundlagen und auf die Behandlung akuter Krankheiten.
		</p>
		<h2>2. Ausbildungsjahr:</h2>
		<p>Im 2. Ausbildungsjahr befassen wir uns mit der Behandlung chronischer
		Krankheiten incl. der Miasmen.</p>
		<p>
		In beiden Jahren üben wir den Umgang mit dem gebräuchlichsten 	Repertorium,, dem “ Synthesis “..
		<br/><br/>
		Zur praktischen Ausbildung gehören kürzere und längere Life-Anamnesen,
		in denen die Dozenten ihre Vorgehensweise zeigen.
		<br/><br/>
		Zum Theorie- Teil sowie zu den meisten Arzneimittelbildern verteilen wir 
		ca. 300 Seiten eigene Skripte, die ein Nacharbeiten ermöglichen.
		Wer besser durch Hören lernt oder einen Vortrag versäumt hat, kann CD`s
		mit unseren Arzneimittelvorträgen erhalten.
		</p>
		<p><strong>Am Ende dieser Ausbildung:</strong></p>
		<ul class="unorderedlist">
			<li>können Sie die häufigsten Akuterkrankungen behandeln</li>
			<li>können Sie eigene Fälle aufnehmen, strukturieren und lösen</li>
			<li>können Sie Krankheitsverläufe beurteilen</li>
			<li>haben Sie den Umgang mit dem Repertorium und den Arzneimittellehren geübt.</li>
			<li>kennen Sie die wichtigsten Arzneimittel zur Behandlung akuter und chronischer Krankheiten.</li>
			<li>besitzen Sie ein umfassenderes Verständnis von Krankheit, Heilungs-und Lebensprozessen</li>
			<li>haben Sie sich selbst im Spiegel persönlicher Erfahrungen mit Arzneimitteln	neu reflektiert.</li>
		</ul>
	</div>
	</div>
		<center>
	<img src="./images/pflanze_2.jpg" Alt="Heilmittel" class="roundborderImage"/>
	</center>
	<div class="whiteopaquebackground">
		<div class="container" id="orga">
		<h1>Organisatorisches</h1>
		<p>Jedes Ausbildungsjahr umfasst 141 Unterrichtsstunden , die sich jährlich auf	<u>8 Wochenenden</u> und 15 abendliche Arbeitskreise am Donnerstagabend (18:30- 21:00) verteilen.
		<p>Die Anzahl der Teilnehmer/ Innen an der Ausbildung wird auf 10 beschränkt. Nach Beendigung der Ausbildung erhalten Sie eine Teilnahmebescheinigung.</p>
		<hr/>
		<h1>Kosten</h1>
		<p>Die Ausbildungskosten incl. Skript betragen 1.300 Euro im Jahr.</p>
		<ul>
		<li><p>a) als <strong>zwei</strong> Jahres-Zahlungen a <strong>1.300 Euro</strong> (im Februar 2017 und Februar 2018)</p></li>
		<li><p>b) in <strong>24 Monatsraten a 110 EUR</strong> (1. Zahlung : Februar 2017)</p></li>
		</ul>
		<p>In den Ausbildungskosten enthalten ist das ca. 300 Seiten umfassende Skript und CD´s mit eigenen Arzneimittelvorträgen.
		</p>
		<p>Bei Anmeldung <strong>bis zum 16. Dezember 2016</strong> wird ein <strong>einmaliger Rabatt</strong> in Form einer Rückzahlung von <strong>50 Euro bis zum 18.Mai 2017</strong> gewährt.</p>
		<h1>Anmeldung</h1>
		<p>Bitte fordern Sie von uns einen <u>Ausbildungsvertrag</u> sowie die <u>detaillierten Ausbildungspläne</u> an. Die Plätze werden nach Reihenfolge des Vertragseingangs vergeben.</p>
		<p><strong>Informationsabende:</strong>
		<p><strong>08. September 19:00 Uhr:</strong>
		<p><strong>10. November 19:00 Uhr</strong>
		<p><strong>Ausbildungsort: Praxis Elke Holexa, Striehlstraße 11; 30159 Hannover:</strong>
		<p>Bei Interesse laden wir Sie auch gerne zu einem persönlichen Kennenlerngespräch in unsere Praxis ein.</p>
		</div>
	</div>
		<center>
	<img src="./images/pflanze_3.jpg" Alt="Heilmittel" class="roundborderImage"/>
	</center>
	<div class="beigebackground">
	<div class="container">
		<h1>Kontakt</h1>
		<center>
		<table style="width:80%">
		<tr>
			<td>
			<p>
			Naturheilpraxis<br/>
			<strong>Hartmut Neumann</strong><br/>
			Hardenkamp 32<br/>
			32699 Extertal<br/>
			<strong>Tel:</strong> 05262/ 4657<br/>
			<strong>eMail:</strong><a href="mailto:hartmut.neumann5@freenet.de">hartmut.neumann5@freenet.de</a></p>
			</td>
			<td>
			<p>
			Praxis<br/>
			<strong>Elke Holexa</strong><br/>
			Striehlstraße 11<br/>
			30159 Hannover<br/>
			<strong>Tel:</strong> 0511/14490<br/>
			<strong>eMail:</strong><a href="mailto:elke.holexa@htp-tel.de">elke.holexa@htp-tel.de</a></p>
			</td>
		</tr>
		</table>
		<img src="./images/imgcontact.jpg" Alt="Unser Team" class="roundborderImage">
		</center>
		<form class="well form-horizontal" method="post"  id="contact_form" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" enctype="multipart/form-data" accept-charset="<?php echo CHARSET; ?>">
		<fieldset>

		<!-- Form Name -->
		<legend>Kontaktieren Sie uns</legend>

		<!-- Text input-->

		<div class="form-group">
		  <label class="col-md-4 control-label">Vorname *</label>  
		  <div class="col-md-4 inputGroupContainer">
		  <div class="input-group">
		  <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
		  <input  name="first_name" placeholder="Vorname" class="form-control"  type="text" value="<?php Formular_Eingabe('Vorname'); ?>">
			</div>
		  </div>
		</div>

		<!-- Text input-->

		<div class="form-group">
		  <label class="col-md-4 control-label" >Nachname *</label> 
			<div class="col-md-4 inputGroupContainer">
			<div class="input-group">
		  <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
		  <input name="last_name" placeholder="Nachname" class="form-control"  type="text" value="<?php Formular_Eingabe('Nachname'); ?>">
			</div>
		  </div>
		</div>

		<!-- Text input-->
			   <div class="form-group">
		  <label class="col-md-4 control-label">E-Mail *</label>  
			<div class="col-md-4 inputGroupContainer">
			<div class="input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
		  <input name="email" placeholder="E-Mail Addresse" class="form-control"  type="text" value="<?php Formular_Eingabe('EMail'); ?>">
			</div>
		  </div>
		</div>


		<!-- Text input-->
			   
		<div class="form-group">
		  <label class="col-md-4 control-label">Telefon</label>  
			<div class="col-md-4 inputGroupContainer">
			<div class="input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
		  <input name="phone" placeholder="(49)12345-1212" class="form-control" type="text" value="<?php Formular_Eingabe('Telefon'); ?>">
			</div>
		  </div>
		</div>

		<!-- Text area -->
		  
		<div class="form-group">
		  <label class="col-md-4 control-label">Text *</label>
			<div class="col-md-4 inputGroupContainer">
			<div class="input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-pencil"></i></span>
					<textarea class="form-control" name="comment" placeholder="Text" value="<?php Formular_Eingabe('Nachricht'); ?>"></textarea>
		  </div>
		  </div>
		</div>

		<?php
if($Meldung) echo '<p class="Meldung" id="',$id,'">',$Meldung,'</p>';	
	//echo'<div class="alert alert-success" role="alert" id="success_message">Anfrage gesendet <i class="glyphicon //glyphicon-thumbs-up"></i> Vielen Dank f&uuml;r Ihre Anfrage. Wir werden diese schnellstm&ouml;glich beantworten.</div>';
if($id=='OK') {
  if(MailAnzeige) echo '<p id="Nachricht">',Formular_Nachricht(true),'</p>';
  if(FormularLink) {
    echo '<p id="FormularLink">
      <a href="'.$_SERVER['SCRIPT_NAME'].'">neues Formular?</a>
    </p>';
  }
}

if(FormularAnzeige || $id != 'OK'): ?>	
		<!-- Success message -->
		<div class="alert alert-success" role="alert" id="success_message">Anfrage gesendet <i class="glyphicon glyphicon-thumbs-up"></i> Vielen Dank f&uuml;r Ihre Anfrage. Wir werden diese schnellstm&ouml;glich beantworten.</div>
<?php endif; ?>
		<!-- Button -->
		<div class="form-group">
		  <label class="col-md-4 control-label"></label>
		  <div class="col-md-4">
			<button type="submit" class="btn btn-warning" >Absenden <span class="glyphicon glyphicon-send"></span></button>
		  </div>
		</div>
		<div style="float:right">
			<p>mit * gekennzeichnete Felder sind Pflichteingabefelder, weitere Angaben optional</p>
		</div>
		</fieldset>
		</form>
		</div>
    </div><!-- CONTAINER-->
	</div>
		<center>
		<img src="./images/pflanze_4.jpg" Alt="Heilmittel" class="roundborderImage"/>
		</center>
	<footer>
	<div class="container">
		<div style="float:left">
			<span style="color:#c0c0c0">&copy; Homöopathieausbildung-Hannover.de</span>
		</div>
		<div style="float:right">
			<a href="#" data-toggle="modal" data-target="#datenschutzModal" class="footerlink">DATENSCHUTZ</a> | 
			<a href="#" data-toggle="modal" data-target="#imprintModal" class="footerlink">IMPRESSUM</a>
		</div>
	</div>
	</footer>
	<!-- Modal -->
	<div class="modal fade" id="imprintModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h4 class="modal-title">Impressum</h4>
		  </div>
		  <div class="modal-body">
			<table class="impressumtable>
			<th>
				<td width="5%"></td>
				<td></td>
				<td width="5%"></td>
				<td></td>
			</th>
			<tr>
				<td width="5%" class="tdTopAlign"><span class="glyphicon glyphicon-user" aria-hidden="true" title="Name"></span></td>
				<td class="tdTopAlign">Elke Holexa</td>
				<td width="5%" class="tdTopAlign"></td>
				<td class="tdTopAlign"></td>
			</tr>
			<tr>
				<td class="tdTopAlign" width="5%"><span class="glyphicon glyphicon-home" aria-hidden="true" title="Anschrift"></span></td>
				<td class="tdTopAlign">Striehlstraße 11 <br/>30159 Hannover</td>
				<td class="tdTopAlign" width="5%"></td>
				<td class="tdTopAlign"></td>
			</tr>
			<tr>
				<td class="tdTopAlign" width="5%"></td>
				<td class="tdTopAlign">&nbsp;</td>
				<td class="tdTopAlign" width="5%"></td>
				<td class="tdTopAlign"></td>
			</tr>			
			<tr>
				<td class="tdTopAlign"><span class="glyphicon glyphicon-envelope" aria-hidden="true" title="E-Mail"></span></td>
				<td class="tdTopAlign"><a href="mailto:elke.holexa@htp-tel.de">elke.holexa@htp-tel.de</a></td>
				<td class="tdTopAlign"></td>
				<td class="tdTopAlign"></td>
			</tr>
			<tr>
				<td class="tdTopAlign"><span class="glyphicon glyphicon-earphone" aria-hidden="true" title="Telefon"></span></span></td>
				<td class="tdTopAlign">0511 - 1 44 90</td>
				<td class="tdTopAlign"></td>
				<td class="tdTopAlign"></td>
			</tr>
			</table>
			<h3>Haftung für Inhalte</h3>
			<p>Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen. Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.</p>
			<h3>Haftung für Links</h3>
			<p>Unser Angebot enthält Links zu externen Webseiten Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich. Die verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar. Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend entfernen.</p>
			<h3>Urheberrecht</h3>
			<p>Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet. Soweit die Inhalte auf dieser Seite nicht vom Betreiber erstellt wurden, werden die Urheberrechte Dritter beachtet. Insbesondere werden Inhalte Dritter als solche gekennzeichnet. Sollten Sie trotzdem auf eine Urheberrechtsverletzung aufmerksam werden, bitten wir um einen entsprechenden Hinweis. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Inhalte umgehend entfernen.</p>
		  </div>
		  <div class="modal-footer" style="text-align:center">
			<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
		  </div>
		</div>
	  </div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="datenschutzModal" tabindex="-1" role="dialog" >
	  <div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h4 class="modal-title">Datenschutz</h4>
		  </div>
		  <div class="modal-body">
			<h3>Geltungsbereich</h3>
			<p>Diese Datenschutzerklärung klärt Nutzer über die Art, den Umfang und Zwecke der Erhebung und Verwendung personenbezogener Daten durch den verantwortlichen Anbieter Elke Holexa auf dieser Website (im folgenden “Angebot”) auf.</p>
			<p>Die rechtlichen Grundlagen des Datenschutzes finden sich im Bundesdatenschutzgesetz (BDSG) und dem Telemediengesetz (TMG).</p>
			<h3>Zugriffsdaten/ Server-Logfiles</h3>
			<p>Der Anbieter (beziehungsweise sein Webspace-Provider) erhebt Daten über jeden Zugriff auf das Angebot (so genannte Serverlogfiles). Zu den Zugriffsdaten gehören:</p>
			<p>Name der abgerufenen Webseite, Datei, Datum und Uhrzeit des Abrufs, übertragene Datenmenge, Meldung über erfolgreichen Abruf, Browsertyp nebst Version, das Betriebssystem des Nutzers, Referrer URL (die zuvor besuchte Seite), IP-Adresse und der anfragende Provider.</p>
			<p>Der Anbieter verwendet die Protokolldaten nur für statistische Auswertungen zum Zweck des Betriebs, der Sicherheit und der Optimierung des Angebotes. Der Anbieterbehält sich jedoch vor, die Protokolldaten nachträglich zu überprüfen, wenn aufgrund konkreter Anhaltspunkte der berechtigte Verdacht einer rechtswidrigen Nutzung besteht.</p>
			<h3>Umgang mit personenbezogenen Daten</h3>
			<p>Personenbezogene Daten sind Informationen, mit deren Hilfe eine Person bestimmbar ist, also Angaben, die zurück zu einer Person verfolgt werden können. Dazu gehören der Name, die Emailadresse oder die Telefonnummer. Aber auch Daten über Vorlieben, Hobbies, Mitgliedschaften oder welche Webseiten von jemandem angesehen wurden zählen zu personenbezogenen Daten.</p>
			<p>Personenbezogene Daten werden von dem Anbieter nur dann erhoben, genutzt und weiter gegeben, wenn dies gesetzlich erlaubt ist oder die Nutzer in die Datenerhebung einwilligen.</p>
			<h3>Kontaktaufnahme</h3>
			<p>Bei der Kontaktaufnahme mit dem Anbieter (zum Beispiel per Kontaktformular oder E-Mail) werden die Angaben des Nutzers zwecks Bearbeitung der Anfrage sowie für den Fall, dass Anschlussfragen entstehen, gespeichert.</p>
			<h3>Einbindung von Diensten und Inhalten Dritter</h3>
			<p>Es kann vorkommen, dass innerhalb dieses Onlineangebotes Inhalte Dritter, wie zum Beispiel Videos von YouTube, Kartenmaterial von Google-Maps, RSS-Feeds oder Grafiken von anderen Webseiten eingebunden werden. Dies setzt immer voraus, dass die Anbieter dieser Inhalte (nachfolgend bezeichnet als "Dritt-Anbieter") die IP-Adresse der Nutzer wahr nehmen. Denn ohne die IP-Adresse, könnten sie die Inhalte nicht an den Browser des jeweiligen Nutzers senden. Die IP-Adresse ist damit für die Darstellung dieser Inhalte erforderlich. Wir bemühen uns nur solche Inhalte zu verwenden, deren jeweilige Anbieter die IP-Adresse lediglich zur Auslieferung der Inhalte verwenden. Jedoch haben wir keinen Einfluss darauf, falls die Dritt-Anbieter die IP-Adresse z.B. für statistische Zwecke speichern. Soweit dies uns bekannt ist, klären wir die Nutzer darüber auf.</p>
			<h3>Cookies</h3>
			<p>Cookies sind kleine Dateien, die es ermöglichen, auf dem Zugriffsgerät der Nutzer (PC, Smartphone o.ä.) spezifische, auf das Gerät bezogene Informationen zu speichern. Sie dienen zum einem der Benutzerfreundlichkeit von Webseiten und damit den Nutzern (z.B. Speicherung von Logindaten). Zum anderen dienen sie, um die statistische Daten der Webseitennutzung zu erfassen und sie zwecks Verbesserung des Angebotes analysieren zu können. Die Nutzer können auf den Einsatz der Cookies Einfluss nehmen. Die meisten Browser verfügen eine Option mit der das Speichern von Cookies eingeschränkt oder komplett verhindert wird. Allerdings wird darauf hingewiesen, dass die Nutzung und insbesondere der Nutzungskomfort ohne Cookies eingeschränkt werden.</p>
			<p>Sie können viele Online-Anzeigen-Cookies von Unternehmen über die US-amerikanische Seite http://www.aboutads.info/choices/ oder die EU-Seite http://www.youronlinechoices.com/uk/your-ad-choices/ verwalten.</p>
			<h3>Widerruf, Änderungen, Berichtigungen und Aktualisierungen</h3>
			<p>Der Nutzer hat das Recht, auf Antrag unentgeltlich Auskunft zu erhalten über die personenbezogenen Daten, die über ihn gespeichert wurden. Zusätzlich hat der Nutzer das Recht auf Berichtigung unrichtiger Daten, Sperrung und Löschung seiner personenbezogenen Daten, soweit dem keine gesetzliche Aufbewahrungspflicht entgegensteht.</p>
		  </div>
		  <div class="modal-footer" style="text-align:center">
			<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
		  </div>
		</div>
	  </div>
	</div>
	
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="./js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="./js/ie10-viewport-bug-workaround.js"></script>
	<script type="text/javascript" src="./js/bootstrapValidator.min.js"></script>
	<script src="./js/lightbox.min.js"></script>
	<script src="./js/site.js"></script>
  </body>
</html>
