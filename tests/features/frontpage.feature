@api
Feature: Front page
  Scenario: It should be possible to reach the front page
    Given I am an anonymous user
    When I am on the homepage
    Then I should see "Crash n Dash" in the "content" region
