@local @local_profilecohort
Feature: Edit rules based on profile fields

  Background:
    Given the following custom user profile fields exist (local_profilecohort):
      | shortname     | name            | datatype | param1           |
      | checkboxfield | Checkbox field  | checkbox |                  |
      | menufield     | Menu field      | menu     | Opt1, Opt2, Opt3 |
      | textfield     | Text field      | text     |                  |
      | textareafield | Text area field | textarea |                  |
      | datefield     | Date field      | datetime |                  |
    And I log in as "admin"
    And the following "cohorts" exist:
      | name                | idnumber |
      | Cohort 1            | c01      |
      | Cohort 2            | c02      |
      | Cohort 3            | c03      |
      | Cohort not included | cnot     |
    And I navigate to "Users > Accounts > Profile field based cohort membership" in site administration
    And I set the following fields to these values:
      | Cohort 1            | 1 |
      | Cohort 2            | 1 |
      | Cohort 3            | 1 |
      | Cohort not included | 0 |
    And I press "Save changes"
    And I should not see "Date field"

  Scenario: Add, update and delete a checkbox field rule
    When I select "Checkbox field" from the "local_profilecohort_add" singleselect
    And "Checkbox field" "text" should exist in the "form.mform" "css_element"
    And I should not see "Cohort not included"
    And I set the following fields to these values:
      | Match value                      | Yes      |
      | the user will be added to cohort | Cohort 1 |
    And I press "Save changes"
    And I navigate to "Users > Accounts > Profile field based cohort membership" in site administration
    Then the following fields match these values:
      | Match value                      | Yes      |
      | the user will be added to cohort | Cohort 1 |
    And I set the following fields to these values:
      | Match value                      | No       |
      | the user will be added to cohort | Cohort 2 |
    And I press "Save changes"
    And I navigate to "Users > Accounts > Profile field based cohort membership" in site administration
    And the following fields match these values:
      | Match value                      | No       |
      | the user will be added to cohort | Cohort 2 |
    And I set the field "Delete this rule" to "1"
    And I press "Save changes"
    And "Checkbox field" "text" should not exist in the "form.mform" "css_element"

  Scenario: Add update and delete a menu field rule
    When I select "Menu field" from the "local_profilecohort_add" singleselect
    And "Menu field" "text" should exist in the "form.mform" "css_element"
    And I set the following fields to these values:
      | Match value                      | Opt2     |
      | the user will be added to cohort | Cohort 1 |
    And I press "Save changes"
    And I navigate to "Users > Accounts > Profile field based cohort membership" in site administration
    Then "Menu field" "text" should exist in the "form.mform" "css_element"
    And the following fields match these values:
      | Match value                      | Opt2     |
      | the user will be added to cohort | Cohort 1 |
    And I set the following fields to these values:
      | Match value                      | Opt3     |
      | the user will be added to cohort | Cohort 2 |
    And I press "Save changes"
    And I navigate to "Users > Accounts > Profile field based cohort membership" in site administration
    And the following fields match these values:
      | Match value                      | Opt3     |
      | the user will be added to cohort | Cohort 2 |
    And I set the field "Delete this rule" to "1"
    And I press "Save changes"
    And "Menu field" "text" should not exist in the "form.mform" "css_element"

  Scenario: Add update and delete a text field rule
    When I select "Text field" from the "local_profilecohort_add" singleselect
    And "Text field" "text" should exist in the "form.mform" "css_element"
    And I set the following fields to these values:
      | Match value                      | testing  |
      | Match type                       | Matches  |
      | the user will be added to cohort | Cohort 1 |
    And I press "Save changes"
    And I navigate to "Users > Accounts > Profile field based cohort membership" in site administration
    Then "Text field" "text" should exist in the "form.mform" "css_element"
    And the following fields match these values:
      | Match value                      | testing  |
      | Match type                       | Matches  |
      | the user will be added to cohort | Cohort 1 |
    And I set the following fields to these values:
      | Match value                      | testing again |
      | Match type                       | Contains      |
      | the user will be added to cohort | Cohort 2      |
    And I press "Save changes"
    And I navigate to "Users > Accounts > Profile field based cohort membership" in site administration
    And the following fields match these values:
      | Match value                      | testing again |
      | Match type                       | Contains      |
      | the user will be added to cohort | Cohort 2      |
    And I set the field "Delete this rule" to "1"
    And I press "Save changes"
    And "Text field" "text" should not exist in the "form.mform" "css_element"
