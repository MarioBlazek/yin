<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Negotiation;

use JsonSchema\Validator;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;

use function json_decode;
use function realpath;

abstract class AbstractMessageValidator
{
    protected ExceptionFactoryInterface $exceptionFactory;

    protected bool $includeOriginalMessage;

    protected ?string $customSchemaPath;

    public function __construct(
        ExceptionFactoryInterface $exceptionFactory,
        bool $includeOriginalMessageInResponse = true,
        ?string $customSchemaPath = null
    ) {
        $this->exceptionFactory = $exceptionFactory;
        $this->includeOriginalMessage = $includeOriginalMessageInResponse;
        $this->customSchemaPath = $customSchemaPath;
    }

    protected function validateJsonMessage(string $message): string
    {
        if (empty($message)) {
            return '';
        }

        $parser = new JsonParser();
        $result = $parser->lint($message);

        if ($result instanceof ParsingException) {
            return $result->getMessage();
        }

        return '';
    }

    protected function validateJsonApiMessage(string $message): array
    {
        if (empty($message)) {
            return [];
        }

        $decodedMessage = json_decode($message);

        $validator = new Validator();
        $validator->validate(
            $decodedMessage,
            (object) ['$ref' => 'file://' . ($this->customSchemaPath ?? realpath(__DIR__ . '/json-api-schema.json'))],
        );

        return $validator->getErrors();
    }
}
