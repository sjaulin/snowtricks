Feature: Show trick details
    Scenario: As visitor I want to show trick details
    Given I want to show trick details
    When I go to trick page
    Then I see trick details (description, image, video)
    