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
 * Local plugin "Profile field based cohort membership" - Handles menu field types
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_profilecohort;

use MoodleQuickForm;

defined('MOODLE_INTERNAL') || die();

/**
 * Class field_menu
 * @package local_profilecohort
 */
class field_menu extends field_base {
    /** @var string[] */
    protected $opts = [];

    /**
     * field_menu constructor.
     * @param object $ruledata (optional)
     */
    protected function __construct($ruledata = null) {
        parent::__construct($ruledata);
        if ($this->param1) {
            $this->opts = explode("\n", $this->param1);
            $this->opts = array_filter(array_map('trim', $this->opts));
            $this->opts = array_combine($this->opts, $this->opts);
        }
    }

    /**
     * @param MoodleQuickForm $mform
     * @param string $id
     * @return \HTML_QuickForm_element[]
     */
    protected function add_form_field_internal(MoodleQuickForm $mform, $id) {
        $label = $mform->createElement('static', "matchlabel[$id]", '', get_string('match_exact', 'local_profilecohort'));
        $opts = [null => get_string('choosedots')] + $this->opts;
        $sel = $mform->createElement('select', "matchvalue[$id]", get_string('matchvalue', 'local_profilecohort'), $opts);
        $mform->setType("matchvalue[$id]", PARAM_TEXT);
        $mform->setDefault("matchvalue[$id]", $this->matchvalue);
        return [$label, $sel];
    }

    /**
     * Validation specific to each field type
     * @param array $formdata
     * @param string $id the form identifier for this rule
     * @return array $formfieldname => $errormessage
     */
    protected function validation_internal($formdata, $id) {
        $errors = [];
        if (empty($formdata['matchvalue'][$id])) {
            $errors["matchvalue[$id]"] = get_string('required');
        }
        return $errors;
    }
}
