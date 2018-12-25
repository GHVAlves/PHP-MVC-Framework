<?php

namespace System\Core {

	class Rest {

    private $curl;
    private $urlRequest;

    public function __construct () {

      $this->urlRequest = 'http://localhost/';

      $this->curl = curl_init();

    }

    public function request (string $method, string $resource, Array $data = null) {

			if ($method === 'POST') {

				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);

			}
			else if ($method === 'GET') {

				if ($data != null && count($data) > 0) {

					foreach ($data as $key => $value) {

						$resource .= '/' . $key . '/' . $value;

					}

				}

			}

      curl_setopt_array($this->curl, array(
			  CURLOPT_PORT => "8080",
			  CURLOPT_URL => $this->urlRequest . $resource,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => $method,
			  CURLOPT_HTTPHEADER => array (
			    "cache-control: no-cache",
			    "content-type: multipart/form-data",
			  ),
			));

			$response = curl_exec($this->curl);
			$error = curl_error($this->curl);

			curl_close($this->curl);

			if ($error) {

			  return $error;

			}
      else {

			  return $response;

			}

    }

  }

}

?>
