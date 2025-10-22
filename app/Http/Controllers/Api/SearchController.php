<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\Product;
use App\Models\Page;
use App\Models\Faq;
use App\Models\SearchLog;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Unified website-wide search.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json([
                'message' => 'Query parameter "q" is required.',
                'data' => [],
            ], 400);
        }
        SearchLog::create([
            'query' => $query,
            'user_id' => Auth::id(), // null if guest
        ]);

        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);

        // --- Fetch results from each model using Scout (MeiliSearch relevance preserved) ---
        $products  = Product::search($query)->take(50)->get();
        $blogPosts = BlogPost::search($query)->take(50)->get();
        $pages     = Page::search($query)->take(50)->get();
        $faqs      = Faq::search($query)->take(50)->get();

        // --- Merge results into a single collection ---
        $results = collect()
            ->concat($products->map(fn($item) => [
                'type' => 'product',
                'title' => $item->name,
                'snippet' => substr($item->description, 0, 150),
                'link' => $item->id,
                'created_at' => $item->created_at,
            ]))
            ->concat($blogPosts->map(fn($item) => [
                'type' => 'blog',
                'title' => $item->title,
                'snippet' => substr(strip_tags($item->body), 0, 150),
                'link' => $item->id,
                'created_at' => $item->published_at,
            ]))
            ->concat($pages->map(fn($item) => [
                'type' => 'page',
                'title' => $item->title,
                'snippet' => substr(strip_tags($item->content), 0, 150),
                'link' => $item->id,
                'created_at' => $item->created_at,
            ]))
            ->concat($faqs->map(fn($item) => [
                'type' => 'faq',
                'title' => $item->question,
                'snippet' => substr($item->answer, 0, 150),
                'link' => $item->id,
                'created_at' => $item->created_at,
            ]));

        // --- Prioritize exact matches ---
        $results = $results->sortByDesc(
            fn($item) =>
            strtolower($item['title']) === strtolower($query) ? 1 : 0
        )->values();

        // --- Manual pagination ---
        $paginatedResults = $results->forPage($page, $perPage);

        return response()->json([
            'query' => $query,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_results' => $results->count(),
            'data' => $paginatedResults,
        ]);
    }
    /**
     * Get search suggestions for typeahead functionality.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([
                'suggestions' => [],
            ]);
        }

        $limit = (int) $request->input('limit', 10);

        // Get suggestions from all models
        $suggestions = collect();

        // Product suggestions
        $productSuggestions = Product::search($query)
            ->take($limit)
            ->get()
            ->map(fn($item) => [
                'text' => $item->name,
                'type' => 'product',
                'value' => $item->name,
            ]);

        // Blog post suggestions
        $blogSuggestions = BlogPost::search($query)
            ->take($limit)
            ->get()
            ->map(fn($item) => [
                'text' => $item->title,
                'type' => 'blog',
                'value' => $item->title,
            ]);

        // Page suggestions
        $pageSuggestions = Page::search($query)
            ->take($limit)
            ->get()
            ->map(fn($item) => [
                'text' => $item->title,
                'type' => 'page',
                'value' => $item->title,
            ]);

        // FAQ suggestions
        $faqSuggestions = Faq::search($query)
            ->take($limit)
            ->get()
            ->map(fn($item) => [
                'text' => $item->question,
                'type' => 'faq',
                'value' => $item->question,
            ]);

        $suggestions = $suggestions
            ->concat($productSuggestions)
            ->concat($blogSuggestions)
            ->concat($pageSuggestions)
            ->concat($faqSuggestions)
            ->unique('text')
            ->take($limit);

        return response()->json([
            'suggestions' => $suggestions->values(),
        ]);
    }

    /**
     * Get search logs and analytics (admin only).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logs(Request $request)
    {
        $limit = (int) $request->input('limit', 50);
        $type = $request->input('type', 'recent'); // 'recent' or 'popular'

        if ($type === 'popular') {
            // Get most searched terms
            $logs = SearchLog::selectRaw('query, COUNT(*) as search_count')
                ->groupBy('query')
                ->orderBy('search_count', 'desc')
                ->limit($limit)
                ->get()
                ->map(fn($log) => [
                    'query' => $log->query,
                    'search_count' => $log->search_count,
                ]);
        } else {
            // Get recent searches
            $logs = SearchLog::with('user')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(fn($log) => [
                    'id' => $log->id,
                    'query' => $log->query,
                    'user_id' => $log->user_id,
                    'created_at' => $log->created_at,
                ]);
        }

        return response()->json([
            'type' => $type,
            'total' => $logs->count(),
            'data' => $logs,
        ]);
    }

    /**
     * Get search statistics and analytics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function analytics()
    {
        $totalSearches = SearchLog::count();
        $uniqueQueries = SearchLog::distinct('query')->count();
        $searchesToday = SearchLog::whereDate('created_at', today())->count();
        $searchesThisWeek = SearchLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        // Top 10 most searched terms
        $topQueries = SearchLog::selectRaw('query, COUNT(*) as search_count')
            ->groupBy('query')
            ->orderBy('search_count', 'desc')
            ->limit(10)
            ->get();

        // Recent searches
        $recentSearches = SearchLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($log) => [
                'query' => $log->query,
                'user' => $log->user ? $log->user->name : 'Guest',
                'created_at' => $log->created_at->diffForHumans(),
            ]);

        return response()->json([
            'statistics' => [
                'total_searches' => $totalSearches,
                'unique_queries' => $uniqueQueries,
                'searches_today' => $searchesToday,
                'searches_this_week' => $searchesThisWeek,
            ],
            'top_queries' => $topQueries,
            'recent_searches' => $recentSearches,
        ]);
    }
}
