<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Screenshare</title>
    <link rel="icon" href="./../ext/img/logos.png" type="image/png">
    <style>
        /* Make the video cover the entire screen */
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: black;
        }

        video {
            width: 100vw;
            height: 100vh;
            object-fit: cover; /* Ensures the video covers the whole screen */
        }
    </style>
</head>
<body>

    <!-- Full-screen autoplay video -->
    <video autoplay muted loop>
        <source src="./../ext/video/testvideo.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <?php include('includes/footer.php'); ?>
    <script src="./dist/js/checkAnnouncements.js"></script>
    <script src="dist/js/modal.js"></script>

</body>
</html>
