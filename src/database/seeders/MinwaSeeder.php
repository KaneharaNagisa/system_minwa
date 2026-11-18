<?php

namespace Database\Seeders;

use App\Models\Municipality;
use App\Models\Story;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MinwaSeeder extends Seeder
{
    /**
     * まんが日本昔ばなし〜データベース〜（長野県・全40話）登録用Seeder。
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $municipalities = [
                '長野市' => '北信',
                '千曲市' => '北信',
                '中野市' => '北信',
                '須坂市' => '北信',
                '栄村' => '北信',
                '野沢温泉村' => '北信',
                '山ノ内町' => '北信',
                '佐久市' => '東信',
                '軽井沢町' => '東信',
                '松本市' => '中信',
                '塩尻市' => '中信',
                '安曇野市' => '中信',
                '白馬村' => '中信',
                '小谷村' => '中信',
                '木祖村' => '中信',
                '諏訪市' => '南信',
                '伊那市' => '南信',
                '駒ヶ根市' => '南信',
                '富士見町' => '南信',
                '阿南町' => '南信',
                '長野県全域' => '全域',
            ];
            $municipalityIds = [];

            foreach ($municipalities as $name => $regionName) {
                $municipality = Municipality::updateOrCreate(
                    ['name' => $name],
                    ['region_name' => $regionName],
                );
                $municipalityIds[$name] = $municipality->id;
            }

            $stories = [
                // 1
                [
                    'municipality_name' => '千曲市',
                    'title' => 'うばすて山',
                    'title_kana' => 'うばすてやま',
                    'summary' => '昔ある山奥に、六十歳以上のお年寄りは親であっても山へ捨てなければならないというお触れがある村がありました。',
                    'body_markdown' => '昔ある山奥に、六十歳以上のお年寄りは親であっても山へ捨てなければならない、というお触れ（ルール）がある所がありました。この村に一人の息子と年老いた母親のすむ家がありました。',
                    'main_image_path' => null,
                    'spot_name' => '姨捨山（冠着山）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県千曲市姨捨',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 2
                [
                    'municipality_name' => '松本市',
                    'title' => '小太郎と母龍',
                    'title_kana' => 'こたろうとははりゅう',
                    'summary' => '太古の昔、信濃の開拓に汗を流す若き長のもとに夜な夜な通う女と、その間に生まれた小太郎の物語。',
                    'body_markdown' => '太古の昔、人々が信濃に住み始め、開拓に汗を流していた。ここに、人々を束ねる若くてたくましい長（おさ）がいた。さて、長のもとに夜な夜な一人の女が通うようになる。',
                    'main_image_path' => null,
                    'spot_name' => '松本市・安曇野市周辺',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県松本市',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 3
                [
                    'municipality_name' => '長野市',
                    'title' => 'キジも鳴かずば',
                    'title_kana' => 'きじもなかずば',
                    'summary' => '毎年秋の大雨で氾濫する犀川のほとりの村で暮らす、弥平という父親とお千代という娘の物語。',
                    'body_markdown' => '犀川という川のほとりに、小さな村があった。この川は毎年秋の大雨になると氾濫し、村人を困らせていた。この村に弥平という父親とお千代という娘が二人で暮らしていた。',
                    'main_image_path' => null,
                    'spot_name' => '犀川流域',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県長野市犀川',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 4
                [
                    'municipality_name' => '中野市',
                    'title' => '大沼池の黒竜',
                    'title_kana' => 'おおぬまいけのこくりゅう',
                    'summary' => '信州中野鴨ヶ岳の麓の小館城主・高梨摂津守政盛の美しい姫君「黒姫」をめぐる伝説。',
                    'body_markdown' => '昔、信州中野鴨ヶ岳（かもがたけ）の麓に小館城（こたてじょう）という城があり、城主の高梨摂津守政盛（たかなしせっつのかみまさもり）には黒姫という美しい姫君がいた。',
                    'main_image_path' => null,
                    'spot_name' => '志賀高原 大沼池・中野市小館',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県下高井郡山ノ内町大沼池',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 5
                [
                    'municipality_name' => '駒ヶ根市',
                    'title' => '猿神退治',
                    'title_kana' => 'さるがみたいじ',
                    'summary' => '毎年秋祭りが近づくと娘がいる家に白羽の矢が立つという村を通りかかった、旅の坊様の物語。',
                    'body_markdown' => 'むかし、国中を旅している坊様がおりました。ある日のこと、坊様はある村にさしかかりました。その村の村人達は元気がありません。毎年秋祭りが近づくと娘がいる家に白羽の矢が立つのでした。',
                    'main_image_path' => null,
                    'spot_name' => '光前寺（駒ヶ根市）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県駒ヶ根市光前寺',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 6
                [
                    'municipality_name' => '長野県全域',
                    'title' => '琵琶法師と竜',
                    'title_kana' => 'びわほうしとりゅう',
                    'summary' => '目の見えない琵琶法師が大きな池のほとりで琵琶を弾き終えたところ、法師を呼び止める声があった。',
                    'body_markdown' => '昔、村から村を旅歩く、目の見えない琵琶法師がいた。ある夏の暑い日のこと、琵琶法師は大きな池のほとりに出た。琵琶法師が、池のほとりで琵琶を1曲弾き終えたところ、法師を呼び止める声があった。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 7
                [
                    'municipality_name' => '長野県全域',
                    'title' => '子どもたちと薬師さま',
                    'title_kana' => 'こどもたちとやくしさま',
                    'summary' => 'お堂のそばで雪合戦などをして毎日遊んでいた子どもたちの一人、権兵衛と薬師様の物語。',
                    'body_markdown' => '昔、ある村に薬師様がありました。村の人たちはたいそう大事にしておりました。子供たちはお堂のそばで、雪合戦などをして毎日遊んでおりました。その子たちの一人が権兵衛でした。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 8
                [
                    'municipality_name' => '諏訪市',
                    'title' => '火ともし山',
                    'title_kana' => 'ひともしやま',
                    'summary' => '諏訪湖の東の村の娘「おなみ」と、湖の向こう側へ移り住むことになった若者の物語。',
                    'body_markdown' => '昔、諏訪湖の東の村に「おなみ」という娘がいた。おなみには、夫婦になろうと言い交わした若者がいた。ところが、ある日若者はやんごとなき理由で今住んでいる村から湖の向こう側に移り住むことになった。',
                    'main_image_path' => null,
                    'spot_name' => '諏訪湖周辺',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県諏訪市諏訪湖',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 9
                [
                    'municipality_name' => '長野県全域',
                    'title' => 'あさこ・ゆうこ',
                    'title_kana' => 'あさこゆうこ',
                    'summary' => 'ちょっとした争いから五十年も行き来がなかった、信濃の山の東と西のふもとの二つの村の物語。',
                    'body_markdown' => '昔、信濃のある山の東と西のふもとに、小さな村がありました。この二つの村は、ちょっとした争いごとがもとで、もう五十年もの長い間、行き来がありませんでした。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 10
                [
                    'municipality_name' => '長野県全域',
                    'title' => 'あんころもちこぞう',
                    'title_kana' => 'あんころもちこぞう',
                    'summary' => '永明寺山のふもとで、いつも「あんころもちが食いたいなー」と言っていた子供の物語。',
                    'body_markdown' => '昔、永明寺山のふもとに、ふんどし一丁の子供がいた。いつもいつも「あんころもちが食いたいなー」と言っては、おっかあに作ってもらっていた。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 11
                [
                    'municipality_name' => '栄村',
                    'title' => '野々海の物語',
                    'title_kana' => 'ののみのものがたり',
                    'summary' => '越後と信濃の国境にある野々海と呼ばれる池に住む龍神・白蛇の主と美しい娘の物語。',
                    'body_markdown' => '昔、越後と信濃の国境に、野々海（ののみ）と呼ばれる池があった。ここには龍神とも白蛇とも言われる主が住んでおり、時々人間の美しい娘の姿を借りては池の上に現れた。',
                    'main_image_path' => null,
                    'spot_name' => '野々海池（栄村）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県下水内郡栄村野々海',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 12
                [
                    'municipality_name' => '長野県全域',
                    'title' => 'きつねのお礼',
                    'title_kana' => 'きつねのおれい',
                    'summary' => '粉ひきの仕事をしていたおじいさんの水車小屋に、ある雨の日に迷い込んだ子狐の物語。',
                    'body_markdown' => '昔、あるところに一軒の水車小屋があって、そこではひとりのおじいさんが粉ひきの仕事をしていた。ある雨の日のこと。小屋に一匹の子狐が迷い込んできた。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 13
                [
                    'municipality_name' => '長野県全域',
                    'title' => '百田ばなし',
                    'title_kana' => 'ひゃくだばなし',
                    'summary' => '深い深い山と山の間にあり、日月を見ることもできないくらい山深い信州の村に住む種作じいさんの物語。',
                    'body_markdown' => '深い深い山との間に、小さな村があった。この村は日月を見る事さえできないくらい、信州の山深いところにあった。この村に、種作じいさんと息子夫婦が住んでいた。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 14
                [
                    'municipality_name' => '軽井沢町',
                    'title' => 'でいだらぼっちでいらん坊',
                    'title_kana' => 'でいだらぼっちでいらんぼう',
                    'summary' => '信州の浅間山の辺りにいた途方もない大男「でいらん坊」が、碓氷峠に腰かけて昼寝をしていた時の物語。',
                    'body_markdown' => 'はるか昔のこと、信州の浅間山の辺りに、でいだらぼっちでいらん坊という途方もない大男がいた。ある時、でいらん坊が碓氷峠に腰かけて昼寝をしていると、猪が昼寝の邪魔をする。',
                    'main_image_path' => null,
                    'spot_name' => '浅間山・碓氷峠',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県北佐久郡軽井沢町碓氷峠',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 15
                [
                    'municipality_name' => '長野県全域',
                    'title' => '山鳥の尾',
                    'title_kana' => 'やまどりのお',
                    'summary' => '母親と二人暮らしの息子が、町へ行く途中で罠にかかった鳥を見つけたことから始まる物語。',
                    'body_markdown' => 'ある所に二人暮らしをしている母親と息子が居る。父親は化け物退治に行ったきり、戻ってこなかった。冬に息子は母親に買い物を頼まれ町に行く途中、罠にかかった鳥を見つける。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 16
                [
                    'municipality_name' => '塩尻市',
                    'title' => 'うとう峠',
                    'title_kana' => 'うとうとうげ',
                    'summary' => '女房に死なれて村を追われた猟師が、子供と共に都へ向かう途中で休んでいた時の物語。',
                    'body_markdown' => '昔ある所に猟師が住んでいた。この猟師女房に死なれてから生活が荒れ、村の掟をも破りついに子供と共に村を追い出されてしまった。それで都へ向かおうと思いある所で休んでいると…。',
                    'main_image_path' => null,
                    'spot_name' => '善知鳥峠（塩尻市）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県塩尻市善知鳥峠',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 17
                [
                    'municipality_name' => '長野県全域',
                    'title' => '山犬物語',
                    'title_kana' => 'やまいぬものがたり',
                    'summary' => '山奥の水車小屋で与作とおっかあと生まれたばかりの女の赤ちゃんが暮らしていた頃の物語。',
                    'body_markdown' => '昔、ある山奥の水車小屋に、与作とおっかあと生まれたばかりの女の赤ん坊が住んでいました。女の子は元気でおっぱいを沢山飲みましたが、それでもおっかあのおっぱいの出が良く…。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 18
                [
                    'municipality_name' => '塩尻市',
                    'title' => 'またたび',
                    'title_kana' => 'またたび',
                    'summary' => '信濃の塩尻で病気の母親と暮らす、博打好きな若者の物語。',
                    'body_markdown' => 'しんしんと降り続いているある夜の事、一人の男が女房に向かって話しはじめました。昔、信濃の塩尻に、病気の母親と暮らす一人の博打好きな若者がいました。若者は毎日博打に明け暮れていました。',
                    'main_image_path' => null,
                    'spot_name' => '塩尻市周辺',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県塩尻市',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 19
                [
                    'municipality_name' => '長野県全域',
                    'title' => '塞の神と厄病神',
                    'title_kana' => 'さいのかみとやくびょうがみ',
                    'summary' => '村の入口の塞の神の横に住み、働きもせず毎日博打ばかりしていた与太郎の物語。',
                    'body_markdown' => '昔、ある村の入口に塞の神（さいのかみ）が立っていて、その横には与太郎（よたろう）という男の家があった。与太郎は働きもせず、女房と子供をほったらかし、毎日サイコロ遊び（博打）ばかりしていた。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 20
                [
                    'municipality_name' => '長野県全域',
                    'title' => 'お蚕さま',
                    'title_kana' => 'おかいこさま',
                    'summary' => '仲の良い夫婦と一匹の馬、そして七年たってやっと産まれた大切な女の子をめぐる養蚕の物語。',
                    'body_markdown' => '昔、信州のある村に、仲の良い夫婦と一匹の馬がいた。七年たってやっと産まれた女の子が、夫婦の一番の宝物だった。娘はすくすく育ち、馬もこの娘がかわいいようで、馬小屋に引き入れ…。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 21
                [
                    'municipality_name' => '塩尻市',
                    'title' => 'ちごちごの花',
                    'title_kana' => 'ちごちごのはな',
                    'summary' => '美しい奈良井川のほとりに住む長者が、先祖の罪滅ぼしのために良い事をしようと決意する物語。',
                    'body_markdown' => '昔、信州の美しい奈良井川のほとりに、大金持ちの長者が住んでいました。子供がいなかった長者は、ある時不思議な夢を見ました。先祖が悪い事をしたので、その償いに何か良い事をするようにと…。',
                    'main_image_path' => null,
                    'spot_name' => '奈良井川流域',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県塩尻市奈良井川',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 22
                [
                    'municipality_name' => '須坂市',
                    'title' => '日滝の笛',
                    'title_kana' => 'ひたきのふえ',
                    'summary' => '信州の須坂の城下にある小さな村・日滝で、春と秋のお祭りに欠かせない笛の名手をめぐる物語。',
                    'body_markdown' => '昔、信州の須坂の城下に、日滝という小さな村がありました。働き者だが貧しいこの村の人々は、春と秋のお祭りを何よりも楽しみにしていました。このお祭りに欠かせないのは、笛の名手でした。',
                    'main_image_path' => null,
                    'spot_name' => '須坂市日滝',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県須坂市日滝',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 23
                [
                    'municipality_name' => '佐久市',
                    'title' => '力ばあさま',
                    'title_kana' => 'ちからばあさま',
                    'summary' => '佐久の内山に住んでいた、木の切り株を素手で掘り起こすほどの怪力を持つ婆さまの物語。',
                    'body_markdown' => '昔、長野は佐久の内山（うちやま）に、物凄く力持ちの婆さまが住んでいた。その怪力ぶりと言ったら、山で木を切れば、木の切り株を素手で掘り起こしてしまう程であった。',
                    'main_image_path' => null,
                    'spot_name' => '佐久市内山',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県佐久市内山',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 24
                [
                    'municipality_name' => '長野県全域',
                    'title' => 'ものぐさどうし',
                    'title_kana' => 'ものぐさどうし',
                    'summary' => '東村に住んでいた、畑仕事はおろか嫁をもらうのも面倒と言う大変なものぐさ男・藤兵衛どんの物語。',
                    'body_markdown' => '昔々、ある所に東村という村があり、そこには藤兵衛（とうべえ）どんという大変なものぐさ男が住んでいた。この藤兵衛どん、畑仕事はおろか嫁さんをもらうのも面倒と言い、挙句の果て…。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 25
                [
                    'municipality_name' => '長野県全域',
                    'title' => '百物語',
                    'title_kana' => 'ひゃくものがたり',
                    'summary' => '暇を見つけては悪さばかりしていた庄屋の息子、刀屋の息子、寺の小坊主の三人が始めた百物語。',
                    'body_markdown' => 'ある所に庄屋の息子、刀屋の息子、寺の小坊主がいて、この三人は暇を見付けては悪さばかりしていた。ある日、寺に三人集まって何か面白いことがないかと相談していると、小坊主が…。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 26
                [
                    'municipality_name' => '野沢温泉村',
                    'title' => '住吉のこい',
                    'title_kana' => 'すみよしのこい',
                    'summary' => '野沢温泉から少し離れた高台にある宿屋「住吉」がいつも繁盛していた理由、池に住む1匹のコイの物語。',
                    'body_markdown' => 'むかし、野沢温泉から少し離れた高台に、住吉という1軒の宿屋があった。温泉街から離れているのにいつも繁盛していた。その理由は、この宿屋の池に住む1匹のコイのおかげだった。',
                    'main_image_path' => null,
                    'spot_name' => '野沢温泉',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県下高井郡野沢温泉村',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 27
                [
                    'municipality_name' => '伊那市',
                    'title' => '孝行猿',
                    'title_kana' => 'こうこうざる',
                    'summary' => '信州の上伊那の山奥で、一人息子の与三松を大切に育てていた猟師・勘助と親子の猿の物語。',
                    'body_markdown' => '信州の上伊那（かみいな）のある山奥に勘助（かんすけ）という猟師がおりました。勘助は、妻に先立たれ、一人息子の与三松（よそまつ）を大切に育てておりました。',
                    'main_image_path' => null,
                    'spot_name' => '上伊那地域',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県伊那市',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 28
                [
                    'municipality_name' => '木祖村',
                    'title' => '権兵衛峠',
                    'title_kana' => 'ごんべえとうげ',
                    'summary' => '山に囲まれて田んぼもなく米を運び入れることもできなかった信州木曽の薮原あたりの物語。',
                    'body_markdown' => '昔、信州木曽の薮原（やぶはら）あたりは、山に囲まれて田んぼもなく、周りの峠道も危険だったため米を運び入れることも出来なかった。そして村人たちは、山の段々畑でそば、キビなどを育てていた。',
                    'main_image_path' => null,
                    'spot_name' => '権兵衛峠',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県権兵衛峠',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 29
                [
                    'municipality_name' => '白馬村',
                    'title' => '白馬岳の魔神',
                    'title_kana' => 'しろうまだけのまじん',
                    'summary' => '大金持ちの長者の美しい娘・たまきと、白馬岳の魔神をめぐる物語。',
                    'body_markdown' => '昔ある所にものすごい大金持ちの長者がいて、その長者には、たまき（玉巻）という大変美しい娘がいた。たまきが年頃になると方々からしっかり嫁に欲しいという声があったが、長者は首を縦に振らなかった。',
                    'main_image_path' => null,
                    'spot_name' => '白馬岳',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県白馬岳',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 30
                [
                    'municipality_name' => '伊那市',
                    'title' => '戸倉山の大鷲',
                    'title_kana' => 'とくらやまのおおわし',
                    'summary' => '戸倉山のふもとの長谷の村に暮らす百姓の清兵衛とお袖、そして二人の間に生まれた子供の物語。',
                    'body_markdown' => '昔、信州は戸倉山のふもとに長谷（はせ）の村があった。この村には清兵衛（せいべえ）という百姓がおり、この春には女房のお袖との間に子供も生まれ、幸せに過ごしていた。ところが…。',
                    'main_image_path' => null,
                    'spot_name' => '戸倉山（伊那市長谷）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県伊那市長谷',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 31
                [
                    'municipality_name' => '長野県全域',
                    'title' => '雑仕橋',
                    'title_kana' => 'ぞうしばし',
                    'summary' => '急流の川を挟んで隣り合う二つの村に暮らす、同じ年ごろの少女と少年の物語。',
                    'body_markdown' => 'あるところに、川を挟んで隣り合う村があった。村の間を流れる川は急流で、橋を架けることも渡ることもできなかった。片方の村には一人の少女が、もう片方の村には同じ年ごろの少年がいた。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 32
                [
                    'municipality_name' => '小谷村',
                    'title' => '五郎右衛門の猫',
                    'title_kana' => 'ごろうえもんのねこ',
                    'summary' => '長野と新潟の境を流れる姫川の谷あい、横川という山あいの村に家族と暮らしていた五郎右衛門と猫の物語。',
                    'body_markdown' => '長野と新潟の境を流れる姫川の谷あいに姫川温泉があり、そこから新潟に通じる道を少し行った所に横川と言う山あいの村があった。そして、そこには五郎右衛門という男が家族と一緒に暮らしていた。',
                    'main_image_path' => null,
                    'spot_name' => '姫川温泉・横川（小谷村）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県北安曇郡小谷村姫川温泉',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 33
                [
                    'municipality_name' => '長野県全域',
                    'title' => 'はなとり地蔵',
                    'title_kana' => 'はなとりじぞう',
                    'summary' => '一重山のふもとの働き者の老夫婦が、村はずれの丘で薄汚れて鼻の欠けたお地蔵さまを見つけた物語。',
                    'body_markdown' => '昔、信州の一重山（ひとえやま）のふもとに、働き者の百姓の老夫婦がいました。ある日の事、老夫婦は、村はずれの丘で薄汚れて鼻の欠けたお地蔵さまが転がっているのを見つけました。',
                    'main_image_path' => null,
                    'spot_name' => null,
                    'spot_map_url' => null,
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 34
                [
                    'municipality_name' => '安曇野市',
                    'title' => 'かぶろ様と天狗',
                    'title_kana' => 'かぶろさまとてんぐ',
                    'summary' => '安曇野の馬羅尾山にいたずら好きの天狗がいた頃、あぜ道を歩いていた庄屋の甚兵衛をめぐる物語。',
                    'body_markdown' => '昔々、信州は安曇野の馬羅尾山（ばらおさん）にいたずら好きの天狗がいた。何か面白いことはないかと、今日も安曇平を見下ろしていると、田植えが終わったあぜ道を庄屋の甚兵衛が歩いていた。',
                    'main_image_path' => null,
                    'spot_name' => '馬羅尾山（安曇野市）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県安曇野市',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 35
                [
                    'municipality_name' => '安曇野市',
                    'title' => 'ねずみ石',
                    'title_kana' => 'ねずみいし',
                    'summary' => '信濃の有明山の麓に仲良く暮らしていたものの、子供がいないことが心残りだった爺さんと婆さんの物語。',
                    'body_markdown' => '昔、信濃の有明山（ありあけやま）の麓に、仲のよい爺さんと婆さんがいた。二人は、これまで何不自由もなく平和に暮らしてきたが、子供がいないことが心残りだった。',
                    'main_image_path' => null,
                    'spot_name' => '有明山（安曇野市）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県安曇野市有明山',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 36
                [
                    'municipality_name' => '伊那市',
                    'title' => '真菰が池のおしどり',
                    'title_kana' => 'まこもがいけのおしどり',
                    'summary' => '天目山の戦いから落ち延び、信州富県の貝沼に住み着いた武田軍の武士・桜井重久の物語。',
                    'body_markdown' => '甲斐国の天目山（てんもくざん）の戦いから落ち延びた武田軍の武士桜井重久（さくらいしげひさ）は、敵地から逃れ信州富県（とみがた）の貝沼に住み付くと、名前も貝沼重久と改め孤独な暮らしを始めた。',
                    'main_image_path' => null,
                    'spot_name' => '富県 貝沼（伊那市）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県伊那市富県',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 37
                [
                    'municipality_name' => '長野県全域',
                    'title' => 'もの言わぬお菊',
                    'title_kana' => 'ものいわぬおきく',
                    'summary' => '犀川に架けられた久米路橋のほとりに住む貧しい少女・お菊が、ある時村の金持ちから小豆を貰った物語。',
                    'body_markdown' => 'お菊は犀川に架けられた久米路橋のほとりに住んでいました。その頃村人の暮しは一様に貧しい生活でした。そして、お菊の家もまた貧しかったのです。あるとき村の金持ちの倉から小豆を…。',
                    'main_image_path' => null,
                    'spot_name' => '久米路橋（犀川）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県久米路橋',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 38
                [
                    'municipality_name' => '富士見町',
                    'title' => '豊吉とアオ',
                    'title_kana' => 'とよきちとあお',
                    'summary' => '諏訪郡の原の茶屋に暮らす貧乏だが心の優しい男・豊吉と、ガリガリにやせ細った馬の物語。',
                    'body_markdown' => '昔、長野県諏訪郡の原の茶屋に、貧乏だが心の優しい豊吉（とよきち）という男が居ました。豊吉が家の前の畑を耕していると、ガリガリにやせ細った馬が馬方に連れられて歩いてきました。',
                    'main_image_path' => null,
                    'spot_name' => '諏訪郡原の茶屋（富士見町周辺）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県諏訪郡富士見町',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 39
                [
                    'municipality_name' => '阿南町',
                    'title' => 'カワランベー',
                    'title_kana' => 'かわらんべー',
                    'summary' => '和合村三度にある和合川に住み、立派なお膳やお椀を必要なだけ貸してくれた河童の物語。',
                    'body_markdown' => '昔、信州は和合村三度（さんど）にある和合川に、一匹のカワランベー（河童）が住んでいた。この河童は、立派なお膳やお椀を必要なだけ貸してくれるので、村人達から大変ありがたがられていた。',
                    'main_image_path' => null,
                    'spot_name' => '和合川（阿南町和合）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県下伊那郡阿南町和合',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
                // 40
                [
                    'municipality_name' => '山ノ内町',
                    'title' => '寿命ののびる石',
                    'title_kana' => 'じゅみょうののびるいし',
                    'summary' => '平尾の大沼池に住み、吐く息で木々を焼き尽くし土手を切って大洪水を起こしていた大蛇の物語。',
                    'body_markdown' => '昔、信濃は平尾の大沼池に一匹の大蛇がいた。この大蛇、吐く息で辺りの木々を焼き尽し、土手を切っては大洪水を起こすなど、その悪さは止まるところを知らなかった。',
                    'main_image_path' => null,
                    'spot_name' => '志賀高原 大沼池（山ノ内町）',
                    'spot_map_url' => 'https://maps.google.com/?q=長野県下高井郡山ノ内町大沼池',
                    'source' => 'まんが日本昔ばなし〜データベース〜（長野県）',
                ],
            ];

            foreach ($stories as $story) {
                $municipalityId = $municipalityIds[$story['municipality_name']];
                unset($story['municipality_name']);

                Story::updateOrCreate(
                    [
                        'municipality_id' => $municipalityId,
                        'title' => $story['title'],
                    ],
                    array_merge($story, [
                        'source_citation_checked' => false,
                        'expression_rewritten_checked' => false,
                    ]),
                );
            }
        });
    }
}
