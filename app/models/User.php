<?php


class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function findUserByEmail($email){
        $this->db->query('SELECT * FROM users WHERE email =:email');
        $this->db->bind(':email', $email);
        $row = $this->db->getSingleResult();

        if($this->db->getRowCount() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function register($data){
        $this->db->query('INSERT INTO users SET name =:name, email =:email, password =:password');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email',  $data['email']);
        $this->db->bind(':password',  $data['password']);
        if($this->db->execute()){
            return true;
        }else{
            return false;
        }
    }

    public function login($email, $password){
        $this->db->query('SELECT * FROM users WHERE email =:email');
        $this->db->bind(':email',  $email);
        $row = $this->db->getSingleResult();
        $hashed_password = $row['password'];
        if(password_verify($password, $hashed_password)){
            return $row;
        }else{
            return false;
        }

    }
}