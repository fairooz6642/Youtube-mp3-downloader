<?php error_reporting (E_ALL ^ E_NOTICE); ?>
<?php
    define("MAX_RESULTS", 4);

     if (isset($_POST['submit']) )
     {
        $keyword = $_POST['keyword'];


    }

?>



<?php
// Load and initialize downloader class
if (isset($_POST["submit"])) {



include_once 'Youtube.class.php';
$handler = new YouTubeDownloader();

// Youtube video url
$youtubeURL = $_POST["url"];

// Check whether the url is valid
if(!empty($youtubeURL) && !filter_var($youtubeURL, FILTER_VALIDATE_URL) === false){
    // Get the downloader object
    $downloader = $handler->getDownloader($youtubeURL);

    // Set the url
    $downloader->setUrl($youtubeURL);

    // Validate the youtube video url
    if($downloader->hasVideo()){
        // Get the video download link info
        $videoDownloadLink = $downloader->getVideoDownloadLink();

        $videoTitle = $videoDownloadLink[0]['title'];
        $videoQuality = $videoDownloadLink[0]['quality'];
        $videoFormat = $videoDownloadLink[0]['format'];
        $videoFileName = strtolower(str_replace(' ', '_', $videoTitle)).'.'.$videoFormat;
        $downloadURL = $videoDownloadLink[0]['url'];

        //
        $fileName = preg_replace('/[^A-Za-z0-9.\_\-]/', '', basename($videoFileName));
        if(!empty($downloadURL)){
            // Define headers
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: binary");

            // Read the file
            readfile($downloadURL);
        }
    }else{
        echo "The video is not found, please check YouTube URL.";
    }
}else{
    echo "Please provide valid YouTube URL.";
}

}
?>


<!doctype html>
<html>
    <head>
        <title>Search Videos by keyword using YouTube Data API V3</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <style>

            body {
                font-family: Arial;
                width: 100%auto;
                height: 100%auto;
                padding: 10px;
            }
            .search-form-container {
                background: #F0F0F0;
                border: #e0dfdf 1px solid;
                padding: 20px;
                border-radius: 2px;
            }
            .input-row {
                margin-bottom: 20px;
            }
            .input-field {
                width: 100%;
                border-radius: 2px;
                padding: 10px;
                border: #e0dfdf 1px solid;
            }
            .btn-submit {
                padding: 10px 20px;
                background: #333;
                border: #1d1d1d 1px solid;
                color: #f0f0f0;
                font-size: 0.9em;
                width: 100px;
                border-radius: 2px;
                cursor:pointer;
            }
           .videos-data-container {
                background: #F0F0F0;
                border: #e0dfdf 1px solid;
                padding: 0px;
                border-radius: 100px;
                width: 1000px;
                height: 550px;
                margin-top: 10px;
                margin-left: auto;
                margin-right: auto;
}
.conversion-form-container {
    background: #F0F0F0;
    border: #e0dfdf 1px solid;
    padding: 20px;
    border-radius: 2px;
}
            .converter-search-box{

                      }
            .conversion-field{
                  width: 100%;
                  border-radius: 2px;
                  padding: 10px;
                  border: #e0dfdf 1px solid;
                }


            .error {
                 background: #fdcdcd;
                 border: #ecc0c1 1px solid;
            }

           .success {
                background: #c5f3c3;
                border: #bbe6ba 1px solid;
            }

            iframe {
                border: 0px;
                margin: 0px;

            }
            .video-tile {
                display: inline-block;
                margin-top: 25px;
                margin-right: : 0px;
                margin-bottom: 150px;
                margin-left: 100px;
                width: 300px;
                height: 100px;
            }

            .videoDiv {
                width: 250px;
                height: 150px;
                margin: 0px;
                display: inline-block;
            }
            .btn-conversion {
                padding: 10px 20px;
                background: #333;
                border: #1d1d1d 1px solid;
                color: #f0f0f0;
                font-size: 0.9em;
                width: 100px;
                border-radius: 2px;
                cursor:pointer;

            .Zoom {
                margin-left:20px;
                }




        </style>

    </head>
    <body>
        <h2>Search Videos by keyword using YouTube Data API V3</h2>
        <div class="search-form-container">
            <form id="keywordForm" method="post" action="">
                <div class="input-row">
                    Search Keyword : <input class="input-field" type="search" id="keyword" name="keyword"  placeholder="Enter Search Keyword">
                </div>

                <input class="btn-submit"  type="submit" name="submit" value="Search">
            </form>
        </div>

        <?php if(!empty($response)) { ?>
                <div class="response <?php echo $response["type"]; ?>"> <?php echo $response["message"]; ?> </div>
        <?php }?>
        <?php
            if (isset($_POST['submit']) )
            {

              if (!empty($keyword))
              {
                $apikey = 'AIzaSyBTbigkXY3sdh1Nx63sinVmbi_i12ThTCU';
                $googleApiUrl = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . $keyword . '&maxResults=' . MAX_RESULTS . '&key=' . $apikey;

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_VERBOSE, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);

                curl_close($ch);
                $data = json_decode($response);
                $value = json_decode(json_encode($data), true);
            ?>


            <div class="videos-data-container" id="SearchResultsDiv">


              <?php

                    $videoId = $value['items'][0]['id']['videoId'];
                    $title = $value['items'][0]['snippet']['title'];
                    $description = $value['items'][0]['snippet']['description'];
                    ?>

                        <div class="video-tile">
                        <div  class="videoDiv">

                            <iframe id="iframe" style="width:150%; height:150%" src="//www.youtube.com/embed/<?php echo $videoId; ?>"
                                    data-autoplay-src="//www.youtube.com/embed/<?php echo $videoId; ?>?autoplay=1"></iframe>
                        </div>


                        </div>






                        <?php

                                $videoId = $value['items'][1]['id']['videoId'];
                                $title = $value['items'][1]['snippet']['title'];
                                $description = $value['items'][1]['snippet']['description'];
                                ?>

                                    <div class="video-tile">
                                    <div  class="videoDiv">
                                        <iframe id="iframe" style="width:150%; height:150%" src="//www.youtube.com/embed/<?php echo $videoId; ?>"
                                                data-autoplay-src="//www.youtube.com/embed/<?php echo $videoId; ?>?autoplay=1"></iframe>
                                    </div>


                                    </div>





                                    <?php
                                      //  for ($i = 0; $i < MAX_RESULTS; $i++) {
                                            $videoId = $value['items'][2]['id']['videoId'];
                                            $title = $value['items'][2]['snippet']['title'];
                                            $description = $value['items'][2]['snippet']['description'];
                                            ?>

                                                <div class="video-tile">
                                                <div  class="videoDiv">
                                                    <iframe id="iframe" style="width:150%; height:150%" src="//www.youtube.com/embed/<?php echo $videoId; ?>"
                                                            data-autoplay-src="//www.youtube.com/embed/<?php echo $videoId; ?>?autoplay=1"></iframe>
                                                </div>


                                                </div>



                                                <?php

                                                        $videoId = $value['items'][3]['id']['videoId'];
                                                        $title = $value['items'][3]['snippet']['title'];
                                                        $description = $value['items'][3]['snippet']['description'];
                                                        ?>

                                                            <div class="video-tile">
                                                            <div  class="videoDiv">
                                                              <iframe id="iframe" style="width:150%; height:150%" src="//www.youtube.com/embed/<?php echo $videoId; ?>"
                                                                        data-autoplay-src="//www.youtube.com/embed/<?php echo $videoId; ?>?autoplay=1"></iframe>
                                                            </div>


                                                            </div>


                </div>







           <?php

                }

            }
            ?>
            &nbsp;
            <form method="POST" class="Zoom">

              <label>Url of Video:</label>
            	<input type="text" class="Downloader" name="url" size="150px" >
            	<input type="submit" name="submit">

            </form>

    </body>



</html>
