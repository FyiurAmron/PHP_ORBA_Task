<?php
declare(strict_types=1);

namespace App\Service;

use League\Uri\Components\Query;
use League\Uri\Uri;
use League\Uri\UriModifier;

use Psr\Log\LoggerInterface;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RandomCatUrlGetter
{
    public const DEFAULT_CAT_API_URL = 'http://randomcatapi.orbalab.com/';
    private const DEFAULT_CAT_API_KEY = '5up3rc0nf1d3n714llp455w0rdf0rc47s';

    public const FALLBACK_URL = '/images/404.jpg';

    private HttpClientInterface $httpClient;
    private DecoderInterface $responseDecoder;
    private LoggerInterface $logger;

    private string $catApiUrl;
    private string $catApiKey;

    /**
     * Constructor.
     *
     * @param HttpClientInterface $httpClient
     * @param DecoderInterface $responseDecoder
     * @param LoggerInterface $logger
     * @param string $catApiUrl
     * @param string $catApiKey
     */
    public function __construct(
        HttpClientInterface $httpClient,
        DecoderInterface $responseDecoder,
        LoggerInterface $logger,
        ?string $catApiUrl = null,
        ?string $catApiKey = null)
    {
        $this->httpClient = $httpClient;
        $this->responseDecoder = $responseDecoder;
        $this->logger = $logger;

        $this->catApiUrl = $catApiUrl ?? $_ENV['CAT_API_URL'] ?? self::DEFAULT_CAT_API_URL;
        $this->catApiKey = $catApiKey ?? $_ENV['CAT_API_KEY'] ?? self::DEFAULT_CAT_API_KEY;
    }

    private function provideFallbackUrl(\Exception $ex): string
    {
        $this->logger->info($ex, ['exception' => $ex]);
        return self::FALLBACK_URL;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        $baseUri = Uri::createFromString($this->catApiUrl);
        $queryParts = [
            ['api_key', $this->catApiKey],
        ];

        $query = Query::createFromPairs($queryParts);
        $uri = UriModifier::appendQuery($baseUri, $query);

        try {
            $response = $this->httpClient->request('GET', (string)$uri);
            $responseContent = $response->getContent();
        } catch (TransportExceptionInterface | HttpExceptionInterface $ex) {
            return $this->provideFallbackUrl($ex);
        }

        try {
            $responseObject = $this->responseDecoder->decode($responseContent, JsonEncoder::FORMAT);
        } catch (NotEncodableValueException $ex) {
            return $this->provideFallbackUrl($ex);
        }
        $catUrl = $responseObject['url'];

        try {
            $response = $this->httpClient->request('HEAD', $catUrl);
            $response->getContent(); // unimportant, but triggers throws on non-200 here
        } catch (TransportExceptionInterface | HttpExceptionInterface $ex) {
            return $this->provideFallbackUrl($ex);
        }

        return $catUrl;
    }
}