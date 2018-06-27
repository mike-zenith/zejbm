<?php

declare(strict_types=1);

namespace zejbm\features\bootstrap;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use PhpAmqpLib\Message\AMQPMessage;

class QueueHandlerContext implements Context
{
    use QueueConnectionTrait;

    public function __construct(string $containerFile) {
        $this->populateConfigFromContainer($containerFile);
    }

    /**
     * @AfterScenario
     */
    public function purgeQueueAfterScenario(AfterScenarioScope $afterScenario) {
        if ($this->connection) {
            $this->purgeQueue();
        }
    }

    /**
     * @Given a queue manager exists
     */
    public function aQueueManagerExistsAt() {
        $connection = $this->getAMQPConnection();
        assert($connection->isConnected());
    }

    /**
     * @Then I should see :count messages in the queue
     * @Then I should see a message in the queue
     */
    public function iShouldSeeAMessageInQueue(string $count = '1') {
        assert($this->getNumberOfMessagesInQueue() === (int) $count);
    }

    /**
     * @Given I put a message with body :body and id :id into the queue
     */
    public function iPutAMessageWithBodyAndIdIntoTheQueue(string $body, string $id) {
        $channel = $this->getAMQPConnection()->channel();
        $channel->basic_publish(new AMQPMessage($body, ['message_id' => $id]));
        $channel->close();
    }

}