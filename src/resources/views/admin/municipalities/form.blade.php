@extends('admin.layout')

@section('title', $municipality->exists ? '市町村の編集' : '市町村の登録')

@section('content')
    <div class="admin-page-heading">
        <h1 class="admin-page-title">@yield('title')</h1>
        <a class="admin-btn-secondary" href="{{ route('admin.municipalities.index') }}">一覧へ戻る</a>
    </div>
    <form class="admin-card admin-simple-form" method="POST" action="{{ $municipality->exists ? route('admin.municipalities.update', $municipality) : route('admin.municipalities.store') }}">
        @csrf
        @if ($municipality->exists)
            @method('PUT')
        @endif
        <div class="admin-form-group">
            <label class="admin-form-label" for="name">市町村名 <span class="admin-required">必須</span></label>
            <input class="admin-form-control" id="name" name="name" value="{{ old('name', $municipality->name) }}" required maxlength="255">
        </div>
        <div class="admin-form-group">
            <label class="admin-form-label" for="region_name">地域名 <span class="admin-required">必須</span></label>
            <input class="admin-form-control" id="region_name" name="region_name" value="{{ old('region_name', $municipality->region_name) }}" required maxlength="255" placeholder="例：北信">
        </div>
        <div class="admin-form-footer">
            <a class="admin-btn-secondary" href="{{ route('admin.municipalities.index') }}">キャンセル</a>
            <button class="admin-btn-primary admin-btn-inline" type="submit">{{ $municipality->exists ? '変更を保存' : '市町村を登録' }}</button>
        </div>
    </form>
@endsection
