<?php

class Dashboard extends CI_Model
{
	var $table = 'users';

	public function summary_data()
	{
		$data = [
			"total_users" => $this->get_total_users(),
			"total_transactions" => $this->get_total_transactions(),
			"total_assets" => $this->get_total_assets(),
			"total_rooms" => $this->get_total_rooms()
		];

		return $data;
	}
	private function get_total_users()
	{
		return $this->db->count_all_results('user');
	}

	private function get_total_transactions()
	{
		return $this->db->count_all_results('transactions');
	}

	private function get_total_assets()
	{
		return $this->db->count_all_results('asset');
	}

	private function get_total_rooms()
	{
		return $this->db->count_all_results('rooms');
	}
}
