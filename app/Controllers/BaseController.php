<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
	/**
	 * @var CLIRequest|IncomingRequest
	 */
	protected $request;

	/**
	 * @var list<string>
	 */
	protected $helpers = [];

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		// Load unread notification count for logged-in user
		try {
			if (session()->has('id')) {
				$notificationModel = new \App\Models\NotificationModel();
				$unreadCount = $notificationModel->getUnreadCount(session('id'));
				$this->data['unreadCount'] = $unreadCount;
			} else {
				$this->data['unreadCount'] = 0;
			}
		} catch (\Exception $e) {
			$this->data['unreadCount'] = 0;
		}
	}
}
