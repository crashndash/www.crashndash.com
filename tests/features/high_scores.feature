@api
Feature: High scores
  Scenario: There should be a list of high scores available
    Given I am an anonymous user
    When I am on the homepage
    And I mock the highscores
    And the cache has been cleared
    Then I should see "World high scores" in the "header" region
    And I click "World high scores"
    Then I should see "World High Scores" in the "content" region
    And I should see 100 ".moai-table tr" elements
