<?php

namespace App\Http\Controllers;

use App\Services\YoutubeApiService;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function show(string $id)
    {
        $ytApi = new YoutubeApiService();
        $channelInformation = $ytApi->getChannelInformation($id);

        $videos = $ytApi->getVideoInformationForPlaylist($channelInformation['playlist_id'], 2);

        return view('video_list', compact('videos'));
    }
}
