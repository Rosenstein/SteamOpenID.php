<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use xPaw\Steam\SteamOpenID;

// The URL for the user to return to (this page), this is also validated that the return_to parameter starts with this.
// Hardcode this to the actual login url. Never derive it from $_SERVER['HTTP_HOST'] or the request path:
// it is the audience that Steam's signed assertion is checked against, so it must not be chosen by the client.
$ReturnToUrl = 'https://example.com/login.php';

$SteamOpenID = new SteamOpenID( $ReturnToUrl );

if( $SteamOpenID->ShouldValidate() )
{
	try
	{
		$CommunityID = $SteamOpenID->Validate();

		// Login succeeded, $CommunityID is the 64-bit SteamID.
		// Start a fresh session here (e.g. session_regenerate_id( true )) before storing it.
		echo 'Logged in as ' . htmlspecialchars( $CommunityID, ENT_QUOTES );
	}
	catch( InvalidArgumentException $e )
	{
		// The request parameters were manipulated; do not show the reason to the user
		error_log( 'Steam OpenID invalid argument: ' . $e->getMessage() );

		echo 'Login failed.';
	}
	catch( Exception $e )
	{
		// Login failed because it could not be validated against Steam
		echo 'Login failed: ' . htmlspecialchars( $e->getMessage(), ENT_QUOTES );
	}
}
else
{
	// As a simple url:
	echo 'Url: <a href="' . htmlspecialchars( $SteamOpenID->GetAuthUrl(), ENT_QUOTES ) . '">Sign in through Steam</a>';

	// Show login form, you can also do "get" method instead of "post" here
	echo '<br><br>Form: <form action="' . SteamOpenID::SERVER . '" method="post">';

	foreach( $SteamOpenID->GetAuthParameters() as $Key => $Value )
	{
		echo '<input type="hidden" name="' . htmlspecialchars( $Key, ENT_QUOTES ) . '" value="' . htmlspecialchars( $Value, ENT_QUOTES ) . '">';
	}

	echo '<input type="image" name="submit" src="https://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_01.png" alt="Sign in through Steam">';
	echo '</form>';
}
