
<?php

  // This tool allows users of Scene 7 to download images with just product ID.  This was created to support the marketing team to find high res images on our ecommerce website. 
  // For more information go to: https://github.com/nxkev
  // Author: Kevin Chan

  // The follow configurations are mandatory to make this work:
  // The default URL is:
$url = "http://s7d1.scene7.com/is/image/";

  // This is your Scene 7 account name
$S7Account = "";

  //This is determined by the Scene7 Reply image size limit
  //http://helpx.adobe.com/experience-manager/scene7/kb/base/images-not-appearing-is/illegal-image-size-error.html
$maxImageWidth = 2000;

  // Configuration Complete
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">


  <title>Scene 7 Imageset Tool</title>
  <link href="bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <?php
  if (isset($_POST["productID"]) && !empty($_POST["productID"])) {
    $productID = $_POST["productID"];
    $productID = trim($productID);
    $productArray = explode("\n", $productID);
    $productArray = array_filter($productArray, 'trim'); 
  }
  ?>
  <div class="container">

    <div class="page-header">
      <h1>Scene 7 Imageset Tool</h1>
      <p class="lead">This image tool will allow you to search for imageset and download images from Scene 7.</p>
    </div>

    <h3></h3>
    <form role="form" action="" method="post">
      <div class="form-group">
        <label for="productID">Enter Product ID(s)</label>
        <textarea class="form-control" rows="3" name="productID" placeholder="Enter Product ID(s)" value=""><?php echo $_POST["productID"]; ?></textarea>
        <p class="help-block">The above field will allow you to enter multiple product IDs at once.  Simply put 1 product ID per line.  Please do not exceed 10, as it will take some time to process. </p>
      </div>
      <button type="submit" name="submit" value="Submit" class="btn btn-default">Submit</button>
    </form>
    <hr>
    <p>
      <?php 
      if (isset($_POST["productID"]) && !empty($_POST["productID"])) {
        echo "<table class='table table-hover'>";
        foreach ($productArray as $line) {
          $line = trim($line);
          $s7productID = $url . "/" . $S7Account . "/" . $line . "/?req=set";

          $curl = curl_init($s7productID);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          $data = curl_exec($curl);
          $xml = simplexml_load_string($data);
          if ($xml->item->i['n'] == $S7Account . "/photo-unavailable") {
            echo "<tr class='warning'><td></td><td>" . $line . "</td><td colspan=2>Image not found on Scene 7.  Please ensure Product ID is correct.</td></tr>";
            $i++;
          } else if ($xml['type'] == 'img') {
            $width = maxWidth($xml->item['dx'], $xml->item['dy']);
            $imageURL = $url . "/" . $xml->item->i['n'] . "/?wid=" . $width;
            $thumbURL = $url . "/" . $xml->item->i['n'] . "/?wid=50";
            $imgWidth = $xml->item['dx'];
            $imgHeight = $xml->item['dy'];
            echo "<tr>";
            echo "<td><a href='" . $imageURL . "' target='_blank'><img src='". $thumbURL . "'></a></td>";
            echo "<td><a href='" . $imageURL . "' target='_blank'>" . $xml->item->i['n'] . "</a></td>";
            echo "<td>Width: " . $xml->item['dx'] . "px</td>";
            echo "<td>Height: " . $xml->item['dy'] . "px</td>";
            echo "</tr>";
            $i++;
          } else if ($xml['type'] == 'img_set') {
            foreach ($xml->item as $item) {
              $width = maxWidth($xml->item['dx'], $xml->item['dy']);
              $imageURL = $url . "/" . $item->i['n'] . "/?wid=" . $width;
              $thumbURL = $url . "/" . $item->i['n'] . "/?wid=50";
              echo "<tr>";
              echo "<td><a href='" . $imageURL . "' target='_blank'><img src='". $thumbURL . "'></a></td>";
              echo "<td><a href='" . $imageURL . "' target='_blank'>" . $item->i['n'] . "</a></td>";
              echo "<td>Width: " . $item['dx'] . "px</td>";
              echo "<td>Height: " . $item['dy'] . "px</td>";
              echo "</tr>";
              $i++;
            } 
          } else {
            echo "<br>";
          };
        };
        echo "<tr><td colspan='4'>" .$i. " items searched</td></tr>";
        echo "<tr><td colspan='4'>To download the images, right click and save it to computer.</td></tr>";
        echo "</table>";
      } else {  
      };

      function maxWidth($width, $height) {
        global $maxImageWidth;
        $width = intval($width);
        $height = intval($height);
        if ($width > $maxImageWidth || $height > $maxImageWidth) {
          if ($height > $width) {
            $ratio = $width / $height;
            $width = round($maxImageWidth * $ratio, 0, PHP_ROUND_HALF_DOWN);
            return $width;
          } else if ($width > $height) {
            $width = $maxImageWidth;
            return $width;
          } else if ($width > $maxImageWidth){
            $width = $maxImageWidth;
            return $width;
          } else {
            return $width;
          } 
        } else {
          return $width;
        }
      }
      ?></p>


    </div>
  </body>
  </html>
