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
 * Local plugin "Profile field based cohort membership" - Base class for handling different field types
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_profilecohort;

use MoodleQuickForm;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Class field_base
 * @package local_profilecohort
 * @property-read int $id
 * @property int $fieldid
 * @property string $matchtype
 * @property string $matchvalue
 * @property string $value
 */
abstract class field_base {
    // Fields from main database table.
    protected $id = null;
    protected $fieldid = null;
    protected $matchtype = null;
    protected $matchvalue = null;
    protected $value = null;
    protected $sortorder = null;
    // Extra fields from user_info_field table.
    protected $name = null;
    protected $param1 = null;

    protected $formposition = null;

    protected static $fields = ['id', 'fieldid', 'matchtype', 'matchvalue', 'value', 'sortorder'];
    protected static $extrafields = ['name', 'param1'];

    const MATCH_ISDEFINED = '!!defined!!';
    const MATCH_NOTDEFINED = '!!notdefined!!';

    /**
     * Creates a new instance of a rule to hold the given data.
     * Returns null for any datatypes that are unsupported by rules.
     *
     * @param object $ruledata - including the 'datatype'
     * @param int $strictness - whether or not to insist that the type exists
     * @return field_base|null
     * @throws \coding_exception
     */
    public static function make_instance($ruledata, $strictness = MUST_EXIST) {
        $classname = __NAMESPACE__.'\field_'.$ruledata->datatype;
        if (!class_exists($classname)) {
            if ($strictness == MUST_EXIST) {
                throw new \coding_exception('Non-existent rule type');
            }
            return null;
        }
        return new $classname($ruledata);
    }

    /**
     * field_base constructor.
     * @param object $ruledata (optional)
     */
    protected function __construct($ruledata = null) {
        if ($ruledata) {
            foreach (array_merge(self::$fields, self::$extrafields) as $field) {
                if (isset($ruledata->$field)) {
                    $this->$field = $ruledata->$field;
                }
            }
        }
    }

    /**
     * The position on the form that this rule is currently being displayed at.
     * @param int $position
     */
    public function set_form_position($position) {
        $this->formposition = $position;
    }

    /**
     * Get the new position that the user has requested for this rule.
     * @param object $formdata the data returned by the form
     * @return array [$dir, $newposition] where $dir is 0, -1, +1 for unchanged, moved up, moved down
     */
    public function get_new_position($formdata) {
        $id = $this->get_form_id();
        if (!empty($formdata->delete[$id])) {
            return [0, $this->formposition];
        }
        if (!isset($formdata->moveto[$id])) {
            return [0, $this->formposition];
        }
        $moveto = $formdata->moveto[$id];
        $dir = 0;
        if ($moveto < $this->formposition) {
            $dir = -1;
        } else if ($moveto > $this->formposition) {
            $dir = 1;
        }
        return [$dir, $moveto];
    }

    /**
     * Get the ID to use for the form elements.
     * @return int|string
     */
    protected function get_form_id() {
        if ($this->id) {
            return $this->id;
        }
        return 'new';
    }

    /**
     * Given all the data returned by the form, update this rule from the relevant fields
     * then (if changed), save the data back into the database.
     *
     * @param $tablename
     * @param $formdata
     * @return bool has the rule changed?
     */
    public function update_from_form_data($tablename, $formdata) {
        $id = $this->get_form_id();

        if (!empty($formdata->delete[$id])) {
            $this->delete($tablename);
            return true;
        }

        $changed = false;
        foreach (self::$fields as $field) {
            if ($field == 'id') {
                continue;
            }
            if (!isset($formdata->$field)) {
                continue;
            }
            $values = $formdata->$field;
            if (!array_key_exists($id, $values)) {
                continue;
            }
            if ($this->$field != $values[$id]) {
                $this->$field = $values[$id];
                $changed = true;
            }
        }
        if ($changed) {
            $this->save($tablename);
        }
        return $changed;
    }

    /**
     * Save the rule into the database.
     * @param string $tablename the table to save the rule in
     */
    public function save($tablename) {
        global $DB;

        $ins = new stdClass();
        foreach (self::$fields as $field) {
            $ins->$field = $this->$field;
        }
        if ($this->id) {
            $ins->id = $this->id;
            $DB->update_record($tablename, $ins);
        } else {
            unset($ins->id);
            $ins->sortorder = intval($DB->get_field($tablename, 'MAX(sortorder)', [])) + 1;
            $this->id = $DB->insert_record($tablename, $ins);
        }
    }

    /**
     * Delete the rule from the database.
     * @param string $tablename the table to delete the rule from
     */
    public function delete($tablename) {
        global $DB;
        if (!$this->id) {
            return;
        }
        $DB->delete_records($tablename, array('id' => $this->id));
        $this->id = null;
    }

    /**
     * Magic get function
     * @param string $name
     * @return mixed
     * @throws \coding_exception
     */
    public function __get($name) {
        if (!in_array($name, self::$fields)) {
            throw new \coding_exception("Attempting to access unknown field $name");
        }
        return $this->$name;
    }

    /**
     * Magic set function
     * @param string $name
     * @param mixed $value
     * @throws \coding_exception
     */
    public function __set($name, $value) {
        if (!in_array($name, self::$fields)) {
            throw new \coding_exception("Attempting to set unknown field $name");
        }
        if ($name == 'id') {
            throw new \coding_exception("Cannot update id field directly");
        }
        $this->$name = $value;
    }

    /**
     * Check if the given profile fields cause this rule to match
     * @param string[] $fields $fieldid => $fieldvalue
     * @return null|string
     */
    public function get_value($fields) {
        if (isset($fields[$this->fieldid])) {
            if ($this->matchtype == self::MATCH_ISDEFINED) {
                return $this->value;
            } else if ($this->matchtype != self::MATCH_NOTDEFINED) {
                if ($this->matches_internal($fields[$this->fieldid])) {
                    return $this->value;
                }
            }
        } else if ($this->matchtype == self::MATCH_NOTDEFINED) {
            return $this->value;
        }
        return null;
    }

    /**
     * Does the given field value match this rule?
     * @param string $value
     * @return bool
     */
    protected function matches_internal($value) {
        return ($value == $this->matchvalue);
    }

    /**
     * Add the fields needed to edit this rule.
     * @param MoodleQuickForm $mform
     * @param array $values the full list of values this could be mapped onto
     * @param int $rulecount
     */
    public function add_form_field(MoodleQuickForm $mform, $values, $rulecount) {
        $id = $this->get_form_id();
        $mform->addElement('hidden', "fieldid[$id]", $this->fieldid);
        $mform->setType("fieldid[$id]", PARAM_INT);

        $group = $this->add_form_field_internal($mform, $id);
        $group[] = $mform->createElement('static', "valuelabel[$id]", '', get_string('selectvalue', 'local_profilecohort'));
        $group[] = $mform->createElement('select', "value[$id]", get_string('selectvalue', 'local_profilecohort'), $values);
        $mform->setDefault("value[$id]", $this->value);

        $prefix = '';
        if ($this->id) {
            $group[] = $mform->createElement('static', '', '', '<br><span class="localprofile-rule-actions">');
            if ($rulecount > 1) {
                $moveopts = range(1, $rulecount);
                $moveopts = array_combine($moveopts, $moveopts);
                $group[] = $mform->createElement('static', "movelabel[$id]", '', get_string('moveto', 'local_profilecohort'));
                $group[] = $mform->createElement('select', "moveto[$id]", get_string('moveto', 'local_profilecohort'), $moveopts,
                                                 ['class' => 'moveto']);
                $mform->setDefault("moveto[$id]", $this->formposition);
                $group[] = $mform->createElement('static', '', '', '<br>');
            }

            $group[] = $mform->createElement('advcheckbox', "delete[$id]", '', get_string('delete', 'local_profilecohort'));
            $group[] = $mform->createElement('static', '', '', '</span>');

            $prefix = '<span class="localprofile-number">'.$this->formposition.'</span>. ';
        }

        $name = $prefix.get_string('iffield', 'local_profilecohort', format_string($this->name));
        $mform->addGroup($group, "group-$id", $name, ' ', false);
    }

    /**
     * Add the fields for editing this specific field type
     * @param MoodleQuickForm $mform
     * @param string $id
     */
    abstract protected function add_form_field_internal(MoodleQuickForm $mform, $id);

    /**
     * Validate the submitted data when editing this form field.
     * @param array $formdata
     * @return array $formfieldname => $errormessage
     */
    public function validation($formdata) {
        $id = $this->get_form_id();
        $errors = $this->validation_internal($formdata, $id);
        if (empty($formdata['value'][$id])) {
            $errors["value[$id]"] = get_string('required');
        }
        // Error messages don't show up properly for grouped elements, so add the message
        // to the group itself, instead.
        if ($errors) {
            $errors = ["group-$id" => get_string('required')];
        }
        return $errors;
    }

    /**
     * Validation specific to each field type
     * @param array $formdata
     * @param string $id the form identifier for this rule
     * @return array $formfieldname => $errormessage
     */
    protected function validation_internal($formdata, $id) {
        return [];
    }
}
