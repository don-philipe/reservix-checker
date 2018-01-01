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

# Some API endpoints (to retrieve information about an event)
# ==================
#
# Get the location of the event:
# https://www.reservix.de/ajax/seating-map/1071977/region
# 	1071977 is the event ID (see end of permalink to the event)
#
# Get information about the pricing of an event:
# https://www.reservix.de/ajax/seating-map/1071977/pricecategory
#
# Get information about the seats of an event:
# https://www.reservix.de/ajax/seating-map/33590/seat/1071977
# 	33590 is the location ID of the "Hochschule für Musik Dresden"
#

$eventID = "1071977";
$cantbesold = 0;	# number of seats the cant be sold, so remove them from the sum of all seats

$url_base = "https://www.reservix.de/";
$url_suffix = "tickets-hfmdd-jazz-orchestra-feat-sebastian-merk-in-dresden-hochschule-fuer-musik-konzertsaal-am-25-1-2018/e1071977";	# insert permalink suffix here. It's something like "tickets-<name-of-concert>-<place-of-concert>-<date-of-concert>/e<id>"
$action_getlocation = "ajax/seating-map/$eventID/region";

$url = "$url_base$action_getlocation";
$result = json_decode(postwoparam($url), true);		# second parameter lets json_decode() return an array
$locationID = $result[0]['id'];

$action_getseats = "ajax/seating-map/$locationID/seat/$eventID";
$url = "$url_base$action_getseats";
$result = json_decode(postwoparam($url), true);
$seats_total = 0;
$seats_free = 0;
foreach($result as $item) {
	$seats_total++;
	if($item['free']) {
		$seats_free++;
	}
}
$seats_total = $seats_total - $cantbesold;
$seats_sold = $seats_total - $seats_free;

echo "seats total: $seats_total";
echo "\n";
echo "seats free: $seats_free";
echo "\n";
echo "seats sold: $seats_sold";

# the action should be GET here
function postwoparam($url) {
	$options = array(
	    'http' => array(
		'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		'method'  => 'POST',
		'content' => ''
	    )
	);
	$context = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) {
		/* Handle error */ 
	} else {
		return $result;
	}
}

exit();
?>
