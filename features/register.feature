Feature: Register
    Scenario: As a snowboarder I want to register so that I publish a new trick
        Given I need to register to publish a new trick
        When I fill the registration form
        Then I can log in with new account
    Scenario: As an admin I want to register so that I publish a new category
        Given I need to register to access admin new category
        When I fill the registration form
        Then I can log in with new account