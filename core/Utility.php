<?php
function dashesToCamelCase($string, $capitalizeFirstCharacter = false) 
{
	if (!$string) {
		return $string;
	}
    $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));

    if (!$capitalizeFirstCharacter) {
        $str[0] = strtolower($str[0]);
    }

    return $str;
}