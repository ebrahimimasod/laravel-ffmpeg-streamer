<!-- resources/views/player.blade.php -->
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>پلیر صوتی</title>
    <style>
        body { margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        #audio-player { width: 100%; }
    </style>
</head>
<body>
<audio id="audio-player" controls></audio>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var audio = document.getElementById('audio-player');
        var playlistUrl = "{{ $playlistUrl }}";

        if (Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource(playlistUrl);
            hls.attachMedia(audio);
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                audio.play();
            });
        }
        else if (audio.canPlayType('application/vnd.apple.mpegurl')) {
            audio.src = playlistUrl;
            audio.addEventListener('loadedmetadata', function() {
                audio.play();
            });
        }

        // جلوگیری از راست کلیک
        document.addEventListener('contextmenu', event => event.preventDefault());
    });
</script>
</body>
</html>
