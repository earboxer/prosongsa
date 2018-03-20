<?php

$files = scandir('.');

foreach( $files as $file ){
	if ( strpos($file, '.php') !== FALSE || strpos($file, '.js') !== FALSE )
	{
		echo "<h3>$file</h3>";
		echo '<pre>';
		echo htmlspecialchars(file_get_contents($file));
		echo '</pre>';
	}
}
