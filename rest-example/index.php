<!DOCTYPE html>
<html lang="de">

<head>
	 <meta charset="UTF-8" />
	 <meta name="viewport" content="width=device-width, initial-scale=1">
	 <title>AITEC - Getting Started </title>
	 <script language="javascript" src="js/jquery-1.10.2.min.js" ></script>
	 <script language="javascript" src="js/rest_arduino.js" ></script>
	 <link rel="stylesheet" href="css/bootstrap.min.css">
	 <script src="js/bootstrap.min.js"></script>
<!--	 <link rel="stylesheet" type="text/css" href="style.css" media="screen" />-->
</head>


<body>
<div class="container">

<div class="page-header">
	<h1>AITEC</h1>
</div>

<div class="panel panel-info">
  <div class="panel-heading"><div class="panel-title"><strong>Liebe/r Teilnehmer/in des Moduls AITEC,</strong></div></div>
  <div class="panel-body">
	Willkommen auf der Startseite der AITEC-VM. Das Document-Root-Verzeichnis dieses Apache Webservers befindet sich in deinem Home-Verzeichnis und heisst <em>www</em>. Dort kannst du deine <em>*.html</em> und <em>*.php</em> Dateien ablegen.
</div>
</div>
<div class="panel panel-default">
  <div class="panel-heading"><div class="panel-title">Arduino Beispiel</div></div>
  <div class="panel-body">
	Nachfolgend ein Beispiel, wie ein Arduino über das Internet gesteuert werden kann.

	Auf dem Arduino muss der Sketch <strong>Restful_LightSensor_NumberDisplay</strong> laufen.

	Anschliessend kann mit einem Klick auf den Button eine tolle Zahl auf das Numberdisplay gezaubert werden:<br /><br />
	<form class="form-inline">
		<div class="form-group">
			<label for="textbox">input_numberdisplay</label>
			<input class="form-control" type='text' id='textbox' value="123456">
		</div>
      <button class="btn btn-default" id="submit" >
			Button
		</button>
	</form><br />
	
<div class="panel panel-danger">
  <div class="panel-heading"><div class="panel-title">Output</div></div>
  <div class="panel-body" id="output">
  <span class="text text-danger">Arduino nicht angeschlossen!?</span>
  </div>
</div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading"><div class="panel-title">PHP</div></div>
  <div class="panel-body">
	<p>Und das aktuelle Datum per php</p>
	<?php 
		echo date("D, d.m.Y"); 
	?>
	</div>
</div>

<div class="panel panel-default">
  <div class="panel-heading"><div class="panel-title">CGI</div></div>
  <div class="panel-body">
  Ein Beispiel für CGI befindet sich im Verzeichniss <em>/home/aitec/cgi-bin</em>. Das sieht dann etwa so aus: <a href="http://localhost/cgi-bin/test.sh">http://localhost/cgi-bin/test.sh</a>. </div>
</div>
</div>
<script>
// Load when page finished loading, not earlier!
jQuery(function() {
	setupButton();
	setupLightDisplay();
});
</script>
</body>
</html>

