var canvas  = document.getElementById("fractal");
var context = canvas.getContext("2d");
var canvasData;

var keyArray = [0, 0];

function saveImg() {
	var img = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
	window.location.href = img;
}

function complex(r, i) {
	this.real = r;
	this.imaginary = i;
}

complex.prototype.add = function(a) {
	this.real += a.real;
	this.imaginary += a.imaginary;
};

complex.prototype.multiply = function(a) {
	var realTmp = this.real * a.real -
			this.imaginary * a.imaginary;
	this.imaginary = this.imaginary * a.real +
			this.real * a.imaginary;
	this.real = realTmp;
};

complex.prototype.square = function() {
	var realTmp  = this.real * this.real -
			this.imaginary * this.imaginary;
	this.imaginary = 2 * this.real * this.imaginary;
	this.real = realTmp;
};

fractal = {
	initted: 0,
	width: canvas.width,
	height: canvas.height,
	fps: 60,
	MinR: -2.0,
	MaxR: 2.0,
	MinI: -2.0,
	MaxI: 2.0,
	iterations: 100,
	rmax: 30,
	mouse: {x: 0, y: 0},
	mouseStart: {x: 0, y: 0},
	mouseDown: 0,
	thread: 0,
	RENDER_DISTANCE: 5,
	progress: 0,
	offX: 0,
	offY: 0,

	bufferedDisplay: 0,

	update: function() {
		if (!fractal.initted) {
			var RENDER_DISTANCE = fractal.RENDER_DISTANCE
			fractal.offX = fractal.width*(RENDER_DISTANCE-1)/2;
			fractal.offY = fractal.height*(RENDER_DISTANCE-1)/2;

			context.font = "bold 15px Verdana";
			fractal.thread = new Worker("projects/buffer.js");
			/*bufferedDisplay = new Uint32Array(RENDER_DISTANCE*RENDER_DISTANCE*
						fractal.width*fractal.height*Uint8ClampedArray.BYTES_PER_ELEMENT);*/

			//Tasks for very first frame
			fractal.thread.addEventListener('message', function(e) {
				var data = e.data;
				switch (data.type) {
					case 'data':
						console.log("Received Buffer");
						fractal.bufferedDisplay = new Uint32Array(data.buffer);
						console.log(fractal.bufferedDisplay[20000]);
						fractal.progress = 1;
						break;
					default:
						console.log("Unrecognized transmission");
				};
			}, false);
			fractal.thread.postMessage({
				'MinR': fractal.MinR,
				'MaxR': fractal.MaxR,
				'MinI': fractal.MinI,
				'MaxI': fractal.MaxI,
				'dimX': fractal.width,
				'dimY': fractal.height,
				'iterations': fractal.iterations,
				'rmax': fractal.rmax,
				'RENDER_DISTANCE': RENDER_DISTANCE
			});

			canvasData = context.createImageData(fractal.width, fractal.height);
			fractal.initted = 1;
			
			fractal.bufferedDisplay = new Uint32Array(RENDER_DISTANCE*RENDER_DISTANCE*
						fractal.width*fractal.height*Uint32Array.BYTES_PER_ELEMENT);
			for (var x = 0; x < fractal.width*RENDER_DISTANCE; x++) {
				for (var y = 0; y < fractal.height*RENDER_DISTANCE; y++) {
					fractal.bufferedDisplay[x+y*fractal.width*RENDER_DISTANCE] =
						(x >= fractal.offX && y >= fractal.offY &&
						 x < fractal.offX + fractal.width &&
						 y < fractal.offY + fractal.height)?
						 	fractal.getColor(x-fractal.offX, y-fractal.offY):
							0x333333;
				}
			}
		}
		if (fractal.initted < 2) {
			for (var x = 0; x < fractal.width; x++) {
				for (var y = 0; y < fractal.height; y++) {
					var tmp = fractal.bufferedDisplay[(x+fractal.offX) + 
						(y+fractal.offY)*fractal.width*fractal.RENDER_DISTANCE];
					var r = tmp/(256*256);
					var g = (tmp - (r | 0)*256*256)/256;
					var b = (tmp - (r | 0)*256*256 - (g | 0)*256);
					fractal.color(x, y, r, g, b, 256);
				}
			}
	
			context.putImageData(canvasData, 0, 0);
			context.fillStyle = "#FFFFFF";
			if (!fractal.progress) context.fillText("Loading Buffer", 0, 15);

			//saveImg();
		}
		setTimeout(fractal.update, 10);
	},

	color: function(x, y, r, g, b, a) {
		var index = (x + y*canvasData.width) * 4;
		canvasData.data[index + 0] = r;
		canvasData.data[index + 1] = g;
		canvasData.data[index + 2] = b;
		canvasData.data[index + 3] = a;
	},

	getColor: function(x, y) {
		var ref = (fractal.MaxR - fractal.MinR)/fractal.width;
		var imf = (fractal.MaxI - fractal.MinI)/fractal.height;

		var val = new complex(fractal.MinR+x*ref, fractal.MaxI - y*imf);
		var z = new complex(val.real, val.imaginary);
		var count = 0;
		while (count < fractal.iterations && z.real + z.imaginary < fractal.rmax) {
			var tmp = z.square();
			z.add(val);
			count++;
		}
		//console.log(count);

		return ((count == fractal.iterations)?0:0xFFFFFF*(count/fractal.iterations));
	}
};

function processMouse(e) {
	fractal.mouseDown = 1;

	fractal.mouseStart = canvas.relMouseCoord(e);
}

function endMouse(e) {
	fractal.mouseDown = 0;
}

function moveMouse(e) {
	/*fractal.mouse.x = canvas.relMouseCoord(e).x;
	fractal.mouse.y = canvas.relMouseCoord(e).y;*/
	
	fractal.mouse = canvas.relMouseCoord(e);
	
	if (fractal.mouseDown) {
		var shiftR = (fractal.mouse.x - fractal.mouseStart.x)/fractal.width * 
			(fractal.MaxR - fractal.MinR);
		var shiftI = (fractal.mouse.y - fractal.mouseStart.y)/fractal.height*
			(fractal.MaxI - fractal.MinI);

		fractal.MinR -= shiftR; fractal.MaxR -= shiftR;
		fractal.MinI += shiftI; fractal.MaxI += shiftI;

		fractal.offX -= fractal.mouse.x - fractal.mouseStart.x;
		fractal.offY -= fractal.mouse.y - fractal.mouseStart.y;
	}

	fractal.mouseStart = fractal.mouse;
}

function processKey(e) {
	if (e.keyCode == 82) keyArray[0] = 1;	//R
}

function endKey(e) {
	if (e.keyCode == 82) keyArray[0] = 0;	//R
}

function relMouseCoord(e) {
	/*var canvasX = 0;
	var canvasY = 0;
	var totalOffsetX = 0;
	var totalOffsetY = 0;
	var currentElement = this;
	
	while (currentElement = currentElement.offsetParent) {
		totalOffsetX += currentElement.offsetLeft - currentElement.scrollLeft;
		totalOffsetY += currentElement.offsetTop - currentElement.scrollTop;
	}

	canvasX = e.pageX - totalOffsetX;
	canvasY = e.pageY - totalOffsetY;
	
	return {x:canvasX, y:canvasY};*/
	return {x:e.pageX - this.offsetLeft, y:e.pageY - this.offsetTop};
}

HTMLCanvasElement.prototype.relMouseCoord = relMouseCoord;

this.canvas.addEventListener('mousedown', this.processMouse, false);
this.canvas.addEventListener('mouseup', this.endMouse, false);
this.canvas.addEventListener('mousemove', this.moveMouse, false);

addEventListener('keydown', this.processKey, false);
addEventListener('keyup', this.endKey, false);

fractal.update();
