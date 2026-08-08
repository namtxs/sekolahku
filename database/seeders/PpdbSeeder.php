<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AdmissionPath;
use App\Models\Applicant;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Data demo modul PPDB: tahun aktif 2026/2027, 3 jalur, dan pendaftar contoh
 * berbagai status. Idempotent: bagian pendaftar dilewati bila sudah ada.
 */
class PpdbSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            ['is_active' => true],
        );

        if (! AcademicYear::query()->where('is_active', true)->exists()) {
            $year->makeActive();
        }

        $paths = $this->seedPaths($year);
        $this->seedMenuItem();

        if (Applicant::query()->exists()) {
            if ($this->command) {
                $this->command->warn('Pendaftar demo sudah ada, bagian pendaftar dilewati.');
            }

            return;
        }

        $this->seedApplicants($paths);
    }

    /**
     * @return array<string, AdmissionPath>
     */
    protected function seedPaths(AcademicYear $year): array
    {
        $items = [
            'Zonasi' => [
                'description' => 'Diperuntukkan bagi calon peserta didik yang berdomisili dalam wilayah zonasi sekolah sesuai kartu keluarga.',
                'quota' => 120,
                'requirements' => '<ul><li>Kartu Keluarga (minimal 1 tahun di alamat tersebut)</li><li>Akta kelahiran</li><li>Ijazah/Surat Keterangan Lulus SMP</li><li>Pas foto terbaru 3x4</li></ul>',
                'opens_at' => now()->subDays(7),
                'closes_at' => now()->addDays(14),
            ],
            'Prestasi' => [
                'description' => 'Jalur seleksi berdasarkan rata-rata nilai rapor dan/atau prestasi akademik maupun non-akademik.',
                'quota' => 40,
                'requirements' => '<ul><li>Rapor semester 1–5 (rata-rata nilai)</li><li>Sertifikat prestasi (bila ada)</li><li>Kartu Keluarga dan akta kelahiran</li><li>Pas foto terbaru 3x4</li></ul>',
                'opens_at' => now()->subDays(7),
                'closes_at' => now()->addDays(14),
            ],
            'Afirmasi' => [
                'description' => 'Diperuntukkan bagi calon peserta didik dari keluarga ekonomi tidak mampu dan penyandang disabilitas.',
                'quota' => 30,
                'requirements' => '<ul><li>Kartu Indonesia Pintar / Kartu Keluarga Sejahtera</li><li>Kartu Keluarga dan akta kelahiran</li><li>Pas foto terbaru 3x4</li></ul>',
                'opens_at' => now()->addDays(21),
                'closes_at' => now()->addDays(35),
            ],
        ];

        $paths = [];

        foreach ($items as $name => $data) {
            $paths[$name] = AdmissionPath::firstOrCreate(
                ['academic_year_id' => $year->id, 'name' => $name],
                [...$data, 'is_active' => true],
            );
        }

        return $paths;
    }

    /**
     * Akses PPDB di header cukup lewat tombol amber "PPDB Online" di navbar
     * (partials/header.blade.php). Item menu primary "/ppdb" justru membuat
     * tautan dobel, jadi seeder membersihkannya bila pernah dibuat.
     */
    protected function seedMenuItem(): void
    {
        Menu::query()
            ->where('location', 'primary')
            ->first()
            ?->items()
            ->where('url', '/ppdb')
            ->delete();
    }

    /**
     * @param array<string, AdmissionPath> $paths
     */
    protected function seedApplicants(array $paths): void
    {
        $tmpDir = storage_path('app/ppdb-seed-tmp');
        File::ensureDirectoryExists($tmpDir);

        $items = [
            // [nama, jalur, status, nilai, catatan]
            ['Ahmad Fauzan Ramadhan', 'Zonasi', Applicant::STATUS_ACCEPTED, null, null],
            ['Siti Nurhaliza Putri', 'Zonasi', Applicant::STATUS_ACCEPTED, null, null],
            ['Muhammad Rizky Pratama', 'Zonasi', Applicant::STATUS_VERIFIED, null, 'Berkas lengkap dan sesuai.'],
            ['Dewi Anggraini', 'Zonasi', Applicant::STATUS_PENDING, null, null],
            ['Bagus Setiawan', 'Zonasi', Applicant::STATUS_REJECTED, null, 'Alamat pada kartu keluarga berada di luar wilayah zonasi.'],
            ['Nadia Salsabila', 'Prestasi', Applicant::STATUS_ACCEPTED, 93.25, null],
            ['Raihan Alfarizi', 'Prestasi', Applicant::STATUS_VERIFIED, 89.50, 'Sertifikat OSN kabupaten terlampir.'],
            ['Putri Maharani', 'Prestasi', Applicant::STATUS_PENDING, 87.75, null],
            ['Fajar Nugroho', 'Prestasi', Applicant::STATUS_PENDING, 85.00, null],
            ['Intan Permatasari', 'Zonasi', Applicant::STATUS_PENDING, null, null],
        ];

        try {
            foreach ($items as $index => [$name, $pathName, $status, $score, $note]) {
                $applicant = Applicant::factory()
                    ->state([
                        'admission_path_id' => $paths[$pathName]->id,
                        'name' => $name,
                        'status' => $status,
                        'avg_score' => $score,
                        'verification_note' => $note,
                        'verified_at' => in_array($status, [Applicant::STATUS_VERIFIED, Applicant::STATUS_ACCEPTED, Applicant::STATUS_REJECTED], true)
                            ? now()->subDays(2)
                            : null,
                        'decided_at' => in_array($status, [Applicant::STATUS_ACCEPTED, Applicant::STATUS_REJECTED], true)
                            ? now()->subDay()
                            : null,
                        'created_at' => now()->subDays(5)->addHours($index * 3),
                    ])
                    ->create();

                $applicant->addMedia($this->makePhoto($tmpDir, $name))
                    ->usingFileName(Str::slug($name).'.png')
                    ->toMediaCollection('photo');
            }
        } finally {
            File::deleteDirectory($tmpDir);
        }
    }

    /**
     * Pas foto placeholder sederhana (inisial di atas latar biru) via GD.
     */
    protected function makePhoto(string $tmpDir, string $name): string
    {
        $width = 300;
        $height = 400;

        $image = imagecreatetruecolor($width, $height);
        $background = imagecolorallocate($image, 1, 69, 133);
        imagefilledrectangle($image, 0, 0, $width, $height, $background);

        $initials = collect(explode(' ', $name))
            ->map(fn (string $word): string => mb_substr($word, 0, 1))
            ->take(2)
            ->implode('');

        $white = imagecolorallocate($image, 255, 255, 255);
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($initials);
        imagestring($image, $font, (int) (($width - $textWidth) / 2), (int) ($height / 2) - 8, $initials, $white);

        $path = $tmpDir.'/'.Str::random(12).'.png';
        imagepng($image, $path);
        imagedestroy($image);

        return $path;
    }
}
