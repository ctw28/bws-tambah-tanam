@extends('admin.template')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h3>Import Data Irigasi</h3>
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('import.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="form-control mb-2" required>
                <button type="submit" class="btn btn-primary">Import</button>
            </form>

        </div>
    </div>
</div>
@endsection