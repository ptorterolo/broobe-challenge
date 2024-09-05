<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\MetricHistoryRun;

use App\Models\Strategy;
use App\Models\Category;
use App\Http\Requests\GetMetricRequest;
use Illuminate\Support\Facades\Validator;

class MetricController extends Controller
{
    public function index()
    {   
        $strategies = Strategy::all();
        $categories = Category::all();
        
        return view('index', compact('strategies', 'categories'));
    }
    public function getMetrics(Request $request)
    {           
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'categories' => 'required|array',
            'strategy' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        

        $client = new Client();
        $urlToCheck = $request->input('url');
        $categories = $request->input('categories');
        $selectedCategories = '';
        foreach ($categories as $value) {
            $selectedCategories = $selectedCategories . 'category='.$value.'&';
        }
        $strategy = $request->input('strategy');        
        $key = env('GOOGLE_API_KEY');
        $pageIsightUrl = env('GOOGLE_PAGEINSIGHTS_API_URL');     
        $url = "{$pageIsightUrl}?url={$urlToCheck}&key={$key}&{$selectedCategories}strategy={$strategy}";
        $response = $client->request('GET', $url);
        $data = $response->getBody(); 

        return response()->json(json_decode($data));
    }

    public function saveMetrics(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'categories' => 'required|array',
            'strategy' => 'required',
            'currentMetrics' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $strategy = Strategy::where('name', $request->input('strategy'))->first();
        $currentMetrics = json_decode($request->input('currentMetrics'));
        $accessibilityMetric = null;
        $performanceMetric = null;
        $seoMetric = null;
        $bestPracticesMetric = null;
        
        foreach ($currentMetrics as $metric) {
            switch ($metric->category) {
                case 'Performance':
                    $performanceMetric = $metric->score;
                    break;
                case 'Accessibility':
                    $accessibilityMetric = $metric->score;
                    break;
                case 'Best Practices':
                    $bestPracticesMetric = $metric->score;
                    break;
                case 'SEO':
                    $seoMetric = $metric->score;
                    break;
                
            }
        }

        MetricHistoryRun::create([
            'url' => $request->input('url'),
            'accesibility_metric' => $accessibilityMetric,            
            'performance_metric' => $performanceMetric,
            'seo_metric' => $seoMetric,
            'best_practices_metric' => $bestPracticesMetric,
            'strategy_id' => $strategy->id,
        ]);

        return response()->json(['success' => true]);
    }

    public function getHistory()
    {
        $metrics = MetricHistoryRun::all();
        foreach ($metrics as $metric) {
            $metric->strategy_name = Strategy::find($metric->strategy_id)->name;
        }
        return view('metrics-history', compact('metrics'));
    }
}

