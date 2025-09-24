<div>
    <form wire:submit="save" class="needs-validation form-horizontal" wire:ignore.self>
        <div class="row">
            <div class="col-md">
                <label for="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name"
                    id="name" placeholder="Shift Name">

                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="col-md">
                <label for="form-label">Start Time</label>
                <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                    wire:model="start_time" placeholder="Start Time">

                @error('start_time')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="col-md">
                <label for="form-label">End Time</label>
                <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                    wire:model="end_time" placeholder="End Time">

                @error('end_time')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="col-md-1">
                <label for="form-label">Status</label>
                <select class="form-control @error('is_active') is-invalid @enderror" wire:model="is_active">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>

                @error('is_active')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <label for="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" wire:model="description"
                    rows="3" placeholder="Shift Description"></textarea>

                @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="mdi mdi-content-save me-1"></i>
                        {{ $statusForm == 'store' ? 'Save' : 'Update' }}
                    </button>

                    <button type="button" class="btn btn-light waves-effect waves-light"
                        wire:click="resetFormFields()">
                        <i class="mdi mdi-refresh me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>