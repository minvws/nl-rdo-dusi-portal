<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Client;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Bridge\Client\Exceptions\TimeoutException;
use MinVWS\DUSi\Shared\Bridge\Shared\DTO\MethodCall;
use MinVWS\DUSi\Shared\Bridge\Shared\DTO\MethodResult;
use MinVWS\DUSi\Shared\Bridge\Shared\Connection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Client for the bridge.
 */
class Client
{
    private ?AMQPChannel $channel = null;
    private ?string $queue = null;
    private ?string $correlationId = null;
    private ?string $encodedResult = null;

    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @return array{AMQPChannel, string}
     */
    private function setupChannelQueue(): array
    {
        if ($this->channel !== null && $this->queue !== null) {
            return [$this->channel, $this->queue];
        }

        $this->channel = $this->connection->connection->channel();

        $queueDecl = $this->channel->queue_declare();
        assert(is_array($queueDecl));
        [$queue] = $queueDecl;
        assert(is_string($queue));
        $this->queue = $queue;

        $this->channel->basic_consume(
            queue: $queue,
            no_ack: true,
            callback: fn (AMQPMessage $message) => $this->onResponse($message)
        );

        return [$this->channel, $this->queue];
    }

    private function onResponse(AMQPMessage $message): void
    {
        if ($message->get('correlation_id') !== $this->correlationId) {
            return;
        }

        $this->encodedResult = $message->body;
    }

    /**
     * @param class-string<TResultData> $resultDataClass
     *
     * @return TResultData
     *
     * @template TResultData of Codable
     */
    public function call(string $method, ?Codable $params, string $resultDataClass, int $timeout = 30): Codable
    {
        [$channel, $queue] = $this->setupChannelQueue();

        $this->correlationId = uniqid();
        $this->encodedResult = null;

        try {
            $body = $this->encodeMethodCall($method, $params);

            $properties = [
                'correlation_id' => $this->correlationId,
                'reply_to' => $queue
            ];

            $msg = new AMQPMessage($body, $properties);

            $channel->basic_publish(msg: $msg, routing_key: $this->connection->queue);
            $channel->wait(timeout: $timeout);

            return $this->decodeMethodResult($resultDataClass);
        } catch (AMQPTimeoutException $e) {
            throw new TimeoutException('Operation timed out', previous: $e);
        } finally {
            $this->correlationId = null;
            $this->encodedResult = null;
        }
    }

    private function encodeMethodCall(string $method, ?Codable $params): string
    {
        $methodCall = new MethodCall($method, $params);
        return (new JSONEncoder())->encode($methodCall);
    }

    /**
     * @param class-string<TResultData> $resultDataClass
     *
     * @return TResultData
     *
     * @template TResultData of Codable
     */
    private function decodeMethodResult(string $resultDataClass): Codable
    {
        $decoder = new JSONDecoder();
        $decoder->getContext()->setValue(MethodResult::DATA_CLASS, $resultDataClass);
        $result = $decoder->decode($this->encodedResult ?? '')->decodeObject(MethodResult::class);
        assert(is_a($result->data, $resultDataClass));
        return $result->data;
    }
}
