<?php
namespace core\extension\cache;
use core\Heepp;

class MemCacheAPI extends Heepp {
    public $config = [];
    public static $_ini;
    public static $_log;

    public function __construct() {
        parent::__construct(__CLASS__);
        $this->config = $this->getData('app.system.memcache');
        self::$_ini = MemCacheLoader::singleton();
    }

    public function exec($command) {
        $buffer = '';
        $handle = null;
        if (!($handle = @fsockopen(MEM_CACHE_HOST,MEM_CACHE_PORT,$errno,$errstr,self::$_ini->get('connection_timeout')))) {
            self::$_log = utf8_encode($errstr);
            return false;
        }
        fwrite($handle,$command."\r\n");
        $buffer = fgets($handle);
        if ($this->end($buffer,$command)) {
            fclose($handle);
            self::$_log = $buffer;
            return false;
        }
        while(!feof($handle)) {
            $line = fgets($handle);
            if ($this->end($line,$command)) {
                break;
            }
            $buffer .= $line;
        }
        fclose($handle);
        return $buffer;
    }

    private function end($buffer,$command) {
        # increment or decrement also return integer
        if (preg_match('/^(incr|decr)/',$command)) {
            if (preg_match('/^(END|ERROR|SERVER_ERROR|CLIENT_ERROR|NOT_FOUND|[0-9]*)/',$buffer)) {
                return true;
            }
        } else {
            if (preg_match('/^(END|DELETED|OK|ERROR|SERVER_ERROR|CLIENT_ERROR|NOT_FOUND|STORED|RESET|TOUCHED)/',$buffer)) {
                return true;
            }
        }
        return false;
    }

    public function parse($string,$stats = true) {
        $return = [];
        $lines = preg_split('/\r\n/',$string);
        if ($stats) {
            foreach($lines as $line) {
                $data = preg_split('/ /',$line);
                if (isset($data[2])) {
                    $return[$data[1]] = $data[2];
                }
            }
        } else {
            foreach($lines as $line) {
                $data = preg_split('/ /',$line);
                if (isset($data[1])) {
                    $return[$data[1]] = [substr($data[2],1),$data[4]];
                }
            }
        }
        return $return;
    }

    public function stats() {
        if ($return = $this->exec('stats')) {
            return $this->parse($return);
        }
        return false;
    }

    public function settings() {
        if ($return = $this->exec('stats settings')) {
            return $this->parse($return);
        }
        return false;
    }

    public function slabs() {
        $slabs = [];
        $stats           = $this->stats();
        $slabs['uptime'] = $stats['uptime'];
        unset($stats);

        if ($result = $this->exec('stats slabs')) {
            $result                  = $this->parse($result);
            $slabs['active_slabs']   = $result['active_slabs'];
            $slabs['total_malloced'] = $result['total_malloced'];
            unset($result['active_slabs'],$result['total_malloced']);

            foreach($result as $key => $value) {
                $key                     = preg_split('/:/',$key);
                $slabs[$key[0]][$key[1]] = $value;
            }

            if ($result = $this->exec('stats items')) {
                $result = $this->parse($result);
                foreach($result as $key => $value) {
                    $key                              = preg_split('/:/',$key);
                    $slabs[$key[1]]['items:'.$key[2]] = $value;
                }

                return $slabs;
            }
        }
        return false;
    }

    public function items($slab) {
        # Initializing
        $items = false;

        # Executing command : stats cachedump
        if ($result = $this->exec('stats cachedump '.$slab.' '.self::$_ini->get('max_item_dump'))) {
            # Parsing result
            $items = $this->parse($result,false);
        }

        return $items;
    }

    public function get($key) {
        if ($string = $this->exec('get '.$key)) {
            $string = preg_replace('/^VALUE '.preg_quote($key,'/').'[0-9 ]*\r\n/','',$string);
            if (ord($string[0]) == 0x78 && in_array(ord($string[1]),[0x01,0x5e,0x9c,0xda])) {
                return gzuncompress($string);
            }
            return $string;
        }
        return false;
    }

    public function set($key,$data,$duration = 1000) {
        # Formatting data
        $data = preg_replace('/\r/','',$data);

        # Executing command : set
        if ($result = $this->exec('set '.$key.' 0 '.$duration.' '.strlen($data)."\r\n".$data)) {
            return $result;
        }
        return $data;
    }

    public function delete($key) {
        # Executing command : delete
        if ($result = $this->exec('delete '.$key)) {
            return $result;
        }
        return true;
    }

    public function increment($key,$value) {
        # Executing command : increment
        if ($result = $this->exec('incr '.$key.' '.$value)) {
            return $result;
        }
        return self::$_log;
    }

    public function decrement($key,$value) {
        # Executing command : decrement
        if ($result = $this->exec('decr '.$key.' '.$value)) {
            return $result;
        }
        return self::$_log;
    }

    public function flush_all($delay) {
        # Executing command : flush_all
        if ($result = $this->exec('flush_all '.$delay)) {
            return $result;
        }
        return self::$_log;
    }

    public function search($search,$level = false,$more = false) {
        $slabs = [];
        $items = false;

        # Executing command : stats
        if (($level == 'full') && ($result = $this->exec('stats'))) {
            # Parsing result
            $result   = $this->parse($result);
            $infinite = isset($result['time'],$result['uptime']) ? ($result['time'] - $result['uptime']) : 0;
        }

        # Executing command : slabs stats
        if ($result = $this->exec('stats slabs')) {
            # Parsing result
            $result = $this->parse($result);
            unset($result['active_slabs'],$result['total_malloced']);
            # Indexing by slabs
            foreach($result as $key => $value) {
                $key            = preg_split('/:/',$key);
                $slabs[$key[0]] = true;
            }
        }

        # Exploring each slabs
        foreach($slabs as $slab => $unused) {
            # Executing command : stats cachedump
            if ($result = $this->exec('stats cachedump '.$slab.' 0')) {
                # Parsing result
                preg_match_all('/^ITEM ((?:.*)'.preg_quote($search,'/').'(?:.*)) \[([0-9]*) b; ([0-9]*) s\]\r\n/imU',$result,$matchs,PREG_SET_ORDER);
                foreach($matchs as $item) {
                    # Search & Delete
                    if ($more == 'delete') {
                        $items[] = $item[1].' : '.$this->delete($item[1]);
                        # Basic search
                    } else {
                        # Detail level
                        if ($level == 'full') {
                            $items[] = $item[1].' : ['.trim(Library_Data_Analysis::byteResize($item[2])).'b, expire in '.(($item[3] == $infinite) ? '&#8734;' : Library_Data_Analysis::uptime($item[3] - time(),true)).']';
                        } else {
                            $items[] = $item[1];
                        }
                    }
                }
            }
            unset($slabs[$slab]);
        }
        if (is_array($items)) {
            sort($items);
        }
        return $items;
    }

    public function telnet($command) {
        # Executing command
        if ($result = $this->exec($command)) {
            return $result;
        }
        return self::$_log;
    }
}
