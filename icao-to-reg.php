<?php

# this script converts a 6 character  hexadecimal U.S. civilian
# ICAO address to it's corresponding registration (tail) number
#
# this script will also convert a U.S. civilian registration (tail) number
# to it's corresponding  6 character ICAO code.
#
# these are just calculations and the  tail/ICAO number may not currently be in use
# 
# the tail/ICAO number can either be supplied as a command line argument or if omitted on # the command line, as input when prompted
#
# Paul Shelton May 2023
# updated October 2024 to include tail to ICAO conversions
#

$reg = "N";
$letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
$len1 = 101711; // number of possible registrations beginning with a given digit
$lenAlpha = 25;
$length = array(10111,951,35,1,1);
$skip = array(601,601,601,25,1);

if (array_key_exists(1,$argv)) {

 $inString = $argv[1];

} else {

 echo "Enter a 6 character ICAO code or a registration number including the leading 'N': ";
 $inStringandle = fopen ("php://stdin","r");
 $inString = fgets($inStringandle);
 fclose($inStringandle);
 echo "\n";

}

$inString = strtoupper(trim($inString));

if (substr($inString,0,1) == "N") {

#
#  Convert a registration to an ICAO 24 bit address
#

$l = strlen($inString);
if ($l < 2 || $l > 6 || !preg_match("/^N[1-9][0-9]?[0-9]?[0-9]?[0-9]?[A-HJ-NP-Z]?[A-HJ-NP-Z]?$/",$inString)) {

   echo "Invalid U.S. civilian registration\n\n";

} else {

  $d = hexdec('A00001');  // base address for U.S. aircraft
  $d = $d + (ord(substr($inString,1,1)) - 49) * $len1;

  for ($x=2;$x<$l;$x++) {

  $ord = ord(substr($inString,$x,1));
  if ($ord < 58) {

    $ord = $ord - 48;
      $d = $d + $skip[$x -2];; // bump past alphas
    $d = $d + $ord * $length[$x - 2];

  } else {

    $pos = strpos($letters,$inString[$x],1);

if (is_numeric(substr($inString,$x - 1,1)) &&$x < 5) {
      $d = $d + 1 +$pos * $lenAlpha;

    } else {

      $d = $d + 1 +$pos;

    }
  }
}

echo strtoupper(dechex($d)) . "\n";

}
exit(0);
}

#
#  Convert a ICAO 24 bit address to a registration (tail) number
#

if (!preg_match("/^[0-9A-F]{6}$/",$inString)) {

 echo "Invalid ICAO code\n\n";
 exit(2);

}

if ($inString < "A00001" || $inString > "ADF7C7") {

 echo "Not a U.S. civilian ICAO code\n\n";
 exit(3);

}

# process numeric portion of registration

$h = substr($inString,1,5);
$d1 = hexdec($h) - 1;
$d2 = floor($d1/$len1);

# add "1" because first digit can only be 1 - 9, 0 is not allowed
$reg .= (string)($d2 + 1);

$d1 = $d1 - ($d2 * $len1);  // normalize
$index = 0;

while ($d1 >= $skip[$index]) {

 $d1 = $d1 - $skip[$index];
 $d2 =floor($d1/$length[$index]);
 $reg .= (string)$d2;
 $d1 = $d1 - ($d2 * $length[$index]);
 $index++;

}

# process alpha portion of registration

if ($d1 > 0) {

 $d1 = $d1 - 1;

 if ($index == 3) {

  $reg .= substr($letters,$d1,1);

 } else {

  $reg .= substr($letters,floor($d1/$lenAlpha),1);
  $d3 = $d1%$lenAlpha;

  if ($d3 != 0) {
   $reg .= substr($letters,$d3 - 1,1);

  }
 }
}

echo "\n" . $reg . "\n\n";

?>
