  <!-- Work Proforma Content -->
  <div class="tab-pane fade" id="workProforma{{ $project->id }}" role="tabpanel"
      aria-labelledby="work-tab{{ $project->id }}">
      <div class="card mt-3">

          <div class="card-body">
              <button class="btn btn-primary mb-3" data-bs-toggle="modal"
                  data-bs-target="#addWorkProformaModal{{ $project->id }}">
                  Add Proforma
              </button>

              @if ($workProformas->isEmpty())
                  <p>No Work Proformas available.</p>
              @else
                  <table class="table table-bordered">
                      <thead>
                          <tr>
                              <th>Ref No</th>
                              <th>Date</th>
                              <th>Before VAT Total</th>
                              <th>VAT Percentage</th>
                              <th>After VAT Total</th>
                              <th>Discount</th>
                              <th>Final Total</th>
                              <th>Actions</th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach ($workProformas as $proforma)
                              @if ($proforma->type === 'work')
                                  <!-- Main Proforma Row -->
                                  <tr data-bs-toggle="collapse" data-bs-target="#collapseProforma{{ $proforma->id }}"
                                      aria-expanded="false">
                                      <td>{{ $proforma->ref_no }}</td>
                                      <td>{{ $proforma->date->format('F d, Y') }}</td>
                                      <td>{{ $proforma->before_vat_total }}</td>
                                      <td>{{ $proforma->vat_percentage }}%</td>
                                      <td>{{ $proforma->after_vat_total }}</td>
                                      <td>{{ $proforma->discount }}</td>
                                      <td>{{ $proforma->final_total }}</td>
                                      <td>
                                          @can('proforma-edit')
                                              <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                                  data-bs-target="#editWorkProformaModal{{ $proforma->id }}">
                                                  Edit
                                              </button>
                                          @endcan
                                          <form action="{{ route('proforma_work.destroy', $proforma->id) }}"
                                              method="POST" style="display:inline;">
                                              @csrf
                                              @method('DELETE')
                                              @can('proforma-delete')
                                                  <button type="submit" class="btn btn-outline-danger btn-sm"
                                                      onclick="return confirm('Are you sure you want to delete?')">
                                                      Delete
                                                  </button>
                                              @endcan
                                          </form>
                                          @can('proforma-print')
                                              <a href="{{ route('print.work', $proforma->id) }}" class="btn btn-secondary"
                                                  target="_blank"
                                                  onclick="event.preventDefault(); window.open(this.href, '_blank'); return false;">
                                                  <i class="fas fa-print"></i>
                                                  Print
                                              </a>
                                          @endcan
                                      </td>
                                  </tr>

                                  <tr class="collapse bg-light" id="collapseProforma{{ $proforma->id }}">
                                      <td colspan="8">
                                          <div class="p-3">
                                              <h6>Work Proforma Details</h6>
                                              <table class="table table-sm">
                                                  <thead>
                                                      <tr>
                                                          <th>Name</th>
                                                          <th>Unit</th>
                                                          <th>Amount</th>
                                                          <th>Quantity</th>
                                                          <th>Total Price</th>
                                                      </tr>
                                                  </thead>
                                                  <tbody>
                                                      @foreach ($proforma->works as $work)
                                                          <tr>
                                                              <td>{{ $work->work_name }}</td>
                                                              <td>{{ $work->work_unit }}</td>
                                                              <td>{{ number_format($work->work_amount, 2) }}</td>
                                                              <td>{{ $work->work_quantity }}</td>
                                                              <td>{{ number_format($work->work_total, 2) }}
                                                              </td>
                                                          </tr>
                                                      @endforeach
                                                  </tbody>
                                              </table>

                                              <div class="row mt-4">
                                                  <div class="col-md-6">
                                                      <p><strong>Payment Validity:</strong>
                                                          {{ $proforma->payment_validity }}</p>
                                                      <p><strong>Delivery Terms:</strong>
                                                          {{ $proforma->delivery_terms }}</p>
                                                  </div>
                                                  <div class="col-md-6 text-end">
                                                      <table class="table table-borderless">
                                                          <tr>
                                                              <th>Subtotal (Before VAT):</th>
                                                              <td>{{ number_format($proforma->before_vat_total, 2) }}
                                                              </td>
                                                          </tr>
                                                          <tr>
                                                              <th>VAT ({{ $proforma->vat_percentage }}%):</th>
                                                              <td>{{ number_format($proforma->vat_amount, 2) }}</td>
                                                          </tr>
                                                          <tr>
                                                              <th>After VAT Total:</th>
                                                              <td>{{ number_format($proforma->after_vat_total, 2) }}
                                                              </td>
                                                          </tr>
                                                          <tr>
                                                              <th>Discount:</th>
                                                              <td>{{ number_format($proforma->discount ?? 0, 2) }}</td>
                                                          </tr>
                                                          <tr>
                                                              <th><strong>Final Total:</strong></th>
                                                              <td><strong>{{ number_format($proforma->final_total, 2) }}</strong>
                                                              </td>
                                                          </tr>
                                                      </table>
                                                  </div>
                                              </div>
                                          </div>
                                      </td>
                                  </tr>

                                  <!-- Edit Work Proforma Modal -->
                                  <div class="modal fade" id="editWorkProformaModal{{ $proforma->id }}" tabindex="-1"
                                      aria-labelledby="editWorkProformaModalLabel{{ $proforma->id }}"
                                      aria-hidden="true">
                                      <div class="modal-dialog modal-lg">
                                          <form action="{{ route('proforma_work.update', $proforma->id) }}"
                                              method="POST">
                                              @csrf
                                              @method('PUT')
                                              <div class="modal-content">
                                                  <div class="modal-header">
                                                      <h5 class="modal-title"
                                                          id="editWorkProformaModalLabel{{ $proforma->id }}">
                                                          Edit Work Proforma
                                                      </h5>
                                                      <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                          aria-label="Close"></button>
                                                  </div>
                                                  <div class="modal-body">
                                                      <input type="hidden" name="project_id"
                                                          value="{{ $project->id }}">
                                                      <input type="hidden" name="customer_id"
                                                          value="{{ $project->customer_id }}">

                                                      <div class="form-group mb-3">
                                                          <label for="type">Type</label>
                                                          <select name="type" class="form-control" required>
                                                              <option value="Aluminium"
                                                                  {{ $proforma->type == 'Aluminium' ? 'selected' : '' }}>
                                                                  Aluminium</option>
                                                              <option value="Finishing"
                                                                  {{ $proforma->type == 'Finishing' ? 'selected' : '' }}>
                                                                  Finishing</option>
                                                          </select>
                                                      </div>

                                                      <div class="form-group mb-3">
                                                          <label for="ref_no">Reference Number</label>
                                                          <input type="text" name="ref_no" class="form-control"
                                                              value="{{ $proforma->ref_no }}"
                                                              placeholder="Enter reference number" required>
                                                      </div>

                                                      <div class="form-group mb-3">
                                                          <label for="date">Date</label>
                                                          <input type="date" name="date" class="form-control"
                                                              value="{{ $proforma->date }}" required>
                                                      </div>

                                                      <div class="form-group mb-3">
                                                          <label for="discount">Discount</label>
                                                          <input type="number" name="discount" class="form-control"
                                                              value="{{ $proforma->discount }}"
                                                              placeholder="Enter discount (if any)">
                                                      </div>
                                                      <div class="form-group mb-3">
                                                          <label for="vat_percentage">VAT Percentage</label>
                                                          <input type="number" name="vat_percentage"
                                                              class="form-control"
                                                              value="{{ $proforma->vat_percentage }}" required>
                                                      </div>

                                                      <div class="form-group mb-3">
                                                          <label for="payment_validity">Payment Validity</label>
                                                          <input type="text" name="payment_validity"
                                                              class="form-control"
                                                              value="{{ $proforma->payment_validity }}"
                                                              placeholder="Enter payment validity">
                                                      </div>
                                                      <div class="form-group mb-3">
                                                          <label for="delivery_terms">Delivery Terms</label>
                                                          <input type="text" name="delivery_terms"
                                                              class="form-control"
                                                              value="{{ $proforma->delivery_terms }}"
                                                              placeholder="Enter delivery terms">
                                                      </div>

                                                      <div id="editWorkEntriesContainer">
                                                          @foreach ($proforma->works as $index => $work)
                                                              <div class="work-entry mb-3">
                                                                  <h6>Work Entry {{ $index + 1 }}</h6>
                                                                  <input type="hidden"
                                                                      name="works[{{ $index }}][id]"
                                                                      value="{{ $work->id }}">

                                                                  <div class="form-group">
                                                                      <label for="work_name[]">Name</label>
                                                                      <input type="text"
                                                                          name="works[{{ $index }}][name]"
                                                                          class="form-control"
                                                                          value="{{ old('works.' . $index . '.name', $work->work_name) }}"
                                                                          placeholder="Enter work name" required>
                                                                  </div>
                                                                  <div class="form-group">
                                                                      <label for="work_unit[]">Unit</label>
                                                                      <input type="text"
                                                                          name="works[{{ $index }}][unit]"
                                                                          class="form-control"
                                                                          value="{{ old('works.' . $index . '.unit', $work->work_unit) }}"
                                                                          placeholder="Enter unit" required>
                                                                  </div>
                                                                  <div class="form-group">
                                                                      <label for="work_amount[]">Amount</label>
                                                                      <input type="number"
                                                                          name="works[{{ $index }}][amount]"
                                                                          class="form-control"
                                                                          value="{{ old('works.' . $index . '.amount', $work->work_amount) }}"
                                                                          placeholder="Enter amount" required>
                                                                  </div>
                                                                  <div class="form-group">
                                                                      <label for="work_quantity[]">Quantity</label>
                                                                      <input type="number"
                                                                          name="works[{{ $index }}][quantity]"
                                                                          class="form-control"
                                                                          value="{{ old('works.' . $index . '.quantity', $work->work_quantity) }}"
                                                                          placeholder="Enter quantity" required>
                                                                  </div>
                                                                  <div class="form-group">
                                                                      <label for="work_total[]">Total</label>
                                                                      <input type="number"
                                                                          name="works[{{ $index }}][total]"
                                                                          class="form-control"
                                                                          value="{{ old('works.' . $index . '.total', $work->work_total) }}"
                                                                          placeholder="Total">
                                                                  </div>
                                                              </div>
                                                          @endforeach
                                                      </div>

                                                  </div>
                                                  <div class="modal-footer">
                                                      <button type="button" class="btn btn-secondary"
                                                          data-bs-dismiss="modal">Close</button>
                                                      <button type="submit" class="btn btn-primary">Update
                                                          Proforma</button>
                                                  </div>
                                              </div>
                                          </form>
                                      </div>
                                  </div>
                              @endif
                          @endforeach
                      </tbody>
                  </table>
              @endif
          </div>

      </div>
  </div>
  <!-- Add Work Proforma Modal -->
  <div class="modal fade" id="addWorkProformaModal{{ $project->id }}" tabindex="-1"
      aria-labelledby="addWorkProformaModalLabel{{ $project->id }}" aria-hidden="true">
      <div class="modal-dialog modal-lg">
          <form action="{{ route('proforma_work.store') }}" method="POST">
              @csrf
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="addWorkProformaModalLabel{{ $project->id }}">Add Work Proforma
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                      <input type="hidden" name="project_id" value="{{ $project->id }}">
                      <input type="hidden" name="customer_id" value="{{ $project->customer_id }}">

                      <div class="form-group mb-3">
                          <label for="type">Type</label>
                          <select name="type" class="form-control" required>
                              <option value="Aluminium">Aluminium</option>
                              <option value="Finishing">Finishing</option>
                          </select>
                      </div>

                      <div class="form-group mb-3">
                          <label for="ref_no">Reference Number</label>
                          <input type="text" name="ref_no" class="form-control"
                              placeholder="Enter reference number" required>
                      </div>

                      <div class="form-group mb-3">
                          <label for="date">Date</label>
                          <input type="date" name="date" class="form-control" required>
                      </div>

                      <div class="form-group mb-3">
                          <label for="discount">Discount</label>
                          <input type="number" name="discount" class="form-control"
                              placeholder="Enter discount (if any)">
                      </div>
                      <div class="form-group mb-3">
                          <label for="vat_percentage">VAT Percentage</label>
                          <input type="number" name="vat_percentage" value="15" class="form-control">
                      </div>

                      <div class="form-group mb-3">
                          <label for="payment_validity">Payment Validity</label>
                          <input type="text" name="payment_validity" class="form-control"
                              placeholder="Enter payment validity">
                      </div>
                      <div class="form-group mb-3">
                          <label for="delivery_terms">Delivery Terms</label>
                          <input type="text" name="delivery_terms" class="form-control"
                              placeholder="Enter delivery terms">
                      </div>

                      <div id="addWorkEntriesContainer">
                          <div class="work-entry mb-3">
                              <h6>Work Entry</h6>
                              <div class="form-group">
                                  <label for="work_name[]">Name</label>
                                  <input type="text" name="works[0][name]" class="form-control"
                                      placeholder="Enter work name" required>
                              </div>
                              <div class="form-group">
                                  <label for="work_unit[]">Unit</label>
                                  <input type="text" name="works[0][unit]" class="form-control"
                                      placeholder="Enter unit" required>
                              </div>
                              <div class="form-group">
                                  <label for="work_amount[]">Amount</label>
                                  <input type="number" name="works[0][amount]" class="form-control"
                                      placeholder="Enter amount" required>
                              </div>
                              <div class="form-group">
                                  <label for="work_quantity[]">Quantity</label>
                                  <input type="number" name="works[0][quantity]" class="form-control"
                                      placeholder="Enter quantity" required>
                              </div>
                              <div class="form-group">
                                  <label for="work_total[]">Total</label>
                                  <input type="number" name="works[0][total]" class="form-control"
                                      placeholder="Total">
                              </div>
                          </div>
                      </div>
                      <button type="button" id="addWorkEntryBtn" class="btn btn-secondary mb-3">Add Another Work
                          Entry</button>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save Proforma</button>
                  </div>
              </div>
          </form>
      </div>
  </div>

  <script>
      let addWorkEntryIndex = 1;
      // Add new work entry in Add Modal
      document.getElementById('addWorkEntryBtn').addEventListener('click', function() {
          const addContainer = document.getElementById('addWorkEntriesContainer');
          const newWorkEntry = createWorkEntryHtml(addWorkEntryIndex);
          addContainer.appendChild(newWorkEntry);
          addWorkEntryIndex++;
      });

      function createWorkEntryHtml(index) {
          const entryDiv = document.createElement('div');
          entryDiv.className = 'work-entry mb-3';
          entryDiv.innerHTML = `
            <h6>Work Entry ${index + 1}</h6>
            <div class="form-group">
                <label for="works[${index}][name]">Name</label>
                <input type="text" name="works[${index}][name]" class="form-control" placeholder="Enter work name" required>
            </div>
            <div class="form-group">
                <label for="works[${index}][unit]">Unit</label>
                <input type="text" name="works[${index}][unit]" class="form-control" placeholder="Enter unit" required>
            </div>
            <div class="form-group">
                <label for="works[${index}][amount]">Amount</label>
                <input type="number" name="works[${index}][amount]" class="form-control" placeholder="Enter amount" required>
            </div>
            <div class="form-group">
                <label for="works[${index}][quantity]">Quantity</label>
                <input type="number" name="works[${index}][quantity]" class="form-control" placeholder="Enter quantity" required>
            </div>
            <div class="form-group">
                <label for="works[${index}][total]">Total</label>
                <input type="number" name="works[${index}][total]" class="form-control" placeholder="Total">
            </div>
        `;
          return entryDiv;
      }
  </script>
