import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { TranslatableField } from '@/components/admin/translatable-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type LocaleMap = Record<string, string>;

type DirectionData = {
    id: number;
    key: string | null;
    icon: string | null;
    title: LocaleMap;
    description: LocaleMap;
    stat_value: string | null;
    stat_label: LocaleMap;
    sort_order: number | null;
};

type Props = {
    locales: string[];
    item?: DirectionData;
};

export default function DirectionForm({ locales, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        title: item?.title ?? { ...blank },
        description: item?.description ?? { ...blank },
        stat_label: item?.stat_label ?? { ...blank },
        key: item?.key ?? '',
        icon: item?.icon ?? '',
        stat_value: item?.stat_value ?? '',
        sort_order: item?.sort_order ?? 0,
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/directions/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/directions', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование направления' : 'Новое направление'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/directions" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование направления' : 'Новое направление'}
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
                        errors={errors}
                        as="input"
                    />

                    <TranslatableField
                        name="description"
                        label="Описание"
                        locales={locales}
                        value={form.data.description}
                        onChange={(v) => form.setData('description', v)}
                        errors={errors}
                        as="textarea"
                    />

                    <TranslatableField
                        name="stat_label"
                        label="Подпись показателя"
                        locales={locales}
                        value={form.data.stat_label}
                        onChange={(v) => form.setData('stat_label', v)}
                        errors={errors}
                        as="input"
                    />

                    <div className="grid gap-2">
                        <Label>Ключ</Label>
                        <Input
                            value={form.data.key}
                            onChange={(event) => form.setData('key', event.target.value)}
                            placeholder="key"
                        />
                        <InputError message={errors.key} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Иконка</Label>
                        <Input
                            value={form.data.icon}
                            onChange={(event) => form.setData('icon', event.target.value)}
                            placeholder="icon-name"
                        />
                        <InputError message={errors.icon} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Значение показателя</Label>
                        <Input
                            value={form.data.stat_value}
                            onChange={(event) => form.setData('stat_value', event.target.value)}
                            placeholder="1000+"
                        />
                        <InputError message={errors.stat_value} />
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
