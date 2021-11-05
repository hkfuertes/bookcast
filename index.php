<?php
    include_once("libs/mp3file.class.php");

    // Variables section
    $protocol = ($_SERVER['HTTPS'] == true) ? "https://": "http://";
    $link = $protocol.$_SERVER['HTTP_HOST'];

    $podcastFolder = "/podcasts/";
    $localPodcastFolder = dirname(__FILE__).$podcastFolder;
    $folder = htmlspecialchars($_GET["dir"]);
    $fileList = glob($localPodcastFolder.$folder.'/*.mp3');


    $xml = new DOMDocument(); 
    $root = $xml->appendChild($xml->createElement('rss')); 
    $root->setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd'); 
    $root->setAttribute('xmlns:media', 'http://search.yahoo.com/mrss/'); 
    $root->setAttribute('xmlns:feedburner', 'http://rssnamespace.org/feedburner/ext/1.0'); 
    $root->setAttribute('version', '2.0'); 

    $chan = $root->appendChild($xml->createElement('channel')); 
    $chan->appendChild($xml->createElement('title', $folder)); 
    $chan->appendChild($xml->createElement('link', $link)); 
    $chan->appendChild($xml->createElement('generator', 'SitePoint Podcast Tutorial')); 
    $chan->appendChild($xml->createElement('language', "en-en")); 

    foreach ($fileList as $episode) { 
        $audioURL = $link.$podcastFolder.$folder."/".basename($episode);

        $item = $chan->appendChild($xml->createElement('item')); 
        $item->appendChild($xml->createElement('title', $folder." [".basename($episode)."]")); 
        $item->appendChild($xml->createElement('link', $audioURL)); 
        $item->appendChild($xml->createElement('itunes:author', $folder)); 
        $item->appendChild($xml->createElement('itunes:summary', "SUMMARY")); 
        $item->appendChild($xml->createElement('guid', $audioURL)); 

        $finfo = finfo_open(FILEINFO_MIME_TYPE); 
        $enclosure = $item->appendChild($xml->createElement('enclosure')); 
        $enclosure->setAttribute('url', $audioURL); 
        $enclosure->setAttribute('length', filesize($episode)); 
        $enclosure->setAttribute('type', finfo_file($finfo, $episode)); 

        $item->appendChild($xml->createElement('pubDate', date('D, d M Y H:i:s O'))); 

        $mp3file = new MP3File($episode); 
        $item->appendChild($xml->createElement('itunes:duration', MP3File::formatTime($mp3file->getDurationEstimate()))); 
    } 

    $xml->formatOutput = true; 

    header ("Content-Type:text/xml");
    print $xml->saveXML(); 