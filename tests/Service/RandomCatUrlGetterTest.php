<?php
declare(strict_types=1);

namespace App\tests\Service;

use App\Service\RandomCatUrlGetter;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class RandomCatUrlGetterTest extends TestCase
{
    private const VALID_RESPONSE = '{"url":"http:\/\/randomcatapi.orbalab.com\/images\/cat10.jpg"}';
    private const VALID_RESPONSE_URL = 'http://randomcatapi.orbalab.com/images/cat10.jpg';

    private TestHandler $lastLoggerTestHandler;

    private function createJsonDecoder(): DecoderInterface
    {
        return new JsonDecode([JsonDecode::ASSOCIATIVE => true]);
    }

    private function createTestLogger(): LoggerInterface
    {
        $logger = new Logger('test');
        $testHandler = new TestHandler();
        $this->lastLoggerTestHandler = $testHandler;
        $logger->pushHandler($testHandler);

        return $logger;
    }

    private function createMockRandomCatUrlGetter(array $httpResponses = []): RandomCatUrlGetter
    {
        return new RandomCatUrlGetter(
            new MockHttpClient($httpResponses),
            $this->createJsonDecoder(),
            $this->createTestLogger()
        );
    }

    private function createRealRandomCatUrlGetter(): RandomCatUrlGetter
    {
        return new RandomCatUrlGetter(
            HttpClient::create(),
            $this->createJsonDecoder(),
            $this->createTestLogger()
        );
    }

    private function getLastLoggedExceptionMessage(): string
    {
        $logRecords = $this->lastLoggerTestHandler->getRecords();
        $exception = \end($logRecords)['context']['exception'];
        return $exception->getMessage();
    }

    private function assertLastLoggedExceptionMessage(string $message): void
    {
        $this->assertStringStartsWith($message, $this->getLastLoggedExceptionMessage());
    }

    // Test Methods

    public function testGetUrlReturns404ImageUrlWhenCatApiReturnsNothing(): void
    {
        $randomCatUrlGetter = $this->createMockRandomCatUrlGetter();
        $result = $randomCatUrlGetter->getUrl();
        $this->assertSame(RandomCatUrlGetter::FALLBACK_URL, $result);
        $this->assertLastLoggedExceptionMessage('The response factory iterator passed to MockHttpClient is empty.');
    }

    public function testGetUrlReturns404ImageUrlWhenCatApiReturnsInvalidData(): void
    {
        $randomCatUrlGetter = $this->createMockRandomCatUrlGetter([
            RandomCatUrlGetter::DEFAULT_CAT_API_URL => new MockResponse('foobar'),
        ]);
        $result = $randomCatUrlGetter->getUrl();
        $this->assertSame(RandomCatUrlGetter::FALLBACK_URL, $result);
        $this->assertLastLoggedExceptionMessage('Syntax error');
    }

    public function testGetUrlReturns404ImageUrlWhenCatApiReturnsValidDataWith404(): void
    {
        $randomCatUrlGetter = $this->createMockRandomCatUrlGetter([
            RandomCatUrlGetter::DEFAULT_CAT_API_URL => new MockResponse(self::VALID_RESPONSE, ['http_code' => 404]),
        ]);
        $result = $randomCatUrlGetter->getUrl();
        $this->assertSame(RandomCatUrlGetter::FALLBACK_URL, $result);
        $this->assertLastLoggedExceptionMessage('HTTP 404 returned for "' . RandomCatUrlGetter::DEFAULT_CAT_API_URL);
    }

    public function testGetUrlReturns404ImageUrlWhenCatApiReturnsValidDataButWithDeadLink(): void
    {
        $randomCatUrlGetter = $this->createMockRandomCatUrlGetter([
            RandomCatUrlGetter::DEFAULT_CAT_API_URL => new MockResponse(self::VALID_RESPONSE),
        ]);
        $result = $randomCatUrlGetter->getUrl();
        $this->assertSame(RandomCatUrlGetter::FALLBACK_URL, $result);
        $this->assertLastLoggedExceptionMessage('The response factory iterator passed to MockHttpClient is empty.');
    }

    public function testGetUrlReturns404ImageUrlWhenCatApiReturnsValidDataButWith404Link(): void
    {
        $randomCatUrlGetter = $this->createMockRandomCatUrlGetter([
            RandomCatUrlGetter::DEFAULT_CAT_API_URL => new MockResponse(self::VALID_RESPONSE),
            self::VALID_RESPONSE_URL => new MockResponse('JPEG content here', ['http_code' => 404]),
        ]);
        $result = $randomCatUrlGetter->getUrl();
        $this->assertSame(RandomCatUrlGetter::FALLBACK_URL, $result);
        $this->assertLastLoggedExceptionMessage('HTTP 404 returned for "' . self::VALID_RESPONSE_URL);
    }

    public function testGetUrlReturnsLinkWhenCatApiReturnsValidDataAndLinkIsNotDead(): void
    {
        $randomCatUrlGetter = $this->createMockRandomCatUrlGetter([
            RandomCatUrlGetter::DEFAULT_CAT_API_URL => new MockResponse(self::VALID_RESPONSE),
            self::VALID_RESPONSE_URL => new MockResponse('JPEG content here'),
        ]);
        $result = $randomCatUrlGetter->getUrl();
        $this->assertSame(self::VALID_RESPONSE_URL, $result);
    }

    /**
     * @group integration
     */
    public function testCanUseExternalApiProperly(): void
    {
        $randomCatUrlGetter = $this->createRealRandomCatUrlGetter();
        $result = $randomCatUrlGetter->getUrl();
        $this->assertNotSame('', $result);
    }
}