# Laravel Media Streaming Project

This project is a Laravel-based application that converts audio and video files into streamable HLS (HTTP Live Streaming) formats using FFmpeg and plays them on the client side with HLS.js.

## Features

- **Media Conversion**: Uses FFmpeg to transcode media files (audio/video) into HLS format.
- **Streamable Content**: Supports streaming media files in segments for efficient delivery.
- **HLS.js Integration**: Implements HLS.js for smooth media playback in modern browsers.
- **Robust Laravel Backend**: Manages file uploads, conversion processes, and stream serving.

## Prerequisites

Before you begin, ensure you have met the following requirements:

- **PHP >= 8.x** – Make sure you have PHP installed.
- **Composer** – PHP dependency manager.
- **Laravel** – This project is built using the Laravel framework.
- **FFmpeg** – Must be installed and available on your system.  
  Check installation with:
  ```bash
  ffmpeg -version
