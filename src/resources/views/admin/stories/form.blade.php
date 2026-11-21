@extends('admin.layout')

@section('title', $story->exists ? '民話の編集' : '民話の登録')

@section('content')
    <div class="admin-page-heading">
        <h1 class="admin-page-title">@yield('title')</h1>
        <a class="admin-btn-secondary" href="{{ route('admin.stories.index') }}">一覧へ戻る</a>
    </div>

    <form class="admin-card admin-edit-form" method="POST" enctype="multipart/form-data" action="{{ $story->exists ? route('admin.stories.update', $story) : route('admin.stories.store') }}" data-story-upload-form>
        @csrf
        @if ($story->exists)
            @method('PUT')
        @endif

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
                <select class="admin-form-control" id="municipality_id" name="municipality_id" data-searchable-select data-search-placeholder="市町村を検索">
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
            <div class="admin-form-group admin-form-wide">
                <label class="admin-form-label" for="related_images">関連画像</label>
                @if ($story->exists && $story->relatedImages->isNotEmpty())
                    <div class="admin-related-images">
                        @foreach ($story->relatedImages as $image)
                            <figure class="admin-related-image">
                                <button
                                    class="admin-related-image-open"
                                    type="button"
                                    data-open-related-image
                                    data-image-src="{{ asset('storage/' . $image->path) }}"
                                    data-image-alt="{{ $image->original_name }}"
                                    aria-label="{{ $image->original_name }}を拡大表示"
                                >
                                    <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $image->original_name }}" loading="lazy">
                                </button>
                                <figcaption title="{{ $image->original_name }}">{{ $image->original_name }}</figcaption>
                                <label class="admin-check admin-related-image-delete">
                                    <input
                                        type="checkbox"
                                        name="delete_related_image_ids[]"
                                        value="{{ $image->id }}"
                                        @checked(in_array($image->id, old('delete_related_image_ids', [])))
                                    >
                                    削除する
                                </label>
                            </figure>
                        @endforeach
                    </div>
                @endif
                <input class="admin-form-control" id="related_images" name="related_images[]" type="file" accept="image/*" multiple>
                <small class="admin-help">画像全体を確認できます。クリックすると拡大表示します。この民話に関連する画像を複数枚選択でき、各画像は10MB以下です。関連画像は管理画面でのみ表示されます。</small>
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
                <label class="admin-form-label" for="related_site_url">関連サイトURL</label>
                <input class="admin-form-control" id="related_site_url" name="related_site_url" type="url" value="{{ old('related_site_url', $story->related_site_url) }}" maxlength="2048">
                <small class="admin-help">民話の詳細画面から新しいタブでリンクを開きます。</small>
            </div>
            <div class="admin-form-group admin-form-wide">
                <label class="admin-form-label" for="source">出典</label>
                <textarea class="admin-form-control admin-textarea-short" id="source" name="source" rows="3">{{ old('source', $story->source) }}</textarea>
            </div>
            <div class="admin-form-group admin-form-wide">
                <label class="admin-form-label" for="admin_memo">管理者メモ（Markdown）</label>
                <textarea class="admin-form-control admin-markdown-editor" id="admin_memo" name="admin_memo" rows="8">{{ old('admin_memo', $story->admin_memo) }}</textarea>
                <small class="admin-help">このメモは管理画面でのみ表示され、エンドユーザー画面には公開されません。</small>
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

    <dialog class="admin-image-dialog" data-related-image-dialog aria-label="関連画像の拡大表示">
        <button class="admin-image-dialog-close" type="button" data-close-related-image aria-label="閉じる">×</button>
        <img data-related-image-preview src="" alt="">
        <p data-related-image-name></p>
    </dialog>
@endsection
