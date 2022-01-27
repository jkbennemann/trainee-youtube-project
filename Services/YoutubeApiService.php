<?php

/*
 * Youtube Data API v3 - Simple Client
 *
 * @see https://developers.google.com/youtube/v3/docs/channels/list
 * @see https://developers.google.com/youtube/v3/docs/playlistItems/list
 */

use Illuminate\Support\Collection;

class YoutubeApiService
{
    private const API_BASE_URL = 'https://youtube.googleapis.com/youtube/v3/';
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.youtube.key');
    }

    public function getChannelInformation(
        string $channelId = 'UC_x5XG1OV2P6uZZ5FSM9Ttw'
    ): array
    {
        //compose endpoint url for API
        $apiUrl = sprintf(
            '%s%s?part=%s&id=%s&key=%s',
            self::API_BASE_URL,
            'channel',
            'snippet,contentDetails,statistics',
            $channelId,
            $this->apiKey
        );

        //perform GET request to API
        $response = Http::withHeaders(['Accept' => 'application/json'])
            ->get($apiUrl);

        //return useful information from api
        return [
            'playlist_id' => $response->json('items.0.contentDetails.relatedPlaylists.uploads'),
            'videos_count' => $response->json('items.0.statistics.videoCount'),
            'views_count' => $response->json('items.0.statistics.viewCount'),
            'subscribers_count' => $response->json('items.0.statistics.subscriberCount'),
        ];
    }
}
