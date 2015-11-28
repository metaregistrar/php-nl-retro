<?php

namespace Metaregistrar\Retro {

    class Retro
    {
        /**
         *
         * @var boolean $logging
         */
        protected $logging;
        /**
         *
         * @var array $logentries
         */
        protected $logentries;
        /**
         *
         * @var string $server
         */
        protected $server;
        /**
         *
         * @var int $port=43
         */
        protected $port;
        /**
         *
         * @var integer $timeout = 60
         */
        protected $timeout;

	protected $result;

        function __construct($question,$logging = false)
        {
            if ($logging)
            {
                $this->enableLogging();
            }
            $this->port=43;
            $this->timeout=600;
            $this->server = 'retro.domain-registry.nl';
            set_error_handler(array($this,'error_handler'));
            $this->writelog("Retro class Initialised");
            $this->query($question);

        }

        function __destruct()
        {
            if ($this->logging)
            {
                $this->showLog();
            }
        }

        function error_handler($errno = 0, $errstr = null, $errfile = null, $errline = null)
        {
            // If error is suppressed with @, don't throw an exception
            if (error_reporting() === 0)
            {
                return true; // return true to continue through the others error handlers
            }
            throw new \Exception('Found '.$errstr.' in line '.$errline.' of '.$errfile, $errno, null);
        }


        /**
         * @param $question
         * @return array
         * @throws \Exception
         */
        private function Query($question)
        {
            if (!$socket=@fsockopen($this->server,$this->port,$this->timeout))
            {
                throw new \Exception("Failed to open socket to ".$this->server);
            }

            $this->writeLog("Question: ".$question);
            if (!fwrite($socket,$question))
            {
                fclose($socket);
                throw new \Exception("Failed to write question to TCP socket");
            }
            $datasize = 8192;
	$rawbuffer = '';
while (!feof($socket)) {
    $rawbuffer .= fread($socket, $datasize);
}
                fclose($socket);
	if (strlen($rawbuffer)==0) {
                throw new \Exception("Failed to read data buffer");
            }

            $this->processbuffer($rawbuffer);
        }


	private function processbuffer($buffer) {
		$processed = array();
		$list = explode("\n",$buffer);
		for ($count=1; $count<count($list)-1; $count++) {
			list($domain,$date) = explode(';',$list[$count]);
			$this->result[]=array('domainname'=>$domain,'date'=>$date);
		}
		var_dump($this->result);
	}

        public function setServer($server)
        {
            $this->server = $server;
        }

        public function getServer()
        {
            return $this->server;
        }

        public function setPort($port)
        {
            $this->port = $port;
        }

        public function getPort()
        {
            return $this->port;
        }

        private function enableLogging()
        {
            $this->logging = true;
        }

        private function showLog()
        {
            echo "==== LOG ====";
            foreach ($this->logentries as $logentry)
            {
                echo $logentry."\n";
            }
        }

        private function writeLog($text)
        {
            if ($this->logging)
            {
                $this->logentries[] = "-----".date("Y-m-d H:i:s")."-----".$text."-----";
            }
        }

        function base32encode($input, $padding = true) {

            $map = array(
                '0', '1', '2', '3', '4', '5', '6', '7', //  7
                '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', // 15
                'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', // 23
                'o', 'p', 'q', 'r', 's', 't', 'u', 'v', // 31
                '='  // padding char
            );

            if(empty($input)) return "";
            $input = str_split($input);
            $binaryString = "";
            for($i = 0; $i < count($input); $i++) {
                $binaryString .= str_pad(base_convert(ord($input[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
            }
            $fiveBitBinaryArray = str_split($binaryString, 5);
            $base32 = "";
            $i=0;
            while($i < count($fiveBitBinaryArray)) {
                $base32 .= $map[base_convert(str_pad($fiveBitBinaryArray[$i], 5,'0'), 2, 10)];
                $i++;
            }
            if($padding && ($x = strlen($binaryString) % 40) != 0) {
                if($x == 8) $base32 .= str_repeat($map[32], 6);
                else if($x == 16) $base32 .= str_repeat($map[32], 4);
                else if($x == 24) $base32 .= str_repeat($map[32], 3);
                else if($x == 32) $base32 .= $map[32];
            }
            return $base32;
        }

        /**
         * @param string $data
         */
        private function DebugBinary($data)
        {
            echo pack("S", $data);
            for ($a = 0; $a < strlen($data); $a++) {
                echo $a;
                echo "\t";
                printf("%d", $data[$a]);
                echo "\t";
                $hex = bin2hex($data[$a]);
                echo "0x" . $hex;
                echo "\t";
                $dec = hexdec($hex);
                echo $dec;
                echo "\t";
                if (($dec > 30) && ($dec < 150)) echo $data[$a];
                echo "\n";
            }
        }

    }

}
