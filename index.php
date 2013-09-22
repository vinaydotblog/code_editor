<?php

	// Turn on Error Reporting
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	
	// Helper function
	function pr($arg){ echo "<pre>"; print_r($arg); echo "</pre>"; }

	// Show me output that's it
	if(isset($_GET['p'])){
		if(file_exists('code.txt') && is_writable('code.txt')){	include 'code.txt';	}
		else{	$path = pathinfo(__FILE__);
				echo "Create a writable file <b>code.txt</b> in '{$path['dirname']}' directory.";
			}

		exit;
	}

	// Init URI and Base Path
	$uri = isset($_SERVER['PATH_INFO']) ? explode('/',$_SERVER['PATH_INFO']) : array();
	preg_match('/(.+)index.php/',$_SERVER['PHP_SELF'], $match );
	$base = $match[1];

	// Just Reset the page
	if(isset($_GET['r'])){
		file_put_contents('code.txt', '');
		header('Location: '. $base);
		exit();
	}

	// Useful vars 
	$code = ''; // will contain final code

	function slug($title){ return preg_replace("/[^a-zA-Z0-9]+/", "-", strtolower($title)); }
	// Get if available in post
	if(isset($_POST['code'])){
		$code = $_POST['code'];
	}

	// Put if yes get if no! we want code!
	if($code){
		$reg = '/<style s[ac]ss>?([\S\s]+)<\/style>/';
		$sass_path = 'scss/SassParser.php';
		preg_match_all($reg, $code, $mc);
		if( isset($mc[1][0]) && file_exists($sass_path) ) // Handle Sass Compilation
		{
			require_once $sass_path;
			file_put_contents('tmp.scss', $mc[1][0]);
			$sass = new SassParser(array('style'=>'compressed'));
			$scss = $sass->toCSS('tmp.scss');
			$code = preg_replace($reg, "<style>\n\t" . $scss . "\n  </style>" , $code);
		}
		file_put_contents('code.txt',$code) ;
	}
	// if($code){ file_put_contents('code.txt', $code); }
	else{ $code = file_get_contents('code.txt'); }

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Programming Editors</title>
	<style type="text/css">
		body{overflow:hidden;margin:0}
		header{background-color: #4d4d4d; background-image:-webkit-linear-gradient(top,#03416a48%,#25547b74%,#03416a42%);height:50px;-moz-box-shadow:0 0 5px #333;-webkit-box-shadow:0 0 5px #333;-o-box-shadow:0 0 5px #333;box-shadow:0 0 5px #333}
		header #logo{font-size:2em;font-weight:700;color:#FFF;font-style:italic;font-family:Halvatica;margin-left:20px}
		#main{position:absolute;height:94%;width:100%; min-height: 300px;}
		#editor,#processed{display:block;}
		#processed{ height: 60%; }

		#code,#editor form{height:100%}
		.controls{position:absolute;right:50px}
		#code{width:100%;font-family:consolas,monaco,'courier new', serif;font-size:15px;
		outline:none;padding-top:5px;border:5px solid #550505;-webkit-box-sizing:border-box;
		height: 300px;
		box-shadow:0 0 4px #201F1F;resize:none;background:#eee;color:#5E0303; tab-size: 3;}
		#editor{background-color:#eee}
		#processed iframe{width:100%;height:100%;border:10px solid #eee;-webkit-box-sizing:border-box}
		input[type=submit]{margin:4px}
		#update{ display: none; }
		#gists{ position: absolute; right: 20px; top: 20px; display: none; }

		a.home{ color: white; float: right; font-size: .5em; margin: 5px; }
	</style>
	<link rel="stylesheet" href="<?php echo $base; ?>codemirror/codemirror.css">
	</head>
<body>	
	<header>
		<div id="logo">editor
		<a href="https://github.com/vinnizworld/code_editor" target="_blank" class="home">Help</a>
		</div>
		
	</header>	
	<div id="main">
		<div id="editor">
			<form action="" method="post">
				<div id="code"><?php echo htmlentities($code); ?></div>
				<!-- <textarea clss="html" name="code" id="code" cols="140" rows="10"></textarea> -->
				<div class="controls">
					<input type="submit" id="btn" value="Process"><a href="?p">Processed</a>
				</div>
		    </form>
		</div>

		<div id="processed">
			<iframe id="frame" src="?p" frameborder="0"></iframe>
			<div class="live"></div>
		</div>
	</div>

	<script type="text/javascript">

		var BASE = '<?php echo $base; ?>';

		var code = document.getElementById('code'),
		form = document.forms[0],
		gists = document.getElementById('gists');

		fr = document.getElementById('frame');
		var ajax = function(h,b,c){var e=Object.prototype.toString,a,d="",f="[object Function]"===e.call(b)?"GET":"POST",c="[object Function]"===e.call(c)?c:!1;a=window.XMLHttpRequest?new XMLHttpRequest:new ActiveXObject("Microsoft.XMLHTTP");a.onreadystatechange=function(){4<=a.readyState&&200==a.status&&("POST"===f?c&&c(a.responseText):b(a.responseText))};if("POST"==f&&"[object Object]"===e.call(b)){for(var g in b)d+=g+"="+encodeURIComponent(b[g])+"&";d=d.slice(0,-1)}a.open(f,h,!0);a.setRequestHeader("Content-type","application/x-www-form-urlencoded");a.send(d)}

		form.addEventListener('submit',function(e){
			ajax('',{ code: editor.getValue() },function(){ fr.src = '?p';	});
			e.preventDefault();
		});

		document.addEventListener('keyup', function(e){
			if( e.ctrlKey && e.keyCode === 13 )
			{
				e.preventDefault();
				ajax('',{ code: editor.getValue() },function(){ fr.src = '?p';	});
				// code.innerHTML = safe_tags_replace( editor.getValue() );
			}
		});

		gists && gists.addEventListener('change',function(){
			var opt = this.item( this.selectedIndex );
			window.location.href = BASE + '/' + this.value + '/' + this.dataset.title; 
		});

		gists && (gists.style.display = 'inline');

	</script>

	<script type="text/javascript" src="<?php echo $base; ?>acebuilds/src-noconflict/ace.js" ></script>
	<script type="text/javascript" src="<?php echo $base; ?>acebuilds/src-noconflict/ext-emmet.js" ></script>
	<script type="text/javascript" src="<?php echo $base; ?>emmet.ace.js" ></script>
	<script type="text/javascript">
		var Emmet = ace.require("ace/ext/emmet");
	    Emmet.setCore(window.emmet);
		var editor = ace.edit('code');
	    editor.setOption("enableEmmet", true);
		editor.setTheme('ace/theme/dawn');
		editor.getSession().setMode('ace/mode/php');
		editor.getSession().setUseWrapMode(true);
		var tagsToReplace = {
		    '&': '&amp;',
		    '<': '&lt;',
		    '>': '&gt;'
		};

		function replaceTag(tag) {
		    return tagsToReplace[tag] || tag;
		}

		function safe_tags_replace(str) {
		    return str.replace(/[&<>]/g, replaceTag);
		}
	</script>
</body>
</html>
