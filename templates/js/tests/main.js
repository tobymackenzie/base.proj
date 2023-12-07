/* global QUnit */
import main from './_src/main.js';

//--name module
QUnit.module('main');

//--define tests
QUnit.test('create', function(assert){
	//==setup

	//==test
	assert.ok(main.foo());
});
