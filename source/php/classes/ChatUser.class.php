<?php

class ChatUser extends ChatBase{
	
	protected $name = '', $gravatar = '', $password='', $userlevel ='';

    /**
     * @return mixed
     */
    public function createUser(){
        $temp = DB::esc($this->password);
        $passwordHash =  password_hash($temp, PASSWORD_DEFAULT);

       DB::query("
			INSERT INTO webchat_users (name, gravatar, password_hash)
			VALUES (
				'".DB::esc($this->name)."',
				'".DB::esc($this->gravatar)."',
				'".$passwordHash."'
		)");


		
		return DB::getMySQLiObject();
	}

    public function loginUser(){
        $temp = DB::esc($this->password);
        $passwordHash =  password_hash($temp, PASSWORD_DEFAULT);


        $result= DB::query("
            SELECT gravatar, password_hash FROM webchat_users WHERE name = '".DB::esc($this->name)."'
        
        ");

        $row = mysqli_fetch_assoc($result);

        if(password_verify($this->password,$row['password_hash'])){
            DB::query("
            UPDATE webchat_users SET status='active' WHERE name = '".DB::esc($this->name)."'   
        ");
            return true;
        }

        return password_verify($this->password,$row['password_hash']);

    }
    public function loginAdmin(){
        $temp = DB::esc($this->password);
        $passwordHash =  password_hash($temp, PASSWORD_DEFAULT);


        $result= DB::query("
            SELECT gravatar, password_hash FROM webchat_users WHERE name = '".DB::esc($this->name)."' AND userlevel = '".DB::esc($this->userlevel)."'
        
        ");

        $row = mysqli_fetch_assoc($result);

        if(password_verify($this->password,$row['password_hash'])){
            DB::query("
            UPDATE webchat_users SET status='active' WHERE name = '".DB::esc($this->name)."'   
        ");
            return true;
        }

        return password_verify($this->password,$row['password_hash']);

    }

    public function changeUserLevel(){
        DB::query("
			UPDATE webchat_users SET userlevel = '".DB::esc($this->userlevel)."'
			WHERE name = '".DB::esc($this->name)."'");
    }

    public function deleteUser(){
        DB::query("
			DELETE FROM webchat_users WHERE name = '".DB::esc($this->name)."'");
    }



	
	public function update(){
		DB::query("
			INSERT INTO webchat_users (name, gravatar)
			VALUES (
				'".DB::esc($this->name)."',
				'".DB::esc($this->gravatar)."'
			) ON DUPLICATE KEY UPDATE last_activity = NOW()");
	}
}

?>