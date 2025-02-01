<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>پادکست</title>

    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css"/>
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>


</head>
<body>
<audio id="player" controls></audio>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const audio = document.getElementById('player');

        if (Hls.isSupported()) {
            const hls = new Hls();
            hls.loadSource("{{route('stream',['folder'=>$uuid,'file'=>'playlist.m3u8'])}}");
            hls.attachMedia(audio);
        } else if (audio.canPlayType('application/vnd.apple.mpegurl')) {
            // For Safari native HLS
            audio.src = "{{route('stream',['folder'=>$uuid])}}";
        }

        // Initialize Plyr on the <audio>
        const player = new Plyr(audio, {
            // Optional Plyr config
            controls: ['play', 'progress', 'current-time', 'mute', 'volume']
        });
    });
</script>


</body>
</html>
