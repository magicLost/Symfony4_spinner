Feature:
  In order to prove that the Behat Symfony extension is correctly installed
  As a user
  I want to have a demo scenario

  @javascript
  Scenario: Login with name
    Given create test user with name "sia123", password "123" and email "sia123@yahoo.com"
    And I am on "/ru/login"
    When I fill in "Имя пользователя" with "sia123"
    And I fill in "Пароль" with "123"
    And I press "_submit"
    Then I should be on "/"
    Then I should see "Выйти"

  @javascript
  Scenario: Loggin with email and redirect
    And I am on "/en/comments"
    When I follow "Login"
    And I fill in "Username" with "sia123@yahoo.com"
    And I fill in "Password" with "123"
    And I press "_submit"
    Then I should be on "/en/comments"
    And I should see "Logout"

  @javascript
  Scenario: Redirect after login
    And I am on "/en"
    When I follow "Login"
    And I follow "Registration"
    And I follow "Login"
    And I fill in "Username" with "sia123@yahoo.com"
    And I fill in "Password" with "123"
    And I press "_submit"
    Then I should be on "/en"
    And I should see "Logout"

  @javascript
  Scenario: Denied access to login page if auth user
    And I am on "/"
    When I follow "Войти"
    When I fill in "Имя пользователя" with "sia123"
    And I fill in "Пароль" with "123"
    And I press "_submit"
    Then I should be on "/"
    When I am on "/ru/login"
    Then I should see "Access Denied."

  @javascript
  Scenario: Check change locale
    Given I am on "/ru/login"
    Then I should see "Войти"
    And I should see "Зарегистрироваться"
    And I should see "Забыли пароль?"
    When I click on "en" locale button
    Then I should see "Login"
    Then I should see "Username"
    Then I should see "Registration"
    And I should see "Forget password?"

