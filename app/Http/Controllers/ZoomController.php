<?php

namespace App\Http\Controllers;

use App\Models\ZoomOnlineClass;
use App\Repositories\ClassSection\ClassSectionInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Repositories\Subject\SubjectInterface;
use App\Repositories\ZoomOnlineClass\ZoomOnlineClassInterface;
use App\Repositories\ZoomSetting\ZoomSettingInterface;
use App\Repositories\ZoomAttendance\ZoomAttendanceInterface;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ZoomController extends Controller
{
    private ZoomOnlineClassInterface $zoomClass;
    private ZoomAttendanceInterface $zoomAttendance;
    private ZoomSettingInterface $zoomSetting;
    private ZoomService $zoomService;
    private ClassSectionInterface $classSection;
    private SubjectInterface $subject;
    private SessionYearInterface $sessionYear;

    public function __construct(
        ZoomOnlineClassInterface $zoomClass,
        ZoomAttendanceInterface $zoomAttendance,
        ZoomSettingInterface $zoomSetting,
        ZoomService $zoomService,
        ClassSectionInterface $classSection,
        SubjectInterface $subject,
        SessionYearInterface $sessionYear
    ) {
        $this->zoomClass = $zoomClass;
        $this->zoomAttendance = $zoomAttendance;
        $this->zoomSetting = $zoomSetting;
        $this->zoomService = $zoomService;
        $this->classSection = $classSection;
        $this->subject = $subject;
        $this->sessionYear = $sessionYear;
    }

    /**
     * Display the online classes list.
     */
    public function index()
    {
        ResponseService::noPermissionThenRedirect('zoom-class-list');
        
        $classSections = $this->classSection->builder()->with('class', 'section', 'medium')->get();
        $subjects = $this->subject->all();
        $sessionYears = $this->sessionYear->all();
        
        return view('zoom.index', compact('classSections', 'subjects', 'sessionYears'));
    }

    /**
     * Show the list of online classes.
     */
    public function show(Request $request)
    {
        ResponseService::noPermissionThenRedirect('zoom-class-list');
        
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = $request->search;
        
        $sql = $this->zoomClass->builder()
            ->with('teacher', 'classSection.class', 'classSection.section', 'subject');
            
        if (!empty($search)) {
            $sql = $sql->where(function($query) use ($search) {
                $query->where('title', 'LIKE', "%$search%")
                    ->orWhere('meeting_id', 'LIKE', "%$search%");
            });
        }
        
        if ($request->has('class_section_id') && !empty($request->class_section_id)) {
            $sql = $sql->where('class_section_id', $request->class_section_id);
        }
        
        if ($request->has('subject_id') && !empty($request->subject_id)) {
            $sql = $sql->where('subject_id', $request->subject_id);
        }
        
        if ($request->has('session_year_id') && !empty($request->session_year_id)) {
            $sql = $sql->where('session_year_id', $request->session_year_id);
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $sql = $sql->where('status', $request->status);
        }
        
        $total = $sql->count();
        
        $sql = $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();
        
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        
        foreach ($res as $row) {
            $operate = '';
            
            // Check if user has edit permission or is a teacher
            $canEdit = ResponseService::noPermissionThenSendJson('zoom-class-edit') || ResponseService::noRoleThenRedirect('Teacher');
            if ($canEdit) {
                $operate .= '<a href="' . route('zoom.edit', $row->id) . '" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id="' . $row->id . '" title="Edit"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            }
            
            // Check if user has delete permission or is a teacher
            $canDelete = ResponseService::noPermissionThenSendJson('zoom-class-delete') || ResponseService::noRoleThenRedirect('Teacher');
            if ($canDelete) {
                $operate .= '<a href="' . route('zoom.destroy', $row->id) . '" class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" data-id="' . $row->id . '" title="Delete"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;';
            }
            
            $start = '<strong>' . date('d-m-Y h:i A', strtotime($row->start_time)) . '</strong>';
            
            $tempRow = array(
                'id' => $row->id,
                'no' => $no++,
                'title' => $row->title,
                'class_section' => $row->classSection ? $row->classSection->class->name . ' ' . $row->classSection->section->name : 'N/A',
                'subject' => $row->subject ? $row->subject->name : 'N/A',
                'teacher' => $row->teacher->full_name,
                'start_time' => $start,
                'end_time' => date('d-m-Y h:i A', strtotime($row->end_time)),
                'meeting_id' => $row->meeting_id,
                'status' => $row->status,
                'join_url' => '<a href="' . $row->join_url . '" target="_blank" class="btn btn-success">Join Meeting</a>',
                'start_url' => Auth::user()->id == $row->teacher_id ? '<a href="' . $row->start_url . '" target="_blank" class="btn btn-primary">Start Meeting</a>' : '',
                'operate' => $operate
            );
            
            $rows[] = $tempRow;
        }
        
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    /**
     * Show the form for creating a new online class.
     */
    public function create()
    {
        ResponseService::noPermissionThenRedirect('zoom-class-create');
        
        // Check if Zoom settings are configured
        $settings = $this->zoomSetting->builder()->owner()->first();
        if (!$settings || !$settings->is_active) {
            ResponseService::errorResponse('Zoom API settings are not configured. Please configure them first.');
        }
        
        $classSections = $this->classSection->builder()->with('class', 'section', 'medium')->get();
        $subjects = $this->subject->all();
        $sessionYears = $this->sessionYear->all();
        $defaultSessionYear = $this->sessionYear->builder()->where('default', 1)->first();
        
        return view('zoom.create', compact('classSections', 'subjects', 'sessionYears', 'defaultSessionYear'));
    }

    /**
     * Store a newly created online class.
     */
    public function store(Request $request)
    {
        ResponseService::noPermissionThenRedirect('zoom-class-create');
        
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'class_section_id' => 'required',
            'subject_id' => 'required',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'description' => 'nullable',
            'session_year_id' => 'required',
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }

        try {
            // Calculate duration in minutes
            $startTime = \Carbon\Carbon::parse($request->start_time);
            $endTime = \Carbon\Carbon::parse($request->end_time);
            $durationMinutes = $endTime->diffInMinutes($startTime);
            
            // Create Zoom meeting
            $zoomMeetingData = [
                'topic' => $request->title,
                'type' => 2, // Scheduled meeting
                'start_time' => $startTime->format('Y-m-d\TH:i:s'),
                'duration' => $durationMinutes,
                'timezone' => config('app.timezone'),
                'agenda' => $request->description,
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => true,
                    'mute_upon_entry' => true,
                    'waiting_room' => false,
                    'audio' => 'both',
                    'auto_recording' => 'none'
                ]
            ];
            
            $response = $this->zoomService->createMeeting($zoomMeetingData);
            
            if (!$response) {
                ResponseService::errorResponse('Failed to create Zoom meeting. Please check your Zoom API settings.');
            }
            
            // Save to database
            $data = [
                'teacher_id' => Auth::id(),
                'title' => $request->title,
                'class_section_id' => $request->class_section_id,
                'subject_id' => $request->subject_id,
                'description' => $request->description,
                'meeting_id' => $response['id'],
                'password' => $response['password'],
                'join_url' => $response['join_url'],
                'start_url' => $response['start_url'],
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration' => $durationMinutes,
                'session_year_id' => $request->session_year_id,
                'status' => 'scheduled',
            ];
            
            $this->zoomClass->create($data);
            
            ResponseService::successResponse('Online class created successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "ZoomController -> Store Method");
            ResponseService::errorResponse('Error creating online class: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing an online class.
     */
    public function edit($id)
    {
        ResponseService::noPermissionThenRedirect('zoom-class-edit');
        
        $onlineClass = $this->zoomClass->findById($id);
        
        // Only the teacher who created the class or a school admin can edit it
        if (Auth::id() != $onlineClass->teacher_id && !ResponseService::noRoleThenRedirect('School Admin')) {
            ResponseService::errorResponse('You do not have permission to edit this online class.');
        }
        
        $classSections = $this->classSection->builder()->with('class', 'section', 'medium')->get();
        $subjects = $this->subject->all();
        $sessionYears = $this->sessionYear->all();
        
        return view('zoom.edit', compact('onlineClass', 'classSections', 'subjects', 'sessionYears'));
    }

    /**
     * Update the specified online class.
     */
    public function update(Request $request, $id)
    {
        ResponseService::noPermissionThenRedirect('zoom-class-edit');
        
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'class_section_id' => 'required',
            'subject_id' => 'required',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'description' => 'nullable',
            'session_year_id' => 'required',
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }

        try {
            $onlineClass = $this->zoomClass->findById($id);
            
            // Only the teacher who created the class or a school admin can update it
            if (Auth::id() != $onlineClass->teacher_id && !ResponseService::noRoleThenRedirect('School Admin')) {
                ResponseService::errorResponse('You do not have permission to update this online class.');
            }
            
            // Calculate duration in minutes
            $startTime = \Carbon\Carbon::parse($request->start_time);
            $endTime = \Carbon\Carbon::parse($request->end_time);
            $durationMinutes = $endTime->diffInMinutes($startTime);
            
            // Update Zoom meeting
            $zoomMeetingData = [
                'topic' => $request->title,
                'type' => 2, // Scheduled meeting
                'start_time' => $startTime->format('Y-m-d\TH:i:s'),
                'duration' => $durationMinutes,
                'timezone' => config('app.timezone'),
                'agenda' => $request->description,
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => true,
                    'mute_upon_entry' => true,
                    'waiting_room' => false,
                    'audio' => 'both',
                    'auto_recording' => 'none'
                ]
            ];
            
            $response = $this->zoomService->updateMeeting($onlineClass->meeting_id, $zoomMeetingData);
            
            if (!$response && $response !== []) {
                ResponseService::errorResponse('Failed to update Zoom meeting. Please check your Zoom API settings.');
            }
            
            // Update database
            $data = [
                'title' => $request->title,
                'class_section_id' => $request->class_section_id,
                'subject_id' => $request->subject_id,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration' => $durationMinutes,
                'session_year_id' => $request->session_year_id,
            ];
            
            $this->zoomClass->update($id, $data);
            
            ResponseService::successResponse('Online class updated successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "ZoomController -> Update Method");
            ResponseService::errorResponse('Error updating online class: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified online class.
     */
    public function destroy($id)
    {
        ResponseService::noPermissionThenRedirect('zoom-class-delete');
        
        try {
            $onlineClass = $this->zoomClass->findById($id);
            
            // Only the teacher who created the class or a school admin can delete it
            if (Auth::id() != $onlineClass->teacher_id && !ResponseService::noRoleThenRedirect('School Admin')) {
                ResponseService::errorResponse('You do not have permission to delete this online class.');
            }
            
            // Delete from Zoom
            $this->zoomService->deleteMeeting($onlineClass->meeting_id);
            
            // Delete from database
            $this->zoomClass->deleteById($id);
            
            ResponseService::successResponse('Online class deleted successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "ZoomController -> Destroy Method");
            ResponseService::errorResponse('Error deleting online class: ' . $e->getMessage());
        }
    }

    /**
     * Display attendance for an online class.
     */
    public function attendance($id)
    {
        ResponseService::noPermissionThenRedirect('zoom-attendance');
        
        $onlineClass = $this->zoomClass->findById($id, ['*'], ['attendances.student']);
        
        // Only the teacher who created the class or a school admin can view attendance
        if (Auth::id() != $onlineClass->teacher_id && !ResponseService::noRoleThenRedirect('School Admin')) {
            ResponseService::errorResponse('You do not have permission to view attendance for this class.');
        }
        
        // Get meeting participants from Zoom API
        $participants = $this->zoomService->getMeetingParticipants($onlineClass->meeting_id);
        
        return view('zoom.attendance', compact('onlineClass', 'participants'));
    }
}
