<?php

function xmldb_quizaccess_activateattempt_upgrade($oldversion)
{
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2017051502) {
        if (!$dbman->table_exists('quizaccess_activateattempt')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__ . '/install.xml', 'quizaccess_activateattempt');
        }

        upgrade_plugin_savepoint(true, '2017051502', 'quizaccess','activateattempt');
    }
}