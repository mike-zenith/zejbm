Feature: Creating a message

  Scenario: On the page, there should be a way to create a queue message
    When I am on the homepage
    Then I should see "queue" in the "button[name=send]" element

  Scenario: Pressing the send button should create a queue message
    Given I am on the homepage
    And a queue manager exists
    When I press "send"
    Then I should see a message in the queue
