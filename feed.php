<?php
    // Variables section
    $protocol = ($_SERVER['HTTPS'] == true) ? "https://": "http://";
    $host=$protocol.$_SERVER['HTTP_HOST'];
    $link =$host.$_SERVER['REQUEST_URI'];

    $folder = ""; // Change it to .
    $localFolder = dirname(__FILE__).$folder;
    //$folder = str_replace("/podcast.xml","",htmlspecialchars($_GET["dir"]));

    /**
        Info -> title, author, summary, chapters (assoc array with filenames)
        Paths -> localFolder, folderUrl
        FileList
    */
    $info = getInfo($localFolder);
    $fileList = glob($localFolder.'/*.mp3');
    $paths = [
        'localFolder' => $localFolder.$folder,
        'folderUrl' => str_replace(basename(__FILE__),"", $link),
    ];

    $xml = generateFeed($info, $paths, $fileList );
    header ("Content-Type:text/xml");
    print $xml->saveXML();
   


    ##########################################################################################################

    function getInfo($localPodcastFolder){
        if(!file_exists($localPodcastFolder.DIRECTORY_SEPARATOR."info.json"))
        die("Info file not present!");
    
        $info = json_decode(file_get_contents($localPodcastFolder.$folder.DIRECTORY_SEPARATOR."info.json"),true);

        if(
            !array_key_exists("title", $info) ||
            !array_key_exists("author", $info) ||
            !array_key_exists("summary", $info)
        )
            die("Info file missing fields!");

        return $info;
    }

    ##########################################################################################################
    /**
        Info -> title, author, summary,
        Paths -> localFolder, folderUrl
        FileList
    */
    function generateFeed($info, $paths, $fileList){
        $xml = new DOMDocument(); 
        $root = $xml->appendChild($xml->createElement('rss')); 
        $root->setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd'); 
        $root->setAttribute('xmlns:media', 'http://search.yahoo.com/mrss/'); 
        $root->setAttribute('xmlns:feedburner', 'http://rssnamespace.org/feedburner/ext/1.0'); 
        $root->setAttribute('version', '2.0'); 

        $chan = $root->appendChild($xml->createElement('channel')); 
        $chan->appendChild($xml->createElement('title', $info['title'])); 
        $thispage = $paths['folderUrl'].basename(__FILE__);
        $chan->appendChild($xml->createElement('link', $thispage)); 
        $chan->appendChild($xml->createElement('generator', 'Simple Podcast Generator Script')); 
        $chan->appendChild($xml->createElement('language', "en-en")); 

        //Set Image
        if(file_exists($paths['localFolder']."/cover.jpg")){
            $image = $xml->createElement('itunes:image');
            $cover = $xml->createAttribute("href");
            $cover->value = $paths['folderUrl']."cover.jpg";
            $image->appendChild($cover);
            $chan->appendChild($image);
        }
        
        $now= new DateTime();
        $number_padding=0;
        $show_number = false;
        foreach ($fileList as $index => $episode) { 
            $episodeName = array_reverse(explode("/", $episode))[0];
            $audioURL = str_replace("/","/",$paths['folderUrl'].basename($episode));

            $item = $chan->appendChild($xml->createElement('item')); 

            if (array_key_exists("chapters",$info) && array_key_exists($episodeName, $info['chapters'])){
                if($show_number){
                    $item->appendChild($xml->createElement('title',str_pad($index, $number_padding, '0', STR_PAD_LEFT).". ".$info['chapters'][$episodeName]));
                }else{
                    $item->appendChild($xml->createElement('title',$info['chapters'][$episodeName]));
                }
                }else{
                if(file_exists($paths['localFolder']."/EPISODES_NO_TITLE")){
                    $title = str_replace(".mp3","",basename($episode));
                    $title = str_replace("_"," ", $title);
                    $title = str_replace("  ", " ", $title);
                    if($show_number){
                        $item->appendChild($xml->createElement('title',str_pad($index, $number_padding, '0', STR_PAD_LEFT).". ".$title));
                    }else{
                        $item->appendChild($xml->createElement('title',$title));
                    }
                }else{
                    if($show_number){
                        $item->appendChild($xml->createElement('title', str_pad($index, $number_padding, '0', STR_PAD_LEFT).". ".$info['title']." [".basename($episode)."]")); 
                    }else{
                        $item->appendChild($xml->createElement('title',$info['title']." [".basename($episode)."]")); 
                    }
                }
            }
            
            $item->appendChild($xml->createElement('link', $audioURL)); 
            $item->appendChild($xml->createElement('itunes:author', $info['author'])); 
            $item->appendChild($xml->createElement('itunes:summary', $info['summary'])); 
            $item->appendChild($xml->createElement('guid', $audioURL)); 

            $finfo = finfo_open(FILEINFO_MIME_TYPE); 
            $enclosure = $item->appendChild($xml->createElement('enclosure')); 
            $enclosure->setAttribute('url', $audioURL); 
            $enclosure->setAttribute('length', filesize($episode)); 
            $enclosure->setAttribute('type', finfo_file($finfo, $episode)); 

            //$item->appendChild($xml->createElement('pubDate', date('D, d M Y H:i:s O', filectime($episode))));
            $item->appendChild($xml->createElement('pubDate', date('D, d M Y H:i:s O', (strtotime("+".$index." seconds", $now->getTimestamp())) ))); 
} 

        $xml->formatOutput = true;
        return $xml; 
    }

