<?php

/* Chat line is used for the chat entries */

class ChatLine extends ChatBase{
	
	protected $text = '', $author = '', $gravatar = '';
	
	public function save(){
		DB::query("
			INSERT INTO webchat_lines (author, gravatar, text)
			VALUES (
				'".DB::esc($this->author)."',
				'".DB::esc($this->gravatar)."',
				'".DB::esc($this->text)."'
		)");
		
		// Returns the MySQLi object of the DB class
		
		return DB::getMySQLiObject();
	}

	public function checkPermission(){
        $result = DB::query("
			Select userlevel FROM webchat_users WHERE user=
				'".DB::esc($this->author)."'
		");


        $row = mysqli_fetch_assoc($result);


        if($row['userlevel']=='default' || $row['userlevel']=='admin'){
            return true;
        }
        return false;


    }
}

?>