<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function notifications(Request $request, $company_id)
    {

        $cacheKey = "notifications:company:$company_id:status:{$request->status}";
        $notifications = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request, $company_id) {
            $query = Notification::where('company_id', $company_id);
            if ($request->status == 'unread') {
                $query->where('status', 'unread');
            }
            if (in_array($request->status, ['critical', 'warning', 'info'])) {
                $query->where('level', $request->status);
            }
            return $query->orderBy('created_at', 'desc')->paginate(20);
        });
        return response()->success($notifications);
    }

    public function countNotifications(Request $request, $company_id)
    {
        $cacheKey = "notifications.counts.company_id:" . $company_id;
        $counts = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($company_id) {
            return Notification::where('company_id', $company_id)
                ->selectRaw("
                    COUNT(*) as all,
                    COUNT(*) FILTER (WHERE level = 'critical') as critical,
                    COUNT(*) FILTER (WHERE level = 'warning') as warning,
                    COUNT(*) FILTER (WHERE level = 'info') as info,
                    COUNT(*) FILTER (WHERE status = 'unread') as unread
                ")->first()->toArray();
        });
        return response()->success($counts);
    }

    public function markAsRead($company_id, $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['status' => 'read']);
        Cache::forget("notifications:company:$company_id:status:{$notification->status}");
        Cache::forget("notifications.counts.company_id:" .$company_id);
        return response()->success($notification);
    }
}
