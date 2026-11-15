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
 * Local plugin "Profile field based cohort membership" - Test the DB uninstall steps
 *
 * @package   local_profilecohort
 * @copyright 2026 Alexander Bias <bias@alexanderbias.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_profilecohort;

/**
 * Test the DB uninstall steps
 *
 * @package   local_profilecohort
 * @copyright 2026 Alexander Bias <bias@alexanderbias.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class uninstall_test extends \advanced_testcase {
    /**
     * Test that the managed cohorts are released when the plugin is uninstalled.
     *
     * @covers ::xmldb_local_profilecohort_uninstall
     */
    public function test_uninstall_releases_managed_cohorts(): void {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/cohort/lib.php');
        require_once($CFG->dirroot . '/local/profilecohort/db/uninstall.php');

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Create two cohorts which are managed by the plugin, a manual cohort and a cohort which is managed by another plugin.
        $managed1 = $generator->create_cohort(['component' => 'local_profilecohort']);
        $managed2 = $generator->create_cohort(['component' => 'local_profilecohort']);
        $manual = $generator->create_cohort();
        $other = $generator->create_cohort(['component' => 'local_somethingelse']);

        // Add a member to each cohort.
        $user = $generator->create_user();
        foreach ([$managed1, $managed2, $manual, $other] as $cohort) {
            cohort_add_member($cohort->id, $user->id);
        }

        // Run the uninstall steps.
        $sink = $this->redirectEvents();
        $this->assertTrue(xmldb_local_profilecohort_uninstall());
        $events = $sink->get_events();
        $sink->close();

        // The managed cohorts must be released.
        $this->assertSame('', $DB->get_field('cohort', 'component', ['id' => $managed1->id]));
        $this->assertSame('', $DB->get_field('cohort', 'component', ['id' => $managed2->id]));

        // The other cohorts must not be touched.
        $this->assertSame('', $DB->get_field('cohort', 'component', ['id' => $manual->id]));
        $this->assertSame('local_somethingelse', $DB->get_field('cohort', 'component', ['id' => $other->id]));

        // The cohort updated event must have been triggered for the managed cohorts only.
        $updatedids = [];
        foreach ($events as $event) {
            if ($event instanceof \core\event\cohort_updated) {
                $updatedids[] = (int) $event->objectid;
            }
        }
        sort($updatedids);
        $this->assertSame([(int) $managed1->id, (int) $managed2->id], $updatedids);

        // The members of all cohorts must be kept.
        foreach ([$managed1, $managed2, $manual, $other] as $cohort) {
            $this->assertTrue(cohort_is_member($cohort->id, $user->id));
        }
    }
}
