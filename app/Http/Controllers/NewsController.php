<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    /**
     * Feed público de notícias
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        $tag = $request->query('tag');
        
        // Query base - apenas aprovados
        $query = Video::approved()
            ->with(['user:id,name,nickname,username', 'tags:id,name,slug'])
            ->select([
                'id', 'title', 'slug', 'summary', 'description', 'thumbnail_url',
                'category', 'views_count', 'comments_count', 'is_sensitive', 'is_members_only',
                'source', 'location', 'incident_date', 'created_at', 'user_id'
            ]);
        
        // Filtro por categoria
        if ($category) {
            $query->byCategory($category);
        }
        
        // Filtro por tag
        if ($tag) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('slug', $tag);
            });
        }
        
        // Notícias paginadas (últimas)
        $news = $query->latest()->paginate(12);
        
        // Destaques (cached por 5 min)
        $featured = Cache::remember('news.featured', 300, function () {
            return Video::approved()
                ->featured()
                ->with(['user:id,name,nickname,username', 'tags:id,name,slug'])
                ->select([
                    'id', 'title', 'slug', 'summary', 'description', 'thumbnail_url',
                    'category', 'views_count', 'is_sensitive', 'is_members_only', 'created_at', 'user_id'
                ])
                ->latest()
                ->take(5)
                ->get();
        });
        
        // Mais vistas da semana (cached por 10 min)
        $trending = Cache::remember('news.trending', 600, function () {
            return Video::approved()
                ->with(['user:id,name,nickname,username'])
                ->select([
                    'id', 'title', 'slug', 'thumbnail_url', 'views_count',
                    'is_sensitive', 'is_members_only', 'category', 'created_at', 'user_id'
                ])
                ->where('created_at', '>=', now()->subWeek())
                ->orderByDesc('views_count')
                ->take(10)
                ->get();
        });
        
        // Categorias disponíveis
        $categories = Cache::remember('news.categories', 3600, function () {
            return Video::approved()
                ->select('category')
                ->distinct()
                ->pluck('category')
                ->filter()
                ->values();
        });
        
        // Tags populares
        $popularTags = Cache::remember('news.popular_tags', 3600, function () {
            return Tag::withCount(['videos' => function ($q) {
                    $q->approved();
                }])
                ->orderByDesc('videos_count')
                ->take(20)
                ->get();
        });
        
        // Mais comentadas (cached por 10 min)
        $mostCommented = Cache::remember('news.most_commented', 600, function () {
            return Video::approved()
                ->with(['user:id,name,nickname,username'])
                ->select([
                    'id', 'title', 'slug', 'thumbnail_url', 'comments_count',
                    'is_sensitive', 'created_at', 'user_id'
                ])
                ->where('comments_count', '>', 0)
                ->orderByDesc('comments_count')
                ->take(5)
                ->get();
        });
        
        // Notícias exclusivas para membros (cached por 10 min)
        $membersOnly = Cache::remember('news.members_only', 600, function () {
            return Video::approved()
                ->with(['user:id,name,nickname,username'])
                ->select([
                    'id', 'title', 'slug', 'summary', 'thumbnail_url',
                    'views_count', 'created_at', 'user_id'
                ])
                ->where('is_members_only', true)
                ->latest()
                ->take(6)
                ->get();
        });
        
        return view('news.index', compact(
            'news',
            'featured',
            'trending',
            'categories',
            'popularTags',
            'mostCommented',
            'membersOnly',
            'category',
            'tag'
        ));
    }

    /**
     * Exibe uma notícia individual
     */
    public function show(Video $video)
    {
        // Verificar se está aprovado
        if ($video->status !== 'approved') {
            abort(404);
        }
        
        // Verificar se conteúdo é apenas para membros e usuário não está autenticado
        if ($video->is_members_only && !auth()->check()) {
            return redirect()->route('news.index')
                ->with('error', 'Este conteúdo está disponível apenas para membros registrados.');
        }
        
        // Incrementar visualizações
        $video->incrementViews();
        
        // Carregar relacionamentos
        $video->load([
            'user:id,name,nickname,username,avatar',
            'editedBy:id,name,nickname,username',
            'tags:id,name,slug',
            'comments' => function ($q) {
                $q->with(['user:id,name,nickname,username,avatar,is_admin', 'replies' => function ($r) {
                    $r->with('user:id,name,nickname,username,avatar,is_admin')
                        ->latest();
                }])
                    ->whereNull('parent_id') // Only top-level comments
                    ->latest()
                    ->take(50);
            },
        ]);
        
        // Verificar se usuário pode ver mídia (conteúdo sensível requer autenticação)
        $canViewMedia = true;
        if ($video->is_sensitive && !auth()->check()) {
            $canViewMedia = false;
        }
        
        // Carregar mídias do vídeo
        $video->load('media');
        
        // Notícias relacionadas (mesma categoria ou tags)
        $related = Cache::remember("news.related.{$video->id}", 600, function () use ($video) {
            return Video::approved()
                ->where('id', '!=', $video->id)
                ->where(function ($q) use ($video) {
                    $q->where('category', $video->category)
                        ->orWhereHas('tags', function ($tagQuery) use ($video) {
                            $tagQuery->whereIn('tags.id', $video->tags->pluck('id'));
                        });
                })
                ->with(['user:id,name,nickname,username'])
                ->select([
                    'id', 'title', 'slug', 'thumbnail_url', 'views_count',
                    'is_sensitive', 'created_at', 'user_id', 'category'
                ])
                ->latest()
                ->take(6)
                ->get();
        });
        
        // "LEIA TAMBÉM" - Recomendação inline para inserir entre parágrafos
        // Critérios: mesma categoria OU mesmas tags, ordenado por relevância (views + recência)
        $readAlso = Cache::remember("news.read_also.{$video->id}", 600, function () use ($video) {
            $candidates = Video::approved()
                ->where('id', '!=', $video->id)
                ->where(function ($q) use ($video) {
                    // Prioridade 1: Mesma categoria
                    $q->where('category', $video->category);
                    
                    // Prioridade 2: Tags em comum
                    if ($video->tags->isNotEmpty()) {
                        $q->orWhereHas('tags', function ($tagQuery) use ($video) {
                            $tagQuery->whereIn('tags.id', $video->tags->pluck('id'));
                        });
                    }
                })
                ->select([
                    'id', 'title', 'slug', 'thumbnail_url', 'views_count',
                    'category', 'created_at'
                ])
                ->orderByDesc('views_count')
                ->take(5)
                ->get();
            
            // Retorna um aleatório dos top 5, ou null se não houver
            return $candidates->isNotEmpty() ? $candidates->random() : null;
        });
        
        // Mais do mesmo autor
        $moreFromAuthor = Video::approved()
            ->where('user_id', $video->user_id)
            ->where('id', '!=', $video->id)
            ->select(['id', 'title', 'slug', 'thumbnail_url', 'created_at'])
            ->latest()
            ->take(4)
            ->get();
        
        return view('news.show', compact(
            'video',
            'canViewMedia',
            'related',
            'readAlso',
            'moreFromAuthor'
        ));
    }

    /**
     * Pesquisa de notícias
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query) || strlen($query) < 2) {
            return redirect()->route('news.index')
                ->with('error', 'Digite pelo menos 2 caracteres para pesquisar.');
        }
        
        $searchTerms = '%' . $query . '%';
        
        $results = Video::approved()
            ->where(function ($q) use ($searchTerms) {
                $q->where('title', 'like', $searchTerms)
                    ->orWhere('description', 'like', $searchTerms)
                    ->orWhere('summary', 'like', $searchTerms)
                    ->orWhere('location', 'like', $searchTerms)
                    ->orWhereHas('tags', function ($tagQ) use ($searchTerms) {
                        $tagQ->where('name', 'like', $searchTerms);
                    });
            })
            ->with(['user:id,name,nickname,username', 'tags:id,name,slug'])
            ->select([
                'id', 'title', 'slug', 'summary', 'description', 'thumbnail_url',
                'category', 'views_count', 'is_sensitive', 'created_at', 'user_id'
            ])
            ->latest()
            ->paginate(20)
            ->appends(['q' => $query]);
        
        return view('news.search', compact('results', 'query'));
    }

    /**
     * Feed por categoria
     */
    public function category(string $category)
    {
        // Lista de categorias válidas
        $validCategories = [
            'guerra', 'terrorismo', 'chacina', 'massacre', 'suicidio', 'tribunal-do-crime',
            'homicidio', 'assalto', 'sequestro', 'tiroteio',
            'acidentes', 'desastres',
            'operacao-policial', 'faccoes',
            'conflitos', 'execucoes'
        ];
        
        if (!in_array($category, $validCategories)) {
            abort(404, 'Categoria não encontrada');
        }
        
        $news = Video::approved()
            ->byCategory($category)
            ->with(['user:id,name,nickname,username', 'tags:id,name,slug'])
            ->select([
                'videos.id', 'videos.title', 'videos.slug', 'videos.summary', 'videos.description', 
                'videos.thumbnail_url', 'videos.category', 'videos.views_count', 'videos.is_sensitive', 
                'videos.created_at', 'videos.user_id'
            ])
            ->orderBy('videos.created_at', 'desc')
            ->paginate(20);
        
        return view('news.category', compact('news', 'category'));
    }

    /**
     * Feed por tag
     */
    public function tag(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        
        $news = $tag->videos()
            ->approved()
            ->with(['user:id,name,nickname,username'])
            ->select([
                'videos.id', 'videos.title', 'videos.slug', 'videos.summary', 'videos.description', 
                'videos.thumbnail_url', 'videos.category', 'videos.views_count', 'videos.is_sensitive', 
                'videos.created_at', 'videos.user_id'
            ])
            ->orderBy('videos.created_at', 'desc')
            ->paginate(20);
        
        return view('news.tag', compact('news', 'tag'));
    }
}
