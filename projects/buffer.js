//Worker script to process the larger fractal.
//This is an alternative to using threads in a 
//compiled environment.

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

var toSend = new Array();	//Contains data to send to main
var lMinR, lMaxR, lMinI, lMaxI;	//Min/maximums for window
var val, z;			//Complex numbers for cycle counting
var count = 0;
var RENDER_DISTANCE = 5;

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

	for (var x = 0; x < dimX*RENDER_DISTANCE; x++) {
		for (var y = 0; y < dimY*RENDER_DISTANCE; y++) {

		}
	}
}
