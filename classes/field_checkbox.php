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
 * Local plugin "Profile field based cohort membership" - Handles checkbox field types
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_profilecohort;

use MoodleQuickForm;

defined('MOODLE_INTERNAL') || die();

/**
 * Class field_checkbox
 * @package local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_checkbox extends field_base {
    /**
     * Add the fields for editing this specific field type
     * @param MoodleQuickForm $mform
     * @param string $id
     * @return \HTML_QuickForm_element[]
     */
    protected function add_form_field_internal(MoodleQuickForm $mform, $id) {
        // Override the matchvalue with the matchtype, if the match type is one of the 'defined' ones.
        $matchvalue = $this->matchvalue;
        if (in_array($this->matchtype, [self::MATCH_NOTDEFINED, self::MATCH_ISDEFINED])) {
            $matchvalue = $this->matchtype;
        }
        $opts = [
            1 => get_string('yes'),
            0 => get_string('no'),
            self::MATCH_ISDEFINED => get_string('match_defined', 'local_profilecohort'),
            self::MATCH_NOTDEFINED => get_string('match_notdefined', 'local_profilecohort'),
        ];

        $label = $mform->createElement('static', "matchlabel[$id]", '', get_string('match_exact', 'local_profilecohort'));
        $sel = $mform->createElement('select', "matchvalue[$id]", get_string('matchvalue', 'local_profilecohort'), $opts);
        $mform->setDefault("matchvalue[$id]", $matchvalue);
        return [$label, $sel];
    }

    /**
     * Given all the data returned by the form, update this rule from the relevant fields
     * then (if changed), save the data back into the database.
     *
     * @param string $tablename
     * @param object $formdata
     * @return bool has the rule changed?
     */
    public function update_from_form_data($tablename, $formdata) {
        // Extract the 'defined/not defined' type from the values select.
        $id = $this->get_form_id();
        if (in_array($formdata->matchvalue[$id], [self::MATCH_NOTDEFINED, self::MATCH_ISDEFINED])) {
            $formdata->matchtype[$id] = $formdata->matchvalue[$id];
            $formdata->matchvalue[$id] = null;
        } else {
            $formdata->matchtype[$id] = null;
        }

        return parent::update_from_form_data($tablename, $formdata);
    }
}
