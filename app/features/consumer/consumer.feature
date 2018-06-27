Feature: Consumer features

  Background: Queue consumer with a message exists
    Given a queue manager exists
    And I put a message with body "123" and id "1" into the queue
    And I start the consumer

  Scenario: Consumer reads message from queue
    Given I start the consumer
    When I wait max "2" sec until consumer finishes
    Then I should see "0" messages in the queue

