<?php

namespace App\Helpers;

class Bitly {

	public function encurtar($url) {
		return self::encurtarBitly(urlencode($url));
	}

	private function encurtarBitly($url) {
		$token = env('TOKEN_BIT_LY');
		$base_url = 'https://api-ssl.bitly.com';
		$u = json_decode(file_get_contents("{$base_url}/v3/shorten?access_token={$token}&longUrl={$url}"));
		return (!empty($u->data)) ? $u->data->url : '';
	}

}
