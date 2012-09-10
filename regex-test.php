<?php

 class IMDB {
 	
 	private $_strSource = NULL;
 	private $_strReview = NULL;
 	private $_strReleaseInfo = NULL;
 	
 	private $strR = NULL;
 	
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
    
    //const IMDB_RELEASE_INFO = '~((USA))</a></b></td>\s+<td align="right"><a href="/date/\(\.\*\)/">((?i:(?:3[01]|[12][0-9]|[1-9])[ \t]+(?:January|February|March|April|May|June|July|August|September|October|November|December)))</a> <a href="/year/\\d\+/">[0-9]{4}</a></td>\s+<td>((?:\(?.*\)?){35})</td></tr>~Ui';
    
    const IMDB_RELEASE_INFO = 
    //'~region=(A[D-GILNQ-UWX]|B[ABDEGHL-ORSTVZ]|C[ACHKLNORUXYZ]|D[EKMO]|E[CES]|F[IJKMOR]|G[BD-GILPR-UY]|H[KMNRTU]|I[DEL-OQ-T]|J[EMOP]|K[HINPRWY]|L[ABCIKTUV]|M[ACEFHKM-QSTVXY]|N[CFILOPRUZ]|OM|P[AE-HK-NRSTWY]|QA|R[EOS]|S[ABEG-KMRVY]|T[CFHKLORTVW]|U[AMSY]|V[ACEGINU]|W[FS]|Y[ET])"|/">((?i:(?:3[01]|[12][0-9]|[1-9])[ \t]+(?:January|February|March|April|May|June|July|August|September|October|November|December)))</a>|">([0-9]{4})</a>|<td>([^\n\r/<>dt]+)</td>~Ui';
    '~region=(?:A[D-GILNQ-UWX]|B[ABDEGHL-ORSTVZ]|C[ACHKLNORUXYZ]|D[EKMO]|E[CES]|F[IJKMOR]|G[BD-GILPR-UY]|H[KMNRTU]|I[DEL-OQ-T]|J[EMOP]|K[HINPRWY]|L[ABCIKTUV]|M[ACEFHKM-QSTVXY]|N[CFILOPRUZ]|OM|P[AE-HK-NRSTWY]|QA|R[EOS]|S[ABEG-KMRVY]|T[CFHKLORTVW]|U[AMSY]|V[ACEGINU]|W[FS]|Y[ET])"|/">(((?i:(?:3[01]|[12][0-9]|[1-9])[ \t]+(?:January|February|March|April|May|June|July|August|September|October|November|December))))</a>|">([0-9]+)</a>|<td>([^\n\r/<>dt])?</td>~Ui';
     

    const IMDB_RELEASE_INFO2 =
    //'~region=(A[D-GILNQ-UWX]|B[ABDEGHL-ORSTVZ]|C[ACHKLNORUXYZ]|D[EKMO]|E[CES]|F[IJKMOR]|G[BD-GILPR-UY]|H[KMNRTU]|I[DEL-OQ-T]|J[EMOP]|K[HINPRWY]|L[ABCIKTUV]|M[ACEFHKM-QSTVXY]|N[CFILOPRUZ]|OM|P[AE-HK-NRSTWY]|QA|R[EOS]|S[ABEG-KMRVY]|T[CFHKLORTVW]|U[AMSY]|V[ACEGINU]|W[FS]|Y[ET])"|/">((?i:(?:3[01]|[12][0-9]|[1-9])[ \t]+(?:January|February|March|April|May|June|July|August|September|October|November|December)))</a>|">([0-9]{4})</a>|<td>([^\n\r/<>dt]+)</td>~Ui';
    '~region=US">USA</a></b></td> <td align="right"><a href="\/date\/~Ui';   
 
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
    
    /**
     * 
     * @param string $cname 개봉일을 얻을 국가명
     * @return string
     */
    public function getReleasInfo($cname) {
    	$this->strR = NULL;
    	
    	$arrMatches = null;
    	
    	preg_match_all('%<tr><td><b><a href="/calendar/\?region=(.+?)</tr>%s', $this->_strReleaseInfo, $arrMatches, PREG_PATTERN_ORDER);
    	
    	for ($i = 0; $i < count($arrMatches[0]); $i++) {
    		# Matched text = $arrMatches[0][$i]; 	 

    		$sub = null;
    	
    		// JP">Japan</a></b></td>    <td align="right"><a href="/date/02-22/">22 February</a> <a href="/year/2008/">2008</a></td>    <td> (Tokyo) (premiere)</td>
    	 	$tmp = $arrMatches[1][$i];
    		
    		// 파싱을 위해 앞을 <td> 로 변경
    		$tmp[0] = '<'; $tmp[1] = 't'; $tmp[2] = 'd';
    		
    		// <td align="right"> 을 <td> 로 변경
    		$tmp = str_ireplace('<td align="right">', '<td>', $tmp);   				
    		preg_match_all('%<td>(.+?)</td>%s', $tmp, $sub, PREG_PATTERN_ORDER);

    		$cnt = count($sub[0]);
    		    		
    		$nation = str_ireplace('</a></b>', '', $sub[1][0]);
    		
    		preg_match_all('%/date/(?P<dt>(?:1[0-2]|0[1-9])-(?:3[01]|[12][0-9]|0[1-9]))/"|/year/(?P<year>[0-9]+)/"%s', $sub[1][1], $dt, PREG_PATTERN_ORDER); ;
    
    		// 개봉장소
    		if ($cnt > 2)
    			$loc = $sub[1][2];
    		else
    			$loc = "";    		
    		
    		if ($nation == $cname) {
    			$this->strR .= "$nation, " . $dt["year"][1] . "-" . $dt["dt"][0] . ",$loc\n";
    		}    		
    	}
    	      	    
    	return $this->strR;
    }
    
    public function getReleasInfo2() {
    	
    	if ($strReturn = $this->matchRegex($this->_strReleaseInfo, IMDB::IMDB_RELEASE_INFO2, 1)) {
    		return trim($strReturn);
    	}
    
    	return $this->strNotFound;
    }
    ///
    public function getMetaScore() {
    	$arrMatches = "";
    	
	    preg_match_all('%<a href="criticreviews">(?P<cr1>[0-9]+)/(?P<cr2>[0-9]+)</a>%', $this->_strSource, $arrMatches, PREG_PATTERN_ORDER);
	    if (count($arrMatches[0]) > 0)
	    {
			for ($i = 0; $i < count($arrMatches[0]); $i++) {
				# Matched text = $arrMatches[0][$i];
				//print_r($arrMatches) . "\n";
				
			}
		        
			return $arrMatches["cr1"][0] . "/" . $arrMatches["cr2"][0];  
	    }
	    
	    return $this->strNotFound; 
    }    
   
    public function getAllReviewsData($count) {
    	$arrMatches = "";
    	
    	preg_match_all('%<p>|(?P<content>.+?)</p>%s', $this->_strReview, $arrMatches, PREG_PATTERN_ORDER);
    	
    	$cnt = count($arrMatches[0]);
    	print "found Review Info=" . $cnt . "\n";
    	for ($i = 0; $i < $cnt; $i++) {
    		# Matched text = $arrMatches[0][$i];
    		//print_r($arrMatches[0][$i]) . "\n";
    		print $arrMatches[0][$i] . "\n\n";
    		
    	}
    	 
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
    $content=  file_get_contents("cache/" . "3294219911e82805762d315860245521.html");
    $releaseinfo = file_get_contents("cache/" . 
    		"1660aa3f839e972ae5a79827eae37a38.html");        
    		//"bc_release_info.html");
    $review = file_get_contents("cache/" . "222a8a41b4d18b1b3111da1c0be057d9.html");
    
    $oIMDB = new IMDB($content);
    $oIMDB->setReleaseInfo($releaseinfo);
    $oIMDB->setReview($review);
        
    print_file($fos,"<ol>");
    
    print_file($fos, "getAllReviewsData=" . $oIMDB->getAllReviewsData(3) . "\n\n\n");
    
    print_file($fos, "getMetaScore=" . $oIMDB->getMetaScore());
    /*
    //print_file($fos,'<li><p>getRating: <b>' . $oIMDB->getRating() . '</b></p></li>');
    print_file($fos, "getRating=" . $oIMDB->getRating());
    print_file($fos, "getRatingCount=" . $oIMDB->getRatingCount());
    print_file($fos, "getReviewsCount=" . $oIMDB->getReviewsCount());
    */
    print_file($fos, "getCriticCount=" . $oIMDB->getCriticCount());
    print_file($fos, "getCriticReviewCount=" . $oIMDB->getCriticReviewCount());
   
    
    print_file($fos, "getReleasInfo=" . $oIMDB->getReleasInfo('USA'));
    
    //print_file($fos, "IMDB_CRITIC_REVIEWS_COUNT=" . $oIMDB->getVariable("IMDB::IMDB_CRITIC_REVIEWS_COUNT"));
  
?>