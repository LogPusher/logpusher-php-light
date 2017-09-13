
<?php 

	include_once "pushSender.lib.class.php";
	
	$sender = new pushSender();
	
// all param not null.
	$new_params = array(
		 "LogMessage"=> "Eravse new", // Message for push notification. 
		  "Source"=> "demo LogPusher", // If you use same app multiple integration please split your source app for exp. Website1, WebSite2 etc 
		  "Category"=> "sample string 5", // Category is dynamic. Exception, Service , Order etc.
		  "LogType"=> "sample string 6", // LogType is Dynamic. What you want to see Error , success etc.
		  "LogTime"=> "sample string 7", // LogTime is hour:minute  for exp : 10:35
		  "CreatedDate"=> "2016-10-24T21:26:46.4000402+03:00", // DateTime must be format for en-EN Culture
		  "EventId"=> "sample string 9" // your event Id. Create new Guid or Any string.
	);
	
	$new_credientals = array(
		'email' => 'hello@logpusher.com',
		'pwd' => 'somepwd'
	);
	
	echo "Datetime :".$sender->get_datetime(). "<br>";
	
	echo "Default Credientals and Parameters <pre>";
	print_r($sender->make_request($new_params));
	echo "</pre>";
	?php>
