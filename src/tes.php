<?php
$array = ['name' => 'John', 'email' => 'john@gmail.com'];
$string = 'Hi [[name]], your email is [[email]]';

// create array of regex using array keys
$rearr = array_map(function($k) { return '/\[\[' . $k . ']]/'; },
         array_keys($array));

# pass 2 arrays to preg_replace
echo preg_replace($rearr, $array, $string) . "\n";
?>
