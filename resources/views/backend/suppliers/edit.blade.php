@extends('layouts.app')
@section('content')
    <div class="card" style="max-width:500px">
        <div class="card-header">Yetkazuvchi tahrirlash</div>
        <div class="card-body">
            <form method="POST" action="{{ route('backend.suppliers.update', $supplier) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefon</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Izoh</label>
                    <textarea name="note" class="form-control">{{ old('note', $supplier->note) }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Saqlash</button>
                <a href="{{ route('backend.suppliers.index') }}" class="btn btn-secondary">Orqaga</a>
            </form>
        </div>
    </div>
@endsection
