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
 * Local plugin "Profile field based cohort membership" - Form to edit the profile field rules
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_profilecohort;

use moodleform;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir.'/formslib.php');

/**
 * Class fields_form
 * @package local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fields_form extends moodleform {

    /**
     * Return the current list of rules.
     * @return field_base[]
     */
    private function get_rules() {
        return $this->_customdata['rules'];
    }

    /**
     * Return the possible values that could be assigned as a result of the rules matching.
     * @return string[]
     */
    private function get_values() {
        return $this->_customdata['values'];
    }

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {
        global $PAGE;

        $mform = $this->_form;

        $mform->addElement('hidden', 'add', null);
        $mform->setType('add', PARAM_INT);
        $mform->addElement('hidden', 'action', null);
        $mform->setType('action', PARAM_ALPHA);
        $mform->addElement('header', 'hiddenheading');

        $values = [null => get_string('choosedots')] + $this->get_values();
        $rules = $this->get_rules();
        foreach ($rules as $rule) {
            $rule->add_form_field($mform, $values, count($rules));
        }
        $this->add_action_buttons();

        $PAGE->requires->js_call_amd('local_profilecohort/reorder', 'init');
    }

    /**
     * Get each of the rules to validate its own fields
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        foreach ($this->get_rules() as $rule) {
            $err = $rule->validation($data);
            $errors = array_merge($errors, $err);
        }
        return $errors;
    }
}
