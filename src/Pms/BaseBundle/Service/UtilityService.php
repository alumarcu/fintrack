<?php
namespace Pms\BaseBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class UtilityService
{
    const JSON_NO_ERROR = 'No error';

    /** @var Container */
    protected $container;

    /** @var JsonEncoder */
    protected $encoder;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function encode(array $data)
    {
        if (empty($this->encoder)) {
            $this->encoder = new JsonEncoder();
        }

        $encoded = $this->encoder->encode($data, JsonEncoder::FORMAT);

        $error = $this->encoder->getLastErrorMessage();
        if (self::JSON_NO_ERROR !== $error) {
            throw new \InvalidArgumentException("JSON encoding error: {$error}");
        }

        return $encoded;
    }

    public function decode($json)
    {
        if (empty($this->encoder)) {
            $this->encoder = new JsonEncoder();
        }

        $decoded = $this->encoder->decode($json, JsonEncoder::FORMAT);

        $error = $this->encoder->getLastErrorMessage();
        if (self::JSON_NO_ERROR !== $error) {
            throw new \InvalidArgumentException("JSON decoding error: {$error}. JSON: {$json}");
        }

        return $decoded;
    }
}
