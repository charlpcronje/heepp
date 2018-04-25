<?php
$settings = json_encode(include __DIR__.'/'.'settings.php',JSON_PRETTY_PRINT);
file_put_contents('settings.json',$settings);
die($settings);