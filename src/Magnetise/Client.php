<?php namespace Magnetise;

date_default_timezone_set( 'UTC' );

class Client {

  private $hostname = 'magneti.se';
  private $secure = true;
  private $apikey;
  private $from;
  private $to;
  private $message;
  private $tags;

  public static function create() {
    return new self();
  }

  public static function withApiKey( $apikey ) {
    $instance = new self();
    return $instance->setApiKey( $apikey );
  }

  public function setApiKey( $apikey ) {
    if ( $apikey === "" ) {
      throw new MagnetiseApiException( 'a valid "apikey" is required' );
    }

    $this->apikey = $apikey;

    return $this;
  }

  public function send( $from, $to, $message, $tags ) {
    if ( empty( $from ) ) {
      throw( new Error( '"from" is required and must not be an empty string' ) );
    }
    $this->from = $from;
    if ( empty( $to ) ) {
      throw( new Error( '"to" is required and must not be an empty string' ) );
    }
    $this->to = $to;
    if ( !is_string( $message ) || empty( $message ) ) {
      throw( new Error( '"message" is required and must be a valid string' ) );
    }
    $this->message = $message;

    $this->tags = $tags;

    return $this->makeRequest();
  }

  private function makeRequest() {
    return $this->apikey ? $this->makePostRequest() : $this->makeLocalRequest();
  }

  private function makeLocalRequest() {
    $log = "Sending message in local mode to \"{$this->to}\" with message \"{$this->message}\"\n";

    $obj = new \stdClass();
    $obj->to = $this->to;
    $obj->from = $this->from;
    $obj->message = $this->message;
    $obj->received = new \DateTime('NOW');
    $obj->live = false;
    $obj->messageid = \time();
    $obj->tags = $this->tags;
    $obj->cost = 0;

    if (php_sapi_name() == "cli") {
      fwrite( STDOUT, $log );
    } else {
      syslog ( LOG_INFO, $log );
    }

    return $obj;
  }

  private function makePostRequest() {
    $uri = ( $this->secure ? 'https' : 'http' ) . "://{$this->hostname}/api/messages";

    $data = array(
      'message' => $this->message,
      'from' => $this->from,
      'to' => $this->to,
      'apikey' => $this->apikey
    );

    if ( !empty( $this->tags ) ) {
      array_merge( $data, array( 'tags' => $this->tags ) );
    }

    $postString = http_build_query( $data, '', '&' );

    try {
      $ch = curl_init( $uri );
      curl_setopt( $ch, CURLOPT_POST, 1 );
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $postString );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

      $response = curl_exec( $ch );
      curl_close( $ch );

      return json_decode( $response );
    } catch ( \HttpException $ex ) {
      throw $ex;
    }
  }

}

?>
