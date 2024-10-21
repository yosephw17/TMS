 <!-- Project Tab -->
 <div class="tab-pane fade show active" id="project{{ $project->id }}" role="tabpanel"
     aria-labelledby="project-tab{{ $project->id }}">
     <div class="mt-3">

         <!-- Starting Date -->
         <div class="mb-3">
             <strong class="font-weight-bold mb-1">Starting Date:</strong>
             <p class="lead">{{ \Carbon\Carbon::parse($project->starting_date)->format('F d, Y') }}
             </p>
         </div>

         <!-- Ending Date (Ongoing if null) -->
         <div class="mb-3">
             <strong class="font-weight-bold mb-1">Ending Date:</strong>
             <p class="lead">
                 {{ $project->ending_date ? \Carbon\Carbon::parse($project->ending_date)->format('F d, Y') : 'Ongoing' }}
             </p>
         </div>

         <!-- Description -->
         <div class="mb-3">
             <strong class="font-weight-bold mb-1">Description:</strong>
             <p class="lead">{{ $project->description }}</p>
         </div>

         <!-- Location -->
         <div class="mb-3">
             <strong class="font-weight-bold mb-1">Location:</strong>
             <p class="lead">{{ $project->location }}</p>
         </div>

         <!-- Services (if any) -->
         <div class="mb-3">
             <strong class="font-weight-bold mb-1">Services Included:</strong>
             @if ($project->serviceDetails->count() > 0)
                 <ul class="list-group">
                     @foreach ($project->serviceDetails as $serviceDetail)
                         <li class="list-group-item">
                             {{ $serviceDetail->detail_name }} (under
                             {{ $serviceDetail->service->name }})
                         </li>
                     @endforeach
                 </ul>
             @else
                 <p class="text-muted">None</p>
             @endif
         </div>

         <!-- Status -->
         <div class="mb-3">
             <strong class="font-weight-bold mb-1">Status:</strong>
             <p class="lead">
                 <span
                     class="{{ $project->status == 'completed' ? 'text-success' : ($project->status == 'canceled' ? 'text-danger' : 'text-warning') }}">
                     {{ ucfirst($project->status) }}
                 </span>
             </p>
         </div>
     </div>
 </div>
