Feature: Publish trick
  Scenario: As a snowboarder I want to publish a new trick
    Given I want to publish a trick
    When I fill the trick
    Then the trick is published and visitor can see the trick