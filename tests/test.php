<?php declare(strict_types=1);

use Outsights\Outpost\Outpost;
use Outsights\Outpost\OutpostFile;
use Outsights\Outpost\OutpostCookie;
use Outsights\Outpost\OutpostRequest;
use Outsights\Outpost\OutpostResponse;

class OutpostTest
{
  public function sendFile(string $url, array $filePayload)
  {

  }

  public function testRequest(string $method, string $url, array $headers, $body)
  {
        # request
        $request = Outpost::createRequest($method, $url);
    
        foreach ($body as $file) {
          if ($file instanceof OutpostFile) {
            $request->withFile($file->inputName(), $file);
          }
        }

        # response
        $response = Outpost::sendRequest($request);
        return $response;
  }

  public function seeResponse(OutpostResponse $response)
  {
    $headers = $response->getHeaders();
    $responseHeaders = array();
  
    foreach($headers as $name => $value) {
      if (is_array($value)) {
        $headerLine = $name.": ".implode(',', $value);
      } else {
        $headerLine = $name.": ".$value;
      }
  
      array_push($responseHeaders, $headerLine);
    }

    echo "<pre>";
    echo "<h3>Headers:</h3>";

    foreach ($responseHeaders as $header) {
      echo "<br>".$header;
    }
    
    echo "<h3>Body:</h3>";
    var_dump(htmlentities($response->getBody()));


    echo "<h3>Parsed Body:</h3>";
    print_r($response->getParsedBody());
  }
}
