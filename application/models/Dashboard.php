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
		return $this->db->count_all_results('transaction_details');
	}

	private function get_total_assets()
	{
		return $this->db->count_all_results('asset');
	}

	private function get_total_rooms()
	{
		return $this->db->count_all_results('rooms');
	}

	public function get_assets($role_id, $room_id, $building_id)
	{
		// when user running the filter by room name
		if ($room_id == null) {
			$query = "SELECT asset.asset_name, COUNT(*) AS total FROM asset INNER JOIN rooms ON asset.room_id = rooms.id INNER JOIN floors ON rooms.floor_id = floors.id INNER JOIN buildings ON floors.building_id = buildings.id WHERE buildings.id = $building_id GROUP BY asset_name LIMIT 10";
			// if user login as administrator
			if ($role_id == '1') {
				$query = "SELECT asset.asset_name, COUNT(*) AS total FROM asset INNER JOIN rooms ON asset.room_id = rooms.id INNER JOIN floors ON rooms.floor_id = floors.id INNER JOIN buildings ON floors.building_id = buildings.id GROUP BY asset_name ORDER BY RAND() LIMIT 10";
			}
		} else {
			$query = "SELECT asset.asset_name, COUNT(*) AS total FROM asset WHERE room_id = '$room_id' GROUP BY asset_name ORDER BY RAND() LIMIT 10";
		}

		return $this->db->query($query)->result_array();
	}

	public function get_recent_transactions($limit = 5)
	{
		$this->db->select('detail_process.*, asset.asset_name, asset.serial_number');
		$this->db->from('detail_process');
		$this->db->join('asset', 'asset.id_asset = detail_process.asset_id');
		$this->db->order_by('detail_process.id_detail_process', 'DESC');
		$this->db->limit($limit);
		return $this->db->get()->result_array();
	}

	public function get_assets_by_category($limit = 5)
	{
		$this->db->select('asset_name, COUNT(*) as total');
		$this->db->group_by('asset_name');
		$this->db->order_by('total', 'DESC');
		$this->db->limit($limit);
		return $this->db->get('asset')->result_array();
	}
}
