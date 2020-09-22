<?php


namespace Egoditor;


use GuzzleHttp\Client;

class FileDownloader
{
    private Client $httpClient;

    // We could add some default options
    private const defaultOptions = [
        'verify' => false
    ];

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function saveToFile(string $fileUrl, string $savePath, array $options = []) : void
    {
        $options['sink'] = $savePath;

        $requestOptions = $this->mergeOptions(self::defaultOptions, $options);

        $this->httpClient->request(
            'GET',
            $fileUrl,
            $requestOptions
        );
    }

    public function stream(string $fileUrl, array $options = []) : string
    {
        $requestOptions = $this->mergeOptions(self::defaultOptions, $options);

        $response = $this->httpClient->request(
            'GET',
            $fileUrl,
            $requestOptions
        );

        return $response->getBody()->getContents();
    }

    private function mergeOptions(array $one, array $two) : array
    {
        $merged = [];

        $keys = array_merge( array_keys($one), array_keys($two) );

        foreach ($keys as $key)
        {
            if( isset($one[$key]) && !isset($two[$key] ) )
            {
                $merged[$key] = $one[$key];
            }
            elseif( isset($two[$key]) )
            {
                $merged[$key] = $two[$key];
            }
            else
            {
                $merged[$key] = $one[$key];
            }
        }

        return $merged;
    }
}
