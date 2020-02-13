moodle-local_profilecohort
==========================

Changes
-------

### v3.8-r1

* 2020-02-13 - Prepare compatibility for Moodle 3.8.

### v3.7-r1

* 2019-07-02 - Make codechecker happy.
* 2019-07-02 - Prepare compatibility for Moodle 3.7.

### v3.6-r2

* 2019-03-26 - Bugfix: rules didn't match properly if last rule had andnextrule.

### v3.6-r1

* 2019-03-26 - Replaced deprecated Behat test steps.
* 2019-03-26 - Check compatibility for Moodle 3.6, no functionality change.
* 2019-03-25 - Update user's cohorts also when the user is created or updated.
* 2019-03-25 - Add the fact that we are listening to \core\event\user_loggedinas event to README.md.
* 2018-12-05 - Changed travis.yml due to upstream changes.

### v3.5-r3

* 2018-07-31 - Update user’s cohorts also on login as, not only on login.

### v3.5-r2

* 2018-07-10 - Support for testing if text fields or textareas are (not) empty.

### v3.5-r1

* 2018-05-30 - Minor change to the card structure due to changes in Moodle core.
* 2018-05-30 - Check compatibility for Moodle 3.5, no functionality change.

### v3.4-r2

* 2018-05-16 - Implement Privacy API.

### v3.4-r1

* 2017-12-12 - Check compatibility for Moodle 3.4, no functionality change.
* 2017-12-05 - Added Workaround to travis.yml for fixing Behat tests with TravisCI.

### v3.3-r1

* 2017-11-23 - Check compatibility for Moodle 3.3, no functionality change.
* 2017-11-08 - Updated travis.yml to use newer node version for fixing TravisCI error.

### v3.2-r6

* 2017-09-25 - Add support for invisible cohorts

### v3.2-r5

* 2017-06-25 - Make codechecker happier

### v3.2-r4

* 2017-05-19 - Bugfix: String in language pack didn't work for Moodle installed in subdirectories - Credits to David Mudrák
* 2017-05-29 - Add Travis CI support

### v3.2-r3

* 2017-05-22 - Make Moodle repo prechecker even happier

### v3.2-r2

* 2017-05-21 - Update code documentation - Credits to Davo Smith

### v3.2-r1

* 2017-05-05 - Improve README.md
* 2017-01-29 - Add several features before publishing the plugin in the Moodle plugin repo
* 2017-01-18 - Check compatibility for Moodle 3.2, no functionality change
* 2017-01-12 - Move Changelog from README.md to CHANGES.md
* 2016-04-18 - Initial version
