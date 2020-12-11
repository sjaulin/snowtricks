Feature: Add comment
  Scenario: As a user I want to add a new comment to a trick
    Given I want to add a comment to a trick
    When I fill the comment
    Then the comment is published and visitors can see the comment on trick page