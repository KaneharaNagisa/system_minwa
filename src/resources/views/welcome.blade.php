<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>長野県民話アーカイブ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-washi text-ink">
    <svg class="absolute h-0 w-0" aria-hidden="true">
        <defs>
            <pattern id="shippo" width="24" height="24" patternUnits="userSpaceOnUse">
                <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="1" opacity=".35"/>
                <circle cx="0" cy="0" r="10" fill="none" stroke="currentColor" stroke-width="1" opacity=".35"/>
                <circle cx="24" cy="0" r="10" fill="none" stroke="currentColor" stroke-width="1" opacity=".35"/>
            </pattern>
        </defs>
    </svg>

    <header class="border-b border-gold/25 px-4 py-8">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="hidden border border-gold/60 bg-ink px-2.5 py-4 text-xs tracking-widest text-white sm:block vertical-label">
                    信州・語り継ぎの記憶
                </div>
                <div>
                    <p class="mb-1 text-xs tracking-widest text-vermilion">SHINSHU FOLKLORE ARCHIVE</p>
                    <h1 class="text-2xl font-bold tracking-wider text-ink md:text-4xl">信州・長野県 民話の語り部</h1>
                    <p class="mt-2 font-serif text-sm text-muted">全市町村に受け継がれる、ふるさとの物語を未来へ。</p>
                </div>
            </div>
            <div class="flex h-24 w-24 shrink-0 flex-col items-center justify-center rounded-full border-2 border-gold/70 bg-white/70 text-center shadow-sm">
                <span class="text-[10px] text-amber-800">登録民話</span>
                <span class="text-2xl font-bold">{{ $registeredStoryCount }}</span>
                <span class="text-[10px] text-muted">編</span>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-10">
        <section class="paper-panel relative p-5 md:p-8">
            <span class="corner corner-tl"></span><span class="corner corner-tr"></span>
            <span class="corner corner-bl"></span><span class="corner corner-br"></span>
            <div>
                <div>
                    <h2 class="mb-1 flex items-center gap-2 text-lg font-bold">
                        <svg class="h-5 w-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2"/></svg>
                        信濃国 絵図から探す
                    </h2>
                    <p class="mb-4 text-xs text-muted">市町村の形を選ぶか、一覧から選択すると、その地域の民話に絞り込めます。</p>
                </div>

                <div class="grid items-start gap-6 lg:grid-cols-2">
                    <div class="relative rounded-sm border border-gold/15 bg-stone-100/70 p-3 sm:p-5">
                        @if ($prefectureWideMunicipality)
                            @php
                                $prefectureWideActive = (string) $prefectureWideMunicipality->id === $selectedMunicipality;
                                $prefectureWideParameters = array_filter([
                                    'municipality' => $prefectureWideActive ? null : $prefectureWideMunicipality->id,
                                    'tag' => $selectedTag,
                                    'q' => $keyword,
                                ]);
                            @endphp
                            <a href="{{ route('stories.index', $prefectureWideParameters) }}"
                               class="absolute left-3 top-3 z-10 flex h-24 w-24 flex-col items-center justify-center rounded-full border-2 text-center shadow-md transition sm:left-5 sm:top-5 sm:h-28 sm:w-28 {{ $prefectureWideActive ? 'border-ink bg-ink text-white ring-4 ring-vermilion/20' : 'border-gold/70 bg-white/95 text-ink hover:bg-washi' }}"
                               @if ($prefectureWideActive) aria-current="page" @endif
                               aria-label="長野県全域、登録民話{{ $prefectureWideMunicipality->stories_count }}編">
                                <span class="text-[10px] font-bold leading-tight sm:text-xs">長野県全域</span>
                                <span class="mt-1 text-xl font-bold sm:text-2xl">{{ $prefectureWideMunicipality->stories_count }}</span>
                                <span class="text-[10px] text-muted {{ $prefectureWideActive ? '!text-amber-100' : '' }}">編</span>
                            </a>
                        @endif
                        <svg viewBox="0 0 560 720" class="mx-auto block h-auto w-full max-w-[31rem]" role="img" aria-labelledby="nagano-map-title nagano-map-description">
                            <title id="nagano-map-title">長野県の市町村別地図</title>
                            <desc id="nagano-map-description">長野県内77市町村の境界図です。各市町村を選ぶと登録民話で絞り込みます。</desc>
                            @foreach ($mapAreas as $area)
                                @php
                                    $active = (string) $area['selection_value'] === $selectedMunicipality;
                                    $parameters = array_filter([
                                        'municipality' => $active ? null : $area['selection_value'],
                                        'tag' => $selectedTag,
                                        'q' => $keyword,
                                    ]);
                                @endphp
                                <a href="{{ route('stories.index', $parameters) }}" class="nagano-map-link" aria-label="{{ $area['name'] }}、登録民話{{ $area['stories_count'] }}編{{ $active ? '、選択中' : '' }}">
                                    <title>{{ $area['name'] }}：登録民話 {{ $area['stories_count'] }} 編</title>
                                    <path
                                        d="{{ $area['path'] }}"
                                        class="nagano-map-area {{ $area['stories_count'] > 0 ? 'has-stories' : '' }} {{ $active ? 'is-selected' : '' }}"
                                    />
                                </a>
                            @endforeach
                        </svg>
                        <div class="mt-3 flex flex-wrap justify-center gap-x-4 gap-y-2 text-xs text-muted" aria-label="地図の凡例">
                            <span class="inline-flex items-center gap-1.5"><span class="map-legend-swatch has-stories"></span>登録あり</span>
                            <span class="inline-flex items-center gap-1.5"><span class="map-legend-swatch"></span>登録なし</span>
                            <span class="inline-flex items-center gap-1.5"><span class="map-legend-swatch is-selected"></span>選択中</span>
                        </div>
                        <p class="mt-3 text-center text-[10px] text-muted">市町村界：国土数値情報「行政区域データ」2020年版（国土交通省）を基に作成</p>
                    </div>

                    <div>
                        <h3 class="mb-2 text-sm font-bold">市町村別 登録民話件数 <span class="font-normal text-muted">（全{{ count($mapAreas) }}市町村）</span></h3>
                        <div class="max-h-[34rem] overflow-y-auto rounded-sm border border-gold/15 bg-white/70 p-2" data-municipality-list>
                            <ul class="grid grid-cols-2 gap-1.5 sm:grid-cols-3 lg:grid-cols-2">
                                @foreach ($mapAreas as $area)
                                    @php
                                        $active = (string) $area['selection_value'] === $selectedMunicipality;
                                        $parameters = array_filter([
                                            'municipality' => $active ? null : $area['selection_value'],
                                            'tag' => $selectedTag,
                                            'q' => $keyword,
                                        ]);
                                    @endphp
                                    <li @if ($active) data-selected-municipality @endif>
                                        <a href="{{ route('stories.index', $parameters) }}"
                                           @if ($active) aria-current="page" @endif
                                           class="flex items-center justify-between gap-1 rounded-sm border px-2 py-1.5 text-xs transition {{ $active ? 'border-vermilion bg-vermilion text-white' : 'border-transparent hover:border-gold/25 hover:bg-washi' }}">
                                            <span class="truncate">{{ $area['name'] }}</span>
                                            <span class="shrink-0 rounded-full px-1.5 py-0.5 {{ $active ? 'bg-white/20' : ($area['stories_count'] > 0 ? 'bg-amber-100 text-amber-900' : 'bg-stone-100 text-muted') }}">{{ $area['stories_count'] }}件</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <section class="paper-panel mt-8 p-5 md:p-8">
            <h2 class="mb-4 text-lg font-bold">条件を指定して探す</h2>
            <form method="GET" action="{{ route('stories.index') }}" class="grid gap-5 md:grid-cols-2 lg:grid-cols-4 lg:items-end">
                <div>
                    <label class="mb-1.5 block text-sm font-bold" for="q">ことばで探す</label>
                    <input id="q" name="q" value="{{ $keyword }}" type="search" placeholder="例：龍神、早太郎、鬼女..."
                           class="w-full rounded-sm border border-gold/25 bg-white/90 px-3 py-2 text-sm focus:border-gold focus:outline-none">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-bold" for="municipality">市町村で絞り込む</label>
                    <select id="municipality" name="municipality" class="w-full rounded-sm border border-gold/25 bg-white/90 px-3 py-2 text-sm">
                        <option value="">すべての市町村</option>
                        @foreach ($municipalities as $municipality)
                            <option value="{{ $municipality->id }}" @selected((string) $municipality->id === $selectedMunicipality)>
                                {{ $municipality->region_name }}：{{ $municipality->name }}（{{ $municipality->stories_count }}件）
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-bold" for="tag">テーマで絞り込む</label>
                    <select id="tag" name="tag" class="w-full rounded-sm border border-gold/25 bg-white/90 px-3 py-2 text-sm">
                        <option value="">すべてのテーマ</option>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}" @selected((string) $tag->id === $selectedTag)>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="rounded-sm bg-ink px-5 py-2 text-sm font-bold text-white hover:bg-ink-soft">検索する</button>
                    @if ($selectedMunicipality || $selectedTag || $keyword)
                        <a href="{{ route('stories.index') }}" class="rounded-sm border border-gold/30 px-5 py-2 text-sm">条件を解除</a>
                    @endif
                </div>
            </form>
        </section>

        <section class="mt-12">
            <div class="mb-6 flex items-center justify-between border-b border-gold/20 pb-2">
                <h2 class="text-xl font-bold">語り継がれる民話 <span class="text-sm font-normal text-muted">（{{ $stories->total() }}編）</span></h2>
            </div>
            @if ($stories->isEmpty())
                <div class="paper-panel p-10 text-center text-muted">該当する民話が見つかりませんでした。</div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($stories as $story)
                        <article class="paper-card flex flex-col justify-between">
                            <div>
                                <span class="inline-block border border-gold/40 bg-ink px-2.5 py-0.5 text-xs text-amber-100">
                                    {{ $story->municipality?->name ?? '市町村未設定' }}
                                </span>
                                <h3 class="mt-3 text-lg font-bold tracking-wide">{{ $story->title }}</h3>
                                @if ($story->title_kana)
                                    <p class="mt-1 text-xs text-muted">{{ $story->title_kana }}</p>
                                @endif
                                <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-muted">{{ $story->summary ?: Str::limit(strip_tags($story->body_markdown), 120) }}</p>
                            </div>
                            <a href="{{ route('stories.show', $story) }}" class="mt-5 flex items-center justify-between border-t border-gold/10 pt-3 text-xs font-bold text-amber-900">
                                <span>物語を読む</span><span aria-hidden="true">→</span>
                            </a>
                        </article>
                    @endforeach
                </div>
                <div class="mt-8">{{ $stories->links() }}</div>
            @endif
        </section>
    </main>
</body>
</html>
