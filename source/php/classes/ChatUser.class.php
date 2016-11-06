<?php

class ChatUser extends ChatBase{
	
	protected $name = '', $gravatar = '', $password='';

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


        $pwhash= DB::query("
            SELECT password_hash FROM webchat_users WHERE name = '".DB::esc($this->name)."'
        
        ");


        if(mysqli_num_rows($pwhash)>=1) {
            throw new Exception('was executed');
            return password_verify($this->password, $pwhash);
        }else{
            throw new Exception('was not executed');
            return false;
        }
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