<?php

 class IMDB {
 	
 	private $_strSource = NULL;
 	private $_strReview = NULL;
 	private $_strReleaseInfo = NULL;
 	
 	private $strNotFound = "Not Found";
 	
 	public function __construct($con) {
 		$this->_strSource = $con;
 		
 	}
 	
 	public function setReleaseInfo($ri)
 	{
 		$this->_strReleaseInfo = $ri;
 	}
 	public function setReview($rv)
 	{
 		$this->_strReview = $rv;
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
    
    const IMDB_REVIEWS_COUNT = '~<span itemprop="reviewCount">([0-9]+)</span>\s*user~Ui';

    const IMDB_CRITIC_COUNT = '~<span itemprop="reviewCount">([0-9]+)</span>\s*critic~Ui';
        
    const IMDB_CRITIC_REVIEWS_COUNT = '~href="criticreviews"\s+title="([0-9]+)\s+review excerpts provided by Metacritic\.com"~Ui';
    
    const IMDB_RELEASE_INFO = '~((USA))</a></b></td>\s+<td align="right"><a href="/date/\(\.\*\)/">((?i:(?:3[01]|[12][0-9]|[1-9])[ \t]+(?:January|February|March|April|May|June|July|August|September|October|November|December)))</a> <a href="/year/\\d\+/">[0-9]{4}</a></td>\s+<td>((?:\(?.*\)?){35})</td></tr>~Ui';
    

    public function getVariable($varName) {
    
    	if ($strReturn = $this->matchRegex($this->_strSource, $$varName, 1)) {
    		return trim($strReturn);
    	}
    
    	return $this->strNotFound;
    }
    
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
    
    public function getCriticReviewCount() {
    
    	if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_CRITIC_REVIEWS_COUNT, 1)) {
    		return trim($strReturn);
    	}
    
    	return $this->strNotFound;
    }
    
    public function getReleasInfo($state = "USA") {
    
    	if ($strReturn = $this->matchRegex($this->_strReleaseInfo, IMDB::IMDB_RELEASE_INFO, 1)) {
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
    $releaseinfo = file_get_contents("cache/" . "1660aa3f839e972ae5a79827eae37a38.html");        
    $review = file_get_contents("cache/" . "222a8a41b4d18b1b3111da1c0be057d9.html");
    
    $oIMDB = new IMDB($content);
    $oIMDB->setReleaseInfo($releaseinfo);
    $oIMDB->setReview($review);
        
    print_file($fos,"<ol>");
    //print_file($fos,'<li><p>getRating: <b>' . $oIMDB->getRating() . '</b></p></li>');
    print_file($fos, "getRating=" . $oIMDB->getRating());
    print_file($fos, "getRatingCount=" . $oIMDB->getRatingCount());
    print_file($fos, "getReviewsCount=" . $oIMDB->getReviewsCount());
    print_file($fos, "getCriticCount=" . $oIMDB->getCriticCount());
    print_file($fos, "getCriticReviewCount=" . $oIMDB->getCriticReviewCount());
    
    print_file($fos, "getReleasInfo=" . $oIMDB->getReleasInfo());
    
    //print_file($fos, "IMDB_CRITIC_REVIEWS_COUNT=" . $oIMDB->getVariable("IMDB::IMDB_CRITIC_REVIEWS_COUNT"));
  
?>