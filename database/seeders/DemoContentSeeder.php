<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\DownloadFile;
use App\Models\Menu;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Konten contoh berbahasa Indonesia untuk demo/uji coba CMS sekolah.
 * Idempotent: dilewati bila sudah ada berita (post) di database.
 */
class DemoContentSeeder extends Seeder
{
    protected string $tmpDir;

    public function run(): void
    {
        if (Post::query()->posts()->exists()) {
            if ($this->command) {
                $this->command->warn('Konten demo sudah ada, seeder dilewati.');
            }

            return;
        }

        $this->tmpDir = storage_path('app/demo-seed-tmp');
        File::ensureDirectoryExists($this->tmpDir);

        try {
            $this->seedSettings();

            [$admin, $penulis] = $this->seedUsers();
            $categories = $this->seedCategories();
            $tags = $this->seedTags();

            $posts = $this->seedPosts($admin, $penulis, $categories, $tags);
            $pages = $this->seedPages($admin);

            $this->seedComments($posts);
            $this->seedMenus($pages);
            $this->seedBanners($posts);
            $this->seedAlbums();
            $this->seedDownloads($admin);
            $this->seedContactMessages();
        } finally {
            File::deleteDirectory($this->tmpDir);
        }
    }

    protected function seedSettings(): void
    {
        $settings = app(GeneralSettings::class);

        $settings->fill([
            'site_name' => 'SMK Muhammadiyah 1 Ambarawa',
            'site_short_name' => 'SMK MUSAMBA',
            'npsn' => '12345678',
            'jenjang' => 'SMK',
            'address' => 'Jl. Pendidikan No. 1, Kel. Sukamaju, Kec. Contoh, Kota Bandung, Jawa Barat 40111',
            'phone' => '(022) 1234567',
            'email' => 'info@smkmusamba.sch.id',
            'headmaster_name' => 'Drs. H. Ahmad Suryadi, M.Pd.',
            'headmaster_greeting' => '<p>Assalamualaikum warahmatullahi wabarakatuh. Puji syukur kita panjatkan ke hadirat Tuhan Yang Maha Esa atas segala rahmat dan karunia-Nya. Selamat datang di website resmi SMK Muhammadiyah 1 Ambarawa (SMK MUSAMBA), media informasi dan komunikasi antara sekolah, orang tua, peserta didik, serta masyarakat luas.</p><p>Melalui website ini kami berupaya menyajikan informasi terkini seputar kegiatan pembelajaran, prestasi peserta didik, agenda sekolah, hingga layanan administrasi. Kami percaya keterbukaan informasi adalah bagian penting dari pelayanan pendidikan yang berkualitas.</p><p>Kami mengucapkan terima kasih atas kunjungan Anda. Saran dan masukan yang membangun sangat kami harapkan demi kemajuan sekolah tercinta ini. Wassalamualaikum warahmatullahi wabarakatuh.</p>',
            'facebook' => 'https://facebook.com/smkmusamba',
            'instagram' => 'https://instagram.com/smkmusamba',
            'youtube' => 'https://youtube.com/@smkmusamba',
            'twitter' => 'https://x.com/smkmusamba',
            'meta_description' => 'Website resmi SMK Muhammadiyah 1 Ambarawa (SMK MUSAMBA). Informasi PPDB, berita sekolah, prestasi siswa, galeri kegiatan, dan unduhan dokumen.',
            'meta_keywords' => 'SMK Muhammadiyah 1 Ambarawa, SMK MUSAMBA, SMK, sekolah, PPDB, pendidikan, Ambarawa',
        ]);

        $settings->save();
    }

    /**
     * @return array{0: User, 1: User}
     */
    protected function seedUsers(): array
    {
        $admin = User::where('email', 'admin@sekolahku-v2.test')->firstOrFail();

        $operator = User::firstOrCreate(
            ['email' => 'operator@sekolahku-v2.test'],
            ['name' => 'Siti Rahmawati', 'password' => Hash::make('password')],
        );
        $operator->syncRoles(['Operator']);

        $penulis = User::firstOrCreate(
            ['email' => 'penulis@sekolahku-v2.test'],
            ['name' => 'Budi Santoso', 'password' => Hash::make('password')],
        );
        $penulis->syncRoles(['Penulis']);

        return [$admin, $penulis];
    }

    /**
     * @return array<string, Category>
     */
    protected function seedCategories(): array
    {
        $items = [
            'Pengumuman' => 'Informasi dan pengumuman resmi sekolah.',
            'Kegiatan Sekolah' => 'Liputan kegiatan dan agenda sekolah.',
            'Prestasi' => 'Capaian dan prestasi peserta didik maupun sekolah.',
            'Akademik' => 'Informasi seputar pembelajaran dan akademik.',
            'Ekstrakurikuler' => 'Kabar dari berbagai kegiatan ekstrakurikuler.',
        ];

        $categories = [];

        foreach ($items as $name => $description) {
            $categories[$name] = Category::firstOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }

        return $categories;
    }

    /**
     * @return array<string, Tag>
     */
    protected function seedTags(): array
    {
        $names = ['OSIS', 'Lomba', 'PPDB', 'Ujian', 'Pramuka', 'Beasiswa', 'Olahraga', 'Seni', 'Literasi', 'HUT RI'];

        $tags = [];

        foreach ($names as $name) {
            $tags[$name] = Tag::firstOrCreate(['name' => $name]);
        }

        return $tags;
    }

    /**
     * @param array<string, Category> $categories
     * @param array<string, Tag> $tags
     * @return array<int, Post>
     */
    protected function seedPosts(User $admin, User $penulis, array $categories, array $tags): array
    {
        $items = [
            [
                'title' => 'Pengumuman Penerimaan Peserta Didik Baru Tahun Ajaran 2026/2027',
                'category' => 'Pengumuman',
                'tags' => ['PPDB'],
                'body' => [
                    'SMK Muhammadiyah 1 Ambarawa membuka pendaftaran Penerimaan Peserta Didik Baru (PPDB) Tahun Ajaran 2026/2027. Pendaftaran dilakukan secara daring melalui laman PPDB resmi dengan empat jalur: zonasi, afirmasi, perpindahan tugas orang tua, dan prestasi.',
                    'Calon peserta didik diminta menyiapkan berkas berupa ijazah atau surat keterangan lulus, kartu keluarga, akta kelahiran, dan pas foto terbaru. Seluruh berkas diunggah dalam format PDF atau JPG dengan ukuran maksimal 2 MB per berkas.',
                    'Jadwal penting: pendaftaran dibuka 1-10 Juli, verifikasi berkas 11-13 Juli, pengumuman hasil seleksi 15 Juli, dan daftar ulang 16-18 Juli. Informasi lebih lanjut dapat menghubungi panitia PPDB di nomor telepon sekolah pada jam kerja.',
                ],
            ],
            [
                'title' => 'Upacara Peringatan HUT RI ke-81 Berlangsung Khidmat',
                'category' => 'Kegiatan Sekolah',
                'tags' => ['HUT RI', 'OSIS'],
                'body' => [
                    'Keluarga besar SMK Muhammadiyah 1 Ambarawa menggelar upacara bendera dalam rangka memperingati Hari Ulang Tahun ke-81 Kemerdekaan Republik Indonesia di lapangan utama sekolah. Upacara diikuti seluruh peserta didik, guru, dan tenaga kependidikan.',
                    'Bertindak sebagai pembina upacara, Kepala Sekolah menyampaikan amanat tentang makna kemerdekaan bagi generasi muda. Beliau mengajak seluruh peserta didik mengisi kemerdekaan dengan belajar sungguh-sungguh dan berkarya bagi bangsa.',
                    'Petugas upacara kali ini adalah pasukan pengibar bendera sekolah yang telah berlatih selama dua pekan di bawah bimbingan pembina OSIS. Upacara ditutup dengan pembacaan doa dan menyanyikan lagu-lagu perjuangan.',
                ],
            ],
            [
                'title' => 'Siswa Raih Juara 1 Lomba Karya Ilmiah Remaja Tingkat Provinsi',
                'category' => 'Prestasi',
                'tags' => ['Lomba'],
                'body' => [
                    'Prestasi membanggakan kembali ditorehkan peserta didik SMK Muhammadiyah 1 Ambarawa. Tim Karya Ilmiah Remaja yang beranggotakan tiga siswa kelas XI berhasil meraih Juara 1 Lomba Karya Ilmiah Remaja tingkat Provinsi Jawa Barat.',
                    'Karya berjudul "Pemanfaatan Limbah Kulit Pisang sebagai Bahan Baku Bioplastik Ramah Lingkungan" berhasil menyisihkan lebih dari 120 karya dari berbagai sekolah. Dewan juri menilai penelitian ini orisinal dan aplikatif.',
                    'Kepala Sekolah menyampaikan apresiasi dan berharap prestasi ini memotivasi peserta didik lain untuk terus berkarya. Tim akan mewakili provinsi pada kompetisi tingkat nasional bulan depan.',
                ],
            ],
            [
                'title' => 'Jadwal Ujian Akhir Semester Ganjil Tahun Ajaran 2026/2027',
                'category' => 'Akademik',
                'tags' => ['Ujian'],
                'body' => [
                    'Ujian Akhir Semester (UAS) Ganjil Tahun Ajaran 2026/2027 akan dilaksanakan selama dua pekan. Seluruh peserta didik diharapkan mempersiapkan diri dengan baik dan memperhatikan jadwal yang telah ditetapkan.',
                    'Ujian dilaksanakan dalam dua sesi setiap harinya: sesi pertama pukul 07.30-09.30 dan sesi kedua pukul 10.00-12.00. Peserta didik wajib hadir 15 menit sebelum ujian dimulai dengan membawa kartu peserta ujian.',
                    'Selama masa ujian, kegiatan ekstrakurikuler diliburkan sementara. Hasil ujian akan dibagikan bersamaan dengan penerimaan rapor pada akhir semester. Orang tua diharapkan mendampingi putra-putrinya selama masa persiapan ujian.',
                ],
            ],
            [
                'title' => 'Kemah Bakti Pramuka di Bumi Perkemahan Cikole',
                'category' => 'Ekstrakurikuler',
                'tags' => ['Pramuka'],
                'body' => [
                    'Gerakan Pramuka Gugus Depan SMK Muhammadiyah 1 Ambarawa menyelenggarakan Kemah Bakti selama tiga hari dua malam di Bumi Perkemahan Cikole, Lembang. Kegiatan diikuti 150 anggota pramuka penegak dari kelas X dan XI.',
                    'Rangkaian kegiatan meliputi penjelajahan alam, pionering, bakti sosial membersihkan area wisata, serta api unggun pada malam puncak. Peserta juga mendapatkan materi kepemimpinan dan manajemen regu dari para pembina.',
                    'Kemah bakti ini bertujuan menumbuhkan kemandirian, kedisiplinan, dan kepedulian sosial peserta didik. Kegiatan berjalan lancar dan seluruh peserta kembali ke sekolah dengan selamat.',
                ],
            ],
            [
                'title' => 'Tim Basket Putra Melaju ke Final Kejuaraan Antar-SMK',
                'category' => 'Prestasi',
                'tags' => ['Olahraga', 'Lomba'],
                'body' => [
                    'Tim basket putra SMK Muhammadiyah 1 Ambarawa memastikan satu tempat di babak final Kejuaraan Basket Antar-SMK se-Kota Bandung setelah mengalahkan tim unggulan dengan skor 68-61 pada laga semifinal yang berlangsung sengit.',
                    'Pertandingan berjalan ketat sejak kuarter pertama. Kemenangan dipastikan lewat tembakan tiga angka kapten tim pada menit-menit akhir. Dukungan suporter yang memadati tribun menjadi energi tambahan bagi para pemain.',
                    'Laga final akan digelar akhir pekan ini di GOR Kota Bandung. Seluruh warga sekolah diundang hadir memberikan dukungan langsung kepada tim kebanggaan sekolah.',
                ],
            ],
            [
                'title' => 'Sosialisasi Program Beasiswa Prestasi bagi Peserta Didik',
                'category' => 'Pengumuman',
                'tags' => ['Beasiswa'],
                'body' => [
                    'Sekolah bekerja sama dengan beberapa mitra menyelenggarakan sosialisasi program beasiswa prestasi bagi peserta didik kelas XI dan XII. Kegiatan berlangsung di aula sekolah dan diikuti lebih dari 200 peserta didik beserta orang tua.',
                    'Narasumber memaparkan berbagai skema beasiswa yang tersedia, mulai dari beasiswa pemerintah, perguruan tinggi, hingga lembaga swasta. Peserta juga mendapatkan tips menyusun esai motivasi dan menyiapkan portofolio prestasi.',
                    'Bagi peserta didik yang berminat, pendaftaran dan konsultasi dapat dilakukan melalui guru Bimbingan Konseling pada jam istirahat atau setelah pulang sekolah.',
                ],
            ],
            [
                'title' => 'Gelar Karya Seni Siswa Meriahkan Pentas Akhir Tahun',
                'category' => 'Kegiatan Sekolah',
                'tags' => ['Seni', 'OSIS'],
                'body' => [
                    'Pentas seni akhir tahun SMK Muhammadiyah 1 Ambarawa berlangsung meriah di lapangan sekolah. Acara bertajuk "Harmoni dalam Karya" ini menampilkan beragam pertunjukan mulai dari tari tradisional, band pelajar, teater, hingga pameran karya rupa.',
                    'Sebanyak 25 penampilan memukau ribuan penonton yang terdiri dari peserta didik, guru, orang tua, dan warga sekitar. Stan pameran karya siswa juga menjual berbagai produk kerajinan hasil pembelajaran kewirausahaan.',
                    'Ketua OSIS menyampaikan bahwa seluruh rangkaian acara dikelola secara mandiri oleh panitia siswa. Kegiatan ini menjadi wadah apresiasi sekaligus ajang unjuk bakat peserta didik di bidang seni.',
                ],
            ],
            [
                'title' => 'Gerakan Literasi Sekolah: Satu Bulan Satu Buku',
                'category' => 'Akademik',
                'tags' => ['Literasi'],
                'body' => [
                    'SMK Muhammadiyah 1 Ambarawa meluncurkan program Gerakan Literasi Sekolah bertema "Satu Bulan Satu Buku". Setiap peserta didik ditargetkan menyelesaikan minimal satu buku bacaan setiap bulan dan menuliskan ulasannya di jurnal literasi.',
                    'Program ini didukung dengan pembenahan perpustakaan sekolah, penambahan koleksi buku baru, serta pojok baca di setiap kelas. Lima belas menit sebelum pembelajaran dimulai, seluruh warga sekolah membaca buku bersama-sama.',
                    'Ulasan buku terbaik setiap bulan akan mendapatkan apresiasi dan dipublikasikan di majalah dinding serta website sekolah. Diharapkan program ini menumbuhkan budaya baca yang kuat di lingkungan sekolah.',
                ],
            ],
            [
                'title' => 'Pemilihan Ketua OSIS Periode 2026/2027 Berjalan Demokratis',
                'category' => 'Kegiatan Sekolah',
                'tags' => ['OSIS'],
                'body' => [
                    'Pemilihan Ketua OSIS SMK Muhammadiyah 1 Ambarawa periode 2026/2027 berlangsung demokratis dengan sistem pemungutan suara elektronik. Tiga pasangan calon bersaing memperebutkan suara dari seluruh peserta didik dan dewan guru.',
                    'Sebelum hari pemilihan, para kandidat mengikuti rangkaian kampanye dan debat terbuka yang menguji visi, misi, serta program kerja masing-masing. Antusiasme pemilih tercatat tinggi dengan partisipasi mencapai 97 persen.',
                    'Pasangan terpilih akan dilantik pada upacara bendera pekan depan. Kepala Sekolah berpesan agar pengurus baru mampu menjadi teladan dan menjalankan program yang bermanfaat bagi seluruh warga sekolah.',
                ],
            ],
            [
                'title' => 'Workshop Penulisan Kreatif Bersama Penulis Nasional',
                'category' => 'Akademik',
                'tags' => ['Literasi', 'Seni'],
                'body' => [
                    'Sebanyak 80 peserta didik mengikuti workshop penulisan kreatif yang menghadirkan penulis nasional sebagai narasumber. Kegiatan ini merupakan rangkaian program literasi sekolah yang digelar di aula utama.',
                    'Peserta mendapatkan materi teknik menulis cerita pendek, membangun karakter tokoh, dan mengembangkan ide dari pengalaman sehari-hari. Sesi praktik menulis langsung mendapat umpan balik dari narasumber.',
                    'Sepuluh karya terbaik peserta workshop akan dibukukan dalam antologi cerpen sekolah yang terbit akhir semester ini.',
                ],
            ],
            [
                'title' => 'Lomba Kebersihan Antar Kelas Sambut Hari Lingkungan Hidup',
                'category' => 'Kegiatan Sekolah',
                'tags' => ['Lomba'],
                'body' => [
                    'Menyambut Hari Lingkungan Hidup Sedunia, sekolah menggelar lomba kebersihan dan penataan kelas. Penilaian meliputi kebersihan, kerapian, penghijauan, serta kreativitas pengelolaan sampah di masing-masing kelas.',
                    'Selama dua pekan, setiap kelas berlomba menghias dan merawat ruang belajarnya. Beberapa kelas bahkan membuat taman mini dan instalasi daur ulang dari botol plastik bekas.',
                    'Pemenang lomba diumumkan saat upacara bendera dan berhak atas piala bergilir serta dana pembinaan kelas. Kegiatan ini diharapkan menumbuhkan kepedulian peserta didik terhadap kebersihan lingkungan.',
                ],
            ],
            [
                'title' => 'Ekstrakurikuler Robotik Wakili Sekolah di Kompetisi Nasional',
                'category' => 'Ekstrakurikuler',
                'tags' => ['Lomba'],
                'body' => [
                    'Tim robotik SMK Muhammadiyah 1 Ambarawa lolos ke babak final Kompetisi Robotik Pelajar Nasional setelah menempati peringkat dua besar pada seleksi regional. Tim beranggotakan lima peserta didik lintas angkatan ini akan berlaga di Jakarta.',
                    'Robot rancangan tim mengusung tema otomasi pertanian, yakni prototipe penyiram tanaman pintar berbasis sensor kelembapan tanah. Persiapan intensif dilakukan setiap sore di laboratorium sekolah.',
                    'Pembina ekstrakurikuler berharap dukungan doa dari seluruh warga sekolah agar tim dapat memberikan hasil terbaik di tingkat nasional.',
                ],
            ],
            [
                'title' => 'Pelaksanaan Asesmen Nasional Berbasis Komputer',
                'category' => 'Akademik',
                'tags' => ['Ujian'],
                'body' => [
                    'SMK Muhammadiyah 1 Ambarawa melaksanakan Asesmen Nasional Berbasis Komputer (ANBK) yang diikuti 45 peserta didik kelas XI terpilih sebagai sampel. Pelaksanaan berlangsung dua hari di laboratorium komputer sekolah.',
                    'Asesmen mencakup literasi membaca, numerasi, serta survei karakter dan lingkungan belajar. Seluruh perangkat dan jaringan telah melewati simulasi sehingga pelaksanaan berjalan lancar tanpa kendala teknis berarti.',
                    'Hasil asesmen akan menjadi bahan evaluasi mutu pembelajaran dan dasar penyusunan program peningkatan kualitas pendidikan di sekolah.',
                ],
            ],
            [
                'title' => 'Kunjungan Studi Banding dari SMA Negeri 2 Harapan',
                'category' => 'Kegiatan Sekolah',
                'tags' => [],
                'body' => [
                    'SMK Muhammadiyah 1 Ambarawa menerima kunjungan studi banding dari SMA Negeri 2 Harapan. Rombongan yang terdiri dari kepala sekolah, wakil kepala sekolah, dan perwakilan guru disambut hangat di ruang pertemuan utama.',
                    'Kunjungan difokuskan pada pengelolaan program sekolah penggerak, sistem informasi akademik, dan pembinaan ekstrakurikuler unggulan. Kedua sekolah saling bertukar praktik baik dalam pengelolaan pembelajaran.',
                    'Kegiatan diakhiri dengan penandatanganan nota kesepahaman kerja sama antar-sekolah di bidang peningkatan kompetensi guru dan pertukaran kegiatan kesiswaan.',
                ],
            ],
        ];

        $posts = [];
        $withoutImage = [4, 9, 13];

        foreach ($items as $index => $item) {
            $publishedAt = now()
                ->subDays($index * 8)
                ->subHours(random_int(1, 6))
                ->setMinute(random_int(0, 59));

            $post = Post::create([
                'type' => 'post',
                'title' => $item['title'],
                'excerpt' => Str::limit(strip_tags($item['body'][0]), 160),
                'body' => $this->toHtml($item['body']),
                'status' => 'published',
                'published_at' => $publishedAt,
                'user_id' => $index % 3 === 0 ? $admin->id : $penulis->id,
                'category_id' => $categories[$item['category']]->id,
                'views' => random_int(10, 500),
                'meta_title' => $item['title'],
                'meta_description' => Str::limit(strip_tags($item['body'][0]), 155),
            ]);

            $post->tags()->sync(
                collect($item['tags'])->map(fn (string $name) => $tags[$name]->id)->all(),
            );

            if (! in_array($index, $withoutImage, true)) {
                $imagePath = $this->makeImage(1200, 630, Str::limit($item['title'], 40, '...'), $this->palette($index));
                $post->addMedia($imagePath)
                    ->usingFileName(Str::slug(Str::limit($item['title'], 50, '')).'.png')
                    ->toMediaCollection('featured');
            }

            $posts[] = $post;
        }

        // 2 draft
        foreach ([
            'Rencana Pembangunan Laboratorium Komputer Baru',
            'Panduan Pembelajaran Semester Genap bagi Peserta Didik',
        ] as $title) {
            Post::create([
                'type' => 'post',
                'title' => $title,
                'excerpt' => null,
                'body' => $this->toHtml([
                    'Artikel ini masih dalam tahap penyusunan oleh tim redaksi website sekolah.',
                    'Informasi lengkap akan dipublikasikan setelah mendapat persetujuan pimpinan sekolah.',
                ]),
                'status' => 'draft',
                'published_at' => null,
                'user_id' => $penulis->id,
                'category_id' => $categories['Pengumuman']->id,
                'views' => 0,
            ]);
        }

        // 1 scheduled (terbit besok)
        Post::create([
            'type' => 'post',
            'title' => 'Libur Nasional dan Cuti Bersama Bulan Depan',
            'excerpt' => 'Informasi hari libur nasional dan cuti bersama yang memengaruhi jadwal pembelajaran bulan depan.',
            'body' => $this->toHtml([
                'Berdasarkan surat edaran dinas pendidikan, kegiatan pembelajaran akan diliburkan pada tanggal yang bertepatan dengan hari libur nasional dan cuti bersama bulan depan.',
                'Peserta didik diharapkan tetap belajar mandiri di rumah dan menyelesaikan tugas yang diberikan oleh masing-masing guru mata pelajaran.',
            ]),
            'status' => 'scheduled',
            'published_at' => now()->addDay(),
            'user_id' => $admin->id,
            'category_id' => $categories['Pengumuman']->id,
            'views' => 0,
        ]);

        return $posts;
    }

    /**
     * @return array<string, Post>
     */
    protected function seedPages(User $admin): array
    {
        $items = [
            'Profil Sekolah' => [
                'SMK Muhammadiyah 1 Ambarawa (SMK MUSAMBA) adalah sekolah menengah kejuruan di bawah naungan Muhammadiyah yang berdiri sejak tahun 1975 dan berlokasi di Ambarawa. Dengan NPSN 12345678, sekolah ini telah terakreditasi A dan menjadi salah satu sekolah rujukan di Jawa Tengah.',
                'Saat ini sekolah membina lebih dari 1.000 peserta didik yang terbagi dalam 30 rombongan belajar, didukung 65 guru dan 20 tenaga kependidikan profesional. Kurikulum yang diterapkan adalah Kurikulum Merdeka dengan penguatan profil pelajar Pancasila.',
                'Sekolah terus berbenah meningkatkan mutu layanan pendidikan melalui pengembangan sarana prasarana, digitalisasi pembelajaran, serta kemitraan dengan perguruan tinggi dan dunia industri.',
            ],
            'Visi dan Misi' => [
                'Visi: "Terwujudnya peserta didik yang beriman, berprestasi, berkarakter, dan berwawasan lingkungan."',
                'Misi sekolah: (1) Menumbuhkan penghayatan dan pengamalan ajaran agama serta nilai-nilai luhur budaya bangsa; (2) Melaksanakan pembelajaran yang aktif, kreatif, efektif, dan menyenangkan; (3) Mengembangkan potensi akademik dan non-akademik peserta didik secara optimal; (4) Membudayakan perilaku hidup bersih, sehat, dan peduli lingkungan; (5) Menjalin kemitraan dengan orang tua, masyarakat, dan dunia usaha.',
                'Tujuan yang ingin dicapai antara lain meningkatnya rata-rata capaian akademik, bertambahnya prestasi pada berbagai kompetisi, serta terciptanya lingkungan sekolah yang aman, nyaman, dan ramah anak.',
            ],
            'Sejarah' => [
                'SMK Muhammadiyah 1 Ambarawa didirikan pada tanggal 17 Juli 1975 berdasarkan Surat Keputusan Menteri Pendidikan dan Kebudayaan. Pada awal berdirinya, sekolah menempati gedung sederhana dengan enam ruang kelas dan 240 peserta didik angkatan pertama.',
                'Seiring berjalannya waktu, sekolah berkembang pesat. Tahun 1990 dibangun gedung laboratorium dan perpustakaan, disusul aula serbaguna pada tahun 2003. Pada tahun 2010 sekolah meraih akreditasi A dan dipercaya menjadi sekolah rintisan berstandar nasional.',
                'Hingga kini SMK Muhammadiyah 1 Ambarawa telah meluluskan lebih dari 15.000 alumni yang tersebar di berbagai profesi, baik di dalam maupun luar negeri. Semangat "berprestasi dan berkarakter" terus diwariskan dari generasi ke generasi.',
            ],
            'Fasilitas' => [
                'SMK Muhammadiyah 1 Ambarawa memiliki fasilitas pembelajaran yang lengkap untuk mendukung proses pendidikan. Tersedia 30 ruang kelas ber-AC yang dilengkapi proyektor, laboratorium IPA (fisika, kimia, biologi), laboratorium komputer, dan laboratorium bahasa.',
                'Fasilitas penunjang lainnya meliputi perpustakaan dengan koleksi lebih dari 10.000 judul buku, aula serbaguna berkapasitas 500 orang, masjid, ruang UKS, ruang bimbingan konseling, serta kantin sehat.',
                'Untuk kegiatan olahraga, sekolah menyediakan lapangan basket, lapangan voli, lapangan futsal, dan area senam. Seluruh area sekolah terjangkau jaringan internet nirkabel untuk mendukung pembelajaran digital.',
            ],
        ];

        $pages = [];

        foreach ($items as $title => $paragraphs) {
            $pages[$title] = Post::create([
                'type' => 'page',
                'title' => $title,
                'excerpt' => Str::limit(strip_tags($paragraphs[0]), 160),
                'body' => $this->toHtml($paragraphs),
                'status' => 'published',
                'published_at' => now()->subMonths(5),
                'user_id' => $admin->id,
                'category_id' => null,
                'views' => random_int(50, 300),
                'meta_title' => $title.' - SMK Muhammadiyah 1 Ambarawa',
                'meta_description' => Str::limit(strip_tags($paragraphs[0]), 155),
            ]);
        }

        return $pages;
    }

    /**
     * @param array<int, Post> $posts
     */
    protected function seedComments(array $posts): void
    {
        $commenters = [
            ['Rina Marlina', 'rina.marlina@example.com'],
            ['Agus Priyanto', 'agus.priyanto@example.com'],
            ['Dewi Lestari', 'dewi.lestari@example.com'],
            ['Hendra Wijaya', 'hendra.wijaya@example.com'],
            ['Sri Wahyuni', 'sri.wahyuni@example.com'],
            ['Taufik Hidayat', 'taufik.h@example.com'],
        ];

        $bodies = [
            'Terima kasih atas informasinya, sangat bermanfaat bagi kami para orang tua.',
            'Semoga sekolah semakin maju dan berprestasi. Sukses selalu!',
            'Apakah informasi lebih lanjut bisa didapatkan di bagian tata usaha?',
            'Keren sekali! Bangga menjadi bagian dari sekolah ini.',
            'Mohon informasi jadwal lengkapnya, terima kasih.',
            'Selamat dan sukses untuk anak-anak hebat SMK Muhammadiyah 1 Ambarawa!',
            'Alhamdulillah, kabar yang menggembirakan. Semoga istiqomah.',
            'Sangat mendukung program sekolah seperti ini. Lanjutkan!',
        ];

        $plan = [
            // [post index, jumlah approved, jumlah pending]
            [0, 3, 1],
            [1, 2, 1],
            [2, 2, 0],
            [3, 1, 1],
            [5, 1, 0],
        ];

        $c = 0;

        foreach ($plan as [$postIndex, $approved, $pending]) {
            $post = $posts[$postIndex];
            $firstComment = null;

            for ($i = 0; $i < $approved + $pending; $i++) {
                [$name, $email] = $commenters[$c % count($commenters)];

                $comment = Comment::create([
                    'post_id' => $post->id,
                    'parent_id' => null,
                    'name' => $name,
                    'email' => $email,
                    'body' => $bodies[$c % count($bodies)],
                    'status' => $i < $approved ? 'approved' : 'pending',
                    'ip_address' => '192.168.1.'.random_int(2, 254),
                ]);

                $firstComment ??= $comment;
                $c++;
            }

            // Balasan bersarang pada dua post pertama.
            if (in_array($postIndex, [0, 2], true) && $firstComment) {
                Comment::create([
                    'post_id' => $post->id,
                    'parent_id' => $firstComment->id,
                    'name' => 'Admin Sekolah',
                    'email' => 'info@smkmusamba.sch.id',
                    'body' => 'Terima kasih atas tanggapannya. Informasi lebih lanjut dapat menghubungi tata usaha pada jam kerja.',
                    'status' => 'approved',
                    'ip_address' => '192.168.1.1',
                ]);
            }
        }
    }

    /**
     * @param array<string, Post> $pages
     */
    protected function seedMenus(array $pages): void
    {
        $primary = Menu::firstOrCreate(['location' => 'primary'], ['name' => 'Menu Utama']);
        $primary->items()->delete();

        $beranda = $primary->items()->create(['title' => 'Beranda', 'url' => '/', 'sort_order' => 1]);

        $profil = $primary->items()->create(['title' => 'Profil', 'url' => null, 'sort_order' => 2]);

        $order = 1;
        foreach (['Profil Sekolah', 'Visi dan Misi', 'Sejarah', 'Fasilitas'] as $pageTitle) {
            $primary->items()->create([
                'parent_id' => $profil->id,
                'title' => $pageTitle,
                'post_id' => $pages[$pageTitle]->id,
                'sort_order' => $order++,
            ]);
        }

        $primary->items()->create(['title' => 'Berita', 'url' => '/berita', 'sort_order' => 3]);
        $primary->items()->create(['title' => 'Galeri', 'url' => '/galeri', 'sort_order' => 4]);
        $primary->items()->create(['title' => 'Unduhan', 'url' => '/unduhan', 'sort_order' => 5]);
        $primary->items()->create(['title' => 'Kontak', 'url' => '/kontak', 'sort_order' => 6]);

        $footer = Menu::firstOrCreate(['location' => 'footer'], ['name' => 'Menu Footer']);
        $footer->items()->delete();

        $footer->items()->create(['title' => 'Berita', 'url' => '/berita', 'sort_order' => 1]);
        $footer->items()->create(['title' => 'Galeri', 'url' => '/galeri', 'sort_order' => 2]);
        $footer->items()->create(['title' => 'Kontak', 'url' => '/kontak', 'sort_order' => 3]);
        $footer->items()->create([
            'title' => 'Profil Sekolah',
            'post_id' => $pages['Profil Sekolah']->id,
            'sort_order' => 4,
        ]);
    }

    /**
     * @param array<int, Post> $posts
     */
    protected function seedBanners(array $posts): void
    {
        $items = [
            [
                'title' => 'Penerimaan Peserta Didik Baru 2026/2027',
                'description' => 'Pendaftaran dibuka secara daring. Segera lengkapi berkas dan daftarkan putra-putri Anda.',
                'url' => '/berita/'.$posts[0]->slug,
                'colors' => [[13, 94, 73], [4, 47, 46]],
            ],
            [
                'title' => 'Sekolah Berprestasi dan Berkarakter',
                'description' => 'Mewujudkan generasi beriman, berprestasi, dan berwawasan lingkungan.',
                'url' => '/profil-sekolah',
                'colors' => [[30, 58, 138], [15, 23, 42]],
            ],
            [
                'title' => 'Ayo Dukung Tim Kebanggaan Sekolah',
                'description' => 'Saksikan laga final kejuaraan basket antar-SMK akhir pekan ini.',
                'url' => '/berita/'.$posts[5]->slug,
                'colors' => [[154, 52, 18], [69, 26, 3]],
            ],
        ];

        foreach ($items as $index => $item) {
            $banner = Banner::create([
                'title' => $item['title'],
                'description' => $item['description'],
                'url' => $item['url'],
                'is_active' => true,
                'sort_order' => $index + 1,
                'starts_at' => now()->subWeek(),
                'ends_at' => null,
            ]);

            $imagePath = $this->makeImage(1920, 700, Str::limit($item['title'], 38, ''), $item['colors']);
            $banner->addMedia($imagePath)
                ->usingFileName('banner-'.($index + 1).'.png')
                ->toMediaCollection('banner');
        }
    }

    protected function seedAlbums(): void
    {
        $albums = [
            [
                'name' => 'Upacara HUT RI',
                'description' => 'Dokumentasi upacara peringatan HUT ke-81 Kemerdekaan RI di lapangan sekolah.',
                'photos' => 7,
                'captions' => ['Pengibaran bendera merah putih', 'Barisan peserta upacara', 'Amanat pembina upacara', 'Paduan suara sekolah', 'Pasukan pengibar bendera', 'Peserta didik mengikuti upacara', 'Penghormatan kepada bendera'],
            ],
            [
                'name' => 'Lomba 17 Agustus',
                'description' => 'Keseruan aneka lomba memperingati hari kemerdekaan di lingkungan sekolah.',
                'photos' => 8,
                'captions' => ['Lomba balap karung', 'Lomba makan kerupuk', 'Tarik tambang antar kelas', 'Lomba estafet air', 'Penyerahan hadiah pemenang', 'Panitia lomba dari OSIS', 'Semangat para peserta', 'Sorak sorai penonton'],
            ],
            [
                'name' => 'Study Tour',
                'description' => 'Perjalanan edukatif peserta didik kelas XI ke berbagai destinasi belajar.',
                'photos' => 6,
                'captions' => ['Kunjungan ke museum', 'Foto bersama di lokasi', 'Belajar di luar kelas', 'Sesi diskusi kelompok', 'Wisata edukasi alam', 'Keberangkatan rombongan'],
            ],
        ];

        foreach ($albums as $albumIndex => $item) {
            $album = Album::create([
                'name' => $item['name'],
                'description' => $item['description'],
                'is_published' => true,
            ]);

            for ($i = 0; $i < $item['photos']; $i++) {
                $caption = $item['captions'][$i];
                $imagePath = $this->makeImage(1200, 800, $caption, $this->palette($albumIndex * 3 + $i));

                $album->addMedia($imagePath)
                    ->usingName($caption)
                    ->usingFileName(Str::slug($caption).'.png')
                    ->withCustomProperties(['caption' => $caption])
                    ->toMediaCollection('photos');
            }
        }
    }

    protected function seedDownloads(User $admin): void
    {
        $items = [
            ['Formulir Pendaftaran Peserta Didik Baru', 'Formulir', 'Formulir pendaftaran PPDB yang wajib diisi calon peserta didik baru.'],
            ['Formulir Izin Tidak Masuk Sekolah', 'Formulir', 'Formulir permohonan izin bagi peserta didik yang berhalangan hadir.'],
            ['Struktur Kurikulum Merdeka Kelas X', 'Kurikulum', 'Dokumen struktur Kurikulum Merdeka untuk peserta didik kelas X.'],
            ['Kalender Pendidikan Tahun Ajaran 2026/2027', 'Kurikulum', 'Kalender pendidikan resmi tahun ajaran 2026/2027.'],
            ['Pengumuman Kelulusan Tahun Ajaran 2025/2026', 'Pengumuman', 'Surat pengumuman kelulusan peserta didik kelas XII.'],
            ['Tata Tertib Peserta Didik', 'Pengumuman', 'Dokumen tata tertib yang berlaku bagi seluruh peserta didik.'],
        ];

        foreach ($items as [$title, $category, $description]) {
            $file = DownloadFile::create([
                'title' => $title,
                'description' => $description,
                'category' => $category,
                'download_count' => random_int(5, 250),
                'is_published' => true,
                'user_id' => $admin->id,
            ]);

            $pdfPath = $this->makePdf($title, 'SMK Muhammadiyah 1 Ambarawa - '.$category);
            $file->addMedia($pdfPath)
                ->usingName($title)
                ->usingFileName(Str::slug($title).'.pdf')
                ->toMediaCollection('file');
        }
    }

    protected function seedContactMessages(): void
    {
        $items = [
            [
                'name' => 'Bambang Setiawan',
                'email' => 'bambang.s@example.com',
                'phone' => '081234567890',
                'subject' => 'Pertanyaan Jalur Zonasi PPDB',
                'message' => 'Selamat pagi, saya ingin menanyakan batas wilayah zonasi untuk pendaftaran PPDB tahun ini. Apakah kelurahan Sukasari masih termasuk zona sekolah? Terima kasih.',
                'is_read' => false,
            ],
            [
                'name' => 'Yuni Astuti',
                'email' => 'yuni.astuti@example.com',
                'phone' => '085678901234',
                'subject' => 'Permohonan Legalisir Ijazah',
                'message' => 'Saya alumni angkatan 2015 ingin melegalisir ijazah untuk keperluan melamar kerja. Apakah bisa datang langsung ke sekolah dan apa saja persyaratannya?',
                'is_read' => false,
            ],
            [
                'name' => 'Doni Prasetyo',
                'email' => 'doni.pras@example.com',
                'phone' => null,
                'subject' => 'Kerja Sama Bimbingan Belajar',
                'message' => 'Kami dari lembaga bimbingan belajar ingin menawarkan program kerja sama persiapan ujian masuk perguruan tinggi bagi siswa kelas XII. Mohon informasi kontak bagian kurikulum.',
                'is_read' => true,
            ],
        ];

        foreach ($items as $item) {
            ContactMessage::create($item);
        }
    }

    /**
     * Bungkus tiap paragraf dengan tag <p>.
     *
     * @param array<int, string> $paragraphs
     */
    protected function toHtml(array $paragraphs): string
    {
        return collect($paragraphs)
            ->map(fn (string $p): string => "<p>{$p}</p>")
            ->implode("\n");
    }

    /**
     * Pasangan warna gradient untuk gambar placeholder.
     *
     * @return array{0: array<int, int>, 1: array<int, int>}
     */
    protected function palette(int $index): array
    {
        $palettes = [
            [[16, 185, 129], [6, 78, 59]],
            [[59, 130, 246], [30, 58, 138]],
            [[249, 115, 22], [124, 45, 18]],
            [[139, 92, 246], [76, 29, 149]],
            [[236, 72, 153], [131, 24, 67]],
            [[234, 179, 8], [113, 63, 18]],
            [[20, 184, 166], [19, 78, 74]],
            [[100, 116, 139], [15, 23, 42]],
        ];

        return $palettes[$index % count($palettes)];
    }

    /**
     * Buat PNG placeholder via GD: gradient vertikal + teks di tengah.
     * Teks digambar pada kanvas kecil lalu diperbesar agar terbaca.
     *
     * @param array{0: array<int, int>, 1: array<int, int>} $colors
     */
    protected function makeImage(int $width, int $height, string $text, array $colors): string
    {
        [$from, $to] = $colors;

        $smallW = (int) ($width / 4);
        $smallH = (int) ($height / 4);

        $small = imagecreatetruecolor($smallW, $smallH);

        for ($y = 0; $y < $smallH; $y++) {
            $ratio = $y / max(1, $smallH - 1);
            $color = imagecolorallocate(
                $small,
                (int) ($from[0] + ($to[0] - $from[0]) * $ratio),
                (int) ($from[1] + ($to[1] - $from[1]) * $ratio),
                (int) ($from[2] + ($to[2] - $from[2]) * $ratio),
            );
            imageline($small, 0, $y, $smallW, $y, $color);
        }

        $text = function_exists('iconv')
            ? (iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text)
            : $text;

        $white = imagecolorallocate($small, 255, 255, 255);
        $shadow = imagecolorallocatealpha($small, 0, 0, 0, 60);
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($text);
        $x = max(4, (int) (($smallW - $textWidth) / 2));
        $y = (int) ($smallH / 2) - (int) (imagefontheight($font) / 2);

        imagestring($small, $font, $x + 1, $y + 1, $text, $shadow);
        imagestring($small, $font, $x, $y, $text, $white);

        $image = imagecreatetruecolor($width, $height);
        imagecopyresized($image, $small, 0, 0, 0, 0, $width, $height, $smallW, $smallH);
        imagedestroy($small);

        $path = $this->tmpDir.'/'.Str::random(16).'.png';
        imagepng($image, $path);
        imagedestroy($image);

        return $path;
    }

    /**
     * Buat PDF satu halaman sederhana (hand-crafted, valid) berisi judul dokumen.
     */
    protected function makePdf(string $title, string $subtitle): string
    {
        $escape = fn (string $s): string => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);

        $stream = implode("\n", [
            'BT /F1 18 Tf 72 760 Td ('.$escape($title).') Tj ET',
            'BT /F1 12 Tf 72 730 Td ('.$escape($subtitle).') Tj ET',
            'BT /F1 10 Tf 72 690 Td (Dokumen contoh yang dihasilkan otomatis oleh seeder demo.) Tj ET',
        ]);

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>',
            "<< /Length ".strlen($stream)." >>\nstream\n{$stream}\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $i => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1)." 0 obj\n{$object}\nendobj\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";

        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xrefPosition}\n%%EOF";

        $path = $this->tmpDir.'/'.Str::random(16).'.pdf';
        file_put_contents($path, $pdf);

        return $path;
    }
}
