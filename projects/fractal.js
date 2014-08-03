var canvas  = document.getElementById("fractal");
var context = canvas.getContext("2d");
var canvasData;

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
	this.real = this.real * a.real +
			this.imaginary * a.imaginary;
	this.imaginary = this.imaginary * a.real +
			this.real * a.imaginary;
};

complex.prototype.square = function() {
	this.real = this.real * this.real +
			this.imaginary * this.imaginary;
	this.imaginary = 2 * this.real * this.imaginary;
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

	update: function() {
		if (!fractal.initted) {
			canvasData = context.createImageData(fractal.width, fractal.height);
			fractal.initted = 1;
		}
		if (fractal.initted < 2) {
			for (var x = 0; x < fractal.width; x++) {
				for (var y = 0; y < fractal.height; y++) {
					var tmp = fractal.getColor(x, y);
					var r = tmp/(256*256);
					var g = (tmp - (r | 0)*256*256)/256;
					var b = (tmp - (r | 0)*256*256 - (g | 0)*256);
					fractal.color(x, y, r, g, b, 256);
				}
			}
	
			context.putImageData(canvasData, 0, 0);

			saveImg();
			fractal.initted = 2;
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

fractal.update();