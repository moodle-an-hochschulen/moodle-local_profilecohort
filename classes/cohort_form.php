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
 * Local plugin "Profile field based cohort membership" - Form for selecting which cohorts this plugin should manage.
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_profilecohort;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir.'/formslib.php');

/**
 * Class cohort_form
 * @package local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;
        $cohorts = $this->_customdata['cohorts'];

        $mform->addElement('html', \html_writer::tag('div', get_string('cohortsintro', 'local_profilecohort').'<br />'.
                                                     get_string('invisiblecohortsnote', 'local_profilecohort'),
                                                     array('id' => 'intro', 'class' => 'box generalbox')));

        if (!$cohorts) {
            $cohorturl = new \moodle_url('/cohort/index.php');
            $link = \html_writer::link($cohorturl, get_string('cohorts', 'core_cohort'));
            $mform->addElement('html', \html_writer::tag('div', get_string('nocohorts', 'local_profilecohort', $link),
                                                         array('class' => 'alert alert-warning')));
        } else {
            foreach ($cohorts as $cohort) {
                $mform->addElement('advcheckbox', "cohort[$cohort->id]", null, format_string($cohort->name));
                $mform->setDefault("cohort[$cohort->id]", ($cohort->component == 'local_profilecohort'));
            }
        }

        $this->add_action_buttons();
    }
}
