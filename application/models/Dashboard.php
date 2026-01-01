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

		echo $query;
		die;
        return $this->db->query($query)->result_array();
    }
}
