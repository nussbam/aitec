<?php

/* The Chat class exploses public static methods, used by ajax.php */

class Chat{
	
	public static function register($name, $email, $password){
		if(!$name || !$email || !$password){
			throw new Exception('Fill in all the required fields.');


		}


		
		if(!filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL)){
			throw new Exception('Your email is invalid.');
		}

		// Preparing the gravatar hash:
		$gravatar = md5(strtolower(trim($email)));
		
		$user = new ChatUser(array(
			'name'		=> $name,
			'gravatar'	=> $gravatar,
            'password'  => $password
		));


		
		// The save method returns a MySQLi object
		if($user->createUser()->affected_rows != 1){

			throw new Exception('This nick is in use. Please choose another one');
		}
		
		$_SESSION['user']	= array(
			'name'		=> $name,
			'gravatar'	=> $gravatar
		);
		
		return array(
			'status'	=> 1,
			'name'		=> $name,
			'gravatar'	=> Chat::gravatarFromHash($gravatar)
		);
	}


    public static function login($name, $password){
        if(!$name || !$password){
            throw new Exception('Fill in all the required fields.');


        }

        $user = new ChatUser(array(
            'name'		=> $name,
            'password'  => $password
        ));



        // The save method returns a MySQLi object
        if(!$user->loginUser()){

            throw new Exception('Invalid Login, try again!');
        }

        //temporary value TODO fix this
        $gravatar = '0bc83cb571cd1c50ba6f3e8a78ef1346';

        $_SESSION['user']	= array(
            'name'		=> $name,
            'gravatar'	=> $gravatar
        );

        return array(
            'status'	=> 1,
            'name'		=> $name,
            'gravatar'	=> Chat::gravatarFromHash($gravatar)
        );
    }

    public static function loginAdmin($name, $password){
        if(!$name || !$password){
            throw new Exception('Fill in all the required fields.');


        }

        $user = new ChatUser(array(
            'name'		=> $name,
            'password'  => $password,
            'userlevel' => 'admin'
        ));



        // The save method returns a MySQLi object
        if(!$user->loginAdmin()){

            throw new Exception('Invalid Login or no permission, try again!');
        }
        else{
            session_start();
            $_SESSION['admin'] = $name;
        }

        //temporary value TODO fix this
        $gravatar = '0bc83cb571cd1c50ba6f3e8a78ef1346';

        $_SESSION['user']	= array(
            'name'		=> $name,
            'gravatar'	=> $gravatar
        );

        return array(
            'status'	=> 1,
            'name'		=> $name,
            'gravatar'	=> Chat::gravatarFromHash($gravatar)
        );
    }

    public static function getCRUDUsers(){
        if ($_SESSION['admin']) {
            $result = DB::query("SELECT id, name, userlevel FROM webchat_users where userlevel!='admin'");
            $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $users;
        }else{
            return array(
                'error' => 'No permission to see the user-records'
            );
        }
    }

    public static function saveUser($data_uid, $dstatus)
    {
        $esc_data_id = DB::esc($data_uid);
        $esc_dstatus = DB::esc($dstatus);
        $result = DB::query("UPDATE webchat_users SET userlevel = '" . $esc_dstatus . "' WHERE id = '" . $esc_data_id . "'");
        return $result;
    }

    public static function deleteUser($data_uid)
    {
        $esc_ID = DB::esc($data_uid);
        $result = DB::query("DELETE FROM webchat_users WHERE id = '" . $esc_ID . "'");
        return $result;
    }
	
	public static function checkLogged(){
		$response = array('logged' => false);
			
		if($_SESSION['user']['name']){
			$response['logged'] = true;
			$response['loggedAs'] = array(
				'name'		=> $_SESSION['user']['name'],
				'gravatar'	=> Chat::gravatarFromHash($_SESSION['user']['gravatar'])
			);
		}
		
		return $response;
	}
	
	public static function logout(){
		DB::query("UPDATE webchat_users SET status='inactive' WHERE name = '".DB::esc($_SESSION['user']['name'])."'");
		
		$_SESSION = array();
		unset($_SESSION);

		return array('status' => 1);
	}
	
	public static function submitChat($chatText){

		if(!$_SESSION['user']){
			throw new Exception('You are not logged in');
		}

		if(!$chatText){
			throw new Exception('You haven\' entered a chat message.');
		}
	
		$chat = new ChatLine(array(
			'author'	=> $_SESSION['user']['name'],
			'gravatar'	=> $_SESSION['user']['gravatar'],
			'text'		=> $chatText
		));

        if($chat->checkPermission()){
	
		    // The save method returns a MySQLi object
		    $insertID = $chat->save()->insert_id;
        }
        else{
            throw new Exception("Useraccount needs to be approved by admin");
        }
	
		return array(
			'status'	=> 1,
			'insertID'	=> $insertID
		);
	}
	
	public static function getUsers(){
		if($_SESSION['user']['name']){
			$user = new ChatUser(array('name' => $_SESSION['user']['name']));
			$user->update();
		}
		
		// Deleting chats older than 5 minutes and users inactive for 30 seconds
		
		DB::query("DELETE FROM webchat_lines WHERE ts < SUBTIME(NOW(),'0:5:0')");
		
		$result = DB::query("SELECT * FROM webchat_users where status='active' ORDER BY name ASC LIMIT 18");

		$users = array();
		while($user = $result->fetch_object()){
			$user->gravatar = Chat::gravatarFromHash($user->gravatar,30);
			$users[] = $user;
		}
	
		return array(
			'users' => $users,
			'total' => DB::query("SELECT COUNT(*) as cnt FROM webchat_users WHERE status='active'")->fetch_object()->cnt
		);
	}
	
	public static function getChats($lastID){
		$lastID = (int)$lastID;
	
		$result = DB::query('SELECT * FROM webchat_lines WHERE id > '.$lastID.' ORDER BY id ASC');
	
		$chats = array();
		while($chat = $result->fetch_object()){
			
			// Returning the GMT (UTC) time of the chat creation:
			
			$chat->time = array(
				'hours'		=> gmdate('H',strtotime($chat->ts)),
				'minutes'	=> gmdate('i',strtotime($chat->ts))
			);
			
			$chat->gravatar = Chat::gravatarFromHash($chat->gravatar);
			
			$chats[] = $chat;
		}
	
		return array('chats' => $chats);
	}
	
	public static function gravatarFromHash($hash, $size=23){
		return 'http://www.gravatar.com/avatar/'.$hash.'?size='.$size.'&amp;default='.
				urlencode('http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?size='.$size);
	}
}


?>