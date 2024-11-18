  <!-- Seller Proforma Content (Empty for Now) -->
  <div class="tab-pane fade" id="sellerProforma{{ $project->id }}" role="tabpanel"
      aria-labelledby="seller-tab{{ $project->id }}">
      <div class="card-body">
          @if ($project->proformaImages->isNotEmpty())
              <div class="row">
                  @foreach ($project->proformaImages as $image)
                      <div class="col-md-4">
                          <div class="card mb-3 shadow-sm">
                              <!-- Image Section -->
                              <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank"
                                  class="text-decoration-none">
                                  <img src="{{ asset('storage/' . $image->image_path) }}" alt="Proforma Image"
                                      class="card-img-top img-thumbnail" style="width: 100%; height: auto;">
                              </a>

                              <!-- Card Body -->
                              <div class="card-body">


                                  <!-- Seller Name -->
                                  <h6 class="card-subtitle mb-2 text-secondary d-flex align-items-center">
                                      <strong> Seller:</strong> {{ $image->seller->name ?? 'No Seller Info' }}
                                  </h6>
                                  <h6 class="card-subtitle mb-2 text-secondary d-flex align-items-center">
                                      <strong> Type:</strong> {{ $image->proforma_type ?? 'No Type' }}
                                  </h6>

                                  <!-- Description -->
                                  <p class="card-text text-muted d-flex align-items-start">
                                      <i class="fa fa-info-circle me-2"></i>
                                      {{ $image->description ?? 'No Description Provided' }}
                                  </p>
                              </div>

                              <!-- Footer Actions -->
                              <div class="card-footer text-center bg-light">
                                  <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank"
                                      class="btn btn-outline-primary btn-sm me-2">
                                      <i class="fa fa-eye"></i> View
                                  </a>

                              </div>
                          </div>
                      </div>
                  @endforeach
              </div>
          @else
              <p>No approved images available for this project.</p>
          @endif
      </div>

  </div>
