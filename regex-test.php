<?php

class Reviewer {
	public function __construct(){}
	
	var $useful = 0;
	var $outof = 0;
	var $title = "";
	var $rev_date = null;
	var $rate = null;
	var $author = null;
	var $url = null;
	var $nation = null;
	var $remark = null;
		
}

 class IMDB {
 	
 	private $_strSource = NULL;
 	private $_strReview = NULL;
 	private $_strReleaseInfo = NULL;
 	
 	private $strR = NULL;
 	
 	private $strNotFound = "Not Found";
 	
 	private $arrReviewer = null;
 	
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
   
    public function getAllReviewsDataQQQQ($count) {
    	$arrMatches = "";
    	      	 
    	$fh  = fopen("Oops.log", "wb");
    		
    	/*
	    preg_match_all('/<hr .+?|(?P<seq2>>(?-i:<p>)(?P<cont>.+?)<div class="yn")/i',  $this->_strReview, $arrMatches, PREG_PATTERN_ORDER);
	    
	    $cnt = count($arrMatches);
	    print "found Review Info=" . $cnt . "\n";
	    
		for ($i = 0; $i < count($arrMatches[0]); $i++) {
			# Matched text = $arrMatches[0][$i];
			$subject = $arrMatches[0][$i];
			
			//print $i . " th " . $subject . "\n\n";
			//if ( preg_match('/^<p>/i', $subject) ) 
			{
				print $i . " th " . $subject . "\n\n";
			}
			//print_r ($subject) . "\n";
		}
		*/
    	/*
		preg_match_all('/<hr .+?|>(?-i:<p>)(?P<cont>.+?)<div class="yn"/i', $this->_strReview, $arrMatches, PREG_PATTERN_ORDER);
		$arrMatches = $arrMatches[1];
		*/    	
    	preg_match_all('/<hr .+?><p>(?P<rev>.+?)<div class="yn"/i', $this->_strReview, $arrMatches, PREG_PATTERN_ORDER);
    	$arrMatches = $arrMatches[1];
    	
		$matCnt = 0;
		
		//$reviews = new Reviewer();
		
		for ($i = 0; $i < count($arrMatches); $i++) {
			$subject = $arrMatches[$i];
			$regs = "";
			
			//print "SUBJECT=Len=" . strlen($subject) . " Value=" . $subject ."\n\n";
			
			if (strlen($subject) < 100)
				continue;
			
			if (preg_match('/^<small>/i', $subject) )
			{
				//print "Match!! \"" . $subject . "\n";
			}
			else {
				print "Not Match!! " . $subject . "\n\n";
				continue;
			}
			
			$result = "";
			//<small>408 out of 593 people found the following review useful:</small><br><b>Not fun, not even in a cheesy sense</b>, <small>7 March 2008</small><br><img width="102" height="12" alt="1/10" src="http://i.media-imdb.com/images/showtimes/10.gif"><br><small>Author:</small><a href="/user/ur5400353/comments">keiichi73</a> <small>from United States</small><br><p><b>*** This review may contain spoilers ***</b></p></p><p>Some critics have moaned that as film technology grows, thestorytelling ability of the movies shrinks. I have never quite agreedwith this assessment, as I believe there is a place for spectacle ofany variety, even the mindless kind. However, to those who share theview of those critics, 10,000 B.C. will most likely be the mostconvincing piece of evidence to their argument. Here is a movie thatlooks like it cost millions to make, but is saddled with a screenplaythat looks like it came from the Dollar Store.<br><br>Director and co-writer, Roland Emmerich is no stranger to brainlessspectacles. This is the guy who brought us Independence Day and 1998&#39;sHollywood take on Godzilla, after all. There&#39;s a very fine line betweenbrainless and just plain brain dead, unfortunately. 10,000 B.C. isshort on spectacle, short on plot, and short on just about anythingthat people go to the movies for. There are characters and a love storyto drive the bare bones plot, but this seems to be added in as anafterthought. I got the impression that Emmerich and fellowscreenwriter, Harald Kloser (a film score composer making his firstscreenplay credit), had the idea for a couple cool scenes, then triedto add a bunch of filler material between them. They threw in somesketchy characters that hardly reach two dimensions to inhabit thisfiller, and called it a screenplay. In order for spectacle to work,even the cheese-filled variety such as this, there has to be somethingfor the audience to get excited about. This movie is just one bigtease.<br><br>The plot, if it can even be called that, is set in the days of earlyman. The heroes are an unnamed tribal people who speak perfect English,all have the bodies of supermodels, and hunt mammoths for food. The twocharacters we&#39;re supposed to be focused on are a pair of young loversnamed D&#39;Leh (Steven Strait) and Evolet (Camilla Belle). Why they are inlove, and why we should care about them, the movie never goes out ofits way to explain. The rest of the villagers do not really matter.They exist simply to be captured when a group of foreign invaders comeriding into their peaceful tribe, and kidnap most of them to work asslaves back in their own home colony. Evolet is one of the captured, soD&#39;Leh and a small handful of others set out to find where they&#39;ve beentaken to, and to seek the aid of other tribes that have also beeninvaded by this enemy. There&#39;s a mammoth herd here, a saber tooth tigerthere, but they have nothing to do with anything. They&#39;re just computergenerated special effects who are there simply because the filmmakersfelt the current scene needed a special effect shot. I&#39;d be moreimpressed if the effects didn&#39;t look so out of place with the actorsmost of the time.<br><br>10,000 B.C. probably would have worked better as a silent movie, or asubtitled one, as most of the dialogue that comes out of the mouths ofthese people are as wooden as the spears they carry. The good tribesare the only people in this movie who have mastered the Queen&#39;sEnglish, naturally. The evil invading tribe speak in subtitles, andsometimes have their voices mechanically altered and lowered, so thatthey sound more threatening and demonic. No one in this movie isallowed to have a personality, or act differently from one another.Everybody in each tribe talks, thinks, and behaves exactly the same,with facial hair and differing body types being the main way to tellthem apart. This would make it hard to get involved in the story, butthe movie dodges this tricky issue by not even having a story in thefirst place. Once the film&#39;s main tribe is attacked, the movie turnsinto an endless string of filler material and padding to drag the wholething out to feature length. Aside from a brief encounter with somebird-like prehistoric creatures, there are no moments of action ordanger until D&#39;Leh and his followers reach the land of the invadingarmy. The movie throws a saber tooth tiger encounter to fool us intothinking something&#39;s gonna happen, but the tiger winds up being just asboring as the human characters inhabiting the movie, and is justmillions in special effects budget wasted on something that didn&#39;t needto be there in the first place, other than to move the shaky plotalong.<br><br>There is a key ingredient missing in 10,000 B.C., and that is fun. Thismovie is not fun to watch at all. I kept on waiting for something,anything, to happen. When something eventually did happen, it wasusually underwhelming. I know of people who are interested in seeingthis movie, because of the special effects, or because they think itlooks enjoyably cheesy. To those people, I say please do not be drawnin by curiosity. This isn&#39;t even enjoyable in a bad sense. Yourprecious time is worth more than what any theater may be charging tosee this movie. For anyone wondering, yes, that includes the budgetcinema and the price of a rental.</p>
			
			// extract reviews count
			preg_match_all('/<small>(?P<rv>[0-9]+)(?-i: out of )(?P<all>[0-9]+)(?-i: people found the following review useful:)/i', $subject, $arrMatches, PREG_PATTERN_ORDER);
				
			//$reviews->useful = $arrMatches[1][0]; 
			//print_r($reviews) . "\n";
			
			//필요한 정보를 하나씩 추출하자..한 regexp 로 처리하기에는  너무 힘들다. 			
			/* 실패한 코드 
			if (preg_match('%<small>(?P<a>[0-9]+) out of (?P<b>[0-9]+) people found the following review useful:</small>|<b>(?P<title>.+?)</b>|<small>(?P<date>(?i:(?:3[01]|[12][0-9]|[1-9])[ \t]+(?:January|February|March|April|May|June|July|August|September|October|November|December)[ \t]+[0-9]{4}))</small>|<img .+?(?P<rate>1/10|10/10|2/10|3/10|4/10|5/10|6/10|7/10|8/10|9/10)[^\n\r>]+?>|<a [^\n\r>]+?>(?P<id>[\dA-Za-z]{9})</a>|<small>from (?P<loc>.+?)</small>|</p><p>(?P<review>.+?)</p>%', $subject, $regs)) {
			*/
			
			//$date = defined($regs['date']) ? $regs['date'] : "";
			//print_r($regs) . "\n";
				
				//$result = $regs['a'] . "/" . $regs['b'] . " title = " . $date;
			
		}

		print "Total match = $matCnt\n";
		 
		//fprintf($fh, "%s\n\n", $dump);
    	    	
		fclose($fh);
    	 
    }

    public function getAllReviewsData($count) {
    	$this->arrReviewer = array();
    	
    	$arrMatches = "";
    	 
    	$fh  = fopen("Oops.log", "wb");
    
    	preg_match_all('/<hr .+?><p>(?P<rev>.+?)<div class="yn"/i', $this->_strReview, $arrMatches, PREG_PATTERN_ORDER);
    	$arrMatches = $arrMatches[1];
    	 
    	$matCnt = 0;       	
    
    	for ($i = 0; $i < count($arrMatches); $i++) {    		
    		$reviews = new Reviewer();
    		
    		$subject = $arrMatches[$i];
    		
    		// html special char 가 붙은 comment 도 있으므로 변환한다.
    		fprintf($fh, "\n\nBefore =======================================================\n");
    		fprintf($fh, "%s", $subject);
    		fprintf($fh, "\n\nAfter =======================================================\n");
    		$subject = htmlspecialchars_decode($subject);
    		fprintf($fh, "%s", $subject);
    		
    		//<small>408 out of 593 people found the following review useful:</small><br><b>Not fun, not even in a cheesy sense</b>, <small>7 March 2008</small><br><img width="102" height="12" alt="1/10" src="http://i.media-imdb.com/images/showtimes/10.gif"><br><small>Author:</small><a href="/user/ur5400353/comments">keiichi73</a> <small>from United States</small><br><p><b>*** This review may contain spoilers ***</b></p></p><p>Some critics have moaned that as film technology grows, thestorytelling ability of the movies shrinks. I have never quite agreedwith this assessment, as I believe there is a place for spectacle ofany variety, even the mindless kind. However, to those who share theview of those critics, 10,000 B.C. will most likely be the mostconvincing piece of evidence to their argument. Here is a movie thatlooks like it cost millions to make, but is saddled with a screenplaythat looks like it came from the Dollar Store.<br><br>Director and co-writer, Roland Emmerich is no stranger to brainlessspectacles. This is the guy who brought us Independence Day and 1998&#39;sHollywood take on Godzilla, after all. There&#39;s a very fine line betweenbrainless and just plain brain dead, unfortunately. 10,000 B.C. isshort on spectacle, short on plot, and short on just about anythingthat people go to the movies for. There are characters and a love storyto drive the bare bones plot, but this seems to be added in as anafterthought. I got the impression that Emmerich and fellowscreenwriter, Harald Kloser (a film score composer making his firstscreenplay credit), had the idea for a couple cool scenes, then triedto add a bunch of filler material between them. They threw in somesketchy characters that hardly reach two dimensions to inhabit thisfiller, and called it a screenplay. In order for spectacle to work,even the cheese-filled variety such as this, there has to be somethingfor the audience to get excited about. This movie is just one bigtease.<br><br>The plot, if it can even be called that, is set in the days of earlyman. The heroes are an unnamed tribal people who speak perfect English,all have the bodies of supermodels, and hunt mammoths for food. The twocharacters we&#39;re supposed to be focused on are a pair of young loversnamed D&#39;Leh (Steven Strait) and Evolet (Camilla Belle). Why they are inlove, and why we should care about them, the movie never goes out ofits way to explain. The rest of the villagers do not really matter.They exist simply to be captured when a group of foreign invaders comeriding into their peaceful tribe, and kidnap most of them to work asslaves back in their own home colony. Evolet is one of the captured, soD&#39;Leh and a small handful of others set out to find where they&#39;ve beentaken to, and to seek the aid of other tribes that have also beeninvaded by this enemy. There&#39;s a mammoth herd here, a saber tooth tigerthere, but they have nothing to do with anything. They&#39;re just computergenerated special effects who are there simply because the filmmakersfelt the current scene needed a special effect shot. I&#39;d be moreimpressed if the effects didn&#39;t look so out of place with the actorsmost of the time.<br><br>10,000 B.C. probably would have worked better as a silent movie, or asubtitled one, as most of the dialogue that comes out of the mouths ofthese people are as wooden as the spears they carry. The good tribesare the only people in this movie who have mastered the Queen&#39;sEnglish, naturally. The evil invading tribe speak in subtitles, andsometimes have their voices mechanically altered and lowered, so thatthey sound more threatening and demonic. No one in this movie isallowed to have a personality, or act differently from one another.Everybody in each tribe talks, thinks, and behaves exactly the same,with facial hair and differing body types being the main way to tellthem apart. This would make it hard to get involved in the story, butthe movie dodges this tricky issue by not even having a story in thefirst place. Once the film&#39;s main tribe is attacked, the movie turnsinto an endless string of filler material and padding to drag the wholething out to feature length. Aside from a brief encounter with somebird-like prehistoric creatures, there are no moments of action ordanger until D&#39;Leh and his followers reach the land of the invadingarmy. The movie throws a saber tooth tiger encounter to fool us intothinking something&#39;s gonna happen, but the tiger winds up being just asboring as the human characters inhabiting the movie, and is justmillions in special effects budget wasted on something that didn&#39;t needto be there in the first place, other than to move the shaky plotalong.<br><br>There is a key ingredient missing in 10,000 B.C., and that is fun. Thismovie is not fun to watch at all. I kept on waiting for something,anything, to happen. When something eventually did happen, it wasusually underwhelming. I know of people who are interested in seeingthis movie, because of the special effects, or because they think itlooks enjoyably cheesy. To those people, I say please do not be drawnin by curiosity. This isn&#39;t even enjoyable in a bad sense. Yourprecious time is worth more than what any theater may be charging tosee this movie. For anyone wondering, yes, that includes the budgetcinema and the price of a rental.</p>

    		//필요한 정보를 하나씩 추출하자..한 regexp 로 처리하기에는  너무 힘들다.
    		/* 실패한 코드
    		 if (preg_match('%<small>(?P<a>[0-9]+) out of (?P<b>[0-9]+) people found the following review useful:</small>|<b>(?P<title>.+?)</b>|<small>(?P<date>(?i:(?:3[01]|[12][0-9]|[1-9])[ \t]+(?:January|February|March|April|May|June|July|August|September|October|November|December)[ \t]+[0-9]{4}))</small>|<img .+?(?P<rate>1/10|10/10|2/10|3/10|4/10|5/10|6/10|7/10|8/10|9/10)[^\n\r>]+?>|<a [^\n\r>]+?>(?P<id>[\dA-Za-z]{9})</a>|<small>from (?P<loc>.+?)</small>|</p><p>(?P<review>.+?)</p>%', $subject, $regs)) {
    		*/
    		
    		$subMatches = ""; $result="";
    		
    		//1. extract reviews count
    		preg_match_all('/<small>(?P<rv>[0-9]+)(?-i: out of )(?P<all>[0-9]+)(?-i: people found the following review useful:)/i', $subject, $subMatches, PREG_PATTERN_ORDER);
    		//$reviews->useful = $subMatches[1][0];
    		$reviews->useful = $subMatches['rv'][0];
    		$reviews->outof = $subMatches['all'][0];
    		    			
    		///2. review write Date Time
    		//preg_match_all('%<small>(?P<dt>(?:1[0-2]|[1-9])[ \t]+March[ \t]+[0-9]{4})</small>%i', $subject, $subMatches, PREG_PATTERN_ORDER);
    		preg_match_all('%<small>(?P<dt>(?:3[01]|[12][0-9]|[1-9])[ \t]+(?:January|February|March|April|May|June|July|August|September|October|November|December)[ \t]+[0-9]{4})</small>%i', $subject, $subMatches, PREG_PATTERN_ORDER);    		    		
    		$reviews->rev_date = $subMatches['dt'][0];
    		
    		//3. title
    		preg_match_all('%</small><br><b>(?P<title>.+?)</b>%i', $subject, $subMatches, PREG_PATTERN_ORDER);
    		$reviews->title = $subMatches['title'][0];    
   		
    		//4. rating
    		preg_match_all('%<img .+?alt="(?P<rate>[0-9]+)/[0-9]+.+?>%i', $subject, $subMatches, PREG_PATTERN_ORDER);
    		$reviews->rate = $subMatches['rate'][0];
    		
    		// 5. author & url => from Canada 등도 같이 추출하려고 했으나 막힘
    		preg_match_all('%<small>Author:</small><a href="(?P<url>.+?)">(?P<author>.+?)</a>%i', $subject, $subMatches, PREG_PATTERN_ORDER);
    		$reviews->url = 'http://www.imdb.com' . $subMatches['url'][0];
    		$reviews->author = $subMatches['author'][0];
    		
    		// 6. from
    		preg_match_all('%<small>from (?P<nation>.+?)</small>%i', $subject, $subMatches, PREG_PATTERN_ORDER);
    		// from United States 와 같은 nation 정보가 없는 사용자가 있음
    		if (count($subMatches['nation']) > 0)
    			$reviews->nation = $subMatches['nation'][0];
    		
    		// 7. remakr -> This review may contain spoilers
    		if (preg_match('/This review may contain spoilers/i', $subject)) {
    			$reviews->remark = 'This review may contain spoilers';
    		} else {
    			# Match attempt failed
    		}    		
    		
    		array_push($this->arrReviewer, $reviews) ;	
    	}
    
    	for ($i = 0; $i < count($this->arrReviewer); $i++) {
    		$reviews = $this->arrReviewer[$i];
    		
    		var_dump($reviews);
    		//var_dump($reviews->useful);
    		//print $reviews->useful[0] . "\n";
    	}
    	
    	print "Total match = $matCnt\n";    			
    	
    	fclose($fh);
    
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
    
    //print_file($fos, "getMetaScore=" . $oIMDB->getMetaScore());
    /*
    //print_file($fos,'<li><p>getRating: <b>' . $oIMDB->getRating() . '</b></p></li>');
    print_file($fos, "getRating=" . $oIMDB->getRating());
    print_file($fos, "getRatingCount=" . $oIMDB->getRatingCount());
    print_file($fos, "getReviewsCount=" . $oIMDB->getReviewsCount());
    */
    //print_file($fos, "getCriticCount=" . $oIMDB->getCriticCount());
    //print_file($fos, "getCriticReviewCount=" . $oIMDB->getCriticReviewCount());
   
    
    //print_file($fos, "getReleasInfo=" . $oIMDB->getReleasInfo('USA'));
    
    //print_file($fos, "IMDB_CRITIC_REVIEWS_COUNT=" . $oIMDB->getVariable("IMDB::IMDB_CRITIC_REVIEWS_COUNT"));
  
?>