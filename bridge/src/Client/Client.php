<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Client;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Bridge\Client\Exceptions\TimeoutException;
use MinVWS\DUSi\Shared\Bridge\DTO\MethodCall;
use MinVWS\DUSi\Shared\Bridge\DTO\MethodResult;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
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

    public function __construct(
        private readonly AMQPStreamConnection $connection,
        private readonly string $routingKey = 'rpc_queue'
    ) {
    }

    private function setupChannelQueue(): string
    {
        if ($this->queue !== null) {
            return $this->queue;
        }

        $this->channel = $this->connection->channel();

        [$queue] = $this->channel->queue_declare();
        assert(is_string($queue));
        $this->queue = $queue;

        $this->channel->basic_consume(
            queue: $queue,
            no_ack: true,
            callback: fn (AMQPMessage $message) => $this->onResponse($message)
        );

        return $queue;
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
        $this->setupChannelQueue();

        $this->correlationId = uniqid();
        $this->encodedResult = null;

        try {
            $body = $this->encodeMethodCall($method, $params);

            $properties = [
                'correlation_id' => $this->correlationId,
                'reply_to' => $this->queue
            ];

            $msg = new AMQPMessage($body, $properties);

            $this->channel->basic_publish(msg: $msg, routing_key: $this->routingKey);
            $this->channel->wait(timeout: $timeout);

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
    private function decodeMethodResult(string $resultDataClass): ?Codable
    {
        $decoder = new JSONDecoder();
        $decoder->getContext()->setValue(MethodResult::DATA_CLASS, $resultDataClass);
        $result = $decoder->decode($this->encodedResult)->decodeObject(MethodResult::class);
        assert(is_a($result->data, $resultDataClass));
        return $result->data;
    }
}
