import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { TranslatableField } from '@/components/admin/translatable-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type LocaleMap = Record<string, string>;

type RegionalOfficeData = {
    id: number;
    region: LocaleMap;
    head: LocaleMap;
    address: LocaleMap;
    phone: string | null;
    sort_order: number;
};

type Props = {
    locales: string[];
    item?: RegionalOfficeData;
};

export default function RegionalOfficeForm({ locales, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        region: item?.region ?? { ...blank },
        head: item?.head ?? { ...blank },
        address: item?.address ?? { ...blank },
        phone: item?.phone ?? '',
        sort_order: item?.sort_order ?? 0,
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/regional-offices/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/regional-offices', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование отделения' : 'Новое отделение'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/regional-offices" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование отделения' : 'Новое отделение'}
                        </h1>
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        <Save className="size-4" /> Сохранить
                    </Button>
                </div>

                <div className="mt-6 max-w-3xl space-y-5">
                    <TranslatableField
                        name="region"
                        label="Регион"
                        locales={locales}
                        value={form.data.region}
                        onChange={(v) => form.setData('region', v)}
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
                        name="address"
                        label="Адрес"
                        locales={locales}
                        value={form.data.address}
                        onChange={(v) => form.setData('address', v)}
                        as="textarea"
                        errors={errors}
                    />

                    <div className="grid gap-2">
                        <Label>Телефон</Label>
                        <Input
                            value={form.data.phone}
                            onChange={(event) => form.setData('phone', event.target.value)}
                            placeholder="+992 ..."
                        />
                        <InputError message={errors.phone} />
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
