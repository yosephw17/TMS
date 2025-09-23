@extends('layouts.admin')
@section('content')
    <main>
        <div class="container-fluid px-4">
            <!-- Enhanced Header -->
            <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                <div>
                    <h1 class="mb-1 fw-bold">Dashboard</h1>
                    <p class="text-muted mb-0">Welcome back! Here's what's happening with your business today.</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">Last updated: {{ now()->format('M d, Y - H:i') }}</small>
                </div>
            </div>
            
            <!-- Enhanced Stats Cards with KPI Design -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="text-center p-3 bg-light rounded-3 h-100 stats-card">
                        <div class="text-primary fs-4 mb-2">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="fw-bold fs-2 mb-1">{{ $customersCount ?? 0 }}</div>
                        <div class="small text-muted mb-2">Total Customers</div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-primary" style="width: {{ $customersCount > 0 ? min(($customersCount / 50) * 100, 100) : 0 }}%"></div>
                        </div>
                        <div class="mt-3">
                            <a class="small text-primary text-decoration-none" href="{{ route('customers.index') }}">
                                View All Customers <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="text-center p-3 bg-light rounded-3 h-100 stats-card">
                        <div class="text-success fs-4 mb-2">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="fw-bold fs-2 mb-1">{{ $projectsCount ?? 0 }}</div>
                        <div class="small text-muted mb-2">Active Projects</div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: {{ $projectsCount > 0 ? min(($projectsCount / 20) * 100, 100) : 0 }}%"></div>
                        </div>
                        <div class="mt-3">
                            <a class="small text-success text-decoration-none" href="{{ route('projects.index') }}">
                                View All Projects <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="text-center p-3 bg-light rounded-3 h-100 stats-card">
                        <div class="text-info fs-4 mb-2">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="fw-bold fs-2 mb-1">{{ $servicesCount ?? 0 }}</div>
                        <div class="small text-muted mb-2">Services Available</div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-info" style="width: {{ $servicesCount > 0 ? min(($servicesCount / 15) * 100, 100) : 0 }}%"></div>
                        </div>
                        <div class="mt-3">
                            <a class="small text-info text-decoration-none" href="{{ route('services.index') }}">
                                View All Services <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="text-center p-3 bg-light rounded-3 h-100 stats-card">
                        <div class="text-warning fs-4 mb-2">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="fw-bold fs-2 mb-1">{{ $sellersCount ?? 0 }}</div>
                        <div class="small text-muted mb-2">Team Members</div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-warning" style="width: {{ $sellersCount > 0 ? min(($sellersCount / 10) * 100, 100) : 0 }}%"></div>
                        </div>
                        <div class="mt-3">
                            <a class="small text-warning text-decoration-none" href="{{ route('sellers.index') }}">
                                View All Team <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Access to Recent Projects - Enhanced Professional Design -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
                        <!-- Elegant Header with Gradient -->
                        <div class="card-header border-0 position-relative">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-white bg-opacity-20 p-2 me-3" style="backdrop-filter: blur(10px);">
                                        <i class="fas fa-rocket text-dark"></i>
                                    </div>
                               
                                    <div>
                                        <h5 class="mb-1 text-dark fw-bold">Active Projects</h5>
                                        <p class="mb-0 text-dark-50 small">Quick access to your ongoing work</p>
                                    </div>
                                </div>
                                <a href="{{ route('projects.index') }}" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm hover-lift">
                                    <i class="fas fa-eye me-1"></i>View All
                                </a>
                            </div>
                        
                        </div>
                        
                        <div class="card-body p-3">
                            @if($recentProjects->count() > 0)
                                <div class="row g-3">
                                    @foreach($recentProjects as $index => $project)
                                        <div class="col-xl-4 col-lg-6 col-md-6">
                                            <a href="{{ route('projects.view', $project->id) }}" class="text-decoration-none">
                                                <div class="project-card h-100 border-0 shadow-sm rounded-3 overflow-hidden position-relative" 
                                                     style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                                                    
                                                    <!-- Status Indicator Strip -->
                                                    <div class="position-absolute top-0 start-0 w-100 h-1" 
                                                         style="background: {{ $project->status == 'pending' ? 'linear-gradient(90deg, #ffd700, #ffed4e)' : 'linear-gradient(90deg, #3b82f6, #60a5fa)' }};"></div>
                                                    
                                                    <div class="p-3">
                                                        <!-- Project Header -->
                                                        <div class="d-flex align-items-start mb-3">
                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="rounded-2 d-flex align-items-center justify-content-center position-relative" 
                                                                     style="width: 45px; height: 45px; background: {{ $project->status == 'pending' ? 'linear-gradient(135deg, #ffd700, #ffed4e)' : 'linear-gradient(135deg, #3b82f6, #60a5fa)' }};">
                                                                    <i class="fas fa-{{ $project->status == 'pending' ? 'clock' : 'play' }} text-white"></i>
                                                                    <div class="position-absolute top-0 end-0 translate-middle">
                                                                        <span class="badge rounded-pill" style="background: {{ $project->status == 'pending' ? '#ff6b35' : '#10b981' }}; font-size: 0.6rem;">
                                                                            {{ $recentProjects->count() - $index }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 min-w-0">
                                                                <h6 class="mb-1 text-dark fw-bold lh-sm">
                                                                    {{ $project->name }}
                                                                </h6>
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <i class="fas fa-user text-muted me-2" style="font-size: 0.75rem;"></i>
                                                                    <span class="text-muted small">{{ $project->customer->name }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Project Details -->
                                                        <div class="mb-3">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="fas fa-map-marker-alt text-muted me-2" style="font-size: 0.75rem;"></i>
                                                                <span class="text-muted small">{{ Str::limit($project->location, 25) }}</span>
                                                            </div>
                                                            @if($project->ending_date)
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-calendar-alt text-muted me-2" style="font-size: 0.75rem;"></i>
                                                                    <span class="text-muted small">Due: {{ \Carbon\Carbon::parse($project->ending_date)->format('M d, Y') }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <!-- Footer -->
                                                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light">
                                                            <span class="badge rounded-pill px-2 py-1 small" 
                                                                  style="background: {{ $project->status == 'pending' ? 'rgba(255, 215, 0, 0.15)' : 'rgba(59, 130, 246, 0.15)' }}; 
                                                                         color: {{ $project->status == 'pending' ? '#b45309' : '#1e40af' }}; 
                                                                         font-size: 0.7rem;">
                                                                <i class="fas fa-{{ $project->status == 'pending' ? 'hourglass-half' : 'spinner' }} me-1"></i>
                                                                {{ str_replace('_', ' ', ucfirst($project->status)) }}
                                                            </span>
                                                            <small class="text-muted">
                                                                {{ $project->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Hover Overlay -->
                                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0 hover-overlay"
                                                         style="background: rgba(255, 255, 255, 0.95); transition: opacity 0.3s ease;">
                                                        <div class="text-center">
                                                            <i class="fas fa-external-link-alt text-primary fs-5 mb-1"></i>
                                                            <p class="text-primary fw-bold mb-0 small">View Details</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="mb-3">
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-folder-open text-muted fs-2"></i>
                                        </div>
                                    </div>
                                    <h5 class="text-muted mb-2 fw-bold">No Active Projects</h5>
                                    <p class="text-muted mb-3 mx-auto" style="max-width: 300px;">
                                        You don't have any active projects at the moment. Start by creating a new project to see it here.
                                    </p>
                                    @can('project-create')
                                        <a href="{{ route('customers.index') }}" class="btn btn-primary rounded-pill px-4">
                                            <i class="fas fa-plus me-1"></i>Create Your First Project
                                        </a>
                                    @endcan
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Styles for Enhanced Design -->
            <style>
                .project-card {
                    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
                    border: 1px solid rgba(0, 0, 0, 0.05) !important;
                }
                
                .project-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
                }
                
                .project-card:hover .hover-overlay {
                    opacity: 1 !important;
                }
                
                .hover-lift:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
                }
                
                .h-1 {
                    height: 3px !important;
                }
                
                /* Responsive adjustments */
                @media (max-width: 768px) {
                    .project-card:hover {
                        transform: none;
                    }
                    
                    .card-header {
                        padding: 1rem !important;
                    }
                    
                    .card-header h5 {
                        font-size: 1.1rem !important;
                    }
                }
            </style>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-area me-1"></i>
                            Projects
                        </div>
                        <div class="card-body same-height">
                            <canvas id="myAreaChart" width="100%" height="50px"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Customers
                        </div>
                        <div class="card-body"><canvas id="myBarChart" width="100%" height="50"></canvas></div>
                    </div>
                </div>
            </div>
            <!-- Pie Chart -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-1"></i>
                            Services Offered
                        </div>
                        <div class="card-body">
                            <canvas id="myPieChart" width="100%" height="40"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection