<?php
    //If password is set, then we enforce it.
    $password = getenv('PASSWORD',null);
    if($password!= null && $password != ""){
        if($password != htmlspecialchars($_GET["password"])){
            header('HTTP/1.0 401 Unauthorized');
            die("Not authorized!");
        }
    }

    // Variables section
    $protocol = ($_SERVER['HTTPS'] == true) ? "https://": "http://";
    $host=$protocol.$_SERVER['HTTP_HOST'];
    $link =$host.$_SERVER['REQUEST_URI'];

    $podcastsFolder = "/podcasts/";
    $localPodcastsFolder = dirname(__FILE__).$podcastsFolder;
    $folder = str_replace("/podcast.xml","",htmlspecialchars($_GET["dir"]));
 
    if($folder != null){
        /**
            Info -> title, author, summary,
            Paths -> localPodcastFolder, podcastFolderUrl, podcastLink
            FileList
        */
        $info = getInfo($localPodcastsFolder.$folder);
        $fileList = glob($localPodcastsFolder.$folder.'/*.mp3');
        $paths = [
            'localPodcastFolder' => $localPodcastsFolder.$folder,
            'podcastFolderUrl' => $host.$podcastsFolder.$folder,
            'podcastLink'=> $link
        ];

        $xml = generateFeed($info, $paths, $fileList );
        header ("Content-Type:text/xml");
        print $xml->saveXML();
    }else{
        echo "<h1>List of AudioBooks:</h1>";
        $folders = glob($localPodcastsFolder."/*", GLOB_ONLYDIR);
        foreach($folders as $folder){
            $podcastLink = $host."/".basename($folder)."/podcast.xml";
            if($password != null && $password!="")
                $podcastLink.="?password=".$password;
            @$info = json_decode(file_get_contents($folder."/info.json"),true);
            if($info != null){
                echo "<b>Title: </b>".$info['title']."<br/>";
                echo "<b>Author: </b>".$info['author']."<br/>";
                //echo $podcastLink."<br/>";
                echo "<a href='$podcastLink'>Feed</a><br/>";
                echo "<br/>";
            }
            
        }
    }


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
        Paths -> localPodcastFolder, podcastFolderUrl, podcastLink
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
        $chan->appendChild($xml->createElement('link', $paths['podcastLink'])); 
        $chan->appendChild($xml->createElement('generator', 'Simple Podcast Generator Script')); 
        $chan->appendChild($xml->createElement('language', "en-en")); 

        //Set Image
        if(file_exists($paths['localPodcastFolder']."/cover.jpg")){
            $image = $xml->createElement('itunes:image');
            $cover = $xml->createAttribute("href");
            $cover->value = $paths['podcastFolderUrl']."/cover.jpg";
            $image->appendChild($cover);
            $chan->appendChild($image);
        }
        
        foreach ($fileList as $episode) { 
            $audioURL = $paths['podcastFolderUrl']."/".basename($episode);

            $item = $chan->appendChild($xml->createElement('item')); 
            $item->appendChild($xml->createElement('title', $info['title']." [".basename($episode)."]")); 
            $item->appendChild($xml->createElement('link', $audioURL)); 
            $item->appendChild($xml->createElement('itunes:author', $info['author'])); 
            $item->appendChild($xml->createElement('itunes:summary', $info['summary'])); 
            $item->appendChild($xml->createElement('guid', $audioURL)); 

            $finfo = finfo_open(FILEINFO_MIME_TYPE); 
            $enclosure = $item->appendChild($xml->createElement('enclosure')); 
            $enclosure->setAttribute('url', $audioURL); 
            $enclosure->setAttribute('length', filesize($episode)); 
            $enclosure->setAttribute('type', finfo_file($finfo, $episode)); 

            $item->appendChild($xml->createElement('pubDate', date('D, d M Y H:i:s O', filectime($episode)))); 
} 

        $xml->formatOutput = true;
        return $xml; 
    }

