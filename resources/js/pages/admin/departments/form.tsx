import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { TranslatableField } from '@/components/admin/translatable-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type LocaleMap = Record<string, string>;

type DepartmentData = {
    id: number;
    title: LocaleMap;
    description: LocaleMap;
    head: LocaleMap;
    icon: string | null;
    sort_order: number;
};

type Props = {
    locales: string[];
    item?: DepartmentData;
};

export default function DepartmentForm({ locales, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        title: item?.title ?? { ...blank },
        description: item?.description ?? { ...blank },
        head: item?.head ?? { ...blank },
        icon: item?.icon ?? '',
        sort_order: item?.sort_order ?? 0,
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/departments/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/departments', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование отдела' : 'Новый отдел'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/departments" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование отдела' : 'Новый отдел'}
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
                        onChange={(v) => form.setData('title', v)}
                        required
                        as="input"
                        errors={errors}
                    />

                    <TranslatableField
                        name="head"
                        label="Руководитель"
                        locales={locales}
                        value={form.data.head}
                        onChange={(v) => form.setData('head', v)}
                        as="input"
                        errors={errors}
                    />

                    <TranslatableField
                        name="description"
                        label="Описание"
                        locales={locales}
                        value={form.data.description}
                        onChange={(v) => form.setData('description', v)}
                        as="textarea"
                        errors={errors}
                    />

                    <div className="grid gap-2">
                        <Label>Иконка</Label>
                        <Input
                            value={form.data.icon}
                            onChange={(event) => form.setData('icon', event.target.value)}
                            placeholder="building"
                        />
                        <InputError message={errors.icon} />
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
