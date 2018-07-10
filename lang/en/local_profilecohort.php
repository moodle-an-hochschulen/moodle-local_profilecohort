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
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addintro'] = 'On this tab, you can add a new rule mapping a custom user profile field\'s value to a cohort the user will be added to.';
$string['addrule'] = 'Add rule for custom user profile field ...';
$string['addrules'] = 'Add new rule';
$string['andnextrule'] = 'The next rule must also match, to join this cohort.';
$string['delete'] = 'Delete this rule';
$string['cohortsintro'] = 'On this tab, you select the cohorts you want this plugin to manage.<br>
Once selected, you will not be able to manually update the members of these cohorts anymore. Furthermore, any users who are currently a member of these cohorts will be removed from the cohorts and the cohorts are then filled from scratch with the users matching the rule(s) you create with this plugin.<br>
If you decide to stop managing a cohort with this plugin and deselect it here, all users who are currently a member of this cohort will keep being a member. Additionally, you will be able to manually update the members of this cohort again.';
$string['iffield'] = 'If {$a}';
$string['invisiblecohortsnote'] = 'Please note: This plugin handles invisible cohorts completely equal to visible cohorts. Thus, the list of cohorts also includes cohorts which have been created as invisible.';
$string['match_contains'] = 'Contains';
$string['match_defined'] = 'Is defined';
$string['match_empty'] = 'Is empty';
$string['match_exact'] = 'Matches';
$string['match_notcontains'] = 'Does not contain';
$string['match_notdefined'] = 'Is not defined';
$string['match_notempty'] = 'Is not empty';
$string['match_notexact'] = 'Does not match';
$string['matchtype'] = 'Match type';
$string['matchvalue'] = 'Match value';
$string['members'] = 'Cohort members';
$string['membersintro'] = 'On this tab, you can see the users who are currently members of the cohorts which are managed by this plugin.';
$string['moveto'] = 'Move to position';
$string['nocohorts'] = 'There are no cohorts available for use by this plugin - please visit {$a} to create some cohorts';
$string['nofields'] = 'No custom user profile fields have been defined.<br>You need to define custom user profile fields before you can add rules here - please visit {$a} to create at least one custom user profile field';
$string['nousers'] = 'This cohort does not, currently, contain any users';
$string['pluginname'] = 'Profile field based cohort membership';
$string['privacy:metadata'] = 'The profile field based cohort membership plugin provides extended functionality to Moodle admins, but does not store any personal data.';
$string['selectcohorts'] = 'Select cohorts to be managed';
$string['selectvalue'] = 'the user will be added to cohort';
$string['updatecohorts'] = 'Update cohorts from custom user profile fields';
$string['viewintro'] = 'On this tab, you define the rules mapping custom user profile fields to the cohorts the user will be added to.<br>
The defined rules are processed in the order that they are displayed. However, a user matching multiple rules will be added to all the relevant cohorts.<br>
Please note: When rules are changed, a background task will be scheduled to update all affected users - there will be a short delay (a few minutes, depending on the configuration of your scheduled tasks in Moodle and the cronjob on the Moodle server) before all user memberships in the cohorts are updated. Nevertheless, any user who logs in before the background task is finished will be updated immediately.';
$string['viewrules'] = 'View / edit rules';
