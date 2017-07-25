<?php

//echo "----- ".$switch_name." ----- \n";
include_once("tplink_switch_config.php");
include_once("tplink_switch_lib.php");
include_once("lib/Net_Telnet/Net/Telnet.php");

$router = '10.22.0.134';

var_dump(get_tplink_running_config($router));
var_dump(get_tplink_mac_addresses_raw_str($router));
var_dump(get_tplink_sys_info($router));
var_dump(get_tplink_ports_arr($router));
var_dump(get_tplink_port_short_name($router, 3));
var_dump(get_tplink_port_macs($router, 1));
exit();

?>
