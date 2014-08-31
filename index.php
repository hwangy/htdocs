<html lang="en">
<?php 
	include "data/connect.php";
?>
<head>
	<!--[if lte IE 6]>
		<style>#leftt, #leftb, #rightt, #rightb, #topl, 
			#topr, #botr, #botl {display: none; }</style>
	<![endif]-->
	<meta http-equiv="Cache-Control" content="no-cache">
	<script type="text/javascript">
		var articles;

		function getArticles() {
			document.getElementById("title").innerHTML = "WooBurgh";
			
			var xmlhttp = new window.XMLHttpRequest();
			var result;

			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					document.getElementById("articles").innerHTML = xmlhttp.responseText;
				}
			}

			xmlhttp.open("POST", "data/articles.php", true);
			xmlhttp.send();
		}

		function modSize() {
			var viewWidth = window.innerWidth;
			var viewHeight = window.innerHeight;

			/*
				* Possibly update this in the future so
				* there is a gradient of alphas so the borders
				* turns transparent
			 */	
			var list = document.getElementById("border").children;	
			for (var i = 0; i < list.length; i++) {
				list[i].style.display = (viewWidth < 920 || viewHeight < 400)?
					"none":"inline";
			}
		}

		function go(link, t) {
			document.getElementById("title").innerHTML = t;
			
			//Load article
			var xmlhttp = new window.XMLHttpRequest();
			var result;

			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState = 4 && xmlhttp.status==200) {
					document.getElementById("articles").innerHTML = xmlhttp.responseText;
				}
			}


			xmlhttp.open("GET", "data/article.php?article=" + link, true);
			xmlhttp.send();
		}

		document.onkeydown = function(e) {
			switch (e.keyCode) {
				case 8:
					getArticles();
					break;
			}
		}
	</script>
	<title id="title">WooBurgh</title>

	<link rel="stylesheet" media="screen" href="style.css">
</head>

<div id="about" onclick="go('articles/about.txt', this)">About Me</div>

<body onresize="modSize()" onload="getArticles()">

<!--For Page Border --!>
<div id="border">
<div id="leftt"></div>	<div id="leftb"></div>
<div id="rightt"></div>	<div id="rightb"></div>
<div id="topl"></div>	<div id="topr"></div>
<div id="botl"></div>	<div id="botr"></div>
</div>

<section class="intro" role="banner">
	<header>
		<h1 onclick="getArticles()">WooBurgh</h1>
		<h2>The Wonderous World of HTML/PHP/JAVASCRIPT</h2>
	</header>
</section>

<section class="body">
	<!--canvas oncontextmenu="return false" id="fractal" width="600" height="600">
		Sorry, canvas is not supported :P<br />
		You should probably upgrade/switch your browser...
	</canvas>
	<script type="text/javascript" src="projects/fractal.js"></script!-->
	
	<div id="articles">
		<table>
			<tr>
				<td><div class="article" id="0">It appears there are no articles....</div></td>
			</tr>
		</table>
	</div>
</section>
</body>
</html>
