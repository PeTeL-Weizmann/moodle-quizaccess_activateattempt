define(['jquery'], function($) {
    return {
        /**
         * Init function.
         *
         */

        init: function(actionlink, cmid, sessionkey, attemptquiz, diffmillisecs, Strdays, Strday, Strhours, Strhour, Strminutes,
            Strminute, quizwillstartinless, quizwillstartinabout) {
            $('.continuebutton').prepend(
                $('</br>'),
                $('<form/>', {
                    'method': 'post',
                    'action': actionlink
                }).append(
                    $('<input>', {
                        'type': 'hidden',
                        'name': 'cmid',
                        'value': cmid
                    }),
                    $('<input>', {
                        'type': 'hidden',
                        'name': 'sesskey',
                        'value': sessionkey
                    }),
                    $('<input>', {
                        'type': 'submit',
                        'class': 'btn btn-secondary',
                        'id': 'startAttemptButton',
                        'value': attemptquiz
                    }),
                    $('<p>', {
                        'id': 'timer'
                    })
                ),
                $('</br>')
            );

            $('#startAttemptButton').hide();
            var quizOpenTime = new Date().getTime() + diffmillisecs;
            var interval = setInterval(function() {
                var currentTime = new Date().getTime();
                var countDownTime = quizOpenTime - currentTime;

                var days = Math.floor(countDownTime / (1000 * 60 * 60 * 24));
                var hours = Math.floor((countDownTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

                var minutes = Math.floor((countDownTime % (1000 * 60 * 60)) / (1000 * 60));

                var seconds = Math.floor((countDownTime % (1000 * 60)) / 1000);

                var daysLeft, hrsLeft, minsLeft;

                if (days > 0) {
                    if (days > 1) {
                        var daysLeft = '<b>' + days + '</b>' + ' ' + Strdays + ', ';
                    } else {
                        daysLeft = '<b>' + days + '</b>' + ' ' + Strday + ', ';
                    }
                } else {
                    daysLeft = '';
                }
                if (hours > 0) {
                    if (hours > 1) {
                        hrsLeft = '<b>' + hours + '</b>' + ' ' + Strhours + ', ';
                    } else {
                        hrsLeft = '<b>' + hours + '</b>' + ' ' + Strhour + ', ';
                    }
                } else {
                    hrsLeft = '';
                }
                if (minutes > 1) {
                    minsLeft = '<b>' + minutes + '</b>' + ' ' + Strminutes;
                } else {
                    minsLeft = '<b>' + minutes + '</b>' + ' ' + Strminute;
                }
                if (days == 0 && hours == 0 && minutes == 0) {
                    document.getElementById('timer').innerHTML = quizwillstartinless;
                } else {
                    document.getElementById('timer').innerHTML = quizwillstartinabout + ' ' + daysLeft + hrsLeft + minsLeft;
                }

                if (countDownTime < 0) {
                    clearInterval(interval);
                    $('#timer').hide();
                    $('#startAttemptButton').show();
                }
            }, 1000);
        }
    }
});