<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cctv->camera_name }} - Live Stream</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .video-container {
            position: relative;
            background: #000;
            border-radius: 12px;
            overflow: hidden;
        }
        .stream-status {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
        }
        .recording-indicator {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 10;
        }
    </style>
</head>
<body class="bg-gray-900">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-6xl w-full">
            <div class="bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ $cctv->camera_name }}</h2>
                            <p class="text-amber-100 text-sm">{{ $cctv->camera_location }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 {{ $cctv->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full animate-pulse"></div>
                                <span class="text-white text-sm">{{ $cctv->is_active ? 'LIVE' : 'OFFLINE' }}</span>
                            </div>
                            <button onclick="window.close()" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-all">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Video Player -->
                <div class="p-6">
                    <div class="video-container bg-black rounded-xl overflow-hidden">
                        <div class="stream-status">
                            <div class="flex items-center gap-2 bg-black/70 backdrop-blur-sm px-3 py-1 rounded-full">
                                <svg class="w-4 h-4 text-{{ $cctv->is_active ? 'green' : 'red' }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-white text-xs">{{ $cctv->is_active ? 'Stream Active' : 'Stream Offline' }}</span>
                            </div>
                        </div>
                        
                        @if($cctv->stream_url && $cctv->is_active)
                            @if(strpos($cctv->stream_url, 'rtsp') === 0)
                                <!-- RTSP Stream - Use VLC or other player -->
                                <div class="aspect-video flex items-center justify-center bg-gradient-to-br from-gray-900 to-gray-800">
                                    <div class="text-center p-8">
                                        <svg class="w-20 h-20 text-amber-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-white text-lg font-semibold mb-2">RTSP Stream Detected</p>
                                        <p class="text-gray-400 text-sm mb-4">Stream URL: {{ $cctv->stream_url }}</p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <a href="vlc://{{ $cctv->stream_url }}" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Open in VLC Player
                                            </a>
                                            <button onclick="copyStreamUrl()" class="inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                                Copy Stream URL
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-4">RTSP streams require a compatible media player like VLC</p>
                                    </div>
                                </div>
                            @elseif(strpos($cctv->stream_url, 'http') === 0)
                                <!-- HTTP/HTTPS Stream - Can be embedded -->
                                <video id="videoPlayer" class="w-full h-full object-cover" autoplay muted loop controls>
                                    <source src="{{ $cctv->stream_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        @else
                            <div class="aspect-video flex items-center justify-center bg-gradient-to-br from-gray-900 to-gray-800">
                                <div class="text-center">
                                    <svg class="w-20 h-20 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-gray-500">Camera is offline or not configured</p>
                                    <p class="text-gray-600 text-sm mt-2">Please check camera connection settings</p>
                                </div>
                            </div>
                        @endif
                        
                        <div class="recording-indicator">
                            @if($cctv->recording_enabled)
                            <div class="flex items-center gap-2 bg-red-500/80 backdrop-blur-sm px-3 py-1 rounded-full">
                                <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                <span class="text-white text-xs">Recording Active</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Camera Details -->
                    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-700 rounded-lg p-3">
                            <p class="text-xs text-gray-400">Camera Status</p>
                            <p class="text-sm text-white font-semibold">{{ $cctv->is_active ? 'Active' : 'Offline' }}</p>
                        </div>
                        <div class="bg-gray-700 rounded-lg p-3">
                            <p class="text-xs text-gray-400">Recording</p>
                            <p class="text-sm text-white font-semibold">{{ $cctv->recording_enabled ? 'Enabled' : 'Disabled' }}</p>
                        </div>
                        <div class="bg-gray-700 rounded-lg p-3">
                            <p class="text-xs text-gray-400">Motion Detection</p>
                            <p class="text-sm text-white font-semibold">{{ $cctv->motion_detection ? 'Active' : 'Inactive' }}</p>
                        </div>
                        <div class="bg-gray-700 rounded-lg p-3">
                            <p class="text-xs text-gray-400">IP Address</p>
                            <p class="text-sm text-white font-mono">{{ $cctv->camera_ip ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <!-- Stream Info -->
                    <div class="mt-4 bg-gray-700 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Stream Information</p>
                        <p class="text-xs text-white font-mono break-all">{{ $cctv->stream_url ?? 'No stream configured' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function copyStreamUrl() {
            const streamUrl = '{{ $cctv->stream_url }}';
            navigator.clipboard.writeText(streamUrl).then(() => {
                alert('Stream URL copied to clipboard!\n\nYou can now paste it into VLC Player:\n1. Open VLC Player\n2. Go to Media → Open Network Stream\n3. Paste the URL and click Play');
            });
        }
        
        // Attempt to play HTTP stream
        const videoPlayer = document.getElementById('videoPlayer');
        if (videoPlayer) {
            videoPlayer.addEventListener('error', function(e) {
                console.log('Video playback error:', e);
            });
        }
        
        // Auto-refresh status every 30 seconds
        setInterval(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>