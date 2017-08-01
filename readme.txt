Moodle Plugin-Auto Activate Quiz Attempt Button

Background and Need ->

1) In a quiz where the start time is crucial students tend to refresh the page every now and 
then just to make sure if the attempt is available.
2) This unnecessarily increases the server load, if there are too many such ‘unwanted’ requests.

Solution ->

The “Activate Attempt” plugin makes the “Attempt quiz now” button auto-appear at quiz open timing 
without requiring to refresh the page. This is done by a client side countdown timer (javascript) which 
is initiated when the page is rendered in the browser. The plugin is implemented as an access-rules plugin 
overriding the default page renderer.The page, by default, displays the time remaining (in minutes) to 
start the quiz, and counts down to the last minute. We have deliberately avoided to display the remaining 
time in seconds as this too can increase the server load since several students will tend to send a 
server request the next second the button appears. Whereas, in when displayed in minutes, the requests are
likely to be spread over several seconds to a minute.

Installation ->

1) Download it into /mod/quiz/accessrule
2) Log in into Moodle
3) A notification will appear stating “Plugins requiring attention”.
4) Complete the installation by clicking on “Upgrade Moodle database now”,click on continue after the success notification appears on the page.

Usage ->

If you enable quiz open time in quiz settings, quiz students will no longer have to manually refresh the 
page in order to get “Attempt Quiz Now” button at the quiz open timing.
