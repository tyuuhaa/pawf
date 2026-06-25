<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PostModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PostAdmin extends BaseController
{
    public function index()
    {
        $post = new PostModel();
        $currentAuthor = $this->currentAuthor();
        $query = trim((string) $this->request->getGet('q'));
        $status = trim((string) $this->request->getGet('status'));
        $sort = $this->resolveSort((string) $this->request->getGet('sort'));

        $builder = $post;
        $this->applyVisibilityScope($builder, $status, $currentAuthor);

        if ($query !== '') {
            $builder->groupStart()
                ->like('title', $query)
                ->orLike('content', $query)
                ->orLike('author', $query)
                ->groupEnd();
        }
        $this->applySort($builder, $sort);

        $posts = $builder->findAll();
        $stats = $this->collectStats($post, $currentAuthor);

        $recentTimeline = [];
        foreach (array_slice($posts, 0, 5) as $item) {
            $recentTimeline[] = [
                'title' => $item['title'],
                'status' => $item['status'],
                'author' => $item['author'] ?: 'Anonim',
                'created_at' => $item['created_at'] ?? null,
            ];
        }

        echo view('admin/admin_post_list', [
            'posts' => $posts,
            'query' => $query,
            'statusFilter' => $status,
            'sortFilter' => $sort,
            'stats' => $stats,
            'userProfile' => $this->currentUserProfile(),
            'recentTimeline' => $recentTimeline,
            'flash' => session()->getFlashdata('message'),
            'currentAuthor' => $currentAuthor,
            'title' => 'RuangCerita | Dashboard',
            'heroTitle' => 'Kelola konten tanpa drama',
        ]);
    }

    //--------------------------------------------------------------

    public function preview($id)
    {
        $post = new PostModel();
        $data['post'] = $post->where('id', $id)->first();

        if (!$data['post'] || !$this->canManagePost($data['post'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $data['relatedPosts'] = $post->where('id !=', $data['post']['id'])
            ->where('author', $this->currentAuthor())
            ->orderBy('created_at', 'DESC')
            ->findAll(3);

        foreach ($data['relatedPosts'] as &$item) {
            $text = trim(strip_tags((string) ($item['content'] ?? '')));
            $dataExcerpt = mb_strlen($text) > 88 ? mb_substr($text, 0, 88) . '...' : $text;
            $item['excerpt'] = $dataExcerpt !== '' ? $dataExcerpt : 'Kontennya belum diisi.';
        }

        $data['reading_time'] = max(1, (int) ceil(str_word_count(strip_tags((string) ($data['post']['content'] ?? ''))) / 200));
        $data['title'] = 'RuangCerita | Preview Post';
        $data['heroTitle'] = (string) $data['post']['title'];
        echo view('post_detail', $data);
    }

    //--------------------------------------------------------------

    public function create()
    {
        $validation = \Config\Services::validation();
        if (strtolower($this->request->getMethod()) === 'post') {
            $validation->setRules([
                'title' => 'required|min_length[5]|max_length[255]',
                'content' => 'required|min_length[40]',
            ]);
            $isDataValid = $validation->withRequest($this->request)->run();

            if ($isDataValid) {
                $title = trim((string) $this->request->getPost('title'));
                $author = $this->currentAuthor();
                $content = trim((string) $this->request->getPost('content'));
                $status = $this->resolveStatus((string) $this->request->getPost('status'));
                $slug = $this->generateUniqueSlug(url_title($title, '-', true));

                $post = new PostModel();
                $post->insert([
                    'title' => $title,
                    'author' => $author,
                    'content' => $content,
                    'status' => $status,
                    'slug' => $slug,
                ]);

                session()->setFlashdata('message', 'Postingan baru berhasil disimpan. Lanjut gas ke konten berikutnya.');
                return redirect('admin/post');
            }
        }

        echo view('admin/admin_post_create', [
            'validation' => $validation,
            'old' => [
                'title' => old('title'),
                'content' => old('content'),
            ],
            'currentAuthor' => $this->currentAuthor(),
            'title' => 'RuangCerita | Tulis Post',
            'heroTitle' => 'Tulis postingan baru',
        ]);
    }

    //--------------------------------------------------------------

    public function edit($id)
    {
        $post = new PostModel();
        $data['post'] = $post->where('id', $id)->first();
        if (!$data['post'] || !$this->canManagePost($data['post'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $validation = \Config\Services::validation();
        if (strtolower($this->request->getMethod()) === 'post') {
            $validation->setRules([
                'id' => 'required',
                'title' => 'required|min_length[5]|max_length[255]',
                'content' => 'required|min_length[40]',
            ]);
            $isDataValid = $validation->withRequest($this->request)->run();

            if ($isDataValid) {
                $title = trim((string) $this->request->getPost('title'));
                $content = trim((string) $this->request->getPost('content'));
                $status = $this->resolveStatus((string) $this->request->getPost('status'));

                $post->update($id, [
                    'title' => $title,
                    'content' => $content,
                    'status' => $status,
                ]);

                session()->setFlashdata('message', 'Postingan berhasil di-update. Perubahanmu sudah live sesuai status.');
                return redirect('admin/post');
            }
        }

        echo view('admin/admin_post_update', [
            'post' => $data['post'],
            'validation' => $validation,
            'title' => 'RuangCerita | Edit Post',
            'heroTitle' => 'Rapihin postingan yang sudah ada',
        ]);
    }

    //--------------------------------------------------------------

    public function toggleStatus($id)
    {
        $post = new PostModel();
        $target = $post->find($id);
        if (!$target || !$this->canManagePost($target)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $newStatus = $target['status'] === 'published' ? 'draft' : 'published';
        if ($newStatus === 'draft') {
            $post->update($id, ['author' => $this->currentAuthor()]);
        }
        $post->update($id, ['status' => $newStatus]);

        $label = $newStatus === 'published' ? 'publish' : 'draft';
        session()->setFlashdata('message', "Status berhasil diubah ke {$label}.");
        return redirect('admin/post');
    }

    //--------------------------------------------------------------

    public function delete($id)
    {
        $post = new PostModel();
        $target = $post->find($id);
        if (!$target || !$this->canManagePost($target)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $post->delete($id);
        session()->setFlashdata('message', 'Postingan dihapus. Workspace jadi lebih rapi.');
        return redirect('admin/post');
    }

    private function resolveStatus(string $status): string
    {
        return $status === 'published' ? 'published' : 'draft';
    }

    private function resolveSort(string $sort): string
    {
        $allowed = ['newest', 'oldest', 'title_asc', 'title_desc', 'status'];
        return in_array($sort, $allowed, true) ? $sort : 'newest';
    }

    private function applySort(PostModel $builder, string $sort): void
    {
        if ($sort === 'oldest') {
            $builder->orderBy('created_at', 'ASC');
            return;
        }

        if ($sort === 'title_asc') {
            $builder->orderBy('title', 'ASC');
            return;
        }

        if ($sort === 'title_desc') {
            $builder->orderBy('title', 'DESC');
            return;
        }

        if ($sort === 'status') {
            $builder->orderBy('status', 'ASC')->orderBy('created_at', 'DESC');
            return;
        }

        $builder->orderBy('created_at', 'DESC');
    }

    private function generateUniqueSlug(string $baseSlug): string
    {
        $post = new PostModel();
        $slug = $baseSlug !== '' ? $baseSlug : 'post-baru';
        $candidate = $slug;
        $counter = 1;

        while ($post->where('slug', $candidate)->countAllResults() > 0) {
            $candidate = $slug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    private function collectStats(PostModel $post, string $currentAuthor): array
    {
        $published = $post->where('status', 'published')->where('author', $currentAuthor)->countAllResults();
        $draft = $post->where('status', 'draft')->where('author', $currentAuthor)->countAllResults();
        $total = $published + $draft;
        $completion = $total > 0 ? (int) round(($published / $total) * 100) : 0;

        return [
            'total' => $total,
            'published' => $published,
            'draft' => $draft,
            'completion' => $completion,
        ];
    }

    private function currentAuthor(): string
    {
        if (function_exists('user') && user()) {
            if (!empty(user()->username)) {
                return (string) user()->username;
            }
            if (!empty(user()->email)) {
                return (string) user()->email;
            }
            if (!empty(user()->id)) {
                return 'user-' . (string) user()->id;
            }
        }

        return 'Anonim';
    }

    private function currentUserProfile(): array
    {
        $profile = [
            'name' => $this->currentAuthor(),
            'email' => '-',
            'emailMasked' => '-',
        ];

        if (function_exists('user') && user()) {
            $currentUser = user();
            $fullName = '';

            if (!empty($currentUser->fullname)) {
                $fullName = (string) $currentUser->fullname;
            } elseif (!empty($currentUser->name)) {
                $fullName = (string) $currentUser->name;
            }

            $profile['name'] = $fullName !== '' ? $fullName : $this->currentAuthor();
            $profile['email'] = !empty($currentUser->email) ? (string) $currentUser->email : '-';
            $profile['emailMasked'] = $this->maskEmail($profile['email']);
        }

        return $profile;
    }

    private function maskEmail(string $email): string
    {
        if ($email === '' || $email === '-' || strpos($email, '@') === false) {
            return '-';
        }

        [$localPart, $domainPart] = explode('@', $email, 2);
        if ($localPart === '' || $domainPart === '') {
            return '-';
        }

        return substr($localPart, 0, 1) . '**@' . $domainPart;
    }

    private function canManagePost(array $post): bool
    {
        return (($post['author'] ?? '') === $this->currentAuthor());
    }

    private function applyVisibilityScope(PostModel $builder, string $status, string $currentAuthor): void
    {
        $builder->where('author', $currentAuthor);

        if ($status === 'draft') {
            $builder->where('status', 'draft');
            return;
        }

        if ($status === 'published') {
            $builder->where('status', 'published');
            return;
        }
    }
}
