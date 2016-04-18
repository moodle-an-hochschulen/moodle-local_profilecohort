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
 * Local plugin "Profile field based cohort membership" - Language pack
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addrule'] = 'Add profile rule ...';
$string['delete'] = 'Delete this rule';
$string['cohortsintro'] = 'Please select the cohorts you want this plugin to manage. Once selected, you will not be able to manually update these cohorts and any users currently allocated to these cohorts will be removed (unless they match the rules you specify).';
$string['iffield'] = 'If {$a}';
$string['intro'] = 'Use this form to define mappings between user profile fields and the cohorts the user will be added to.<br>
Rules are processed in the order that they are displayed - the first matching rule will be used.<br>
When rules are changed a background task will be scheduled to update all affected users - there will be a short delay before all users are updated (any user who logs in before then will be updated immediately).';
$string['match_contains'] = 'Contains';
$string['match_exact'] = 'Matches';
$string['matchtype'] = 'Match type';
$string['matchvalue'] = 'Match value';
$string['nocohorts'] = 'There are no cohorts available for use by this plugin - please visit {$a} to create some cohorts';
$string['nofields'] = 'No custom profile fields have been defined';
$string['pluginname'] = 'Profile field based cohort membership';
$string['selectcohorts'] = 'Select cohorts for this plugin to manage';
$string['selectvalue'] = 'the user will be added to cohort';
$string['updatecohorts'] = 'Update cohorts from profile fields';
