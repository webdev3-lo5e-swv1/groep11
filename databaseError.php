<?php
require_once('include/connect.php');
if ($DBerr == false){
    header('location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MBO Cinemas - error page</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./style/style.css">
</head>
<body>
    <h1>Our database seems to be down at the moment. <br>We are sorry for the inconvience! please try our site again later.</h1>
</body>
</html>