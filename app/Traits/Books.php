<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

trait Books 
{
    public function bookVolumes() {
        $api_url = 'https://www.googleapis.com/books/v1/volumes?q=surfing&maxResults=40&key='.env('GOOGLE_KEY', null);

        $client = new Client();
        $res = $client->request('GET', $api_url);

        return $res->getBody(); 
    }

    public function bookByVolumeId($id) {
        $api_url = 'https://www.googleapis.com/books/v1/volumes?q=id:'.urlencode($id).'&key='.env('GOOGLE_KEY', null);

        $client = new Client();
        $res = $client->request('GET', $api_url);

        return $res->getBody(); 
    }
}

