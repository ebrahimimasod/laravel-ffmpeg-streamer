<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>


<audio id="audioPlayer" controls></audio>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        var audio = document.getElementById('audioPlayer');
        if (Hls.isSupported()) {
            var hls = new Hls();
            // آدرس فایل m3u8 که کنترلر برمی‌گرداند
            hls.loadSource('{{ route("stream", ["uuid" => $media->uuid]) }}');
            hls.attachMedia(audio);
            hls.on(Hls.Events.MANIFEST_PARSED, function () {
                audio.play();
            });
        } else if (audio.canPlayType('application/vnd.apple.mpegurl')) {
            // برای مرورگرهایی که به صورت بومی پشتیبانی می‌کنند (مثل سافاری)
            audio.src = '{{ route("stream", ["uuid" => $media->uuid]) }}';
            audio.addEventListener('loadedmetadata', function() {
                audio.play();
            });
        }
    });
</script>

</body>
</html>
