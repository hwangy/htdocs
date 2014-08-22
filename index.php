<html lang="en">
<?php 
	include "../data/connect.php";
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
	</script>
	<title>WOOBURGH</title>

	<link rel="stylesheet" media="screen" href="style.css">
</head>

<body onresize="modSize()">

<!--For Page Border --!>
<div id="border">
<div id="leftt"></div>	<div id="leftb"></div>
<div id="rightt"></div>	<div id="rightb"></div>
<div id="topl"></div>	<div id="topr"></div>
<div id="botl"></div>	<div id="botr"></div>
</div>

<section class="intro" role="banner">
	<header>
		<h1>WooBurgh</h1>
		<h2>The Wonderous World of HTML/PHP/JAVASCRIPT</h2>
	</header>
</section>

<section class="body">
	<canvas oncontextmenu="return false" id="fractal" width="600" height="600">
		Sorry, canvas is not supported :P
	</canvas>
	<script type="text/javascript" src="projects/fractal.js"></script>
	<!--div class="end"></div!-->
</section>
</body>
</html>
