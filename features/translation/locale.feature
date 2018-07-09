Feature:
  In order to prove that the Behat Symfony extension is correctly installed
  As a user
  I want to have a demo scenario

  @javascript
  Scenario: Check if change locale works
    When I am on "/"
    Then I should see "Отзывы"
    And I should see "ru"
    When I click on "en" locale button
    Then I should see "Comments"
    Then I should see "en"
    When I follow "Comments"
    Then I should see "Home"
    Then I should see "en"
    When I click on "ru" locale button
    Then I should see "Отзывы"
    And I should see "ru"

