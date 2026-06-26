import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { CoverUploader, type CoverUrls } from '@/components/admin/cover-uploader';
import { TranslatableField } from '@/components/admin/translatable-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type LocaleMap = Record<string, string>;

type SlideData = {
    id: number;
    category: LocaleMap;
    title: LocaleMap;
    date: string | null;
    source: string | null;
    sort_order: number;
    news_id: number | null;
    image: CoverUrls | null;
};

type Props = {
    locales: string[];
    newsOptions: { id: number; title: string }[];
    item?: SlideData;
};

export default function SlideForm({ locales, newsOptions, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        category: item?.category ?? { ...blank },
        title: item?.title ?? { ...blank },
        date: item?.date ?? '',
        source: item?.source ?? '',
        sort_order: item?.sort_order ?? 0,
        news_id: item?.news_id ?? null,
        image: null as File | null,
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/slides/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/slides', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование слайда' : 'Новый слайд'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/slides" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование слайда' : 'Новый слайд'}
                        </h1>
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        <Save className="size-4" /> Сохранить
                    </Button>
                </div>

                <div className="mt-6 max-w-3xl space-y-5">
                    <TranslatableField
                        name="category"
                        label="Категория"
                        locales={locales}
                        value={form.data.category}
                        onChange={(value) => form.setData('category', value)}
                        errors={errors}
                        as="input"
                        placeholder="Категория слайда"
                    />

                    <TranslatableField
                        name="title"
                        label="Заголовок"
                        locales={locales}
                        value={form.data.title}
                        onChange={(value) => form.setData('title', value)}
                        required
                        errors={errors}
                        as="input"
                        placeholder="Заголовок слайда"
                    />

                    <div className="grid gap-2">
                        <Label>Изображение</Label>
                        <CoverUploader existing={item?.image ?? null} onChange={(file) => form.setData('image', file)} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Дата</Label>
                        <Input
                            value={form.data.date}
                            onChange={(event) => form.setData('date', event.target.value)}
                            placeholder="Например: 26 июня 2026"
                        />
                        <InputError message={errors.date} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Источник</Label>
                        <Input
                            value={form.data.source}
                            onChange={(event) => form.setData('source', event.target.value)}
                            placeholder="Пресс-центр КҲФ"
                        />
                        <InputError message={errors.source} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Связанная новость</Label>
                        <Select
                            value={form.data.news_id ? String(form.data.news_id) : 'none'}
                            onValueChange={(value) =>
                                form.setData('news_id', value === 'none' ? null : Number(value))
                            }
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Без новости" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="none">Без новости</SelectItem>
                                {newsOptions.map((option) => (
                                    <SelectItem key={option.id} value={String(option.id)}>
                                        {option.title}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.news_id} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Порядок сортировки</Label>
                        <Input
                            type="number"
                            value={form.data.sort_order}
                            onChange={(event) => form.setData('sort_order', Number(event.target.value))}
                        />
                        <InputError message={errors.sort_order} />
                    </div>
                </div>
            </form>
        </>
    );
}
