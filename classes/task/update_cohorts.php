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
 * Local plugin "Profile field cohort membership" - Task to update user cohorts, based on the specified rules.
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_profilecohort\task;

use local_profilecohort\profilecohort;

defined('MOODLE_INTERNAL') || die();

/**
 * Class updated_cohorts
 * @package local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_cohorts extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     * @return string
     */
    public function get_name() {
        return get_string('updatecohorts', 'local_profilecohort');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        if (get_config('local_profilecohort', 'updatecohorts')) {
            $manager = new profilecohort();
            $manager->update_all_cohorts_from_rules();
            set_config('updatecohorts', false, 'local_profilecohort');
        }
    }
}
