<?php

namespace App\Http\Controllers;

use App\Models\ApplicationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CandidatePortalController extends Controller
{
    public function showLogin()
    {
        return view('candidate.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('candidate')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('candidate.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('candidate')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('candidate.login');
    }

    public function dashboard()
    {
        $candidate = Auth::guard('candidate')->user();
        $applications = $candidate->applications()
            ->with(['jobPosting', 'documents'])
            ->latest()
            ->get();

        return view('candidate.dashboard', compact('candidate', 'applications'));
    }

    public function reupload(Request $request, ApplicationDocument $document)
    {
        $candidate = Auth::guard('candidate')->user();
        abort_unless($document->application->candidate_id === $candidate->id, 403);
        abort_unless($document->status === 'rejected', 422);

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $file = $request->file('file');
        $path = $file->store(
            "applications/{$document->application_id}/{$document->type}",
            'local'
        );

        if ($document->file_path) {
            Storage::disk('local')->delete($document->file_path);
        }

        $document->update([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'status' => 'pending',
            'notes' => null,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ]);

        return back()->with('success', 'Document re-uploaded for review.');
    }
}
