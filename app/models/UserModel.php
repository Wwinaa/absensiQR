<?php
/**
 * User Model
 * Handles user database operations
 */
class UserModel extends BaseModel {
    
    /**
     * Get user by username
     * @param string $username
     * @return object|false User object if found, false otherwise
     */
    public function getUserByUsername($username) {
        $clean = strtolower(trim($username));
        if ($clean === 'kepala_sekolah' || $clean === 'kepsek' || $clean === 'kepalasekolah' || $clean === 'kepala sekolah') {
            $this->db->query("SELECT * FROM users WHERE (username = :u OR username = 'kepsek' OR role = 'kepala_sekolah') AND status = 'active' LIMIT 1");
            $this->db->bind(':u', $username);
            return $this->db->single();
        }
        $this->db->query("SELECT * FROM users WHERE username = :username AND status = 'active'");
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

    /**
     * Get user by ID
     * @param int $id
     * @return object|false
     */
    public function getUserById($id) {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }


    /**
     * Get user details based on role
     */
    public function getUserProfile($userId, $role) {
        if ($role === 'guru' || $role === 'walikelas') {
            $this->db->query("SELECT * FROM guru WHERE user_id = :id");
        } else if ($role === 'siswa') {
            $this->db->query("SELECT * FROM siswa WHERE user_id = :id");
        } else {
            return null; // Admin and kepsek might not have specific profiles in this minimal setup
        }
        
        $this->db->bind(':id', $userId);
        return $this->db->single();
    }

    /**
     * Create a new user account
     * @return int Inserted ID
     */
    public function createUser($data) {
        $this->db->query("INSERT INTO users (username, password, role, status) VALUES (:username, :password, :role, :status)");
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':status', isset($data['status']) ? $data['status'] : 'active');
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    public function updateUserPassword($id, $password) {
        $this->db->query("UPDATE users SET password = :password WHERE id = :id");
        $this->db->bind(':password', $password);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function deleteUser($id) {
        $this->db->query("DELETE FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
