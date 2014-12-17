Magnetise client for PHP
===========================

Getting started
===============

Our RESTful SMS message API can enable any application to send SMS messages to
recipients.

Using an API client with the SMS message API
--------------------------------------------

The simplest way for an application to use the SMS API, is for it to use one of
the pre‑built API Clients. If there isn't a suitable API client for the
application then it can call the SMS API directly.

Integrating an API client
-------------------------
First make sure that the applications development environment is setup with a
suitable package manager.

Once the environment is set up and ready, install the API client

In your terminal run

```php
$ php composer.phar install magnetise/magnetise-php
```

Once the API client has been installed the application can begin sending SMS messages through the SMS message API with code similar to the following

```php
require('./path/to/magnetise-client.php');

$magnetise = \Magnetise\Client::withApiKey( 'your-api-key' );

$res = $client->Send( "Console", "+445555889993", "SMS integration - done", "Testing, campaign 2" );

fwrite( STDOUT, "Sent successfully Id: {$res->messageid}\n" );
```

Find out more about using the API clients.

Responses from the SMS message API
----------------------------------

When sending an SMS message the SMS API will return a 202 indicating we’ve
received the request and are processing it. It will also return the following
information

```
// HTTP POST from API

{
  "to": "4405555889993",                                  // number to which the SMS message was sent
  "from": "A Test",                                       // the sent from number or label
  "message": "SMS integraion - done!",                    // the text content that was received
  "received": "2014-06-18T15:04:00.3799301Z",             // date and time the SMS message was received
  "live": true,                                           // indicates if live API Key was used
  "messageid": "813bbeda-d8ba-4085-a921-2b16b280284d",    // unique id of the message
  "tags": "Testing, campaign 2"                           // the tags given to the message
}
```

SMS messages are sent asynchronously so delays by mobile operators do not impact
the running of the application. It is possible to configure a callback for
delivery notifications so that the application can track when SMS messages
are delivered. We provide the messageid from the initial response within all
callbacks to the application so that it can track each SMS message individually.
