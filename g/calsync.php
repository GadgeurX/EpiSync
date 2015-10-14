<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/var/www/html/g/google-api-php-client-master/src/Google');
require 'autoload.php';

define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('CREDENTIALS_PATH', '~/.credentials/calendar-php-quickstart.json');
define('CLIENT_SECRET_PATH', 'client_secret.json');
define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR)
));

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfigFile(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');

  // Load previously authorized credentials from a file.
  if (isset($_GET['auth']))
  {
	$credentialsPath = $_GET['auth'] . '.json';
	$credentialsPath = str_replace("/", "", $credentialsPath);
	if (file_exists($credentialsPath)) {
		$accessToken = file_get_contents($credentialsPath);
	}
	else
	{
		$accessToken = $client->authenticate($_GET['auth']);
		// Store the credentials to disk.
		/*if(!file_exists($credentialsPath)) {
			mkdir($credentialsPath, 0700, true);
		}*/
		file_put_contents($credentialsPath, $accessToken);
		printf("Credentials saved to %s\n</br>", $credentialsPath);
	}
	$client->setAccessToken($accessToken);
	
	// Refresh the token if it's expired.
	if ($client->isAccessTokenExpired()) {
		$client->refreshToken($client->getRefreshToken());
		file_put_contents($credentialsPath, $client->getAccessToken());
	}
  }
  else
  {
	  ?>
	  <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="http://getbootstrap.com/examples/starter-template/../../favicon.ico">

    <title>Snaze Intra Updater</title>

    <!-- Bootstrap core CSS -->
    <link href="http://getbootstrap.com/examples/starter-template/../../dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="starter-template.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="http://getbootstrap.com/examples/starter-template/../../assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Snaze</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">

      <div class="starter-template">
        <h1>Snaze</h1>
		<?php
		$authUrl = $client->createAuthUrl();
        ?>
		<p class="lead">You can find the update script on this link : <a href="intra.js" download>Script</a></p>
		<p class="lead"><?php echo 'Open the following link in your browser: <a href="' . $authUrl . '">Autorise google api</a>' ?></p>
		<p class="lead">You can execute the script with <a href="http://phantomjs.org/">PhantomJs</a>, run it with "true" arg to setup the api key.</p>
		<img src="capture_calendar.PNG" alt="capture"  style="width:80%;height:80%;">
      </div>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://getbootstrap.com/examples/starter-template/../../dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="http://getbootstrap.com/examples/starter-template/../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
	  <?php
  }
  return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
  }
  return str_replace('~', realpath($homeDirectory), $path);
}

// Get the API client and construct the service object.
$client = getClient();
if (isset($_GET['auth']))
{
	$service = new Google_Service_Calendar($client);
	echo ($_GET["year"] . '-' . $_GET["month"] . '-' . $_GET["day"] . 'T00:00:00+02:00');
	// Print the next 10 events on the user's calendar.
	$calendarId = 'primary';
	$optParams = array(
	'maxResults' => 10,
	'orderBy' => 'startTime',
	'singleEvents' => TRUE,
	'timeMin' => ($_GET["year"] . '-' . $_GET["month"] . '-' . $_GET["day"] . 'T00:01:00+02:00'),
	'timeMax' => ($_GET["year"] . '-' . $_GET["month"] . '-' . $_GET["day"] . 'T23:59:00+02:00'),
	);
	$results = $service->events->listEvents($calendarId, $optParams);
	$cont = true;
	foreach ($results->getItems() as $event) {
		$start = $event->start->dateTime;
		if (empty($start)) {
		$start = $event->start->date;
		}
		if ($event->getDescription() == $_GET["desc"])
			$cont = false;
	}
	if ($cont == true)
	{
		$event = new Google_Service_Calendar_Event(array(
			'summary' => utf8_encode($_GET["desc"]),
			'location' => $_GET["salle"],
			'description' => $_GET["desc"],
			'start' => array(
				'dateTime' => ($_GET["year"] . '-' . $_GET["month"] . '-' . $_GET["day"] . 'T' . $_GET["starth"] . ':' . $_GET["startm"] . ':00+02:00'),
			),
			'end' => array(
				'dateTime' => ($_GET["year"] . '-' . $_GET["month"] . '-' . $_GET["day"] . 'T' . $_GET["endh"] . ':' . $_GET["endm"] . ':00+02:00'),
			),
	));
	
	$calendarId = 'primary';
	$event = $service->events->insert($calendarId, $event);
	}
}
?>