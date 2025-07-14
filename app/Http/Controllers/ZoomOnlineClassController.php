<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\SessionYear;
use App\Models\Subject;
use App\Models\ZoomOnlineClass;
use App\Models\ZoomSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZoomOnlineClassController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = ZoomOnlineClass::with(['teacher', 'classSection', 'subject'])
            ->where('school_id', $user->school_id);

        // If teacher, only show their classes
        if ($user->hasRole('Teacher')) {
            $query->where('teacher_id', $user->id);
        }

        $classes = $query->orderBy('start_time', 'desc')->paginate(10);
        return view('zoom.classes.index', compact('classes'));
    }

    public function create()
    {
        $user = Auth::user();
        $settings = ZoomSetting::where('school_id', $user->school_id)->first();

        if (!$settings || !$settings->is_active) {
            return redirect()->route('zoom.settings.index')
                ->with('error', 'Please configure Zoom settings first.');
        }

        $classSections = ClassSection::where('school_id', $user->school_id)->get();
        $subjects = Subject::where('school_id', $user->school_id)->get();
        $sessionYear = SessionYear::where('school_id', $user->school_id)
            ->where('is_active', 1)
            ->first();

        return view('zoom.classes.create', compact('classSections', 'subjects', 'sessionYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'start_time' => 'required|date',
            'duration' => 'required|integer|min:15|max:300',
            'is_recurring' => 'boolean',
            'recurrence_type' => 'required_if:is_recurring,true|in:daily,weekly,monthly',
            'recurring_interval' => 'required_if:is_recurring,true|integer|min:1|max:90'
        ]);

        $user = Auth::user();
        $settings = ZoomSetting::where('school_id', $user->school_id)->first();

        if (!$settings || !$settings->is_active) {
            return redirect()->back()
                ->with('error', 'Please configure Zoom settings first.')
                ->withInput();
        }

        // Here you would integrate with Zoom API to create the meeting
        // For now, we'll just create a dummy meeting
        $class = new ZoomOnlineClass();
        $class->school_id = $user->school_id;
        $class->teacher_id = $user->id;
        $class->class_section_id = $request->class_section_id;
        $class->subject_id = $request->subject_id;
        $class->title = $request->title;
        $class->description = $request->description;
        $class->start_time = $request->start_time;
        $class->duration = $request->duration;
        $class->end_time = \Carbon\Carbon::parse($request->start_time)->addMinutes($request->duration);
        $class->is_recurring = $request->is_recurring ?? false;
        $class->recurrence_type = $request->recurrence_type;
        $class->recurring_interval = $request->recurring_interval;
        $class->meeting_id = 'dummy_' . time();
        $class->join_url = 'https://zoom.us/j/dummy';
        $class->start_url = 'https://zoom.us/s/dummy';
        $class->session_year_id = SessionYear::where('school_id', $user->school_id)
            ->where('is_active', 1)
            ->first()
            ->id;
        $class->save();

        return redirect()->route('zoom.classes.index')
            ->with('success', 'Online class created successfully.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $class = ZoomOnlineClass::where('school_id', $user->school_id)
            ->findOrFail($id);

        if ($user->hasRole('Teacher') && $class->teacher_id !== $user->id) {
            abort(403);
        }

        $classSections = ClassSection::where('school_id', $user->school_id)->get();
        $subjects = Subject::where('school_id', $user->school_id)->get();

        return view('zoom.classes.edit', compact('class', 'classSections', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'start_time' => 'required|date',
            'duration' => 'required|integer|min:15|max:300',
            'is_recurring' => 'boolean',
            'recurrence_type' => 'required_if:is_recurring,true|in:daily,weekly,monthly',
            'recurring_interval' => 'required_if:is_recurring,true|integer|min:1|max:90'
        ]);

        $user = Auth::user();
        $class = ZoomOnlineClass::where('school_id', $user->school_id)
            ->findOrFail($id);

        if ($user->hasRole('Teacher') && $class->teacher_id !== $user->id) {
            abort(403);
        }

        // Here you would update the meeting in Zoom API
        $class->update([
            'title' => $request->title,
            'description' => $request->description,
            'class_section_id' => $request->class_section_id,
            'subject_id' => $request->subject_id,
            'start_time' => $request->start_time,
            'duration' => $request->duration,
            'end_time' => \Carbon\Carbon::parse($request->start_time)->addMinutes($request->duration),
            'is_recurring' => $request->is_recurring ?? false,
            'recurrence_type' => $request->recurrence_type,
            'recurring_interval' => $request->recurring_interval
        ]);

        return redirect()->route('zoom.classes.index')
            ->with('success', 'Online class updated successfully.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $class = ZoomOnlineClass::where('school_id', $user->school_id)
            ->findOrFail($id);

        if ($user->hasRole('Teacher') && $class->teacher_id !== $user->id) {
            abort(403);
        }

        // Here you would delete the meeting from Zoom API
        $class->delete();

        return redirect()->route('zoom.classes.index')
            ->with('success', 'Online class deleted successfully.');
    }
} 