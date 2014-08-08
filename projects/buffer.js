//Worker script to process the larger fractal.
//This is an alternative to using threads in a 
//compiled environment.

var toSend;			//Contains data to send to main
var lMinR, lMaxR, lMinI, lMaxI;	//Min/maximums for window
var MinR, MaxR, MinI, MaxI;	//Min/maximums for fractal
var val, z;			//Complex numbers for cycle counting
var count = 0;
var RENDER_DISTANCE;
var dimX, dimY;
var iterations, rmax;

self.addEventListener('message', function(e) {
	var data = e.data;
	MinR = data.MinR;
	MaxR = data.MaxR;
	MinI = data.MinI;
	MaxI = data.MaxI;
	dimX = data.dimX;
	dimY = data.dimY;
	iterations = data.iterations;
	rmax = data.rmax;

	RENDER_DISTANCE = data.RENDER_DISTANCE;
	toSend = new Uint32Array(RENDER_DISTANCE*RENDER_DISTANCE*
				 dimX*dimY*Uint32Array.BYTES_PER_ELEMENT);

	bufferData();	
}, false);

function complex(r, i) {
	this.real = r;
	this.imaginary = i;
};

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

//This is almost a line by line port of my code from
//Fractal-Awesome (github) method (void thread(void* simon))
//
//Variables I need to move over from the onmessage to the worker:
//	MinR
//	MaxR
//	MinI
//	MaxI
//	RENDER_DISTANCE
//	dimX, dimY
function bufferData() {
	var mult = (RENDER_DISTANCE-1)/2;

	lMinR = MinR - (MaxR - MinR)*mult;
	lMaxR = MaxR + (MaxR - MinR)*mult;
	lMinI = MinI - (MaxI - MinI)*mult;
	lMaxI = MaxI + (MaxI - MinI)*mult;

	var ref = (lMaxR - lMinR)/(dimX*RENDER_DISTANCE);
	var imf = (lMaxI - lMinI)/(dimY*RENDER_DISTANCE);

	var progress = 0;
	var val, z;
	var count;
	var color;

	for (var x = 0; x < dimX*RENDER_DISTANCE; x++) {
		progress = 100.0 * (x / (dimX * RENDER_DISTANCE));
		
		for (var y = 0; y < dimY*RENDER_DISTANCE; y++) {
			val = new complex(lMinR + x*ref, lMaxI - y*imf);
			z = new complex(val.real, val.imaginary);
			count = 0;

			while (count < iterations && z.real + z.imaginary < rmax) {
				var tmp = z.square();
				z.add(val);
				count++;
			}
			
			//This is building the array in a function direction
			//Will correct later
			color = (count == iterations)?0:0xFFFFFF*(count/iterations);
			toSend[x + y*dimX*RENDER_DISTANCE] = color;
		}
	}
	console.log(toSend[20000]);

	self.postMessage({
		'type': 'data',
		'buffer': toSend.buffer
	}, [toSend.buffer]);
}
