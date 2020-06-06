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
 * Implementaton of the quizaccess_activateattempt plugin.
 *
 * @package   quizaccess_activateattempt
 * @author    Amrata Ramchandani <ramchandani.amrata@gmail.com>
 * @copyright 2017 Indian Institute Of Technology,Bombay,India
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined ( 'MOODLE_INTERNAL' ) || die ();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');


/**
 * A rule implementing auto-appearance of “Attempt quiz now” button at quiz open timing without requiring to refresh the page.
 *
 * @copyright  2017 Indian Institute Of Technology,Bombay,India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_activateattempt extends quiz_access_rule_base {
    /** Return an appropriately configured instance of this rule
     * @param quiz $quizobj information about the quiz in question.
     * @param int $timenow the time that should be considered as 'now'.
     * @param bool $canignoretimelimits whether the current user is exempt from
     *  time limits by the mod/quiz:ignoretimelimits capability.
     */
    public static function make(quiz $quizobj, $timenow, $canignoretimelimits) {
        // This rule is always used, even if the quiz has no open or close date.
        return new self ( $quizobj, $timenow );
    }
    /**
     * Whether or not a user should be allowed to start a new attempt at this quiz now.
     * @param int $numattempts the number of previous attempts this user has made.
     * @param object $lastattempt information about the user's last completed attempt.
     */
    public function prevent_access() {
        global $PAGE, $CFG;
        $id = optional_param ( 'id', 0, PARAM_INT );
        $cm = get_coursemodule_from_id ('quiz', $id);
        $actionlink = "$CFG->wwwroot/mod/quiz/startattempt.php";
        $sessionkey = sesskey();
        $quizwillstartinabout = get_string ('quizwillstartinabout', 'quizaccess_activateattempt');
        $quizwillstartinless = get_string ('quizwillstartinless', 'quizaccess_activateattempt');
        $attemptquiz = get_string ('attemptquiz', 'quizaccess_activateattempt');
        $days = get_string ('days', 'quizaccess_activateattempt');
        $day = get_string ('day', 'quizaccess_activateattempt');
        $hours = get_string ('hours', 'quizaccess_activateattempt');
        $hour = get_string ( 'hour', 'quizaccess_activateattempt' );
        $minutes = get_string ( 'minutes', 'quizaccess_activateattempt' );
        $minute = get_string ( 'minute', 'quizaccess_activateattempt' );
        $result = "";
        // Added 0...45 seconds random delay (to load balance quiz start, for large amount of concurrent users)
        // Set the following global on your config.php
        //$CFG->randomquizstartdelay = 45;
        $randomstartdelay = ($CFG->randomquizstartdelay) ?: 0;
        // TODO: use $this->quiz->randomstartdelay
        if ($this->timenow < $this->quiz->timeopen) {
            $diff = ($this->quiz->timeopen) - ($this->timenow);
            $diffmillisecs = $diff * 1000;
            $result = $PAGE->requires->js_call_amd('quizaccess_activateattempt/timer', 'init',
                array($actionlink, $cm->id, $sessionkey, $attemptquiz, $diffmillisecs, $days, $day, $hours, $hour, $minutes,
                    $minute, $quizwillstartinless, $quizwillstartinabout, $randomstartdelay));
        }
        return $result; // Used as a prevent message.
    }

    // TODO: Following code is working but not used (yet)
    public static function add_settings_form_fields(
        mod_quiz_mod_form $quizform, MoodleQuickForm $mform) {
        global $CFG;

        // Max delay time in seconds
        $mform->addElement('text', 'randomstartdelay',
            get_string('randomstartdelay', 'quizaccess_activateattempt'), array('autofocus' => 'true'));
        $mform->addHelpButton('randomstartdelay', 'randomstartdelay', 'quizaccess_activateattempt');
        $mform->setDefault('randomstartdelay', ($CFG->randomquizstartdelay) ?: 0);
        $mform->setAdvanced('randomstartdelay');
    }

    public static function save_settings($quiz) {
        global $DB, $CFG;

        if (empty($quiz->randomstartdelay)) {
            $DB->delete_records('quizaccess_activateattempt', array('quizid' => $quiz->id));
        } else {
            $record = $DB->get_record('quizaccess_activateattempt', array('quizid' => $quiz->id));
            if (!$record) {
                $record = new stdClass();
                $record->quizid = $quiz->id;
                $record->randomstartdelay = $quiz->randomstartdelay; //($CFG->randomquizstartdelay) ?: 0;
                $DB->insert_record('quizaccess_activateattempt', $record);
            } else {
                $record->randomstartdelay = $quiz->randomstartdelay;
                $DB->update_record('quizaccess_activateattempt', $record);
            }
        }
    }

    public static function delete_settings($quiz) {
        global $DB;
        $DB->delete_records('quizaccess_activateattempt', array('quizid' => $quiz->id));
    }

    public static function get_settings_sql($quizid) {
        return array(
            'activateattempt.randomstartdelay AS randomstartdelay',
            'LEFT JOIN {quizaccess_activateattempt} activateattempt ON activateattempt.quizid = quiz.id',
            array());
    }


}

