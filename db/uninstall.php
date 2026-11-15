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
 * Local plugin "Profile field based cohort membership" - DB uninstall steps
 *
 * @package   local_profilecohort
 * @copyright 2026 Alexander Bias <bias@alexanderbias.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Steps to take when the plugin is uninstalled
 *
 * The plugin marks the cohorts which it manages by setting the component field in the core cohort table.
 * Core does not allow to edit, delete or manually fill cohorts which have a component set.
 * Thus, the managed cohorts have to be released here, otherwise they would remain locked after the plugin is gone.
 * The members of the cohorts are kept, just like when a cohort is deselected on the plugin's cohorts tab.
 *
 * @return bool
 */
function xmldb_local_profilecohort_uninstall() {
    global $CFG, $DB;

    require_once($CFG->dirroot . '/cohort/lib.php');

    $cohorts = $DB->get_records('cohort', ['component' => 'local_profilecohort'], 'id', 'id, contextid');
    foreach ($cohorts as $cohort) {
        // Use the core API (and pass only the fields which are needed) to make sure
        // that the cohort_updated event is triggered and timemodified is updated.
        cohort_update_cohort((object) [
            'id' => $cohort->id,
            'contextid' => $cohort->contextid,
            'component' => '',
        ]);
    }

    return true;
}
