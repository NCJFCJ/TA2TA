<?php

namespace IAWP\Models;

/** @internal */
trait View_Stats
{
    protected $views;
    protected $previous_period_views;
    protected $views_growth;
    protected $views_per_session;
    protected $visitors;
    protected $previous_period_visitors;
    protected $visitors_growth;
    protected $sessions;
    protected $previous_period_sessions;
    protected $average_session_duration;
    protected $previous_period_average_session_duration;
    protected $average_view_duration;
    protected $previous_period_average_view_duration;
    protected $bounces;
    protected $bounce_rate;
    public function views()
    {
        return $this->views;
    }
    public function previous_period_views()
    {
        return $this->previous_period_views;
    }
    public function views_growth()
    {
        return $this->views_growth;
    }
    public function visitors()
    {
        return $this->visitors;
    }
    public function previous_period_visitors()
    {
        return $this->previous_period_visitors;
    }
    public function visitors_growth()
    {
        return $this->visitors_growth;
    }
    public function sessions()
    {
        return $this->sessions;
    }
    public function previous_period_sessions()
    {
        return $this->previous_period_sessions;
    }
    public function sessions_growth()
    {
        $current = $this->sessions();
        $previous = $this->previous_period_sessions();
        if ($current == 0 || $previous == 0) {
            return 0;
        } else {
            return ($current - $previous) / $previous * 100;
        }
    }
    public function average_session_duration()
    {
        return $this->average_session_duration;
    }
    public function previous_period_average_session_duration()
    {
        return $this->previous_period_average_session_duration;
    }
    public function average_session_duration_growth()
    {
        $current = $this->average_session_duration();
        $previous = $this->previous_period_average_session_duration();
        if ($current == 0 || $previous == 0) {
            return 0;
        } else {
            return ($current - $previous) / $previous * 100;
        }
    }
    public function average_view_duration()
    {
        return $this->average_view_duration;
    }
    public function previous_period_average_view_duration()
    {
        return $this->previous_period_average_view_duration;
    }
    public function average_view_duration_growth()
    {
        $current = $this->average_view_duration();
        $previous = $this->previous_period_average_view_duration();
        if ($current == 0 || $previous == 0) {
            return 0;
        } else {
            return ($current - $previous) / $previous * 100;
        }
    }
    public function bounces() : int
    {
        return $this->bounces;
    }
    public function bounce_rate() : float
    {
        return $this->bounce_rate;
    }
    public function views_per_session() : float
    {
        return $this->views_per_session;
    }
    protected function set_view_stats($row)
    {
        $this->views = isset($row->views) ? \intval($row->views) : null;
        $this->previous_period_views = isset($row->previous_period_views) ? \intval($row->previous_period_views) : null;
        $this->views_growth = isset($row->views_growth) ? \floatval($row->views_growth) : null;
        $this->views_per_session = isset($row->views_per_session) ? \floatval($row->views_per_session) : null;
        $this->visitors = isset($row->visitors) ? \intval($row->visitors) : null;
        $this->previous_period_visitors = isset($row->previous_period_visitors) ? \intval($row->previous_period_visitors) : null;
        $this->visitors_growth = isset($row->visitors_growth) ? \floatval($row->visitors_growth) : null;
        $this->average_session_duration = isset($row->average_session_duration) ? \intval($row->average_session_duration) : null;
        $this->average_view_duration = isset($row->average_view_duration) ? \intval($row->average_view_duration) : null;
        $this->sessions = isset($row->sessions) ? \intval($row->sessions) : null;
        $this->previous_period_sessions = isset($row->previous_period_sessions) ? \intval($row->previous_period_sessions) : null;
        $this->previous_period_average_session_duration = isset($row->previous_period_average_session_duration) ? \intval($row->previous_period_average_session_duration) : null;
        $this->previous_period_average_view_duration = isset($row->previous_period_average_view_duration) ? \intval($row->previous_period_average_view_duration) : null;
        $this->bounces = isset($row->bounces) ? \intval($row->bounces) : null;
        $this->bounce_rate = isset($row->bounce_rate) ? \floatval($row->bounce_rate) : null;
    }
}
