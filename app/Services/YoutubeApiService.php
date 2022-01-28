<?php

namespace App\Services;

/*
 * Youtube Data API v3 - Simple Client
 *
 * @see https://developers.google.com/youtube/v3/docs/channels/list
 * @see https://developers.google.com/youtube/v3/docs/playlistItems/list
 */

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

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
            'channels',
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

    public function getVideoInformationForPlaylist(
        string $playlistId,
        int $limit = 10
    ): Collection
    {
        //compose endpoint url for API
        $apiUrl = sprintf(
            '%s%s?part=%s&playlistId=%s&maxResults=%s&key=%s',
            self::API_BASE_URL,
            'playlistItems',
            'snippet',
            $playlistId,
            $limit,
            $this->apiKey
        );

        //perform GET request to API
        $response = Http::withHeaders(['Accept' => 'application/json'])
            ->get($apiUrl);

        //initialize collection to store results in
        $videos = collect();

        //compose all information from api
        foreach($response->json('items') as $item) {
            $videoTitle = $item['snippet']['title'];
            $videoDescription = $item['snippet']['description'];
            $videoId = $item['snippet']['resourceId']['videoId'];
            $videoThumbnail = $item['snippet']['thumbnails']['high']['url'];
            $datePublished = $item['snippet']['publishedAt'];
            $iframeMarkup = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$videoId.'" title="'.$videoTitle.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            $videos->add([
                'id' => $videoId,
                'title' => $videoTitle,
                'description' => $videoDescription,
                'thumbnail_url' => $videoThumbnail,
                'published_at' => $datePublished,
                'iframe' => $iframeMarkup,
            ]);
        }

        //return all videos
        return $videos;
    }
}
