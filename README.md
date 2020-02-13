moodle-local_profilecohort
==========================

[![Build Status](https://travis-ci.org/moodleuulm/moodle-local_profilecohort.svg?branch=master)](https://travis-ci.org/moodleuulm/moodle-local_profilecohort)

Moodle plugin which lets admins manage cohort memberships based on users' custom profile fields


Requirements
------------

This plugin requires Moodle 3.8+


Motivation for this plugin
--------------------------

Moodle core provides a mechanim to manually fill cohorts with users (on Site administration -> Users -> Accounts -> Cohorts). This is fine for small Moodle installations where the cohort members don't change too often and where the Moodle admin has plenty of time to update the cohorts.

Now, larger or fragmented Moodle installations may have the need to manage a large number of cohorts which have a large amount of members and which may also change quite often. Managing cohorts by hand in such scenarios is simply unprofessional overkill - even / particularly if you distribute the work among multiple Moodle admins.

On the other hand these large or fragmented Moodle installations might already have some custom user profile fields which can be leveraged to decide which cohort(s) a user should be a member of. This plugin implements a simple solution to manage cohort memberships based on a users' custom profile field.


Installation
------------

Install the plugin like any other plugin to folder
/local/profilecohort

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage & Settings
----------------

After installing the plugin, it does not do anything to Moodle yet.

To configure the plugin and its behaviour, please visit:
Site administration -> Users -> Accounts -> Profile field based cohort membership.

There, you find four tabs:

### 1. View / edit rules

On this tab, you define the rules mapping custom user profile fields to the cohorts the user will be added to.
The defined rules are processed in the order that they are displayed. However, a user matching multiple rules will be added to all the relevant cohorts.

### 2. Add new rule

When you use the plugin for the first time and there are no rules yet, this is the tab you will be shown second.

On this tab, you can add a new rule mapping a custom user profile field's value to a cohort the user will be added to.

If no custom user profile fields have been defined in your Moodle installation yet, you need to define custom user profile fields first on /user/profile/index.php before you can add rules here.

### 3. Cohort members

On this tab, you can see the users who are currently members of the cohorts which are managed by this plugin.

This list is just a simple fill-in for a missing managed cohort members list in Moodle core (see below for details), but is sufficient for checking if the cohorts are filled properly.

### 4. Select cohorts to be managed

When you use the plugin for the first time, this is the tab you will be shown first.

On this tab, you select the cohorts you want this plugin to manage.

Once selected, you will not be able to manually update the members of these cohorts anymore. Furthermore, any users who are currently a member of these cohorts will be removed from the cohorts and the cohorts are then filled from scratch with the users matching the rule(s) you create with this plugin.

If you decide to stop managing a cohort with this plugin and deselect it here, all users who are currently a member of this cohort will keep being a member. Additionally, you will be able to manually update the members of this cohort again.


How this plugin works
---------------------

Besides the manual management of cohorts in Moodle core, Moodle is already prepared to let cohorts be managed by plugins. This plugin just leverages this prepared mechanism and marks existing cohorts as managed by local_profilecohort as soon as they are selected to be managed in the plugin.

For adding members to the cohorts, this plugin simply listens for the \core\event\user_loggedin, \core\event\user_loggedinas, \core\event\user_created and \core\event\user_updated events, checks all existing rules and adds the user to the cohorts matching for his custom user profile field values respectively removes him from all managed cohorts which he is already a member but does not match any rules anymore.

Additionally, there is a scheduled task which is used to update the cohorts of all affected users as soon as you create, change or delete any rule in the plugin. Depending on the configuration of your scheduled tasks in Moodle and the cronjob on the Moodle server, there might be a short delay before all user memberships in the cohorts are updated. Nevertheless, any user who logs in before the background task is finished will be updated immediately. If you want to check the plugin's scheduled task, please visit Site Administration -> Server -> Scheduled tasks and search for the "Update cohorts from custom user profile fields" task.


Relation to Totara audiences
----------------------------

There is a long-standing similar (and even more powerful) mechanism in Totara (https://www.totaralms.com/) called "audiences". This plugin was inspired by Totara audiences, but does neither reuse its code nor strive to fully reimplement it for Moodle.


Companion plugin local_cohortrole
---------------------------------

This plugin provides a mapping from custom user profile fields to cohorts. If you are looking for a mapping from custom user profile fields to system roles, you might want to look at local_cohortrole as a companion plugin.

local_cohortrole is maintained by Paul Holden and published on https://moodle.org/plugins/local_cohortrole. The development of this plugin and local_cohortrole is not related or synchronized in any way, we just want to recommend it as a solution approach as we are using both plugins in combination in production.


Managed cohorts member list in Moodle Core
------------------------------------------

Unfortunately, Moodle core does not have a members list for managed cohorts. That's why we added this member list to this plugin as a fill-in.

However, we created a Moodle tracker ticket on https://tracker.moodle.org/browse/MDL-58840 which proposes to add a managed cohorts member list to Moodle core.

Please vote for this ticket if you want to have this realized.


Theme support
-------------

This plugin acts behind the scenes, therefore it should work with all Moodle themes.
It has been developed on and tested only with Moodle Core's Boost theme.
It should also work with Boost child themes, including Moodle Core's Classic theme. However, we can't support any other theme than Boost.


Plugin repositories
-------------------

This plugin is published and regularly updated in the Moodle plugins repository:
http://moodle.org/plugins/view/local_profilecohort

The latest development version can be found on Github:
https://github.com/moodleuulm/moodle-local_profilecohort


Bug and problem reports / Support requests
------------------------------------------

This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.

Please report bugs and problems on Github:
https://github.com/moodleuulm/moodle-local_profilecohort/issues

We will do our best to solve your problems, but please note that due to limited resources we can't always provide per-case support.


Feature proposals
-----------------

Due to limited resources, the functionality of this plugin is primarily implemented for our own local needs and published as-is to the community. We are aware that members of the community will have other needs and would love to see them solved by this plugin.

Please issue feature proposals on Github:
https://github.com/moodleuulm/moodle-local_profilecohort/issues

Please create pull requests on Github:
https://github.com/moodleuulm/moodle-local_profilecohort/pulls

We are always interested to read about your feature proposals or even get a pull request from you, but please accept that we can handle your issues only as feature _proposals_ and not as feature _requests_.


Moodle release support
----------------------

Due to limited resources, this plugin is only maintained for the most recent major release of Moodle. However, previous versions of this plugin which work in legacy major releases of Moodle are still available as-is without any further updates in the Moodle Plugins repository.

There may be several weeks after a new major release of Moodle has been published until we can do a compatibility check and fix problems if necessary. If you encounter problems with a new major release of Moodle - or can confirm that this plugin still works with a new major relase - please let us know on Github.

If you are running a legacy version of Moodle, but want or need to run the latest version of this plugin, you can get the latest version of the plugin, remove the line starting with $plugin->requires from version.php and use this latest plugin version then on your legacy Moodle. However, please note that you will run this setup completely at your own risk. We can't support this approach in any way and there is a undeniable risk for erratic behavior.


Translating this plugin
-----------------------

This Moodle plugin is shipped with an english language pack only. All translations into other languages must be managed through AMOS (https://lang.moodle.org) by what they will become part of Moodle's official language pack.

As the plugin creator, we manage the translation into german for our own local needs on AMOS. Please contribute your translation into all other languages in AMOS where they will be reviewed by the official language pack maintainers for Moodle.


Right-to-left support
---------------------

This plugin has not been tested with Moodle's support for right-to-left (RTL) languages.
If you want to use this plugin with a RTL language and it doesn't work as-is, you are free to send us a pull request on Github with modifications.


PHP7 Support
------------

Since Moodle 3.4 core, PHP7 is mandatory. We are developing and testing this plugin for PHP7 only.


Copyright
---------

Davo Smith
Synergy Learning UK
www.synergy-learning.com

on behalf of

Ulm University
Communication and Information Centre (kiz)
Alexander Bias
