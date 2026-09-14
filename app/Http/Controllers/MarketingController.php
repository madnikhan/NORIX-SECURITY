<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MarketingController extends Controller
{
    public function home()
    {
        return view('marketing.home');
    }

    public function about()
    {
        return view('marketing.about');
    }

    public function services()
    {
        return view('marketing.services');
    }

    public function policies()
    {
        $policies = Policy::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        return view('marketing.policies', compact('policies'));
    }

    public function downloadPolicy(Policy $policy): StreamedResponse
    {
        abort_unless($policy->is_published && $policy->file_path, 404);

        return Storage::disk('local')->download($policy->file_path, $policy->slug.'.pdf');
    }

    public function contact()
    {
        return view('marketing.contact');
    }

    public function storeContact(Request $request)
    {
        $data = $request->validate([
            'company' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'service_interest' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Lead::create($data);

        return back()->with('success', 'Thanks — we have your enquiry and will respond shortly.');
    }

    public function llmsTxt()
    {
        $services = collect(config('norix.services'))
            ->map(fn ($s) => '- '.$s['title'].': '.$s['summary'])
            ->implode("\n");

        $body = implode("\n", [
            '# '.config('norix.name'),
            '',
            config('norix.description'),
            '',
            '## Contact',
            'Email: '.config('norix.email'),
            'Phone: '.config('norix.phone'),
            'Address: '.config('norix.address'),
            '',
            '## Services',
            $services,
            '',
            '## Key URLs',
            '- Home: '.url('/'),
            '- Services: '.url('/services'),
            '- Careers: '.url('/careers'),
            '- Policies: '.url('/policies'),
            '- Contact: '.url('/contact'),
        ]);

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap()
    {
        $urls = [
            url('/'),
            url('/about'),
            url('/services'),
            url('/careers'),
            url('/policies'),
            url('/contact'),
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $url) {
            $xml .= '<url><loc>'.e($url).'</loc></url>';
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
