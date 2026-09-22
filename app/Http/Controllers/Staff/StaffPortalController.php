<?php

namespace App\Http\Controllers\Staff;

use App\Actions\ProcessAttendancePunch;
use App\Http\Controllers\Controller;
use App\Models\AttendancePunch;
use App\Models\Guard;
use App\Models\Incident;
use App\Models\LeaveRequest;
use App\Models\Policy;
use App\Models\PolicyAcknowledgement;
use App\Models\Shift;
use App\Models\StaffConversation;
use App\Models\StaffMessage;
use App\Models\StaffNotification;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffPortalController extends Controller
{
    private function guard(): Guard
    {
        /** @var Guard $guard */
        $guard = Auth::guard('staff')->user();

        return $guard;
    }

    public function home()
    {
        $guard = $this->guard();

        $upcoming = $guard->shifts()
            ->with('site')
            ->where('status', 'published')
            ->where('ends_at', '>=', now()->subHours(2))
            ->orderBy('starts_at')
            ->limit(5)
            ->get();

        $activeShift = $upcoming->first(function (Shift $shift) {
            return $shift->starts_at->lte(now()->addMinutes((int) config('staff.clock_early_minutes')))
                && $shift->ends_at->gte(now()->subMinutes((int) config('staff.clock_grace_minutes')));
        }) ?? $upcoming->first();

        if ($activeShift) {
            $activeShift->load(['clockInPunch', 'clockOutPunch', 'site']);
        }

        $weekHours = (float) Timesheet::query()
            ->where('guard_id', $guard->id)
            ->where('submitted_at', '>=', now()->startOfWeek())
            ->sum('hours');

        $unread = StaffNotification::query()
            ->where('guard_id', $guard->id)
            ->whereNull('read_at')
            ->count();

        return view('staff.home', compact('guard', 'upcoming', 'activeShift', 'weekHours', 'unread'));
    }

    public function schedule()
    {
        $guard = $this->guard();

        $shifts = $guard->shifts()
            ->with(['site', 'clockInPunch', 'clockOutPunch'])
            ->where('starts_at', '>=', now()->subDays(7))
            ->orderBy('starts_at')
            ->limit(60)
            ->get();

        return view('staff.schedule', compact('guard', 'shifts'));
    }

    public function clock(Request $request, Shift $shift, ProcessAttendancePunch $processAttendancePunch)
    {
        $guard = $this->guard();
        abort_unless((int) $shift->guard_id === (int) $guard->id, 403);

        $validated = $request->validate([
            'type' => ['required', Rule::in([AttendancePunch::TYPE_CLOCK_IN, AttendancePunch::TYPE_CLOCK_OUT])],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $processAttendancePunch($guard, $shift, $validated['type'], [
                'lat' => (float) $validated['lat'],
                'lng' => (float) $validated['lng'],
                'accuracy' => isset($validated['accuracy']) ? (float) $validated['accuracy'] : null,
                'device_meta' => [
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    'ip' => $request->ip(),
                ],
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['clock' => $e->getMessage()]);
        }

        $label = $validated['type'] === AttendancePunch::TYPE_CLOCK_IN ? 'Clocked in' : 'Clocked out';

        return back()->with('success', $label.' successfully.');
    }

    public function hours()
    {
        $guard = $this->guard();

        $timesheets = Timesheet::query()
            ->with(['site', 'shift'])
            ->where('guard_id', $guard->id)
            ->latest('submitted_at')
            ->paginate(20);

        $totalHours = (float) Timesheet::query()->where('guard_id', $guard->id)->sum('hours');
        $payEstimate = (float) Timesheet::query()
            ->where('guard_id', $guard->id)
            ->selectRaw('COALESCE(SUM(hours * hourly_rate), 0) as total')
            ->value('total');

        return view('staff.hours', compact('guard', 'timesheets', 'totalHours', 'payEstimate'));
    }

    public function hoursExport(): StreamedResponse
    {
        $guard = $this->guard();

        $filename = 'norix-hours-'.$guard->id.'-'.now()->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($guard) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Submitted', 'Site', 'Hours', 'Rate', 'Estimate', 'Status']);

            Timesheet::query()
                ->with('site')
                ->where('guard_id', $guard->id)
                ->orderByDesc('submitted_at')
                ->chunk(100, function ($rows) use ($out) {
                    foreach ($rows as $row) {
                        fputcsv($out, [
                            optional($row->submitted_at)?->toDateTimeString(),
                            $row->site?->name,
                            $row->hours,
                            $row->hourly_rate,
                            round((float) $row->hours * (float) $row->hourly_rate, 2),
                            $row->status,
                        ]);
                    }
                });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function analytics()
    {
        $guard = $this->guard();

        $weekHours = (float) Timesheet::query()
            ->where('guard_id', $guard->id)
            ->where('submitted_at', '>=', now()->startOfWeek())
            ->sum('hours');

        $monthHours = (float) Timesheet::query()
            ->where('guard_id', $guard->id)
            ->where('submitted_at', '>=', now()->startOfMonth())
            ->sum('hours');

        $sitesWorked = Timesheet::query()
            ->where('guard_id', $guard->id)
            ->distinct('site_id')
            ->count('site_id');

        $shifts = Shift::query()
            ->with(['clockInPunch', 'site'])
            ->where('guard_id', $guard->id)
            ->where('status', 'completed')
            ->where('starts_at', '>=', now()->subDays(90))
            ->get();

        $onTime = 0;
        $late = 0;
        $early = 0;

        foreach ($shifts as $shift) {
            $punch = $shift->clockInPunch;
            if ($punch === null) {
                continue;
            }
            if ($punch->punched_at->betweenIncluded(
                $shift->starts_at->copy()->subMinutes(5),
                $shift->starts_at->copy()->addMinutes(5),
            )) {
                $onTime++;
            } elseif ($punch->punched_at->gt($shift->starts_at->copy()->addMinutes(5))) {
                $late++;
            } else {
                $early++;
            }
        }

        $totalTimed = max(1, $onTime + $late + $early);
        $onTimePct = round(($onTime / $totalTimed) * 100);

        return view('staff.analytics', compact(
            'guard',
            'weekHours',
            'monthHours',
            'sitesWorked',
            'onTime',
            'late',
            'early',
            'onTimePct',
        ));
    }

    public function incidents()
    {
        $guard = $this->guard();

        $incidents = Incident::query()
            ->with('site')
            ->where('guard_id', $guard->id)
            ->latest()
            ->limit(30)
            ->get();

        $shifts = $guard->shifts()
            ->with('site')
            ->where('starts_at', '>=', now()->subDays(14))
            ->orderByDesc('starts_at')
            ->limit(20)
            ->get();

        return view('staff.incidents', compact('guard', 'incidents', 'shifts'));
    }

    public function storeIncident(Request $request)
    {
        $guard = $this->guard();

        $validated = $request->validate([
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:5000'],
            'severity' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'photo' => ['nullable', 'image', 'max:10240'],
        ]);

        $siteId = null;
        $shiftId = $validated['shift_id'] ?? null;

        if ($shiftId) {
            $shift = Shift::query()->findOrFail($shiftId);
            abort_unless((int) $shift->guard_id === (int) $guard->id, 403);
            $siteId = $shift->site_id;
        } else {
            $siteId = $guard->shifts()->latest('starts_at')->value('site_id');
        }

        abort_if($siteId === null, 422, 'No site available for this report.');

        $paths = [];
        if ($request->hasFile('photo')) {
            $paths[] = $request->file('photo')->store("incidents/{$guard->id}", 'local');
        }

        Incident::query()->create([
            'site_id' => $siteId,
            'shift_id' => $shiftId,
            'guard_id' => $guard->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'severity' => $validated['severity'],
            'attachment_paths' => $paths ?: null,
            'created_by' => null,
        ]);

        return back()->with('success', 'Incident reported to operations.');
    }

    public function messages()
    {
        $guard = $this->guard();

        $conversation = StaffConversation::query()
            ->firstOrCreate(
                ['guard_id' => $guard->id],
                ['subject' => 'Operations']
            );

        $messages = $conversation->messages()->with('sender')->get();

        StaffMessage::query()
            ->where('staff_conversation_id', $conversation->id)
            ->where('sender_type', '!=', Guard::class)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('staff.messages', compact('guard', 'conversation', 'messages'));
    }

    public function storeMessage(Request $request)
    {
        $guard = $this->guard();

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);

        $conversation = StaffConversation::query()
            ->firstOrCreate(
                ['guard_id' => $guard->id],
                ['subject' => 'Operations']
            );

        $conversation->messages()->create([
            'sender_type' => Guard::class,
            'sender_id' => $guard->id,
            'body' => $validated['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        return back()->with('success', 'Message sent.');
    }

    public function leave()
    {
        $guard = $this->guard();

        $requests = $guard->leaveRequests()->latest()->limit(30)->get();

        return view('staff.leave', compact('guard', 'requests'));
    }

    public function storeLeave(Request $request)
    {
        $guard = $this->guard();

        $validated = $request->validate([
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $clashes = $guard->shifts()
            ->where('status', 'published')
            ->whereDate('starts_at', '<=', $validated['ends_on'])
            ->whereDate('ends_at', '>=', $validated['starts_on'])
            ->count();

        LeaveRequest::query()->create([
            'guard_id' => $guard->id,
            'starts_on' => $validated['starts_on'],
            'ends_on' => $validated['ends_on'],
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
        ]);

        $msg = 'Leave request submitted.';
        if ($clashes > 0) {
            $msg .= " Note: you have {$clashes} published shift(s) in that window — ops will review.";
        }

        return back()->with('success', $msg);
    }

    public function alerts()
    {
        $guard = $this->guard();

        $notifications = StaffNotification::query()
            ->where('guard_id', $guard->id)
            ->latest()
            ->limit(50)
            ->get();

        StaffNotification::query()
            ->where('guard_id', $guard->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('staff.alerts', compact('guard', 'notifications'));
    }

    public function profile()
    {
        $guard = $this->guard();
        $policies = Policy::query()->orderBy('title')->get();
        $acks = PolicyAcknowledgement::query()
            ->where('guard_id', $guard->id)
            ->pluck('acknowledged_at', 'policy_id');

        return view('staff.profile', compact('guard', 'policies', 'acks'));
    }

    public function updatePassword(Request $request)
    {
        $guard = $this->guard();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password:staff'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $guard->forceFill([
            'password' => $validated['password'],
            'must_set_password' => false,
        ])->save();

        return back()->with('success', 'Password updated.');
    }

    public function acknowledgePolicy(Policy $policy)
    {
        $guard = $this->guard();

        PolicyAcknowledgement::query()->updateOrCreate(
            ['guard_id' => $guard->id, 'policy_id' => $policy->id],
            ['acknowledged_at' => now()],
        );

        return back()->with('success', 'Policy acknowledged.');
    }
}
