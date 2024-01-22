<?php

namespace JFStudio;

use JFStudio\Curl;

class Binance {
    const API_KEY = "dfcwr7vl3i3zqmkptcixzft0fmefqd8j6r1qpnuyoiwwur4dgctflr643woipr3w"; 
    const SECRET_KEY = "miwsqdstuj8pjdt8qfnofuy964op8ne6jiuhwn31lru4fcyyloufiwjvtjxgaihk";

    private static $instance;
    
	public static function getInstance()
 	{
    	if(!self::$instance instanceof self)
      		self::$instance = new self;

    	return self::$instance;
 	}
}


// BuMqiOW2SHLAAHvmQBcR114SF51tapcw9twBZd8WZLqzpG77cQR7ryd5shfJMS5u
// Secret Key
// qIYLUDKo1z9ugOx4MmX2R3Tkmz8LsqiYlYAaSwKgIrM01y6ZCmTgvnI5Fg8qpLIn