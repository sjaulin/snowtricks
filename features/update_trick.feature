Feature: Update trick
  Scenario: As a snowboarder I want to update my trick
    Given I want to update my trick
    When I fill the trick
    Then the trick is updated and visitor can see the trick with new content