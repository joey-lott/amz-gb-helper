<?php

return [
	'store' => [
		'realpeoplegoods' => [
			'merchantId' => env("AMAZON_MERCHANT_ID"),
			'marketplaceId' => env("AMAZON_MARKETPLACE_ID"),
			'keyId' => env("AMAZON_MWS_KEY"),
			'secretKey' => env("AMAZON_MWS_SECRET"),
			'amazonServiceUrl' => env("AMAZON_MWS_SERVICE_URL"),
		]
	],

	// Default service URL
	'AMAZON_SERVICE_URL' => 'https://mws.amazonservices.com/',

	'muteLog' => false
];
