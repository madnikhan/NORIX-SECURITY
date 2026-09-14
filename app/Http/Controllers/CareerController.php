<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Candidate;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CareerController extends Controller
{
    public function index()
    {
        $jobs = JobPosting::query()->where('is_active', true)->latest()->get();

        return view('careers.index', compact('jobs'));
    }

    public function apply(JobPosting $job)
    {
        abort_unless($job->is_active, 404);

        return view('careers.apply', compact('job'));
    }

    public function store(Request $request, JobPosting $job)
    {
        abort_unless($job->is_active, 404);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'sia_licence_number' => ['nullable', 'string', 'max:50'],
            'sia_expiry' => ['nullable', 'date'],
            'right_to_work_share_code' => ['nullable', 'string', 'max:50'],
            'passport' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'sia_licence' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'right_to_work_share_code_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'brp' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'work_permit' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'other' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $application = DB::transaction(function () use ($data, $request, $job) {
            $candidate = Candidate::query()->where('email', $data['email'])->first();
            if ($candidate) {
                $candidate->update([
                    'name' => $data['full_name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make($data['password']),
                ]);
            } else {
                $candidate = Candidate::create([
                    'name' => $data['full_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'password' => Hash::make($data['password']),
                ]);
            }

            $now = now()->toIso8601String();
            $application = Application::create([
                'candidate_id' => $candidate->id,
                'job_posting_id' => $job->id,
                'reference' => 'NX-'.Str::upper(Str::random(8)),
                'sia_licence_number' => $data['sia_licence_number'] ?? null,
                'sia_expiry' => $data['sia_expiry'] ?? null,
                'right_to_work_share_code' => $data['right_to_work_share_code'] ?? null,
                'status' => 'submitted',
                'status_history' => [['status' => 'submitted', 'at' => $now]],
            ]);

            $uploads = [
                'passport' => 'passport',
                'sia_licence' => 'sia_licence',
                'right_to_work_share_code_file' => 'right_to_work_share_code',
                'brp' => 'brp',
                'work_permit' => 'work_permit',
                'other' => 'other',
            ];

            foreach ($uploads as $field => $type) {
                if (! $request->hasFile($field)) {
                    continue;
                }
                $file = $request->file($field);
                $path = $file->store("applications/{$application->id}/{$type}", 'local');
                ApplicationDocument::create([
                    'application_id' => $application->id,
                    'type' => $type,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'status' => 'pending',
                ]);
            }

            Auth::guard('candidate')->login($candidate);

            return $application;
        });

        return redirect()
            ->route('candidate.dashboard')
            ->with('success', 'Application '.$application->reference.' submitted. Track your status below.');
    }
}
