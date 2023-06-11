<?php

# this script converts a 6 character  hexadecimal U.S. civilian
# ICAO address to it's corresponding registration (tail) number
#
# this is just a ICAO address to registration calculation,
# the resulting registration may not currently be in use
#
# the hex code can either be supplied as a command line argument or if omitted on the command line,
# as input to the script
#
# Paul Shelton May 2023
#

$reg = "N";
$letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
$len1 = 101711; // number of possible registrations beginning with a given digit
$lenAlpha = 25;
$length = array(10111,951,35,1,1);
$skip = array(601,601,601,25,1);

if (array_key_exists(1,$argv)) {

 $h = $argv[1];

} else {

 echo "Enter 6 character U.S. civilian ICAO address: ";
 $handle = fopen ("php://stdin","r");
 $h = fgets($handle);
 fclose($handle);
 echo "\n";

}

$h = strtoupper(trim($h));

if (!preg_match("/^[0-9A-F]{6}$/",$h)) {

 echo "Invalid ICAO code\n\n";
 exit(2);

}

if ($h < "A00001" || $h > "ADF7C7") {

 echo "Not a U.S. civilian ICAO code\n\n";
 exit(3);

}

# process numeric portion of registration

$h = substr($h,1,5);
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

echo $reg . "\n\n";

?>
