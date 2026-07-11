<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Password_reset_model extends CI_Model {

    /**
     * Memeriksa apakah user bisa me-request reset password (rate limit 3x / 15 menit).
     */
    public function can_request_reset($user_id)
    {
        $limit_time = date('Y-m-d H:i:s', strtotime('-15 minutes'));
        
        $count = $this->db->where('user_id', (int) $user_id)
                          ->where('created_at >', $limit_time)
                          ->count_all_results('password_resets');
        
        return $count < 3;
    }

    /**
     * Generate token reset password baru, invalidate token lama, dan simpan hash-nya.
     */
    public function create_token($user_id)
    {
        // Invalidate token lama (set expired)
        $this->db->where('user_id', (int) $user_id)
                 ->where('used_at IS NULL')
                 ->update('password_resets', ['expired_at' => date('Y-m-d H:i:s')]);

        // Generate token
        $token = bin2hex(random_bytes(32));
        $token_hash = hash('sha256', $token);
        
        $now = date('Y-m-d H:i:s');
        $expired_at = date('Y-m-d H:i:s', strtotime('+60 minutes'));

        $this->db->insert('password_resets', [
            'user_id'    => (int) $user_id,
            'token_hash' => $token_hash,
            'expired_at' => $expired_at,
            'created_at' => $now
        ]);

        return $token; // Return token asli untuk diemail, bukan hash!
    }

    /**
     * Memvalidasi token yang diberikan. Mengembalikan user_id jika valid, false jika tidak.
     */
    public function validate_token($token)
    {
        $token_hash = hash('sha256', $token);
        $now = date('Y-m-d H:i:s');

        $reset_record = $this->db->where('token_hash', $token_hash)
                                 ->where('used_at IS NULL')
                                 ->where('expired_at >', $now)
                                 ->get('password_resets')
                                 ->row_array();

        if ($reset_record) {
            return $reset_record['user_id'];
        }

        return false;
    }

    /**
     * Menandai token sebagai sudah digunakan.
     */
    public function mark_used($token)
    {
        $token_hash = hash('sha256', $token);
        
        $this->db->where('token_hash', $token_hash)
                 ->update('password_resets', [
                     'used_at' => date('Y-m-d H:i:s')
                 ]);
    }
}
