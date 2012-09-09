<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>PHP-IMDB-Grabber by Fabian Beiner | Examples</title>
  <style>
    body {
      background-color:#aaa;
      color:#111;
      font-family:Corbel, "Lucida Grande", "Lucida Sans Unicode",  "Lucida Sans", "DejaVu Sans", "Bitstream Vera Sans", "Liberation Sans", Verdana, sans-serif;
      font-size:14px;
      margin:20px auto;
      width:700px;
    }
    p {
      margin:0;
      padding:0;
      margin-bottom:5px;
    }
    hr {
      clear:both;
      margin:20px 0;
    }
  </style>
</head>
<body>
<?php
include_once 'imdb.class.php';

$fos = null;

function print_file($file, $msg)
{
	fprintf ($file, $msg);
	echo $msg;	
}

$data = file_get_contents('input.txt', true);
$convert = explode("\n", $data); //create array separate by new line

foreach ($convert as $movie)
{
	$movie = chop($movie);
	if ($movie == "")
		continue;
	
	$fos = fopen($movie . ".html", 'wb');
	
	$oIMDB = new IMDB($movie);
	if ($oIMDB->isReady) {
		print_file($fos,"<ol>");
        print_file($fos,'<li><p>Also Known As: <b>' . $oIMDB->getAka() . '</b></p></li>');
        print_file($fos,'<li><p>Budget: <b>' . $oIMDB->getBudget() . '</b></p></li>');
        print_file($fos,'<li><p>Cast (limited to 5): <b>' . $oIMDB->getCast(5) . '</b></p></li>');
        print_file($fos,'<li><p>Cast as URL (default limited to 20): <b>' . $oIMDB->getCastAsUrl() . '</b></p></li>');
        print_file($fos,'<li><p>Cast and Character (limited to 10): <b>' . $oIMDB->getCastAndCharacter(10) . '</b></p></li>');
        print_file($fos,'<li><p>Cast and Character as URL (limited to 10): <b>' . $oIMDB->getCastAndCharacterAsUrl(10) . '</b></p></li>');
        print_file($fos,'<li><p>Color: <b>' . $oIMDB->getColor() . '</b></p></li>');
        print_file($fos,'<li><p>Company as URL: <b>' . $oIMDB->getCompanyAsUrl() . '</b></p></li>');
        print_file($fos,'<li><p>Company: <b>' . $oIMDB->getCompany() . '</b></p></li>');
        print_file($fos,'<li><p>Countries as URL: <b>' . $oIMDB->getCountryAsUrl() . '</b></p></li>');
        print_file($fos,'<li><p>Countries: <b>' . $oIMDB->getCountry() . '</b></p></li>');
        print_file($fos,'<li><p>Creators as URL: <b>' . $oIMDB->getCreatorAsUrl() . '</b></p></li>');
        print_file($fos,'<li><p>Creators: <b>' . $oIMDB->getCreator() . '</b></p></li>');
        print_file($fos,'<li><p>Directors as URL: <b>' . $oIMDB->getDirectorAsUrl() . '</b></p></li>');
        print_file($fos,'<li><p>Directors: <b>' . $oIMDB->getDirector() . '</b></p></li>');
        print_file($fos,'<li><p>Genres as URL: <b>' . $oIMDB->getGenreAsUrl() . '</b></p></li>');
        print_file($fos,'<li><p>Genres: <b>' . $oIMDB->getGenre() . '</b></p></li>');
        print_file($fos,'<li><p>Languages as URL: <b>' . $oIMDB->getLanguagesAsUrl() . '</b></p></li>');
        print_file($fos,'<li><p>Languages: <b>' . $oIMDB->getLanguages() . '</b></p></li>');
        print_file($fos,'<li><p>Location as URL: <b>' . $oIMDB->getLocationAsUrl() . '</b></p></li>');
        print_file($fos,'<li><p>Location: <b>' . $oIMDB->getLocation() . '</b></p></li>');
        print_file($fos,'<li><p>MPAA: <b>' . $oIMDB->getMpaa() . '</b></p></li>');
        print_file($fos,'<li><p>Plot (shortened to 150 chars): <b>' . $oIMDB->getPlot(150) . '</b></p></li>');
        print_file($fos,'<li><p>Poster: <b>' . $oIMDB->getPoster() . '</b></p></li>');
        print_file($fos,'<li><p>Rating: <b>' . $oIMDB->getRating() . '</b></p></li>');
        print_file($fos,'<li><p>Release Date: <b>' . $oIMDB->getReleasInfoByNation('USA') . '</b></p></li>');
        
        print_file($fos,'<li><p>Reviews: ' . '</p></li>');
        print_file($fos,'<ul><li><p>Reviews Count: <b>' . $oIMDB->getReviewCount() . '</b></p></li>');
        print_file($fos,'</ul>'); // Endof Reviews
        
        print_file($fos,'<li><p>Runtime: <b>' . $oIMDB->getRuntime() . '</b></p></li>');
        print_file($fos,'<li><p>Seasons: <b>' . $oIMDB->getSeasons() . '</b></p></li>');
        print_file($fos,'<li><p>Tagline: <b>' . $oIMDB->getTagline() . '</b></p></li>');
        print_file($fos,'<li><p>Title: <b>' . $oIMDB->getTitle() . '</b></p></li>');
        print_file($fos,'<li><p>Trailer: <br>');
        if ($oIMDB->getTrailerAsUrl() != 'n/A') {
            print_file($fos,'<iframe width="660" height="500" scrolling="no" border="0" src="' . $oIMDB->getTrailerAsUrl() . '"></iframe>');
        }
        else {
            print_file($fos,'No trailer found.');
        }
        print_file($fos,'</p></li>');
        print_file($fos,'<p><li>Url: <b><a href="' . $oIMDB->getUrl() . '">' . $oIMDB->getUrl() . '</a></b></p></li>');
        print_file($fos,'<p><li>Votes: <b>' . $oIMDB->getVotes() . '</b></p></li>');
        print_file($fos,'<p><li>Writers as URL: <b>' . $oIMDB->getWriterAsUrl() . '</b></p></li>');
        print_file($fos,'<p><li>Writers: <b>' . $oIMDB->getWriter() . '</b></p></li>');
        print_file($fos,'<p><li>Year: <b>' . $oIMDB->getYear() . '</b></p></li>');
        
        print_file($fos,"</ol>");		
	}
	else {
	    print_file($fos,'<p>Movie not found!</p>');
	}
	
	fclose($fos);
}
?>

<hr>

<?php
/**
$oIMDB = new IMDB('http://www.imdb.com/title/tt1022603/');
if ($oIMDB->isReady) {
    echo '<p><a href="' . $oIMDB->getUrl() . '">' . $oIMDB->getTitle() . '</a> got rated ' . $oIMDB->getRating() . '.</p>';
    echo '<p><img src="' . $oIMDB->getPoster() . '" style="float:left;margin:4px 10px 10px 0;"> <b>About the movie:</b> ' . $oIMDB->getPlot() . '</p>';
}
else {
    echo '<p>Movie not found!</p>';
}
*/
?>

</body>
</html>
