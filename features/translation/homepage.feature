Feature:
  In order to prove that the Behat Symfony extension is correctly installed
  As a user
  I want to have a demo scenario

  @javascript
  Scenario: When we go to homepage it must redirects to /ru or /en
    When I am on "/"
    Then I should see "Welcome to"