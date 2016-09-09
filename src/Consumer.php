<?php

namespace Vinelab\Bowler;

use PhpAmqpLib\Message\AMQPMessage;

/**
 * Bowler Consumer.
 *
 * @author Ali Issa <ali@vinelab.com>
 */
class Consumer
{
    /**
     * the main class of the package where we define the channel and the connection.
     *
     * @var Vinelab\Bowler\Connection
     */
    private $connection;

    /**
     * the name of the exchange where the producer sends its messages to.
     *
     * @var string
     */
    private $exchangeName;

    /**
     * type of exchange:
     * fanout: routes messages to all of the queues that are bound to it and the routing key is ignored.
     *
     * direct: delivers messages to queues based on the message routing key. A direct exchange is ideal for the unicast routing of messages (although they can be used for multicast routing as well)
     *
     * default: a direct exchange with no name (empty string) pre-declared by the broker. It has one special property that makes it very useful for simple applications: every queue that is created is automatically bound to it with a routing key which is the same as the queue name
     *
     * topic: route messages to one or many queues based on matching between a message routing key and the pattern that was used to bind a queue to an exchange. The topic exchange type is often used to implement various publish/subscribe pattern variations. Topic exchanges are commonly used for the multicast routing of messages
     *
     * @var string
     */
    private $exchangeType;

    /**
     * If set, the server will reply with Declare-Ok if the exchange already exists with the same name, and raise an error if not. The client can use this to check whether an exchange exists without modifying the server state.
     *
     * @var bool
     */
    private $passive;

    /**
     * If set when creating a new exchange, the exchange will be marked as durable. Durable exchanges remain active when a server restarts. Non-durable exchanges (transient exchanges) are purged if/when a server restarts.
     *
     * @var bool
     */
    private $durable;

    /**
     * If set, the exchange is deleted when all queues have finished using it.
     *
     * @var bool
     */
    private $autoDelete;

    /**
     * Non-persistent (1) or persistent (2).
     *
     * @var [type]
     */
    private $deliveryMode;

    private $msgProcessor;

    /**
     * @param Vinelab\Bowler\Connection $connection
     * @param string                $exchangeName
     * @param string                $exchangeType
     * @param bool                  $passive
     * @param bool                  $durable
     * @param bool                  $autoDelete
     * @param int                   $deliveryMode
     */
    public function __construct(Connection $connection, $exchangeName, $exchangeType = 'fanout', $passive = false, $durable = false, $autoDelete = false, $deliveryMode = 2)
    {
        $this->connection = $connection;
        $this->exchangeName = $exchangeName;
        $this->exchangeType = $exchangeType;
        $this->passive = $passive;
        $this->durable = $durable;
        $this->autoDelete = $autoDelete;
        $this->deliveryMode = $deliveryMode;
    }

    /**
     * publish a message to a specified exchange.
     *
     * @param string $data
     */
    public function listenToQueue($handlerClass)
    {
        $this->connection->getChannel()->exchange_declare($this->exchangeName, $this->exchangeType, $this->passive, $this->durable, $this->autoDelete);
        list($queue_name) = $this->connection->getChannel()->queue_declare('', false, false, false, false);
        $this->connection->getChannel()->queue_bind($queue_name, $this->exchangeName);

        echo ' [*] Waiting for CRUD operations. To exit press CTRL+C', "\n";

        $handler = new $handlerClass;

        $callback = function ($msg) use ($handler) {
            $handler->handle($msg);
        };

        $this->connection->getChannel()->basic_qos(null, 1, null);
        $this->connection->getChannel()->basic_consume($queue_name, '', false, false, false, false, $callback);

        while (count($this->connection->getChannel()->callbacks)) {
            $this->connection->getChannel()->wait();
        }
    }

}