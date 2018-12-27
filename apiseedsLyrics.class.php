<?php

/*
Version   Date        Change
--------  ----------  -----------------------------------------------------
1.0       2018-..-..  Initial version
*/

// a very simple implementation of ApiSeeds Lyrics API
//
// Usage:
// $obj = new apiseedsLyrics('The Beatles', 'Yesterday') -- bool
// $obj -> getLyrics() -- string
// $obj -> getArtistName() -- string
// $obj -> getTrackName() -- string
// $obj -> getLangCode() -- string (ex: 'en')
// $obj -> getLangName() -- string (ex: 'English')
// $obj -> getProbability() -- int (0-100)
// $obj -> getSimilarity() -- int
// $obj -> getRemainingRate() -- int (when 0, cannot issue new requests)
// $obj -> getRemainingCredits() -- int (when 0, cannot issue new requests)

class apiseedsLyrics
{
	private $api_key = 'fUbDCpFi4MPvKoQ1n8HJVK4Nw2IewFAtM1gg50Lh378y4H8x8fcBIXARp9VU8mI9';
	private $urlpattern = 'https://orion.apiseeds.com/api/music/lyric/:artist/:track?apikey=:apikey';
	private $artist;
	private $track;
	private $lyrics;
	private $probability;
	private $similarity;
	private $credits;
	private $rate;
	private $langCode, $langName;

	public function
	__construct (string $artist, string $track)
	{
		$url = $this -> urlpattern;
		$url = str_replace (':artist', urlencode($artist), $url);
		$url = str_replace (':track',  urlencode($track), $url);
		$url = str_replace (':apikey', $this->api_key, $url);

		$http = new tinyHttp ($url);
		$r = $http->send();

		switch ($r -> getStatus() )
		{
		case 200: // ok
			break;

		case 404 : // no lyrics found
			$str = $r -> getBody();
			$a = json_decode ($str, true);
			throw new Exception($a['error']);
			//throw new Exception ('no lyrics found');

		default :	// misc errors
tracelog ('error: ' . $http->getBody() );
			throw new Exception ('http code ' . $r -> getStatus());
		}

		$str = $r -> getBody();
		if ($str == '')
			throw new Exception ('empty answer');

		$a = json_decode ($str, true);
		if (!$a)
			throw new Exception ('could not decode reply');

		if (!array_key_exists ('result', $a))
			throw new Exception ('no result');

		$res = $a['result'];

		$this->artist = $res['artist']['name'];
		$this->track = $res['track']['name'];
		$this->langCode = $res['track']['lang']['code'];
		$this->langName = $res['track']['lang']['name'];

		$lyrics = str_replace ('\n', "\n", $res['track']['text']);
		$lyrics = htmlspecialchars_decode ($lyrics);
		$this -> lyrics = $lyrics;

		//$res['copyright']['notice']
		//$res['copyright']['artist']
		//$res['copyright']['text']
		$this->probability = $res['probability'];
		$this->similarity = $res['similarity'];
		$this->credits = $r->getHeader('X-Credits');
		$this->rate = $r->getHeader('X-RateLimit-Remaining');

		return true;
	}


	public function
	getLyrics(): string
	{
		return $this -> lyrics;
	}

	public function
	getArtistName(): string
	{
		return $this -> artist;
	}

	public function
	getTrackName(): string
	{
		return $this -> track;
	}

	// returns: 0-100
	public function
	getProbability(): int
	{
		return $this -> probability;
	}

	public function
	getSimilarity(): int
	{
		return $this -> similarity;
	}

	// max requests that can still be issued today
	public function
	getRemainingCredits(): int
	{
		return $this -> credits;
	}

	// max requests that can still be issued during this minute
	public function
	getRemainingRate(): int
	{
		return $this -> rate;
	}

	public function
	getLangCode(): string
	{
		return $this -> langCode;
	}

	public function
	getLangName(): string
	{
		return $this -> langName;
	}
}

/*
demo@apiseeds: ~$ GET  https://orion.apiseeds.com/api/music/lyric/Gainsbourg/Javanaise?apikey=fUbDCpFi4MPvKoQ1n8HJVK4Nw2IewFAtM1gg50Lh378y4H8x8fcBIXARp9VU8mI9 
demo@apiseeds: ~$ Request time: Thu Jun 28 2018 14:15:46 GMT+0200 (heure d.été d.Europe centrale) 
demo@apiseeds: ~$ Waiting response ... 
demo@apiseeds: ~$ http status code: 404 
demo@apiseeds: ~$ http status text:  
demo@apiseeds: ~$ [HEADER] X-Credits: 20000 
demo@apiseeds: ~$ [HEADER] X-Credits-Premium: 0 
demo@apiseeds: ~$ [HEADER] X-RateLimit-Limit: 200 
demo@apiseeds: ~$ [HEADER] X-RateLimit-Remaining: 199 
  
application/json; charset=utf-8 
  
{
  "error": "Lyric no found, try again later."
} 
*/

/*
{"result":{"artist":{"name":"The Beatles"},"track":{"name":"Yesterday","text":"[Verse 1]\nYesterday\nAll my troubles seemed so far away\nNow it looks as though they're here to stay\nOh, I believe in yesterday\n\n[Verse 2]\nSuddenly\nI'm not half the man I used to be\nThere's a shadow hanging over me\nOh, yesterday came suddenly\n\n[Chorus]\nWhy she had to go\nI don't know, she wouldn't say\nI said something wrong\nNow I long for yesterday\n\n[Verse 3]\nYesterday\nLove was such an easy game to play\nNow I need a place to hide away\nOh, I believe in yesterday\n\n[Chorus]\nWhy she had to go\nI don't know, she wouldn't say\nI said something wrong\nNow I long for yesterday\n\n[Verse 4]\nYesterday\nLove was such an easy game to play\nNow I need a place to hide away\nOh, I believe in yesterday","lang":{"code":"en","name":"English"}},"copyright":{"notice":"Yesterday lyrics are property and copyright of their owners. Commercial use is not allowed.","artist":"Copyright The Beatles","text":"All lyrics provided for educational purposes and personal use only."},"probability":100,"similarity":1}}
*/
?>
