@extends('layouts.app-frontend')
@section('content')
    <div class="incomes-page">
        <div class="container">
            <incomes
                    :initial-goal-value="{{ Auth::user()->profile->goal_value }}"
                    :initial-goal-color="'{{ Auth::user()->profile->goal_color }}'"
                    :year-of-registration="{{ Auth::user()->created_at->format('Y') }}"
            ></incomes>
        </div>
    </div>
@endsection