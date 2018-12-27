<?php
require_once '../tinyHttp/tinyHttp.class.php';
require_once 'apiseedsLyrics.class.php';

$obj = new apiseedsLyrics('The Beatles', 'Yesterday');

echo "------------------------------------------\n";
echo 'Artist name: ' . $obj -> getArtistName() . "\n";
echo 'Track name: ' . $obj -> getTrackName() . "\n";
echo 'Lang code: ' . $obj -> getLangCode() . "\n";
echo 'Lang name: ' . $obj -> getLangName() . "\n";
echo 'Probability: ' . $obj -> getProbability() . "\n";
echo 'Similarity: ' . $obj -> getSimilarity() . "\n";
echo 'Remaining rate: ' . $obj -> getRemainingRate() . "\n";
echo 'Remaining credits: ' . $obj -> getRemainingCredits() . "\n";
echo "------------------------------------------\n";
echo 'Lyrics: ' . $obj -> getLyrics() . "\n";
echo "------------------------------------------\n";
?>
