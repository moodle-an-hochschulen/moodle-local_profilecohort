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
 * Page for editing the list of cohorts available to the plugin
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_profilecohort\profilecohort;

require_once(dirname(__FILE__).'/../../config.php');
global $PAGE, $CFG, $OUTPUT;
require_once($CFG->libdir.'/adminlib.php');

$url = new moodle_url('/local/profilecohort/cohorts.php');
admin_externalpage_setup('local_profilecohort', '', null, $url);

$title = get_string('selectcohorts', 'local_profilecohort');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$manager = new profilecohort();
$manager->process_cohort_form();

echo $OUTPUT->header();
echo $manager->output_cohort_form();
echo $OUTPUT->footer();
