<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = DB::table('sessions')->get();
        
        // Enrich sessions with user info
        $enrichedSessions = $sessions->map(function ($session) {
            $data = json_decode($session->payload, true);
            
            return (object) [
                'id' => $session->id,
                'user_id' => $data['login_web'] ?? null,
                'ip_address' => $session->ip_address,
                'user_agent' => $session->user_agent,
                'last_activity' => $session->last_activity,
                'is_current' => $session->id === session()->getId(),
            ];
        })->filter(fn($s) => $s->user_id !== null);

        // Get user details for each session
        $enrichedSessions = $enrichedSessions->map(function ($session) {
            $user = User::find($session->user_id);
            return (object) [
                ...(array) $session,
                'user' => $user,
                'browser' => $this->parseBrowser($session->user_agent),
                'device' => $this->parseDevice($session->user_agent),
                'last_activity_ago' => $this->timeAgo($session->last_activity),
            ];
        });

        return view('admin.sessions.index', compact('enrichedSessions'));
    }

    public function destroy($sessionId)
    {
        DB::table('sessions')->where('id', $sessionId)->delete();

        return redirect()->back()->with('success', 'Session terminated successfully.');
    }

    public function destroyAll(Request $request)
    {
        // Delete all sessions except the current one
        DB::table('sessions')
            ->where('id', '!=', session()->getId())
            ->delete();

        return redirect()->back()->with('success', 'All other sessions have been terminated.');
    }

    private function parseBrowser($userAgent)
    {
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'Opera') !== false) {
            return 'Opera';
        }
        return 'Unknown';
    }

    private function parseDevice($userAgent)
    {
        if (strpos($userAgent, 'Mobile') !== false) {
            return 'Mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            return 'Tablet';
        }
        return 'Desktop';
    }

    private function timeAgo($timestamp)
    {
        $now = time();
        $diff = $now - $timestamp;

        if ($diff < 60) {
            return 'now';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . 'm ago';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . 'h ago';
        } else {
            return floor($diff / 86400) . 'd ago';
        }
    }
}
