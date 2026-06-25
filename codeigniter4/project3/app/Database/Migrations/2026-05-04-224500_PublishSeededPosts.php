<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PublishSeededPosts extends Migration
{
    public function up()
    {
        $this->db->table('posts')
            ->whereIn('slug', ['hello-world', 'meetup-comunity'])
            ->where('status', 'draft')
            ->update(['status' => 'published']);
    }

    public function down()
    {
        $this->db->table('posts')
            ->whereIn('slug', ['hello-world', 'meetup-comunity'])
            ->where('status', 'published')
            ->update(['status' => 'draft']);
    }
}
