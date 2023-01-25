<!doctype html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <style>
        html, body {
            margin: 0;
            height: 100%;
        }

        textarea {
            width: 100%;
            height: 100%;
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
<textarea>
<?php
    exec('./vendor/bin/phpunit --verbose tests', $output);
    foreach( $output as $str ) {
        echo $str . "\n";
    }
?>
</textarea>
</body>
</html>
