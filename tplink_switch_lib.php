<?php


        include_once("lib/Net_Telnet/Net/Telnet.php");
        
        $router='10.22.0.134';
        
        function get_tplink_login_first_arr ($password) {
         return array(
             'login_prompt'  => 'User:',
             'login_success' => '',
             'login_fail'    => 'Login invalid',
             'login'         => 'admin',
             'password_prompt'   =>  'Password:',
             'password'      => $password,
             'prompt'        => ">",
             'debug'         => false,
             );
        }
        
        function get_tplink_login_enable_arr($enable_secret) {
          return array(
                 'login_prompt'  => '',
                 'login_success' => '',  // reset from previous call
                 'password'      => $enable_secret,
                 'password_prompt' => 'Password:',
                 'prompt'        => "#",
                 'debug'         => true,
                 );
        }
        
        function get_tplink_connect_init_arr ($router) {
          return array ('host'        => $router,
                        'telnet_bugs' => false,
                        'debug'         => false,
                        );
        }
        
        function get_tplink_running_config ($router) {
        
        $password='admin18';
        $enable_secret='admin18';

        
        $login_first = get_tplink_login_first_arr($password);
        $login_enable = get_tplink_login_enable_arr($enable_secret);
        $init_arr = get_tplink_connect_init_arr($router);

        try {
         $t = new Net_Telnet($init_arr);
         $t->connect();

         $t->login($login_first);

         // tp-link page prompt
         $t->page_prompt(' any key to continue (Q to quit)',' ');
    
         # send enable command
         $t->println("enable");
            
         # reuse login() to send enable secret
         $t->login($login_enable);
    
         $switch_name = array_reduce(explode("\n",$t->cmd("show system-info")),function ($acc, $item) {
           if(preg_match("/^[\s]{0,}System Name[\s]{0,}-[\s]{0,}([\w-]{0,})[\s]{0,}\$/i",$item, $matched)) {
             $acc = $matched[1];
           }
           return $acc;
         },[]);
    
         $t->prompt("end");
         $run_cmd= $t->cmd('show running-config');
    
         $t->disconnect();

         } catch (Exception $e) {
          echo "Caught Exception ('{$e->getMessage()}')\n{$e}\n";
         }
         return $run_cmd;

      }
      
      function get_tplink_mac_addresses_raw_str ($router) {
        $password='admin18';
        $enable_secret='admin18';

        $login_first = get_tplink_login_first_arr($password);
        $login_enable = get_tplink_login_enable_arr($enable_secret);
        $init_arr = get_tplink_connect_init_arr($router);                

        try {
         $t = new Net_Telnet($init_arr);
         $t->connect();

         $t->login($login_first);

         // tp-link page prompt
         $t->page_prompt(' any key to continue (Q to quit)',' ');
    
         # send enable command
         $t->println("enable");
            
         # reuse login() to send enable secret
         $t->login($login_enable);
    
         $switch_name = array_reduce(explode("\n",$t->cmd("show system-info")),function ($acc, $item) {
           if(preg_match("/^[\s]{0,}System Name[\s]{0,}-[\s]{0,}([\w-]{0,})[\s]{0,}\$/i",$item, $matched)) {
             $acc = $matched[1];
           }
           return $acc;
         },[]);
    
         $t->prompt("{$switch_name}#");    
         $fdb=$t->cmd('show mac address-table address dynamic');   
    
         $t->disconnect();

         } catch (Exception $e) {
          echo "Caught Exception ('{$e->getMessage()}')\n{$e}\n";
         }
         return $fdb;

       }

       function get_tplink_sys_info ($router) {
        $password='admin18';
        $enable_secret='admin18';

        $login_first = get_tplink_login_first_arr($password);
        $login_enable = get_tplink_login_enable_arr($enable_secret);
        $init_arr = get_tplink_connect_init_arr($router);

        try {
         $t = new Net_Telnet($init_arr);
         $t->connect();

         $t->login($login_first);

         // tp-link page prompt
         $t->page_prompt(' any key to continue (Q to quit)',' ');
    
         # send enable command
         $t->println("enable");
            
         # reuse login() to send enable secret
         $t->login($login_enable);
    
         $switch_info = array_reduce(explode("\n",$t->cmd("show system-info")),function ($acc, $item) {
           $regex_to_match = "/^[\s]{0,}([\w]{0,}[\s]{0,}[\w]{0,})[\s]{0,}-[\s]{0,}(.*)\$/i";
           if(preg_match($regex_to_match,$item, $matched)) {
             $key = strtolower(preg_replace("[\s]","_",$matched[1]));
             $acc[$key] = $matched[2];
           }
           return $acc;
         },[]);
    
    
         $t->disconnect();

         } catch (Exception $e) {
          echo "Caught Exception ('{$e->getMessage()}')\n{$e}\n";
         }
         return $switch_info;

       }

       function get_tplink_ports_arr ($router) {
        $password='admin18';
        $enable_secret='admin18';

        $login_first = get_tplink_login_first_arr($password);
        $login_enable = get_tplink_login_enable_arr($enable_secret);
        $init_arr = get_tplink_connect_init_arr($router);

        try {
         $t = new Net_Telnet($init_arr);
         $t->connect();

         $t->login($login_first);

         // tp-link page prompt
         $t->page_prompt(' any key to continue (Q to quit)',' ');
    
         # send enable command
         $t->println("enable");
            
         # reuse login() to send enable secret
         $t->login($login_enable);
    
         $interfaces_arr = array_reduce(explode("\n",$t->cmd("show interface description")),
            function ($acc,$item) {
                $regex="/^[\s]{0,}([a-z0-9]+\/[0-9]+\/[0-9]+)[\s]+([\w]+)[\s]{0,}([a-z0-9\-\.]{0,})[\s]{0,}\$/i";
                if(preg_match($regex,$item,$matches)) {
                  $acc[]=["iface"=>$matches[1],"status"=>$matches[2],"desc"=>$matches[3]];
                }
                return $acc;

            },[0]);
            
         $t->println("logout");
         $t->disconnect();
         

         } catch (Exception $e) {
          echo "Caught Exception ('{$e->getMessage()}')\n{$e}\n";
         }
         return $interfaces_arr;

       }

       function get_tplink_port_short_name($router, $portNum) {
         $arr=get_tplink_ports_arr($router);
         return $arr[$portNum]["iface"];
       }
       
       function convert_tplink_short_port_to_long($s) {
          $patterns[]="/Fa/";$replacements[]="fastEthernet ";
          $patterns[]="/Gi/";$replacements[]="gigabitEthernet ";
                                   
          return preg_replace($patterns, $replacements, $s);
       }
       
       function convert_tplink_mac_long_string_to_arr($string) {
      
          $retArr=array_reduce(explode("\n",$string), function ($acc,$item) {
              if(preg_match("/^([0-9a-z\:]{1,})[\s]+([0-9]+)[\s]+([0-9a-z\/]+)[\s]{0,}dynamic.*\$/xi",$item,$matches)) {
                $acc[]=$matches[2]." ".$matches[1]." ".$matches[3];
              }
              return $acc;
          },[]);
          
          return array_unique($retArr);
         
       }  
       
                                                 
       function get_tplink_port_long_name($router, $portNum) {
         return convert_tplink_short_port_to_long(get_tplink_port_short_name($router, $portNum));
       }
       
       function get_tplink_port_macs($router, $portNum) {
        
         
        $password='admin18';
        $enable_secret='admin18';					

        $login_first = get_tplink_login_first_arr($password);
        $login_enable = get_tplink_login_enable_arr($enable_secret);
        $init_arr = get_tplink_connect_init_arr($router);                
        $fdb="";

        try {
           $t = new Net_Telnet($init_arr);
           $t->connect();

           $t->login($login_first);

           // tp-link page prompt
           $t->page_prompt(' any key to continue (Q to quit)',' ');
    
           # send enable command
           $t->println("enable");
            
           # reuse login() to send enable secret
           $t->login($login_enable);
    
           $switch_name = array_reduce(explode("\n",$t->cmd("show system-info")),function ($acc, $item) {
             if(preg_match("/^[\s]{0,}System Name[\s]{0,}-[\s]{0,}([a-z0-9\-\.]{0,})[\s]{0,}\$/i",$item, $matched)) {
               $acc = $matched[1];
             }
             return $acc;
           },[]);
           
         $portLongName = get_tplink_port_long_name($router, $portNum);    
         
         $t->prompt("{$switch_name}#");    
         $fdb=$t->cmd("show mac address-table interface ".$portLongName);   
    
         $t->disconnect();

         } catch (Exception $e) {
          echo "Caught Exception ('{$e->getMessage()}')\n{$e}\n";
          var_dump($e);
         }
         
         return convert_tplink_mac_long_string_to_arr($fdb);
       }
                           

//echo "----- ".$switch_name." ----- \n";
//var_dump(get_tplink_running_config($router));
//var_dump(get_tplink_mac_addresses_raw_str($router));
//var_dump(get_tplink_sys_info($router));
//var_dump(get_tplink_ports_arr($router));
//var_dump(get_tplink_port_short_name($router, 3));
var_dump(get_tplink_port_macs($router, 1));
exit();

?>
