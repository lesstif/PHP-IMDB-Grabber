<?php

 class IMDB {
 	
 	private $_strSource = NULL;
 	private $strNotFound = "Not Found";
 	
 	public function __construct($con) {
 		$this->_strSource = $con;
 		
 	}
 	
    public function testStream()
    {
    	//fclose(STDOUT);

    	$convert = array("1408", "BC 10,000");
    	
		foreach ($convert as $movie)
		{
			$movie = chop($movie);
			if ($movie == "")
				continue;
			
			print "MOvie=$movie\n";
			
			$fo = fopen($movie . ".html", 'wb');
			
			fprintf ($fo, "$movie\n");
			fprintf ($fo, "AAAAAAAAAAAA\n");
			
			fclose($fo);
			
			print "Subcess";
		}
		
		
    }
    
    private function matchRegex($strContent, $strRegex, $intIndex = null) {
    	preg_match_all($strRegex, $strContent, $arrMatches);
    	if ($arrMatches === FALSE) return false;
    	if ($intIndex != null && is_int($intIndex)) {
    		if ($arrMatches[$intIndex]) {
    			return $arrMatches[$intIndex][0];
    		}
    		return false;
    	}
    	return $arrMatches;
    }
    
    public function getAka() {
    	if ($this->isReady) {
    		if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_AKA, 1)) {
    			return trim($strReturn);
    		}
    	}
    	return $this->strNotFound;
    }
        
    const IMDB_RATING         = //'/<span itemprop="ratingValue">((?:[0-9]+\.[0-9])+)</span>/';
    		'~<span itemprop="ratingValue">((?:[0-9]+\.[0-9])+)</span>~Ui';
    
    const IMDB_RATING_COUNT = '~<span itemprop="ratingCount">([\d \',]+)</span>~Ui';
    
    const IMDB_REVIEWS_COUNT = '~<span itemprop="reviewCount">([0-9]+)</span>(\s)*user~Ui';

    const IMDB_CRITIC_COUNT = '~<span itemprop="reviewCount">([0-9]+)</span>(\s)*critic~Ui';
        
    public function getRating() {
    	
    		if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_RATING, 1)) {
    			return trim($strReturn);
    		}
    	
    	return $this->strNotFound;
    }
    
    public function getRatingCount() {
    	 
    	if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_RATING_COUNT, 1)) {
    		return trim($strReturn);
    	}
    	 
    	return $this->strNotFound;
    }
    

    public function getReviewsCount() {
    
    	if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_REVIEWS_COUNT, 1)) {
    		return trim($strReturn);
    	}
    
    	return $this->strNotFound;
    }
    
    public function getCriticCount() {
    	 
    	if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_CRITIC_COUNT, 1)) {
    		return trim($strReturn);
    	}
    	 
    	return $this->strNotFound;
    }
 }
 
 function print_file($file, $msg)
 {
 	if ($file != null)
	 	fprintf ($file, $msg . "\n");
 	
 	echo $msg . "\n";
 }
 	$fos = null;
 	
    //testStream();
    $content=  file_get_contents("cache/" . "8b3b1cbb1b6cb55234c96b79cfb89fda.html");
        
    $oIMDB = new IMDB($content);
        
    print_file($fos,"<ol>");
    //print_file($fos,'<li><p>getRating: <b>' . $oIMDB->getRating() . '</b></p></li>');
    print_file($fos, "getRating=" . $oIMDB->getRating());
    print_file($fos, "getRatingCount=" . $oIMDB->getRatingCount());
    print_file($fos, "getReviewsCount=" . $oIMDB->getReviewsCount());
    print_file($fos, "getCriticCount=" . $oIMDB->getCriticCount());

?>