import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { TranslatableField } from '@/components/admin/translatable-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type LocaleMap = Record<string, string>;

type ItemData = {
    id: number;
    title: LocaleMap;
    description: LocaleMap;
    slug: string;
    icon: string | null;
    topics_count: number | null;
    posts_count: number | null;
    sort_order: number | null;
};

type Props = {
    locales: string[];
    item?: ItemData;
};

export default function ForumCategoryForm({ locales, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        title: item?.title ?? { ...blank },
        description: item?.description ?? { ...blank },
        slug: item?.slug ?? '',
        icon: item?.icon ?? '',
        topics_count: item?.topics_count ?? 0,
        posts_count: item?.posts_count ?? 0,
        sort_order: item?.sort_order ?? 0,
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/forum-categories/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/forum-categories', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование категории' : 'Новая категория'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/forum-categories" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование категории' : 'Новая категория'}
                        </h1>
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        <Save className="size-4" /> Сохранить
                    </Button>
                </div>

                <div className="mt-6 max-w-3xl space-y-5">
                    <TranslatableField
                        name="title"
                        label="Название"
                        locales={locales}
                        value={form.data.title}
                        onChange={(value) => form.setData('title', value)}
                        required
                        errors={errors}
                        as="input"
                        placeholder="Название категории"
                    />

                    <TranslatableField
                        name="description"
                        label="Описание"
                        locales={locales}
                        value={form.data.description}
                        onChange={(value) => form.setData('description', value)}
                        errors={errors}
                        as="textarea"
                        placeholder="Краткое описание раздела"
                    />

                    <div className="grid gap-2">
                        <Label>Слаг (URL)</Label>
                        <Input
                            value={form.data.slug}
                            onChange={(event) => form.setData('slug', event.target.value)}
                            placeholder="slug-kategorii"
                        />
                        <InputError message={errors.slug} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Иконка</Label>
                        <Input
                            value={form.data.icon}
                            onChange={(event) => form.setData('icon', event.target.value)}
                            placeholder="MessageSquare"
                        />
                        <InputError message={errors.icon} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Количество тем</Label>
                        <Input
                            type="number"
                            value={form.data.topics_count}
                            onChange={(event) => form.setData('topics_count', Number(event.target.value))}
                        />
                        <InputError message={errors.topics_count} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Количество сообщений</Label>
                        <Input
                            type="number"
                            value={form.data.posts_count}
                            onChange={(event) => form.setData('posts_count', Number(event.target.value))}
                        />
                        <InputError message={errors.posts_count} />
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
