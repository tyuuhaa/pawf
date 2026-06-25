<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PostModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Post extends BaseController
{
    public function index()
	{
        $post = new PostModel();
        $query = trim((string) $this->request->getGet('q'));

        $builder = $post->where('status', 'published');
        if ($query !== '') {
            $builder->groupStart()
                ->like('title', $query)
                ->orLike('content', $query)
                ->orLike('author', $query)
                ->groupEnd();
        }

        $posts = $builder->orderBy('created_at', 'DESC')->findAll();
        foreach ($posts as &$item) {
            $item['excerpt'] = $this->buildExcerpt((string) ($item['content'] ?? ''), 150);
            $item['reading_time'] = max(1, (int) ceil(str_word_count(strip_tags((string) ($item['content'] ?? ''))) / 200));
        }

        $data = [
            'posts' => $posts,
            'query' => $query,
            'title' => 'RuangCerita | Blog',
            'heroTitle' => 'Kumpulan artikel yang sudah publish',
        ];

        echo view('post', $data);
	}

	//------------------------------------------------------------

	public function viewPost($slug)
	{
		$post = new PostModel();
		$data['post'] = $post->where([
			'slug' => $slug,
			'status' => 'published'
		])->first();

        // tampilkan 404 error jika data tidak ditemukan
		if (!$data['post']) {
			throw PageNotFoundException::forPageNotFound();
		}

        $data['relatedPosts'] = $post->where('status', 'published')
            ->where('id !=', $data['post']['id'])
            ->orderBy('created_at', 'DESC')
            ->findAll(3);

        foreach ($data['relatedPosts'] as &$item) {
            $item['excerpt'] = $this->buildExcerpt((string) ($item['content'] ?? ''), 88);
        }

        $data['reading_time'] = max(1, (int) ceil(str_word_count(strip_tags((string) ($data['post']['content'] ?? ''))) / 200));
        $data['title'] = 'RuangCerita | Detail Post';
        $data['heroTitle'] = (string) $data['post']['title'];

		echo view('post_detail', $data);
	}

    private function buildExcerpt(string $content, int $limit): string
    {
        $text = trim(strip_tags($content));
        if ($text === '') {
            return 'Tulisan ini lagi di-draft dan belum punya isi. Coba cek lagi nanti ya.';
        }
        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $limit)) . '...';
    }
}
