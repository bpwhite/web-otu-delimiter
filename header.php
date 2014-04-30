<html>
<head>
	<title>SCCWRP DNA Barcoding OTU Delimiter v 0.01</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
</head>
<body>
	
	<div style="float:left;margin-right:10px;">	
	<a href="/dna-barcoding-web-tools"><img src=images/sccwrp_logo_small.jpg></a>
	</div>

	<div style="float:left;margin-left:15px;">
	SCCWRP DNA Barcoding OTU Delimiter v 0.01<br />
	<a href="/dna-barcoding-web-tools">Delim Home</a> | <a href="http://www.sccwrp.org">sccwrp.org</a>
	</div>
	<div style="float:left;margin-left:15px;">
	Server load:
	<?php 
		$load = sys_getloadavg();
		if($load[0] < 0.50) {
			echo "<img src=images/server_green.png>";
		} elseif($load[0] >= 0.50 && $load[0] <= 1) {
			echo "<img src=images/server_yellow.png>";
		} elseif($load[0] >= 1) {
			echo "<img src=images/server_red.png>";
		}
	?>
	<br />
	Sample Data: <br />
	Citation: <br />
	Documentation: <br />
	Git source: <a href="https://github.com/SCCWRP/dna-barcoding-web-tools">https://github.com/SCCWRP/dna-barcoding-web-tools</a>
	<br />
	</div>
	<p style="clear:both;">
	</p>
	<hr>
