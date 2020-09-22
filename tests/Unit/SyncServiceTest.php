<?php

namespace Tests\Unit;

use Egoditor\FileDownloader;
use Egoditor\SyncException;
use Egoditor\SyncService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SyncServiceTest extends TestCase
{
    public function testDownloadCsvInfoException()
    {
        $this->expectException(SyncException::class);

        $mock = new MockHandler([
                                    new Response(200, [], 'Not Valid json'),
                                ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $fileDownloader = new FileDownloader($client);

        $syncService = new SyncService($fileDownloader);
        $syncService->setURL('http://random.url');

        $csv = $this->invokeMethod($syncService, 'downloadCSVInfo');

    }

    public function testDownloadCsvInfo()
    {
        $jsonData = json_encode(['csv'=>['url'=>'http://some.url']]);

        $mock = new MockHandler([
                                    new Response(200, [], $jsonData),
                                ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $fileDownloader = new FileDownloader($client);

        $syncService = new SyncService($fileDownloader);
        $syncService->setURL('http://random.url');

        $csv = $this->invokeMethod($syncService, 'downloadCSVInfo');

        $this->assertEquals('http://some.url', $csv->url);

    }

    public function testUnzip()
    {
        $content = "Random text " . uniqid();

        // Name of the file we're compressing
        $fileName = "testing_zip_".uniqid();
        $unzippedPath = storage_path("testing_unzip_".uniqid().".txt");
        $file = storage_path($fileName.".txt");
        File::put($file, $content);
        $gzfile = storage_path($fileName.".gz");
        $fp = gzopen ($gzfile, 'w9');
        gzwrite ($fp, file_get_contents($file));
        gzclose($fp);

        $syncService = app(SyncService::class);

        $unzipped = $this->invokeMethod($syncService, 'unzipCSV', [$gzfile, $unzippedPath]);

        $this->assertTrue($unzipped);
        $this->assertStringEqualsFile($unzippedPath, $content);

        File::delete($file);
        File::delete($unzippedPath);
        File::delete($gzfile);
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
