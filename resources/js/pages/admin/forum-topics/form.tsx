import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { TranslatableField } from '@/components/admin/translatable-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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

type ItemData = {
    id: number;
    title: LocaleMap;
    forum_category_id: number | null;
    author: string | null;
    replies: number | null;
    views: number | null;
    pinned: boolean;
    last_activity: string | null;
    sort_order: number | null;
};

type Props = {
    locales: string[];
    forumCategories: { id: number; name: string }[];
    item?: ItemData;
};

export default function ForumTopicForm({ locales, forumCategories, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        title: item?.title ?? { ...blank },
        forum_category_id: item?.forum_category_id ?? null,
        author: item?.author ?? '',
        replies: item?.replies ?? 0,
        views: item?.views ?? 0,
        pinned: item?.pinned ?? false,
        last_activity: item?.last_activity ?? '',
        sort_order: item?.sort_order ?? 0,
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/forum-topics/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/forum-topics', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование темы' : 'Новая тема'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/forum-topics" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование темы' : 'Новая тема'}
                        </h1>
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        <Save className="size-4" /> Сохранить
                    </Button>
                </div>

                <div className="mt-6 max-w-3xl space-y-5">
                    <TranslatableField
                        name="title"
                        label="Заголовок"
                        locales={locales}
                        value={form.data.title}
                        onChange={(value) => form.setData('title', value)}
                        required
                        errors={errors}
                        as="input"
                        placeholder="Заголовок темы"
                    />

                    <div className="grid gap-2">
                        <Label>Категория</Label>
                        <Select
                            value={form.data.forum_category_id ? String(form.data.forum_category_id) : 'none'}
                            onValueChange={(value) =>
                                form.setData('forum_category_id', value === 'none' ? null : Number(value))
                            }
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Без категории" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="none">Без категории</SelectItem>
                                {forumCategories.map((category) => (
                                    <SelectItem key={category.id} value={String(category.id)}>
                                        {category.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.forum_category_id} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Автор</Label>
                        <Input
                            value={form.data.author}
                            onChange={(event) => form.setData('author', event.target.value)}
                            placeholder="Имя автора"
                        />
                        <InputError message={errors.author} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Ответы</Label>
                        <Input
                            type="number"
                            value={form.data.replies}
                            onChange={(event) => form.setData('replies', Number(event.target.value))}
                        />
                        <InputError message={errors.replies} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Просмотры</Label>
                        <Input
                            type="number"
                            value={form.data.views}
                            onChange={(event) => form.setData('views', Number(event.target.value))}
                        />
                        <InputError message={errors.views} />
                    </div>

                    <div className="flex items-center gap-2">
                        <Checkbox
                            id="pinned"
                            checked={form.data.pinned}
                            onCheckedChange={(checked) => form.setData('pinned', checked === true)}
                        />
                        <Label htmlFor="pinned">Закреплена</Label>
                    </div>

                    <div className="grid gap-2">
                        <Label>Последняя активность</Label>
                        <Input
                            value={form.data.last_activity}
                            onChange={(event) => form.setData('last_activity', event.target.value)}
                            placeholder="2 часа назад"
                        />
                        <InputError message={errors.last_activity} />
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
