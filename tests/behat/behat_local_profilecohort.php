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
 * Local plugin "Profile field based cohort membership" - Define custom behat rules
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Gherkin\Node\TableNode;

require_once(__DIR__.'/../../../../lib/behat/behat_base.php');

/**
 * Class behat_local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_local_profilecohort extends behat_base {
    /**
     * Create the user profile fields requested.
     * @Given /^the following custom user profile fields exist \(local_profilecohort\):$/
     * @param TableNode $table
     * @throws Exception
     */
    public function the_following_custom_user_profile_fields_exist(TableNode $table) {
        global $DB;

        $required = [
            'shortname',
            'name',
            'datatype',
        ];
        $optional = [
            'param1' => ''
        ];

        $data = $table->getHash();
        $firstrow = reset($data);

        // Check required fields are present.
        foreach ($required as $reqname) {
            if (!isset($firstrow[$reqname])) {
                throw new Exception('Custom profile fields require the field '.$reqname.' to be set');
            }
        }

        // Create each custom profile field.
        $catid = $DB->get_field('user_info_category', 'MIN(id)', []);
        if (!$catid) {
            $ins = (object) ['name' => 'Other fields', 'sortorder' => 1];
            $catid = $DB->insert_record('user_info_category', $ins);
        }
        $sharedinfo = ['descriptionformat' => 1, 'categoryid' => $catid, 'visible' => 2];

        foreach ($data as $row) {
            $ins = array_merge($optional, $sharedinfo);
            foreach ($row as $fieldname => $value) {
                if (!in_array($fieldname, $required) && !array_key_exists($fieldname, $optional)) {
                    throw new Exception('Invalid field '.$fieldname.' in custom profile field');
                }
                if ($fieldname == 'param1' && $row['datatype'] == 'menu') {
                    // It is difficult to include multi-line params, so replace commas with newlines for menu options.
                    $value = str_replace(',', "\n", $value);
                }
                $ins[$fieldname] = $value;
            }
            $DB->insert_record('user_info_field', (object) $ins);
        }
    }
}
