import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { TranslatableField } from '@/components/admin/translatable-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type LocaleMap = Record<string, string>;

type CategoryData = {
    id: number;
    name: LocaleMap;
    slug: string;
    sort_order: number | null;
};

type Props = {
    locales: string[];
    item?: CategoryData;
};

export default function DocumentCategoryForm({ locales, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        name: item?.name ?? { ...blank },
        slug: item?.slug ?? '',
        sort_order: item?.sort_order ?? 0,
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/document-categories/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/document-categories', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование категории' : 'Новая категория'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/document-categories" aria-label="Назад">
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
                        name="name"
                        label="Название"
                        locales={locales}
                        value={form.data.name}
                        onChange={(value) => form.setData('name', value)}
                        required
                        errors={errors}
                        as="input"
                        placeholder="Название категории"
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
