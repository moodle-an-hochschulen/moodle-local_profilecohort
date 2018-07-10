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
 * Local plugin "Profile field based cohort membership" - Handles text field types
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_profilecohort;

use MoodleQuickForm;

defined('MOODLE_INTERNAL') || die();

/**
 * Class field_text
 * @package local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_text extends field_base {
    /**
     * Field must match the value exactly.
     */
    const MATCH_EXACT = 'exact';
    /**
     * Field must contain the value.
     */
    const MATCH_CONTAINS = 'contains';
    /**
     * Field must not match the value.
     */
    const MATCH_NOTEXACT = 'notexact';
    /**
     * Field must not contain the value.
     */
    const MATCH_NOTCONTAINS = 'notcontains';
    /**
     * Field is empty.
     */
    const MATCH_EMPTY = 'empty';
    /**
     * Field is not empty.
     */
    const MATCH_NOTEMPTY = 'notempty';

    /**
     * @var string[] list of valid match types for this field.
     */
    protected static $matchtypes = [
        self::MATCH_EXACT, self::MATCH_CONTAINS,
        self::MATCH_NOTEXACT, self::MATCH_NOTCONTAINS,
        self::MATCH_ISDEFINED, self::MATCH_NOTDEFINED,
        self::MATCH_EMPTY, self::MATCH_NOTEMPTY
    ];

    /**
     * field_text constructor.
     * @param object $ruledata (optional)
     */
    protected function __construct($ruledata = null) {
        parent::__construct($ruledata);
        if (!in_array($this->matchtype, self::$matchtypes)) {
            $this->matchtype = self::MATCH_EXACT;
        }
    }

    /**
     * Does the given field value match this rule?
     * @param string $value
     * @return bool
     */
    protected function matches_internal($value) {
        $value = strtolower(trim(strip_tags($value)));
        $matchvalue = strtolower(trim($this->matchvalue));
        switch ($this->matchtype) {
            case self::MATCH_NOTEXACT:
                return ($value != $matchvalue);
            case self::MATCH_CONTAINS:
                return (strpos($value, $matchvalue) !== false);
            case self::MATCH_NOTCONTAINS:
                return (strpos($value, $matchvalue) === false);
            case self::MATCH_EMPTY:
                return strlen($value) == 0;
            case self::MATCH_NOTEMPTY:
                return strlen($value) > 0;
            case self::MATCH_EXACT:
            default:
                return ($value == $matchvalue);
        }
    }

    /**
     * Add the fields for editing this specific field type
     * @param MoodleQuickForm $mform
     * @param string $id
     * @return \HTML_QuickForm_element[]
     */
    protected function add_form_field_internal(MoodleQuickForm $mform, $id) {
        $matchopts = [];
        foreach (self::$matchtypes as $matchtype) {
            $strmatchtype = 'match_'.str_replace('!', '', $matchtype);
            $matchopts[$matchtype] = get_string($strmatchtype, 'local_profilecohort');
        }
        $type = $mform->createElement('select', "matchtype[$id]", get_string('matchtype', 'local_profilecohort'), $matchopts);
        $mform->setType("matchtype[$id]", PARAM_TEXT);
        $mform->setDefault("matchtype[$id]", $this->matchtype);

        $match = $mform->createElement('text', "matchvalue[$id]", get_string('matchvalue', 'local_profilecohort'));
        $mform->setType("matchvalue[$id]", PARAM_TEXT);
        $mform->setDefault("matchvalue[$id]", $this->matchvalue);
        $mform->disabledIf("matchvalue[$id]", "matchtype[$id]", 'eq', self::MATCH_ISDEFINED);
        $mform->disabledIf("matchvalue[$id]", "matchtype[$id]", 'eq', self::MATCH_NOTDEFINED);
        $mform->disabledIf("matchvalue[$id]", "matchtype[$id]", 'eq', self::MATCH_EMPTY);
        $mform->disabledIf("matchvalue[$id]", "matchtype[$id]", 'eq', self::MATCH_NOTEMPTY);

        return [$type, $match];
    }

    /**
     * Validation specific to each field type
     * @param array $formdata
     * @param string $id the form identifier for this rule
     * @return array $formfieldname => $errormessage
     */
    protected function validation_internal($formdata, $id) {
        $errors = [];
        if (!in_array($formdata['matchtype'][$id], [
                self::MATCH_ISDEFINED, self::MATCH_NOTDEFINED,
                self::MATCH_EMPTY, self::MATCH_NOTEMPTY
        ])) {
            if (empty($formdata['matchvalue'][$id])) {
                $errors["matchvalue[$id]"] = get_string('required');
            }
        }
        return $errors;
    }
}
