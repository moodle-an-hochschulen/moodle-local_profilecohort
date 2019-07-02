<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Local plugin "Profile field based cohort membership" - Test the application of user profile rules
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_profilecohort\field_base;
use local_profilecohort\field_text;

defined('MOODLE_INTERNAL') || die();

/**
 * Class test_profilefields
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class test_profilecohort extends \local_profilecohort\profilecohort {
    /**
     * Expose the results of the protected 'load_rules' function.
     * @return field_base[]
     */
    public static function test_load_rules() {
        return self::load_rules();
    }
}

/**
 * Class local_profilecohort_testcase
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_profilecohort_testcase extends advanced_testcase {

    /** @var int[] mapping user profile field shortname => field id */
    protected $fieldids = [];
    /** @var int[] ids of the cohorts to use in the testing */
    protected $cohortids = [];
    /** The name of the table storing the rule definitions. */
    const TABLENAME = 'local_profilecohort';

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    public function setUp() {
        global $DB;

        // Create some custom profile fields to work with.
        $catid = $DB->get_field('user_info_category', 'MIN(id)', []);
        if (!$catid) {
            $ins = (object) ['name' => 'Other fields', 'sortorder' => 1];
            $catid = $DB->insert_record('user_info_category', $ins);
        }
        $sharedinfo = ['descriptionformat' => 1, 'categoryid' => $catid, 'visible' => 2];
        $fieldinfo = [
            'checkboxfield' => ['name' => 'Checkbox field', 'datatype' => 'checkbox'],
            'menufield' => ['name' => 'Menu field', 'datatype' => 'menu', 'param1' => "Opt 1\nOpt 2\nOpt 3"],
            'textfield' => ['name' => 'Text field', 'datatype' => 'text'],
            'textareafield' => ['name' => 'Text area field', 'datatype' => 'textarea'],
        ];
        foreach ($fieldinfo as $shortname => $info) {
            $ins = (object) array_merge($sharedinfo, $info);
            $ins->shortname = $shortname;
            $this->fieldids[$shortname] = $DB->insert_record('user_info_field', $ins);
        }

        $this->cohortids[] = $this->getDataGenerator()->create_cohort(['component' => 'local_profilecohort'])->id;
        $this->cohortids[] = $this->getDataGenerator()->create_cohort(['component' => 'local_profilecohort'])->id;
        $this->cohortids[] = $this->getDataGenerator()->create_cohort(['component' => 'local_profilecohort'])->id;
        $this->cohortids[] = $this->getDataGenerator()->create_cohort(['component' => 'local_profilecohort'])->id;

        $this->resetAfterTest();
    }

    /**
     * Test creating a range of new rules.
     */
    public function test_create_rules() {
        // Create a 'checkbox' rule.
        $ruledata = (object) [
            'fieldid' => $this->fieldids['checkboxfield'], 'datatype' => 'checkbox',
            'matchvalue' => 1, 'value' => $this->cohortids[0],
        ];
        $rule = field_base::make_instance($ruledata);
        $rule->save(self::TABLENAME);

        // Load all rules and check the data matches.
        $rules = test_profilecohort::test_load_rules();
        $this->assertCount(1, $rules);
        list($loadedrule) = $rules;
        $this->assertEquals($ruledata->fieldid, $loadedrule->fieldid);
        $this->assertEquals($ruledata->matchvalue, $loadedrule->matchvalue);
        $this->assertEquals($ruledata->value, $loadedrule->value);

        // Create a 'menu' rule.
        $ruledata = (object) [
            'fieldid' => $this->fieldids['menufield'], 'datatype' => 'menu',
            'matchvalue' => 'Opt 2', 'value' => $this->cohortids[1],
        ];
        $rule = field_base::make_instance($ruledata);
        $rule->save(self::TABLENAME);

        // Load all rules and check the data matches.
        $rules = test_profilecohort::test_load_rules();
        $this->assertCount(2, $rules);
        list(, $loadedrule) = $rules;
        $this->assertEquals($ruledata->fieldid, $loadedrule->fieldid);
        $this->assertEquals($ruledata->matchvalue, $loadedrule->matchvalue);
        $this->assertEquals($ruledata->value, $loadedrule->value);

        // Create a 'text' rule.
        $ruledata = (object) [
            'fieldid' => $this->fieldids['textfield'], 'datatype' => 'text',
            'matchtype' => field_text::MATCH_EXACT,
            'matchvalue' => 'testing', 'value' => $this->cohortids[2],
        ];
        $rule = field_base::make_instance($ruledata);
        $rule->save(self::TABLENAME);

        // Load all rules and check the data matches.
        $rules = test_profilecohort::test_load_rules();
        $this->assertCount(3, $rules);
        list(, , $loadedrule) = $rules;
        $this->assertEquals($ruledata->fieldid, $loadedrule->fieldid);
        $this->assertEquals($ruledata->matchvalue, $loadedrule->matchvalue);
        $this->assertEquals($ruledata->matchtype, $loadedrule->matchtype);
        $this->assertEquals($ruledata->value, $loadedrule->value);

        // Create a 'textarea' rule.
        $ruledata = (object) [
            'fieldid' => $this->fieldids['textareafield'], 'datatype' => 'textarea',
            'matchtype' => field_text::MATCH_CONTAINS,
            'matchvalue' => 'testing', 'value' => $this->cohortids[3],
        ];
        $rule = field_base::make_instance($ruledata);
        $rule->save(self::TABLENAME);

        // Load all rules and check the data matches.
        $rules = test_profilecohort::test_load_rules();
        $this->assertCount(4, $rules);
        list(, , , $loadedrule) = $rules;
        $this->assertEquals($ruledata->fieldid, $loadedrule->fieldid);
        $this->assertEquals($ruledata->matchvalue, $loadedrule->matchvalue);
        $this->assertEquals($ruledata->matchtype, $loadedrule->matchtype);
        $this->assertEquals($ruledata->value, $loadedrule->value);
    }

    /**
     * Test updating a rule.
     */
    public function test_update_rule() {
        // Create a 'text' rule.
        $ruledata = (object) [
            'fieldid' => $this->fieldids['textfield'], 'datatype' => 'text',
            'matchtype' => field_text::MATCH_EXACT,
            'matchvalue' => 'testing', 'value' => $this->cohortids[0],
        ];
        $rule = field_base::make_instance($ruledata);
        $rule->save(self::TABLENAME);
        // Create a 'menu' rule.
        $ruledata2 = (object) [
            'fieldid' => $this->fieldids['menufield'], 'datatype' => 'menu',
            'matchvalue' => 'Opt 2', 'value' => $this->cohortids[1],
        ];
        $rule = field_base::make_instance($ruledata2);
        $rule->save(self::TABLENAME);

        // Reload the 'text' rule, change it, then save it.
        list($loadedrule, ) = test_profilecohort::test_load_rules();
        $loadedrule->matchtype = field_text::MATCH_CONTAINS;
        $loadedrule->matchvalue = 'testing2';
        $loadedrule->value = $this->cohortids[2];
        $loadedrule->save(self::TABLENAME);

        // Check the 'text' rule has been updated and the 'menu' rule is unchanged.
        $rules = test_profilecohort::test_load_rules();
        $this->assertCount(2, $rules);
        list($changedrule, $unchangedrule) = $rules;
        $this->assertEquals($ruledata->fieldid, $changedrule->fieldid);
        $this->assertEquals('testing2', $changedrule->matchvalue);
        $this->assertEquals(field_text::MATCH_CONTAINS, $changedrule->matchtype);
        $this->assertEquals($this->cohortids[2], $changedrule->value);

        $this->assertEquals($ruledata2->fieldid, $unchangedrule->fieldid);
        $this->assertEquals($ruledata2->matchvalue, $unchangedrule->matchvalue);
        $this->assertEquals($ruledata2->value, $unchangedrule->value);
    }

    /**
     * Test deleting a rule.
     */
    public function test_delete_rule() {
        // Create a 'text' rule.
        $ruledata = (object) [
            'fieldid' => $this->fieldids['textfield'], 'datatype' => 'text',
            'matchtype' => field_text::MATCH_EXACT,
            'matchvalue' => 'testing', 'value' => $this->cohortids[0]
        ];
        $rule = field_base::make_instance($ruledata);
        $rule->save(self::TABLENAME);
        // Create a 'menu' rule.
        $ruledata2 = (object) [
            'fieldid' => $this->fieldids['menufield'], 'datatype' => 'menu',
            'matchvalue' => 'Opt 2', 'value' => $this->cohortids[1]
        ];
        $rule = field_base::make_instance($ruledata2);
        $rule->save(self::TABLENAME);

        // Reload the rules and delete the first rule.
        /** @var field_base $rule */
        list($rule, ) = test_profilecohort::test_load_rules();
        $rule->delete(self::TABLENAME);

        // Reload the rules and check that only the second remains.
        $rules = test_profilecohort::test_load_rules();
        $this->assertCount(1, $rules);
        list($rule2) = $rules;
        $this->assertEquals($ruledata2->fieldid, $rule2->fieldid);
        $this->assertEquals($ruledata2->matchvalue, $rule2->matchvalue);
        $this->assertEquals($ruledata2->value, $rule2->value);
    }

    /**
     * Test using 'update_from_form_data' to update rules.
     */
    public function test_update_from_form_data() {
        // Create a 'text' rule + reload.
        $ruledata = (object) [
            'fieldid' => $this->fieldids['textfield'], 'datatype' => 'text',
            'matchtype' => field_text::MATCH_EXACT,
            'matchvalue' => 'testing', 'value' => $this->cohortids[0],
        ];
        $rule1 = field_base::make_instance($ruledata);
        $rule1->save(self::TABLENAME);
        // Create an empty 'menu' rule.
        $ruledata2 = (object) [
            'fieldid' => $this->fieldids['menufield'], 'datatype' => 'menu',
        ];
        $rule2 = field_base::make_instance($ruledata2);
        list($rule1) = test_profilecohort::test_load_rules();
        /** @var field_base[] $rules */
        $rules = [$rule1, $rule2];

        // Form data: fill in the empty 'menu' rule (and save).
        $formdata = (object) [
            'fieldid' => ['new' => $this->fieldids['menufield'], $rule1->id => $rule1->fieldid],
            'matchtype' => [$rule1->id => $rule1->matchtype],
            'matchvalue' => ['new' => 'Opt 2', $rule1->id => $rule1->matchvalue],
            'value' => ['new' => $this->cohortids[1], $rule1->id => $rule1->value],
        ];
        foreach ($rules as $updrule) {
            $updrule->update_from_form_data(self::TABLENAME, $formdata);
        }
        $rules = test_profilecohort::test_load_rules();
        $this->assertCount(2, $rules);
        list($rule1, $rule2) = $rules;
        // Rule1 should be unchanged.
        $this->assertEquals($ruledata->fieldid, $rule1->fieldid);
        $this->assertEquals($ruledata->matchvalue, $rule1->matchvalue);
        $this->assertEquals($ruledata->matchtype, $rule1->matchtype);
        $this->assertEquals($ruledata->value, $rule1->value);
        // Rule2 should have been created.
        $this->assertEquals($this->fieldids['menufield'], $rule2->fieldid);
        $this->assertEquals('Opt 2', $rule2->matchvalue);
        $this->assertEquals($this->cohortids[1], $rule2->value);

        // Form data: update the 'text' rule.
        $formdata = (object) [
            'delete' => [$rule1->id => 0],
            'fieldid' => [$rule1->id => $rule1->fieldid, $rule2->id => $rule2->fieldid],
            'matchtype' => [$rule1->id => field_text::MATCH_CONTAINS],
            'matchvalue' => [$rule1->id => 'teting updated', $rule2->id => $rule2->matchvalue],
            'value' => [$rule1->id => $this->cohortids[2], $rule2->id => $rule2->value],
        ];
        foreach ($rules as $updrule) {
            $updrule->update_from_form_data(self::TABLENAME, $formdata);
        }
        $rules = test_profilecohort::test_load_rules();
        $this->assertCount(2, $rules);
        list($rule1, $rule2) = $rules;
        // Rule1 should be updated.
        $this->assertEquals($ruledata->fieldid, $rule1->fieldid);
        $this->assertEquals(field_text::MATCH_CONTAINS, $rule1->matchtype);
        $this->assertEquals('teting updated', $rule1->matchvalue);
        $this->assertEquals($this->cohortids[2], $rule1->value);
        // Rule2 should be unchanged.
        $this->assertEquals($this->fieldids['menufield'], $rule2->fieldid);
        $this->assertEquals('Opt 2', $rule2->matchvalue);
        $this->assertEquals($this->cohortids[1], $rule2->value);

        // Form data: delete the 'text' rule.
        $formdata = (object) [
            'delete' => [$rule1->id => 1],
            'fieldid' => [$rule1->id => $rule1->fieldid, $rule2->id => $rule2->fieldid],
            'matchtype' => [$rule1->id => field_text::MATCH_CONTAINS],
            'matchvalue' => [$rule1->id => 'teting updated', $rule2->id => $rule2->matchvalue],
            'value' => [$rule1->id => $this->cohortids[2], $rule2->id => $rule2->value],
        ];
        foreach ($rules as $updrule) {
            $updrule->update_from_form_data(self::TABLENAME, $formdata);
        }
        $rules = test_profilecohort::test_load_rules();

        // Only 1 rule should remain.
        $this->assertCount(1, $rules);
        list($rule2) = $rules;
        // Rule2 should be unchanged.
        $this->assertEquals($this->fieldids['menufield'], $rule2->fieldid);
        $this->assertEquals('Opt 2', $rule2->matchvalue);
        $this->assertEquals($this->cohortids[1], $rule2->value);
    }

    /**
     * Test matching users based on checkbox profile fields.
     */
    public function test_match_checkbox() {
        global $DB;
        // Set up 3 users:
        // user1 has profile field unchecked.
        // user2 has profile field checked.
        // user3 does not have the profile field set.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $ins = (object) ['userid' => $user1->id, 'fieldid' => $this->fieldids['checkboxfield'], 'data' => 0];
        $DB->insert_record('user_info_data', $ins);
        $ins = (object) ['userid' => $user2->id, 'fieldid' => $this->fieldids['checkboxfield'], 'data' => 1];
        $DB->insert_record('user_info_data', $ins);

        // Create a 'checkbox' rule.
        $ruledata = (object) [
            'fieldid' => $this->fieldids['checkboxfield'], 'datatype' => 'checkbox',
            'matchvalue' => 1, 'value' => $this->cohortids[0]
        ];
        $rule = field_base::make_instance($ruledata);
        $rule->save(self::TABLENAME);

        // Check the rule matches as expected.
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user1->id));
        $this->assertEquals($this->cohortids[0], test_profilecohort::get_mapped_value($user2->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user3->id));

        // Swap the rule to match unticked fields.
        $rule->matchvalue = 0;
        $rule->save(self::TABLENAME);

        // Check the rule matches as expected.
        $this->assertEquals($this->cohortids[0], test_profilecohort::get_mapped_value($user1->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user2->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user3->id));
    }

    /**
     * Test matching users based on menu profile fields.
     */
    public function test_match_menu() {
        global $DB;
        // Set up 3 users:
        // user1 has profile field 'Opt 1'.
        // user2 has profile field 'Opt 2'.
        // user3 does not have the profile field set.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $ins = (object) ['userid' => $user1->id, 'fieldid' => $this->fieldids['menufield'], 'data' => 'Opt 1'];
        $DB->insert_record('user_info_data', $ins);
        $ins = (object) ['userid' => $user2->id, 'fieldid' => $this->fieldids['menufield'], 'data' => 'Opt 2'];
        $DB->insert_record('user_info_data', $ins);

        // Create a 'menu' rule.
        $ruledata = (object) [
            'fieldid' => $this->fieldids['menufield'], 'datatype' => 'menu',
            'matchvalue' => 'Opt 2', 'value' => $this->cohortids[0]
        ];
        $rule = field_base::make_instance($ruledata);
        $rule->save(self::TABLENAME);

        // Check the rule matches as expected.
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user1->id));
        $this->assertEquals($this->cohortids[0], test_profilecohort::get_mapped_value($user2->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user3->id));

        // Swap the rule to match 'Opt 1' fields.
        $rule->matchvalue = 'Opt 1';
        $rule->save(self::TABLENAME);

        // Check the rule matches as expected.
        $this->assertEquals($this->cohortids[0], test_profilecohort::get_mapped_value($user1->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user2->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user3->id));
    }

    /**
     * Test matching users based on text profile fields.
     */
    public function test_match_text() {
        global $DB;
        // Set up 3 users:
        // user1 has profile field 'Testing ABC'.
        // user2 has profile field 'Another test'.
        // user3 does not have the profile field set.
        // user4 has an empty profile field.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $ins = (object) ['userid' => $user1->id, 'fieldid' => $this->fieldids['textfield'], 'data' => 'Testing ABC'];
        $DB->insert_record('user_info_data', $ins);
        $ins = (object) ['userid' => $user2->id, 'fieldid' => $this->fieldids['textfield'], 'data' => 'Another test'];
        $DB->insert_record('user_info_data', $ins);
        $ins = (object) ['userid' => $user4->id, 'fieldid' => $this->fieldids['textfield'], 'data' => ''];
        $DB->insert_record('user_info_data', $ins);

        // Create a 'text' rule, matching 'Another test' exactly.
        $ruledata = (object) [
            'fieldid' => $this->fieldids['textfield'], 'datatype' => 'text',
            'matchtype' => field_text::MATCH_EXACT,
            'matchvalue' => 'Another test', 'value' => $this->cohortids[0]
        ];
        $rule = field_base::make_instance($ruledata);
        $rule->save(self::TABLENAME);

        // Check the rule matches as expected (user2).
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user1->id));
        $this->assertEquals($this->cohortids[0], test_profilecohort::get_mapped_value($user2->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user3->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user4->id));

        // Swap the rule to match 'testing abc' fields.
        $rule->matchvalue = 'testing abc';
        $rule->save(self::TABLENAME);
        // Check the rule matches as expected (user1).
        $this->assertEquals($this->cohortids[0], test_profilecohort::get_mapped_value($user1->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user2->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user3->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user4->id));

        // Swap the rule to match 'test'.
        $rule->matchvalue = 'test';
        $rule->save(self::TABLENAME);
        // Check the rule matches as expected (no users).
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user1->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user2->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user3->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user4->id));

        // Swap the rule to 'contains' instead of 'exact'.
        $rule->matchtype = field_text::MATCH_CONTAINS;
        $rule->save(self::TABLENAME);
        // Check the rule matches as expected (user1 + user2).
        $this->assertEquals($this->cohortids[0], test_profilecohort::get_mapped_value($user1->id));
        $this->assertEquals($this->cohortids[0], test_profilecohort::get_mapped_value($user2->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user3->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user4->id));

        // Swap the rule to match empty value.
        $rule->matchtype = field_text::MATCH_EMPTY;
        $rule->save(self::TABLENAME);
        // Check the rule matches as expected (user4).
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user1->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user2->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user3->id));
        $this->assertEquals($this->cohortids[0], test_profilecohort::get_mapped_value($user4->id));

        // Swap the rule to match any non-empty value.
        $rule->matchtype = field_text::MATCH_NOTEMPTY;
        $rule->save(self::TABLENAME);
        // Check the rule matches as expected (user1 + user2).
        $this->assertEquals($this->cohortids[0], test_profilecohort::get_mapped_value($user1->id));
        $this->assertEquals($this->cohortids[0], test_profilecohort::get_mapped_value($user2->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user3->id));
        $this->assertEquals(null, test_profilecohort::get_mapped_value($user4->id));
    }

    /**
     * Test updating the cohort membership for all users.
     */
    public function test_update_all_cohort_memberships() {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/cohort/lib.php');

        // Set up a user with 'menufield' set to 'Opt 1'.
        $user1 = $this->getDataGenerator()->create_user();
        $ins = (object) ['userid' => $user1->id, 'fieldid' => $this->fieldids['menufield'], 'data' => 'Opt 1'];
        $DB->insert_record('user_info_data', $ins);

        // Create a couple of extra cohorts, not managed by this plugin.
        $incohort = $this->getDataGenerator()->create_cohort();
        $notincohort = $this->getDataGenerator()->create_cohort();
        cohort_add_member($incohort->id, $user1->id);

        // Create two 'menu' rules, one matching 'Opt 1' to 'cohorts[0]',
        // the other matching 'Opt 2' to 'cohorts[1]'.
        $ruledata1 = (object) [
            'fieldid' => $this->fieldids['menufield'], 'datatype' => 'menu',
            'matchvalue' => 'Opt 1', 'value' => $this->cohortids[0]
        ];
        $rule1 = field_base::make_instance($ruledata1);
        $rule1->save(self::TABLENAME);
        $ruledata2 = (object) [
            'fieldid' => $this->fieldids['menufield'], 'datatype' => 'menu',
            'matchvalue' => 'Opt 2', 'value' => $this->cohortids[1]
        ];
        $rule2 = field_base::make_instance($ruledata2);
        $rule2->save(self::TABLENAME);

        // Update the cohorts from the rules.
        $manager = new \local_profilecohort\profilecohort();
        $manager->update_all_cohorts_from_rules();

        // Check the user has been added to the matched cohort.
        $this->assertTrue(cohort_is_member($this->cohortids[0], $user1->id));
        $this->assertFalse(cohort_is_member($this->cohortids[1], $user1->id));
        // Check the other cohorts are untouched.
        $this->assertTrue(cohort_is_member($incohort->id, $user1->id));
        $this->assertFalse(cohort_is_member($notincohort->id, $user1->id));

        // Update rule 2, so the user now matches.
        $rule2->matchvalue = 'Opt 1';
        $rule2->save(self::TABLENAME);

        // Update the cohorts from the rules.
        $manager = new \local_profilecohort\profilecohort();
        $manager->update_all_cohorts_from_rules();

        // Check the cohorts have been updated, as expected.
        $this->assertTrue(cohort_is_member($this->cohortids[0], $user1->id));
        $this->assertTrue(cohort_is_member($this->cohortids[1], $user1->id));
        $this->assertTrue(cohort_is_member($incohort->id, $user1->id));
        $this->assertFalse(cohort_is_member($notincohort->id, $user1->id));
    }

    /**
     * Test updating the cohort membership for a single user.
     */
    public function test_update_cohort_membership() {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/cohort/lib.php');

        // Set up a user with 'menufield' set to 'Opt 1'.
        $user1 = $this->getDataGenerator()->create_user();
        $ins = (object)['userid' => $user1->id, 'fieldid' => $this->fieldids['menufield'], 'data' => 'Opt 1'];
        $DB->insert_record('user_info_data', $ins);

        // Create a couple of extra cohorts, not managed by this plugin.
        $incohort = $this->getDataGenerator()->create_cohort();
        $notincohort = $this->getDataGenerator()->create_cohort();
        cohort_add_member($incohort->id, $user1->id);

        // Create two 'menu' rules, one matching 'Opt 1' to 'cohorts[0]',
        // the other matching 'Opt 2' to 'cohorts[1]'.
        $ruledata1 = (object)[
            'fieldid' => $this->fieldids['menufield'], 'datatype' => 'menu',
            'matchvalue' => 'Opt 1', 'value' => $this->cohortids[0]
        ];
        $rule1 = field_base::make_instance($ruledata1);
        $rule1->save(self::TABLENAME);
        $ruledata2 = (object)[
            'fieldid' => $this->fieldids['menufield'], 'datatype' => 'menu',
            'matchvalue' => 'Opt 2', 'value' => $this->cohortids[1]
        ];
        $rule2 = field_base::make_instance($ruledata2);
        $rule2->save(self::TABLENAME);

        // Update the cohorts from the rules.
        \local_profilecohort\profilecohort::set_cohorts_from_profile(null, $user1->id);

        // Check the user has been added to the matched cohort.
        $this->assertTrue(cohort_is_member($this->cohortids[0], $user1->id));
        $this->assertFalse(cohort_is_member($this->cohortids[1], $user1->id));
        // Check the other cohorts are untouched.
        $this->assertTrue(cohort_is_member($incohort->id, $user1->id));
        $this->assertFalse(cohort_is_member($notincohort->id, $user1->id));

        // Update rule 2, so the user now matches.
        $rule2->matchvalue = 'Opt 1';
        $rule2->save(self::TABLENAME);

        // Update the cohorts from the rules.
        \local_profilecohort\profilecohort::set_cohorts_from_profile(null, $user1->id);

        // Check the cohorts have been updated, as expected.
        $this->assertTrue(cohort_is_member($this->cohortids[0], $user1->id));
        $this->assertTrue(cohort_is_member($this->cohortids[1], $user1->id));
        $this->assertTrue(cohort_is_member($incohort->id, $user1->id));
        $this->assertFalse(cohort_is_member($notincohort->id, $user1->id));
    }

    /**
     * Test combining rules together using 'and'
     */
    public function test_and_rules() {
        global $DB;

        // Set up a user with 'menufield' set to 'Opt 1', 'checkboxfield' set to 'No', 'textfield' set to 'Fred'.
        $user1 = $this->getDataGenerator()->create_user();
        $ins = (object)['userid' => $user1->id, 'fieldid' => $this->fieldids['menufield'], 'data' => 'Opt 1'];
        $DB->insert_record('user_info_data', $ins);
        $ins = (object)['userid' => $user1->id, 'fieldid' => $this->fieldids['checkboxfield'], 'data' => '0'];
        $DB->insert_record('user_info_data', $ins);
        $ins = (object)['userid' => $user1->id, 'fieldid' => $this->fieldids['textfield'], 'data' => 'Fred'];
        $DB->insert_record('user_info_data', $ins);

        // Set up a user with 'menufield' set to 'Opt 1', 'checkboxfield' set to 'No', 'textfield' set to 'George'.
        $user2 = $this->getDataGenerator()->create_user();
        $ins = (object)['userid' => $user2->id, 'fieldid' => $this->fieldids['menufield'], 'data' => 'Opt 1'];
        $DB->insert_record('user_info_data', $ins);
        $ins = (object)['userid' => $user2->id, 'fieldid' => $this->fieldids['checkboxfield'], 'data' => '0'];
        $DB->insert_record('user_info_data', $ins);
        $ins = (object)['userid' => $user2->id, 'fieldid' => $this->fieldids['textfield'], 'data' => 'George'];
        $DB->insert_record('user_info_data', $ins);

        // Set up a user with 'menufield' set to 'Opt 2', 'checkboxfield' set to 'No', 'textfield' set to 'Fred'.
        $user3 = $this->getDataGenerator()->create_user();
        $ins = (object)['userid' => $user3->id, 'fieldid' => $this->fieldids['menufield'], 'data' => 'Opt 2'];
        $DB->insert_record('user_info_data', $ins);
        $ins = (object)['userid' => $user3->id, 'fieldid' => $this->fieldids['checkboxfield'], 'data' => '0'];
        $DB->insert_record('user_info_data', $ins);
        $ins = (object)['userid' => $user3->id, 'fieldid' => $this->fieldids['textfield'], 'data' => 'Fred'];
        $DB->insert_record('user_info_data', $ins);

        // Create 4 rules - note cohort values for rule 2 + 3 (cohort 1 + 2) should never be used,
        // as they are additional rules to the rules above them.

        // As 'menufield' ==  'Opt 1' => cohort 0 AND next rule must match.
        $ruledata1 = (object)[
            'fieldid' => $this->fieldids['menufield'], 'datatype' => 'menu',
            'matchvalue' => 'Opt 1', 'value' => $this->cohortids[0], 'andnextrule' => 1
        ];
        $rule1 = field_base::make_instance($ruledata1);
        $rule1->save(self::TABLENAME);
        // As 'checkboxfield' == 0 => cohort 1 AND next rule must match.
        $ruledata2 = (object)[
            'fieldid' => $this->fieldids['checkboxfield'], 'datatype' => 'checkbox',
            'matchvalue' => '0', 'value' => $this->cohortids[1], 'andnextrule' => 1
        ];
        $rule2 = field_base::make_instance($ruledata2);
        $rule2->save(self::TABLENAME);
        // As 'textfield' == 'Fred' => cohort 2.
        $ruledata3 = (object)[
            'fieldid' => $this->fieldids['textfield'], 'datatype' => 'text',
            'matchvalue' => 'Fred', 'value' => $this->cohortids[2], 'andnextrule' => 0
        ];
        $rule3 = field_base::make_instance($ruledata3);
        $rule3->save(self::TABLENAME);
        // As 'textfield' == 'Fred' => cohort 3 - this rule should match on its own.
        $ruledata4 = (object)[
            'fieldid' => $this->fieldids['textfield'], 'datatype' => 'text',
            'matchvalue' => 'Fred', 'value' => $this->cohortids[3], 'andnextrule' => 0
        ];
        $rule4 = field_base::make_instance($ruledata4);
        $rule4->save(self::TABLENAME);

        // Process the rules to get the new cohortids.
        $user1cohortids = \local_profilecohort\profilecohort::get_mapped_value($user1->id, true);
        $user2cohortids = \local_profilecohort\profilecohort::get_mapped_value($user2->id, true);
        $user3cohortids = \local_profilecohort\profilecohort::get_mapped_value($user3->id, true);

        // User1 should match rule 1, 2 + 3 (cohort 0) and rule 4 (cohort 3) - but not cohort 1 or 2 (from rules 2 + 3).
        $this->assertEquals([$this->cohortids[0], $this->cohortids[3]], $user1cohortids);

        // User2 does not match rule 3 (so, not cohort 0) or rule 4 (so, not cohort 3).
        $this->assertEquals([], $user2cohortids);

        // User3 does not match rule 1 (so, not cohort 0), but does match rule 4 (cohort 3).
        $this->assertEquals([$this->cohortids[3]], $user3cohortids);

        // Execute all rules, to check cohort membership is updated correctly.
        $manager = new \local_profilecohort\profilecohort();
        $manager->update_all_cohorts_from_rules();

        // Check the cohorts have been updated, as expected.
        $this->assertTrue(cohort_is_member($this->cohortids[0], $user1->id));
        $this->assertFalse(cohort_is_member($this->cohortids[1], $user1->id));
        $this->assertFalse(cohort_is_member($this->cohortids[2], $user1->id));
        $this->assertTrue(cohort_is_member($this->cohortids[3], $user1->id));

        $this->assertFalse(cohort_is_member($this->cohortids[0], $user2->id));
        $this->assertFalse(cohort_is_member($this->cohortids[1], $user2->id));
        $this->assertFalse(cohort_is_member($this->cohortids[2], $user2->id));
        $this->assertFalse(cohort_is_member($this->cohortids[3], $user2->id));

        $this->assertFalse(cohort_is_member($this->cohortids[0], $user3->id));
        $this->assertFalse(cohort_is_member($this->cohortids[1], $user3->id));
        $this->assertFalse(cohort_is_member($this->cohortids[2], $user3->id));
        $this->assertTrue(cohort_is_member($this->cohortids[3], $user3->id));
    }

    /**
     * Test combining rules together using 'and' where the _last_ rule has the 'andnextrule' flag set
     */
    public function test_and_rules_ending_in_and() {
        global $DB;

        // Set up a user with 'menufield' set to 'Opt 1', 'checkboxfield' set to 'No'.
        $user1 = $this->getDataGenerator()->create_user();
        $ins = (object)['userid' => $user1->id, 'fieldid' => $this->fieldids['menufield'], 'data' => 'Opt 1'];
        $DB->insert_record('user_info_data', $ins);
        $ins = (object)['userid' => $user1->id, 'fieldid' => $this->fieldids['checkboxfield'], 'data' => '0'];
        $DB->insert_record('user_info_data', $ins);

        // Set up a user with 'menufield' set to 'Opt 1', 'checkboxfield' set to 'Yes'.
        $user2 = $this->getDataGenerator()->create_user();
        $ins = (object)['userid' => $user2->id, 'fieldid' => $this->fieldids['menufield'], 'data' => 'Opt 1'];
        $DB->insert_record('user_info_data', $ins);
        $ins = (object)['userid' => $user2->id, 'fieldid' => $this->fieldids['checkboxfield'], 'data' => '1'];
        $DB->insert_record('user_info_data', $ins);

        // Create rules - note cohort value for rule 2 (cohort 1) should never be used,
        // as it is an additional rule to the rule above it.

        // As 'menufield' ==  'Opt 1' => cohort 0 AND next rule must match.
        $ruledata1 = (object)[
            'fieldid' => $this->fieldids['menufield'], 'datatype' => 'menu',
            'matchvalue' => 'Opt 1', 'value' => $this->cohortids[0], 'andnextrule' => 1
        ];
        $rule1 = field_base::make_instance($ruledata1);
        $rule1->save(self::TABLENAME);
        // As 'checkboxfield' == 0 => cohort 1 AND next rule must match.
        $ruledata2 = (object)[
            'fieldid' => $this->fieldids['checkboxfield'], 'datatype' => 'checkbox',
            'matchvalue' => '0', 'value' => $this->cohortids[1], 'andnextrule' => 1
        ];
        $rule2 = field_base::make_instance($ruledata2);
        $rule2->save(self::TABLENAME);

        // Process the rules to get the new cohortids.
        $user1cohortids = \local_profilecohort\profilecohort::get_mapped_value($user1->id, true);
        $user2cohortids = \local_profilecohort\profilecohort::get_mapped_value($user2->id, true);

        // User1 should match rule 1 + 2 (cohort 0).
        $this->assertEquals([$this->cohortids[0]], $user1cohortids);

        // User2 does not match rule 2 (so, not cohort 0).
        $this->assertEquals([], $user2cohortids);

        // Execute all rules, to check cohort membership is updated correctly.
        $manager = new \local_profilecohort\profilecohort();
        $manager->update_all_cohorts_from_rules();

        // Check the cohorts have been updated, as expected.
        $this->assertTrue(cohort_is_member($this->cohortids[0], $user1->id));
        $this->assertFalse(cohort_is_member($this->cohortids[1], $user1->id));
        $this->assertFalse(cohort_is_member($this->cohortids[2], $user1->id));
        $this->assertFalse(cohort_is_member($this->cohortids[3], $user1->id));

        $this->assertFalse(cohort_is_member($this->cohortids[0], $user2->id));
        $this->assertFalse(cohort_is_member($this->cohortids[1], $user2->id));
        $this->assertFalse(cohort_is_member($this->cohortids[2], $user2->id));
        $this->assertFalse(cohort_is_member($this->cohortids[3], $user2->id));
    }
}
