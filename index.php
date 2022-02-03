<?php
$protocol = ($_SERVER['HTTPS'] == true) ? "https://": "http://";
$host=$protocol.$_SERVER['HTTP_HOST'];
$booksFolder = "books";

echo "<h1>List of AudioBooks:</h1>";
echo "<p>Copy and paste the link of the book into <a href='https://f-droid.org/en/packages/de.danoeh.antennapod/'>AntenaPod</a>.<br/>";
echo "In order for a book to apear both <a href='info.json' download><b><i>info.json</i></b></a> and <a href='feed.php' download><b><i>feed.php</i></b></a> have to exist in the folder and be correct.<br/>";
echo "Optionally a <b><i>cover.jpg</i></b> can exist in the book folder to be the cover image of the book.</p>";
$folders = glob($booksFolder."/*", GLOB_ONLYDIR);
foreach($folders as $folder){
    $podcastLink = $host."/".$booksFolder."/".basename($folder)."/feed.php";
    @$info = json_decode(file_get_contents($folder."/info.json"),true);
    if($info != null && file_exists($folder."/feed.php")){
        echo "<b>Title: </b>".$info['title']."<br/>";
        echo "<b>Author: </b>".$info['author']."<br/>";
        //echo $podcastLink."<br/>";
        //echo "<a href='$podcastLink'>Feed</a><br/>";
        echo "<blockquote>".$podcastLink."</blockquote>";
        echo "<br/>";
    }
    
}