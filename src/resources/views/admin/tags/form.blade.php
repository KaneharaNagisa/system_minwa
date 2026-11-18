@extends('admin.layout')

@section('title', $tag->exists ? 'タグの編集' : 'タグの登録')

@section('content')
    <div class="admin-page-heading">
        <h1 class="admin-page-title">@yield('title')</h1>
        <a class="admin-btn-secondary" href="{{ route('admin.tags.index') }}">一覧へ戻る</a>
    </div>
    <form class="admin-card admin-simple-form" method="POST" action="{{ $tag->exists ? route('admin.tags.update', $tag) : route('admin.tags.store') }}">
        @csrf
        @if ($tag->exists)
            @method('PUT')
        @endif
        <div class="admin-form-group">
            <label class="admin-form-label" for="name">タグ名 <span class="admin-required">必須</span></label>
            <input class="admin-form-control" id="name" name="name" value="{{ old('name', $tag->name) }}" required maxlength="255">
        </div>
        <div class="admin-form-footer">
            <a class="admin-btn-secondary" href="{{ route('admin.tags.index') }}">キャンセル</a>
            <button class="admin-btn-primary admin-btn-inline" type="submit">{{ $tag->exists ? '変更を保存' : 'タグを登録' }}</button>
        </div>
    </form>
@endsection
