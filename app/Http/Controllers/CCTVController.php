<?php

namespace App\Http\Controllers;

use App\Models\CCTV;
use App\Models\CCTVLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CCTVController extends Controller
{
    public function index()
    {
        $cameras = CCTV::all();
        $recentLogs = CCTVLog::with('cctv', 'user')
            ->orderBy('timestamp', 'desc')
            ->limit(20)
            ->get();
            
        return view('cctv.index', compact('cameras', 'recentLogs'));
    }

    public function cameras()
    {
        $cameras = CCTV::paginate(10);
        return view('cctv.cameras', compact('cameras'));
    }

    public function logs(Request $request)
    {
        $query = CCTVLog::with('cctv', 'user');
        
        if ($request->get('cctv_id')) {
            $query->where('cctv_id', $request->cctv_id);
        }
        
        if ($request->get('event_type')) {
            $query->where('event_type', $request->event_type);
        }
        
        if ($request->get('start_date')) {
            $query->whereDate('timestamp', '>=', $request->start_date);
        }
        
        if ($request->get('end_date')) {
            $query->whereDate('timestamp', '<=', $request->end_date);
        }
        
        $logs = $query->orderBy('timestamp', 'desc')->paginate(50);
        $cameras = CCTV::all();
        
        return view('cctv.logs', compact('logs', 'cameras'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'camera_name' => 'required|string|max:255',
            'camera_ip' => 'nullable|ip',
            'camera_location' => 'required|string',
            'stream_url' => 'nullable|url',
            'is_active' => 'boolean',
            'recording_enabled' => 'boolean',
            'motion_detection' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        CCTV::create($request->all());
        return redirect()->route('cctv.index')
            ->with('success', 'Camera added successfully!');
    }

    public function edit(CCTV $cctv)
    {
        return view('cctv.edit', compact('cctv'));
    }

    public function update(Request $request, CCTV $cctv)
    {
        $validator = Validator::make($request->all(), [
            'camera_name' => 'required|string|max:255',
            'camera_ip' => 'nullable|ip',
            'camera_location' => 'required|string',
            'stream_url' => 'nullable|url',
            'is_active' => 'boolean',
            'recording_enabled' => 'boolean',
            'motion_detection' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cctv->update($request->all());
        
        return redirect()->route('cctv.index')
            ->with('success', 'Camera updated successfully!');
    }

    public function destroy(CCTV $cctv)
    {
        $cctv->delete();
        return redirect()->route('cctv.index')
            ->with('success', 'Camera deleted successfully!');
    }

    public function stream(CCTV $cctv)
    {
        return view('cctv.stream', compact('cctv'));
    }

    public function logEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cctv_id' => 'required|exists:cctvs,id',
            'event_type' => 'required|string',
            'event_data' => 'nullable|array',
            'screenshot_path' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $log = CCTVLog::create([
            'cctv_id' => $request->cctv_id,
            'event_type' => $request->event_type,
            'event_data' => $request->event_data,
            'timestamp' => now(),
            'user_id' => auth()->id(),
            'screenshot_path' => $request->screenshot_path,
        ]);

        return response()->json(['success' => true, 'log' => $log]);
    }
}