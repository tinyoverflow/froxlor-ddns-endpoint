<?php

/** Prevent direct access to this file */
if (defined('APPLICATION') === false) {
	die('Direct access not permitted!');
}

/** Require dependencies */
require_once 'FroxlorAPI.class.php';

class DDNSProvider
{
	/** @var Config */
	private $config;

	/** @var FroxlorAPI */
	private $api;

	/** @var string */
	private $domainName;

	/** @var int */
	private $domainTtl;

	/**
	 * Creates a new instance of this class.
	 * @param Config $config Configuration to use.
	 * @return void
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->api = new FroxlorAPI(
			$this->config->get('froxlor.endpoint'),
			$this->config->get('froxlor.api_key'),
			$this->config->get('froxlor.api_secret')
		);
	}

	public function use(string $user, string $token, string $domain): bool
	{
		$users = array_keys($this->config->get('users'));

		/** Check if the user exists. */
		if (in_array($user, $users) === false) {
			return false;
		}

		/** Read user properties */
		$userToken = $this->config->get("users.${user}.token");
		$userDomains = $this->config->get("users.${user}.domains");

		/** Verify token */
		if (strcmp($userToken, $token) !== 0) {
			return false;
		}

		/** Verify domain */
		if (in_array($domain, array_keys($userDomains)) === false) {
			return false;
		}

		$this->domainName = $domain;
		$this->domainTtl = $userDomains[$domain];
		return true;
	}

	public function updateIp(string $host, string $ip)
	{
		if (isset($this->domainName) === false) {
			die('ERROR: Make sure to select a zone before trying to update the IP address.');
		}

		/** Get current entry IDs and delete them */
		$entryIds = $this->getCurrentEntry($host);
		foreach ($entryIds as $id) {
			$this->api->request('DomainZones.delete', ['entry_id' => $id, 'domainname' => $this->domainName]);
		}

		/** Create the new record with the current ID */
		$this->api->request('DomainZones.add', [
			'domainname' => $this->domainName,
			'record' => $host,
			'type' => 'A',
			'content' => $ip,
			'ttl' => $this->domainTtl
		]);
	}

	/**
	 * Finds all A records for the current host and returns their IDs.
	 * @param string $host Host to check for.
	 * @return array Array of all IDs.
	 */
	private function getCurrentEntry(string $host): array
	{
		$this->api->request('DomainZones.listing', ['domainname' => $this->domainName]);

		/** Check if an error occoured */
		if (empty($this->api->getLastError()) === false) {
			return [];
		}

		$response = $this->api->getLastResponse();

		/** If no entry exists, simply skip as there is nothing to query for */
		$count = $response['count'];
		if ($count === 0) {
			return [];
		}

		$entries = $response['list'];
		$ids = [];
		foreach ($entries as $entry) {
			if (strcmp($host, $entry['record']) !== 0 || strcmp('A', $entry['type']) !== 0) {
				continue;
			}

			$ids[] = $entry['id'];
		}

		return $ids;
	}
}
