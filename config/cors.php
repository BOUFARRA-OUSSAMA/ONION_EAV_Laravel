<?php

return [
	'paths' => ['api/*', 'sanctum/csrf-cookie'],
	'allowed_methods' => ['*'],
	'allowed_origins' => ['*'], // In production, restrict this to your frontend domain
	'allowed_origins_patterns' => [],
	'allowed_headers' => ['*'],
	'exposed_headers' => [],
	'max_age' => 0,
	'supports_credentials' => true, // Important for Sanctum to work with frontend
];