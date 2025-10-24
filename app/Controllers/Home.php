<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Home extends BaseController
{
	public function index()
	{
		$unreadCount = session()->get('isLoggedIn') ? (new NotificationModel())->getUnreadCount(session()->get('id')) : 0;
		return view('index', ['unreadCount' => $unreadCount]);
	}

	public function about()
	{
		$unreadCount = session()->get('isLoggedIn') ? (new NotificationModel())->getUnreadCount(session()->get('id')) : 0;
		return view('about', ['unreadCount' => $unreadCount]);
	}

	public function contact()
	{
		$unreadCount = session()->get('isLoggedIn') ? (new NotificationModel())->getUnreadCount(session()->get('id')) : 0;
		return view('contact', ['unreadCount' => $unreadCount]);
	}
}
