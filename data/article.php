<?php
	session_start();
	$file = $_GET['article'];

	if (file_exists("../".$file)) {
		$f = fopen("../".$file, "r");
		$f = fread($f, filesize("../".$file));
		
	} else echo "FILE ".$file." DOES NOT EXIST.";

	$head = substr($f, 0, strpos($f, "<article>"));
	$body = substr($f, strpos($f, "<article>") + 9);

	$totalmsg = "";

	$tags = split("\n", $head);
	for ($i = 2; $i < count($tags); $i++) {
		$tmp = split("\t", $tags[$i]);

		$t = strtolower($tmp[0]);
		if (!strcmp($t, "title")) {
			$totalmsg .= "<div id='t'>".$tmp[1]."</div>";
		} else if (!strcmp($t, "author")) {
			$totalmsg .= "<div id='a'>By ".$tmp[1]."</div>";
		}

	}


	//Check for custom tags
	while (true) {
		if (($i = strpos($body, "<plugin a="))) {
			$begin = substr($body, 0, $i);
			$tomod = substr($body, $i, strpos($body, "</plugin")+9-$i);
			$end = substr($body, strpos($body, "</plugin></center>")+9);
			//Locate location tag
			$tmp = strpos($tomod, "a=\"")+3;
			$adr = substr($tomod, $tmp, strpos($tomod, "\">")-$tmp);
			$newtag = "<canvas oncontextmenu=\"return false\" id=\"fractal\" width=\"600\"
					height=\"600\"></canvas>
					<script type=\"text/javascript\" src=\"".$adr."\"></script>";
			$body = $begin.$newtag.$end;

			break;
		}
	}

	$totalmsg .= "<br /><div id='text'>".$body."</div>";

	echo $totalmsg;
?>
