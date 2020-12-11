Feature: Delete comment
  Scenario: As an admin I want to delete a comment
    Given I want to delete a comment
    When I click on delete link
    Then the comment is deleted and visitor no longer sees the comment