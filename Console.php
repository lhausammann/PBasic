<?php
namespace PBasic;

error_reporting(E_ALL);
ini_set("display_errors", true);
require('vendor/autoload.php');

use PBasic\Interpreter\Basic;
use PBasic\Interpreter\Cmd\AbstractStatement;
use AbstractBlockStatement;



function fileSelect($files, $selectedIndex = 0) {
    $select = "<label for='files'>Load...</label><select name='files'>";
    foreach ($files as $i => $file) {
        $selectedAttr = ($selectedIndex === $i) ? ' selected="selected"' : '';
        $select .= "<option value=$i" . $selectedAttr . ">" . $file . "</option>";
    }
    $select .="</select>";
    $select .="<input type='submit' value='go'>";

    return $select;
}

function getFiles($src = './src/', $ext = 'bas') {
    $files = array();
    $iterator = new \DirectoryIterator($src);
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $info = pathinfo($src . $file->getFilename());

            if ($info['extension'] == $ext)
                $files[] = $file->getFilename();
        }
    }

    return $files;
}

function getFileToRun($nr, $src = './src/', $ext = 'bas') {
    $files = getFiles($src, $ext);
    return $src . $files[$nr];
}

function drawSelector($src = './src', $selectedIndex) {
    echo fileSelect(getFiles($src, 'bas'), $selectedIndex);
}


session_start();
$nr = isset($_GET['files']) ? $_GET['files'] : 0;
if (! isset ($_GET['files'])) {
    $nr = isset($_SESSION['f']) ? $_SESSION['f'] : 0;
}

$_SESSION['f'] = $nr;
$file = getFileToRun($nr);






?>
<html>
<head>
    <style>
        * {
            margin: 0px;
            padding:0px;
        }
        body {
            background-color:black;

            color: white;
            width: 100%;
            height: 100%;
        }
        select, input[type=submit], h1, label {
            border: none;
            color: lightgreen;
            font-family: ‘Lucida Console’, Monaco, monospace;
            background-color: black;
            height: 40px;
            top: auto;
            display: block;
            float:left;
        }
        input[type=submit], h1 {
            padding-right: 5em;
            padding-left: 2%;
        }

        .blink {
            text-decoration: blink;
        }


        .clear {
            color: lightgreen;
            clear: both;
            margin-bottom: 5px;
        }
        #console {
            width: 80%;
            padding-left: 2%;
            padding-right: 2%;
        }
        pre, input, .print {
            font-size:24px;
            font-style: bold;
            display:block;
            font-family: ‘Lucida Console’, Monaco, monospace;
        }
        .print {
            padding-left: 15px;
        }
    </style>
</head>
<body>
<form name="Basic" class="controls">
    <h1 class="blink">Welcome to PBASIC</h1>

    <? drawSelector('./src/', $nr);?>
</form>
<hr class="clear" />

<div id="console">
    <? echo Basic::run($file);?>
</div>
</body>
</html>