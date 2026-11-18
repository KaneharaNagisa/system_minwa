@extends('admin.layout')

@section('title', $story->exists ? '民話の編集' : '民話の登録')

@section('content')
    <div class="admin-page-heading">
        <h1 class="admin-page-title">@yield('title')</h1>
        <a class="admin-btn-secondary" href="{{ route('admin.stories.index') }}">一覧へ戻る</a>
    </div>

    <form class="admin-card admin-edit-form" method="POST" enctype="multipart/form-data" action="{{ $story->exists ? route('admin.stories.update', $story) : route('admin.stories.store') }}">
        @csrf
        @if ($story->exists)
            @method('PUT')
        @endif

        <section class="admin-ocr-card" data-ocr-uploader data-ocr-url="{{ route('admin.stories.ocr') }}">
            <h2 class="admin-ocr-title"><span aria-hidden="true">⇧</span> 1. 資料画像のアップロードと文字の自動読み込み（OCR）</h2>
            <p class="admin-ocr-description">書籍やパンフレットを撮影した画像（複数選択可）をアップロードすると、文字を読み取り、本文欄に追加します。画像はこのサーバー内で処理し、第三者サービスへ送信しません。</p>
            <div class="admin-ocr-dropzone" data-ocr-dropzone>
                <input class="admin-ocr-file-input" id="ocr_images" type="file" accept="image/*" multiple data-ocr-files>
                <button class="admin-btn-secondary admin-ocr-select" type="button" data-ocr-select>＋ 画像ファイルを選択（複数可）</button>
                <p class="admin-ocr-file-list" data-ocr-file-list aria-live="polite">最大10枚、1枚10MBまで</p>
            </div>
            <p class="admin-ocr-status" data-ocr-status role="status" aria-live="polite"></p>
        </section>

        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label class="admin-form-label" for="title">タイトル <span class="admin-required">必須</span></label>
                <input class="admin-form-control" id="title" name="title" value="{{ old('title', $story->title) }}" required maxlength="255">
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label" for="title_kana">よみがな</label>
                <input class="admin-form-control" id="title_kana" name="title_kana" value="{{ old('title_kana', $story->title_kana) }}" maxlength="255">
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label" for="municipality_id">市町村</label>
                <select class="admin-form-control" id="municipality_id" name="municipality_id">
                    <option value="">未設定</option>
                    @foreach ($municipalities as $municipality)
                        <option value="{{ $municipality->id }}" @selected(old('municipality_id', $story->municipality_id) == $municipality->id)>{{ $municipality->name }}（{{ $municipality->region_name }}）</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-form-group admin-form-wide">
                <label class="admin-form-label" for="summary">あらすじ</label>
                <textarea class="admin-form-control admin-textarea-short" id="summary" name="summary" rows="3">{{ old('summary', $story->summary) }}</textarea>
            </div>
            <div class="admin-form-group admin-form-wide">
                <label class="admin-form-label" for="body_markdown">本文（Markdown） <span class="admin-required">必須</span></label>
                <textarea class="admin-form-control admin-markdown-editor" id="body_markdown" name="body_markdown" rows="16" required>{{ old('body_markdown', $story->body_markdown) }}</textarea>
                <small class="admin-help">画像は ![説明](画像URLまたは画像パス) の形式で挿入できます。</small>
            </div>
            <div class="admin-form-group admin-form-wide">
                <label class="admin-form-label" for="main_image">メインイラスト</label>
                @if ($story->main_image_path)
                    <p class="admin-current-image">現在の画像: {{ $story->main_image_path }}</p>
                @endif
                <input class="admin-form-control" id="main_image" name="main_image" type="file" accept="image/*">
                <label class="admin-form-label admin-sub-label" for="main_image_path">または画像URL・保存済みパス</label>
                <input class="admin-form-control" id="main_image_path" name="main_image_path" value="{{ old('main_image_path', $story->main_image_path) }}" maxlength="2048">
                <small class="admin-help">画像ファイルは10MB以下です。新しいファイルを選ぶと保存済みパスより優先されます。</small>
            </div>
            <div class="admin-form-group admin-form-wide">
                <label class="admin-form-label" for="body_images">本文中の挿絵</label>
                <input class="admin-form-control" id="body_images" name="body_images[]" type="file" accept="image/*" multiple>
                <small class="admin-help">選択した画像は本文末尾にMarkdown記法で追加されます。位置は本文欄で調整してください。各画像10MB以下。</small>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label" for="spot_name">ゆかりの地 名称</label>
                <input class="admin-form-control" id="spot_name" name="spot_name" value="{{ old('spot_name', $story->spot_name) }}" maxlength="255">
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label" for="spot_address">ゆかりの地 住所</label>
                <input class="admin-form-control" id="spot_address" name="spot_address" value="{{ old('spot_address', $story->spot_address) }}">
            </div>
            <div class="admin-form-group admin-form-wide">
                <label class="admin-form-label" for="spot_map_url">GoogleマップURL</label>
                <input class="admin-form-control" id="spot_map_url" name="spot_map_url" type="url" value="{{ old('spot_map_url', $story->spot_map_url) }}" maxlength="2048">
            </div>
            <div class="admin-form-group admin-form-wide">
                <label class="admin-form-label" for="source">出典</label>
                <textarea class="admin-form-control admin-textarea-short" id="source" name="source" rows="3">{{ old('source', $story->source) }}</textarea>
            </div>
            <fieldset class="admin-form-group admin-form-wide admin-checks">
                <legend class="admin-form-label">出典・著作権確認</legend>
                <label class="admin-check"><input type="checkbox" name="source_citation_checked" value="1" @checked(old('source_citation_checked', $story->source_citation_checked))> 出典元を明記した</label>
                <label class="admin-check"><input type="checkbox" name="expression_rewritten_checked" value="1" @checked(old('expression_rewritten_checked', $story->expression_rewritten_checked))> 表現を書き直した</label>
                <small class="admin-help">チェックは権利処理そのものを保証しません。利用可否を確認してください。</small>
            </fieldset>
            <fieldset class="admin-form-group admin-form-wide admin-checks">
                <legend class="admin-form-label">テーマタグ</legend>
                @forelse ($tags as $tag)
                    <label class="admin-check">
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" @checked(in_array($tag->id, old('tag_ids', $story->exists ? $story->tags->modelKeys() : [])))>
                        {{ $tag->name }}
                    </label>
                @empty
                    <p class="admin-help">タグはまだ登録されていません。</p>
                @endforelse
            </fieldset>
        </div>
        <div class="admin-form-footer">
            <a class="admin-btn-secondary" href="{{ route('admin.stories.index') }}">キャンセル</a>
            <button class="admin-btn-primary admin-btn-inline" type="submit">{{ $story->exists ? '変更を保存' : '民話を登録' }}</button>
        </div>
    </form>
@endsection
