
	<script id="clProgramVectorAdd" type="text/x-opencl">
		kernel void ckVectorAdd(global uint* vectorIn1,
					global uint* vectorIn2,
					global uint* vectorOut, uint uiVectorWidth) {
			uint x = get_global_id(0);
			if (x >= uiVectorWidth) return;
			vectorOut[x] = vectorIn1[x] + vectorIn2[x];
		}
	</script>
    <script type="text/javascript">    
    /**
			* Experimenting with OpenCL
			* */
		function loadKernel(id) {
            var kernelElement = document.getElementById(id);
            vectorAdd(kernelElement.text);
            
            if (kernelElement.src != "") {
				var mHttpReq = new XMLHttpRequest();

				mHttpReq.onreadystatechange = function() {
					kernelSource = mHttpReq.responseText;
 
                    vectorAdd(kernelSource);
				};

				mHttpReq.open("GET", kernelElement.src,false);
				mHttpReq.send("NULL");
			}
		}

		function vectorAdd(kernelSource) {
            var output = document.getElementById("output");

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
				cmdQueue.enqueueWriteBuffer (buffin1, false, 0, buffsize, UIVector1);
				cmdQueue.enqueueWriteBuffer (buffin2, false, 0, buffsize, UIVector2);

				var localWS = [8];
				var globalWS = [Math.ceil(vectorLength/localWS) * localWS];

				output.innerHTML += "<br>Global work item size: " + globalWS;
				output.innerHTML += "<br>Local work item size: " + localWS;

				cmdQueue.enqueueNDRangeKernel(kernel, globalWS.length, null, globalWS, localWS);

				outBuffer = new Uint32Array(vectorLength);
				cmdQueue.enqueueReadBuffer(buffout, false, 0 , buffsize, outBuffer);
                cmdQueue.finish();

                output.innerHTML += "<br>Vector1 = ";
                for (var i = 0; i < vectorLength; i++) output.innerHTML += UIVector1[i] +", ";

                output.innerHTML += "<br>Vector2 = ";
                for (var i = 0; i < vectorLength; i++) output.innerHTML += UIVector2[i] +", ";

                output.innerHTML += "<br>Result = ";
                for (var i = 0; i < vectorLength; i++) output.innerHTML += outBuffer[i] +", ";

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

