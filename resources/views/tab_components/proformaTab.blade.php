<!-- Proformas Tab -->

<div class="tab-pane fade" id="proformas{{ $project->id }}" role="tabpanel"
    aria-labelledby="proformas-tab{{ $project->id }}">
    <div class="card mb-4">

        <!-- Card Header -->
        <div class="card-header  text-white">
            <h5>{{ $project->name }} Proformas</h5>
        </div>

        <!-- Tabs for Proforma Types -->
        <div class="card-body">
            <!-- Tabs for Buyer and Seller Proformas -->
            <ul class="nav nav-tabs" id="mainProformaTabs{{ $project->id }}" role="tablist">
                <!-- Buyer Proforma Tab -->
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="buyer-tab{{ $project->id }}" data-bs-toggle="tab"
                        href="#buyerProforma{{ $project->id }}" role="tab"
                        aria-controls="buyerProforma{{ $project->id }}" aria-selected="true">
                        <i class="bi bi-person"></i> Aluminium Proforma
                    </a>
                </li>
                <!-- Seller Proforma Tab -->
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="seller-tab{{ $project->id }}" data-bs-toggle="tab"
                        href="#sellerProforma{{ $project->id }}" role="tab"
                        aria-controls="sellerProforma{{ $project->id }}" aria-selected="false">
                        <i class="bi bi-people"></i> Sales Proforma
                    </a>
                </li>
            </ul>

            <!-- Tab Content for Buyer and Seller Proformas -->
            <div class="tab-content mt-3" id="mainProformaTabContent{{ $project->id }}">
                <!-- Buyer Proforma Content -->
                <div class="tab-pane fade show active" id="buyerProforma{{ $project->id }}" role="tabpanel"
                    aria-labelledby="buyer-tab{{ $project->id }}">
                    <div class="card-body">
                        <!-- Tabs inside Buyer Proforma -->
                        <ul class="nav nav-tabs" id="proformaTabs{{ $project->id }}" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="profile-tab{{ $project->id }}" data-bs-toggle="tab"
                                    href="#profileProforma{{ $project->id }}" role="tab"
                                    aria-controls="profileProforma{{ $project->id }}" aria-selected="true">
                                    <i class="bi bi-box-seam"></i> Aluminium Profile Proforma
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="accessories-tab{{ $project->id }}" data-bs-toggle="tab"
                                    href="#accessoriesProforma{{ $project->id }}" role="tab"
                                    aria-controls="accessoriesProforma{{ $project->id }}" aria-selected="false">
                                    <i class="bi bi-gear"></i> Accessories Proforma
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="work-tab{{ $project->id }}" data-bs-toggle="tab"
                                    href="#workProforma{{ $project->id }}" role="tab"
                                    aria-controls="workProforma{{ $project->id }}" aria-selected="false">
                                    <i class="bi bi-briefcase"></i> Work Proforma
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content inside Buyer Proforma -->
                        <div class="tab-content mt-3" id="proformaTabContent{{ $project->id }}">
                            <!-- Aluminium Profile Proforma Content -->
                            @include('tab_components.aluminiumProfileTab')

                            <!-- Accessories Proforma Content -->
                            @include('tab_components.aluminiumAccessoriesTab')

                            <!-- Work Proforma Content -->
                            @include('tab_components.workProformaTab')
                        </div>
                    </div>
                </div>

                @include('tab_components.sellerProformaTab')
            </div>
        </div>

    </div>






</div>







</div>
</div>
</div>
