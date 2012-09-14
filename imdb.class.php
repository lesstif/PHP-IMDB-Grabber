<?php
/**
 * PHP-IMDb-Grabber -- a PHP IMDb.com scraper
 *
 * This class can be used to retrieve data from IMDb.com with PHP.
 *
 * The technique used is called “web scraping”
 * (see http://en.wikipedia.org/wiki/Web_scraping for details).
 * Which means: If IMDb changes *anything* on their HTML, the script is going to
 * fail (even a single space might be enough).
 *
 * You might not know, but there is an IMDb API available. The problem?
 * You will have to pay at least $15.000 to use it. Great, thank you.
 *
 *
 * If you want to thank me for my work and the support, feel free to do this
 * through PayPal (use mail@fabian-beiner.de as payment destination) or just
 * buy me a book at Amazon (http://www.amazon.de/wishlist/3IAUEEEY6GD20)
 * – thank you! :-)
 *
 *
 * @author  Fabian Beiner (mail@fabian-beiner.de)
 * @link    http://fabian-beiner.de
 * @license Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported
 *
 * @version 5.5.5 (June 23rd, 2012)
*/

class IMDBException extends Exception {}

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
	var $review  = null;
}

class IMDB {
    // Define what to return if something is not found.
    public $strNotFound  = 'n/A';
    // Define the seperator (eg. for cast).
    public $strSeperator = ' / ';
    // Please set this to 'true' for debugging purposes only.
    const IMDB_DEBUG     = false;
    // Define a timeout for the request of the IMDb page.
    const IMDB_TIMEOUT   = 15;

    // Regular expressions, I would not touch them. :)
    const IMDB_AKA          = '~<h4 class="inline">Also Known As:</h4>(.*)<span~Ui';
    const IMDB_ASPECT_RATIO = '~<h4 class="inline">Aspect Ratio:</h4>(.*)</div>~Ui';
    const IMDB_BUDGET       = '~Budget:</h4>(.*)\(estimated\)~Ui';
    const IMDB_CAST         = '~<td class="name">\s+<a\s+onclick="(?:.*)"\s+href="/name/nm(\d+)/"\s+>(.*)</a>\s+</td~Ui';
    const IMDB_CHAR         = '~<td class="character">(.*)</td~Ui';
    const IMDB_COLOR        = '~href="/search/title\?colors=(?:.*)"\s+>(.*)</a>~Ui';
    const IMDB_COMPANY      = '~<h4 class="inline">Production Co:</h4>(.*)<span~Ui';
    const IMDB_COMPANY_NAME = '~href="/company/co(\d+)/"\s+>(.*)</a>~Ui';
    const IMDB_COUNTRY      = '~href="/country/(\w+)"(?:>|    >)(.*)</a>~Ui';
    const IMDB_CREATOR      = '~<h4 class="inline">\s+(Creator|Creators):\s+</h4>(.*)</div><div~Ui';
    
    const IMDB_CRITIC_COUNT = '~<span itemprop="reviewCount">([0-9]+)</span>\s*critic~Ui';    
    const IMDB_CRITIC_REVIEWS_COUNT = '~href="criticreviews"\s+title="([0-9]+)\s+review excerpts provided by Metacritic\.com"~Ui';
    
    const IMDB_DESCRIPTION  = '~<p itemprop="description">(.*)(?:<a |</p>)~Ui';
    const IMDB_DIRECTOR     = '~<h4 class="inline">\s+(?:Director|Directors):\s+</h4>(.*)</div>~Ui';
    const IMDB_GENRE        = '~href="/genre/(.*)"~Ui';
    const IMDB_LANGUAGES    = '~href="/language/(\w+)"\s+itemprop="inLanguage"\s+>(.*)</a>~Ui';
    const IMDB_LOCATION     = '~href="/search/title\?locations=(.+)"\s+>(.*)</a>~Ui';
    const IMDB_MPAA         = '~<span itemprop="contentRating">(.*)</span>~Ui';
    const IMDB_NAME         = '~href="/name/nm(\d+)/"\s+(?:itemprop="(?:\w+)"\s+>|>)(.*)</a>~Ui';
    const IMDB_OPENING      = '~<h4 class="inline">Opening Weekend:</h4>(.*)\(~Ui';
    const IMDB_PLOT         = '~<h2>Storyline</h2><p>(.*)(<em class="nobr">|</p>)~Ui';
    const IMDB_POSTER       = '~href="/media/(.*)"\s+><img src="(.*)"~Ui';
    const IMDB_RATING       = '~<span class="rating-rating"><span class="value".*?>(\d+\.\d+)</span>~Ui';
    const IMDB_REDIRECT     = '~Location:\s(.*)~';
    const IMDB_RELEASE_DATE = '~Release Date:</h4>(.*)(<span|</div>)~Ui';
    const IMDB_RUNTIME      = '~(\d+)\smin~Uis';
    const IMDB_SEARCH1      = '~<b>Titles \(Exact Matches\)</b> \(Displaying (?:\d+) Result(?:s|)\)<table><tr> <td valign="top"><a href="/title/tt(\d+)/~i';
    const IMDB_SEARCH2      = '~<b>Media from&nbsp;<a href="/title/tt(\d+)/"~i';
    const IMDB_SEASONS      = '~<h4 class="inline">Season:</h4><span class="see-more inline">(.*)</span></div>~Ui';
    const IMDB_SITES        = '~<h4 class="inline">Official Sites:</h4>(.*)</div>~Ui';
    const IMDB_SITES_A      = '~href="(.*)"\s+rel="nofollow"\s+>(.*)</a>~Ui';
    const IMDB_SOUND_MIX    = '~<h4 class="inline">Sound Mix:</h4>(.*)</div><div~Ui';
    const IMDB_SOUND_MIX_A  = '~href="/search/title\?sound_mixes=(.*)"\s+>(.*)</a>~Ui';
    const IMDB_TAGLINE      = '~<h4 class="inline">Taglines:</h4>(.*)(<[^>]+>)~Ui';
    const IMDB_TITLE        = '~og:title" content="(.*) \((.*)\)"~Ui';
    const IMDB_TITLE_ORIG   = '~<span class="title-extra">(.*) <i>\(original title\)</i></span>~Ui';
    const IMDB_TRAILER      = '~href="/video/(.*)/"~Ui';
    const IMDB_URL          = '~http://(.*\.|.*)imdb.com/(t|T)itle(\?|/)(..\d+)~i';
    const IMDB_VOTES        = '~<span itemprop="ratingCount">(.*)</span>~Ui';
    const IMDB_WRITER       = '~<h4 class="inline">\s+(?:Writer|Writers):\s+</h4>(.*)</div>~Ui';

    // new key word
    const IMDB_REVIEW_COUNT       = '~href="reviews"\s+title="(\d+) IMDb user reviews~Ui';
    const IMDB_USA_RELEASE_DATE       = '~USA(.*)<a href="/date/(\d+)(.*)<a href="/year/(\d+)/">~Ui';
           
    // cURL cookie file.
    private $_fCookie   = false;
    // IMDb url.
    private $_strUrl    = NULL;
    // IMDb source.
    private $_strSource = NULL;
    
    // IMDb review.
    private $_strReviews = NULL;
    // IMDb releaseinfo.
    private $_strReleaseInfo = NULL;
    
    // Reviewer Array
    private $arrReviewer = null;
    
    // IMDb cache.
    private $_strCache  = 0;
    // IMDb posters directory.
    private $_bolPoster = false;
    // IMDb cache directory.
    private $_bolCache  = false;
    // IMDb movie id.
    private $_strId     = false;
    // Movie found?
    public $isReady     = false;
    // Define root of this script.
    private $_strRoot   = '';
    // Current version.
    const IMDB_VERSION  = '5.5.5';

    /**
     * IMDB constructor.
     *
     * @param string  $strSearch The movie name / IMDb url
     * @param integer $intCache  The maximum age (in minutes) of the cache (default 1 day)
     */
    public function __construct($strSearch, $intCache = 1440) {
        if (!$this->_strRoot) {
            $this->_strRoot = dirname(__FILE__);
        }
        // Posters and cache directory existant?
        if (is_writable($this->_strRoot . '/posters/') || mkdir($this->_strRoot . '/posters/')) {
            $this->_bolPoster = true;
        }
        else {
            throw new IMDBException($this->_strRoot . '/posters/ is not writable!');
        }
        if (is_writable($this->_strRoot . '/cache/') || mkdir($this->_strRoot . '/cache/')) {
            $this->_bolCache = true;
        }
        else {
            throw new IMDBException($this->_strRoot . '/cache/ is not writable!');
        }
        // cURL.
        if (!function_exists('curl_init')) {
            throw new IMDBException('You need PHP with cURL enabled to use this script!');
        }
        // Debug only.
        if (IMDB::IMDB_DEBUG) {
            error_reporting(-1);
            ini_set('display_errors', 1);
            echo '<b>- Running:</b> IMDB::fetchUrl<br>';
        }
        // Set global cache and fetch the data.
        $this->_intCache = (int)$intCache;
        IMDB::fetchUrl($strSearch);
        
        // fetch Sub url
        IMDB::fetchSubUrl($this->_strUrl, "releaseinfo");
        IMDB::fetchSubUrl($this->_strUrl,  "reviews");
        
    }

    /**
     * Regular expressions helper function.
     *
     * @param string  $strContent The content to search in
     * @param string  $strRegex   The regular expression
     * @param integer $intIndex   The index to return
     * @return string The match found
     * @return array  The matches found
     */
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

    /**
     * Returns a shortened text.
     *
     * @param string  $strText   The text to shorten
     * @param integer $intLength The new length of the text
     */
    public function getShortText($strText, $intLength = 100) {
        $strText = trim($strText) . ' ';
        $strText = substr($strText, 0, $intLength);
        $strText = substr($strText, 0, strrpos($strText, ' '));
        return $strText . '…';
    }

    /**
     * Fetch data from the given url.
     *
     * @param string  $url The movie name / IMDb url
     * @param string  $sub The movie subinfo / releaseinfo, reviews, etc...
     * @param string  $strSave   The path to the file
     * @return boolean
     */
    private function fetchSubUrl($url, $sub) {
    	$cachefile = $url . $sub;
    	    
    	// Check if there is a cache we can use.
    	$fCache = $this->_strRoot . '/cache/' . md5($cachefile) . '.html';
    	if (file_exists($fCache)) {
    		$bolUseCache = true;
    		$intChanged  = filemtime($fCache);
    		$intNow      = time();
    		$intDiff     = round(abs($intNow - $intChanged) / 60);
    		if ($intDiff > $this->_intCache) {
    			$bolUseCache = false;
    		}
    	}
    	else {
    		$bolUseCache = false;
    	}
    
    	if ($bolUseCache) {
    		if (IMDB::IMDB_DEBUG) echo '<b>- Using cache for ' . $strSearch . ' from ' . $fCache . '</b><br>';
    		$strOutput = file_get_contents($fCache);
    		goto proc;
    	}
    	else {
    		// Cookie path.
    		if (function_exists('sys_get_temp_dir')) {
    			$this->_fCookie = tempnam(sys_get_temp_dir(), 'imdb');
    			if (IMDB::IMDB_DEBUG) echo '<b>- Path to cookie:</b> ' . $this->_fCookie . '<br>';
    		}
    		// Initialize and run the request.
    		if (IMDB::IMDB_DEBUG) echo '<b>- Run cURL on:</b> ' . $cachefile . '<br>';
    		$oCurl = curl_init($cachefile);
    		curl_setopt_array($oCurl, array (
    		CURLOPT_VERBOSE => FALSE,
    		CURLOPT_HEADER => TRUE,
    		CURLOPT_FRESH_CONNECT => TRUE,
    		CURLOPT_RETURNTRANSFER => TRUE,
    		CURLOPT_TIMEOUT => IMDB::IMDB_TIMEOUT,
    		CURLOPT_CONNECTTIMEOUT => 0,
    		CURLOPT_REFERER => 'http://www.google.com',
    		CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)',
    		CURLOPT_FOLLOWLOCATION => FALSE,
    		CURLOPT_COOKIEFILE => $this->_fCookie
    		));
    	
    	

    		$strOutput = curl_exec($oCurl);
    		
    		curl_close($oCurl);
    	}

proc:    	
   		$content = NULL;
    		
   		if ($sub == "releaseinfo")
   		{
   			$this->_strReleaseInfo = $strOutput;
   			$content = &$this->_strReleaseInfo;
   		}
   		else if ($sub == "reviews")
   		{
   			$this->_strReviews = $strOutput;
   			$content = &$this->_strReviews;
   		}
        		
   		// Remove cookie.
   		if ($this->_fCookie) {
   			unlink($this->_fCookie);
   		}
    
   		// Check if the request actually worked.
   		if ($strOutput === FALSE) {
   			if (IMDB::IMDB_DEBUG) echo '<b>! cURL error:</b> ' . $cachefile . '<br>';
   			if ($this->_strSource = @file_get_contents($fCache)) {
   				return true;
   			}
  			return false;
   		}     	 		
    
   		// Set the global source.   		
   		$content = preg_replace('~(\r|\n|\r\n)~', '', $content);
    
   		// Save cache.
		if (IMDB::IMDB_DEBUG) echo '<b>- Saved a new cache:</b> ' . $fCache . '<br>';
		file_put_contents($fCache, $content);
    
   		return true;

    }
    
    /**
     * Fetch data from the given url.
     *
     * @param string  $strSearch The movie name / IMDb url
     * @param string  $strSave   The path to the file
     * @return boolean
     */
    private function fetchUrl($strSearch) {
        // Remove whitespaces.
        $strSearch = trim($strSearch);

        // "Remote Debug" - so I can see which version you're running.
        // To due people complaing about broken functions while they're
        // using old versions. Feel free to remove this.
        if ($strSearch == '##REMOTEDEBUG##') {
            $strSearch = 'http://www.imdb.com/title/tt1022603/';
            echo '<pre>Running PHP-IMDB-Grabber v' . IMDB::IMDB_VERSION . '.</pre>';
        }

        // Check for a valid IMDb URL and use it, if available.
        if ($strId = IMDB::matchRegex($strSearch, IMDB::IMDB_URL, 4)) {
            $this->_strId  = preg_replace('~[\D]~', '', $strId);
            $this->_strUrl = 'http://www.imdb.com/title/tt' . $this->_strId . '/';
            $bolFind       = false;
            $this->isReady = true;
        }
        // Otherwise try to find one.
        else {
            $this->_strUrl = 'http://www.imdb.com/find?s=all&q=' . str_replace(' ', '+', $strSearch);
            $bolFind       = true;
            // Check for cached redirects of this search.
            if ($fRedirect = @file_get_contents($this->_strRoot . '/cache/' . md5($this->_strUrl) . '.redir')) {
                if (IMDB::IMDB_DEBUG) echo '<b>- Found an old redirect:</b> ' . $fRedirect . '<br>';
                $this->_strUrl = trim($fRedirect);
                $this->_strId  = preg_replace('~[\D]~', '', IMDB::matchRegex($fRedirect, IMDB::IMDB_URL, 4));
                $this->isReady = true;
                $bolFind       = false;
            }
        }

        // Check if there is a cache we can use.
        $fCache = $this->_strRoot . '/cache/' . md5($this->_strUrl) . '.html';
        if (file_exists($fCache)) {
            $bolUseCache = true;
            $intChanged  = filemtime($fCache);
            $intNow      = time();
            $intDiff     = round(abs($intNow - $intChanged) / 60);
            if ($intDiff > $this->_intCache) {
                $bolUseCache = false;
            }
        }
        else {
            $bolUseCache = false;
        }

        if ($bolUseCache) {
            if (IMDB::IMDB_DEBUG) echo '<b>- Using cache for ' . $strSearch . ' from ' . $fCache . '</b><br>';
            $this->_strSource = file_get_contents($fCache);
            return true;
        }
        else {
            // Cookie path.
            if (function_exists('sys_get_temp_dir')) {
                $this->_fCookie = tempnam(sys_get_temp_dir(), 'imdb');
                if (IMDB::IMDB_DEBUG) echo '<b>- Path to cookie:</b> ' . $this->_fCookie . '<br>';
            }
            // Initialize and run the request.
            if (IMDB::IMDB_DEBUG) echo '<b>- Run cURL on:</b> ' . $this->_strUrl . '<br>';
            $oCurl = curl_init($this->_strUrl);
            curl_setopt_array($oCurl, array (
                                            CURLOPT_VERBOSE => FALSE,
                                            CURLOPT_HEADER => TRUE,
                                            CURLOPT_FRESH_CONNECT => TRUE,
                                            CURLOPT_RETURNTRANSFER => TRUE,
                                            CURLOPT_TIMEOUT => IMDB::IMDB_TIMEOUT,
                                            CURLOPT_CONNECTTIMEOUT => 0,
                                            CURLOPT_REFERER => 'http://www.google.com',
                                            CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)',
                                            CURLOPT_FOLLOWLOCATION => FALSE,
                                            CURLOPT_COOKIEFILE => $this->_fCookie
                                            ));
            $strOutput = curl_exec($oCurl);
            $this->_strSource = $strOutput;

            // Remove cookie.
            if ($this->_fCookie) {
                unlink($this->_fCookie);
            }

            // Check if the request actually worked.
            if ($strOutput === FALSE) {
                if (IMDB::IMDB_DEBUG) echo '<b>! cURL error:</b> ' . $_strUrl . '<br>';
                if ($this->_strSource = @file_get_contents($fCache)) {
                    return true;
                }
                return false;
            }

            // Get returned information.
            $arrInfo = curl_getinfo($oCurl);
            curl_close($oCurl);

            // Check if there is a redirect given (IMDb sometimes does not return 301 for this...).
            $fRedirect = $this->_strRoot . '/cache/' . md5($this->_strUrl) . '.redir';
            if ($strMatch = $this->matchRegex($strOutput, IMDB::IMDB_REDIRECT, 1)) {
                $arrExplode = explode('?fr=', $strMatch);
                $strMatch   = ($arrExplode[0] ? $arrExplode[0] : $strMatch);
                if (IMDB::IMDB_DEBUG) echo '<b>- Found a redirect:</b> ' . $strMatch . '<br>';
                // Try to save the redirect for later usage.
                if (IMDB::IMDB_DEBUG) echo '<b>- Saved a new redirect:</b> ' . $fRedirect . '<br>';
                file_put_contents($fRedirect, $strMatch);
                // Run the cURL request again with the new url.
                IMDB::fetchUrl($strMatch);
            }
            // Check if any of the search regexes is matching.
            elseif (($strMatch = $this->matchRegex($strOutput, IMDB::IMDB_SEARCH1, 1)) || ($strMatch = $this->matchRegex($strOutput, IMDB::IMDB_SEARCH2, 1))) {
                $strMatch = 'http://www.imdb.com/title/tt' . $strMatch . '/';
                if (IMDB::IMDB_DEBUG) echo '<b>- Using the first search result:</b> ' . $strMatch . '<br>';
                // Try to save the redirect for later usage.
                if (IMDB::IMDB_DEBUG) echo '<b>- Saved a new redirect:</b> ' . $fRedirect . '<br>';
                file_put_contents($fRedirect, $strMatch);
                // Run the cURL request again with the new url.
                IMDB::fetchUrl($strMatch);
            }
            // If it's not a redirect and the HTTP response is not 200 or 302, abort.
            elseif ($arrInfo['http_code'] != 200 && $arrInfo['http_code'] != 302) {
                if (IMDB::IMDB_DEBUG) echo '<b>- Wrong HTTP code received, aborting:</b> ' . $arrInfo['http_code'] . '<br>';
                return false;
            }

            // Set the global source.
            $this->_strSource = preg_replace('~(\r|\n|\r\n)~', '', $this->_strSource);

            // Save cache.
            if (!$bolFind) {
                if (IMDB::IMDB_DEBUG) echo '<b>- Saved a new cache:</b> ' . $fCache . '<br>';
                file_put_contents($fCache, $this->_strSource);
            }

            return true;
        }
        return false;
    }

    /**
     * Save the image locally.
     *
     * @param string $_strUrl The URL to the image on imdb
     * @return string The local path to the image
     */
    private function saveImage($_strUrl) {
        $_strUrl = trim($_strUrl);

        if (preg_match('/imdb-share-logo.gif/', $_strUrl) && file_exists('posters/not-found.jpg')) {
            return 'posters/not-found.jpg';
        }

        $strFilename = $this->_strRoot . '/posters/' . $this->_strId . '.jpg';
        if (file_exists($strFilename)) {
            return 'posters/' . $this->_strId . '.jpg';
        }
        $oCurl = curl_init($_strUrl);
        curl_setopt_array($oCurl, array (
                                        CURLOPT_VERBOSE => FALSE,
                                        CURLOPT_HEADER => FALSE,
                                        CURLOPT_RETURNTRANSFER => TRUE,
                                        CURLOPT_TIMEOUT => IMDB::IMDB_TIMEOUT,
                                        CURLOPT_CONNECTTIMEOUT => 0,
                                        CURLOPT_REFERER => $_strUrl,
                                        CURLOPT_BINARYTRANSFER => TRUE));
        $sOutput = curl_exec($oCurl);
        $arrInfo = curl_getinfo($oCurl);
        curl_close($oCurl);
        if ($arrInfo['http_code'] != 200 && $arrInfo['http_code'] != 302) {
            return $_strUrl;
        }
        $oFile = fopen($strFilename, 'x');
        fwrite($oFile, $sOutput);
        fclose($oFile);
        return 'posters/' . $this->_strId . '.jpg';
    }

    /**
     * Returns the "also known as" name.
     *
     * @return string The aka name.
     */
    public function getAka() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_AKA, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the aspect ratio of the movie.
     *
     * @return string The aspect ratio.
     */
    public function getAspectRatio() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_ASPECT_RATIO, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the budget.
     *
     * @return string The movie budget.
     */
    public function getBudget() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_BUDGET, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the cast.
     *
     * @return array The movie cast (default limited to 20).
     */
    public function getCast($intLimit = 20, $bolMore = true) {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_CAST);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) {
                        break;
                    }
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '…' : '');
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the cast as URL.
     *
     * @return array The movie cast as URL (default limited to 20).
     */
    public function getCastAsUrl($intLimit = 20, $bolMore = true, $strTarget = '') {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_CAST);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) {
                        break;
                    }
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '…' : '');
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the cast and character.
     *
     * @return array The movie cast and character (default limited to 20).
     */
    public function getCastAndCharacter($intLimit = 20, $bolMore = true) {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_CAST);
            $arrChar     = $this->matchRegex($this->_strSource, IMDB::IMDB_CHAR);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) {
                        break;
                    }
                    $arrChar[1][$i] = trim(preg_replace('~\((.*)\)~Ui', '', strip_tags($arrChar[1][$i])));
                    if ($arrChar[1][$i]) {
                        $arrReturn[] = trim($strName) . ' as ' . trim($arrChar[1][$i]);
                    }
                    else {
                        $arrReturn[] = trim($strName);
                    }
                }
                return implode($this->strSeperator, $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '…' : '');
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the cast and character as URL .
     *
     * @return array The movie cast and character as URL (default limited to 20).
     */
    public function getCastAndCharacterAsUrl($intLimit = 20, $bolMore = true, $strTarget = '') {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_CAST);
            $arrChar     = $this->matchRegex($this->_strSource, IMDB::IMDB_CHAR);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) {
                        break;
                    }
                    $arrChar[1][$i] = trim(preg_replace('~\((.*)\)~Ui', '', $arrChar[1][$i]));
                    preg_match_all('~<a href="/character/ch(\d+)/">(.*)</a>~Ui', $arrChar[1][$i], $arrMatches);
                    if (isset($arrMatches[1][0]) && isset($arrMatches[2][0])) {
                        $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a> as <a href="http://www.imdb.com/character/ch' . trim($arrMatches[1][0]) . '/">' . trim($arrMatches[2][0]) . '</a>';
                    }
                    else {
                        if ($arrChar[1][$i]) {
                            $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a> as ' . strip_tags(trim($arrChar[1][$i]));
                        }
                        else {
                            $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                        }
                    }
                }
                return implode($this->strSeperator, $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '…' : '');
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the color.
     *
     * @return string The movie color.
     */
    public function getColor() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_COLOR, 1)) {
                return $strReturn;
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the companies.
     *
     * @return array The movie companies.
     */
    public function getCompany() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_COMPANY, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_COMPANY_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the companies as URL.
     *
     * @return array The movie companies as URL.
     */
    public function getCompanyAsUrl($strTarget = '') {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_COMPANY, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_COMPANY_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/company/co' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the countr(y|ies).
     *
     * @return array The movie countr(y|ies).
     */
    public function getCountry() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_COUNTRY);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the countr(y|ies) as URL
     *
     * @return array The movie countr(y|ies) as URL.
     */
    public function getCountryAsUrl($strTarget = '') {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_COUNTRY);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/country/' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the creator(s).
     *
     * @return array The movie creator(s).
     */
    public function getCreator() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_CREATOR, 2);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the creator(s) as URL.
     *
     * @return array The movie creator(s) as URL.
     */
    public function getCreatorAsUrl($strTarget = '') {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_CREATOR, 2);
            $arrReturned = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
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
     * Returns the description.
     *
     * @return string The movie description.
     */
    public function getDescription() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_DESCRIPTION, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the director(s).
     *
     * @return array The movie director(s).
     */
    public function getDirector() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_DIRECTOR, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the director(s) as URL.
     *
     * @return array The movie director(s) as URL.
     */
    public function getDirectorAsUrl($strTarget = '') {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_DIRECTOR, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the genre(s).
     *
     * @return array The movie genre(s).
     */
    public function getGenre() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_GENRE);
            if (count($arrReturned[1])) {
                foreach ($arrReturned[1] as $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, array_unique($arrReturn));
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the genres as URL.
     *
     * @return array The movie genre as URL.
     */
    public function getGenreAsUrl($strTarget = '') {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_GENRE);
            if (count($arrReturned[1])) {
               foreach ($arrReturned[1] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/genre/' . trim($strName) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, array_unique($arrReturn));
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the language(s).
     *
     * @return string The movie language(s).
     */
    public function getLanguages() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_LANGUAGES);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the language(s) as URL.
     *
     * @return string The movie language(s) as URL.
     */
    public function getLanguagesAsUrl($strTarget = '') {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_LANGUAGES);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/language/' . trim($arrReturned[1][$i]) . '"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the movie location.
     *
     * @return string The location of the movie.
     */
    public function getLocation() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_LOCATION, 2)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the movie location as URL.
     *
     * @return string The location of the movie as URL.
     */
    public function getLocationAsUrl($strTarget = '') {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_LOCATION, 2)) {
                return '<a href="http://www.imdb.com/search/title?locations=' . urlencode(trim($strReturn)) . '"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strReturn) . '</a>';
            }
        }
        return $this->strNotFound;
    }


    /**
     * Returns the MPAA.
     *
     * @return string The movie MPAA.
     */
    public function getOpening() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_OPENING, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the opening weekend revenue.
     *
     * @return string The opening weekend revenue.
     */
    public function getMpaa() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_MPAA, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the plot.
     *
     * @return string The movie plot.
     */
    public function getPlot($intLimit = 0) {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_PLOT, 1)) {
                if ($intLimit) {
                    return $this->getShortText($strReturn, $intLimit);
                }
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Download the poster, cache it and return the local path to the image.
     *
     * @return string The path to the poster (either local or online).
     */
    public function getPoster() {
       if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_POSTER, 2)) {
                if ($strLocal = $this->saveImage($strReturn)) {
                    return $strLocal;
                }
                return $strReturn;
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the rating.
     *
     * @return string The movie rating.
     */
    public function getRating() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_RATING, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the release date.
     *
     * @return string The movie release date.
     */
    public function getReleaseDate() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_RELEASE_DATE, 1)) {
                return str_replace('(', ' (', trim($strReturn));
            }
        }
        return $this->strNotFound;
    }

    /**
     *
     * @param string $nationName 개봉일을 얻을 국가명
     * @return string
     */
    public function getReleasInfoByNation($nationName) {
    	$strReaseInfo = NULL;
    	 
    	$arrMatches = null;
    	 
    	//preg_match_all('~<tr><td><b><a href="/calendar/\?region=(.+?)</tr>~Ui', $this->_strReleaseInfo, $arrMatches, PREG_PATTERN_ORDER);
    	preg_match_all('~<tr><td><b><a href="/calendar/\?region=(.+?)</tr>~Ui', $this->_strReleaseInfo, $arrMatches, PREG_PATTERN_ORDER);
    				   
    	/*
    	preg_match_all('%/date/(?P<dt>(?:1[0-2]|0[1-9])-(?:3[01]|[12][0-9]|0[1-9]))/"|/year/(?P<year>[0-9]+)/"%', $this->_strReleaseInfo, $arrMatches, PREG_PATTERN_ORDER);
    	for ($i = 0; $i < count($arrMatches[0]); $i++) {
    		# Matched text = $arrMatches[0][$i];
    	}   
    	*/ 	
    	
    	print "Match ReleaseInfo=" . count($arrMatches[0]) . "\n";
    	
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
    
    		if ($nation == $nationName) {
    			$strReaseInfo .= "$nation, " . $dt["year"][1] . "-" . $dt["dt"][0] . ",$loc\n";
    		}
    	}
    
    	return $strReaseInfo;
    }
    /**
     * Returns the Reviews.
     *
     * @return string The review count
     */
    public function getReviewCount() {
    	if ($this->isReady) {
    		if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_REVIEW_COUNT, 1)) {
    			return trim($strReturn);
    		}
    	}
    	return $this->strNotFound;
    }
    
    /**
     * Returns the runtime.
     *
     * @return string The movie runtime.
     */
    public function getRuntime() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_RUNTIME, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the seasons.
     *
     * @return string The movie seasons.
     */
    public function getSeasons() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_SEASONS)) {
                $strReturn = strip_tags(implode($strReturn[1]));
                $strFind   = array('&raquo;', '&nbsp;', 'Full episode list', ' ');
                $strReturn = str_replace($strFind, '', $strReturn);
                $arrReturn = explode('|', $strReturn);
                if ($arrReturn[0]) {
                    return implode($this->strSeperator, $arrReturn);
                }
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the official sites as URL.
     *
     * @return string The official sites as URL.
     */
    public function getSitesAsUrl($strTarget = '') {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_SITES, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_SITES_A);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="' . $arrReturned[1][$i] . '"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the sound mix(es).
     *
     * @return string The sound mix(es).
     */
    public function getSoundMix() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_SOUND_MIX, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_SOUND_MIX_A);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the tagline.
     *
     * @return string The movie tagline.
     */
    public function getTagline() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_TAGLINE, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Return the title.
     *
     * @return string The movie title.
     */
    public function getTitle() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_TITLE_ORIG, 1)) {
                return trim($strReturn);
            }
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_TITLE, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /** Return the first video found (should be the trailer). Thanks to Seifer Almasy.
     *
     * @return string The url to the trailer.
     */
    public function getTrailerAsUrl() {
       if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_TRAILER, 1)) {
                return 'http://www.imdb.com/video/' . $strReturn . '/player';
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the URL.
     *
     * @return string The movie URL.
     */
    public function getUrl() {
        return $this->_strUrl;
    }

    /**
     * Returns the votes.
     *
     * @return string The votes of the movie.
     */
    public function getVotes() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_VOTES, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the writer(s).
     *
     * @return array The movie writer(s).
     */
    public function getWriter() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_WRITER, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the writer(s) as URL.
     *
     * @return array The movie writer(s) as URL.
     */
    public function getWriterAsUrl($strTarget = '') {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_WRITER, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the movie year.
     *
     * @return string The year of the movie.
     */
    public function getYear() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_TITLE, 2)) {
                return substr(preg_replace('~[\D]~', '', $strReturn), 0, 4);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns all the information found.
     *
     * @return Object An object with all the information found.
     */
    public function getAll(){
        $oData = new stdClass;
        $oData->aka = $this->getAka();
        $oData->aspectRatio = $this->getAspectRatio();
        $oData->budget = $this->getBudget();
        $oData->cast = $this->getCast();
        $oData->castAsUrl = $this->getCastAsUrl();
        $oData->castAndCharacter = $this->getCastAndCharacter();
        $oData->castAndCharacterAsUrl = $this->getCastAndCharacterAsUrl();
        $oData->color = $this->getColor();
        $oData->company = $this->getCompany();
        $oData->companyAsUrl = $this->getCompanyAsUrl();
        $oData->country = $this->getCountry();
        $oData->countryAsUrl = $this->getCountryAsUrl();
        $oData->creator = $this->getCreator();
        $oData->creatorAsUrl = $this->getCreatorAsUrl();
        $oData->description = $this->getDescription();
        $oData->director = $this->getDirector();
        $oData->directorAsUrl = $this->getDirectorAsUrl();
        $oData->genre = $this->getGenre();
        $oData->genreAsUrl = $this->getGenreAsUrl();
        $oData->languages = $this->getLanguages();
        $oData->languagesAsUrl = $this->getLanguagesAsUrl();
        $oData->location = $this->getLocation();
        $oData->locationAsUrl = $this->getLocationAsUrl();
        $oData->mpaa = $this->getMpaa();
        $oData->opening = $this->getOpening();
        $oData->plot = $this->getPlot();
        $oData->poster = $this->getPoster();
        $oData->rating = $this->getRating();
        $oData->releaseDate = $this->getReleaseDate();
        $oData->runtime = $this->getRuntime();
        $oData->seasons = $this->getSeasons();
        $oData->soundMix = $this->getSoundMix();
        $oData->sitesAsUrl = $this->getSitesAsUrl();
        $oData->tagline = $this->getTagline();
        $oData->title = $this->getTitle();
        $oData->trailerAsUrl = $this->getTrailerAsUrl();
        $oData->url = $this->getUrl();
        $oData->votes = $this->getVotes();
        $oData->writers = $this->getWriter();
        $oData->writersAsUrl = $this->getWriterAsUrl();
        $oData->year = $this->getYear();
        return $oData;
    }
    
    public function getAllReviewsData() {
    	$this->arrReviewer = array();
    	 
    	$arrMatches = "";
    
    	//$fh  = fopen("Oops.log", "wb");
    
    	preg_match_all('/<hr .+?><p>(?P<rev>.+?)<div class="yn"/i', $this->_strReviews, $arrMatches, PREG_PATTERN_ORDER);
    	$arrMatches = $arrMatches[1];
    
    	$matCnt = 0;
    
    	for ($i = 0; $i < count($arrMatches); $i++) {
    		$reviews = new Reviewer();
    
    		$subject = $arrMatches[$i];
    
    		// html special char 가 붙은 comment 도 있으므로 변환한다.
    		$subject = htmlspecialchars_decode($subject);
    		
    		/*
    		fprintf($fh, "\n\nBefore =======================================================\n");
    		fprintf($fh, "%s", $arrMatches[$i]);
    		fprintf($fh, "\n\nAfter =======================================================\n");    		
    		fprintf($fh, "%s", $subject);
    		*/
    
    		//<small>408 out of 593 people found the following review useful:</small><br><b>Not fun, not even in a cheesy sense</b>, <small>7 March 2008</small><br><img width="102" height="12" alt="1/10" src="http://i.media-imdb.com/images/showtimes/10.gif"><br><small>Author:</small><a href="/user/ur5400353/comments">keiichi73</a> <small>from United States</small><br><p><b>*** This review may contain spoilers ***</b></p></p><p>Some critics have moaned that as film technology grows, thestorytelling ability of the movies shrinks. I have never quite agreedwith this assessment, as I believe there is a place for spectacle ofany variety, even the mindless kind. However, to those who share theview of those critics, 10,000 B.C. will most likely be the mostconvincing piece of evidence to their argument. Here is a movie thatlooks like it cost millions to make, but is saddled with a screenplaythat looks like it came from the Dollar Store.<br><br>Director and co-writer, Roland Emmerich is no stranger to brainlessspectacles. This is the guy who brought us Independence Day and 1998&#39;sHollywood take on Godzilla, after all. There&#39;s a very fine line betweenbrainless and just plain brain dead, unfortunately. 10,000 B.C. isshort on spectacle, short on plot, and short on just about anythingthat people go to the movies for. There are characters and a love storyto drive the bare bones plot, but this seems to be added in as anafterthought. I got the impression that Emmerich and fellowscreenwriter, Harald Kloser (a film score composer making his firstscreenplay credit), had the idea for a couple cool scenes, then triedto add a bunch of filler material between them. They threw in somesketchy characters that hardly reach two dimensions to inhabit thisfiller, and called it a screenplay. In order for spectacle to work,even the cheese-filled variety such as this, there has to be somethingfor the audience to get excited about. This movie is just one bigtease.<br><br>The plot, if it can even be called that, is set in the days of earlyman. The heroes are an unnamed tribal people who speak perfect English,all have the bodies of supermodels, and hunt mammoths for food. The twocharacters we&#39;re supposed to be focused on are a pair of young loversnamed D&#39;Leh (Steven Strait) and Evolet (Camilla Belle). Why they are inlove, and why we should care about them, the movie never goes out ofits way to explain. The rest of the villagers do not really matter.They exist simply to be captured when a group of foreign invaders comeriding into their peaceful tribe, and kidnap most of them to work asslaves back in their own home colony. Evolet is one of the captured, soD&#39;Leh and a small handful of others set out to find where they&#39;ve beentaken to, and to seek the aid of other tribes that have also beeninvaded by this enemy. There&#39;s a mammoth herd here, a saber tooth tigerthere, but they have nothing to do with anything. They&#39;re just computergenerated special effects who are there simply because the filmmakersfelt the current scene needed a special effect shot. I&#39;d be moreimpressed if the effects didn&#39;t look so out of place with the actorsmost of the time.<br><br>10,000 B.C. probably would have worked better as a silent movie, or asubtitled one, as most of the dialogue that comes out of the mouths ofthese people are as wooden as the spears they carry. The good tribesare the only people in this movie who have mastered the Queen&#39;sEnglish, naturally. The evil invading tribe speak in subtitles, andsometimes have their voices mechanically altered and lowered, so thatthey sound more threatening and demonic. No one in this movie isallowed to have a personality, or act differently from one another.Everybody in each tribe talks, thinks, and behaves exactly the same,with facial hair and differing body types being the main way to tellthem apart. This would make it hard to get involved in the story, butthe movie dodges this tricky issue by not even having a story in thefirst place. Once the film&#39;s main tribe is attacked, the movie turnsinto an endless string of filler material and padding to drag the wholething out to feature length. Aside from a brief encounter with somebird-like prehistoric creatures, there are no moments of action ordanger until D&#39;Leh and his followers reach the land of the invadingarmy. The movie throws a saber tooth tiger encounter to fool us intothinking something&#39;s gonna happen, but the tiger winds up being just asboring as the human characters inhabiting the movie, and is justmillions in special effects budget wasted on something that didn&#39;t needto be there in the first place, other than to move the shaky plotalong.<br><br>There is a key ingredient missing in 10,000 B.C., and that is fun. Thismovie is not fun to watch at all. I kept on waiting for something,anything, to happen. When something eventually did happen, it wasusually underwhelming. I know of people who are interested in seeingthis movie, because of the special effects, or because they think itlooks enjoyably cheesy. To those people, I say please do not be drawnin by curiosity. This isn&#39;t even enjoyable in a bad sense. Yourprecious time is worth more than what any theater may be charging tosee this movie. For anyone wondering, yes, that includes the budgetcinema and the price of a rental.</p>
    
    		//필요한 정보를 하나씩 추출하자..한 regexp 로 처리하기에는  너무 힘들다.
    		/* 실패한 코드
    		 if (preg_match('%<small>(?P<a>[0-9]+) out of (?P<b>[0-9]+) people found the following review useful:</small>|<b>(?P<title>.+?)</b>|<small>(?P<date>(?i:(?:3[01]|[12][0-9]|[1-9])[ \t]+(?:January|February|March|April|May|June|July|August|September|October|November|December)[ \t]+[0-9]{4}))</small>|<img .+?(?P<rate>1/10|10/10|2/10|3/10|4/10|5/10|6/10|7/10|8/10|9/10)[^\n\r>]+?>|<a [^\n\r>]+?>(?P<id>[\dA-Za-z]{9})</a>|<small>from (?P<loc>.+?)</small>|</p><p>(?P<review>.+?)</p>%', $subject, $regs)) {
    		*/
    
    		$subMatches = ""; $result="";
    
    		//1. extract reviews count
    		preg_match_all('/<small>(?P<rv>[0-9]+)(?-i: out of )(?P<all>[0-9]+)(?-i: people found the following review useful:)/i', $subject, $subMatches, PREG_PATTERN_ORDER);
    		//$reviews->useful = $subMatches[1][0];
    		if (count($subMatches['rv']) > 0)
    			$reviews->useful = $subMatches['rv'][0];
    		if (count($subMatches['all']) > 0)
    			$reviews->outof = $subMatches['all'][0];
    		 
    		///2. review write Date Time
    		//preg_match_all('%<small>(?P<dt>(?:1[0-2]|[1-9])[ \t]+March[ \t]+[0-9]{4})</small>%i', $subject, $subMatches, PREG_PATTERN_ORDER);
    		preg_match_all('%<small>(?P<dt>(?:3[01]|[12][0-9]|[1-9])[ \t]+(?:January|February|March|April|May|June|July|August|September|October|November|December)[ \t]+[0-9]{4})</small>%i', $subject, $subMatches, PREG_PATTERN_ORDER);
    		$reviews->rev_date = $subMatches['dt'][0];
    
    		//3. title
    		preg_match_all('%</small><br><b>(?P<title>.+?)</b>%i', $subject, $subMatches, PREG_PATTERN_ORDER);
    		if (count($subMatches['title']) > 0)
    			$reviews->title = $subMatches['title'][0];
    		 
    		//4. rating
    		preg_match_all('%<img .+?alt="(?P<rate>[0-9]+)/[0-9]+.+?>%i', $subject, $subMatches, PREG_PATTERN_ORDER);
    		// rate 없는 review 도 있네 --;
    		// http://www.imdb.com/title/tt0075610/reviews 여기에서 tedg (tedg@FilmsFolded.com) 가 작성한 리뷰
    		if (count($subMatches['rate']) > 0)
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
    
    		// 8. review : remark 가 있는 경우가 있으니 확인
    		preg_match_all('%<p>(?P<review>.+?)</p>%i', $subject, $subMatches, PREG_PATTERN_ORDER);
    		$rc =count($subMatches['review']);
    		if ($rc == 1)
    			$reviews->review = $subMatches['review'][0];
    		else if ($rc == 2)
    			$reviews->review = $subMatches['review'][1];
    		
    		array_push($this->arrReviewer, $reviews) ;
    	}
    
    	/*
    	for ($i = 0; $i < count($this->arrReviewer); $i++) {
    		$reviews = $this->arrReviewer[$i];
    
    		var_dump($reviews);
    		//var_dump($reviews->useful);
    		//print $reviews->useful[0] . "\n";
    	}
    	*/
    	
    	//print "Total match = $matCnt\n";
    	 
    	//fclose($fh);
    	return $this->arrReviewer;    
    }
}
