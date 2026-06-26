<?php

namespace Database\Seeders;

use App\Enums\DocType;
use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        /** @var array<string, string> $categories */
        $categories = [
            'laws' => 'Қонунҳо',
            'decrees' => 'Қарорҳо',
            'orders' => 'Фармонҳо',
            'guides' => 'Дастурамалҳо',
            'reports' => 'Ҳисоботҳо',
        ];

        $categoryModels = [];
        $order = 0;
        foreach ($categories as $slug => $label) {
            $categoryModels[$slug] = DocumentCategory::updateOrCreate(
                ['slug' => $slug],
                ['name' => ['tg' => $label, 'ru' => $label, 'en' => $label], 'sort_order' => $order++],
            );
        }

        /** @var list<array{slug:string,cat:string,title:string,number:string,date:string,type:DocType,size:string}> $items */
        $items = [
            ['slug' => 'd1', 'cat' => 'laws', 'title' => 'Қонуни ҶТ «Дар бораи ҳифзи аҳолӣ ва ҳудудҳо аз ҳолатҳои фавқулодда»', 'number' => '№ 53', 'date' => '15.07.2004', 'type' => DocType::Pdf, 'size' => '420 КБ'],
            ['slug' => 'd2', 'cat' => 'laws', 'title' => 'Қонуни ҶТ «Дар бораи мудофиаи гражданӣ»', 'number' => '№ 391', 'date' => '29.12.2010', 'type' => DocType::Pdf, 'size' => '356 КБ'],
            ['slug' => 'd3', 'cat' => 'laws', 'title' => 'Қонуни ҶТ «Дар бораи бехатарии оташнишонӣ»', 'number' => '№ 1018', 'date' => '23.07.2016', 'type' => DocType::Pdf, 'size' => '298 КБ'],
            ['slug' => 'd4', 'cat' => 'decrees', 'title' => 'Қарори Ҳукумати ҶТ оид ба тадбирҳои пешгирии офатҳои табиӣ', 'number' => '№ 344', 'date' => '01.08.2020', 'type' => DocType::Pdf, 'size' => '512 КБ'],
            ['slug' => 'd5', 'cat' => 'decrees', 'title' => 'Қарор дар бораи Стратегияи миллии паст кардани хатари офатҳо', 'number' => '№ 164', 'date' => '29.03.2019', 'type' => DocType::Pdf, 'size' => '1,2 МБ'],
            ['slug' => 'd6', 'cat' => 'decrees', 'title' => 'Қарор оид ба тасдиқи Низомномаи системаи огоҳии барвақт', 'number' => '№ 89', 'date' => '12.02.2023', 'type' => DocType::Pdf, 'size' => '640 КБ'],
            ['slug' => 'd7', 'cat' => 'orders', 'title' => 'Фармони раиси Кумита дар бораи омодагии мавсими селоб', 'number' => '№ 21-ф', 'date' => '10.04.2026', 'type' => DocType::Docx, 'size' => '88 КБ'],
            ['slug' => 'd8', 'cat' => 'orders', 'title' => 'Фармон оид ба гузаронидани машқҳои мудофиаи гражданӣ', 'number' => '№ 17-ф', 'date' => '03.03.2026', 'type' => DocType::Docx, 'size' => '76 КБ'],
            ['slug' => 'd9', 'cat' => 'guides', 'title' => 'Дастурамал оид ба рафтори аҳолӣ ҳангоми заминҷунбӣ', 'number' => 'Д-04', 'date' => '20.01.2026', 'type' => DocType::Pdf, 'size' => '210 КБ'],
            ['slug' => 'd10', 'cat' => 'guides', 'title' => 'Дастурамал оид ба амалиёт ҳангоми селу обхезӣ', 'number' => 'Д-06', 'date' => '20.01.2026', 'type' => DocType::Pdf, 'size' => '245 КБ'],
            ['slug' => 'd11', 'cat' => 'reports', 'title' => 'Ҳисоботи солонаи Кумита барои соли 2025', 'number' => 'ҲС-2025', 'date' => '28.02.2026', 'type' => DocType::Pdf, 'size' => '3,4 МБ'],
            ['slug' => 'd12', 'cat' => 'reports', 'title' => 'Маълумоти оморӣ оид ба ҳолатҳои фавқулодда (нимсолаи I)', 'number' => 'ОМ-2026/1', 'date' => '05.06.2026', 'type' => DocType::Xlsx, 'size' => '120 КБ'],
        ];

        $order = 0;
        foreach ($items as $item) {
            Document::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'document_category_id' => $categoryModels[$item['cat']]->id,
                    'title' => ['tg' => $item['title'], 'ru' => $item['title'], 'en' => $item['title']],
                    'number' => $item['number'],
                    'document_date' => Carbon::createFromFormat('d.m.Y', $item['date']),
                    'type' => $item['type'],
                    'size' => $item['size'],
                    'sort_order' => $order++,
                ],
            );
        }
    }
}
