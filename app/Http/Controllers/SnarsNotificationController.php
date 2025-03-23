<?php

namespace App\Http\Controllers;

use App\Models\SnarsNotification;
use App\Models\SnarsNotificationRecipient;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SnarsNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsNotification::with(['tenant', 'recipients', 'creator', 'updater']);

        // Apply filters
        if ($request->has('tenant_id')) {
            $query->forTenant($request->input('tenant_id'));
        }

        if ($request->has('notification_type')) {
            $query->ofType($request->input('notification_type'));
        }

        if ($request->has('priority')) {
            $query->withPriority($request->input('priority'));
        }

        if ($request->has('status')) {
            $query->withStatus($request->input('status'));
        }

        if ($request->has('is_active')) {
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);
            if ($isActive) {
                $query->active();
            } else {
                $query->expired();
            }
        }

        if ($request->has('is_scheduled')) {
            $isScheduled = filter_var($request->input('is_scheduled'), FILTER_VALIDATE_BOOLEAN);
            if ($isScheduled) {
                $query->scheduled();
            }
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('notification_type', 'like', "%{$search}%");
            });
        }

        // Sort results
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 15);
        $notifications = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $notifications,
                'message' => 'SNARS notifications retrieved successfully'
            ]);
        }

        return view('snars.notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $notificationTypes = [
            'system' => 'System Notification',
            'assessment' => 'Assessment Notification',
            'document' => 'Document Notification',
            'compliance' => 'Compliance Notification',
            'update' => 'Update Notification',
            'other' => 'Other'
        ];

        $priorities = [
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low'
        ];

        $statuses = [
            'pending' => 'Pending',
            'sent' => 'Sent',
            'cancelled' => 'Cancelled'
        ];

        $users = User::all();
        $departments = Department::all();

        return view('snars.notifications.create', compact(
            'notificationTypes', 
            'priorities', 
            'statuses', 
            'users', 
            'departments'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'tenant_id' => 'required|exists:tenants,id',
                'title' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
                'notification_type' => 'required|string|max:50',
                'source_type' => 'nullable|string|max:100',
                'source_id' => 'nullable|string|max:36',
                'priority' => 'required|string|in:high,medium,low',
                'status' => 'required|string|in:pending,sent,cancelled',
                'scheduled_at' => 'nullable|date',
                'expires_at' => 'nullable|date|after_or_equal:scheduled_at',
                'recipients' => 'required|array',
                'recipients.users' => 'nullable|array',
                'recipients.users.*' => 'exists:users,id',
                'recipients.departments' => 'nullable|array',
                'recipients.departments.*' => 'exists:departments,id',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            
            // Add user info
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $notification = SnarsNotification::create($data);

            // Process recipients
            if (isset($data['recipients']['users'])) {
                foreach ($data['recipients']['users'] as $userId) {
                    SnarsNotificationRecipient::create([
                        'notification_id' => $notification->id,
                        'user_id' => $userId,
                        'recipient_type' => 'user',
                        'status' => 'pending',
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            if (isset($data['recipients']['departments'])) {
                foreach ($data['recipients']['departments'] as $departmentId) {
                    SnarsNotificationRecipient::create([
                        'notification_id' => $notification->id,
                        'department_id' => $departmentId,
                        'recipient_type' => 'department',
                        'status' => 'pending',
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $notification->load('recipients'),
                    'message' => 'SNARS notification created successfully'
                ], 201);
            }

            return redirect()->route('snars.notifications.show', $notification->id)
                ->with('success', 'SNARS notification created successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to create SNARS notification',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create SNARS notification: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $notification = SnarsNotification::with(['tenant', 'recipients.user', 'recipients.department', 'creator', 'updater'])
                ->findOrFail($id);

            if (request()->expectsJson()) {
                return response()->json([
                    'data' => $notification,
                    'message' => 'SNARS notification retrieved successfully'
                ]);
            }

            return view('snars.notifications.show', compact('notification'));
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'SNARS notification not found',
                    'error' => $e->getMessage()
                ], 404);
            }

            return redirect()->route('snars.notifications.index')
                ->with('error', 'SNARS notification not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $notification = SnarsNotification::with(['recipients'])->findOrFail($id);
            
            $notificationTypes = [
                'system' => 'System Notification',
                'assessment' => 'Assessment Notification',
                'document' => 'Document Notification',
                'compliance' => 'Compliance Notification',
                'update' => 'Update Notification',
                'other' => 'Other'
            ];

            $priorities = [
                'high' => 'High',
                'medium' => 'Medium',
                'low' => 'Low'
            ];

            $statuses = [
                'pending' => 'Pending',
                'sent' => 'Sent',
                'cancelled' => 'Cancelled'
            ];

            $users = User::all();
            $departments = Department::all();

            // Get current recipients
            $selectedUsers = $notification->recipients()
                ->where('recipient_type', 'user')
                ->pluck('user_id')
                ->toArray();
                
            $selectedDepartments = $notification->recipients()
                ->where('recipient_type', 'department')
                ->pluck('department_id')
                ->toArray();

            return view('snars.notifications.edit', compact(
                'notification',
                'notificationTypes', 
                'priorities', 
                'statuses', 
                'users', 
                'departments',
                'selectedUsers',
                'selectedDepartments'
            ));
        } catch (\Exception $e) {
            return redirect()->route('snars.notifications.index')
                ->with('error', 'SNARS notification not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $notification = SnarsNotification::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'tenant_id' => 'required|exists:tenants,id',
                'title' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
                'notification_type' => 'required|string|max:50',
                'source_type' => 'nullable|string|max:100',
                'source_id' => 'nullable|string|max:36',
                'priority' => 'required|string|in:high,medium,low',
                'status' => 'required|string|in:pending,sent,cancelled',
                'scheduled_at' => 'nullable|date',
                'expires_at' => 'nullable|date|after_or_equal:scheduled_at',
                'recipients' => 'required|array',
                'recipients.users' => 'nullable|array',
                'recipients.users.*' => 'exists:users,id',
                'recipients.departments' => 'nullable|array',
                'recipients.departments.*' => 'exists:departments,id',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            
            // Update user info
            $data['updated_by'] = Auth::id();

            $notification->update($data);

            // Update recipients - first remove all existing recipients
            $notification->recipients()->delete();

            // Then add new recipients
            if (isset($data['recipients']['users'])) {
                foreach ($data['recipients']['users'] as $userId) {
                    SnarsNotificationRecipient::create([
                        'notification_id' => $notification->id,
                        'user_id' => $userId,
                        'recipient_type' => 'user',
                        'status' => 'pending',
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            if (isset($data['recipients']['departments'])) {
                foreach ($data['recipients']['departments'] as $departmentId) {
                    SnarsNotificationRecipient::create([
                        'notification_id' => $notification->id,
                        'department_id' => $departmentId,
                        'recipient_type' => 'department',
                        'status' => 'pending',
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $notification->load('recipients'),
                    'message' => 'SNARS notification updated successfully'
                ]);
            }

            return redirect()->route('snars.notifications.show', $notification->id)
                ->with('success', 'SNARS notification updated successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to update SNARS notification',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update SNARS notification: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            $notification = SnarsNotification::findOrFail($id);
            
            // Delete all recipients first
            $notification->recipients()->delete();
            
            // Then delete the notification
            $notification->delete();
            
            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'SNARS notification deleted successfully'
                ]);
            }

            return redirect()->route('snars.notifications.index')
                ->with('success', 'SNARS notification deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to delete SNARS notification',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->route('snars.notifications.index')
                ->with('error', 'Failed to delete SNARS notification: ' . $e->getMessage());
        }
    }

    /**
     * Mark a notification as read for a specific user.
     */
    public function markAsRead(string $id)
    {
        try {
            $userId = Auth::id();
            
            $recipient = SnarsNotificationRecipient::where('notification_id', $id)
                ->where('user_id', $userId)
                ->where('recipient_type', 'user')
                ->firstOrFail();
            
            $recipient->markAsRead();

            if (request()->expectsJson()) {
                return response()->json([
                    'data' => $recipient,
                    'message' => 'Notification marked as read'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Notification marked as read');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to mark notification as read',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to mark notification as read: ' . $e->getMessage());
        }
    }

    /**
     * Mark a notification as unread for a specific user.
     */
    public function markAsUnread(string $id)
    {
        try {
            $userId = Auth::id();
            
            $recipient = SnarsNotificationRecipient::where('notification_id', $id)
                ->where('user_id', $userId)
                ->where('recipient_type', 'user')
                ->firstOrFail();
            
            $recipient->markAsUnread();

            if (request()->expectsJson()) {
                return response()->json([
                    'data' => $recipient,
                    'message' => 'Notification marked as unread'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Notification marked as unread');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to mark notification as unread',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to mark notification as unread: ' . $e->getMessage());
        }
    }

    /**
     * Get all notifications for a specific user.
     */
    public function getUserNotifications(Request $request)
    {
        try {
            $userId = $request->input('user_id', Auth::id());
            $perPage = $request->input('per_page', 15);
            $onlyUnread = filter_var($request->input('only_unread', false), FILTER_VALIDATE_BOOLEAN);
            
            $query = SnarsNotificationRecipient::with(['notification'])
                ->where('user_id', $userId)
                ->where('recipient_type', 'user');
                
            if ($onlyUnread) {
                $query->unread();
            }
            
            $notifications = $query->paginate($perPage);

            return response()->json([
                'data' => $notifications,
                'message' => 'User notifications retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark all notifications as read for a specific user.
     */
    public function markAllAsRead(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $userId = $request->input('user_id', Auth::id());
            
            $recipients = SnarsNotificationRecipient::where('user_id', $userId)
                ->where('recipient_type', 'user')
                ->whereNull('read_at')
                ->get();
            
            foreach ($recipients as $recipient) {
                $recipient->markAsRead();
            }
            
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'All notifications marked as read',
                    'count' => $recipients->count()
                ]);
            }

            return redirect()->back()
                ->with('success', 'All notifications marked as read');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to mark all notifications as read',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to mark all notifications as read: ' . $e->getMessage());
        }
    }
}
