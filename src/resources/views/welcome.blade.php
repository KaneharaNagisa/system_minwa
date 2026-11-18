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
                <span class="text-2xl font-bold">{{ $stories->total() }}</span>
                <span class="text-[10px] text-muted">編</span>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-10">
        <section class="paper-panel relative p-5 md:p-8">
            <span class="corner corner-tl"></span><span class="corner corner-tr"></span>
            <span class="corner corner-bl"></span><span class="corner corner-br"></span>
            <div class="grid gap-8 md:grid-cols-2">
                <div>
                    <h2 class="mb-1 flex items-center gap-2 text-lg font-bold">
                        <svg class="h-5 w-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2"/></svg>
                        信濃国 絵図から探す
                    </h2>
                    <p class="mb-4 text-xs text-muted">市町村を選ぶと、その地域の民話に絞り込めます。</p>
                    <div class="flex min-h-80 items-center justify-center rounded-sm border border-gold/15 bg-stone-100/70 p-4">
                        <div class="grid w-full grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach ($municipalities as $municipality)
                                @php $active = (string) $municipality->id === $selectedMunicipality; @endphp
                                <a href="{{ route('stories.index', array_filter(['municipality' => $active ? null : $municipality->id, 'tag' => $selectedTag, 'q' => $keyword])) }}"
                                   class="rounded-sm border px-3 py-3 text-center text-sm transition {{ $active ? 'border-vermilion bg-vermilion text-white' : 'border-gold/25 bg-white/90 hover:border-gold' }}">
                                    <span class="block text-xs opacity-75">{{ $municipality->region_name }}</span>
                                    <span class="font-bold">{{ $municipality->name }}</span>
                                    <span class="ml-1 text-xs">（{{ $municipality->stories_count }}）</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('stories.index') }}" class="space-y-5">
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
            </div>
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
