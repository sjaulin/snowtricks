Feature: Delete trick
  Scenario: As a snowboarder I want to delete my trick
    Given I want to delete my trick
    When I click on delete link
    Then the trick is deleted and visitor no longer sees my trick