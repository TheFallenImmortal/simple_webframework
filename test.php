<?php
$arr = [1, 3, 7, 2, 'test' => 5, 4];
foreach ($arr as $key => $value) {
	$key = $value;
	print $value;
}