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
 * @package    quizaccess_activateattempt
 * @author     Amrata Ramchandani <ramchandani.amrata@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');

class quizaccess_activateattempt extends quiz_access_rule_base {

	
    public static function make(quiz $quizobj, $timenow, $canignoretimelimits) {
        // This rule is always used, even if the quiz has no open or close date.
        return new self($quizobj, $timenow);
    }
 
    public function prevent_new_attempt($numprevattempts, $lastattempt) {	
    	
    	global $PAGE,$CFG;
    	$PAGE->requires->jquery();
    	
    	$id = optional_param('id', 0, PARAM_INT);
    	$cm = get_coursemodule_from_id('quiz', $id);    	
    	$sessionKey = $_SESSION[USER]->sesskey;
    	$sessionKeyJS = json_encode($sessionKey);
    	$quizwillstartin = get_string('quizwillstartin', 'quizaccess_activateattempt');
    	$attemptquiz = get_string('attemptquiz', 'quizaccess_activateattempt');
    	$days=get_string('days', 'quizaccess_activateattempt');
    	$day=get_string('day', 'quizaccess_activateattempt');
    	$hours=get_string('hours', 'quizaccess_activateattempt');
    	$hour=get_string('hour', 'quizaccess_activateattempt');
    	$minutes=get_string('minutes', 'quizaccess_activateattempt');
    	$minute=get_string('minute', 'quizaccess_activateattempt');
    	
    	if($this->timenow < $this->quiz->timeopen)
    	{     		
    		$diff=($this->quiz->timeopen) - ($this->timenow);    		
    		$diffMilliSecs= $diff*1000;    		    		
    		$result.="<script>
					 function countDownTimer(diffMilliSecs) {
    					
					   $(document).ready(function() {
        					
						  $('.continuebutton').prepend(
            					$('<form/>', {
                					'method': 'post',
                					'action': '$CFG->wwwroot/mod/quiz/startattempt.php'
            					}).append(
                					$('<input>', {
                    					'type': 'hidden',
                   					    'name': 'cmid',
                    					'value': $cm->id
                					}),
                					$('<input>', {
                   					    'type': 'hidden',
                    					'name': 'sesskey',
                   					    'value': $sessionKeyJS
               					    }),
									$('<input>', {
                   					    'type': 'submit',
                    					'class': 'btn btn-secondary',
										'id'   : 'startAttemptButton',
                   					    'value': '$attemptquiz'
               					    }),
									$('<p>', {
										'id':'timer'
									}) 
            					),
           						$('</br>')
       					 );

						$('#startAttemptButton').hide();
						
						var quizOpenTime = new Date().getTime() + diffMilliSecs;
     					 
						var interval = setInterval(function() {
          				 	
           				 	var currentTime = new Date().getTime();
           				 	var countDownTime = quizOpenTime - currentTime;

            				var days = Math.floor(countDownTime / (1000 * 60 * 60 * 24));
            				var hours = Math.floor((countDownTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            				var minutes = Math.floor((countDownTime % (1000 * 60 * 60)) / (1000 * 60)) + 1;
           				    var seconds = Math.floor((countDownTime % (1000 * 60)) / 1000);

            				var daysLeft, hrsLeft, minsLeft;

          				    if (days > 0) {
               					 if (days > 1)
                   					 var daysLeft = '<b>' + days + '</b>' + ' $days, ';
                			     else
                    				 daysLeft = '<b>' + days + '</b>' + ' $day, ';
            				} else
                				daysLeft = '';

            				if (hours > 0) {
                				if (hours > 1)
                    				hrsLeft = '<b>' + hours + '</b>' + ' $hours, ';
                				else
                    				hrsLeft = '<b>' + hours + '</b>' + ' $hour, ';
           				    } else
                				hrsLeft = '';

            				if (minutes > 1)
                				minsLeft = '<b>' + minutes + '</b>' + ' $minutes ';
            				else
                				minsLeft = '<b>' + minutes + '</b>' + ' $minute ';

            				document.getElementById('timer').innerHTML = '$quizwillstartin'+' '+daysLeft+hrsLeft+minsLeft;

            				if (countDownTime < 0) {
                				clearInterval(interval);
                				$('#timer').hide();
               				    $('#startAttemptButton').show();
            				}
        
					}, 1000);
    				});
					}
				</script>";
    		$result.="<script type='text/javascript'>countDownTimer($diffMilliSecs);</script>";    		
    	}
    	return $result;  //used as a prevent message
    }    
}


