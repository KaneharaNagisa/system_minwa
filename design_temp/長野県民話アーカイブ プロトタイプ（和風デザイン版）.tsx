import React, { useState, useMemo } from 'react';
import { 
  Search, MapPin, BookOpen, Plus, Upload, FileText, 
  CheckCircle, Eye, Settings, Users, Sparkles, X, ChevronRight, Loader2 
} from 'lucide-react';

// --- 初期データ（長野県市町村マスタの一部と初期民話） ---
const MUNICIPALITIES = [
  { id: 'nagano', name: '長野市', region: '北信', x: 165, y: 75 },
  { id: 'chikuma', name: '千曲市', region: '北信', x: 160, y: 105 },
  { id: 'shinanomachi', name: '信濃町', region: '北信', x: 150, y: 45 },
  { id: 'ueda', name: '上田市', region: '東信', x: 205, y: 120 },
  { id: 'komoro', name: '小諸市', region: '東信', x: 230, y: 135 },
  { id: 'matsumoto', name: '松本市', region: '中信', x: 125, y: 160 },
  { id: 'azumino', name: '安曇野市', region: '中信', x: 105, y: 135 },
  { id: 'suwa', name: '諏訪市', region: '南信', x: 155, y: 205 },
  { id: 'komagane', name: '駒ヶ根市', region: '南信', x: 125, y: 255 },
  { id: 'iida', name: '飯田市', region: '南信', x: 115, y: 310 },
  { id: 'anan', name: '阿南町', region: '南信', x: 110, y: 345 },
];

const INITIAL_STORIES = [
  {
    id: 1,
    title: '鬼女紅葉伝説',
    kana: 'きじょもみじでんせつ',
    municipalityId: 'nagano',
    tags: ['鬼・妖怪', '歴史'],
    summary: '都から戸隠・鬼無里へ流された才色兼備の女性「紅葉」が、やがて鬼女と化し平維茂に討伐された伝説。',
    body: '平安時代の昔、都から信濃の国の山深い鬼無里（きなさ）へと流された美しい女性がいました。彼女の名は紅葉。都の文化や医療を村人に教え慕われていましたが、やがて都への思いが募り、妖術を使う鬼女へと姿を変えてしまいます。朝廷の命を受けた平維茂は、激戦の末に紅葉を討伐したと伝えられています。',
    source: '長野市戸隠・鬼無里伝承'
  },
  {
    id: 2,
    title: '姨捨山',
    kana: 'おばすてやま',
    municipalityId: 'chikuma',
    tags: ['知恵', '家族愛'],
    summary: '年老いた母を山に捨てる掟の時代、帰り道に迷わないよう枝を折る母の愛に打たれ、息子が連れ帰る物語。',
    body: '昔、60歳になった親を山に捨てなければならない悲しい掟がありました。ある若い男が母を背負って山を登ると、母は道々で木の枝を折り続けます。「お前が帰り道に迷わないように」という母の言葉に涙した男は、母を連れ帰り家の床下に隠しました。後に殿様が出した難題を母の知恵で解決したことから、その掟は廃止されました。',
    source: '千曲市さらしなの里伝承'
  },
  {
    id: 3,
    title: '泉小太郎',
    kana: 'いずみこたろう',
    municipalityId: 'matsumoto',
    tags: ['龍神・水', '開拓'],
    summary: '犀龍の母と人間の父の間に生まれた小太郎が、母の背に乗り岩山を突き破って安曇平を開拓した神話。',
    body: '大昔、松本や安曇野の一帯は大きな湖でした。犀龍（さいりゅう）の母から生まれた泉小太郎は、母を探して旅に出ます。再会した母の背に乗った小太郎は、人々が暮らせる豊かな土地を作るため、立ちはだかる山清路の岩山を突き破りました。水は日本海へと流れ去り、肥沃な安曇平が誕生したと伝えられています。',
    source: '松本・安曇野地域伝承'
  },
  {
    id: 4,
    title: '霊犬・早太郎',
    kana: 'れいけんはやたろう',
    municipalityId: 'komagane',
    tags: ['動物', '忠義'],
    summary: '遠州の村を襲う大ヒヒを、信濃の光前寺で飼われていた強い犬「早太郎」が命懸けで退治した物語。',
    body: '昔、遠州の村では毎年、神社の化け物に若い娘を差し出さねばなりませんでした。旅の僧が化け物の「信濃の早太郎には知らせるな」という呟きを聞き、駒ヶ根の光前寺から強い犬・早太郎を借り受けます。早太郎は見事に大ヒヒを退治しましたが、自らも深手を負い、光前寺まで辿り着いた後に力尽きて息を引き取りました。',
    source: '駒ヶ根市 光前寺'
  },
  {
    id: 5,
    title: '新野の盆踊りと関氏の伝承',
    kana: 'にいののぼんおどり',
    municipalityId: 'anan',
    tags: ['歴史', '祭り'],
    summary: '室町時代から500年以上にわたり、先祖の霊を送り迎えしてきたユネスコ無形文化遺産の祈りの伝承。',
    body: '山深い阿南町新野の盆地では、室町時代から途絶えることなく「新野の盆踊り」が受け継がれてきました。楽器を使わず、音頭取りの唄と踊り手の手拍子・足拍子だけで夜通し踊り明かします。お盆の最終日には、切子灯籠を先頭に先祖の霊を送り出す「踊り神送り」が行われ、地域の人々の深い祈りが込められています。',
    source: '阿南町新野 保存会伝承'
  }
];

// 和紙テクスチャーと和柄パターン用のSVG定義
const WashiSvgDefs = () => (
  <svg className="hidden">
    <defs>
      {/* 和紙の繊維ノイズ */}
      <filter id="washi-noise">
        <feTurbulence type="fractalNoise" baseFrequency="0.65" numOctaves="3" stitchTiles="stitch" />
        <feColorMatrix type="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 0.05 0" />
      </filter>
      {/* 七宝・幾何学和柄パターン */}
      <pattern id="wagara-shippo" width="24" height="24" patternUnits="userSpaceOnUse">
        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" strokeWidth="1" opacity="0.35" />
        <circle cx="0" cy="0" r="10" fill="none" stroke="currentColor" strokeWidth="1" opacity="0.35" />
        <circle cx="24" cy="0" r="10" fill="none" stroke="currentColor" strokeWidth="1" opacity="0.35" />
        <circle cx="0" cy="24" r="10" fill="none" stroke="currentColor" strokeWidth="1" opacity="0.35" />
        <circle cx="24" cy="24" r="10" fill="none" stroke="currentColor" strokeWidth="1" opacity="0.35" />
      </pattern>
    </defs>
  </svg>
);

export default function App() {
  const [mode, setMode] = useState('user'); // 'user' | 'admin'
  const [stories, setStories] = useState(INITIAL_STORIES);

  // エンドユーザー画面用 State
  const [selectedMunicipality, setSelectedMunicipality] = useState('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [activeStory, setActiveStory] = useState(null);
  const [fontSize, setFontSize] = useState('text-base');

  // 管理画面用 State
  const [uploadedFiles, setUploadedFiles] = useState([]);
  const [isOcrRunning, setIsOcrRunning] = useState(false);
  const [ocrCompleted, setOcrCompleted] = useState(false);
  const [newTitle, setNewTitle] = useState('');
  const [newKana, setNewKana] = useState('');
  const [newMuniId, setNewMuniId] = useState('nagano');
  const [newSummary, setNewSummary] = useState('');
  const [newBody, setNewBody] = useState('');
  const [newSource, setNewSource] = useState('');
  const [saveSuccessMsg, setSaveSuccessMsg] = useState('');

  // 市町村ごとの登録件数を集計
  const storyCountsByMuni = useMemo(() => {
    const counts = {};
    MUNICIPALITIES.forEach(m => (counts[m.id] = 0));
    stories.forEach(s => {
      if (counts[s.municipalityId] !== undefined) {
        counts[s.municipalityId]++;
      }
    });
    return counts;
  }, [stories]);

  // 絞り込み済み民話
  const filteredStories = useMemo(() => {
    return stories.filter(story => {
      const matchMuni = selectedMunicipality === 'all' || story.municipalityId === selectedMunicipality;
      const matchQuery =
        searchQuery === '' ||
        story.title.includes(searchQuery) ||
        story.summary.includes(searchQuery) ||
        story.body.includes(searchQuery);
      return matchMuni && matchQuery;
    });
  }, [stories, selectedMunicipality, searchQuery]);

  const handleFileChange = (e) => {
    const files = Array.from(e.target.files || []);
    if (files.length > 0) {
      setUploadedFiles(files.map(f => f.name));
      setOcrCompleted(false);
    }
  };

  const handleRunOcr = () => {
    if (uploadedFiles.length === 0) return;
    setIsOcrRunning(true);
    setTimeout(() => {
      setIsOcrRunning(false);
      setOcrCompleted(true);
      setNewTitle('黒姫物語（OCR自動抽出）');
      setNewKana('くろひめものがたり');
      setNewMuniId('shinanomachi');
      setNewSummary('美しい黒姫に恋をした黒竜が若者に化けて結婚を申し込むが、殿様の策略で傷つき大蛇に戻ってしまう悲恋の物語。');
      setNewBody(
        '昔、信濃の国に黒姫という美しいお姫様がいました。ある日、野立てをしていた姫を見た山の黒竜が恋をし、美しい若者に姿を変えて結婚を申し込みます。しかし殿様は無理難題を出して断ろうとし、怒った殿様の策略によって黒竜は傷つき大蛇の姿に戻ってしまいました。大水を起こした黒竜は、最後は姫への愛ゆえに自ら姿を消し、姫もまた後を追って池に身を投げたと伝えられています。'
      );
      setNewSource('アップロード画像（' + uploadedFiles.join(', ') + '）より自動抽出');
    }, 1200);
  };

  const handleSaveStory = (e) => {
    e.preventDefault();
    if (!newTitle || !newBody) return;
    const newStory = {
      id: Date.now(),
      title: newTitle,
      kana: newKana,
      municipalityId: newMuniId,
      tags: ['新規登録'],
      summary: newSummary || newBody.slice(0, 60) + '...',
      body: newBody,
      source: newSource || '管理者登録'
    };
    setStories([newStory, ...stories]);
    setNewTitle('');
    setNewKana('');
    setNewSummary('');
    setNewBody('');
    setNewSource('');
    setUploadedFiles([]);
    setOcrCompleted(false);
    setSaveSuccessMsg('民話を登録しました！エンドユーザー画面に即時反映されています。');
    setTimeout(() => setSaveSuccessMsg(''), 4000);
  };

  const getMuniName = (id) => MUNICIPALITIES.find(m => m.id === id)?.name || '不明';

  return (
    <div className="min-h-screen flex flex-col font-sans">
      <WashiSvgDefs />

      {/* プロトタイプ用 画面切り替えバー */}
      <div className="bg-slate-900 text-white px-4 py-2 flex items-center justify-between text-sm sticky top-0 z-50 shadow">
        <div className="flex items-center gap-2 font-medium">
          <Sparkles className="w-4 h-4 text-amber-400" />
          <span>長野県民話アーカイブ プロトタイプ</span>
        </div>
        <div className="flex bg-slate-800 rounded-lg p-1 gap-1">
          <button
            onClick={() => setMode('user')}
            className={`px-3 py-1 rounded-md flex items-center gap-1.5 transition ${
              mode === 'user' ? 'bg-emerald-800 text-white font-semibold' : 'text-slate-300 hover:text-white'
            }`}
          >
            <Users className="w-4 h-4" />
            エンドユーザー画面（和風）
          </button>
          <button
            onClick={() => setMode('admin')}
            className={`px-3 py-1 rounded-md flex items-center gap-1.5 transition ${
              mode === 'admin' ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:text-white'
            }`}
          >
            <Settings className="w-4 h-4" />
            管理画面（OCR登録）
          </button>
        </div>
      </div>

      {/* =========================================================
          1. エンドユーザー画面（和紙テクスチャー ＋ 和モダン）
         ========================================================= */}
      {mode === 'user' && (
        <div
          className="flex-1 relative text-stone-800 overflow-hidden"
          style={{ backgroundColor: '#F6F2E9' }}
        >
          {/* 和紙テクスチャーオーバーレイ */}
          <svg className="pointer-events-none fixed inset-0 w-full h-full opacity-60 z-0">
            <rect width="100%" height="100%" filter="url(#washi-noise)" />
          </svg>

          {/* 和柄の丸紋装飾（右上のあしらい） */}
          <svg className="pointer-events-none absolute top-0 right-0 w-72 h-72 text-emerald-900 opacity-15 z-0" viewBox="0 0 200 200">
            <circle cx="160" cy="40" r="65" fill="url(#wagara-shippo)" />
            <circle cx="110" cy="70" r="30" fill="#C84B31" opacity="0.4" />
            <circle cx="170" cy="110" r="22" fill="#6B3FA0" opacity="0.3" />
          </svg>

          {/* 和風ヘッダー（金枠円形モチーフと縦書き短冊） */}
          <header className="relative z-10 pt-10 pb-8 px-4 border-b border-amber-700/20">
            <div className="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
              <div className="flex items-center gap-5">
                {/* 縦書きの黒短冊ラベル */}
                <div
                  className="bg-stone-900 text-stone-100 px-2.5 py-4 text-xs font-serif tracking-widest border border-amber-500/60 shadow-sm hidden sm:block"
                  style={{ writingMode: 'vertical-rl' }}
                >
                  信州・語り継ぎの記憶
                </div>
                <div>
                  <p className="text-amber-800 text-xs tracking-widest uppercase font-serif mb-1">
                    SHINSHU FOLKLORE ARCHIVE
                  </p>
                  <h1 className="text-3xl md:text-4xl font-serif font-bold tracking-wider text-stone-900">
                    信州・長野県 民話の語り部
                  </h1>
                  <p className="text-stone-600 text-sm mt-2 font-serif leading-relaxed">
                    全七十七市町村に受け継がれる、ふるさとの物語を未来へ。
                  </p>
                </div>
              </div>

              {/* 金色の円形紋章風バッジ */}
              <div className="w-24 h-24 rounded-full border-2 border-amber-600/70 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center text-center shadow-sm p-2">
                <span className="text-[10px] text-amber-800 font-serif tracking-widest">登録民話</span>
                <span className="text-2xl font-serif font-bold text-stone-900">{stories.length}</span>
                <span className="text-[10px] text-stone-500 font-serif">編</span>
              </div>
            </div>
          </header>

          <main className="relative z-10 max-w-5xl mx-auto px-4 py-10 space-y-12">
            {/* 長野県シルエット地図 ＆ 市町村別件数 */}
            <section className="bg-white/80 backdrop-blur-sm rounded-sm p-6 md:p-8 shadow-sm border border-amber-800/20 relative">
              {/* 四隅の和風飾り枠 */}
              <div className="absolute top-2 left-2 w-3 h-3 border-t-2 border-l-2 border-amber-700/50" />
              <div className="absolute top-2 right-2 w-3 h-3 border-t-2 border-r-2 border-amber-700/50" />
              <div className="absolute bottom-2 left-2 w-3 h-3 border-b-2 border-l-2 border-amber-700/50" />
              <div className="absolute bottom-2 right-2 w-3 h-3 border-b-2 border-r-2 border-amber-700/50" />

              <div className="flex flex-col md:flex-row gap-8 items-center">
                {/* 左側：長野県シルエットSVG */}
                <div className="w-full md:w-1/2 flex flex-col items-center">
                  <h2 className="text-lg font-serif font-bold text-stone-900 mb-1 flex items-center gap-2 self-start">
                    <MapPin className="w-5 h-5 text-amber-700" />
                    信濃国 絵図から探す
                  </h2>
                  <p className="text-xs text-stone-500 mb-4 self-start font-serif">
                    丸印の数字は登録されている民話の数です。クリックで絞り込めます。
                  </p>
                  <div className="relative bg-stone-100/70 rounded-sm border border-amber-900/15 p-4 w-full flex justify-center">
                    <svg viewBox="0 0 300 400" className="w-64 h-80 drop-shadow-sm">
                      <path
                        d="M150,20 L195,35 L210,85 L255,120 L240,175 L195,215 L175,260 L145,365 L95,360 L75,280 L95,220 L70,150 L95,85 L125,35 Z"
                        fill="#EAE4D3"
                        stroke="#8C6D3F"
                        strokeWidth="2"
                      />
                      {MUNICIPALITIES.map((m) => {
                        const count = storyCountsByMuni[m.id] || 0;
                        const isSelected = selectedMunicipality === m.id;
                        return (
                          <g
                            key={m.id}
                            onClick={() => setSelectedMunicipality(isSelected ? 'all' : m.id)}
                            className="cursor-pointer group"
                          >
                            <circle
                              cx={m.x}
                              cy={m.y}
                              r={isSelected ? 14 : 11}
                              fill={isSelected ? '#B93822' : count > 0 ? '#2C4C3E' : '#94A3B8'}
                              stroke="#FAF8F5"
                              strokeWidth="1.5"
                              className="transition-all"
                            />
                            <text
                              x={m.x}
                              y={m.y + 4}
                              textAnchor="middle"
                              fill="#FFFFFF"
                              fontSize="10"
                              fontWeight="bold"
                            >
                              {count}
                            </text>
                            <text
                              x={m.x + 15}
                              y={m.y + 4}
                              fill="#2D2D2D"
                              fontSize="10"
                              fontFamily="serif"
                              fontWeight={isSelected ? 'bold' : 'normal'}
                              className="select-none"
                            >
                              {m.name}
                            </text>
                          </g>
                        );
                      })}
                    </svg>
                  </div>
                </div>

                {/* 右側：検索＆市町村リスト */}
                <div className="w-full md:w-1/2 space-y-5">
                  <div>
                    <label className="block text-sm font-serif font-bold text-stone-800 mb-1.5">
                      ことばで探す
                    </label>
                    <div className="relative">
                      <Search className="w-4 h-4 text-stone-400 absolute left-3 top-3" />
                      <input
                        type="text"
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        placeholder="例：龍神、早太郎、鬼女..."
                        className="w-full pl-9 pr-4 py-2 border border-amber-900/25 rounded-sm focus:outline-none focus:ring-1 focus:ring-amber-700 bg-white/90 text-sm"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-sm font-serif font-bold text-stone-800 mb-1.5">
                      市区町村で絞り込む
                    </label>
                    <select
                      value={selectedMunicipality}
                      onChange={(e) => setSelectedMunicipality(e.target.value)}
                      className="w-full px-3 py-2 border border-amber-900/25 rounded-sm focus:outline-none focus:ring-1 focus:ring-amber-700 bg-white/90 text-sm font-serif"
                    >
                      <option value="all">すべての市町村（全 {stories.length} 件）</option>
                      {MUNICIPALITIES.map((m) => (
                        <option key={m.id} value={m.id}>
                          {m.region}：{m.name}（{storyCountsByMuni[m.id] || 0}件）
                        </option>
                      ))}
                    </select>
                  </div>

                  {selectedMunicipality !== 'all' && (
                    <button
                      onClick={() => setSelectedMunicipality('all')}
                      className="text-xs text-amber-800 underline hover:text-amber-600 font-serif"
                    >
                      絞り込みを解除してすべて表示
                    </button>
                  )}
                </div>
              </div>
            </section>

            {/* 民話一覧 */}
            <section>
              <div className="flex items-center justify-between mb-6 pb-2 border-b border-amber-800/20">
                <h2 className="text-xl font-serif font-bold text-stone-900 flex items-center gap-2">
                  <BookOpen className="w-5 h-5 text-amber-800" />
                  {selectedMunicipality === 'all'
                    ? '語り継がれる民話'
                    : `${getMuniName(selectedMunicipality)}の民話`}
                  <span className="text-sm font-serif font-normal text-stone-500">
                    （{filteredStories.length}編）
                  </span>
                </h2>
              </div>

              {filteredStories.length === 0 ? (
                <div className="bg-white/80 rounded-sm p-10 text-center border border-amber-800/20 text-stone-500 font-serif">
                  該当する民話がまだ登録されていません。管理画面から登録できます。
                </div>
              ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {filteredStories.map((story) => (
                    <article
                      key={story.id}
                      onClick={() => setActiveStory(story)}
                      className="bg-white/85 backdrop-blur-sm rounded-sm p-6 shadow-sm border border-amber-900/20 hover:border-amber-700 hover:shadow-md transition cursor-pointer flex flex-col justify-between relative"
                    >
                      <div>
                        <div className="flex items-center justify-between mb-3">
                          <span className="text-xs font-serif px-2.5 py-0.5 bg-stone-800 text-amber-100 border border-amber-500/40">
                            {getMuniName(story.municipalityId)}
                          </span>
                        </div>
                        <h3 className="text-lg font-serif font-bold text-stone-900 mb-2 tracking-wide">
                          {story.title}
                        </h3>
                        <p className="text-sm text-stone-600 line-clamp-3 leading-relaxed font-serif">
                          {story.summary}
                        </p>
                      </div>
                      <div className="mt-5 pt-3 border-t border-amber-900/10 flex items-center justify-between text-xs text-amber-900 font-serif font-semibold">
                        <span>物語を読む</span>
                        <ChevronRight className="w-4 h-4" />
                      </div>
                    </article>
                  ))}
                </div>
              )}
            </section>
          </main>

          {/* 民話詳細モーダル */}
          {activeStory && (
            <div className="fixed inset-0 bg-stone-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
              <div
                className="rounded-sm max-w-2xl w-full max-h-screen overflow-y-auto shadow-2xl border-2 border-amber-700/40 relative"
                style={{ backgroundColor: '#F6F2E9' }}
              >
                <div className="sticky top-0 bg-stone-900 text-stone-100 px-6 py-4 flex items-center justify-between border-b border-amber-600/50">
                  <div>
                    <span className="text-xs bg-amber-800/80 px-2 py-0.5 text-amber-100 font-serif">
                      {getMuniName(activeStory.municipalityId)}
                    </span>
                    <h3 className="text-xl font-serif font-bold mt-1 tracking-wider">{activeStory.title}</h3>
                  </div>
                  <button onClick={() => setActiveStory(null)} className="p-1 hover:bg-stone-800 rounded">
                    <X className="w-5 h-5" />
                  </button>
                </div>
                <div className="p-6 md:p-8 space-y-6">
                  <div className="flex items-center justify-end gap-2 text-xs text-stone-600 font-serif">
                    <span>文字の大きさ：</span>
                    <button
                      onClick={() => setFontSize('text-sm')}
                      className={`px-2.5 py-1 border border-amber-900/30 ${fontSize === 'text-sm' ? 'bg-stone-800 text-white' : 'bg-white/80'}`}
                    >
                      小
                    </button>
                    <button
                      onClick={() => setFontSize('text-base')}
                      className={`px-2.5 py-1 border border-amber-900/30 ${fontSize === 'text-base' ? 'bg-stone-800 text-white' : 'bg-white/80'}`}
                    >
                      中
                    </button>
                    <button
                      onClick={() => setFontSize('text-lg')}
                      className={`px-2.5 py-1 border border-amber-900/30 ${fontSize === 'text-lg' ? 'bg-stone-800 text-white' : 'bg-white/80'}`}
                    >
                      大
                    </button>
                  </div>

                  <div className={`leading-loose text-stone-800 font-serif whitespace-pre-wrap ${fontSize}`}>
                    {activeStory.body}
                  </div>

                  <div className="pt-4 border-t border-amber-900/20 text-xs text-stone-500 font-serif">
                    出典・伝承：{activeStory.source}
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>
      )}

      {/* =========================================================
          2. 管理画面（スレート基調・OCR自動入力対応）
         ========================================================= */}
      {mode === 'admin' && (
        <div className="flex-1 bg-slate-100 text-slate-900">
          <header className="bg-slate-800 text-white py-5 px-6 shadow">
            <div className="max-w-5xl mx-auto flex items-center justify-between">
              <div>
                <h1 className="text-xl font-bold">長野県民話アーカイブ 管理画面</h1>
                <p className="text-xs text-slate-300 mt-0.5">複数画像からの文字自動認識（OCR）＆民話登録</p>
              </div>
              <div className="text-sm bg-slate-700 px-3 py-1.5 rounded-lg">
                登録総数：<span className="font-bold text-emerald-400">{stories.length}</span> 件
              </div>
            </div>
          </header>

          <main className="max-w-5xl mx-auto px-4 py-8 space-y-6">
            {saveSuccessMsg && (
              <div className="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm font-medium">
                <CheckCircle className="w-5 h-5 text-emerald-600" />
                {saveSuccessMsg}
              </div>
            )}

            {/* ステップ1：複数画像アップロード ＆ OCR */}
            <section className="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
              <h2 className="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                <Upload className="w-5 h-5 text-blue-600" />
                1. 資料画像のアップロードと文字の自動読み込み（OCR）
              </h2>
              <p className="text-xs text-slate-500 mb-4">
                書籍やパンフレットを撮影した画像（複数選択可）をアップロードすると、文字を自動で読み取り下のフォームに入力します。
              </p>

              <div className="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center bg-slate-50">
                <input
                  type="file"
                  multiple
                  accept="image/*"
                  onChange={handleFileChange}
                  id="ocr-file-input"
                  className="hidden"
                />
                <label
                  htmlFor="ocr-file-input"
                  className="cursor-pointer inline-flex items-center gap-2 bg-white border border-slate-300 px-4 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 shadow-sm"
                >
                  <Plus className="w-4 h-4" />
                  画像ファイルを選択（複数可）
                </label>

                {uploadedFiles.length > 0 && (
                  <div className="mt-4 space-y-3">
                    <div className="flex flex-wrap justify-center gap-2">
                      {uploadedFiles.map((name, idx) => (
                        <span key={idx} className="text-xs bg-blue-50 text-blue-800 px-2.5 py-1 rounded border border-blue-200 flex items-center gap-1">
                          <FileText className="w-3.5 h-3.5" />
                          {name}
                        </span>
                      ))}
                    </div>
                    <button
                      type="button"
                      onClick={handleRunOcr}
                      disabled={isOcrRunning}
                      className="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow-sm disabled:opacity-50"
                    >
                      {isOcrRunning ? (
                        <>
                          <Loader2 className="w-4 h-4 animate-spin" />
                          文字を解析中...
                        </>
                      ) : (
                        <>
                          <Sparkles className="w-4 h-4" />
                          画像から文字を読み込む（OCR実行）
                        </>
                      )}
                    </button>
                  </div>
                )}

                {ocrCompleted && (
                  <p className="mt-3 text-xs text-emerald-700 font-semibold">
                    ✓ 画像からテキストを抽出しました！下のフォームで内容を確認・修正して登録してください。
                  </p>
                )}
              </div>
            </section>

            {/* ステップ2：民話登録フォーム */}
            <section className="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
              <h2 className="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                <FileText className="w-5 h-5 text-blue-600" />
                2. 民話データ登録フォーム
              </h2>

              <form onSubmit={handleSaveStory} className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-xs font-bold text-slate-700 mb-1">民話タイトル *</label>
                    <input
                      type="text"
                      required
                      value={newTitle}
                      onChange={(e) => setNewTitle(e.target.value)}
                      placeholder="例：黒姫物語"
                      className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-bold text-slate-700 mb-1">よみがな</label>
                    <input
                      type="text"
                      value={newKana}
                      onChange={(e) => setNewKana(e.target.value)}
                      placeholder="例：くろひめものがたり"
                      className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-xs font-bold text-slate-700 mb-1">該当する市区町村 *</label>
                    <select
                      value={newMuniId}
                      onChange={(e) => setNewMuniId(e.target.value)}
                      className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      {MUNICIPALITIES.map((m) => (
                        <option key={m.id} value={m.id}>
                          {m.region}：{m.name}
                        </option>
                      ))}
                    </select>
                  </div>
                  <div>
                    <label className="block text-xs font-bold text-slate-700 mb-1">出典・参考文献</label>
                    <input
                      type="text"
                      value={newSource}
                      onChange={(e) => setNewSource(e.target.value)}
                      placeholder="例：〇〇村誌、〇〇保存会資料"
                      className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-xs font-bold text-slate-700 mb-1">あらすじ（一覧表示用）</label>
                  <input
                    type="text"
                    value={newSummary}
                    onChange={(e) => setNewSummary(e.target.value)}
                    placeholder="1〜2行程度の短い要約"
                    className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>

                <div>
                  <label className="block text-xs font-bold text-slate-700 mb-1">民話本文 *</label>
                  <textarea
                    required
                    rows={6}
                    value={newBody}
                    onChange={(e) => setNewBody(e.target.value)}
                    placeholder="ここに民話の本文を入力（または上のOCRで自動入力）"
                    className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 leading-relaxed"
                  />
                </div>

                <div className="flex justify-end gap-3 pt-2">
                  <button
                    type="button"
                    onClick={() => setMode('user')}
                    className="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 flex items-center gap-1.5"
                  >
                    <Eye className="w-4 h-4" />
                    公開画面を確認
                  </button>
                  <button
                    type="submit"
                    className="px-6 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold shadow-sm"
                  >
                    この内容で登録する
                  </button>
                </div>
              </form>
            </section>
          </main>
        </div>
      )}
    </div>
  );
}