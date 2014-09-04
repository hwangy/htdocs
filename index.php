<html lang="en">
<?php 
	include "data/connect.php";
?>
<head>
	<script id="clProgramVectorAdd" type="text/x-opencl">
		kernel void ckVectorAdd(global uint* vectorIn1,
					global uint* vectorIn2,
					global uint* vectorOut, uint uiVectorWidth) {
			uint x = get_global_id(0);
			if (x >= uiVectorWidth) return;
			vectorOut[x] = vectorIn1[x] + vectorIn2[x];
		}
	</script>

	<!--[if lte IE 6]>
		<style>#leftt, #leftb, #rightt, #rightb, #topl, 
			#topr, #botr, #botl {display: none; }</style>
	<![endif]-->
	<meta http-equiv="Cache-Control" content="no-cache">
	
	<script type="text/javascript" src="data/jquery-1.11.1.min.js"></script>
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
			};

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
					var response = xmlhttp.responseText.split("BREAK");
					
					document.getElementById("articles").innerHTML = response[response.length-1];

					for (var i = 0; i < response.length-1; i++) {
						$.ajax({
							url: "http://localhost/htdocs" + response[i],
							dataType: "script",
							success: function(){}
						});
					}
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
		
		/**
			* Experimenting with OpenCL
			* */
		function loadKernel(id) {
			var kernelElement = document.getElementById(id);
			var kernelSource = kernelElement.innerHTML;
			if (kernelElement.innerHTML != "") {
				var mHttpReq = new XMLHttpRequest();

				mHttpReq.onreadystatechange = function() {
					kernelSource = mHttpReq.responseText;
					return kernelSource;
				};

				mHttpReq.open("GET", kernelElement.src);
				mHttpReq.send("NULL");
			}

			return kernelSource
		}

		function vectorAdd(output) {
			output.innerHTML = "";

			try {
				if (!detectCL()) return;

				var vectorLength = 30;
				var UIVector1 = new Uint32Array(vectorLength);
				var UIVector2 = new Uint32Array(vectorLength);

				for (var i = 0; i < vectorLength; i++) {
					UIVector1[i] = Math.floor(Math.random() * 100);
					UIVector2[i] = Math.floor(Math.random() * 100);
				}

				output.innerHTML = "<br>Vector Length " + vectorLength;

				//Launch OpenCL interface
				var ctx = webcl.createContext();

				var buffsize = vectorLength * 4;
				var buffin1 = ctx.createBuffer(WebCL.MEM_READ_ONLY, buffsize);
				var buffin2 = ctx.createBuffer(WebCL.MEM_READ_ONLY, buffsize);
				var buffout = ctx.createBuffer(WebCL.MEM_WRITE_ONLY, buffsize);

				var kernelSource = loadKernel("clProgramVectorAdd");
				console.log(kernelSource);
				var program = ctx.createProgram(kernelSource);
				var device = ctx.getInfo(WebCL.CONTEXT_DEVICES)[0];

				try {
					program.build([device], "");
				} catch (e) {
					alert ("Failed to build WebCL program. Error "
						+ program.getBuildInfo (device, WebCL.PROGRAM_BUILD_STATUS)
						+ ":  " + program.getBuildInfo (device, WebCL.PROGRAM_BUILD_LOG));
					throw e;
				}

				var kernel = program.createKernel("ckVectorAdd");
				kernel.setArg(0, buffin1);
				kernel.setArg(1, buffin2);
				kernel.setArg(2, buffout);
				kernel.setArg(3, new Uint32Array([vectorLength]));

				var cmdQueue = ctx.createCommandQueue(device);
				cmdQueue.enqueueWriteBuffer (buffin1, false, 0, buffsize, UIvector1);
				cmdQueue.enqueueWriteBuffer (buffin2, false, 0, buffsize, UIvector2);

				var localWS = [8];
				var globalWS = [Math.ceil(vectorLength/localWS) * localWS];

				output.innerHTML += "<br>Global work item size: " + globalWS;
				output.innerHTML += "<br>Local work item size: " + localWS;

				cmdQueue.enqueueNDRangeKernel(kernel, globalWS.length, NULL, globalWS, localWS);

				outBuffer = new Uint32Array(vectorLength);
				cmdQueue.enqueueReadBuffer(buffout, false, 0 , buffsize, outBuffer);
				cmdQueue.finish();
			} catch (e) {
				document.getElementById("output").innerHTML 
					+= "<h3>ERROR:</h3><pre style=\"color:red;\">" + e.message + "</pre>";
				throw e;
			}
		}

		function detectCL() {
			if (window.webcl == undefined) {
				alert("OPENCL: Unfortunately your system does not support WebCL. " +
				      "Make sure that you have both the OpenCL driver " +
			              "and the WebCL browser extension installed.");
				return false;
			}

			try {
				var platforms = webcl.getPlatforms();
				var devices = [];
				for (var i in platforms) {
					var plat = platforms[i];
					devices[i] = plat.getDevices();
				}
				return true;	
			} catch (e) {
				alert("OPENCL: Unfortunately platform or device inquiry failed.");
				return false;
			}
		}

	</script>
	<title id="title">WooBurgh</title>

	<link rel="stylesheet" media="screen" href="style.css">
</head>

<div id="about" onclick="go('articles/about.txt', this)">About Me</div>

<body onresize="modSize()" onload="
			getArticles();
			detectCL();">

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
		<br /><div id="output" onclick="vectorAdd(this)">CONSOLE</div>
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
