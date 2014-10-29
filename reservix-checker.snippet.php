<?php
/*
 * Description: Checks for reserved (sold) tickets on reservix.
 * 		It's intended for use with wordpress, but can also used in other ways.
 * Version: 1.1
 * Author: don
 * License: GPL2
 *
 * Copyright 2014 don  (email : don_philipe@gmx.de)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

$url_base = "http://www.reservix.de/";
$url_suffix = "";	# insert permalink suffix here. It's something like "tickets-<name-of-concert>-<place-of-concert>-<date-of-concert>/e<id>"
$url = "$url_base$url_suffix";
$count = 0;

$html = @fopen($url, "r");
if(!$html)
{
	$count = "Website not available!";
}
else
{
	# search for the link (button) to the seat selection:
	while(!feof($html)) 
	{
		$line = fgets($html); 
		if(preg_match('@http://www\.reservix\.de/reservation/plan_reservation_back\.php\?PHPSESSID=[0-9a-f]+&amp;eventID=[0-9]+&amp;eventGrpID=[0-9]+&amp;presellercheckID=[0-9]@', $line, $hits)) 
		{
			$url = $hits[0];
			break;
		}
	}
	# replace for working url:
	$url = preg_replace('@amp;@', "", $url);

	$html = @fopen($url, "r");
	if(!$html)
	{
		$count = "Website not available!";
	}
	else
	{
		# search occurences of reserved seats:
		while(!feof($html)) 
		{
			$line = fgets($html); 
			$count = $count + preg_match_all('/class="BG1 ds X1"/', $line, $matches);	# $matches for compatibility with php versions < 5.4.0
			$count = $count + preg_match_all('/class="ds BG1 X1"/', $line, $matches);	# $matches for compatibility with php versions < 5.4.0
		}

		# subtract 5 for the seats for disabled persons (they are reserved everytime):
		#$count = $count - 5;
	}
}
$count = preg_replace('[!0-9]', "", $count);
echo $count;

?>
