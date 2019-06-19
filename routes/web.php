<?php

$router->group(['prefix' => 'api/v1'], function() use ($router) {
	$router->post('meeting', [
		'uses' => 'MeetingController@store'
	]);

	$router->get('meeting', [
		'uses' => 'MeetingController@index'
	]);

	$router->get('meeting/{id}', [
		'uses' => 'MeetingController@show'
	]);

	$router->patch('meeting/{id}', [
		'uses' => 'MeetingController@update'
	]);

	$router->delete('meeting/{id}', [
		'uses' => 'MeetingController@destroy'
	]);

	$router->post('meeting/registration', [
		'uses' => 'RegistrationController@store'
	]);

	$router->delete('meeting/registration/{id}', [
		'uses' => 'RegistrationController@destroy'
	]);

	$router->post('user', [
		'uses' => 'AuthController@store'
	]);

	$router->post('user/signin', [
		'uses' => 'AuthController@signin'
	]);
});