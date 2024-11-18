<!-- Daily Tasks Tab Content (Updated IDs) -->
<div class="tab-pane fade" id="daily-tasks{{ $project->id }}" role="tabpanel"
    aria-labelledby="daily-tasks-tab{{ $project->id }}">
    <div>
        <h5>Daily Tasks for Project: {{ $project->name }}</h5>

        <form action="{{ route('daily_activities.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="description">Task Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
            @can('daily-activity-create')
                <button type="submit" class="btn btn-primary mt-3">Add Task</button>
            @endcan
        </form>

        <h5 class="mt-4">Existing Tasks</h5>
        @if ($dailyActivities->count() > 0)
            <ul class="list-group">
                @foreach ($dailyActivities as $activity)
                    <li class="list-group-item">
                        <strong>{{ $activity->user->name }}</strong>: {{ $activity->description }}
                        <em class="text-muted"> - {{ $activity->created_at->format('M d, Y') }}</em>
                    </li>
                @endforeach
            </ul>
        @else
            <p>No daily tasks added for this project yet.</p>
        @endif
    </div>
</div>
