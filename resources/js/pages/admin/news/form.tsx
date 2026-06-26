import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { useState } from 'react';
import { CoverUploader, type CoverUrls } from '@/components/admin/cover-uploader';
import { LocaleTabs } from '@/components/admin/locale-tabs';
import { RichTextEditor } from '@/components/admin/rich-text-editor';
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
import { Textarea } from '@/components/ui/textarea';

type LocaleMap = Record<string, string>;

type NewsData = {
    id: number;
    slug: string;
    title: LocaleMap;
    excerpt: LocaleMap;
    body: LocaleMap;
    news_category_id: number | null;
    author: string | null;
    region: string | null;
    status: string;
    published_at: string | null;
    cover: CoverUrls | null;
};

type Props = {
    locales: string[];
    categories: { id: number; name: string }[];
    statuses: { value: string; label: string }[];
    news?: NewsData;
};

export default function NewsForm({ locales, categories, statuses, news }: Props) {
    const isEdit = Boolean(news);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        title: news?.title ?? { ...blank },
        excerpt: news?.excerpt ?? { ...blank },
        body: news?.body ?? { ...blank },
        slug: news?.slug ?? '',
        news_category_id: news?.news_category_id ?? null,
        author: news?.author ?? '',
        region: news?.region ?? '',
        status: news?.status ?? 'draft',
        published_at: news?.published_at ?? '',
        cover: null as File | null,
    });

    const [locale, setLocale] = useState(locales.includes('ru') ? 'ru' : locales[0]);
    const errors = form.errors as Record<string, string>;

    const setTrans = (field: 'title' | 'excerpt' | 'body', value: string) => {
        form.setData(field, { ...form.data[field], [locale]: value });
    };

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && news) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/news/${news.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/news', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование новости' : 'Новая новость'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/news" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование новости' : 'Новая новость'}
                        </h1>
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        <Save className="size-4" /> Сохранить
                    </Button>
                </div>

                <div className="mt-6 grid gap-6 lg:grid-cols-[1fr_320px]">
                    {/* Основной контент */}
                    <div className="space-y-5">
                        <LocaleTabs locales={locales} active={locale} onChange={setLocale} />

                        <div className="grid gap-2">
                            <Label>
                                Заголовок {locale === 'ru' && <span className="text-destructive">*</span>}
                            </Label>
                            <Input
                                value={form.data.title[locale] ?? ''}
                                onChange={(event) => setTrans('title', event.target.value)}
                                placeholder="Заголовок новости"
                            />
                            <InputError message={errors[`title.${locale}`]} />
                        </div>

                        <div className="grid gap-2">
                            <Label>Краткое описание</Label>
                            <Textarea
                                rows={3}
                                value={form.data.excerpt[locale] ?? ''}
                                onChange={(event) => setTrans('excerpt', event.target.value)}
                                placeholder="Анонс для карточек и ленты"
                            />
                        </div>

                        <div className="grid gap-2">
                            <Label>Текст</Label>
                            <RichTextEditor
                                key={`body-${locale}`}
                                value={form.data.body[locale] ?? ''}
                                onChange={(html) => setTrans('body', html)}
                                placeholder="Текст новости…"
                            />
                        </div>
                    </div>

                    {/* Боковая панель */}
                    <div className="space-y-5">
                        <div className="grid gap-2">
                            <Label>Статус</Label>
                            <Select value={form.data.status} onValueChange={(value) => form.setData('status', value)}>
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    {statuses.map((status) => (
                                        <SelectItem key={status.value} value={status.value}>
                                            {status.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="grid gap-2">
                            <Label>Дата публикации</Label>
                            <Input
                                type="datetime-local"
                                value={form.data.published_at}
                                onChange={(event) => form.setData('published_at', event.target.value)}
                            />
                        </div>

                        <div className="grid gap-2">
                            <Label>Категория</Label>
                            <Select
                                value={form.data.news_category_id ? String(form.data.news_category_id) : 'none'}
                                onValueChange={(value) =>
                                    form.setData('news_category_id', value === 'none' ? null : Number(value))
                                }
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Без категории" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="none">Без категории</SelectItem>
                                    {categories.map((category) => (
                                        <SelectItem key={category.id} value={String(category.id)}>
                                            {category.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="grid gap-2">
                            <Label>Слаг (URL)</Label>
                            <Input
                                value={form.data.slug}
                                onChange={(event) => form.setData('slug', event.target.value)}
                                placeholder="slug-novosti"
                            />
                            <InputError message={errors.slug} />
                        </div>

                        <div className="grid gap-2">
                            <Label>Автор</Label>
                            <Input
                                value={form.data.author}
                                onChange={(event) => form.setData('author', event.target.value)}
                                placeholder="Пресс-центр КҲФ"
                            />
                        </div>

                        <div className="grid gap-2">
                            <Label>Регион</Label>
                            <Input
                                value={form.data.region}
                                onChange={(event) => form.setData('region', event.target.value)}
                                placeholder="Душанбе"
                            />
                        </div>

                        <div className="grid gap-2">
                            <Label>Обложка</Label>
                            <CoverUploader existing={news?.cover ?? null} onChange={(file) => form.setData('cover', file)} />
                        </div>
                    </div>
                </div>
            </form>
        </>
    );
}
