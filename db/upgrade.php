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
 * Local plugin "Profile field based cohort membership" - DB upgrade steps
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Steps to take during upgrades
 * @param int $oldversion (optional)
 * @return bool
 */
function xmldb_local_profilecohort_upgrade($oldversion = 0) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016071200) {
        // Changing nullability of field matchvalue on table local_profilecohort to null.
        $table = new xmldb_table('local_profilecohort');
        $field = new xmldb_field('matchvalue', XMLDB_TYPE_TEXT, null, null, null, null, null, 'matchtype');

        // Launch change of nullability for field matchvalue.
        $dbman->change_field_notnull($table, $field);

        // Profilecohort savepoint reached.
        upgrade_plugin_savepoint(true, 2016071200, 'local', 'profilecohort');
    }

    if ($oldversion < 2016091601) {

        // Define field andnextrule to be added to local_profilecohort.
        $table = new xmldb_table('local_profilecohort');
        $field = new xmldb_field('andnextrule', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'sortorder');

        // Conditionally launch add field andnextrule.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Profilecohort savepoint reached.
        upgrade_plugin_savepoint(true, 2016091601, 'local', 'profilecohort');
    }

    return true;
}
