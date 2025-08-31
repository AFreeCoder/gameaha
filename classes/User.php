<?php

class User
{
	public $id = null;
	public $username = null;
	public $password = null;
	public $email = null;
	public $birth_date = null;
	public $join_date = null;
	public $gender = null;
	public $role = null;
	public $data = null;
	public $avatar = 0;
	public $bio = '';
	public $xp = 0;
	public $rank = '-';
	public $level = 1;
	//
	private $is_subscriber = null;
    private $subscription_end_date = null;
    private $subscription_type = null;

	public function __construct($data = array())
	{
		if (isset($data['id'])) $this->id = (int)$data['id'];
		if (isset($data['username'])) $this->username = $data['username'];
		if (isset($data['password'])) $this->password = $data['password'];
		if (isset($data['email'])) $this->email = $data['email'];
		if (isset($data['birth_date'])) $this->birth_date = $data['birth_date'];
		if (isset($data['join_date'])) $this->join_date = $data['join_date'];
		if (isset($data['gender'])) $this->gender = $data['gender'];
		if (isset($data['data'])) $this->data = json_decode($data['data'], true);
		if (isset($data['avatar'])) $this->avatar = $data['avatar'];
		if (isset($data['role'])) $this->role = $data['role'];
		if (isset($data['bio'])) $this->bio = $data['bio'];
		if (isset($data['xp'])) $this->xp = $data['xp'];
		if (is_null($this->xp)) {
			$this->xp = 0;
		}
		if (is_null($this->birth_date)) {
			$this->birth_date = date('Y-m-d');
		}

		if (!$this->data) {
			$this->data = array();
			$this->data['likes'] = [];
		}

		if (file_exists(ABSPATH . 'includes/rank.json')) {
			$rank = json_decode(file_get_contents(ABSPATH . 'includes/rank.json'), true);
			if ($rank) {
				$index = 0;
				foreach ($rank as $name => $value) {
					if ($this->xp >= $value) {
						$index++;
						$this->level = $index;
						$this->rank = $name;
					}
				}
			}
		}

		// Load subscription data if provided directly
        if (isset($data['subscription_type'])) {
            $this->subscription_type = $data['subscription_type'];
        }
        if (isset($data['subscription_end_date'])) {
            $this->subscription_end_date = $data['subscription_end_date'];
            if ($this->subscription_end_date && strtotime($this->subscription_end_date) > time()) {
                $this->is_subscriber = true;
            } else {
                $this->is_subscriber = false;
            }
        }
	}

	public function storeFormValues($params)
	{
		$this->__construct($params);
		if (is_null($this->join_date)) {
			$this->join_date = date('Y-m-d');
		}
	}

	public static function getById($id)
	{
		$conn = open_connection();
		$sql = "SELECT * FROM users WHERE id = :id";
		$st = $conn->prepare($sql);
		$st->bindValue(":id", $id, PDO::PARAM_INT);
		$st->execute();
		$row = $st->fetch();
		if ($row) return new User($row); //$row
	}

	public static function getByUsername($username)
	{
		$conn = open_connection();
		$sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
		$st = $conn->prepare($sql);
		$st->bindValue(":username", $username, PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetch();
		if ($row) return new User($row); //$row
	}

	public static function getList(int $amount = 30, $sort = 'desc', int $offset = 0)
	{
		$conn = open_connection();
		$sql = "SELECT * FROM users
			ORDER BY id $sort LIMIT $amount OFFSET $offset";
		$st = $conn->prepare($sql);
		$st->execute();
		$list = array();

		while ($row = $st->fetch()) {
			$user = new User($row);
			$list[] = $user;
		}
		$totalRows = $conn->query('SELECT count(*) FROM users')->fetchColumn();
		$totalPages = 0;
		if (count($list)) {
			$totalPages = ceil($totalRows / $amount);
		}
		return (array(
			"results" => $list,
			"totalRows" => $totalRows,
			"totalPages" => $totalPages
		));
	}

	public static function getListByRole($role, $sort = 'desc', $amount = null, $offset = 0)
	{
		$conn = open_connection();
		$sql = "SELECT * FROM users WHERE role = :role ORDER BY id $sort";

		if ($amount) {
			$sql .= " LIMIT $amount OFFSET $offset";
		}

		$st = $conn->prepare($sql);
		$st->bindValue(":role", $role, PDO::PARAM_STR);
		$st->execute();

		$list = array();
		while ($row = $st->fetch()) {
			$user = new User($row);
			$list[] = $user;
		}

		// Get total count for pagination
		$total = $conn->query("SELECT COUNT(*) FROM users WHERE role = '$role'")->fetchColumn();
		$totalPages = $amount ? ceil($total / $amount) : 0;

		return array(
			"results" => $list,
			"totalRows" => $total,
			"totalPages" => $totalPages
		);
	}

	public static function getByEmail($email)
	{
		$conn = open_connection();
		$sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
		$st = $conn->prepare($sql);
		$st->bindValue(":email", $email, PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetch();
		if ($row) return new User($row); //$row
	}

	public function hasAccess($target_page, $target_slug = null)
	{
		// If user is admin, always grant access
		if ($this->role === 'admin') {
			return true;
		}

		// If not crew, deny access
		if ($this->role !== 'crew') {
			return false;
		}

		// First check if page exists in allowed permissions list
		if (!isset(AVAILABLE_PERMISSIONS[$target_page])) {
			if($target_page !== 'plugin'){
				return false;
			}
		}

		if(!$target_page){
			return false;
		}

		$conn = open_connection();

		if (is_null($target_slug)) {
			$sql = "SELECT id FROM user_permissions WHERE user_id = :user_id AND page = :page";
			$st = $conn->prepare($sql);
			$st->bindValue(":user_id", $this->id, PDO::PARAM_INT);
			$st->bindValue(":page", $target_page, PDO::PARAM_STR);
		} else {
			$sql = "SELECT id FROM user_permissions WHERE user_id = :user_id AND page = :page AND slug = :slug";
			$st = $conn->prepare($sql);
			$st->bindValue(":user_id", $this->id, PDO::PARAM_INT);
			$st->bindValue(":page", $target_page, PDO::PARAM_STR);
			$st->bindValue(":slug", $target_slug, PDO::PARAM_STR);
		}

		$st->execute();
		return $st->fetch() ? true : false;
	}

	public function grantAccess($page, $slug = null)
	{
		if ($this->role !== 'crew') {
			return false;
		}

		$conn = open_connection();
		$sql = "INSERT INTO user_permissions (user_id, page, slug) VALUES (:user_id, :page, :slug)";

		$st = $conn->prepare($sql);
		$st->bindValue(":user_id", $this->id, PDO::PARAM_INT);
		$st->bindValue(":page", $page, PDO::PARAM_STR);
		$st->bindValue(":slug", $slug, PDO::PARAM_STR);

		return $st->execute();
	}

	public function revokeAccess($page, $slug = null)
	{
		if ($this->role !== 'crew') {
			return false;
		}

		$conn = open_connection();

		if (is_null($slug)) {
			$sql = "DELETE FROM user_permissions WHERE user_id = :user_id AND page = :page AND slug IS NULL";
			$st = $conn->prepare($sql);
			$st->bindValue(":user_id", $this->id, PDO::PARAM_INT);
			$st->bindValue(":page", $page, PDO::PARAM_STR);
		} else {
			$sql = "DELETE FROM user_permissions WHERE user_id = :user_id AND page = :page AND slug = :slug";
			$st = $conn->prepare($sql);
			$st->bindValue(":user_id", $this->id, PDO::PARAM_INT);
			$st->bindValue(":page", $page, PDO::PARAM_STR);
			$st->bindValue(":slug", $slug, PDO::PARAM_STR);
		}

		return $st->execute();
	}

	public function getUserPermissions()
	{
		if ($this->role !== 'crew') {
			return [];
		}

		$conn = open_connection();
		$sql = "SELECT page, slug FROM user_permissions WHERE user_id = :user_id ORDER BY page, slug";

		$st = $conn->prepare($sql);
		$st->bindValue(":user_id", $this->id, PDO::PARAM_INT);
		$st->execute();

		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function array_id_exist($id)
	{
		if (!is_null($this->id)) {
			if (!is_null($this->data) && isset($this->data['likes'])) {
				$index = 1;
				foreach ($this->data['likes'] as $val) {
					if ($val == $id) {
						return $index;
					}
					$index++;
				}
				return false;
			}
		}
	}

	public function favoriteGames()
	{
		if (!is_null($this->id)) {
			$conn = open_connection();
			$sql = "SELECT * FROM favorites WHERE user_id = :user_id ORDER BY id DESC";
			$st = $conn->prepare($sql);
			$st->bindValue(":user_id", $this->id, PDO::PARAM_INT);
			$st->execute();
			$row = $st->fetchAll();
			return $row;
		}
		return null;
	}

	public static function getTotalUsers()
	{
		// Get total users
		$conn = open_connection();
		$sql = "SELECT COUNT(*) FROM users";

		$st = $conn->prepare($sql);
		$st->execute();
		return $st->fetchColumn();
	}

	// Check if user is subscriber (with caching)
    public function isSubscriber()
    {
        if ($this->is_subscriber === null) {
            $conn = open_connection();
            $sql = "SELECT subscription_type, end_date 
                    FROM user_subscriptions 
                    WHERE user_id = :user_id 
                    AND status = 'active' 
                    AND end_date > NOW() 
                    LIMIT 1";
            
            $st = $conn->prepare($sql);
            $st->bindValue(":user_id", $this->id, PDO::PARAM_INT);
            $st->execute();
            
            $subscription = $st->fetch(PDO::FETCH_ASSOC);
            
            if ($subscription) {
                $this->is_subscriber = true;
                $this->subscription_type = $subscription['subscription_type'];
                $this->subscription_end_date = $subscription['end_date'];
            } else {
                $this->is_subscriber = false;
                $this->subscription_type = null;
                $this->subscription_end_date = null;
            }
        }
        
        return $this->is_subscriber;
    }

    // Get subscription details
    public function getSubscriptionDetails()
    {
        if ($this->isSubscriber()) {
            return [
                'type' => $this->subscription_type,
                'end_date' => $this->subscription_end_date,
                'days_remaining' => ceil((strtotime($this->subscription_end_date) - time()) / (60 * 60 * 24))
            ];
        }
        return null;
    }

    // Subscribe user to a plan
    public function subscribe($plan_type, $duration = 1, $duration_unit = 'month')
    {
        if (is_null($this->id)) {
            trigger_error("User::subscribe(): Attempt to subscribe a User object that does not have its ID property set.", E_USER_ERROR);
        }

        $conn = open_connection();
        $start_date = date('Y-m-d H:i:s');
        $end_date = date('Y-m-d H:i:s', strtotime("+{$duration} {$duration_unit}s"));

        try {
            // First cancel any active subscriptions
            $this->cancelSubscription();

            $sql = "INSERT INTO user_subscriptions (user_id, subscription_type, status, start_date, end_date) 
                    VALUES (:user_id, :type, 'active', :start_date, :end_date)";
            
            $st = $conn->prepare($sql);
            $st->bindValue(":user_id", $this->id, PDO::PARAM_INT);
            $st->bindValue(":type", $plan_type, PDO::PARAM_STR);
            $st->bindValue(":start_date", $start_date, PDO::PARAM_STR);
            $st->bindValue(":end_date", $end_date, PDO::PARAM_STR);
            $st->execute();

            $this->resetSubscriptionCache();
            return true;
        } catch (PDOException $e) {
            trigger_error("Failed to create subscription: " . $e->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    // Cancel subscription
    public function cancelSubscription()
    {
        if (is_null($this->id)) {
            trigger_error("User::cancelSubscription(): Attempt to cancel subscription of a User object that does not have its ID property set.", E_USER_ERROR);
        }

        $conn = open_connection();
        
        try {
            $sql = "UPDATE user_subscriptions 
                    SET status = 'cancelled', 
                        updated_at = NOW() 
                    WHERE user_id = :user_id 
                    AND status = 'active'";
            
            $st = $conn->prepare($sql);
            $st->bindValue(":user_id", $this->id, PDO::PARAM_INT);
            $st->execute();

            $this->resetSubscriptionCache();
            return true;
        } catch (PDOException $e) {
            trigger_error("Failed to cancel subscription: " . $e->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    // Renew subscription
    public function renewSubscription($duration_months = 1)
    {
        if (!$this->isSubscriber()) {
            return false;
        }

        $conn = open_connection();
        $new_end_date = date('Y-m-d H:i:s', strtotime("+{$duration_months} months", strtotime($this->subscription_end_date)));
        
        try {
            $sql = "UPDATE user_subscriptions 
                    SET end_date = :end_date,
                        updated_at = NOW()
                    WHERE user_id = :user_id 
                    AND status = 'active'";
            
            $st = $conn->prepare($sql);
            $st->bindValue(":user_id", $this->id, PDO::PARAM_INT);
            $st->bindValue(":end_date", $new_end_date, PDO::PARAM_STR);
            $st->execute();

            $this->resetSubscriptionCache();
            return true;
        } catch (PDOException $e) {
            trigger_error("Failed to renew subscription: " . $e->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    // Get subscription history
    public function getSubscriptionHistory()
    {
        if (is_null($this->id)) {
            return [];
        }

        $conn = open_connection();
        $sql = "SELECT * FROM user_subscriptions 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC";
        
        $st = $conn->prepare($sql);
        $st->bindValue(":user_id", $this->id, PDO::PARAM_INT);
        $st->execute();
        
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    // Reset subscription cache
    private function resetSubscriptionCache()
    {
        $this->is_subscriber = null;
        $this->subscription_type = null;
        $this->subscription_end_date = null;
    }

    // Add static method to get subscribers list
    public static function getSubscribersList($limit = 30, $offset = 0)
    {
        $conn = open_connection();
        $sql = "SELECT DISTINCT u.* 
                FROM users u 
                JOIN user_subscriptions s ON u.id = s.user_id 
                WHERE s.status = 'active' 
                AND s.end_date > NOW() 
                ORDER BY s.created_at DESC 
                LIMIT :limit OFFSET :offset";

        $st = $conn->prepare($sql);
        $st->bindValue(":limit", $limit, PDO::PARAM_INT);
        $st->bindValue(":offset", $offset, PDO::PARAM_INT);
        $st->execute();

        $subscribers = [];
        while ($row = $st->fetch()) {
            $subscribers[] = new User($row);
        }

        return $subscribers;
    }

	public function like($id)
	{
		if (!is_null($this->id)) {
			if (is_null($this->data) || $this->data == '') {
				$this->data = array();
				$this->data['likes'] = array();
			}
			if (!isset($this->data['likes'])) {
				$this->data['likes'] = array();
			}
			if (!$this->array_id_exist($id)) {
				array_push($this->data['likes'], $id);
			}
			$this->xp += 10;
			$this->update_data();
			$this->update_xp();
		} else {
			echo "User is null";
		}
	}

	public function dislike($id)
	{
		if (!is_null($this->id)) {
			if (is_null($this->data) || $this->data == '') {
				$this->data = array();
				$this->data['likes'] = array();
			}
			if (!isset($this->data['likes'])) {
				$this->data['likes'] = array();
			}
			$arr = $this->array_id_exist($id);
			if ($arr) {
				array_splice($this->data['likes'], $arr - 1, 1);
				$this->update_data();
			}
		} else {
			echo "User is null";
		}
	}

	public function insert()
	{
		if (!is_null($this->id)) trigger_error("User::insert(): Attempt to insert an User object that already has its ID property set (to $this->id).", E_USER_ERROR);

		if (!$this->avatar) {
			$this->avatar = rand(1, 20);
		}

		$conn = open_connection();
		$sql = 'INSERT INTO users ( username, password, email, birth_date, join_date, gender, data, role, avatar ) 
				  VALUES ( :username, :password, :email, :birth_date, :join_date, :gender, :data, :role, :avatar )';
		$st = $conn->prepare($sql);
		$st->bindValue(":username", $this->username, PDO::PARAM_STR);
		$st->bindValue(":password", $this->password, PDO::PARAM_STR);
		$st->bindValue(":email", $this->email, PDO::PARAM_STR);
		$st->bindValue(":birth_date", $this->birth_date, PDO::PARAM_STR);
		$st->bindValue(":join_date", $this->join_date, PDO::PARAM_STR);
		$st->bindValue(":gender", $this->gender, PDO::PARAM_STR);
		$st->bindValue(":data", json_encode($this->data), PDO::PARAM_STR);
		$st->bindValue(":role", 'user', PDO::PARAM_STR);
		$st->bindValue(":avatar", $this->avatar, PDO::PARAM_INT);
		$st->execute();
		$this->id = $conn->lastInsertId();
		//
		if (function_exists('log_action')) {
			log_action(
				'create',
				'user',
				$this->id,
				$this->username
			);
		}
	}

	public function update_data()
	{
		if (is_null($this->id)) trigger_error("User::update(): Attempt to update an User object that does not have its ID property set.", E_USER_ERROR);
		//
		$conn = open_connection();
		$sql = "UPDATE users SET data=:data WHERE id = :id";

		$st = $conn->prepare($sql);
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->bindValue(":data", json_encode($this->data), PDO::PARAM_STR);
		$st->execute();
		//
		if (function_exists('log_action')) {
			log_action(
				'update',
				'user',
				$this->id,
				$this->username
			);
		}
	}

	public function update_xp()
	{
		if (is_null($this->id)) trigger_error("User::update(): Attempt to update an User object that does not have its ID property set.", E_USER_ERROR);
		//
		$conn = open_connection();
		$sql = "UPDATE users SET xp=:xp WHERE id = :id";

		$st = $conn->prepare($sql);
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->bindValue(":xp", $this->xp, PDO::PARAM_INT);
		$st->execute();
	}

	public function add_xp($val)
	{
		if (is_null($this->id)) trigger_error("User::update(): Attempt to update an User object that does not have its ID property set.", E_USER_ERROR);
		//
		$this->xp += (int)$val;

		$conn = open_connection();
		$sql = "UPDATE users SET xp=:xp WHERE id = :id";

		$st = $conn->prepare($sql);
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->bindValue(":xp", $this->xp, PDO::PARAM_INT);
		$st->execute();
	}

	public function updateRole($new_role)
	{
		if (!defined('USER_ADMIN') || !USER_ADMIN) {
			throw new Exception("Only administrators can update user roles.");
			return false;
		}

		if (is_null($this->id)) {
			throw new Exception("Attempt to update a User object that does not have its ID property set.");
			return false;
		}

		// Validate role input
		$valid_roles = ['user', 'crew', 'admin'];
		if (!in_array($new_role, $valid_roles)) {
			throw new Exception("Invalid role specified.");
			return false;
		}

		try {
			$conn = open_connection();
			$sql = "UPDATE users SET role = :role WHERE id = :id";

			$st = $conn->prepare($sql);
			$st->bindValue(":id", $this->id, PDO::PARAM_INT);
			$st->bindValue(":role", $new_role, PDO::PARAM_STR);
			$st->execute();

			// Update the role property if successful
			$this->role = $new_role;
			//
			if (function_exists('log_action')) {
				log_action(
					'update',
					'user',
					$this->id,
					$this->username
				);
			}
			return true;
		} catch (PDOException $e) {
			throw new Exception("Database error: " . $e->getMessage());
			return false;
		}
	}

	public function update()
	{
		if (is_null($this->id)) trigger_error("User::update(): Attempt to update an User object that does not have its ID property set.", E_USER_ERROR);
		//
		$conn = open_connection();
		$sql = "UPDATE users SET username=:username, password=:password, email=:email, birth_date=:birth_date, gender=:gender, bio=:bio, avatar=:avatar WHERE id = :id";

		$st = $conn->prepare($sql);
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->bindValue(":username", $this->username, PDO::PARAM_STR);
		$st->bindValue(":password", $this->password, PDO::PARAM_STR);
		$st->bindValue(":email", $this->email, PDO::PARAM_STR);
		$st->bindValue(":birth_date", $this->birth_date, PDO::PARAM_STR);
		$st->bindValue(":gender", $this->gender, PDO::PARAM_STR);
		$st->bindValue(":bio", $this->bio, PDO::PARAM_STR);
		$st->bindValue(":avatar", $this->avatar, PDO::PARAM_INT);
		$st->execute();
		//
		if (function_exists('log_action')) {
			log_action(
				'update',
				'user',
				$this->id,
				$this->username
			);
		}
	}

	public function delete($pass = null)
	{
		if (is_null($this->id)) trigger_error("User::delete(): Attempt to delete an User object that does not have its ID property set.", E_USER_ERROR);

		if (password_verify($pass, $this->password) || USER_ADMIN) {
			$conn = open_connection();
			$st = $conn->prepare("DELETE FROM users WHERE id = :id LIMIT 1");
			$st->bindValue(":id", $this->id, PDO::PARAM_INT);
			$st->execute();
			//Delete its avatar if exist
			if (file_exists(ABSPATH . 'images/avatar/' . $this->username . '.png')) {
				unlink(ABSPATH . 'images/avatar/' . $this->username . '.png');
			}

			//Remove all comments from this user
			$st = $conn->prepare("DELETE FROM comments WHERE sender_id = :id");
			$st->bindValue(":id", $this->id, PDO::PARAM_INT);
			$st->execute();

			//Remove all scores from this user
			$st = $conn->prepare("DELETE FROM scores WHERE user_id = :id");
			$st->bindValue(":id", $this->id, PDO::PARAM_INT);
			$st->execute();
			//
			if (function_exists('log_action')) {
				log_action(
					'delete',
					'user',
					$this->id,
					$this->username
				);
			}
		}
	}
}
