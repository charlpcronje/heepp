<?php

	require_once('VFXP.inc.php');
	
	$xmldoc =& new VFXP_Document();
	$xmldoc->parseFromFile('test.xml');
	
	$rootelem =& $xmldoc->rootElement();
	
	
	function spaces($n) {
		$out = '';
		for ($p = 0; $p < $n; ++$p) {
			$out .= ' ';
		}
		return $out;
	}
	function recursive_print(&$element, $level = 0) {
		$out = '';
		$out .= spaces($level*8) . 'Begin Element' . "\n";
		$out .= spaces($level*8) . '-------Name: ' . $element->name() . "\n";
		$out .= spaces($level*8) . '------Value: ' . $element->value() . "\n";
		$out .= spaces($level*8) . '-Attributes: ' . "\n";
		foreach ($element->attributes() as $key => $val) {
			$out .= spaces($level*8+8) . "[{$key}] => $val" . "\n";
		}
		$out .= spaces($level*8) . '---Children: ' . "\n";
		foreach ($element->children() as $child) {
			$out .= recursive_print($child, $level + 1);
		}
		$out .= spaces($level*8) . 'End Element' . "\n";
		return $out;
	}
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>VFXP Test</title>
</head>

<body>

<pre>
<?php

//print_r($rootelem);

echo recursive_print($rootelem);

?>
</pre>

</body>
</html>
