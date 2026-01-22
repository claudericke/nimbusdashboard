<?php

class ActivityController extends BaseController
{
    private $activityLog;

    public function __construct()
    {
        $this->activityLog = new ActivityLog();
    }

    public function getRecent()
    {
        if (!Session::isLoggedIn()) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $logs = $this->activityLog->getRecent(5);

        // Format relative time (e.g., "2 mins ago")
        foreach ($logs as &$log) {
            $time = strtotime($log['created_at']);
            $log['time_ago'] = $this->timeElapsedString($time);

            // Assign icon/color based on action type
            switch ($log['action_type']) {
                case 'security':
                    $log['icon'] = 'fa-shield-alt';
                    $log['bg_class'] = 'bg-vibrant-rose';
                    break;
                case 'settings':
                    $log['icon'] = 'fa-cog';
                    $log['bg_class'] = 'bg-vibrant-indigo';
                    break;
                case 'email':
                    $log['icon'] = 'fa-envelope';
                    $log['bg_class'] = 'bg-vibrant-emerald';
                    break;
                case 'user':
                    $log['icon'] = 'fa-user-plus';
                    $log['bg_class'] = 'bg-vibrant-cyan'; // Ensure this class exists or fallback
                    break;
                default:
                    $log['icon'] = 'fa-info';
                    $log['bg_class'] = 'bg-dark';
            }
        }

        echo json_encode(['logs' => $logs]);
        exit;
    }

    private function timeElapsedString($ptime)
    {
        $etime = time() - $ptime;

        if ($etime < 1) {
            return 'just now';
        }

        $a = array(
            365 * 24 * 60 * 60 => 'year',
            30 * 24 * 60 * 60 => 'month',
            24 * 60 * 60 => 'day',
            60 * 60 => 'hour',
            60 => 'min',
            1 => 'sec'
        );
        $a_plural = array(
            'year' => 'years',
            'month' => 'months',
            'day' => 'days',
            'hour' => 'hours',
            'min' => 'mins',
            'sec' => 'secs'
        );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
            }
        }
    }
}
