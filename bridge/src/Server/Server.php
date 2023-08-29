<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Server;

use Closure;
use Exception;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Exceptions\ValueTypeMismatchException;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Bridge\Shared\DTO\Binding;
use MinVWS\DUSi\Shared\Bridge\Shared\DTO\MethodCall;
use MinVWS\DUSi\Shared\Bridge\Shared\DTO\MethodResult;
use MinVWS\DUSi\Shared\Bridge\Shared\Connection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Server
{
    /**
     * @var array<string, Binding>
     */
    private array $bindings = [];

    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @param class-string<TParams> $paramsClass
     *
     * @param (Closure(TParams): Codable) $callback
     *
     * @template TParams of Codable
     */
    public function bind(string $method, string $paramsClass, Closure $callback): void
    {
        $this->bindings[$method] = new Binding($method, $paramsClass, $callback);
    }

    private function setupChannel(): AMQPChannel
    {
        $channel = $this->connection->connection->channel();

        [$queue] = $channel->queue_declare($this->connection->queue, auto_delete: false) ?? [];
        assert(is_string($queue));

        $channel->basic_qos(0, 1, false);
        $channel->basic_consume(queue: $queue, callback: fn (AMQPMessage $message) => $this->onMethodCall($message));

        return $channel;
    }

    private function decodeMethodCall(string $encodedCall): MethodCall
    {
        $decoder = new JSONDecoder();
        $decoder->getContext()->setValue(MethodCall::BINDINGS, $this->bindings);
        $call = $decoder->decode($encodedCall)->decodeObject(MethodCall::class);
        assert($call instanceof MethodCall);
        return $call;
    }

    /**
     * @throws Exception
     */
    private function encodeMethodResult(Codable $data): string
    {
        $methodResult = new MethodResult($data);
        return (new JSONEncoder())->encode($methodResult);
    }

    private function onMethodCall(AMQPMessage $request): void
    {
        try {
            $call = $this->decodeMethodCall($request->body);

            $binding = $this->bindings[$call->method];
            assert($binding instanceof Binding);

            $resultData = call_user_func($binding->callback, $call->params);

            $body = $this->encodeMethodResult($resultData);
            $properties = [
                'correlation_id' => $request->get('correlation_id')
            ];

            $channel = $request->getChannel();
            assert(!is_null($channel));

            $msg = new AMQPMessage($body, $properties);
            $channel->basic_publish(
                $msg,
                routing_key: $request->get('reply_to')
            );

            $request->ack();
        } catch (Exception) {
            $request->nack();
            // TODO: log
        }
    }

    public function run(): void
    {
        $channel = $this->setupChannel();

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
    }
}
