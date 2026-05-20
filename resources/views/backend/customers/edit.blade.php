@extends('layouts.app')
@section('content')
    <div class="card" style="max-width:500px">
        <div class="card-header">Mijoz tahrirlash</div>
        <div class="card-body">
            <form method="POST" action="{{ route('backend.customers.update', $customer) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Ism</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefon</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Izoh</label>
                    <textarea name="note" class="form-control">{{ old('note', $customer->note) }}</textarea>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1"
                        {{ $customer->is_active ? 'checked' : '' }}>
                    <label class="form-check-label">Faol</label>
                </div>
                <button type="submit" class="btn btn-primary">Saqlash</button>
                <a href="{{ route('backend.customers.index') }}" class="btn btn-secondary">Orqaga</a>
            </form>
        </div>
    </div>
@endsection
