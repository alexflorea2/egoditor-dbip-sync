<?php

namespace Tests\Unit;

use Egoditor\FileDownloader;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FileDownloaderTest extends TestCase
{
    public function testMergeOptions()
    {
        $defaultOptions = ['sink'=>'random url', 'progress'=>'default progress function'];
        $callOptions = ['progress'=>'custom progress function','other_key'=>'other_value'];

        $downloader = app(FileDownloader::class);

        $mergeOptions = $this->invokeMethod($downloader, 'mergeOptions', [$defaultOptions, $callOptions]);

        $this->assertEquals([
                                'sink'=>'random url',
                                'progress'=>'custom progress function',
                                'other_key'=>'other_value'
                            ], $mergeOptions);
    }

    public function testStreamContent()
    {
        $mock = new MockHandler([
                                    new Response(200, [], 'The content'),
                                ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $fileDownloader = new FileDownloader($client);

        $response = $fileDownloader->stream('http://test.example/test.json');

        $this->assertEquals('The content', $response);
    }

    public function testSaveToFile()
    {
        $testFilePath = storage_path("testing".uniqid().".txt");

        $mock = new MockHandler([
                                    new Response(200, [], 'The content'),
                                ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $fileDownloader = new FileDownloader($client);

        $fileDownloader->saveToFile('http://test.example/test.json', $testFilePath);

        $this->assertStringEqualsFile($testFilePath, 'The content' );
        File::delete($testFilePath);
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
