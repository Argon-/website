---
layout: null
---
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="Stylesheet" type="text/css" href="css/main.css">
    <script src="scripts/jquery-1.11.1.min.js" type="text/javascript"></script>
    <title>Index</title>
</head>
<body>


<!--
Hi. Valid parameters:
   - &loop
   - &expand=<video id>
   - &autoplay (only with &expand)
-->


<div id="header">
    <p class="header">
        Encoding experiments.
    </p>
</div>


<div id="content">
    <noscript><p class="jsnotice">This website requires JavaScript.</p></noscript>
    <div class="container">

    <script type="text/javascript">
        var can_play = false;
        var video_node = document.createElement('video');
        if (video_node.canPlayType('video/webm; codecs="vp8.0, vorbis"'))
            can_play = true;

        var content_margin = parseInt(window.getComputedStyle(document.getElementById('content')).getPropertyValue('margin-top')) || 30;
    </script>

<?php

    $files = glob('media/[0-9][0-9][0-9][0-9]-*.{webm}', GLOB_BRACE);
    $fcount = $files ? count($files) : 0;
    $findex = $fcount;

    $loop = isset($_GET["loop"]) || isset($_GET["l"]);
    $autoplay = isset($_GET["autoplay"]) || isset($_GET["auto"]) || isset($_GET["a"]);
    $noexpand = isset($_GET["noexpand"]) || isset($_GET["noe"]);
    if (isset($_GET['expand']) && preg_match('/^\d+$/', $_GET['expand']))
        $expandnum = abs(intval($_GET['expand'])) > 9999 ? $findex : abs(intval($_GET['expand']));
    else
        $expandnum = 0;    // no entry

    if (!$findex) {
        echo "<h3>No videos found.</h3>" . PHP_EOL;
    }

    while ($findex)
    {
        $file = $files[--$findex];
        list($filenum, $title) = explode("-", basename($file), 2);
        $title = pathinfo($title, PATHINFO_FILENAME);
        $title = str_replace("-", " ", $title);      // dash is space, underscore is dash (I prefer it that way...)
        $title = str_replace("_", "-", $title);
        echo PHP_EOL;
?>
        <!-- ID: <?php echo $filenum; ?> -->
        <div class="container_prefix">&gt;</div>
        <div class="container_title"><?php echo $title; ?></div>
        <div class="container_content">
            <div class="video">
                <video controls <?php if ($loop) echo "loop "; if ($autoplay && ($findex + 1) == $expandnum) echo "autoplay "; ?>preload="metadata">
                <source src="<?php echo $file; ?>" type='video/webm; codecs="vp8.0, vorbis"'>
                    <p>Your browser does not support HTML5 video.</p>
                </video>
            </div>
            <br>
        </div>
<?php
    }
?>

    </div>
</div>


<script type="text/javascript">
    if (!can_play)
    {
        var div_video = document.getElementsByClassName("video");
        for (var i = 0; i < div_video.length; ++i) {
            while (div_video[i].firstChild) {
                div_video[i].removeChild(div_video[i].firstChild);
            }
            var warning_msg = document.createElement('p');
            warning_msg.textContent = "Your browser does not support VP8/Vorbis.";
            div_video[i].appendChild(warning_msg);
        }
    }
</script>


<div id="footer">
    <?php if (isset($_GET["linkback"]) || isset($_GET["lb"])) { ?>
    <p class="linkback">
        <a href="{{ site.baseurl }}/">Main Page</a>
    </p>
    <?php } ?>
    <p class="footer">
        This website makes extensive use of VP8/Vorbis encoded video.
    </p>
</div>


<script type="text/javascript">
    $(".container_title").click(function() {
        $title = $(this);
        $prefix = $title.prev()
        $content = $title.next();

        if (!$content.is(":visible")) {
            $('html, body').animate({
                scrollTop: $title.offset().top + $('window').height() - content_margin
            }, <?php echo $autoplay ? "200" : "400" ?>);
        }

        $content.slideToggle(<?php echo $autoplay ? "200" : "400" ?>, function() {
            $prefix.text(function() {
                return $content.is(":visible") ? ">>" : ">";
            });
        });
    });

<?php if (!$noexpand) { ?>
    setTimeout(function() {
        $("div.container_title:eq(<?php echo max($fcount - $expandnum, 0); ?>)").trigger("click");
    }, <?php echo $autoplay ? "200" : "600" ?>);
<?php } ?>
</script>
</body>
</html>
