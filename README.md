# Web Code Editor

A web based code editor meant to be used on your local development environment.


## Installation

Clone this repository using 

	git clone https://github.com/vinnizworld/code_editor.git editor

cd into editor directory and fetch all sub modules recursively.

	cd editor
	git submodule update --init --recursive

Make `code.txt` writable.
	
	chmod 777 code.txt


## Features
- PHP, HTML, CSS, JavaScript Compilation
- Quickly try code snippets
- [SCSS](http://sass-lang.org/) Support
- Full [Emmet](http://docs.emmet.io) integration


## Shortcut Keys
- `ctrl+enter` Compile code and see output
- `ctrl+d` Remove current line
- `ctrl+shift+d` Duplicate current line

Feel free to try [emmet](http://docs.emmet.io) awesomeness and [ace keyboard shortcuts](https://github.com/ajaxorg/ace/wiki/Default-Keyboard-Shortcuts).
