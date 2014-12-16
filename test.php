<?php
  error_reporting( E_ALL );
  ini_set( 'display_errors', 1 );

  include( './class-magnetise.php' );

  $client = \Magnetise\Client::withApiKey( '12b69c82-c20e-4a00-b26e-1bedecb27560' );

  $response = $client->Send( "Console", "+445555889993", "SMS integration - done", "Testing, campaign 2" );

  $output = "Sent successfully Id: {$response->messageid}\n";

  fwrite( STDOUT, $output );
?>
